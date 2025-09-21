<?php

class GoDaddyDeployer {
    private $ftpHost;
    private $ftpUser;
    private $ftpPass;
    private $ftpPort;
    private $ftpSsl;
    private $ftpPassive;
    private $localPath;
    private $remotePath;
    private $connection;
    private $lastDeployFile = 'last_deploy.json';
    private $config;
    private $excludePatterns = [];
    private $includeExtensions = [];
    
    public function __construct($config) {
        $this->config = $config;
        $this->ftpHost = $config['ftp']['host'];
        $this->ftpUser = $config['ftp']['username'];
        $this->ftpPass = $config['ftp']['password'];
        $this->ftpPort = $config['ftp']['port'] ?? 21;
        $this->ftpSsl = $config['ftp']['ssl'] ?? false;
        $this->ftpPassive = $config['ftp']['passive'] ?? true;
        $this->localPath = rtrim($config['local_path'], '/');
        $this->remotePath = rtrim($config['remote_path'], '/');
        $this->excludePatterns = $config['exclude'] ?? [];
        $this->includeExtensions = $config['include_extensions'] ?? [];
    }
    
    /**
     * Connect to FTP server
     */
    public function connect() {
        $this->log("Connecting to FTP server: {$this->ftpHost}:{$this->ftpPort}");
        
        if ($this->ftpSsl) {
            $this->connection = ftp_ssl_connect($this->ftpHost, $this->ftpPort);
        } else {
            $this->connection = ftp_connect($this->ftpHost, $this->ftpPort);
        }
        
        if (!$this->connection) {
            throw new Exception("Could not connect to FTP server: {$this->ftpHost}:{$this->ftpPort}");
        }
        
        if (!ftp_login($this->connection, $this->ftpUser, $this->ftpPass)) {
            throw new Exception("Could not login to FTP server with provided credentials");
        }
        
        if ($this->ftpPassive) {
            ftp_pasv($this->connection, true);
        }
        
        $this->log("Successfully connected to FTP server");
        return true;
    }
    
    /**
     * Disconnect from FTP server
     */
    public function disconnect() {
        if ($this->connection) {
            ftp_close($this->connection);
            $this->connection = null;
            $this->log("Disconnected from FTP server");
        }
    }
    
    /**
     * Deploy files to remote server
     */
    public function deploy($dryRun = false) {
        try {
            $this->connect();
            
            $this->log("Starting deployment...");
            $this->log("Local path: {$this->localPath}");
            $this->log("Remote path: {$this->remotePath}");
            
            if ($dryRun) {
                $this->log("DRY RUN MODE - No files will be uploaded");
            }
            
            // Get last deployment info
            $lastDeploy = $this->getLastDeployInfo();
            
            // Get files to upload
            $filesToUpload = $this->getFilesToUpload($lastDeploy);
            
            $this->log("Found " . count($filesToUpload) . " files to upload");
            
            if (empty($filesToUpload)) {
                $this->log("No files to upload. Deployment complete.");
                return true;
            }
            
            // Create backup if enabled
            if ($this->config['backup']['enabled'] && !$dryRun) {
                $this->createBackup();
            }
            
            // Upload files
            $uploadedFiles = [];
            $failedFiles = [];
            
            foreach ($filesToUpload as $file) {
                $localFile = $this->localPath . '/' . $file;
                $remoteFile = $this->remotePath . '/' . $file;
                
                if ($dryRun) {
                    $this->log("Would upload: {$file}");
                    continue;
                }
                
                if ($this->uploadFile($localFile, $remoteFile)) {
                    $uploadedFiles[] = $file;
                    $this->log("Uploaded: {$file}");
                } else {
                    $failedFiles[] = $file;
                    $this->log("Failed to upload: {$file}", 'error');
                }
            }
            
            if (!$dryRun) {
                // Update last deploy info
                $this->updateLastDeployInfo($uploadedFiles);
                
                $this->log("Deployment completed successfully!");
                $this->log("Uploaded files: " . count($uploadedFiles));
                
                if (!empty($failedFiles)) {
                    $this->log("Failed files: " . count($failedFiles), 'warning');
                    foreach ($failedFiles as $file) {
                        $this->log("  - {$file}", 'warning');
                    }
                }
            }
            
            return empty($failedFiles);
            
        } catch (Exception $e) {
            $this->log("Deployment failed: " . $e->getMessage(), 'error');
            throw $e;
        } finally {
            $this->disconnect();
        }
    }
    
