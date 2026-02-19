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
});

// ฟังก์ชันสำหรับหน้าลืมรหัสผ่าน (Simulate)
function handleForgotPass() {
    const usernameInput = document.querySelector('#forgot_user');
    if (usernameInput) {
        const username = usernameInput.value;
        if(username === "") {
            alert("กรุณากรอก Username");
        } else {
            alert("ระบบได้รับเรื่องแล้ว กรุณารอการติดต่อกลับจาก Admin");
        }
    }
} // <--- เติมปิดปีกกาตรงนี้เพื่อให้โค้ดด้านล่างทำงานได้

// ==========================================
// ระบบเปิด-ปิดตา (Show/Hide Password)
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            // หาว่าปุ่มนี้กำลังคุม Input ช่องไหนอยู่ (ดึงค่าจาก data-target)
            const targetId = this.getAttribute('data-target');
            const inputField = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            // สลับสถานะระหว่าง password กับ text
            if (inputField.type === 'password') {
                inputField.type = 'text';
                icon.classList.remove('bi-eye-slash'); // เอาตาปิดออก
                icon.classList.add('bi-eye'); // ใส่ตาเปิด
            } else {
                inputField.type = 'password';
                icon.classList.remove('bi-eye'); // เอาตาเปิดออก
                icon.classList.add('bi-eye-slash'); // ใส่ตาปิด
            }
        });
    });
});

// ==========================================
// ระบบสลับธีม Dark / Light Mode
// ==========================================
// ใส่ไว้ใน DOMContentLoaded เพื่อให้หาปุ่ม themeToggle เจอแน่นอน
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlElement = document.documentElement;

    // 1. เช็คธีมที่เคยเก็บไว้ใน LocalStorage
    const currentTheme = localStorage.getItem('theme') || 'light';
    htmlElement.setAttribute('data-bs-theme', currentTheme);
    if (themeIcon) updateIcon(currentTheme, themeIcon);

    // 2. เมื่อกดปุ่มสลับธีม
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            let theme = htmlElement.getAttribute('data-bs-theme');
            let newTheme = (theme === 'light') ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme); // บันทึกค่าลงเครื่อง
            if (themeIcon) updateIcon(newTheme, themeIcon);
        });
    }
});

// ฟังก์ชันเปลี่ยนไอคอน
function updateIcon(theme, themeIcon) {
    if (theme === 'dark') {
        themeIcon.classList.remove('bi-moon-stars-fill');
        themeIcon.classList.add('bi-sun-fill');
        themeIcon.style.color = '#ffc107'; // สีเหลืองทอง
    } else {
        themeIcon.classList.remove('bi-sun-fill');
        themeIcon.classList.add('bi-moon-stars-fill');
        themeIcon.style.color = '#ffffff';
    }
}