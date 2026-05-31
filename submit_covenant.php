<?php

session_start();
require_once 'includes/db.php';
require_once 'includes/logger.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // 1. Gather form data
    $organization_name = trim($_POST['organization_name'] ?? '');
    $institution_type = trim($_POST['institution_type'] ?? '');
    $represented_by = trim($_POST['represented_by'] ?? '');
    $position_title = trim($_POST['position_title'] ?? '');
    $email_address = trim($_POST['email_address'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $signature_data = $_POST['signature_data'] ?? '';
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Get active event ID
    $stmtEvent = $pdo->query("SELECT * FROM events WHERE is_active = 1 LIMIT 1");
    $activeEvent = $stmtEvent->fetch();
    if (!$activeEvent) {
        $_SESSION['error_msg'] = "Submissions are currently locked because there is no active event.";
        header("Location: covenant.php");
        exit;
    }
    
    // Check if signing is allowed for this event
    $eventDateRaw = $activeEvent['event_date'];
    $eventYMD = date('Y-m-d', strtotime($eventDateRaw));
    $todayYMD = date('Y-m-d');

    if (isset($activeEvent['is_signing_open']) && $activeEvent['is_signing_open'] == 0) {
        $_SESSION['error_msg'] = "The administrator has closed the signing form for this event.";
        header("Location: covenant.php");
        exit;
    } elseif (isset($activeEvent['is_exact_date_only']) && $activeEvent['is_exact_date_only'] == 1) {
        if ($eventYMD !== $todayYMD) {
            $_SESSION['error_msg'] = "Signing for this event is strictly allowed only on the specific event date.";
            header("Location: covenant.php");
            exit;
        }
    }
    
    $activeEventId = $activeEvent['id'];



    // Basic Validation
    if (empty($organization_name) || empty($institution_type) || empty($represented_by) || empty($email_address) || empty($signature_data)) {
        $_SESSION['error_msg'] = "Please fill in all required fields and provide a signature.";
        header("Location: covenant");
        exit;
    }

    // STRICT duplicate check: One signature per real person (by Full Name)
    // Even if they use a different email or organization, if the name is the same, they cannot sign again.
    $checkStmt = $pdo->prepare("SELECT id FROM covenant_submissions WHERE represented_by = ?");
    $checkStmt->execute([$represented_by]);
    if ($checkStmt->rowCount() > 0) {
        $_SESSION['error_msg'] = "The name '$represented_by' has already signed the covenant. Each participant may only sign once.";
        header("Location: covenant");
        exit;
    }

    // 2. Process Base64 Signature to Image File
    try {
        // Ensure required directories exist
        if (!is_dir('assets/signatures')) {
            mkdir('assets/signatures', 0755, true);
        }
        if (!is_dir('assets/pdfs')) {
            mkdir('assets/pdfs', 0755, true);
        }

        $safe_name = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '_', $represented_by));
        $file_name = 'sign_guest_' . time() . '_' . $safe_name . '.png';
        $file_path = 'assets/signatures/' . $file_name;

        // Extract base64 data
        $sig_parts = explode(',', $signature_data);
        $sig_base64 = end($sig_parts);
        $sig_decoded = base64_decode($sig_base64);

        if (!file_put_contents($file_path, $sig_decoded)) {
            throw new Exception("Failed to save signature image.");
        }

        // 3. Generate PDF
        require_once 'includes/pdf_generator.php';

        $pdf_filename = 'covenant_guest_' . time() . '_' . $safe_name . '.pdf';
        $pdf_path = 'assets/pdfs/' . $pdf_filename;

        $submission_data = [
            'organization_name' => $organization_name,
            'institution_type' => $institution_type,
            'represented_by' => $represented_by,
            'position_title' => $position_title,
            'email_address' => $email_address,
            'contact_number' => $contact_number,
            'signed_at' => date('Y-m-d H:i:s'),
            'ip_address' => $ip_address
        ];


        // Generate the PDF
        generateCovenantPDF($submission_data, $file_path, $pdf_path, $activeEvent);


        // 4. Generate Random Positioning for the Signature Wall
        $pos_left = rand(5, 85) + (mt_rand() / mt_getrandmax());
        $pos_top = rand(10, 80) + (mt_rand() / mt_getrandmax());
        $pos_rotation = rand(-12, 12) + (mt_rand() / mt_getrandmax());
        $pos_scale = 0.85 + ((mt_rand() / mt_getrandmax()) * 0.3);


        $stmt = $pdo->prepare("INSERT INTO covenant_submissions 
            (event_id, user_id, organization_name, institution_type, represented_by, position_title, email_address, contact_number, signature_file, pdf_file, ip_address, pos_left, pos_top, pos_rotation, pos_scale) 
            VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $activeEventId,
            $organization_name,
            $institution_type,
            $represented_by,
            $position_title,
            $email_address,
            $contact_number,
            $file_name,
            $pdf_filename,
            $ip_address,
            $pos_left,
            $pos_top,
            $pos_rotation,
            $pos_scale
        ]);

        $submission_id = $pdo->lastInsertId();


        // 6. Log the activity (Guest mode)
        logActivity($pdo, NULL, 'covenant_signed', "Guest '$represented_by' signed the covenant for '$organization_name'", $represented_by);

        // 7. Trigger Email via Brevo API (Wrapped to prevent hangs)
        try {
            require_once 'includes/brevo_mailer.php';
            @sendCovenantEmail($email_address, $represented_by, $pdf_path, $activeEvent);
        } catch (Exception $mailEx) {
            error_log("Email notification failed: " . $mailEx->getMessage());
        }


        // Redirect to Success Page
        header("Location: success?id=" . $submission_id);
        exit;

    } catch (Exception $e) {

        if (isset($file_path) && file_exists($file_path))
            unlink($file_path);
        $_SESSION['error_msg'] = "Error: " . $e->getMessage();
        header("Location: covenant");
        exit;
    }

} else {
    header("Location: covenant");
    exit;
}
?>