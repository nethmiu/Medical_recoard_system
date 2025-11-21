<?php
session_start(); // Added session_start() which was missing
// login.php
require_once 'includes/db_connect.php';
require_once 'user_ctrl_route.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, username, password, role, status FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password and check if account is active
        if (password_verify($password, $user['password']) && $user['status'] == 'active') {
            // Password is correct, start session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    header("location: dashboard_admin.php");
                    break;
                case 'doctor':
                    header("location: dashboard_doctor.php");
                    break;
                case 'patient':
                    header("location: dashboard_patient.php");
                    break;
            }
            exit;
        } else {
            $error_message = "Incorrect username or password, or account is inactive.";
        }
    } else {
        $error_message = "Incorrect username or password.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Medical System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6e24e6ff;
            --secondary-color: #030303ff;
            --accent-color: #4fc3f7;
            --light-color: #5683b1ff;
            --dark-color: #276eb6ff;
            --success-color: #47056dff;
            --error-color: #dc3545;
            --gradient-primary: linear-gradient(135deg, #e686f3ff 0%, #9906eeff 100%);
            --gradient-card: linear-gradient(145deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%);
            --shadow-light: 0 8px 32px rgba(31, 38, 135, 0.15);
            --shadow-hover: 0 15px 40px rgba(31, 38, 135, 0.25);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gradient-primary);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--dark-color);
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
            background: url('src/new.jpg') center/cover;
            opacity: 0.1;
            z-index: -2;
        }

        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 70%, rgba(110, 36, 230, 0.3) 0%, transparent 50%),
                        radial-gradient(circle at 70% 30%, rgba(153, 6, 238, 0.2) 0%, transparent 50%);
            z-index: -1;
        }
        
        .login-container {
            background: var(--gradient-card);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 3rem 2.5rem;
            border-radius: 24px;
            box-shadow: var(--shadow-light);
            width: 100%;
            max-width: 450px;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
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
            background: linear-gradient(90deg, #6e24e6ff, #4fc3f7, #9906eeff);
        }
        
        .login-container:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-5px);
        }

        .header-section {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .logo-container {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(110, 36, 230, 0.3);
            transition: all 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 15px 40px rgba(110, 36, 230, 0.4);
        }

        .logo-container i {
            font-size: 40px;
            color: white;
        }

        .system-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .system-subtitle {
            font-size: 0.9rem;
            color: #666;
            font-weight: 400;
        }
        
        .form-group {
            margin-bottom: 1.8rem;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 0.7rem;
            font-weight: 500;
            color: var(--secondary-color);
            font-size: 0.95rem;
        }

        .input-container {
            position: relative;
        }

        .input-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
            transition: all 0.3s ease;
            z-index: 2;
        }
        
        input {
            width: 100%;
            padding: 1rem 1rem 1rem 45px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        
        input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(79, 195, 247, 0.15);
            background: white;
            transform: translateY(-2px);
        }

        input:focus + i,
        input:not(:placeholder-shown) + i {
            color: var(--accent-color);
            transform: translateY(-50%) scale(1.1);
        }
        
        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--success-color));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }
        
        .btn:hover {
            background: linear-gradient(135deg, var(--success-color), var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(110, 36, 230, 0.4);
        }

        .btn:active {
            transform: translateY(-1px);
        }
        
        .error-message {
            color: var(--error-color);
            text-align: center;
            margin: 1.5rem 0;
            padding: 1rem;
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 12px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            backdrop-filter: blur(10px);
        }

        .links-section {
            margin-top: 2rem;
            text-align: center;
        }

        .auth-links {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.8;
        }

        .auth-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .auth-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .auth-links a:hover {
            color: var(--success-color);
            transform: translateY(-1px);
        }

        .auth-links a:hover::after {
            width: 100%;
        }

        .divider {
            margin: 1rem 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #ddd, transparent);
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
                border-radius: 20px;
            }

            .system-title {
                font-size: 1.5rem;
            }

            .logo-container {
                width: 80px;
                height: 80px;
            }

            .logo-container i {
                font-size: 32px;
            }
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

        /* Entrance animation */
        .login-container {
            animation: slideInUp 0.6s ease-out;
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

        /* Floating particles effect */
        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) { width: 10px; height: 10px; top: 20%; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 15px; height: 15px; top: 60%; left: 80%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 8px; height: 8px; top: 80%; left: 20%; animation-delay: 4s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
        }
    </style>
</head>
<body>
    <!-- Floating particles -->
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <div class="login-container">
        <div class="header-section">
            <div class="logo">
                <div class="logo-container">
                    <i class="fas fa-heartbeat"></i>
                </div>
            </div>
            <h2 class="system-title">Online Medical Record System</h2>
            <p class="system-subtitle">Secure & Professional Healthcare Management</p>
        </div>

        <form action="login.php" method="post" id="loginForm">
            <div class="form-group">
                <label for="username">Username:</label>
                <div class="input-container">
                    <input type="text" name="username" id="username" required autofocus placeholder="Enter your username">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="input-container">
                    <input type="password" name="password" id="password" required placeholder="Enter your password">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
            
            <?php if(!empty($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <button type="submit" class="btn" id="loginBtn">
                <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                Login
            </button>

            <div class="links-section">
                <div class="auth-links">
                    Do not have account? <a href="register.php">Register here</a>
                </div>
                <div class="divider"></div>
                <div class="auth-links">
                    Forgot Password? <a href="forgot_password.php">Reset here</a>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Add interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const btn = document.getElementById('loginBtn');
            const inputs = document.querySelectorAll('input');

            // Add focus effects
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Add loading state to button
            form.addEventListener('submit', function() {
                btn.classList.add('loading');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>Signing In...';
            });

            // Add keyboard navigation
            inputs.forEach((input, index) => {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && index < inputs.length - 1) {
                        e.preventDefault();
                        inputs[index + 1].focus();
                    }
                });
            });

            // Add password visibility toggle
            const passwordInput = document.getElementById('password');
            const passwordIcon = passwordInput.nextElementSibling;
            
            passwordIcon.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordIcon.className = 'fas fa-eye-slash';
                } else {
                    passwordInput.type = 'password';
                    passwordIcon.className = 'fas fa-lock';
                }
            });
            
            passwordIcon.style.cursor = 'pointer';
        });
    </script>
</body>
</html>