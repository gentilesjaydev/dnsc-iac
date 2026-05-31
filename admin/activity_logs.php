<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Enforce admin access only
requireAdmin();

// Pagination setup
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Fetch total count for pagination
$totalStmt = $pdo->query("SELECT COUNT(*) FROM activity_logs");
$totalLogs = $totalStmt->fetchColumn();
$totalPages = ceil($totalLogs / $limit);

// Fetch logs with user details
$stmt = $pdo->prepare("SELECT l.*, u.full_name, u.role 
                       FROM activity_logs l 
                       LEFT JOIN users u ON l.user_id = u.id 
                       ORDER BY l.created_at DESC 
                       LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - Admin IAC Covenant</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/iclogo.png" type="image/x-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime('../assets/css/style.css'); ?>">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Outfit Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <!-- Mobile Toggle -->
    <div
        class="admin-mobile-nav d-lg-none bg-white p-3 border-bottom sticky-top d-flex justify-content-between align-items-center">
        <div class="fw-bold fw-bold" style="font-family: 'Outfit';">IAC <span class="text-primary">Covenant</span></div>
        <button class="btn btn-light border" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-logo d-flex align-items-center gap-2 mb-4">
            <img src="../assets/images/dnsclogo.png" alt="DNSC" style="height: 35px;">
            <img src="../assets/images/iclogo.png" alt="IC" style="height: 42px; margin-top: -2px;">
            <div class="ms-1">
                <span style="color: #5b21b6;">IAC</span> <span style="color: var(--accent-core);">Covenant</span>
            </div>
        </div>
        <ul class="nav nav-admin flex-column">
            <li class="nav-item">
                <a href="dashboard" class="nav-link"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="attendance" class="nav-link"><i class="fa-solid fa-user-check"></i> Attendance</a>
            </li>
            <li class="nav-item">
                <a href="submissions" class="nav-link"><i class="fa-solid fa-file-signature"></i> Submissions</a>
            </li>
            <li class="nav-item">
                <a href="activity_logs" class="nav-link active"><i class="fa-solid fa-list-ul"></i> Activity
                    Logs</a>
            </li>
            <li class="nav-item">
                <a href="edit_profile" class="nav-link"><i class="fa-solid fa-user-cog"></i> Profile</a>
            </li>
            <li class="nav-item">
                <a href="../covenant" class="nav-link" target="_blank"><i class="fa-solid fa-file-contract"></i>
                    Signing Form</a>
            </li>
            <li class="nav-item">
                <a href="../signature-wall" class="nav-link" target="_blank"><i class="fa-solid fa-external-link"></i>
                    Live Wall</a>
            </li>
        </ul>
        <div style="margin-top: auto; padding-top: 2rem; border-top: 1px solid #f1f5f9;">
            <a href="#" onclick="confirmLogout(event)" class="nav-link text-danger fw-bold">
                <i class="fa-solid fa-power-off"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="admin-content">
        <div class="mb-4">
            <h3 style="font-family: 'Outfit'; font-weight: 700; color: var(--accent-deep);">System Activity Logs</h3>
            <p class="text-muted">Monitor logins, signatures, and system actions with IP tracking.</p>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Activity</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="small">
                                    <div class="fw-bold">
                                        <?php echo date('M d, Y', strtotime($log['created_at'])); ?>
                                    </div>
                                    <div class="text-muted">
                                        <?php echo date('h:i:s A', strtotime($log['created_at'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">
                                        <?php
                                        if ($log['full_name']) {
                                            echo htmlspecialchars($log['full_name']);
                                        } elseif ($log['guest_name']) {
                                            echo htmlspecialchars($log['guest_name']);
                                        } else {
                                            echo 'System/Unknown';
                                        }
                                        ?>
                                    </div>
                                    <div class="small text-muted text-uppercase">
                                        <?php echo $log['role'] ?? 'GUEST'; ?>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge-log <?php echo ($log['activity_type'] === 'login') ? 'badge-login' : 'badge-covenant'; ?>">
                                        <?php echo str_replace('_', ' ', $log['activity_type']); ?>
                                    </span>
                                </td>
                                <td class="small">
                                    <?php echo htmlspecialchars($log['description']); ?>
                                </td>
                                <td>
                                    <span class="ip-text">
                                        <?php echo $log['ip_address']; ?>
                                    </span>
                                    <div style="font-size: 0.65rem; color: #94a3b8; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                        title="<?php echo htmlspecialchars($log['user_agent']); ?>">
                                        <?php echo htmlspecialchars($log['user_agent']); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No activity logs recorded yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            $('#adminSidebar').toggleClass('show');
            $('#sidebarOverlay').toggleClass('show');
        }

        function confirmLogout(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Sign Out?',
                text: "Are you sure you want to end your current session?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Sign Out'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../logout';
                }
            });
        }
    </script>
</body>

</html>




