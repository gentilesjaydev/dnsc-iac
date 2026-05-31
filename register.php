<?php
require_once 'includes/db.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: user_dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $sig_method = $_POST['sig_method'] ?? 'draw';
    $signature_file = '';

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email address already exists in the system.";
        } else {
            // Handle Signature Saving
            $upload_dir = 'assets/signatures/users/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            if ($sig_method === 'upload' && !empty($_FILES['sig_upload']['name'])) {
                // Handle PNG Upload
                $file_ext = strtolower(pathinfo($_FILES['sig_upload']['name'], PATHINFO_EXTENSION));
                if ($file_ext !== 'png') {
                    $error = "Only PNG files with transparent backgrounds are allowed for uploads.";
                } else {
                    $signature_file = 'user_sig_' . time() . '_' . uniqid() . '.png';
                    move_uploaded_file($_FILES['sig_upload']['tmp_name'], $upload_dir . $signature_file);
                }
            } else if ($sig_method === 'draw' && !empty($_POST['sig_data'])) {
                // Handle Drawn Signature (Base64)
                $sig_data = $_POST['sig_data'];
                $sig_data = str_replace('data:image/png;base64,', '', $sig_data);
                $sig_data = str_replace(' ', '+', $sig_data);
                $data = base64_decode($sig_data);
                $signature_file = 'user_sig_' . time() . '_' . uniqid() . '.png';
                file_put_contents($upload_dir . $signature_file, $data);
            } else {
                $error = "A signature is required to create an account.";
            }

            if (empty($error)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, signature_file) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$full_name, $email, $hashed_password, $signature_file])) {
                    $success = "Account created successfully! You can now login.";
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - IAC</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Signature Pad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <style>
        .auth-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 0; }
        .auth-card { background: var(--bg-doc); border-radius: 20px; box-shadow: var(--shadow-float); padding: 2.5rem; width: 100%; max-width: 600px; border: 1px solid var(--border-subtle); position: relative; }
        .auth-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px; background: var(--accent-gradient); border-top-left-radius: 20px; border-top-right-radius: 20px; }
        .sig-container { border: 2px dashed #cbd5e1; border-radius: 12px; background: #f8fafc; overflow: hidden; height: 160px; position: relative; }
        canvas { width: 100%; height: 100%; cursor: crosshair; }
        .method-toggle { display: flex; gap: 10px; margin-bottom: 15px; }
        .method-btn { flex: 1; padding: 10px; border: 1px solid #cbd5e1; background: white; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .method-btn.active { background: var(--accent-core); color: white; border-color: var(--accent-core); }
    </style>
</head>
<body>

    <div class="container auth-container">
        <div class="auth-card">
            <div class="text-center mb-4">
                <h4 style="font-family: 'Outfit'; font-weight: 700; color: var(--accent-deep);">Partner Registration</h4>
                <p class="text-muted small">Register your account with your digital signature</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success py-2 small"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST" enctype="multipart/form-data" id="regForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="field-label mb-1">Full Name</label>
                        <input type="text" name="full_name" class="form-control tech-input" required placeholder="John Doe">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="field-label mb-1">Email Address</label>
                        <input type="email" name="email" class="form-control tech-input" required placeholder="john@example.com">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="field-label mb-1">Password</label>
                        <input type="password" name="password" class="form-control tech-input" minlength="8" required placeholder="********">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="field-label mb-1">Confirm</label>
                        <input type="password" name="confirm_password" class="form-control tech-input" minlength="8" required placeholder="********">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="field-label mb-2">Digital Signature <span class="text-danger">*</span></label>
                    <div class="method-toggle">
                        <button type="button" class="method-btn active" data-method="draw">Draw Signature</button>
                        <button type="button" class="method-btn" data-method="upload">Upload PNG</button>
                    </div>
                    <input type="hidden" name="sig_method" id="sig_method" value="draw">
                    
                    <div id="draw_section">
                        <div class="sig-container">
                            <canvas id="sig_canvas"></canvas>
                        </div>
                        <div class="text-end mt-1">
                            <button type="button" class="btn btn-sm py-0 text-muted" id="clear_sig"><u>Clear Drawing</u></button>
                        </div>
                        <input type="hidden" name="sig_data" id="sig_data">
                    </div>

                    <div id="upload_section" style="display:none;">
                        <input type="file" name="sig_upload" class="form-control tech-input" accept="image/png">
                        <p class="small text-muted mt-2 mb-0">Please upload a <strong>PNG</strong> file with a transparent background.</p>
                    </div>
                </div>

                <button type="submit" class="btn btn-tech w-100 mb-3" <?php echo $success ? 'disabled' : ''; ?>>Create Account & Save Signature</button>
                <div class="text-center"><p class="text-muted small mb-0">Already have an account? <a href="login.php" class="fw-bold">Sign in here</a></p></div>
            </form>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('sig_canvas');
        const signaturePad = new SignaturePad(canvas, { penColor: "rgb(15, 23, 42)" });
        
        function resize() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }
        window.addEventListener("resize", resize);
        resize();

        document.querySelectorAll('.method-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const method = btn.dataset.method;
                document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('sig_method').value = method;
                document.getElementById('draw_section').style.display = method === 'draw' ? 'block' : 'none';
                document.getElementById('upload_section').style.display = method === 'upload' ? 'block' : 'none';
                if(method === 'draw') resize();
            });
        });

        document.getElementById('clear_sig').addEventListener('click', () => signaturePad.clear());
        
        document.getElementById('regForm').addEventListener('submit', (e) => {
            if(document.getElementById('sig_method').value === 'draw' && !signaturePad.isEmpty()) {
                document.getElementById('sig_data').value = signaturePad.toDataURL();
            }
        });
    </script>
</body>
</html>
