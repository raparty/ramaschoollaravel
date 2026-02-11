-- ============================================================================
-- Test Users Setup for RBAC Testing
-- ============================================================================
-- This script creates test users for each role to test RBAC functionality
-- 
-- IMPORTANT: Run rbac_schema.sql FIRST before running this script
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
-- Test User Credentials
-- ============================================================================
-- 
-- Admin:
--   Username: admin
--   Password: Test@123
--   Role: Admin
--   Access: Full system access, all modules
-- 
-- Teacher:
--   Username: teacher1
--   Password: Test@123
--   Role: Teacher
--   Access: Academic modules (students, exams, library, attendance)
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
WHERE user_id IN ('admin', 'teacher1', 'student1')
ORDER BY 
    CASE role 
        WHEN 'Admin' THEN 1 
        WHEN 'Teacher' THEN 2 
        WHEN 'Student' THEN 3 
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
