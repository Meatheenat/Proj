document.addEventListener('DOMContentLoaded', function() {
    // 1. จัดการระบบธีม (Theme Management)
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlElement = document.documentElement;

    // ดึงค่าจาก LocalStorage
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

    // 2. ระบบเปิด-ปิดตา (Show/Hide Password)
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

    // 3. Form Validation
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

// ฟังก์ชันอัปเดตไอคอน (แยกออกมาข้างนอก)
function updateIcon(theme) {
    const themeIcon = document.getElementById('themeIcon');
    if (!themeIcon) return;

    if (theme === 'dark') {
        themeIcon.className = 'bi bi-sun-fill'; // ใช้ className เพื่อความชัวร์
        themeIcon.style.color = '#ffc107';
    } else {
        themeIcon.className = 'bi bi-moon-stars-fill';
        themeIcon.style.color = '#ffffff';
    }
}

// ฟังก์ชันลืมรหัสผ่าน
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