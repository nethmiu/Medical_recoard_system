<?php
// report.php
include 'includes/header.php';

// Only admin access
if ($_SESSION['role'] !== 'admin') {
    header("location: login.php");
    exit;
}

// Query counts grouped by role and status
$sql = "SELECT role, status, COUNT(*) as count FROM users GROUP BY role, status";
$result = $conn->query($sql);

// Initialize counts array
$counts = [
    'patient' => ['total' => 0, 'active' => 0, 'inactive' => 0],
    'doctor' => ['total' => 0, 'active' => 0, 'inactive' => 0],
    'admin' => ['total' => 0, 'active' => 0, 'inactive' => 0],
];

// Fill counts from query result
while ($row = $result->fetch_assoc()) {
    $role = $row['role'];
    $status = $row['status'];
    $count = $row['count'];
    
    if (isset($counts[$role])) {
        $counts[$role][$status] = $count;
        $counts[$role]['total'] += $count;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reports Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background particles */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
            animation: particleFloat 15s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes particleFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* Header Section */
        .header-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .report-icon {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .report-icon i {
            font-size: 40px;
            color: white;
        }

        h2 {
            color: white;
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 18px;
            font-weight: 400;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Stats Overview Cards */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-icon.patients { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-icon.doctors { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .stat-icon.admins { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

        .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Main Report Container */
        .report-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .report-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f7fafc;
        }

        .table-title {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
            display: flex;
            align-items: center;
        }

        .table-title i {
            margin-right: 10px;
            color: #667eea;
        }

        .export-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .export-btn i {
            margin-right: 8px;
        }

        /* Enhanced Table Styles */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 16px;
            overflow: hidden;
        }

        th, td {
            padding: 20px 24px;
            text-align: left;
            font-size: 16px;
        }

        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 14px;
            position: relative;
        }

        th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255, 255, 255, 0.3);
        }

        td {
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
            font-weight: 500;
            position: relative;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #f8faff;
            transform: scale(1.01);
        }

        /* Role Icons and Styling */
        .role-cell {
            display: flex;
            align-items: center;
            font-weight: 600;
        }

        .role-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 16px;
            color: white;
        }

        .role-icon.patients { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .role-icon.doctors { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .role-icon.admins { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

        /* Number Styling */
        .number-cell {
            font-weight: 700;
            font-size: 18px;
            position: relative;
        }

        .total-count { color: #2d3748; }
        .active-count { color: #38a169; }
        .inactive-count { color: #e53e3e; }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.active {
            background: rgba(56, 161, 105, 0.1);
            color: #38a169;
        }

        .status-badge.inactive {
            background: rgba(229, 62, 62, 0.1);
            color: #e53e3e;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 10px;
            }
            
            .report-container {
                padding: 20px;
                margin: 10px;
            }
            
            h2 {
                font-size: 28px;
            }
            
            .report-icon {
                width: 80px;
                height: 80px;
            }
            
            .report-icon i {
                font-size: 32px;
            }
            
            .stats-overview {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .table-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            th, td {
                padding: 15px 12px;
                font-size: 14px;
            }
            
            .role-cell {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .role-icon {
                margin-right: 0;
                margin-bottom: 5px;
            }
        }

        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="report-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <h2>📊 User Report Dashboard</h2>
            <p class="subtitle">Comprehensive overview of user statistics and role distribution</p>
        </div>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon patients">
                    <i class="fas fa-user-injured"></i>
                </div>
                <div class="stat-number"><?php echo $counts['patient']['total']; ?></div>
                <div class="stat-label">Total Patients</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon doctors">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="stat-number"><?php echo $counts['doctor']['total']; ?></div>
                <div class="stat-label">Total Doctors</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon admins">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-number"><?php echo $counts['admin']['total']; ?></div>
                <div class="stat-label">Total Admins</div>
            </div>
        </div>

        <!-- Main Report Container -->
        <div class="report-container">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-table"></i>
                    Detailed User Statistics
                </div>
                <button class="export-btn" onclick="exportTable()">
                    <i class="fas fa-download"></i>
                    Export Report
                </button>
            </div>

            <div class="table-wrapper">
                <table id="reportTable">
                    <thead>
                        <tr>
                            <th>User Role</th>
                            <th>Total Registered</th>
                            <th>Active</th>
                            <th>Inactive</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="role-cell">
                                    <div class="role-icon patients">
                                        <i class="fas fa-user-injured"></i>
                                    </div>
                                    Patients
                                </div>
                            </td>
                            <td class="number-cell total-count"><?php echo $counts['patient']['total']; ?></td>
                            <td class="number-cell active-count"><?php echo $counts['patient']['active']; ?></td>
                            <td class="number-cell inactive-count"><?php echo $counts['patient']['inactive']; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="role-cell">
                                    <div class="role-icon doctors">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                    Doctors
                                </div>
                            </td>
                            <td class="number-cell total-count"><?php echo $counts['doctor']['total']; ?></td>
                            <td class="number-cell active-count"><?php echo $counts['doctor']['active']; ?></td>
                            <td class="number-cell inactive-count"><?php echo $counts['doctor']['inactive']; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="role-cell">
                                    <div class="role-icon admins">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    Administrators
                                </div>
                            </td>
                            <td class="number-cell total-count"><?php echo $counts['admin']['total']; ?></td>
                            <td class="number-cell active-count"><?php echo $counts['admin']['active']; ?></td>
                            <td class="number-cell inactive-count"><?php echo $counts['admin']['inactive']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Show loading animation on page load
        window.addEventListener('load', function() {
            document.querySelector('.loading-overlay').style.display = 'none';
        });

        // Export table functionality
        function exportTable() {
            const table = document.getElementById('reportTable');
            let csv = [];
            
            // Get table headers
            const headers = Array.from(table.querySelectorAll('th')).map(th => th.textContent.trim());
            csv.push(headers.join(','));
            
            // Get table rows
            const rows = Array.from(table.querySelectorAll('tbody tr'));
            rows.forEach(row => {
                const rowData = [];
                const cells = row.querySelectorAll('td');
                
                // Extract text content, handling the role cell specially
                cells.forEach((cell, index) => {
                    if (index === 0) {
                        // For role cell, extract just the role name
                        rowData.push(cell.textContent.trim().replace(/\s+/g, ' '));
                    } else {
                        rowData.push(cell.textContent.trim());
                    }
                });
                csv.push(rowData.join(','));
            });
            
            // Create and download CSV file
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'user_report_' + new Date().toISOString().split('T')[0] + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        // Add smooth scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all animatable elements
        document.querySelectorAll('.stat-card, .report-container').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });

        // Add table row animation on hover
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
                this.style.transition = 'all 0.3s ease';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?>