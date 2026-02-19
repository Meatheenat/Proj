document.addEventListener('DOMContentLoaded', function() {
    
    // 1. ระบบสลับธีม Dark / Light Mode (แก้ไขบั๊ก)
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlElement = document.documentElement;

    // เช็คธีมที่เคยเก็บไว้ใน LocalStorage
    const currentTheme = localStorage.getItem('theme') || 'light';
    htmlElement.setAttribute('data-bs-theme', currentTheme);
    updateIcon(currentTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            let theme = htmlElement.getAttribute('data-bs-theme');
            let newTheme = (theme === 'light') ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    }

    function updateIcon(theme) {
        if (!themeIcon) return;
        if (theme === 'dark') {
            themeIcon.className = 'bi bi-sun-fill';
            themeIcon.style.color = '#ffc107'; // สีเหลืองทอง
        } else {
            themeIcon.className = 'bi bi-moon-stars-fill';
            themeIcon.style.color = '#ffffff';
        }
    }

    // 2. ระบบเปิด-ปิดตา (Show/Hide Password)
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const inputField = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (inputField.type === 'password') {
                inputField.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                inputField.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    });

    // 3. ตรวจสอบการกรอกข้อมูล (Form Validation)
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            let password = form.querySelector('input[name="password"]');
            if (password && password.value.length < 4) {
                alert('รหัสผ่านต้องมีความยาวอย่างน้อย 4 ตัวอักษร');
                event.preventDefault();
            }
        });
    });
});

// 4. ฟังก์ชันสำหรับหน้าลืมรหัสผ่าน (อยู่นอก DOMContentLoaded เพื่อให้ HTML เรียกใช้ได้)
function handleForgotPass() {
    const usernameInput = document.querySelector('#forgot_user');
    if(!usernameInput) return;
    
    const username = usernameInput.value;
    if(username === "") {
        alert("กรุณากรอก Username");
    } else {
        alert("ระบบได้รับเรื่องแล้ว กรุณารอการติดต่อกลับจาก Admin");
    }
}