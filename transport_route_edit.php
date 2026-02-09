<?php
declare(strict_types=1);

/**
 * ID 3.3: Edit Transport Route
 * Fix: Forced error reporting to reveal SQL/PHP crashes
 */

// 1. THE DEBUGGER: Force all errors to show
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

$conn = Database::connection();
$msg = "";
$sid = $_GET['sid'] ?? $_POST['route_id'] ?? ''; // Support both GET and POST for the ID

// 2. FORM UPDATE LOGIC
if(isset($_POST['submit'])) {
    $destination = mysqli_real_escape_string($conn, trim((string)$_POST['destination']));
    $cost = mysqli_real_escape_string($conn, trim((string)$_POST['cost']));
    $safe_sid = mysqli_real_escape_string($conn, (string)$sid);
    
    if(!empty($destination) && !empty($safe_sid)) {
        // Query check: Ensure table and columns match your DB
        $sql_update = "UPDATE transport_add_route SET route_name='$destination', cost='$cost' WHERE route_id='$safe_sid'";
        
        if(mysqli_query($conn, $sql_update)) {
            // Success: Redirect
            echo "<script>window.location='transport_route_detail.php?msg=3';</script>";
            exit;
        } else {
            // If query fails, this will now print the error instead of a blank page
            die("SQL Error: " . mysqli_error($conn) . " | Query: " . $sql_update);
        }
    } else {
        $msg = "<div class='alert fluent-danger'>Error: Missing Route ID or Destination.</div>";
    }
}

// 3. FETCH CURRENT DATA
$safe_sid = mysqli_real_escape_string($conn, (string)$sid);
$sql_fetch = "SELECT * FROM transport_add_route WHERE route_id='$safe_sid'";
$res_fetch = mysqli_query($conn, $sql_fetch);

if (!$res_fetch) {
    die("Database Query Failed: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($res_fetch);
?>

<style>
    #container, #content, .grid_container, .grid_12 { all: unset !important; display: block !important; }
    .transport-edit-layout {
        margin-left: 270px !important; 
        padding: 40px !important;
        background-color: #f8f9fa;
        min-height: 100vh;
    }
    .form-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 40px; max-width: 600px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .fluent-input { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 20px; box-sizing: border-box; }
    .fluent-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 15px; margin-bottom: 20px; }
</style>

<div class="transport-edit-layout">
    <div style="margin-bottom: 35px;">
        <?php include_once("includes/transport_setting_sidebar.php"); ?>
    </div>

    <div style="margin-bottom: 25px;">
        <h3 style="color: #0078D4; font-size: 26px; font-weight: 500; margin: 0;">Edit Route Detail</h3>
    </div>

    <?php if($msg != "") echo $msg; ?>

    <div class="form-card">
        <form action="transport_route_edit.php?sid=<?php echo $sid; ?>" method="post">
            <input type="hidden" name="route_id" value="<?php echo htmlspecialchars((string)$sid); ?>">
            
            <label style="display:block; font-weight:600; margin-bottom:8px;">Destination Name</label>
            <input name="destination" type="text" class="fluent-input" value="<?php echo htmlspecialchars($row['route_name'] ?? ''); ?>" required />

            <label style="display:block; font-weight:600; margin-bottom:8px;">Monthly Cost (₹)</label>
            <input name="cost" type="text" class="fluent-input" value="<?php echo htmlspecialchars($row['cost'] ?? ''); ?>" required />

            <div style="margin-top: 10px; display: flex; gap: 10px;">
                <button type="submit" name="submit" style="background: #0078D4; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Update Route</button>
                <a href="transport_route_detail.php" style="background: #f1f5f9; color: #475569; text-decoration: none; padding: 12px 30px; border-radius: 4px; border: 1px solid #e2e8f0; font-weight: 600;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once("includes/footer.php"); ?>
