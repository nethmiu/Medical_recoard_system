<?php
// dashboard_doctor.php
include 'includes/header.php';

// Ensure only doctor can access
if ($_SESSION['role'] !== 'doctor') {
    header("location: login.php");
    exit;
}

// Fetch all patients
$patients = $conn->query("SELECT id, full_name, phone_number FROM users WHERE role='patient' AND status='active'");
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
        overflow-x: auto;
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

    .page-wrapper {
        position: relative;
        z-index: 1;
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 30px 0;
    }

    .doctor-avatar {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-radius: 50%;
        margin: 0 auto 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 20px 40px rgba(79, 172, 254, 0.3);
        position: relative;
        overflow: hidden;
    }

    .doctor-avatar::before {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        background: linear-gradient(45deg, #ff9a9e, #fecfef, #fecfef);
        border-radius: 50%;
        z-index: -1;
        opacity: 0.6;
        animation: rotate 3s linear infinite;
    }

    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .doctor-avatar i {
        color: white;
        font-size: 40px;
        text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        z-index: 2;
    }

    .welcome-text {
        color: white;
        font-size: 36px;
        font-weight: 700;
        text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        margin-bottom: 10px;
    }

    .subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 18px;
        font-weight: 300;
        max-width: 600px;
        margin: 0 auto;
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 25px;
        box-shadow: 
            0 30px 60px rgba(0, 0, 0, 0.15),
            0 0 0 1px rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
    }

    .dashboard-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #4facfe, #00f2fe, #667eea, #764ba2);
    }

    .dashboard-container h2 {
        font-size: 28px;
        color: #2d3748;
        margin-bottom: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .dashboard-container h2::before {
        content: '🏥';
        font-size: 32px;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }

    .dashboard-container p {
        font-size: 16px;
        color: #64748b;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .stats-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border-left: 5px solid #4facfe;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 60px;
        height: 60px;
        background: rgba(79, 172, 254, 0.1);
        border-radius: 50%;
        transform: translate(20px, -20px);
    }

    .stat-card h3 {
        color: #4facfe;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
    }

    .search-section {
        margin-bottom: 30px;
    }

    .search-container {
        text-align: center;
        position: relative;
        display: inline-block;
        width: 100%;
        max-width: 450px;
        margin: 0 auto;
        display: block;
    }

    .search-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .search-container input {
        width: 100%;
        padding: 18px 25px 18px 60px;
        border-radius: 25px;
        border: none;
        font-size: 16px;
        outline: none;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        color: #374151;
        border: 2px solid transparent;
    }

    .search-container input:focus {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        background: white;
        border-color: #4facfe;
    }

    .search-icon {
        position: absolute;
        left: 22px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 18px;
        transition: color 0.3s ease;
    }

    .search-container input:focus + .search-icon {
        color: #4facfe;
    }

    .table-section {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: transparent;
    }

    thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
    }

    thead::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #ffd700, #ffed4e);
    }

    th {
        padding: 20px 20px;
        text-align: left;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    th i {
        margin-right: 8px;
        color: #ffd700;
    }

    td {
        padding: 20px;
        text-align: left;
        font-size: 15px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        vertical-align: middle;
    }

    tbody tr {
        background: #fff;
        transition: all 0.3s ease;
        position: relative;
    }

    tbody tr:nth-child(even) {
        background: rgba(249, 249, 249, 0.5);
    }

    tbody tr:hover {
        background: linear-gradient(135deg, rgba(79, 172, 254, 0.08), rgba(0, 242, 254, 0.08));
        transform: scale(1.01);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .patient-name {
        font-weight: 600;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .patient-name::before {
        content: '👤';
        font-size: 18px;
    }

    .phone-number {
        color: #64748b;
        font-family: 'Courier New', monospace;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .phone-number::before {
        content: '📞';
        font-size: 16px;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn {
        padding: 10px 18px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    .btn1 {
        background: linear-gradient(135deg, #7e0e74, #b91c7f);
        color: white;
        box-shadow: 0 4px 15px rgba(126, 14, 116, 0.3);
    }

    .btn1:hover {
        background: linear-gradient(135deg, #b91c7f, #d63384);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(126, 14, 116, 0.4);
    }

    .btn-view {
        background: linear-gradient(135deg, #00bcd4, #0097a7);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 188, 212, 0.3);
    }

    .btn-view:hover {
        background: linear-gradient(135deg, #0097a7, #00838f);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 188, 212, 0.4);
    }

    .btn-add {
        background: linear-gradient(135deg, #4CAF50, #388e3c);
        color: white;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }

    .btn-add:hover {
        background: linear-gradient(135deg, #388e3c, #2e7d32);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
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
        width: 120px;
        height: 120px;
        top: 10%;
        left: 5%;
        animation-delay: 0s;
    }

    .floating-shape:nth-child(2) {
        width: 80px;
        height: 80px;
        top: 70%;
        right: 10%;
        animation-delay: 3s;
    }

    .floating-shape:nth-child(3) {
        width: 100px;
        height: 100px;
        bottom: 15%;
        left: 15%;
        animation-delay: 6s;
    }

    .floating-shape:nth-child(4) {
        width: 60px;
        height: 60px;
        top: 40%;
        right: 20%;
        animation-delay: 2s;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.7; }
        50% { transform: translateY(-30px) rotate(180deg); opacity: 1; }
    }

    .no-results {
        text-align: center;
        padding: 40px;
        color: #64748b;
        font-style: italic;
        font-size: 16px;
    }

    .no-results i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #cbd5e1;
    }

    @media (max-width: 1200px) {
        .dashboard-container {
            padding: 30px 25px;
        }
        
        table {
            font-size: 14px;
        }
        
        th, td {
            padding: 15px 12px;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 20px 0;
        }

        .welcome-text {
            font-size: 28px;
        }

        .doctor-avatar {
            width: 80px;
            height: 80px;
        }

        .doctor-avatar i {
            font-size: 32px;
        }

        .dashboard-container {
            padding: 20px 15px;
        }

        .stats-section {
            grid-template-columns: 1fr;
        }

        .search-container input {
            padding: 15px 20px 15px 50px;
        }

        table, thead, tbody, th, td, tr {
            display: block;
            width: 100%;
        }

        thead {
            display: none;
        }

        tr {
            margin-bottom: 20px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        td {
            padding: 15px 20px;
            border-bottom: 1px solid #f1f5f9;
            position: relative;
        }

        td:before {
            content: attr(data-label);
            font-weight: 600;
            display: block;
            color: #4facfe;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .action-buttons {
            flex-direction: column;
            gap: 10px;
            align-items: stretch;
        }

        .btn {
            justify-content: center;
            padding: 12px 20px;
        }
    }

    @media (max-width: 480px) {
        .welcome-text {
            font-size: 24px;
        }

        .dashboard-container h2 {
            font-size: 22px;
        }

        .btn {
            font-size: 13px;
            padding: 10px 15px;
        }
    }
</style>

<div class="floating-elements">
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
</div>

<div class="page-wrapper">
    <div class="page-header">
        <div class="doctor-avatar">
            <i class="fa-solid fa-user-doctor"></i>
        </div>
        <h1 class="welcome-text">👩‍⚕️ Doctor Dashboard</h1>
        <p class="subtitle">Manage your patients and medical records efficiently</p>
    </div>

    <div class="dashboard-container">
        <?php 
        // Count total patients for stats
        $total_patients = $patients->num_rows;
        $patients->data_seek(0); // Reset result pointer
        ?>
        
        <div class="stats-section">
            <div class="stat-card">
                <h3><i class="fa-solid fa-users"></i> Active Patients</h3>
                <div class="stat-value"><?php echo $total_patients; ?></div>
                <div class="stat-label">Currently registered patients</div>
            </div>
            
            <div class="stat-card">
                <h3><i class="fa-solid fa-calendar-check"></i> Today's Access</h3>
                <div class="stat-value"><?php echo date('M d, Y'); ?></div>
                <div class="stat-label">Current session date</div>
            </div>
        </div>

        <h2>👩‍⚕️ Patient List</h2>
        <p>View and manage all your registered patients. You can search, view medical records, and add new records for each patient.</p>

        <div class="search-section">
            <div class="search-container">
                <div class="search-wrapper">
                    <input type="text" id="searchInput" placeholder="Search patient by name...">
                    <i class="fa-solid fa-search search-icon"></i>
                </div>
            </div>
        </div>

        <div class="table-section">
            <table id="patientsTable">
                <thead>
                    <tr>
                        <th><i class="fa-solid fa-user"></i> Patient Name</th>
                        <th><i class="fa-solid fa-phone"></i> Phone Number</th>
                        <th><i class="fa-solid fa-cogs"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $patients->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Patient Name">
                            <div class="patient-name">
                                <?php echo htmlspecialchars($row['full_name']); ?>
                            </div>
                        </td>
                        <td data-label="Phone Number">
                            <div class="phone-number">
                                <?php echo htmlspecialchars($row['phone_number']); ?>
                            </div>
                        </td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="view_records.php?patient_id=<?php echo $row['id']; ?>" class="btn btn-view">
                                    <i class="fa-solid fa-eye"></i> View Records
                                </a>
                                <a href="add_record.php?patient_id=<?php echo $row['id']; ?>" class="btn btn-add">
                                    <i class="fa-solid fa-plus"></i> Add New Record
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#patientsTable tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const patientName = row.querySelector('td[data-label="Patient Name"]').textContent.toLowerCase();
        if(patientName.includes(filter)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    const tbody = document.querySelector('#patientsTable tbody');
    let existingNoResults = document.querySelector('.no-results-row');
    
    if (visibleCount === 0 && filter.length > 0) {
        if (!existingNoResults) {
            const noResultsRow = document.createElement('tr');
            noResultsRow.className = 'no-results-row';
            noResultsRow.innerHTML = `
                <td colspan="3" class="no-results">
                    <i class="fa-solid fa-search"></i>
                    <div>No patients found matching your search criteria.</div>
                    <div style="font-size: 14px; margin-top: 5px;">Try searching with a different name.</div>
                </td>
            `;
            tbody.appendChild(noResultsRow);
        }
    } else if (existingNoResults) {
        existingNoResults.remove();
    }
});

// Add button click animations
document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = '';
        }, 150);
    });
});

// Add smooth scrolling for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats cards on load
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 200 * index);
    });

    // Animate table rows on load
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.4s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, 100 * index);
    });
});
</script>

<?php include 'includes/footer.php'; ?>