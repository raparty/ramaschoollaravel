<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();
$session = mysqli_real_escape_string($conn, (string)($_SESSION['session'] ?? ''));

// Fetch summary data for the Root Hub
$sql_total = "SELECT SUM(fine_amount) as total FROM student_fine_detail WHERE session = '$session'";
$res_total = mysqli_query($conn, $sql_total);
$fine_data = mysqli_fetch_assoc($res_total);
$display_total = $fine_data['total'] ?? 0;
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <h3 style="padding:15px 0 0 20px; color:#0078D4">Library Management / Fines Hub</h3>
            
            <?php include_once("includes/library_setting_sidebar.php"); ?>

            <div class="grid_12">
                <div class="widget_wrap azure-card">
                    <div class="widget_top">
                        <h6 class="fluent-card-header">Fines Management (Root)</h6>
                    </div>
                    <div class="widget_content" style="padding: 30px;">
                        
                        <div style="background: #f0f7ff; border: 1px solid #0078D4; border-radius: 12px; padding: 25px; margin-bottom: 30px; display: flex; align-items: center; gap: 40px;">
                            <div style="font-size: 48px;"></div>
                            <div>
                                <p style="margin:0; color: #64748b; font-size: 13px; font-weight: 700; text-transform: uppercase;">Total Fine Revenue (Session: <?php echo htmlspecialchars($session); ?>)</p>
                                <h1 style="margin:5px 0 0; color: #0078D4; font-size: 36px;">₹ <?php echo number_format((float)$display_total, 2); ?></h1>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                            
                            <a href="student_fine_detail.php" class="hub-action-card">
                                <div class="hub-card-icon">📊</div>
                                <div class="hub-card-text">
                                    <strong>Fine History & Reports</strong>
                                    <span>View the full ledger of all student penalties.</span>
                                </div>
                            </a>

                            <a href="library_student_fine_entry.php" class="hub-action-card">
                                <div class="hub-card-icon">✍️</div>
                                <div class="hub-card-text">
                                    <strong>Manual Fine Entry</strong>
                                    <span>Manually record fines for lost/damaged books.</span>
                                </div>
                            </a>

                            <a href="library_fine_manager.php" class="hub-action-card">
                                <div class="hub-card-icon">⚙️</div>
                                <div class="hub-card-text">
                                    <strong>Rate Settings</strong>
                                    <span>Update per-day fine rates and grace periods.</span>
                                </div>
                            </a>

                            <a href="library_entry_student_return_books.php" class="hub-action-card">
                                <div class="hub-card-icon">🔄</div>
                                <div class="hub-card-text">
                                    <strong>Return Book Logic</strong>
                                    <span>Verify students and auto-calculate late fees.</span>
                                </div>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hub-action-card {
    display: flex; gap: 15px; padding: 20px;
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
    text-decoration: none; transition: 0.3s ease;
}
.hub-action-card:hover {
    border-color: #0078D4; background: #f8fafc; transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.hub-card-icon { font-size: 28px; }
.hub-card-text strong { display: block; color: #0078D4; margin-bottom: 4px; }
.hub-card-text span { font-size: 11px; color: #64748b; line-height: 1.4; }
</style>

<?php include_once("includes/footer.php"); ?>
