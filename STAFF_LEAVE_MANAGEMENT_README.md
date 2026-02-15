# Staff Leave Management Feature

## Overview
This feature implements a comprehensive staff leave management system that allows:
- Admin to manage different types of leaves (Sick Leave, Casual Leave, etc.)
- Staff to apply for leave
- Admin to approve or reject leave applications

## Features Implemented

### 1. Leave Types Management (Admin)
**Location:** Staff > Leave Types

**Features:**
- Create, edit, and delete leave types
- Configure maximum days allowed per year (or unlimited)
- Set whether leave requires approval
- Toggle active/inactive status
- View all leave types with status indicators

**Access:** Navigate to Staff menu > Leave Types

### 2. Staff Leave Applications
**Location:** Staff > Staff Leaves

**Features:**
- Apply for leave by selecting staff member, leave type, dates, and reason
- Automatic calculation of leave days
- Validation for overlapping leaves
- View all leave applications with filters (by status, staff, leave type)
- Edit pending leave applications
- Delete pending leave applications
- Admin can approve or reject with remarks

**Access:** Navigate to Staff menu > Staff Leaves

### 3. Leave Application Workflow
1. **Staff applies for leave** - Fill in the application form
2. **Application submitted** - Status: Pending
3. **Admin reviews** - Can approve or reject
4. **If approved** - Status changes to Approved, staff receives approval
5. **If rejected** - Status changes to Rejected with admin's reason

## Database Tables

### leave_types
Stores different types of leaves available for staff.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | string | Leave type name (e.g., "Sick Leave") |
| description | text | Description of the leave type |
| max_days | integer | Maximum days allowed per year (null = unlimited) |
| requires_approval | boolean | Whether approval is required |
| is_active | boolean | Whether leave type is active |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

### staff_leaves
Stores staff leave applications and their status.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| staff_id | bigint | Reference to staff member |
| leave_type_id | bigint | Foreign key to leave_types |
| start_date | date | Leave start date |
| end_date | date | Leave end date |
| days | integer | Number of leave days |
| reason | text | Reason for leave |
| status | enum | pending/approved/rejected |
| approved_by | bigint | User ID who approved/rejected |
| approved_at | timestamp | Approval/rejection timestamp |
| admin_remarks | text | Admin's remarks/comments |
| created_at | timestamp | Application creation time |
| updated_at | timestamp | Last update time |

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

This will create the `leave_types` and `staff_leaves` tables.

### 2. Seed Default Leave Types (Optional)
You can manually create leave types through the admin interface, or add a seeder. Common leave types include:
- Sick Leave
- Casual Leave
- Earned Leave
- Maternity Leave
- Paternity Leave
- Emergency Leave

### 3. Access the Features
1. Login to the application
2. Navigate to **Staff** menu in the sidebar
3. Click on **Leave Types** to manage leave types (Admin)
4. Click on **Staff Leaves** to manage leave applications

## Usage Guide

### For Admins

#### Managing Leave Types
1. Go to Staff > Leave Types
2. Click "Add Leave Type"
3. Fill in:
   - Leave type name (e.g., "Sick Leave")
   - Description (optional)
   - Max days per year (leave empty for unlimited)
   - Whether it requires approval (Yes/No)
   - Status (Active/Inactive)
4. Click "Save Leave Type"

#### Reviewing Leave Applications
1. Go to Staff > Staff Leaves
2. View all leave applications
3. Use filters to find specific applications
4. For pending applications:
   - Click the green checkmark to approve (with optional remarks)
   - Click the red X to reject (requires reason)
5. View approved/rejected applications and their history

### For Staff

#### Applying for Leave
1. Go to Staff > Staff Leaves
2. Click "Apply for Leave"
3. Fill in:
   - Select your name from staff dropdown
   - Select leave type
   - Choose start date and end date
   - System automatically calculates number of days
   - Provide reason for leave
4. Click "Submit Application"
5. Wait for admin approval

#### Editing Pending Applications
1. Go to Staff > Staff Leaves
2. Find your pending application
3. Click the edit icon
4. Update the details
5. Click "Update Application"

#### Viewing Application Status
1. Go to Staff > Staff Leaves
2. Use filters to view:
   - Pending applications
   - Approved leaves
   - Rejected applications
3. Click "View" to see full details including admin remarks

## Features & Validations

### Validations Implemented
- **Leave dates**: Start date must be today or future
- **Date range**: End date must be equal to or after start date
- **Overlapping leaves**: System prevents overlapping leave applications for the same staff
- **Unique leave types**: Each leave type name must be unique
- **Edit restrictions**: Only pending applications can be edited
- **Delete restrictions**: Only pending applications can be deleted
- **Approval restrictions**: Only pending applications can be approved/rejected

