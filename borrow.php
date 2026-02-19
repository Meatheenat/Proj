<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 
// 1. ตรวจสอบว่า Login หรือยัง
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include('config/db.php');
date_default_timezone_set('Asia/Bangkok'); // ตั้งค่าโซนเวลาให้เป็นประเทศไทย

// 2. ตรวจสอบว่ามีการส่งรหัสหนังสือ (id) มาหรือไม่
if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ไม่พบรหัสหนังสือที่ต้องการยืม'); window.location='books.php';</script>";
    exit();
}

$book_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

// 3. ดึงข้อมูลหนังสือมาแสดงผล
$sql_book = "SELECT * FROM books WHERE book_id = '$book_id'";
$result_book = mysqli_query($conn, $sql_book);

if(mysqli_num_rows($result_book) == 0) {
    echo "<script>alert('ไม่พบข้อมูลหนังสือในระบบ'); window.location='books.php';</script>";
    exit();
}

$book = mysqli_fetch_assoc($result_book);

// 4. เช็คซ้ำอีกรอบว่าหนังสือว่างจริงไหม (ป้องกันคนกดเข้าลิงก์ตรงๆ)
if($book['status'] == 'borrowed') {
    echo "<script>alert('ขออภัย หนังสือเล่มนี้ถูกยืมไปแล้ว'); window.location='books.php';</script>";
    exit();
}

// ==========================================
// 5. เมื่อผู้ใช้กดปุ่ม "ยืนยันการยืม" (POST Request)
// ==========================================
if(isset($_POST['confirm_borrow'])) {
    $borrow_date = date('Y-m-d'); // ดึงวันที่ปัจจุบัน
    
    // 5.1 บันทึกประวัติลงตาราง borrow_records
    $sql_insert = "INSERT INTO borrow_records (user_id, book_id, borrow_date, status) 
                   VALUES ('$user_id', '$book_id', '$borrow_date', 'pending')";
                   
    if(mysqli_query($conn, $sql_insert)) {
        // 5.2 ถ้าบันทึกประวัติสำเร็จ ให้อัปเดตสถานะหนังสือเป็นถูกยืม ('borrowed')
        $sql_update = "UPDATE books SET status = 'borrowed' WHERE book_id = '$book_id'";
        mysqli_query($conn, $sql_update);
        
        // เด้งไปหน้าประวัติการยืม (ที่เดี๋ยวเราจะทำต่อไป)
        echo "<script>
                alert('ทำรายการยืมสำเร็จ! ขอบคุณที่ใช้บริการ'); 
                window.location='history.php';
              </script>";
        exit();
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันการยืมหนังสือ - ระบบยืมคืนหนังสือ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .borrow-wrapper {
            min-height: calc(100vh - 56px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .borrow-card {
            width: 100%;
            max-width: 500px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: white;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="books.php"><i class="bi bi-arrow-left me-2"></i>กลับหน้ารายการ</a>
    <div class="ms-auto text-white">
        <span class="d-none d-sm-inline">สวัสดี, <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
    </div>
  </div>
</nav>

<div class="container borrow-wrapper">
    <div class="borrow-card border-0">
        <div class="card-header-custom">
            <i class="bi bi-book mb-2" style="font-size: 3rem;"></i>
            <h4 class="fw-bold mb-0">ยืนยันการทำรายการ</h4>
        </div>
        
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-4 text-center">รายละเอียดการยืม</h5>
            
            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold text-muted small">ชื่อหนังสือ</div>
                        <span class="fs-6 text-primary fw-bold"><?php echo htmlspecialchars($book['book_name']); ?></span>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold text-muted small">ผู้แต่ง / หมวดหมู่</div>
                        <?php echo htmlspecialchars($book['author']); ?> <br>
                        <span class="badge bg-secondary mt-1"><?php echo htmlspecialchars($book['category']); ?></span>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold text-muted small">ชื่อผู้ยืม</div>
                        <?php echo htmlspecialchars($_SESSION['fullname']); ?>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold text-muted small">วันที่ทำรายการ</div>
                        <span class="text-success fw-bold"><?php echo date('d/m/Y'); ?></span>
                    </div>
                </li>
            </ul>

            <form action="borrow.php?id=<?php echo $book['book_id']; ?>" method="POST">
                <div class="d-flex gap-2">
                    <a href="books.php" class="btn btn-light btn-lg w-50 fw-bold border">ยกเลิก</a>
                    <button type="submit" name="confirm_borrow" class="btn btn-primary btn-lg w-50 fw-bold">ยืนยันการยืม</button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>