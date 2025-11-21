<?php
// manage_users.php
include 'includes/header.php';

// Ensure only admin can access
if ($_SESSION['role'] !== 'admin') {
    header("location: login.php");
    exit;
}

// Handle form submission for adding a new user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $phone = $_POST['phone_number'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO users (full_name, username, password, role, phone_number, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $full_name, $username, $password, $role, $phone, $address);
    $stmt->execute();
    $stmt->close();

    // Redirect to view_users.php with success message
    echo "<script> alert('User added successfully'); window.location.href='view_users.php?success=1';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background elements */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            animation: backgroundMove 20s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes backgroundMove {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }

        .container {
            width: 100%;
            max-width: 100%;
            position: relative;
            z-index: 1;
        }

        .form-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .header-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .header-icon i {
            font-size: 32px;
            color: white;
        }

        h2 {
            color: white;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
            font-weight: 400;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 30px 60px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.3);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-icon {
            margin-right: 8px;
            color: #667eea;
            font-size: 16px;
            width: 20px;
        }

        input, select, textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: white;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }

        input:hover, select:hover, textarea:hover {
            border-color: #cbd5e0;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        select {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            appearance: none;
        }

        .btn-container {
            text-align: center;
            margin-top: 30px;
        }

        .btn1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 40px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            min-width: 200px;
        }

        .btn1:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn1:active {
            transform: translateY(0);
        }

        .btn1 i {
            margin-right: 8px;
            font-size: 18px;
        }

        /* Role icons styling */
        .role-indicator {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .form-container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            .header-icon {
                width: 60px;
                height: 60px;
            }
            
            .header-icon i {
                font-size: 24px;
            }
        }

        /* Loading animation for form submission */
        .form-container.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .form-container.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            margin: -20px 0 0 -20px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Success message styling */
        .success-message {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
        }

        .success-message i {
            margin-right: 10px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <div class="header-icon">
                
                <i class="fas fa-users-cog"></i>
            </div>
            <h2>User Management</h2>
            <p class="subtitle">Add new users to the healthcare system</p>
        </div>

        <div class="form-container">
            <form action="manage_users.php" method="post" id="userForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-user input-icon"></i>
                            Full Name
                        </label>
                        <input type="text" name="full_name" required placeholder="Enter full name">
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-user-tag input-icon"></i>
                            Username
                        </label>
                        <input type="text" name="username" required placeholder="Enter username">
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-lock input-icon"></i>
                            Password
                        </label>
                        <input type="password" name="password" required placeholder="Enter password">
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-user-shield input-icon"></i>
                            Role
                        </label>
                        <select name="role" required>
                            <option value="">Select a role</option>
                            <option value="patient">👤 Patient</option>
                            <option value="doctor">👨‍⚕️ Doctor</option>
                            <option value="admin">👨‍💼 Admin</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-phone input-icon"></i>
                            Phone Number
                        </label>
                        <input type="tel" name="phone_number" placeholder="Enter phone number">
                    </div>
                    
                    <div class="form-group full-width">
                        <label>
                            <i class="fas fa-location-dot input-icon"></i>
                            Address
                        </label>
                        <textarea name="address" placeholder="Enter full address" rows="4"></textarea>
                    </div>
                </div>
                
                <div class="btn-container">
                    <button type="submit" name="add_user" class="btn1">
                        <i class="fas fa-plus"></i>
                        Add User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add smooth form submission animation
        document.getElementById('userForm').addEventListener('submit', function() {
            document.querySelector('.form-container').classList.add('loading');
        });

        // Add input validation feedback
        const inputs = document.querySelectorAll('input[required], select[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.style.borderColor = '#e53e3e';
                    this.style.boxShadow = '0 0 0 3px rgba(229, 62, 62, 0.1)';
                } else {
                    this.style.borderColor = '#48bb78';
                    this.style.boxShadow = '0 0 0 3px rgba(72, 187, 120, 0.1)';
                }
            });
        });

        // Reset border colors on focus
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = '#667eea';
                this.style.boxShadow = '0 0 0 3px rgba(102, 126, 234, 0.1)';
            });
        });
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?>