<?php
// register.php
require_once 'includes/db_connect.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);

    // Validate passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare insert query
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, password, role, phone_number, address, status) VALUES (?, ?, ?, 'patient', ?, ?, 'active')");
        $stmt->bind_param("sssss", $full_name, $username, $hashed_password, $phone_number, $address);

        if ($stmt->execute()) {
            $success_message = "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            if ($conn->errno === 1062) { // Duplicate username
                $error_message = "Username already exists. Please choose another.";
            } else {
                $error_message = "Error: " . $conn->error;
            }
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Medical System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #b76ee7ff 0%, #a008f8ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Background decorative elements */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('src/index.jpg') center/cover;
            opacity: 0.08;
            z-index: -2;
        }

        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 70%, rgba(183, 110, 231, 0.4) 0%, transparent 50%),
                        radial-gradient(circle at 70% 30%, rgba(160, 8, 248, 0.3) 0%, transparent 50%);
            z-index: -1;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            max-width: 480px;
            width: 100%;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            position: relative;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            animation: slideInUp 0.6s ease-out;
        }

        .register-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2);
        }

        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #b76ee7ff, #a008f8ff, #8629f0ff);
            border-radius: 24px 24px 0 0;
        }

        .header-section {
            margin-bottom: 30px;
        }

        .logo-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #b76ee7ff, #a008f8ff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 15px 35px rgba(183, 110, 231, 0.4);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .logo-container::before {
            content: '';
            position: absolute;
            width: 120%;
            height: 120%;
            background: conic-gradient(from 0deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: rotate 3s linear infinite;
        }

        .logo-container:hover {
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 20px 45px rgba(183, 110, 231, 0.5);
        }

        .logo-container i {
            font-size: 40px;
            color: white;
            z-index: 2;
            position: relative;
        }

        h2 {
            color: #2d3748;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #b76ee7ff, #2d3748);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 20px;
            font-weight: 400;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2d3748;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .input-container {
            position: relative;
        }

        .input-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 16px;
            transition: all 0.3s ease;
            z-index: 2;
        }

        input, textarea {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            color: #2d3748;
        }

        input:focus, textarea:focus {
            border-color: #b76ee7ff;
            background: white;
            box-shadow: 0 0 0 4px rgba(183, 110, 231, 0.15);
            outline: none;
            transform: translateY(-2px);
        }

        input:focus + i, textarea:focus + i {
            color: #b76ee7ff;
            transform: translateY(-50%) scale(1.1);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
            padding-top: 14px;
        }

        .btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #8629f0ff 0%, #bc2ee7ff 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            margin-top: 10px;
            position: relative;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: all 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            background: linear-gradient(135deg, #c009f8ff 0%, #65058bff 100%);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(183, 110, 231, 0.4);
        }

        .btn:active {
            transform: translateY(-1px);
        }

        .error-message {
            color: #e53e3e;
            background: rgba(254, 226, 226, 0.9);
            backdrop-filter: blur(10px);
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid rgba(254, 202, 202, 0.8);
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }

        .success-message {
            color: #22543d;
            background: rgba(209, 250, 229, 0.9);
            backdrop-filter: blur(10px);
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid rgba(167, 243, 208, 0.8);
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }

        .success-message a {
            color: #2b6cb0;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .success-message a:hover {
            color: #2c5aa0;
            text-decoration: underline;
        }

        .login-link {
            margin-top: 25px;
            font-size: 14px;
            color: #4a5568;
        }

        .login-link a {
            color: #b76ee7ff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #b76ee7ff;
            transition: width 0.3s ease;
        }

        .login-link a:hover {
            color: #a008f8ff;
            transform: translateY(-1px);
        }

        .login-link a:hover::after {
            width: 100%;
        }

        /* Form validation styles */
        .form-group.error input,
        .form-group.error textarea {
            border-color: #e53e3e;
            background: rgba(254, 226, 226, 0.3);
        }

        .form-group.success input,
        .form-group.success textarea {
            border-color: #38a169;
            background: rgba(209, 250, 229, 0.3);
        }

        /* Loading animation */
        .btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            margin: auto;
            border: 2px solid white;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Responsive design */
        @media (max-width: 480px) {
            .register-container {
                margin: 10px;
                padding: 30px 25px;
                border-radius: 20px;
            }

            h2 {
                font-size: 1.7rem;
            }

            .logo-container {
                width: 80px;
                height: 80px;
            }

            .logo-container i {
                font-size: 32px;
            }

            input, textarea {
                padding: 12px 12px 12px 40px;
            }

            .btn {
                padding: 14px;
            }
        }

        /* Floating elements */
        .floating-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
            animation: float 6s ease-in-out infinite;
        }

        .floating-element:nth-child(1) { 
            width: 20px; height: 20px; 
            top: 10%; left: 10%; 
            animation-delay: 0s; 
        }
        .floating-element:nth-child(2) { 
            width: 15px; height: 15px; 
            top: 70%; left: 85%; 
            animation-delay: 2s; 
        }
        .floating-element:nth-child(3) { 
            width: 25px; height: 25px; 
            top: 30%; left: 5%; 
            animation-delay: 4s; 
        }

        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg); 
                opacity: 0.6; 
            }
            50% { 
                transform: translateY(-30px) rotate(180deg); 
                opacity: 1; 
            }
        }
    </style>
