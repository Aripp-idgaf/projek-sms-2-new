// === 1. LOGIKA NAVIGASI SPA ===
        function switchView(viewId, element) {
            document.querySelectorAll('.view-section').forEach(view => view.classList.add('d-none'));
            document.getElementById(viewId).classList.remove('d-none');
            document.querySelectorAll('.nav-wrapper').forEach(nav => nav.classList.remove('active'));
            if(element) element.classList.add('active');
            if(viewId === 'view-home') renderAdminCharts();
        }

        function logout() {
            if(confirm('Apakah Anda yakin ingin keluar?')) { window.location.href = '../../index.html'; }
        }

        // ==========================================
        // === DATABASE UTAMA (LOCAL STORAGE) ===
        // ==========================================
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
            { kode: "BK-77201", pasienRm: "RM-882910", kelas: "President Suite", nomor: "Kamar 501 (Bed A)", checkIn: "2026-05-18", checkOut: "2026-05-22", status: "Checked-In", keterangan: "Observasi luka bakar pasca-bedah." },
            { kode: "BK-77202", pasienRm: "RM-882912", kelas: "Kelas I", nomor: "Kamar 204 (Bed C)", checkIn: "2026-05-20", checkOut: "2026-05-25", status: "Waiting", keterangan: "Rawat rujukan sekunder Poli Jantung." }
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

        // --- MANAJEMEN DROPDOWN BOOKING PASIEN ---
        function isiDropdownPasienBooking() {
            const selectEl = document.getElementById('inputPasienBooking'); if(!selectEl) return; selectEl.innerHTML = '';
            if(dataPasien.length === 0) { selectEl.innerHTML = '<option value="">Tidak ada data pasien</option>'; return; }
            dataPasien.forEach(p => { selectEl.innerHTML += `<option value="${p.rm}">${p.nama} (${p.rm}) - [${p.layanan}]</option>`; });
            
            // Langsung panggil filter pertama kali modal dibuka
            filterKelasKamarBerdasarLayanan('inputPasienBooking', 'inputKelasKamar');
        }

        // 🔥 LOGIKA SINKRONISASI FILTER PILIHAN KAMAR (BPJS VS UMUM) 🔥
        function filterKelasKamarBerdasarLayanan(pasienSelectId, kelasSelectId) {
            const pasienRm = document.getElementById(pasienSelectId).value;
            const kelasSelect = document.getElementById(kelasSelectId);
            if(!pasienRm || !kelasSelect) return;

            const pasienObj = dataPasien.find(p => p.rm === pasienRm);
            if(!pasienObj) return;

            kelasSelect.innerHTML = '';
            if(pasienObj.layanan === 'BPJS') {
                // Pasien BPJS dibatasi hanya bisa memilih kelas standar BPJS
                kelasSelect.innerHTML = `
                    <option value="Kelas I">Kelas I (Fasilitas BPJS)</option>
                    <option value="Kelas II">Kelas II (Fasilitas BPJS)</option>
                    <option value="Kelas III">Kelas III (Fasilitas BPJS)</option>
                `;
            } else {
                // Pasien Umum bebas memilih semua jenjang kelas/fasilitas
                kelasSelect.innerHTML = `
                    <option value="President Suite">President Suite (Fasilitas Umum)</option>
                    <option value="VIP">VIP (Fasilitas Umum)</option>
                    <option value="Kelas I">Kelas I (Fasilitas Umum)</option>
                    <option value="Kelas II">Kelas II (Fasilitas Umum)</option>
                    <option value="Kelas III">Kelas III (Fasilitas Umum)</option>
                `;
            }
        }

        // --- HITUNG DAN UPDATE OKUPANSI KAMAR INAP DI HOMEsecara REAL-TIME ---
        function updateStatistikDashboard() {
            renderAdminCharts();
            const totalPasienEl = document.getElementById('rswnTotalPasien'); if(totalPasienEl) totalPasienEl.innerText = `${dataPasien.length} Pasien`;
            const totalDokterEl = document.getElementById('rswnTotalDokter'); if(totalDokterEl) { let aktif = dataDokter.filter(d => d.status === 'Aktif').length; totalDokterEl.innerText = `${aktif} Aktif / ${dataDokter.length} Dokter`; }
            const totalBookingEl = document.getElementById('rswnTotalBooking'); if(totalBookingEl) totalBookingEl.innerText = `${dataBooking.length} Booking`;

            // Hitung ranjang inap aktif (Status: Checked-In)
            let vipInap = dataBooking.filter(b => (b.kelas === 'President Suite' || b.kelas === 'VIP') && b.status === 'Checked-In').length;
            let k1Inap = dataBooking.filter(b => b.kelas === 'Kelas I' && b.status === 'Checked-In').length;
            let k2Inap = dataBooking.filter(b => b.kelas === 'Kelas II' && b.status === 'Checked-In').length;
            let k3Inap = dataBooking.filter(b => b.kelas === 'Kelas III' && b.status === 'Checked-In').length;

            // Ditambah angka dummy/bawaan agar dashboard terlihat ramai kapasitasnya
            if(document.getElementById('bedVipDash')) document.getElementById('bedVipDash').innerText = `${vipInap + 4} / 15 Bed`;
            if(document.getElementById('bedKelas1Dash')) document.getElementById('bedKelas1Dash').innerText = `${k1Inap + 18} / 40 Bed`;
            if(document.getElementById('bedKelas2Dash')) document.getElementById('bedKelas2Dash').innerText = `${k2Inap + 32} / 50 Bed`;
            if(document.getElementById('bedKelas3Dash')) document.getElementById('bedKelas3Dash').innerText = `${k3Inap + 54} / 80 Bed`;
        }

        // ==========================================
        // === 2. MANAGEMENT DATA PASIEN ===
        // ==========================================
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
            let darahVal = document.getElementById('inputGolDarah').value; let layananVal = document.getElementById('inputJenisLayanan').value;
            let kategoriVal = document.getElementById('inputKategoriUsia').value;

            if(namaVal.trim() === '') { alert('Mohon lengkapi Nama Pasien!'); return; }
            if(!dokterKodeVal) { alert('Silakan tentukan dokter pemeriksa aktif!'); return; }
            
            let randomRM = "RM-" + Math.floor(100000 + Math.random() * 900000);
            dataPasien.push({ rm: randomRM, nama: namaVal, darah: darahVal, layanan: layananVal, kategori: kategoriVal, dokterKode: dokterKodeVal });
            localStorage.setItem('dataPasienMediFlow', JSON.stringify(dataPasien));
            
            renderTabelPasien(); renderTabelDokter(); document.getElementById('inputNamaPasien').value = '';
            bootstrap.Modal.getInstance(document.getElementById('modalTambahPasien')).hide();
        }

        function bukaModalEditPasien(index) {
            indexPasienAkanDiedit = index; let p = dataPasien[index]; document.getElementById('editNamaPasien').value = p.nama;
            isiDropdownDokterPilihan('editDokterPasien', p.dokterKode); new bootstrap.Modal(document.getElementById('modalEditPasien')).show();
        }

        function simpanPerubahanPasien() {
            if(indexPasienAkanDiedit === null) return; let dokterBaruKode = document.getElementById('editDokterPasien').value;
            if(!dokterBaruKode) { alert('Dokter rujukan tidak boleh kosong!'); return; }
            dataPasien[indexPasienAkanDiedit].dokterKode = dokterBaruKode;
            localStorage.setItem('dataPasienMediFlow', JSON.stringify(dataPasien));
            renderTabelPasien(); renderTabelDokter(); renderTabelBooking();
            bootstrap.Modal.getInstance(document.getElementById('modalEditPasien')).hide();
        }

        function konfirmasiHapusPasien(index) { indexPasienAkanHapus = index; document.getElementById('namaHapus').innerText = dataPasien[index].nama; new bootstrap.Modal(document.getElementById('modalHapusPasien')).show(); }
        function eksekusiHapus() {
            if(indexPasienAkanHapus !== null) {
                let targetRm = dataPasien[indexPasienAkanHapus].rm;
                dataBooking = dataBooking.filter(b => b.pasienRm !== targetRm);
                localStorage.setItem('dataBookingMediFlow', JSON.stringify(dataBooking));
                
                dataPasien.splice(indexPasienAkanHapus, 1); localStorage.setItem('dataPasienMediFlow', JSON.stringify(dataPasien));
                renderTabelPasien(); renderTabelDokter(); renderTabelBooking(); indexPasienAkanHapus = null;
                bootstrap.Modal.getInstance(document.getElementById('modalHapusPasien')).hide();
            }
        }

        // ==========================================
        // === 3. MANAGEMENT DATA DOKTER ===
        // ==========================================
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
                                        <p class="small text-muted mb-0" style="font-size:0.65rem;">Informasi tindakan dan status rawat medis saat ini.</p>
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
            let namaVal = document.getElementById('inputNamaDokter').value; let spesialisVal = document.getElementById('inputSpesialis').value;
            let ruanganVal = document.getElementById('inputRuangan').value; let jadwalVal = document.getElementById('inputJadwal').value;
            let jamVal = document.getElementById('inputJam').value; let statusVal = document.getElementById('inputStatusDokter').value;

            if(namaVal.trim() === '') { alert('Mohon lengkapi Nama Dokter!'); return; }
            if(statusVal !== 'Cuti') { if(ruanganVal.trim() === '' || jamVal.trim() === '') { alert('Ruangan dan Jam Kerja wajib diisi jika dokter Aktif!'); return; } } 
            else { if(ruanganVal.trim() === '') ruanganVal = '-'; if(jamVal.trim() === '') jamVal = '-'; }

            let randomKode = "DK-" + Math.floor(1000 + Math.random() * 9000);
            dataDokter.push({ kode: randomKode, nama: namaVal, spesialis: spesialisVal, ruangan: ruanganVal, hari: jadwalVal, jam: jamVal, status: statusVal });
            localStorage.setItem('dataDokterMediFlow', JSON.stringify(dataDokter));
            
            renderTabelDokter(); 
            document.getElementById('inputNamaDokter').value = ''; document.getElementById('inputRuangan').value = ''; document.getElementById('inputJam').value = '';
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
            if(indexDokterAkanDiedit === null) return;
            dataDokter[indexDokterAkanDiedit].nama = document.getElementById('editNamaDokter').value;
            dataDokter[indexDokterAkanDiedit].spesialis = document.getElementById('editSpesialis').value;
            dataDokter[indexDokterAkanDiedit].ruangan = document.getElementById('editRuangan').value;
            dataDokter[indexDokterAkanDiedit].hari = document.getElementById('editJadwal').value;
            dataDokter[indexDokterAkanDiedit].jam = document.getElementById('editJam').value;
            dataDokter[indexDokterAkanDiedit].status = document.getElementById('editStatusDokter').value;

            localStorage.setItem('dataDokterMediFlow', JSON.stringify(dataDokter));
            renderTabelDokter(); renderTabelPasien(); 
            bootstrap.Modal.getInstance(document.getElementById('modalEditDokter')).hide();
        }

        function konfirmasiHapusDokter(index) { indexDokterAkanHapus = index; document.getElementById('namaHapusDokter').innerText = dataDokter[index].nama; new bootstrap.Modal(document.getElementById('modalHapusDokter')).show(); }
        function eksekusiHapusDokter() {
            if(indexDokterAkanHapus !== null) {
                let deletedKode = dataDokter[indexDokterAkanHapus].kode;
                dataPasien.forEach(p => { if(p.dokterKode === deletedKode) p.dokterKode = ''; });
                localStorage.setItem('dataPasienMediFlow', JSON.stringify(dataPasien));
                dataDokter.splice(indexDokterAkanHapus, 1); localStorage.setItem('dataDokterMediFlow', JSON.stringify(dataDokter));
                renderTabelDokter(); renderTabelPasien(); indexDokterAkanHapus = null;
                bootstrap.Modal.getInstance(document.getElementById('modalHapusDokter')).hide();
            }
        }

        // =========================================================
        // === 4. MANAGEMENT DATA BOOKING KAMAR RAWAT INAP ===
        // =========================================================
        function renderTabelBooking() {
            const tbody = document.getElementById('tabelBookingBody');
            if(!tbody) return; tbody.innerHTML = '';
            
            dataBooking.forEach((booking, index) => {
                let pObj = dataPasien.find(p => p.rm === booking.pasienRm);
                let namaPasien = pObj ? pObj.nama : 'Pasien Tidak Diketahui';
                
                let badgeClass = 'st-checkedin';
                if(booking.status === 'Waiting') badgeClass = 'st-waiting';
                if(booking.status === 'Checked-Out') badgeClass = 'st-checkedout';

                let rowHTML = `
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
                                <small class="d-block text-muted text-truncate" style="max-width:170px;" title="${booking.keterangan}">💬 ${booking.keterangan}</small>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="dropdown dropdown-action mx-auto" style="width:max-content;">
                                <button class="btn btn-sm rounded-circle text-muted" type="button" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item text-dark" href="#" onclick="bukaModalEditBooking(${index})"><i class="bi bi-pencil-square text-primary me-2"></i>Ubah Kamar</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="konfirmasiHapusBooking(${index})"><i class="bi bi-trash3 text-danger me-2"></i>Hapus Booking</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += rowHTML;
            });
            updateStatistikDashboard();
        }

        function simpanBookingBaru() {
            let pasienRmVal = document.getElementById('inputPasienBooking').value;
            let kelasVal = document.getElementById('inputKelasKamar').value;
            let nomorVal = document.getElementById('inputNomorKamar').value;
            let checkInVal = document.getElementById('inputCheckIn').value;
            let checkOutVal = document.getElementById('inputCheckOut').value;
            let statusVal = document.getElementById('inputStatusBooking').value;
            let keteranganVal = document.getElementById('inputKeteranganBooking').value;

            if(!pasienRmVal) { alert('Silakan pilih pasien terlebih dahulu!'); return; }
            if(nomorVal.trim() === '' || checkInVal === '' || checkOutVal === '') { alert('Semua data wajib dilengkapi!'); return; }

            let randomKode = "BK-" + Math.floor(10000 + Math.random() * 90000);
            dataBooking.push({
                kode: randomKode, pasienRm: pasienRmVal, kelas: kelasVal, nomor: nomorVal,
                checkIn: checkInVal, checkOut: checkOutVal, status: statusVal,
                keterangan: keteranganVal ? keteranganVal : 'Tidak ada catatan tambahan.'
            });

            localStorage.setItem('dataBookingMediFlow', JSON.stringify(dataBooking));
            renderTabelBooking();

            document.getElementById('inputNomorKamar').value = '';
            bootstrap.Modal.getInstance(document.getElementById('modalTambahBooking')).hide();
        }

        // 🔥 MODAL EDIT BOOKING: OTOMATIS FILTER SESUAI BPJS/UMUM KEMBALI 🔥
        function bukaModalEditBooking(index) {
            indexBookingAkanDiedit = index; let b = dataBooking[index];
            let pObj = dataPasien.find(p => p.rm === b.pasienRm);
            
            document.getElementById('editNamaPasienBooking').value = pObj ? `${pObj.nama} (${b.pasienRm})` : b.pasienRm;
            
            // Render filter dropdown edit secara dinamis sesuai profile aslinya
            const kelasSelect = document.getElementById('editKelasKamar');
            if(kelasSelect && pObj) {
                kelasSelect.innerHTML = '';
                if(pObj.layanan === 'BPJS') {
                    kelasSelect.innerHTML = `
                        <option value="Kelas I">Kelas I (Fasilitas BPJS)</option>
                        <option value="Kelas II">Kelas II (Fasilitas BPJS)</option>
                        <option value="Kelas III">Kelas III (Fasilitas BPJS)</option>
                    `;
                } else {
                    kelasSelect.innerHTML = `
                        <option value="President Suite">President Suite (Fasilitas Umum)</option>
                        <option value="VIP">VIP (Fasilitas Umum)</option>
                        <option value="Kelas I">Kelas I (Fasilitas Umum)</option>
                        <option value="Kelas II">Kelas II (Fasilitas Umum)</option>
                        <option value="Kelas III">Kelas III (Fasilitas Umum)</option>
                    `;
                }
            }

            document.getElementById('editKelasKamar').value = b.kelas;
            document.getElementById('editNomorKamar').value = b.nomor;
            document.getElementById('editCheckIn').value = b.checkIn;
            document.getElementById('editCheckOut').value = b.checkOut;
            document.getElementById('editStatusBooking').value = b.status;
            document.getElementById('editKeteranganBooking').value = b.keterangan;

            new bootstrap.Modal(document.getElementById('modalEditBooking')).show();
        }

        function simpanPerubahanBooking() {
            if(indexBookingAkanDiedit === null) return;
            let nomorVal = document.getElementById('editNomorKamar').value;
            let checkInVal = document.getElementById('editCheckIn').value;
            let checkOutVal = document.getElementById('editCheckOut').value;

            if(nomorVal.trim() === '' || checkInVal === '' || checkOutVal === '') { alert('Data perubahan tidak boleh kosong!'); return; }

            dataBooking[indexBookingAkanDiedit].kelas = document.getElementById('editKelasKamar').value;
            dataBooking[indexBookingAkanDiedit].nomor = nomorVal;
            dataBooking[indexBookingAkanDiedit].checkIn = checkInVal;
            dataBooking[indexBookingAkanDiedit].checkOut = checkOutVal;
            dataBooking[indexBookingAkanDiedit].status = document.getElementById('editStatusBooking').value;
            dataBooking[indexBookingAkanDiedit].keterangan = document.getElementById('editKeteranganBooking').value;

            localStorage.setItem('dataBookingMediFlow', JSON.stringify(dataBooking));
            renderTabelBooking();
            bootstrap.Modal.getInstance(document.getElementById('modalEditBooking')).hide();
        }

        function konfirmasiHapusBooking(index) {
            indexBookingAkanHapus = index; let b = dataBooking[index];
            let pObj = dataPasien.find(p => p.rm === b.pasienRm);
            document.getElementById('namaHapusBooking').innerText = pObj ? pObj.nama : b.pasienRm;
            new bootstrap.Modal(document.getElementById('modalHapusBooking')).show();
        }

        function eksekusiHapusBooking() {
            if(indexBookingAkanHapus !== null) {
                dataBooking.splice(indexBookingAkanHapus, 1);
                localStorage.setItem('dataBookingMediFlow', JSON.stringify(dataBooking));
                renderTabelBooking(); indexBookingAkanHapus = null;
                bootstrap.Modal.getInstance(document.getElementById('modalHapusBooking')).hide();
            }
        }

        // ==========================================
        // === 5. FITUR CHART & BPJS ===
        // ==========================================
        function cekBpjsAdmin() {
            const input = document.getElementById('adminInput').value.toLowerCase().trim();
            const hasilBox = document.getElementById('hasilAdmin'); const resNama = document.getElementById('resNamaAdmin');
            const resStatus = document.getElementById('resStatusAdmin'); const btnAksi = document.getElementById('btnAksiAdmin');

            if(input === '') { alert('Masukkan nama!'); return; }
            hasilBox.classList.remove('d-none');
            
            if(input.includes('john')) {
                resNama.innerText = "John Black";
                resStatus.innerHTML = "<span class='badge bg-danger mb-2 px-3 py-1.5 rounded-pill text-white'>MENUNGGAK</span><p class='small m-0 text-muted'>Pasien memiliki tunggakan iuran.</p>";
                btnAksi.innerHTML = `<button class="btn btn-warning w-100 fw-bold rounded-pill text-dark btn-sm shadow-sm">Alihkan ke UMUM</button>`;
            } else if (input.includes('jane') || input.includes('ashley')) {
                resNama.innerText = input.includes('jane') ? "Jane Black" : "Ashley Black";
                resStatus.innerHTML = "<span class='badge bg-success mb-2 px-3 py-1.5 rounded-pill text-white'>AKTIF</span><p class='small m-0 text-muted'>Data tervalidasi aktif.</p>";
                btnAksi.innerHTML = `<button class="btn btn-primary w-100 fw-bold rounded-pill btn-sm border text-white shadow-sm">Cetak Antrean</button>`;
            } else {
                resNama.innerText = "Tidak Ditemukan"; resStatus.innerHTML = "<span class='small text-muted'>Pastikan nama benar.</span>"; btnAksi.innerHTML = "";
            }
        }

        let adminBarChart = null;
        function renderAdminCharts() {
            const canvasBar = document.getElementById('adminBarChart');
            if (canvasBar) {
                const ctxBar = canvasBar.getContext('2d');
                if (adminBarChart) adminBarChart.destroy();
                adminBarChart = new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                        datasets: [{ label: 'Pasien', data: [800, 950, 1100, 1040, 1200, 1248], backgroundColor: '#38c8e6', borderRadius: 4, barPercentage: 0.4 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#f0f0f0' }, ticks: { font: { size: 9 } } }, x: { grid: { display: false }, ticks: { font: { size: 9 } } } } }
                });
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            renderTabelPasien(); 
            renderTabelDokter(); 
            renderTabelBooking(); 
            renderAdminCharts(); 
        });