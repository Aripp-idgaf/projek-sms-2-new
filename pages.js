// 1. Cek Autentikasi Saat Landing Page Dimuat
document.addEventListener('DOMContentLoaded', () => {
    checkAuthStatus();
    initDoctorAccordion(); // Panggil fungsi akordeon dokter yang baru ditambahkan
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
        authBtn.href = "./login/index.php";
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
        window.location.href = "./login/index.php";
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

// 4. ANIMASI AKORDEON PROFIL DOKTER (BARU DITAMBAHKAN)
function initDoctorAccordion() {
    const accItems = document.querySelectorAll('.accordion-item');
    const docDisplayImg = document.getElementById('doc-display-img');
    const docBadgeText = document.getElementById('doc-badge-text');

    accItems.forEach(item => {
        item.addEventListener('click', () => {
            // Jika diklik item yang sudah terbuka, abaikan saja
            if(item.classList.contains('active')) return;

            // 1. Tutup semua akordeon yang lain (ubah ikon jadi plus)
            accItems.forEach(el => {
                el.classList.remove('active');
                const icon = el.querySelector('.accordion-header i');
                icon.classList.remove('bi-dash');
                icon.classList.add('bi-plus');
            });

            // 2. Buka akordeon yang baru diklik (ubah ikon jadi minus)
            item.classList.add('active');
            const clickedIcon = item.querySelector('.accordion-header i');
            clickedIcon.classList.remove('bi-plus');
            clickedIcon.classList.add('bi-dash');

            // 3. Dapatkan data gambar dan label spesialis dari atribut html
            const newImgSrc = item.getAttribute('data-img');
            const newBadgeTxt = item.getAttribute('data-badge');

            // 4. Lakukan animasi memudarkan gambar (fade effect)
            docDisplayImg.style.opacity = '0';
            
            // Tunggu 300ms (sampai gambar memudar), lalu ganti src gambarnya dan munculkan lagi
            setTimeout(() => {
                docDisplayImg.src = newImgSrc;
                docBadgeText.innerHTML = `<i class="bi bi-check-circle"></i> ${newBadgeTxt}`;
                docDisplayImg.style.opacity = '1';
            }, 300);
        });
    });
}