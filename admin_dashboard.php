<?php 
// 1. เปิดโหมดดู Error (IT Support Style)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 
// ตรวจสอบความปลอดภัย: ต้องเป็น Admin เท่านั้น
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

include('config/db.php');

// ดึงข้อมูลสมาชิกทั้งหมด
$sql_members = "SELECT user_id, username, fullname, role FROM users ORDER BY user_id DESC";
$res_members = mysqli_query($conn, $sql_members);

// ดึงข้อมูลการยืมที่ยังไม่คืน (Status: pending)
$sql_borrowed = "SELECT br.*, b.book_name, u.fullname 
                 FROM borrow_records br
                 JOIN books b ON br.book_id = b.book_id
                 JOIN users u ON br.user_id = u.user_id
                 WHERE br.status = 'pending'
                 ORDER BY br.borrow_date ASC";
$res_borrowed = mysqli_query($conn, $sql_borrowed);
?>
<!DOCTYPE html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Console - Library System</title>
    
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
        /* --- นิยามตัวแปรสี Full Dark Mode --- */
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
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .navbar { background-color: #212529 !important; }

        .admin-card { 
            background-color: var(--bg-card) !important;
            border-radius: 15px; 
            border: none; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.1); 
        }

        .nav-tabs .nav-link { 
            color: var(--text-color); 
            font-weight: 600; 
            border: none;
            opacity: 0.6;
        }
        .nav-tabs .nav-link.active { 
            color: var(--primary-color, #4e73df) !important; 
            background: transparent !important;
            border-bottom: 3px solid var(--primary-color, #4e73df) !important; 
            opacity: 1;
        }

        .table { color: var(--text-color) !important; }
        .form-control, .form-select {
            background-color: var(--input-bg) !important;
            color: var(--text-color) !important;
            border-color: var(--input-border) !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
        <i class="bi bi-shield-lock-fill me-2 text-warning"></i>Library Admin
    </a>
    <div class="ms-auto d-flex align-items-center">
        <button class="btn btn-link text-white me-3 p-0" id="themeToggle" type="button">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>
        <span class="text-white me-3 d-none d-md-inline small opacity-75">ผู้ดูแล: <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
        <a href="index.php" class="btn btn-outline-light btn-sm fw-bold rounded-pill px-3">กลับหน้าบ้าน</a>
    </div>
  </div>
</nav>

<div class="container mt-5 mb-5">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="fw-bold m-0 text-primary">การจัดการระบบหลังบ้าน</h2>
            <p class="text-muted small">LibraryMobile Console v1.2</p>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4 border-0" id="adminTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active px-4 py-3" id="members-tab" data-bs-toggle="tab" data-bs-target="#members" type="button">
                <i class="bi bi-people me-2"></i>ข้อมูลสมาชิก
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-4 py-3" id="addbook-tab" data-bs-toggle="tab" data-bs-target="#addbook" type="button">
                <i class="bi bi-plus-circle me-2"></i>เพิ่มหนังสือใหม่
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-4 py-3" id="borrowed-tab" data-bs-toggle="tab" data-bs-target="#borrowed" type="button">
                <i class="bi bi-journal-check me-2"></i>รายการค้างส่ง
            </button>
        </li>
    </ul>

    <div class="tab-content" id="adminTabContent">
        
        <div class="tab-pane fade show active" id="members" role="tabpanel">
            <div class="card admin-card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-person-lines-fill me-2 text-primary"></i>รายชื่อสมาชิกในระบบ</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>ตำแหน่ง</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($user = mysqli_fetch_assoc($res_members)) { ?>
                                <tr>
                                    <td><?php echo $user['user_id']; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo ($user['role'] == 'admin') ? 'bg-danger' : 'bg-primary'; ?>">
                                            <?php echo strtoupper($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-pencil-square"></i></button>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="addbook" role="tabpanel">
            <div class="card admin-card mx-auto shadow-sm" style="max-width: 650px;">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-plus-circle-fill text-primary" style="font-size: 3rem;"></i>
                        <h4 class="fw-bold mt-2">เพิ่มหนังสือใหม่เข้าระบบ</h4>
                    </div>
                    <form action="add_book_action.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">ชื่อหนังสือ</label>
                            <input type="text" name="book_name" class="form-control form-control-lg" placeholder="ระบุชื่อหนังสือ" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">ชื่อผู้แต่ง</label>
                                <input type="text" name="author" class="form-control form-control-lg" placeholder="ชื่อผู้แต่ง" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">หมวดหมู่</label>
                                <select name="category" class="form-select form-select-lg">
                                    <option value="นิยาย">นิยาย</option>
                                    <option value="วิชาการ">วิชาการ</option>
                                    <option value="การ์ตูน">การ์ตูน</option>
                                    <option value="ทั่วไป">ทั่วไป</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small">จำนวนวันที่อนุญาตให้ยืม (คั่นด้วยคอมม่า)</label>
                            <input type="text" name="borrow_duration" class="form-control form-control-lg" value="7,10,15,30" placeholder="เช่น 7,15,30">
                            <div class="form-text small opacity-50">ตัวเลือกที่สมาชิกจะเห็นตอนกดยืมหนังสือ</div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm rounded-pill py-3">
                            <i class="bi bi-check-circle me-2"></i>ยืนยันการเพิ่มหนังสือ
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="borrowed" role="tabpanel">
            <div class="card admin-card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-warning"><i class="bi bi-clock-history me-2"></i>รายการที่รอดำเนินการคืน</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ผู้ยืม</th>
                                    <th>หนังสือ</th>
                                    <th>วันที่ยืม</th>
                                    <th>กำหนดคืน</th>
                                    <th class="text-center">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($res_borrowed) > 0) { 
                                      while($row = mysqli_fetch_assoc($res_borrowed)) { 
                                          $is_overdue = (date('Y-m-d') > $row['due_date']);
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($row['fullname']); ?></div>
                                        <small class="text-muted">Member ID: <?php echo $row['user_id']; ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['book_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['borrow_date'])); ?></td>
                                    <td class="<?php echo $is_overdue ? 'text-danger fw-bold' : ''; ?>">
                                        <?php echo date('d/m/Y', strtotime($row['due_date'])); ?>
                                        <?php echo $is_overdue ? '<i class="bi bi-exclamation-circle ms-1"></i>' : ''; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="return_action.php?id=<?php echo $row['borrow_id']; ?>" 
                                           class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm" 
                                           onclick="return confirm('ยืนยันว่าสมาชิกคืนหนังสือเล่มนี้แล้วใช่หรือไม่?')">
                                           รับคืนสำเร็จ
                                        </a>
                                    </td>
                                </tr>
                                <?php } } else { ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">ขณะนี้ไม่มีหนังสือที่ถูกยืมอยู่</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

[Image of an administrative dashboard UI with data tables and form elements in a clean dark theme layout]

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
            const newTheme = (currentTheme === 'light') ? 'dark' : 'light';
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    }
});
</script>

</body>
</html>