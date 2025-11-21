<?php
// profile.php
include 'includes/header.php';

if ($_SESSION['role'] !== 'patient') { 
    header("location: login.php"); 
    exit; 
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone_number = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $phone_number, $address, $user_id);
    if ($stmt->execute()) {
        $_SESSION['full_name'] = $full_name; // Update session
        $message = "<p class='success-message'>Profile updated successfully!</p>";
    } else {
        $message = "<p class='error-message'>Error updating profile.</p>";
    }
    $stmt->close();
}

// Fetch current user data
$user_data = $conn->query("SELECT full_name, username, phone_number, address FROM users WHERE id=$user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Medical System</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
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
            background: url('src/help.jpg') center/cover;
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
            background: radial-gradient(circle at 30% 70%, rgba(102, 126, 234, 0.3) 0%, transparent 50%),
                        radial-gradient(circle at 70% 30%, rgba(118, 75, 162, 0.2) 0%, transparent 50%);
            z-index: -1;
        }

        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
            animation: slideInUp 0.6s ease-out;
        }

        .profile-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
        }

        .profile-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .profile-avatar::before {
            content: '';
            position: absolute;
            width: 130%;
            height: 130%;
            background: conic-gradient(from 0deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: rotate 3s linear infinite;
        }

        .profile-avatar:hover {
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 20px 45px rgba(102, 126, 234, 0.4);
        }

        .profile-avatar i {
            font-size: 50px;
            color: white;
            z-index: 2;
            position: relative;
        }

        .profile-header h2 {
            color: #2d3748;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #667eea, #2d3748);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .profile-header p {
            color: #718096;
            font-size: 1rem;
            font-weight: 400;
            margin-bottom: 0;
        }

        .profile-content {
            padding: 40px 30px;
        }

        /* Messages */
        .success-message {
            color: #22543d;
            background: rgba(209, 250, 229, 0.9);
            backdrop-filter: blur(10px);
            padding: 16px 20px;
            border: 1px solid rgba(167, 243, 208, 0.8);
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .error-message {
            color: #e53e3e;
            background: rgba(254, 226, 226, 0.9);
            backdrop-filter: blur(10px);
            padding: 16px 20px;
            border: 1px solid rgba(254, 202, 202, 0.8);
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        /* Form */
        form {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(15px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
            font-size: 14px;
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

        input[type="text"], 
        textarea {
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

        input[type="text"]:focus,
        textarea:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
            outline: none;
            transform: translateY(-2px);
        }

        input[type="text"]:focus + i,
        textarea:focus + i {
            color: #667eea;
            transform: translateY(-50%) scale(1.1);
        }

        input[disabled] {
            background: rgba(242, 242, 242, 0.8);
            color: #718096;
            cursor: not-allowed;
            border-style: dashed;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
            font-family: 'Inter', sans-serif;
        }

        small {
            display: block;
            margin-top: 6px;
            color: #718096;
            font-size: 12px;
            font-style: italic;
        }

        /* Buttons */
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn1 {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #4cafef, #667eea);
            color: white;
            padding: 14px 24px;
            text-decoration: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            min-width: 140px;
            justify-content: center;
        }

        .btn1::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: all 0.5s;
        }

        .btn1:hover::before {
            left: 100%;
        }

        .btn1:hover {
            background: linear-gradient(135deg, #3a9ed8, #5a67d8);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(76, 175, 239, 0.4);
        }

        .btn1:active {
            transform: translateY(-1px);
        }

        .btn1.primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .btn1.primary:hover {
            background: linear-gradient(135deg, #5a67d8, #68408d);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn1.secondary {
            background: linear-gradient(135deg, #48bb78, #38a169);
        }

        .btn1.secondary:hover {
            background: linear-gradient(135deg, #38a169, #2f855a);
            box-shadow: 0 10px 25px rgba(72, 187, 120, 0.4);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .profile-container {
                margin: 10px;
                border-radius: 20px;
            }

            .profile-header,
            .profile-content {
                padding: 25px 20px;
            }

            .profile-header h2 {
                font-size: 1.8rem;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
            }

            .profile-avatar i {
                font-size: 40px;
            }

            form {
                padding: 25px 20px;
            }

            .button-group {
                flex-direction: column;
                align-items: center;
            }

            .btn1 {
                width: 100%;
                max-width: 250px;
            }
        }

        /* Loading animation */
        .btn1.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn1.loading::after {
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

        /* Floating elements */
        .floating-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
            animation: float 8s ease-in-out infinite;
        }

        .floating-element:nth-child(1) { 
            width: 15px; height: 15px; 
            top: 15%; left: 10%; 
            animation-delay: 0s; 
        }
        .floating-element:nth-child(2) { 
            width: 20px; height: 20px; 
            top: 70%; left: 85%; 
            animation-delay: 3s; 
        }
        .floating-element:nth-child(3) { 
            width: 12px; height: 12px; 
            top: 40%; left: 5%; 
            animation-delay: 6s; 
        }

        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg); 
                opacity: 0.6; 
            }
            50% { 
                transform: translateY(-25px) rotate(180deg); 
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

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <h2>My Profile</h2>
            <p>You can view and update your personal information here.</p>
        </div>

        <div class="profile-content">
            <?php if($message): ?>
                <?php if(strpos($message, 'success-message') !== false): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        Profile updated successfully!
                    </div>
                <?php else: ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error updating profile.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form action="profile.php" method="post" id="profileForm">
                <div class="form-group">
                    <label>Full Name:</label>
                    <div class="input-container">
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required placeholder="Enter your full name">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Username:</label>
                    <div class="input-container">
                        <input type="text" value="<?php echo htmlspecialchars($user_data['username']); ?>" disabled placeholder="Username (cannot be changed)">
                        <i class="fas fa-at"></i>
                    </div>
                    <small>Username cannot be changed.</small>
                </div>
                
                <div class="form-group">
                    <label>Phone Number:</label>
                    <div class="input-container">
                        <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user_data['phone_number']); ?>" placeholder="Enter your phone number">
                        <i class="fas fa-phone"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Address:</label>
                    <div class="input-container">
                        <textarea name="address" placeholder="Enter your address"><?php echo htmlspecialchars($user_data['address']); ?></textarea>
                        <i class="fas fa-home"></i>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" name="update_profile" class="btn1 primary" id="updateBtn">
                        <i class="fas fa-save"></i>
                        Update Profile
                    </button>
                    <a href="dashboard_patient.php" class="btn1">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                    <a href="change_password.php" class="btn1 secondary">
                        <i class="fas fa-key"></i>
                        Change Password
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profileForm');
            const updateBtn = document.getElementById('updateBtn');
            const inputs = document.querySelectorAll('input[type="text"]:not([disabled]), textarea');

            // Add focus effects
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Add loading state to update button
            form.addEventListener('submit', function() {
                updateBtn.classList.add('loading');
                updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            });

            // Auto-resize textarea
            const textarea = document.querySelector('textarea');
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
            }

            // Add form validation
            const requiredInputs = document.querySelectorAll('input[required]');
            requiredInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.style.borderColor = '#e53e3e';
                        this.style.backgroundColor = 'rgba(254, 226, 226, 0.3)';
                    } else {
                        this.style.borderColor = '#38a169';
                        this.style.backgroundColor = 'rgba(209, 250, 229, 0.3)';
                    }
                });

                input.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.style.borderColor = '#e2e8f0';
                        this.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                    }
                });
            });

            // Smooth scroll for mobile
            if (window.innerWidth <= 768) {
                const container = document.querySelector('.profile-container');
                container.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?>