<?php
session_start();
// For the one-day event, everyone goes straight to the covenant unless it's an admin
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header("Location: admin/dashboard");
} else {
    header("Location: covenant");
}
exit;
?>