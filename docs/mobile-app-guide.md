# Building a Mobile App Version of E-bili

This guide provides instructions on how to convert the E-bili web application into a native mobile app using React Native.

## Why React Native?

React Native allows you to build mobile apps using JavaScript and React. It offers several advantages:

1. **Cross-platform development**: Build for both iOS and Android with a single codebase
2. **Native performance**: Uses native components for better performance than hybrid solutions
3. **Large community and ecosystem**: Access to many libraries and resources
4. **Familiar development experience**: If you know React for web, the transition is easier
5. **Hot reloading**: See changes instantly during development

## Prerequisites

Before starting, ensure you have the following installed:

- Node.js (v14 or newer)
- npm or Yarn
- JDK 11 or newer (for Android development)
- Android Studio (for Android development)
- Xcode (for iOS development, macOS only)
- CocoaPods (for iOS dependencies, macOS only)
- React Native CLI

## Setting Up the Project

1. Install React Native CLI:
   ```bash
   npm install -g react-native-cli
   ```

2. Create a new React Native project:
   ```bash
   npx react-native init EbiliMobile
   ```

3. Navigate to the project directory:
   ```bash
   cd EbiliMobile
   ```

4. Install necessary dependencies:
   ```bash
   npm install @react-navigation/native @react-navigation/stack axios react-native-paper react-native-vector-icons react-native-gesture-handler react-native-safe-area-context @react-native-async-storage/async-storage
   ```

## Project Structure

Organize your project with the following structure:

```
EbiliMobile/
├── src/
│   ├── api/              # API calls to Laravel backend
│   ├── assets/           # Images, fonts, etc.
│   ├── components/       # Reusable components
│   ├── context/          # Context providers (auth, etc.)
│   ├── navigation/       # Navigation configuration
│   ├── screens/          # Screen components
│   ├── styles/           # Global styles
│   ├── utils/            # Utility functions
│   └── App.js            # Main component
├── android/              # Android-specific files
└── ios/                  # iOS-specific files
```

## Connecting to the Laravel Backend

1. Create an API service in `src/api/api.js`:

```javascript
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_URL = 'https://your-laravel-api-url.com/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add token to requests
api.interceptors.request.use(
  async (config) => {
    const token = await AsyncStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

export default api;
```

2. Create API functions for each endpoint:

```javascript
// src/api/auth.js
import api from './api';
import AsyncStorage from '@react-native-async-storage/async-storage';

export const login = async (email, password) => {
  try {
    const response = await api.post('/login', { email, password });
    await AsyncStorage.setItem('token', response.data.token);
    return response.data;
  } catch (error) {
    throw error.response.data;
  }
};

export const logout = async () => {
  try {
    await api.post('/logout');
    await AsyncStorage.removeItem('token');
  } catch (error) {
    throw error.response.data;
  }
};

// src/api/loans.js
import api from './api';

export const getLoans = async () => {
  try {
    const response = await api.get('/loans');
    return response.data;
  } catch (error) {
    throw error.response.data;
  }
};

export const requestLoan = async (loanData) => {
  try {
    const response = await api.post('/loan/request', loanData);
    return response.data;
  } catch (error) {
    throw error.response.data;
  }
};

export const makePayment = async (paymentId, paymentData) => {
  try {
    const response = await api.post(`/loan-payments/${paymentId}/pay-now`, paymentData);
    return response.data;
  } catch (error) {
    throw error.response.data;
  }
};
```

## Laravel API Endpoints

You'll need to create API endpoints in your Laravel application. Here's how to set up the necessary routes:

1. Create API routes in `routes/api.php`:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LoanController;
use App\Http\Controllers\API\PaymentController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User profile
    Route::get('/user', [AuthController::class, 'user']);
    
    // Loans
    Route::get('/loans', [LoanController::class, 'index']);
    Route::post('/loan/request', [LoanController::class, 'requestLoan']);
    Route::get('/loans/{loan}', [LoanController::class, 'show']);
    
    // Payments
    Route::post('/loan-payments/{id}/pay-now', [PaymentController::class, 'payNow']);
    Route::get('/loan-payments/{id}/payment-modal', [PaymentController::class, 'showPaymentModal']);
});
```

2. Create API controllers that return JSON responses.

## Building the Mobile UI

1. Create screens for each major feature:
   - Login/Register
   - Dashboard
   - Loan Request
   - Loan History
   - Loan Details
   - Payment Screen

2. Use React Navigation for navigation between screens:

```javascript
// src/navigation/AppNavigator.js
import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { useAuth } from '../context/AuthContext';

// Screens
import LoginScreen from '../screens/LoginScreen';
import RegisterScreen from '../screens/RegisterScreen';
import DashboardScreen from '../screens/DashboardScreen';
import LoanHistoryScreen from '../screens/LoanHistoryScreen';
import LoanDetailsScreen from '../screens/LoanDetailsScreen';
import RequestLoanScreen from '../screens/RequestLoanScreen';
import PaymentScreen from '../screens/PaymentScreen';

const Stack = createStackNavigator();

