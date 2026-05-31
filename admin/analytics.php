<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Enforce admin access only
requireAdmin();

$filterEventId = isset($_GET['event_id']) ? $_GET['event_id'] : 'all';
$isAllEvents = ($filterEventId === 'all');

$whereClause = $isAllEvents ? "" : "WHERE event_id = :event_id";
$whereAndClause = $isAllEvents ? "" : "AND event_id = :event_id";
$queryParams = $isAllEvents ? [] : ['event_id' => $filterEventId];

// ---------------------------------------------------------
// 1. DATA AGGREGATION & KPIs
// ---------------------------------------------------------
$eventsStmt = $pdo->query("SELECT id, title, event_date FROM events ORDER BY created_at ASC");
$allEvents = $eventsStmt->fetchAll();

$totalSigsStmt = $pdo->prepare("SELECT COUNT(*) FROM covenant_submissions $whereClause");
$totalSigsStmt->execute($queryParams);
$totalSigs = $totalSigsStmt->fetchColumn();

$totalEvents = count($allEvents);
$avgSigsPerEvent = $isAllEvents ? ($totalEvents > 0 ? round($totalSigs / $totalEvents, 1) : 0) : $totalSigs;

$busiestDayStmt = $pdo->prepare("SELECT DATE(signed_at) as date, COUNT(*) as c FROM covenant_submissions $whereClause GROUP BY date ORDER BY c DESC LIMIT 1");
$busiestDayStmt->execute($queryParams);
$busiestDayRaw = $busiestDayStmt->fetch();
$busiestDay = $busiestDayRaw ? date('M j, Y', strtotime($busiestDayRaw['date'])) : "N/A";

$topTypeStmt = $pdo->prepare("SELECT institution_type, COUNT(*) as c FROM covenant_submissions $whereClause GROUP BY institution_type ORDER BY c DESC LIMIT 1");
$topTypeStmt->execute($queryParams);
$topTypeRaw = $topTypeStmt->fetch();
$topType = $topTypeRaw && $totalSigs > 0 ? $topTypeRaw['institution_type'] . " (" . round(($topTypeRaw['c'] / $totalSigs) * 100) . "%)" : "N/A";

// ---------------------------------------------------------
// 2. CHART: OVERALL EVENT COMPARISON (Signatures per Event)
// ---------------------------------------------------------
$chartEvents = $isAllEvents ? $allEvents : array_filter($allEvents, fn($e) => $e['id'] == $filterEventId);
$chartEvents = array_values($chartEvents);

$eventLabels = [];
$eventCounts = [];
$baseColors = ['#7c3aed', '#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#ec4899', '#8b5cf6'];
$eventColors = [];
$i = 0;

foreach ($chartEvents as $ev) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM covenant_submissions WHERE event_id = ?");
    $stmt->execute([$ev['id']]);
    $count = $stmt->fetchColumn();
    
    $eventLabels[] = $ev['title'];
    $eventCounts[] = $count;
    $eventColors[] = $baseColors[$i % count($baseColors)];
    $i++;
}

// ---------------------------------------------------------
// 3. CHART: GLOBAL INSTITUTION BREAKDOWN
// ---------------------------------------------------------
$typeStmt = $pdo->prepare("SELECT institution_type, COUNT(*) as count FROM covenant_submissions $whereClause GROUP BY institution_type");
$typeStmt->execute($queryParams);
$typeData = $typeStmt->fetchAll();
$typeLabels = array_column($typeData, 'institution_type');
$typeCounts = array_column($typeData, 'count');

// ---------------------------------------------------------
// 4. CHART: DAILY GROWTH TIMELINE
// ---------------------------------------------------------
$dailyStmt = $pdo->prepare("SELECT DATE(signed_at) as date, COUNT(*) as count FROM covenant_submissions $whereClause GROUP BY DATE(signed_at) ORDER BY date ASC");
$dailyStmt->execute($queryParams);
$dailyData = $dailyStmt->fetchAll();
$dailyLabels = [];
$dailyCounts = [];
$cumulative = 0;
$dailyCumulative = [];
foreach($dailyData as $row) {
    $dailyLabels[] = date('M j', strtotime($row['date']));
    $dailyCounts[] = $row['count'];
    $cumulative += $row['count'];
    $dailyCumulative[] = $cumulative;
}

