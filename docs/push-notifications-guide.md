# Push Notifications Implementation Guide - E-Bili Application

This document provides a comprehensive guide for the Firebase Cloud Messaging (FCM) push notification system implemented in the E-Bili application.

## 🚀 Overview

The push notification system enables real-time communication with users across web and mobile platforms using Firebase Cloud Messaging (FCM). The system supports:

- **Device Token Management** - Automatic registration and cleanup
- **Multi-Platform Support** - iOS, Android, and Web browsers
- **Targeted Notifications** - Send to specific users, groups, or all users
- **Rich Notifications** - Custom data, actions, and platform-specific features
- **Admin Management** - Backend administration interface

## 📋 System Components

### Backend Components

#### 1. Database Schema
- **`device_tokens` table** - Stores FCM tokens and device information
- **Fields**: user_id, device_token, device_type, platform, device_id, app_version, is_active, last_used_at

#### 2. Models
- **`DeviceToken`** - Eloquent model for device token management
- **`User`** - Extended with device token relationships

#### 3. Services
- **`FirebasePushNotificationService`** - Core service for sending notifications
- **Features**: Single/batch sending, platform targeting, error handling

#### 4. Controllers
- **`PushNotificationController`** - API endpoints for token management and sending

#### 5. Routes
```php
// User Routes (Authenticated)
POST /api/notifications/register-token
POST /api/notifications/unregister-token
POST /api/notifications/test
GET  /api/notifications/tokens
DELETE /api/notifications/tokens/{id}

// Admin Routes
POST /admin/notifications/send-to-user
POST /admin/notifications/broadcast
POST /admin/notifications/cleanup-expired
```

### Frontend Components

#### 1. Mobile App (React Native)
- **`NotificationService.js`** - Firebase messaging integration
- **Features**: Token registration, message handling, permission management

#### 2. Web App (Laravel Blade)
- **Service Worker** - Background notification handling
- **JavaScript Integration** - Token registration and management

## ⚙️ Configuration

### 1. Firebase Project Setup

#### Create Firebase Project
1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Create a new project or select existing
3. Enable Cloud Messaging
4. Generate service account key

#### Download Configuration Files
- **Android**: `google-services.json` → `android/app/`
- **iOS**: `GoogleService-Info.plist` → `ios/Runner/`
- **Web**: Firebase config object
- **Server**: Service account JSON file → `storage/app/firebase-credentials.json`

### 2. Environment Configuration

```env
# Firebase Configuration
FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_CREDENTIALS_PATH=storage/app/firebase-credentials.json
FIREBASE_DATABASE_URL=https://your-project-id-default-rtdb.firebaseio.com/
FCM_SERVER_KEY=your-fcm-server-key
```

### 3. Mobile App Configuration

#### Android Setup (`android/app/build.gradle`)
```gradle
dependencies {
    implementation 'com.google.firebase:firebase-messaging:23.0.0'
    implementation 'com.google.firebase:firebase-analytics:21.0.0'
}

apply plugin: 'com.google.gms.google-services'
```

#### iOS Setup (`ios/Podfile`)
```ruby
pod 'Firebase/Messaging'
pod 'Firebase/Analytics'
```

#### Permissions

**Android** (`android/app/src/main/AndroidManifest.xml`):
```xml
<uses-permission android:name="android.permission.INTERNET" />
<uses-permission android:name="android.permission.WAKE_LOCK" />
<uses-permission android:name="android.permission.VIBRATE" />
<uses-permission android:name="com.google.android.c2dm.permission.RECEIVE" />
<uses-permission android:name="android.permission.POST_NOTIFICATIONS" />
```

**iOS** (`ios/Runner/Info.plist`):
```xml
<key>UIBackgroundModes</key>
<array>
    <string>remote-notification</string>
</array>
```

## 🔧 Implementation Details

### 1. Device Token Registration

#### Automatic Registration
```javascript
// Mobile App - Automatic on app start
await NotificationService.initialize();
```

