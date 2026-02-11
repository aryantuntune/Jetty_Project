# DATABASE MIGRATION INSTRUCTIONS
## Jetty Ferry Management System: MySQL → PostgreSQL

---

## 📋 OVERVIEW

This document provides **complete, step-by-step instructions** for migrating the Jetty Ferry Management System database from MySQL to PostgreSQL using Prisma ORM.

**Migration Strategy**: Schema-first approach using Prisma for type safety and modern database management.

---

## 🎯 MIGRATION GOALS

### Technical Stack
- **Source**: MySQL 8.0+ (jetty_db)
- **Target**: PostgreSQL 15+
- **ORM**: Prisma (for type safety and migrations)
- **Runtime**: Node.js 20+ with TypeScript
- **Primary Keys**: BIGSERIAL (auto-incrementing) with optional UUID for public-facing IDs

### Success Criteria
✅ Zero data loss  
✅ All relationships preserved  
✅ Improved performance with PostgreSQL features  
✅ Type-safe database access via Prisma  
✅ Backward compatibility with existing APIs during transition  

---

## 📊 TYPE MAPPING REFERENCE

### Critical Type Conversions

| MySQL Type | PostgreSQL Type | Prisma Type | Notes |
|------------|-----------------|-------------|-------|
| `BIGINT UNSIGNED AUTO_INCREMENT` | `BIGSERIAL` or `IDENTITY` | `BigInt @id @default(autoincrement())` | Primary keys |
| `BIGINT UNSIGNED` | `BIGINT` | `BigInt` | Foreign keys |
| `VARCHAR(255)` | `VARCHAR(255)` or `TEXT` | `String` | TEXT performs well in PostgreSQL |
| `TEXT` | `TEXT` | `String` | No length limit |
| `LONGTEXT` | `TEXT` | `String` | PostgreSQL TEXT handles all sizes |
| `TINYINT(1)` | `BOOLEAN` | `Boolean` | Native boolean type |
| `DECIMAL(10,2)` | `NUMERIC(10,2)` | `Decimal` | Exact numeric type |
| `DATETIME` | `TIMESTAMP` | `DateTime` | Without timezone |
| `TIMESTAMP` | `TIMESTAMPTZ` | `DateTime` | **Always use WITH TIME ZONE** for ferry scheduling |
| `JSON` | `JSONB` | `Json` | **Use JSONB for indexing and performance** |
| `ENUM('a','b')` | `TEXT CHECK` or native `ENUM` | `enum` | Prisma native enum support |

### Important Differences

**MySQL**:
```sql
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
is_active TINYINT(1) DEFAULT 1
```

**PostgreSQL**:
```sql
id BIGSERIAL PRIMARY KEY
-- OR using IDENTITY (SQL standard)
id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY

created_at TIMESTAMPTZ DEFAULT NOW()
updated_at TIMESTAMPTZ DEFAULT NOW()
is_active BOOLEAN DEFAULT TRUE
```

**Prisma Schema**:
```prisma
id         BigInt   @id @default(autoincrement())
created_at DateTime @default(now())
updated_at DateTime @updatedAt
is_active  Boolean  @default(true)
```

---

## 🗂️ MIGRATION EXECUTION ORDER

### ⚠️ CRITICAL: Follow This Exact Order

Foreign key dependencies require tables to be created in this specific sequence:

### **Phase A: Master Data (No Dependencies)**
1. `item_categories` - Passenger/cargo type definitions
2. `special_charges` - Surcharges and seasonal fees
3. `guests` - Frequent passenger records

### **Phase B: Infrastructure Setup**
4. `branches` ⚠️ **Self-referencing table** - Ferry terminals
5. `ferryboats` - Ferry vessels (depends on branches)
6. `houseboat_rooms` - Houseboat room types (JSONB amenities)

### **Phase C: Users & Authentication**
7. `users` - Staff accounts (depends on branches, ferryboats)
8. `customers` - Online customer accounts

### **Phase D: Operational Configuration**
9. `ferry_schedules` - Departure times (depends on branches, ferryboats)
10. `item_rates` - Pricing rules (depends on branches)

### **Phase E: Transactional Data**
11. `tickets` - Counter-issued tickets (depends on branches, ferryboats, customers, users)
12. `ticket_lines` - Ticket line items (depends on tickets)
13. `bookings` - Online bookings with JSONB items (depends on customers, branches, ferryboats, tickets)
14. `houseboat_bookings` - Tourism reservations (depends on houseboat_rooms)

### **Phase F: Reporting & Metadata**
15. `daily_summaries` - Aggregated daily reports (depends on branches, ferryboats)
16. `branch_transfers` - Employee transfer records (depends on users, branches)

### **Phase G: System Tables**
17. `sessions` - Session storage
18. `cache` & `cache_locks` - Caching layer
19. `failed_jobs` - Queue failure tracking
20. `personal_access_tokens` - API authentication tokens

---

## 🔧 STEP-BY-STEP MIGRATION PROCESS

### **STEP 1: Install PostgreSQL**

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install postgresql-15 postgresql-contrib-15

# macOS
brew install postgresql@15

# Start PostgreSQL
sudo systemctl start postgresql  # Linux
brew services start postgresql@15  # macOS

# Verify installation
psql --version
```

### **STEP 2: Create PostgreSQL Database & User**

```bash
# Switch to postgres user
sudo -u postgres psql

# In psql prompt:
CREATE DATABASE jetty_db;
CREATE USER jetty_user WITH ENCRYPTED PASSWORD 'your_secure_password_here';
GRANT ALL PRIVILEGES ON DATABASE jetty_db TO jetty_user;

# Connect to new database
\c jetty_db

# Grant schema privileges (PostgreSQL 15+)
GRANT ALL ON SCHEMA public TO jetty_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO jetty_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO jetty_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO jetty_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO jetty_user;

# Enable required extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";  -- For UUID generation
CREATE EXTENSION IF NOT EXISTS "pg_trgm";     -- For fuzzy text search

