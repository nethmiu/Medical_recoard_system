<?php
// view_records.php
include 'includes/header.php';

if ($_SESSION['role'] !== 'doctor') { 
    header("location: login.php"); 
    exit; 
}
if (!isset($_GET['patient_id'])) { 
    echo "<script> window.location.href='dashboard_doctor.php';</script>";
    exit; 
}
if ($_GET['patient_id'] <= 0 || $_GET['patient_id'] == null){
    echo "<script> window.location.href='dashboard_doctor.php';</script>";
    exit; 
}

$patient_id = intval($_GET['patient_id']);

// Sanitize and get dates from GET (or default null)
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Fetch patient details
$patient = $conn->query("SELECT full_name, phone_number, address FROM users WHERE id=$patient_id")->fetch_assoc();

// Prepare SQL query with optional date filtering
$sql = "
    SELECT mr.*, u.full_name as doctor_name 
    FROM medical_records mr 
    JOIN users u ON mr.doctor_id = u.id 
    WHERE mr.patient_id = ?
";

$params = [$patient_id];
$param_types = "i";

if ($start_date && $end_date) {
    $sql .= " AND mr.record_date BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $param_types .= "ss";
}

$sql .= " ORDER BY mr.record_date DESC";

$stmt = $conn->prepare($sql);

if (count($params) == 3) {
    $stmt->bind_param($param_types, $params[0], $params[1], $params[2]);
} else {
    $stmt->bind_param($param_types, $params[0]);
}

$stmt->execute();
$records = $stmt->get_result();
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px 0;
    }

    .records-container {
        max-width: 1200px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: 
            0 25px 50px -12px rgba(0, 0, 0, 0.25),
            0 0 0 1px rgba(255, 255, 255, 0.1);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .header-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .header-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover;
        opacity: 0.1;
        z-index: 0;
    }

    .header-content {
        position: relative;
        z-index: 1;
    }

    .records-container h2 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .records-container h2 i {
        font-size: 2.2rem;
        background: rgba(255, 255, 255, 0.2);
        padding: 12px;
        border-radius: 16px;
        backdrop-filter: blur(10px);
    }

    .patient-info {
        font-size: 1.1rem;
        margin-bottom: 24px;
        background: rgba(255, 255, 255, 0.15);
        padding: 20px;
        border-radius: 16px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .patient-info strong {
        font-weight: 600;
        margin-right: 8px;
    }

    .patient-info i {
        margin-right: 8px;
        width: 20px;
        text-align: center;
    }

    .content-section {
        padding: 40px;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, #00bcd4, #00acc1);
        color: white;
        padding: 14px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-size: 1rem;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 32px;
        box-shadow: 0 8px 25px rgba(0, 188, 212, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .back-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(0, 188, 212, 0.4);
        background: linear-gradient(135deg, #00acc1, #0097a7);
    }

    .back-btn i {
        font-size: 1.1rem;
    }

    .filter-section {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        padding: 32px;
        border-radius: 20px;
        margin-bottom: 32px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .filter-form {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .filter-form label {
        font-weight: 600;
        color: #374151;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-form label i {
        color: #667eea;
    }

    .filter-form input[type="date"] {
        padding: 12px 16px;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        font-size: 1rem;
        background: white;
        transition: all 0.3s ease;
        min-width: 160px;
    }

    .filter-form input[type="date"]:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-btn {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        display: flex;
        align-items: center;
        gap: 8px;
        align-self: flex-end;
    }

    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    }

    .table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    th {
        padding: 20px 24px;
        text-align: left;
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    th i {
        margin-right: 8px;
    }

    td {
        padding: 20px 24px;
        font-size: 1rem;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
    }

    tbody tr {
        background: white;
        transition: all 0.3s ease;
        position: relative;
    }

    tbody tr:hover {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        transform: translateX(4px);
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.1);
    }

    tbody tr:hover::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .btn-view {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        background: linear-gradient(135deg, #059669, #047857);
    }

    .no-records {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    .no-records i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #d1d5db;
    }

    .no-records h3 {
        font-size: 1.5rem;
        margin-bottom: 12px;
        color: #374151;
    }

    .no-records p {
        font-size: 1.1rem;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .records-container {
            margin: 10px;
            border-radius: 16px;
        }

        .header-section {
            padding: 30px 20px;
        }

        .records-container h2 {
            font-size: 2rem;
        }

        .content-section {
            padding: 20px;
        }

        .filter-section {
            padding: 20px;
        }

        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            width: 100%;
        }

        .filter-form input[type="date"] {
            min-width: 100%;
        }

        .filter-btn {
            align-self: center;
            width: 100%;
            justify-content: center;
        }

        .table-container {
            overflow-x: auto;
        }

        table, thead, tbody, th, td, tr {
            display: block;
            width: 100%;
        }

        thead {
            display: none;
        }

        tr {
            margin-bottom: 16px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        td:before {
            content: attr(data-label);
            font-weight: 600;
            color: #667eea;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td:last-child {
            border-bottom: none;
        }
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .records-container {
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    tbody tr {
        animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    tbody tr:nth-child(1) { animation-delay: 0.1s; }
    tbody tr:nth-child(2) { animation-delay: 0.2s; }
    tbody tr:nth-child(3) { animation-delay: 0.3s; }
    tbody tr:nth-child(4) { animation-delay: 0.4s; }
    tbody tr:nth-child(5) { animation-delay: 0.5s; }
</style>

<div class="records-container">
    <div class="header-section">
        <div class="header-content">
            <h2><i class="fas fa-user-md"></i>Medical History for: <?php echo htmlspecialchars($patient['full_name']); ?></h2>
            <div class="patient-info">
                <div><i class="fas fa-phone"></i><strong>Phone:</strong> <?php echo htmlspecialchars($patient['phone_number']); ?></div>
                <div style="margin-top: 8px;"><i class="fas fa-home"></i><strong>Address:</strong> <?php echo htmlspecialchars($patient['address']); ?></div>
            </div>
        </div>
    </div>

    <div class="content-section">
        <a href="dashboard_doctor.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Patient List
        </a>

        <div class="filter-section">
            <form method="GET" class="filter-form">
                <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                
                <div class="filter-group">
                    <label for="start_date"><i class="fas fa-calendar-alt"></i>Start Date:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                </div>

                <div class="filter-group">
                    <label for="end_date"><i class="fas fa-calendar-alt"></i>End Date:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>

                <button type="submit" class="filter-btn">
                    <i class="fas fa-filter"></i>
                    Filter Records
                </button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar"></i>Record Date</th>
                        <th><i class="fas fa-user-md"></i>Doctor</th>
                        <th><i class="fas fa-cogs"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($records->num_rows > 0): ?>
                        <?php while($row = $records->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Record Date"><?php echo htmlspecialchars($row['record_date']); ?></td>
                            <td data-label="Doctor">Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td data-label="Actions">
                                <a href="view_medical_rec.php?rec_id=<?php echo $row['record_id']; ?>" class="btn-view">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="no-records">
                                <i class="fas fa-folder-open"></i>
                                <h3>No Records Found</h3>
                                <p>No medical records found for this patient in the selected date range.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
$stmt->close(); 
include 'includes/footer.php'; 
?>