# Complete RBAC Setup Guide - Single SQL Installation

## 🎯 Quick Setup (3 Steps)

This is the **EASIEST** way to set up the complete RBAC system with all 5 roles.

### Step 1: Run the Complete Setup SQL

Run this **ONE** SQL file to set up everything:

```bash
mysql -u your_username -p your_database < db/rbac_complete_setup.sql
```

**What this does:**
- ✅ Creates `permissions` table
- ✅ Creates `role_permissions` table with ALL 5 roles
- ✅ Updates `users` table to support 5 roles
- ✅ Inserts all 50+ permissions
- ✅ Assigns permissions to all 5 roles
- ✅ Creates default admin user (admin / Admin@123)

### Step 2: Login as Admin

```
URL: http://your-school-erp/
Username: admin
Password: Admin@123
```

### Step 3: Create Users

1. Go to **User Management** from the dashboard
2. Click "Add New User"
3. Fill in the form:
   - Username (login ID)
   - Password
   - Full Name
   - Contact Number
   - Select Role (Admin, Office Manager, Librarian, Teacher, Student)
4. Click "Add User"

**Done!** You now have a complete RBAC system with 5 roles.

---

## 📊 The 5 Roles Explained

### 👑 Admin
- **Access:** Everything
- **Use For:** School administrators, principals, IT staff
- **Can Do:** Manage all modules, create users, view all data

### 💼 Office Manager
- **Access:** Transport, Fees, Accounts
- **Use For:** Office staff, fee collectors, administrative assistants
- **Can Do:** Manage transport routes/vehicles, collect fees, manage income/expenses

### 📚 Librarian
- **Access:** Library only
- **Use For:** Library staff
- **Can Do:** Manage books, issue/return books, manage fines

### 👨‍🏫 Teacher
- **Access:** Academic only (Exams, Attendance)
- **Use For:** Teaching staff
- **Can Do:** Enter exam marks, mark attendance, view students
- **Cannot Do:** Transport, Library, Fees, Accounts

### 🎓 Student
- **Access:** View personal records only
- **Use For:** Students and parents
- **Can Do:** View own exam results, fees, library status

---

## 🔑 Test Users (Optional)

If you want ready-made test users, run:

```bash
mysql -u your_username -p your_database < db/test_users.sql
```

This creates:
- `admin / Test@123` - Admin
- `office1 / Test@123` - Office Manager
- `librarian1 / Test@123` - Librarian
- `teacher1 / Test@123` - Teacher
- `student1 / Test@123` - Student

---

## 📋 What SQL Gets Executed

The `rbac_complete_setup.sql` file contains:

```sql
-- 1. Create RBAC tables
CREATE TABLE permissions (...)
CREATE TABLE role_permissions (...)

-- 2. Update users table for 5 roles
ALTER TABLE users MODIFY role enum('Admin','Office Manager','Librarian','Teacher','Student')

-- 3. Insert 50+ permissions
INSERT INTO permissions (module, action, description) VALUES
  ('dashboard', 'view', 'Access dashboard'),
  ('admission', 'view', 'View admission records'),
  ('admission', 'add', 'Add new admission'),
  -- ... 50+ more permissions

-- 4. Assign permissions to roles
-- Admin gets all permissions
INSERT INTO role_permissions SELECT 'Admin', id FROM permissions;

-- Office Manager gets transport, fees, accounts
INSERT INTO role_permissions SELECT 'Office Manager', id FROM permissions 
WHERE module IN ('transport', 'fees', 'account') ...

-- Librarian gets library only
INSERT INTO role_permissions SELECT 'Librarian', id FROM permissions 
WHERE module = 'library' ...

-- Teacher gets exams and attendance only
INSERT INTO role_permissions SELECT 'Teacher', id FROM permissions 
WHERE module IN ('exam', 'attendance') ...

-- Student gets view-only access
INSERT INTO role_permissions SELECT 'Student', id FROM permissions 
WHERE module IN ('dashboard', 'fees', 'exam', 'library') AND action = 'view' ...

-- 5. Create default admin user
INSERT INTO users (user_id, password, role, ...) 
VALUES ('admin', 'hashed_password', 'Admin', ...)
```