# Exit psql
\q
```

### **STEP 3: Setup Node.js Project with Prisma**

```bash
# Create migration project directory
mkdir jetty-db-migration
cd jetty-db-migration

# Initialize Node.js project
npm init -y

# Install dependencies
npm install prisma @prisma/client
npm install -D typescript @types/node tsx

# Install migration utilities
npm install mysql2 pg dotenv

# Initialize Prisma
npx prisma init --datasource-provider postgresql
```

This creates:
- `prisma/schema.prisma` - Database schema definition
- `.env` - Database connection strings

### **STEP 4: Configure Environment Variables**

Edit `.env`:

```env
# Source MySQL Database (for data migration)
MYSQL_HOST=localhost
MYSQL_PORT=3306
MYSQL_DATABASE=jetty_db
MYSQL_USER=root
MYSQL_PASSWORD=your_mysql_password

# Target PostgreSQL Database
DATABASE_URL="postgresql://jetty_user:your_secure_password_here@localhost:5432/jetty_db?schema=public"
```

### **STEP 5: Create Prisma Schema**

Edit `prisma/schema.prisma`:

```prisma
// Prisma Schema for Jetty Ferry Management System
// Target: PostgreSQL 15+

generator client {
  provider = "prisma-client-js"
}

datasource db {
  provider = "postgresql"
  url      = env("DATABASE_URL")
}

// ============================================
// ENUMS
// ============================================

enum UserRole {
  SUPER_ADMIN  // role_id = 1
  ADMIN        // role_id = 2
  MANAGER      // role_id = 3
  OPERATOR     // role_id = 4
  CHECKER      // role_id = 5
}

enum PaymentMode {
  Cash
  Card
  UPI
  Online
}

enum BookingStatus {
  pending
  confirmed
  completed
  cancelled
}

enum HouseboatStatus {
  pending
  confirmed
  checked_in
  completed
  cancelled
}

// ============================================
// PHASE A: MASTER DATA
// ============================================

model ItemCategory {
  id            BigInt   @id @default(autoincrement())
  category_name String   @unique @db.VarChar(255)
  levy          Decimal  @default(0.00) @db.Decimal(10, 2)
  is_active     Boolean  @default(true)
  created_at    DateTime @default(now()) @db.Timestamptz
  updated_at    DateTime @updatedAt @db.Timestamptz

  @@map("item_categories")
}

model SpecialCharge {
  id              BigInt    @id @default(autoincrement())
  charge_name     String    @db.VarChar(255)
  charge_amount   Decimal   @db.Decimal(10, 2)
  charge_type     String    @default("Fixed") @db.VarChar(50)
  applicable_from DateTime  @db.Date
  applicable_to   DateTime? @db.Date
  is_active       Boolean   @default(true)
  created_at      DateTime  @default(now()) @db.Timestamptz
  updated_at      DateTime  @updatedAt @db.Timestamptz

  @@index([applicable_from, applicable_to])
  @@index([is_active])
  @@map("special_charges")
}

model Guest {
  id              BigInt    @id @default(autoincrement())
  first_name      String    @db.VarChar(255)
  last_name       String    @db.VarChar(255)
  mobile          String?   @db.VarChar(15)
  email           String?   @db.VarChar(255)
  address         String?   @db.Text
  id_proof_type   String?   @db.VarChar(50)
  id_proof_number String?   @db.VarChar(100)
  created_at      DateTime  @default(now()) @db.Timestamptz
  updated_at      DateTime  @updatedAt @db.Timestamptz

  @@index([first_name, last_name])
  @@index([mobile])
  @@map("guests")
}

// ============================================
// PHASE B: INFRASTRUCTURE
// ============================================

model Branch {
  id              BigInt     @id @default(autoincrement())
  branch_id       String     @unique @db.VarChar(50)
  branch_name     String     @db.VarChar(255)
  branch_address  String?    @db.Text
  branch_phone    String?    @db.VarChar(20)
  dest_branch_id  BigInt?    // Self-referencing foreign key
  ferry_boat_id   BigInt?
  latitude        Decimal?   @db.Decimal(10, 8)
  longitude       Decimal?   @db.Decimal(11, 8)
  is_active       Boolean    @default(true)
  created_at      DateTime   @default(now()) @db.Timestamptz
  updated_at      DateTime   @updatedAt @db.Timestamptz

  // Self-referencing relationship
  destinationBranch Branch?  @relation("BranchDestination", fields: [dest_branch_id], references: [id], onDelete: SetNull)
  sourceBranches    Branch[] @relation("BranchDestination")

  // Ferry relationship
  defaultFerry      FerryBoat? @relation("BranchDefaultFerry", fields: [ferry_boat_id], references: [id], onDelete: SetNull)

  // Related records
  ferryboats        FerryBoat[]      @relation("FerryBranchHome")
  users             User[]
  ferrySchedules    FerrySchedule[]
  itemRates         ItemRate[]
  ticketsFrom       Ticket[]         @relation("TicketFromBranch")
  ticketsTo         Ticket[]         @relation("TicketToBranch")
  bookingsFrom      Booking[]        @relation("BookingFromBranch")
  bookingsTo        Booking[]        @relation("BookingToBranch")
  dailySummaries    DailySummary[]
  transfersFrom     BranchTransfer[] @relation("TransferFromBranch")
  transfersTo       BranchTransfer[] @relation("TransferToBranch")

  @@index([is_active])
  @@map("branches")
}

model FerryBoat {
  id                  BigInt          @id @default(autoincrement())
  number              String          @db.VarChar(50)
  name                String          @db.VarChar(255)
  branch_id           BigInt?
  capacity_passengers Int             @default(0)
  capacity_vehicles   Int             @default(0)
  is_active           Boolean         @default(true)
  created_at          DateTime        @default(now()) @db.Timestamptz
  updated_at          DateTime        @updatedAt @db.Timestamptz

  // Relationships
  branch              Branch?         @relation("FerryBranchHome", fields: [branch_id], references: [id], onDelete: SetNull)
  defaultForBranches  Branch[]        @relation("BranchDefaultFerry")
  users               User[]
  tickets             Ticket[]
  bookings            Booking[]
  ferrySchedules      FerrySchedule[]
  dailySummaries      DailySummary[]

  @@index([branch_id])
  @@index([is_active])
  @@map("ferryboats")
}

