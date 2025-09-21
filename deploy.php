<?php
// ==== CONFIGURATION ==== //
$ftp_host = "amigostayo.com";
$ftp_user = "deployer@amigostayo.com";
$ftp_pass = "2_i@29qr.vcW";

$local_root = __DIR__; // local project root
$remote_root = "public_html/coop"; // remote root folder on FTP

$exclude = [
    '.git', '.gitignore',
    'vendor', 'node_modules',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'deploy.php'
];

// ==== DRY-RUN MODE ==== //
// 1. Set to true for testing
$dryRun = false;

// 2. CLI flag support
if (in_array('--dry-run', $argv ?? [])) {
    $dryRun = true;
}

// ==== CONNECT TO FTP ==== //
$conn_id = ftp_connect($ftp_host);
if (!$conn_id) {
    exit("❌ Could not connect to FTP host: $ftp_host\n");
}

if (!ftp_login($conn_id, $ftp_user, $ftp_pass)) {
    exit("❌ Could not login with provided credentials\n");
}

ftp_pasv($conn_id, true); // enable passive mode

echo ($dryRun ? "🔍 DRY-RUN: " : "🚀 ")."Starting deployment to $remote_root ...\n";

// ==== FUNCTIONS ==== //
function isExcluded($path, $exclude) {
    foreach ($exclude as $ex) {
        if (stripos($path, $ex) !== false) {
            return true;
        }
    }
    return false;
}

function ftpEnsureDirExists($conn_id, $dir) {
    $parts = explode('/', $dir);
    $path = '';
    foreach ($parts as $part) {
        if ($part === '') continue;
        $path .= "/$part";
        if (@ftp_chdir($conn_id, $path)) {
            ftp_chdir($conn_id, "/"); // go back root
            continue;
        }
        @ftp_mkdir($conn_id, $path);
    }
}

// Recursively upload changed files
function uploadDir($conn_id, $local_dir, $remote_dir, $exclude, $dryRun) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($local_dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        $local_path = $file->getPathname();

        if (isExcluded($local_path, $exclude)) {
            continue;
        }

        $relative_path = str_replace($local_dir, '', $local_path);
        $relative_path = ltrim(str_replace('\\', '/', $relative_path), '/');

        $remote_path = $remote_dir . '/' . $relative_path;

        if ($file->isDir()) {
            if (!$dryRun) {
                ftpEnsureDirExists($conn_id, $remote_path);
            }
            continue;
        }

        // Check remote file timestamp
        $remote_time = ftp_mdtm($conn_id, $remote_path);
        $local_time  = filemtime($local_path);

        if ($remote_time == -1 || $local_time > $remote_time) {
            echo "📤 Uploading: $relative_path\n";
            if (!$dryRun) {
                ftpEnsureDirExists($conn_id, dirname($remote_path));
                if (!ftp_put($conn_id, $remote_path, $local_path, FTP_BINARY)) {
                    echo "⚠️ Failed to upload: $relative_path\n";
                }
            }
        } else {
            echo "⏩ Skipped (no change): $relative_path\n";
        }
    }
}

// ==== START UPLOAD ==== //
uploadDir($conn_id, $local_root, $remote_root, $exclude, $dryRun);

ftp_close($conn_id);
echo ($dryRun ? "✅ DRY-RUN completed.\n" : "✅ Deployment completed.\n");
