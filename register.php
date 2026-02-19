<div class="container d-flex justify-content-center mt-5">
    <div class="card login-card">
        <div class="card-body">
            <h3 class="text-center mb-4">สมัครสมาชิก</h3>
            <form action="auth_action.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">ชื่อ-นามสกุล</label>
                    <input type="text" name="fullname" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">ชื่อผู้ใช้งาน</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="register" class="btn btn-success w-100">ยืนยันสมัครสมาชิก</button>
                <p class="text-center mt-3"><a href="login.php">มีบัญชีอยู่แล้ว? เข้าสู่ระบบ</a></p>
            </form>
        </div>
    </div>
</div>