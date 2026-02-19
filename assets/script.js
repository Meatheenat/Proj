// ตรวจสอบการกรอกข้อมูล (Form Validation)
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            let password = form.querySelector('input[name="password"]');
            
            // ตัวอย่าง: ถ้าเป็นหน้า Register ให้เช็คความยาวรหัสผ่าน
            if (password && password.value.length < 4) {
                alert('รหัสผ่านต้องมีความยาวอย่างน้อย 4 ตัวอักษร');
                event.preventDefault(); // ระงับการส่งฟอร์ม
            }
        });
    });

    // ==========================================
    // ระบบเปิด-ปิดตา (Show/Hide Password)
    // ==========================================
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const inputField = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (inputField.type === 'password') {
                inputField.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                inputField.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    });

    // ==========================================
    // ระบบสลับธีม Dark / Light Mode
    // ==========================================
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlElement = document.documentElement;

    // 1. เช็คธีมที่เคยเก็บไว้ใน LocalStorage
    const currentTheme = localStorage.getItem('theme') || 'light';
    htmlElement.setAttribute('data-bs-theme', currentTheme);
    if(themeIcon) updateIcon(currentTheme);

    // 2. เมื่อกดปุ่มสลับธีม
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            let theme = htmlElement.getAttribute('data-bs-theme');
            let newTheme = (theme === 'light') ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    }

    // ฟังก์ชันเปลี่ยนไอคอน (อยู่ข้างในเพื่อให้เข้าถึง themeIcon ได้)
    function updateIcon(theme) {
        const icon = document.getElementById('themeIcon');
        if(!icon) return;
        if (theme === 'dark') {
            icon.classList.remove('bi-moon-stars-fill');
            icon.classList.add('bi-sun-fill');
            icon.style.color = '#ffc107'; // สีเหลืองทอง
        } else {
            icon.classList.remove('bi-sun-fill');
            icon.classList.add('bi-moon-stars-fill');
            icon.style.color = '#ffffff';
        }
    }
});

// ฟังก์ชันสำหรับหน้าลืมรหัสผ่าน (Simulate) - แยกไว้ข้างนอกตามโครงเดิม
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
}