# RBAC System - Quick Reference (v2 - 5 Roles)

## 📋 What's Included

This School ERP now has complete Role-Based Access Control (RBAC) with **5 specialized roles** to reduce teacher burden:

### Core System Files
- `db/rbac_schema.sql` - Initial database schema
- `db/rbac_schema_v2.sql` - **NEW: Migration for 5 roles**
- `db/test_users.sql` - Test users for all 5 roles
- `includes/rbac.php` - RBAC class
- `access_denied.php` - Access denied page
- `rbac_management.php` - Admin interface

### Visual & Documentation
- `rbac_diagram_v2.html` - **NEW: Interactive visual diagram (5 roles)**
- `RBAC_v2_UPDATE.md` - **NEW: What changed and why**
- `RBAC_SUMMARY.md` - Complete overview
- `docs/RBAC_DOCUMENTATION.md` - Technical docs
- `docs/RBAC_SETUP_GUIDE.md` - Setup guide

## 🚀 Quick Setup

```bash
# 1. Setup database
mysql -u username -p database < db/rbac_schema.sql
mysql -u username -p database < db/rbac_schema_v2.sql  # NEW: 5 roles migration
mysql -u username -p database < db/test_users.sql

# 2. Test with these users:
#    admin / Test@123 (Full access)
#    office1 / Test@123 (Transport, fees, accounts)
#    librarian1 / Test@123 (Library only)
#    teacher1 / Test@123 (Academic only - NO transport/library)
#    student1 / Test@123 (View only)
```

## 📊 Role Permissions (Updated)

| Role | Access Level |
|------|--------------|
| **Admin** | ✅ Full system access - All modules, all actions |
| **Office Manager** | ✅ Operations - Transport, fees, accounts |
| **Librarian** | ✅ Library specialist - Library operations only |
| **Teacher** | ✅ Academic only - Exams, attendance (NO transport/library) |
| **Student** | ✅ Personal records - Own dashboard, results, fees, library |

## 🎯 What's New in v2?

**Problem:** Teachers were burdened with transport and library management

**Solution:** Created specialized roles
- **Office Manager** - Takes over transport, fees, accounts
- **Librarian** - Takes over all library operations
- **Teacher** - Now focused ONLY on academic work (exams, attendance)

## 📖 Documentation

Start here: **`RBAC_v2_UPDATE.md`** - What changed in v2 (5 roles)

Then read: **`RBAC_SUMMARY.md`** - Complete overview

For more details:
- Setup: `docs/RBAC_SETUP_GUIDE.md`
- Technical: `docs/RBAC_DOCUMENTATION.md`
- Visual: Open `rbac_diagram_v2.html` in browser
- Admin UI: Login as admin → RBAC Management

## ✨ Key Features

- 🔐 Database-driven permissions
- 🎯 Module + action level control
- 🚪 Automatic access denial
- 📊 Dynamic sidebar menus
- 👥 **Five specialized roles:** Admin, Office Manager, Librarian, Teacher, Student
- 📋 Permission matrix for all modules
- 🎨 Visual diagram and admin interface
- 💼 **Reduces teacher burden** by separating operational roles

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

### Test Office Manager Access (NEW)
- ✅ Can access Transport module
- ✅ Can manage fees and accounts
- ✅ Can view students (for transport/fees)
- ❌ Cannot access exams or library
- ❌ Cannot access staff management

### Test Librarian Access (NEW)
- ✅ Can access Library module  
- ✅ Can issue/return books
- ✅ Can view students (for book issue)
- ❌ Cannot access transport or fees
- ❌ Cannot access exams

### Test Teacher Access (UPDATED)
- ✅ Sees 5-6 academic modules only
- ✅ Can mark attendance, enter exam marks
- ❌ **Cannot access Transport** (redirected)
- ❌ **Cannot access Library** (redirected)
- ❌ Cannot access fees, accounts, staff
- ✅ Sidebar does NOT show transport or library

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
