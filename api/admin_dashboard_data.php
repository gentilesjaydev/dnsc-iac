<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

try {
    // 1. Fetch Total Signatories
    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM covenant_submissions");
    $totalSignatories = (int) $stmtTotal->fetchColumn();

    // 2. Fetch Signings Today
    $stmtToday = $pdo->query("SELECT COUNT(*) FROM covenant_submissions WHERE DATE(signed_at) = CURDATE()");
    $signingsToday = (int) $stmtToday->fetchColumn();

    // 3. Fetch Recent Submissions (Limit 10)
    $stmtRecent = $pdo->query("SELECT c.*, u.full_name, u.email 
                               FROM covenant_submissions c 
                               LEFT JOIN users u ON c.user_id = u.id 
                               ORDER BY c.signed_at DESC LIMIT 10");
    $recentSubmissions = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

    // Format dates for friendly display
    foreach ($recentSubmissions as &$sub) {
        $sub['date_friendly'] = date('M d, Y', strtotime($sub['signed_at']));
        $sub['time_friendly'] = date('h:i A', strtotime($sub['signed_at']));
    }

    // 4. Fetch Institution Type Breakdown
    $stmtTypes = $pdo->query("SELECT institution_type, COUNT(*) as count FROM covenant_submissions GROUP BY institution_type");
    $typeBreakdown = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);

    // 5. Fetch Hourly Signings (Today)
    $stmtHourly = $pdo->query("SELECT HOUR(signed_at) as hour, COUNT(*) as count FROM covenant_submissions WHERE DATE(signed_at) = CURDATE() GROUP BY HOUR(signed_at) ORDER BY hour");
    $hourlyData = $stmtHourly->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'stats' => [
            'total' => $totalSignatories,
            'today' => $signingsToday
        ],
        'recent' => $recentSubmissions,
        'charts' => [
            'types' => $typeBreakdown,
            'hourly' => $hourlyData
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
