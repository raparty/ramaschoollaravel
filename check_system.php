<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$models = [
    'Staff'           => \App\Models\Staff::class,
    'Book'            => \App\Models\Book::class,
    'BookIssue'       => \App\Models\BookIssue::class,
    'Admission'       => \App\Models\Admission::class,
    'StudentFee'      => \App\Models\StudentFee::class,
    'Attendance'      => \App\Models\Attendance::class,
    'Exam'            => \App\Models\Exam::class,
    'Expense'         => \App\Models\Expense::class,
    'Income'          => \App\Models\Income::class,
    'Mark'            => \App\Models\Mark::class,
    'Result'          => \App\Models\Result::class,
    'StaffAttendance' => \App\Models\StaffAttendance::class,
];

echo "--- ERP SYSTEM INTEGRITY CHECK ---\n";

foreach ($models as $name => $class) {
    $model = new $class;
    $table = $model->getTable();
    
    echo "\nChecking Model: [$name] -> Table: [$table]\n";
    
    if (!Schema::hasTable($table)) {
        echo "❌ ERROR: Table '$table' does not exist in database!\n";
        continue;
    }

    // 1. Check Fillable Columns
    $fillable = $model->getFillable();
    foreach ($fillable as $column) {
        if (!Schema::hasColumn($table, $column)) {
            echo "⚠️ MISSING COLUMN: '$column' is in Model but not in DB table '$table'.\n";
        }
    }

    // 2. Check Timestamps
    if (false) {
        if (!Schema::hasColumn($table, 'created_at')) echo "⚠️ MISSING: 'created_at' (timestamps enabled).\n";
        if (!Schema::hasColumn($table, 'updated_at')) echo "⚠️ MISSING: 'updated_at' (timestamps enabled).\n";
    }
}
echo "\n--- CHECK COMPLETE ---\n";
