<?php
declare(strict_types=1);

/**
 * ID 3.4: Transport Vehicle Details
 * Logic Fix: Corrected delete target and route-name resolution
 */

require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

$conn = Database::connection();

// 1. Logic Fix: Delete from vehicle table, not route table
if(isset($_GET['sid']) && $_GET['sid'] != '') {
    $sid = mysqli_real_escape_string($conn, $_GET['sid']);
    mysqli_query($conn, "DELETE FROM transport_add_vechile WHERE vechile_id='$sid'");
    header("Location: transport_vechile_detail.php?msg=deleted");
    exit;
}
?>

<div class="vehicle-main-layout" style="margin-left: 270px; padding: 30px 50px;">
    
    <div style="margin-bottom: 40px;">
        <?php include_once("includes/transport_setting_sidebar.php"); ?>
    </div>

    <div class="vehicle-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h3 style="font-size: 26px; color: #1e293b; font-weight: 500; margin: 0;">Vehicle Detail</h3>
        <a href="transport_add_vechile.php" style="background: #0078d4; color: white; text-decoration: none; padding: 10px 25px; border-radius: 4px; font-weight: 600; font-size: 14px;">
            + Add New Vehicle
        </a>
    </div>

    <div class="vehicle-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="padding: 15px; text-align: left; font-size: 13px; color: #475569;">S.No.</th>
                    <th style="padding: 15px; text-align: left; font-size: 13px; color: #475569;">Vehicle Number</th>
                    <th style="padding: 15px; text-align: left; font-size: 13px; color: #475569;">Destination(s)</th>
                    <th style="padding: 15px; text-align: center; font-size: 13px; color: #475569;">No of Seats</th>
                    <th style="padding: 15px; text-align: center; font-size: 13px; color: #475569;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 0;
                $sql = "SELECT * FROM transport_add_vechile";
                $res = mysqli_query($conn, $sql);
                
                if(mysqli_num_rows($res) > 0) {
                    while($row_v = mysqli_fetch_assoc($res)) {
                        $i++;
                        
                        // 2. Logic Fix: Resolve comma-separated route IDs to Names
                        $route_ids = explode(",", (string)$row_v['route_id']);
                        $route_names = [];
                        foreach($route_ids as $id) {
                            $id = trim($id);
                            if(!empty($id)) {
                                $res_r = mysqli_query($conn, "SELECT route_name FROM transport_add_route WHERE route_id='$id'");
                                if($r_data = mysqli_fetch_assoc($res_r)) {
                                    $route_names[] = $r_data['route_name'];
                                }
                            }
                        }
                        $display_routes = !empty($route_names) ? implode(", ", $route_names) : "None";
                ?>
                <tr style="border-top: 1px solid #f1f5f9;">
                    <td style="padding: 15px;"><?php echo $i; ?></td>
                    <td style="padding: 15px; font-weight: 600; color: #0052a6;"><?php echo htmlspecialchars((string)$row_v['vechile_no']); ?></td>
                    <td style="padding: 15px; color: #f04508; font-size: 13px;"><?php echo htmlspecialchars($display_routes); ?></td>
                    <td style="padding: 15px; text-align: center; font-weight: 600;"><?php echo htmlspecialchars((string)$row_v['no_of_seats']); ?></td>
                    <td style="padding: 15px; text-align: center;">
                        <a href="transport_edit_vehicle.php?sid=<?php echo $row_v['vechile_id']; ?>" style="color: #0078d4; text-decoration: none; margin-right: 15px;">Edit</a>
                        <a href="transport_vechile_detail.php?sid=<?php echo $row_v['vechile_id']; ?>" style="color: #dc2626; text-decoration: none;" onclick="return confirm('Delete this vehicle?')">Delete</a>
                    </td>
                </tr>
                <?php 
                    } 
                } else { ?>
                <tr>
                    <td colspan="5" style="padding: 50px; text-align: center; color: #94a3b8;">No vehicle data available.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once("includes/footer.php"); ?>
