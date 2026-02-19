<?php 
session_start(); 
// ถ้า Login อยู่แล้ว ให้เด้งไปหน้าแรกของห้องสมุดทันที
if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลืมรหัสผ่าน - Library System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
    
    <style>
        /* --- นิยามตัวแปรสีหลักสำหรับทั้งหน้า --- */
        [data-bs-theme="light"] {
            --bg-page: #f8f9fa;
            --bg-card: #ffffff;
            --text-color: #212529;
            --input-bg: #ffffff;
            --input-border: #dee2e6;
        }

        [data-bs-theme="dark"] {
            --bg-page: #121212;
            --bg-card: #1e1e1e;
            --text-color: #f8f9fa;
            --input-bg: #2b2b2b;
            --input-border: #444444;
        }

        body {
            background-color: var(--bg-page) !important;
            color: var(--text-color) !important;
            font-family: 'Sarabun', sans-serif;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .forgot-wrapper {
            min-height: calc(100vh - 56px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background-color: var(--bg-card) !important;
            color: var(--text-color) !important;
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important;
        }

        .form-control {
            background-color: var(--input-bg) !important;
            color: var(--text-color) !important;
            border-color: var(--input-border) !important;
        }

        .form-control::placeholder {
            color: #888;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
        <i class="bi bi-book-half me-2"></i>LibraryMobile
    </a>
    <div class="ms-auto d-flex align-items-center">
        <button class="btn btn-link text-white me-2 p-0" id="themeToggle" type="button" style="text-decoration: none;">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>
        <a href='login.php' class='btn btn-outline-light btn-sm fw-bold px-3'>เข้าสู่ระบบ</a>
    </div>
  </div>
</nav>

<div class="container forgot-wrapper">
    <div class="card shadow-lg" style="width: 100%; max-width: 450px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-question-circle-fill text-warning" style="font-size: 3.5rem;"></i>
                <h3 class="fw-bold mt-2">ลืมรหัสผ่าน?</h3>
                <p class="opacity-75 small">กรุณากรอกอีเมลเพื่อตรวจสอบและค้นหาบัญชีห้องสมุดของคุณ</p>
            </div>
            
            <form action="auth_action.php" method="POST">
                <div class="mb-4">
                    <label for="email" class="form-label fw-bold small">อีเมลที่ใช้สมัคร (Email)</label>
                    <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="example@email.com" required autofocus>
                </div>
                
                <button type="submit" name="forgot_password" class="btn btn-warning btn-lg w-100 mb-3 fw-bold py-2 shadow-sm text-dark">ตรวจสอบข้อมูล</button>
                
                <div class="text-center mt-3 small">
                    <p class="mb-0 opacity-75">นึกรหัสผ่านออกแล้ว? <a href="login.php" class="text-decoration-none fw-bold text-primary">เข้าสู่ระบบที่นี่</a></p>
                </div>
            </form>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ระบบสลับธีม (Full Page Toggle)
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlElement = document.documentElement;

    function updateIcon(theme) {
        if (theme === 'dark') {
            themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
            themeIcon.style.color = '#ffc107'; 
        } else {
            themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
            themeIcon.style.color = '#ffffff';
        }
    }

    // ตั้งค่าไอคอนตอนโหลด
    updateIcon(htmlElement.getAttribute('data-bs-theme'));

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
            console.log("Forgot Pass Page Theme: " + newTheme);
        });
    }
});
</script>

</body>
</html>