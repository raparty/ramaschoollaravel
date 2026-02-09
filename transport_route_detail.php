<?php
declare(strict_types=1);

/**
 * ID 3.1: Transport Route Details
 * Fix: Final alignment correction for sidebar and header icons
 */

require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

$conn = Database::connection();
?>

<style>
    /* Kill legacy CSS that creates ghost spacing and centering */
    #container, #content, .grid_container, .grid_12 { 
        all: unset !important; 
        display: block !important; 
        width: auto !important;
    }

    .transport-main-layout {
        margin-left: 270px !important; /* Fixed Sidebar Width */
        padding: 30px 50px !important;
        background-color: #f8f9fa;
        min-height: 100vh;
        box-sizing: border-box !important;
    }

    /* Modern Table Card */
    .route-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        margin-top: 20px;
        overflow: hidden;
    }

    .route-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .fluent-table { width: 100%; border-collapse: collapse; }
    .fluent-table th { background: #f1f5f9; padding: 15px 20px; text-align: left; color: #475569; font-size: 13px; text-transform: uppercase; }
    .fluent-table td { padding: 18px 20px; border-top: 1px solid #f1f5f9; color: #1e293b; font-size: 15px; }
    
    .btn-add {
        background: #0078d4;
        color: white;
        text-decoration: none;
        padding: 10px 25px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 14px;
        transition: background 0.2s;
    }
    .btn-add:hover { background: #005a9e; }
</style>

<div class="transport-main-layout">
    
    <div style="margin-bottom: 40px;">
        <?php include_once("includes/transport_setting_sidebar.php"); ?>
    </div>

    <div class="route-header">
        <h3 style="font-size: 26px; color: #1e293b; font-weight: 500; margin: 0;">Route Detail</h3>
        <a href="transport_add_route.php" class="btn-add">+ Add New Route</a>
    </div>

    <div class="route-card">
        <table class="fluent-table">
            <thead>
                <tr>
                    <th style="width: 80px;">S.No.</th>
                    <th>Destination</th>
                    <th>Cost (Monthly)</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT * FROM transport_add_route";
                $res = mysqli_query($conn, $sql);
                $i = 1;
                
                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) { ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td style="font-weight: 500;"><?php echo htmlspecialchars($row['route_name']); ?></td>
                        <td><span style="color: #16a34a; font-weight: 600;">₹<?php echo number_format((float)$row['cost'], 2); ?></span></td>
                        <td style="text-align: center;">
                            <a href="transport_route_edit.php?sid=<?php echo $row['route_id']; ?>" style="color: #0078d4; text-decoration: none; margin-right: 15px;">Edit</a>
                            <a href="transport_route_detail.php?sid=<?php echo $row['route_id']; ?>" style="color: #dc2626; text-decoration: none;" onclick="return confirm('Delete this route?')">Delete</a>
                        </td>
                    </tr>
                <?php } 
                } else { ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 60px; color: #64748b;">
                            <div style="font-size: 16px; margin-bottom: 10px;">No routes found.</div>
                            <div style="font-size: 13px;">Click "+ Add New Route" to set up your first transportation destination.</div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once("includes/footer.php"); ?>
