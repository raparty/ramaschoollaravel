<?php
declare(strict_types=1);

/**
 * ID 3.2: Add Transport Route
 * Fix: Added Debugger and standardized DB connection to fix blank page
 */

// 1. THE DEBUGGER: Force errors to show on screen
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

$conn = Database::connection(); // Standardized connection
$msg = "";

// 2. FORM PROCESSING LOGIC
if(isset($_POST['submit'])) {
    // Sanitize inputs
    $destination = mysqli_real_escape_string($conn, trim((string)($_POST['destination'] ?? '')));
    $cost = mysqli_real_escape_string($conn, trim((string)($_POST['cost'] ?? '0')));
    
    if(!empty($destination)) {
        // Check if table exists to prevent crash
        $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'transport_add_route'");
        if(mysqli_num_rows($table_check) == 0) {
            die("Critical Error: Table 'transport_add_route' does not exist in the database.");
        }

        // Check for duplicates
        $check = mysqli_query($conn, "SELECT * FROM transport_add_route WHERE route_name='$destination'");
        if (!$check) { die("Query Error: " . mysqli_error($conn)); }

        if(mysqli_num_rows($check) == 0) {
            $sql = "INSERT INTO transport_add_route(route_name, cost) VALUES ('$destination', '$cost')";
            if(mysqli_query($conn, $sql)) {
                // Success: Redirect back to the list
                echo "<script>window.location='transport_route_detail.php?msg=1';</script>";
                exit;
            } else {
                die("Insert Error: " . mysqli_error($conn));
            }
        } else {
            $msg = "<div class='alert fluent-danger'>This route destination already exists.</div>";
        }
    } else {
        $msg = "<div class='alert fluent-danger'>Please enter a destination name.</div>";
    }
}
?>

<style>
    #container, #content, .grid_container, .grid_12 { all: unset !important; display: block !important; }
    .transport-form-layout {
        margin-left: 270px !important; 
        padding: 40px !important;
        background-color: #f8f9fa;
        min-height: 100vh;
    }
    .form-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 40px; max-width: 600px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .fluent-input { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 20px; }
    .fluent-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 15px; margin-bottom: 20px; }
</style>

<div class="transport-form-layout">
    <div style="margin-bottom: 35px;">
        <?php include_once("includes/transport_setting_sidebar.php"); ?>
    </div>

    <div style="margin-bottom: 25px;">
        <h3 style="color: #0078D4; font-size: 26px; font-weight: 500;">Add New Route</h3>
    </div>

    <?php if($msg != "") echo $msg; ?>

    <div class="form-card">
        <form action="transport_add_route.php" method="post">
            <label style="display:block; font-weight:600; margin-bottom:8px;">Destination Name</label>
            <input name="destination" type="text" class="fluent-input" placeholder="e.g. Downtown" required />

            <label style="display:block; font-weight:600; margin-bottom:8px;">Monthly Cost (₹)</label>
            <input name="cost" type="number" step="0.01" class="fluent-input" placeholder="0.00" required />

            <div style="margin-top: 10px; display: flex; gap: 10px;">
                <button type="submit" name="submit" style="background: #0078D4; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Save Route</button>
                <a href="transport_route_detail.php" style="background: #f1f5f9; color: #475569; text-decoration: none; padding: 12px 30px; border-radius: 4px; border: 1px solid #e2e8f0;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once("includes/footer.php"); ?>
