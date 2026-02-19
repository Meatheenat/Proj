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
    const username = document.querySelector('#forgot_user').value;
    if(username === "") {
        alert("กรุณากรอก Username");
    } else {
        alert("ระบบได้รับเรื่องแล้ว กรุณารอการติดต่อกลับจาก Admin");
    }
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
}