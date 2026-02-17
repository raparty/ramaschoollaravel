# Hostel Management Module

A comprehensive hostel management system for educational institutions with support for resource allocation, fee management, and student imprest accounts.

## Features

### 1. **Hostel & Resource Management**
- Hierarchical structure: Hostel → Block → Floor → Room → Bed/Locker/Furniture
- QR/Asset code tracking for all assets
- Condition status monitoring (Good/Damaged/Under Repair)
- Room capacity management with over-allocation prevention
- Multi-type hostel support (Boys/Girls/Junior/Senior)

### 2. **Student Allocation System**
- Student check-in/check-out functionality
- Bed and locker assignment
- One active bed per student validation
- Auto-calculation of room occupancy
- Allocation history maintenance
- Receipt generation for check-ins

### 3. **Warden & Staff Management**
- Warden profile management
- Hostel assignment with shift timings
- Incident reporting capability
- Daily hostel attendance submission
- Student complaint logging

### 4. **Fee Management**
- Flexible fee structure configuration
- Multiple fee types (Monthly, Quarterly, Yearly, One-time)
- Fee categories (Accommodation, Food, Maintenance, Security Deposit)
- Overdue tracking with fine calculation
- Payment mode tracking (Cash, Cheque, Card, UPI, etc.)
- Security deposit with refund workflow
- Student fee statement reports

### 5. **Imprest Account (Wallet System)**
- Student wallet management with opening and current balance
- Expense categories with approval workflow
- Warden-submitted expenses
- Accountant approval process
- Transaction ledger with full audit trail
- Negative balance prevention
- Low balance alerts

### 6. **Reporting**
- Hostel occupancy reports
- Room strength reports
- Bed availability reports
- Student room lists
- Warden allocation reports
- Fee collection reports
- Pending dues reports
- Imprest ledger summary
- Hostel profitability reports

## Database Schema

### Core Tables

1. **hostels** - Main hostel entities
2. **hostel_blocks** - Blocks within hostels
3. **hostel_floors** - Floors within blocks
4. **hostel_rooms** - Rooms with capacity limits
5. **hostel_beds** - Individual beds with QR codes
6. **hostel_lockers** - Individual lockers with QR codes
7. **hostel_furniture** - Furniture assets with asset codes

### Allocation Tables

8. **hostel_student_allocations** - Bed allocations with check-in/out
9. **hostel_security_deposits** - Security deposit tracking

### Staff Tables

10. **hostel_wardens** - Warden information
11. **hostel_warden_assignments** - Hostel assignments with shifts
12. **hostel_incidents** - Incident reporting
13. **hostel_attendance** - Daily attendance records
14. **hostel_complaints** - Student complaints

### Financial Tables

15. **hostel_fee_structures** - Fee configurations
16. **hostel_student_fees** - Fee ledger entries
17. **hostel_payments** - Payment transactions
18. **hostel_imprest_wallets** - Student wallets
19. **hostel_expense_categories** - Expense categories
20. **hostel_expenses** - Expense transactions

## Installation

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Seed Sample Data

```bash
php artisan db:seed --class=HostelSeeder
```

This will create:
- 3 sample hostels (Boys Senior, Girls Senior, Junior)
- Blocks, floors, rooms, beds, and lockers
- 4 sample wardens
- Fee structures for all hostels
- Expense categories

### 3. Include Routes

Add to your `routes/web.php`:

```php
require __DIR__.'/hostel.php';
```

## Usage

### Hostel Management

```php
// List all hostels
GET /hostel/hostels

// Create new hostel
POST /hostel/hostels

// View hostel details
GET /hostel/hostels/{id}

// Update hostel
PUT /hostel/hostels/{id}

// Delete hostel
DELETE /hostel/hostels/{id}
```

### Room Management

```php
// List all rooms
GET /hostel/rooms

// Create new room
POST /hostel/rooms

// View room details with beds and furniture
GET /hostel/rooms/{id}
```

### Student Allocation (Check-in/Check-out)

```php
// List allocations
GET /hostel/allocations

// Check-in student
POST /hostel/allocations

// View allocation details
GET /hostel/allocations/{id}

// Check-out student
POST /hostel/allocations/{id}/checkout

// Cancel allocation
POST /hostel/allocations/{id}/cancel
```

### Wallet & Expense Management

```php
// List wallets
GET /hostel/wallets

// Create wallet
POST /hostel/wallets

// Credit wallet
POST /hostel/wallets/{id}/credit

// View statement
GET /hostel/wallets/{id}/statement

// Submit expense
POST /hostel/expenses

// Pending approvals
GET /hostel/expenses/pending

// Approve expense
POST /hostel/expenses/{id}/approve

// Reject expense
POST /hostel/expenses/{id}/reject
```