const AppNavigator = () => {
  const { isAuthenticated } = useAuth();

  return (
    <NavigationContainer>
      <Stack.Navigator>
        {!isAuthenticated ? (
          // Auth screens
          <>
            <Stack.Screen name="Login" component={LoginScreen} />
            <Stack.Screen name="Register" component={RegisterScreen} />
          </>
        ) : (
          // App screens
          <>
            <Stack.Screen name="Dashboard" component={DashboardScreen} />
            <Stack.Screen name="LoanHistory" component={LoanHistoryScreen} />
            <Stack.Screen name="LoanDetails" component={LoanDetailsScreen} />
            <Stack.Screen name="RequestLoan" component={RequestLoanScreen} />
            <Stack.Screen name="Payment" component={PaymentScreen} />
          </>
        )}
      </Stack.Navigator>
    </NavigationContainer>
  );
};

export default AppNavigator;
```

## Building and Publishing

### Android

1. Generate a signing key:
   ```bash
   keytool -genkeypair -v -storetype PKCS12 -keystore my-release-key.keystore -alias my-key-alias -keyalg RSA -keysize 2048 -validity 10000
   ```

2. Set up Gradle variables in `android/gradle.properties`:
   ```
   MYAPP_RELEASE_STORE_FILE=my-release-key.keystore
   MYAPP_RELEASE_KEY_ALIAS=my-key-alias
   MYAPP_RELEASE_STORE_PASSWORD=*****
   MYAPP_RELEASE_KEY_PASSWORD=*****
   ```

3. Build the APK:
   ```bash
   cd android
   ./gradlew assembleRelease
   ```

4. The APK will be generated at `android/app/build/outputs/apk/release/app-release.apk`

5. Publish to Google Play Store:
   - Create a developer account
   - Create a new application
   - Upload the APK
   - Fill in store listing details
   - Submit for review

### iOS (macOS only)

1. Open the project in Xcode:
   ```bash
   npx react-native run-ios
   ```

2. In Xcode, select Product > Archive

3. After archiving, click "Distribute App"

4. Follow the steps to upload to App Store Connect

5. Submit for review in App Store Connect

## Native Features Integration

### Push Notifications

1. Install the required packages:
   ```bash
   npm install @react-native-firebase/app @react-native-firebase/messaging
   ```

2. Follow the Firebase setup for each platform

3. Implement notification handling:
   ```javascript
   import messaging from '@react-native-firebase/messaging';

   // Request permission
   async function requestUserPermission() {
     const authStatus = await messaging().requestPermission();
     return authStatus === messaging.AuthorizationStatus.AUTHORIZED ||
            authStatus === messaging.AuthorizationStatus.PROVISIONAL;
   }

   // Get FCM token
   async function getFcmToken() {
     const fcmToken = await messaging().getToken();
     console.log('FCM Token:', fcmToken);
     // Send this token to your server
   }

   // Handle notifications
   messaging().onMessage(async remoteMessage => {
     console.log('Foreground Message:', remoteMessage);
     // Display notification
   });

   messaging().setBackgroundMessageHandler(async remoteMessage => {
     console.log('Background Message:', remoteMessage);
   });
   ```

### Camera (for payment proof)

1. Install the camera package:
   ```bash
   npm install react-native-camera
   ```

2. Create a camera component:
   ```javascript
   import React, { useState } from 'react';
   import { View, TouchableOpacity, StyleSheet, Text } from 'react-native';
   import { RNCamera } from 'react-native-camera';

   const CameraComponent = ({ onPictureTaken }) => {
     const takePicture = async (camera) => {
       if (camera) {
         const options = { quality: 0.5, base64: true };
         const data = await camera.takePictureAsync(options);
         onPictureTaken(data.uri);
       }
     };

     return (
       <View style={styles.container}>
         <RNCamera
           style={styles.preview}
           type={RNCamera.Constants.Type.back}
           captureAudio={false}
         >
           {({ camera }) => (
             <View style={styles.buttonContainer}>
               <TouchableOpacity onPress={() => takePicture(camera)} style={styles.capture}>
                 <Text style={styles.buttonText}>Take Photo</Text>
               </TouchableOpacity>
             </View>
           )}
         </RNCamera>
       </View>
     );
   };

   const styles = StyleSheet.create({
     container: {
       flex: 1,
     },
     preview: {
       flex: 1,
       justifyContent: 'flex-end',
       alignItems: 'center',
     },
     buttonContainer: {
       flex: 0,
       flexDirection: 'row',
       justifyContent: 'center',
     },
     capture: {
       flex: 0,
       backgroundColor: '#fff',
       borderRadius: 5,
       padding: 15,
       paddingHorizontal: 20,
       alignSelf: 'center',
       margin: 20,
     },
     buttonText: {
       fontSize: 14,
     },
   });

   export default CameraComponent;
   ```

## Testing

1. Test on real devices for both Android and iOS
2. Use React Native's debugging tools
3. Implement unit tests with Jest
4. Use React Native Testing Library for component tests

## Conclusion

Converting your Laravel application to a React Native mobile app requires creating a proper API in your Laravel backend and building a mobile frontend that consumes this API. The process involves:

1. Setting up a React Native project
2. Creating API endpoints in Laravel
3. Building the mobile UI components
4. Implementing authentication and data fetching
5. Adding native features like camera and push notifications
6. Testing thoroughly on real devices
7. Building and publishing to app stores

This approach gives you a true native mobile experience while leveraging your existing Laravel backend.