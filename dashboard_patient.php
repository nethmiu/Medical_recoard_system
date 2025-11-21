<?php
// dashboard_patient.php
include 'includes/header.php';

// Ensure only patient can access
if ($_SESSION['role'] !== 'patient') {
    header("location: login.php");
    exit;
}

$patient_id = $_SESSION['user_id'];

// Fetch patient's medical records
$stmt = $conn->prepare("
    SELECT mr.*, u.full_name as doctor_name 
    FROM medical_records mr
    JOIN users u ON mr.doctor_id = u.id
    WHERE mr.patient_id = ? 
    ORDER BY mr.record_date DESC
");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$records = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }

        /* Medical background pattern */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80') center/cover;
            opacity: 0.05;
            z-index: -1;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('src/new.jpg') center/cover;
            opacity: 0.1;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .patient-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 20px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            display: block;
        }

        h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .records-icon {
            font-size: 28px;
        }

        .dashboard-content {
            padding: 40px;
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8faff 0%, #e8f2ff 100%);
            padding: 25px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon i {
            color: white;
            font-size: 24px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }

        .records-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .section-header {
            background: linear-gradient(135deg, #f8faff 0%, #e8f2ff 100%);
            padding: 25px 30px;
            border-bottom: 1px solid #e9ecef;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .records-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .records-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .records-table th {
            padding: 20px 25px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: white;
        }

        .records-table td {
            padding: 20px 25px;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }

        .records-table tbody tr {
            transition: all 0.3s ease;
        }

        .records-table tbody tr:hover {
            background: linear-gradient(135deg, #f8faff 0%, #e8f2ff 100%);
            transform: translateX(2px);
        }

        .date-badge {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            color: #1565c0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .doctor-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .doctor-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #e9ecef;
        }

        .doctor-name {
            font-weight: 500;
            color: #333;
        }

        .view-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .view-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .no-records {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-records-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            opacity: 0.3;
        }

        .no-records h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .no-records p {
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .dashboard-content {
                padding: 20px;
            }

            .dashboard-header {
                padding: 30px 20px;
            }

            h2 {
                font-size: 24px;
            }

            .stats-overview {
                grid-template-columns: 1fr;
            }

            .records-table {
                font-size: 14px;
            }

            .records-table th,
            .records-table td {
                padding: 15px 10px;
            }

            .doctor-info {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }

            .date-badge {
                font-size: 12px;
                padding: 6px 8px;
            }
        }

        .health-tips {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
            border-radius: 16px;
            padding: 20px;
            margin-top: 30px;
            border-left: 4px solid #4caf50;
        }

        .health-tips h4 {
            color: #2e7d32;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .health-tips p {
            color: #388e3c;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="header-content">
                <img src="src/4.png" alt="Patient" class="patient-avatar">
                <h2>
                    <i class="fas fa-clipboard-list records-icon"></i>
                    My Medical History
                </h2>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div class="stat-number"><?php echo $records->num_rows; ?></div>
                    <div class="stat-label">Total Records</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-number">
                        <?php
                        $records->data_seek(0);
                        $doctors = [];
                        while($row = $records->fetch_assoc()) {
                            $doctors[$row['doctor_name']] = true;
                        }
                        echo count($doctors);
                        $records->data_seek(0);
                        ?>
                    </div>
                    <div class="stat-label">Doctors Visited</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="stat-number">Active</div>
                    <div class="stat-label">Health Status</div>
                </div>
            </div>

            <div class="records-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-history"></i>
                        Medical Records History
                    </h3>
                </div>
                
                <table class="records-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar-alt"></i> Record Date</th>
                            <th><i class="fas fa-user-md"></i> Doctor</th>
                            <th><i class="fas fa-eye"></i> Diagnosis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($records->num_rows > 0): ?>
                            <?php while($row = $records->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="date-badge">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo htmlspecialchars($row['record_date']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="doctor-info">
                                        <img src="src/5.png" alt="Doctor" class="doctor-avatar">
                                        <span class="doctor-name">Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <a href="view_medical_rec.php?rec_id=<?php echo $row['record_id']; ?>" class="view-btn">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="no-records">
                                    <img src="src/6.png" alt="No Records" class="no-records-icon">
                                    <h3>No Medical Records Found</h3>
                                    <p>You haven't visited any doctors yet. Your medical records will appear here once you have consultations.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="health-tips">
                <h4>
                    <i class="fas fa-lightbulb"></i>
                    Health Tip
                </h4>
                <p>Regular check-ups help maintain good health and catch potential issues early. Schedule routine visits with your healthcare provider to stay on top of your wellness journey.</p>
            </div>
        </div>
    </div>
</body>
</html>

<?php $stmt->close(); ?>
<?php include 'includes/footer.php'; ?>