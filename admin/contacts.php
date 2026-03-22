<?php
/**
 * Admin Contact Submissions Page
 * View and manage contact form submissions
 */

// Include database configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/Katkar_New/config/db.php';

// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle status update
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);
    $db->query("UPDATE contact_submissions SET status = 'read' WHERE id = $id");
    header('Location: contacts.php');
    exit;
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $db->query("DELETE FROM contact_submissions WHERE id = $id");
    header('Location: contacts.php?deleted=1');
    exit;
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT * FROM contact_submissions WHERE 1=1";

if (!empty($status_filter)) {
    $safe_status = $db->escape($status_filter);
    $query .= " AND status = '$safe_status'";
}

if (!empty($search)) {
    $safe_search = $db->escape($search);
    $query .= " AND (name LIKE '%$safe_search%' OR phone LIKE '%$safe_search%' OR email LIKE '%$safe_search%' OR message LIKE '%$safe_search%')";
}

$query .= " ORDER BY created_at DESC";

// Get submissions
$submissions = [];
$result = $db->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $submissions[] = $row;
    }
}

// Get counts
$new_count = 0;
$read_count = 0;
$total_count = 0;

$count_result = $db->query("SELECT status, COUNT(*) as count FROM contact_submissions GROUP BY status");
if ($count_result) {
    while ($row = $count_result->fetch_assoc()) {
        if ($row['status'] === 'new') $new_count = $row['count'];
        if ($row['status'] === 'read') $read_count = $row['count'];
        $total_count += $row['count'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Submissions - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <!-- Admin Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-seedling me-2"></i>Admin Panel
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">
                                <i class="fas fa-box me-2"></i>Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">
                                <i class="fas fa-shopping-cart me-2"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="customers.php">
                                <i class="fas fa-users me-2"></i>Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="contacts.php">
                                <i class="fas fa-envelope me-2"></i>Contact Forms
                                <?php if ($new_count > 0): ?>
                                    <span class="badge bg-danger ms-2"><?php echo $new_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">
                                <i class="fas fa-chart-bar me-2"></i>Reports
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link text-success" href="../index.php" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>Back to Website
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Contact Form Submissions</h1>
                </div>

                <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>Submission deleted successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h3 class="text-danger"><?php echo $new_count; ?></h3>
                                <small class="text-muted">New Submissions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h3 class="text-success"><?php echo $read_count; ?></h3>
                                <small class="text-muted">Read Submissions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h3 class="text-primary"><?php echo $total_count; ?></h3>
                                <small class="text-muted">Total Submissions</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter and Search -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="search" class="form-control" placeholder="Search by name, phone, email..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Submissions Table -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Contact Submissions</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($submissions)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5>No submissions found</h5>
                                <p class="text-muted">
                                    <?php if (!empty($search) || !empty($status_filter)): ?>
                                        Try adjusting your search criteria.
                                    <?php else: ?>
                                        Contact form submissions will appear here.
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th>Subject</th>
                                            <th>Message</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submissions as $submission): ?>
                                        <tr class="<?php echo $submission['status'] === 'new' ? 'table-warning' : ''; ?>">
                                            <td>#<?php echo $submission['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($submission['name']); ?></strong>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($submission['phone']); ?><br>
                                                    <?php if (!empty($submission['email'])): ?>
                                                        <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($submission['email']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                            <td><?php echo htmlspecialchars($submission['subject'] ?: 'No Subject'); ?></td>
                                            <td>
                                                <small><?php echo htmlspecialchars(substr($submission['message'], 0, 100)) . (strlen($submission['message']) > 100 ? '...' : ''); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $submission['status'] === 'new' ? 'warning' : 'success'; ?>">
                                                    <?php echo ucfirst($submission['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?php echo date('d M Y H:i', strtotime($submission['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <?php if ($submission['status'] === 'new'): ?>
                                                        <a href="contacts.php?mark_read=<?php echo $submission['id']; ?>" class="btn btn-outline-success" title="Mark as Read">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="https://wa.me/91<?php echo $submission['phone']; ?>?text=<?php echo urlencode('Hello ' . $submission['name'] . ', thank you for contacting Adhunik Krushi Bhandar. How can we help you?'); ?>" 
                                                       target="_blank" class="btn btn-outline-success" title="Reply via WhatsApp">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                    <a href="tel:<?php echo $submission['phone']; ?>" class="btn btn-outline-primary" title="Call Customer">
                                                        <i class="fas fa-phone"></i>
                                                    </a>
                                                    <a href="contacts.php?delete=<?php echo $submission['id']; ?>" 
                                                       class="btn btn-outline-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this submission?')"
                                                       title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
