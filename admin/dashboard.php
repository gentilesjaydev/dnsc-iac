<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Enforce admin access only
requireAdmin();

// Fetch statistics
$stmtTotalSignatories = $pdo->query("SELECT COUNT(*) FROM covenant_submissions");
$totalSignatories = $stmtTotalSignatories->fetchColumn();

// Fetch recent submissions
$stmtRecent = $pdo->query("SELECT c.*, u.full_name, u.email 
                           FROM covenant_submissions c 
                           LEFT JOIN users u ON c.user_id = u.id 
                           ORDER BY c.signed_at DESC LIMIT 10");
$recentSubmissions = $stmtRecent->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - IAC Covenant</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/iclogo.png" type="image/x-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime('../assets/css/style.css'); ?>">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <span style="color: #2e1065;">IAC</span> <span style="color: var(--accent-core);">Covenant</span>
            </div>
        </div>
        <ul class="nav nav-admin flex-column">
            <li class="nav-item">
                <a href="dashboard" class="nav-link active"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="attendance" class="nav-link"><i class="fa-solid fa-user-check"></i> Attendance</a>
            </li>
            <li class="nav-item">
                <a href="submissions" class="nav-link"><i class="fa-solid fa-file-signature"></i> Submissions</a>
            </li>
            <li class="nav-item">
                <a href="activity_logs" class="nav-link"><i class="fa-solid fa-list-ul"></i> Activity Logs</a>
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

    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-end mb-5 animate-fade-in">
            <div>
                <h1
                    style="font-family: 'Outfit'; font-weight: 800; font-size: 2.2rem; color: #2e1065; margin-bottom: 0.25rem;">
                    Event Overview</h1>
                <p class="text-muted fw-medium mb-0">Welcome back, administrator. Here's what's happening today.</p>
            </div>
            <div class="d-flex gap-3">
                <a href="export_csv" class="btn btn-tech px-4 py-2 shadow-neon fw-bold" style="border-radius: 12px;">
                    <i class="fa-solid fa-download me-2"></i> Export Data
                </a>
            </div>
        </div>

        <?php
        $stmtToday = $pdo->query("SELECT COUNT(*) FROM covenant_submissions WHERE DATE(signed_at) = CURDATE()");
        $signingsToday = $stmtToday->fetchColumn();
        ?>

        <div class="row g-4 mb-5">
            <div class="col-md-6 animate-fade-in" style="animation-delay: 0.1s;">
                <div class="premium-stat-card" style="border-left: 5px solid var(--accent-core);">
                    <div class="stat-icon"><i class="fa-solid fa-file-contract"></i></div>
                    <div class="stat-value" id="totalSignatoriesCount"><?php echo number_format($totalSignatories); ?>
                    </div>
                    <div class="stat-label">Total Signatures Collected</div>
                </div>
            </div>
            <div class="col-md-6 animate-fade-in" style="animation-delay: 0.2s;">
                <div class="premium-stat-card" style="border-left: 5px solid #f59e0b;">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;"><i
                            class="fa-solid fa-bolt"></i></div>
                    <div class="stat-value" id="signingsTodayCount"><?php echo number_format($signingsToday); ?></div>
                    <div class="stat-label">Signings Recorded Today</div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-8 animate-fade-in" style="animation-delay: 0.3s;">
                <div class="glass-panel h-100">
                    <h5 class="fw-bold mb-4" style="font-family: 'Outfit';">Signings per Hour (Today)</h5>
                    <div style="height: 300px;">
                        <canvas id="hourlyChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 animate-fade-in" style="animation-delay: 0.4s;">
                <div class="glass-panel h-100">
                    <h5 class="fw-bold mb-4" style="font-family: 'Outfit';">Partner Mix</h5>
                    <div style="height: 300px;">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-panel animate-fade-in" style="animation-delay: 0.5s;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 style="font-family: 'Outfit'; font-weight: 800; font-size: 1.4rem;">Recent Stream</h5>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold"
                    style="font-size: 0.7rem;">
                    <i class="fa-solid fa-circle me-1" style="font-size: 0.5rem;"></i> LIVE MONITORING
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Signatory</th>
                            <th>Organization</th>
                            <th>Institution</th>
                            <th>Dated</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recentStreamBody">
                        <?php if (count($recentSubmissions) > 0): ?>
                            <?php foreach ($recentSubmissions as $sub): ?>
                                <tr class="submission-row" data-id="<?php echo $sub['id']; ?>">
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <?php echo htmlspecialchars($sub['represented_by']); ?>
                                        </div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($sub['position_title']); ?>
                                        </div>
                                    </td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($sub['organization_name']); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1 rounded small"
                                            style="font-size: 0.7rem;">
                                            <?php echo htmlspecialchars($sub['institution_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-bold date-val">
                                            <?php echo date('M d, Y', strtotime($sub['signed_at'])); ?>
                                        </div>
                                        <div class="text-muted small time-val">
                                            <?php echo date('h:i A', strtotime($sub['signed_at'])); ?>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="../assets/signatures/<?php echo $sub['signature_file']; ?>" target="_blank"
                                            class="btn-action-round" title="Signature"><i class="fa-solid fa-image"></i></a>
                                        <a href="../view_certificate?file=<?php echo urlencode($sub['pdf_file']); ?>"
                                            target="_blank" class="btn-action-round" title="PDF"><i
                                                class="fa-solid fa-file-pdf"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="emptyStateRow">
                                <td colspan="5" class="text-center py-5">
                                    <i class="fa-solid fa-inbox mb-3 text-muted" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted fw-bold">No active signatures recorded yet.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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

        // Initialize Charts
        const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
        const typeCtx = document.getElementById('typeChart').getContext('2d');

        const hourlyChart = new Chart(hourlyCtx, {
            type: 'line',
            data: {
                labels: Array.from({ length: 24 }, (_, i) => `${i}:00`),
                datasets: [{
                    label: 'Signatures',
                    data: new Array(24).fill(0),
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124, 58, 237, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#7c3aed'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });

        const typeChart = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: ['#7c3aed', '#7c3aed', '#5b21b6', '#64748b'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { family: 'Inter', weight: 600 } } }
                },
                cutout: '70%'
            }
        });

        // Real-time Dashboard Updates
        function updateDashboard() {
            $.ajax({
                url: '../api/admin_dashboard_data',
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // Update Stats with subtle pulse
                        const totalEl = $('#totalSignatoriesCount');
                        const todayEl = $('#signingsTodayCount');

                        if (totalEl.text() != response.stats.total) {
                            totalEl.text(response.stats.total).addClass('animate-pulse');
                            setTimeout(() => totalEl.removeClass('animate-pulse'), 1000);
                        }
                        if (todayEl.text() != response.stats.today) {
                            todayEl.text(response.stats.today).addClass('animate-pulse');
                            setTimeout(() => todayEl.removeClass('animate-pulse'), 1000);
                        }

                        // Update Hourly Chart
                        const hourlyValues = new Array(24).fill(0);
                        response.charts.hourly.forEach(row => {
                            hourlyValues[row.hour] = row.count;
                        });
                        hourlyChart.data.datasets[0].data = hourlyValues;
                        hourlyChart.update();

                        // Update Type Chart
                        typeChart.data.labels = response.charts.types.map(t => t.institution_type);
                        typeChart.data.datasets[0].data = response.charts.types.map(t => t.count);
                        typeChart.update();

                        // Update Table
                        const tbody = $('#recentStreamBody');
                        if (response.recent.length > 0) {
                            $('#emptyStateRow').remove();

                            response.recent.forEach(sub => {
                                let existingRow = $(`.submission-row[data-id="${sub.id}"]`);
                                if (existingRow.length === 0) {
                                    // New row! Prepend it
                                    const newRow = $(`
                                        <tr class="submission-row animate-fade-in" data-id="${sub.id}" style="background: rgba(124, 58, 237, 0.05);">
                                            <td>
                                                <div class="fw-bold text-dark">${sub.represented_by}</div>
                                                <div class="text-muted small">${sub.position_title}</div>
                                            </td>
                                            <td class="fw-semibold">${sub.organization_name}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1 rounded small" style="font-size: 0.7rem;">
                                                    ${sub.institution_type}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small fw-bold">${sub.date_friendly}</div>
                                                <div class="text-muted small">${sub.time_friendly}</div>
                                            </td>
                                            <td class="text-end">
                                                <a href="../assets/signatures/${sub.signature_file}" target="_blank" class="btn-action-round"><i class="fa-solid fa-image"></i></a>
                                                <a href="../view_certificate?file=${sub.pdf_file}" target="_blank" class="btn-action-round"><i class="fa-solid fa-file-pdf"></i></a>
                                           </td>
                                        </tr>
                                    `);
                                    tbody.prepend(newRow);

                                    // Fade to normal background after 3s
                                    setTimeout(() => {
                                        newRow.css('transition', 'background 2s').css('background', 'transparent');
                                    }, 3000);

                                    // Keep only top 10
                                    if ($('.submission-row').length > 10) {
                                        $('.submission-row').last().remove();
                                    }
                                }
                            });
                        }
                    }
                },
                error: function (err) {
                    console.error("Dashboard auto-update failed", err);
                }
            });
        }

        // Run update every 5 seconds
        setInterval(updateDashboard, 5000);
    </script>

    <style>
        @keyframes pulse-admin {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
                color: var(--accent-core);
            }

            100% {
                transform: scale(1);
            }
        }

        .animate-pulse {
            display: inline-block;
            animation: pulse-admin 0.6s ease-out;
        }
    </style>
</body>

</html>





