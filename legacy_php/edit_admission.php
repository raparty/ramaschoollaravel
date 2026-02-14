<?php
declare(strict_types=1);

/**
 * ID 1.3: Edit Admission
 * Fixes: Professional Datepicker and User-Friendly Numeric Validation
 */
require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

$student_id = (int)($_GET['student_id'] ?? 0);
if ($student_id <= 0) {
    header("Location: student_detail.php");
    exit;
}

$sql = "SELECT * FROM admissions WHERE id = $student_id LIMIT 1";
$res = db_query($sql);
$row = db_fetch_array($res);

if (!$row) {
    echo "<div class='alert alert-danger'>Record not found.</div>";
    exit;
}
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

<div class="grid_container">
    <div class="page_title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h3>Edit Student Profile: <?php echo htmlspecialchars($row['student_name']); ?></h3>
        <a href="student_detail.php" class="btn-outline-secondary">Cancel & Back</a>
    </div>

    <div class="widget_wrap azure-card">
        <div class="widget_top">
            <h6 class="fluent-card-header">Personal & Academic Information</h6>
        </div>
        <div class="widget_content" style="padding: 25px;">
            <form action="process_edit_admission.php" method="post" enctype="multipart/form-data" id="editAdmissionForm">
                <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form_group">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Full Name</label>
                        <input type="text" name="student_name" class="form-control fluent-input" value="<?php echo htmlspecialchars($row['student_name']); ?>" required>
                    </div>
                    <div class="form_group">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Registration No (Locked)</label>
                        <input type="text" name="reg_no" class="form-control fluent-input" value="<?php echo htmlspecialchars($row['reg_no']); ?>" readonly style="background: #f3f4f6;">
                    </div>
                    <div class="form_group">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Date of Birth</label>
                        <input type="text" name="dob" id="dob_picker" class="form-control fluent-input" 
                               value="<?php echo $row['dob']; ?>" placeholder="YYYY-MM-DD" readonly required>
                    </div>
                    <div class="form_group">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Gender</label>
                        <select name="gender" class="form-control fluent-input">
                            <option value="Male" <?php echo ($row['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($row['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($row['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px; padding-top: 20px; border-top: 1px solid var(--app-border);">
                    <div class="form_group">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Guardian Name</label>
                        <input type="text" name="guardian_name" class="form-control fluent-input" value="<?php echo htmlspecialchars($row['guardian_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form_group">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Guardian Phone</label>
                        <input type="text" name="guardian_phone" class="form-control fluent-input numeric-only" 
                               value="<?php echo htmlspecialchars($row['guardian_phone'] ?? ''); ?>" 
                               maxlength="10" placeholder="Please enter nos" required>
                        <small class="error-text" style="color:red; display:none;">Please enter numbers only.</small>
                    </div>
                    <div class="form_group">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Aadhaar Number</label>
                        <input type="text" name="aadhaar_no" class="form-control fluent-input numeric-only" 
                               value="<?php echo htmlspecialchars($row['aadhaar_no'] ?? ''); ?>" 
                               maxlength="12" placeholder="Please enter nos" required>
                        <small class="error-text" style="color:red; display:none;">Please enter numbers only.</small>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form_group">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Blood Group</label>
                        <input type="text" name="blood_group" class="form-control fluent-input" value="<?php echo htmlspecialchars($row['blood_group'] ?? ''); ?>">
                    </div>
                    <div class="form_group">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Admission Date</label>
                        <input type="text" name="admission_date" id="admission_picker" class="form-control fluent-input" value="<?php echo $row['admission_date']; ?>" readonly required>
                    </div>
                </div>

                <div class="form_group" style="margin-bottom: 30px; padding-top: 20px; border-top: 1px solid var(--app-border);">
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">Past School Information</label>
                    <textarea name="past_school_info" class="form-control fluent-input" rows="3"><?php echo htmlspecialchars($row['past_school_info'] ?? ''); ?></textarea>
                </div>

                <div class="fluent-action-group" style="text-align: right;">
                    <button type="submit" class="btn-fluent-primary">Update Full Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 1. Professional Datepicker Initialization
    $("#dob_picker").datepicker({
        dateFormat: "yy-mm-dd",
        maxDate: 0, // Prevent future dates
        changeMonth: true,
        changeYear: true,
        yearRange: "-30:+0"
    });

    $("#admission_picker").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true
    });

    // 2. Custom Numeric Validation
    $('.numeric-only').on('input', function() {
        var node = $(this);
        var errorMsg = node.siblings('.error-text');
        
        // Remove non-numbers
        var sanitized = node.val().replace(/[^0-9]/g, '');
        
        if (node.val() !== sanitized) {
            node.val(sanitized);
            errorMsg.fadeIn(200).delay(2000).fadeOut(500);
        } else {
            errorMsg.hide();
        }
    });
});
</script>

<style>
/* Ensure the calendar is styled properly within the Azure UI */
.ui-datepicker { font-family: 'Segoe UI', Tahoma, sans-serif; border: 1px solid var(--app-border); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.ui-datepicker-header { background: var(--app-primary); color: white; border: none; }
.error-text { font-size: 11px; margin-top: 4px; font-weight: 600; }
</style>

<?php require_once("includes/footer.php"); ?>
