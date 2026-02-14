<?php
declare(strict_types=1);

// Enable error reporting to diagnose the "blank page" issue
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();
$sid = isset($_GET['sid']) ? db_escape($_GET['sid']) : '';
$msg = "";

// Process Update Logic
if(isset($_POST['submit'])) {
    $vechile_number = db_escape($_POST['vechile_number']);
    $seats = db_escape($_POST['seat']);
    
    // Process route IDs
    $route_ids = isset($_POST['route_id']) ? array_map('db_escape', $_POST['route_id']) : [];
    $route_id_str = implode(",", $route_ids);
    
    // Ensure we have a valid ID before updating
    if(!empty($sid)) {
        $sql_update = "UPDATE transport_add_vechile SET 
                       vechile_no = '$vechile_number', 
                       route_id = '$route_id_str', 
                       no_of_seats = '$seats' 
                       WHERE vechile_id = '$sid'";
        
        $result = db_query($sql_update);
        
        if($result) {
            // Redirect on success
            echo "<script>window.location.href='transport_vechile_detail.php?msg=3';</script>";
            exit;
        } else {
            // Display exact error instead of a blank page
            $msg = "<div class='alert alert-danger'>Update Failed: " . htmlspecialchars((string)db_error()) . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Error: Missing Vehicle ID.</div>";
    }
}

// Fetch Data for the form
if(empty($sid)) {
    echo "<script>window.location.href='transport_vechile_detail.php';</script>";
    exit;
}

$sql_fetch = "SELECT * FROM transport_add_vechile WHERE vechile_id = '$sid'";
$res_fetch = db_query($sql_fetch);

if(!$res_fetch || db_num_rows($res_fetch) == 0) {
    echo "<script>window.location.href='transport_vechile_detail.php?msg=error';</script>";
    exit;
}

$row = db_fetch_array($res_fetch);

// Handle column name fallbacks
$display_number = $row['vechile_no'] ?? $row['vechile_number'] ?? '';
$display_seats = $row['no_of_seats'] ?? $row['seat'] ?? '';
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <h3 style="padding:10px 0 0 20px; color:#1c75bc">Transport Management</h3>
            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_top">
                        <h6>Edit Vehicle Detail</h6>
                    </div>
                    <div class="widget_content" style="padding: 20px;">
                        <?php if($msg != "") echo $msg; ?>
                        
                        <form action="transport_edit_vehicle.php?sid=<?php echo htmlspecialchars((string)$sid); ?>" method="post">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Vehicle Number <span style="color:red;">*</span></label>
                                    <input name="vechile_number" type="text" style="width:100%;" value="<?php echo htmlspecialchars((string)$display_number); ?>" required />
                                </div>
                                <div class="col-md-6">
                                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Number of Seats <span style="color:red;">*</span></label>
                                    <input name="seat" type="number" style="width:100%;" value="<?php echo htmlspecialchars((string)$display_seats); ?>" required />
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Assign Routes</label>
                                    <div style="border: 1px solid #ddd; padding: 15px; max-height: 200px; overflow-y: auto; background: #f9f9f9; border-radius: 4px;">
                                        <?php 
                                        $current_routes = explode(",", (string)$row['route_id']);
                                        $sql_r = "SELECT * FROM transport_add_route ORDER BY route_name ASC";
                                        $res_r = db_query($sql_r);
                                        
                                        if($res_r && db_num_rows($res_r) > 0) {
                                            while($row_r = db_fetch_array($res_r)) {
                                                $checked = in_array($row_r['route_id'], $current_routes) ? 'checked' : '';
                                                echo '<div style="margin-bottom: 8px;">';
                                                echo '<input type="checkbox" name="route_id[]" value="'.$row_r['route_id'].'" id="route_'.$row_r['route_id'].'" '.$checked.'> ';
                                                echo '<label for="route_'.$row_r['route_id'].'">'.htmlspecialchars($row_r['route_name']).'</label>';
                                                echo '</div>';
                                            }
                                        } else {
                                            echo '<p style="color:#888;">No routes found in the database.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                                <button type="submit" name="submit" class="btn_small btn_blue">
                                    <span>Update Vehicle</span>
                                </button>
                                <a href="transport_vechile_detail.php" class="btn_small btn_orange" style="margin-left:10px;">
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
