<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

// GET context from the audit: student_id=X
$student_id = (int)($_GET['student_id'] ?? 0);

if ($student_id <= 0) {
    header("Location: admission.php");
    exit;
}

$sql = "SELECT * FROM admissions WHERE id = $student_id LIMIT 1";
$res = db_query($sql);
$row = db_fetch_array($res);

if (!$row) {
    echo "<div class='alert alert-danger'>Record not found. <a href='admission.php'>Back to List</a></div>";
    require_once("includes/footer.php");
    exit;
}
?>

<div class="grid_container">
    <div class="page_title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h3>Edit Student: <?php echo htmlspecialchars($row['student_name']); ?></h3>
        <a href="admission.php" class="btn-outline-secondary">Cancel & Back</a>
    </div>

    <div class="widget_wrap azure-card">
        <div class="widget_top">
            <h6 class="fluent-card-header">Admission Information</h6>
        </div>
        <div class="widget_content" style="padding: 25px;">
            <form action="process_edit_admission.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form_group">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Full Name</label>
                        <input type="text" name="student_name" class="form-control fluent-input" value="<?php echo htmlspecialchars($row['student_name']); ?>" required>
                    </div>
                    
                    <div class="form_group">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Registration No</label>
                        <input type="text" name="reg_no" class="form-control fluent-input" value="<?php echo htmlspecialchars($row['reg_no']); ?>" readonly style="background: #f3f4f6;">
                    </div>

                    <div class="form_group">
                        <label style="font-weight: 600; display: block; margin-bottom: 8px;">Class</label>
                        <select name="class_id" class="form-control fluent-input">
                            <?php 
                            $c_sql = "SELECT id, class_name FROM classes ORDER BY class_name ASC";
                            $c_res = db_query($c_sql);
                            while($c = db_fetch_array($c_res)) {
                                $sel = ($c['id'] == $row['class_id']) ? 'selected' : '';
                                echo "<option value='{$c['id']}' $sel>{$c['class_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="fluent-action-group" style="margin-top: 30px; border-top: 1px solid var(--app-border); padding-top: 20px;">
                    <button type="submit" class="btn-fluent-primary">Update Admission Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once("includes/footer.php"); ?>
