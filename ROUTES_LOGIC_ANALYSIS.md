# Ferry Routes Logic Analysis

## 🚢 Your Actual Ferry System

### How It SHOULD Work (Your Business Logic):

**Ferry routes are SEQUENTIAL with STOPS in order:**

```
Route 1: DABHOL (seq 1) → DHOPAVE (seq 2) → VESHVI (seq 3)
Route 2: DABHOL (seq 1) → BAGMANDALE (seq 2) → JAIGAD (seq 3)
Route 3: TAVSAL (seq 1) → AGARDANDA (seq 2) → DIGHI (seq 3)
Route 4: VASAI (seq 1) → BHAYANDER (seq 2) → VIRAR (seq 3)
```

**Critical Rule:** Ferry only travels START → END, NO mid-journey boarding!

### Example Scenarios:

**Scenario 1: Customer at DABHOL (sequence 1)**
- ✅ Can buy ticket to: DHOPAVE (seq 2) or VESHVI (seq 3) on Route 1
- ✅ Can buy ticket to: BAGMANDALE (seq 2) or JAIGAD (seq 3) on Route 2
- ❌ CANNOT board mid-journey

**Scenario 2: Customer at DHOPAVE (sequence 2)**
- ✅ Can ONLY buy ticket to: VESHVI (seq 3) - the NEXT stop
- ❌ CANNOT go back to DABHOL (seq 1) - ferry already left
- ❌ CANNOT jump routes

**Scenario 3: Customer wants DABHOL → VESHVI**
- **Option A:** Buy ONE ticket DABHOL → VESHVI (direct, ferry makes 1 stop in between)
- **Option B (Your Logic):** Buy TWO tickets:
  - Ticket 1: DABHOL → DHOPAVE
  - Ticket 2: DHOPAVE → VESHVI

**YOUR BUSINESS MODEL: Option B** - Customer buys separate tickets for each leg!

---

## 🔍 What Current Code Does

### Current getToBranches Logic (WRONG):

```php
public function getToBranches($branchId)
{
    $routeId = DB::table('routes')->where('branch_id', $branchId)->value('route_id');

    $toBranchIds = DB::table('routes')
        ->where('route_id', $routeId)
        ->where('branch_id', '!=', $branchId)
        ->pluck('branch_id');  // Gets ALL branches on route, ignoring sequence!
}
```

**Problem:** Returns ALL branches on the route, regardless of sequence!

**Example - Customer at DHOPAVE (seq 2):**
- Current code returns: DABHOL (seq 1), VESHVI (seq 3)
- ✅ Should only return: VESHVI (seq 3) - the NEXT stop forward

---

## ✅ CORRECT Logic (What You Need)

### Rule: Only show destinations with HIGHER sequence numbers!

```php
public function getToBranches($branchId)
{
    // Get current branch's sequence on each route
    $currentBranch = DB::table('routes')
        ->where('branch_id', $branchId)
        ->get(['route_id', 'sequence']);

    if ($currentBranch->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No routes found for this branch',
            'data' => []
        ]);
    }

    $destinations = collect();

    // For each route this branch is on
    foreach ($currentBranch as $current) {
        // Get branches with HIGHER sequence (forward direction only)
        $forwardBranches = DB::table('routes as r')
            ->join('branches as b', 'r.branch_id', '=', 'b.id')
            ->where('r.route_id', $current->route_id)
            ->where('r.sequence', '>', $current->sequence)  // ⭐ KEY: Only forward!
            ->orderBy('r.sequence')
            ->select('b.id', 'b.branch_name as name', 'r.sequence')
            ->get();

        $destinations = $destinations->merge($forwardBranches);
    }

    // Remove duplicates and sort
    $destinations = $destinations->unique('id')->sortBy('sequence')->values();

    return response()->json([
        'success' => true,
        'message' => 'To branches retrieved successfully',
        'data' => $destinations
    ]);
}
```

---

## 📊 Examples with Correct Logic

### Example 1: DABHOL (sequence 1)

**Routes DABHOL is on:**
- Route 1, sequence 1
- Route 2, sequence 1

**Available Destinations (sequence > 1):**

From Route 1:
- ✅ DHOPAVE (seq 2)
- ✅ VESHVI (seq 3)

From Route 2:
- ✅ BAGMANDALE (seq 2)
- ✅ JAIGAD (seq 3)

**Total destinations: 4** ✅ CORRECT

---

### Example 2: DHOPAVE (sequence 2)

**Routes DHOPAVE is on:**
- Route 1, sequence 2

**Available Destinations (sequence > 2):**

From Route 1:
- ✅ VESHVI (seq 3)

**Total destinations: 1** ✅ CORRECT (can only go forward to next stop)

---

