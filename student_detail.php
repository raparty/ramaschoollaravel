<?php
declare(strict_types=1);

/**
 * ID 1.0: Student Directory Master List
 * Part of Group A: Admissions Management
 */
require_once("includes/bootstrap.php");
require_once("includes/header.php"); // Updated to require_once for shell integrity
require_once("includes/sidebar.php");

// Handle success messages from the process file
$msg = $_GET['msg'] ?? '';
$reg = $_GET['reg'] ?? '';
?>

<div class="dashboard-header-container">
    <h2 class="enterprise-title">Student Directory</h2>
    <div class="dashboard-search-wrapper">
        <form action="searchby_name.php" method="get" class="fluent-search-form">
            <div class="input-group shadow-sm">
                <input name="q" type="text" class="form-control fluent-input" placeholder="Search by name, registration number, or class...">
                <button type="submit" class="btn-azure-search">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="white"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                    <span>Search</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="grid_container">
    <?php if ($msg === 'success'): ?>
        <div class="alert fluent-alert-success shadow-sm mb-4">
            <strong>Success!</strong> Student admitted successfully. Registration: <strong><?php echo htmlspecialchars($reg); ?></strong>
        </div>
    <?php endif; ?>

    <div class="widget_wrap azure-card">
        <div class="widget_top">
            <h6 class="fluent-card-header">Enrolled Students</h6>
            <div class="widget_actions">
                <a href="add_admission.php" class="btn-fluent-primary">+ New Admission</a>
            </div>
        </div>
        <div class="widget_content">
            <table class="display data_tbl fluent-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Photo</th>
                        <th style="width: 120px;">Reg. No</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Gender</th>
                        <th style="width: 140px;">Admission Date</th>
                        <th style="width: 180px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Main SQL Logic Preserved
                    $sql = "SELECT a.*, c.class_name 
                            FROM admissions a 
                            LEFT JOIN classes c ON a.class_id = c.id 
                            ORDER BY a.id DESC";
                    $res = db_query($sql);
                    
                    while($row = db_fetch_array($res)) { 
                        $photo = !empty($row['student_pic']) ? $row['student_pic'] : 'assets/images/no-photo.png';
                    ?>		
                    <tr>
                        <td class="center">
                            <img src="<?php echo $photo; ?>" alt="Student" class="fluent-table-avatar">
                        </td>
                        <td class="center"><span class="fluent-badge-outline"><?php echo htmlspecialchars($row['reg_no']); ?></span></td>
                        <td class="font-weight-bold"><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td class="center"><?php echo htmlspecialchars($row['class_name'] ?? 'N/A'); ?></td>
                        <td class="center"><?php echo $row['gender']; ?></td>
                        <td class="center"><?php echo date('d-M-Y', strtotime($row['admission_date'])); ?></td>
                        <td class="center">
                            <div class="fluent-action-group">
                                <a href="view_student_detail.php?student_id=<?php echo $row['id']; ?>" class="fluent-btn-icon" title="View Full Profile">
                                    <svg viewBox="0 0 24 24" width="18" height="18"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                </a>
                                <a href="delete_admission.php?sid=<?php echo $row['id']; ?>" class="fluent-btn-icon icon-delete" onclick="return confirm('Are you sure you want to delete this record?')" title="Delete Student">
                                    <svg viewBox="0 0 24 24" width="18" height="18"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* 1. Header & Artifact Cleanup */
    .title_icon, .users_mm { display: none !important; }
    
    /* 2. Azure Grade Table Styling */
    .fluent-table-avatar {
        width: 36px; height: 36px; border-radius: 50%; 
        object-fit: cover; border: 1px solid var(--app-border);
    }
    .fluent-badge-outline {
        background: #f3f4f6; color: var(--fluent-slate); 
        padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;
    }
    .fluent-action-group { display: flex; gap: 10px; justify-content: center; }
    .fluent-btn-icon {
        width: 32px; height: 32px; display: flex; align-items: center; 
        justify-content: center; border-radius: 4px; transition: 0.2s;
    }
    .fluent-btn-icon svg { fill: var(--app-primary); }
    .fluent-btn-icon:hover { background: #eff6fc; }
    .fluent-btn-icon.icon-delete svg { fill: #d1d5db; }
    .fluent-btn-icon.icon-delete:hover { background: #fef2f2; }
    .fluent-btn-icon.icon-delete:hover svg { fill: #ef4444; }

    /* Centered Search Bar UI */
    .dashboard-header-container { text-align: center; padding: 40px 20px; border-bottom: 1px solid var(--app-border); margin-bottom: 30px; }
    .enterprise-title { font-weight: 300; font-size: 28px; color: var(--fluent-slate); margin-bottom: 20px; }
    .dashboard-search-wrapper { max-width: 550px; margin: 0 auto; }
</style>

<?php require_once("includes/footer.php"); ?>
