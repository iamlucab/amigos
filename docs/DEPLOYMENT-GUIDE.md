# Complete Deployment Guide for Ebili Laravel Application

This guide covers both **file deployment** and **database deployment** to your GoDaddy hosting.

## 🚀 Quick Deployment Overview

Your deployment system consists of two parts:
1. **File Deployment** - Uploads your code files via FTP
2. **Database Deployment** - Synchronizes database structure and migrations

## 📁 File Deployment

### Commands
```bash
# Test FTP connection
php deploy.php --test

# Preview what files will be uploaded
php deploy.php --dry-run

# Deploy files to live server
php deploy.php
```

### What Gets Deployed
- ✅ PHP files, Blade templates
- ✅ CSS, JavaScript, images
- ✅ Public assets and storage files
- ❌ Development files (.git, node_modules, etc.)
- ❌ Environment files (.env)
- ❌ Integration scripts

## 🗄️ Database Deployment

### Setup Required
1. **Configure your GoDaddy database credentials** in [`database-config.php`](database-config.php:1):
   ```php
   'remote' => [
       'host' => 'your-godaddy-db-host.com',
       'database' => 'your_production_db_name',
       'username' => 'your_db_username',
       'password' => 'your_db_password',
   ]
   ```

### Commands
```bash
# Test database connections
php deploy-database.php --test

# Compare local vs remote database
php deploy-database.php --compare

# Show deployment status
php deploy-database.php --status

# Export schema to SQL file
php deploy-database.php --export

# Preview database changes
php deploy-database.php --dry-run

# Deploy database changes
php deploy-database.php
```

### What Gets Deployed
- ✅ New tables from local database
- ✅ Table structure changes
- ✅ Migration tracking
- ✅ Automatic backups (before changes)
- ❌ Existing data (preserved)

## 🔄 Complete Deployment Workflow

### Step 1: Prepare for Deployment
```bash
# Check what files need uploading
php deploy.php --status

# Check database changes
php deploy-database.php --status
```

### Step 2: Test Connections
```bash
# Test FTP connection
php deploy.php --test

# Test database connections
php deploy-database.php --test
```

### Step 3: Preview Changes (Recommended)
```bash
# Preview file deployment
php deploy.php --dry-run

# Preview database deployment
php deploy-database.php --dry-run
```

### Step 4: Deploy Files First
```bash
# Deploy your code files
php deploy.php
```

### Step 5: Deploy Database Changes
```bash
# Deploy database structure
php deploy-database.php
```

### Step 6: Verify Deployment
- Visit https://www.ebili.online
- Test key application features
- Check for any errors

## ⚙️ Configuration Files

| File | Purpose |
|------|---------|
| [`deploy-config.php`](deploy-config.php:1) | FTP credentials and file deployment settings |
| [`database-config.php`](database-config.php:1) | Database connection settings |
| [`GoDaddyDeployer.php`](GoDaddyDeployer.php:1) | File deployment class |
| [`DatabaseDeployer.php`](DatabaseDeployer.php:1) | Database deployment class |

## 🛡️ Safety Features

### File Deployment
- Incremental uploads (only changed files)
- Comprehensive exclusion rules
- Deployment history tracking
- Dry-run mode for testing

### Database Deployment
- Automatic backups before changes
- Schema comparison and validation
- Migration tracking
- Rollback-friendly SQL exports

## 🚨 Important Notes

### Database Deployment Limitations
- **GoDaddy Shared Hosting** may have restrictions on direct database access
- **Complex schema changes** may require manual intervention
- **Data migrations** are not automated (structure only)
- **Always backup** your production database before deployment

### Alternative Database Deployment Methods

#### Method 1: Manual via phpMyAdmin (Recommended for GoDaddy)
1. Export schema: `php deploy-database.php --export`
2. Login to GoDaddy hosting panel
3. Open phpMyAdmin
4. Import the generated SQL file

#### Method 2: Laravel Migrations (If SSH available)
```bash
# If you have SSH access to your server
php artisan migrate
php artisan config:cache
```

## 🔧 Troubleshooting

### File Deployment Issues
```bash
# Connection problems
php deploy.php --test

# Check what's pending
php deploy.php --status

# Preview without uploading
php deploy.php --dry-run
```

### Database Deployment Issues
```bash
# Connection problems
php deploy-database.php --test

# Compare schemas
php deploy-database.php --compare

# Export for manual import
php deploy-database.php --export
```

### Common Issues

#### "Could not open input file: deploy"
**Solution:** Use the full filename with extension:
```bash
php deploy.php --dry-run  # ✅ Correct
php deploy --dry-run      # ❌ Wrong
```

#### Database Connection Failed
**Solution:** Update [`database-config.php`](database-config.php:1) with correct GoDaddy database credentials

#### FTP Connection Failed
**Solution:** Verify credentials in [`deploy-config.php`](deploy-config.php:1)

## 📋 Pre-Deployment Checklist

### Before File Deployment
- [ ] Test FTP connection
- [ ] Review files to be uploaded
- [ ] Ensure .env is configured on server
- [ ] Backup current live files (if needed)

### Before Database Deployment
- [ ] Test database connections
- [ ] Review schema differences
- [ ] Backup production database
- [ ] Test changes in staging environment

### After Deployment
- [ ] Verify website loads correctly
- [ ] Test user authentication
- [ ] Check database connectivity
- [ ] Test key application features
- [ ] Monitor error logs

## 🆘 Emergency Procedures

### Rollback File Deployment
- Re-upload previous version files
- Or restore from FTP backup (if available)

### Rollback Database Changes
- Restore from backup created before deployment
- Use GoDaddy's database backup tools

## 📞 Support

### For File Deployment Issues
1. Check FTP credentials
2. Verify file permissions
3. Contact GoDaddy FTP support

### For Database Issues
1. Verify database credentials
2. Check GoDaddy database restrictions
3. Use manual phpMyAdmin import
4. Contact GoDaddy database support

---

**Target Environment:** GoDaddy Shared Hosting  
**Application:** Ebili Laravel Application  
**Domain:** https://www.ebili.online  
**Last Updated:** January 2025