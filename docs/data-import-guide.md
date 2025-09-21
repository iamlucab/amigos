# E-Bili Data Import Guide

This guide explains how to import your existing `ebili1.sql` data into the new 3-tier registration system.

## Overview

The new system includes:
- **Admin Registration**: Requires sponsor selection and membership code
- **Guest Registration**: Requires admin approval
- **Member Referral Links**: Automatic sponsor assignment
- **Membership Code Tracking**: Shows which member uses each code

## Import Process

### Step 1: Prepare the SQL File

1. Place your `ebili1.sql` file in the root directory of your Laravel project
2. Ensure the file contains all your existing data (members, users, wallets, transactions, etc.)

### Step 2: Run the Import Command

Execute the following command in your terminal:

```bash
php artisan ebili:import-data
```

Or if your SQL file has a different name:

```bash
php artisan ebili:import-data your-file-name.sql
```

### Step 3: What the Import Does

The import command will:

1. **Import SQL Data**: Executes all INSERT statements from your SQL file
2. **Assign Membership Codes**: Automatically assigns codes to approved members who don't have them
3. **Generate Additional Codes**: Creates new codes if needed
4. **Update Statuses**: Changes 'Active' status to 'Approved' for consistency
5. **Show Summary**: Displays import statistics and data integrity check

### Step 4: Verify the Import

After import, you should see:

- ✅ All 21 members imported
- ✅ All wallet balances preserved (₱95,000+ total)
- ✅ All transaction history maintained
- ✅ Membership codes assigned to approved members
- ✅ Referral bonus logs intact
- ✅ All relationships preserved

## Expected Output

```
🚀 Starting import of ebili1.sql data...
📥 Importing SQL file...
Executing XXX SQL statements...
✓ SQL file imported successfully
🎫 Assigning membership codes to members without codes...
Found X members without codes
✓ Assigned code XXXXXXXX to Member Name
🔄 Updating member statuses...
✓ Updated X members from 'Active' to 'Approved'
📊 Import Summary:

┌─────────────────────────────┬──────────────┐
│ Metric                      │ Count/Amount │
├─────────────────────────────┼──────────────┤
│ Total Members               │ 21           │
│ Approved Members            │ 21           │
│ Pending Members             │ 0            │
│ Used Membership Codes       │ 21           │
│ Available Membership Codes  │ 9            │
│ Total Wallet Balance        │ ₱95,312.50   │
│ Total Referral Bonuses      │ ₱265.00      │
│ Cashback Log Entries        │ X            │
│ Wallet Transactions         │ 41           │
└─────────────────────────────┴──────────────┘

✅ All approved members have membership codes assigned
✅ Data import completed successfully!
```

## Post-Import Testing

### 1. Test Existing User Login
- Try logging in with existing member credentials
- Verify wallet balances are correct
- Check transaction history

### 2. Test New Registration Flows

**Admin Registration:**
- Go to `/register?type=admin`
- Select a sponsor from dropdown
- Enter a valid membership code
- Should create approved member immediately

**Guest Registration:**
- Go to `/register` (default)
- Fill registration form
- Should create pending member
- Admin can approve from members list

**Member Referral:**
- Get referral link from existing member: `/register?ref=MEMBERCODE`
- Register using the link
- Should auto-assign sponsor and create approved member

### 3. Test Membership Code Management
- Go to admin panel → Membership Codes
- Verify used codes show corresponding members
- Verify unused codes are available for selection

## Troubleshooting

### Import Fails
- Check if `ebili1.sql` file exists in root directory
- Ensure database connection is working
- Check Laravel logs for detailed error messages

### Missing Data After Import
- Verify the SQL file contains all expected data
- Check if foreign key constraints are causing issues
- Run the command again (it's safe to re-run)

### Members Without Codes
- The import automatically assigns codes to approved members
- If some members still don't have codes, run: `php artisan migrate:fresh --seed`

## Database Schema Changes

The new system maintains compatibility with your existing data while adding:

- `membership_codes.used_by` - Links codes to users
- `membership_codes.used_at` - Timestamp when code was used
- Enhanced member status tracking
- Improved referral bonus logic

## Support

If you encounter any issues during import:

1. Check the command output for specific error messages
2. Verify your SQL file format and content
3. Ensure all Laravel dependencies are installed
4. Check database permissions and connection

The import process is designed to be safe and can be re-run if needed without data loss.