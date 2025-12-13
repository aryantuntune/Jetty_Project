# Deployment Documentation

## Build Configurations

### Development Build
**Purpose**: Testing and development
```bash
flutter build apk --debug
```
- Debug symbols included
- Larger file size
- Performance profiling enabled

### Release Build
**Purpose**: Production deployment
```bash
flutter build apk --release
```
- Code obfuscation enabled
- Optimized performance
- Smaller file size
- No debug symbols

### App Bundle (Google Play)
```bash
flutter build appbundle --release
```
- Dynamic feature delivery
- Optimized download size
- Required for Play Store

## Android Deployment

### App Signing
- **Keystore File**: `jetty-release-key.jks`
- **Key Alias**: `jetty-key`
- Stored securely (not in version control)
- Password protected

### Key Configuration
Located in `android/key.properties`:
```
storePassword=<store_password>
keyPassword=<key_password>
keyAlias=jetty-key
storeFile=<path_to_keystore>
```

### Version Management
In `pubspec.yaml`:
```yaml
version: 1.0.0+1
```
- Semantic versioning (MAJOR.MINOR.PATCH)
- Build number increments for each release

### Build Variants
- **Debug**: Development testing
- **Profile**: Performance testing
- **Release**: Production deployment

## Google Play Store Deployment

### Prerequisites
- Google Play Developer account
- App signing key configured
- Privacy policy URL
- App screenshots and graphics
- Content rating questionnaire

### Store Listing Requirements
- **App Name**: Jetty Ferry Booking
- **Short Description**: 80 characters max
- **Full Description**: 4000 characters max
- **Screenshots**: 2-8 phone screenshots
- **Feature Graphic**: 1024x500 pixels
- **App Icon**: 512x512 pixels

### Release Tracks
1. **Internal Testing**: Team testing
2. **Closed Testing**: Beta testers
3. **Open Testing**: Public beta
4. **Production**: Public release

### Deployment Steps
1. Build app bundle
2. Generate release notes
3. Upload to Play Console
4. Complete store listing
5. Submit for review
6. Monitor review status
7. Publish when approved

## iOS Deployment (Future)

### Requirements
- Apple Developer account ($99/year)
- Xcode on macOS
- iOS development certificate
- Provisioning profiles

### Build Command
```bash
flutter build ios --release
```

### App Store Submission
- Similar process to Android
- TestFlight for beta testing
- App Store review (typically 24-48 hours)

## Environment Configuration

### Development
- API Base URL: `http://localhost:8000/api`
- Debug mode enabled
- Verbose logging

### Staging
- API Base URL: `https://staging.jetty.com/api`
- Limited logging
- Testing payment gateway

### Production
- API Base URL: `https://api.jetty.com/api`
- Error reporting only
- Live payment gateway

## CI/CD Pipeline

### Automated Build Process
1. Code push to repository
2. Run automated tests
3. Build APK/App Bundle
4. Upload to distribution platform
5. Notify team

### Tools
- GitHub Actions / GitLab CI
- Firebase App Distribution
- Fastlane for automation

## Release Checklist
- [ ] All tests passing
- [ ] Version number incremented
- [ ] Release notes prepared
- [ ] API endpoints verified
- [ ] Payment gateway tested
- [ ] App signing configured
- [ ] Screenshots updated
- [ ] Privacy policy current
- [ ] Performance tested
- [ ] Security audit completed
