<aside class="app-sidebar">
    <nav class="nav flex-column gap-1">
        <a class="nav-link" href="dashboard.php">Dashboard</a>

        <?php if(has_access('admission', 'view')): ?>
            <div class="nav-section">Admissions</div>
            <a class="nav-link" href="admission.php">Admission</a>
            <a class="nav-link" href="student_detail.php">Student Details</a>
            <a class="nav-link" href="student_tc.php">Transfer Certificates</a>
        <?php endif; ?>

        <?php if(has_access('class', 'view')): ?>
            <div class="nav-section">Academics</div>
            <a class="nav-link" href="school_setting.php">School Settings</a>
            <a class="nav-link" href="school_detail.php">School Details</a>
            <a class="nav-link" href="class.php">Classes</a>
            <a class="nav-link" href="section.php">Sections</a>
            <a class="nav-link" href="stream.php">Streams</a>
            <a class="nav-link" href="subject.php">Subjects</a>
            <a class="nav-link" href="allocate_section.php">Allocate Sections</a>
            <a class="nav-link" href="allocate_stream.php">Allocate Streams</a>
            <a class="nav-link" href="allocate_subject.php">Allocate Subjects</a>
        <?php endif; ?>

        <?php if(has_access('fees', 'view')): ?>
            <div class="nav-section">Fees & Accounts</div>
            <a class="nav-link" href="fees_setting.php">Fees Settings</a>
            <a class="nav-link" href="fees_manager.php">Fees Manager</a>
            <a class="nav-link" href="account_setting.php">Account Settings</a>
            <a class="nav-link" href="account_report.php">Account Reports</a>
        <?php endif; ?>

        <?php if(has_access('exam', 'view')): ?>
            <div class="nav-section">Examinations</div>
            <a class="nav-link" href="exam_setting.php">Exam Settings</a>
            <a class="nav-link" href="exam_result.php">Results</a>
        <?php endif; ?>

        <div class="nav-section">Operations</div>
        
        <?php if(has_access('transport', 'view')): ?>
            <a class="nav-link" href="transport_setting.php">Transport</a>
        <?php endif; ?>

        <?php if(has_access('library', 'view')): ?>
            <a class="nav-link" href="library_setting.php">Library</a>
        <?php endif; ?>

        <?php if(has_access('staff', 'view')): ?>
            <a class="nav-link" href="staff_setting.php">Staff</a>
        <?php endif; ?>

        <?php if(has_access('attendance', 'view')): ?>
            <a class="nav-link" href="Attendance.php">Attendance</a>
        <?php endif; ?>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
            <div class="nav-section">System</div>
            <a class="nav-link" href="user_manager.php" style="color: var(--fluent-azure); font-weight: 600;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px; vertical-align: middle;">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                User Manager
            </a>
        <?php endif; ?>
    </nav>
</aside>
<div class="app-content">
