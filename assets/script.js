/**
 * Library System Main Script
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // === 1. จัดการระบบธีม (Theme Management) ===
    const themeToggle = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;

    // ดึงค่าธีมที่บันทึกไว้ หรือใช้ 'light' เป็นค่าเริ่มต้น
    const savedTheme = localStorage.getItem('theme') || 'light';
    htmlElement.setAttribute('data-bs-theme', savedTheme);
    updateIcon(savedTheme);
    
    console.log("ธีมปัจจุบันคือ: " + savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = (currentTheme === 'light') ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
            
            console.log("เปลี่ยนเป็นธีม: " + newTheme);
        });
    }

    // === 2. ระบบเปิด-ปิดตา (Show/Hide Password) ===
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            // ดึง ID ของ input จาก attribute data-target
            const targetId = this.getAttribute('data-target');
            const inputField = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (inputField) {
                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    // สลับไอคอนจาก ปิดตา เป็น เปิดตา
                    if (icon.classList.contains('bi-eye-slash')) {
                        icon.classList.replace('bi-eye-slash', 'bi-eye');
                    }
                } else {
                    inputField.type = 'password';
                    // สลับไอคอนจาก เปิดตา เป็น ปิดตา
                    if (icon.classList.contains('bi-eye')) {
                        icon.classList.replace('bi-eye', 'bi-eye-slash');
                    }
                }
            }
        });
    });

    // === 3. ระบบตรวจสอบฟอร์ม (Form Validation) ===
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            const password = form.querySelector('input[name="password"]');
            if (password && password.value.length < 4) {
                alert('รหัสผ่านต้องมีความยาวอย่างน้อย 4 ตัวอักษร');
                event.preventDefault(); // ระงับการส่งฟอร์มถ้าไม่ผ่านเงื่อนไข
            }
        });
    });

});

/**
 * ฟังก์ชันอัปเดตไอคอนธีม
 * แยกออกมาเพื่อให้เรียกใช้ได้ทั้งตอนโหลดหน้าเว็บและตอนกดสลับ
 */
function updateIcon(theme) {
    const themeIcon = document.getElementById('themeIcon');
    if (!themeIcon) return;

    if (theme === 'dark') {
        themeIcon.className = 'bi bi-sun-fill'; 
        themeIcon.style.color = '#ffc107'; // สีเหลืองทอง
    } else {
        themeIcon.className = 'bi bi-moon-stars-fill';
        themeIcon.style.color = '#ffffff'; // สีขาว (สำหรับ Navbar เข้ม)
    }
}

/**
 * ฟังก์ชันลืมรหัสผ่าน
 */
function handleForgotPass() {
    const forgotInput = document.querySelector('#forgot_user');
    if (forgotInput) {
        const username = forgotInput.value;
        if (username === "") {
            alert("กรุณากรอก Username");
        } else {
            alert("ระบบได้รับเรื่องแล้ว กรุณารอการติดต่อกลับจาก Admin");
        }
    }
}