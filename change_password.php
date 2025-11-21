<?php
// change_password.php
include 'includes/header.php';

if ($_SESSION['role'] !== 'patient') {
    header("location: login.php");
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
    background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
    min-height: 100vh;
    padding: 20px;
    position: relative;
    overflow-x: hidden;
}

body::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(102, 166, 255, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(102, 166, 255, 0.2) 0%, transparent 50%);
    pointer-events: none;
}

.container {
    max-width: 100%;
    margin: 0 auto;
    position: relative;
    z-index: 1;
    background-color:  #764ba2;
}

.form-container {
    max-width: 500px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
}

.form-container::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4cafef, #66a6ff, #89f7fe);
}

.patient-header {
    text-align: center;
    margin-bottom: 30px;
}

.patient-icon {
    width: 90px;
    height: 90px;
    background: url('src/7.png') no-repeat center/cover;
    border-radius: 50%;
    margin: 0 auto 20px;
    box-shadow: 0 10px 30px rgba(102, 166, 255, 0.3);
}

h2 {
    color: white;
    font-size: 28px;
    font-weight: 600;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    margin-bottom: 10px;
}

.subtitle {
    color: rgba(255, 255, 255, 0.85);
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
}

.success-message::before {
    content: '✅';
    margin-right: 10px;
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
}

.error-message::before {
    content: '❌';
    margin-right: 10px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    font-weight: 600;
    display: block;
    margin-bottom: 8px;
    color: #060208ff;
    font-size: 14px;
    text-transform: uppercase;
}

input[type="password"] {
    width: 100%;
    padding: 14px 16px;
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

.btn1 {
    width: 100%;
    background: linear-gradient(135deg, #c304f3ff 0%, #a706f1ff 100%);
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
    background: linear-gradient(135deg, #c609ecff 0%, #e40aadff 100%);
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(74, 175, 239, 0.4);
}

.btn1:hover::before {
    left: 100%;
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
</style>

<div class="container">
    <div class="patient-header">
        <div class="patient-icon"></div>
        <h2>Change Password</h2>
        <p class="subtitle">Keep your patient account secure</p>
    </div>

    <?php echo $message; ?>

    <div class="form-container">
        <form method="post" action="">
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password:</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" name="change_password" class="btn1">Change Password</button>
        </form>

        <div class="security-note">
            <strong>Security Tip:</strong> Use a strong password with at least 8 characters, including uppercase, lowercase, numbers, and special characters.
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
