<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Enforce admin access only
requireAdmin();

// Handle Status Toggle
if (isset($_GET['toggle_status'])) {
    $id = (int)$_GET['toggle_status'];
    $current = $_GET['current'];
    $newStatus = ($current === 'active') ? 'inactive' : 'active';
    
    $pdo->prepare("UPDATE users SET status = ? WHERE id = ? AND id != ?")->execute([$newStatus, $id, $_SESSION['user_id']]);
    $_SESSION['msg'] = "User status updated.";
    header("Location: manage_users.php");
    exit;
}

// Handle Promote to Admin
if (isset($_GET['make_admin'])) {
    $id = (int)$_GET['make_admin'];
    $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?")->execute([$id]);
    $_SESSION['msg'] = "User promoted to Admin.";
    header("Location: manage_users.php");
    exit;
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        $_SESSION['msg'] = "User deleted successfully.";
    }
    header("Location: manage_users.php");
    exit;
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin IAC Covenant</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Outfit Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: var(--bg-page); }
        .admin-sidebar { width: 260px; background: #ffffff; border-right: 1px solid var(--border-subtle); height: 100vh; position: fixed; top: 0; left: 0; padding: 2rem 1rem; z-index: 100; }
        .admin-content { margin-left: 260px; padding: 2rem 3rem; }
        .admin-sidebar-logo { font-family: 'Outfit'; font-weight: 800; color: var(--accent-deep); font-size: 1.5rem; text-align: center; margin-bottom: 2.5rem; }
        .nav-pills .nav-link { color: var(--text-muted); font-weight: 500; border-radius: 8px; padding: 0.8rem 1.2rem; margin-bottom: 0.5rem; }
        .nav-pills .nav-link:hover, .nav-pills .nav-link.active { background-color: rgba(124, 58, 237, 0.1); color: var(--accent-core); font-weight: 600; }
        .table-container { background: white; border-radius: 16px; padding: 1.5rem; border: 1px solid var(--border-subtle); box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .badge-role { background: rgba(124, 58, 237, 0.1); color: var(--accent-core); padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .badge-admin { background: rgba(30, 64, 175, 0.1); color: var(--accent-deep); }
        .btn-action { border: 1px solid var(--border-subtle); background: white; border-radius: 8px; width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; color: var(--text-dark); margin-left: 5px; transition: all 0.2s; }
        .btn-action:hover { background: var(--accent-core); color: white; border-color: var(--accent-core); }
        .btn-delete:hover { background: #ef4444; color: white; border-color: #ef4444; }
        .text-inactive { text-decoration: line-through; color: #94a3b8; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="admin-sidebar shadow-sm">
        <div class="admin-sidebar-logo d-flex align-items-center gap-2 mb-4 justify-content-center">
            <img src="../assets/images/dnsclogo.png" alt="DNSC" style="height: 35px;">
            <img src="../assets/images/iclogo.png" alt="IC" style="height: 35px;">
            <div class="ms-1 text-start">
                <span style="color: #5b21b6; display: block; font-size: 1rem;">IAC</span>
                <span style="color: var(--accent-core); display: block; font-size: 1rem;">Covenant</span>
            </div>
        </div>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="submissions.php" class="nav-link"><i class="fa-solid fa-file-signature me-2"></i> Submissions</a>
            </li>
            <li class="nav-item">
                <a href="manage_users.php" class="nav-link active"><i class="fa-solid fa-users-gear me-2"></i> Manage Users</a>
            </li>
            <li class="nav-item">
                <a href="../signature-wall.php" class="nav-link" target="_blank"><i class="fa-solid fa-desktop me-2"></i> Signature Wall</a>
            </li>
        </ul>
        <hr>
        <a href="../logout.php" class="dropdown-item text-danger px-3 fw-bold"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Sign Out</a>
    </div>

    <!-- Main Content -->
    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="font-family: 'Outfit'; font-weight: 700; color: var(--accent-deep);">User Management</h3>
                <p class="text-muted mb-0">Manage partner accounts and access control.</p>
            </div>
        </div>

        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td>
                                <div class="fw-bold <?php echo ($user['status'] == 'inactive') ? 'text-inactive' : ''; ?>">
                                    <?php echo htmlspecialchars($user['full_name']); ?>
                                    <?php if($user['id'] == $_SESSION['user_id']): ?> <span class="badge bg-secondary ms-2 small" style="font-size: 0.6rem;">YOU</span> <?php endif; ?>
                                </div>
                            </td>
                            <td><div class="small fw-medium"><?php echo htmlspecialchars($user['email']); ?></div></td>
                            <td><span class="badge-role <?php echo ($user['role'] == 'admin') ? 'badge-admin' : ''; ?>"><?php echo $user['role']; ?></span></td>
                            <td>
                                <?php if($user['status'] == 'active'): ?>
                                    <span class="badge bg-success small"><i class="fa-solid fa-check-circle me-1"></i> Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger small"><i class="fa-solid fa-times-circle me-1"></i> Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><div class="small border-dark text-muted"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div></td>
                            <td class="text-end">
                                <?php if($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="?toggle_status=<?php echo $user['id']; ?>&current=<?php echo $user['status']; ?>" class="btn-action" title="<?php echo ($user['status'] == 'active') ? 'Deactivate' : 'Activate'; ?>">
                                        <i class="fa-solid <?php echo ($user['status'] == 'active') ? 'fa-user-lock' : 'fa-user-check'; ?>"></i>
                                    </a>
                                    <?php if($user['role'] == 'user'): ?>
                                        <a href="?make_admin=<?php echo $user['id']; ?>" class="btn-action" title="Promote to Admin" onclick="return confirm('Promote this user to Administrator?')">
                                            <i class="fa-solid fa-user-shield"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="?delete=<?php echo $user['id']; ?>" class="btn-action btn-delete" title="Delete User" onclick="return confirm('Are you sure you want to PERMANENTLY delete this user?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="small text-muted fst-italic">No actions</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>






