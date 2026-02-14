<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();
$msg = "";
$sid = $_GET['sid'] ?? $_POST['route_id'] ?? '';

// Handle form submission
if(isset($_POST['submit'])) {
    $destination = mysqli_real_escape_string($conn, trim((string)$_POST['destination']));
    $cost = mysqli_real_escape_string($conn, trim((string)$_POST['cost']));
    $safe_sid = mysqli_real_escape_string($conn, (string)$sid);
    
    if(!empty($destination) && !empty($safe_sid)) {
        $sql_update = "UPDATE transport_add_route SET route_name='$destination', cost='$cost' WHERE route_id='$safe_sid'";
        
        if(mysqli_query($conn, $sql_update)) {
            header("Location: transport_route_detail.php?msg=3");
            exit;
        } else {
            $msg = "<div class='alert alert-danger'>Error updating route: " . htmlspecialchars(mysqli_error($conn)) . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Error: Missing Route ID or Destination.</div>";
    }
}

// Fetch current data
$safe_sid = mysqli_real_escape_string($conn, (string)$sid);
$sql_fetch = "SELECT * FROM transport_add_route WHERE route_id='$safe_sid'";
$res_fetch = mysqli_query($conn, $sql_fetch);

if (!$res_fetch) {
    die("Database Query Failed: " . htmlspecialchars(mysqli_error($conn)));
}

$row = mysqli_fetch_assoc($res_fetch);
?>

<div class="page_title">
    <h3>Edit Route Detail</h3>
</div>

<?php include_once("includes/transport_setting_sidebar.php"); ?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <div class="grid_12">
                <div class="widget_wrap enterprise-card">
                    <div class="widget_top">
                        <h6>Edit Route Information</h6>
                    </div>
                    <div class="widget_content p-4">
                        <?php if($msg != "") echo $msg; ?>
                        
                        <form action="transport_route_edit.php?sid=<?php echo $sid; ?>" method="post">
                            <input type="hidden" name="route_id" value="<?php echo htmlspecialchars((string)$sid); ?>">
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Destination Name <span class="text-danger">*</span></label>
                                    <input name="destination" type="text" class="form-control" value="<?php echo htmlspecialchars($row['route_name'] ?? ''); ?>" placeholder="Enter destination name" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Monthly Cost (₹) <span class="text-danger">*</span></label>
                                    <input name="cost" type="number" step="0.01" class="form-control" value="<?php echo htmlspecialchars($row['cost'] ?? ''); ?>" placeholder="Enter monthly cost" required />
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" name="submit" class="btn-fluent-primary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:6px;">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" fill="currentColor"/>
                                    </svg>
                                    Update Route
                                </button>
                                <a href="transport_route_detail.php" class="btn-fluent-secondary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:6px;">
                                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="currentColor"/>
                                    </svg>
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once("includes/footer.php"); ?>
