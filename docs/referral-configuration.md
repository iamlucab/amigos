# Referral Configuration System

This document explains how to use the configurable referral system to manage referral bonuses in the application.

## Overview

The referral configuration system allows administrators to:

1. Set a total allocation amount for referral bonuses
2. Configure the maximum referral level depth (up to 11 levels)
3. Customize bonus amounts for specific levels
4. Automatically distribute remaining amounts to unspecified levels

## Accessing the Referral Configuration

1. Log in as an administrator
2. On the admin dashboard, you'll see the current active referral configuration
3. Click on the "Manage Configurations" button to access the configuration management page

## Creating a New Configuration

1. From the configuration management page, click on "Create New Configuration"
2. Fill in the following details:
   - **Name**: A descriptive name for the configuration
   - **Description** (optional): Additional details about this configuration
   - **Total Allocation**: The total amount to be distributed as referral bonuses
   - **Maximum Level**: The maximum depth of the referral tree (1-11)
   - **Level Bonus Amounts**: Custom amounts for specific levels (optional)

3. As you fill in the form, the preview section will show how the total allocation will be distributed

### Distribution Methods

The system supports three distribution methods:

1. **Equal Distribution**: If you don't specify any custom amounts, the total allocation will be distributed equally among all levels
   - Example: Total Allocation = ₱1000, Max Level = 5
   - Result: Level 1 = ₱200, Level 2 = ₱200, Level 3 = ₱200, Level 4 = ₱200, Level 5 = ₱200

2. **Partial Customization**: You can specify custom amounts for some levels, and the remaining amount will be distributed equally among the unspecified levels
   - Example: Total Allocation = ₱1000, Max Level = 5, Custom: Level 1 = ₱300, Level 2 = ₱200
   - Result: Level 1 = ₱300, Level 2 = ₱200, Level 3 = ₱167, Level 4 = ₱167, Level 5 = ₱166

3. **Full Customization**: You can specify custom amounts for all levels
   - Example: Total Allocation = ₱1000, Max Level = 5, Custom: Level 1 = ₱300, Level 2 = ₱250, Level 3 = ₱200, Level 4 = ₱150, Level 5 = ₱100
   - Result: All levels use the specified amounts

## Activating a Configuration

Only one configuration can be active at a time. When you create a new configuration, it automatically becomes the active one. To activate a different configuration:

1. Go to the configuration management page
2. Find the configuration you want to activate
3. Click the "Activate" button

The previously active configuration will be deactivated, and the new one will become active.

## How Referral Bonuses Are Applied

When a new member registers or a pending member is approved:

1. The system checks for the active referral configuration
2. It identifies the sponsor chain (upline) for the new member
3. It applies bonuses to each sponsor in the chain, up to the maximum level defined in the configuration
4. The bonus amounts are credited to each sponsor's wallet according to the configuration

## Commands

The system includes a command to backfill referral bonuses for existing members:

```
php artisan referrals:backfill
```

This command will apply referral bonuses to all approved members who haven't received them yet, using the currently active configuration.