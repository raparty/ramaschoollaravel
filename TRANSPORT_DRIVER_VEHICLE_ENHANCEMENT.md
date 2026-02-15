# Transport Module Enhancement - Driver and Vehicle Details

## Overview
This document describes the implementation of driver and vehicle details management in the transport module.

## Requirements Implemented
1. ✅ Add/Edit Driver Details (License, Aadhar)
2. ✅ Add/Edit Vehicle Details (Insurance, Permit)
3. ✅ Allot vehicles to drivers
4. ✅ View which vehicle is with which driver

## Database Changes

### New Table: `transport_drivers`
```sql
- driver_id (INT, Primary Key, Auto Increment)
- driver_name (VARCHAR 100)
- license_number (VARCHAR 50, Nullable)
- aadhar_number (VARCHAR 20, Nullable)
- contact_number (VARCHAR 20, Nullable)
- address (TEXT, Nullable)
- status (ENUM: 'active', 'inactive', Default: 'active')
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### Modified Table: `transport_add_vechile`
Added columns:
```sql
- driver_id (INT, Nullable, Foreign Key to transport_drivers.driver_id)
- insurance_number (VARCHAR 100, Nullable)
- insurance_expiry (DATE, Nullable)
- permit_number (VARCHAR 100, Nullable)
- permit_expiry (DATE, Nullable)
```

## Models

### TransportDriver (`app/Models/TransportDriver.php`)
- Manages driver records
- Relationships:
  - `hasMany` vehicles (one driver can be assigned to multiple vehicles)
- Scopes:
  - `ordered()` - Orders by driver name
  - `search()` - Searches by name, license, or contact
  - `status()` - Filters by status
- Accessors:
  - `vehicle_numbers` - Gets comma-separated list of assigned vehicle numbers

### TransportVehicle (Updated)
- Added new fillable fields for documentation
- Added relationship:
  - `belongsTo` driver (vehicle belongs to one driver)
- New casts for date fields (insurance_expiry, permit_expiry)

## Controllers

### TransportDriverController (`app/Http/Controllers/TransportDriverController.php`)
Full CRUD operations:
- `index()` - List all drivers with search and status filter
- `create()` - Show create form
- `store()` - Save new driver
- `edit()` - Show edit form
- `update()` - Update driver
- `destroy()` - Delete driver (with vehicle assignment check)

### TransportVehicleController (Updated)
- Added driver dropdown in create/edit forms
- Added driver to eager loading in index
- Added validation for new fields

## Views

### Driver Management Views
1. **Index** (`resources/views/transport/drivers/index.blade.php`)
   - Lists all drivers with search and status filter
   - Shows assigned vehicles for each driver
   - Edit and delete actions

2. **Create** (`resources/views/transport/drivers/create.blade.php`)
   - Form to add new driver
   - Fields: name, license, aadhar, contact, address, status

3. **Edit** (`resources/views/transport/drivers/edit.blade.php`)
   - Form to edit driver details
   - Shows currently assigned vehicles

### Vehicle Management Views (Updated)
1. **Index** - Added "Driver" column showing assigned driver with contact
2. **Create** - Added:
   - Driver dropdown selection
   - Vehicle documentation section (Insurance and Permit details)
3. **Edit** - Added same fields as create form

### Transport Index (Updated)
- Added "Drivers" card in the main transport dashboard
- Updated statistics to show driver count

## Routes
Added to `routes/web.php`:
```php
Route::resource('drivers', TransportDriverController::class)->except(['show']);
```

## Features

### Driver Management
- Add/Edit/Delete drivers
- Track license and Aadhar numbers
- Store contact information
- Mark drivers as active/inactive
- View assigned vehicles per driver
- Prevent deletion if vehicles are assigned

### Vehicle Management
- Assign driver to vehicle
- Track insurance details (number and expiry)
- Track permit details (number and expiry)
- View driver information in vehicle list
- Unassign driver by selecting "-- Select Driver --"

### Vehicle-Driver Assignment
- One-to-many relationship (one driver can have multiple vehicles)
- Visible in both driver and vehicle views
- Cascading delete protection (can't delete driver with assigned vehicles)

## Navigation
- Accessible via: Transport → Drivers
- Direct link: `/transport/drivers`

## Validation
### Driver Validation
- driver_name: Required, max 100 characters
- license_number: Optional, max 50 characters
- aadhar_number: Optional, max 20 characters
- contact_number: Optional, max 20 characters
- address: Optional, text
- status: Required, must be 'active' or 'inactive'

### Vehicle Validation (New Fields)
- driver_id: Optional, must exist in transport_drivers table
- insurance_number: Optional, max 100 characters
- insurance_expiry: Optional, date format
- permit_number: Optional, max 100 characters
- permit_expiry: Optional, date format

## Migration Files
1. `2026_02_15_152000_create_transport_drivers_table.php`
   - Creates transport_drivers table
   - Uses `Schema::hasTable()` for idempotency

2. `2026_02_15_152100_add_vehicle_and_driver_details_to_transport_vechile.php`
   - Adds new columns to transport_add_vechile
   - Creates foreign key constraint for driver_id
   - Uses `Schema::hasColumn()` checks for idempotency

## Usage Instructions

### Adding a Driver
1. Navigate to Transport → Drivers
2. Click "Add New Driver"
3. Fill in driver details
4. Click "Save Driver"

### Assigning Driver to Vehicle
1. Navigate to Transport → Vehicles
2. Click "Edit" on a vehicle or create new
3. Select driver from "Assign Driver" dropdown
4. Fill in insurance and permit details if available
5. Click "Save/Update Vehicle"

### Viewing Vehicle-Driver Assignments
- **From Drivers Page**: The "Assigned Vehicles" column shows vehicle count and numbers
- **From Vehicles Page**: The "Driver" column shows driver name and contact

### Unassigning a Driver
1. Edit the vehicle
2. Select "-- Select Driver --" from dropdown
3. Save changes

## Technical Notes
- Maintains legacy table name `transport_add_vechile` (with spelling)
- Uses foreign key with `onDelete('set null')` to prevent orphaned records
- Date fields use Laravel's date casting for easy manipulation
- Eager loading used to prevent N+1 query issues
- All forms include CSRF protection
- Success/error messages shown after operations

## Future Enhancements (Not Implemented)
- Document expiry alerts/notifications
- Driver schedule management
- Vehicle inspection tracking
- Document upload capability
- Driver performance tracking
- Automated renewal reminders
