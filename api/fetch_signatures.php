<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

try {
    // Fetch all signatures, order by newest first
    $stmt = $pdo->query("SELECT id, organization_name, represented_by, signature_file, pos_top, pos_left, pos_rotation, pos_scale FROM covenant_submissions ORDER BY signed_at DESC");
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