# Admin Notification Management System Guide

## Overview

The E-Bili Admin Notification Management System provides comprehensive tools for administrators to manage both push notifications and SMS text blasting campaigns. This system integrates Firebase Cloud Messaging (FCM) for push notifications and Semaphore SMS API for text messaging.

## Features

### Push Notifications
- **Firebase Integration**: Complete FCM setup with device token management
- **Multi-Platform Support**: Android and iOS device compatibility
- **Targeted Broadcasting**: Send to all users, specific roles, or individual users
- **Rich Notifications**: Support for titles, bodies, images, and custom data
- **Scheduling**: Schedule notifications for future delivery
- **Analytics**: Track delivery rates and engagement metrics

### SMS Text Blasting
- **Semaphore Integration**: Philippines-based SMS service provider
- **Bulk Messaging**: Send to multiple recipients simultaneously
- **Cost Tracking**: Real-time balance checking and cost estimation
- **Campaign Management**: Organize SMS campaigns with names and notes
- **Delivery Tracking**: Monitor sent, pending, and failed messages
- **Mobile Number Formatting**: Automatic Philippines mobile number validation

### Device Management
- **Token Registry**: Comprehensive device token database
- **Platform Analytics**: Android vs iOS device statistics
- **Active/Inactive Tracking**: Monitor device engagement
- **Bulk Operations**: Mass activate, deactivate, or delete tokens
- **Test Notifications**: Send test messages to specific devices

## System Architecture

### Backend Components

#### Services
- **[`FirebasePushNotificationService`](../app/Services/FirebasePushNotificationService.php)**: Core push notification service
- **[`SemaphoreSmsService`](../app/Services/SemaphoreSmsService.php)**: SMS messaging service

#### Controllers
- **[`NotificationController`](../app/Http/Controllers/Admin/NotificationController.php)**: Admin interface controller
- **[`PushNotificationController`](../app/Http/Controllers/PushNotificationController.php)**: API endpoints for mobile app

#### Models
- **[`DeviceToken`](../app/Models/DeviceToken.php)**: FCM device token management
- **[`SmsLog`](../app/Models/SmsLog.php)**: SMS campaign tracking
- **[`User`](../app/Models/User.php)**: Enhanced with notification preferences

#### Database Schema
- **`device_tokens`**: Stores FCM tokens with device metadata
- **`sms_logs`**: Tracks SMS campaigns and delivery statistics

### Frontend Components

#### Admin Views
- **[Dashboard](../resources/views/admin/notifications/index.blade.php)**: Overview with statistics
- **[Push Notifications](../resources/views/admin/notifications/push.blade.php)**: Send push notifications
- **[SMS Blasting](../resources/views/admin/notifications/sms.blade.php)**: Send SMS campaigns
- **[SMS History](../resources/views/admin/notifications/sms-history.blade.php)**: Campaign history and analytics
- **[Device Tokens](../resources/views/admin/notifications/device-tokens.blade.php)**: Device management

#### Mobile App Integration
- **[NotificationService](../EbiliMobile/src/utils/NotificationService.js)**: React Native FCM integration
- **[App.js](../EbiliMobile/src/App.js)**: Notification initialization

## Installation & Setup

### 1. Environment Configuration

Add the following variables to your `.env` file:

```env
# Firebase Configuration
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_PRIVATE_KEY_ID=your-private-key-id
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nYour-Private-Key\n-----END PRIVATE KEY-----\n"
FIREBASE_CLIENT_EMAIL=your-service-account@your-project.iam.gserviceaccount.com
FIREBASE_CLIENT_ID=your-client-id
FIREBASE_AUTH_URI=https://accounts.google.com/o/oauth2/auth
FIREBASE_TOKEN_URI=https://oauth2.googleapis.com/token
FIREBASE_AUTH_PROVIDER_CERT_URL=https://www.googleapis.com/oauth2/v1/certs
FIREBASE_CLIENT_CERT_URL=https://www.googleapis.com/robot/v1/metadata/x509/your-service-account%40your-project.iam.gserviceaccount.com

# Semaphore SMS Configuration
SEMAPHORE_API_KEY=your-semaphore-api-key
SEMAPHORE_SENDER_NAME=E-Bili
```