#### Manual Registration
```javascript
// Register specific device
const tokenData = {
    device_token: 'fcm_token_here',
    device_type: 'mobile',
    platform: 'ios',
    device_id: 'unique_device_id',
    app_version: '1.0.0'
};

await api.post('/api/notifications/register-token', tokenData);
```

### 2. Sending Notifications

#### Send to Single User
```php
$pushService = new FirebasePushNotificationService();
$pushService->sendToUser(
    $userId,
    'Notification Title',
    'Notification Body',
    ['custom_data' => 'value']
);
```

#### Send to All Users
```php
$pushService->sendToAllUsers(
    'Broadcast Title',
    'Broadcast Message',
    ['type' => 'announcement']
);
```

#### Send to Specific Platform
```php
$pushService->sendToPlatform(
    'ios',
    'iOS Only Title',
    'This message is for iOS users only'
);
```

### 3. Advanced Features

#### Platform-Specific Configuration
```php
$options = [
    'android' => [
        'notification' => [
            'sound' => 'default',
            'color' => '#63189e',
            'icon' => 'notification_icon'
        ]
    ],
    'apns' => [
        'payload' => [
            'aps' => [
                'sound' => 'default',
                'badge' => 1
            ]
        ]
    ]
];

$pushService->sendToUser($userId, $title, $body, $data, $options);
```

#### Rich Notifications with Actions
```php
$data = [
    'type' => 'order_update',
    'order_id' => '12345',
    'action_url' => '/orders/12345',
    'actions' => [
        ['title' => 'View Order', 'action' => 'view'],
        ['title' => 'Track Package', 'action' => 'track']
    ]
];
```

## 📱 Mobile App Integration

### 1. Notification Handling

#### Foreground Messages
```javascript
messaging().onMessage(async remoteMessage => {
    // Show in-app notification
    Alert.alert(
        remoteMessage.notification.title,
        remoteMessage.notification.body
    );
});
```

#### Background Messages
```javascript
messaging().setBackgroundMessageHandler(async remoteMessage => {
    console.log('Background message:', remoteMessage);
    // Handle background logic
});
```

#### Notification Tap Handling
```javascript
messaging().onNotificationOpenedApp(remoteMessage => {
    // Navigate to specific screen
    navigation.navigate('OrderDetails', {
        orderId: remoteMessage.data.order_id
    });
});
```

### 2. Permission Management

#### Request Permissions
```javascript
const hasPermission = await NotificationService.requestPermission();
if (!hasPermission) {
    // Show permission explanation
    Alert.alert(
        'Notifications Disabled',
        'Enable notifications to receive important updates'
    );
}
```

#### Check Permission Status
```javascript
const isEnabled = await NotificationService.areNotificationsEnabled();
```

## 🌐 Web Push Notifications

### 1. Service Worker Setup

#### Register Service Worker (`public/sw.js`)
```javascript
importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "your-api-key",
    authDomain: "your-project.firebaseapp.com",
    projectId: "your-project-id",
    storageBucket: "your-project.appspot.com",
    messagingSenderId: "123456789",
    appId: "your-app-id"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/logo.png',
        badge: '/badge.png'
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
```

#### Frontend Integration
```javascript
// Initialize Firebase messaging
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

const messaging = getMessaging();

// Get registration token
const token = await getToken(messaging, {
    vapidKey: 'your-vapid-key'
});

// Handle foreground messages
onMessage(messaging, (payload) => {
    console.log('Foreground message:', payload);
});
```

## 🛠️ Administration

### 1. Admin Panel Integration

#### Send Notification Form
```html
<form action="{{ route('admin.notifications.broadcast') }}" method="POST">
    @csrf
    <input type="text" name="title" placeholder="Notification Title" required>
    <textarea name="body" placeholder="Notification Message" required></textarea>
    <select name="platform">
        <option value="">All Platforms</option>
        <option value="ios">iOS Only</option>
        <option value="android">Android Only</option>
        <option value="web">Web Only</option>
    </select>
    <button type="submit">Send Broadcast</button>
</form>
```

