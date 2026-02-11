-- ============================================================================
-- Test Users Setup for RBAC Testing (v2 - 5 Roles)
-- ============================================================================
-- This script creates test users for each role to test RBAC functionality
-- 
-- IMPORTANT: Run rbac_schema.sql and rbac_schema_v2.sql FIRST
-- ============================================================================

-- Create test users with different roles
-- Default password for all test users: 'Test@123' 

-- Admin User
INSERT INTO `users` (`user_id`, `password`, `role`, `full_name`, `contact_no`, `created_at`) 
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: Test@123
    'Admin',
    'System Administrator',
    '9876543210',
    NOW()
) ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role = 'Admin';

-- Office Manager User
INSERT INTO `users` (`user_id`, `password`, `role`, `full_name`, `contact_no`, `created_at`) 
VALUES (
    'office1',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: Test@123
    'Office Manager',
    'Robert Brown',
    '9876543213',
    NOW()
) ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role = 'Office Manager';

-- Librarian User
INSERT INTO `users` (`user_id`, `password`, `role`, `full_name`, `contact_no`, `created_at`) 
VALUES (
    'librarian1',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: Test@123
    'Librarian',
    'Sarah Williams',
    '9876543214',
    NOW()
) ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role = 'Librarian';

-- Teacher User
INSERT INTO `users` (`user_id`, `password`, `role`, `full_name`, `contact_no`, `created_at`) 
VALUES (
    'teacher1',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: Test@123
    'Teacher',
    'John Smith',
    '9876543211',
    NOW()
) ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role = 'Teacher';

-- Student User
INSERT INTO `users` (`user_id`, `password`, `role`, `full_name`, `contact_no`, `created_at`) 
VALUES (
    'student1',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: Test@123
    'Student',
    'Alice Johnson',
    '9876543212',
    NOW()
) ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role = 'Student';

-- ============================================================================
-- Test User Credentials (v2 - 5 Roles)
-- ============================================================================
-- 
-- Admin:
--   Username: admin
--   Password: Test@123
--   Role: Admin
--   Access: Full system access, all modules
-- 
-- Office Manager:
--   Username: office1
--   Password: Test@123
--   Role: Office Manager
--   Access: Transport, fees, accounts, student view
-- 
-- Librarian:
--   Username: librarian1
--   Password: Test@123
--   Role: Librarian
--   Access: Library management, student view
-- 
-- Teacher:
--   Username: teacher1
--   Password: Test@123
--   Role: Teacher
--   Access: Academic only (exams, attendance, view students/classes)
--   NO transport or library management
-- 
-- Student:
--   Username: student1
--   Password: Test@123
--   Role: Student
--   Access: View personal records only (dashboard, exam results, fees)
-- 
-- ============================================================================

-- Verify users were created
SELECT 
    user_id,
    role,
    full_name,
    contact_no,
    created_at
FROM users 
WHERE user_id IN ('admin', 'office1', 'librarian1', 'teacher1', 'student1')
ORDER BY 
    CASE role 
        WHEN 'Admin' THEN 1 
        WHEN 'Office Manager' THEN 2 
        WHEN 'Librarian' THEN 3
        WHEN 'Teacher' THEN 4 
        WHEN 'Student' THEN 5 
    END;

-- ============================================================================
-- Password Generation Note
-- ============================================================================
-- To generate a new password hash for testing, use PHP:
-- 
-- php -r "echo password_hash('YourPassword', PASSWORD_DEFAULT) . PHP_EOL;"
-- 
-- Then update the password field in the users table with the generated hash.
-- ============================================================================