// ---------------------------------------------------------
// 5. CHART: PEAK HOURS HEATMAP/BAR
// ---------------------------------------------------------
$hoursStmt = $pdo->prepare("SELECT HOUR(signed_at) as hour, COUNT(*) as count FROM covenant_submissions $whereClause GROUP BY HOUR(signed_at) ORDER BY hour ASC");
$hoursStmt->execute($queryParams);
$hoursData = $hoursStmt->fetchAll();
$hoursDist = array_fill(0, 24, 0);
foreach($hoursData as $row) {
    if ($row['hour'] !== null) {
        $hoursDist[(int)$row['hour']] = (int)$row['count'];
    }
}
$hourLabels = array_map(function($h) {
    return $h == 0 ? "12 AM" : ($h < 12 ? "$h AM" : ($h == 12 ? "12 PM" : ($h-12)." PM"));
}, range(0, 23));

// ---------------------------------------------------------
// 6. CHART: STACKED INSTITUTION DEMOGRAPHICS PER EVENT
// ---------------------------------------------------------
$uniqueTypesQuery = "SELECT DISTINCT institution_type FROM covenant_submissions " . ($isAllEvents ? "WHERE institution_type IS NOT NULL" : "WHERE event_id = :event_id AND institution_type IS NOT NULL");
$uniqueTypesStmt = $pdo->prepare($uniqueTypesQuery);
$uniqueTypesStmt->execute($queryParams);
$uniqueTypes = $uniqueTypesStmt->fetchAll(PDO::FETCH_COLUMN);

$stackedDatasets = [];
$cIndex = 0;
foreach($uniqueTypes as $type) {
    $data = [];
    foreach($chartEvents as $ev) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM covenant_submissions WHERE event_id = ? AND institution_type = ?");
        $stmt->execute([$ev['id'], $type]);
        $data[] = $stmt->fetchColumn();
    }
    $stackedDatasets[] = [
        'label' => $type,
        'data' => $data,
        'backgroundColor' => $baseColors[$cIndex % count($baseColors)],
        'stack' => 'Stack 0'
    ];
    $cIndex++;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deep Analytics - IAC Covenant</title>
    <link rel="shortcut icon" href="../assets/images/iclogo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .analytics-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid rgba(226, 232, 240, 0.8);
            margin-bottom: 2rem;
            height: 100%;
        }
        .analytics-title {
            font-family: 'Outfit';
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }
        
        .kpi-card {
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
            border-radius: 16px;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 10px 20px rgba(124, 58, 237, 0.2);
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .kpi-card::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .kpi-card.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2); }
        .kpi-card.orange { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); box-shadow: 0 10px 20px rgba(245, 158, 11, 0.2); }
        .kpi-card.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2); }
        
        .kpi-value { font-size: 2.5rem; font-weight: 800; font-family: 'Outfit'; margin: 0; line-height: 1.2; }
        .kpi-label { font-size: 0.9rem; font-weight: 600; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; }
        .kpi-icon { position: absolute; right: 20px; bottom: 20px; font-size: 4rem; opacity: 0.1; }
    </style>
