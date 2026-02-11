# RBAC (Role-Based Access Control) System Documentation

## Overview

The School ERP system now includes a comprehensive Role-Based Access Control (RBAC) system that manages permissions for different user roles. This system ensures that users can only access features and data appropriate to their role.

## Architecture

### Database Schema

The RBAC system uses two main tables:

1. **permissions** - Stores all available permissions in the system
   - `id` - Primary key
   - `module` - Module name (e.g., 'admission', 'fees', 'exam')
   - `action` - Action type (e.g., 'view', 'add', 'edit', 'delete')
   - `description` - Human-readable description
   - `created_at` - Timestamp

2. **role_permissions** - Maps roles to permissions
   - `id` - Primary key
   - `role` - User role (Admin, Teacher, Student)
   - `permission_id` - Foreign key to permissions table
   - `created_at` - Timestamp

### RBAC Class (`includes/rbac.php`)

The RBAC class provides the following key methods:

- `RBAC::hasPermission($module, $action)` - Check if current user has a specific permission
- `RBAC::requirePermission($module, $action)` - Require permission or redirect to access denied page
- `RBAC::getUserPermissions()` - Get all permissions for current user
- `RBAC::canAccessPage($page)` - Check if user can access a specific page
- `RBAC::getUserRole()` - Get the current user's role
- `RBAC::getAccessibleModules()` - Get list of modules user can access

## User Roles & Permissions

### Admin Role

**Full System Access** - Admins have complete control over all modules and actions.

Permissions include:
- All view, add, edit, and delete operations
- User and role management
- System configuration
- Financial reports and accounts
- Complete audit trail

### Teacher Role

**Academic Operations** - Teachers have access to academic modules with some restrictions.

Permissions include:
- **View**: Students, Classes, Sections, Streams, Subjects, Exams, Library, Attendance
- **Add/Edit**: Exam marks, Attendance records, Library book issues
- **Special**: Return library books, Generate exam results

Restrictions:
- Cannot access financial data
- Cannot manage staff
- Cannot modify system settings
- Cannot delete most records

### Student Role

**Personal Records Only** - Students have very limited, view-only access.

Permissions include:
- **View**: Personal dashboard, Own exam results, Library status, Fee records

Restrictions:
- Cannot view other students' data
- Cannot access staff information
- Cannot view financial reports
- Cannot add, edit, or delete anything
- Cannot access system settings

## Implementation Guide

### Setup Instructions

1. **Run Database Migration**
   ```bash
   mysql -u username -p database_name < db/rbac_schema.sql
   ```

2. **RBAC is Automatically Loaded**
   The RBAC class is loaded automatically via `includes/bootstrap.php`, so it's available throughout the application.

### Protecting Pages

To protect a page with RBAC, add the permission check after including bootstrap.php but before including header.php:

```php
<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");

// RBAC: Check if user has permission
RBAC::requirePermission('module_name', 'action_type');

include_once("includes/header.php");
include_once("includes/sidebar.php");
?>
```

**Examples:**

```php
// Protect admission page (view only)
RBAC::requirePermission('admission', 'view');

// Protect add admission page
RBAC::requirePermission('admission', 'add');

// Protect edit fees page
RBAC::requirePermission('fees', 'edit');

// Protect delete staff page
RBAC::requirePermission('staff', 'delete');
```

### Conditional UI Elements

Show/hide UI elements based on permissions:

```php
<?php if (RBAC::hasPermission('admission', 'add')): ?>
    <a href="add_admission.php" class="btn-fluent-primary">Add New Admission</a>
<?php endif; ?>

<?php if (RBAC::hasPermission('fees', 'delete')): ?>
    <button onclick="deleteRecord()" class="btn-danger">Delete</button>
<?php endif; ?>
```

### Dynamic Sidebar

The sidebar automatically filters menu items based on user permissions. This is already implemented in `includes/sidebar.php`.

## Available Modules & Actions

