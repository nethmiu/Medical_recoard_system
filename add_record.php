<?php
// add_record.php
include 'includes/header.php';

if ($_SESSION['role'] !== 'doctor') { 
    header("location: login.php"); 
    exit; 
}
if (!isset($_GET['patient_id'])) { 
    header("location: dashboard_doctor.php"); 
    exit; 
}

$patient_id = $_GET['patient_id'];
$doctor_id = $_SESSION['user_id'];

// Fetch patient name
$patient_name = $conn->query("SELECT full_name FROM users WHERE id=$patient_id")->fetch_assoc()['full_name'];

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $record_date = $_POST['record_date'];
    $diagnosis = $_POST['diagnosis'];
    $prescription = $_POST['prescription'];

    $stmt = $conn->prepare("INSERT INTO medical_records (patient_id, doctor_id, record_date, diagnosis, prescription) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $patient_id, $doctor_id, $record_date, $diagnosis, $prescription);
    
    if ($stmt->execute()) {
        $message = "✅ Record added successfully! Redirecting...";
        $messageClass = "success";
        echo "<script>setTimeout(function(){ window.location.href = 'view_records.php?patient_id=".$patient_id."'; }, 2000);</script>";
    } else {
        $message = "❌ Error adding record. Please try again.";
        $messageClass = "error";
    }
    $stmt->close();
}
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            url('src/8.jpg') center/cover,
            linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
        opacity: 0.1;
        z-index: -1;
    }

    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: 
            0 32px 64px rgba(0, 0, 0, 0.2),
            0 0 0 1px rgba(255, 255, 255, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.18);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .form-container:hover {
        transform: translateY(-4px);
        box-shadow: 
            0 40px 80px rgba(0, 0, 0, 0.25),
            0 0 0 1px rgba(255, 255, 255, 0.25);
    }

    .header-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px;
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
        background: url('src/index.jpg') center/cover;
        opacity: 0.15;
        z-index: 0;
    }

    .header-content {
        position: relative;
        z-index: 1;
    }

    .form-container h2 {
        font-size: 2.2rem;
        color: white;
        margin: 0;
        font-weight: 800;
        text-align: center;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        line-height: 1.3;
    }

    .header-icon {
        background: rgba(255, 255, 255, 0.2);
        padding: 16px;
        border-radius: 20px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        font-size: 1.8rem;
    }

    .form-content {
        padding: 50px;
    }

    .message {
        padding: 20px 24px;
        border-radius: 16px;
        margin-bottom: 32px;
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 12px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        animation: slideInDown 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .success {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(22, 163, 74, 0.15));
        color: #059669;
        border-color: rgba(34, 197, 94, 0.3);
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.2);
    }

    .success::before {
        content: '\f00c';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 1.2rem;
        color: #059669;
    }

    .error {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.15));
        color: #dc2626;
        border-color: rgba(239, 68, 68, 0.3);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.2);
    }

    .error::before {
        content: '\f071';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 1.2rem;
        color: #dc2626;
    }

    .form-group {
        margin-bottom: 32px;
        position: relative;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 12px;
        color: #374151;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .label-icon {
        color: #667eea;
        font-size: 1rem;
        width: 20px;
        text-align: center;
    }

    .input-wrapper {
        position: relative;
    }

    input[type="date"],
    textarea {
        width: 100%;
        padding: 16px 20px;
        border-radius: 14px;
        border: 2px solid #e5e7eb;
        font-size: 1.1rem;
        font-family: 'Inter', sans-serif;
        background: white;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    input[type="date"]:focus,
    textarea:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 
            0 0 0 4px rgba(102, 126, 234, 0.1),
            0 8px 25px rgba(102, 126, 234, 0.15);
        transform: translateY(-2px);
    }

    textarea {
        resize: vertical;
        min-height: 120px;
        line-height: 1.6;
    }

    .button-group {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 16px 32px;
        border-radius: 14px;
        text-decoration: none;
        font-size: 1.1rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: 'Inter', sans-serif;
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

    .btn-save {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(16, 185, 129, 0.5);
    }

    .btn-back {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
        box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
    }

    .btn-back:hover {
        background: linear-gradient(135deg, #4b5563, #374151);
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(107, 114, 128, 0.5);
    }

    .form-decoration {
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        border-radius: 50%;
        z-index: -1;
    }

    .form-decoration:nth-child(2) {
        top: auto;
        bottom: -100px;
        left: -100px;
        right: auto;
        width: 300px;
        height: 300px;
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

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
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

    .form-container {
        animation: slideInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .form-group {
        animation: fadeIn 0.6s ease-out;
    }

    .form-group:nth-child(1) { animation-delay: 0.1s; }
    .form-group:nth-child(2) { animation-delay: 0.2s; }
    .form-group:nth-child(3) { animation-delay: 0.3s; }
    .form-group:nth-child(4) { animation-delay: 0.4s; }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        body {
            padding: 10px 0;
        }

        .form-container {
            margin: 10px;
            border-radius: 20px;
        }

        .header-section {
            padding: 30px 20px;
        }

        .form-container h2 {
            font-size: 1.8rem;
            flex-direction: column;
            gap: 12px;
            text-align: center;
        }

        .form-content {
            padding: 30px 20px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        input[type="date"],
        textarea {
            padding: 14px 16px;
            font-size: 1rem;
        }

        .button-group {
            flex-direction: column;
            gap: 16px;
            margin-top: 30px;
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            min-width: auto;
        }

        .message {
            padding: 16px 20px;
            font-size: 1rem;
            margin-bottom: 24px;
        }
    }

    @media (max-width: 480px) {
        .header-section {
            padding: 20px 15px;
        }

        .form-content {
            padding: 20px 15px;
        }

        .form-container h2 {
            font-size: 1.6rem;
        }

        .header-icon {
            padding: 12px;
            font-size: 1.4rem;
        }
    }

    /* Loading animation for form submission */
    .btn-save.loading {
        pointer-events: none;
        opacity: 0.8;
    }

    .btn-save.loading::after {
        content: '';
        width: 20px;
        height: 20px;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-left: 8px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="form-container">
    <div class="form-decoration"></div>
    <div class="form-decoration"></div>
    
    <div class="header-section">
        <div class="header-content">
            <h2>
                <div class="header-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                Add New Medical Record for <?php echo htmlspecialchars($patient_name); ?>
            </h2>
        </div>
    </div>

    <div class="form-content">
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageClass; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="add_record.php?patient_id=<?php echo $patient_id; ?>" method="post" id="medicalForm">
            <div class="form-group">
                <label for="record_date">
                    <i class="fas fa-calendar-alt label-icon"></i>
                    Record Date:
                </label>
                <div class="input-wrapper">
                    <input type="date" name="record_date" id="record_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="diagnosis">
                    <i class="fas fa-stethoscope label-icon"></i>
                    Diagnosis:
                </label>
                <div class="input-wrapper">
                    <textarea name="diagnosis" id="diagnosis" rows="5" placeholder="Enter detailed diagnosis..." required></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="prescription">
                    <i class="fas fa-prescription-bottle-alt label-icon"></i>
                    Prescription:
                </label>
                <div class="input-wrapper">
                    <textarea name="prescription" id="prescription" rows="5" placeholder="Enter medication details and instructions..." required></textarea>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-save" id="saveBtn">
                    <i class="fas fa-save"></i>
                    Save Record
                </button>
                <a href="dashboard_doctor.php" class="btn btn-back">
                    <i class="fas fa-times"></i>
                    Close
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Add loading state to save button on form submission
document.getElementById('medicalForm').addEventListener('submit', function() {
    const saveBtn = document.getElementById('saveBtn');
    saveBtn.classList.add('loading');
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
});

// Auto-resize textareas
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>