### Example 3: VESHVI (sequence 3 - END)

**Routes VESHVI is on:**
- Route 1, sequence 3 (LAST STOP)

**Available Destinations (sequence > 3):**
- ❌ NONE (this is the end of the route)

**Total destinations: 0** ✅ CORRECT (ferry terminates here)

---

## 🌐 Web Code Analysis

### What web customer dashboard does:

```javascript
// Line ~300
document.getElementById('fromBranch').addEventListener('change', function() {
    const branchId = this.value;
    loadToBranches(branchId);
});

function loadToBranches(branchId) {
    fetch(`/booking/to-branches/${branchId}`)
        .then(response => response.json())
        .then(branches => {
            // Populates dropdown with returned branches
        });
}
```

**Web booking uses:** `BookingController::getToBranches()` (same endpoint as API)

**Current behavior:**
- Shows ALL branches on route (WRONG)
- Doesn't respect sequence order
- Allows backwards travel (impossible!)

---

## 📱 Mobile App Impact

### Current Mobile App Flow:

1. User selects "From Branch" → Calls `/api/branches/{id}/to-branches`
2. API returns ALL branches on route (ignoring sequence)
3. User sees impossible destinations (e.g., going backwards)

### After Fix:

1. User selects "From Branch"
2. API returns ONLY forward destinations (sequence > current)
3. User sees only valid next stops
4. Multiple tickets needed for multi-leg journeys

---

## 🔧 Required Changes

### 1. Fix getToBranches in ApiController ⚠️ CRITICAL

**File:** `app/Http/Controllers/Api/ApiController.php`

**Change from:**
```php
$toBranchIds = DB::table('routes')
    ->where('route_id', $routeId)
    ->where('branch_id', '!=', $branchId)
    ->pluck('branch_id');
```

**Change to:**
```php
// Get current branch sequence(s)
$currentBranches = DB::table('routes')
    ->where('branch_id', $branchId)
    ->get(['route_id', 'sequence']);

$destinations = collect();

foreach ($currentBranches as $current) {
    // Only get branches with HIGHER sequence (forward travel)
    $forwardBranches = DB::table('routes as r')
        ->join('branches as b', 'r.branch_id', '=', 'b.id')
        ->where('r.route_id', $current->route_id)
        ->where('r.sequence', '>', $current->sequence)
        ->orderBy('r.sequence')
        ->select('b.id', 'b.branch_name as name')
        ->get();

    $destinations = $destinations->merge($forwardBranches);
}

$branches = $destinations->unique('id')->values();
```

### 2. Fix getToBranches in BookingController (WEB)

**File:** `app/Http/Controllers/BookingController.php`

Same fix as above - web uses same endpoint.

### 3. Update Mobile App UI (Optional)

Add note: "For multi-stop journeys, purchase separate tickets for each leg"

---

## 💡 Business Logic Summary

### Your Ferry System:

1. **Routes are LINEAR:** A → B → C (in sequence order)
2. **One-way travel:** Can only go FORWARD (higher sequence)
3. **No mid-journey boarding:** Ferry departs from start, travels to end
4. **Multi-leg requires multiple tickets:**
   - DABHOL → VESHVI = buy 2 tickets:
     - Ticket 1: DABHOL → DHOPAVE
     - Ticket 2: DHOPAVE → VESHVI

### Current Code Problem:

- ❌ Shows ALL branches on route
- ❌ Allows impossible backwards travel
- ❌ Doesn't respect sequence order
- ❌ Confusing for customers

### After Fix:

- ✅ Shows ONLY valid forward destinations
- ✅ Respects sequence order
- ✅ Prevents impossible bookings
- ✅ Clear customer experience

---

## 🎯 Testing After Fix

### Test Case 1: DABHOL
- Should show: DHOPAVE, VESHVI, BAGMANDALE, JAIGAD
- Should NOT show: Nothing (DABHOL is start)

### Test Case 2: DHOPAVE
- Should show: VESHVI
- Should NOT show: DABHOL (backwards!)

### Test Case 3: VESHVI
- Should show: Nothing (end of route)
- Should NOT show: DABHOL, DHOPAVE (backwards!)

### Test Case 4: BAGMANDALE
- Should show: JAIGAD
- Should NOT show: DABHOL (backwards!)

---

## 📋 Deployment Checklist

- [ ] Fix getToBranches in ApiController (sequence logic)
- [ ] Fix getToBranches in BookingController (same fix)
- [ ] Test web booking with sequence constraints
- [ ] Test mobile app with sequence constraints
- [ ] Update user documentation (multi-leg requires multiple tickets)
- [ ] Deploy to production
- [ ] Verify no backwards travel possible

---

*Ready to implement the correct sequence-based logic!*
