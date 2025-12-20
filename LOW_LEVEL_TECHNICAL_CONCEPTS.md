# Low-Level Technical Concepts & Design Patterns

## Table of Contents
1. [Server-Side (Laravel) - Low-Level Concepts](#server-side-laravel)
2. [Mobile App (Flutter/Dart) - Low-Level Concepts](#mobile-app-flutter)
3. [Network & Communication Layer](#network-communication)
4. [Security & Authentication Mechanisms](#security-authentication)
5. [Data Persistence & State Management](#data-persistence)
6. [Performance Optimization Techniques](#performance-optimization)

---

## Server-Side (Laravel) - Low-Level Concepts

### 1. **Request Lifecycle & Middleware Pipeline**

**Concept:** Laravel uses a middleware pipeline pattern (Chain of Responsibility)

```
HTTP Request
    ↓
Entry Point (public/index.php)
    ↓
Bootstrap Laravel Application
    ↓
Service Provider Registration
    ↓
Middleware Pipeline (ordered execution)
    ├── CORS Handling
    ├── Session Management
    ├── CSRF Verification (web routes only)
    ├── Authentication (customer.api middleware)
    └── Route Resolution
    ↓
Controller Method Execution
    ↓
Response Generation
    ↓
Middleware Termination (reverse order)
    ↓
HTTP Response
```

**Low-Level Techniques:**
- **Pipeline Pattern:** Each middleware is a filter that can:
  - Inspect request
  - Modify request
  - Short-circuit pipeline (reject early)
  - Execute logic before/after controller

- **Dependency Injection Container:** Laravel uses reflection to:
  - Auto-resolve class dependencies
  - Inject dependencies into constructors
  - Manage singleton vs transient instances

- **Service Container Binding:** Services registered at boot time
  - Singleton binding (one instance per request cycle)
  - Contextual binding (different implementations based on context)

---

### 2. **Authentication Architecture - Sanctum Token System**

**Concept:** Stateless token-based authentication using database-backed tokens

**How It Works (Deep Dive):**

```
1. TOKEN GENERATION
   User Login Request
      ↓
   Validate Credentials (bcrypt hash comparison)
      ↓
   Generate Random Token (40 characters, cryptographically secure)
      ↓
   Hash Token (SHA-256)
      ↓
   Store in personal_access_tokens table:
      - tokenable_id: customer_id
      - tokenable_type: 'App\Models\Customer' (polymorphic)
      - name: 'auth-token'
      - token: hashed_value
      - abilities: JSON array of permissions
      ↓
   Return Plain Token to Client (ONLY TIME IT'S VISIBLE)


2. TOKEN VALIDATION (Every Request)
   Extract Bearer Token from Authorization Header
      ↓
   Hash Incoming Token (SHA-256)
      ↓
   Database Query:
      SELECT * FROM personal_access_tokens
      WHERE token = hashed_value
      AND (expires_at IS NULL OR expires_at > NOW())
      ↓
   If Found:
      Load Tokenable Model (Customer)
         ↓
      Set Auth Guard User
         ↓
      Proceed to Controller
   Else:
      Return 401 Unauthorized
```

**Low-Level Techniques:**
- **Cryptographic Hashing:** One-way SHA-256 prevents token theft from DB
- **Polymorphic Relationships:** One tokens table for multiple user types
- **Database Indexing:** Token column indexed for O(1) lookup
- **Lazy Loading Prevention:** Uses eager loading to avoid N+1 queries

---

### 3. **ORM (Object-Relational Mapping) - Eloquent**

**Concept:** Active Record pattern with lazy/eager loading strategies

**Query Builder Techniques:**

```
LOW-LEVEL QUERY EXECUTION FLOW:

PHP Code:
Booking::where('customer_id', 5)->with('ferry')->get()

↓ (Query Builder)

Builds Query Object:
- table: 'bookings'
- wheres: [['customer_id', '=', 5]]
- eager: ['ferry']

↓ (SQL Compilation)

Main Query:
SELECT * FROM bookings WHERE customer_id = 5

↓ (Eager Loading - Prevents N+1)

Relationship Query:
SELECT * FROM ferry_boats
WHERE id IN (1, 3, 5, 7)  -- IDs from main query

↓ (Hydration)

Convert DB Rows → Eloquent Models
Attach Related Models to Parent
Return Collection of Models
```

**Low-Level Optimizations:**
- **Query Result Caching:** Prepared statements cached in PDO
- **Connection Pooling:** Persistent DB connections reused
- **Lazy Evaluation:** Queries not executed until needed
- **Chunk Processing:** Large datasets processed in batches (memory efficient)

---

### 4. **Database Transactions - ACID Properties**

**Concept:** Ensure data integrity with atomic operations

```
TRANSACTION LIFECYCLE:

DB::beginTransaction()
   ↓
MySQL: START TRANSACTION
   ↓
Acquire Row-Level Locks (InnoDB)
   ↓
Execute Multiple Queries:
   INSERT INTO bookings ...
   UPDATE customers SET ...
   INSERT INTO audit_log ...
   ↓
If All Succeed:
   DB::commit()
      ↓
   MySQL: COMMIT
      ↓
   Release Locks
      ↓
   Changes Permanent

If Any Fail:
   DB::rollBack()
      ↓
   MySQL: ROLLBACK
      ↓
   Undo All Changes
      ↓
   Release Locks
```

**Low-Level Techniques:**
- **Isolation Levels:** READ COMMITTED (default in MySQL)
- **Row Locking:** Prevents concurrent modification
- **Write-Ahead Logging (WAL):** Changes logged before commit
- **Deadlock Detection:** MySQL detects and resolves deadlocks

---

### 5. **JSON Data Handling**

**Concept:** Store complex data structures in single database column

**Why JSON Column:**
```
TRADITIONAL APPROACH (Normalized):
bookings table (id, customer_id, total)
booking_items table (id, booking_id, item_id, quantity)
   ↓
Requires:
- JOIN query for every read
- Multiple INSERTs for write
- Foreign key constraints
- Index management

JSON APPROACH (Denormalized):
bookings table (id, customer_id, total, items JSON)
items: [{"item_rate_id":101, "quantity":2}]
   ↓
Benefits:
- Single SELECT (no JOIN)
- Single INSERT
- Immutable (historical accuracy)
- Faster reads
```

**Low-Level Storage:**
- **Binary JSON (MySQL 5.7+):** Stored as binary tree
- **Path Indexing:** Can index specific JSON paths
- **Compression:** JSON data compressed at storage layer

**Trade-offs:**
- ✅ Faster reads (no joins)
- ✅ Simpler queries
- ❌ Can't query individual items efficiently
- ❌ No referential integrity

---

### 6. **Payment Gateway Integration - Razorpay**

**Concept:** Secure payment flow with signature verification

```
CRYPTOGRAPHIC SIGNATURE FLOW:

1. CREATE ORDER
   Server generates order_id
      ↓
   Razorpay stores order_id + amount
      ↓
   Return order_id to app

2. PAYMENT COMPLETION
   User pays via Razorpay UI
      ↓
   Razorpay generates:
      payment_id
      ↓
   Calculate signature:
   HMAC-SHA256(order_id + "|" + payment_id, secret_key)
      ↓
   Return: {order_id, payment_id, signature}

3. VERIFICATION (Server-Side)
   Receive: order_id, payment_id, signature
      ↓
   Re-calculate signature using same formula
      ↓
   Compare signatures (constant-time comparison)
      ↓
   If match: payment authentic
   If mismatch: potential tampering
```

**Low-Level Security Techniques:**
- **HMAC (Hash-based Message Authentication):** Prevents tampering
- **Constant-Time Comparison:** Prevents timing attacks
- **Idempotency:** Same order_id can't be paid twice
- **Server-Side Validation:** Never trust client-calculated amounts

---

### 7. **Validation & Sanitization**

**Concept:** Input filtering at multiple layers

```
VALIDATION PIPELINE:

HTTP Request
   ↓
1. FRAMEWORK VALIDATION (FormRequest)
   - Type checking (integer, string, email)
   - Format validation (regex, date format)
   - Presence validation (required, nullable)
   - Custom rules (business logic)
   ↓
2. DATABASE VALIDATION (Model)
   - Length constraints (VARCHAR limits)
   - Data type enforcement (INT, DECIMAL)
   - Null constraints
   ↓
3. BUSINESS LOGIC VALIDATION (Service Layer)
   - Cross-field validation
   - External API checks
   - Inventory/capacity checks
```

**Low-Level Techniques:**
- **Parameterized Queries:** Prevent SQL injection
- **HTML Entity Encoding:** Prevent XSS attacks
- **Type Juggling Prevention:** Strict type checking
- **Mass Assignment Protection:** $fillable/$guarded arrays

---

### 8. **Caching Strategies**

**Concept:** Multi-layer caching for performance

```
CACHE HIERARCHY:

1. APPLICATION CACHE (Laravel Cache)
   - OPcache (PHP bytecode cache)
   - APCu (in-memory data cache)
   - Redis (distributed cache)

2. QUERY CACHE (MySQL)
   - Identical queries return cached results
   - Invalidated on table updates

3. HTTP CACHE
   - Browser cache (Cache-Control headers)
   - CDN cache (for static assets)
```

**Low-Level Implementation:**
- **Cache Tags:** Group related cache entries
- **Cache-Aside Pattern:** Check cache → if miss, query DB → store in cache
- **Write-Through Cache:** Write to cache and DB simultaneously
- **TTL (Time To Live):** Automatic cache expiration

---

## Mobile App (Flutter/Dart) - Low-Level Concepts

### 1. **Asynchronous Programming - Futures & Async/Await**

**Concept:** Non-blocking I/O for network operations

```
SYNCHRONOUS (Blocking):
main()
   ↓
login()  ← waits 2 seconds
   ↓
fetchBranches()  ← waits 1 second
   ↓
UI renders  ← Total 3 seconds of blocking


ASYNCHRONOUS (Non-blocking):
main()
   ↓
login() → Future<LoginResponse>
   ↓ (immediately returns Future)
UI shows loading spinner
   ↓ (2 seconds later, Future completes)
then((response) => fetchBranches())
   ↓ (immediately returns Future)
UI updates with login data
   ↓ (1 second later, Future completes)
then((branches) => updateUI())
   ↓
UI shows branches

Total UI blocking: ~0ms (all async)
```

**Low-Level Techniques:**
- **Event Loop:** Single-threaded async execution model
- **Microtask Queue:** High-priority async tasks
- **Event Queue:** Regular async tasks (timers, I/O)
- **Isolates:** Separate memory spaces for CPU-intensive work

---

### 2. **HTTP Client - Network Layer**

**Concept:** RESTful API communication with automatic retries

```
HTTP REQUEST FLOW:

1. DART CODE
   http.get('https://api.com/branches')
      ↓
2. PLATFORM CHANNEL (Method Channel)
   Dart → Native (Android/iOS)
      ↓
3. NATIVE HTTP STACK
   Android: OkHttp
   iOS: URLSession
      ↓
4. TCP CONNECTION
   - DNS Resolution (domain → IP)
   - TCP 3-way handshake
   - TLS Handshake (HTTPS)
      ↓
5. HTTP REQUEST
   GET /api/branches HTTP/1.1
   Host: api.com
   Authorization: Bearer token
   Accept: application/json
      ↓
6. SERVER RESPONSE
   HTTP/1.1 200 OK
   Content-Type: application/json
   {"success":true,"data":[...]}
      ↓
7. PARSE JSON
   jsonDecode(response.body)
      ↓
8. DESERIALIZE
   Branch.fromJson(json) → Dart Objects
```

**Low-Level Optimizations:**
- **Connection Pooling:** Reuse TCP connections
- **HTTP/2 Multiplexing:** Multiple requests on one connection
- **Gzip Compression:** Reduce payload size
- **Request Interception:** Modify requests globally (add auth headers)

---

### 3. **State Management - Reactive Programming**

**Concept:** Unidirectional data flow with streams

```
REACTIVE DATA FLOW:

User Action (Tap Button)
   ↓
Event Emitted
   ↓
State Management (setState, Provider, BLoC)
   ↓
Update State Object
   ↓
Notify Listeners (Observer Pattern)
   ↓
Widget Rebuild (Reactive)
   ↓
UI Updates
```

**Low-Level Techniques:**
- **Observer Pattern:** Widgets listen to state changes
- **Immutable State:** New state objects, never mutate
- **Diffing Algorithm:** Only rebuild changed widgets
- **Widget Tree Optimization:** Const constructors prevent rebuilds

---

### 4. **JSON Serialization/Deserialization**

**Concept:** Convert between JSON strings and Dart objects

```
DESERIALIZATION PROCESS:

JSON String:
'{"id":1,"name":"Branch A"}'
   ↓
jsonDecode() → Dart Map
{
  "id": 1,
  "name": "Branch A"
}
   ↓
Type-Safe Conversion:
Branch.fromJson(map) {
  id: map['id'] as int,
  name: map['name'] as String,
}
   ↓
Dart Object:
Branch(id: 1, name: "Branch A")
```

**Low-Level Techniques:**
- **Type Casting:** Runtime type checking
- **Null Safety:** Explicit nullable types
- **Factory Constructors:** Named constructors for deserialization
- **Code Generation (json_serializable):** Auto-generate toJson/fromJson

---

### 5. **Local Storage - Shared Preferences**

**Concept:** Key-value storage for persistent data

```
STORAGE MECHANISM:

Dart Code:
await SharedPreferences.setString('token', 'abc123')
   ↓
Platform Channel
   ↓
Android: SharedPreferences (XML file)
iOS: NSUserDefaults (plist file)
   ↓
File System Write
   ↓
Encrypted Storage (Android Keystore, iOS Keychain)
```

**Low-Level Implementation:**
- **Async Write:** Non-blocking I/O
- **Atomic Writes:** Prevent data corruption
- **Platform-Specific APIs:** Native storage mechanisms
- **Encryption:** Secure storage for sensitive data

---

### 6. **Widget Lifecycle & Build Process**

**Concept:** Declarative UI with automatic rebuilds

```
WIDGET LIFECYCLE:

1. CREATE WIDGET
   Widget build(BuildContext context) {
     return Text('Hello');
   }
      ↓
2. CREATE ELEMENT
   Element tree node created
      ↓
3. MOUNT ELEMENT
   Attach to widget tree
      ↓
4. BUILD RENDER OBJECT
   RenderObject for painting
      ↓
5. LAYOUT
   Calculate size & position
      ↓
6. PAINT
   Draw pixels to screen
      ↓
7. COMPOSE
   GPU renders frame
      ↓
8. STATE CHANGE
   setState() called
      ↓
   Mark widget dirty
      ↓
   Rebuild only dirty widgets
      ↓
   Diff old vs new widget tree
      ↓
   Update only changed RenderObjects
```

**Low-Level Optimizations:**
- **Widget Recycling:** Reuse widget instances
- **Const Constructors:** Widgets never rebuild
- **Keys:** Preserve widget state during reordering
- **RepaintBoundary:** Isolate painting regions

---

## Network & Communication Layer

### 1. **HTTP Protocol Deep Dive**

**Request Anatomy:**
```
POST /api/bookings HTTP/1.1
Host: unfurling.ninja
Connection: keep-alive
Content-Type: application/json
Content-Length: 245
Authorization: Bearer 16|xxx
Accept-Encoding: gzip, deflate
User-Agent: Dart/2.19 (dart:io)

{"ferry_id":1,"from_branch_id":2,...}
```

**Response Anatomy:**
```
HTTP/1.1 201 Created
Server: nginx/1.18.0
Date: Mon, 16 Dec 2025 10:30:00 GMT
Content-Type: application/json
Content-Length: 312
Connection: keep-alive

{"success":true,"data":{...}}
```

**Low-Level Mechanisms:**
- **Persistent Connections:** Connection reuse (keep-alive)
- **Chunked Transfer:** Stream large responses
- **Content Negotiation:** Accept/Content-Type matching
- **Status Codes:** Semantic meaning (2xx success, 4xx client error, 5xx server error)

---

### 2. **TLS/SSL Encryption**

**Concept:** Encrypted communication channel

```
TLS HANDSHAKE:

1. CLIENT HELLO
   Supported cipher suites, TLS version
      ↓
2. SERVER HELLO
   Selected cipher suite, certificate
      ↓
3. CERTIFICATE VERIFICATION
   Verify server identity (CA signature)
      ↓
4. KEY EXCHANGE
   Generate shared secret (Diffie-Hellman)
      ↓
5. ENCRYPTED COMMUNICATION
   All data encrypted with symmetric key
```

**Low-Level Security:**
- **Certificate Pinning:** Prevent MITM attacks
- **Forward Secrecy:** Unique session keys
- **AES-256 Encryption:** Symmetric encryption
- **RSA/ECC:** Asymmetric key exchange

---

### 3. **DNS Resolution**

```
DOMAIN → IP MAPPING:

App requests: https://unfurling.ninja
   ↓
Check Local DNS Cache
   ↓
If miss: Query DNS Server
   ↓
Recursive DNS Lookup:
   . (root) → .ninja → unfurling.ninja
   ↓
Return IP: 123.45.67.89
   ↓
Cache result (TTL: 3600s)
   ↓
Connect to IP
```

---

## Security & Authentication Mechanisms

### 1. **Password Hashing - Bcrypt**

```
REGISTRATION:
Password: "mypassword123"
   ↓
Generate Salt (random 16 bytes)
   ↓
Bcrypt Hash (cost factor: 10)
   ↓
Hash: $2y$10$abcdef... (60 characters)
   ↓
Store in database


LOGIN:
Input: "mypassword123"
   ↓
Retrieve stored hash from DB
   ↓
Extract salt from hash
   ↓
Hash input with same salt & cost
   ↓
Constant-time comparison
   ↓
Match: authenticated
```

**Low-Level Security:**
- **Adaptive Hashing:** Cost factor increases over time
- **Rainbow Table Resistance:** Unique salt per password
- **Timing Attack Prevention:** Constant-time comparison
- **Computational Delay:** Slow by design (prevent brute force)

---

### 2. **CSRF Protection (Web Only)**

```
FORM SUBMISSION:

1. Page Load
   Server generates CSRF token
   Stores in session
   Embeds in HTML form

2. Form Submit
   Browser sends token + data

3. Server Validation
   Compare token with session
   If match: process
   If mismatch: reject (403)
```

**Not Used in API** because:
- Token-based auth (stateless)
- No cookies (no CSRF risk)
- CORS handles cross-origin

---

### 3. **CORS (Cross-Origin Resource Sharing)**

```
PREFLIGHT REQUEST:

Browser: "Can I access API from app.com?"
   ↓
OPTIONS /api/bookings
Origin: https://app.com
   ↓
Server Response:
Access-Control-Allow-Origin: https://app.com
Access-Control-Allow-Methods: GET, POST
Access-Control-Allow-Headers: Authorization
   ↓
If allowed: Proceed with actual request
```

---

## Data Persistence & State Management

### 1. **Database Indexing**

```
WITHOUT INDEX:
SELECT * FROM bookings WHERE customer_id = 5
   ↓
Full Table Scan (O(n))
Reads all 1,000,000 rows
Time: 2000ms


WITH INDEX:
CREATE INDEX idx_customer ON bookings(customer_id)
   ↓
B-Tree Index Structure
   ↓
Binary Search (O(log n))
Reads ~20 rows
Time: 5ms
```

**Index Types:**
- **B-Tree:** Default, range queries
- **Hash:** Equality lookups only
- **Full-Text:** Text search
- **Spatial:** Geographic data

---

### 2. **Connection Pooling**

```
WITHOUT POOLING:
Request 1: Open connection → query → close
Request 2: Open connection → query → close
(Expensive: 3-way handshake each time)


WITH POOLING:
Connection Pool (10 connections)
   ↓
Request 1: Borrow connection → query → return
Request 2: Borrow connection → query → return
(Reuse connections, much faster)
```

---

### 3. **Caching Strategies**

**Cache-Aside Pattern:**
```
Read Request
   ↓
Check Cache
   ↓
If Hit: Return cached data
If Miss:
   ↓
   Query Database
      ↓
   Store in Cache (TTL: 1 hour)
      ↓
   Return data
```

**Write-Through Pattern:**
```
Write Request
   ↓
Write to Database
   ↓
Write to Cache (simultaneously)
   ↓
Return success
```

---

## Performance Optimization Techniques

### 1. **Lazy Loading vs Eager Loading**

**Lazy Loading (N+1 Problem):**
```
$bookings = Booking::all();  // 1 query

foreach ($bookings as $booking) {
    echo $booking->ferry->name;  // N queries (one per booking)
}

Total Queries: 1 + 100 = 101
```

**Eager Loading (Solution):**
```
$bookings = Booking::with('ferry')->all();  // 2 queries

Query 1: SELECT * FROM bookings
Query 2: SELECT * FROM ferries WHERE id IN (1,3,5,7,...)

Total Queries: 2 (much faster)
```

---

### 2. **Database Query Optimization**

```
SLOW QUERY:
SELECT * FROM item_rates
WHERE branch_id = 1
AND ending_date IS NULL
   ↓
Full table scan, returns all columns


OPTIMIZED:
SELECT id, item_name, item_rate, item_lavy
FROM item_rates
WHERE branch_id = 1
AND ending_date IS NULL
LIMIT 20
   ↓
Index on (branch_id, ending_date)
Returns only needed columns
Limits result set
```

---

### 3. **HTTP Response Compression**

```
UNCOMPRESSED:
Response Size: 25 KB JSON
Transfer Time: 500ms (50 Kbps)


GZIP COMPRESSED:
Original: 25 KB
Compressed: 3 KB (88% reduction)
Transfer Time: 60ms
   ↓
Headers:
Content-Encoding: gzip
Accept-Encoding: gzip, deflate
```

---

### 4. **Pagination**

```
WITHOUT PAGINATION:
SELECT * FROM bookings
ORDER BY created_at DESC
   ↓
Returns 10,000 rows
Memory: 50 MB
Time: 5 seconds


WITH PAGINATION:
SELECT * FROM bookings
ORDER BY created_at DESC
LIMIT 25 OFFSET 0
   ↓
Returns 25 rows
Memory: 125 KB
Time: 50ms
```

---

## Summary of Key Techniques

### Server-Side:
1. **Middleware Pipeline** - Request filtering
2. **Dependency Injection** - Auto-wiring
3. **ORM with Eager Loading** - N+1 prevention
4. **Database Transactions** - ACID compliance
5. **Bcrypt Hashing** - Password security
6. **Token-Based Auth** - Stateless authentication
7. **Query Optimization** - Indexing, caching
8. **JSON Denormalization** - Fast reads

### Mobile App:
1. **Async/Await** - Non-blocking I/O
2. **HTTP Connection Pooling** - Reuse connections
3. **JSON Serialization** - Type-safe parsing
4. **Reactive UI** - Observer pattern
5. **Local Storage** - Persistent state
6. **Widget Diffing** - Efficient rebuilds
7. **Platform Channels** - Native integration

### Network:
1. **TLS Encryption** - Secure transport
2. **HMAC Signatures** - Data integrity
3. **Gzip Compression** - Bandwidth optimization
4. **Connection Keep-Alive** - Reduce latency

---

**Last Updated:** December 16, 2025
