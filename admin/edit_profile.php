<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/logger.php';

// Enforce admin access only
requireAdmin();

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Fetch current admin details
$stmt = $pdo->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_admin = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($full_name) || empty($email)) {
        $error_msg = "Full Name and Email are required.";
    } else {
        try {
            // Update basic info
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $user_id]);
            $_SESSION['full_name'] = $full_name; // Update session name

            // Update password if provided
            if (!empty($new_password)) {
                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashed_password, $user_id]);
                    $success_msg = "Profile and password updated successfully.";
                    logActivity($pdo, $user_id, 'profile_update', 'Updated profile details and password');
                } else {
                    $error_msg = "Passwords do not match.";
                }
            } else {
                $success_msg = "Profile updated successfully.";
                logActivity($pdo, $user_id, 'profile_update', 'Updated profile details');
            }

            // Refresh current data
            $current_admin['full_name'] = $full_name;
            $current_admin['email'] = $email;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error_msg = "This email address is already in use.";
            } else {
                $error_msg = "An error occurred: " . $e->getMessage();
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
    <title>Edit Profile - Admin IAC Covenant</title>
    <!-- Favicon -->
    <link rel="icon" href="../assets/images/iclogo.png" type="image/png">
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/iclogo.png" type="image/x-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo filemtime('../assets/css/style.css'); ?>">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Outfit Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php $active_page = 'edit_profile'; include 'sidebar.php'; ?>

    <div class="admin-content">
        <div class="mb-5">
            <h3 style="font-family: 'Outfit'; font-weight: 700; color: var(--accent-deep);">Account Settings</h3>
            <p class="text-muted">Update your administrative credentials and profile information.</p>
        </div>

        <div class="profile-card">
            <form action="edit_profile.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" name="full_name" class="form-control"
                        value="<?php echo htmlspecialchars($current_admin['full_name']); ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control"
                        value="<?php echo htmlspecialchars($current_admin['email']); ?>" required>
                </div>

                <hr class="my-4">
                <h6 class="mb-3 text-muted">Change Password (leave blank to keep current)</h6>

                <div class="mb-3">
                    <label class="form-label fw-bold">New Password</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Minimum 8 characters">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control">
                </div>

                <button type="submit" class="btn btn-tech px-5">Save Changes</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            $('#adminSidebar').toggleClass('show');
            $('#sidebarOverlay').toggleClass('show');
        }

        function confirmLogout(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Sign Out?',
                text: "Are you sure you want to end your current session?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Sign Out'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../logout';
                }
            });
        }

        <?php if ($success_msg): ?>
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated',
                text: '<?php echo $success_msg; ?>',
                confirmButtonColor: '#7c3aed'
            });
        <?php endif; ?>

        <?php if ($error_msg): ?>
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: '<?php echo $error_msg; ?>',
                confirmButtonColor: '#7c3aed'
            });
        <?php endif; ?>
    </script>
</body>

</html>





