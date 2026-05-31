<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/logger.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] == 'inactive') {
            $error = 'Your account has been disabled. Please contact the administrator.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Log the activity
            logActivity($pdo, $user['id'], 'login', 'User logged into the system');

            $login_success = true;
            $redirect_url = ($user['role'] === 'admin') ? "admin/dashboard" : "user_dashboard";
        }
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IAC Covenant</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/iclogo.png" type="image/x-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime('assets/css/style.css'); ?>">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            background: var(--bg-doc);
            border-radius: 20px;
            box-shadow: var(--shadow-float);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            border: 1px solid var(--border-subtle);
            position: relative;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--accent-gradient);
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .form-floating>.form-control,
        .form-floating>.form-control-plaintext {
            padding: 1rem 0.75rem;
        }

        .form-floating>.form-control::placeholder {
            color: transparent;
        }

        .form-floating>label {
            padding: 1rem 0.75rem;
        }
    </style>
</head>

<body>

    <div class="container auth-container">
        <div class="auth-card">

            <div class="text-center mb-4">
                <img src="assets/images/dnsclogo.png" alt="Logo" style="width: 70px; margin-bottom: 20px;"
                    onerror="this.style.display='none'">
                <img src="assets/images/iclogo.png" alt="Logo" style="width: 95px; margin-bottom: 20px; margin-left: 10px;"
                    onerror="this.style.display='none'">
                <h4 style="font-family: 'Outfit'; font-weight: 700; color: var(--accent-deep);">IAC Covenant System</h4>
                <p class="text-muted small">Authorized access to the administrative dashboard</p>
            </div>

            <form action="login" method="POST">
                <div class="mb-3">
                    <label class="field-label mb-2">Email Address</label>
                    <input type="email" name="email" class="form-control tech-input w-100" required
                        placeholder="Enter your email">
                </div>

                <div class="mb-4">
                    <label class="field-label mb-2">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passwordInput" class="form-control tech-input" required
                            placeholder="Enter your password">
                        <button class="btn btn-outline-secondary border" type="button" id="togglePassword" style="border-left: none !important; color: #64748b; background: white;">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-tech w-100 mb-3">Login Securely</button>

            </form>

        </div>
    </div>

    <?php if ($error): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Authentication Failed',
                text: '<?php echo $error; ?>',
                confirmButtonColor: '#7c3aed'
            });
        </script>
    <?php endif; ?>

    <?php if (isset($login_success) && $login_success): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Welcome Back!',
                text: 'Login successful. Redirecting to your dashboard...',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            }).then(() => {
                window.location.href = '<?php echo $redirect_url; ?>';
            });
        </script>
    <?php endif; ?>

    <script>
        // Password Toggle Logic
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>

</html>




