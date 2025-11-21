<?php
include 'includes/header.php';

if ($_SESSION['role'] !== 'doctor') {
    header("location: login.php");
    exit;
}

if (!isset($_GET['record_id'])) {
    header("location: dashboard_doctor.php");
    exit;
}

$record_id = $_GET['record_id'];

// Fetch existing record data
$stmt = $conn->prepare("SELECT * FROM medical_records WHERE record_id = ?");
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-error'>
            <img src='src/2.png' alt='Error' class='alert-icon'>
            <span>Record not found.</span>
          </div>";
    exit;
}

$record = $result->fetch_assoc();
$patient_id = $record['patient_id'];
$record_date = $record['record_date'];
$diagnosis = $record['diagnosis'];
$prescription = $record['prescription'];

// Fetch patient name for display
$patient_name = $conn->query("SELECT full_name FROM users WHERE id=$patient_id")->fetch_assoc()['full_name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $record_date = $_POST['record_date'];
    $diagnosis = $_POST['diagnosis'];
    $prescription = $_POST['prescription'];

    $update_stmt = $conn->prepare("UPDATE medical_records SET record_date = ?, diagnosis = ?, prescription = ? WHERE record_id = ?");
    $update_stmt->bind_param("sssi", $record_date, $diagnosis, $prescription, $record_id);

    if ($update_stmt->execute()) {
        echo "<div class='alert alert-success'>
                <img src='src/3.png' alt='Success' class='alert-icon'>
                <span>Record updated successfully!</span>
              </div>";
        echo "<script>setTimeout(function(){ window.location.href = 'view_medical_rec.php?rec_id=". $record_id."'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-error'>
                <img src='src/2.png' alt='Error' class='alert-icon'>
                <span>Error updating record.</span>
              </div>";
    }
    $update_stmt->close();
}

$stmt->close();
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
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80') center/cover;
            opacity: 0.1;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .doctor-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .patient-info {
            font-size: 16px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .form-container {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 18px;
            z-index: 2;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #444;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input[type="date"],
        textarea {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            font-family: inherit;
            resize: vertical;
            transition: all 0.3s ease;
            background: #fafbfc;
        }

        input[type="date"]:focus,
        textarea:focus {
            border-color: #667eea;
            outline: none;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea {
            min-height: 120px;
            line-height: 1.6;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            min-width: 140px;
            justify-content: center;
        }
        

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #6c757d;
            border: 2px solid #e9ecef;
        }

        .btn-secondary:hover {
            background: #e9ecef;
            color: #495057;
            transform: translateY(-1px);
        }

        .alert {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 12px;
            margin: 20px 0;
            font-weight: 500;
            animation: slideIn 0.3s ease-out;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da, #f1b0b7);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-icon {
            width: 24px;
            height: 24px;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .form-container {
                padding: 20px;
            }

            .header {
                padding: 20px;
            }

            h2 {
                font-size: 24px;
            }

            .button-group {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                min-width: unset;
            }
        }

        .medical-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.03;
            z-index: -1;
            background: url('src/index.jpg') center/cover;
        }
    </style>
</head>

<body>
    <div class="medical-bg"></div>
    
    <div class="container">
        <div class="header">
            <div class="header-content">
                <img src="src/1.png" alt="Doctor" class="doctor-icon">
                <h2><i class="fas fa-edit"></i> Edit Medical Record</h2>
                <div class="patient-info">
                    <i class="fas fa-user"></i>
                    <span>Patient: <?php echo htmlspecialchars($patient_name); ?></span>
                </div>
            </div>
        </div>

        <div class="form-container">
            <form action="edit_record.php?record_id=<?php echo $record_id; ?>" method="post">
                <div class="form-group">
                    <label for="record_date">
                        <i class="fas fa-calendar-alt"></i> Record Date
                    </label>
                    <div class="input-group">
                        <i class="fas fa-calendar-alt input-icon"></i>
                        <input type="date" name="record_date" id="record_date" value="<?php echo htmlspecialchars($record_date); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="diagnosis">
                        <i class="fas fa-stethoscope"></i> Diagnosis
                    </label>
                    <div class="input-group">
                        <i class="fas fa-stethoscope input-icon"></i>
                        <textarea name="diagnosis" id="diagnosis" rows="5" required placeholder="Enter detailed diagnosis..."><?php echo htmlspecialchars($diagnosis); ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="prescription">
                        <i class="fas fa-prescription-bottle-alt"></i> Prescription
                    </label>
                    <div class="input-group">
                        <i class="fas fa-prescription-bottle-alt input-icon"></i>
                        <textarea name="prescription" id="prescription" rows="5" required placeholder="Enter prescription details..."><?php echo htmlspecialchars($prescription); ?></textarea>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Record
                    </button>
                    <a href="view_record.php?rec_id=<?php echo $record_id; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php include 'includes/footer.php'; ?>