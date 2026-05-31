<?php
/**
 * exit-privacy.php
 * A professional exit page for users who decline the Data Privacy Agreement.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commitment Declined - IAC Covenant</title>
    <!-- Favicon -->
    <link rel="icon" href="assets/images/iclogo.png" type="image/png">
    <link rel="shortcut icon" href="assets/images/iclogo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent-purple: #7c3aed;
            --deep-navy: #5b21b6;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }
        .exit-card {
            background: white;
            padding: 4rem 3rem;
            border-radius: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            max-width: 550px;
            width: 90%;
            text-align: center;
            border: 1px solid rgba(124, 58, 237, 0.1);
            position: relative;
        }
        .icon-box {
            width: 100px;
            height: 100px;
            background: rgba(124, 58, 237, 0.05);
            color: var(--accent-blue);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 2rem;
        }
        h1 {
            color: var(--deep-navy);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        p {
            color: #64748b;
            line-height: 1.8;
            font-size: 1.1rem;
            margin-bottom: 2.5rem;
        }
        .btn-return {
            background: var(--deep-navy);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 100px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-return:hover {
            background: var(--accent-blue);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(124, 58, 237, 0.2);
        }
        .countdown {
            margin-top: 3rem;
            font-size: 0.85rem;
            color: #94a3b8;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .bg-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 30px 30px;
            z-index: -1;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="bg-grid"></div>
    
    <div class="exit-card">
        <div class="icon-box">
            <i class="fa-solid fa-handshake-slash"></i>
        </div>
        <h1>Action Discontinued</h1>
        <p>
            You have chosen not to agree to the Data Privacy terms. As a result, we cannot proceed with your digital covenant signature at this time.
        </p>
        
        <div class="d-flex flex-column gap-3 align-items-center">
            <a href="index" class="btn-return">
                <i class="fa-solid fa-house"></i> Return to Home
            </a>
            <a href="https://dnsc.edu.ph/" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
                <i class="fa-solid fa-building-columns me-2"></i> Visit Official DNSC Website
            </a>
        </div>

        <div class="mt-5 pt-3 border-top">
            <span class="text-muted" style="font-size: 0.85rem; opacity: 0.8;">
                <i class="fa-solid fa-shield-halved me-1"></i> Your privacy is our priority. Thank you.
            </span>
        </div>
    </div>
</body>
</html>





