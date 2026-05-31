<?php
session_start();

// Verify file parameter
if (!isset($_GET['file'])) {
    die("Direct access not allowed.");
}

$file = basename($_GET['file']);
$img_path = "assets/signatures/" . $file;

if (!file_exists($img_path)) {
    die("Signature not found.");
}

// Security: Prevent directory traversal (handled by basename but double check)
$file_ext = strtolower(pathinfo($img_path, PATHINFO_EXTENSION));
if (!in_array($file_ext, ['png', 'jpg', 'jpeg'])) {
    die("Invalid file type.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($file); ?></title>
    <!-- Favicon -->
    <link rel="icon" href="assets/images/iclogo.png" type="image/png">

    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #0e0e0e; /* Chrome default image viewer background */
        }
        img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            /* Add a subtle white background if the signature is transparent */
            background-color: white; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>
    <img src="<?php echo $img_path; ?>" alt="Signature Image">
</body>
</html>
