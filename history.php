<?php 
// เปิดโหมดดู Error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 
// ตรวจสอบการ Login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include('config/db.php');

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลประวัติการยืม-คืน ของ User คนนี้ เชื่อมกับตารางหนังสือเพื่อเอาชื่อหนังสือ
$sql = "SELECT br.*, b.book_id, b.book_name, b.author, b.category 
        FROM borrow_records br
        JOIN books b ON br.book_id = b.book_id
        WHERE br.user_id = '$user_id'
        ORDER BY br.borrow_id DESC"; // เอาประวัติล่าสุดขึ้นก่อน
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการยืม-คืน - ระบบยืมคืนหนังสือ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .table-card {
            background-color: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .status-badge {
            font-weight: 600;
            padding: 0.5em 1em;
            border-radius: 30px;
        }
        .btn-borrow-again {
            transition: all 0.2s;
        }
        .btn-borrow-again:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-book-half me-2"></i>LibraryMobile</a>
    <div class="ms-auto">
        <a href="index.php" class="btn btn-outline-light btn-sm fw-bold">กลับหน้าหลัก</a>
    </div>
  </div>
</nav>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2 class="fw-bold text-dark">ประวัติการยืม-คืนหนังสือ</h2>
            <p class="text-muted">ตรวจสอบรายการหนังสือทั้งหมดที่คุณเคยทำรายการ</p>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">ชื่อหนังสือ</th>
                            <th class="py-3">วันที่ยืม</th>
                            <th class="py-3">วันที่คืน</th>
                            <th class="py-3">สถานะ</th>
                            <th class="py-3 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                // จัดการรูปแบบสถานะ
                                if($row['status'] == 'pending') {
                                    $status_label = '<span class="badge bg-warning text-dark status-badge">กำลังยืม</span>';
                                    $return_date = '<span class="text-muted small">- ยังไม่คืน -</span>';
                                    $action_btn = '<a href="return_book.php" class="btn btn-sm btn-success fw-bold px-3 shadow-sm">ไปหน้าคืน</a>';
                                } else {
                                    $status_label = '<span class="badge bg-success status-badge text-white">คืนแล้ว</span>';
                                    $return_date = date('d/m/Y', strtotime($row['return_date']));
                                    
                                    // แก้ไขส่วนนี้: เปลี่ยนจากปุ่ม disabled เป็นปุ่มยืมอีกครั้ง
                                    $action_btn = '<a href="borrow.php?id=' . $row['book_id'] . '" class="btn btn-sm btn-outline-primary fw-bold px-3 btn-borrow-again">
                                                    <i class="bi bi-arrow-repeat me-1"></i>ยืมอีกครั้ง
                                                   </a>';
                                }
                        ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-primary"><?php echo htmlspecialchars($row['book_name']); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($row['author']); ?></div>
                            </td>
                            <td class="py-3 align-middle">
                                <?php echo date('d/m/Y', strtotime($row['borrow_date'])); ?>
                            </td>
                            <td class="py-3 align-middle">
                                <?php echo $return_date; ?>
                            </td>
                            <td class="py-3 align-middle">
                                <?php echo $status_label; ?>
                            </td>
                            <td class="py-3 align-middle text-center">
                                <?php echo $action_btn; ?>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>ยังไม่มีประวัติการทำรายการ</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4 text-center text-muted small">
        <i class="bi bi-info-circle me-1"></i> หากมีข้อสงสัยเกี่ยวกับประวัติการยืม โปรดติดต่อเจ้าหน้าที่ห้องสมุด
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>