model HouseboatRoom {
  id              BigInt             @id @default(autoincrement())
  name            String             @db.VarChar(255)
  description     String?            @db.Text
  price           Decimal            @db.Decimal(10, 2)
  capacity_adults Int                @default(2)
  capacity_kids   Int                @default(2)
  amenities       Json?              @db.JsonB  // ["AC", "TV", "Breakfast"]
  image_url       String?            @db.VarChar(500)
  gallery         Json?              @db.JsonB  // ["img1.jpg", "img2.jpg"]
  is_available    Boolean            @default(true)
  created_at      DateTime           @default(now()) @db.Timestamptz
  updated_at      DateTime           @updatedAt @db.Timestamptz

  bookings        HouseboatBooking[]

  @@map("houseboat_rooms")
}

// ============================================
// PHASE C: USERS & AUTHENTICATION
// ============================================

model User {
  id                 BigInt           @id @default(autoincrement())
  name               String           @db.VarChar(255)
  email              String           @unique @db.VarChar(255)
  email_verified_at  DateTime?        @db.Timestamptz
  password           String           @db.VarChar(255)
  mobile             String?          @db.VarChar(15)
  role_id            Int              @default(4)  // 1-5: roles
  branch_id          BigInt?
  ferry_boat_id      BigInt?
  remember_token     String?          @db.VarChar(100)
  created_at         DateTime         @default(now()) @db.Timestamptz
  updated_at         DateTime         @updatedAt @db.Timestamptz

  // Relationships
  branch             Branch?          @relation(fields: [branch_id], references: [id], onDelete: SetNull)
  ferryBoat          FerryBoat?       @relation(fields: [ferry_boat_id], references: [id], onDelete: SetNull)
  verifiedTickets    Ticket[]         @relation("TicketChecker")
  branchTransfers    BranchTransfer[] @relation("TransferUser")
  approvedTransfers  BranchTransfer[] @relation("TransferApprover")

  @@index([role_id])
  @@index([branch_id])
  @@map("users")
}

model Customer {
  id                 BigInt             @id @default(autoincrement())
  first_name         String             @db.VarChar(255)
  last_name          String             @db.VarChar(255)
  email              String             @unique @db.VarChar(255)
  email_verified_at  DateTime?          @db.Timestamptz
  password           String?            @db.VarChar(255)  // Nullable for Google sign-in
  mobile             String             @db.VarChar(15)
  profile_image      String?            @db.VarChar(500)
  google_id          String?            @unique @db.VarChar(255)
  remember_token     String?            @db.VarChar(100)
  created_at         DateTime           @default(now()) @db.Timestamptz
  updated_at         DateTime           @updatedAt @db.Timestamptz

  // Relationships
  tickets            Ticket[]
  bookings           Booking[]
  houseboatBookings  HouseboatBooking[]

  @@index([email])
  @@index([mobile])
  @@index([google_id])
  @@map("customers")
}

// ============================================
// PHASE D: OPERATIONAL CONFIGURATION
// ============================================

model FerrySchedule {
  id            BigInt    @id @default(autoincrement())
  hour          Int
  minute        Int
  schedule_time String    @db.Time  // PostgreSQL TIME type
  branch_id     BigInt
  ferry_boat_id BigInt
  is_active     Boolean   @default(true)
  created_at    DateTime  @default(now()) @db.Timestamptz
  updated_at    DateTime  @updatedAt @db.Timestamptz

  // Relationships
  branch        Branch    @relation(fields: [branch_id], references: [id], onDelete: Cascade)
  ferryBoat     FerryBoat @relation(fields: [ferry_boat_id], references: [id], onDelete: Cascade)

  @@index([schedule_time])
  @@index([is_active])
  @@index([branch_id, ferry_boat_id])  // Composite index for performance
  @@map("ferry_schedules")
}

model ItemRate {
  id                  BigInt    @id @default(autoincrement())
  item_name           String    @db.VarChar(255)
  item_rate           Decimal   @db.Decimal(10, 2)
  item_lavy           Decimal   @default(0.00) @db.Decimal(10, 2)
  item_surcharge_pct  Decimal   @default(0.00) @db.Decimal(5, 2)
  branch_id           BigInt
  is_vehicle          Boolean   @default(false)
  starting_date       DateTime  @db.Date
  ending_date         DateTime? @db.Date
  created_at          DateTime  @default(now()) @db.Timestamptz
  updated_at          DateTime  @updatedAt @db.Timestamptz

  // Relationships
  branch              Branch    @relation(fields: [branch_id], references: [id], onDelete: Cascade)

  @@index([branch_id])
  @@index([is_vehicle])
  @@index([starting_date, ending_date])
  @@map("item_rates")
}

// ============================================
// PHASE E: TRANSACTIONAL DATA
// ============================================

model Ticket {
  id              BigInt       @id @default(autoincrement())
  ticket_no       String       @unique @db.VarChar(50)
  ticket_date     DateTime     @db.Date
  branch_id       BigInt
  dest_branch_id  BigInt
  ferry_boat_id   BigInt
  ferry_time      String       @db.Time
  payment_mode    String       @default("Cash") @db.VarChar(50)
  total_amount    Decimal      @db.Decimal(10, 2)
  customer_id     BigInt?
  verified_at     DateTime?    @db.Timestamptz
  checker_id      BigInt?
  created_at      DateTime     @default(now()) @db.Timestamptz
  updated_at      DateTime     @updatedAt @db.Timestamptz

  // Relationships
  fromBranch      Branch       @relation("TicketFromBranch", fields: [branch_id], references: [id], onDelete: Cascade)
  toBranch        Branch       @relation("TicketToBranch", fields: [dest_branch_id], references: [id], onDelete: Cascade)
  ferryBoat       FerryBoat    @relation(fields: [ferry_boat_id], references: [id], onDelete: Cascade)
  customer        Customer?    @relation(fields: [customer_id], references: [id], onDelete: SetNull)
  checker         User?        @relation("TicketChecker", fields: [checker_id], references: [id], onDelete: SetNull)
  ticketLines     TicketLine[]
  booking         Booking?

  @@index([ticket_no])
  @@index([ticket_date])
  @@index([branch_id])
  @@index([verified_at])
  @@index([customer_id])
  @@map("tickets")
}

