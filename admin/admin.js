// === LOGIKA TIMING CLOCK & BANNER DATE ===
function updateDate() {
    const today = new Date();
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const formattedDate = `${today.getDate()} ${months[today.getMonth()]} ${today.getFullYear()}`;
    const bannerBadge = document.getElementById('dynamic-banner-date');
    if (bannerBadge) bannerBadge.innerHTML = `<i class="bi bi-calendar-check me-2"></i>${formattedDate}`;
    const locationDate = document.getElementById('dynamic-banner-location-date');
    if (locationDate) {
        let sesi = 'Sesi Pagi'; const hour = today.getHours();
        if (hour >= 11 && hour < 15) sesi = 'Sesi Siang'; else if (hour >= 15 && hour < 18) sesi = 'Sesi Sore'; else if (hour >= 18) sesi = 'Sesi Malam';
        locationDate.innerHTML = `<i class="bi bi-cloud-sun fs-5"></i> Semarang, ${formattedDate} | ${sesi}`;
    }
}
function updateClock() {
    const now = new Date(); 
    const clockElement = document.getElementById('realtime-clock');
    if(clockElement) {
        clockElement.textContent = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0') + ' WIB';
    }
}
setInterval(updateClock, 1000);

// === EFEK KARAKTER ===
function playDoctorWelcomeEffect() {
    const doctorImg = document.getElementById('doctor-image');
    if (!doctorImg) return;
    doctorImg.classList.remove('welcome-effect');
    void doctorImg.offsetWidth; 
    doctorImg.classList.add('welcome-effect');
    setTimeout(() => {
        doctorImg.classList.remove('welcome-effect');
    }, 600);
}

function playBubbleEffect() {
    const bubble = document.getElementById('welcome-bubble');
    if (!bubble) return;
    bubble.classList.remove('bubble-pop');
    void bubble.offsetWidth; 
    bubble.classList.add('bubble-pop');
    setTimeout(() => {
        bubble.classList.remove('bubble-pop');
    }, 1000);
}

