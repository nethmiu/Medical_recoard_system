<?php 

session_start();
$user_id = $_SESSION["user_id"] ?? null;
$full_name = $_SESSION['full_name'] ?? null;
$username = $_SESSION['username'] ?? null;
$role = $_SESSION['role'] ?? null;

$doctor_name = $patient_name = $date = $diagnosis = $prescription = "N/A";
$record_id = null;

if ($role == "patient" || $role == "doctor") {
    if (isset($_GET['rec_id'])) {
        $record_id = $_GET['rec_id'];
        require_once 'includes/db_connect.php';

        $stmt = "SELECT * FROM medical_records WHERE record_id = ?";
        $run = $conn->prepare($stmt);
        $run->bind_param("i", $record_id);
        $run->execute();
        $result = $run->get_result();
        if ($row = $result->fetch_assoc()) {
            $patient = $row['patient_id'];
            $doctor = $row['doctor_id'];
            $date = $row['record_date'];
            $diagnosis = $row['diagnosis'];
            $prescription = $row['prescription'];
            $record_id = $row['record_id'];

            // Get patient name
            $get_names_p = "SELECT full_name FROM users WHERE id = ?";
            $stmt_p = $conn->prepare($get_names_p);
            $stmt_p->bind_param("i", $patient);
            $stmt_p->execute();
            $res_p = $stmt_p->get_result();
            if ($row_p = $res_p->fetch_assoc()) {
                $patient_name = $row_p['full_name'];
            }
            $stmt_p->close();

            // Get doctor name
            $get_names_d = "SELECT full_name FROM users WHERE id = ?";
            $stmt_d = $conn->prepare($get_names_d);
            $stmt_d->bind_param("i", $doctor);
            $stmt_d->execute();
            $res_d = $stmt_d->get_result();
            if ($row_d = $res_d->fetch_assoc()) {
                $doctor_name = $row_d['full_name'];
            }
            $stmt_d->close();
        } else {
            $error = "Record not found!";
        }
        $run->close();
    } else {
        $error = "Record ID not provided!";
    }
} else {
    $error = "You are not authorized to view this page.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>View Medical Record</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        margin: 0;
        padding: 20px;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Medical background pattern */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            url('src/index.jpg') center/cover,
            linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
        opacity: 0.1;
        z-index: -1;
    }

    .container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: 
            0 32px 64px rgba(0, 0, 0, 0.2),
            0 0 0 1px rgba(255, 255, 255, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
        max-width: 800px;
        width: 100%;
        padding: 0;
        position: relative;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(255, 255, 255, 0.18);
        overflow: hidden;
    }

    .container:hover {
        transform: translateY(-4px);
        box-shadow: 
            0 40px 80px rgba(0, 0, 0, 0.25),
            0 0 0 1px rgba(255, 255, 255, 0.25);
    }

    .header-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px 50px;
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
        background: url('src/help.jpg') center/cover;
        opacity: 0.15;
        z-index: 0;
    }

    .header-content {
        position: relative;
        z-index: 1;
    }

    h1 {
        margin: 0;
        color: white;
        font-weight: 800;
        text-align: center;
        font-size: 2.5rem;
        letter-spacing: -0.02em;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
    }

    .header-icon {
        background: rgba(255, 255, 255, 0.2);
        padding: 16px;
        border-radius: 20px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .content-section {
        padding: 50px;
    }

    .record-info {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        margin-bottom: 40px;
    }

    .info-item {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        border-radius: 16px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(226, 232, 240, 0.8);
        position: relative;
        overflow: hidden;
    }

    .info-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .info-item:hover {
        transform: translateX(4px);
        box-shadow: 0 8px 30px rgba(102, 126, 234, 0.15);
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    }

    .info-item:hover::before {
        transform: scaleY(1);
    }

    .info-icon {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-weight: 600;
        color: #64748b;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1e293b;
        word-wrap: break-word;
    }

    .card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin-bottom: 32px;
        border: 1px solid rgba(226, 232, 240, 0.6);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        padding: 24px 32px;
        position: relative;
        overflow: hidden;
    }

    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('src/hand.jpg') center/cover;
        opacity: 0.1;
        z-index: 0;
    }

    .card-header-content {
        position: relative;
        z-index: 1;
    }

    .card h2 {
        margin: 0;
        font-size: 1.4rem;
        color: white;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    .card-icon {
        background: rgba(255, 255, 255, 0.2);
        padding: 8px;
        border-radius: 10px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .card-content {
        padding: 32px;
    }

    .card p {
        white-space: pre-wrap;
        line-height: 1.8;
        font-size: 1.1rem;
        color: #374151;
        font-weight: 400;
        margin: 0;
    }

    .error {
        color: #dc2626;
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border: 2px solid #fecaca;
        padding: 32px;
        border-radius: 20px;
        max-width: 600px;
        margin: 0 auto;
        text-align: center;
        font-weight: 600;
        font-size: 1.2rem;
        box-shadow: 0 10px 40px rgba(220, 38, 38, 0.15);
        position: relative;
        backdrop-filter: blur(20px);
    }

    .error::before {
        content: '\f071';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 3rem;
        color: #dc2626;
        display: block;
        margin-bottom: 16px;
        opacity: 0.7;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 16px 32px;
        border-radius: 14px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-align: center;
        user-select: none;
        letter-spacing: 0.02em;
        position: relative;
        overflow: hidden;
        min-width: 160px;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn:hover::before {
        left: 100%;
    }

    .btn-edit {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    }

    .btn-edit:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(59, 130, 246, 0.5);
    }

    .btn-delete {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
    }

    .btn-delete:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(239, 68, 68, 0.5);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
        box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563, #374151);
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(107, 114, 128, 0.5);
    }

    /* Animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .container {
        animation: slideInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .info-item {
        animation: fadeIn 0.6s ease-out;
    }

    .info-item:nth-child(1) { animation-delay: 0.1s; }
    .info-item:nth-child(2) { animation-delay: 0.2s; }
    .info-item:nth-child(3) { animation-delay: 0.3s; }

    .card {
        animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card:nth-child(1) { animation-delay: 0.2s; }
    .card:nth-child(2) { animation-delay: 0.3s; }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        body {
            padding: 10px;
        }

        .container {
            margin: 0;
            border-radius: 20px;
        }

        .header-section {
            padding: 30px 20px;
        }

        h1 {
            font-size: 2rem;
            flex-direction: column;
            gap: 12px;
        }

        .content-section {
            padding: 30px 20px;
        }

        .info-item {
            padding: 20px;
            flex-direction: column;
            text-align: center;
            gap: 16px;
        }

        .info-content {
            width: 100%;
        }

        .card-header {
            padding: 20px 24px;
        }

        .card-content {
            padding: 24px;
        }

        .card h2 {
            font-size: 1.2rem;
            justify-content: center;
        }

        .action-buttons {
            flex-direction: column;
            gap: 16px;
            margin-top: 30px;
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            min-width: auto;
        }
    }

    @media (max-width: 480px) {
        .header-section {
            padding: 20px 15px;
        }

        .content-section {
            padding: 20px 15px;
        }

        h1 {
            font-size: 1.8rem;
        }

        .info-value {
            font-size: 1.1rem;
        }

        .card p {
            font-size: 1rem;
        }
    }
</style>
</head>
<body>

<?php if (isset($error)) : ?>
    <div class="container">
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <div class="action-buttons">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="container" role="main" aria-label="Medical Record Details">
        <div class="header-section">
            <div class="header-content">
                <h1>
                    <div class="header-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    Medical Record Details
                </h1>
            </div>
        </div>

        <div class="content-section">
            <div class="record-info">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Doctor's Name</div>
                        <div class="info-value"><?= htmlspecialchars($doctor_name) ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Patient's Name</div>
                        <div class="info-value"><?= htmlspecialchars($patient_name) ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Record Date</div>
                        <div class="info-value"><?= htmlspecialchars($date) ?></div>
                    </div>
                </div>
            </div>

            <div class="card" aria-label="Diagnosis Information">
                <div class="card-header">
                    <div class="card-header-content">
                        <h2>
                            <div class="card-icon">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                            Diagnosis
                        </h2>
                    </div>
                </div>
                <div class="card-content">
                    <p><?= nl2br(htmlspecialchars($diagnosis)) ?></p>
                </div>
            </div>

            <div class="card" aria-label="Prescription Information">
                <div class="card-header">
                    <div class="card-header-content">
                        <h2>
                            <div class="card-icon">
                                <i class="fas fa-prescription-bottle-alt"></i>
                            </div>
                            Prescription
                        </h2>
                    </div>
                </div>
                <div class="card-content">
                    <p><?= nl2br(htmlspecialchars($prescription)) ?></p>
                </div>
            </div>

            <div class="action-buttons">
                <?php if ($role == 'doctor') : ?>
                    <a href="edit_record.php?record_id=<?= $record_id ?>" class="btn btn-edit" role="button" aria-label="Edit Medical Record">
                        <i class="fas fa-edit"></i>
                        Edit Record
                    </a>
                    <a href="delete_record.php?record_id=<?= $record_id.'&patient_id='.$patient ?>" class="btn btn-delete" role="button" aria-label="Delete Medical Record">
                        <i class="fas fa-trash-alt"></i>
                        Delete Record
                    </a>
                <?php endif; ?>
                <a href="dashboard_patient.php?record_id=<?= $record_id ?>" class="btn btn-secondary" role="button" aria-label="Back to Dashboard">
                    <i class="fas fa-times"></i>
                    Close
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

</body>
</html>