model TicketLine {
  id           BigInt   @id @default(autoincrement())
  ticket_id    BigInt
  item_name    String   @db.VarChar(255)
  qty          Int      @default(1)
  rate         Decimal  @db.Decimal(10, 2)
  levy         Decimal  @default(0.00) @db.Decimal(10, 2)
  amount       Decimal  @db.Decimal(10, 2)
  vehicle_name String?  @db.VarChar(255)
  vehicle_no   String?  @db.VarChar(50)
  created_at   DateTime @default(now()) @db.Timestamptz
  updated_at   DateTime @updatedAt @db.Timestamptz

  // Relationships
  ticket       Ticket   @relation(fields: [ticket_id], references: [id], onDelete: Cascade)

  @@index([ticket_id])
  @@index([vehicle_no])
  @@map("ticket_lines")
}

model Booking {
  id            BigInt        @id @default(autoincrement())
  customer_id   BigInt
  from_branch   BigInt
  to_branch     BigInt
  ferry_boat_id BigInt
  ferry_time    String        @db.Time
  items         Json          @db.JsonB  // CRITICAL: Array of booking items
  total_amount  Decimal       @db.Decimal(10, 2)
  payment_id    String?       @db.VarChar(255)
  status        BookingStatus @default(pending)
  ticket_id     BigInt?       @unique
  verified_at   DateTime?     @db.Timestamptz
  created_at    DateTime      @default(now()) @db.Timestamptz
  updated_at    DateTime      @updatedAt @db.Timestamptz

  // Relationships
  customer      Customer      @relation(fields: [customer_id], references: [id], onDelete: Cascade)
  fromBranch    Branch        @relation("BookingFromBranch", fields: [from_branch], references: [id], onDelete: Cascade)
  toBranch      Branch        @relation("BookingToBranch", fields: [to_branch], references: [id], onDelete: Cascade)
  ferryBoat     FerryBoat     @relation(fields: [ferry_boat_id], references: [id], onDelete: Cascade)
  ticket        Ticket?       @relation(fields: [ticket_id], references: [id], onDelete: SetNull)

  @@index([customer_id])
  @@index([status])
  @@index([payment_id])
  @@map("bookings")
}

model HouseboatBooking {
  id                 BigInt           @id @default(autoincrement())
  room_id            BigInt
  customer_name      String           @db.VarChar(255)
  customer_email     String           @db.VarChar(255)
  customer_phone     String           @db.VarChar(15)
  check_in           DateTime         @db.Date
  check_out          DateTime         @db.Date
  guests_adults      Int
  guests_kids        Int              @default(0)
  room_count         Int              @default(1)
  total_amount       Decimal          @db.Decimal(10, 2)
  payment_status     String           @default("pending") @db.VarChar(50)
  status             HouseboatStatus  @default(pending)
  booking_reference  String           @unique @db.VarChar(100)
  special_requests   String?          @db.Text
  created_at         DateTime         @default(now()) @db.Timestamptz
  updated_at         DateTime         @updatedAt @db.Timestamptz

  // Relationships
  room               HouseboatRoom    @relation(fields: [room_id], references: [id], onDelete: Cascade)

  @@index([check_in, check_out])
  @@index([status])
  @@index([booking_reference])
  @@map("houseboat_bookings")
}

// ============================================
// PHASE F: REPORTING & METADATA
// ============================================

model DailySummary {
  id             BigInt    @id @default(autoincrement())
  summary_date   DateTime  @db.Date
  branch_id      BigInt
  ferry_boat_id  BigInt?
  total_tickets  Int       @default(0)
  total_passengers Int     @default(0)
  total_vehicles Int       @default(0)
  total_amount   Decimal   @default(0.00) @db.Decimal(10, 2)
  cash_amount    Decimal   @default(0.00) @db.Decimal(10, 2)
  card_amount    Decimal   @default(0.00) @db.Decimal(10, 2)
  upi_amount     Decimal   @default(0.00) @db.Decimal(10, 2)
  created_at     DateTime  @default(now()) @db.Timestamptz
  updated_at     DateTime  @updatedAt @db.Timestamptz

  // Relationships
  branch         Branch    @relation(fields: [branch_id], references: [id], onDelete: Cascade)
  ferryBoat      FerryBoat? @relation(fields: [ferry_boat_id], references: [id], onDelete: SetNull)

  @@unique([summary_date, branch_id, ferry_boat_id])
  @@index([summary_date])
  @@index([branch_id])
  @@map("daily_summaries")
}

model BranchTransfer {
  id             BigInt   @id @default(autoincrement())
  user_id        BigInt
  from_branch_id BigInt
  to_branch_id   BigInt
  transfer_date  DateTime @db.Date
  reason         String?  @db.Text
  approved_by    BigInt?
  status         String   @default("pending") @db.VarChar(50)
  created_at     DateTime @default(now()) @db.Timestamptz
  updated_at     DateTime @updatedAt @db.Timestamptz

  // Relationships
  user           User     @relation("TransferUser", fields: [user_id], references: [id], onDelete: Cascade)
  fromBranch     Branch   @relation("TransferFromBranch", fields: [from_branch_id], references: [id], onDelete: Cascade)
  toBranch       Branch   @relation("TransferToBranch", fields: [to_branch_id], references: [id], onDelete: Cascade)
  approver       User?    @relation("TransferApprover", fields: [approved_by], references: [id], onDelete: SetNull)

  @@index([user_id])
  @@index([status])
  @@map("branch_transfers")
}

// ============================================
// PHASE G: SYSTEM TABLES
// ============================================

