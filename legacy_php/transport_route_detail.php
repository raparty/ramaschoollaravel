<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();

// Handle route deletion
if(isset($_GET['sid']) && $_GET['sid'] != '') {
    $sid = mysqli_real_escape_string($conn, $_GET['sid']);
    mysqli_query($conn, "DELETE FROM transport_add_route WHERE route_id='$sid'");
    header("Location: transport_route_detail.php?msg=2");
    exit;
}

$msg = "";
if(isset($_GET['msg'])) {
    if($_GET['msg'] == 1) {
        $msg = "<div class='alert alert-success'>Route added successfully</div>";
    } else if($_GET['msg'] == 2) {
        $msg = "<div class='alert alert-success'>Route deleted successfully</div>";
    } else if($_GET['msg'] == 3) {
        $msg = "<div class='alert alert-success'>Route updated successfully</div>";
    }
}
?>

<div class="page_title">
    <h3>Route Detail</h3>
</div>

<?php include_once("includes/transport_setting_sidebar.php"); ?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <div class="grid_12">
                <div class="widget_wrap enterprise-card">
                    <div class="widget_top d-flex justify-content-between align-items-center">
                        <h6>Transport Routes</h6>
                        <a href="transport_add_route.php" class="btn-fluent-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:6px;">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" fill="currentColor"/>
                            </svg>
                            Add New Route
                        </a>
                    </div>
                    <div class="widget_content">
                        <?php if($msg != "") echo $msg; ?>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                    $sql = "SELECT * FROM transport_add_route ORDER BY route_name ASC";
                                    $res = mysqli_query($conn, $sql);
                                    $i = 1;
                                    
                                    if(mysqli_num_rows($res) > 0) {
                                        while($row = mysqli_fetch_assoc($res)) { ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($row['route_name']); ?></td>
                                            <td><span class="text-success fw-bold">₹<?php echo number_format((float)$row['cost'], 2); ?></span></td>
                                            <td style="text-align: center;">
                                                <a href="transport_route_edit.php?sid=<?php echo $row['route_id']; ?>" class="btn btn-sm btn-primary me-2">Edit</a>
                                                <a href="transport_route_detail.php?sid=<?php echo $row['route_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this route?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php } 
                                    } else { ?>
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 60px; color: #64748b;">
                                                <div style="font-size: 16px; margin-bottom: 10px;">No routes found.</div>
                                                <div style="font-size: 13px;">Click "Add New Route" to set up your first transportation destination.</div>
                                            </td>
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
</div>

<?php require_once("includes/footer.php"); ?>
