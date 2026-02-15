# Settings Module - RBAC Implementation

## Overview
The Settings module provides a complete Role-Based Access Control (RBAC) system for managing user permissions across the School ERP system.

## Features

### 1. Role Management
- Create, edit, view, and delete user roles
- Assign multiple permissions to each role
- Track number of users per role
- Active/inactive status for roles

### 2. Permission Management
- View all permissions organized by module
- Create new custom permissions
- Delete unused permissions
- Permissions structure: Module + Action + Optional Submodule

### 3. User Management
- Assign roles to users
- Change user roles
- Search and filter users by role
- View user details with current role

### 4. Dashboard
- Statistics overview (roles count, permissions count, users count)
- Quick access to all management modules
- Clean, modern UI with Bootstrap 5

## Installation & Setup

### 1. Run Database Seeders
```bash
# Seed permissions (32 default permissions)
php artisan db:seed --class=PermissionSeeder

# Seed roles and assign permissions (6 default roles)
php artisan db:seed --class=RoleSeeder

# Or run both at once
php artisan db:seed
```

### 2. Default Roles Created
- **Admin**: Full system access (all permissions automatically)
- **Principal**: Almost all permissions (view, create, edit)
- **Teacher**: Students, attendance, exams management
- **Accountant**: Fees, accounts, and financial reports
- **Librarian**: Library operations (books, issue, return)
- **Receptionist**: Student admission and basic viewing

### 3. Default Permissions by Module
- **Students**: view, create, edit, delete
- **Fees**: view, create, edit, delete
- **Library**: view, create, edit, issue, return
- **Staff**: view, create, edit, delete
- **Attendance**: view, create, edit
- **Exams**: view, create, edit, marks
- **Accounts**: view, create, edit
- **Reports**: view
- **Transport**: view, create, edit

## Usage

### Accessing Settings Module
1. Login as admin user
2. Navigate to Settings from the sidebar
3. You'll see three main sections:
   - Role Management
   - Permissions
   - User Management

### Creating a New Role
1. Go to Settings → Role Management
2. Click "Create New Role"
3. Enter role name and description
4. Select permissions by module
5. Set status (active/inactive)
6. Click "Create Role"

### Assigning Roles to Users
1. Go to Settings → User Management
2. Find the user (use search/filter if needed)
3. Click "Change Role"
4. Select the new role from dropdown
5. Click "Update User Role"

### Managing Permissions
1. Go to Settings → Permissions
2. View all permissions grouped by module
3. Create new custom permissions as needed
4. Delete unused permissions (if not assigned to any role)

## Authorization Gates

The system uses Laravel Gates for authorization:
- `manage-settings`: Access to settings module (admin only)
- Module-specific gates (e.g., `view-students`, `create-fees`)

## Model Relationships

### Role Model
- Has many permissions (through role_permissions pivot)
- Users are linked via string-based role name

### Permission Model
- Belongs to many roles (through role_permissions pivot)
- Identified by unique combination of module + action

### User Model
- Has role as string attribute
- `hasPermission($module, $action)` method to check permissions
- Admins automatically have all permissions

## Database Tables Used
- `roles`: Store role definitions
- `permissions`: Store permission definitions
- `role_permissions`: Pivot table linking roles to permissions
- `users`: Contains role column for user role assignment

## Routes
```
GET  /settings                    - Dashboard
GET  /settings/roles              - List roles
GET  /settings/roles/create       - Create role form
POST /settings/roles              - Store new role
GET  /settings/roles/{id}         - View role details
GET  /settings/roles/{id}/edit    - Edit role form
PUT  /settings/roles/{id}         - Update role
DELETE /settings/roles/{id}       - Delete role

GET  /settings/permissions        - List permissions
GET  /settings/permissions/create - Create permission form
POST /settings/permissions        - Store new permission
DELETE /settings/permissions/{id} - Delete permission

GET  /settings/users              - List users
GET  /settings/users/{id}/edit    - Edit user role form
PUT  /settings/users/{id}         - Update user role
```

## Security Considerations
- Only admin users can access settings module
- Role names are stored as strings in users table
- Permissions are checked via User model's hasPermission() method
- Admin role bypasses all permission checks (Gate::before)

## Extending the System

### Adding New Permissions
1. Either use the UI (Settings → Permissions → Create)
2. Or add to PermissionSeeder.php for default setup

### Adding New Roles
1. Either use the UI (Settings → Roles → Create)
2. Or add to RoleSeeder.php for default setup

### Custom Permission Logic
Edit `AuthServiceProvider.php` to add custom gate definitions or modify existing ones.

## Troubleshooting

### Users can't access features
1. Check if user has a role assigned
2. Check if role has required permissions
3. Verify role status is "active"
4. Check AuthServiceProvider gate definitions

### Role/Permission changes not taking effect
1. User may need to log out and log back in
2. Clear application cache: `php artisan cache:clear`
3. Verify database changes were saved

## Code Structure
```
app/
├── Http/Controllers/
│   ├── SettingsController.php         # Main dashboard
│   ├── RoleController.php              # Role CRUD
│   ├── PermissionController.php        # Permission management
│   └── UserManagementController.php    # User role assignment
├── Models/
│   ├── Role.php                        # Role model
│   ├── Permission.php                  # Permission model
│   ├── RolePermission.php             # Pivot model
│   └── User.php                        # Updated with RBAC methods
└── Providers/
    └── AuthServiceProvider.php         # Gate definitions

database/seeders/
├── PermissionSeeder.php                # Default permissions
└── RoleSeeder.php                      # Default roles

resources/views/settings/
├── index.blade.php                     # Dashboard
├── roles/
│   ├── index.blade.php                 # Role list
│   ├── create.blade.php                # Create role
│   ├── edit.blade.php                  # Edit role
│   └── show.blade.php                  # View role
├── permissions/
│   ├── index.blade.php                 # Permission list
│   └── create.blade.php                # Create permission
└── users/
    ├── index.blade.php                 # User list
    └── edit.blade.php                  # Edit user role
```

## Screenshots
(To be added after manual testing)

## Future Enhancements
- Permission inheritance
- Custom permission groups
- Role templates
- Audit log for role/permission changes
- Bulk user role updates
- Export/import roles and permissions
