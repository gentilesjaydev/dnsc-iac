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
    // Determine current event filter
    $isAllEvents = isset($_GET['event_id']) && $_GET['event_id'] === 'all';
    $eventId = isset($_GET['event_id']) && $_GET['event_id'] !== 'all' ? (int)$_GET['event_id'] : 0;
    
    if (!$isAllEvents && $eventId === 0) {
        $stmtEvent = $pdo->query("SELECT id FROM events WHERE is_active = 1 LIMIT 1");
        $eventId = (int)$stmtEvent->fetchColumn();
        if (!$eventId) {
            $stmtEvent = $pdo->query("SELECT id FROM events LIMIT 1");
            $eventId = (int)$stmtEvent->fetchColumn();
        }
    }

    // 1. Fetch Total Signatories
    if ($isAllEvents) {
        $stmtTotal = $pdo->query("SELECT COUNT(*) FROM covenant_submissions");
        $totalSignatories = (int) $stmtTotal->fetchColumn();
    } else {
        $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM covenant_submissions WHERE event_id = ?");
        $stmtTotal->execute([$eventId]);
        $totalSignatories = (int) $stmtTotal->fetchColumn();
    }

    // 2. Fetch Signings Today
    if ($isAllEvents) {
        $stmtToday = $pdo->query("SELECT COUNT(*) FROM covenant_submissions WHERE DATE(signed_at) = CURDATE()");
        $signingsToday = (int) $stmtToday->fetchColumn();
    } else {
        $stmtToday = $pdo->prepare("SELECT COUNT(*) FROM covenant_submissions WHERE DATE(signed_at) = CURDATE() AND event_id = ?");
        $stmtToday->execute([$eventId]);
        $signingsToday = (int) $stmtToday->fetchColumn();
    }

    // 3. Fetch Recent Submissions (Limit 10)
    if ($isAllEvents) {
        $stmtRecent = $pdo->query("SELECT c.*, u.full_name, u.email 
                                   FROM covenant_submissions c 
                                   LEFT JOIN users u ON c.user_id = u.id 
                                   ORDER BY c.signed_at DESC LIMIT 10");
    } else {
        $stmtRecent = $pdo->prepare("SELECT c.*, u.full_name, u.email 
                                   FROM covenant_submissions c 
                                   LEFT JOIN users u ON c.user_id = u.id 
                                   WHERE c.event_id = ?
                                   ORDER BY c.signed_at DESC LIMIT 10");
        $stmtRecent->execute([$eventId]);
    }
    $recentSubmissions = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

    // Format dates for friendly display
    foreach ($recentSubmissions as &$sub) {
        $sub['date_friendly'] = date('M d, Y', strtotime($sub['signed_at']));
        $sub['time_friendly'] = date('h:i A', strtotime($sub['signed_at']));
    }

    // 4. Fetch Institution Type Breakdown
    if ($isAllEvents) {
        $stmtTypes = $pdo->query("SELECT institution_type, COUNT(*) as count FROM covenant_submissions GROUP BY institution_type");
    } else {
        $stmtTypes = $pdo->prepare("SELECT institution_type, COUNT(*) as count FROM covenant_submissions WHERE event_id = ? GROUP BY institution_type");
        $stmtTypes->execute([$eventId]);
    }
    $typeBreakdown = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);

    // 5. Fetch Hourly Signings (Today)
    if ($isAllEvents) {
        $stmtHourly = $pdo->query("SELECT HOUR(signed_at) as hour, COUNT(*) as count FROM covenant_submissions WHERE DATE(signed_at) = CURDATE() GROUP BY HOUR(signed_at) ORDER BY hour");
    } else {
        $stmtHourly = $pdo->prepare("SELECT HOUR(signed_at) as hour, COUNT(*) as count FROM covenant_submissions WHERE DATE(signed_at) = CURDATE() AND event_id = ? GROUP BY HOUR(signed_at) ORDER BY hour");
        $stmtHourly->execute([$eventId]);
    }
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
