<?php 
// เปิดโหมดดู Error เพื่อการตรวจสอบ (IT Support Style)
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

// 2.2.12 ดึงข้อมูลสมาชิกทั้งหมด
$sql_members = "SELECT user_id, username, fullname, role FROM users ORDER BY user_id DESC";
$res_members = mysqli_query($conn, $sql_members);

// 2.2.14 ดึงข้อมูลการยืมที่ยังไม่คืน (Status: pending)
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
    <title>แอดมินแดชบอร์ด - Library System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .nav-tabs .nav-link { color: var(--text-main); font-weight: 600; }
        .nav-tabs .nav-link.active { border-bottom: 3px solid var(--primary-color); }
        .admin-card { border-radius: 15px; border: none; box-shadow: var(--shadow-soft); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-gear-fill me-2 text-warning"></i>Admin Console</a>
    <div class="ms-auto d-flex align-items-center">
        <button class="btn btn-link text-white me-3 p-0" id="themeToggle" type="button"><i class="bi bi-moon-stars-fill" id="themeIcon"></i></button>
        <span class="text-white me-3 d-none d-md-inline">แอดมิน: <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
        <a href="index.php" class="btn btn-outline-light btn-sm fw-bold">กลับหน้าหลัก</a>
    </div>
  </div>
</nav>

<div class="container mt-5 mb-5">
    <h2 class="fw-bold mb-4 text-primary">การจัดการระบบหลังบ้าน</h2>

    <ul class="nav nav-tabs mb-4" id="adminTab" role="tablist">
        <li class="nav-link-item" role="presentation">
            <button class="nav-link active" id="members-tab" data-bs-toggle="tab" data-bs-target="#members" type="button">
                <i class="bi bi-people me-2"></i>2.2.12 ข้อมูลสมาชิก
            </button>
        </li>
        <li class="nav-link-item" role="presentation">
            <button class="nav-link" id="addbook-tab" data-bs-toggle="tab" data-bs-target="#addbook" type="button">
                <i class="bi bi-plus-circle me-2"></i>2.2.13 เพิ่มรายการหนังสือ
            </button>
        </li>
        <li class="nav-link-item" role="presentation">
            <button class="nav-link" id="borrowed-tab" data-bs-toggle="tab" data-bs-target="#borrowed" type="button">
                <i class="bi bi-journal-check me-2"></i>2.2.14 รายการหนังสือที่ถูกยืม
            </button>
        </li>
    </ul>

    <div class="tab-content" id="adminTabContent">
        
        <div class="tab-pane fade show active" id="members" role="tabpanel">
            <div class="card admin-card">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">รายชื่อสมาชิกในระบบ</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>ตำแหน่ง</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($user = mysqli_fetch_assoc($res_members)) { ?>
                                <tr>
                                    <td><?php echo $user['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                    <td><span class="badge <?php echo ($user['role'] == 'admin') ? 'bg-danger' : 'bg-primary'; ?>"><?php echo $user['role']; ?></span></td>
                                    <td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="addbook" role="tabpanel">
            <div class="card admin-card" style="max-width: 600px; margin: auto;">
                <div class="card-body">
                    <h5 class="fw-bold mb-4 text-center">เพิ่มหนังสือใหม่เข้าระบบ</h5>
                    <form action="add_book_action.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">ชื่อหนังสือ</label>
                            <input type="text" name="book_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ชื่อผู้แต่ง</label>
                            <input type="text" name="author" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">หมวดหมู่</label>
                            <select name="category" class="form-select">
                                <option value="นิยาย">นิยาย</option>
                                <option value="วิชาการ">วิชาการ</option>
                                <option value="การ์ตูน">การ์ตูน</option>
                                <option value="ทั่วไป">ทั่วไป</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mt-2">ยืนยันการเพิ่มหนังสือ</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="borrowed" role="tabpanel">
            <div class="card admin-card">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 text-warning">รายการที่รอดำเนินการคืน</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>ชื่อผู้ยืม</th>
                                    <th>หนังสือ</th>
                                    <th>วันที่ยืม</th>
                                    <th>สถานะ</th>
                                    <th>การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($res_borrowed) > 0) { 
                                      while($row = mysqli_fetch_assoc($res_borrowed)) { ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($row['fullname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['book_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['borrow_date'])); ?></td>
                                    <td><span class="badge bg-warning text-dark">กำลังยืม</span></td>
                                    <td>
                                        <a href="return_action.php?id=<?php echo $row['borrow_id']; ?>" class="btn btn-sm btn-success fw-bold" onclick="return confirm('ยืนยันการคืนหนังสือเล่มนี้?')">ยืนยันการคืน</a>
                                    </td>
                                </tr>
                                <?php } } else { ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">ขณะนี้ไม่มีหนังสือที่ถูกยืมอยู่</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>
</body>
</html>