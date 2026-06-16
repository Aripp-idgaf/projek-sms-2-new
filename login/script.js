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

// ==========================================
// SLIDER LOGIKA (ANIMASI GESER INFINITE LOOP)
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