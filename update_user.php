<?php 
include 'includes/header.php';

if ($_SESSION['role'] !== 'admin') {
    header("location: login.php");
    exit;
}

// Check if user_id is provided
if (!isset($_GET['user_id'])) {
    echo "<p>User ID not provided.</p>";
    exit;
}

$user_id = intval($_GET['user_id']);

// Fetch user details
$stmt = $conn->prepare("SELECT full_name, username, role, status FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<p>User not found.</p>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, role = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $full_name, $username, $role, $status, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('User updated successfully'); window.location.href='view_users.php';</script>";
    exit;
}
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
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
        z-index: 0;
    }

    .container {
        max-width: 100%;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 30px 0;
    }

    .header-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 15px 35px rgba(79, 172, 254, 0.3);
        position: relative;
    }

    .header-icon::before {
        content: '';
        position: absolute;
        top: -3px;
        left: -3px;
        right: -3px;
        bottom: -3px;
        background: linear-gradient(45deg, #ff9a9e, #fecfef, #fecfef);
        border-radius: 50%;
        z-index: -1;
        opacity: 0.5;
        animation: pulse 2s ease-in-out infinite alternate;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.5; }
        100% { transform: scale(1.05); opacity: 0.8; }
    }

    .header-icon i {
        color: white;
        font-size: 32px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    h2 {
        color: white;
        font-size: 32px;
        font-weight: 700;
        text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        margin-bottom: 10px;
    }

    .subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 16px;
        font-weight: 300;
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
    .form-container{
        max-width: 500px;
        margin: 0 auto;
    }

    .form-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #4facfe, #00f2fe, #667eea);
        
    }

    .user-info-card {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        border-left: 5px solid #4facfe;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .user-info-card h3 {
        color: #334155;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-info-card p {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 0;
    }

    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #374151;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .input-container {
        position: relative;
    }

    input, select {
        width: 100%;
        padding: 16px 20px 16px 50px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #fafafa;
        color: #374151;
        outline: none;
        appearance: none;
    }

    select {
        cursor: pointer;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6,9 12,15 18,9"></polyline></svg>');
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 18px;
        padding-right: 50px;
    }

    input:focus, select:focus {
        border-color: #4facfe;
        background: white;
        box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
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
        z-index: 1;
    }

    input:focus + .input-icon, select:focus + .input-icon {
        color: #4facfe;
    }

    button {
        width: 100%;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        padding: 16px 24px;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
        position: relative;
        overflow: hidden;
    }

    button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    button:hover {
        background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(79, 172, 254, 0.4);
    }

    button:hover::before {
        left: 100%;
    }

    button:active {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(79, 172, 254, 0.3);
    }

    .role-preview {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-left: 10px;
    }

    .role-admin {
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        color: white;
    }

    .role-doctor {
        background: linear-gradient(135deg, #06b6d4, #67e8f9);
        color: white;
    }

    .role-patient {
        background: linear-gradient(135deg, #10b981, #34d399);
        color: white;
    }

    .status-preview {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-left: 10px;
    }

    .status-active {
        background: linear-gradient(135deg, #10b981, #34d399);
        color: white;
    }

    .status-inactive {
        background: linear-gradient(135deg, #ef4444, #f87171);
        color: white;
    }

    .floating-elements {
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        pointer-events: none;
        z-index: 0;
    }

    .floating-shape {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        animation: float 8s ease-in-out infinite;
    }

    .floating-shape:nth-child(1) {
        width: 100px;
        height: 100px;
        top: 15%;
        left: 10%;
        animation-delay: 0s;
    }

    .floating-shape:nth-child(2) {
        width: 60px;
        height: 60px;
        top: 70%;
        right: 15%;
        animation-delay: 3s;
    }

    .floating-shape:nth-child(3) {
        width: 80px;
        height: 80px;
        bottom: 20%;
        left: 20%;
        animation-delay: 6s;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.7; }
        50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: white;
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 20px;
        padding: 10px 20px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 25px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .back-button:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .security-note {
        margin-top: 20px;
        padding: 15px;
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        border-radius: 10px;
        border-left: 4px solid #4facfe;
        font-size: 13px;
        color: #6b7280;
        line-height: 1.5;
    }

    .security-note::before {
        content: '⚠️';
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
        
        input, select {
            padding: 14px 18px 14px 45px;
            font-size: 15px;
        }
        
        button {
            padding: 14px 20px;
            font-size: 15px;
        }
        
        .header-icon {
            width: 60px;
            height: 60px;
        }
        
        .header-icon i {
            font-size: 24px;
        }
    }
</style>

<div class="floating-elements">
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
</div>

<div class="container">
    <a href="view_users.php" class="back-button">
        <i class="fa-solid fa-arrow-left"></i>
        Back to Users
    </a>

    <div class="page-header">
        <div class="header-icon">
            <i class="fa-solid fa-user-pen"></i>
        </div>
        <h2>Update User</h2>
        <p class="subtitle">Modify user information and permissions</p>
    </div>

    <div class="form-container">
        <div class="user-info-card">
            <h3>
                <i class="fa-solid fa-info-circle" style="color: #4facfe;"></i>
                Current User Information
            </h3>
            <p>Editing user: <strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>
                    <i class="fa-solid fa-user"></i>
                    Full Name
                </label>
                <div class="input-container">
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    <span class="input-icon">
                        <i class="fa-solid fa-user"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fa-solid fa-at"></i>
                    Username
                </label>
                <div class="input-container">
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    <span class="input-icon">
                        <i class="fa-solid fa-at"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fa-solid fa-crown"></i>
                    Role
                    <span class="role-preview role-<?php echo strtolower($user['role']); ?>">
                        Current: <?php echo ucfirst($user['role']); ?>
                    </span>
                </label>
                <div class="input-container">
                    <select name="role" required>
                        <option value="admin"   <?php if($user['role'] == 'admin')   echo 'selected'; ?>>Admin</option>
                        <option value="doctor"  <?php if($user['role'] == 'doctor')  echo 'selected'; ?>>Doctor</option>
                        <option value="patient" <?php if($user['role'] == 'patient') echo 'selected'; ?>>Patient</option>
                    </select>
                    <span class="input-icon">
                        <i class="fa-solid fa-crown"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fa-solid fa-circle-dot"></i>
                    Status
                    <span class="status-preview status-<?php echo strtolower($user['status']); ?>">
                        Current: <?php echo ucfirst($user['status']); ?>
                    </span>
                </label>
                <div class="input-container">
                    <select name="status" required>
                        <option value="active"   <?php if($user['status'] == 'active')   echo 'selected'; ?>>Active</option>
                        <option value="inactive" <?php if($user['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                    </select>
                    <span class="input-icon">
                        <i class="fa-solid fa-circle-dot"></i>
                    </span>
                </div>
            </div>

            <button type="submit">
                <i class="fa-solid fa-save"></i>
                Update User
            </button>
        </form>

        <div class="security-note">
            <strong>Important:</strong> Changing a user's role or status will affect their access permissions immediately. Please ensure you have the necessary authorization before making changes.
        </div>
    </div>
</div>

<script>
// Add real-time preview updates
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role"]');
    const statusSelect = document.querySelector('select[name="status"]');
    const rolePreview = document.querySelector('.role-preview');
    const statusPreview = document.querySelector('.status-preview');

    roleSelect.addEventListener('change', function() {
        const selectedRole = this.value;
        rolePreview.className = `role-preview role-${selectedRole}`;
        rolePreview.textContent = `Current: ${selectedRole.charAt(0).toUpperCase() + selectedRole.slice(1)}`;
    });

    statusSelect.addEventListener('change', function() {
        const selectedStatus = this.value;
        statusPreview.className = `status-preview status-${selectedStatus}`;
        statusPreview.textContent = `Current: ${selectedStatus.charAt(0).toUpperCase() + selectedStatus.slice(1)}`;
    });

    // Add form validation feedback
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const button = form.querySelector('button[type="submit"]');
        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Updating...';
        button.disabled = true;
    });

    // Add input animations
    const inputs = document.querySelectorAll('input, select');
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

<?php include 'includes/footer.php'; ?>