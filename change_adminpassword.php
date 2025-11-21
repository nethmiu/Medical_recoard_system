<?php 
// admin_change_password.php
include 'includes/header.php';

if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied. Admins only.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $message = "<p class='error-message'>New password and confirmation do not match.</p>";
    } else {
        // Hash and update the new password
        $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_hashed, $user_id);
        
        if ($stmt->execute()) {
            $message = "<p class='success-message'>Password changed successfully!</p>";
        } else {
            $message = "<p class='error-message'>Error updating password.</p>";
        }
        $stmt->close();
    }
}
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', Arial, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
    position: relative;
    overflow-x: hidden;
}

body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(120, 119, 198, 0.2) 0%, transparent 50%);
    pointer-events: none;
}

.container {
    max-width: 100%;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}
.form-container{
    max-width: 500px;
    margin: 0 auto;
}

.admin-header {
    text-align: center;
    margin-bottom: 30px;
}

.admin-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #4cafef, #3a9ed8);
    border-radius: 50%;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(74, 175, 239, 0.3);
    position: relative;
}

.admin-icon::before {
    content: '🔐';
    font-size: 32px;
    color: white;
}

.admin-icon::after {
    content: '';
    position: absolute;
    top: -5px;
    right: -5px;
    width: 20px;
    height: 20px;
    background: #ffd700;
    border-radius: 50%;
    box-shadow: 0 2px 10px rgba(255, 215, 0, 0.5);
}

h2 {
    color: white;
    font-size: 28px;
    font-weight: 600;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    margin-bottom: 10px;
}

.subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 16px;
    font-weight: 300;
}

.success-message {
    color: #10b981;
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    padding: 16px 20px;
    border: 1px solid #a7f3d0;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 500;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.success-message::before {
    content: '✅';
    margin-right: 10px;
    font-size: 16px;
}

.error-message {
    color: #ef4444;
    background: linear-gradient(135deg, #fef2f2, #fecaca);
    padding: 16px 20px;
    border: 1px solid #f87171;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 500;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.1);
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.error-message::before {
    content: '❌';
    margin-right: 10px;
    font-size: 16px;
}

.form-container {
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.15),
        0 0 0 1px rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
}

.form-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4cafef, #667eea, #764ba2);
}

.form-group {
    margin-bottom: 25px;
    position: relative;
}

label {
    font-weight: 600;
    display: block;
    margin-bottom: 8px;
    color: #374151;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.input-container {
    position: relative;
}

input[type="password"] {
    width: 100%;
    padding: 16px 20px 16px 50px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: #fafafa;
    color: #374151;
    outline: none;
}

input[type="password"]:focus {
    border-color: #4cafef;
    background: white;
    box-shadow: 0 0 0 3px rgba(74, 175, 239, 0.1);
    transform: translateY(-2px);
}

.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 18px;
    transition: color 0.3s ease;
}

input[type="password"]:focus + .input-icon {
    color: #4cafef;
}

.btn1 {
    width: 100%;
    background: linear-gradient(135deg, #4cafef 0%, #3a9ed8 100%);
    color: white;
    padding: 16px 24px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(74, 175, 239, 0.3);
    position: relative;
    overflow: hidden;
}

.btn1::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.btn1:hover {
    background: linear-gradient(135deg, #3a9ed8 0%, #2980b9 100%);
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(74, 175, 239, 0.4);
}

.btn1:hover::before {
    left: 100%;
}

.btn1:active {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(74, 175, 239, 0.3);
}

.security-note {
    margin-top: 20px;
    padding: 15px;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    border-radius: 10px;
    border-left: 4px solid #4cafef;
    font-size: 13px;
    color: #6b7280;
    line-height: 1.5;
}

.security-note::before {
    content: '🛡️';
    margin-right: 8px;
}

@media (max-width: 600px) {
    .container {
        padding: 0 15px;
    }
    
    .form-container {
        padding: 30px 20px;
        margin: 0 10px;
    }
    
    h2 {
        font-size: 24px;
    }
    
    input[type="password"] {
        padding: 14px 18px 14px 45px;
        font-size: 15px;
    }
    
    .btn1 {
        padding: 14px 20px;
        font-size: 15px;
    }
}

.floating-shapes {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
}

.shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float 6s ease-in-out infinite;
}

.shape:nth-child(1) {
    width: 100px;
    height: 100px;
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.shape:nth-child(2) {
    width: 60px;
    height: 60px;
    top: 60%;
    right: 15%;
    animation-delay: 2s;
}

.shape:nth-child(3) {
    width: 80px;
    height: 80px;
    bottom: 20%;
    left: 20%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}
</style>

<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>

<div class="container">
    <div class="admin-header">
        <div class="admin-icon"></div>
        <h2>Password Change</h2>
        <p class="subtitle">Secure your administrator account</p>
    </div>
    
    <?php echo $message; ?>
    
    <div class="form-container">
        <form method="post" action="">
            <div class="form-group">
                <label>New Password:</label>
                <div class="input-container">
                    <input type="password" name="new_password" required>
                    <span class="input-icon">🔒</span>
                </div>
            </div>
            
            <div class="form-group">
                <label>Confirm New Password:</label>
                <div class="input-container">
                    <input type="password" name="confirm_password" required>
                    <span class="input-icon">🔐</span>
                </div>
            </div>
            
            <button type="submit" name="change_password" class="btn1">Change Password</button>
        </form>
        
        <div class="security-note">
            <strong>Security Tip:</strong> Use a strong password with at least 8 characters, including uppercase, lowercase, numbers, and special characters.
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>