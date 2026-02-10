<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");
include_once("includes/staff_setting_sidebar.php");

$conn = Database::connection();
$msg = "";
$staff_id = (int)($_GET['staff_id'] ?? $_POST['staff_id'] ?? 0);

if ($staff_id <= 0) {
    echo "<script>window.location.href='view_staff.php';</script>";
    exit;
}

// 1. HANDLE UPDATE SUBMISSION
if (isset($_POST['submit'])) {
    $emp_id         = mysqli_real_escape_string($conn, (string)$_POST['emp_id']);
    $first          = mysqli_real_escape_string($conn, (string)$_POST['first']);
    $last           = mysqli_real_escape_string($conn, (string)$_POST['last']);
    $email          = mysqli_real_escape_string($conn, (string)$_POST['email']);
    $gender         = mysqli_real_escape_string($conn, (string)$_POST['gender']);
    $department     = (int)$_POST['staff_department'];
    $category       = (int)$_POST['staff_category'];
    $position       = (int)$_POST['staff_position'];
    $qualification  = (int)$_POST['staff_qualification'];
    $job            = mysqli_real_escape_string($conn, (string)$_POST['job_title']);
    $exp            = mysqli_real_escape_string($conn, (string)$_POST['exp']);
    $marritial      = mysqli_real_escape_string($conn, (string)$_POST['marritial_status']);
    $father         = mysqli_real_escape_string($conn, (string)$_POST['father_name']);
    $mother         = mysqli_real_escape_string($conn, (string)$_POST['mother_name']);
    $blood_group    = mysqli_real_escape_string($conn, (string)$_POST['blood_group']);
    $nationality    = mysqli_real_escape_string($conn, (string)$_POST['nationality']);
    $address1       = mysqli_real_escape_string($conn, (string)$_POST['address1']);
    
    // Image Update Logic
    $image_name = $_POST['old_image']; 
    if (!empty($_FILES['image']['name'])) {
        $path = "employee_image/";
        // Delete old photo if it exists and isn't the default
        if ($image_name != "" && $image_name != "no-photo.png" && file_exists($path . $image_name)) {
            unlink($path . $image_name);
        }
        $image_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $path . $image_name);
    }

    $sql_update = "UPDATE staff_employee SET 
        emp_id='$emp_id', first='$first', last='$last', email='$email', gender='$gender', 
        staff_department_id='$department', staff_cat_id='$category', staff_pos_id='$position', 
        staff_qualification_id='$qualification', job_title='$job', exp='$exp', 
        marritial_status='$marritial', father_name='$father', mother_name='$mother', 
        blood_group='$blood_group', nationality='$nationality', address1='$address1', image='$image_name' 
        WHERE staff_id = $staff_id";

    if (mysqli_query($conn, $sql_update)) {
        $msg = "<div class='alert alert-success'><h4>Staff Profile Updated Successfully</h4></div>";
    } else {
        $msg = "<div class='alert alert-error'><h4>Update Error: " . mysqli_error($conn) . "</h4></div>";
    }
}

// 2. FETCH CURRENT DATA
$sql_fetch = "SELECT * FROM staff_employee WHERE staff_id = $staff_id";
$res_fetch = mysqli_query($conn, $sql_fetch);
$row = mysqli_fetch_assoc($res_fetch);

if (!$row) { die("Record not found."); }
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <h3 style="padding:20px 0 0 20px; color:#0078D4;">Edit Staff Profile</h3>
            <div style="padding: 20px;">
                <?php if($msg != "") echo $msg; ?>
                
                <form action="#" method="post" class="form_container left_label" enctype="multipart/form-data">
                    <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>">
                    <input type="hidden" name="old_image" value="<?php echo $row['image']; ?>">
                    
                    <ul>
                        <li style="border-bottom:1px solid #F7630C;"><h4 style="color:#F7630C;">General Details</h4></li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Employee Id / Email</label>
                                <div class="form_input">
                                    <input type="text" name="emp_id" value="<?php echo htmlspecialchars($row['emp_id']); ?>" style="width:45%;" required />
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" style="width:45%;" required />
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Full Name</label>
                                <div class="form_input">
                                    <input type="text" name="first" value="<?php echo htmlspecialchars($row['first']); ?>" style="width:45%;" required />
                                    <input type="text" name="last" value="<?php echo htmlspecialchars($row['last']); ?>" style="width:45%;" required />
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Gender</label>
                                <div class="form_input">
                                    <input type="radio" name="gender" value="male" <?php if($row['gender'] == 'male') echo 'checked'; ?> /> Male &nbsp;&nbsp;
                                    <input type="radio" name="gender" value="female" <?php if($row['gender'] == 'female') echo 'checked'; ?> /> Female
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Department / Qualification</label>
                                <div class="form_input">
                                    <select name="staff_department" style="width:45%">
                                        <?php
                                        $depts = mysqli_query($conn, "SELECT * FROM staff_department");
                                        while($d = mysqli_fetch_assoc($depts)) {
                                            $sel = ($d['staff_department_id'] == $row['staff_department_id']) ? "selected" : "";
                                            echo "<option value='{$d['staff_department_id']}' $sel>{$d['staff_department']}</option>";
                                        }
                                        ?>
                                    </select>
                                    <select name="staff_qualification" style="width:45%">
                                        <?php
                                        $quals = mysqli_query($conn, "SELECT * FROM staff_qualification");
                                        while($q = mysqli_fetch_assoc($quals)) {
                                            $sel = ($q['staff_qualification_id'] == $row['staff_qualification_id']) ? "selected" : "";
                                            echo "<option value='{$q['staff_qualification_id']}' $sel>{$q['staff_qualification']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li style="border-bottom:1px solid #F7630C; margin-top:20px;"><h4 style="color:#F7630C;">Personal Details</h4></li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Father's Name</label>
                                <div class="form_input"><input type="text" name="father_name" value="<?php echo htmlspecialchars($row['father_name'] ?? ''); ?>" style="width:91%" /></div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Mother's Name</label>
                                <div class="form_input"><input type="text" name="mother_name" value="<?php echo htmlspecialchars($row['mother_name'] ?? ''); ?>" style="width:91%" /></div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Marital Status / Blood Group</label>
                                <div class="form_input">
                                    <select name="marritial_status" style="width:45%">
                                        <option value="Single" <?php if($row['marritial_status'] == 'Single') echo 'selected'; ?>>Single</option>
                                        <option value="Married" <?php if($row['marritial_status'] == 'Married') echo 'selected'; ?>>Married</option>
                                    </select>
                                    <input type="text" name="blood_group" value="<?php echo htmlspecialchars($row['blood_group'] ?? ''); ?>" style="width:45%" />
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Employee Photo</label>
                                <div class="form_input">
                                    <img src="employee_image/<?php echo $row['image'] ?: 'no-photo.png'; ?>" style="width:60px; height:60px; margin-bottom:10px; border-radius:4px;"><br>
                                    <input type="file" name="image" />
                                    <span class="label_intro">Leave blank to keep existing photo</span>
                                </div>
                            </div>
                        </li>
                        <li style="margin-top:20px;">
                            <div class="form_input">
                                <button type="submit" name="submit" class="btn_small btn_blue"><span>Update Profile</span></button>
                                <a href="view_staff.php" class="btn_small btn_orange"><span>Cancel</span></a>
                            </div>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once("includes/footer.php"); ?>
