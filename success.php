<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isset($_GET['id'])) {
    header("Location: covenant.php");
    exit;
}

$submission_id = (int) $_GET['id'];

// Guest mode: Only verify that the submission exists
$stmt = $pdo->prepare("SELECT * FROM covenant_submissions WHERE id = ?");
$stmt->execute([$submission_id]);
$submission = $stmt->fetch();

if (!$submission) {
    header("Location: covenant.php");
    exit;
}

$pdf_link = "assets/pdfs/" . htmlspecialchars($submission['pdf_file']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success - Sign Covenant</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/iclogo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        body {
            background: var(--bg-page);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .success-card {
            background: white;
            border-radius: 20px;
            padding: 4rem 3rem;
            text-align: center;
            box-shadow: var(--shadow-float);
            max-width: 600px;
            width: 100%;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border-subtle);
        }

        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--accent-gradient);
        }

        .check-icon-wrapper {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(124, 58, 237, 0.1);
            color: #7c3aed;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            margin: 0 auto 2rem;
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-title {
            font-family: 'Outfit';
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 1rem;
            font-size: 2rem;
        }

        .success-message {
            color: var(--text-muted);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .btn-download {
            background: rgba(124, 58, 237, 0.1);
            color: var(--accent-deep);
            border: 2px solid rgba(124, 58, 237, 0.2);
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-download:hover {
            background: var(--accent-core);
            color: white;
            border-color: var(--accent-core);
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <div class="success-card">
        <div class="check-icon-wrapper">
            <i class="fa-solid fa-check"></i>
        </div>
        <h2 class="success-title">Covenant Signed!</h2>
        <p class="success-message">
            Thank you, <strong><?php echo htmlspecialchars($submission['represented_by']); ?></strong>.<br>
            Your digital signature has been recorded and a verified PDF of the Covenant of Commitment has been
            permanently generated.
        </p>

        <div class="d-flex justify-content-center flex-column align-items-center">
            <div class="mb-4">
                <a href="view_certificate?file=<?php echo urlencode($submission['pdf_file']); ?>" target="_blank"
                    class="btn btn-primary btn-lg px-4 mb-3">
                    <i class="fa-solid fa-file-pdf me-2"></i> View & Download PDF
                </a>
            </div>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="signature-wall" class="btn btn-outline-primary btn-lg px-4 rounded-pill">
                    <i class="fa-solid fa-users-rectangle me-2"></i> View Signature Wall
                </a>

                <a href="covenant" class="btn btn-outline-secondary btn-lg px-4 rounded-pill">
                    Sign Another
                </a>
            </div>
        </div>
    </div>

    <script>
        // Trigger cool tech confetti on load
        window.onload = function () {
            var duration = 3000;
            var end = Date.now() + duration;

            (function frame() {
                confetti({
                    particleCount: 5,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                    colors: ['#7c3aed', '#ffffff', '#5b21b6']
                });
                confetti({
                    particleCount: 5,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                    colors: ['#7c3aed', '#ffffff', '#5b21b6']
                });

                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            }());
        };
    </script>
</body>

</html>