#### Device Token Management
```php
// View user's devices
$devices = DeviceToken::where('user_id', $userId)
    ->active()
    ->orderBy('last_used_at', 'desc')
    ->get();

// Cleanup expired tokens
$deletedCount = DeviceToken::cleanupExpired();
```

### 2. Notification Analytics

#### Track Delivery Status
```php
// Log notification attempts
Log::info('Notification sent', [
    'user_id' => $userId,
    'title' => $title,
    'platform' => $platform,
    'success' => $result
]);
```

#### Monitor Token Health
```php
// Check for invalid tokens
$invalidTokens = DeviceToken::where('is_active', false)
    ->where('updated_at', '>', now()->subDays(7))
    ->count();
```

## 🔍 Testing

### 1. Test Notification Endpoint

#### Send Test Notification
```bash
curl -X POST http://localhost:8000/api/notifications/test \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"device_token": "FCM_TOKEN_HERE"}'
```

#### Test Response
```json
{
    "success": true,
    "message": "Test notification sent successfully"
}
```

### 2. Mobile App Testing

#### Debug Mode
```javascript
// Enable debug logging
console.log('FCM Token:', await messaging().getToken());
console.log('Permission Status:', await messaging().hasPermission());
```

#### Test Scenarios
- App in foreground
- App in background
- App completely closed
- Different notification types
- Platform-specific features

## 🚨 Troubleshooting

### Common Issues

#### 1. Token Registration Failed
**Symptoms**: Device tokens not being saved
**Solutions**:
- Check Firebase credentials
- Verify API endpoint authentication
- Ensure proper permissions

#### 2. Notifications Not Received
**Symptoms**: Messages sent but not delivered
**Solutions**:
- Verify FCM token validity
- Check device notification permissions
- Confirm Firebase project configuration

#### 3. iOS Notifications Not Working
**Symptoms**: Android works, iOS doesn't
**Solutions**:
- Verify APNs certificates
- Check iOS app bundle ID
- Ensure proper entitlements

#### 4. Web Push Not Working
**Symptoms**: Mobile works, web doesn't
**Solutions**:
- Verify VAPID keys
- Check service worker registration
- Ensure HTTPS connection

### Debug Commands

```bash
# Check Firebase credentials
php artisan tinker
>>> config('services.firebase')

# Test notification service
>>> $service = new App\Services\FirebasePushNotificationService();
>>> $service->sendTestNotification('FCM_TOKEN_HERE');

# Check device tokens
>>> App\Models\DeviceToken::active()->count();
```

## 📈 Performance Optimization

### 1. Batch Processing
```php
// Send to multiple users efficiently
$userIds = [1, 2, 3, 4, 5];
$pushService->sendToUsers($userIds, $title, $body);
```

### 2. Queue Jobs
```php
// Queue notification jobs for better performance
dispatch(new SendPushNotificationJob($userId, $title, $body));
```

### 3. Token Cleanup
```php
// Schedule regular cleanup
// In app/Console/Kernel.php
$schedule->call(function () {
    DeviceToken::cleanupExpired();
})->daily();
```

## 🔒 Security Considerations

### 1. Token Protection
- Store FCM tokens securely
- Implement token rotation
- Validate token ownership

### 2. Message Content
- Sanitize notification content
- Avoid sensitive data in messages
- Implement content filtering

### 3. Rate Limiting
- Limit notification frequency per user
- Implement admin sending limits
- Monitor for abuse

## 📊 Monitoring & Analytics

### 1. Delivery Tracking
```php
// Track notification metrics
$metrics = [
    'sent' => $totalSent,
    'delivered' => $delivered,
    'failed' => $failed,
    'platforms' => $platformBreakdown
];
```

### 2. User Engagement
- Track notification open rates
- Monitor user preferences
- Analyze optimal sending times

---

**Last Updated**: January 2025  
**Version**: 1.0.0  
**Author**: E-Bili Development Team

## 📞 Support

For technical support with push notifications:
- Check Firebase Console for delivery status
- Review Laravel logs for backend errors
- Use mobile app debug mode for client issues
- Monitor device token health regularly