-- =====================================================
-- SQL CREATE TABLE statements for missing Laravel models
-- =====================================================
-- This file contains SQL to create tables for Laravel models
-- that don't have corresponding tables in the database.
--
-- Execute these statements to enable full functionality of:
-- - Exam management
-- - Result compilation and publication
-- - Staff salary management
-- - Grade/GPA system
--
-- Note: Ensure you have proper backup before executing.
-- =====================================================

-- =====================================================
-- 1. EXAMS TABLE
-- =====================================================
-- Stores examination schedules and details
-- Used by: Exam model (app/Models/Exam.php)
-- =====================================================

CREATE TABLE IF NOT EXISTS `exams` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT 'Exam name (Midterm, Final, Unit Test 1, etc.)',
  `class_id` INT NOT NULL COMMENT 'Related class ID from classes table',
  `session` VARCHAR(50) NOT NULL COMMENT 'Academic session (e.g., 2023-2024)',
  `start_date` DATE NOT NULL COMMENT 'Exam start date',
  `end_date` DATE NOT NULL COMMENT 'Exam end date',
  `total_marks` INT NOT NULL DEFAULT 100 COMMENT 'Total maximum marks for the exam',
  `pass_marks` INT NOT NULL DEFAULT 40 COMMENT 'Minimum pass marks',
  `is_published` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Whether results are published (0=No, 1=Yes)',
  `description` TEXT NULL COMMENT 'Exam description or instructions',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  INDEX `idx_class_id` (`class_id`),
  INDEX `idx_session` (`session`),
  INDEX `idx_is_published` (`is_published`),
  INDEX `idx_dates` (`start_date`, `end_date`),
  CONSTRAINT `fk_exams_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Examination schedules and details';

-- =====================================================
-- 2. EXAM_SUBJECTS TABLE
-- =====================================================
-- Stores subjects assigned to each exam with marks configuration
-- Used by: ExamSubject model (app/Models/ExamSubject.php)
-- =====================================================

CREATE TABLE IF NOT EXISTS `exam_subjects` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `exam_id` INT NOT NULL COMMENT 'Related exam ID',
  `subject_id` INT NOT NULL COMMENT 'Related subject ID from subjects table',
  `theory_marks` INT NOT NULL DEFAULT 70 COMMENT 'Maximum theory marks',
  `practical_marks` INT NOT NULL DEFAULT 30 COMMENT 'Maximum practical marks',
  `pass_marks` INT NOT NULL DEFAULT 40 COMMENT 'Minimum pass marks for this subject',
  `exam_date` DATE NULL COMMENT 'Specific exam date for this subject',
  `exam_time` TIME NULL COMMENT 'Exam start time',
  `duration_minutes` INT NULL DEFAULT 180 COMMENT 'Exam duration in minutes',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_exam_subject` (`exam_id`, `subject_id`),
  INDEX `idx_exam_id` (`exam_id`),
  INDEX `idx_subject_id` (`subject_id`),
  INDEX `idx_exam_date` (`exam_date`),
  CONSTRAINT `fk_exam_subjects_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_exam_subjects_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Subjects assigned to exams with marks configuration';

-- =====================================================
-- 3. RESULTS TABLE
-- =====================================================
-- Stores compiled results of students for exams
-- Used by: Result model (app/Models/Result.php)
-- =====================================================

CREATE TABLE IF NOT EXISTS `results` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL COMMENT 'Related student ID from admissions table',
  `exam_id` INT NOT NULL COMMENT 'Related exam ID',
  `total_marks_obtained` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total marks obtained by student',
  `total_max_marks` INT NOT NULL COMMENT 'Total maximum marks for the exam',
  `percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Percentage obtained',
  `grade` VARCHAR(10) NULL COMMENT 'Grade (A+, A, B+, B, C, D, F)',
  `rank` INT NULL COMMENT 'Rank in class/exam',
  `is_passed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Whether student passed (0=No, 1=Yes)',
  `is_published` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Whether result is published (0=No, 1=Yes)',
  `remarks` TEXT NULL COMMENT 'Overall remarks or comments',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_exam` (`student_id`, `exam_id`),
  INDEX `idx_exam_id` (`exam_id`),
  INDEX `idx_student_id` (`student_id`),
  INDEX `idx_is_published` (`is_published`),
  INDEX `idx_is_passed` (`is_passed`),
  INDEX `idx_percentage` (`percentage`),
  CONSTRAINT `fk_results_student` FOREIGN KEY (`student_id`) REFERENCES `admissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_results_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Compiled exam results for students';

-- =====================================================
-- 4. STAFF_SALARIES TABLE
-- =====================================================
-- Stores staff salary records and payment information
-- Used by: Salary model (app/Models/Salary.php)
-- =====================================================

CREATE TABLE IF NOT EXISTS `staff_salaries` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `staff_id` INT NOT NULL COMMENT 'Related staff ID from staff_employee table',
  `month` TINYINT NOT NULL COMMENT 'Month (1-12)',
  `year` YEAR NOT NULL COMMENT 'Year (e.g., 2024)',
  `basic_salary` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Basic salary amount',
  `allowances` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total allowances (HRA, DA, etc.)',
  `deductions` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total deductions (PF, tax, etc.)',
  `net_salary` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Net salary after allowances and deductions',
  `status` ENUM('pending', 'paid') NOT NULL DEFAULT 'pending' COMMENT 'Payment status',
  `payment_date` DATE NULL COMMENT 'Date when salary was paid',
  `payment_method` VARCHAR(50) NULL COMMENT 'Payment method (Cash, Bank Transfer, Cheque)',
  `notes` TEXT NULL COMMENT 'Additional notes or remarks',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_staff_month_year` (`staff_id`, `month`, `year`),
  INDEX `idx_staff_id` (`staff_id`),
  INDEX `idx_month_year` (`month`, `year`),
  INDEX `idx_status` (`status`),
  INDEX `idx_payment_date` (`payment_date`),
  CONSTRAINT `fk_salaries_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff_employee` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Staff salary records and payments';

