<?php
declare(strict_types=1);

/**
 * ID 3.6: Edit Transport Vehicle
 * Fix: Standardized column mapping and removed SQL echos
 */

include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();
$sid = isset($_GET['sid']) ? db_escape($_GET['sid']) : '';

// 1. Process Update Logic
if(isset($_POST['submit'])) {
    $vechile_number = db_escape($_POST['vechile_number']);
    $seats = db_escape($_POST['seat']);
    $route_id_str = isset($_POST['route_id']) ? implode(",", $_POST['route_id']) : "";
    
    // Using standardized column names from the detail view
    $sql_update = "UPDATE transport_add_vechile SET vechile_no='$vechile_number', route_id='$route_id_str', no_of_seats='$seats' WHERE vechile_id='$sid'";
    
    if(db_query($sql_update)) {
        header("Location:transport_vechile_detail.php?msg=3");
        exit;
    } else {
        die("Update Error: " . db_error());
    }
}

// 2. Fetch Data (Echos removed)
$sql_fetch = "SELECT * FROM transport_add_vechile WHERE vechile_id='$sid'";
$res_fetch = db_query($sql_fetch);
$row = db_fetch_array($res_fetch);

// Logic to handle potential column name differences
$display_number = $row['vechile_no'] ?? $row['vechile_number'] ?? '';
$display_seats = $row['no_of_seats'] ?? $row['seat'] ?? '';
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
                    <h3 style="padding-left:20px; color:#0078D4">Edit Vehicle Detail</h3>
                    
                    <form action="#" method="post" class="form_container left_label">
                        <ul>
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title">Vehicle Number</label>
                                    <div class="form_input">
                                        <div class="form_grid_5 alpha">
                                            <input name="vechile_number" type="text" value="<?php echo htmlspecialchars((string)$display_number); ?>"/>
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
                                            $current_routes = explode(",", (string)$row['route_id']);
                                            $sql_r = "SELECT * FROM transport_add_route";
                                            $res_r = db_query($sql_r);
                                            while($row_r = db_fetch_array($res_r)) {
                                                $selected = in_array($row_r['route_id'], $current_routes) ? 'selected="selected"' : '';
                                                echo "<option $selected value='".$row_r['route_id']."'>".htmlspecialchars($row_r['route_name'])."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="form_grid_12">
                                    <label class="field_title">No of Seats</label>
                                    <div class="form_input">
                                        <div class="form_grid_5 alpha">
                                            <input name="seat" type="text" value="<?php echo htmlspecialchars((string)$display_seats); ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="form_grid_12">
                                    <div class="form_input">
                                        <button type="submit" name="submit" class="btn_small btn_blue"><span>Update</span></button>
                                        <a href="transport_vechile_detail.php" class="btn_small btn_orange" style="text-decoration:none; padding:7px 20px; display:inline-block;"><span>Back</span></a>
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

<?php include_once("includes/footer.php"); ?>
