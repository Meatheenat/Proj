document.addEventListener('DOMContentLoaded', function() {
    // === 1. จัดการระบบธีม (Theme Management) ===
    const themeToggle = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;

    // ดึงค่าจาก LocalStorage หรือตั้งเป็น light ถ้าไม่มีค่า
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
            const targetId = this.getAttribute('data-target');
            const inputField = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (inputField && inputField.type === 'password') {
                inputField.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else if (inputField) {
                inputField.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
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
                event.preventDefault();
            }
        });
    });
});

/**
 * ฟังก์ชันอัปเดตไอคอนธีม (อยู่นอก DOMContentLoaded เพื่อให้เรียกใช้ได้ทั่วถึง)
 */
function updateIcon(theme) {
    const themeIcon = document.getElementById('themeIcon');
    if (!themeIcon) return;

    if (theme === 'dark') {
        themeIcon.className = 'bi bi-sun-fill'; 
        themeIcon.style.color = '#ffc107'; // สีเหลืองทองเมื่อเป็น Dark Mode
    } else {
        themeIcon.className = 'bi bi-moon-stars-fill';
        themeIcon.style.color = '#ffffff'; // สีขาวเมื่อเป็น Light Mode (บน Navbar ดำ)
    }
}

/**
 * ฟังก์ชันลืมรหัสผ่าน
 */
function handleForgotPass() {
    const forgotInput = document.querySelector('#forgot_user');
    if(forgotInput) {
        const username = forgotInput.value;
        if(username === "") {
            alert("กรุณากรอก Username");
        } else {
            alert("ระบบได้รับเรื่องแล้ว กรุณารอการติดต่อกลับจาก Admin");
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;

    // 1. โหลดธีมที่บันทึกไว้
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-bs-theme', savedTheme);
    updateIcon(savedTheme);

    // 2. ระบบสลับธีม
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-bs-theme');
            const newTheme = (currentTheme === 'light') ? 'dark' : 'light';
            
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    }

    // 3. ระบบลูกตาเปิด-ปิดรหัสผ่าน
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const target = document.getElementById(this.getAttribute('data-target'));
            const icon = this.querySelector('i');
            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                target.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    });
});

function updateIcon(theme) {
    const icon = document.getElementById('themeIcon');
    if (!icon) return;
    if (theme === 'dark') {
        icon.className = 'bi bi-sun-fill text-warning';
    } else {
        icon.className = 'bi bi-moon-stars-fill text-white';
    }
}
}