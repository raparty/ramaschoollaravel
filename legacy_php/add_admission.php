<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");
?>

<div id="container">
    <div class="page_title" style="margin-bottom: 25px;">
        <h3 style="font-weight: 300; font-size: 28px; color: var(--fluent-slate);">New Student Admission</h3>
    </div>

    <div class="grid_container">
        <div class="widget_wrap azure-card">
            <div class="widget_top">
                <h6 class="fluent-card-header">Student Personal Profile & Identity</h6>
            </div>
            <div class="widget_content" style="padding: 30px;">
                <form action="admission_process.php" method="post" enctype="multipart/form-data" id="admissionForm">
                    <div class="row" style="display: flex; gap: 30px; margin-bottom: 30px;">
                        <div style="flex: 0 0 200px; text-align: center; border-right: 1px solid var(--app-border); padding-right: 30px;">
                            <div id="photoPreview" style="width: 150px; height: 150px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                <span>Photo Preview</span>
                            </div>
                            <input type="file" name="student_pic" class="form-control fluent-input" accept="image/*" required>
                            <small class="text-muted">Max size: 2MB</small>
                        </div>

                        <div style="flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form_group">
                                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Full Name <span style="color:red">*</span></label>
                                <input name="student_name" type="text" class="form-control fluent-input" placeholder="Enter Full Name" required>
                            </div>
                            <div class="form_group">
                                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Admission Date</label>
                                <input name="admission_date" type="date" class="form-control fluent-input" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form_group">
                                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Date of Birth <span style="color:red">*</span></label>
                                <input name="dob" id="dob" type="date" class="form-control fluent-input" onchange="validateAge()" required>
                                <small id="ageError" style="color:red; display:none;">Warning: Age may be incompatible with selected class.</small>
                            </div>
                            <div class="form_group">
                                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Gender</label>
                                <select name="gender" class="form-control fluent-input" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 25px; padding-top: 25px; border-top: 1px solid var(--app-border);">
                        <div class="form_group">
                            <label style="font-weight: 600; display: block; margin-bottom: 8px;">Guardian Name <span style="color:red">*</span></label>
                            <input name="guardian_name" type="text" class="form-control fluent-input" placeholder="Father/Mother Name" required>
                        </div>
                        <div class="form_group">
                            <label style="font-weight: 600; display: block; margin-bottom: 8px;">Guardian Phone <span style="color:red">*</span></label>
                            <input name="guardian_phone" type="text" class="form-control fluent-input" placeholder="10 Digit Mobile" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" required>
                        </div>
                        <div class="form_group">
                            <label style="font-weight: 600; display: block; margin-bottom: 8px;">Aadhaar Number <span style="color:red">*</span></label>
                            <input name="aadhaar_no" type="text" class="form-control fluent-input" maxlength="12" pattern="[0-9]{12}" placeholder="12 Digit Number" title="12 digit numeric format required" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                        <div class="form_group">
                            <label style="font-weight: 600; display: block; margin-bottom: 8px;">Class for Admission <span style="color:red">*</span></label>
                            <select name="class_id" id="class_id" class="form-control fluent-input" onchange="validateAge()" required>
                                <option value="">Select Class</option>
                                <?php 
                                $classes = db_query("SELECT id, class_name FROM classes ORDER BY id ASC");
                                while($c = db_fetch_array($classes)) {
                                    echo "<option value='".$c['id']."'>".$c['class_name']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form_group">
                            <label style="font-weight: 600; display: block; margin-bottom: 8px;">Blood Group</label>
                            <input name="blood_group" type="text" class="form-control fluent-input" placeholder="e.g. O+">
                        </div>
                        <div class="form_group">
                            <label style="font-weight: 600; display: block; margin-bottom: 8px;">Aadhaar Document (PDF)</label>
                            <input type="file" name="aadhaar_doc" class="form-control fluent-input" accept=".pdf">
                        </div>
                    </div>

                    <div class="form_group" style="margin-bottom: 30px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Past School Information</label>
                        <textarea name="past_school_info" class="form-control fluent-input" rows="3" placeholder="Enter previous school name, location, and highest grade completed"></textarea>
                    </div>

                    <div class="fluent-action-group" style="border-top: 1px solid var(--app-border); padding-top: 25px; text-align: right;">
                        <button type="submit" class="btn-fluent-primary">Confirm & Save Admission</button>
                        <a href="student_detail.php" class="btn-outline-secondary" style="margin-left: 10px; text-decoration: none;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Real-time Age Validation for Class
 */
function validateAge() {
    const dobValue = document.getElementById('dob').value;
    const classId = document.getElementById('class_id').value;
    const errorMsg = document.getElementById('ageError');
    
    if (dobValue && classId) {
        const birthDate = new Date(dobValue);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        // Example logic: Nursery (ID 1) requires min age 3
        if (classId == "1" && age < 3) {
            errorMsg.style.display = 'block';
            return false;
        }
        errorMsg.style.display = 'none';
    }
    return true;
}
</script>

<?php include_once("includes/footer.php"); ?>
