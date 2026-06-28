// ==========================================
// TOGGLE FORM LOGIN & REGISTER
// ==========================================
function toggleForms(formType) {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const formContainer = document.getElementById('form-container');

    formContainer.style.opacity = 0;
    setTimeout(() => {
        if (formType === 'register') {
            loginForm.classList.add('d-none');
            registerForm.classList.remove('d-none');
        } else {
            registerForm.classList.add('d-none');
            loginForm.classList.remove('d-none');
        }
        formContainer.style.opacity = 1;
    }, 300);
}

document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // LOGIKA DROPDOWN GOLONGAN DARAH
    // ==========================================
    document.querySelectorAll('.custom-dd-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const menu = this.closest('.dropdown-menu');
            const displayArea = menu.previousElementSibling;

            menu.querySelectorAll('.custom-dd-item').forEach(el => el.classList.remove('active'));
            this.classList.add('active');

            const selectedText = this.innerText.trim();
            displayArea.innerText = selectedText;
            displayArea.style.color = '#1e2f3a';

            const container = this.closest('.dropdown-custom-container');
            if (container) {
                const hiddenSelect = container.querySelector('select');
                if (hiddenSelect) {
                    hiddenSelect.value = this.getAttribute('data-value');
                    hiddenSelect.dispatchEvent(new Event('change'));
                }
            }
        });
    });

    // ==========================================
    // LOGIKA TOGGLE PASSWORD (MATA)
    // ==========================================
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.remove('bi-eye-slash');
                this.classList.add('bi-eye');
                this.style.color = '#38c8e6';
            } else {
                passwordInput.type = 'password';
                this.classList.remove('bi-eye');
                this.classList.add('bi-eye-slash');
                this.style.color = '#a0b8c2';
            }
        });
    });

    // ==========================================
    // SLIDER LOGIKA (ANIMASI GESER INFINITE)
    // ==========================================
    const sliderTrack = document.getElementById('slider-track');
    if (sliderTrack) {
        const images = [
            "../wallpaper/rs1.jpg",
            "../wallpaper/rs2.jpg",
            "../wallpaper/rs3.jpg",
            "../wallpaper/rs4.jpg",
            "../wallpaper/rs5.png"
        ];

        images.forEach(src => {
            let img = document.createElement('img');
            img.src = src;
            sliderTrack.appendChild(img);
        });

        setInterval(() => {
            sliderTrack.style.transition = "transform 1.5s ease-in-out";
            sliderTrack.style.transform = `translateX(-100%)`;

            setTimeout(() => {
                sliderTrack.style.transition = "none";
                sliderTrack.appendChild(sliderTrack.firstElementChild);
                sliderTrack.style.transform = "translateX(0)";
            }, 1500);
        }, 10000);
    }
});