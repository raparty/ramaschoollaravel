<?php
declare(strict_types=1);

/**
 * ID 1.5: Student Transfer Certificate (TC) Hub
 * Integrated with reset logic for "Change Student" functionality.
 */
require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

$conn = Database::connection();
$student = null;
$error_msg = "";

// 1. Capture Registration Number
// We use $_GET to ensure a clean refresh when the user clicks 'Change Student'
$reg_no = $_GET['registration_no'] ?? '';

// 2. Data Retrieval Logic
if (!empty($reg_no)) {
    $safe_reg = mysqli_real_escape_string($conn, $reg_no);
    $sql = "SELECT a.*, c.class_name 
            FROM admissions a 
            LEFT JOIN classes c ON a.class_id = c.id 
            WHERE a.reg_no = '$safe_reg' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $student = mysqli_fetch_assoc($result);

    if (!$student) {
        $error_msg = "No student found with Registration No: " . htmlspecialchars($reg_no);
    }
}
?>

<div class="grid_container">
    <div class="page_title" style="margin-bottom: 25px;">
        <h3 style="font-weight:300; color:var(--fluent-slate); font-size: 26px;">Transfer Certificate (TC) Module</h3>
    </div>

    <?php if (!$student): ?>
    
    <div class="azure-card" style="padding: 50px; text-align: center; border: 1px solid var(--app-border);">
        <div style="max-width: 500px; margin: 0 auto;">
            <div style="background: #eff6fc; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <svg viewBox="0 0 24 24" width="40" fill="var(--app-primary)"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            </div>
            <h4 style="margin-bottom: 10px; color: var(--fluent-slate);">Initiate Transfer Certificate</h4>
            <p style="color: #64748b; margin-bottom: 30px;">Enter a Registration Number to load records.</p>
            
            <form action="student_tc.php" method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="registration_no" class="form-control fluent-input" placeholder="e.g. ADM-2026-001" required style="flex: 1; padding: 12px;">
                <button type="submit" class="btn-fluent-primary" style="padding: 0 25px;">Load Records</button>
            </form>
            
            <?php if ($error_msg): ?>
                <div style="margin-top: 20px; color: #e53e3e; font-size: 13px; font-weight: 600; padding: 10px; background: #fff5f5; border-radius: 4px; border: 1px solid #feb2b2;">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
    
    <div class="azure-card" style="padding: 0; overflow: hidden; border: 1px solid var(--app-border);">
        <form action="student_tc_show.php" method="POST">
            <input type="hidden" name="registration_no" value="<?php echo htmlspecialchars($student['reg_no']); ?>">

            <div style="background: var(--app-primary); color: white; padding: 25px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="margin:0; font-weight: 500; font-size: 20px;">Exit Details for: <?php echo htmlspecialchars($student['student_name']); ?></h4>
                    <p style="margin:5px 0 0; opacity: 0.8; font-size: 13px;">Reg No: <?php echo htmlspecialchars($student['reg_no']); ?> | Class: <?php echo htmlspecialchars($student['class_name']); ?></p>
                </div>
                <a href="student_tc.php" style="color: white; font-size: 13px; text-decoration: none; border: 1px solid rgba(255,255,255,0.4); padding: 5px 12px; border-radius: 4px; background: rgba(255,255,255,0.1);">Change Student</a>
            </div>

            <div style="padding: 40px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                    <div class="form_group">
                        <label style="font-weight:600; display:block; margin-bottom:10px; color: #475569;">Date of Admission</label>
                        <input type="text" class="form-control fluent-input" value="<?php echo $student['admission_date']; ?>" readonly style="background:#f8fafc; border-color: #e2e8f0; color: #94a3b8;">
                    </div>
                    <div class="form_group">
                        <label style="font-weight:600; display:block; margin-bottom:10px; color: #475569;">Date of Removal (TC Date)</label>
                        <input type="date" name="removal_date" class="form-control fluent-input" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form_group">
                        <label style="font-weight:600; display:block; margin-bottom:10px; color: #475569;">Reason for Leaving</label>
                        <input type="text" name="removal_cause" class="form-control fluent-input" placeholder="e.g. Relocating, Completed 10th Grade" required>
                    </div>
                    <div class="form_group">
                        <label style="font-weight:600; display:block; margin-bottom:10px; color: #475569;">Student Conduct</label>
                        <select name="conduct" class="form-control fluent-input">
                            <option value="Excellent">Excellent</option>
                            <option value="Very Good">Very Good</option>
                            <option value="Good" selected>Good</option>
                            <option value="Fair">Fair</option>
                        </select>
                    </div>
                </div>

                <div style="text-align: right; border-top: 1px solid #f1f5f9; padding-top: 30px;">
                    <button type="submit" class="btn-fluent-primary" style="padding: 12px 40px; font-weight: 600;">Generate TC PDF</button>
                </div>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<?php include_once("includes/footer.php"); ?>
