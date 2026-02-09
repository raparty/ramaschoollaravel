<?php
declare(strict_types=1);

// 1. Enable error reporting to kill the blank page
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();
$msg = "";

// Form processing logic
if(isset($_POST['submit'])) {
    // Standardize input
    $vechile_no = mysqli_real_escape_string($conn, trim((string)$_POST['number']));
    $seats = (int)($_POST['seat'] ?? 0);
    
    // Process route selection
    $route_ids_raw = isset($_POST['route_id']) ? $_POST['route_id'] : [];
    $route_ids_escaped = array_map(function($id) use ($conn) {
        return mysqli_real_escape_string($conn, (string)$id);
    }, $route_ids_raw);
    $route_ids = implode(",", $route_ids_escaped);

    if(!empty($vechile_no)) {
        // Check if vehicle already exists
        $check_sql = "SELECT * FROM transport_add_vechile WHERE vechile_no = '$vechile_no'";
        $check_res = mysqli_query($conn, $check_sql);
        
        if($check_res && mysqli_num_rows($check_res) == 0) {
            $sql_ins = "INSERT INTO transport_add_vechile (vechile_no, route_id, no_of_seats) 
                        VALUES ('$vechile_no', '$route_ids', '$seats')";
            
            if(mysqli_query($conn, $sql_ins)) {
                // Use JS redirect to avoid blank pages caused by header issues
                echo "<script>window.location.href='transport_vechile_detail.php?msg=1';</script>";
                exit;
            } else {
                $msg = "<div class='alert alert-danger'>Database Error: " . htmlspecialchars(mysqli_error($conn)) . "</div>";
            }
        } else {
            $msg = "<div class='alert alert-danger'>Vehicle Number '$vechile_no' already exists.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Please fill in the Vehicle Number.</div>";
    }
}
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <h3 style="padding:10px 0 0 20px; color:#1c75bc">Transport Management</h3>
            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_top">
                        <h6>Add New Vehicle</h6>
                    </div>
                    <div class="widget_content" style="padding: 20px;">
                        <?php if($msg != "") echo $msg; ?>
                        
                        <form action="transport_add_vechile.php" method="post">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Vehicle Number <span style="color:red;">*</span></label>
                                    <input name="number" type="text" style="width:100%;" placeholder="e.g. TS 09 EA 1234" required />
                                </div>
                                <div class="col-md-6">
                                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Number of Seats <span style="color:red;">*</span></label>
                                    <input name="seat" type="number" style="width:100%;" placeholder="40" required />
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Select Route(s)</label>
                                    <div style="border: 1px solid #ddd; padding: 15px; max-height: 200px; overflow-y: auto; background: #f9f9f9; border-radius: 4px;">
                                        <?php 
                                        $sql_r = "SELECT * FROM transport_add_route ORDER BY route_name ASC";
                                        $res_r = mysqli_query($conn, $sql_r);
                                        
                                        if($res_r && mysqli_num_rows($res_r) > 0) {
                                            while($row_r = mysqli_fetch_assoc($res_r)) {
                                                echo '<div style="margin-bottom: 8px;">';
                                                echo '<input type="checkbox" name="route_id[]" value="'.$row_r['route_id'].'" id="route_'.$row_r['route_id'].'"> ';
                                                echo '<label for="route_'.$row_r['route_id'].'">'.htmlspecialchars($row_r['route_name']).'</label>';
                                                echo '</div>';
                                            }
                                        } else {
                                            echo '<p style="color:#888;">No routes found. Please add routes first.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                                <button type="submit" name="submit" class="btn_small btn_blue">
                                    <span>Save Vehicle</span>
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
