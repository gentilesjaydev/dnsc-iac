<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Enforce admin access only
requireAdmin();

$successMsg = '';
$errorMsg = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        try {
            if ($action === 'add_event') {
                $title = trim($_POST['title']);
                $venue = trim($_POST['venue']);
                $event_date = trim($_POST['event_date']);
                $is_signing_open = (int)$_POST['is_signing_open'];
                $is_exact_date_only = (int)$_POST['is_exact_date_only'];
                
                $stmt = $pdo->prepare("INSERT INTO events (title, venue, event_date, is_signing_open, is_exact_date_only) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $venue, $event_date, $is_signing_open, $is_exact_date_only]);
                $_SESSION['flash_success'] = "Event added successfully!";
            } elseif ($action === 'edit_event') {
                $id = (int)$_POST['event_id'];
                $title = trim($_POST['title']);
                $venue = trim($_POST['venue']);
                $event_date = trim($_POST['event_date']);
                $is_signing_open = (int)$_POST['is_signing_open'];
                $is_exact_date_only = (int)$_POST['is_exact_date_only'];
                
                $stmt = $pdo->prepare("UPDATE events SET title = ?, venue = ?, event_date = ?, is_signing_open = ?, is_exact_date_only = ? WHERE id = ?");
                $stmt->execute([$title, $venue, $event_date, $is_signing_open, $is_exact_date_only, $id]);
                $_SESSION['flash_success'] = "Event updated successfully!";
            } elseif ($action === 'delete_event') {
                $id = (int)$_POST['event_id'];
                
                // Prevent deleting if it's the only active event
                $stmt = $pdo->prepare("SELECT is_active FROM events WHERE id = ?");
                $stmt->execute([$id]);
                if ($stmt->fetchColumn()) {
                    $_SESSION['flash_error'] = "Cannot delete the currently active event. Deactivate it first before deleting.";
                } else {
                    // Fetch all associated submissions to delete their files
                    $stmtFiles = $pdo->prepare("SELECT signature_file, pdf_file FROM covenant_submissions WHERE event_id = ?");
                    $stmtFiles->execute([$id]);
                    $files = $stmtFiles->fetchAll();

                    foreach ($files as $file) {
                        if ($file['signature_file'] && file_exists("../assets/signatures/" . $file['signature_file'])) {
                            unlink("../assets/signatures/" . $file['signature_file']);
                        }
                        if ($file['pdf_file'] && file_exists("../assets/pdfs/" . $file['pdf_file'])) {
                            unlink("../assets/pdfs/" . $file['pdf_file']);
                        }
                    }

                    // Delete submissions (if ON DELETE CASCADE is missing)
                    $pdo->prepare("DELETE FROM covenant_submissions WHERE event_id = ?")->execute([$id]);

                    // Delete event
                    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
                    $stmt->execute([$id]);
                    $_SESSION['flash_success'] = "Event and all associated records deleted successfully!";
                }
            } elseif ($action === 'set_active') {
                $id = (int)$_POST['event_id'];
                
                // Deactivate all
                $pdo->exec("UPDATE events SET is_active = 0");
                // Activate selected
                $stmt = $pdo->prepare("UPDATE events SET is_active = 1 WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['flash_success'] = "Active event updated!";
            } elseif ($action === 'deactivate_event') {
                $id = (int)$_POST['event_id'];
                $stmt = $pdo->prepare("UPDATE events SET is_active = 0 WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['flash_success'] = "Event deactivated successfully. The covenant system is now on standby.";
            }
            header("Location: events");
            exit;
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Database error: " . $e->getMessage();
            header("Location: events");
            exit;
        }
    }
}

// Fetch all events
$stmtEvents = $pdo->query("SELECT * FROM events ORDER BY created_at DESC");
$events = $stmtEvents->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Admin Dashboard</title>
    <link rel="shortcut icon" href="../assets/images/iclogo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Flatpickr CSS & ConfirmDate Plugin -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">
    <style>
        /* Premium Flatpickr Overrides */
        .flatpickr-calendar {
            border-radius: 16px !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
            font-family: 'Outfit', sans-serif !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
            background: #7c3aed !important;
            border-color: #7c3aed !important;
            font-weight: bold;
        }
        .flatpickr-day {
            border-radius: 8px !important;
        }
        .flatpickr-months .flatpickr-month {
            color: #2e1065 !important;
            fill: #2e1065 !important;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 700 !important;
        }
        .flatpickr-time {
            border-top: 1px solid rgba(226, 232, 240, 0.8) !important;
        }
        .flatpickr-time input:hover, .flatpickr-time .flatpickr-am-pm:hover, .flatpickr-time input:focus, .flatpickr-time .flatpickr-am-pm:focus {
            background: rgba(124, 58, 237, 0.05) !important;
        }
        /* Style the Confirm Button */
        .flatpickr-confirm {
            background: #7c3aed !important;
            color: white !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            margin: 10px !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 10px rgba(124,58,237,0.2) !important;
            transition: all 0.2s;
            cursor: pointer;
        }
        .flatpickr-confirm:hover {
            background: #6d28d9 !important;
        }
    </style>
</head>
<body>

    <!-- Mobile Toggle -->
    <div class="admin-mobile-nav d-lg-none bg-white p-3 border-bottom sticky-top d-flex justify-content-between align-items-center">
        <div class="fw-bold fw-bold" style="font-family: 'Outfit';">IAC <span class="text-primary">Covenant</span></div>
        <button class="btn btn-light border" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-logo d-flex align-items-center gap-2 mb-4">
            <img src="../assets/images/dnsclogo.png" alt="DNSC" style="height: 35px;">
            <img src="../assets/images/iclogo.png" alt="IC" style="height: 35px;">
            <div class="ms-1">
                <span style="color: #2e1065;">IAC</span> <span style="color: var(--accent-core);">Covenant</span>
            </div>
        </div>
        <ul class="nav nav-admin flex-column">
            <li class="nav-item"><a href="dashboard" class="nav-link"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li class="nav-item"><a href="analytics" class="nav-link"><i class="fa-solid fa-chart-line"></i> Analytics</a></li>
            <li class="nav-item"><a href="events" class="nav-link active"><i class="fa-solid fa-calendar-alt"></i> Events</a></li>
            <li class="nav-item"><a href="attendance" class="nav-link"><i class="fa-solid fa-user-check"></i> Attendance</a></li>
            <li class="nav-item"><a href="submissions" class="nav-link"><i class="fa-solid fa-file-signature"></i> Submissions</a></li>
            <li class="nav-item"><a href="activity_logs" class="nav-link"><i class="fa-solid fa-list-ul"></i> Activity Logs</a></li>
            <li class="nav-item"><a href="edit_profile" class="nav-link"><i class="fa-solid fa-user-cog"></i> Profile</a></li>
            <li class="nav-item"><a href="../covenant" class="nav-link" target="_blank"><i class="fa-solid fa-file-contract"></i> Signing Form</a></li>
            <li class="nav-item"><a href="../signature-wall" class="nav-link" target="_blank"><i class="fa-solid fa-external-link"></i> Live Wall</a></li>
        </ul>
        <div style="margin-top: auto; padding-top: 2rem; border-top: 1px solid #f1f5f9;">
            <a href="#" onclick="confirmLogout(event)" class="nav-link text-danger fw-bold"><i class="fa-solid fa-power-off"></i> Logout</a>
        </div>
    </div>

    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-end mb-5 animate-fade-in">
            <div>
                <h1 style="font-family: 'Outfit'; font-weight: 800; font-size: 2.2rem; color: #2e1065; margin-bottom: 0.25rem;">Manage Events</h1>
                <p class="text-muted fw-medium mb-0">Create and manage events for the digital covenant system.</p>
            </div>
            <button type="button" class="btn btn-tech px-4 py-2 shadow-neon fw-bold" data-bs-toggle="modal" data-bs-target="#addEventModal" style="border-radius: 12px;">
                <i class="fa-solid fa-plus me-2"></i> New Event
            </button>
        </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo $_SESSION['flash_success']; ?>',
                confirmButtonColor: '#7c3aed'
            });
        </script>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $_SESSION['flash_error']; ?>',
                confirmButtonColor: '#7c3aed'
            });
        </script>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

        <div class="glass-panel animate-fade-in">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Signing Mode</th>
                            <th>Event Title</th>
                            <th>Venue</th>
                            <th>Date & Time</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td>
                                    <?php if ($event['is_active']): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill"><i class="fa-solid fa-circle text-success me-1" style="font-size: 0.5rem;"></i> Active</span>
                                            <form method="POST" style="display:inline;" class="deactivate-form">
                                                <input type="hidden" name="action" value="deactivate_event">
                                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill deactivate-btn" title="Deactivate Event"><i class="fa-solid fa-power-off"></i></button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="set_active">
                                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                            <button type="submit" class="btn btn-sm rounded-pill text-nowrap fw-semibold" style="background: rgba(124, 58, 237, 0.1); color: var(--accent-core); border: 1px solid rgba(124, 58, 237, 0.2); transition: all 0.2s;" onmouseover="this.style.background='var(--accent-core)'; this.style.color='white';" onmouseout="this.style.background='rgba(124, 58, 237, 0.1)'; this.style.color='var(--accent-core)';"><i class="fa-solid fa-power-off me-1"></i> Activate</button>
                                        </form>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($event['is_signing_open'] == 0): ?>
                                        <span class="badge bg-danger rounded-pill"><i class="fa-solid fa-lock me-1"></i> Closed</span>
                                    <?php elseif ($event['is_exact_date_only'] == 1): ?>
                                        <span class="badge bg-warning text-dark rounded-pill"><i class="fa-solid fa-clock me-1"></i> Exact Date Only</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary rounded-pill"><i class="fa-solid fa-door-open me-1"></i> Open Anytime</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($event['title']); ?></td>
                                <td class="text-muted"><?php echo htmlspecialchars($event['venue']); ?></td>
                                <td class="text-muted"><?php echo htmlspecialchars($event['event_date']); ?></td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-light border text-primary edit-event-btn" 
                                                data-id="<?php echo $event['id']; ?>"
                                                data-title="<?php echo htmlspecialchars($event['title']); ?>"
                                                data-venue="<?php echo htmlspecialchars($event['venue']); ?>"
                                                data-date="<?php echo htmlspecialchars($event['event_date']); ?>"
                                                data-open="<?php echo $event['is_signing_open'] ?? 1; ?>"
                                                data-exact="<?php echo $event['is_exact_date_only'] ?? 0; ?>"
                                                data-bs-toggle="modal" data-bs-target="#editEventModal">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                        <?php if (!$event['is_active']): ?>
                                            <form method="POST" class="delete-form m-0 p-0">
                                                <input type="hidden" name="action" value="delete_event">
                                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                                <button type="button" class="btn btn-sm btn-light border text-danger delete-btn"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($events)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">No events found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" style="font-family: 'Outfit'; color: #2e1065;">Create New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_event">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Event Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. IAC Meeting 2026">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Venue</label>
                            <input type="text" name="venue" class="form-control" required placeholder="e.g. DNSC GAD Conference Room">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Event Date & Time</label>
                            <input type="text" name="event_date" class="form-control event-datepicker" required placeholder="Select Date & Time">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Master Signing Switch</label>
                                <select name="is_signing_open" class="form-select" required>
                                    <option value="1">Open (Accepting Signatures)</option>
                                    <option value="0">Closed (Form Disabled)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Availability Mode</label>
                                <select name="is_exact_date_only" class="form-select" required>
                                    <option value="0">Allow Anytime</option>
                                    <option value="1">Strict: Only on Exact Date</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-tech px-4 text-white">Create Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" style="font-family: 'Outfit'; color: #2e1065;">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_event">
                        <input type="hidden" name="event_id" id="edit_event_id">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Event Title</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Venue</label>
                            <input type="text" name="venue" id="edit_venue" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Event Date & Time</label>
                            <input type="text" name="event_date" id="edit_date" class="form-control event-datepicker" required placeholder="Select Date & Time">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Master Signing Switch</label>
                                <select name="is_signing_open" id="edit_signing_open" class="form-select" required>
                                    <option value="1">Open (Accepting Signatures)</option>
                                    <option value="0">Closed (Form Disabled)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Availability Mode</label>
                                <select name="is_exact_date_only" id="edit_exact_date" class="form-select" required>
                                    <option value="0">Allow Anytime</option>
                                    <option value="1">Strict: Only on Exact Date</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-tech px-4 text-white">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
    <script>
        // Initialize Flatpickr for beautiful date/time selection
        flatpickr(".event-datepicker", {
            enableTime: true,
            dateFormat: "F j, Y h:i K",
            minDate: "today",
            animate: true,
            plugins: [new confirmDatePlugin({
                confirmIcon: "",
                confirmText: "SET DATE & TIME",
                showAlways: true
            })]
        });

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
        function toggleSidebar() {
            $('#adminSidebar').toggleClass('show');
            $('#sidebarOverlay').toggleClass('show');
        }

        // Fill edit modal data
        $('.edit-event-btn').click(function() {
            const button = $(this);
            $('#edit_event_id').val(button.data('id'));
            $('#edit_title').val(button.data('title'));
            $('#edit_venue').val(button.data('venue'));
            $('#edit_date').val(button.data('date'));
            $('#edit_signing_open').val(button.data('open'));
            $('#edit_exact_date').val(button.data('exact'));
        });

        // SweetAlert2 for Deactivate
        $('.deactivate-btn').click(function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Deactivate Event?',
                text: "The front-end system will be locked until a new event is set to active.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Deactivate'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // SweetAlert2 for Delete
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Delete Event?',
                text: "You won't be able to revert this! All submissions tied to this event may be affected.",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>
