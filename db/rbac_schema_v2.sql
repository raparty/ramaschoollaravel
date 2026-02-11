-- ============================================================================
-- RBAC (Role-Based Access Control) Schema v2 for School ERP
-- ============================================================================
-- This migration adds Office Manager and Librarian roles
-- Updates existing roles to remove burden from Teachers
--
-- MIGRATION: Run this AFTER rbac_schema.sql
-- ============================================================================

-- Step 1: Update users table to support new roles
ALTER TABLE `users` 
MODIFY COLUMN `role` enum('Admin','Office Manager','Librarian','Teacher','Student') NOT NULL;

-- Step 2: Update role_permissions table to support new roles
ALTER TABLE `role_permissions` 
MODIFY COLUMN `role` enum('Admin','Office Manager','Librarian','Teacher','Student') NOT NULL;

-- Step 3: Remove existing Teacher permissions that will be reassigned
DELETE FROM `role_permissions` 
WHERE role = 'Teacher' 
AND permission_id IN (
    SELECT id FROM permissions 
    WHERE module IN ('transport', 'library')
);

-- ============================================================================
-- Office Manager Permissions
-- ============================================================================
-- Office Manager handles: Transport, Fees, Accounts, General Operations
-- Cannot access: Academic content (exams, student academic records)

INSERT INTO `role_permissions` (`role`, `permission_id`)
SELECT 'Office Manager', id FROM permissions WHERE 
  -- View permissions
  (module IN ('dashboard', 'student', 'class', 'section', 'transport', 'fees', 'account') AND action = 'view')
  OR 
  -- Full transport management
  (module = 'transport' AND action IN ('add', 'edit', 'delete'))
  OR
  -- Fees management
  (module = 'fees' AND action IN ('add', 'edit', 'delete', 'receipt'))
  OR
  -- Account management
  (module = 'account' AND action IN ('add', 'edit', 'delete'))
  OR
  -- Can view but not modify allocations
  (module = 'allocation' AND action = 'view');

-- ============================================================================
-- Librarian Permissions
-- ============================================================================
-- Librarian handles: Library only
-- Has full control over library operations

INSERT INTO `role_permissions` (`role`, `permission_id`)
SELECT 'Librarian', id FROM permissions WHERE 
  -- Dashboard access
  (module = 'dashboard' AND action = 'view')
  OR
  -- View students (to issue books)
  (module = 'student' AND action = 'view')
  OR
  -- Full library management
  (module = 'library' AND action IN ('view', 'add', 'edit', 'delete', 'return'));

-- ============================================================================
-- Updated Teacher Permissions (Focused on Academics)
-- ============================================================================
-- Teachers now focus ONLY on academic operations:
-- - View students, classes, subjects
-- - Manage exams and marks
-- - Mark attendance
-- NO transport or library management

-- First, clear all existing Teacher permissions
DELETE FROM `role_permissions` WHERE role = 'Teacher';

-- Re-add focused Teacher permissions
INSERT INTO `role_permissions` (`role`, `permission_id`)
SELECT 'Teacher', id FROM permissions WHERE 
  -- Dashboard access
  (module = 'dashboard' AND action = 'view')
  OR
  -- View academic structure
  (module IN ('student', 'class', 'section', 'stream', 'subject', 'allocation') AND action = 'view')
  OR
  -- Exam management (full control)
  (module = 'exam' AND action IN ('view', 'add', 'edit', 'result'))
  OR
  -- Attendance management
  (module = 'attendance' AND action IN ('view', 'add', 'edit'));

-- ============================================================================
-- Summary of New Role Structure
-- ============================================================================
-- 
-- ADMIN:
--   - Full system access (unchanged)
-- 
-- OFFICE MANAGER:
--   - Transport: Full CRUD
--   - Fees: Full management including receipts
--   - Accounts: Income/expense management
--   - Students: View only (for transport assignment)
--   - Dashboard access
-- 
-- LIBRARIAN:
--   - Library: Full CRUD + book issue/return
--   - Students: View only (for book issue)
--   - Dashboard access
-- 
-- TEACHER (UPDATED):
--   - Students: View only
--   - Classes/Sections/Subjects: View only
--   - Exams: Full management (marks entry, results)
--   - Attendance: Full management
--   - NO transport management
--   - NO library management
-- 
-- STUDENT:
--   - Personal records: View only (unchanged)
-- 
-- ============================================================================

-- Verification Query: Check permissions per role
SELECT 
    rp.role,
    COUNT(DISTINCT p.module) as module_count,
    COUNT(*) as total_permissions,
    GROUP_CONCAT(DISTINCT p.module ORDER BY p.module SEPARATOR ', ') as modules
FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
GROUP BY rp.role
ORDER BY 
    CASE rp.role 
        WHEN 'Admin' THEN 1 
        WHEN 'Office Manager' THEN 2 
        WHEN 'Librarian' THEN 3
        WHEN 'Teacher' THEN 4 
        WHEN 'Student' THEN 5 
    END;
