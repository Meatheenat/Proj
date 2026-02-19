<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Library System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* CSS เดิมของคุณ */
        body { background-color: #f8f9fa; height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: 0.3s; }
        .login-card { width: 100%; max-width: 400px; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background-color: #fff; }
        
        /* เพิ่ม CSS สำหรับ Dark Mode นิดหน่อยเพื่อให้สลับธีมได้จริง */
        [data-bs-theme="dark"] body { background-color: #121212; }
        [data-bs-theme="dark"] .login-card { background-color: #1e1e1e; color: #fff; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark w-100 fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">LibraryMobile</a>
    <div class="ms-auto text-white d-flex align-items-center">
        <button class="btn btn-link text-white me-2" id="themeToggle" type="button" style="text-decoration: none;">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>
        <a href='login.php' class='btn btn-outline-light btn-sm'>กลับหน้าเข้าสู่ระบบ</a>
    </div>
  </div>
</nav>

<div class="container d-flex justify-content-center">
    <div class="card login-card">
        <div class="card-body">
            <h3 class="text-center mb-4">เข้าสู่ระบบ</h3>
            <form action="auth_action.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">ชื่อผู้ใช้งาน</label>
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">รหัสผ่าน (Password)</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="กรอกรหัสผ่าน" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 mb-3">Login</button>
                <div class="text-center">
                    <a href="register.php" class="text-decoration-none">สมัครสมาชิก</a> | 
                    <a href="forgot_password.php" class="text-decoration-none text-muted">ลืมรหัสผ่าน?</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. ระบบลูกตา เปิด-ปิดรหัสผ่าน (โค้ดเดิมของคุณ)
        const togglePasswordButtons = document.querySelectorAll('.toggle-password');
        togglePasswordButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const inputField = document.getElementById(targetId);
                const icon = this.querySelector('i');
                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                } else {
                    inputField.type = 'password';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                }
            });
        });

        // 2. ระบบสลับธีม Dark/Light
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const html = document.documentElement;

        themeToggle.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-bs-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-bs-theme', newTheme);
            if(newTheme === 'dark') {
                themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
                themeIcon.style.color = '#ffc107';
            } else {
                themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
                themeIcon.style.color = '#fff';
            }
        });
    });
</script>
</body>
</html>