</head>
<body>

    <?php $active_page = 'analytics'; include 'sidebar.php'; ?>

    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
            <div>
                <h2 class="mb-1 fw-bold" style="font-family: 'Outfit'; color: var(--text-dark);">Deep Analytics</h2>
                <p class="text-muted mb-0">High-level insights, engagement tracking, and demographic breakdowns.</p>
            </div>
            <div class="d-flex gap-3 align-items-center flex-wrap">
                <form method="GET" class="d-flex align-items-center gap-2">
                    <label class="fw-bold small text-muted text-nowrap">Analyze Scope:</label>
                    <select name="event_id" class="form-select form-select-sm shadow-sm border-0" style="min-width: 200px; height: 38px; font-family: 'Outfit'; font-weight: 500;" onchange="this.form.submit()">
                        <option value="all" <?php echo $isAllEvents ? 'selected' : ''; ?>>All Events Combined</option>
                        <?php foreach($allEvents as $ev): ?>
                            <option value="<?php echo $ev['id']; ?>" <?php echo (!$isAllEvents && $ev['id'] == $filterEventId) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ev['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <button onclick="window.print()" class="btn btn-tech rounded-pill shadow-sm px-4 h-100" style="min-height: 38px;"><i class="fa-solid fa-print me-2"></i> Print Report</button>
            </div>
        </div>

        <!-- KPI Row -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="kpi-card">
                    <p class="kpi-label">Total Signatures</p>
                    <p class="kpi-value"><?php echo number_format($totalSigs); ?></p>
                    <i class="fa-solid fa-file-signature kpi-icon"></i>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="kpi-card green">
                    <p class="kpi-label"><?php echo $isAllEvents ? "Avg Sigs per Event" : "Active Participants"; ?></p>
                    <p class="kpi-value"><?php echo $avgSigsPerEvent; ?></p>
                    <i class="fa-solid fa-chart-column kpi-icon"></i>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="kpi-card orange">
                    <p class="kpi-label">Busiest Day</p>
                    <p class="kpi-value" style="font-size: 1.8rem; margin-top: 10px;"><?php echo $busiestDay; ?></p>
                    <i class="fa-solid fa-fire kpi-icon"></i>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="kpi-card blue">
                    <p class="kpi-label">Top Demographic</p>
                    <p class="kpi-value" style="font-size: 1.5rem; margin-top: 10px;"><?php echo $topType; ?></p>
                    <i class="fa-solid fa-users kpi-icon"></i>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-8">
                <div class="analytics-card animate-fade-in">
                    <h5 class="analytics-title"><i class="fa-solid fa-layer-group text-primary"></i> Demographic Breakdown by Event</h5>
                    <p class="text-muted small mb-4">A stacked view showing exactly which demographic groups attended which events.</p>
                    <div class="chart-container">
                        <canvas id="stackedDemographicsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="analytics-card animate-fade-in" style="animation-delay: 0.1s;">
                    <h5 class="analytics-title"><i class="fa-regular fa-clock text-warning"></i> Global Peak Engagement Hours</h5>
                    <p class="text-muted small mb-4">Shows what time of day users are actually signing the covenant.</p>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="peakHoursChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-6">
                <div class="analytics-card animate-fade-in" style="animation-delay: 0.2s;">
                    <h5 class="analytics-title"><i class="fa-solid fa-arrow-trend-up text-success"></i> Cumulative Growth Trajectory</h5>
                    <p class="text-muted small mb-4">Tracking total platform adoption and signature volume over time.</p>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="cumulativeGrowthChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="analytics-card animate-fade-in" style="animation-delay: 0.3s;">
                    <h5 class="analytics-title"><i class="fa-solid fa-chart-pie text-danger"></i> Global Institution Ratio</h5>
                    <p class="text-muted small mb-4">Overall distribution of signatories across the selected scope.</p>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('adminSidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }

        // Initialize Charts
        Chart.defaults.font.family = "'Outfit', sans-serif";
        Chart.defaults.color = '#64748b';
        Chart.defaults.scale.grid.color = 'rgba(226, 232, 240, 0.5)';

        // 1. Stacked Demographics by Event
        new Chart(document.getElementById('stackedDemographicsChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($eventLabels); ?>,
                datasets: <?php echo json_encode($stackedDatasets); ?>
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: {
                        stacked: true,
                        ticks: {
                            callback: function(value) {
                                let label = this.getLabelForValue(value);
                                return label ? (label.length > 20 ? label.substr(0, 20) + '...' : label) : '';
                            }
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // 2. Peak Hours (Bar Chart)
        new Chart(document.getElementById('peakHoursChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($hourLabels); ?>,
                datasets: [{
                    label: 'Signatures at this hour',
                    data: <?php echo json_encode(array_values($hoursDist)); ?>,
                    backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    borderRadius: 4,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: {
                        ticks: {
                            maxTicksLimit: 8
                        }
                    }
                }
            }
        });

        // 3. Cumulative Growth Line Chart
        new Chart(document.getElementById('cumulativeGrowthChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dailyLabels); ?>,
                datasets: [
                    {
                        label: 'Cumulative Total',
                        data: <?php echo json_encode($dailyCumulative); ?>,
                        borderColor: '#7c3aed',
                        backgroundColor: 'rgba(124, 58, 237, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#7c3aed',
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    {
                        type: 'bar',
                        label: 'Daily Signatures',
                        data: <?php echo json_encode($dailyCounts); ?>,
                        backgroundColor: 'rgba(16, 185, 129, 0.5)',
                        borderRadius: 4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Cumulative Total' },
                        ticks: { stepSize: 1 }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Daily Sigs' },
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // 4. Global Type Doughnut Chart
        new Chart(document.getElementById('typeChart'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($typeLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($typeCounts); ?>,
                    backgroundColor: ['#7c3aed', '#10b981', '#3b82f6', '#f59e0b', '#ec4899', '#6366f1'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>
