<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");
include_once("includes/staff_setting_sidebar.php");

$conn = Database::connection();
$msg = "";

// 1. Handle the Form Submission
if (isset($_POST['submit'])) {
    $dept_name = mysqli_real_escape_string($conn, trim((string)$_POST['staff_department']));
    
    if (!empty($dept_name)) {
        // Check if department already exists to prevent duplicates
        $check_sql = "SELECT * FROM staff_department WHERE staff_department = '$dept_name'";
        $check_res = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_res) == 0) {
            $sql = "INSERT INTO staff_department (staff_department) VALUES ('$dept_name')";
            if (mysqli_query($conn, $sql)) {
                $msg = "<div class='alert alert-success'><h4>Staff Department Added Successfully</h4></div>";
            } else {
                $msg = "<div class='alert alert-error'><h4>Error: " . mysqli_error($conn) . "</h4></div>";
            }
        } else {
            $msg = "<div class='alert alert-error'><h4>This Department already exists in the system</h4></div>";
        }
    } else {
        $msg = "<div class='alert alert-error'><h4>Please enter a department name</h4></div>";
    }
}
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <div class="grid_12">
                <div class="widget_wrap">
                    <h3 style="padding:20px 0 0 20px; color:#0078D4">Add New Staff Department</h3>
                    
                    <div class="widget_content" style="padding: 25px;">
                        <?php if($msg != "") echo $msg; ?>
                        
                        <form action="#" method="post" class="form_container left_label">
                            <ul>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Department Name <span style="color:red;">*</span></label>
                                        <div class="form_input">
                                            <input name="staff_department" type="text" required="required" placeholder="e.g. Academic, Administration, Accounts" style="width:400px;"/>
                                            <span class="label_intro">Enter the name of the new school department</span>
                                        </div>
                                    </div>
                                </li>
                                
                                <li style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                                    <div class="form_input">
                                        <button type="submit" name="submit" class="btn_small btn_blue"><span>Save Department</span></button>
                                        <a href="view_staff_department.php" class="btn_small btn_orange" style="margin-left:10px; text-decoration:none;"><span>Back to List</span></a>
                                    </div>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("includes/footer.php"); ?>
