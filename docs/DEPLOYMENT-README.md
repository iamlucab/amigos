# E-Bili Deployment Guide

## 🚀 Quick Deployment Setup

This guide will help you prepare the E-Bili application database for deployment by clearing test data and setting up the production-ready admin user and sample data.

## 📋 What This Does

The deployment preparation process will:

1. **Clear Database Tables** - Removes all data from tables except:
   - `categories` (preserved)
   - `migrations` (preserved)
   - `products` (preserved)
   - `units` (preserved)

2. **Create System Administrator**:
   - Name: System Administrator
   - Username: `09177260180`
   - Email: `mrcabandez@gmail.com`
   - Password: `!@#123123`
   - Role: Admin

3. **Create Sample Users**:
   - 2 Staff users with temporary credentials
   - 3 Member users with temporary credentials

4. **Sync Users to Members** - Ensures all users have corresponding member records

## 🛠️ Deployment Methods

### Method 1: Using the PHP Script (Recommended)

```bash
# Run from the project root directory
php prepare-deployment.php
```

### Method 2: Using Artisan Command

```bash
# Interactive mode (asks for confirmation)
php artisan deploy:prepare

# Force mode (no confirmation)
php artisan deploy:prepare --force
```

### Method 3: Using Database Seeder

```bash
# Run all seeders including deployment
php artisan db:seed

# Run only the deployment seeder
php artisan db:seed --class=DeploymentSeeder
```

## 🔐 Default Login Credentials

### System Administrator
- **Username**: `09177260180`
- **Email**: `mrcabandez@gmail.com`
- **Password**: `!@#123123`
- **Role**: Admin

### Staff Users (Temporary)
- **Username**: `staff001` | **Email**: `staff1@ebili.com` | **Password**: `password123`
- **Username**: `staff002` | **Email**: `staff2@ebili.com` | **Password**: `password123`

### Member Users (Temporary)
- **Username**: `member001` | **Email**: `member1@ebili.com` | **Password**: `password123`
- **Username**: `member002` | **Email**: `member2@ebili.com` | **Password**: `password123`
- **Username**: `member003` | **Email**: `member3@ebili.com` | **Password**: `password123`

## ⚠️ Important Notes

1. **Backup First**: Always backup your database before running deployment preparation
2. **Environment**: Make sure you're running this on the correct database environment
3. **Preserved Data**: Categories, products, and units tables will be preserved
4. **User Sync**: All users are automatically synced to the members table
5. **Temporary Users**: Staff and Member users are for testing - delete them in production

## 🔧 Post-Deployment Steps

1. **Login as Admin**: Use the system administrator credentials to access the admin panel
2. **Update Settings**: Configure application settings through the admin interface
3. **Remove Temporary Users**: Delete the temporary staff and member accounts
4. **Add Real Users**: Create actual user accounts for your organization
5. **Configure Services**: Set up SMS, email, and push notification services
6. **Test Features**: Verify all functionality works correctly

## 📁 Files Created/Modified

- `database/seeders/DeploymentSeeder.php` - Main deployment seeder
- `app/Console/Commands/PrepareDeployment.php` - Artisan command
- `prepare-deployment.php` - Standalone PHP script
- `DEPLOYMENT-README.md` - This documentation

## 🐛 Troubleshooting

### Command Not Found
If `php artisan deploy:prepare` doesn't work:
```bash
php artisan list | grep deploy
```

### Permission Issues
Make sure the web server has write permissions to the database and storage directories.

### Database Connection
Verify your `.env` file has correct database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Foreign Key Constraints
If you encounter foreign key constraint errors, the seeder will automatically disable and re-enable them.

## 📞 Support

If you encounter any issues during deployment preparation, check:
1. Database connection settings
2. File permissions
3. Laravel logs in `storage/logs/`

---

**Ready for deployment!** 🎉

After running the deployment preparation, your E-Bili application will be ready for production use with a clean database and proper admin access.