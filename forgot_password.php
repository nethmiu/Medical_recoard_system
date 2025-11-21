<?php
// forgot_password.php
require_once 'includes/db_connect.php';

$step = 1; // Start with step 1: Enter username
$username = '';
$error_message = '';
$success_message = '';

// Handle Step 1: Check if username exists
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check_username'])) {
    $username = $_POST['username'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Username exists, proceed to step 2
        $step = 2;
    } else {
        $error_message = "This username does not exist in the system. Please try again.";
    }
    $stmt->close();
}

// Handle Step 2: Reset the password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        // Passwords match, hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the database
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hashed_password, $username);

        if ($stmt->execute()) {
            $success_message = "Password has been changed successfully! You can now <a href='login.php'>Login</a>.";
            $step = 3; // Go to a final success step
        } else {
            $error_message = "An error occurred while changing the password. Please try again.";
            $step = 2; // Stay on step 2 to show the error
        }
        $stmt->close();
    } else {
        $error_message = "The two passwords you entered do not match.";
        $step = 2; // Stay on step 2 to show the error
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Medical System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .header-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .medical-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        h2 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #718096;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 10px;
        }

        .step {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e2e8f0;
            transition: all 0.3s ease;
        }

        .step.active {
            background: #667eea;
            transform: scale(1.2);
        }

        .step.completed {
            background: #48bb78;
        }

        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #e53e3e;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .success-message {
            background: #c6f6d5;
            color: #22543d;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #38a169;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .success-message a {
            color: #2b6cb0;
            text-decoration: none;
            font-weight: 500;
        }

        .success-message a:hover {
            text-decoration: underline;
        }

        .form-description {
            color: #4a5568;
            font-size: 14px;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: #2d3748;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .input-container {
            position: relative;
        }

        .input-container i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 16px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            color: #764ba2;
            gap: 8px;
        }

        .username-highlight {
            color: #667eea;
            font-weight: 600;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
                padding: 30px 25px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            .medical-icon {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="login-container fade-in">
        <div class="header-section">
            <div class="medical-icon">
                <i class="fas fa-user-lock"></i>
            </div>
            <h2>Reset Password</h2>
            <div class="subtitle">Medical System</div>
        </div>

        <div class="step-indicator">
            <div class="step <?php echo ($step >= 1) ? 'active' : ''; ?>"></div>
            <div class="step <?php echo ($step >= 2) ? 'active' : ''; ?> <?php echo ($step > 2) ? 'completed' : ''; ?>"></div>
            <div class="step <?php echo ($step >= 3) ? 'active completed' : ''; ?>"></div>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php // Step 1: Form to enter username ?>
        <?php if ($step == 1): ?>
            <div class="form-description">
                Please enter your account's username to begin the password reset process.
            </div>
            <form action="forgot_password.php" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <div class="input-container">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" id="username" required>
                    </div>
                </div>
                <button type="submit" name="check_username" class="btn">
                    <i class="fas fa-search" style="margin-right: 8px;"></i>
                    Check Username
                </button>
                <div class="back-link">
                    <a href="login.php">
                        <i class="fas fa-arrow-left"></i>
                        Back to Login
                    </a>
                </div>
            </form>
        <?php endif; ?>

        <?php // Step 2: Form to reset password ?>
        <?php if ($step == 2): ?>
            <div class="form-description">
                Enter a new password for username '<span class="username-highlight"><?php echo htmlspecialchars($username); ?></span>'.
            </div>
            <form action="forgot_password.php" method="post">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="new_password" id="new_password" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                    </div>
                </div>
                <button type="submit" name="reset_password" class="btn">
                    <i class="fas fa-key" style="margin-right: 8px;"></i>
                    Reset Password
                </button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>