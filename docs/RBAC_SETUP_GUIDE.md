# RBAC Setup and Testing Guide

## Quick Start Guide

### 1. Database Setup

Run the following SQL scripts in order:

```bash
# Step 1: Create RBAC tables and default permissions
mysql -u your_username -p your_database < db/rbac_schema.sql

# Step 2: Create test users (optional, for testing)
mysql -u your_username -p your_database < db/test_users.sql
```

### 2. Test User Credentials

After running the test users script, you can log in with:

| Role | Username | Password | Access Level |
|------|----------|----------|--------------|
| **Admin** | admin | Test@123 | Full system access |
| **Teacher** | teacher1 | Test@123 | Academic operations only |
| **Student** | student1 | Test@123 | View personal records only |

### 3. Verification Checklist

Test the RBAC system with each user role:

#### Admin User (Full Access)
- [ ] Can access Dashboard
- [ ] Can view RBAC Management page
- [ ] Can access all modules (Admission, Fees, Exams, etc.)
- [ ] Can add, edit, and delete records
- [ ] Sees all sidebar menu items

#### Teacher User (Limited Access)
- [ ] Can access Dashboard
- [ ] Cannot see RBAC Management
- [ ] Can view Students, Classes, Exams, Library, Attendance
- [ ] Can add/edit Exam marks and Attendance
- [ ] Cannot access Fees, Accounts, Staff management
- [ ] Cannot delete most records
- [ ] Sidebar shows only permitted modules

#### Student User (View Only)
- [ ] Can access Dashboard (limited view)
- [ ] Can view own Exam results
- [ ] Can view own Library status
- [ ] Can view own Fee records
- [ ] Cannot access any other modules
- [ ] Cannot add, edit, or delete anything
- [ ] Sees minimal sidebar items

## Visual Testing

### Test Scenarios

#### Scenario 1: Admin Access
1. Log in as `admin` / `Test@123`
2. Navigate to Dashboard - Should see all 11 modules + RBAC Management
3. Click on any module - Should have full access
4. Try to add/edit/delete - All operations should work

#### Scenario 2: Teacher Access
1. Log in as `teacher1` / `Test@123`
2. Navigate to Dashboard - Should see only 7-8 modules (no RBAC, Fees, Accounts, Staff)
3. Try to access `admission.php` - Should be **denied** (redirected to access_denied.php)
4. Try to access `exam_setting.php` - Should be **allowed**
5. Try to access `staff_setting.php` - Should be **denied**

#### Scenario 3: Student Access
1. Log in as `student1` / `Test@123`
2. Navigate to Dashboard - Should see only 3-4 modules (Dashboard, Exam Results, Library, Fees)
3. Try to access `admission.php` - Should be **denied**
4. Try to access `fees_setting.php` - Should be **denied** (cannot see settings)
5. Try to view exam results - Should be **allowed** (view only)

## RBAC Features Overview

### 1. Database-Driven Permissions

All permissions are stored in the database:
- `permissions` table: Defines all available permissions
- `role_permissions` table: Maps roles to permissions
- Easy to modify without code changes

### 2. Module-Based Access Control

Permissions are organized by module:
- **Dashboard**: View dashboard
- **Admission**: View, add, edit, delete admissions
- **Student**: View, edit students, manage TCs
- **Fees**: View, add, edit, delete, receipt
- **Exam**: View, add, edit, result
- **And 10+ more modules...**

### 3. Action-Level Permissions

Each module has specific actions:
- **view**: Read-only access
- **add**: Create new records
- **edit**: Modify existing records
- **delete**: Remove records
- **Special**: Custom actions (e.g., 'receipt', 'result', 'tc')

### 4. Dynamic UI Elements

The system automatically:
- Hides/shows sidebar menu items based on permissions
- Displays RBAC Management for admins only
- Redirects unauthorized access attempts
- Shows role-specific dashboard content

### 5. Security Features

- Permission checks at page load
- Cannot bypass with URL manipulation
- Session-based authentication
- Database-driven authorization
- Access denied page for unauthorized attempts

## Architecture Diagram

```
┌─────────────────────────────────────────────────┐
│                   USER LOGIN                    │
│              (username/password)                │
└───────────────────┬─────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────┐
│           SESSION MANAGEMENT                    │
│   stores: user_id, username, role               │
└───────────────────┬─────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────┐
│             RBAC CLASS (includes/rbac.php)      │
│  • hasPermission(module, action)                │
│  • requirePermission(module, action)            │
│  • getUserPermissions()                         │
└───────────────────┬─────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────┐
│           DATABASE LOOKUP                       │
│  permissions + role_permissions tables          │
└───────────────────┬─────────────────────────────┘
                    │
         ┌──────────┴──────────┐
         ▼                     ▼
    [GRANTED]             [DENIED]
         │                     │
         ▼                     ▼
  Load Page           access_denied.php
```

## Permission Matrix Summary

| Module | Admin | Teacher | Student |
|--------|-------|---------|---------|
| Dashboard | ✓ All | ✓ View | ✓ View |
| Admission | ✓ All | ✗ None | ✗ None |
| Student | ✓ All | ✓ View | ✗ None |
| School Settings | ✓ All | ✗ None | ✗ None |
| Classes/Sections | ✓ All | ✓ View | ✗ None |
| Subjects | ✓ All | ✓ View | ✗ None |
| Fees | ✓ All | ✗ None | ✓ View Own |
| Accounts | ✓ All | ✗ None | ✗ None |
| Exams | ✓ All | ✓ Add/Edit | ✓ View Results |
| Transport | ✓ All | ✗ None | ✗ None |
| Library | ✓ All | ✓ Add/Return | ✓ View Own |
| Staff | ✓ All | ✗ None | ✗ None |
| Attendance | ✓ All | ✓ Add/Edit | ✗ None |

Legend:
- ✓ All = Full access (view, add, edit, delete)
- ✓ View = Read-only access
- ✓ Add/Edit = Can create and modify
- ✓ View Own = Can only view personal records
- ✗ None = No access

## Troubleshooting

### Common Issues

1. **User cannot log in**
   - Verify user exists in `users` table
   - Check password hash is correct
   - Ensure role is set correctly

2. **User redirected to access_denied.php**
   - Check user's role in database
   - Verify permissions exist in `role_permissions` table
   - Check module and action names match

3. **Sidebar shows no items**
   - Ensure RBAC tables are populated
   - Verify user role has permissions
   - Check database connection

4. **Permission check not working**
   - Ensure `rbac_schema.sql` was run
   - Verify `includes/bootstrap.php` loads `rbac.php`
   - Check session is active

### SQL Queries for Debugging

```sql
-- Check user's role
SELECT user_id, role FROM users WHERE user_id = 'username';

-- Check user's permissions
SELECT p.module, p.action 
FROM role_permissions rp 
JOIN permissions p ON rp.permission_id = p.id 
WHERE rp.role = 'Teacher';

-- Count permissions per role
SELECT role, COUNT(*) as permission_count 
FROM role_permissions 
GROUP BY role;
```

## Next Steps

1. Review the RBAC Management page at `rbac_management.php`
2. View the visual diagram at `rbac_diagram.html`
3. Read full documentation in `docs/RBAC_DOCUMENTATION.md`
4. Test with different user roles
5. Customize permissions as needed for your school

## Support

For detailed documentation, see:
- `docs/RBAC_DOCUMENTATION.md` - Complete technical documentation
- `rbac_diagram.html` - Visual representation of RBAC structure
- `rbac_management.php` - Admin interface for viewing permissions

---

**Setup Complete!** Your School ERP now has a fully functional RBAC system.
