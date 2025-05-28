<?php
require_once "db.php";
session_start();

$email = $_GET['email'] ?? '';
$reset_id = $_GET['reset_id'] ?? '';

// Validate parameters
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !ctype_digit($reset_id)) {
    header("Location: forgot-pws-employer.php?type=error&message=Invalid password reset link.");
    exit;
}

// Check if reset_id and email are valid and OTP is verified
$stmt = $conn->prepare("SELECT id FROM password_resets WHERE id = ? AND email = ? AND is_verified = 1");
$stmt->bind_param("is", $reset_id, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    header("Location: forgot-pws-employer.php?type=error&message=Invalid or expired reset link.");
    exit;
}
$stmt->close();

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate password
    if (
        strlen($new_password) < 8 ||
        !preg_match('/[A-Z]/', $new_password) ||
        !preg_match('/[a-z]/', $new_password) ||
        !preg_match('/\d/', $new_password) ||
        !preg_match('/[@$!%*?&]/', $new_password)
    ) {
        $alertType = "danger";
        $alertMsg = "Password must be at least 8 characters, include uppercase, lowercase, number, and special character.";
    } elseif ($new_password !== $confirm_password) {
        $alertType = "danger";
        $alertMsg = "Passwords do not match.";
    } else {
        // Hash password (use password_hash for security)
        $hashed = md5($new_password);

        // Update password in job_seeker table
        $update = $conn->prepare("UPDATE employer SET password = ?, secret = ? WHERE email = ?");
        $update->bind_param("sss", $hashed, $new_password, $email);
        $update->execute();
        $update->close();

        // Invalidate all password resets for this email
        $conn->query("DELETE FROM password_resets WHERE email = '" . $conn->real_escape_string($email) . "'");

        // Show success alert and redirect after 2 seconds
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>Password Reset Success</title>
            <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'>
            <script>
                setTimeout(function() {
                    window.location.href = 'user-login-signup.php?type=success&message=Password reset successful. You can now log in.';
                }, 2000);
            </script>
        </head>
        <body>
            <div class='container' style='max-width:400px;margin:80px auto;'>
                <div class='alert alert-success text-center'>
                    Password reset successful! Redirecting to login...
                </div>
            </div>
        </body>
        </html>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | PinoySeaman</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .reset-container { 
            max-width: 400px; 
            padding: 30px; 
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px); 
            border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            align-self: center;
        }
        .alert { margin-bottom: 20px; }
        .reset-pwd-ctn{ 
            display: flex;
            justify-content: center;
            flex-direction: column;
        }

        .reset-pwd-logo{
            display: flex;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border-radius: 10px;
        }

        .reset-pwd-logo img{
            border-radius: 10px;
            width: 300px;
            object-fit: cover;
        } 
        
        .reset-pwd-ctn {
            height: auto;
            align-items: center;
            background-image: url('../pinoyseaman-logo/HeroImage.png');
            background-repeat: no-repeat; 
            background-size: cover;
            background-position: center;
        }

        .form-control{
            background-color: rgba(255, 255, 255, 0.8);
        }

        .reset-container h2{
            margin-bottom: 9px;
        }

        .reset-content{
            margin: 140px auto;
            gap: 20px;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
    
    <main class="reset-pwd-ctn">
        <section class="reset-content">
            <div class="reset-pwd-logo">
                <img src="pinoyseaman-logo/pinoy-logo.png" alt="admin-logo">
            </div>
            <div class="reset-container">
                <h2>Reset Your Password</h2>
                <?php if (!empty($alertMsg)): ?>
                    <div class="alert alert-<?= $alertType ?>"><?= htmlspecialchars($alertMsg) ?></div>
                <?php endif; ?>
                <form method="POST" autocomplete="off">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                    <input type="hidden" name="reset_id" value="<?= htmlspecialchars($reset_id) ?>">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" name="new_password" id="new_password" required
                            pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$"
                            title="At least 8 characters, include uppercase, lowercase, number, and special character.">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                </form>
            </div>
        </section>
    </main>
    <script>
    // Optionally, you can use this to show alerts dynamically
    function showAlert(message, type = 'success') {
        const alertBox = document.getElementById('alert');
        alertBox.textContent = message;
        alertBox.className = `alert alert-${type}`;
        alertBox.style.display = 'block';
        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 3000);
    }
    </script>
</body>
</html>