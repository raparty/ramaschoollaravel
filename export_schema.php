<?php
require_once("includes/bootstrap.php");
$conn = Database::connection();

// Define the absolute path and filename
$directory = '/var/www/html/school_erp/db';
$filename = 'school_erp_schema_audit.sql';
$filepath = $directory . '/' . $filename;

// Check if directory exists, if not, try to create it
if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
}

$output = "-- School ERP Schema Export\n";
$output .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";

$tables = mysqli_query($conn, "SHOW TABLES");

while ($row = mysqli_fetch_row($tables)) {
    $table = $row[0];
    
    // Get the exact CREATE TABLE statement
    $createTable = mysqli_query($conn, "SHOW CREATE TABLE `$table` ");
    $tableDetail = mysqli_fetch_row($createTable);
    
    $output .= "--- Table: $table ---\n";
    $output .= $tableDetail[1] . ";\n\n";
}

// Write the content to the file
if (file_put_contents($filepath, $output)) {
    echo "Success: Schema exported to " . htmlspecialchars($filepath);
} else {
    echo "Error: Could not write file. Check folder permissions for " . htmlspecialchars($directory);
}
?>