model Session {
  id            String   @id @db.VarChar(255)
  user_id       BigInt?
  ip_address    String?  @db.VarChar(45)
  user_agent    String?  @db.Text
  payload       String   @db.Text
  last_activity Int

  @@index([user_id])
  @@index([last_activity])
  @@map("sessions")
}

model Cache {
  key        String @id @db.VarChar(255)
  value      String @db.Text
  expiration Int

  @@map("cache")
}

model CacheLock {
  key        String @id @db.VarChar(255)
  owner      String @db.VarChar(255)
  expiration Int

  @@map("cache_locks")
}

model FailedJob {
  id         BigInt   @id @default(autoincrement())
  uuid       String   @unique @db.VarChar(255)
  connection String   @db.Text
  queue      String   @db.Text
  payload    String   @db.Text
  exception  String   @db.Text
  failed_at  DateTime @default(now()) @db.Timestamptz

  @@map("failed_jobs")
}

model PersonalAccessToken {
  id              BigInt    @id @default(autoincrement())
  tokenable_type  String    @db.VarChar(255)
  tokenable_id    BigInt
  name            String    @db.VarChar(255)
  token           String    @unique @db.VarChar(64)
  abilities       String?   @db.Text
  last_used_at    DateTime? @db.Timestamptz
  expires_at      DateTime? @db.Timestamptz
  created_at      DateTime  @default(now()) @db.Timestamptz
  updated_at      DateTime  @updatedAt @db.Timestamptz

  @@index([tokenable_type, tokenable_id])
  @@index([token])
  @@map("personal_access_tokens")
}
```

### **STEP 6: Validate Schema**

```bash
# Format schema
npx prisma format

# Validate schema (checks for errors)
npx prisma validate
```

### **STEP 7: Generate Migration**

```bash
# Generate SQL migration from schema
npx prisma migrate dev --name initial_migration --create-only

# This creates: prisma/migrations/TIMESTAMP_initial_migration/migration.sql
# Review the generated SQL before applying
```

### **STEP 8: Review Generated Migration SQL**

Open `prisma/migrations/TIMESTAMP_initial_migration/migration.sql` and verify:

✅ All tables created in correct order  
✅ Foreign keys have proper `ON DELETE` actions  
✅ Indexes are created  
✅ JSONB columns for `items` and `amenities`  
✅ TIMESTAMPTZ used for all timestamps  

### **STEP 9: Apply Migration to PostgreSQL**

```bash
# Apply migration
npx prisma migrate deploy

# OR for development
npx prisma migrate dev

# Generate Prisma Client (for Node.js usage)
npx prisma generate
```

### **STEP 10: Create Data Migration Script**

Create `scripts/migrate-data.ts`:

```typescript
import mysql from 'mysql2/promise';
import { PrismaClient } from '@prisma/client';
import * as dotenv from 'dotenv';

dotenv.config();

const prisma = new PrismaClient();

