// 1. Cek Autentikasi Saat Landing Page Dimuat
document.addEventListener('DOMContentLoaded', () => {
    checkAuthStatus();
});

function checkAuthStatus() {
    const token = localStorage.getItem('mediflow_token');
    const role = localStorage.getItem('mediflow_role');
    const authBtn = document.getElementById('navAuthBtn');

    if (token && role === 'pasien') {
        const userName = localStorage.getItem('mediflow_name') || 'Pasien';
        const initial = userName.charAt(0).toUpperCase();

        authBtn.innerHTML = `
            <div class="btn-profile-img">${initial}</div> 
            ${userName}
        `;
        authBtn.href = "login/pasien/dashboard.pasien.html"; 
        authBtn.classList.add('logged-in'); 
    } else {
        authBtn.innerHTML = "Login / Register";
        authBtn.href = "login/index.html";
        authBtn.classList.remove('logged-in');
    }
}

// 2. Fungsi Akses Layanan 
function aksesLayanan(menuId) {
    const token = localStorage.getItem('mediflow_token');
    const role = localStorage.getItem('mediflow_role');
    
    if (token && role === 'pasien') {
        window.location.href = `login/pasien/dashboard.pasien.html#${menuId}`;
    } else {
        window.location.href = "login/index.html";
    }
}

// 3. ANIMASI SAAT SCROLL
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('show-scroll');
        }
    });
}, { threshold: 0.15 });

const hiddenElements = document.querySelectorAll('.hidden-scroll');
hiddenElements.forEach((el) => {
    observer.observe(el);
});