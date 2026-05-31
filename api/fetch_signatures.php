<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

try {
    // Fetch active event id
    $stmtEvent = $pdo->query("SELECT id FROM events WHERE is_active = 1 LIMIT 1");
    $activeEventId = $stmtEvent->fetchColumn();
    if (!$activeEventId) $activeEventId = 1;

    // Fetch all signatures for active event, order by newest first
    $stmt = $pdo->prepare("SELECT id, organization_name, represented_by, signature_file, pos_top, pos_left, pos_rotation, pos_scale FROM covenant_submissions WHERE event_id = ? ORDER BY signed_at DESC");
    $stmt->execute([$activeEventId]);
    $signatures = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $signatures
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>