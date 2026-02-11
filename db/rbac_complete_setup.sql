-- ============================================================================
-- COMPLETE RBAC SETUP FOR SCHOOL ERP (5 ROLES)
-- ============================================================================
-- This file contains EVERYTHING needed to set up the complete RBAC system
-- Run this ONCE to create all tables, permissions, and role assignments
-- 
-- WARNING: This will drop existing RBAC tables if they exist!
-- ============================================================================

-- Drop existing RBAC tables (if migrating from old system)
-- DROP TABLE IF EXISTS `role_permissions`;
-- DROP TABLE IF EXISTS `permissions`;

-- ============================================================================
-- STEP 1: CREATE RBAC TABLES
-- ============================================================================

-- Create permissions table
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL COMMENT 'Module name (e.g., admission, fees, exam)',
  `action` varchar(50) NOT NULL COMMENT 'Action type (view, add, edit, delete)',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_permission` (`module`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create role_permissions mapping table with ALL 5 ROLES
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role` enum('Admin','Office Manager','Librarian','Teacher','Student') NOT NULL,
  `permission_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role`, `permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- STEP 2: UPDATE USERS TABLE TO SUPPORT 5 ROLES
-- ============================================================================

-- Update users table to support all 5 roles
ALTER TABLE `users` 
MODIFY COLUMN `role` enum('Admin','Office Manager','Librarian','Teacher','Student') NOT NULL;

-- ============================================================================
-- STEP 3: INSERT ALL PERMISSIONS
-- ============================================================================

-- Dashboard Permission
INSERT INTO `permissions` (`module`, `action`, `description`) VALUES
('dashboard', 'view', 'Access dashboard');

-- Admission Module Permissions
INSERT INTO `permissions` (`module`, `action`, `description`) VALUES
('admission', 'view', 'View admission records'),
('admission', 'add', 'Add new admission'),
('admission', 'edit', 'Edit admission records'),
('admission', 'delete', 'Delete admission records');

-- Student Module Permissions
INSERT INTO `permissions` (`module`, `action`, `description`) VALUES
('student', 'view', 'View student details'),
('student', 'edit', 'Edit student details'),
('student', 'delete', 'Delete student records'),
('student', 'tc', 'Manage transfer certificates');

-- Academic Settings Permissions
INSERT INTO `permissions` (`module`, `action`, `description`) VALUES
('school_setting', 'view', 'View school settings'),
('school_setting', 'edit', 'Edit school settings'),
('class', 'view', 'View classes'),
('class', 'add', 'Add new class'),
('class', 'edit', 'Edit class details'),
('class', 'delete', 'Delete class'),
('section', 'view', 'View sections'),
('section', 'add', 'Add new section'),
('section', 'edit', 'Edit section details'),
('section', 'delete', 'Delete section'),
('stream', 'view', 'View streams'),
('stream', 'add', 'Add new stream'),
('stream', 'edit', 'Edit stream details'),
('stream', 'delete', 'Delete stream'),
('subject', 'view', 'View subjects'),
('subject', 'add', 'Add new subject'),
('subject', 'edit', 'Edit subject details'),
('subject', 'delete', 'Delete subject'),
('allocation', 'view', 'View allocations'),
('allocation', 'add', 'Add allocations'),
('allocation', 'edit', 'Edit allocations'),
('allocation', 'delete', 'Delete allocations');

-- Fees Module Permissions
INSERT INTO `permissions` (`module`, `action`, `description`) VALUES
('fees', 'view', 'View fees records'),
('fees', 'add', 'Add fees entry'),
('fees', 'edit', 'Edit fees records'),
('fees', 'delete', 'Delete fees records'),
('fees', 'receipt', 'Generate fees receipt');

-- Account Module Permissions
INSERT INTO `permissions` (`module`, `action`, `description`) VALUES
('account', 'view', 'View account reports'),
('account', 'add', 'Add income/expense'),
('account', 'edit', 'Edit account records'),
('account', 'delete', 'Delete account records');

-- Exam Module Permissions
INSERT INTO `permissions` (`module`, `action`, `description`) VALUES
('exam', 'view', 'View exam settings and results'),
('exam', 'add', 'Add exam marks'),
('exam', 'edit', 'Edit exam marks and settings'),
('exam', 'delete', 'Delete exam records'),
('exam', 'result', 'View and generate results');

-- Transport Module Permissions
INSERT INTO `permissions` (`module`, `action`, `description`) VALUES
('transport', 'view', 'View transport records'),
('transport', 'add', 'Add transport details'),
('transport', 'edit', 'Edit transport records'),
('transport', 'delete', 'Delete transport records');

-- Library Module Permissions
INSERT INTO `permissions` (`module`, `action`, `description`) VALUES
('library', 'view', 'View library records'),
('library', 'add', 'Add books and issue books'),
('library', 'edit', 'Edit library records'),
('library', 'delete', 'Delete library records'),
('library', 'return', 'Return books');

-- Staff Module Permissions
INSERT INTO `permissions` (`module`, `action`, `description`) VALUES
('staff', 'view', 'View staff records'),
('staff', 'add', 'Add new staff'),
('staff', 'edit', 'Edit staff records'),
('staff', 'delete', 'Delete staff records');

-- Attendance Module Permissions
INSERT INTO `permissions` (`module`, `action`, `description`) VALUES
('attendance', 'view', 'View attendance records'),
('attendance', 'add', 'Mark attendance'),
('attendance', 'edit', 'Edit attendance records');

-- ============================================================================
-- STEP 4: ASSIGN PERMISSIONS TO ROLES
-- ============================================================================

-- ----------------------------------------------------------------------------
-- ADMIN ROLE: Gets ALL permissions
-- ----------------------------------------------------------------------------
INSERT INTO `role_permissions` (`role`, `permission_id`)
SELECT 'Admin', id FROM permissions;

-- ----------------------------------------------------------------------------
-- OFFICE MANAGER ROLE: Transport, Fees, Accounts, Operations
-- ----------------------------------------------------------------------------
INSERT INTO `role_permissions` (`role`, `permission_id`)
SELECT 'Office Manager', id FROM permissions WHERE 
  -- Dashboard access
  (module = 'dashboard' AND action = 'view')
  OR
  -- View students (for transport/fees operations)
  (module = 'student' AND action = 'view')
  OR
  -- View classes (for transport/fees operations)
  (module IN ('class', 'section') AND action = 'view')
  OR
  -- Full transport management
  (module = 'transport' AND action IN ('view', 'add', 'edit', 'delete'))
  OR
  -- Full fees management
  (module = 'fees' AND action IN ('view', 'add', 'edit', 'delete', 'receipt'))
  OR
  -- Full account management
  (module = 'account' AND action IN ('view', 'add', 'edit', 'delete'))
  OR
  -- View allocations
  (module = 'allocation' AND action = 'view');

-- ----------------------------------------------------------------------------
-- LIBRARIAN ROLE: Library operations only
-- ----------------------------------------------------------------------------
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

-- ----------------------------------------------------------------------------
-- TEACHER ROLE: Academic operations only (NO transport, NO library)
-- ----------------------------------------------------------------------------
INSERT INTO `role_permissions` (`role`, `permission_id`)
SELECT 'Teacher', id FROM permissions WHERE 
  -- Dashboard access
  (module = 'dashboard' AND action = 'view')
  OR
  -- View academic structure
  (module IN ('student', 'class', 'section', 'stream', 'subject', 'allocation') AND action = 'view')
  OR
  -- Full exam management
  (module = 'exam' AND action IN ('view', 'add', 'edit', 'result'))
  OR
  -- Full attendance management
  (module = 'attendance' AND action IN ('view', 'add', 'edit'));

-- ----------------------------------------------------------------------------
-- STUDENT ROLE: View personal records only
-- ----------------------------------------------------------------------------
INSERT INTO `role_permissions` (`role`, `permission_id`)
SELECT 'Student', id FROM permissions WHERE 
  -- Dashboard access
  (module = 'dashboard' AND action = 'view')
  OR
  -- View own fees
  (module = 'fees' AND action = 'view')
  OR
  -- View exam results
  (module = 'exam' AND action IN ('view', 'result'))
  OR
  -- View library status
  (module = 'library' AND action = 'view');

-- ============================================================================
-- STEP 5: CREATE DEFAULT ADMIN USER (if not exists)
-- ============================================================================

-- Create default admin user (password: Admin@123)
-- Password hash for 'Admin@123'
INSERT INTO `users` (`user_id`, `password`, `role`, `full_name`, `contact_no`, `created_at`) 
VALUES (
    'admin',
    '$2y$10$3euPcmQFCiblsZeEu8s8KOBLbq5Ij0Y1I4Nt/jQQmJ1Jx6qGQXYqy',
    'Admin',
    'System Administrator',
    '0000000000',
    NOW()
) ON DUPLICATE KEY UPDATE 
    role = 'Admin',
    password = '$2y$10$3euPcmQFCiblsZeEu8s8KOBLbq5Ij0Y1I4Nt/jQQmJ1Jx6qGQXYqy';

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Show count of permissions per role
SELECT 
    rp.role,
    COUNT(DISTINCT p.module) as module_count,
    COUNT(*) as total_permissions
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

-- Show total permissions created
SELECT COUNT(*) as total_permissions FROM permissions;

-- Show all roles in users table enum
SHOW COLUMNS FROM users LIKE 'role';

-- ============================================================================
-- SETUP COMPLETE!
-- ============================================================================
-- 
-- Next Steps:
-- 1. Login with: admin / Admin@123
-- 2. Go to User Management to create users for each role
-- 3. Test each role's access
-- 
-- Test Users (Optional - run test_users.sql to create):
-- - office1 / Test@123 (Office Manager)
-- - librarian1 / Test@123 (Librarian)
-- - teacher1 / Test@123 (Teacher)
-- - student1 / Test@123 (Student)
-- 
-- ============================================================================