### User Experience Features
- **Dynamic day calculation**: Leave days are calculated automatically
- **Leave type information**: Shows max days and approval requirement
- **Status badges**: Color-coded status indicators (Pending=Yellow, Approved=Green, Rejected=Red)
- **Modal confirmations**: Approval/rejection through modal dialogs
- **Filters**: Filter applications by status, staff member, and leave type
- **Pagination**: All listings support pagination
- **Responsive design**: Works on all screen sizes

## Navigation
The leave management features are accessible through the Staff submenu:
- **Staff** (main menu)
  - Staff Members
  - Departments
  - Positions
  - **Leave Types** (new)
  - **Staff Leaves** (new)

## Models

### LeaveType Model
- **Location**: `app/Models/LeaveType.php`
- **Relationships**: 
  - `hasMany` StaffLeave
- **Scopes**: 
  - `active()` - Get only active leave types
  - `inactive()` - Get only inactive leave types

### StaffLeave Model
- **Location**: `app/Models/StaffLeave.php`
- **Relationships**:
  - `belongsTo` Staff
  - `belongsTo` LeaveType
  - `belongsTo` User (approver)
- **Scopes**:
  - `pending()` - Get pending applications
  - `approved()` - Get approved applications
  - `rejected()` - Get rejected applications
  - `forStaff($staffId)` - Get applications for specific staff
  - `dateRange($start, $end)` - Get applications in date range

### Staff Model (Updated)
- **Location**: `app/Models/Staff.php`
- **New Relationship**: 
  - `hasMany` StaffLeave (leaves)

## Controllers

### LeaveTypeController
- **Location**: `app/Http/Controllers/LeaveTypeController.php`
- **Routes**: Resource routes (`leave-types.*`)
- **Additional**: `toggleStatus` - Toggle leave type active/inactive

### StaffLeaveController
- **Location**: `app/Http/Controllers/StaffLeaveController.php`
- **Routes**: Resource routes (`staff-leaves.*`)
- **Additional Routes**:
  - `approve` - Approve a leave application
  - `reject` - Reject a leave application

## Views

### Leave Types
- `resources/views/leave-types/index.blade.php` - List all leave types
- `resources/views/leave-types/create.blade.php` - Create new leave type
- `resources/views/leave-types/edit.blade.php` - Edit leave type

### Staff Leaves
- `resources/views/staff-leaves/index.blade.php` - List all leave applications
- `resources/views/staff-leaves/create.blade.php` - Apply for leave
- `resources/views/staff-leaves/edit.blade.php` - Edit leave application
- `resources/views/staff-leaves/show.blade.php` - View leave application details

## Routes

### Leave Types
```php
Route::resource('leave-types', LeaveTypeController::class);
Route::post('/leave-types/{leaveType}/toggle-status', [LeaveTypeController::class, 'toggleStatus']);
```

### Staff Leaves
```php
Route::resource('staff-leaves', StaffLeaveController::class);
Route::post('/staff-leaves/{staffLeave}/approve', [StaffLeaveController::class, 'approve']);
Route::post('/staff-leaves/{staffLeave}/reject', [StaffLeaveController::class, 'reject']);
```

## Technical Notes

### Staff Table Naming
The codebase has an inconsistency where:
- The migration creates a table named `staff`
- The Staff model references `staff_employee`

To handle this, the `staff_leaves` table uses `unsignedBigInteger` for `staff_id` with an index, but without a foreign key constraint. This ensures compatibility regardless of which table name is actually used.

### Future Enhancements
Possible enhancements for future development:
- Leave balance tracking per staff member
- Leave reports and analytics
- Email notifications for leave applications and approvals
- Leave calendar view
- Leave application on behalf of staff
- Bulk approval/rejection
- Leave carry forward rules
- Holiday calendar integration
- Leave quota management per department/position

## Troubleshooting

### Migration Issues
If you encounter issues running migrations:
1. Check database connection in `.env`
2. Ensure `staff` or `staff_employee` table exists
3. Ensure `users` table exists (for approver foreign key)
4. Run `php artisan migrate:fresh` to recreate all tables (WARNING: This will delete all data)

### Staff Not Showing in Dropdown
If staff members don't appear in dropdowns:
1. Ensure staff records exist in the database
2. Check the Staff model's table name configuration
3. Verify staff records have required fields (id, name, employee_id)

### Permission Issues
If you encounter permission errors:
1. Ensure you're logged in
2. Check if your user role has appropriate permissions
3. Update the controllers to add authorization checks if needed

## Support
For issues or questions, please refer to the main application documentation or contact the development team.
