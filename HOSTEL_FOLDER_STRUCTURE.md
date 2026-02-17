# Hostel Management Module - Folder Structure

## Directory Tree

```
ramaschoollaravel/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── HostelController.php
│   │       ├── HostelRoomController.php
│   │       ├── HostelStudentAllocationController.php
│   │       ├── HostelWardenController.php
│   │       ├── HostelImprestWalletController.php
│   │       └── HostelExpenseController.php
│   │
│   └── Models/
│       ├── Hostel.php
│       ├── HostelBlock.php
│       ├── HostelFloor.php
│       ├── HostelRoom.php
│       ├── HostelBed.php
│       ├── HostelLocker.php
│       ├── HostelFurniture.php
│       ├── HostelStudentAllocation.php
│       ├── HostelWarden.php
│       ├── HostelWardenAssignment.php
│       ├── HostelIncident.php
│       ├── HostelAttendance.php
│       ├── HostelComplaint.php
│       ├── HostelFeeStructure.php
│       ├── HostelStudentFee.php
│       ├── HostelPayment.php
│       ├── HostelSecurityDeposit.php
│       ├── HostelImprestWallet.php
│       ├── HostelExpenseCategory.php
│       └── HostelExpense.php
│
├── database/
│   ├── migrations/
│   │   └── 2026_02_17_083400_create_hostel_management_tables.php
│   │
│   └── seeders/
│       ├── HostelSeeder.php
│       ├── HostelInfrastructureSeeder.php
│       ├── HostelWardenSeeder.php
│       ├── HostelFeeStructureSeeder.php
│       └── HostelExpenseCategorySeeder.php
│
├── routes/
│   └── hostel.php
│
├── resources/
│   └── views/
│       └── hostel/
│           ├── dashboard.blade.php
│           │
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── edit.blade.php
│           ├── show.blade.php
│           │
│           ├── rooms/
│           │   ├── index.blade.php
│           │   ├── create.blade.php
│           │   ├── edit.blade.php
│           │   └── show.blade.php
│           │
│           ├── allocations/
│           │   ├── index.blade.php
│           │   ├── create.blade.php
│           │   ├── show.blade.php
│           │   └── checkout.blade.php
│           │
│           ├── wardens/
│           │   ├── index.blade.php
│           │   ├── create.blade.php
│           │   ├── edit.blade.php
│           │   └── show.blade.php
│           │
│           ├── wallets/
│           │   ├── index.blade.php
│           │   ├── create.blade.php
│           │   ├── show.blade.php
│           │   ├── credit.blade.php
│           │   └── statement.blade.php
│           │
│           └── expenses/
│               ├── index.blade.php
│               ├── create.blade.php
│               ├── show.blade.php
│               ├── pending.blade.php
│               ├── approve.blade.php
│               └── reject.blade.php
│
└── Documentation/
    ├── HOSTEL_MODULE_README.md
    ├── HOSTEL_ER_DIAGRAM.md
    └── HOSTEL_FOLDER_STRUCTURE.md (this file)
```

## File Descriptions

### Controllers (app/Http/Controllers/)

| File | Purpose | Key Methods |
|------|---------|-------------|
| `HostelController.php` | Manages hostel CRUD operations | index, create, store, show, edit, update, destroy |
| `HostelRoomController.php` | Manages room operations with validation | index, create, store, show, edit, update, destroy |
| `HostelStudentAllocationController.php` | Handles check-in/check-out workflow | index, create, store, show, checkoutForm, checkout, cancel |
| `HostelWardenController.php` | Manages warden profiles | index, create, store, show, edit, update, destroy |
| `HostelImprestWalletController.php` | Manages student wallets | index, create, store, show, creditForm, credit, statement, toggleActive |
| `HostelExpenseController.php` | Manages expense approval workflow | index, create, store, show, approveForm, approve, rejectForm, reject, pendingApprovals |

### Models (app/Models/)

#### Infrastructure Models
- `Hostel.php` - Main hostel entity
- `HostelBlock.php` - Hostel blocks
- `HostelFloor.php` - Floors within blocks
- `HostelRoom.php` - Rooms with capacity management
- `HostelBed.php` - Individual beds with QR codes
- `HostelLocker.php` - Individual lockers
- `HostelFurniture.php` - Furniture assets

#### Allocation Models
- `HostelStudentAllocation.php` - Bed allocations
- `HostelSecurityDeposit.php` - Security deposits