// === LOGIKA NAVIGASI SPA ===
function switchView(viewId) {
    document.querySelectorAll('.view-section').forEach(view => view.classList.add('d-none'));
    document.getElementById(viewId).classList.remove('d-none');
    
    if(viewId === 'view-home') {
        updateStatistikDashboard();
        const docImg = document.querySelector('.wel-doctor-img');
        if(docImg) {
            docImg.style.animation = 'none'; docImg.offsetHeight; 
            docImg.style.animation = 'zoomInDoctorImg 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards';
        }
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function logout() { 
        // Sesuaikan dengan file logout PHP kamu nanti
        window.location.href = '../login/logout.php';
}

// === LOCAL STORAGE DATABASE SYSTEMS ===
let dataDokterBawaan = [
    { kode: "DK-09211", nama: "Dr. Goyounjung, Sp.KK", spesialis: "Spesialis Kulit", ruangan: "Poli Kulit (R.12)", hari: "Senin - Jumat", jam: "08:00 - 15:00", status: "Aktif" },
    { kode: "DK-09212", nama: "Dr. Andi Saputra, Sp.JP", spesialis: "Spesialis Jantung", ruangan: "Poli Jantung (R.05)", hari: "Senin - Kamis", jam: "09:00 - 14:00", status: "Aktif" }
];

let dataPasienBawaan = [
    { rm: "RM-882910", nama: "Ashley Black", darah: "A+", layanan: "BPJS", kategori: "Anak", dokterKode: "DK-09211" },
    { rm: "RM-882911", nama: "John Black", darah: "O+", layanan: "Umum", kategori: "Dewasa", dokterKode: "DK-09212" },
    { rm: "RM-882912", nama: "Jane Black", darah: "B+", layanan: "BPJS", kategori: "Lansia", dokterKode: "DK-09211" }
];

let dataBookingBawaan = [
    { kode: "BK-77201", pasienRm: "RM-882910", kelas: "President Suite", nomor: "Kamar 501", checkIn: "2026-05-18", checkOut: "2026-05-22", status: "Checked-In", keterangan: "Observasi luka bakar pasca-bedah." },
    { kode: "BK-77202", pasienRm: "RM-882912", kelas: "Kelas I", nomor: "Kamar 204", checkIn: "2026-05-20", checkOut: "2026-05-25", status: "Waiting", keterangan: "Rawat rujukan sekunder Poli Jantung." }
];

let dataDokter = JSON.parse(localStorage.getItem('dataDokterMediFlow')) || dataDokterBawaan;
let dataPasien = JSON.parse(localStorage.getItem('dataPasienMediFlow')) || dataPasienBawaan;
let dataBooking = JSON.parse(localStorage.getItem('dataBookingMediFlow')) || dataBookingBawaan;

let indexPasienAkanDihapus = null; let indexPasienAkanDiedit = null;
let indexDokterAkanHapus = null;   let indexDokterAkanDiedit = null;
let indexBookingAkanHapus = null;  let indexBookingAkanDiedit = null;

function isiDropdownDokterPilihan(selectId, selectedKode = '') {
    const selectEl = document.getElementById(selectId); if(!selectEl) return; selectEl.innerHTML = '';
    let dokterTersedia = dataDokter.filter(d => d.status === 'Aktif');
    if(dokterTersedia.length === 0) { selectEl.innerHTML = '<option value="">Tidak ada dokter aktif tersedia</option>'; return; }
    dokterTersedia.forEach(d => {
        let selectedAttr = d.kode === selectedKode ? 'selected' : '';
        selectEl.innerHTML += `<option value="${d.kode}" ${selectedAttr}>${d.nama} (${d.spesialis})</option>`;
    });
}

function filterKelasKamarBerdasarLayanan(pasienSelectId, kelasSelectId) {
    const pasienRm = document.getElementById(pasienSelectId).value; const kelasSelect = document.getElementById(kelasSelectId);
    if(!pasienRm || !kelasSelect) return; const pasienObj = dataPasien.find(p => p.rm === pasienRm); if(!pasienObj) return;
    kelasSelect.innerHTML = pasienObj.layanan === 'BPJS' ? `<option value="Kelas I">Kelas I (Fasilitas BPJS)</option><option value="Kelas II">Kelas II (Fasilitas BPJS)</option><option value="Kelas III">Kelas III (Fasilitas BPJS)</option>` : `<option value="President Suite">President Suite (Fasilitas Umum)</option><option value="VIP">VIP (Fasilitas Umum)</option><option value="Kelas I">Kelas I (Fasilitas Umum)</option><option value="Kelas II">Kelas II (Fasilitas Umum)</option><option value="Kelas III">Kelas III (Fasilitas Umum)</option>`;
}

function isiDropdownPasienBooking() {
    const selectEl = document.getElementById('inputPasienBooking'); if(!selectEl) return; selectEl.innerHTML = '';
    if(dataPasien.length === 0) { selectEl.innerHTML = '<option value="">Tidak ada data pasien</option>'; return; }
    dataPasien.forEach(p => { selectEl.innerHTML += `<option value="${p.rm}">${p.nama} (${p.rm}) - [${p.layanan}]</option>`; });
    filterKelasKamarBerdasarLayanan('inputPasienBooking', 'inputKelasKamar');
}

function updateStatistikDashboard() {
    renderAdminCharts();
    if(document.getElementById('rswnTotalPasien')) document.getElementById('rswnTotalPasien').innerText = `${dataPasien.length} Pasien`;
    if(document.getElementById('rswnTotalDokter')) document.getElementById('rswnTotalDokter').innerText = `${dataDokter.filter(d=>d.status==='Aktif').length} Aktif Dokter`;
    if(document.getElementById('rswnTotalBooking')) document.getElementById('rswnTotalBooking').innerText = `${dataBooking.length} Reservasi`;

    let vipInap = dataBooking.filter(b => (b.kelas === 'President Suite' || b.kelas === 'VIP') && b.status === 'Checked-In').length;
    let k1Inap = dataBooking.filter(b => b.kelas === 'Kelas I' && b.status === 'Checked-In').length;
    let k2Inap = dataBooking.filter(b => b.kelas === 'Kelas II' && b.status === 'Checked-In').length;
    let k3Inap = dataBooking.filter(b => b.kelas === 'Kelas III' && b.status === 'Checked-In').length;

    if(document.getElementById('bedVipDash')) document.getElementById('bedVipDash').innerText = `${vipInap + 4} / 15 Bed`;
    if(document.getElementById('bedKelas1Dash')) document.getElementById('bedKelas1Dash').innerText = `${k1Inap + 18} / 40 Bed`;
    if(document.getElementById('bedKelas2Dash')) document.getElementById('bedKelas2Dash').innerText = `${k2Inap + 32} / 50 Bed`;
    if(document.getElementById('bedKelas3Dash')) document.getElementById('bedKelas3Dash').innerText = `${k3Inap + 54} / 80 Bed`;
}

// === 2. MANAGEMENT DATA PASIEN ===
function renderTabelPasien() {
    const tbody = document.getElementById('tabelPasienBody'); if (!tbody) return; tbody.innerHTML = ''; 
    dataPasien.forEach((pasien, index) => {
        let badgeLayanan = pasien.layanan === 'BPJS' ? 'st-bpjs' : 'st-umum';
        let dokterObj = dataDokter.find(d => d.kode === pasien.dokterKode);
        let namaDokterPemeriksa = dokterObj ? dokterObj.nama : '<span class="text-danger">Belum Dirujuk</span>';

        let rowHTML = `
            <tr>
                <td class="fw-bold text-primary">${pasien.rm}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=${pasien.nama.replace(/ /g, '+')}&background=eef5f5&color=38c8e6" class="rounded-circle me-3 border border-2 border-white shadow-sm" width="45"> 
                        <div><span class="fw-bold d-block text-dark mb-1">${pasien.nama}</span><span class="badge bg-light text-dark border" style="font-size: 0.65rem;"><i class="bi bi-people text-info me-1"></i>${pasien.kategori}</span></div>
                    </div>
                </td>
                <td class="fw-semibold text-dark"><i class="bi bi-heart-pulse text-primary me-2"></i>${namaDokterPemeriksa}</td>
                <td><span class="badge-status ${badgeLayanan}">${pasien.layanan}</span></td>
                <td class="fw-bold text-danger"><i class="bi bi-droplet-half me-1"></i> ${pasien.darah}</td>
                <td class="text-center">
                    <div class="dropdown dropdown-action mx-auto" style="width:max-content;">
                        <button class="btn btn-sm rounded-circle text-muted" type="button" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item text-dark" href="#" onclick="bukaModalEditPasien(${index})"><i class="bi bi-pencil-square text-primary me-2"></i>Ubah Rujukan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="konfirmasiHapusPasien(${index})"><i class="bi bi-trash3 text-danger me-2"></i>Hapus Pasien</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        `;
        tbody.innerHTML += rowHTML;
    });
    updateStatistikDashboard();
}

function simpanPasienBaru() {
    let namaVal = document.getElementById('inputNamaPasien').value; let dokterKodeVal = document.getElementById('inputDokterPasien').value;
    if(namaVal.trim() === '' || !dokterKodeVal) { alert('Mohon lengkapi seluruh form pendaftaran!'); return; }
    let randomRM = "RM-" + Math.floor(100000 + Math.random() * 900000);
    dataPasien.push({ rm: randomRM, nama: namaVal, darah: document.getElementById('inputGolDarah').value, layanan: document.getElementById('inputJenisLayanan').value, kategori: document.getElementById('inputKategoriUsia').value, dokterKode: dokterKodeVal });
    localStorage.setItem('dataPasienMediFlow', JSON.stringify(dataPasien));
    renderTabelPasien(); renderTabelDokter(); document.getElementById('inputNamaPasien').value = '';
    bootstrap.Modal.getInstance(document.getElementById('modalTambahPasien')).hide();
}

function bukaModalEditPasien(index) {
    indexPasienAkanDiedit = index; let p = dataPasien[index]; document.getElementById('editNamaPasien').value = p.nama;
    isiDropdownDokterPilihan('editDokterPasien', p.dokterKode); new bootstrap.Modal(document.getElementById('modalEditPasien')).show();
}

function simpanPerubahanPasien() {
    let dokterBaruKode = document.getElementById('editDokterPasien').value;
    if(!dokterBaruKode) { alert('Dokter rujukan tidak boleh kosong!'); return; }
    dataPasien[indexPasienAkanDiedit].dokterKode = dokterBaruKode;
    localStorage.setItem('dataPasienMediFlow', JSON.stringify(dataPasien));
    renderTabelPasien(); renderTabelDokter(); renderTabelBooking();
    bootstrap.Modal.getInstance(document.getElementById('modalEditPasien')).hide();
}

function konfirmasiHapusPasien(index) { indexPasienAkanHapus = index; document.getElementById('namaHapus').innerText = dataPasien[index].nama; new bootstrap.Modal(document.getElementById('modalHapusPasien')).show(); }
function eksekusiHapus() {
    let targetRm = dataPasien[indexPasienAkanHapus].rm; dataBooking = dataBooking.filter(b => b.pasienRm !== targetRm);
    localStorage.setItem('dataBookingMediFlow', JSON.stringify(dataBooking));
    dataPasien.splice(indexPasienAkanHapus, 1); localStorage.setItem('dataPasienMediFlow', JSON.stringify(dataPasien));
    renderTabelPasien(); renderTabelDokter(); renderTabelBooking(); indexPasienAkanHapus = null;
    bootstrap.Modal.getInstance(document.getElementById('modalHapusPasien')).hide();
}

// === 3. MANAGEMENT DATA DOKTER ===
function renderTabelDokter() {
    const tbody = document.getElementById('tabelDokterBody'); if (!tbody) return; tbody.innerHTML = ''; 
    dataDokter.forEach((dokter, index) => {
        let badgeStatus = dokter.status === 'Aktif' ? 'st-aktif' : 'st-cuti';
        let displayJam = dokter.status === 'Cuti' ? '-' : (dokter.jam.includes('WIB') ? dokter.jam : dokter.jam + ' WIB');
        let displayRuangan = dokter.status === 'Cuti' ? '-' : dokter.ruangan;

        let assignedPatients = dataPasien.filter(p => p.dokterKode === dokter.kode);
        let pasienListHTML = '';
        if(assignedPatients.length > 0 && dokter.status === 'Aktif') {
            assignedPatients.forEach(p => {
                let statusRawat = p.layanan === 'BPJS' ? 'Rawat Jalan (BPJS)' : 'Konsultasi Umum';
                let b = p.layanan === 'BPJS' ? 'bg-success' : 'bg-warning text-dark';
                pasienListHTML += `<tr>
                    <td class="fw-bold text-dark"><i class="bi bi-person-fill text-muted me-2"></i>${p.nama} <span class="text-muted fw-normal">(${p.rm})</span></td>
                    <td class="text-muted">Pemeriksaan Kategori ${p.kategori}</td>
                    <td><span class="badge ${b} rounded-pill">${statusRawat}</span></td>
                </tr>`;
            });
        } else if (dokter.status === 'Cuti') {
            pasienListHTML = `<tr><td colspan="3" class="text-center text-muted py-3"><i class="bi bi-calendar-x fs-4 d-block mb-1"></i>Dokter sedang cuti, tidak ada pasien terjadwal.</td></tr>`;
        } else {
            pasienListHTML = `<tr><td colspan="3" class="text-center text-muted py-3"><i class="bi bi-inbox fs-4 d-block mb-1"></i>Belum ada pasien yang dirujuk ke dokter ini.</td></tr>`;
        }

        let rowHTML = `
            <tr>
                <td class="fw-bold text-primary">${dokter.kode}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=${dokter.nama.replace(/ /g, '+')}&background=eef5f5&color=38c8e6" class="rounded-circle me-3 border border-2 border-white shadow-sm" width="45"> 
                        <span class="fw-bold d-block text-dark">${dokter.nama}</span>
                    </div>
                </td>
                <td>
                    <span class="d-block fw-bold text-dark small mb-1"><i class="bi bi-award text-warning me-1"></i>${dokter.spesialis}</span>
                    <span class="text-muted small"><i class="bi bi-door-open me-1"></i>${displayRuangan}</span>
                </td>
                <td>
                    <span class="d-block text-muted small fw-bold mb-1"><i class="bi bi-calendar-check me-1"></i>${dokter.hari}</span>
                    <span class="text-primary small fw-bold"><i class="bi bi-clock me-1"></i>${displayJam}</span>
                </td>
                <td><span class="badge-status ${badgeStatus}">${dokter.status}</span></td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-sm btn-expand collapsed rounded-pill px-3 shadow-sm fw-bold" data-bs-toggle="collapse" data-bs-target="#pasienDoc${index}" style="font-size:0.7rem;">
                            <i class="bi bi-people-fill me-1"></i>Pasien (${assignedPatients.length})
                        </button>
                        <div class="dropdown dropdown-action">
                            <button class="btn btn-sm rounded-circle text-muted" type="button" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item text-dark" href="#" onclick="bukaModalEditDokter(${index})"><i class="bi bi-pencil-square text-primary me-2"></i>Ubah Jadwal</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="konfirmasiHapusDokter(${index})"><i class="bi bi-trash3 text-danger me-2"></i>Hapus Dokter</a></li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="collapse" id="pasienDoc${index}">
                <td colspan="6" class="p-0 border-0">
                    <div class="table-sub-row shadow-sm">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-clipboard2-pulse fs-4 text-primary me-2"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size:0.85rem;">Daftar Pasien Ditangani (${assignedPatients.length} Pasien)</h6>
                            </div>
                        </div>
                        <table class="table table-sm table-borderless mb-0">
                            <thead class="text-muted border-bottom" style="font-size:0.7rem;">
                                <tr><th>Nama Lengkap Pasien</th><th>Tindakan / Layanan Medis</th><th>Status Perawatan</th></tr>
                            </thead>
                            <tbody style="font-size:0.8rem;">${pasienListHTML}</tbody>
                        </table>
                    </div>
                </td>
            </tr>
        `;
        tbody.innerHTML += rowHTML;
    });
    updateStatistikDashboard();
}

function simpanDokterBaru() {
    let namaVal = document.getElementById('inputNamaDokter').value; if(namaVal.trim() === '') { alert('Lengkapi form!'); return; }
    let randomKode = "DK-" + Math.floor(1000 + Math.random() * 9000);
    dataDokter.push({ kode: randomKode, nama: namaVal, spesialis: document.getElementById('inputSpesialis').value, ruangan: document.getElementById('inputRuangan').value, hari: document.getElementById('inputJadwal').value, jam: document.getElementById('inputJam').value || '08:00 - 15:00', status: document.getElementById('inputStatusDokter').value });
    localStorage.setItem('dataDokterMediFlow', JSON.stringify(dataDokter));
    renderTabelDokter(); document.getElementById('inputNamaDokter').value = ''; document.getElementById('inputRuangan').value = ''; document.getElementById('inputJam').value = '';
    bootstrap.Modal.getInstance(document.getElementById('modalTambahDokter')).hide();
}

function bukaModalEditDokter(index) {
    indexDokterAkanDiedit = index; let d = dataDokter[index];
    document.getElementById('editNamaDokter').value = d.nama; document.getElementById('editSpesialis').value = d.spesialis;
    document.getElementById('editRuangan').value = d.ruangan; document.getElementById('editJadwal').value = d.hari;
    document.getElementById('editJam').value = d.jam.replace(' WIB', ''); document.getElementById('editStatusDokter').value = d.status;
    new bootstrap.Modal(document.getElementById('modalEditDokter')).show();
}

function simpanPerubahanDokter() {
    dataDokter[indexDokterAkanDiedit].nama = document.getElementById('editNamaDokter').value;
    dataDokter[indexDokterAkanDiedit].ruangan = document.getElementById('editRuangan').value;
    dataDokter[indexDokterAkanDiedit].jam = document.getElementById('editJam').value;
    dataDokter[indexDokterAkanDiedit].status = document.getElementById('editStatusDokter').value;
    localStorage.setItem('dataDokterMediFlow', JSON.stringify(dataDokter));
    renderTabelDokter(); renderTabelPasien(); bootstrap.Modal.getInstance(document.getElementById('modalEditDokter')).hide();
}

function konfirmasiHapusDokter(index) { indexDokterAkanHapus = index; document.getElementById('namaHapusDokter').innerText = dataDokter[index].nama; new bootstrap.Modal(document.getElementById('modalHapusDokter')).show(); }
function eksekusiHapusDokter() {
    let deletedKode = dataDokter[indexDokterAkanHapus].kode; dataPasien.forEach(p => { if(p.dokterKode === deletedKode) p.dokterKode = ''; });
    localStorage.setItem('dataPasienMediFlow', JSON.stringify(dataPasien));
    dataDokter.splice(indexDokterAkanHapus, 1); localStorage.setItem('dataDokterMediFlow', JSON.stringify(dataDokter));
    renderTabelDokter(); renderTabelPasien(); bootstrap.Modal.getInstance(document.getElementById('modalHapusDokter')).hide();
}

// === 4. MANAGEMENT DATA BOOKING KAMAR RAWAT INAP ===
function renderTabelBooking() {
    const tbody = document.getElementById('tabelBookingBody'); if(!tbody) return; tbody.innerHTML = '';
    
    dataBooking.forEach((booking, index) => {
        let pObj = dataPasien.find(p => p.rm === booking.pasienRm); let namaPasien = pObj ? pObj.nama : 'Pasien Tidak Valid';
        let badgeClass = booking.status === 'Checked-In' ? 'st-checkedin' : (booking.status === 'Waiting' ? 'st-waiting' : 'st-checkedout');
        
        tbody.innerHTML += `
            <tr>
                <td class="fw-bold text-primary">${booking.kode}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=${namaPasien.replace(/ /g, '+')}&background=eef5f5&color=38c8e6" class="rounded-circle me-3 border border-2 border-white shadow-sm" width="45"> 
                        <div>
                            <span class="fw-bold d-block text-dark mb-1">${namaPasien}</span>
                            <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">${booking.pasienRm}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="d-block fw-bold text-dark small mb-1"><i class="bi bi-layer-forward text-warning me-1"></i>${booking.kelas}</span>
                    <span class="text-muted small"><i class="bi bi-hash me-1"></i>${booking.nomor}</span>
                </td>
                <td>
                    <span class="d-block text-muted small fw-bold mb-1"><i class="bi bi-calendar-plus text-success me-1"></i>In: ${booking.checkIn}</span>
                    <span class="text-primary small fw-bold"><i class="bi bi-calendar-minus text-danger me-1"></i>Out: ${booking.checkOut}</span>
                </td>
                <td>
                    <div>
                        <span class="badge-status ${badgeClass} mb-1">${booking.status}</span>
                        <small class="d-block text-muted text-truncate" style="max-width:170px;">💬 ${booking.keterangan}</small>
                    </div>
                </td>
                <td class="text-center">
                    <div class="dropdown dropdown-action mx-auto" style="width:max-content;">
                        <button class="btn btn-sm rounded-circle text-muted" type="button" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item text-dark" href="#" onclick="bukaModalEditBooking(${index})"><i class="bi bi-pencil-square text-primary me-2"></i>Ubah Kamar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="konfirmasiHapusBooking(${index})"><i class="bi bi-trash3 text-danger me-2"></i>Batalkan Reservasi</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        `;
    });
    updateStatistikDashboard();
}

function simpanBookingBaru() {
    let pasienRmVal = document.getElementById('inputPasienBooking').value;
    let checkInVal = document.getElementById('inputCheckIn').value;
    if(!pasienRmVal || checkInVal === '') { alert('Mohon lengkapi form reservasi!'); return; }

    let randomKode = "BK-" + Math.floor(10000 + Math.random() * 90000);
    dataBooking.push({
        kode: randomKode, pasienRm: pasienRmVal, kelas: document.getElementById('inputKelasKamar').value, nomor: document.getElementById('inputNomorKamar').value,
        checkIn: checkInVal, checkOut: document.getElementById('inputCheckOut').value, status: document.getElementById('inputStatusBooking').value,
        keterangan: document.getElementById('inputKeteranganBooking').value || 'Rawat inap medis.'
    });

    localStorage.setItem('dataBookingMediFlow', JSON.stringify(dataBooking));
    renderTabelBooking(); bootstrap.Modal.getInstance(document.getElementById('modalTambahBooking')).hide();
}

function bukaModalEditBooking(index) {
    indexBookingAkanDiedit = index; let b = dataBooking[index]; let pObj = dataPasien.find(p => p.rm === b.pasienRm);
    document.getElementById('editNamaPasienBooking').value = pObj ? pObj.nama : b.pasienRm;
    
    const kelasSelect = document.getElementById('editKelasKamar');
    if(kelasSelect && pObj) {
        kelasSelect.innerHTML = pObj.layanan === 'BPJS' ? `<option value="Kelas I">Kelas I</option><option value="Kelas II">Kelas II</option><option value="Kelas III">Kelas III</option>` : `<option value="President Suite">President Suite</option><option value="VIP">VIP</option><option value="Kelas I">Kelas I</option><option value="Kelas II">Kelas II</option><option value="Kelas III">Kelas III</option>`;
    }

    document.getElementById('editKelasKamar').value = b.kelas; document.getElementById('editNomorKamar').value = b.nomor;
    document.getElementById('editCheckIn').value = b.checkIn; document.getElementById('editCheckOut').value = b.checkOut;
    document.getElementById('editStatusBooking').value = b.status; document.getElementById('editKeteranganBooking').value = b.keterangan;
    new bootstrap.Modal(document.getElementById('modalEditBooking')).show();
}

function simpanPerubahanBooking() {
    let b = dataBooking[indexBookingAkanDiedit];
    b.kelas = document.getElementById('editKelasKamar').value; b.nomor = document.getElementById('editNomorKamar').value;
    b.checkIn = document.getElementById('editCheckIn').value; b.checkOut = document.getElementById('editCheckOut').value;
    b.status = document.getElementById('editStatusBooking').value; b.keterangan = document.getElementById('editKeteranganBooking').value;
    localStorage.setItem('dataBookingMediFlow', JSON.stringify(dataBooking));
    renderTabelBooking(); bootstrap.Modal.getInstance(document.getElementById('modalEditBooking')).hide();
}

function konfirmasiHapusBooking(index) { indexBookingAkanHapus = index; let b = dataBooking[index]; let p = dataPasien.find(x=>x.rm===b.pasienRm); document.getElementById('namaHapusBooking').innerText = p?p.nama:b.pasienRm; new bootstrap.Modal(document.getElementById('modalHapusBooking')).show(); }
function eksekusiHapusBooking() { dataBooking.splice(indexBookingAkanHapus, 1); localStorage.setItem('dataBookingMediFlow', JSON.stringify(dataBooking)); renderTabelBooking(); bootstrap.Modal.getInstance(document.getElementById('modalHapusBooking')).hide(); }

// === 5. FITUR CHART & BPJS ===
function cekBpjsAdmin() {
    const input = document.getElementById('adminInput').value.toLowerCase().trim();
    const hasilBox = document.getElementById('hasilAdmin'); const resNama = document.getElementById('resNamaAdmin'); const resStatus = document.getElementById('resStatusAdmin'); const btnAksi = document.getElementById('btnAksiAdmin');
    if(input === '') { alert('Masukkan nama pasien!'); return; }
    hasilBox.classList.remove('d-none');
    
    if(input.includes('john')) {
        resNama.innerText = "John Black"; resStatus.innerHTML = "<span class='badge bg-danger mb-2 px-3 py-1.5 rounded-pill text-white'>MENUNGGAK</span><p class='small m-0 text-muted'>Pasien memiliki tunggakan iuran.</p>"; btnAksi.innerHTML = `<button class="btn btn-warning w-100 fw-bold rounded-pill text-dark btn-sm shadow-sm">Alihkan ke UMUM</button>`;
    } else if (input.includes('jane') || input.includes('ashley')) {
        resNama.innerText = input.includes('jane') ? "Jane Black" : "Ashley Black"; resStatus.innerHTML = "<span class='badge bg-success mb-2 px-3 py-1.5 rounded-pill text-white'>AKTIF</span><p class='small m-0 text-muted'>Data tervalidasi aktif.</p>"; btnAksi.innerHTML = `<button class="btn btn-primary w-100 fw-bold rounded-pill btn-sm border text-white shadow-sm">Cetak Antrean</button>`;
    } else { resNama.innerText = "Tidak Ditemukan"; resStatus.innerHTML = "<span class='small text-muted'>Pastikan nama benar.</span>"; btnAksi.innerHTML = ""; }
}

let adminBarChart = null;
function renderAdminCharts() {
    const canvasBar = document.getElementById('adminBarChart'); if (!canvasBar) return;
    const ctxBar = canvasBar.getContext('2d'); if (adminBarChart) adminBarChart.destroy();
    adminBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            datasets: [{ label: 'Pasien', data: [800, 950, 1100, 1040, 1200, 1248], backgroundColor: '#23c4e6', borderRadius: 4, barPercentage: 0.4 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f0f0f0' }, ticks: { font: { size: 9 } } }, x: { grid: { display: false }, ticks: { font: { size: 9 } } } } }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    updateDate(); updateClock(); renderTabelPasien(); renderTabelDokter(); renderTabelBooking();
});