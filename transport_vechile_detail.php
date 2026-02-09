<?php
declare(strict_types=1);

// Enable error reporting to diagnose if the database query itself fails
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();

// Handle vehicle deletion - Moved to top to ensure it processes correctly
if(isset($_GET['sid']) && $_GET['sid'] != '') {
    $sid = mysqli_real_escape_string($conn, (string)$_GET['sid']);
    $delete_query = "DELETE FROM transport_add_vechile WHERE vechile_id='$sid'";
    
    if(mysqli_query($conn, $delete_query)) {
        // Use JS redirect to avoid blank page "headers already sent" issues
        echo "<script>window.location.href='transport_vechile_detail.php?msg=2';</script>";
        exit;
    } else {
        die("Deletion Failed: " . mysqli_error($conn));
    }
}

$msg = "";
if(isset($_GET['msg'])) {
    if($_GET['msg'] == 1) {
        $msg = "<div class='alert alert-success'>Vehicle added successfully</div>";
    } else if($_GET['msg'] == 2) {
        $msg = "<div class='alert alert-success'>Vehicle deleted successfully</div>";
    } else if($_GET['msg'] == 3) {
        $msg = "<div class='alert alert-success'>Vehicle updated successfully</div>";
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
                        <h6 style="display:inline-block;">Transport Vehicles</h6>
                        <div style="float:right; padding: 5px;">
                            <a href="transport_add_vechile.php" class="btn_small btn_blue">
                                <span>+ Add New Vehicle</span>
                            </a>
                        </div>
                    </div>
                    <div class="widget_content" style="padding: 20px;">
                        <?php if($msg != "") echo $msg; ?>
                        
                        <table class="display data_tbl">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">S.No.</th>
                                    <th>Vehicle Number</th>
                                    <th>Route Name(s)</th>
                                    <th style="text-align: center;">Seats</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 0;
                                $sql = "SELECT * FROM transport_add_vechile ORDER BY vechile_no ASC";
                                $res = mysqli_query($conn, $sql);
                                
                                if($res && mysqli_num_rows($res) > 0) {
                                    while($row_v = mysqli_fetch_assoc($res)) {
                                        $i++;
                                        
                                        // Resolve comma-separated route IDs to Names
                                        $route_ids = explode(",", (string)$row_v['route_id']);
                                        $route_names = [];
                                        foreach($route_ids as $id) {
                                            $id = trim($id);
                                            if(!empty($id)) {
                                                $safe_id = mysqli_real_escape_string($conn, $id);
                                                $res_r = mysqli_query($conn, "SELECT route_name FROM transport_add_route WHERE route_id='$safe_id'");
                                                if($r_data = mysqli_fetch_assoc($res_r)) {
                                                    $route_names[] = $r_data['route_name'];
                                                }
                                            }
                                        }
                                        $display_routes = !empty($route_names) ? implode(", ", $route_names) : "None";
                                ?>
                                <tr>
                                    <td class="center"><?php echo $i; ?></td>
                                    <td class="center"><strong><?php echo htmlspecialchars((string)$row_v['vechile_no']); ?></strong></td>
                                    <td class="center"><?php echo htmlspecialchars($display_routes); ?></td>
                                    <td class="center"><?php echo htmlspecialchars((string)$row_v['no_of_seats']); ?></td>
                                    <td class="center">
                                        <span><a class="action-icons c-edit" href="transport_edit_vehicle.php?sid=<?php echo $row_v['vechile_id']; ?>" title="Edit">Edit</a></span>
                                        <span><a class="action-icons c-delete" href="transport_vechile_detail.php?sid=<?php echo $row_v['vechile_id']; ?>" title="Delete" onclick="return confirm('Delete this vehicle?')">Delete</a></span>
                                    </td>
                                </tr>
                                <?php 
                                    } 
                                } else { ?>
                                <tr>
                                    <td colspan="5" class="center" style="padding: 40px; color: #999;">No vehicles found.</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("includes/footer.php"); ?>
