# Jetty Customer App - TODO

## Future Features

### Mobile App Customization Panel (Admin Portal)
**Priority:** High  
**Description:** Add a mobile app customization panel on the website admin portal.

**Details:**
- Passengers, vehicles, and other items displayed in the app can be edited by the admin
- New categories/items can be added via admin portal
- Need to ensure mobile app items don't get mixed up with web items
- Admin should be able to:
  - Add/edit/delete item categories for mobile app
  - Set different pricing for mobile app
  - Control which items appear in the mobile app
  - Set mobile-specific configurations

**Why:** The current rates/items are shared between web and mobile. Admin needs separate control over what appears in the mobile app to avoid confusion.

---

## Completed Features

### Time & Date Validation ✅
- Users cannot book past ferry times on the same day
- Extended ferry schedule to 22:00
- If all ferries for today are past, shows "Next Day" times

### Booking API Integration ✅
- Booking data properly formatted with `item_rate_id` and `quantity`
- All bookings saved to VPS database
- QR codes generated server-side