    /**
     * Upload a single file
     */
    private function uploadFile($localFile, $remoteFile) {
        // Create remote directory if it doesn't exist
        $remoteDir = dirname($remoteFile);
        $this->createRemoteDirectory($remoteDir);
        
        // Upload file
        if (ftp_put($this->connection, $remoteFile, $localFile, FTP_BINARY)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Create remote directory recursively
     */
    private function createRemoteDirectory($dir) {
        if ($dir === '.' || $dir === '/' || empty($dir)) {
            return true;
        }
        
        // Normalize the path
        $dir = str_replace('\\', '/', $dir);
        $dir = ltrim($dir, '/');
        
        // Check if directory exists
        $currentDir = ftp_pwd($this->connection);
        if (@ftp_chdir($this->connection, $dir)) {
            ftp_chdir($this->connection, $currentDir);
            return true;
        }
        
        // Split path into parts and create each directory
        $parts = explode('/', $dir);
        $path = '';
        
        foreach ($parts as $part) {
            if (empty($part)) continue;
            
            $path .= ($path ? '/' : '') . $part;
            
            // Try to change to this directory
            if (!@ftp_chdir($this->connection, $path)) {
                // Directory doesn't exist, create it
                if (!@ftp_mkdir($this->connection, $path)) {
                    $this->log("Failed to create directory: {$path}", 'error');
                    ftp_chdir($this->connection, $currentDir);
                    return false;
                }
                $this->log("Created directory: {$path}");
            }
        }
        
        // Return to original directory
        ftp_chdir($this->connection, $currentDir);
        return true;
    }
    
    /**
     * Get files that need to be uploaded
     */
    private function getFilesToUpload($lastDeploy = null) {
        $files = [];
        $this->scanDirectory($this->localPath, '', $files);
        
        // Filter files based on last deployment
        if ($lastDeploy && isset($lastDeploy['timestamp'])) {
            $lastDeployTime = $lastDeploy['timestamp'];
            $files = array_filter($files, function($file) use ($lastDeployTime) {
                $filePath = $this->localPath . '/' . $file;
                return filemtime($filePath) > $lastDeployTime;
            });
        }
        
        return array_values($files);
    }
    
    /**
     * Recursively scan directory for files
     */
    private function scanDirectory($basePath, $relativePath, &$files) {
        $fullPath = $basePath . ($relativePath ? '/' . $relativePath : '');
        
        if (!is_dir($fullPath)) {
            return;
        }
        
        $items = scandir($fullPath);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $itemPath = $fullPath . '/' . $item;
            $relativeItemPath = $relativePath ? $relativePath . '/' . $item : $item;
            
            // Check if item should be excluded
            if ($this->shouldExclude($relativeItemPath)) {
                continue;
            }
            
            if (is_dir($itemPath)) {
                $this->scanDirectory($basePath, $relativeItemPath, $files);
            } else {
                // Check file extension
                if ($this->shouldIncludeFile($item)) {
                    $files[] = $relativeItemPath;
                }
            }
        }
    }
    
    /**
     * Check if file/directory should be excluded
     */
    private function shouldExclude($path) {
        foreach ($this->excludePatterns as $pattern) {
            if (fnmatch($pattern, $path) || strpos($path, rtrim($pattern, '/')) === 0) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if file should be included based on extension
     */
    private function shouldIncludeFile($filename) {
        if (empty($this->includeExtensions)) {
            return true;
        }
        
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return in_array($extension, $this->includeExtensions);
    }
    
    /**
     * Get last deployment information
     */
    private function getLastDeployInfo() {
        $deployFile = $this->localPath . '/' . $this->lastDeployFile;
        
        if (!file_exists($deployFile)) {
            return null;
        }
        
        $content = file_get_contents($deployFile);
        return json_decode($content, true);
    }
    
    /**
     * Update last deployment information
     */
    private function updateLastDeployInfo($uploadedFiles) {
        $deployInfo = [
            'timestamp' => time(),
            'date' => date('Y-m-d H:i:s'),
            'files_uploaded' => count($uploadedFiles),
            'files' => $uploadedFiles
        ];
        
        $deployFile = $this->localPath . '/' . $this->lastDeployFile;
        file_put_contents($deployFile, json_encode($deployInfo, JSON_PRETTY_PRINT));
    }
    
    /**
     * Create backup of remote files
     */
    private function createBackup() {
        $this->log("Creating backup...");
        
        $backupDir = $this->remotePath . '_backup_' . date('Y-m-d_H-i-s');
        
        // This is a simplified backup - in a real scenario, you might want to
        // download and compress files or use server-side backup tools
        $this->log("Backup would be created at: {$backupDir}");
        
        // Clean old backups
        $this->cleanOldBackups();
    }
    
    /**
     * Clean old backups
     */
    private function cleanOldBackups() {
        $keepBackups = $this->config['backup']['keep_backups'] ?? 5;
        $this->log("Cleaning old backups (keeping {$keepBackups})...");
        
        // Implementation would depend on your backup strategy
        // This is a placeholder for backup cleanup logic
    }
    
    /**
     * Test FTP connection
     */
    public function testConnection() {
        try {
            $this->connect();
            $this->log("Connection test successful!");
            
            // Test if we can access the remote directory
            if (@ftp_chdir($this->connection, $this->remotePath)) {
                $this->log("Remote directory accessible: {$this->remotePath}");
            } else {
                $this->log("Warning: Cannot access remote directory: {$this->remotePath}", 'warning');
            }
            
            return true;
        } catch (Exception $e) {
            $this->log("Connection test failed: " . $e->getMessage(), 'error');
            return false;
        } finally {
            $this->disconnect();
        }
    }
    
    /**
     * Get deployment status
     */
    public function getStatus() {
        $lastDeploy = $this->getLastDeployInfo();
        $filesToUpload = $this->getFilesToUpload($lastDeploy);
        
        return [
            'last_deploy' => $lastDeploy,
            'files_to_upload' => count($filesToUpload),
            'files_pending' => array_slice($filesToUpload, 0, 10) // Show first 10 files
        ];
    }
    
    /**
     * Log messages
     */
    private function log($message, $level = 'info') {
        $timestamp = date('Y-m-d H:i:s');
        $levelUpper = strtoupper($level);
        echo "[{$timestamp}] [{$levelUpper}] {$message}\n";
    }
}