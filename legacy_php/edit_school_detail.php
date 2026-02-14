<?php
declare(strict_types=1);

// Enable error reporting to diagnose if the query fails
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");
include_once("includes/school_setting_sidebar.php");

$conn = Database::connection();
$sid = isset($_GET['sid']) ? mysqli_real_escape_string($conn, (string)$_GET['sid']) : '';

// Process Update Logic
if(isset($_POST['submit'])) {
    $school_name = mysqli_real_escape_string($conn, trim((string)$_POST['school_name']));
    $school_address = mysqli_real_escape_string($conn, trim((string)$_POST['school_address']));
    $path = "school_logo/";
    
    if(isset($_FILES['school_logo']['name']) && $_FILES['school_logo']['name'] != "") {
        $school_logo = time() . "_" . $_FILES['school_logo']['name'];
        move_uploaded_file($_FILES['school_logo']['tmp_name'], $path . $school_logo);
        
        // Remove old logo if it exists
        $old_photo = $_POST['old_logo'];
        if(!empty($old_photo) && file_exists($path . $old_photo)) {
            @unlink($path . $old_photo);
        }
    } else {
        $school_logo = $_POST['old_logo'];
    }

    // Updated to use your pluralized table 'school_details'
    $sql_update = "UPDATE school_details SET 
                   `school_name` = '$school_name', 
                   `school_address` = '$school_address', 
                   `school_logo` = '$school_logo' 
                   WHERE id = '$sid'";
    
    if(mysqli_query($conn, $sql_update)) {
        // JS redirect avoids the blank page issue caused by headers
        echo "<script>window.location.href='school_detail.php?msg=3';</script>";
        exit;
    } else {
        die("Update Error: " . mysqli_error($conn));
    }
}

// Fetch existing data
if(empty($sid)) {
    echo "<script>window.location.href='school_detail.php';</script>";
    exit;
}

$sql_fetch = "SELECT * FROM school_details WHERE `id` = '$sid'";
$res_fetch = mysqli_query($conn, $sql_fetch);
$row = mysqli_fetch_array($res_fetch);

if(!$row) {
    echo "<script>window.location.href='school_detail.php?error=notfound';</script>";
    exit;
}
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <h3 style="padding:10px 0 0 20px; color:#1c75bc">Institutional Identity</h3>
            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_top">
                        <h6>Edit School Information</h6>
                    </div>
                    <div class="widget_content" style="padding: 20px;">
                        <form action="edit_school_detail.php?sid=<?php echo htmlspecialchars($sid); ?>" method="post" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label style="display:block; margin-bottom:5px; font-weight:bold;">School Name <span style="color:red;">*</span></label>
                                    <input name="school_name" type="text" style="width:100%;" value="<?php echo htmlspecialchars((string)$row['school_name']); ?>" required />
                                </div>
                                <div class="col-md-6">
                                    <label style="display:block; margin-bottom:5px; font-weight:bold;">School Address <span style="color:red;">*</span></label>
                                    <input name="school_address" type="text" style="width:100%;" value="<?php echo htmlspecialchars((string)$row['school_address']); ?>" required />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Update School Logo</label>
                                    <input name="school_logo" type="file" style="width:100%;" accept="image/*" />
                                    <input type="hidden" name="old_logo" value="<?php echo htmlspecialchars((string)$row['school_logo']); ?>">
                                    <div style="margin-top: 10px;">
                                        <small style="display:block; color:#666;">Current Logo:</small>
                                        <img src="school_logo/<?php echo $row['school_logo']; ?>" width="60" height="60" style="border: 1px solid #ddd; border-radius: 4px;" />
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                                <button type="submit" name="submit" class="btn_small btn_blue">
                                    <span>Update Details</span>
                                </button>
                                <a href="school_detail.php" class="btn_small btn_orange" style="margin-left:10px;">
                                    <span>Cancel</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("includes/footer.php"); ?>
