# GoDaddy Deployment Guide for Ebili Laravel Application

This guide explains how to deploy your Ebili Laravel application to GoDaddy hosting using the automated deployment system.

## Files Overview

- **`GoDaddyDeployer.php`** - Main deployment class with FTP functionality
- **`deploy-config.php`** - Configuration file with GoDaddy credentials and settings
- **`deploy.php`** - Command-line deployment script
- **`deploy-config.example.php`** - Example configuration file

## Quick Start

### 1. Test Connection
First, verify that your FTP credentials are working:
```bash
php deploy.php --test
```

### 2. Preview Deployment
See what files would be uploaded without actually deploying:
```bash
php deploy.php --dry-run
```

### 3. Deploy to Live Server
Deploy your application to the live server:
```bash
php deploy.php
```

## Available Commands

| Command | Description |
|---------|-------------|
| `php deploy.php --test` | Test FTP connection only |
| `php deploy.php --status` | Show deployment status and pending files |
| `php deploy.php --dry-run` | Preview what would be deployed |
| `php deploy.php --help` | Show help message |
| `php deploy.php` | Deploy to live server |

## Configuration

### FTP Settings
The deployment is configured for:
- **Host:** ebili.online
- **Username:** admin@ebili.online
- **Remote Path:** / (root directory)
- **Port:** 21 (standard FTP)

### Excluded Files/Directories
The following are automatically excluded from deployment:
- `.git/`, `node_modules/`, `vendor/`
- `storage/logs/`, `storage/framework/cache/`
- `.env` files
- Development tools and configs
- Integration scripts
- Mobile app files

### Included File Types
Only these file extensions are uploaded:
- PHP: `.php`, `.blade.php`
- Web: `.js`, `.css`, `.html`, `.json`, `.xml`
- Images: `.jpg`, `.jpeg`, `.png`, `.gif`, `.svg`, `.ico`, `.webp`
- Fonts: `.ttf`, `.woff`, `.woff2`, `.eot`
- Other: `.txt`, `.md`, `.htaccess`

## Deployment Process

1. **Connection Test** - Verifies FTP credentials
2. **File Scanning** - Identifies files that need uploading
3. **Backup Creation** - Creates backup if enabled
4. **File Upload** - Uploads files via FTP
5. **Progress Tracking** - Saves deployment history

## Post-Deployment Steps

After successful deployment:

1. **Verify .env file** - Ensure production environment variables are set
2. **Check database connection** - Verify database credentials
3. **Test file permissions** - Ensure proper permissions (755 for directories, 644 for files)
4. **Verify storage directories** - Check that `storage/` and `bootstrap/cache/` are writable
5. **Test application** - Verify key features work correctly

### Optional Laravel Commands (if SSH access available)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

## Troubleshooting

### Connection Issues
- Verify FTP credentials in `deploy-config.php`
- Check if your IP is whitelisted with GoDaddy
- Ensure FTP service is enabled in your hosting panel

### Upload Failures
- Check file permissions on local files
- Verify available disk space on server
- Check for special characters in file names

### Performance Tips
- Use `--dry-run` to preview large deployments
- Deploy during low-traffic hours
- Monitor deployment logs for errors

## Security Notes

- **Never commit `deploy-config.php`** to version control
- Keep FTP credentials secure
- Use strong passwords
- Consider enabling FTPS if available

## Support

For deployment issues:
1. Check the deployment logs
2. Verify FTP credentials
3. Test connection with `--test` flag
4. Contact GoDaddy support for server-side issues

## Example Workflow

```bash
# 1. Test connection
php deploy.php --test

# 2. Check what needs deploying
php deploy.php --status

# 3. Preview deployment
php deploy.php --dry-run

# 4. Deploy to live server
php deploy.php

# 5. Verify deployment
# Visit https://www.ebili.online to test
```

---

**Target URL:** https://www.ebili.online  
**Last Updated:** January 2025