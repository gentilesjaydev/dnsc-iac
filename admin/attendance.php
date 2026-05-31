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
    if ($currentEventId === 0 && count($eventsList) > 0) $currentEventId = $eventsList[0]['id'];
}

// Fetch total count for pagination
if ($isAllEvents) {
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM covenant_submissions");
    $totalAttendees = $totalStmt->fetchColumn();
} else {
    $totalStmt = $pdo->prepare("SELECT COUNT(*) FROM covenant_submissions WHERE event_id = ?");
    $totalStmt->execute([$currentEventId]);
    $totalAttendees = $totalStmt->fetchColumn();
}
$totalPages = ceil($totalAttendees / $limit);

// Fetch attendees
if ($isAllEvents) {
    $stmt = $pdo->prepare("SELECT id, represented_by, position_title, organization_name, institution_type, signed_at 
                           FROM covenant_submissions 
                           ORDER BY signed_at DESC 
                           LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT id, represented_by, position_title, organization_name, institution_type, signed_at 
                           FROM covenant_submissions 
                           WHERE event_id = :event_id
                           ORDER BY signed_at DESC 
                           LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':event_id', $currentEventId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
}
$attendees = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance List - Admin IAC Covenant</title>
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
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php $active_page = 'attendance'; include 'sidebar.php'; ?>

    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="font-family: 'Outfit'; font-weight: 700; color: #5b21b6;">Representative Attendance</h3>
                <p class="text-muted mb-0">List of institutional representatives who have signed and attended.</p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
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
                <button onclick="window.print()" class="btn btn-light border px-4">
                    <i class="fa-solid fa-print me-2"></i> Print
                </button>
                <a href="export_csv?event_id=<?php echo $isAllEvents ? 'all' : $currentEventId; ?>" class="btn btn-tech px-4">
                    <i class="fa-solid fa-download me-2"></i> Export
                </a>
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Attendee Name</th>
                            <th>Designation</th>
                            <th>Organization</th>
                            <th>Category</th>
                            <th>Checked In At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($attendees) > 0): ?>
                            <?php foreach ($attendees as $row): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <?php echo htmlspecialchars($row['represented_by']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            <?php echo htmlspecialchars($row['position_title']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            <?php echo htmlspecialchars($row['organization_name']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-type">
                                            <?php echo htmlspecialchars($row['institution_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark">
                                            <?php echo date('M d, Y', strtotime($row['signed_at'])); ?>
                                        </div>
                                        <div class="text-muted small">
                                            <?php echo date('h:i A', strtotime($row['signed_at'])); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fa-solid fa-user-slash mb-3 text-muted" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted fw-bold">No attendance recorded yet.</p>
                                </td>
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
                text: "Ending administrator session.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sign Out'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../logout';
                }
            });
        }
    </script>
</body>

</html>