-- =====================================================
-- 5. GRADES TABLE
-- =====================================================
-- Stores grading system configuration (A+, A, B, etc.)
-- Used by: Grade model (app/Models/Grade.php)
-- =====================================================

CREATE TABLE IF NOT EXISTS `grades` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `grade` VARCHAR(10) NOT NULL COMMENT 'Grade name (A+, A, B+, B, C, D, F)',
  `min_percentage` DECIMAL(5,2) NOT NULL COMMENT 'Minimum percentage for this grade',
  `max_percentage` DECIMAL(5,2) NOT NULL COMMENT 'Maximum percentage for this grade',
  `points` DECIMAL(3,2) NOT NULL DEFAULT 0.00 COMMENT 'Grade points (for GPA calculation)',
  `description` TEXT NULL COMMENT 'Grade description (Excellent, Good, etc.)',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_grade` (`grade`),
  INDEX `idx_percentage_range` (`min_percentage`, `max_percentage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Grading system configuration';

-- =====================================================
-- SAMPLE DATA FOR GRADES TABLE
-- =====================================================
-- Insert standard grading system
-- You can modify these according to your institution's requirements
-- =====================================================

INSERT INTO `grades` (`grade`, `min_percentage`, `max_percentage`, `points`, `description`) VALUES
('A+', 90.00, 100.00, 4.00, 'Outstanding'),
('A', 80.00, 89.99, 3.70, 'Excellent'),
('B+', 70.00, 79.99, 3.30, 'Very Good'),
('B', 60.00, 69.99, 3.00, 'Good'),
('C+', 50.00, 59.99, 2.70, 'Above Average'),
('C', 40.00, 49.99, 2.00, 'Average'),
('D', 33.00, 39.99, 1.00, 'Pass'),
('F', 0.00, 32.99, 0.00, 'Fail')
ON DUPLICATE KEY UPDATE 
  `min_percentage` = VALUES(`min_percentage`),
  `max_percentage` = VALUES(`max_percentage`),
  `points` = VALUES(`points`),
  `description` = VALUES(`description`);

-- =====================================================
-- END OF SQL STATEMENTS
-- =====================================================
-- After executing these statements, all Laravel models
-- will have their corresponding database tables.
--
-- Remember to:
-- 1. Backup your database before executing
-- 2. Adjust foreign key references if your table names differ
-- 3. Modify grade ranges according to your institution's policy
-- 4. Update any existing code that may be referencing old structures
-- =====================================================
