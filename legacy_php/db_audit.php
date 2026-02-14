<?php
// Force errors to show so we don't get a blank page
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Use your existing ERP connection logic
require_once("includes/bootstrap.php");
$conn = Database::connection();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<html><body style='font-family:sans-serif; padding:20px;'>";
echo "<h1 style='color:#0078D4;'>School ERP: Database Schema Audit</h1>";

$tables = mysqli_query($conn, "SHOW TABLES");

while($table = mysqli_fetch_array($tables)) {
    $tableName = $table[0];
    echo "<div style='background:#f4f4f4; margin-bottom:20px; padding:15px; border-left:5px solid #0078D4;'>";
    echo "<h3>Table: <span style='color:#d40000;'>$tableName</span></h3>";
    
    $columns = mysqli_query($conn, "SHOW COLUMNS FROM $tableName");
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse; background:white; width:100%;'>";
    echo "<tr style='background:#eee;'><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    while($col = mysqli_fetch_assoc($columns)) {
        echo "<tr>
                <td><strong>{$col['Field']}</strong></td>
                <td>{$col['Type']}</td>
                <td>{$col['Null']}</td>
                <td>{$col['Key']}</td>
                <td>{$col['Default']}</td>
              </tr>";
    }
    echo "</table></div>";
}
echo "</body></html>";
?>
