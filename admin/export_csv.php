<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Enforce admin access
requireAdmin();

$filename = "raise_davao_submissions_" . date('Y-m-d') . ".csv";

// Fetch all submissions
$stmt = $pdo->query("SELECT c.*, u.full_name, u.email as user_email 
                     FROM covenant_submissions c 
                     LEFT JOIN users u ON c.user_id = u.id 
                     ORDER BY c.signed_at DESC");
$submissions = $stmt->fetchAll();

// Set headers for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

// Set CSV header row
fputcsv($output, [
    'Submission ID', 
    'Organization Name', 
    'Type of Institution', 
    'Represented By', 
    'Position', 
    'Email Address', 
    'Contact Number', 
    'Signed At', 
    'IP Address',
    'User Full Name',
    'User Account Email'
]);

// Write data rows
foreach ($submissions as $row) {
    fputcsv($output, [
        $row['id'],
        $row['organization_name'],
        $row['institution_type'],
        $row['represented_by'],
        $row['position_title'],
        $row['email_address'],
        $row['contact_number'],
        $row['signed_at'],
        $row['ip_address'],
        $row['full_name'],
        $row['user_email']
    ]);
}

fclose($output);
exit;
?>