async function main() {
  // MySQL connection
  const mysqlConn = await mysql.createConnection({
    host: process.env.MYSQL_HOST!,
    user: process.env.MYSQL_USER!,
    password: process.env.MYSQL_PASSWORD!,
    database: process.env.MYSQL_DATABASE!,
  });

  console.log('✅ Connected to MySQL');

  try {
    // PHASE A: Master Data
    console.log('\n📦 Migrating Phase A: Master Data...');
    
    // 1. Item Categories
    console.log('  → Migrating item_categories...');
    const [itemCategories] = await mysqlConn.query('SELECT * FROM item_categories');
    for (const cat of itemCategories as any[]) {
      await prisma.itemCategory.create({
        data: {
          id: BigInt(cat.id),
          category_name: cat.category_name,
          levy: cat.levy,
          is_active: Boolean(cat.is_active),
          created_at: cat.created_at,
          updated_at: cat.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(itemCategories as any[]).length} item categories`);

    // 2. Special Charges
    console.log('  → Migrating special_charges...');
    const [specialCharges] = await mysqlConn.query('SELECT * FROM special_charges');
    for (const charge of specialCharges as any[]) {
      await prisma.specialCharge.create({
        data: {
          id: BigInt(charge.id),
          charge_name: charge.charge_name,
          charge_amount: charge.charge_amount,
          charge_type: charge.charge_type,
          applicable_from: charge.applicable_from,
          applicable_to: charge.applicable_to,
          is_active: Boolean(charge.is_active),
          created_at: charge.created_at,
          updated_at: charge.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(specialCharges as any[]).length} special charges`);

    // 3. Guests
    console.log('  → Migrating guests...');
    const [guests] = await mysqlConn.query('SELECT * FROM guests');
    for (const guest of guests as any[]) {
      await prisma.guest.create({
        data: {
          id: BigInt(guest.id),
          first_name: guest.first_name,
          last_name: guest.last_name,
          mobile: guest.mobile,
          email: guest.email,
          address: guest.address,
          id_proof_type: guest.id_proof_type,
          id_proof_number: guest.id_proof_number,
          created_at: guest.created_at,
          updated_at: guest.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(guests as any[]).length} guests`);

    // PHASE B: Infrastructure
    console.log('\n📦 Migrating Phase B: Infrastructure...');
    
    // 4. Branches (two-pass to handle self-referencing)
    console.log('  → Migrating branches (pass 1 - without dest_branch_id)...');
    const [branches] = await mysqlConn.query('SELECT * FROM branches ORDER BY id');
    for (const branch of branches as any[]) {
      await prisma.branch.create({
        data: {
          id: BigInt(branch.id),
          branch_id: branch.branch_id,
          branch_name: branch.branch_name,
          branch_address: branch.branch_address,
          branch_phone: branch.branch_phone,
          latitude: branch.latitude,
          longitude: branch.longitude,
          is_active: Boolean(branch.is_active),
          created_at: branch.created_at,
          updated_at: branch.updated_at,
          // Skip dest_branch_id and ferry_boat_id for now
        },
      });
    }
    console.log(`  ✅ Migrated ${(branches as any[]).length} branches (pass 1)`);

    // 5. Ferry Boats
    console.log('  → Migrating ferryboats...');
    const [ferryboats] = await mysqlConn.query('SELECT * FROM ferryboats');
    for (const ferry of ferryboats as any[]) {
      await prisma.ferryBoat.create({
        data: {
          id: BigInt(ferry.id),
          number: ferry.number,
          name: ferry.name,
          branch_id: ferry.branch_id ? BigInt(ferry.branch_id) : null,
          capacity_passengers: ferry.capacity_passengers,
          capacity_vehicles: ferry.capacity_vehicles,
          is_active: Boolean(ferry.is_active),
          created_at: ferry.created_at,
          updated_at: ferry.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(ferryboats as any[]).length} ferryboats`);

    // Update branches with dest_branch_id and ferry_boat_id (pass 2)
    console.log('  → Updating branches (pass 2 - with foreign keys)...');
    for (const branch of branches as any[]) {
      if (branch.dest_branch_id || branch.ferry_boat_id) {
        await prisma.branch.update({
          where: { id: BigInt(branch.id) },
          data: {
            dest_branch_id: branch.dest_branch_id ? BigInt(branch.dest_branch_id) : null,
            ferry_boat_id: branch.ferry_boat_id ? BigInt(branch.ferry_boat_id) : null,
          },
        });
      }
    }
    console.log('  ✅ Updated branch foreign keys');

    // 6. Houseboat Rooms (with JSONB)
    console.log('  → Migrating houseboat_rooms...');
    const [houseboatRooms] = await mysqlConn.query('SELECT * FROM houseboat_rooms');
    for (const room of houseboatRooms as any[]) {
      await prisma.houseboatRoom.create({
        data: {
          id: BigInt(room.id),
          name: room.name,
          description: room.description,
          price: room.price,
          capacity_adults: room.capacity_adults,
          capacity_kids: room.capacity_kids,
          amenities: room.amenities ? JSON.parse(room.amenities) : null,
          image_url: room.image_url,
          gallery: room.gallery ? JSON.parse(room.gallery) : null,
          is_available: Boolean(room.is_available),
          created_at: room.created_at,
          updated_at: room.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(houseboatRooms as any[]).length} houseboat rooms`);

    // PHASE C: Users & Authentication
    console.log('\n📦 Migrating Phase C: Users & Authentication...');
    
    // 7. Users
    console.log('  → Migrating users...');
    const [users] = await mysqlConn.query('SELECT * FROM users');
    for (const user of users as any[]) {
      await prisma.user.create({
        data: {
          id: BigInt(user.id),
          name: user.name,
          email: user.email,
          email_verified_at: user.email_verified_at,
          password: user.password,
          mobile: user.mobile,
          role_id: user.role_id,
          branch_id: user.branch_id ? BigInt(user.branch_id) : null,
          ferry_boat_id: user.ferry_boat_id ? BigInt(user.ferry_boat_id) : null,
          remember_token: user.remember_token,
          created_at: user.created_at,
          updated_at: user.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(users as any[]).length} users`);

    // 8. Customers
    console.log('  → Migrating customers...');
    const [customers] = await mysqlConn.query('SELECT * FROM customers');
    for (const customer of customers as any[]) {
      await prisma.customer.create({
        data: {
          id: BigInt(customer.id),
          first_name: customer.first_name,
          last_name: customer.last_name,
          email: customer.email,
          email_verified_at: customer.email_verified_at,
          password: customer.password,
          mobile: customer.mobile,
          profile_image: customer.profile_image,
          google_id: customer.google_id,
          remember_token: customer.remember_token,
          created_at: customer.created_at,
          updated_at: customer.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(customers as any[]).length} customers`);

    // PHASE D: Operational Configuration
    console.log('\n📦 Migrating Phase D: Operational Configuration...');
    
    // 9. Ferry Schedules
    console.log('  → Migrating ferry_schedules...');
    const [ferrySchedules] = await mysqlConn.query('SELECT * FROM ferry_schedules');
    for (const schedule of ferrySchedules as any[]) {
      await prisma.ferrySchedule.create({
        data: {
          id: BigInt(schedule.id),
          hour: schedule.hour,
          minute: schedule.minute,
          schedule_time: schedule.schedule_time,
          branch_id: BigInt(schedule.branch_id),
          ferry_boat_id: BigInt(schedule.ferry_boat_id),
          is_active: Boolean(schedule.is_active),
          created_at: schedule.created_at,
          updated_at: schedule.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(ferrySchedules as any[]).length} ferry schedules`);

    // 10. Item Rates
    console.log('  → Migrating item_rates...');
    const [itemRates] = await mysqlConn.query('SELECT * FROM item_rates');
    for (const rate of itemRates as any[]) {
      await prisma.itemRate.create({
        data: {
          id: BigInt(rate.id),
          item_name: rate.item_name,
          item_rate: rate.item_rate,
          item_lavy: rate.item_lavy,
          item_surcharge_pct: rate.item_surcharge_pct,
          branch_id: BigInt(rate.branch_id),
          is_vehicle: Boolean(rate.is_vehicle),
          starting_date: rate.starting_date,
          ending_date: rate.ending_date,
          created_at: rate.created_at,
          updated_at: rate.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(itemRates as any[]).length} item rates`);

    // PHASE E: Transactional Data
    console.log('\n📦 Migrating Phase E: Transactional Data...');
    
    // 11. Tickets
    console.log('  → Migrating tickets...');
    const [tickets] = await mysqlConn.query('SELECT * FROM tickets');
    for (const ticket of tickets as any[]) {
      await prisma.ticket.create({
        data: {
          id: BigInt(ticket.id),
          ticket_no: ticket.ticket_no,
          ticket_date: ticket.ticket_date,
          branch_id: BigInt(ticket.branch_id),
          dest_branch_id: BigInt(ticket.dest_branch_id),
          ferry_boat_id: BigInt(ticket.ferry_boat_id),
          ferry_time: ticket.ferry_time,
          payment_mode: ticket.payment_mode,
          total_amount: ticket.total_amount,
          customer_id: ticket.customer_id ? BigInt(ticket.customer_id) : null,
          verified_at: ticket.verified_at,
          checker_id: ticket.checker_id ? BigInt(ticket.checker_id) : null,
          created_at: ticket.created_at,
          updated_at: ticket.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(tickets as any[]).length} tickets`);

    // 12. Ticket Lines
    console.log('  → Migrating ticket_lines...');
    const [ticketLines] = await mysqlConn.query('SELECT * FROM ticket_lines');
    for (const line of ticketLines as any[]) {
      await prisma.ticketLine.create({
        data: {
          id: BigInt(line.id),
          ticket_id: BigInt(line.ticket_id),
          item_name: line.item_name,
          qty: line.qty,
          rate: line.rate,
          levy: line.levy,
          amount: line.amount,
          vehicle_name: line.vehicle_name,
          vehicle_no: line.vehicle_no,
          created_at: line.created_at,
          updated_at: line.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(ticketLines as any[]).length} ticket lines`);

    // 13. Bookings (with JSONB items)
    console.log('  → Migrating bookings...');
    const [bookings] = await mysqlConn.query('SELECT * FROM bookings');
    for (const booking of bookings as any[]) {
      await prisma.booking.create({
        data: {
          id: BigInt(booking.id),
          customer_id: BigInt(booking.customer_id),
          from_branch: BigInt(booking.from_branch),
          to_branch: BigInt(booking.to_branch),
          ferry_boat_id: BigInt(booking.ferry_boat_id),
          ferry_time: booking.ferry_time,
          items: JSON.parse(booking.items), // Parse JSON string to object
          total_amount: booking.total_amount,
          payment_id: booking.payment_id,
          status: booking.status,
          ticket_id: booking.ticket_id ? BigInt(booking.ticket_id) : null,
          verified_at: booking.verified_at,
          created_at: booking.created_at,
          updated_at: booking.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(bookings as any[]).length} bookings`);

    // 14. Houseboat Bookings
    console.log('  → Migrating houseboat_bookings...');
    const [houseboatBookings] = await mysqlConn.query('SELECT * FROM houseboat_bookings');
    for (const booking of houseboatBookings as any[]) {
      await prisma.houseboatBooking.create({
        data: {
          id: BigInt(booking.id),
          room_id: BigInt(booking.room_id),
          customer_name: booking.customer_name,
          customer_email: booking.customer_email,
          customer_phone: booking.customer_phone,
          check_in: booking.check_in,
          check_out: booking.check_out,
          guests_adults: booking.guests_adults,
          guests_kids: booking.guests_kids,
          room_count: booking.room_count,
          total_amount: booking.total_amount,
          payment_status: booking.payment_status,
          status: booking.status,
          booking_reference: booking.booking_reference,
          special_requests: booking.special_requests,
          created_at: booking.created_at,
          updated_at: booking.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(houseboatBookings as any[]).length} houseboat bookings`);

    // PHASE F: Reporting & Metadata
    console.log('\n📦 Migrating Phase F: Reporting & Metadata...');
    
    // 15. Daily Summaries
    console.log('  → Migrating daily_summaries...');
    const [dailySummaries] = await mysqlConn.query('SELECT * FROM daily_summaries');
    for (const summary of dailySummaries as any[]) {
      await prisma.dailySummary.create({
        data: {
          id: BigInt(summary.id),
          summary_date: summary.summary_date,
          branch_id: BigInt(summary.branch_id),
          ferry_boat_id: summary.ferry_boat_id ? BigInt(summary.ferry_boat_id) : null,
          total_tickets: summary.total_tickets,
          total_passengers: summary.total_passengers,
          total_vehicles: summary.total_vehicles,
          total_amount: summary.total_amount,
          cash_amount: summary.cash_amount,
          card_amount: summary.card_amount,
          upi_amount: summary.upi_amount,
          created_at: summary.created_at,
          updated_at: summary.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(dailySummaries as any[]).length} daily summaries`);

    // 16. Branch Transfers
    console.log('  → Migrating branch_transfers...');
    const [branchTransfers] = await mysqlConn.query('SELECT * FROM branch_transfers');
    for (const transfer of branchTransfers as any[]) {
      await prisma.branchTransfer.create({
        data: {
          id: BigInt(transfer.id),
          user_id: BigInt(transfer.user_id),
          from_branch_id: BigInt(transfer.from_branch_id),
          to_branch_id: BigInt(transfer.to_branch_id),
          transfer_date: transfer.transfer_date,
          reason: transfer.reason,
          approved_by: transfer.approved_by ? BigInt(transfer.approved_by) : null,
          status: transfer.status,
          created_at: transfer.created_at,
          updated_at: transfer.updated_at,
        },
      });
    }
    console.log(`  ✅ Migrated ${(branchTransfers as any[]).length} branch transfers`);

    // Reset sequences to max ID
    console.log('\n🔄 Resetting PostgreSQL sequences...');
    const tables = [
      'item_categories', 'special_charges', 'guests', 'branches', 'ferryboats',
      'houseboat_rooms', 'users', 'customers', 'ferry_schedules', 'item_rates',
      'tickets', 'ticket_lines', 'bookings', 'houseboat_bookings',
      'daily_summaries', 'branch_transfers'
    ];

    for (const table of tables) {
      await prisma.$executeRawUnsafe(
        `SELECT setval(pg_get_serial_sequence('${table}', 'id'), (SELECT MAX(id) FROM ${table}))`
      );
    }
    console.log('✅ Sequences reset successfully');

    console.log('\n✅ Migration completed successfully!');

  } catch (error) {
    console.error('❌ Migration error:', error);
    throw error;
  } finally {
    await mysqlConn.end();
    await prisma.$disconnect();
  }
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  });
```

### **STEP 11: Run Data Migration**

```bash
# Compile and run migration script
npx tsx scripts/migrate-data.ts
```

### **STEP 12: Create PostgreSQL Performance Indexes**

Create `prisma/migrations/TIMESTAMP_add_performance_indexes/migration.sql`:

```sql
-- GIN indexes for JSONB columns
CREATE INDEX idx_bookings_items_gin ON bookings USING GIN (items);
CREATE INDEX idx_houseboat_rooms_amenities_gin ON houseboat_rooms USING GIN (amenities);
CREATE INDEX idx_houseboat_rooms_gallery_gin ON houseboat_rooms USING GIN (gallery);

-- Full-text search indexes
CREATE INDEX idx_branches_fulltext ON branches 
  USING gin(to_tsvector('english', branch_name || ' ' || COALESCE(branch_address, '')));

CREATE INDEX idx_ferryboats_fulltext ON ferryboats 
  USING gin(to_tsvector('english', name || ' ' || number));

-- Partial indexes for active records only
CREATE INDEX idx_branches_active ON branches (id) WHERE is_active = TRUE;
CREATE INDEX idx_ferryboats_active ON ferryboats (id) WHERE is_active = TRUE;
CREATE INDEX idx_ferry_schedules_active ON ferry_schedules (id) WHERE is_active = TRUE;

-- Composite indexes for common queries
CREATE INDEX idx_tickets_date_branch ON tickets (ticket_date, branch_id);
CREATE INDEX idx_bookings_customer_status ON bookings (customer_id, status);
CREATE INDEX idx_daily_summaries_date_branch ON daily_summaries (summary_date DESC, branch_id);
```

Apply the indexes:

```bash
npx prisma migrate deploy
```

### **STEP 13: Validation & Testing**

Create `scripts/validate-migration.ts`:

```typescript
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function validate() {
  console.log('🔍 Validating migration...\n');

  // Count checks
  const tables = [
    'branch', 'ferryBoat', 'user', 'customer', 'ticket',
    'ticketLine', 'booking', 'houseboatBooking'
  ];

  for (const table of tables) {
    const count = await (prisma[table as keyof typeof prisma] as any).count();
    console.log(`✓ ${table}: ${count} records`);
  }

  // Foreign key validation
  console.log('\n🔗 Checking foreign key integrity...');
  
  const orphanedTickets = await prisma.$queryRaw`
    SELECT COUNT(*) FROM tickets t
    LEFT JOIN branches b ON t.branch_id = b.id
    WHERE b.id IS NULL
  `;
  console.log(`✓ Orphaned tickets: ${orphanedTickets[0].count}`);

  const orphanedBookings = await prisma.$queryRaw`
    SELECT COUNT(*) FROM bookings b
    LEFT JOIN customers c ON b.customer_id = c.id
    WHERE c.id IS NULL
  `;
  console.log(`✓ Orphaned bookings: ${orphanedBookings[0].count}`);

  // JSONB validation
  console.log('\n📦 Validating JSONB columns...');
  
  const bookingsWithItems = await prisma.booking.count({
    where: { items: { not: null } }
  });
  console.log(`✓ Bookings with items: ${bookingsWithItems}`);

  const roomsWithAmenities = await prisma.houseboatRoom.count({
    where: { amenities: { not: null } }
  });
  console.log(`✓ Rooms with amenities: ${roomsWithAmenities}`);

  console.log('\n✅ Validation complete!');
}

validate()
  .catch(console.error)
  .finally(() => prisma.$disconnect());
```

Run validation:

```bash
npx tsx scripts/validate-migration.ts
```

---

## ✅ POST-MIGRATION CHECKLIST

After completing all steps, verify:

- [ ] All tables created in PostgreSQL
- [ ] All data migrated from MySQL
- [ ] No orphaned records (foreign key violations)
- [ ] Sequences reset to correct values
- [ ] JSONB columns contain valid JSON
- [ ] TIMESTAMPTZ columns have timezone info
- [ ] Performance indexes created
- [ ] Prisma Client generated successfully
- [ ] Sample queries work correctly
- [ ] Backup of original MySQL database exists

---

## 🚀 NEXT STEPS

After successful database migration:

1. **Update Backend**: Modify Node.js/Express code to use Prisma Client
2. **Test Queries**: Ensure all existing queries work with PostgreSQL
3. **Performance Testing**: Compare query performance vs MySQL
4. **Deploy**: Deploy PostgreSQL database to production environment
5. **Monitor**: Set up monitoring for query performance and errors

---

## 📝 TROUBLESHOOTING

### Common Issues

**Issue**: "Sequence is out of sync"
```bash
# Fix all sequences at once
SELECT 'SELECT SETVAL(' ||
       quote_literal(quote_ident(PGT.schemaname) || '.' || quote_ident(S.relname)) ||
       ', COALESCE(MAX(' ||quote_ident(C.attname)|| '), 1) ) FROM ' ||
       quote_ident(PGT.schemaname)|| '.'||quote_ident(T.relname)|| ';'
FROM pg_class AS S,
     pg_depend AS D,
     pg_class AS T,
     pg_attribute AS C,
     pg_tables AS PGT
WHERE S.relkind = 'S'
    AND S.oid = D.objid
    AND D.refobjid = T.oid
    AND D.refobjid = C.attrelid
    AND D.refobjsubid = C.attnum
    AND T.relname = PGT.tablename
ORDER BY S.relname;
```

**Issue**: "JSONB parse error"
```sql
-- Find invalid JSON
SELECT id, items FROM bookings WHERE NOT (items::text ~ '^[\[\{]');
```

**Issue**: "Foreign key constraint violation"
```sql
-- Find missing references
SELECT t.id, t.branch_id 
FROM tickets t 
LEFT JOIN branches b ON t.branch_id = b.id 
WHERE b.id IS NULL;
```

---

## 📚 ADDITIONAL RESOURCES

- [Prisma Documentation](https://www.prisma.io/docs)
- [PostgreSQL JSON Types](https://www.postgresql.org/docs/current/datatype-json.html)
- [PostgreSQL Indexes](https://www.postgresql.org/docs/current/indexes.html)
- [Node.js MySQL to Postgres Migration Guide](https://node-postgres.com/)

---

**END OF DATABASE MIGRATION INSTRUCTIONS**
