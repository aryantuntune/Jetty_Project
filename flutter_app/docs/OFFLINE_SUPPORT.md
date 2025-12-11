# Offline Support Documentation

## Overview
The app provides robust offline functionality to ensure users can access essential features without internet connectivity.

## Offline Capabilities

### 1. View Booking History
- **Local Cache**: Bookings stored in SQLite database
- **Auto-sync**: Updates when online
- **Full Details**: All booking information available offline
- **Ticket Access**: View digital tickets without internet

### 2. Route Information
- **Route Cache**: Popular routes stored locally
- **Schedule Cache**: Recent schedule data available
- **Ferry Details**: Boat information accessible offline
- **Port Information**: Location and facility details cached

### 3. User Profile
- **Profile Data**: User information stored locally
- **Settings**: App preferences work offline
- **Booking History**: Past bookings always accessible
- **Profile Picture**: Cached for offline viewing

### 4. Search History
- **Recent Searches**: Stored locally
- **Quick Access**: Fast offline search suggestions
- **Route Suggestions**: Based on past bookings

## Online-Required Features

### Cannot Work Offline
- New booking creation
- Payment processing
- Real-time schedule updates
- Booking cancellation
- Profile updates
- Registration/Login
- Live seat availability

### Graceful Degradation
- Clear indicators when offline
- Informative error messages
- Queue operations for later sync
- Cached content clearly marked

## Data Synchronization

### Sync Strategy
- **On App Open**: Check for updates
- **Periodic Background**: Every 30 minutes (when possible)
- **After Network Restoration**: Immediate sync
- **User-initiated**: Pull to refresh

### Sync Priority
1. Critical: Authentication tokens, active bookings
2. High: Recent bookings, profile updates
3. Medium: Route updates, schedules
4. Low: Historical data, analytics

### Conflict Resolution
- Server data takes precedence
- User notified of conflicts
- Manual conflict resolution for important changes
- Automatic merge for non-conflicting updates

## Local Storage

### SQLite Database
**Tables**:
- `users` - User profile data
- `bookings` - Booking history
- `routes` - Ferry routes
- `schedules` - Ferry schedules
- `cached_images` - Profile and other images

**Storage Management**:
- Maximum cache size: 50MB
- Auto-cleanup of old data (30 days)
- User can clear cache manually
- Critical data never auto-deleted

### Shared Preferences
- User preferences
- App settings
- Last sync timestamp
- Feature flags

### Secure Storage
- Authentication tokens
- Encrypted credentials
- Payment method tokens (if applicable)

## Connectivity Detection

### Network Monitoring
- Real-time connectivity status
- Network type detection (WiFi, Mobile)
- Connection quality assessment
- Automatic retry on reconnection

### User Indicators
- **Status Bar**: Online/offline indicator
- **Banners**: "You're offline" message
- **Button States**: Disabled for online-only actions
- **Icons**: Visual indicators on cached content

## Offline Queue

### Pending Actions
- Queue for operations requiring internet
- Automatic execution when online
- User notification on completion
- Error handling for failed operations

### Queued Operations
- Profile updates
- Booking modifications
- Search queries for server logging
- Analytics events

## Cache Management

### Intelligent Caching
- LRU (Least Recently Used) eviction
- Size-based limits
- Time-based expiry
- User-triggered refresh

### Cache Categories
- **Must-have**: User bookings, profile
- **Nice-to-have**: Routes, schedules
- **Optional**: Images, search history

### User Controls
- View cache size
- Clear cache option
- Selective cache clearing
- Download for offline (future feature)

## Best Practices

### For Users
- Sync app when on WiFi
- Keep important bookings accessible
- Check ticket details before traveling
- Update profile when online
- Clear cache periodically

### For Developers
- Always check connectivity before API calls
- Cache response data appropriately
- Provide offline fallbacks
- Test offline scenarios thoroughly
- Handle sync conflicts gracefully

## Future Enhancements

### Planned Features
- **Offline Booking Draft**: Save booking details offline
- **Predictive Caching**: Pre-cache likely searches
- **Offline Maps**: Port location maps
- **Download for Offline**: Manual content download
- **Smart Sync**: AI-powered sync optimization

## Performance Considerations

### Optimization
- Lazy loading of cached data
- Incremental sync (only changes)
- Background sync with WorkManager
- Battery-aware sync frequency
- Data compression for storage

### Monitoring
- Cache hit/miss ratios
- Sync success rates
- Offline usage patterns
- Storage usage analytics
