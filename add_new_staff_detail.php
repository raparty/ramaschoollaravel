<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");
include_once("includes/staff_setting_sidebar.php");

$conn = Database::connection();
$msg = "";

if (isset($_POST['submit'])) {
    // 1. Capture and Sanitize all requested fields
    $emp_id         = mysqli_real_escape_string($conn, (string)$_POST['emp_id']);
    $first          = mysqli_real_escape_string($conn, (string)$_POST['first']);
    $last           = mysqli_real_escape_string($conn, (string)$_POST['last']);
    $email          = mysqli_real_escape_string($conn, (string)$_POST['email']);
    $gender         = mysqli_real_escape_string($conn, (string)$_POST['gender']);
    $department     = (int)$_POST['staff_department'];
    $category       = (int)$_POST['staff_category'];
    $position       = (int)$_POST['staff_position'];
    $qualification  = (int)$_POST['staff_qualification']; // Added back
    $job            = mysqli_real_escape_string($conn, (string)$_POST['job_title']);
    $exp            = mysqli_real_escape_string($conn, (string)$_POST['exp']);
    $marritial      = mysqli_real_escape_string($conn, (string)$_POST['marritial_status']);
    $father         = mysqli_real_escape_string($conn, (string)$_POST['father_name']); // Added back
    $mother         = mysqli_real_escape_string($conn, (string)$_POST['mother_name']); // Added back
    $blood_group    = mysqli_real_escape_string($conn, (string)$_POST['blood_group']); // Made separate
    $nationality    = mysqli_real_escape_string($conn, (string)$_POST['nationality']); // Added back
    $address1       = mysqli_real_escape_string($conn, (string)$_POST['address1']);
    $address2       = mysqli_real_escape_string($conn, (string)$_POST['address2']);

    $image_name = "";
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $path = "employee_image/";
        $image_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $path . $image_name);
    }

    $check_sql = "SELECT * FROM staff_employee WHERE email='$email' OR emp_id='$emp_id'";
    $check_res = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_res) == 0) {
        $sql = "INSERT INTO staff_employee (
                    emp_id, first, last, email, gender, staff_department_id, 
                    staff_cat_id, staff_pos_id, staff_qualification_id, job_title, 
                    exp, marritial_status, father_name, mother_name, blood_group, 
                    nationality, address1, address2, image
                ) VALUES (
                    '$emp_id', '$first', '$last', '$email', '$gender', '$department', 
                    '$category', '$position', '$qualification', '$job', 
                    '$exp', '$marritial', '$father', '$mother', '$blood_group', 
                    '$nationality', '$address1', '$address2', '$image_name'
                )";
        
        if (mysqli_query($conn, $sql)) {
            $msg = "<div class='alert alert-success'><h4>Employee Detail Added Successfully</h4></div>";
        } else {
            $msg = "<div class='alert alert-error'><h4>Error: " . mysqli_error($conn) . "</h4></div>";
        }
    } else {
        $msg = "<div class='alert alert-error'><h4>Employee ID or Email Already Exists</h4></div>";
    }
}
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <h3 style="padding:20px 0 0 20px; color:#0078D4; border-bottom:1px solid #e2e2e2;">Employee Registration</h3>
            
            <div style="padding: 20px;">
                <?php if($msg != "") echo $msg; ?>
                
                <form action="#" method="post" class="form_container left_label" enctype="multipart/form-data">
                    <ul>
                        <li style="border-bottom:1px solid #F7630C;"><h4 style="color:#F7630C;">General Details</h4></li>
                        
                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Employee Id / Email</label>
                                <div class="form_input">
                                    <input type="text" name="emp_id" placeholder="Employee ID" style="width:45%;" required />
                                    <input type="email" name="email" placeholder="Email Address" style="width:45%;" required />
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Full Name</label>
                                <div class="form_input">
                                    <input type="text" name="first" placeholder="First Name" style="width:45%;" required />
                                    <input type="text" name="last" placeholder="Last Name" style="width:45%;" required />
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Gender</label>
                                <div class="form_input">
                                    <input type="radio" name="gender" value="male" checked /> Male &nbsp;&nbsp;
                                    <input type="radio" name="gender" value="female" /> Female
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
                                        while($d = mysqli_fetch_assoc($depts)) echo "<option value='{$d['staff_department_id']}'>{$d['staff_department']}</option>";
                                        ?>
                                    </select>
                                    <select name="staff_qualification" style="width:45%">
                                        <?php
                                        $quals = mysqli_query($conn, "SELECT * FROM staff_qualification");
                                        while($q = mysqli_fetch_assoc($quals)) echo "<option value='{$q['staff_qualification_id']}'>{$q['staff_qualification']}</option>";
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Job Title / Experience</label>
                                <div class="form_input">
                                    <input type="text" name="job_title" placeholder="e.g. Senior Teacher" style="width:45%;" />
                                    <input type="text" name="exp" placeholder="e.g. 5 Years" style="width:45%;" />
                                </div>
                            </div>
                        </li>

                        <li style="border-bottom:1px solid #F7630C; margin-top:20px;"><h4 style="color:#F7630C;">Personal Details</h4></li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Father's Name</label>
                                <div class="form_input"><input type="text" name="father_name" style="width:91%" /></div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Mother's Name</label>
                                <div class="form_input"><input type="text" name="mother_name" style="width:91%" /></div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Marital Status / Blood Group</label>
                                <div class="form_input">
                                    <select name="marritial_status" style="width:45%">
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                    </select>
                                    <input type="text" name="blood_group" placeholder="Blood Group (e.g. O+)" style="width:45%" />
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Nationality</label>
                                <div class="form_input"><input type="text" name="nationality" value="Indian" style="width:91%" /></div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Address Line 1</label>
                                <div class="form_input"><input type="text" name="address1" style="width:91%" /></div>
                            </div>
                        </li>

                        <li>
                            <div class="form_grid_12">
                                <label class="field_title">Employee Photo</label>
                                <div class="form_input"><input type="file" name="image" /></div>
                            </div>
                        </li>

                        <li style="margin-top:20px;">
                            <div class="form_input">
                                <button type="submit" name="submit" class="btn_small btn_blue"><span>Register Staff</span></button>
                            </div>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once("includes/footer.php"); ?>
