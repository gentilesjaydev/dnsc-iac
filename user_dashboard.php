<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Enforce login for regular users
requireLogin();

// Redirect admins to their dashboard
if (isAdmin()) {
    header("Location: admin/dashboard");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user's submissions
$stmt = $pdo->prepare("SELECT * FROM covenant_submissions WHERE user_id = ? ORDER BY signed_at DESC");
$stmt->execute([$user_id]);
$submissions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - IAC Covenant</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: var(--bg-page); }
        
        .user-header {
            background: white;
            border-bottom: 1px solid var(--border-subtle);
            padding: 1.5rem 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .action-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
            border: 1px solid var(--border-subtle);
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            text-decoration: none;
            color: var(--text-dark);
        }

        .action-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(124, 58, 237, 0.15);
            border-color: rgba(124, 58, 237, 0.3);
        }

        .action-icon {
            font-size: 3.5rem;
            color: var(--accent-core);
            margin-bottom: 1.5rem;
            background: rgba(124, 58, 237, 0.1);
            width: 100px; height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .action-card:hover .action-icon {
            background: var(--accent-gradient);
            color: white;
            transform: scale(1.1);
        }

        .action-title {
            font-family: 'Outfit';
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
        }

        .doc-history-card {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--border-subtle);
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }

        .doc-history-header {
            background: rgba(124, 58, 237, 0.05);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-subtle);
            font-family: 'Outfit';
            font-weight: 600;
            color: var(--accent-deep);
        }

        .doc-item {
            padding: 1.2rem 2rem;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }

        .doc-item:hover {
            background: #f8fafc;
        }

        .doc-item:last-child {
            border-bottom: none;
        }

        .btn-sm-tech {
            background: rgba(124, 58, 237, 0.1);
            color: var(--accent-core);
            border: 1px solid rgba(124, 58, 237, 0.2);
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-sm-tech:hover {
            background: var(--accent-core);
            color: white;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="user-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <img src="assets/images/dnsclogo.png" alt="DNSC" style="height: 40px;">
                <img src="assets/images/iclogo.png" alt="IC" style="height: 40px;">
                <h4 style="font-family: 'Outfit'; font-weight: 800; color: var(--text-dark); margin: 0; padding-left: 10px; border-left: 2px solid var(--border-subtle);">
                    IAC <span style="color: var(--accent-core);">Covenant</span>
                </h4>
            </div>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--accent-gradient); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; margin-right: 10px;">
                        <?php echo substr($_SESSION['full_name'], 0, 1); ?>
                    </div>
                    <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item" href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket me-2 text-danger"></i> Sign Out</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container py-5">
        
        <div class="row mb-5 pb-3 border-bottom">
            <div class="col-12">
                <h2 style="font-family: 'Outfit'; font-weight: 800; color: var(--accent-deep);">Partner Dashboard</h2>
                <p class="text-muted" style="font-size: 1.1rem;">Manage your organization's commitments to the IAC Covenant alliance.</p>
            </div>
        </div>

        <!-- Main Action Links -->
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <a href="covenant.php" class="action-card text-decoration-none">
                    <div class="action-icon">
                        <i class="fa-solid fa-signature"></i>
                    </div>
                    <div class="action-title">Read & Sign Covenant</div>
                    <p class="text-muted mb-0">Fill out the official form and digitally sign the agreement of commitment.</p>
                </a>
            </div>
            
            <div class="col-md-6">
                <a href="signature-wall.php" target="_blank" class="action-card text-decoration-none" style="border-style: dashed;">
                    <div class="action-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fa-solid fa-display"></i>
                    </div>
                    <div class="action-title">View Live Signature Wall</div>
                    <p class="text-muted mb-0">Watch the live projector mode update as partners sign the covenant.</p>
                </a>
            </div>
        </div>

        <!-- Document History -->
        <div class="row">
            <div class="col-12">
                <div class="doc-history-card">
                    <div class="doc-history-header d-flex justify-content-between align-items-center">
                        <span class="fs-5"><i class="fa-regular fa-folder-open me-2"></i> My Signed Documents</span>
                        <span class="badge bg-primary rounded-pill"><?php echo count($submissions); ?> Total</span>
                    </div>
                    
                    <div class="doc-history-body">
                        <?php if (count($submissions) > 0): ?>
                            <?php foreach ($submissions as $sub): ?>
                                <div class="doc-item flex-column flex-md-row gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="font-size: 2rem; color: #ef4444;"><i class="fa-solid fa-file-pdf"></i></div>
                                        <div>
                                            <h6 class="mb-1" style="font-weight: 600; color: var(--text-dark);">
                                                Covenant of Commitment - <?php echo htmlspecialchars($sub['organization_name']); ?>
                                            </h6>
                                            <div class="text-muted small">
                                                <i class="fa-regular fa-clock me-1"></i> Signed on <?php echo date('M d, Y h:i A', strtotime($sub['signed_at'])); ?> 
                                                <span class="mx-2">|</span> 
                                                <i class="fa-regular fa-id-badge me-1"></i> Represented by: <?php echo htmlspecialchars($sub['represented_by']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <?php if (!empty($sub['pdf_file'])): ?>
                                            <a href="assets/pdfs/<?php echo urlencode($sub['pdf_file']); ?>" target="_blank" class="btn-sm-tech">
                                                <i class="fa-solid fa-download me-1"></i> Download PDF
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small"><em>PDF missing</em></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;">
                                    <i class="fa-solid fa-file-circle-xmark"></i>
                                </div>
                                <h5>No documents signed yet</h5>
                                <p class="text-muted mb-4">You have not endorsed the covenant yet.</p>
                                <a href="covenant.php" class="btn btn-tech">Sign the Covenant Now</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>





