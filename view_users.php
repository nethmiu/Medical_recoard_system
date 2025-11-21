<?php
// view_users.php
include 'includes/header.php';

if ($_SESSION['role'] !== 'admin') {
    header("location: login.php");
    exit;
}

// Handle user status change / delete
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $action = $_GET['action'];

    if ($action == 'deactivate' || $action == 'activate') {
        $new_status = ($action == 'deactivate') ? 'inactive' : 'active';
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $user_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'delete') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }

    echo "<script> window.location.href='view_users.php';</script>";
    exit;
}

// Fetch all users
$users = $conn->query("SELECT id, full_name, username, role, status, created_at FROM users");
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

    .container {
        max-width: 100%;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 30px 0;
    }

    .header-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 15px 35px rgba(79, 172, 254, 0.3);
        position: relative;
    }

    .header-icon::before {
        content: '';
        position: absolute;
        top: -3px;
        left: -3px;
        right: -3px;
        bottom: -3px;
        background: linear-gradient(45deg, #ff9a9e, #fecfef, #fecfef);
        border-radius: 50%;
        z-index: -1;
        opacity: 0.5;
        animation: pulse 2s ease-in-out infinite alternate;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.5; }
        100% { transform: scale(1.05); opacity: 0.8; }
    }

    h2 {
        color: white;
        font-size: 32px;
        font-weight: 700;
        text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        margin-bottom: 10px;
    }

    h2 i {
        color: #ffd700;
        margin-right: 15px;
        font-size: 36px;
        text-shadow: 0 2px 10px rgba(255, 215, 0, 0.5);
    }

    .subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 16px;
        font-weight: 300;
        max-width: 500px;
        margin: 0 auto;
    }

    .search-container {
        text-align: center;
        margin: 30px 0 40px;
        position: relative;
    }

    .search-wrapper {
        position: relative;
        display: inline-block;
        max-width: 400px;
        width: 100%;
    }

    .search-container input {
        width: 100%;
        padding: 16px 20px 16px 55px;
        border-radius: 25px;
        border: none;
        font-size: 16px;
        outline: none;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        color: #374151;
    }

    .search-container input:focus {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        background: white;
    }

    .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 18px;
        transition: color 0.3s ease;
    }

    .search-container input:focus + .search-icon {
        color: #4facfe;
    }

    .table-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 
            0 25px 50px rgba(0, 0, 0, 0.15),
            0 0 0 1px rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
    }

    .table-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #4facfe, #00f2fe, #667eea);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: transparent;
    }

    table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
    }

    table thead::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #ffd700, #ffed4e);
    }

    table th {
        padding: 20px 15px;
        text-align: center;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 14px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    table td {
        padding: 18px 15px;
        text-align: center;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        vertical-align: middle;
    }

    table tbody tr {
        transition: all 0.3s ease;
        position: relative;
    }

    table tbody tr:nth-child(even) {
        background: rgba(249, 249, 249, 0.5);
    }

    table tbody tr:hover {
        background: linear-gradient(135deg, rgba(79, 172, 254, 0.1), rgba(0, 242, 254, 0.1));
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-active {
        background: linear-gradient(135deg, #10b981, #34d399);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .status-inactive {
        background: linear-gradient(135deg, #ef4444, #f87171);
        color: white;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .status-badge i {
        margin-right: 6px;
        font-size: 8px;
    }

    .role-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-admin {
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        color: white;
    }

    .role-user {
        background: linear-gradient(135deg, #06b6d4, #67e8f9);
        color: white;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
        align-items: center;
    }

    .btn1 {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn1::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn1:hover::before {
        left: 100%;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
    }

    .btn1 {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn1:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }

    .btn-edit {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .btn-edit:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
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

    .stats-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 15px;
        margin-bottom: 20px;
        text-align: center;
        color: white;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .date-cell {
        font-family: 'Courier New', monospace;
        font-weight: 500;
        color: #6b7280;
    }

    @media (max-width: 1200px) {
        .container {
            padding: 0 15px;
        }
        
        table {
            font-size: 14px;
        }
        
        table th, table td {
            padding: 12px 8px;
        }
    }

    @media (max-width: 768px) {
        .search-container input {
            width: 100%;
            max-width: 300px;
        }
        
        .action-buttons {
            flex-direction: column;
            gap: 5px;
        }
        
        h2 {
            font-size: 24px;
        }
        
        .header-icon {
            width: 60px;
            height: 60px;
        }
        
        table {
            font-size: 12px;
        }
        
        .btn {
            width: 32px;
            height: 32px;
        }
    }

    .no-results {
        text-align: center;
        padding: 40px;
        color: #6b7280;
        font-style: italic;
    }
</style>

<div class="floating-elements">
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
</div>

<div class="container">
    <div class="page-header">
        <div class="header-icon">
            <i class="fa-solid fa-users" style="color: white; font-size: 32px;"></i>
        </div>
        <h2><i class="fa-solid fa-users"></i> All Users</h2>
        <p class="subtitle">Manage and monitor all registered users in your system</p>
    </div>

    <div class="search-container">
        <div class="search-wrapper">
            <input type="text" id="searchInput" placeholder="Search by Full Name...">
            <i class="fa-solid fa-search search-icon"></i>
        </div>
    </div>

    <div class="table-container">
        <table id="usersTable">
            <thead>
                <tr>
                    <th><i class="fa-solid fa-user"></i> Full Name</th>
                    <th><i class="fa-solid fa-at"></i> Username</th>
                    <th><i class="fa-solid fa-crown"></i> Role</th>
                    <th><i class="fa-solid fa-circle-dot"></i> Status</th>
                    <th><i class="fa-solid fa-calendar"></i> Registered On</th>
                    <th><i class="fa-solid fa-cogs"></i> Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td>
                        <span class="role-badge role-<?php echo strtolower($row['role']); ?>">
                            <?php echo ucfirst(htmlspecialchars($row['role'])); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'active'): ?>
                            <span class="status-badge status-active">
                                <i class="fa-solid fa-circle"></i> Active
                            </span>
                        <?php else: ?>
                            <span class="status-badge status-inactive">
                                <i class="fa-solid fa-circle"></i> Inactive
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="date-cell"><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <?php if ($row['status'] == 'active'): ?>
                                <a href="view_users.php?action=deactivate&user_id=<?php echo $row['id']; ?>" 
                                   class="btn btn-danger" 
                                   title="Deactivate User">
                                   <i class="fa-solid fa-user-slash"></i>
                                </a>
                            <?php else: ?>
                                <a href="view_users.php?action=activate&user_id=<?php echo $row['id']; ?>" 
                                   class="btn btn1" 
                                   title="Activate User">
                                   <i class="fa-solid fa-user-check"></i>
                                </a>
                            <?php endif; ?>

                            <a href="update_user.php?user_id=<?php echo $row['id']; ?>" 
                               class="btn btn-edit" 
                               title="Edit User">
                               <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            
                            <a href="view_users.php?action=delete&user_id=<?php echo $row['id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this user?');" 
                               title="Delete User">
                               <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#usersTable tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let fullName = row.querySelector('td').textContent.toLowerCase();
        if (fullName.includes(filter)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    let tbody = document.querySelector('#usersTable tbody');
    let existingNoResults = document.querySelector('.no-results-row');
    
    if (visibleCount === 0 && filter.length > 0) {
        if (!existingNoResults) {
            let noResultsRow = document.createElement('tr');
            noResultsRow.className = 'no-results-row';
            noResultsRow.innerHTML = '<td colspan="6" class="no-results"><i class="fa-solid fa-search"></i> No users found matching your search criteria.</td>';
            tbody.appendChild(noResultsRow);
        }
    } else if (existingNoResults) {
        existingNoResults.remove();
    }
});

// Add loading animation for buttons
document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!this.onclick || !this.onclick.toString().includes('confirm')) {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>