<?php
session_start();
session_unset(); // ล้างตัวแปร Session ทั้งหมด
session_destroy(); // ทำลาย Session
header("Location: login.php"); // เด้งกลับไปหน้า Login
exit();
?>