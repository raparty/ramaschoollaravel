<?php
echo "<h1>ERP Code Logic Audit</h1>";
$files = glob("*.php"); // Scans all PHP files in the main folder

foreach ($files as $file) {
    if ($file == "code_audit.php" || $file == "db_audit.php") continue;

    $content = file_get_contents($file);
    
    // Pattern to find SQL queries in your code
    preg_match_all('/(SELECT|INSERT|UPDATE|DELETE).*FROM\s+([a-zA-Z0-9_]+)/i', $content, $matches);

    if (!empty($matches[0])) {
        echo "<div style='background:#f9f9f9; padding:10px; margin:10px; border:1px solid #ccc;'>";
        echo "<h3>Page: <span style='color:blue;'>$file</span></h3>";
        echo "<strong>Tables touched:</strong> " . implode(", ", array_unique($matches[2])) . "<br>";
        echo "<strong>SQL Queries found:</strong><ul>";
        foreach ($matches[0] as $query) {
            echo "<li><code>" . htmlspecialchars(substr($query, 0, 100)) . "...</code></li>";
        }
        echo "</ul></div>";
    }
}
?>
