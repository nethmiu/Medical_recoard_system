<?php
// dashboard_admin.php
include 'includes/header.php';

// Ensure only admin can access
if ($_SESSION['role'] !== 'admin') {
    header("location: login.php");
    exit;
}

// Fetch system stats
$patient_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='patient'")->fetch_assoc()['count'];
$doctor_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='doctor'")->fetch_assoc()['count'];
?>

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --info-gradient: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --shadow-soft: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    --shadow-hover: 0 20px 40px -5px rgba(0, 0, 0, 0.15), 0 16px 20px -8px rgba(0, 0, 0, 0.1);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.main-content {
    padding: 2rem;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: calc(100vh - 200px);
}

.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
}

.welcome-section {
    background: var(--primary-gradient);
    border-radius: var(--border-radius);
    padding: 3rem 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-soft);
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.welcome-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=400&h=400&fit=crop&crop=center') center/cover;
    opacity: 0.1;
    transform: rotate(15deg);
}

.welcome-content {
    position: relative;
    z-index: 2;
}

.welcome-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    background: linear-gradient(45deg, #fff, #e0e7ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.welcome-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 300;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--shadow-soft);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--info-gradient);
    border-radius: var(--border-radius) var(--border-radius) 0 0;
}

.stat-card.patients::before {
    background: var(--info-gradient);
}

.stat-card.doctors::before {
    background: var(--success-gradient);
}

.stat-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.stat-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}

.stat-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.stat-icon::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.stat-info {
    flex: 1;
}

.stat-label {
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.stat-number {
    font-size: 3rem;
    font-weight: 800;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-subtitle {
    font-size: 0.85rem;
    color: #9ca3af;
    font-weight: 400;
}

.actions-section {
    background: white;
    border-radius: var(--border-radius);
    padding: 2.5rem;
    box-shadow: var(--shadow-soft);
    text-align: center;
}

.section-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
    position: relative;
    display: inline-block;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: var(--primary-gradient);
    border-radius: 2px;
}

.section-description {
    color: #6b7280;
    font-size: 1rem;
    margin-bottom: 2.5rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    color: white;
    box-shadow: var(--shadow-soft);
    min-width: 200px;
    justify-content: center;
}

.action-btn.primary {
    background: var(--info-gradient);
}

.action-btn.success {
    background: var(--success-gradient);
}

.action-btn::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.1);
    opacity: 0;
    transition: var(--transition);
}

.action-btn:hover::before {
    opacity: 1;
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

.action-btn i {
    font-size: 1.1rem;
}

.quick-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.quick-stat {
    text-align: center;
    padding: 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.5);
}

.quick-stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 0.25rem;
}

.quick-stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

@media (max-width: 768px) {
    .main-content {
        padding: 1rem;
    }
    
    .welcome-title {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .stat-content {
        gap: 1rem;
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .action-btn {
        min-width: 250px;
    }
}

.floating-elements {
    position: fixed;
    top: 50%;
    right: 2rem;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 1rem;
    z-index: 10;
}

.floating-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--primary-gradient);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: var(--shadow-soft);
    transition: var(--transition);
    border: 3px solid white;
}

.floating-btn:hover {
    transform: scale(1.1);
    box-shadow: var(--shadow-hover);
}

@media (max-width: 768px) {
    .floating-elements {
        display: none;
    }
}
</style>

<div class="main-content">
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-content">
                <h1 class="welcome-title">System Administration</h1>
                <p class="welcome-subtitle">Comprehensive management dashboard for your medical record system</p>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card patients">
                <div class="stat-content">
                    <div class="stat-icon">
                        <img src="src/patient.png" alt="Patients" />
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Total Patients</div>
                        <div class="stat-number"><?php echo $patient_count; ?></div>
                        <div class="stat-subtitle">Active registered patients</div>
                    </div>
                </div>
            </div>

            <div class="stat-card doctors">
                <div class="stat-content">
                    <div class="stat-icon">
                        <img src="src/doctor.png" alt="Doctors" />
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Total Doctors</div>
                        <div class="stat-number"><?php echo $doctor_count; ?></div>
                        <div class="stat-subtitle">Medical professionals</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Section -->
        <div class="actions-section">
            <h2 class="section-title">Administrative Actions</h2>
            <p class="section-description">
                Manage your healthcare system efficiently with our comprehensive administrative tools. 
                Add new users, generate detailed reports, and maintain system oversight.
            </p>
            
            <div class="action-buttons">
                <a href="manage_users.php" class="action-btn primary">
                    <i class="fas fa-user-plus"></i>
                    <span>Add Users</span>
                </a>
                <a href="report.php" class="action-btn success">
                    <i class="fas fa-chart-line"></i>
                    <span>View Reports</span>
                </a>
            </div>

            <div class="quick-stats">
                <div class="quick-stat">
                    <div class="quick-stat-number"><?php echo $patient_count + $doctor_count; ?></div>
                    <div class="quick-stat-label">Total Users</div>
                </div>
                <div class="quick-stat">
                    <div class="quick-stat-number">100%</div>
                    <div class="quick-stat-label">System Health</div>
                </div>
                <div class="quick-stat">
                    <div class="quick-stat-number">24/7</div>
                    <div class="quick-stat-label">Availability</div>
                </div>
                <div class="quick-stat">
                    <div class="quick-stat-number">Secure</div>
                    <div class="quick-stat-label">Data Protection</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Buttons -->
<div class="floating-elements">
    <a href="manage_users.php" class="floating-btn" title="Quick Add User">
        <i class="fas fa-plus"></i>
    </a>
    <a href="report.php" class="floating-btn" title="Quick Reports">
        <i class="fas fa-chart-bar"></i>
    </a>
</div>

<?php include 'includes/footer.php'; ?>