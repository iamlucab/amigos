# Specific Members Integration from amigos-latest.sql

This integration script imports specific members and their related data from the `amigos-latest.sql` file into the current E-Bili application database.

## Overview

The integration includes the following specific members:

1. **Bernie Baldesco** (ID: 10026) - Businessman, sponsored by Ruthcil (16)
2. **Cindy Bandao** (ID: 10027) - Saleswoman, sponsored by Bernie (10026)
3. **Nor Umpar** (ID: 10028) - Lawyer, sponsored by Ruthcil (16)
4. **Ariel Capili** (ID: 10029) - sponsored by Nor (10028)
5. **Mary Ann Olbez** (ID: 10030) - sponsored by Ruthcil (16)
6. **Renz Licarte** (ID: 10031) - Engineer, sponsored by Ruthcil (16)
7. **Margie Palacio** (ID: 10032) - Business owner, sponsored by Ruthcil (16)
8. **Leah Perez** (ID: 10033) - Supervisor, sponsored by Margie (10032)
9. **Melanie Guiday** (ID: 10034) - Real Estate Salesperson, sponsored by Nor (10028)

## What Gets Integrated

### 1. Members Table
- All 9 specific members with their personal information
- Proper sponsor relationships (foreign keys)
- Original timestamps from amigos database

### 2. Users Table
- Corresponding user accounts for each member
- Mobile number as username
- Email format: `{mobile_number}@coop.local`
- Default password: `password123`

### 3. Wallets Table
- Main wallet and cashback wallet for each member
- Proper wallet IDs from original database
- Current balances as per amigos data

### 4. Wallet Transactions
- Referral bonus transactions
- Proper relationships to wallets and members
- Original timestamps and amounts

### 5. Cash-in Requests
- Pending cash-in requests for Melanie Guiday
- Original proof file paths and amounts

## Files Created

1. **`integrate_specific_members.php`** - Standalone PHP script
2. **`app/Console/Commands/IntegrateSpecificMembers.php`** - Laravel Artisan command
3. **`test_integration.php`** - Verification and testing script
4. **`INTEGRATION_README.md`** - This documentation file

## How to Run the Integration

### Method 1: Using Laravel Artisan Command (Recommended)

```bash
# Run the integration
php artisan integrate:specific-members

# Run only verification (without integration)
php artisan integrate:specific-members --verify
```

### Method 2: Using Standalone PHP Script

```bash
# Make sure you're in the Laravel project root
php integrate_specific_members.php
```

### Method 3: Run Test Script Only

```bash
# Test the integration after running it
php test_integration.php
```

## Pre-requisites

1. **Laravel Application**: Make sure your Laravel application is properly set up
2. **Database Connection**: Ensure your database connection is configured
3. **Models**: The following models must exist:
   - `App\Models\Member`
   - `App\Models\User`
   - `App\Models\Wallet`
   - `App\Models\WalletTransaction`
   - `App\Models\CashInRequest`

4. **Existing Data**: The integration assumes that member ID 16 (Ruthcil) already exists as a sponsor

## Safety Features

- **Transaction Rollback**: If any step fails, all changes are rolled back
- **Duplicate Prevention**: Checks for existing records before creating new ones
- **Model Events Disabled**: Prevents automatic wallet creation during member insertion
- **Verification**: Built-in verification to confirm successful integration

## Expected Results

After successful integration, you should have:

- ✅ 9 new members with proper relationships
- ✅ 9 new user accounts with authentication capability
- ✅ 18 new wallets (main + cashback for each member)
- ✅ 4 wallet transactions (referral bonuses)
- ✅ 2 cash-in requests
- ✅ Proper foreign key relationships maintained

## Verification

The integration includes comprehensive verification that checks:

1. **Members**: All 9 members created with correct data
2. **Users**: All 9 users linked to their respective members
3. **Wallets**: 18 wallets created (2 per member) with correct balances
4. **Transactions**: Referral bonus transactions properly recorded
5. **Cash-in Requests**: Pending requests for member 10034
6. **Relationships**: Sponsor-member relationships working correctly

## Wallet Balances After Integration

- **Bernie Baldesco**: ₱25.00 cashback (referral bonus from Cindy)
- **Cindy Bandao**: ₱0.00 (no bonuses received)
- **Nor Umpar**: ₱50.00 cashback (bonuses from Ariel and Melanie)
- **Ariel Capili**: ₱0.00 (no bonuses received)
- **Mary Ann Olbez**: ₱0.00 (no bonuses received)
- **Renz Licarte**: ₱0.00 (no bonuses received)
- **Margie Palacio**: ₱25.00 cashback (referral bonus from Leah)
- **Leah Perez**: ₱0.00 (no bonuses received)
- **Melanie Guiday**: ₱0.00 (no bonuses received)

**Total Cashback Balance**: ₱100.00

## Troubleshooting

### Common Issues

1. **Member ID 16 not found**
   - Solution: Ensure Ruthcil Cabandez (ID: 16) exists in your members table

2. **Database connection error**
   - Solution: Check your `.env` file database configuration

3. **Model not found errors**
   - Solution: Ensure all required models exist and are properly namespaced

4. **Permission errors**
   - Solution: Check file permissions and ensure Laravel can write to storage

### Rollback

If you need to remove the integrated data:

```sql
-- Remove in reverse order to maintain referential integrity
DELETE FROM cash_in_requests WHERE id IN (2, 3);
DELETE FROM wallet_transactions WHERE id IN (19, 25, 44, 37);
DELETE FROM wallets WHERE id BETWEEN 26 AND 43;
DELETE FROM users WHERE id BETWEEN 11045 AND 11053;
DELETE FROM members WHERE id BETWEEN 10026 AND 10034;
```

## Support

If you encounter any issues:

1. Check the Laravel logs: `storage/logs/laravel.log`
2. Run the verification: `php artisan integrate:specific-members --verify`
3. Test with: `php test_integration.php`
4. Check database constraints and foreign key relationships

## Notes

- All passwords are set to `password123` for testing purposes
- Email addresses follow the pattern `{mobile}@coop.local`
- Original timestamps from amigos database are preserved
- The integration is idempotent - running it multiple times won't create duplicates