# 📦 BUILD APK GUIDE

## Prerequisites

Before building APK, ensure:
- ✅ Flutter SDK installed
- ✅ Android SDK installed
- ✅ Project has `android/` folder

---

## Quick Build Commands

### Debug APK (For Testing)
```bash
cd flutter_app
flutter build apk --debug
```

**Output**: `build/app/outputs/flutter-apk/app-debug.apk`

### Release APK (For Distribution)
```bash
cd flutter_app
flutter build apk --release
```

**Output**: `build/app/outputs/flutter-apk/app-release.apk`

### Split APKs (Smaller Size)
```bash
flutter build apk --split-per-abi
```

**Output**: Creates 3 APKs (arm64-v8a, armeabi-v7a, x86_64)

---

## Step-by-Step: Build Release APK

### Step 1: Check Flutter is Working

```bash
flutter doctor
```

Fix any issues shown.

### Step 2: Update Version

**File**: `pubspec.yaml`

```yaml
version: 1.0.0+1  # Change version here
```

Format: `major.minor.patch+buildNumber`

### Step 3: Update App Name

**File**: `android/app/src/main/AndroidManifest.xml`

```xml
<application
    android:label="Jetty Ferry"  <!-- Your app name -->
    android:icon="@mipmap/ic_launcher">
```

### Step 4: Build APK

```bash
cd flutter_app
flutter clean
flutter pub get
flutter build apk --release
```

Wait 2-5 minutes for build to complete.

### Step 5: Find Your APK

```
flutter_app/build/app/outputs/flutter-apk/app-release.apk
```

---

## Current Limitation ⚠️

**Your Flutter project is missing the `android/` folder!**

This folder is created by `flutter create` command.

### Solution:

```bash
cd C:\Users\aryan_x846cd2\Desktop\Jetty

# Create temporary project with Android support
flutter create ferry_booking_app_temp

# Copy your custom code
xcopy flutter_app\lib ferry_booking_app_temp\lib /E /I /Y
copy flutter_app\pubspec.yaml ferry_booking_app_temp\pubspec.yaml

# Backup old and use new
move flutter_app flutter_app_old
move ferry_booking_app_temp flutter_app

# Now you can build APK
cd flutter_app
flutter pub get
flutter build apk --release
```

---

## Signing the APK (For Play Store)

### Step 1: Create Keystore

```bash
keytool -genkey -v -keystore my-release-key.jks -keyalg RSA -keysize 2048 -validity 10000 -alias my-key-alias
```

Save this file in a secure location!

### Step 2: Create Key Properties File

**File**: `android/key.properties`

```properties
storePassword=your_store_password
keyPassword=your_key_password
keyAlias=my-key-alias
storeFile=C:/path/to/my-release-key.jks
```

⚠️ **Don't commit this file to Git!**

### Step 3: Update build.gradle

**File**: `android/app/build.gradle`

Add before `android {`:

```gradle
def keystoreProperties = new Properties()
def keystorePropertiesFile = rootProject.file('key.properties')
if (keystorePropertiesFile.exists()) {
    keystoreProperties.load(new FileInputStream(keystorePropertiesFile))
}
```

Update `buildTypes`:

```gradle
buildTypes {
    release {
        signingConfig signingConfigs.release
    }
}

signingConfigs {
    release {
        keyAlias keystoreProperties['keyAlias']
        keyPassword keystoreProperties['keyPassword']
        storeFile keystoreProperties['storeFile'] ? file(keystoreProperties['storeFile']) : null
        storePassword keystoreProperties['storePassword']
    }
}
```

### Step 4: Build Signed APK

```bash
flutter build apk --release
```

---

## APK Size Optimization

### Current Size: ~20-25 MB

### Optimize with Split APKs:

```bash
flutter build apk --split-per-abi
```

This creates 3 smaller APKs:
- `app-arm64-v8a-release.apk` (~15 MB) - Most devices
- `app-armeabi-v7a-release.apk` (~15 MB) - Older devices
- `app-x86_64-release.apk` (~15 MB) - Emulators

Upload all 3 to Play Store for optimal size.

### Further Optimization:

**In** `android/app/build.gradle`:

```gradle
android {
    buildTypes {
        release {
            shrinkResources true
            minifyEnabled true
            proguardFiles getDefaultProguardFile('proguard-android.txt'), 'proguard-rules.pro'
        }
    }
}
```

---

## Testing APK

### Install on Device:

```bash
adb install build/app/outputs/flutter-apk/app-release.apk
```

### Or Manually:

1. Copy APK to phone
2. Enable "Install from Unknown Sources"
3. Tap APK file to install

---

## Play Store Requirements

### 1. Create App Bundle (Recommended)

```bash
flutter build appbundle --release
```

**Output**: `build/app/outputs/bundle/release/app-release.aab`

Upload this to Play Store instead of APK.

### 2. Screenshots Required

Take screenshots on:
- Phone (at least 2)
- 7-inch tablet (at least 2)
- 10-inch tablet (optional)

Sizes: 1080x1920 or 1440x2560

### 3. Privacy Policy

Required if app collects data.

Host at: `https://unfurling.ninja/privacy-policy`

### 4. Content Rating

Complete questionnaire in Play Console.

### 5. Target API Level

Must target Android 13 (API 33) or higher.

**In** `android/app/build.gradle`:

```gradle
android {
    compileSdkVersion 33
    defaultConfig {
        targetSdkVersion 33
    }
}
```

---

## Troubleshooting

### Error: "android/ folder not found"

Run `flutter create` to generate platform files.

### Error: "Gradle build failed"

```bash
cd android
./gradlew clean
cd ..
flutter clean
flutter pub get
flutter build apk --release
```

### Error: "SDK not found"

Set Android SDK path:

```bash
flutter config --android-sdk C:\Users\YourName\AppData\Local\Android\Sdk
```

### Error: "Java not found"

Install JDK 11 or higher.

---

## Quick Commands Reference

```bash
# Build debug APK
flutter build apk --debug

# Build release APK
flutter build apk --release

# Build app bundle (Play Store)
flutter build appbundle --release

# Build split APKs (smaller)
flutter build apk --split-per-abi

# Clean build
flutter clean

# Install on device
adb install app-release.apk

# Check APK size
ls -lh build/app/outputs/flutter-apk/
```

---

## After Building APK

### Share for Testing:

1. Copy APK from `build/app/outputs/flutter-apk/`
2. Upload to Google Drive / Dropbox
3. Share link with testers
4. Testers need to enable "Install from Unknown Sources"

### Upload to Play Store:

1. Go to https://play.google.com/console
2. Create new app
3. Complete store listing
4. Upload AAB file (not APK)
5. Complete content rating
6. Set pricing
7. Submit for review

---

**Note**: Currently you need to run `flutter create` first to generate the `android/` folder before you can build APK.

See **API_CONNECTION_GUIDE.md** for connecting to backend.
