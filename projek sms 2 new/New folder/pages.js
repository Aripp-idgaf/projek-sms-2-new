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
        authBtn.href = "./pasien/dashboard.pasien.html"; 
        authBtn.classList.add('logged-in'); 
    } else {
        authBtn.innerHTML = "Login / Register";
        authBtn.href = "./login/index.html";
        authBtn.classList.remove('logged-in');
    }
}

// 2. Fungsi Akses Layanan 
function aksesLayanan(menuId) {
    const token = localStorage.getItem('mediflow_token');
    const role = localStorage.getItem('mediflow_role');
    
    if (token && role === 'pasien') {
        window.location.href = `./pasien/dashboard.pasien.html#${menuId}`;
    } else {
        window.location.href = "./login/index.html";
    }
}

// 3. ANIMASI SAAT SCROLL (SANGAT PENTING UNTUK EFEK TEKS TERBELAH LAMBAT)
const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.15 
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
        }
    });
}, observerOptions);

const hiddenElements = document.querySelectorAll('.reveal-up');
hiddenElements.forEach((el) => {
    observer.observe(el);
});