| Module | Actions | Description |
|--------|---------|-------------|
| dashboard | view | Access main dashboard |
| admission | view, add, edit, delete | Admission management |
| student | view, edit, delete, tc | Student records and TCs |
| school_setting | view, edit | School configuration |
| class | view, add, edit, delete | Class management |
| section | view, add, edit, delete | Section management |
| stream | view, add, edit, delete | Stream management |
| subject | view, add, edit, delete | Subject management |
| allocation | view, add, edit, delete | Resource allocation |
| fees | view, add, edit, delete, receipt | Fees management |
| account | view, add, edit, delete | Account management |
| exam | view, add, edit, delete, result | Examination system |
| transport | view, add, edit, delete | Transport management |
| library | view, add, edit, delete, return | Library operations |
| staff | view, add, edit, delete | Staff management |
| attendance | view, add, edit | Attendance tracking |

## RBAC Management Interface

Admins can view and manage permissions through the RBAC Management page:

**URL**: `rbac_management.php`

Features:
- Visual overview of all roles and their permissions
- Permissions matrix showing which roles have which permissions
- Role statistics and descriptions
- Color-coded permission indicators

## Visual Diagram

A visual representation of the RBAC structure is available at:

**URL**: `rbac_diagram.html`

This diagram shows:
- All three roles and their permission levels
- What each role can and cannot do
- Visual hierarchy of access control
- Color-coded role indicators

## Security Best Practices

1. **Always check permissions** at the beginning of protected pages
2. **Use requirePermission()** for critical operations (redirects if no permission)
3. **Use hasPermission()** for conditional UI elements
4. **Never trust client-side** checks - always validate on server
5. **Test with different roles** to ensure proper access control
6. **Log permission denials** for security auditing (future enhancement)

## Extending the RBAC System

### Adding New Permissions

1. Insert new permission into the database:
   ```sql
   INSERT INTO permissions (module, action, description) 
   VALUES ('new_module', 'new_action', 'Description');
   ```

2. Assign permission to roles:
   ```sql
   INSERT INTO role_permissions (role, permission_id) 
   SELECT 'Admin', id FROM permissions WHERE module='new_module' AND action='new_action';
   ```

3. Update `RBAC::getPageModuleMap()` if needed for automatic page protection.

### Adding New Roles

To add a new role:

1. Update the `role` enum in the `users` table
2. Update the `role` enum in the `role_permissions` table
3. Define permissions for the new role in `role_permissions`
4. Update UI components that display roles

## Troubleshooting

### User Cannot Access Page

1. Check if user is logged in (`$_SESSION['role']` is set)
2. Verify user's role in the database
3. Check if role has the required permission in `role_permissions`
4. Verify permission exists in `permissions` table
5. Check for typos in module/action names

### Sidebar Not Showing Expected Items

1. Ensure RBAC class is loaded (via bootstrap.php)
2. Check sidebar configuration in `includes/sidebar.php`
3. Verify permissions are correctly assigned to the role
4. Clear browser cache if using cached sessions

### Permission Check Not Working

1. Ensure `RBAC::requirePermission()` is called BEFORE `header.php`
2. Check database connection is working
3. Verify session is started (done in bootstrap.php)
4. Check for SQL errors in server logs

## Files Changed/Added

- `db/rbac_schema.sql` - Database schema for RBAC tables
- `includes/rbac.php` - RBAC class with all methods
- `includes/bootstrap.php` - Modified to load RBAC class
- `includes/sidebar.php` - Updated with dynamic permission-based menu
- `access_denied.php` - Access denied page
- `rbac_management.php` - Admin interface for viewing permissions
- `rbac_diagram.html` - Visual RBAC structure diagram
- Multiple page files - Added permission checks

## Future Enhancements

1. **Permission Auditing** - Log all permission checks and denials
2. **Dynamic Permission Management** - UI for admins to modify permissions without SQL
3. **Fine-grained Permissions** - Row-level security (e.g., teachers only see their classes)
4. **Permission Inheritance** - Role hierarchy with inherited permissions
5. **Time-based Permissions** - Temporary access grants
6. **API Endpoints** - RESTful API for permission management

## Support

For questions or issues with the RBAC system:
1. Check this documentation
2. Review the visual diagram (`rbac_diagram.html`)
3. Use the RBAC Management interface (`rbac_management.php`)
4. Check server error logs for permission-related issues

---

**Last Updated**: 2026-02-11  
**Version**: 1.0.0  
**Author**: School ERP Development Team
