# Data Migration Guide for New Registration System

## Overview

This guide explains how to migrate existing data from the old registration system to the new three-tier registration system while preserving all existing cashback, bonuses, and member history.

## What's Being Preserved

✅ **All existing member data**
✅ **All cashback logs and balances**
✅ **All referral bonus logs and wallet transactions**
✅ **All wallet balances (main and cashback)**
✅ **All order history and product purchases**
✅ **All existing sponsor relationships**

## Migration Steps

### Step 1: Import Your Database

First, import your existing database (`ebili1.sql`) into your development/production environment:

```bash
mysql -u your_username -p your_database_name < ebili1.sql
```

### Step 2: Run the Migration

Execute the migration to assign membership codes to existing members:

```bash
php artisan migrate
```

This will run the migration file `2025_07_31_000000_migrate_existing_members_assign_codes.php` which:
- Generates membership codes for existing members without codes
- Assigns codes to all existing approved members
- Updates status from 'Active' to 'Approved' (new system standard)

### Step 3: Run the Data Seeder

Execute the comprehensive data seeder to ensure data integrity:

```bash
php artisan db:seed --class=MigrateExistingDataSeeder
```

This seeder will:
- Verify all members have membership codes
- Preserve all existing cashback and bonus history
- Validate data integrity
- Provide a detailed migration summary

## What Happens to Existing Data

### Members Table
- **Status Update**: All members with status 'Active' → 'Approved'
- **Membership Codes**: Auto-assigned to existing members
- **Sponsor Relationships**: Preserved exactly as they were
- **All Other Data**: Unchanged (names, mobile numbers, photos, etc.)

### Users Table
- **Status Update**: All users with status 'Active' → 'Approved'
- **Login Credentials**: Unchanged
- **All Other Data**: Preserved

### Membership Codes Table
- **Existing Used Codes**: Remain linked to their current users
- **New Codes**: Generated and assigned to members without codes
- **Usage History**: Preserved with `used_at` timestamps

### Financial Data (CRITICAL - All Preserved)
- **Wallet Balances**: All main and cashback wallet balances preserved
- **Referral Bonus Logs**: All 10 existing bonus entries preserved
- **Cashback Logs**: All cashback history preserved
- **Wallet Transactions**: All 41 transaction records preserved
- **Order History**: All orders and payments preserved

## Verification Checklist

After migration, verify the following:

### ✅ Member Data Integrity
```sql
-- Check all approved members have membership codes
SELECT m.id, m.first_name, m.last_name, m.status, mc.code 
FROM members m 
LEFT JOIN users u ON m.id = u.member_id 
LEFT JOIN membership_codes mc ON u.id = mc.used_by 
WHERE m.status = 'Approved' AND mc.code IS NULL;
-- Should return 0 rows
```

### ✅ Financial Data Preservation
```sql
-- Verify wallet balances are preserved
SELECT COUNT(*) as wallets_with_balance FROM wallets WHERE balance > 0;
-- Should match your original count

-- Verify referral bonus logs are preserved
SELECT COUNT(*) as bonus_logs FROM referral_bonus_logs;
-- Should be 10 (from your original data)

-- Verify cashback logs are preserved
SELECT COUNT(*) as cashback_logs FROM cashback_logs;
-- Should be 1 (from your original data)
```

### ✅ Membership Code Assignment
```sql
-- Check membership code usage
SELECT 
    COUNT(CASE WHEN used = 1 THEN 1 END) as used_codes,
    COUNT(CASE WHEN used = 0 THEN 1 END) as unused_codes
FROM membership_codes;
```

## New Registration Flow Impact

### Existing Members (No Impact)
- Can continue using the system normally
- All their data, balances, and history preserved
- Login credentials unchanged
- Referral relationships maintained

### New Members (After Migration)
- **Admin Registration**: Requires sponsor selection + membership code
- **Guest Registration**: Subject to admin approval (no code required during registration)
- **Referral Registration**: Automatic sponsor assignment + admin approval required

### Referral Bonuses (Important Change)
- **Existing Bonuses**: All preserved and unchanged
- **New Bonuses**: Only distributed when members are approved (not on registration)
- **Pending Members**: No bonuses until approved by admin

## Rollback Plan (If Needed)

If you need to rollback:

1. **Database Backup**: Always backup before migration
2. **Status Rollback**: Change 'Approved' back to 'Active' if needed
3. **Code Assignment**: Membership code assignments can be cleared if necessary

```sql
-- Emergency rollback (use with caution)
UPDATE members SET status = 'Active' WHERE status = 'Approved';
UPDATE users SET status = 'Active' WHERE status = 'Approved';
```

## Testing Recommendations

1. **Test on Development First**: Always test migration on a copy of production data
2. **Verify Login**: Ensure existing users can still login
3. **Check Balances**: Verify all wallet balances are correct
4. **Test New Registration**: Try all three registration methods
5. **Admin Approval**: Test the admin approval workflow

## Support

If you encounter any issues during migration:

1. Check the migration logs for detailed output
2. Verify database constraints are met
3. Ensure all foreign key relationships are intact
4. Contact support with specific error messages

## Summary

This migration is designed to be **non-destructive** and **backward-compatible**. All existing data is preserved, and the system continues to work for existing users while adding the new registration workflows for future members.

The key principle: **Preserve everything, enhance functionality**.