<?php
session_start();
require_once 'includes/db.php';

// Verify file parameter
if (!isset($_GET['file'])) {
    die("Direct access not allowed.");
}

$file = basename($_GET['file']);
$pdf_path = "assets/pdfs/" . $file;

if (!file_exists($pdf_path)) {
    die("Certificate not found.");
}

// Security: Prevent directory traversal (handled by basename but double check)
$file_ext = strtolower(pathinfo($pdf_path, PATHINFO_EXTENSION));
if ($file_ext !== 'pdf') {
    die("Invalid file type.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Covenant Certificate -
        <?php echo htmlspecialchars($file); ?>
    </title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/iclogo.png" type="image/x-icon">

    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            background-color: #525659;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            background-color: #323639;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            height: 40px;
        }

        .header-title {
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-logo {
            height: 24px;
            border-radius: 4px;
        }

        .btn-download {
            background-color: #7c3aed;
            color: white;
            text-decoration: none;
            padding: 6px 15px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn-download:hover {
            background-color: #0284c7;
        }

        .viewer-container {
            height: calc(100vh - 60px);
            width: 100%;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-title">
            <img src="assets/images/iclogo.png" class="header-logo" alt="IAC">
            Covenant of Commitment - Digital Certificate
        </div>
        <a href="<?php echo $pdf_path; ?>" download class="btn-download">
            <i class="fa-solid fa-download"></i> Download Original
        </a>
    </div>

    <div class="viewer-container">
        <iframe src="<?php echo $pdf_path; ?>#toolbar=1&navpanes=0&scrollbar=1"></iframe>
    </div>

</body>

</html>

