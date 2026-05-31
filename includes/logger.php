<?php
// Activity Logging Helper Function
function logActivity($pdo, $user_id, $activity_type, $description, $guest_name = NULL)
{
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, guest_name, activity_type, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $guest_name,
            $activity_type,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? '::1',
            $_SERVER['HTTP_USER_AGENT'] ?? 'System'
        ]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
?>