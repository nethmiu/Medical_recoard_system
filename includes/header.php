<?php
// header.php
session_start();
require_once 'db_connect.php';

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}

// Get user data
$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'] ?? 'Guest';
$username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Record System - <?php echo htmlspecialchars($role); ?> Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --danger: #e74c3c;
            --danger-dark: #c0392b;
            --success: #2ecc71;
            --success-dark: #27ae60;
            --warning: #f39c12;
            --warning-dark: #e67e22;
            --dark: #2c3e50;
            --light: #ecf0f1;
            --gray: #95a5a6;
            --white: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            padding: 1rem 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-title {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .header-title i {
            font-size: 1.3rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .user-greeting {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        
        .user-greeting .role-badge {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 0.5rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            gap: 0.5rem;
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: var(--white);
        }
        
        .btn-danger:hover {
            background-color: var(--danger-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .nav-container {
            background-color: var(--white);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .nav-menu {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 1rem;
            padding: 0.75rem 2rem;
        }
        
        .nav-link {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-link:hover {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--primary);
        }
        
        .nav-link.active {
            background-color: rgba(52, 152, 219, 0.2);
            color: var(--primary);
        }
        
        .nav-link i {
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .user-info {
                width: 100%;
                justify-content: space-between;
            }
            
            .nav-menu {
                overflow-x: auto;
                padding: 0.75rem 1rem;
                -webkit-overflow-scrolling: touch;
            }
            
            .nav-link {
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <div class="header-content">
            <div class="header-title">
                <i class="fas fa-heartbeat"></i>
                <span>Medical Record System - <?php echo htmlspecialchars(ucfirst($role)); ?> Dashboard</span>
            </div>
            <div class="user-info">
                <div class="user-greeting">
                    <i class="fas fa-user-circle"></i>
                    <span>Welcome, <strong><?php echo htmlspecialchars($full_name); ?></strong></span>
                    <span class="role-badge"><?php echo htmlspecialchars($role); ?></span>
                </div>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </a>
            </div>
        </div>
    </header>
    
    <nav class="nav-container">
        <div class="nav-menu">
            
            
           
            
            <?php if ($role == 'patient') : ?>
            

            <a href="dashboard_patient.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard_patient.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-cog"></i>
                <span>Profile Settings</span>
            </a>
           
            <?php endif; ?>

            <?php if ($role == 'doctor') : ?>

                <a href="dashboard_doctor.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard_doctor.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            

            <a href="change_doctorpassword.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'change_doctorpassword.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-cog"></i>
                <span>Profile Settings</span>
            </a>
           
            <?php endif; ?>
            
            
            
            <?php if ($role == 'admin') : ?>
                <a href="dashboard_admin.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard_admin.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="view_users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'view_users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users-cog"></i>
                <span>User Management</span>
            </a>

             <a href="change_adminpassword.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'change_adminpassword.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-cog"></i>
                <span>Profile Settings</span>
            </a>
            <a href="report.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-medical"></i>
                <span>User Reports</span>
            </a>
            <?php endif; ?>
        </div>
    </nav>
    
    <main class="main-content">