<?php
declare(strict_types=1);

// Enable error reporting to catch issues immediately
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");
include_once("includes/school_setting_sidebar.php");

$conn = Database::connection();
$msg = "";

if (isset($_POST['submit'])) {
    $school_name = mysqli_real_escape_string($conn, trim((string)$_POST['school_name']));
    $school_address = mysqli_real_escape_string($conn, trim((string)$_POST['school_address']));
    
    // Check if table exists and if any record is present
    $sql_check = "SELECT * FROM school_details"; 
    $res_check = mysqli_query($conn, $sql_check);
    
    if (!$res_check) {
        $msg = "<div class='alert alert-danger'>Database Error: Table 'school_details' missing. Please run the SQL migration.</div>";
    } elseif (mysqli_num_rows($res_check) == 0) {
        $school_logo = "";
        if (isset($_FILES['school_logo']['name']) && $_FILES['school_logo']['name'] != "") {
            $school_logo = time() . "_" . $_FILES['school_logo']['name'];
            $path = "school_logo/";
            if (!is_dir($path)) { mkdir($path, 0775, true); }
            move_uploaded_file($_FILES['school_logo']['tmp_name'], $path . $school_logo);
        }

        if (!empty($school_name) && !empty($school_address)) {
            $sql_ins = "INSERT INTO school_details (school_name, school_address, school_logo) 
                        VALUES ('$school_name', '$school_address', '$school_logo')";
            
            if (mysqli_query($conn, $sql_ins)) {
                echo "<script>window.location.href='school_detail.php?msg=1';</script>";
                exit;
            } else {
                $msg = "<div class='alert alert-danger'>Insert Failed: " . htmlspecialchars(mysqli_error($conn)) . "</div>";
            }
        } else {
            $msg = "<div class='alert alert-danger'>Please fill in all required fields.</div>";
        }
    } else {
        $msg = "<div class='alert alert-warning'>School details already exist. Please use the Edit page instead.</div>";
    }
}
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <h3 style="padding:10px 0 0 20px; color:#1c75bc">Institutional Identity</h3>
            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_top">
                        <h6>Add School Information</h6>
                    </div>
                    <div class="widget_content" style="padding: 20px;">
                        <?php if ($msg != "") echo $msg; ?>
                        
                        <form action="add_school_detail.php" method="post" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label style="display:block; margin-bottom:5px; font-weight:bold;">School Name <span style="color:red;">*</span></label>
                                    <input name="school_name" type="text" style="width:100%;" placeholder="e.g. Hope School" required />
                                </div>
                                <div class="col-md-6">
                                    <label style="display:block; margin-bottom:5px; font-weight:bold;">School Address <span style="color:red;">*</span></label>
                                    <input name="school_address" type="text" style="width:100%;" placeholder="Location details" required />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Upload Logo</label>
                                    <input name="school_logo" type="file" accept="image/*" />
                                </div>
                            </div>
                            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                                <button type="submit" name="submit" class="btn_small btn_blue"><span>Save Detail</span></button>
                                <a href="school_detail.php" class="btn_small btn_orange" style="margin-left:10px;"><span>Cancel</span></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("includes/footer.php"); ?>
