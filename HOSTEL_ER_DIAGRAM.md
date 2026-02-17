# Hostel Management Module - Entity Relationship Diagram

## Overview
This document describes the relationships between all entities in the Hostel Management Module.

## Entity Relationship Diagram (Text-Based)

```
┌─────────────────────────────────────────────────────────────────────┐
│                    HOSTEL INFRASTRUCTURE                              │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│   Hostels    │1──────*│    Blocks    │1──────*│    Floors    │
│──────────────│         │──────────────│         │──────────────│
│ id           │         │ id           │         │ id           │
│ name         │         │ hostel_id FK │         │ block_id FK  │
│ type         │         │ name         │         │ floor_number │
│ capacity     │         │ total_floors │         │ name         │
│ address      │         │ is_active    │         │ is_active    │
│ is_active    │         └──────────────┘         └──────────────┘
└──────────────┘                                          │
                                                          │1
                                                          │
                                                          │*
                                                  ┌──────────────┐
                                                  │    Rooms     │
                                                  │──────────────│
                                                  │ id           │
                                                  │ floor_id FK  │
                                                  │ room_number  │
                                                  │ room_type    │
                                                  │ max_strength │
                                                  │ is_active    │
                                                  └──────────────┘
                                                          │1
                                    ┌─────────────────────┼─────────────────────┐
                                    │*                    │*                    │*
                            ┌──────────────┐      ┌──────────────┐     ┌──────────────┐
                            │     Beds     │      │   Lockers    │     │  Furniture   │
                            │──────────────│      │──────────────│     │──────────────│
                            │ id           │      │ id           │     │ id           │
                            │ room_id FK   │      │ room_id FK   │     │ room_id FK   │
                            │ bed_number   │      │ locker_number│     │ asset_code   │
                            │ qr_code      │      │ qr_code      │     │ item_name    │
                            │ condition    │      │ condition    │     │ type         │
                            │ is_occupied  │      │ is_assigned  │     │ quantity     │
                            │ is_active    │      │ is_active    │     │ condition    │
                            └──────────────┘      └──────────────┘     └──────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                    STUDENT ALLOCATION                                 │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────┐         ┌──────────────────────┐         ┌──────────────┐
│  Admissions  │1──────*│ StudentAllocations   │*──────1│     Beds     │
│ (Students)   │         │──────────────────────│         └──────────────┘
│──────────────│         │ id                   │
│ id           │         │ student_id FK        │         ┌──────────────┐
│ reg_no       │         │ bed_id FK            │*──────1│   Lockers    │
│ student_name │         │ locker_id FK         │         └──────────────┘
│ class_id     │         │ check_in_date        │
└──────────────┘         │ check_out_date       │         ┌──────────────────┐
                         │ status               │1──────1│ SecurityDeposits │
                         │ receipt_number       │         │──────────────────│
                         └──────────────────────┘         │ id               │
                                                           │ allocation_id FK │
                                                           │ deposit_amount   │
                                                           │ refund_amount    │
                                                           │ status           │
                                                           └──────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                    WARDEN & STAFF MANAGEMENT                          │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────┐         ┌──────────────────────┐         ┌──────────────┐
│   Wardens    │1──────*│  WardenAssignments   │*──────1│   Hostels    │
│──────────────│         │──────────────────────│         └──────────────┘
│ id           │         │ id                   │
│ name         │         │ warden_id FK         │
│ employee_code│         │ hostel_id FK         │
│ phone        │         │ assigned_from        │
│ gender       │         │ assigned_to          │
│ status       │         │ shift_start_time     │
│ is_active    │         │ shift_end_time       │
└──────────────┘         │ shift_type           │
      │                  │ is_primary           │
      │                  └──────────────────────┘
      │
      │1                                    ┌──────────────┐
      ├────────────────────────────────────*│  Incidents   │
      │                                     │──────────────│
      │                                     │ id           │
      │                                     │ hostel_id FK │
      │                                     │ reported_by FK│
      │                                     │ student_id FK│
      │                                     │ title        │
      │                                     │ severity     │
      │                                     │ status       │
      │                                     └──────────────┘
      │
      │1                                    ┌──────────────┐
      ├────────────────────────────────────*│  Attendance  │
      │                                     │──────────────│
      │                                     │ id           │
      │                                     │ hostel_id FK │
      │                                     │ student_id FK│
      │                                     │ date         │
      │                                     │ status       │
      │                                     │ submitted_by │
      │                                     └──────────────┘
      │
      │1                                    ┌──────────────┐
      └────────────────────────────────────*│  Complaints  │
                                            │──────────────│
                                            │ id           │
                                            │ student_id FK│
                                            │ hostel_id FK │
                                            │ subject      │
                                            │ category     │
                                            │ status       │
                                            │ responded_by │
                                            └──────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                    FEE MANAGEMENT                                     │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────┐         ┌──────────────────┐         ┌──────────────┐
│   Hostels    │1──────*│  FeeStructures   │1──────*│ StudentFees  │
└──────────────┘         │──────────────────│         │──────────────│
                         │ id               │         │ id           │
                         │ hostel_id FK     │         │ student_id FK│
                         │ fee_name         │         │ fee_struct FK│
                         │ amount           │         │ amount_due   │
                         │ fee_type         │         │ amount_paid  │
                         │ category         │         │ due_date     │
                         │ is_mandatory     │         │ status       │
                         └──────────────────┘         │ fine_amount  │
                                                      └──────────────┘
                                                              │1
                                                              │
                                                              │*
                                                      ┌──────────────┐
┌──────────────┐                                     │   Payments   │
│    Users     │1──────────────────────────────────*│──────────────│
│ (Accountant) │                                     │ id           │
└──────────────┘                                     │ student_id FK│
                                                     │ fee_id FK    │
                                                     │ receipt_no   │
                                                     │ amount       │
                                                     │ payment_mode │
                                                     │ received_by  │
                                                     └──────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                    IMPREST WALLET SYSTEM                              │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────┐         ┌──────────────────┐         ┌──────────────────┐
│  Admissions  │1──────1│  ImprestWallets  │1──────*│    Expenses      │
│ (Students)   │         │──────────────────│         │──────────────────│
└──────────────┘         │ id               │         │ id               │
                         │ student_id FK    │         │ wallet_id FK     │
                         │ opening_balance  │         │ category_id FK   │
                         │ current_balance  │         │ amount           │
                         │ total_credited   │         │ expense_date     │
                         │ total_debited    │         │ description      │
                         │ is_active        │         │ status           │
                         └──────────────────┘         │ submitted_by FK  │
                                                      │ approved_by FK   │
                                                      │ approved_at      │
                                                      └──────────────────┘
                                                              │
                                        ┌─────────────────────┼──────────────┐
                                        │*                                   │*
                                ┌──────────────────┐            ┌──────────────┐
                                │ ExpenseCategories│            │   Wardens    │
                                │──────────────────│            │ (Submitter)  │
                                │ id               │            └──────────────┘
                                │ name             │
                                │ requires_approval│            ┌──────────────┐
                                │ is_active        │            │    Users     │
                                └──────────────────┘            │ (Approver)   │
                                                                └──────────────┘
```

