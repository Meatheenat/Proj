<?php 
// 1. เปิดโหมดดู Error เพื่อการตรวจสอบ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 

// 2. ตรวจสอบการ Login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// 3. นำเข้าไฟล์เชื่อมต่อฐานข้อมูล
include('config/db.php');

// 4. ระบบนับจำนวนแจ้งเตือนหนังสือเกินกำหนด (Notification Count)
$today = date('Y-m-d');
$user_id = $_SESSION['user_id'];
$sql_noti = "SELECT COUNT(*) as total FROM borrow_records 
             WHERE user_id = '$user_id' 
             AND status = 'pending' 
             AND due_date < '$today'";
$res_noti = mysqli_query($conn, $sql_noti);
$noti_count = mysqli_fetch_assoc($res_noti)['total'];

// 5. ดึงข้อมูลหนังสือแนะนำ สุ่มมา 4 เล่ม
$sql_recommend = "SELECT * FROM books ORDER BY RAND() LIMIT 4";
$result_recommend = mysqli_query($conn, $sql_recommend);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรก - ระบบยืมคืนหนังสือ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        /* สไตล์เพิ่มเติมเฉพาะหน้า Index */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color, #4e73df) 0%, #224abe 100%);
            border-radius: 20px;
            color: white;
        }
        [data-bs-theme="dark"] .hero-section {
            background: linear-gradient(135deg, #1e1e1e 0%, #333 100%);
            border: 1px solid #444;
        }
        .book-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        .book-card:hover {
            transform: translateY(-10px);
        }
        .book-img-placeholder {
            height: 220px;
            background: #eee;
            border-radius: 15px 15px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #ccc;
        }
        [data-bs-theme="dark"] .book-img-placeholder {
            background: #2a2a2a;
            color: #444;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
        <i class="bi bi-book-half me-2"></i>LibraryMobile
    </a>
    
    <div class="ms-auto d-flex align-items-center">
        <a href="notifications.php" class="btn btn-link text-white position-relative me-3 p-0" style="text-decoration: none;">
            <i class="bi bi-bell-fill fs-5"></i>
            <?php if($noti_count > 0) { ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                    <?php echo $noti_count; ?>
                </span>
            <?php } ?>
        </a>

        <button class="btn btn-link text-white me-3 p-0" id="themeToggle" type="button" style="text-decoration: none;">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>

        <div class="dropdown">
            <button class="btn btn-outline-light btn-sm dropdown-toggle fw-bold px-3" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['fullname']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px;">
                <li><a class="dropdown-item" href="history.php"><i class="bi bi-clock-history me-2 text-primary"></i>ประวัติของฉัน</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</a></li>
            </ul>
        </div>
    </div>
  </div>
</nav>

<div class="container mt-4 mb-5">
    
    <div class="hero-section shadow-sm mb-5">
        <div class="p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-md-7 text-center text-md-start">
                    <h1 class="fw-bold mb-3">สวัสดีครับคุณ, <?php echo explode(' ', $_SESSION['fullname'])[0]; ?>!</h1>
                    <p class="lead mb-4 opacity-75">ค้นหาหนังสือที่ใช่ และทำรายการยืม-คืนได้รวดเร็วผ่านระบบออนไลน์ของคุณ</p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                        <a href="books.php" class="btn btn-light btn-lg fw-bold px-4 text-primary shadow-sm"><i class="bi bi-search me-2"></i>ค้นหาหนังสือ</a>
                        <a href="return_book.php" class="btn btn-success btn-lg fw-bold px-4 shadow-sm"><i class="bi bi-arrow-left-right me-2"></i>คืนหนังสือ</a>
                    </div>
                </div>
                <div class="col-md-5 d-none d-md-block text-center">
                    <i class="bi bi-journal-bookmark-fill" style="font-size: 8rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
    <div class="alert alert-dark border-0 shadow-sm d-flex justify-content-between align-items-center p-3 mb-5" style="border-radius: 15px;">
        <span class="fw-bold"><i class="bi bi-shield-lock-fill me-2 text-warning"></i>ระบบจัดการสำหรับผู้ดูแลระบบ</span>
        <a href="admin_dashboard.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-3">ไปที่หน้าแอดมิน</a>
    </div>
    <?php } ?>

    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-bold m-0"><i class="bi bi-stars text-warning me-2"></i>หนังสือแนะนำ</h3>
            <p class="text-muted m-0">สุ่มหนังสือที่น่าสนใจมาให้คุณได้อ่าน</p>
        </div>
        <a href="books.php" class="btn btn-outline-primary fw-bold rounded-pill px-3 shadow-sm">ดูทั้งหมด</a>
    </div>

    <div class="row g-4">
        <?php 
        if($result_recommend && mysqli_num_rows($result_recommend) > 0) {
            while($book = mysqli_fetch_assoc($result_recommend)) {
                $is_available = ($book['status'] == 'available');
                $status_text = $is_available ? 'ว่าง' : 'ถูกยืมแล้ว';
                $status_color = $is_available ? 'bg-success' : 'bg-danger';
        ?>
        
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card book-card h-100 shadow-sm">
                <div class="book-img-placeholder">
                    <i class="bi bi-book"></i>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <span class="badge <?php echo $status_color; ?> rounded-pill px-2"><?php echo $status_text; ?></span>
                    </div>
                    <h6 class="card-title fw-bold text-truncate" title="<?php echo htmlspecialchars($book['book_name']); ?>">
                        <?php echo htmlspecialchars($book['book_name']); ?>
                    </h6>
                    <p class="card-text text-muted small mb-3">โดย: <?php echo htmlspecialchars($book['author']); ?></p>
                    
                    <div class="mt-auto">
                        <?php if($is_available) { ?>
                            <a href="borrow.php?id=<?php echo $book['book_id']; ?>" class="btn btn-primary btn-sm w-100 fw-bold rounded-pill shadow-sm py-2">ยืมเล่มนี้</a>
                        <?php } else { ?>
                            <button class="btn btn-secondary btn-sm w-100 fw-bold rounded-pill py-2" disabled>ไม่ว่าง</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <?php 
            }
        } else {
            echo "<div class='col-12 text-center py-5'><i class='bi bi-inbox text-muted' style='font-size: 3rem;'></i><p class='text-muted mt-2'>ยังไม่มีข้อมูลหนังสือในขณะนี้</p></div>";
        }
        ?>
    </div>

</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center text-md-start">
        <div class="row align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
                <p class="mb-0 fw-bold">LibraryMobile System</p>
                <p class="small opacity-50 mb-0">© 2026 ระบบจัดการห้องสมุดยุคใหม่ เพื่อการเรียนรู้</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-white opacity-50 me-3 text-decoration-none small">ข้อกำหนดการใช้งาน</a>
                <a href="#" class="text-white opacity-50 text-decoration-none small">นโยบายความเป็นส่วนตัว</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>

</body>
</html>