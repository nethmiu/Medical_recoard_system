<?php
// help.php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Help - Online Medical Record System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background: #f5f7ff;
            color: var(--dark);
            margin: 0;
            padding: 0;
        }
        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 25px 20px;
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        header::after {
            content: "";
            position: absolute;
            bottom: -50px;
            left: -10%;
            width: 120%;
            height: 100px;
            background: var(--light);
            transform: rotate(-2deg);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h2 {
            color: var(--secondary);
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        h2 i {
            color: var(--primary);
        }
        section {
            background: white;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid var(--primary);
        }
        section:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        ul {
            margin-left: 20px;
            padding-left: 10px;
        }
        li {
            margin-bottom: 8px;
        }
        strong {
            color: var(--secondary);
        }
        a.contact-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        a.contact-link:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        .hero-img {
            text-align: center;
            margin: 30px 0;
        }
        .hero-img img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .faq-item {
            background: #f8f9ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .faq-item strong {
            display: block;
            margin-bottom: 5px;
        }
        .contact-methods {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .contact-card {
            flex: 1;
            min-width: 200px;
            background: #f8f9ff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .contact-card:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-5px);
        }
        .contact-card i {
            font-size: 2rem;
            margin-bottom: 15px;
            color: var(--primary);
        }
        .contact-card:hover i {
            color: white;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        Online Medical Record System - Help Center
    </div>
</header>

<div class="container">
    <div class="hero-img">
        <img src="src/help.jpg" alt="Medical professionals using digital system">
    </div>

    <section>
        <h2><i class="fas fa-info-circle"></i> 1. Introduction</h2>
        <p>This system securely stores and manages patients' medical records digitally, providing authorized access for patients, doctors, and administrators.</p>
    </section>

    <section>
        <h2><i class="fas fa-user-shield"></i> 2. User Roles & Access</h2>
        <ul>
            <li><strong>Patients:</strong> View and manage their own medical records and personal information, Register as a new patient.</li>
            <li><strong>Doctors:</strong> Enter, view, update and delete patients' medical records.</li>
            <li><strong>Administrators:</strong> Manage user accounts, generate user reports, and oversee system operations.</li>
        </ul>
    </section>

    <section>
        <h2><i class="fas fa-cogs"></i> 3. Key Functionalities</h2>
        <ul>
            <li>User login and logout with secure authentication.</li>
            <li>Patient registration and profile management.</li>
            <li>Password management and account security.</li>
            <li>Doctors can search patients, add new medical records, and update existing ones.</li>
            <li>Patients can view their history within a specific time period, view their profiles, update personal details, and change their own password.</li>
            <li>All users can change their own passwords</li>
            <li>Administrators manage accounts and view basic system reports.</li>
        </ul>
    </section>

    <section>
        <h2><i class="fas fa-lock"></i> 4. Security and Privacy</h2>
        <p>All sensitive patient data is securely stored and passwords are encrypted and stored. The system ensures that only authorized users can access specific pages and information.</p>
    </section>

    <section>
        <h2><i class="fas fa-tachometer-alt"></i> 5. Performance and Usability</h2>
        <p>The system is designed to load pages quickly (under 3 seconds) and provide a simple, user-friendly interface for ease of use by all users and compatible with any smart device.</p>
    </section>

    <section>
        <h2><i class="fas fa-question-circle"></i> 6. Frequently Asked Questions (FAQs)</h2>
        <div class="faq-item">
            <strong>Q:</strong> What should I do if I forget my password?<br />
            <strong>A:</strong> Use the "Forgot Password" feature or contact the administrator for help via info@medicare.com.
        </div>
        <div class="faq-item">
            <strong>Q:</strong> Can patients edit their medical records?<br />
            <strong>A:</strong> Patients can update their personal information but not medical records; only doctors can add and update medical records.
        </div>
        <div class="faq-item">
            <strong>Q:</strong> Can user can self register in the system?<br />
            <strong>A:</strong> Yes, users can self-register as a patient. Other roles registrations are only available through the administrator.
        </div>
        <div class="faq-item">
            <strong>Q:</strong> How do I contact support?<br />
            <strong>A:</strong> See the contact information below for assistance.
        </div>
    </section>

    <section>
        <h2><i class="fas fa-headset"></i> 7. Contact & Support</h2>
        <p>If you need further assistance, please contact us:</p>
        
        <div class="contact-methods">
            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <p><a href="mailto:info@medicare.com" class="contact-link">info@medicare.com</a></p>
            </div>
            <div class="contact-card">
                <i class="fas fa-phone"></i>
                <p>(011) 456-7890</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-map-marker-alt"></i>
                <p>123 Medical Drive, Colombo 1</p>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