---

## 🆚 Alternative: Two-Step Migration (Old Method)

If you already ran the old migration, you can update:

```bash
# If you already have rbac_schema.sql (3 roles)
mysql -u your_username -p your_database < db/rbac_schema_v2.sql
```

But it's **easier to just run** `rbac_complete_setup.sql` instead!

---

## ✅ Verification

After running the setup, verify it worked:

### Check Tables Exist
```sql
SHOW TABLES LIKE '%permission%';
-- Should show: permissions, role_permissions
```

### Check Roles
```sql
SHOW COLUMNS FROM users LIKE 'role';
-- Should show: enum('Admin','Office Manager','Librarian','Teacher','Student')
```

### Check Permissions Count
```sql
SELECT COUNT(*) as total_permissions FROM permissions;
-- Should show: ~50+ permissions

SELECT role, COUNT(*) as permission_count 
FROM role_permissions 
GROUP BY role;
-- Admin: ~50+, Office Manager: ~15, Librarian: ~7, Teacher: ~12, Student: ~5
```

### Check Admin User Exists
```sql
SELECT user_id, role FROM users WHERE user_id = 'admin';
-- Should show: admin | Admin
```

---

## 🚀 Usage Examples

### Create an Office Manager
1. Login as admin
2. Go to User Management
3. Add User:
   - Username: `office_user`
   - Password: `SecurePass123`
   - Role: Office Manager
   - Full Name: John Doe
4. Login as `office_user` → Can access Transport, Fees, Accounts
5. Try accessing Exams → Gets "Access Denied"

### Create a Librarian
1. Login as admin
2. Go to User Management
3. Add User:
   - Username: `lib_user`
   - Password: `SecurePass123`
   - Role: Librarian
   - Full Name: Jane Smith
4. Login as `lib_user` → Can access Library only
5. Try accessing Transport → Gets "Access Denied"

### Create a Teacher
1. Login as admin
2. Go to User Management
3. Add User:
   - Username: `teacher_user`
   - Password: `SecurePass123`
   - Role: Teacher
   - Full Name: Bob Wilson
4. Login as `teacher_user` → Can access Exams, Attendance
5. Try accessing Transport or Library → Gets "Access Denied"

---

## 🔧 Troubleshooting

### Error: Table already exists
The script uses `CREATE TABLE IF NOT EXISTS`, so it should be safe.
If you get errors, drop the tables first:
```sql
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
```
Then run the setup again.

### Error: Duplicate entry
The script uses `INSERT ... ON DUPLICATE KEY UPDATE` for the admin user.
If you get other duplicate errors, the permissions may already exist.
Clear and re-run:
```sql
TRUNCATE TABLE role_permissions;
TRUNCATE TABLE permissions;
```
Then run the setup again.

### Can't login as admin
Default credentials:
- Username: `admin`
- Password: `Admin@123`

If this doesn't work, reset the password:
```sql
UPDATE users 
SET password = '$2y$10$3euPcmQFCiblsZeEu8s8KOBLbq5Ij0Y1I4Nt/jQQmJ1Jx6qGQXYqy'
WHERE user_id = 'admin';
```

---

## 📖 Additional Resources

- **User Management UI:** `user_management.php` - Create/edit users
- **RBAC Management UI:** `rbac_management.php` - View permissions matrix
- **Visual Diagram:** `rbac_diagram_v2.html` - See all 5 roles visually
- **Full Documentation:** `docs/RBAC_DOCUMENTATION.md` - Technical details

---

## 🎉 Summary

**ONE SQL file to rule them all!**

```bash
mysql -u user -p database < db/rbac_complete_setup.sql
```

That's it! You now have:
- ✅ 5 specialized roles
- ✅ 50+ permissions
- ✅ Complete role-permission mapping
- ✅ Admin user ready to use
- ✅ User management interface
- ✅ Full RBAC system

**Login as admin and start creating users!** 🚀