## Business Rules

### Allocation Rules
1. One active bed per student
2. Room capacity cannot be exceeded
3. Beds must be in "Good" condition for allocation
4. Student cannot have multiple active allocations

### Financial Rules
1. No negative balance allowed in wallets
2. Expenses require approval based on category
3. Wallet must have sufficient balance for expenses
4. Security deposits are refundable on checkout
5. Overdue fees automatically calculate fines

### Asset Rules
1. Each bed/locker must have unique QR code
2. Each furniture item must have unique asset code
3. Assets marked "Under Repair" cannot be allocated
4. Room strength cannot be less than occupied beds

## Security Features

### Audit Trail
All tables include:
- `created_by` - User who created the record
- `updated_by` - User who last updated the record
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

### Soft Deletes
Implemented on:
- Hostels
- Blocks, Floors, Rooms
- Beds, Lockers, Furniture
- Allocations
- Wardens
- Fee Structures
- Payments
- Security Deposits

### Transaction Management
Database transactions ensure data consistency for:
- Student check-in/check-out
- Wallet credits/debits
- Expense approvals
- Payment processing

## Models

All models include:
- Eloquent relationships
- Query scopes for common filters
- Accessors for computed properties
- Validation in controllers

### Key Relationships

```
Hostel
  → hasMany Blocks
  → hasMany WardenAssignments
  → hasMany FeeStructures

HostelRoom
  → hasMany Beds
  → hasMany Lockers
  → hasMany Furniture

HostelStudentAllocation
  → belongsTo Student (Admission)
  → belongsTo Bed
  → belongsTo Locker
  → hasOne SecurityDeposit

HostelImprestWallet
  → belongsTo Student (Admission)
  → hasMany Expenses

HostelExpense
  → belongsTo Wallet
  → belongsTo Category
  → belongsTo Submitter (Warden)
  → belongsTo Approver (User)
```

## API Endpoints

All endpoints support:
- Pagination (20 items per page by default)
- Filtering by various parameters
- Search functionality
- Sorting

## Scalability

The system is designed to handle:
- 1000+ students
- Multiple hostels
- Thousands of transactions
- Concurrent operations

## Future Enhancements

Potential additions:
- Mobile app integration
- QR code scanning for bed/locker tracking
- SMS/Email notifications for fee dues
- Online payment gateway integration
- Parent portal for wallet recharge
- Visitor management system
- Leave/Outpass management
- Mess menu management
- Biometric attendance integration

## Troubleshooting

### Migration Error: "Referencing column 'student_id' and referenced column 'id' are incompatible"

**Problem**: When running the hostel management migration, you may encounter an error stating that the `student_id` column is incompatible with the `admissions.id` column.

**Root Cause**: This error occurs when there's a type mismatch between foreign key columns. The `admissions` table must have its `id` column as `INT UNSIGNED` (created with `increments()`), but foreign key migration sometimes uses `BIGINT UNSIGNED` (from `foreignId()`).

**Solution**:

1. **Verify you have the latest code**:
   ```bash
   git pull origin main
   ```

2. **Check if the admissions table exists and has the correct schema**:
   ```bash
   php artisan tinker
   ```
   Then run:
   ```php
   DB::select("SHOW COLUMNS FROM admissions WHERE Field = 'id'");
   ```
   The Type should be `int unsigned`, NOT `bigint unsigned`.

3. **If you have partially created hostel tables, roll them back**:
   ```bash
   php artisan migrate:rollback --path=database/migrations/2026_02_17_083400_create_hostel_management_tables.php
   ```

4. **Re-run the migration**:
   ```bash
   php artisan migrate --path=database/migrations/2026_02_17_083400_create_hostel_management_tables.php
   ```

**Prevention**: Always ensure the core tables migration (`2026_02_14_072514_create_core_tables.php`) is run before the hostel management migration. The admissions table must exist with the correct schema.

### Migration Error: "Table already exists"

If you encounter "table already exists" errors, the migration has idempotency guards (`Schema::hasTable()`) that should prevent this. However, if tables exist with incorrect schemas:

1. Manually drop the hostel tables:
   ```bash
   php artisan migrate:rollback --path=database/migrations/2026_02_17_083400_create_hostel_management_tables.php
   ```

2. Or manually drop them via SQL:
   ```sql
   DROP TABLE IF EXISTS hostel_expenses;
   DROP TABLE IF EXISTS hostel_expense_categories;
   -- ... (drop all hostel tables in reverse order)
   ```

3. Re-run the migration with the latest code.

## Support

For issues or questions, please contact the development team.

## License

This module is part of the School ERP system.
