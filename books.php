<?php 
// 1. เปิดโหมดดู Error เพื่อความชัวร์ในการเช็คระบบ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
include('config/db.php'); // เรียกใช้ตามโครงสร้างไฟล์ของเพื่อน

// 2. ระบบนับแจ้งเตือนหนังสือเกินกำหนด (สำหรับไอคอนกระดิ่งบน Navbar)
$today = date('Y-m-d');
$user_id = $_SESSION['user_id'];
$sql_noti = "SELECT COUNT(*) as total FROM borrow_records 
             WHERE user_id = '$user_id' AND status = 'pending' AND due_date < '$today'";
$res_noti = mysqli_query($conn, $sql_noti);
$noti_count = ($res_noti) ? mysqli_fetch_assoc($res_noti)['total'] : 0;

// ==========================================
// ระบบค้นหาและตัวกรอง (Keep Logic เดิมของเพื่อน)
// ==========================================
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

$sql = "SELECT * FROM books WHERE 1=1";
if($search != ''){
    $sql .= " AND (book_name LIKE '%$search%' 
              OR author LIKE '%$search%' 
              OR category LIKE '%$search%'
              OR publisher LIKE '%$search%'
              OR publish_year LIKE '%$search%')";
}
if($category != ''){ $sql .= " AND category = '$category'"; }
if($status != ''){ $sql .= " AND status = '$status'"; }

$sql .= " ORDER BY book_id DESC";
$result = mysqli_query($conn, $sql);

$cat_sql = "SELECT DISTINCT category FROM books WHERE category IS NOT NULL AND category != ''";
$cat_result = mysqli_query($conn, $cat_sql);
?>
<!DOCTYPE html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการหนังสือ - Library System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css"> 

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <style>
        /* --- นิยามตัวแปรสี Full Dark Mode เปลี่ยนทั้งหน้า --- */
        [data-bs-theme="light"] {
            --bg-page: #f8f9fa;
            --bg-card: #ffffff;
            --text-color: #212529;
            --input-bg: #ffffff;
            --input-border: #dee2e6;
        }
        [data-bs-theme="dark"] {
            --bg-page: #121212;      /* ดำสนิททั้งหน้า */
            --bg-card: #1e1e1e;      /* การ์ดและกล่องค้นหาสีเข้ม */
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

        .navbar { background-color: #212529 !important; }

        /* กล่องค้นหา (Search Box) ให้ลอยและเปลี่ยนสีตามธีม */
        .search-box {
            background-color: var(--bg-card) !important;
            color: var(--text-color) !important;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }

        .form-control, .form-select {
            background-color: var(--input-bg) !important;
            color: var(--text-color) !important;
            border-color: var(--input-border) !important;
        }

        .book-card {
            background-color: var(--bg-card) !important;
            border: none !important;
            border-radius: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3) !important;
        }

        .book-cover-placeholder {
            height: 180px;
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 3.5rem; border-radius: 15px 15px 0 0;
        }
        [data-bs-theme="dark"] .book-cover-placeholder {
            background: linear-gradient(135deg, #2a2a2a 0%, #444 100%);
            opacity: 0.7;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top pb-5 shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
        <i class="bi bi-book-half me-2 text-primary"></i>LibraryMobile
    </a>
    
    <div class="ms-auto d-flex align-items-center">
        <a href="notifications.php" class="btn btn-link text-white position-relative me-3 p-0">
            <i class="bi bi-bell-fill fs-5"></i>
            <?php if($noti_count > 0) { ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                    <?php echo $noti_count; ?>
                </span>
            <?php } ?>
        </a>

        <button class="btn btn-link text-white me-3 p-0" id="themeToggle" type="button">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>

        <div class="dropdown">
            <button class="btn btn-outline-light btn-sm dropdown-toggle fw-bold px-3 rounded-pill" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['fullname']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li><a class="dropdown-item" href="history.php"><i class="bi bi-clock-history me-2 text-primary"></i>ประวัติของฉัน</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</a></li>
            </ul>
        </div>
    </div>
  </div>
</nav>

<div class="container mb-5">
    
    <div class="search-box mb-5">
        <form action="books.php" method="GET">
            <div class="row g-3">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search opacity-50"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="ชื่อหนังสือ, ผู้แต่ง, สำนักพิมพ์..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                
                <div class="col-6 col-md-3">
                    <select name="category" class="form-select fw-bold">
                        <option value="">-- ทุกหมวดหมู่ --</option>
                        <?php while($cat = mysqli_fetch_assoc($cat_result)) { ?>
                            <option value="<?php echo $cat['category']; ?>" <?php if($category == $cat['category']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($cat['category']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select fw-bold">
                        <option value="">-- ทุกสถานะ --</option>
                        <option value="available" <?php if($status == 'available') echo 'selected'; ?>>ว่าง (ยืมได้)</option>
                        <option value="borrowed" <?php if($status == 'borrowed') echo 'selected'; ?>>ถูกยืมแล้ว</option>
                    </select>
                </div>
                
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-2">ค้นหา</button>
                </div>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">ผลการค้นหา</h4>
        <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm">พบ <?php echo mysqli_num_rows($result); ?> เล่ม</span>
    </div>

    <div class="row g-4">
        <?php 
        if(mysqli_num_rows($result) > 0) {
            while($book = mysqli_fetch_assoc($result)) {
                $is_avail = ($book['status'] == 'available');
                $status_color = $is_avail ? 'bg-success' : 'bg-danger';
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card book-card h-100 border-0">
                <div class="book-cover-placeholder"><i class="bi bi-book"></i></div>
                <div class="card-body d-flex flex-column p-3">
                    <div class="mb-2"><span class="badge <?php echo $status_color; ?> rounded-pill px-2"><?php echo $is_avail ? 'ว่าง' : 'ถูกยืม'; ?></span></div>
                    <h6 class="card-title fw-bold text-truncate mb-1" title="<?php echo htmlspecialchars($book['book_name']); ?>">
                        <?php echo htmlspecialchars($book['book_name']); ?>
                    </h6>
                    <p class="card-text opacity-75 small mb-2"><i class="bi bi-person me-1"></i><?php echo htmlspecialchars($book['author']); ?></p>
                    <div class="mt-auto">
                        <?php if($is_avail) { ?>
                            <a href="borrow.php?id=<?php echo $book['book_id']; ?>" class="btn btn-primary btn-sm w-100 fw-bold rounded-pill shadow-sm">ยืมเล่มนี้</a>
                        <?php } else { ?>
                            <button class="btn btn-secondary btn-sm w-100 fw-bold rounded-pill opacity-50" disabled>ไม่ว่าง</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } } else { ?>
            <div class='col-12 text-center py-5'><i class='bi bi-search text-muted fs-1'></i><p class='text-muted mt-2'>ไม่พบข้อมูลหนังสือ</p></div>
        <?php } ?>
    </div>
</div>

<footer class="text-center py-4 mt-5 opacity-50"><p class="small">LibraryMobile System © 2026</p></footer>

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