# Transport Module Database Schema

## Entity Relationship Diagram

```
┌─────────────────────────┐
│   transport_drivers     │
├─────────────────────────┤
│ PK: driver_id (INT)     │
│     driver_name         │
│     license_number      │
│     aadhar_number       │
│     contact_number      │
│     address             │
│     status              │
│     created_at          │
│     updated_at          │
└─────────────────────────┘
            │
            │ 1:N (One driver can have multiple vehicles)
            │
            ▼
┌─────────────────────────────┐
│  transport_add_vechile      │
├─────────────────────────────┤
│ PK: vechile_id (INT)        │
│     vechile_no              │
│     route_id (CSV)          │
│     no_of_seats             │
│ FK: driver_id → drivers     │◄────┐ Shows which driver
│     insurance_number        │     │ is assigned to which
│     insurance_expiry        │     │ vehicle
│     permit_number           │     │
│     permit_expiry           │     │
└─────────────────────────────┘     │
            │                        │
            │ 1:N (One vehicle       │
            │ has many student       │
            │ assignments)           │
            ▼                        │
┌─────────────────────────────┐     │
│ transport_student_detail    │     │
├─────────────────────────────┤     │
│ PK: id (INT)                │     │
│     registration_no         │     │
│ FK: route_id → routes       │     │
│ FK: vehicle_id → vehicles   │─────┘
│     class_id                │
│     stream_id               │
│     session                 │
└─────────────────────────────┘
            │
            │ N:1 (Many students
            │ use one route)
            ▼
┌─────────────────────────────┐
│   transport_add_route       │
├─────────────────────────────┤
│ PK: route_id (INT)          │
│     route_name              │
│     cost                    │
│     created_at              │
└─────────────────────────────┘
```

## Key Relationships

1. **Driver → Vehicle** (1:N)
   - One driver can be assigned to multiple vehicles
   - Foreign key: `transport_add_vechile.driver_id` → `transport_drivers.driver_id`
   - Constraint: ON DELETE SET NULL (if driver deleted, vehicle becomes unassigned)

2. **Vehicle → Student Assignments** (1:N)
   - One vehicle can have many student assignments
   - Foreign key: `transport_student_detail.vehicle_id` → `transport_add_vechile.vechile_id`

3. **Route → Student Assignments** (1:N)
   - One route can have many student assignments
   - Foreign key: `transport_student_detail.route_id` → `transport_add_route.route_id`

4. **Vehicle → Routes** (N:N via CSV)
   - Legacy implementation: `transport_add_vechile.route_id` stores comma-separated route IDs
   - Note: Not a proper foreign key constraint due to legacy design

## New Fields Added

### transport_drivers (New Table)
- `driver_name` - Full name of the driver
- `license_number` - Driving license number
- `aadhar_number` - Aadhar card number
- `contact_number` - Phone number
- `address` - Residential address
- `status` - Active/Inactive status

### transport_add_vechile (Enhanced)
- `driver_id` - Links to driver (NEW)
- `insurance_number` - Vehicle insurance policy number (NEW)
- `insurance_expiry` - Insurance expiry date (NEW)
- `permit_number` - Vehicle permit number (NEW)
- `permit_expiry` - Permit expiry date (NEW)

## Usage Scenarios

### Scenario 1: Assigning a Driver to a Vehicle
```
1. Admin creates/edits a vehicle
2. Selects driver from dropdown (showing driver name and license)
3. System saves driver_id to vehicle record
4. Vehicle now shows driver info in vehicle list
5. Driver's page shows this vehicle in their assigned vehicles
```

### Scenario 2: Viewing Vehicle-Driver Assignments
```
From Vehicles Page:
- Each vehicle row shows assigned driver name and contact
- Vehicles without drivers show "No driver assigned"

From Drivers Page:
- Each driver row shows count and list of assigned vehicles
- Drivers without vehicles show "No vehicle assigned"
```

### Scenario 3: Unassigning a Driver
```
1. Admin edits vehicle
2. Selects "-- Select Driver --" (empty option)
3. System sets driver_id to NULL
4. Vehicle no longer shows in driver's assigned vehicles
```

## Data Integrity

### Constraints
- Cannot delete a driver who has vehicles assigned
- Cannot delete a vehicle that has students assigned
- Driver status can be set to 'inactive' without affecting assignments
- If driver is deleted (after unassigning all vehicles), vehicle.driver_id becomes NULL

### Validation
- Driver name is required
- Vehicle number is required and must be unique
- Insurance and permit fields are optional
- Date fields must be valid dates if provided
- Driver ID must exist in drivers table if provided
