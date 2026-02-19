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
    const forgotInput = document.querySelector('#forgot_user');
    if(forgotInput) {
        const username = forgotInput.value;
        if(username === "") {
            alert("กรุณากรอก Username");
        } else {
            alert("ระบบได้รับเรื่องแล้ว กรุณารอการติดต่อกลับจาก Admin");
        }
    }
} // <--- เติมปิดปีกกาตรงนี้ เพื่อให้โค้ดระบบสลับธีมข้างล่างทำงานได้!!

// ==========================================
// ระบบเปิด-ปิดตา (Show/Hide Password)
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
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
});

// ==========================================
// ระบบสลับธีม Dark / Light Mode
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlElement = document.documentElement;

    const currentTheme = localStorage.getItem('theme') || 'light';
    htmlElement.setAttribute('data-bs-theme', currentTheme);
    if (themeIcon) updateIcon(currentTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            let theme = htmlElement.getAttribute('data-bs-theme');
            let newTheme = (theme === 'light') ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    }
});

function updateIcon(theme) {
    const themeIcon = document.getElementById('themeIcon');
    if (!themeIcon) return;
    if (theme === 'dark') {
        themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
        themeIcon.style.color = '#ffc107';
    } else {
        themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
        themeIcon.style.color = '#ffffff';
    }
}