#### Staff Models
- `HostelWarden.php` - Warden profiles
- `HostelWardenAssignment.php` - Warden-hostel assignments
- `HostelIncident.php` - Incident reports
- `HostelAttendance.php` - Daily attendance
- `HostelComplaint.php` - Student complaints

#### Financial Models
- `HostelFeeStructure.php` - Fee configurations
- `HostelStudentFee.php` - Fee ledger entries
- `HostelPayment.php` - Payment transactions
- `HostelImprestWallet.php` - Student wallets
- `HostelExpenseCategory.php` - Expense categories
- `HostelExpense.php` - Expense transactions

### Migrations (database/migrations/)

- `2026_02_17_083400_create_hostel_management_tables.php` - Creates all 20 hostel tables

### Seeders (database/seeders/)

| Seeder | Purpose | Data Created |
|--------|---------|--------------|
| `HostelSeeder.php` | Main seeder that calls all others | Orchestrates all hostel data seeding |
| `HostelInfrastructureSeeder.php` | Creates hostel structure | 3 hostels with blocks, floors, rooms, beds, lockers, furniture |
| `HostelWardenSeeder.php` | Creates sample wardens | 4 wardens with different profiles |
| `HostelFeeStructureSeeder.php` | Creates fee structures | 6 fee types for each hostel |
| `HostelExpenseCategorySeeder.php` | Creates expense categories | 9 standard expense categories |

### Routes (routes/)

- `hostel.php` - All hostel module routes with proper naming and grouping

### Views (resources/views/hostel/)

Views follow Laravel Blade templating conventions:
- Dashboard view
- CRUD views for each entity
- Specialized views for workflows (checkout, approve, reject)
- Statement and report views

## Naming Conventions

### Controllers
- Suffix: `Controller`
- Format: `Hostel{Entity}Controller`
- Example: `HostelStudentAllocationController`

### Models
- Prefix: `Hostel`
- Format: `Hostel{Entity}`
- Example: `HostelImprestWallet`

### Routes
- Prefix: `hostel.`
- Format: `hostel.{resource}.{action}`
- Example: `hostel.allocations.checkout`

### Database Tables
- Prefix: `hostel_`
- Format: Snake case, plural
- Example: `hostel_student_allocations`

### Views
- Directory: `resources/views/hostel/{resource}/`
- Format: Kebab case
- Example: `hostel/allocations/checkout.blade.php`

## Code Organization Principles

### Single Responsibility
Each controller handles one resource type

### RESTful Design
Controllers follow REST conventions where applicable

### Transaction Management
Financial operations wrapped in database transactions

### Validation
- Request validation in controllers
- Business logic validation in models
- Database constraints for data integrity

### DRY (Don't Repeat Yourself)
- Common functionality in base model
- Reusable scopes for queries
- Shared validation rules

### Security
- Mass assignment protection via `$fillable`
- Soft deletes for data preservation
- Audit trail fields on all tables
- Authorization checks (to be implemented)

## Extension Points

### Adding New Features

1. **New Entity**
   - Create migration
   - Create model with relationships
   - Create controller
   - Add routes
   - Create views

2. **New Report**
   - Add method to existing controller
   - Create dedicated report controller if complex
   - Create view template
   - Add route

3. **New Validation Rule**
   - Add to controller validation
   - Or create custom Form Request class

4. **New Business Logic**
   - Add method to relevant model
   - Use scopes for reusable queries
   - Use accessors for computed properties

## Performance Considerations

### Eager Loading
Controllers use `with()` to prevent N+1 queries

### Indexes
All foreign keys and commonly queried fields are indexed

### Pagination
All list views paginate results (20 per page default)

### Caching
Can be added for:
- Hostel capacity calculations
- Fee structure lookups
- Expense category lists

## Testing Structure

Tests should be organized as:
```
tests/
├── Feature/
│   └── Hostel/
│       ├── HostelManagementTest.php
│       ├── StudentAllocationTest.php
│       ├── WalletManagementTest.php
│       └── ExpenseApprovalTest.php
│
└── Unit/
    └── Models/
        ├── HostelTest.php
        ├── HostelRoomTest.php
        └── HostelImprestWalletTest.php
```

## Deployment Checklist

- [ ] Run migrations
- [ ] Run seeders (optional for demo data)
- [ ] Include routes in main routing file
- [ ] Configure permissions
- [ ] Set up scheduled tasks (for overdue fee calculations)
- [ ] Configure file storage for receipts
- [ ] Set up backup strategy
- [ ] Configure logging
- [ ] Set up monitoring

---

**Last Updated**: 2026-02-17  
**Module Version**: 1.0.0
