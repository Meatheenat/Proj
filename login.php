<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th" data-bs-theme="light"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Library System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css">

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <style>
        [data-bs-theme="light"] {
            --bg-page: #f8f9fa;
            --bg-card: #ffffff;
            --text-color: #212529;
            --input-bg: #ffffff;
            --input-border: #dee2e6;
            --info-bg: #e7f1ff;
        }

        [data-bs-theme="dark"] {
            --bg-page: #121212;
            --bg-card: #1e1e1e;
            --text-color: #f8f9fa;
            --input-bg: #2b2b2b;
            --input-border: #444444;
            --info-bg: #0d2137;
        }

        body {
            background-color: var(--bg-page) !important;
            color: var(--text-color) !important;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .navbar { background-color: #212529 !important; }

        .login-wrapper {
            min-height: calc(100vh - 56px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background-color: var(--bg-card) !important;
            border: none !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important;
            border-radius: 20px;
        }

        .form-control {
            background-color: var(--input-bg) !important;
            color: var(--text-color) !important;
            border-color: var(--input-border) !important;
        }

        /* สไตล์กล่องคู่มือ Login */
        .test-account-box {
            background-color: var(--info-bg);
            border-radius: 12px;
            padding: 15px;
            margin-top: 25px;
            border: 1px dashed #0d6efd;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
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

<div class="login-wrapper">
    <div class="card shadow-lg" style="width: 100%; max-width: 450px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-person-badge text-primary" style="font-size: 3.5rem;"></i>
                <h3 class="fw-bold mt-2">ยินดีต้อนรับสู่ห้องสมุด</h3>
                <p class="opacity-75 small">เข้าสู่ระบบเพื่อใช้งานระบบยืม-คืนหนังสือ</p>
            </div>

            <form action="auth_action.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small">ชื่อผู้ใช้งาน</label>
                    <input type="text" name="username" class="form-control form-control-lg" placeholder="กรอก Username" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold small">รหัสผ่าน (Password)</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="กรอกรหัสผ่าน" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 mb-3 fw-bold py-2 shadow-sm">
                    เข้าสู่ระบบ
                </button>
            </form>

            <div class="text-center small mb-4">
                <a href="register.php" class="text-decoration-none fw-bold">สมัครสมาชิกใหม่</a>
                <span class="mx-2 opacity-50">|</span>
                <a href="forgot_password.php" class="text-decoration-none opacity-75">ลืมรหัสผ่าน?</a>
            </div>

            <div class="test-account-box">
                <h6 class="fw-bold text-primary mb-2"><i class="bi bi-info-circle-fill me-2"></i>ข้อมูลสำหรับทดสอบระบบ (Test Accounts)</h6>
                <div class="row g-0 small">
                    <div class="col-6 border-end pe-2">
                        <p class="mb-1 fw-bold text-success">สิทธิ์สมาชิก (User)</p>
                        <div class="opacity-75">User: <code class="fw-bold">U</code></div>
                        <div class="opacity-75">Pass: <code class="fw-bold">1234</code></div>
                    </div>
                    <div class="col-6 ps-2">
                        <p class="mb-1 fw-bold text-danger">สิทธิ์แอดมิน (Admin)</p>
                        <div class="opacity-75">User: <code class="fw-bold">P</code></div>
                        <div class="opacity-75">Pass: <code class="fw-bold">1234</code></div>
                    </div>
                </div>
            </div>
            </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

    updateIcon(htmlElement.getAttribute('data-bs-theme'));

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    }

    // ระบบซ่อน/แสดงรหัสผ่าน
    const toggleBtn = document.querySelector('.toggle-password');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    }
});
</script>

</body>
</html>