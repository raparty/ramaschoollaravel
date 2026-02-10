<?php
declare(strict_types=1);

require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();
$msg = "";
$sid = (int)($_GET['sid'] ?? $_POST['sid'] ?? 0);

if ($sid <= 0) {
    echo "<script>window.location.href='student_fine_detail.php';</script>";
    exit;
}

// 1. HANDLE UPDATE SUBMISSION
if (isset($_POST['submit'])) {
    $fine_amount = mysqli_real_escape_string($conn, trim((string)$_POST['fine_amount']));
    // Ensure session is never empty
    $session = mysqli_real_escape_string($conn, trim((string)$_POST['session']));
    if (empty($session)) { $session = $_SESSION['session']; }

    $sql_upd = "UPDATE student_fine_detail SET 
                fine_amount = '$fine_amount', 
                session = '$session' 
                WHERE id = '$sid'";

    if (mysqli_query($conn, $sql_upd)) {
        echo "<script>window.location.href='student_fine_detail.php?msg=update_success';</script>";
        exit;
    } else {
        $msg = "<div class='alert alert-danger'>Update Failed: " . mysqli_error($conn) . "</div>";
    }
}

// 2. FETCH CURRENT DATA
$sql_fine = "SELECT * FROM student_fine_detail WHERE id = $sid";
$res_fine = mysqli_query($conn, $sql_fine);
$fine_row = mysqli_fetch_assoc($res_fine);

if (!$fine_row) { die("Error: Fine record not found."); }

$reg_no = mysqli_real_escape_string($conn, (string)$fine_row['registration_no']);
$sql_std = "SELECT student_name FROM admissions WHERE reg_no = '$reg_no'";
$student = mysqli_fetch_assoc(mysqli_query($conn, $sql_std));
?>

<?php include_once("includes/library_setting_sidebar.php"); ?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <div class="grid_12">
                <div class="widget_wrap">
                    <h3 style="padding:20px 0 0 20px; color:#0078D4">Edit Student Fine</h3>
                    <div class="widget_content" style="padding: 25px;">
                        <?php if($msg != "") echo $msg; ?>
                        <form action="" method="post" class="form_container left_label">
                            <input type="hidden" name="sid" value="<?php echo $sid; ?>">
                            <ul>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Student Name</label>
                                        <div class="form_input">
                                            <input type="text" value="<?php echo htmlspecialchars($student['student_name'] ?? 'Unknown'); ?>" readonly style="background:#f3f4f6; width:350px;" />
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Fine Amount (₹) <span style="color:red;">*</span></label>
                                        <div class="form_input">
                                            <input name="fine_amount" type="number" step="0.01" value="<?php echo htmlspecialchars($fine_row['fine_amount']); ?>" required style="width:350px;" />
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Session <span style="color:red;">*</span></label>
                                        <div class="form_input">
                                            <input name="session" type="text" value="<?php echo htmlspecialchars($fine_row['session'] ?: $_SESSION['session']); ?>" required style="width:350px;" />
                                            <span class="label_intro">Verify correct academic year (e.g. 2025-26)</span>
                                        </div>
                                    </div>
                                </li>
                                <li style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                                    <div class="form_input">
                                        <button type="submit" name="submit" class="btn_small btn_blue"><span>Update Fine</span></button>
                                        <a href="student_fine_detail.php" class="btn_small btn_orange" style="margin-left:10px;"><span>Cancel</span></a>
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
