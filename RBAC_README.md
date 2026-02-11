# RBAC System - Quick Reference

## 📋 What's Included

This School ERP now has complete Role-Based Access Control (RBAC) with:

### Core System Files
- `db/rbac_schema.sql` - Database schema
- `db/test_users.sql` - Test users for testing
- `includes/rbac.php` - RBAC class
- `access_denied.php` - Access denied page
- `rbac_management.php` - Admin interface

### Visual & Documentation
- `rbac_diagram.html` - Interactive visual diagram
- `RBAC_SUMMARY.md` - Complete overview
- `docs/RBAC_DOCUMENTATION.md` - Technical docs
- `docs/RBAC_SETUP_GUIDE.md` - Setup guide
- `screenshots/rbac_structure_diagram.png` - Visual diagram

## 🚀 Quick Setup

```bash
# 1. Setup database
mysql -u username -p database < db/rbac_schema.sql
mysql -u username -p database < db/test_users.sql

# 2. Test with these users:
#    admin / Test@123 (Full access)
#    teacher1 / Test@123 (Academic only)
#    student1 / Test@123 (View only)
```

## 📊 Role Permissions

| Role | Access Level |
|------|--------------|
| **Admin** | ✅ Full system access - All modules, all actions |
| **Teacher** | ✅ Academic operations - Students, exams, attendance, library |
| **Student** | ✅ Personal records - Own dashboard, results, fees, library |

## 📖 Documentation

Start here: **`RBAC_SUMMARY.md`** - Complete overview with everything you need

For more details:
- Setup: `docs/RBAC_SETUP_GUIDE.md`
- Technical: `docs/RBAC_DOCUMENTATION.md`
- Visual: Open `rbac_diagram.html` in browser
- Admin UI: Login as admin → RBAC Management

## ✨ Key Features

- 🔐 Database-driven permissions
- 🎯 Module + action level control
- 🚪 Automatic access denial
- 📊 Dynamic sidebar menus
- 👥 Three roles: Admin, Teacher, Student
- 📋 Permission matrix for all modules
- 🎨 Visual diagram and admin interface

## 🎯 How It Works

1. User logs in → Role stored in session
2. Page loads → Checks required permission
3. RBAC queries database → Grants/denies access
4. If denied → Redirects to access_denied.php
5. Sidebar shows only permitted modules

## 🔍 Test Scenarios

### Test Admin Access
- ✅ Should see all 11+ modules
- ✅ Can add/edit/delete everything
- ✅ Sees "RBAC Management" card

### Test Teacher Access  
- ✅ Sees 7-8 academic modules only
- ✅ Can mark attendance, enter exam marks
- ❌ Cannot access fees, accounts, staff
- ❌ Redirected from restricted pages

### Test Student Access
- ✅ Sees minimal menu (4 items)
- ✅ Can view own results and fees
- ❌ Cannot access any settings
- ❌ Cannot modify anything

## 💡 Quick Code Examples

### Protect a Page
```php
<?php
require_once("includes/bootstrap.php");
RBAC::requirePermission('module_name', 'action');
include_once("includes/header.php");
?>
```

### Check Permission
```php
<?php if (RBAC::hasPermission('fees', 'add')): ?>
    <button>Add Fee</button>
<?php endif; ?>
```

### Get User Role
```php
<?php 
$role = RBAC::getUserRole(); // 'Admin', 'Teacher', or 'Student'
?>
```

## 📞 Support

Read the docs first:
1. `RBAC_SUMMARY.md` - Start here
2. `docs/RBAC_SETUP_GUIDE.md` - Setup help
3. `docs/RBAC_DOCUMENTATION.md` - Technical details

---

**The School ERP now has enterprise-grade access control! 🎉**