## Key Relationships

### One-to-Many (1:*)
- Hostel → Blocks
- Block → Floors
- Floor → Rooms
- Room → Beds, Lockers, Furniture
- Student → Allocations
- Warden → Assignments, Incidents, Attendance
- Hostel → FeeStructures
- FeeStructure → StudentFees
- StudentFee → Payments
- Student → ImprestWallet
- Wallet → Expenses
- Category → Expenses

### Many-to-One (*:1)
- Allocation → Student, Bed, Locker
- Expense → Wallet, Category, Warden, User

### One-to-One (1:1)
- Allocation ↔ SecurityDeposit
- Student ↔ ImprestWallet

## Database Indexes

For optimal performance, the following indexes are created:

### Primary Keys
All tables have auto-incrementing primary keys (id)

### Foreign Keys
All foreign key columns are indexed automatically

### Additional Indexes
- `hostels.type` - Filter by hostel type
- `hostel_beds.qr_code` - Quick QR code lookup
- `hostel_beds.is_occupied` - Available bed searches
- `hostel_lockers.qr_code` - Quick QR code lookup
- `hostel_furniture.asset_code` - Quick asset lookup
- `hostel_student_allocations.status` - Active allocation searches
- `hostel_expenses.status` - Pending approval queries
- `hostel_payments.receipt_number` - Receipt lookups
- `hostel_attendance.attendance_date` - Daily attendance queries

### Unique Constraints
- `hostel_beds.qr_code` - Unique QR codes
- `hostel_lockers.qr_code` - Unique QR codes
- `hostel_furniture.asset_code` - Unique asset codes
- `hostel_wardens.employee_code` - Unique employee codes
- `hostel_payments.receipt_number` - Unique receipt numbers
- `hostel_imprest_wallets.student_id` - One wallet per student
- `hostel_attendance.(student_id, attendance_date)` - One attendance per day

## Data Integrity Rules

### Check Constraints (Application Level)
1. Room max_strength >= current occupied beds
2. Room max_strength >= total beds count
3. Wallet current_balance >= 0 (no negative balance)
4. Payment amount > 0
5. Expense amount > 0
6. Bed allocation: check_out_date >= check_in_date

### Cascading Deletes
- Hostel deleted → Blocks deleted → Floors deleted → Rooms deleted
- Room deleted → Beds/Lockers/Furniture deleted
- Allocation deleted → Security deposit deleted

### Soft Deletes
Most entities support soft deletes to maintain historical data integrity

## Transaction Boundaries

Critical operations wrapped in database transactions:
1. Student check-in (allocation + bed update + locker update)
2. Student check-out (allocation update + bed update + locker update)
3. Expense approval (expense update + wallet debit)
4. Payment processing (payment create + fee update)
5. Wallet credit (wallet update + transaction log)

---

**Note**: This ER diagram is optimized for PostgreSQL, MySQL, and SQLite databases.
All timestamps are automatically managed by Laravel's Eloquent ORM.
