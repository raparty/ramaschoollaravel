<?php
declare(strict_types=1);

/**
 * ID 2.2: Fee Student Search
 * Fix: Synchronized with 'admissions' table and corrected CSS distortion
 */
require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

$conn = Database::connection();
$search_results = [];
$searched = false;

// 1. Unified Search Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searched = true;
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $class_id = (int)($_POST['class'] ?? 0);
    $stream_id = (int)($_POST['stream'] ?? 0);

    // Dynamic Query Builder targeting 'admissions' table
    $sql = "SELECT a.*, c.class_name 
            FROM admissions a 
            LEFT JOIN classes c ON a.class_id = c.id 
            WHERE 1=1";

    if (!empty($name)) $sql .= " AND a.student_name LIKE '%$name%'";
    if ($class_id > 0) $sql .= " AND a.class_id = $class_id";
    if ($stream_id > 0) $sql .= " AND a.stream_id = $stream_id";

    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $search_results[] = $row;
    }
}
?>

<div style="margin-left: 280px !important; padding: 40px !important; display: block !important;">
    
    <div class="page_title" style="margin-bottom: 25px;">
        <h3 style="color: #1c75bc; font-size: 26px; font-weight: 400;">Fee Management: Student Search</h3>
    </div>

    <div class="azure-card" style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <form action="fees_searchby_name.php" method="post" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; align-items: end;">
            <div class="form_group">
                <label style="display:block; font-weight:600; margin-bottom:8px;">Student Name</label>
                <input name="name" type="text" class="form-control fluent-input" placeholder="Enter name...">
            </div>
            <div class="form_group">
                <label style="display:block; font-weight:600; margin-bottom:8px;">Class</label>
                <select name="class" class="form-control fluent-input">
                    <option value="">- All Classes -</option>
                    <?php
                    $classes = mysqli_query($conn, "SELECT * FROM classes");
                    while($c = mysqli_fetch_assoc($classes)) {
                        echo "<option value='{$c['id']}'>{$c['class_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form_group">
                <button type="submit" class="btn-fluent-primary" style="padding: 12px 30px; width: 100%;">Search Student</button>
            </div>
        </form>
    </div>

    <?php if ($searched): ?>
    <div class="azure-card" style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 0; overflow: hidden;">
        <div class="widget_top" style="background: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0;">
            <h6 style="margin:0; font-size:14px; color:#64748b; text-transform:uppercase;">Search Results</h6>
        </div>
        <table class="fluent-data-table" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f1f5f9; text-align: left;">
                    <th style="padding: 15px; border-bottom: 2px solid #e2e8f0;">Reg. No</th>
                    <th style="padding: 15px; border-bottom: 2px solid #e2e8f0;">Student Name</th>
                    <th style="padding: 15px; border-bottom: 2px solid #e2e8f0;">Class</th>
                    <th style="padding: 15px; border-bottom: 2px solid #e2e8f0;">Guardian</th>
                    <th style="padding: 15px; border-bottom: 2px solid #e2e8f0; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($search_results)): ?>
                    <tr><td colspan="5" style="padding: 40px; text-align: center; color: #ef4444; font-weight: 600;">No students found matching your criteria.</td></tr>
                <?php else: ?>
                    <?php foreach ($search_results as $row): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 15px; font-weight: 600; color: #0052a6;"><?php echo htmlspecialchars($row['reg_no']); ?></td>
                        <td style="padding: 15px;"><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td style="padding: 15px;"><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td style="padding: 15px;"><?php echo htmlspecialchars($row['guardian_name'] ?? 'N/A'); ?></td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="add_student_fees.php?registration_no=<?php echo urlencode($row['reg_no']); ?>" class="btn-fluent-primary" style="padding: 6px 20px; font-size: 12px; text-decoration: none;">Collect Fees</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once("includes/footer.php"); ?>