### 2. Install Dependencies

```bash
# Install Firebase Admin SDK
composer require kreait/firebase-php

# Install Guzzle HTTP Client (if not already installed)
composer require guzzlehttp/guzzle
```

### 3. Run Database Migrations

```bash
# Run specific migrations
php artisan migrate --path=database/migrations/2025_07_30_180255_create_device_tokens_table.php
php artisan migrate --path=database/migrations/2025_07_30_182526_create_sms_logs_table.php
```

### 4. Configure Routes

The following routes are automatically registered:

```php
// Admin Notification Routes
Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/push', [NotificationController::class, 'push'])->name('push');
    Route::post('/push/send', [NotificationController::class, 'sendPush'])->name('send.push');
    Route::get('/sms', [NotificationController::class, 'sms'])->name('sms');
    Route::post('/sms/send', [NotificationController::class, 'sendSms'])->name('send.sms');
    Route::get('/sms/history', [NotificationController::class, 'smsHistory'])->name('sms.history');
    Route::get('/device-tokens', [NotificationController::class, 'deviceTokens'])->name('device.tokens');
    // Additional API routes...
});
```

## Usage Guide

### Admin Dashboard Access

Navigate to `/admin/notifications` to access the main dashboard. This provides:

- **Statistics Overview**: Total users, active devices, recent campaigns
- **Quick Actions**: Direct links to send notifications or view history
- **Recent Activity**: Latest push notifications and SMS campaigns

### Sending Push Notifications

1. **Access Push Interface**: Go to `/admin/notifications/push`
2. **Select Target Audience**:
   - All Users: Send to all registered devices
   - All Members: Send to verified members only
   - By Role: Target specific user roles (Admin, Staff, Member)
   - Specific Users: Choose individual recipients
3. **Compose Message**:
   - Title: Notification headline
   - Message: Main notification content
   - Image URL: Optional notification image
   - Action URL: Deep link or web URL
4. **Schedule (Optional)**: Set future delivery time
5. **Preview & Send**: Review notification preview before sending

### SMS Text Blasting

1. **Access SMS Interface**: Go to `/admin/notifications/sms`
2. **Campaign Setup**:
   - Campaign Name: Internal identifier
   - Target Audience: Similar to push notifications
3. **Message Composition**:
   - Message: SMS content (max 1000 characters)
   - Character Counter: Real-time count with SMS part calculation
   - Cost Estimation: Automatic cost calculation
4. **Preview**: SMS bubble preview showing sender and content
5. **Send Campaign**: Bulk SMS delivery with progress tracking

### Device Token Management

1. **Access Device Management**: Go to `/admin/notifications/device-tokens`
2. **View Device List**: See all registered devices with user information
3. **Filter & Search**: Find specific devices or users
4. **Bulk Operations**:
   - Test notifications to multiple devices
   - Activate/deactivate tokens in bulk
   - Clean up inactive tokens
5. **Individual Actions**:
   - Send test notifications
   - View full token details
   - Activate/deactivate specific tokens

### Campaign History & Analytics

#### Push Notification History
- View sent notifications with delivery statistics
- Filter by date, status, or target audience
- Retry failed notifications
- Export campaign data

#### SMS Campaign History
- Track SMS campaigns with cost analysis
- View delivery rates and failed messages
- Retry failed SMS messages
- Campaign performance metrics

## API Endpoints

### Mobile App Integration

#### Register Device Token
```http
POST /api/push-notifications/register
Content-Type: application/json
Authorization: Bearer {token}

{
    "token": "fcm-device-token",
    "platform": "android|ios",
    "device_name": "Device Name",
    "device_model": "Device Model",
    "app_version": "1.0.0"
}
```

#### Update Token Status
```http
PUT /api/push-notifications/token/{id}
Content-Type: application/json
Authorization: Bearer {token}

{
    "is_active": true
}
```

### Admin API Endpoints

#### Send Test Notification
```http
POST /admin/notifications/device-tokens/{id}/test
Content-Type: application/json
X-CSRF-TOKEN: {csrf-token}
```

#### Check SMS Balance
```http
GET /admin/notifications/sms/balance
```

