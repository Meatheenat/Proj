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

include('config/db.php'); // เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลสมาชิก
$res_members = mysqli_query($conn, "SELECT user_id, username, fullname, role, status FROM users ORDER BY user_id DESC");

// ดึงรายการค้างส่ง
$res_borrowed = mysqli_query($conn, "SELECT br.*, b.book_name, u.fullname FROM borrow_records br JOIN books b ON br.book_id = b.book_id JOIN users u ON br.user_id = u.user_id WHERE br.status = 'pending' ORDER BY br.borrow_date ASC");

// รับค่า Tab ปัจจุบัน (เพื่อให้หน้าเว็บค้างอยู่ที่เดิมเวลา Search)
$active_tab = $_GET['tab'] ?? 'members';
?>
<!DOCTYPE html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Console - Library System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <style>
        [data-bs-theme="light"] { --bg-page: #f8f9fa; --bg-card: #ffffff; --text-color: #212529; --input-bg: #ffffff; --input-border: #dee2e6; }
        [data-bs-theme="dark"] { --bg-page: #121212; --bg-card: #1e1e1e; --text-color: #f8f9fa; --input-bg: #2b2b2b; --input-border: #444444; }
        body { background-color: var(--bg-page) !important; color: var(--text-color) !important; font-family: 'Sarabun', sans-serif; transition: all 0.3s ease; min-height: 100vh; }
        .navbar { background-color: #212529 !important; }
        .admin-card { background-color: var(--bg-card) !important; border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; }
        .nav-tabs .nav-link { color: var(--text-color); font-weight: 600; border: none; opacity: 0.6; }
        .nav-tabs .nav-link.active { color: #4e73df !important; background: transparent !important; border-bottom: 3px solid #4e73df !important; opacity: 1; }
        .table { color: var(--text-color) !important; }
        .form-control, .form-select { background-color: var(--input-bg) !important; color: var(--text-color) !important; border-color: var(--input-border) !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-shield-lock-fill me-2 text-warning"></i>Library Admin</a>
    <div class="ms-auto d-flex align-items-center">
        <button class="btn btn-link text-white me-3 p-0" id="themeToggle" type="button"><i class="bi bi-moon-stars-fill" id="themeIcon"></i></button>
        <span class="text-white me-3 d-none d-md-inline small">แอดมิน: <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
        <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold">กลับหน้าหลัก</a>
    </div>
  </div>
</nav>

<div class="container mt-5 mb-5">
    <div class="mb-4">
        <h2 class="fw-bold m-0 text-primary">การจัดการระบบหลังบ้าน</h2>
        <p class="text-muted small">จัดระเบียบสมาชิก หนังสือ และการยืมคืน</p>
    </div>

    <ul class="nav nav-tabs mb-4 border-0" id="adminTab" role="tablist">
        <li class="nav-item"><button class="nav-link <?php if($active_tab == 'members') echo 'active'; ?> px-4 py-3" data-bs-toggle="tab" data-bs-target="#members" type="button"><i class="bi bi-people-fill me-2"></i>สมาชิก</button></li>
        <li class="nav-item"><button class="nav-link <?php if($active_tab == 'managebooks') echo 'active'; ?> px-4 py-3" data-bs-toggle="tab" data-bs-target="#managebooks" type="button"><i class="bi bi-journal-bookmark-fill me-2"></i>คลังหนังสือ</button></li>
        <li class="nav-item"><button class="nav-link px-4 py-3" data-bs-toggle="tab" data-bs-target="#addbook" type="button"><i class="bi bi-plus-circle-fill me-2"></i>เพิ่มหนังสือ</button></li>
        <li class="nav-item"><button class="nav-link px-4 py-3" data-bs-toggle="tab" data-bs-target="#borrowed" type="button"><i class="bi bi-clock-history me-2"></i>รายการค้างส่ง</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade <?php if($active_tab == 'members') echo 'show active'; ?>" id="members" role="tabpanel">
            <div class="card admin-card p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr><th>ชื่อ-นามสกุล</th><th>บทบาท</th><th>สถานะ</th><th>เปลี่ยนสิทธิ์</th><th class="text-center">จัดการ</th></tr>
                        </thead>
                        <tbody>
                            <?php while($user = mysqli_fetch_assoc($res_members)) { $is_banned = ($user['status'] == 'banned'); ?>
                            <tr>
                                <td><div class="fw-bold"><?php echo htmlspecialchars($user['fullname']); ?></div><small class="opacity-50">@<?php echo htmlspecialchars($user['username']); ?></small></td>
                                <td><span class="badge rounded-pill <?php echo ($user['role'] == 'admin') ? 'bg-danger' : 'bg-primary'; ?>"><?php echo strtoupper($user['role']); ?></span></td>
                                <td><span class="badge rounded-pill <?php echo $is_banned ? 'bg-secondary' : 'bg-success'; ?>"><?php echo $is_banned ? 'ถูกระงับ' : 'ปกติ'; ?></span></td>
                                <td>
                                    <form action="manage_user_action.php" method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <select name="new_role" class="form-select form-select-sm w-auto d-inline" onchange="this.form.submit()">
                                            <option value="user" <?php if($user['role'] == 'user') echo 'selected'; ?>>User</option>
                                            <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                        </select>
                                        <input type="hidden" name="action" value="update_role">
                                    </form>
                                </td>
                                <td class="text-center">
                                    <?php if($user['user_id'] != $_SESSION['user_id']) { ?>
                                        <a href="manage_user_action.php?id=<?php echo $user['user_id']; ?>&action=toggle_status&status=<?php echo $user['status']; ?>" class="btn btn-sm <?php echo $is_banned ? 'btn-outline-success' : 'btn-outline-danger'; ?> rounded-pill px-3" onclick="return confirm('ยืนยัน?')"><?php echo $is_banned ? 'ปลดแบน' : 'แบน'; ?></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade <?php if($active_tab == 'managebooks') echo 'show active'; ?>" id="managebooks" role="tabpanel">
            <div class="card admin-card p-4">
                <div class="mb-4 p-3 bg-body-tertiary rounded-4 border">
                    <form action="admin_dashboard.php" method="GET" class="row g-2">
                        <input type="hidden" name="tab" value="managebooks">
                        <div class="col-md-5">
                            <input type="text" name="search_book" class="form-control" placeholder="ค้นชื่อหนังสือ/ผู้แต่ง..." value="<?= $_GET['search_book'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="cat_filter" class="form-select">
                                <option value="">ทุกหมวดหมู่</option>
                                <?php 
                                $cats = mysqli_query($conn, "SELECT DISTINCT category FROM books WHERE category != ''");
                                while($c = mysqli_fetch_assoc($cats)) {
                                    $sel = (isset($_GET['cat_filter']) && $_GET['cat_filter'] == $c['category']) ? 'selected' : '';
                                    echo "<option value='".$c['category']."' $sel>".$c['category']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill">ค้นหา</button>
                            <a href="admin_dashboard.php?tab=managebooks" class="btn btn-outline-secondary px-3 rounded-pill">ล้างค่า</a>
                        </div>
                    </form>
                </div>

                <?php 
                $s = mysqli_real_escape_string($conn, $_GET['search_book'] ?? '');
                $f = mysqli_real_escape_string($conn, $_GET['cat_filter'] ?? '');
                $q = "SELECT * FROM books WHERE 1=1";
                if($s != '') $q .= " AND (book_name LIKE '%$s%' OR author LIKE '%$s%')";
                if($f != '') $q .= " AND category = '$f'";
                $q .= " ORDER BY book_id DESC";
                $res_manage = mysqli_query($conn, $q);
                ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr><th>หน้าปก</th><th>ชื่อหนังสือ</th><th>หมวดหมู่</th><th>สถานะ</th><th class="text-center">จัดการ</th></tr>
                        </thead>
                        <tbody>
                            <?php while($b = mysqli_fetch_assoc($res_manage)) { ?>
                            <tr>
                                <td>
                                    <?php if($b['book_image']): ?>
                                        <img src="assets/img/covers/<?= $b['book_image'] ?>" width="40" height="55" style="object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <i class="bi bi-book opacity-25"></i>
                                    <?php endif; ?>
                                </td>
                                <td><div class="fw-bold"><?= htmlspecialchars($b['book_name']) ?></div><small class="text-muted"><?= htmlspecialchars($b['author']) ?></small></td>
                                <td><?= htmlspecialchars($b['category']) ?></td>
                                <td><span class="badge rounded-pill <?= ($b['status'] == 'available') ? 'bg-success' : 'bg-danger' ?>"><?= ($b['status'] == 'available') ? 'ว่าง' : 'ถูกยืม' ?></span></td>
                                <td class="text-center">
                                    <a href="edit_book.php?id=<?= $b['book_id'] ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3 me-1"><i class="bi bi-pencil"></i></a>
                                    <?php if($b['status'] == 'available'): ?>
                                        <a href="delete_book_action.php?id=<?= $b['book_id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('ลบหนังสือเล่มนี้?')"><i class="bi bi-trash"></i></a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary rounded-pill px-3" disabled title="ถูกยืมอยู่ ลบไม่ได้"><i class="bi bi-lock-fill"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="addbook" role="tabpanel">
            <div class="card admin-card mx-auto p-4 p-md-5" style="max-width: 800px;">
                <h4 class="fw-bold mb-4 text-center text-primary"><i class="bi bi-plus-circle me-2"></i>เพิ่มหนังสือใหม่</h4>
                <form action="add_book_action.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label class="form-label fw-bold small">ชื่อหนังสือ</label>
                            <input type="text" name="book_name" class="form-control" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-bold small">ชื่อผู้แต่ง</label>
                            <input type="text" name="author" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">หมวดหมู่</label>
                            <select name="category" class="form-select">
                                <?php 
                                $cat_list = mysqli_query($conn, "SELECT DISTINCT category FROM books WHERE category != ''");
                                while($cl = mysqli_fetch_assoc($cat_list)) { echo "<option value='".$cl['category']."'>".$cl['category']."</option>"; }
                                ?>
                                <option value="ทั่วไป">ทั่วไป</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">ระยะเวลายืม (วัน)</label>
                            <input type="text" name="borrow_duration" class="form-control" value="7,15,30">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">รูปหน้าปก</label>
                        <input type="file" name="book_image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small">เรื่องย่อ</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-pill shadow">บันทึกหนังสือเข้าฐานข้อมูล</button>
                </form>
            </div>
        </div>

        <div class="tab-pane fade" id="borrowed" role="tabpanel">
            <div class="card admin-card p-4">
                <h5 class="fw-bold mb-4 text-warning">รายการหนังสือที่รอดำเนินการคืน</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr><th>ผู้ยืม</th><th>หนังสือ</th><th>กำหนดคืน</th><th class="text-center">จัดการ</th></tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($res_borrowed) > 0) { while($row = mysqli_fetch_assoc($res_borrowed)) { $is_overdue = (isset($row['due_date']) && date('Y-m-d') > $row['due_date']); ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($row['book_name']); ?></td>
                                <td class="<?php echo $is_overdue ? 'text-danger fw-bold' : ''; ?>"><?php echo isset($row['due_date']) ? date('d/m/Y', strtotime($row['due_date'])) : '-'; ?></td>
                                <td class="text-center"><a href="return_action.php?id=<?php echo $row['borrow_id']; ?>" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" onclick="return confirm('รับคืน?')">รับคืน</a></td>
                            </tr>
                            <?php } } else { echo '<tr><td colspan="4" class="text-center py-5 opacity-50">ไม่มีรายการค้างส่ง</td></tr>'; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ระบบเปลี่ยน Theme
    const themeToggle = document.getElementById('themeToggle'); const themeIcon = document.getElementById('themeIcon'); const htmlElement = document.documentElement;
    function updateIcon(theme) { if (theme === 'dark') { themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill'); themeIcon.style.color = '#ffc107'; } else { themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill'); themeIcon.style.color = '#ffffff'; } }
    updateIcon(htmlElement.getAttribute('data-bs-theme'));
    themeToggle.addEventListener('click', function() { const currentTheme = htmlElement.getAttribute('data-bs-theme'); const newTheme = currentTheme === 'light' ? 'dark' : 'light'; htmlElement.setAttribute('data-bs-theme', newTheme); localStorage.setItem('theme', newTheme); updateIcon(newTheme); });

    // ฟังก์ชั่นคอนเฟิร์มเปลี่ยนสิทธิ์
    function confirmRoleChange(selectObj, name, currentRole) { if (confirm(`เปลี่ยนสิทธิ์ ${name} เป็น ${selectObj.value.toUpperCase()}?`)) selectObj.form.submit(); else selectObj.value = currentRole; }
</script>
</body>
</html>