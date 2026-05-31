<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/logger.php';

// Enforce admin access only
requireAdmin();

// Pagination setup
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// Fetch events for dropdown
$eventsStmt = $pdo->query("SELECT id, title, is_active FROM events ORDER BY created_at DESC");
$eventsList = $eventsStmt->fetchAll();

// Determine current event filter
$isAllEvents = isset($_GET['event_id']) && $_GET['event_id'] === 'all';
$currentEventId = isset($_GET['event_id']) && $_GET['event_id'] !== 'all' ? (int)$_GET['event_id'] : 0;

if (!$isAllEvents && $currentEventId === 0 && !isset($_GET['event_id'])) {
    foreach ($eventsList as $ev) {
        if ($ev['is_active']) {
            $currentEventId = $ev['id'];
            break;
        }
    }
    // Fallback if no active
    if ($currentEventId === 0 && count($eventsList) > 0) $currentEventId = $eventsList[0]['id'];
}

// Fetch total count for pagination
if ($isAllEvents) {
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM covenant_submissions");
    $totalSubmissions = $totalStmt->fetchColumn();
} else {
    $totalStmt = $pdo->prepare("SELECT COUNT(*) FROM covenant_submissions WHERE event_id = ?");
    $totalStmt->execute([$currentEventId]);
    $totalSubmissions = $totalStmt->fetchColumn();
}
$totalPages = ceil($totalSubmissions / $limit);

// Fetch submissions
if ($isAllEvents) {
    $stmt = $pdo->prepare("SELECT c.*, u.full_name, u.email, e.title as event_title
                           FROM covenant_submissions c 
                           LEFT JOIN users u ON c.user_id = u.id 
                           LEFT JOIN events e ON c.event_id = e.id
                           ORDER BY c.signed_at DESC 
                           LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT c.*, u.full_name, u.email, e.title as event_title
                           FROM covenant_submissions c 
                           LEFT JOIN users u ON c.user_id = u.id 
                           LEFT JOIN events e ON c.event_id = e.id
                           WHERE c.event_id = :event_id
                           ORDER BY c.signed_at DESC 
                           LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':event_id', $currentEventId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
}
$submissions = $stmt->fetchAll();

// Handle Delete Action
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // Get file paths before deleting
    $stmt = $pdo->prepare("SELECT signature_file, pdf_file FROM covenant_submissions WHERE id = ?");
    $stmt->execute([$id]);
    $files = $stmt->fetch();

    if ($files) {
        // Delete physical files
        if (file_exists("../assets/signatures/" . $files['signature_file'])) {
            unlink("../assets/signatures/" . $files['signature_file']);
        }
        if (file_exists("../assets/pdfs/" . $files['pdf_file'])) {
            unlink("../assets/pdfs/" . $files['pdf_file']);
        }

        // Delete from database
        $pdo->prepare("DELETE FROM covenant_submissions WHERE id = ?")->execute([$id]);

        // Log Activity
        logActivity($pdo, $_SESSION['user_id'], 'submission_management', "Deleted covenant submission ID $id");

        $_SESSION['msg'] = "Submission deleted successfully.";
        header("Location: submissions");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Submissions - Admin IAC Covenant</title>
    <!-- Favicon -->
    <link rel="icon" href="../assets/images/iclogo.png" type="image/png">
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

    <?php $active_page = 'submissions'; include 'sidebar.php'; ?>

    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h3 style="font-family: 'Outfit'; font-weight: 700; color: var(--accent-deep);">Covenant Submissions</h3>
                <p class="text-muted mb-0">Manage and view all signed digital certificates.</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <form method="GET" class="d-flex align-items-center gap-2">
                    <label class="fw-bold small text-muted text-nowrap">Filter by Event:</label>
                    <select name="event_id" class="form-select form-select-sm" style="max-width: 300px;" onchange="this.form.submit()">
                        <option value="all" <?php echo $isAllEvents ? 'selected' : ''; ?>>All Events Combined</option>
                        <?php foreach($eventsList as $ev): ?>
                            <option value="<?php echo $ev['id']; ?>" <?php echo (!$isAllEvents && $ev['id'] == $currentEventId) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ev['title']); ?> <?php echo $ev['is_active'] ? '(Active)' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <a href="export_csv?event_id=<?php echo $isAllEvents ? 'all' : $currentEventId; ?>" class="btn btn-tech btn-sm px-4 text-nowrap">Export to CSV</a>
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Organization</th>
                            <th>Representative</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Signed At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($submissions) > 0): ?>
                            <?php foreach ($submissions as $sub): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($sub['organization_name']); ?></div>
                                        <small class="text-muted">IP: <?php echo $sub['ip_address']; ?></small>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($sub['represented_by']); ?></div>
                                        <small
                                            class="text-muted"><?php echo htmlspecialchars($sub['position_title']); ?></small>
                                    </td>
                                    <td><span
                                            class="badge-type"><?php echo htmlspecialchars($sub['institution_type']); ?></span>
                                    </td>
                                    <td>
                                        <div class="small"><?php echo htmlspecialchars($sub['email_address']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($sub['contact_number']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold"><?php echo date('M d, Y', strtotime($sub['signed_at'])); ?>
                                        </div>
                                        <div class="small text-muted"><?php echo date('h:i A', strtotime($sub['signed_at'])); ?>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="../view_signature?file=<?php echo urlencode($sub['signature_file']); ?>" target="_blank"
                                            class="btn-action" title="View Signature"><i class="fa-solid fa-signature"></i></a>
                                        <a href="../view_certificate?file=<?php echo urlencode($sub['pdf_file']); ?>"
                                            target="_blank" class="btn-action" title="View PDF"><i
                                                class="fa-solid fa-file-pdf"></i></a>
                                        <a href="#" onclick="return confirmDeleteSubmission(<?php echo $sub['id']; ?>)"
                                            class="btn-action btn-delete" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No submissions found.</td>
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
                                <a class="page-link" href="?event_id=<?php echo $currentEventId; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
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

        function confirmDeleteSubmission(id) {
            Swal.fire({
                title: 'Delete Submission?',
                text: "This will permanently delete the covenant entry and the generated PDF file. This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete Entry'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?delete=' + id;
                }
            });
            return false;
        }

        <?php if (isset($_SESSION['msg'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Submission Deleted',
                text: '<?php echo $_SESSION['msg']; ?>',
                confirmButtonColor: '#7c3aed'
            });
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>
    </script>
</body>

</html>