#### Send Test SMS
```http
POST /admin/notifications/test/sms
Content-Type: application/json
X-CSRF-TOKEN: {csrf-token}

{
    "mobile_number": "09171234567",
    "message": "Test message"
}
```

## Configuration Options

### Firebase Configuration

The Firebase service is configured in [`config/services.php`](../config/services.php):

```php
'firebase' => [
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'),
    'private_key' => env('FIREBASE_PRIVATE_KEY'),
    'client_email' => env('FIREBASE_CLIENT_EMAIL'),
    'client_id' => env('FIREBASE_CLIENT_ID'),
    'auth_uri' => env('FIREBASE_AUTH_URI'),
    'token_uri' => env('FIREBASE_TOKEN_URI'),
    'auth_provider_x509_cert_url' => env('FIREBASE_AUTH_PROVIDER_CERT_URL'),
    'client_x509_cert_url' => env('FIREBASE_CLIENT_CERT_URL'),
],
```

### Semaphore SMS Configuration

```php
'semaphore' => [
    'api_key' => env('SEMAPHORE_API_KEY'),
    'sender_name' => env('SEMAPHORE_SENDER_NAME', 'E-Bili'),
    'base_url' => 'https://api.semaphore.co',
],
```

## Security Considerations

### Authentication & Authorization
- All admin routes require authentication
- Role-based access control for admin functions
- CSRF protection on all form submissions
- API token validation for mobile endpoints

### Data Protection
- Device tokens are encrypted in database
- SMS logs include privacy-compliant data only
- User targeting respects privacy settings
- Secure API key storage in environment variables

### Rate Limiting
- API endpoints have rate limiting enabled
- Bulk operations include progress tracking
- Failed delivery retry mechanisms
- Automatic cleanup of inactive tokens

## Troubleshooting

### Common Issues

#### Firebase Connection Issues
1. Verify Firebase credentials in `.env`
2. Check Firebase project permissions
3. Ensure service account has FCM permissions
4. Validate JSON key format

#### SMS Delivery Problems
1. Check Semaphore API key validity
2. Verify account balance
3. Validate mobile number format (Philippines: 09XXXXXXXXX)
4. Review SMS content for restricted keywords

#### Device Token Issues
1. Ensure mobile app has FCM properly configured
2. Check device permissions for notifications
3. Verify token registration API calls
4. Clean up expired or invalid tokens

### Debug Mode

Enable debug logging by setting `APP_DEBUG=true` in `.env`. This will provide detailed error messages and API response logging.

### Log Files

Monitor the following log files:
- `storage/logs/laravel.log`: General application logs
- Firebase SDK logs: Check for FCM delivery status
- SMS API logs: Semaphore API responses

## Performance Optimization

### Database Optimization
- Index device tokens by user_id and platform
- Regular cleanup of old SMS logs
- Optimize queries for large user bases
- Use database queues for bulk operations

### Caching
- Cache user statistics for dashboard
- Store SMS balance with TTL
- Cache device token counts
- Implement Redis for session management

### Queue Management
- Use Laravel queues for bulk notifications
- Implement job batching for large campaigns
- Set up queue workers for background processing
- Monitor queue performance and failures

## Monitoring & Analytics

### Key Metrics
- Push notification delivery rates
- SMS campaign success rates
- Device token registration trends
- User engagement with notifications

### Reporting
- Daily/weekly/monthly campaign reports
- Cost analysis for SMS campaigns
- Device platform distribution
- User notification preferences

## Maintenance

### Regular Tasks
- Clean up inactive device tokens (monthly)
- Archive old SMS logs (quarterly)
- Update Firebase SDK (as needed)
- Monitor API rate limits and costs

### Backup Procedures
- Regular database backups including notification data
- Export campaign history for compliance
- Backup Firebase service account keys
- Document API key rotation procedures

## Support & Documentation

### Additional Resources
- [Firebase Cloud Messaging Documentation](https://firebase.google.com/docs/cloud-messaging)
- [Semaphore SMS API Documentation](https://docs.semaphore.co/)
- [Laravel Notification Documentation](https://laravel.com/docs/notifications)

### Contact Information
For technical support or questions about the notification system, contact the development team or refer to the project documentation.

---

*Last Updated: July 30, 2025*
*Version: 1.0.0*