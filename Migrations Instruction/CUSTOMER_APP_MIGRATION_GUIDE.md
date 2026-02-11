# CUSTOMER APP MIGRATION GUIDE: Flutter to React Native

## Migration Checklist & Implementation Points

### 1. PROJECT INITIALIZATION

**Setup Tasks:**
- [ ] Create Expo TypeScript project: `npx create-expo-app@latest jetty-customer-rn --template expo-template-blank-typescript`
- [ ] Install navigation packages: `@react-navigation/native`, `@react-navigation/stack`, `@react-navigation/bottom-tabs`
- [ ] Install state management: `@reduxjs/toolkit`, `react-redux`
- [ ] Install HTTP client: `axios`
- [ ] Install storage: `@react-native-async-storage/async-storage`
- [ ] Install QR code display: `react-native-qrcode-svg`
- [ ] Install image picker: `expo-image-picker`
- [ ] Install Razorpay: `react-native-razorpay`
- [ ] Install date utilities: `date-fns`
- [ ] Configure TypeScript with path aliases in `tsconfig.json`
- [ ] Configure Babel with `babel-plugin-module-resolver` for imports
- [ ] Set up `app.json` with app name, bundle identifiers, permissions, splash screen, icon

**Critical Configuration Points:**
- API base URL in `app.json` extra config: `https://unfurling.ninja/api`
- Razorpay key ID in extra config
- iOS permissions: Camera, Photo Library
- Android permissions: Camera, Storage, Internet
- App icon (1024x1024), Splash screen with ocean blue (#006994) background

---

### 2. ARCHITECTURE & FOLDER STRUCTURE

**Create Directory Structure:**
```
src/
├── navigation/          # Navigation configuration
├── screens/            # All screen components
│   ├── auth/          # 7 authentication screens
│   └── main/          # 6 main app screens
├── components/         # Reusable components
│   ├── common/        # Generic UI components
│   ├── booking/       # Booking-specific components
│   └── profile/       # Profile components
├── store/             # Redux store and slices
│   └── slices/        # 3 slices: auth, booking, app
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
- All business logic in Redux thunks, not in components
- Services layer handles all API communication
- Components should be presentational, not container components

---

### 3. TYPE DEFINITIONS

**Create TypeScript Interfaces for:**

**Models (`types/models.ts`):**
- Customer: id, firstName, lastName, email, mobile, profileImage, googleId
- Branch: id, branchId, branchName, branchAddress, branchPhone, latitude, longitude, isActive
- Ferry: id, number, name, branchId, capacityPassengers, capacityVehicles, isActive
- ItemRate: id, itemName, itemRate, itemLavy, itemSurchargePct, branchId, isVehicle, startingDate, endingDate
- BookingItem: itemName, qty, rate, levy, amount, vehicleNo (optional)
- Booking: id, customerId, fromBranch, toBranch, ferryBoat, ferryTime, items[], totalAmount, paymentId, status, ticketId, verifiedAt, createdAt
- Ticket: id, ticketNo, ticketDate, branch, destBranch, ferryBoat, ferryTime, totalAmount, verifiedAt, qrCode

**API Types (`types/api.ts`):**
- ApiResponse<T>: Generic wrapper with data, message, error, errors
- PaginatedResponse<T>: data[], currentPage, lastPage, perPage, total
- LoginResponse: token, customer
- OTPResponse: message, otpSent
- CreateBookingRequest: All booking creation parameters
- RazorpayOrderResponse: orderId, amount, currency, keyId

**Navigation Types (`types/navigation.ts`):**
- AuthStackParamList: 7 auth screens with params
- MainStackParamList: 6 main screens with params
- MainTabParamList: 3 tab screens
- Type-safe navigation props for each screen

---

### 4. SERVICES LAYER

**API Service (`services/api.ts`):**
- Create Axios instance with base URL from app config
- Set timeout to 30 seconds
- Add request interceptor to inject Bearer token from AsyncStorage
- Add response interceptor to handle 401 errors (logout user)
- Implement error handling: Network errors, server errors, timeout errors
- Methods: get<T>, post<T>, put<T>, delete<T>, upload<T> (for multipart/form-data)
- Handle FormData for image uploads

**Storage Service (`services/storageService.ts`):**
- Wrap AsyncStorage with type-safe methods
- Methods: saveToken, getToken, clearToken, saveCustomer, getCustomer, isLoggedIn, clearAll
- Use keys: 'auth_token', 'customer_data'
- JSON stringify/parse for customer object

**Auth Service (`services/authService.ts`):**
- generateOTP: POST `/customer/generate-otp` with firstName, lastName, email, mobile, password, passwordConfirmation
- verifyOTP: POST `/customer/verify-otp` with email, otp → Save token and customer
- login: POST `/customer/login` with email, password → Save token and customer
- googleSignIn: POST `/customer/google-signin` with idToken, firstName, lastName, email, profileImage → Save token and customer
- logout: GET `/customer/logout` → Clear storage
- requestPasswordOTP: POST `/customer/password-reset/request-otp` with email
- verifyPasswordOTP: POST `/customer/password-reset/verify-otp` with email, otp
- resetPassword: POST `/customer/password-reset/reset` with email, password, passwordConfirmation
- getProfile: GET `/customer/profile`
- updateProfile: PUT `/customer/profile` with firstName, lastName, mobile → Update stored customer
- uploadProfilePicture: POST `/customer/profile/upload-picture` with FormData containing image

**Booking Service (`services/bookingService.ts`):**
- getBranches: GET `/customer/branch`
- getToBranches: GET `/branches/{fromBranchId}/to-branches`
- getFerriesByBranch: GET `/customer/ferries/branch/{branchId}`
- getRatesByBranch: GET `/customer/rates/branch/{branchId}`
- createRazorpayOrder: POST `/razorpay/order` with amount
- verifyRazorpayPayment: POST `/razorpay/verify` with razorpayOrderId, razorpayPaymentId, razorpaySignature
- createBooking: POST `/bookings` with fromBranch, toBranch, ferryBoatId, ferryTime, items[], totalAmount, paymentId
- getBookings: GET `/bookings?page={page}`
- getBookingDetail: GET `/bookings/{bookingId}`
- cancelBooking: POST `/bookings/{bookingId}/cancel`

---

### 5. STATE MANAGEMENT (REDUX TOOLKIT)

**Store Configuration (`store/index.ts`):**
- Combine 3 reducers: auth, booking, app
- Configure middleware to ignore serialization warnings for dates
- Export typed hooks: useAppDispatch, useAppSelector
- Export RootState and AppDispatch types

**Auth Slice (`store/slices/authSlice.ts`):**

**State:**
- customer: Customer | null
- isAuthenticated: boolean
- isLoading: boolean
- error: string | null

**Async Thunks:**
- checkAuthStatus: Check if token exists, load customer from storage
- login: Call authService.login, handle success/error
- register: Call authService.generateOTP (just triggers OTP send)
- verifyOTP: Call authService.verifyOTP, set authenticated state
- logout: Call authService.logout, clear state
- updateProfile: Call authService.updateProfile, update customer in state

**Reducers:**
- setCustomer: Manually set customer and authenticated flag
- clearError: Reset error to null

**Booking Slice (`store/slices/bookingSlice.ts`):**

**State:**
- branches: Branch[]
- ferries: Ferry[]
- rates: ItemRate[]
- fromBranch: Branch | null
- toBranch: Branch | null
- selectedFerry: Ferry | null
- selectedTime: string | null
- items: BookingItem[]
- totalAmount: number
- bookings: Booking[]
- currentBooking: Booking | null
- isLoading: boolean
- error: string | null

**Async Thunks:**
- fetchBranches: Load all branches
- fetchFerriesByBranch: Load ferries for selected branch
- fetchRatesByBranch: Load pricing for selected branch
- createBooking: Submit booking with payment
- fetchBookings: Load paginated booking history
- fetchBookingDetail: Load single booking with ticket
- cancelBooking: Cancel a booking

**Reducers:**
- setFromBranch: Set source, clear destination/ferry/time
- setToBranch: Set destination
- setSelectedFerry: Set ferry
- setSelectedTime: Set time
- addItem: Add passenger/vehicle item, recalculate total
- removeItem: Remove item by index, recalculate total
- updateItem: Update item by index, recalculate total
- clearBookingForm: Reset all form fields
- clearError: Reset error

**App Slice (`store/slices/appSlice.ts`):**

**State:**
- isOnline: boolean
- theme: 'light' | 'dark'
- language: 'en' | 'mr'

**Reducers:**
- setOnlineStatus: Update online/offline state
- setTheme: Switch theme
- setLanguage: Switch language

---

### 6. NAVIGATION STRUCTURE

**Root Navigator (`navigation/RootNavigator.tsx`):**
- Check auth status on mount using checkAuthStatus thunk
- Conditionally render AuthNavigator or MainNavigator based on isAuthenticated
- Show splash screen while checking auth status

**Auth Navigator (`navigation/AuthNavigator.tsx`):**
- Stack navigator with 7 screens:
  1. SplashScreen (initial route)
  2. LoginScreen
  3. RegisterScreen
  4. OTPScreen (receives email param)
  5. ForgotPasswordScreen
  6. ForgotPasswordOTPScreen (receives email param)
  7. ResetPasswordScreen (receives email param)
- No header on splash/login
- Custom back buttons with themed colors

**Main Navigator (`navigation/MainNavigator.tsx`):**
- Bottom tab navigator with 3 tabs:
  1. HomeTab → Stack with HomeScreen, BookingScreen
  2. BookingsTab → Stack with BookingsListScreen, BookingDetailScreen
  3. ProfileTab → Stack with ProfileScreen, EditProfileScreen
- Tab icons: home, list/history, person
- Ocean blue active tint color (#006994)

---

### 7. THEME SYSTEM

**Colors (`theme/colors.ts`):**
- Primary colors: primaryColor (#006994), primaryDark (#004A6B), primaryLight (#00A8E8)
- Accent colors: accentColor (#00D4FF), secondaryColor (#4CAF50)
- Background colors: backgroundColor (#F5F5F5), cardBackground (#FFFFFF), inputBackground (#F9F9F9)
- Text colors: textPrimary (#212121), textSecondary (#757575), textHint (#BDBDBD), textWhite (#FFFFFF)
- Status colors: success (#4CAF50), error (#F44336), warning (#FF9800), info (#2196F3)
- Primary gradient: [primaryColor, primaryLight]

**Typography (`theme/typography.ts`):**
- Font sizes: xs (10), sm (12), base (14), lg (16), xl (18), 2xl (20), 3xl (24), 4xl (28), 5xl (32)
- Font weights: light (300), regular (400), medium (500), semibold (600), bold (700)
- Line heights: tight (1.2), normal (1.5), relaxed (1.75)

**Spacing (`theme/spacing.ts`):**
- Scale: xs (4), sm (8), md (12), lg (16), xl (20), 2xl (24), 3xl (32), 4xl (40), 5xl (48)

---

### 8. COMMON COMPONENTS

**Button Component (`components/common/Button.tsx`):**
- Props: text, onPress, variant (primary/secondary/outline/text), size (sm/md/lg), disabled, loading, fullWidth, icon
- Styles: Apply theme colors, handle disabled state opacity, show ActivityIndicator when loading
- Touch feedback with opacity

**Input Component (`components/common/Input.tsx`):**
- Props: label, value, onChangeText, placeholder, secureTextEntry, keyboardType, error, leftIcon, rightIcon, multiline, numberOfLines
- Styles: Border, padding, theme colors, error state (red border)
- Show error message below input

**Card Component (`components/common/Card.tsx`):**
- Props: children, style, onPress (optional for touchable cards)
- Styles: White background, rounded corners, shadow/elevation

**Loading Component (`components/common/Loading.tsx`):**
- Full-screen centered ActivityIndicator
- Overlay with semi-transparent background
- Theme color for indicator

**Avatar Component (`components/common/Avatar.tsx`):**
- Props: uri, size, name (for initials fallback)
- Display image or initials in colored circle
- Handle loading/error states

**Badge Component (`components/common/Badge.tsx`):**
- Props: text, variant (success/error/warning/info)
- Small colored pill with text
- Map variant to theme status colors

---

### 9. AUTHENTICATION SCREENS

**SplashScreen (`screens/auth/SplashScreen.tsx`):**
- Display app logo centered with gradient background
- Dispatch checkAuthStatus on mount
- Navigate to Login or Home based on auth state
- 2-second minimum display time for branding

**LoginScreen (`screens/auth/LoginScreen.tsx`):**
- Form fields: email (TextInput with email keyboard), password (TextInput with secure entry)
- "Forgot Password?" link → Navigate to ForgotPasswordScreen
- "Register" link → Navigate to RegisterScreen
- Login button → Dispatch login thunk with form data
- Google Sign-In button → Trigger expo-auth-session flow, call googleSignIn thunk
- Show loading spinner during login
- Display error messages from Redux state
- Clear error on unmount

**RegisterScreen (`screens/auth/RegisterScreen.tsx`):**
- Form fields: firstName, lastName, email, mobile (phone keyboard), password (minimum 8 chars), confirmPassword
- Validate: All fields required, email format, passwords match, password length
- Register button → Dispatch register thunk
- On success → Navigate to OTPScreen with email param
- "Already have account?" link → Navigate back to Login
- Show loading and errors from Redux

**OTPScreen (`screens/auth/OTPScreen.tsx`):**
- Receive email from route params
- Display 6 input boxes for OTP digits (auto-focus next box on input)
- Verify button → Dispatch verifyOTP thunk with email and concatenated OTP
- Resend OTP button with 60-second countdown timer
- On success → Auto-navigate to Home (auth state changes)
- Show loading and errors

**ForgotPasswordScreen (`screens/auth/ForgotPasswordScreen.tsx`):**
- Single email input field
- Submit button → Call authService.requestPasswordOTP
- On success → Navigate to ForgotPasswordOTPScreen with email param
- Back button to Login

**ForgotPasswordOTPScreen (`screens/auth/ForgotPasswordOTPScreen.tsx`):**
- Same OTP input UI as registration OTP
- Receive email from params
- Verify button → Call authService.verifyPasswordOTP
- On success → Navigate to ResetPasswordScreen with email param
- Resend OTP with countdown

**ResetPasswordScreen (`screens/auth/ResetPasswordScreen.tsx`):**
- Receive email from params
- Form fields: newPassword, confirmPassword
- Validate passwords match and length
- Submit button → Call authService.resetPassword
- On success → Navigate to Login with success message
- Show password requirements below fields

---

### 10. MAIN APP SCREENS

**HomeScreen (`screens/main/HomeScreen.tsx`):**
- Display welcome message with customer first name from Redux state
- Show profile avatar (from customer.profileImage or initials)
- Two large action cards:
  1. "Book New Ticket" → Navigate to BookingScreen
  2. "View Bookings" → Navigate to BookingsListScreen
- Show "Upcoming Trips" section:
  - Dispatch fetchBookings on mount (filter for upcoming)
  - Display 3 most recent confirmed bookings as cards
  - Each card: From-To route, date, time, status badge, "View Ticket" button
  - "View All" link to BookingsListScreen
- Pull-to-refresh to reload bookings
- Handle empty state with illustration

**BookingScreen (`screens/main/BookingScreen.tsx`):**

**This is the most complex screen - 27KB in Flutter. Critical implementation points:**

**Step 1: Branch Selection**
- Dropdown for "From Branch" using branches from Redux
- On select: Dispatch setFromBranch, fetchFerriesByBranch, fetchRatesByBranch, load destination branches
- Dropdown for "To Branch" populated after "From" selection
- On select: Dispatch setToBranch

**Step 2: Ferry & Time Selection**
- Dropdown for ferry from ferries array in Redux
- Dropdown for departure time (hardcode common times: 06:00, 08:00, 10:00, 12:00, 14:00, 16:00, 18:00)
- Dispatch setSelectedFerry and setSelectedTime

**Step 3: Passenger Selection**
- Section with passenger categories from rates (filter isVehicle: false)
- For each category: Display name, rate, levy
- Stepper component (- and + buttons) to adjust quantity
- On quantity change: Dispatch addItem or updateItem with calculated amount (rate + levy) * qty

**Step 4: Vehicle Selection (Optional)**
- Toggle/accordion to show vehicle section
- Dropdown for vehicle type from rates (filter isVehicle: true)
- Text input for vehicle registration number
- Add button → Dispatch addItem with vehicle data
- Display added vehicles in list with remove buttons
- Validate vehicle number format

**Step 5: Summary Display**
- Card showing breakdown:
  - List each item: name, qty, amount
  - Subtotal for passengers
  - Subtotal for vehicles
  - Total amount (large, bold)
- Update in real-time as items change (subscribe to Redux items and totalAmount)

**Step 6: Payment & Submission**
- "Proceed to Payment" button
- Validate: fromBranch, toBranch, ferry, time, items.length > 0 all required
- On press:
  1. Call bookingService.createRazorpayOrder with totalAmount
  2. Open Razorpay checkout with returned order details
  3. On payment success: Get payment ID from Razorpay callback
  4. Call bookingService.verifyRazorpayPayment
  5. Dispatch createBooking thunk with all form data + paymentId
  6. On booking success: Navigate to BookingDetailScreen with new booking ID
  7. Show success message
- Handle payment failure: Show error, allow retry
- Show loading overlay during payment processing

**Critical Notes:**
- All form state lives in Redux booking slice
- Clear form with clearBookingForm action after successful booking
- Implement ScrollView for entire form
- Add validation messages for each step
- Disable "Proceed" button until all required fields filled

**BookingsListScreen (`screens/main/BookingsListScreen.tsx`):**
- Dispatch fetchBookings on mount
- Display bookings from Redux state as FlatList
- Each booking card:
  - From → To route with arrow
  - Date and time
  - Ferry name
  - Total amount
  - Status badge (confirmed: green, pending: yellow, cancelled: red)
  - Tap → Navigate to BookingDetailScreen with booking.id
- Implement pull-to-refresh
- Implement pagination: Load more on scroll to bottom
- Filter tabs: All, Upcoming, Completed, Cancelled (filter bookings array)
- Empty state with "Book Your First Ticket" button

**BookingDetailScreen (`screens/main/BookingDetailScreen.tsx`):**
- Receive bookingId from route params
- Dispatch fetchBookingDetail on mount
- Display currentBooking from Redux:
  - Ticket number (from ticket)
  - Journey details: From, To, Ferry, Date, Time
  - Items breakdown table with columns: Item, Qty, Amount
  - Total amount (prominent display)
  - Payment status
  - Booking status badge
  - QR code using react-native-qrcode-svg (data: ticket.id or ticketNo)
  - "Show this QR code at gate" instruction
  - Verification status: If verifiedAt exists, show green "Verified" badge with timestamp
- Action buttons:
  - "Cancel Booking" (only if status is 'confirmed' and not verified) → Confirm dialog, dispatch cancelBooking
  - Share button → Use React Native Share API with booking details
- Print-style layout for easy screenshot

**ProfileScreen (`screens/main/ProfileScreen.tsx`):**
- Display customer from Redux auth state
- Profile header with avatar (large), name, email, mobile
- Menu items:
  - Edit Profile → Navigate to EditProfileScreen
  - Change Password → Show modal or navigate to change password screen
  - Language (English/Marathi) → Dispatch setLanguage
  - Theme (Light/Dark) → Dispatch setTheme
  - About Us
  - Terms & Conditions
  - Privacy Policy
  - Logout → Confirm dialog, dispatch logout thunk

**EditProfileScreen (`screens/main/EditProfileScreen.tsx`):**
- Load customer from Redux
- Avatar with "Change Photo" button:
  - On press: Show action sheet (Take Photo / Choose from Library / Remove Photo)
  - Use expo-image-picker to get image URI
  - Display selected image locally
  - On save: Call authService.uploadProfilePicture first, get URL
- Form fields: firstName, lastName, mobile (editable)
- Save button:
  - Dispatch updateProfile thunk with form data
  - If image selected, upload first, then update profile
  - On success: Navigate back to ProfileScreen
  - Show success toast
- Cancel button: Navigate back without saving

---

### 11. BOOKING COMPONENTS

**RouteSelector (`components/booking/RouteSelector.tsx`):**
- Two dropdowns side by side or stacked
- Props: branches, selectedFrom, selectedTo, onFromChange, onToChange
- Use React Native Picker or custom dropdown component
- Apply theme styles

**FerrySelector (`components/booking/FerrySelector.tsx`):**
- Dropdown for ferry selection
- Props: ferries, selectedFerry, onFerryChange
- Show ferry name and number

**TimeSelector (`components/booking/TimeSelector.tsx`):**
- Dropdown or horizontal scroll of time chips
- Props: times[], selectedTime, onTimeChange
- Highlight selected time

**PassengerSelector (`components/booking/PassengerSelector.tsx`):**
- Section with title "Passengers"
- Map over passenger rates
- For each: Card with category name, rate display, stepper
- Stepper component: - button, quantity display, + button
- Props: rates (filtered for passengers), selectedItems, onItemChange

**VehicleSelector (`components/booking/VehicleSelector.tsx`):**
- Collapsible section (useState for expanded state)
- Dropdown for vehicle type
- TextInput for registration number
- Add button
- List of added vehicles with remove button
- Props: rates (filtered for vehicles), selectedVehicles, onVehicleAdd, onVehicleRemove

**ItemRow (`components/booking/ItemRow.tsx`):**
- Display single booking item in list
- Props: item (BookingItem), onRemove
- Show: item name, qty, vehicle number (if present), amount
- Remove button (X icon)

**BookingSummary (`components/booking/BookingSummary.tsx`):**
- Card component showing booking breakdown
- Props: items[], totalAmount
- Display items table
- Show subtotals and total
- Prominent total display with currency symbol

---

### 12. THIRD-PARTY INTEGRATIONS

**Razorpay Payment Integration:**
- Install: `npm install react-native-razorpay`
- Import: `import RazorpayCheckout from 'react-native-razorpay'`
- Configure options object:
  - key: Razorpay key ID from app config
  - amount: Amount in paise (multiply by 100)
  - order_id: From backend createRazorpayOrder response
  - name: "Jetty Ferry"
  - description: "Ferry Ticket Booking"
  - currency: "INR"
  - prefill: { email, contact: mobile } from customer
- Open checkout: `RazorpayCheckout.open(options)`
- Handle success callback: Extract razorpay_payment_id, razorpay_order_id, razorpay_signature
- Handle error callback: Show user-friendly error message
- Verify payment on backend before creating booking

**Google Sign-In with Expo:**
- Install: `npx expo install expo-auth-session expo-crypto expo-web-browser`
- Configure Google OAuth in app.json or app.config.js
- Use expo-auth-session to initiate Google auth flow
- Get ID token from response
- Extract user info: given_name (firstName), family_name (lastName), email, picture (profileImage)
- Call authService.googleSignIn with data
- Handle errors gracefully

**Image Picker:**
- Install: `npx expo install expo-image-picker`
- Request permissions on iOS/Android
- Use `ImagePicker.launchCameraAsync()` for camera
- Use `ImagePicker.launchImageLibraryAsync()` for gallery
- Options: mediaTypes (Images), allowsEditing (true), aspect (1:1 for profile), quality (0.8)
- Get result.uri and result.type
- Display preview before upload
- Create FormData and upload via authService.uploadProfilePicture

**QR Code Display:**
- Install: `npm install react-native-qrcode-svg`
- Import: `import QRCode from 'react-native-qrcode-svg'`
- Usage: `<QRCode value={ticket.id.toString()} size={200} />`
- Center in view, add border and background for contrast
- Add instruction text below: "Show this code at the boarding gate"

---

### 13. VALIDATION & ERROR HANDLING

**Form Validation (`utils/validators.ts`):**
- Email validator: Regex for valid email format
- Password validator: Minimum 8 characters, check strength
- Mobile validator: 10 digits, starts with 6-9
- Required field validator: Check not empty/null
- Password match validator: Compare two password fields
- OTP validator: Exactly 6 digits
- Vehicle number validator: Indian vehicle registration format

**Error Display Strategy:**
- Show validation errors below input fields (inline)
- Show API errors in modal or toast notifications
- Use React Native Alert for critical errors
- Network errors: "Check your internet connection"
- 401 errors: Logout user automatically (handled in API interceptor)
- 500 errors: "Something went wrong, please try again"
- Validation errors: Display specific field error messages

**Loading States:**
- Global loading: Full-screen Loading component with overlay
- Button loading: Replace button text with ActivityIndicator
- List loading: Show skeleton screens or shimmer effect
- Pull-to-refresh: Use RefreshControl component
- Pagination loading: Footer spinner in FlatList

---

### 14. PERFORMANCE OPTIMIZATIONS

**React Native Best Practices:**
- Use React.memo for components that don't need frequent re-renders
- Use useCallback for event handlers passed as props
- Use useMemo for expensive calculations in render
- Avoid inline styles (define StyleSheet.create outside component)
- Optimize FlatList with: getItemLayout, removeClippedSubviews, maxToRenderPerBatch, windowSize
- Use FastImage for images with caching

**Redux Performance:**
- Use Redux selectors to subscribe only to needed state slices
- Avoid selecting entire store in components
- Use createSelector from reselect for memoized derived state
- Keep Redux state normalized (avoid nested objects)

**Bundle Size Optimization:**
- Use Expo managed workflow for optimized builds
- Remove console.log statements in production
- Enable Hermes engine for Android (faster startup)
- Lazy load screens with React.lazy if needed

---

### 15. TESTING ON EXPO GO

**Testing Workflow:**
- Install Expo Go app on iPhone from App Store
- Run `npx expo start` in project directory
- Scan QR code with iPhone camera
- App opens in Expo Go
- Live reload enabled: Save file to see changes immediately
- Shake device for developer menu
- Test on multiple devices/screen sizes using Expo Dev Client

**Critical Testing Points:**
- Test authentication flow completely: register → OTP → login
- Test booking flow end-to-end: selection → payment → confirmation
- Test QR code display clearly visible
- Test image picker on real device (camera and gallery)
- Test Razorpay payment (use test mode keys)
- Test network error scenarios (disable WiFi mid-action)
- Test app state persistence (close and reopen app)
- Test navigation stack (back button behavior)
- Test form validation messages
- Test pull-to-refresh on lists

---

### 16. BUILD & DEPLOYMENT

**Development Build:**
- Run: `npx expo start`
- Press `i` for iOS simulator, `a` for Android emulator
- Or scan QR for Expo Go testing

**Production Build:**

**For iOS:**
- Run: `eas build --platform ios`
- Configure `eas.json` with Apple Developer credentials
- Submit to App Store: `eas submit --platform ios`

**For Android:**
- Run: `eas build --platform android`
- Generate APK or AAB
- Submit to Play Store: `eas submit --platform android`

**Pre-Build Checklist:**
- Update version in app.json
- Test on multiple devices
- Verify API endpoints point to production
- Test payment with production Razorpay keys
- Remove debug console.logs
- Test offline scenarios
- Verify all permissions in app.json
- Test deep linking if implemented
- Update app icons and splash screens
- Write release notes

---

### 17. MIGRATION-SPECIFIC NOTES

**Flutter → React Native Equivalents:**
- `StatefulWidget` → `useState` hook or Redux state
- `Provider` → Redux useSelector
- `Navigator.push` → `navigation.navigate`
- `TextFormField` → Custom Input component with TextInput
- `GestureDetector` → TouchableOpacity/TouchableHighlight/Pressable
- `CircularProgressIndicator` → ActivityIndicator
- `SnackBar` → Toast library or custom Toast component
- `ListView.builder` → FlatList
- `GridView` → FlatList with numColumns
- `Container` → View with StyleSheet
- `SizedBox` → View with fixed width/height
- `Padding` → View with padding style
- `Row` → View with flexDirection: 'row'
- `Column` → View with flexDirection: 'column'
- `Expanded` → View with flex: 1
- `Image.network` → Image component with uri
- `showDialog` → Modal component or react-native Alert.alert

**State Migration Pattern:**
- Flutter Provider → Redux slice
- Provider method → Redux thunk
- notifyListeners() → No equivalent (Redux auto-notifies)
- Consumer widget → useSelector hook

**Key Differences to Remember:**
- React Native uses flexbox by default (no need to specify display: flex)
- Styles are objects, not classes (StyleSheet.create)
- No hot reload by default in React Native (use Fast Refresh)
- Async operations always in thunks or useEffect, never in reducers
- Navigation state managed by React Navigation, not Redux
- Forms are uncontrolled by default (use controlled components with useState)

---

### 18. FINAL CHECKLIST BEFORE CONSIDERING MIGRATION COMPLETE

- [ ] All 13 screens implemented and tested
- [ ] All API endpoints integrated and tested
- [ ] Redux state management working correctly
- [ ] Authentication flow works: register, OTP, login, logout
- [ ] Booking flow works end-to-end with payment
- [ ] QR code displays correctly on ticket
- [ ] Image upload works for profile picture
- [ ] All form validations working
- [ ] Error handling for all API calls
- [ ] Loading states for all async operations
- [ ] Pull-to-refresh on list screens
- [ ] Pagination on bookings list
- [ ] Navigation between all screens works
- [ ] Back button behavior correct
- [ ] Tab navigation works
- [ ] Razorpay payment integration tested
- [ ] Google Sign-In working (if implemented)
- [ ] App persists auth state (stays logged in)
- [ ] App handles offline scenarios gracefully
- [ ] Responsive on different screen sizes
- [ ] Tested on both iOS and Android
- [ ] No console errors or warnings
- [ ] TypeScript types all correctly defined
- [ ] Code follows React Native best practices
- [ ] Performance is acceptable (no lag/jank)
- [ ] App icon and splash screen configured
- [ ] Ready for Expo Go testing
- [ ] Ready for production build

---

## Summary

This guide provides a comprehensive, step-by-step approach to migrating the Flutter Customer App to React Native using Expo. Follow these points sequentially to ensure a successful migration that maintains all functionality while taking advantage of React Native's ecosystem.

**Key Success Factors:**
1. Set up proper TypeScript types from the start
2. Implement Redux state management correctly
3. Create reusable components for common UI patterns
4. Test incrementally on Expo Go
5. Handle all error cases gracefully
6. Optimize for performance throughout development

The migration will result in a unified React/React Native codebase across web and mobile platforms, simplifying maintenance and enabling code sharing opportunities.
