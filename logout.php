<?php
session_start();
session_unset(); // ลบข้อมูล Session ทั้งหมด
session_destroy(); // ทำลาย Session
header("Location: login.php"); // เด้งกลับไปหน้าเข้าสู่ระบบ
exit();
?>