# CHECKER APP MIGRATION GUIDE: Flutter to React Native

## Migration Checklist & Implementation Points

### 1. PROJECT INITIALIZATION

**Setup Tasks:**
- [ ] Create Expo TypeScript project: `npx create-expo-app@latest jetty-checker-rn --template expo-template-blank-typescript`
- [ ] Install navigation packages: `@react-navigation/native`, `@react-navigation/stack`
- [ ] Install state management: `@reduxjs/toolkit`, `react-redux`
- [ ] Install HTTP client: `axios`
- [ ] Install storage: `@react-native-async-storage/async-storage`
- [ ] Install QR scanner: `expo-camera`, `expo-barcode-scanner`
- [ ] Configure TypeScript with path aliases in `tsconfig.json`
- [ ] Configure Babel with `babel-plugin-module-resolver` for imports
- [ ] Set up `app.json` with app name, bundle identifiers, permissions, splash screen, icon

**Critical Configuration Points:**
- API base URL in `app.json` extra config: `https://unfurling.ninja/api`
- **Platform**: Android ONLY (optimized for checker devices at gates)
- Camera permissions: Required for QR scanning
- App icon (1024x1024), Splash screen with blue theme (#0066cc)
- Minimum SDK: Android 21 (Lollipop)

**Key Difference from Customer App:**
- This is an Android-only app (no iOS support needed)
- Simpler architecture (only 4 main screens)
- No payment integration
- No image uploads
- Focus on QR scanning and ticket verification

---

### 2. ARCHITECTURE & FOLDER STRUCTURE

**Create Directory Structure:**
```
src/
├── navigation/          # Navigation configuration
│   ├── RootNavigator.tsx
│   └── AuthNavigator.tsx
├── screens/            # Screen components
│   ├── SplashScreen.tsx
│   ├── LoginScreen.tsx
│   ├── HomeScreen.tsx
│   └── QRScannerScreen.tsx
├── components/         # Reusable components
│   ├── common/        # Generic UI components
│   └── ticket/        # Ticket verification components
├── store/             # Redux store and slices
│   └── slices/        # 2 slices: auth, verification
├── services/          # API services layer
├── types/             # TypeScript definitions
├── utils/             # Helper functions
└── theme/             # Design system
```

**Key Architectural Decisions:**
- Use Redux Toolkit for state (replaces Flutter Provider)
- Use React Navigation for routing (replaces Flutter Navigator)
- Use Axios for HTTP (replaces Flutter http package)
- Use AsyncStorage for persistence (replaces shared_preferences)
- Use Expo Camera/Barcode Scanner for QR scanning (replaces mobile_scanner)
- Simpler state management than Customer App (only auth and verification state)
- No complex forms - just login and scanning

---

### 3. TYPE DEFINITIONS

**Create TypeScript Interfaces for:**

**Models (`types/models.ts`):**
- Checker: id, name, email, roleId (must be 5), branchId, ferryBoatId, branchName, ferryBoatName
- Ticket: id, ticketNo, ticketDate, fromBranch, toBranch, ferryName, ferryTime, totalAmount, items[], verifiedAt, verifiedBy
- TicketItem: itemName, quantity, amount, vehicleNo (optional)

**API Types (`types/api.ts`):**
- ApiResponse<T>: Generic wrapper with data, message, error
- LoginResponse: token, checker
- VerifyTicketResponse: success, ticket, message

**Navigation Types (`types/navigation.ts`):**
- AuthStackParamList: Splash, Login
- MainStackParamList: Home, QRScanner
- Type-safe navigation props

**Important Validation:**
- Checker model MUST have roleId === 5
- Only users with role_id = 5 can login to this app
- Backend validates this during authentication

---

### 4. SERVICES LAYER

**API Service (`services/api.ts`):**
- Create Axios instance with base URL from app config
- Set timeout to 15 seconds (shorter than customer app)
- Add request interceptor to inject Bearer token from AsyncStorage
- Add response interceptor to handle 401 errors (logout checker)
- Implement error handling: Network errors, server errors, timeout errors
- Methods: get<T>, post<T>
- No need for put, delete, or upload methods (not used in checker app)

**Storage Service (`services/storageService.ts`):**
- Wrap AsyncStorage with type-safe methods
- Methods: saveToken, getToken, clearToken, saveChecker, getChecker, isLoggedIn, clearAll
- Use keys: 'auth_token', 'checker_data'
- JSON stringify/parse for checker object
- Track daily verification count locally: 'daily_verifications'

**Auth Service (`services/authService.ts`):**
- login: POST `/checker/login` with email, password → Validate roleId === 5, Save token and checker
- logout: POST `/checker/logout` → Clear storage
- getProfile: GET `/checker/profile` → Get checker details with branch/ferry info
- isLoggedIn: Check if token exists in storage

**Critical Login Validation:**
- After successful login response, check if checker.roleId === 5
- If not, throw error: "Invalid credentials. Only checkers can login."
- Do not save token if roleId is not 5
- This prevents operators/admins from accessing checker app

**Verification Service (`services/verificationService.ts`):**
- verifyTicket: POST `/checker/verify-ticket` with ticketId (integer)
- Response contains: success (boolean), ticket (full ticket object), message (string)
- Backend updates: verified_at = current timestamp, checker_id = current checker
- Backend validates: Ticket exists, ticket not already verified
- parseTicketId: Extract ticket ID from QR code data (may be plain number or encoded)

---

### 5. STATE MANAGEMENT (REDUX TOOLKIT)

**Store Configuration (`store/index.ts`):**
- Combine 2 reducers: auth, verification
- Configure middleware (standard settings)
- Export typed hooks: useAppDispatch, useAppSelector
- Export RootState and AppDispatch types

**Auth Slice (`store/slices/authSlice.ts`):**

**State:**
- checker: Checker | null
- isAuthenticated: boolean
- isLoading: boolean
- error: string | null

**Async Thunks:**
- checkAuthStatus: Check if token exists, load checker from storage, verify roleId === 5
- login: Call authService.login, validate roleId, handle success/error
- logout: Call authService.logout, clear state
- getProfile: Call authService.getProfile, update checker in state

**Reducers:**
- setChecker: Manually set checker and authenticated flag
- clearError: Reset error to null

**Critical Validation:**
- In login thunk: After receiving response, check if checker.roleId === 5
- If not, reject with error and don't set authenticated state
- In checkAuthStatus: Verify stored checker has roleId === 5, else logout

**Verification Slice (`store/slices/verificationSlice.ts`):**

**State:**
- verifiedToday: number (count of tickets verified today)
- lastVerifiedTicket: Ticket | null
- isVerifying: boolean
- error: string | null

**Async Thunks:**
- verifyTicket: Call verificationService.verifyTicket with ticketId
- Handle success: Increment verifiedToday counter, store lastVerifiedTicket
- Handle error: Show appropriate error message (already verified, not found, etc.)

**Reducers:**
- incrementVerifiedCount: Add 1 to verifiedToday
- setLastVerifiedTicket: Store most recently verified ticket
- resetDailyCount: Reset verifiedToday to 0 (call at midnight or on new day)
- clearError: Reset error to null

**Local Persistence:**
- Save verifiedToday count to AsyncStorage
- Save with date key to reset on new day
- Load on app start

---

### 6. NAVIGATION STRUCTURE

**Root Navigator (`navigation/RootNavigator.tsx`):**
- Check auth status on mount using checkAuthStatus thunk
- Conditionally render LoginScreen or HomeScreen based on isAuthenticated
- Show splash screen while checking auth status
- No complex navigator nesting needed (app is simple)

**Auth Navigator (`navigation/AuthNavigator.tsx`):**
- Stack navigator with 2 screens:
  1. SplashScreen (initial route)
  2. LoginScreen
- No header on both screens
- Blue color scheme

**Main Navigator:**
- Stack navigator with 2 screens:
  1. HomeScreen (initial route)
  2. QRScannerScreen
- Header on both screens
- Back button from scanner to home
- Blue color scheme

**Simple Navigation Flow:**
```
Splash → Check Auth
  ├─ Not Logged In → Login → Home
  └─ Logged In → Home

Home → Scanner → Verify → Home (with success message)
```

---

### 7. THEME SYSTEM

**Colors (`theme/colors.ts`):**
- Primary colors: primaryColor (#0066cc), primaryDark (#004d99), primaryLight (#3385ff)
- Background colors: backgroundColor (#F5F5F5), cardBackground (#FFFFFF)
- Text colors: textPrimary (#212121), textSecondary (#757575), textWhite (#FFFFFF)
- Status colors: success (#4CAF50), error (#F44336), warning (#FF9800)
- Scanner overlay: overlayDark (rgba(0, 0, 0, 0.6))

**Typography (`theme/typography.ts`):**
- Font sizes: sm (12), base (14), lg (16), xl (18), 2xl (20), 3xl (24), 4xl (32), 5xl (48)
- Font weights: regular (400), medium (500), semibold (600), bold (700)

**Spacing (`theme/spacing.ts`):**
- Scale: xs (4), sm (8), md (12), lg (16), xl (20), 2xl (24), 3xl (32), 4xl (48)

**Scanner Styles:**
- Define scanner frame dimensions: 250x250dp
- Scanner frame border: 3dp white with rounded corners
- Overlay: semi-transparent black (rgba(0,0,0,0.6))
- Scanner active area: transparent center square

---

### 8. COMMON COMPONENTS

**Button Component (`components/common/Button.tsx`):**
- Props: text, onPress, variant (primary/secondary), size (md/lg), disabled, loading, fullWidth, icon
- Styles: Blue theme colors, handle disabled state, show ActivityIndicator when loading
- Touch feedback with opacity

**Input Component (`components/common/Input.tsx`):**
- Props: label, value, onChangeText, placeholder, secureTextEntry, error, leftIcon
- Styles: Border, padding, theme colors, error state (red border)
- Show error message below input
- Simpler than customer app (no multiline, no right icon)

**Card Component (`components/common/Card.tsx`):**
- Props: children, style
- Styles: White background, rounded corners, shadow/elevation
- Used for displaying ticket details

**Loading Component (`components/common/Loading.tsx`):**
- Full-screen centered ActivityIndicator
- Blue color for indicator
- Semi-transparent overlay

**StatCard Component (`components/common/StatCard.tsx`):**
- Props: title, value, color, icon
- Display statistics on home screen
- Large number display with label
- Color-coded background

---

### 9. TICKET COMPONENTS

**TicketDetailsModal (`components/ticket/TicketDetailsModal.tsx`):**
- Props: visible, ticket, onClose, isSuccess (verification status)
- Modal overlay showing ticket information
- Display ticket details: ticketNo, from, to, ferry, time, amount
- Show items breakdown list
- Show verification status:
  - If just verified: Green "Verified" badge with checkmark
  - If already verified: Orange "Already Verified" badge with previous checker name
- Close button at bottom
- Animate entry/exit

**TicketDetailRow (`components/ticket/TicketDetailRow.tsx`):**
- Props: label, value
- Simple row displaying label-value pair
- Used inside ticket details modal
- Consistent styling across all detail rows

**VerificationBadge (`components/ticket/VerificationBadge.tsx`):**
- Props: status (success/already_verified/error), message
- Colored badge with icon
- Green for success, orange for already verified, red for errors
- Icon based on status: checkmark, info, error

---

### 10. SCREEN IMPLEMENTATIONS

**SplashScreen (`screens/SplashScreen.tsx`):**
- Display app logo/icon centered
- App title: "Jetty Checker"
- Subtitle: "Verify ferry tickets"
- Blue gradient background
- Dispatch checkAuthStatus on mount
- Navigate to Login or Home based on auth state
- 2-second minimum display time

**LoginScreen (`screens/LoginScreen.tsx`):**

**UI Elements:**
- Large app icon at top
- Title: "Checker Login"
- Subtitle: "Verify ferry tickets"
- Email input field (email keyboard type)
- Password input field (secure entry)
- Login button (full width, primary color)
- Loading spinner during login

**Implementation:**
- Form validation: Both fields required, email format check
- Login button disabled until both fields filled
- On press: Dispatch login thunk with credentials
- Show error messages from Redux state below button
- Error examples:
  - "Invalid credentials. Only checkers can login." (if roleId !== 5)
  - "Incorrect email or password"
  - "Network error. Please check your connection."
- On success: Auto-navigate to Home (auth state changes)
- Clear error on unmount or when typing

**Critical Security:**
- Only allow login if backend returns roleId === 5
- Show specific error message if non-checker tries to login
- Don't show different error messages that could reveal valid emails

**HomeScreen (`screens/HomeScreen.tsx`):**

**UI Structure:**
- Header section:
  - Checker name and branch/ferry assignment
  - Profile icon
  - Logout button in header
- Statistics card:
  - "Verified Today" count (large, prominent)
  - Green background
  - Number display (48-60pt font size)
  - "tickets" label below
- Primary action button:
  - "Scan QR Code" (large, full-width)
  - Primary blue color
  - QR scanner icon
  - Navigate to QRScannerScreen on press
- Secondary action:
  - "Enter Ticket ID Manually" (outlined button)
  - Show dialog on press
- Optional: Recent verifications list (last 5 tickets verified)

**Implementation:**
- Load checker from Redux auth state on mount
- Display checker.name, checker.branchName, checker.ferryBoatName
- Display verifiedToday count from Redux verification state
- Pull-to-refresh to reload count
- Increment counter when returning from successful verification
- Manual entry dialog:
  - TextInput for ticket ID (number keyboard)
  - Validate: Must be positive integer
  - Cancel and Verify buttons
  - On verify: Call verifyTicket thunk with entered ID
  - Show result in modal
- Logout confirmation dialog before logout

**QRScannerScreen (`screens/QRScannerScreen.tsx`):**

**This is the most critical screen for the app's primary function:**

**UI Structure:**
- Full-screen camera view
- Scanner overlay (dark with transparent center square)
- Scanner frame (250x250 white border in center)
- Instructions text at bottom: "Position the QR code within the frame"
- Flash toggle button (top right)
- Close/back button (top left)
- Loading overlay during verification

**Implementation:**

**Camera Setup:**
- Use expo-camera with barcode scanning enabled
- Request camera permission on mount
- Handle permission denied: Show error, redirect to settings or manual entry
- Camera facing: back camera
- Barcode types: QR_CODE only
- Auto-focus enabled

**QR Detection:**
- On barcode scanned: Extract data from QR code
- Parse ticket ID from QR data using verificationService.parseTicketId
- QR code format from backend: Plain ticket ID number (e.g., "12345")
- Prevent multiple scans: Set isProcessing flag, disable scanning until verification complete

**Verification Flow:**
1. Scan QR code
2. Set isProcessing = true, show loading overlay
3. Parse ticket ID from QR data
4. Dispatch verifyTicket thunk with ticket ID
5. Stop camera
6. On success:
   - Vibrate device (haptic feedback)
   - Show success modal with ticket details
   - Play success sound (optional)
7. On error:
   - Vibrate differently (error pattern)
   - Show error modal with message
   - Common errors:
     - "Ticket already verified by [checker name] on [date]"
     - "Ticket not found"
     - "Invalid QR code"
8. User closes modal → Navigate back to HomeScreen

**Error Handling:**
- Network error: Show error, allow retry
- Already verified: Show warning modal with previous verification details
- Invalid QR: Show error, allow retry
- Camera error: Show error, suggest manual entry

**Flash Toggle:**
- Button in top right corner
- Toggle camera torch on/off
- Update icon based on state

**Manual Entry Fallback:**
- If QR scan fails repeatedly
- Show "Having trouble?" link
- Open same manual entry dialog as HomeScreen

**Performance:**
- Debounce QR detection (prevent multiple rapid scans)
- Release camera resources when navigating away
- Handle app backgrounding (pause camera)

---

### 11. CAMERA INTEGRATION (EXPO)

**Installation:**
- Install: `npx expo install expo-camera expo-barcode-scanner`
- These are the React Native equivalents of Flutter's mobile_scanner package

**Camera Permissions:**
- Request on first use: `Camera.requestCameraPermissionsAsync()`
- Check permission status before opening camera
- Handle denied permission: Show alert with instructions, offer settings redirect
- Android: Add camera permission to app.json
- No iOS permissions needed (Android-only app)

**Barcode Scanner Setup:**
- Use Camera component from expo-camera
- Set barcode scanner enabled: `barCodeScannerSettings`
- Set barcode types: `[BarCodeScanner.Constants.BarCodeType.qr]`
- Handle onBarCodeScanned callback

**Barcode Scanned Event:**
- Callback receives: type, data, bounds
- data contains the QR code string (ticket ID)
- Debounce callback to prevent multiple scans
- Parse data to extract ticket ID

**Camera Controls:**
- Flash/Torch: Use camera ref with `toggleFlash()` method
- Focus: Auto-focus enabled by default
- Zoom: Not needed for checker app

**Scanner Overlay Implementation:**
- Use absolute positioned Views to create overlay
- Transparent center square (250x250)
- Dark overlay on all four sides
- White border around center square
- Implementation approach:
  - Full-screen dark overlay View with opacity
  - Use layout calculations to create center cutout effect
  - Or use react-native-hole-view if available

**Performance Optimization:**
- Limit scan rate: Only process barcode every 500ms
- Stop scanning during verification processing
- Release camera when leaving screen
- Handle app lifecycle: Pause camera on blur, resume on focus

---

### 12. VALIDATION & ERROR HANDLING

**Input Validation (`utils/validators.ts`):**
- Email validator: Regex for valid email format
- Required field validator: Check not empty/null
- Ticket ID validator: Must be positive integer, no decimals
- QR data parser: Extract ticket ID from various QR formats

**API Error Handling:**

**Login Errors:**
- Invalid credentials: "Incorrect email or password"
- Non-checker role: "Invalid credentials. Only checkers can login."
- Network error: "Network error. Please check your connection."
- Server error: "Something went wrong. Please try again."

**Verification Errors:**
- Ticket already verified: 
  - Title: "Already Verified"
  - Message: "This ticket was verified by [checker name] on [date at time]"
  - Show ticket details
  - Orange warning color
- Ticket not found:
  - Title: "Ticket Not Found"
  - Message: "The ticket ID does not exist in the system"
  - Red error color
- Invalid ticket ID:
  - Title: "Invalid Ticket"
  - Message: "Please scan a valid ticket QR code"
  - Red error color
- Network error:
  - Title: "Connection Error"
  - Message: "Check your internet connection and try again"
  - Allow retry button

**Loading States:**
- Login: Button loading spinner
- Verification: Full-screen overlay with spinner and "Verifying ticket..." text
- Home screen refresh: Standard pull-to-refresh indicator

**Success Feedback:**
- Haptic feedback (vibration) on successful verification
- Success modal with green checkmark icon
- Success sound (optional, can be disabled in settings)
- Auto-increment verified count
- Show verified ticket details

---

### 13. LOCAL DATA PERSISTENCE

**What to Store:**
- Auth token (required for API calls)
- Checker data (name, email, role, branch, ferry)
- Daily verification count with date
- Last verification timestamp

**Daily Count Reset Logic:**
- Save verifiedToday with current date in AsyncStorage
- On app start: Check if stored date === today
- If different date: Reset count to 0
- If same date: Load stored count
- Update count after each successful verification

**Storage Keys:**
```
auth_token: string
checker_data: JSON string of Checker object
verification_count: JSON string { count: number, date: string }
last_verified_at: ISO timestamp string
```

**Data Sync:**
- Count is local only (not synced to backend)
- Backend tracks all verifications with checker_id
- Local count is just for UI display

---

### 14. PERFORMANCE & OPTIMIZATION

**Camera Performance:**
- Limit barcode scan frequency (debounce to 500ms)
- Release camera resources when not in use
- Handle app backgrounding properly
- Disable scanning during verification processing

**Redux Performance:**
- Minimal state (only auth and verification)
- No complex selectors needed
- Simple state updates

**App Size:**
- Android-only build reduces size
- No image processing libraries needed
- No complex forms or UI libraries

**Battery Optimization:**
- Camera only active on scanner screen
- Stop camera when navigating away
- No background processing
- No location tracking

---

### 15. TESTING ON REAL DEVICE

**Why Real Device is Required:**
- Camera testing requires physical device
- QR scanning cannot be tested in emulator effectively
- Barcode scanner functionality needs real camera

**Testing Workflow:**
- Install Expo Go on Android device
- Run `npx expo start` in project directory
- Scan QR code with Expo Go app
- App opens with live reload enabled
- Test camera permissions
- Test QR scanning with printed QR codes

**Test QR Codes:**
- Generate test QR codes with ticket IDs from database
- Print QR codes for testing
- Test with various ticket statuses:
  - New unverified ticket (should succeed)
  - Already verified ticket (should show warning)
  - Invalid ticket ID (should show error)
  - Non-numeric QR code (should show error)

**Critical Testing Points:**
- Login with checker credentials (role_id = 5)
- Try login with non-checker credentials (should fail)
- Camera permission request and handling
- QR code scanning accuracy
- Verification success flow
- Already verified ticket handling
- Network error scenarios
- Manual ticket ID entry
- Logout functionality
- Daily count increment
- Flash/torch toggle
- Back button from scanner

---

### 16. BUILD & DEPLOYMENT

**Development Testing:**
- Run: `npx expo start`
- Press `a` for Android emulator (limited - can't test camera)
- Scan QR for physical device testing (recommended)

**Production Build - Android Only:**
- Run: `eas build --platform android`
- Configure `eas.json`:
  ```json
  {
    "build": {
      "production": {
        "android": {
          "buildType": "apk"
        }
      }
    }
  }
  ```
- Generate APK for direct installation on checker devices
- Or generate AAB for Play Store

**Distribution Strategy:**
- APK direct installation on checker devices (recommended for enterprise)
- Or internal Play Store release
- Device management: Install on all checker devices at ferry gates
- Update strategy: Push updates via Play Store or direct APK replacement

**Pre-Build Checklist:**
- Update version in app.json
- Test on multiple Android devices
- Verify API endpoints point to production
- Test camera on multiple device models
- Remove debug console.logs
- Test offline scenarios
- Verify camera permissions in app.json
- Test QR scanning in various lighting conditions
- Update app icon
- Write deployment documentation for IT staff

**Device Requirements for Checkers:**
- Android 5.0 (Lollipop) or higher
- Working rear camera
- Minimum 2GB RAM
- 4G/WiFi connectivity
- Recommended: Rugged Android device for outdoor use

---

### 17. MIGRATION-SPECIFIC NOTES

**Flutter → React Native Equivalents (Checker App Specific):**

**Camera & QR Scanning:**
- Flutter `mobile_scanner` → Expo Camera with BarCodeScanner
- Flutter `MobileScanner` widget → React Native `Camera` component
- Flutter `onDetect` callback → React Native `onBarCodeScanned` callback
- Flutter `BarcodeCapture` → React Native barcode scan result object
- Flutter `cameraController.start/stop` → Component mount/unmount handles camera lifecycle

**State Management:**
- Flutter `Provider` with ViewModel → Redux Toolkit slices
- Flutter `ChangeNotifier` → Redux async thunks
- Flutter `notifyListeners()` → Automatic Redux subscriber notifications
- Flutter `Consumer<T>` → React `useSelector` hook

**UI Components:**
- Flutter `Card` → React Native custom Card component
- Flutter `CircularProgressIndicator` → React Native `ActivityIndicator`
- Flutter `AlertDialog` → React Native `Modal` component
- Flutter `SnackBar` → Custom Toast or Alert.alert

**Navigation:**
- Flutter `Navigator.push` → React Navigation `navigation.navigate`
- Flutter `Navigator.pop` → React Navigation `navigation.goBack`
- Flutter route arguments → React Navigation params

**Permissions:**
- Flutter `permission_handler` → Expo Camera permission methods
- Flutter `Permission.camera.request()` → `Camera.requestCameraPermissionsAsync()`

**Haptic Feedback:**
- Flutter `HapticFeedback.mediumImpact()` → `Haptics.impactAsync(ImpactFeedbackStyle.Medium)`
- Install: `npx expo install expo-haptics`

**Key Differences:**
- Expo Camera is component-based (renders camera view)
- Flutter mobile_scanner is widget-based with similar approach
- React Native requires explicit permission requests before rendering camera
- Scanner overlay must be created with absolute positioned views
- Barcode scan events are callbacks, not streams

---

### 18. SECURITY CONSIDERATIONS

**Authentication Security:**
- Only role_id = 5 users can access app
- Validate roleId on every login attempt
- Clear all local data on logout
- Token expires after inactivity (backend handles this)
- Auto-logout on 401 responses

**API Security:**
- All verification requests require Bearer token
- HTTPS only connections
- Token stored securely in AsyncStorage (encrypted on Android 6.0+)

**Device Security:**
- App can only be installed on authorized checker devices
- Consider implementing device registration if needed
- Prevent screenshot of verification screens (optional)

**Data Privacy:**
- Don't log ticket details in production
- Don't store verified ticket data permanently
- Clear sensitive data on logout

---

### 19. ACCESSIBILITY FEATURES

**Camera Scanner:**
- Provide manual entry alternative for accessibility
- Clear instructions for QR scanning
- High contrast scanner frame
- Audio feedback option for successful scan

**Text & Buttons:**
- Minimum touch target size: 44x44 points
- Clear, readable font sizes
- High contrast text colors
- Accessible button labels

**Screen Reader Support:**
- Add accessibilityLabel to all interactive elements
- Provide context for scanner screen
- Announce verification results

---

### 20. OFFLINE HANDLING

**Network Requirement:**
- App requires internet connection for verification
- Verification MUST be online (updates backend immediately)
- Cannot cache or queue verifications

**Offline Detection:**
- Show offline indicator when no connection
- Disable scan button when offline
- Show clear error message: "Internet connection required for verification"
- Allow retry when connection restored

**Future Enhancement (Optional):**
- Download today's ticket list at shift start
- Verify against local list when offline
- Sync verifications when back online
- Add sync status indicator

---

### 21. FINAL CHECKLIST BEFORE CONSIDERING MIGRATION COMPLETE

**Core Functionality:**
- [ ] Checker login working (only role_id = 5)
- [ ] Non-checker login properly rejected
- [ ] Auth state persistence working
- [ ] Camera permissions requested and handled
- [ ] QR code scanning working accurately
- [ ] Ticket verification API integration working
- [ ] Successful verification flow complete
- [ ] Already verified ticket handling correct
- [ ] Ticket not found error handling correct
- [ ] Network error handling implemented

**UI/UX:**
- [ ] Splash screen displays correctly
- [ ] Login screen properly styled
- [ ] Home screen shows checker info and stats
- [ ] Scanner screen has proper overlay
- [ ] Scanner frame clearly visible
- [ ] Ticket details modal displays correctly
- [ ] Verification badges show proper colors/icons
- [ ] Manual entry dialog works
- [ ] Loading states on all async operations
- [ ] Error messages are clear and helpful

**Navigation:**
- [ ] Navigation between all screens works
- [ ] Back button behavior correct
- [ ] Scanner closes properly
- [ ] Can return to home after verification

**Features:**
- [ ] Daily verification count increments
- [ ] Daily count persists across app restarts
- [ ] Daily count resets at midnight
- [ ] Flash toggle works
- [ ] Haptic feedback on verification
- [ ] Manual ticket ID entry works
- [ ] Logout works correctly

**Camera:**
- [ ] Camera opens on scanner screen
- [ ] Camera closes when leaving screen
- [ ] QR codes detected reliably
- [ ] Multiple rapid scans prevented
- [ ] Camera handles app backgrounding
- [ ] Flash/torch toggle functional

**Testing:**
- [ ] Tested on real Android device
- [ ] Tested QR scanning with printed codes
- [ ] Tested in various lighting conditions
- [ ] Tested with already verified tickets
- [ ] Tested with invalid ticket IDs
- [ ] Tested network error scenarios
- [ ] Tested camera permission denied
- [ ] Tested logout and re-login

**Production Readiness:**
- [ ] No console errors or warnings
- [ ] TypeScript types all correctly defined
- [ ] Code follows React Native best practices
- [ ] Performance is acceptable (no lag)
- [ ] App icon configured
- [ ] Splash screen configured
- [ ] Version number updated
- [ ] Production API endpoint configured
- [ ] Ready for APK build
- [ ] Deployment documentation written

---

## Summary

This guide provides a comprehensive approach to migrating the Flutter Checker App to React Native using Expo. The Checker app is significantly simpler than the Customer app, with only 4 main screens and a focused purpose: authenticate checkers and verify ferry tickets via QR scanning.

**Key Success Factors:**
1. Properly implement camera/barcode scanning with expo-camera
2. Validate checker role (role_id = 5) during authentication
3. Handle already-verified tickets gracefully
4. Provide clear error messages and manual entry fallback
5. Test extensively on real Android devices
6. Optimize camera performance

**Main Differences from Customer App:**
- Android-only (no iOS)
- Simpler state management (only auth + verification)
- No payment integration
- No image uploads
- No complex forms
- Focus on QR scanning performance
- Single-purpose app (verification only)

**Migration Complexity:**
- **Lower complexity** than Customer App
- **Estimated timeline**: 1-2 weeks
- **Critical dependency**: Camera/QR scanning must work reliably
- **Primary testing requirement**: Real Android device with camera

The migration will result in a unified React Native codebase that can be maintained alongside the Customer app, with shared components and utilities where applicable.

---

## Additional Resources

**Expo Camera Documentation:**
- https://docs.expo.dev/versions/latest/sdk/camera/
- https://docs.expo.dev/versions/latest/sdk/bar-code-scanner/

**Testing QR Codes:**
- Generate test QR codes online: https://www.qr-code-generator.com/
- Use actual ticket IDs from your database
- Print codes on paper for realistic testing

**Android Testing:**
- Use Expo Go app from Play Store
- Or build development client: `npx expo install expo-dev-client`
- Test on multiple Android versions and device models

This completes the comprehensive Checker App migration guide.
