<?php
declare(strict_types=1);

/**
 * ID 3.5: Add Transport Vehicle
 * Logic: Fixed SQL 'Blank Page' crash and enabled Route Dropdown
 */

// 1. DEBUGGER: Force errors to display instead of a blank page
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

$conn = Database::connection();
$msg = "";

// 2. FORM PROCESSING LOGIC
if(isset($_POST['submit'])) {
    $vechile_no = mysqli_real_escape_string($conn, trim((string)$_POST['number']));
    $seats = (int)($_POST['seat'] ?? 0);
    
    // Convert multiple selected routes into a comma-separated string
    $route_ids = isset($_POST['route_id']) ? implode(",", $_POST['route_id']) : "";

    if(!empty($vechile_no)) {
        // Check if vehicle already exists using correct column name
        $check_sql = "SELECT * FROM transport_add_vechile WHERE vechile_no = '$vechile_no'";
        $check_res = mysqli_query($conn, $check_sql);
        
        if($check_res && mysqli_num_rows($check_res) == 0) {
            // INSERT using columns verified in detail view
            $sql_ins = "INSERT INTO transport_add_vechile (vechile_no, route_id, no_of_seats) 
                        VALUES ('$vechile_no', '$route_ids', '$seats')";
            
            if(mysqli_query($conn, $sql_ins)) {
                echo "<script>window.location='transport_vechile_detail.php?msg=1';</script>";
                exit;
            } else {
                // This will print the exact SQL error if the insert fails
                die("SQL Insert Error: " . mysqli_error($conn));
            }
        } else {
            $msg = "<span style='color:#FF0000;'><h4>Vehicle Number already exists.</h4></span>";
        }
    } else {
        $msg = "<span style='color:#FF0000;'><h4>Please fill in the Vehicle Number.</h4></span>";
    }
}
?>

<div class="page_title">
    <div class="top_search">
        <form action="#" method="post">
            <ul id="search_box">
                <li><input name="" type="text" class="search_input" id="suggest1" placeholder="Search..."></li>
                <li><input name="" type="submit" value="Search" class="search_btn"></li>
            </ul>
        </form>
    </div>
</div>
<?php include_once("includes/transport_setting_sidebar.php"); ?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <div class="grid_12">
                <div class="widget_wrap">
                    <h3 style="padding-left:20px; color:#0078D4">Add New Vehicle</h3>
                     <?php if($msg != "") echo $msg; ?>
                    
                    <form action="transport_add_vechile.php" method="post" class="form_container left_label">
                        <ul>
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title">Vehicle Number</label>
                                    <div class="form_input">
                                        <div class="form_grid_5 alpha">
                                            <input name="number" type="text" required />
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title">Select Route(s)</label>
                                    <div class="form_input">
                                        <select name="route_id[]" style="width:300px;" multiple class="chzn-select">
                                            <?php 
                                            $sql_r = "SELECT * FROM transport_add_route ORDER BY route_name ASC";
                                            $res_r = mysqli_query($conn, $sql_r);
                                            while($row_r = mysqli_fetch_assoc($res_r)) {
                                                echo "<option value='{$row_r['route_id']}'>".htmlspecialchars($row_r['route_name'])."</option>";
                                            }
                                            ?>
                                        </select>
                                        <span class="label_intro">Hold Ctrl (or Cmd) to select multiple routes.</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title">No of Seats</label>
                                    <div class="form_input">
                                        <div class="form_grid_5 alpha">
                                            <input name="seat" type="number" />
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="form_grid_12">
                                    <div class="form_input">
                                        <button type="submit" name="submit" class="btn_small btn_blue"><span>Save Vehicle</span></button>
                                        <a href="transport_vechile_detail.php" class="btn_small btn_orange" style="text-decoration:none; padding: 7px 20px;">Back</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once("includes/footer.php"); ?>
