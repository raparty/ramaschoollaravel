<?php
require_once("includes/bootstrap.php");

$sql = "SELECT * FROM admissions LIMIT 1";
$res = db_query($sql);

if ($res) {
    $data = db_fetch_array($res);
    echo "Connection Successful! Found Student: " . $data['student_name'];
} else {
    echo "Connection Failed: " . db_error();
}
?>
