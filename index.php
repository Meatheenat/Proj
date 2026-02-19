<?php 
// เปิดโหมดดู Error (ถ้ามีปัญหาอีกจะได้รู้ทันทีว่าบรรทัดไหน)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 
// ตรวจสอบการ Login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// นำเข้าไฟล์เชื่อมต่อฐานข้อมูล
include('config/db.php');

// ดึงข้อมูลหนังสือแนะนำ สุ่มมา 4 เล่ม
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
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .book-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            border: none;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .book-cover-placeholder {
            height: 200px;
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-book-half me-2"></i>LibraryMobile</a>
    <div class="ms-auto text-white d-flex align-items-center">
        <span class="me-3 d-none d-sm-inline">สวัสดี, <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
        <a href='logout.php' class='btn btn-outline-danger btn-sm fw-bold'>ออกจากระบบ</a>
    </div>
  </div>
</nav>

<div class="container mt-4 mb-5">
    
    <div class="card bg-white shadow-sm border-0 mb-4" style="border-radius: 15px;">
        <div class="card-body p-4 p-md-5 d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="text-center text-md-start mb-4 mb-md-0">
                <h2 class="fw-bold text-primary mb-2">ยินดีต้อนรับสู่ระบบห้องสมุด</h2>
                <p class="text-muted mb-0">ค้นหาหนังสือที่คุณสนใจ และทำรายการยืม-คืนได้ง่ายๆ ผ่านมือถือของคุณ</p>
            </div>
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="books.php" class="btn btn-primary btn-lg fw-bold px-4"><i class="bi bi-search me-2"></i>ค้นหาหนังสือ</a>
                <a href="history.php" class="btn btn-outline-secondary btn-lg fw-bold px-4"><i class="bi bi-clock-history me-2"></i>ประวัติของฉัน</a>
                
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                    <a href="admin_dashboard.php" class="btn btn-dark btn-lg fw-bold px-4"><i class="bi bi-gear me-2"></i>แอดมิน</a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 mt-5">
        <h4 class="fw-bold text-dark m-0"><i class="bi bi-star-fill text-warning me-2"></i>หนังสือแนะนำที่น่าสนใจ</h4>
        <a href="books.php" class="text-decoration-none">ดูทั้งหมด <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="row g-4">
        <?php 
        // เช็คว่า Query ทำงานสำเร็จและมีข้อมูลหรือไม่
        if($result_recommend && mysqli_num_rows($result_recommend) > 0) {
            while($book = mysqli_fetch_assoc($result_recommend)) {
                $status_text = ($book['status'] == 'available') ? 'ว่าง (ยืมได้)' : 'ถูกยืมแล้ว';
                $status_color = ($book['status'] == 'available') ? 'bg-success' : 'bg-danger';
        ?>
        
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card book-card h-100 shadow-sm">
                <div class="book-cover-placeholder">
                    <i class="bi bi-book"></i>
                </div>
                <div class="card-body d-flex flex-column">
                    <span class="badge <?php echo $status_color; ?> mb-2 align-self-start"><?php echo $status_text; ?></span>
                    <h6 class="card-title fw-bold text-truncate" title="<?php echo htmlspecialchars($book['book_name']); ?>">
                        <?php echo htmlspecialchars($book['book_name']); ?>
                    </h6>
                    <p class="card-text text-muted small mb-3">ผู้แต่ง: <?php echo htmlspecialchars($book['author']); ?></p>
                    
                    <div class="mt-auto">
                        <?php if($book['status'] == 'available') { ?>
                            <a href="borrow.php?id=<?php echo $book['book_id']; ?>" class="btn btn-outline-primary btn-sm w-100 fw-bold">ขอยืมเล่มนี้</a>
                        <?php } else { ?>
                            <button class="btn btn-secondary btn-sm w-100 fw-bold" disabled>ไม่ว่าง</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <?php 
            }
        } else {
            // แก้ไข Syntax Error ตรงนี้แล้วครับ (เปลี่ยน "" เป็น '')
            echo "<div class='col-12'><p class='text-center text-muted'>ยังไม่มีข้อมูลหนังสือในระบบ</p></div>";
        }
        ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>

</body>
</html>