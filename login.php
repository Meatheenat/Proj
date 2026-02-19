<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Library System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; height: 100vh; display: flex; align-items: center; }
        .login-card { width: 100%; max-width: 400px; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="card login-card">
        <div class="card-body">
            <h3 class="text-center mb-4">เข้าสู่ระบบ</h3>
            <form action="auth_action.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">ชื่อผู้ใช้งาน</label>
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
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

</body>
</html>