</head>
<body>
    <!-- Floating elements -->
    <div class="floating-element"></div>
    <div class="floating-element"></div>
    <div class="floating-element"></div>

    <div class="register-container">
        <div class="header-section">
            <div class="logo-container">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2>Create Patient Account</h2>
            <p class="subtitle">Join our medical system for better healthcare</p>
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

        <form action="register.php" method="post" id="registerForm">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <div class="input-container">
                    <input type="text" name="full_name" id="full_name" required placeholder="Enter your full name">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <div class="input-container">
                    <input type="text" name="username" id="username" required placeholder="Choose a username">
                    <i class="fas fa-at"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="input-container">
                    <input type="password" name="password" id="password" required placeholder="Create a password">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <div class="input-container">
                    <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm your password">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <div class="input-container">
                    <input type="text" name="phone_number" id="phone_number" placeholder="Enter phone number (optional)">
                    <i class="fas fa-phone"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <div class="input-container">
                    <textarea name="address" id="address" placeholder="Enter your address (optional)"></textarea>
                    <i class="fas fa-home"></i>
                </div>
            </div>
            <button type="submit" class="btn" id="registerBtn">
                <i class="fas fa-user-plus" style="margin-right: 8px;"></i>
                Register
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const btn = document.getElementById('registerBtn');
            const inputs = document.querySelectorAll('input, textarea');
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');

            // Add focus effects
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                    this.parentElement.parentElement.classList.remove('error');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                    validateField(this);
                });
            });

            // Real-time password confirmation validation
            confirmPasswordField.addEventListener('input', function() {
                if (passwordField.value && this.value) {
                    if (passwordField.value === this.value) {
                        this.parentElement.parentElement.classList.add('success');
                        this.parentElement.parentElement.classList.remove('error');
                    } else {
                        this.parentElement.parentElement.classList.add('error');
                        this.parentElement.parentElement.classList.remove('success');
                    }
                }
            });

            // Form validation
            function validateField(field) {
                const formGroup = field.parentElement.parentElement;
                if (field.required && !field.value.trim()) {
                    formGroup.classList.add('error');
                    formGroup.classList.remove('success');
                } else if (field.value.trim()) {
                    formGroup.classList.add('success');
                    formGroup.classList.remove('error');
                }
            }

            // Add loading state to button
            form.addEventListener('submit', function(e) {
                // Validate passwords match
                if (passwordField.value !== confirmPasswordField.value) {
                    e.preventDefault();
                    confirmPasswordField.parentElement.parentElement.classList.add('error');
                    return;
                }

                btn.classList.add('loading');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>Creating Account...';
            });

            // Keyboard navigation
            inputs.forEach((input, index) => {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && index < inputs.length - 1) {
                        e.preventDefault();
                        inputs[index + 1].focus();
                    }
                });
            });

            // Enhanced password field interactions
            const passwordIcons = document.querySelectorAll('.fa-lock');
            passwordIcons.forEach(icon => {
                icon.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.className = 'fas fa-eye-slash';
                    } else {
                        input.type = 'password';
                        this.className = 'fas fa-lock';
                    }
                });
                icon.style.cursor = 'pointer';
            });
        });
    </script>
</body>
</html>