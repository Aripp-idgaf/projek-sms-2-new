// ====================================================
// SCRIPT NAVIGASI HALAMAN (SINGLE PAGE APPLICATION)
// ====================================================
function switchView(viewId) {
    document.querySelectorAll('.view-section').forEach(el => {
        el.classList.add('d-none');
        el.style.animation = 'none'; 
    });
    
    const targetSection = document.getElementById(viewId);
    targetSection.classList.remove('d-none');
    
    setTimeout(() => {
        targetSection.style.animation = 'fadeInView 0.5s ease-out forwards';
    }, 10);
    
    document.getElementById('main-content-area').scrollTo({ top: 0, behavior: 'smooth' });
}

function kirimJadwalAlert() {
    alert('Jadwal berhasil dikirim! Silakan tunggu konfirmasi Admin.');
    switchView('view-home'); 
}

// ====================================================
// SCRIPT DASHBOARD - WAKTU
// ====================================================
function updateDateTime() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
    let formattedDate = now.toLocaleDateString('id-ID', options).replace(/\./g, ':'); 
    document.getElementById('realtime-datetime').innerText = formattedDate;
}
setInterval(updateDateTime, 1000);
updateDateTime();

function togglePanel() {
    const panel = document.getElementById('rightPanel');
    panel.classList.toggle('closed'); 
}

// ====================================================
// FUNGSI LOGOUT (VERSI PHP)
// ====================================================
function logoutSession() {
    // Mengarahkan tombol keluar ke file proses pemutus session PHP
    window.location.href = "../login/logout.php"; 
}

window.addEventListener('load', () => {
    const hash = window.location.hash.substring(1); 
    if (hash && document.getElementById(hash)) {
        setTimeout(() => {
            switchView(hash);
            history.replaceState(null, null, ' ');
        }, 500); 
    }
});