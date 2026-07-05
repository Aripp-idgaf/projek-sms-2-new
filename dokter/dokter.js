let currentQueueIdx = 0;

document.addEventListener('DOMContentLoaded', function() {
    renderQueue();
    
    // ==========================================
    // FUNGSI TOGGLE MATA UNTUK PASSWORD
    // ==========================================
    const togglePwd = document.getElementById('toggleSettingsPassword');
    if(togglePwd) {
        togglePwd.addEventListener('click', function() {
            const pwdInput = document.getElementById('settingsPasswordInput');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                this.classList.remove('bi-eye-slash');
                this.classList.add('bi-eye');
                this.style.color = '#38c8e6'; // Warna aktif (teal)
            } else {
                pwdInput.type = 'password';
                this.classList.remove('bi-eye');
                this.classList.add('bi-eye-slash');
                this.style.color = '#a0b8c2'; // Warna non-aktif (abu-abu)
            }
        });
    }
});

function updateDateTime() {
    const realtimeElement = document.getElementById('realtime-datetime');
    if(realtimeElement) {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        realtimeElement.innerText = now.toLocaleDateString('id-ID', options).replace(/\./g, ':');
    }
}
setInterval(updateDateTime, 1000);
updateDateTime();

function switchView(viewId) {
    document.querySelectorAll('.view-section').forEach(el => el.classList.add('d-none'));
    const targetSection = document.getElementById(viewId);
    if (targetSection) targetSection.classList.remove('d-none');
}

function renderQueue() {
    const container = document.getElementById('queue-data-container');
    const counter = document.getElementById('queue-counter');
    const wrapper = document.getElementById('queue-card-wrapper');
    const badge = document.getElementById('queue-badge');
    
    // ======== JIKA ANTREAN KOSONG ========
    if (!antreanData || antreanData.length === 0) {
        wrapper.style.borderColor = 'transparent';
        wrapper.classList.remove('border-start'); 
        badge.style.display = 'none';

        container.innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center text-center w-100" style="border: 2px dashed #d1e5e5; border-radius: 12px; padding: 25px 15px; background-color: #fafbfc; min-height: 140px;">
                <i class="bi bi-calendar-x text-muted mb-2" style="font-size: 2.2rem; opacity: 0.5;"></i>
                <h6 class="fw-bold text-dark mb-2">Belum Ada Antrean Hari Ini</h6>
                <p class="text-muted small mb-0" style="max-width: 90%;">Anda belum memiliki antrean pasien. Pasien yang mendaftar poli Anda akan muncul di sini.</p>
            </div>
        `;
        counter.innerText = '0/0';
        return;
    }

    // ======== JIKA ADA ANTREAN ========
    wrapper.style.borderColor = '#f39c12';
    wrapper.classList.add('border-start');
    badge.style.display = 'block';

    const data = antreanData[currentQueueIdx];
    container.innerHTML = `
        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-person-bounding-box text-teal-mediflow me-2"></i>Pasien Berikutnya</h6>
        <hr class="my-2" style="opacity:0.1">
        <div class="row g-2 mt-1 small">
            <div class="col-6"><span class="text-muted d-block" style="font-size:0.7rem;">Nama Pasien</span><span class="fw-bold text-dark">${data.nama_pasien}</span></div>
            <div class="col-6"><span class="text-muted d-block" style="font-size:0.7rem;">No. RM</span><span class="fw-bold text-dark">${data.no_rm}</span></div>
            <div class="col-6 mt-3"><span class="text-muted d-block" style="font-size:0.7rem;">Tanggal & Jam</span><span class="fw-bold text-danger">${data.tanggal} • ${data.waktu}</span></div>
            <div class="col-6 mt-3"><span class="text-muted d-block" style="font-size:0.7rem;">Keluhan Utama</span><span class="fw-bold text-dark text-truncate d-block" title="${data.keluhan}">${data.keluhan}</span></div>
        </div>
    `;
    counter.innerText = (currentQueueIdx + 1) + '/' + antreanData.length;
}

function nextQueue() {
    if (antreanData && antreanData.length > 0) {
        currentQueueIdx = (currentQueueIdx + 1) % antreanData.length;
        renderQueue();
    }
}

function prevQueue() {
    if (antreanData && antreanData.length > 0) {
        currentQueueIdx = (currentQueueIdx - 1 + antreanData.length) % antreanData.length;
        renderQueue();
    }
}