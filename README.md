# 🏥 Online Medical Record System – README

## 📘 Overview

The **Online Medical Record System** is a secure, web-based application built with **PHP** to store, manage, and access patient medical records digitally. It supports **role‑based access** for **Patients, Doctors, and Administrators**, ensuring that sensitive medical information is protected and accessible only to authorized users.

This system helps hospitals and clinics transition from manual record‑keeping to a modern, digital, efficient solution.

---

## ✨ Key Features

### 🔒 Authentication & Security

* Secure login and logout
* Encrypted password storage
* Authorization checks for sensitive operations
* Session‑based access control

### 👩‍⚕️ User Roles

#### **Patients**

* Register as a new patient
* View personal medical records and history
* Update personal profile details
* Change password

#### **Doctors**

* Search for patients
* Add new medical records
* Update or delete existing medical records

#### **Administrators**

* Manage all user accounts
* Register doctors and system users
* View basic system reports
* Oversee system operations

---

## ⚙️ System Functionalities

* Patient self‑registration
* Password recovery ("Forgot Password")
* Medical record creation, updating, and management (doctor only)
* Profile management for all user types
* Contact and help pages for guidance
* Mobile‑friendly responsive UI

---

## 🛡️ Privacy & Data Protection

* Patient data securely stored in database
* Passwords hashed using secure encryption
* Role‑based restricted page access
* Unauthorized viewing/updating prevented

---

## 🚀 Performance & Usability

* Loads within 3 seconds (optimized)
* Clean, modern, responsive interface
* Works on desktops, tablets, and mobiles

---

## ❓ FAQs

**🔹 Forgot your password?**
Use the **"Forgot Password"** option.

**🔹 Can patients edit medical records?**
No. Only doctors can add or update medical records. Patients may edit only their personal details.

**🔹 Can users self‑register?**
Yes, but only as **patients**. Doctors and admins must be registered by the system administrator.

---

## 🖥️ Installation & Setup

### 1️⃣ Requirements

* PHP 7.4+
* Apache or Nginx server
* MySQL / MariaDB
* Composer (optional)

### 2️⃣ Steps to Run the Project

**Clone the project:**

```bash
git clone https://github.com/nethmiu/Medical_recoard_system.git
```

**Move the project to your server directory:**

* XAMPP: `htdocs/`
* WAMP: `www/`
* Linux: `/var/www/html/`

**Import the database:**

1. Open phpMyAdmin
2. Create a database (e.g., `medical_records`)
3. Import the provided SQL file

**Configure database connection** (in `config.php` or similar):

```php
$conn = mysqli_connect("localhost", "root", "", "medical_records");
```

**Start your server and visit:**

```
http://localhost/Medical_recoard_system
```

Log in using the default credentials or create a new patient account.

---

## 🧑‍💻 Technologies Used

* **PHP** (Backend)
* **MySQL** (Database)
* **HTML5, CSS3** (Frontend)
* **Font Awesome** (Icons)
* **JavaScript** (Interactivity)

---

## 📄 License

This project is **open‑source** and available for educational and development use.

<img width="1919" height="909" alt="image" src="https://github.com/user-attachments/assets/5b2291d2-b7bd-4c34-8120-9455cfb2fcb3" />
<img width="1903" height="913" alt="image" src="https://github.com/user-attachments/assets/2cde4af3-ecbc-4d32-9ae4-80deb7fb5a23" />
<img width="1901" height="912" alt="image" src="https://github.com/user-attachments/assets/6a9a0701-5bd1-4ca5-aa16-25d874bb86a4" />
<img width="1906" height="909" alt="image" src="https://github.com/user-attachments/assets/1285d3ec-1b52-4d1a-9533-691dd629ffa8" />
<img width="1905" height="985" alt="image" src="https://github.com/user-attachments/assets/dacfe453-2026-4778-a56c-87e0273a9f17" />
<img width="1918" height="918" alt="image" src="https://github.com/user-attachments/assets/dcf90bfb-7aaa-4a00-a42f-9ef1bb92e780" />

<img width="1906" height="912" alt="image" src="https://github.com/user-attachments/assets/e51c691b-d128-4892-b8b0-24bbb6415b99" />
<img width="1907" height="917" alt="image" src="https://github.com/user-attachments/assets/ee003bc9-686f-4aaf-a611-556578017702" />
<img width="1905" height="910" alt="image" src="https://github.com/user-attachments/assets/afadd87b-18e7-48b1-9257-33f1f423bd56" />
<img width="1904" height="914" alt="image" src="https://github.com/user-attachments/assets/09d47393-cc1c-4ba5-b8a6-8467dd228283" />
<img width="1906" height="905" alt="image" src="https://github.com/user-attachments/assets/33f1c0f9-dc74-4bbd-8f10-998f1a526549" />
<img width="1903" height="912" alt="image" src="https://github.com/user-attachments/assets/107a5435-2e94-4d00-9d6c-dc7b2e62af6e" />


<img width="1891" height="906" alt="image" src="https://github.com/user-attachments/assets/b0ee522e-850f-4eb1-9783-9613fe216995" />
<img width="1910" height="910" alt="image" src="https://github.com/user-attachments/assets/7aadb950-da2f-4ce9-afe6-658e13e09cab" />
<img width="1917" height="913" alt="image" src="https://github.com/user-attachments/assets/dfbc3304-4a5f-4f75-9b10-e7837ae6aa3d" />
<img width="1908" height="910" alt="image" src="https://github.com/user-attachments/assets/aa5b52b7-bd85-49e8-b767-3297531351d5" />
<img width="1900" height="921" alt="image" src="https://github.com/user-attachments/assets/296700c4-b7a1-4127-8700-f112fe216e62" />







