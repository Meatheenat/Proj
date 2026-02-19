<?php
// เปิดโหมดดู Error ตามสไตล์ IT Support
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('config/db.php');

// 1. ตรวจสอบสิทธิ์แอดมิน (ป้องกันการยิง URL เล่น)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("คุณไม่มีสิทธิ์เข้าถึงส่วนนี้");
}

// 2. รับค่า ID ที่ส่งมาจาก URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $borrow_id = mysqli_real_escape_string($conn, $_GET['id']);

    // --- เริ่มกระบวนการ Transaction ---
    // ค้นหา book_id จากรายการยืมนี้ก่อนเพื่อนำไปอัปเดตสถานะหนังสือ
    $sql_find = "SELECT book_id FROM borrow_records WHERE borrow_id = '$borrow_id' LIMIT 1";
    $result_find = mysqli_query($conn, $sql_find);
    $record = mysqli_fetch_assoc($result_find);

    if ($record) {
        $book_id = $record['book_id'];
        $today = date('Y-m-d H:i:s');

        // A. อัปเดตตาราง borrow_records: ตั้งสถานะเป็น returned และใส่วันที่คืน
        $sql_update_record = "UPDATE borrow_records SET 
                              status = 'returned', 
                              return_date = '$today' 
                              WHERE borrow_id = '$borrow_id'";

        // B. อัปเดตตาราง books: คืนสถานะหนังสือให้ว่าง (available)
        $sql_update_book = "UPDATE books SET status = 'available' WHERE book_id = '$book_id'";

        // รัน Query ทั้งคู่
        if (mysqli_query($conn, $sql_update_record) && mysqli_query($conn, $sql_update_book)) {
            // สำเร็จ! ส่งกลับหน้า Dashboard พร้อมแจ้งเตือน
            echo "<script>
                    alert('คืนหนังสือเรียบร้อยแล้ว!');
                    window.location.href = 'admin_dashboard.php';
                  </script>";
        } else {
            // กรณี Query พัง
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('ไม่พบข้อมูลรายการยืมนี้'); window.location.href = 'admin_dashboard.php';</script>";
    }
} else {
    header("Location: admin_dashboard.php");
}
?>