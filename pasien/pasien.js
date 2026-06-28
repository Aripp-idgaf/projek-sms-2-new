// ====================================================
// SCRIPT NAVIGASI & MENU
// ====================================================
function switchView(viewId) {
    document.querySelectorAll('.view-section').forEach(el => el.classList.add('d-none'));
    const targetSection = document.getElementById(viewId);
    if (targetSection) targetSection.classList.remove('d-none');
}

function updateDateTime() {
    const realtimeElement = document.getElementById('realtime-datetime');
    if(realtimeElement) {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        realtimeElement.innerText = now.toLocaleDateString('id-ID', options);
    }
}
setInterval(updateDateTime, 1000);
updateDateTime();

function togglePanel() {
    document.getElementById('rightPanel').classList.toggle('closed'); 
}

function logoutSession() {
    window.location.href = "../login/logout.php"; 
}

// ====================================================
// LOGIKA DROPDOWN KUSTOM
// ====================================================
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.custom-dd-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const menu = this.closest('.dropdown-menu');
            const toggleBtn = menu.previousElementSibling;
            const displayArea = toggleBtn.querySelector('span') || toggleBtn;

            menu.querySelectorAll('.custom-dd-item').forEach(el => el.classList.remove('active'));
            this.classList.add('active');

            const selectedText = this.innerText.trim();
            if(toggleBtn.querySelector('span')){
                displayArea.innerText = selectedText;
                displayArea.classList.remove('text-muted');
                displayArea.style.color = '#1e2f3a';
            }

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

    detectDevice(); 
});

document.addEventListener('DOMContentLoaded', function() {
    const togglePwd = document.getElementById('toggleSettingsPassword');
    if(togglePwd) {
        togglePwd.addEventListener('click', function() {
            const pwdInput = document.getElementById('settingsPasswordInput');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                this.classList.remove('bi-eye-slash');
                this.classList.add('bi-eye');
                this.style.color = '#38c8e6'; 
            } else {
                pwdInput.type = 'password';
                this.classList.remove('bi-eye');
                this.classList.add('bi-eye-slash');
                this.style.color = '#a0b8c2'; 
            }
        });
    }
});

// ====================================================
// GANTI FOTO PROFIL (UTAMA, TAMBAH KEL, EDIT KEL)
// ====================================================
const uploadProfileInput = document.getElementById('uploadProfile');
if (uploadProfileInput) {
    uploadProfileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('settingsProfilePic').src = e.target.result;
                document.getElementById('settingsProfilePic').style.display = 'block';
                document.getElementById('settingsProfileInitials').style.display = 'none';

                document.getElementById('mainProfilePic').src = e.target.result;
                document.getElementById('mainProfilePic').style.display = 'block';
                document.getElementById('mainProfileInitials').style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });
}

const kelFotoInput = document.getElementById('kelFoto');
if(kelFotoInput){
    kelFotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('previewFotoKel').src = event.target.result;
                document.getElementById('previewFotoKel').style.display = 'block';
                document.getElementById('iconDefaultKel').style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });
}

const editKelFotoInput = document.getElementById('editKelFoto');
if(editKelFotoInput){
    editKelFotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('previewEditFotoKel').src = event.target.result;
                document.getElementById('previewEditFotoKel').style.display = 'block';
                document.getElementById('iconEditDefaultKel').style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });
}

// ====================================================
// FITUR PEMILIHAN KAMAR ALA TIX ID & INTEGRASI JADWAL
// ====================================================
let dataPesananKamar = null;

function pilihBed(elemen, nomorBed, namaKelas, tarif) {
    if (elemen.classList.contains('terisi')) {
        alert("Mohon maaf, Bed " + nomorBed + " saat ini sedang digunakan/tidak tersedia.");
        return;
    }

    document.querySelectorAll('.tix-seat').forEach(box => {
        box.classList.remove('selected');
    });

    elemen.classList.add('selected');

    dataPesananKamar = {
        kelas: namaKelas,
        bed: nomorBed,
        tarif: tarif
    };

    document.getElementById('txtSelectedBed').innerText = namaKelas + " (Bed " + nomorBed + ")";
    document.getElementById('bookingBar').classList.remove('d-none');
}

// TOMBOL "LANJUTKAN" DI KAMAR (TANPA ALERT)
function prosesPesanKamar() {
    if (!dataPesananKamar) return;
    
    // Tulis info bed terpilih ke kotak Jadwal
    const infoContainer = document.getElementById('infoKamarTerpilih');
    infoContainer.innerHTML = `
        <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded-3 border border-info shadow-sm mt-2">
            <div>
                <span class="d-block fw-bold text-dark">${dataPesananKamar.kelas}</span>
                <span class="text-muted" style="font-size:0.8rem;">Nomor Bed: <strong class="text-primary">${dataPesananKamar.bed}</strong></span>
            </div>
            <div class="text-end">
                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1 mb-1 d-block" style="font-size:0.65rem;">Terpilih</span>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.7rem;" onclick="batalPilihBed()">Hapus</button>
            </div>
        </div>
    `;
    
    // Isi value form hidden
    const inputHidden = document.getElementById('inputKamarTerpilih');
    if(inputHidden) inputHidden.value = dataPesananKamar.bed;

    // Sembunyikan dan Reset UI Kamar TIX ID
    document.getElementById('bookingBar').classList.add('d-none');
    document.querySelectorAll('.tix-seat').forEach(box => box.classList.remove('selected'));

    // Langsung pindah ke halaman Jadwal dengan mulus tanpa Alert
    switchView('view-jadwal');
}

// TOMBOL "HAPUS/BATAL" DI FORM JADWAL
function batalPilihBed() {
    dataPesananKamar = null;
    const inputHidden = document.getElementById('inputKamarTerpilih');
    if(inputHidden) inputHidden.value = '';
    
    const infoContainer = document.getElementById('infoKamarTerpilih');
    infoContainer.innerHTML = `
        <div class="text-muted small">Belum ada kamar/bed yang dipilih. (Abaikan jika hanya butuh Rawat Jalan).</div>
        <button type="button" class="btn btn-sm btn-outline-teal mt-2" onclick="switchView('view-kamar')">
            <i class="bi bi-search me-1"></i> Cari / Pilih Kamar
        </button>
    `;
}

function kirimJadwalAlert() {
    alert('Jadwal berhasil dikirim! Silakan tunggu konfirmasi Admin.');
    switchView('view-home'); 
}

// ====================================================
// FITUR SEARCH BAR REALTIME (DOKTER, POLI, RIWAYAT)
// ====================================================
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    if(searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase().trim();

            if (e.key === 'Enter') {
                if (term.includes('kamar') || term.includes('inap') || term.includes('bed')) {
                    switchView('view-kamar');
                    searchInput.value = ''; return;
                }
                if (term.includes('jadwal') || term.includes('janji')) {
                    switchView('view-jadwal');
                    searchInput.value = ''; return;
                }
                if (term.includes('setting') || term.includes('profil')) {
                    const settingModal = new bootstrap.Modal(document.getElementById('settingsModal'));
                    settingModal.show();
                    searchInput.value = ''; return;
                }
            }

            if (term.length > 0 && document.getElementById('view-home').classList.contains('d-none') === false) {
                switchView('view-riwayat');
            } else if (term.length === 0) {
                switchView('view-home');
            }

            const riwayatItems = document.querySelectorAll('.riwayat-item');
            riwayatItems.forEach(item => {
                const textContent = item.innerText.toLowerCase();
                if (textContent.includes(term)) {
                    item.style.display = 'block'; 
                } else {
                    item.style.display = 'none'; 
                }
            });
            
        });
    }
});


// ====================================================
// SISTEM KELUARGA (TAMBAH, EDIT, HAPUS, SWITCH)
// ====================================================
function toggleFormKeluarga() {
    const formTambah = document.getElementById('formTambahKeluarga');
    const formEdit = document.getElementById('formEditKeluarga');
    const btn = document.getElementById('btnBukaFormKeluarga');
    
    if (formTambah.classList.contains('d-none')) {
        formTambah.classList.remove('d-none');
        formEdit.classList.add('d-none'); 
        btn.classList.add('d-none'); 
    } else {
        formTambah.classList.add('d-none');
        btn.classList.remove('d-none'); 
        
        document.getElementById('kelNama').value = '';
        document.getElementById('kelNik').value = '';
        document.getElementById('kelTglLahir').value = '';
        document.getElementById('kelGolDarah').value = '-';
        document.getElementById('kelAlamat').value = '';
        document.getElementById('kelBpjs').value = ''; 
        document.getElementById('kelHubungan').value = '';
        document.getElementById('kelFoto').value = '';
        document.getElementById('previewFotoKel').style.display = 'none';
        document.getElementById('iconDefaultKel').style.display = 'block';
        
        const displayHubungan = document.getElementById('displayKelHubungan');
        if (displayHubungan) displayHubungan.querySelector('span').innerText = 'Pilih Hubungan...';
    }
}

function simpanKeluarga() {
    const nama = document.getElementById('kelNama').value;
    const nik = document.getElementById('kelNik').value;
    const tglLahir = document.getElementById('kelTglLahir').value;
    const golDarah = document.getElementById('kelGolDarah').value;
    const hubungan = document.getElementById('kelHubungan').value;
    const alamatKeluarga = document.getElementById('kelAlamat').value;
    const bpjsKeluarga = document.getElementById('kelBpjs').value; 
    const fotoFile = document.getElementById('kelFoto').files[0];

    if (nama === '' || nik === '' || hubungan === '' || tglLahir === '') {
        alert("Harap lengkapi semua data keluarga (Nama, NIK, Tanggal Lahir, Hubungan)!");
        return;
    }

    const btn = document.getElementById('btnSimpanKel');
    const oldText = btn.innerText;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    btn.disabled = true;

    const formData = new FormData();
    formData.append('nama', nama);
    formData.append('nik', nik);
    formData.append('tanggal_lahir', tglLahir);
    formData.append('gol_darah', golDarah);
    formData.append('alamat', alamatKeluarga);
    formData.append('bpjs', bpjsKeluarga);
    formData.append('hubungan', hubungan);
    if(fotoFile) { formData.append('foto_keluarga', fotoFile); }

    fetch('simpan_keluarga.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerText = oldText;

        if(data.status === 'sukses'){
            let umurKeluarga = "-";
            if (tglLahir) {
                const birthDate = new Date(tglLahir);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
                umurKeluarga = age + " Tahun";
            }

            let rmKeluarga = "RM-K" + Math.floor(1000 + Math.random() * 9000);
            let fotoPath = (data.foto != '') ? '../uploads/' + data.foto : '';
            let idBaru = data.id;

            dataKeluarga.push({
                id: idBaru,
                nama: nama,
                nik: nik,
                tanggal_lahir: tglLahir,
                hubungan: hubungan,
                umur: umurKeluarga, 
                rm: rmKeluarga,
                darah: golDarah === '-' ? '-' : golDarah,
                alamat: alamatKeluarga !== '' ? alamatKeluarga : '-',
                bpjs: bpjsKeluarga !== '' ? bpjsKeluarga : '-',
                foto: fotoPath
            });

            const currIndex = dataKeluarga.length - 1;
            const initName = nama.charAt(0).toUpperCase();
            const namaPanggilan = nama.split(" ")[0]; 

            let imgHTML = fotoPath != '' ? `<img src="${fotoPath}" id="list-img-${idBaru}" style="width:100%; height:100%; object-fit:cover;">` : `<span id="list-init-${idBaru}">${initName}</span>`;
            
            const listContainer = document.getElementById('settingsFamilyList');
            const newListItem = `
                <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-3 rounded-3 mb-2 border" id="list-kel-${idBaru}">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-3 overflow-hidden" style="width: 40px; height: 40px; font-weight: bold;">${imgHTML}</div>
                        <div>
                            <h6 class="mb-0 small fw-bold text-dark" id="list-nama-${idBaru}">${nama}</h6>
                            <span class="small text-muted" id="list-hub-${idBaru}" style="font-size: 0.75rem;">${hubungan}</span>
                        </div>
                    </div>
                    <div>
                        <i class="bi bi-pencil-square text-primary fs-5 me-2" style="cursor:pointer;" onclick="bukaFormEditKeluarga(${idBaru}, ${currIndex})" title="Edit Anggota"></i>
                        <i class="bi bi-trash text-danger fs-5" style="cursor:pointer;" onclick="hapusKeluarga(${idBaru}, ${currIndex})" title="Hapus Anggota"></i>
                    </div>
                </li>
            `;
            listContainer.insertAdjacentHTML('beforeend', newListItem);

            const tabContainer = document.getElementById('familyTabsContainer');
            const newTab = `<div class="kk-tab flex-shrink-0" id="tab-kel-${idBaru}" onclick="switchProfileTab(${currIndex}, this)">${namaPanggilan}</div>`;
            tabContainer.insertAdjacentHTML('beforeend', newTab);

            toggleFormKeluarga();
        } else {
            alert("Gagal menyimpan ke database! Error: " + data.pesan);
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerText = oldText;
        console.error("Error:", error);
        alert("Gagal koneksi ke server!");
    });
}

function bukaFormEditKeluarga(idKeluarga, index) {
    const data = dataKeluarga[index];
    if(!data) return;

    document.getElementById('formTambahKeluarga').classList.add('d-none');
    document.getElementById('btnBukaFormKeluarga').classList.add('d-none');
    document.getElementById('formEditKeluarga').classList.remove('d-none');

    document.getElementById('editKelId').value = data.id;
    document.getElementById('editKelIndex').value = index;
    document.getElementById('editKelNama').value = data.nama;
    document.getElementById('editKelNik').value = data.nik !== '-' ? data.nik : '';
    document.getElementById('editKelTglLahir').value = data.tanggal_lahir;
    document.getElementById('editKelAlamat').value = data.alamat !== '-' ? data.alamat : '';
    document.getElementById('editKelBpjs').value = data.bpjs !== '-' ? data.bpjs : '';
    
    let darahVal = data.darah !== '-' ? data.darah : '-';
    document.getElementById('editKelGolDarah').value = darahVal;
    let menuDarah = document.getElementById('editKelGolDarah').nextElementSibling;
    menuDarah.querySelector('span').innerText = darahVal !== '-' ? darahVal : 'Pilih Golongan Darah...';
    
    document.getElementById('editKelHubungan').value = data.hubungan;
    let menuHub = document.getElementById('editKelHubungan').nextElementSibling;
    menuHub.querySelector('span').innerText = data.hubungan;
    
    if(data.foto && data.foto !== '') {
        document.getElementById('previewEditFotoKel').src = data.foto;
        document.getElementById('previewEditFotoKel').style.display = 'block';
        document.getElementById('iconEditDefaultKel').style.display = 'none';
    } else {
        document.getElementById('previewEditFotoKel').style.display = 'none';
        document.getElementById('iconEditDefaultKel').style.display = 'block';
    }
}

function batalEditKeluarga() {
    document.getElementById('formEditKeluarga').classList.add('d-none');
    document.getElementById('btnBukaFormKeluarga').classList.remove('d-none');
}

function simpanEditKeluarga() {
    const id = document.getElementById('editKelId').value;
    const index = document.getElementById('editKelIndex').value;
    const nama = document.getElementById('editKelNama').value;
    const nik = document.getElementById('editKelNik').value;
    const tglLahir = document.getElementById('editKelTglLahir').value;
    const golDarah = document.getElementById('editKelGolDarah').value;
    const hubungan = document.getElementById('editKelHubungan').value;
    const alamat = document.getElementById('editKelAlamat').value;
    const bpjs = document.getElementById('editKelBpjs').value; 
    const fotoFile = document.getElementById('editKelFoto').files[0];

    if (nama === '' || nik === '' || hubungan === '' || tglLahir === '') {
        alert("Harap lengkapi data wajib (Nama, NIK, Tanggal Lahir, Hubungan)!");
        return;
    }

    const btn = document.getElementById('btnSimpanEditKel');
    const oldText = btn.innerText;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    btn.disabled = true;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('nama', nama);
    formData.append('nik', nik);
    formData.append('tanggal_lahir', tglLahir);
    formData.append('gol_darah', golDarah);
    formData.append('alamat', alamat);
    formData.append('bpjs', bpjs);
    formData.append('hubungan', hubungan);
    if(fotoFile) { formData.append('foto_keluarga', fotoFile); }

    fetch('edit_keluarga.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerText = oldText;

        if(data.status === 'sukses'){
            let umurKeluarga = "-";
            if (tglLahir) {
                const birthDate = new Date(tglLahir);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
                umurKeluarga = age + " Tahun";
            }

            dataKeluarga[index].nama = nama;
            dataKeluarga[index].nik = nik;
            dataKeluarga[index].tanggal_lahir = tglLahir;
            dataKeluarga[index].darah = golDarah;
            dataKeluarga[index].alamat = alamat;
            dataKeluarga[index].bpjs = bpjs;
            dataKeluarga[index].hubungan = hubungan;
            dataKeluarga[index].umur = umurKeluarga;
            if(data.foto !== '') { dataKeluarga[index].foto = '../uploads/' + data.foto; }

            document.getElementById('list-nama-'+id).innerText = nama;
            document.getElementById('list-hub-'+id).innerText = hubungan;
            
            if(data.foto !== ''){
                let imgEl = document.getElementById('list-img-'+id);
                if(imgEl) {
                    imgEl.src = '../uploads/' + data.foto;
                } else {
                    let initEl = document.getElementById('list-init-'+id);
                    if(initEl){
                        let parent = initEl.parentElement;
                        initEl.remove();
                        parent.innerHTML = `<img src="../uploads/${data.foto}" id="list-img-${id}" style="width:100%; height:100%; object-fit:cover;">`;
                    }
                }
            } else {
                let initEl = document.getElementById('list-init-'+id);
                if(initEl){ initEl.innerText = nama.charAt(0).toUpperCase(); }
            }

            const tabName = document.getElementById('tab-kel-'+id);
            if(tabName) tabName.innerText = nama.split(" ")[0];

            if(tabName && tabName.classList.contains('active')){
                switchProfileTab(index, tabName);
            }

            batalEditKeluarga();
            alert("Data keluarga berhasil diupdate!");
        } else {
            alert("Gagal mengupdate data: " + data.pesan);
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerText = oldText;
        console.error("Error:", err);
        alert("Gagal koneksi ke server!");
    });
}

function hapusKeluarga(idKeluarga, arrayIndex) {
    if(!confirm("Apakah Anda yakin ingin menghapus data anggota keluarga ini?")) return;

    const formData = new FormData();
    formData.append('id', idKeluarga);

    fetch('hapus_keluarga.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if(data.trim() === 'sukses') {
            const listItem = document.getElementById('list-kel-' + idKeluarga);
            if(listItem) listItem.remove();

            const tabItem = document.getElementById('tab-kel-' + idKeluarga);
            if(tabItem) {
                if(tabItem.classList.contains('active')) {
                    const tabUtama = document.querySelector('#familyTabsContainer .kk-tab:first-child');
                    switchProfileTab(0, tabUtama);
                }
                tabItem.remove();
            }

            dataKeluarga[arrayIndex] = null;
        } else {
            alert("Gagal menghapus data dari sistem!");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Gagal menghapus! Periksa koneksi internet.");
    });
}

function switchProfileTab(index, elem) {
    if(!dataKeluarga[index]) return; 

    document.querySelectorAll('.kk-tab').forEach(tab => tab.classList.remove('active'));
    elem.classList.add('active');

    const profilDipilih = dataKeluarga[index];

    const dashboardTitle = document.getElementById('ashNameTitle');
    if(dashboardTitle) dashboardTitle.innerText = profilDipilih.nama.split(" ")[0];

    document.getElementById('sidebarName').innerText = profilDipilih.nama;
    document.getElementById('sidebarNik').innerText = profilDipilih.nik || '-';
    document.getElementById('sidebarRm').innerText = profilDipilih.rm || '-';
    document.getElementById('sidebarUmur').innerText = profilDipilih.umur || '-';
    document.getElementById('sidebarDarah').innerText = profilDipilih.darah || '-';
    
    const sidebarAlamat = document.getElementById('sidebarAlamat');
    if(sidebarAlamat) sidebarAlamat.innerText = profilDipilih.alamat || '-';

    const sidebarBpjs = document.getElementById('sidebarBpjs');
    if(sidebarBpjs) sidebarBpjs.innerText = profilDipilih.bpjs || '-'; 

    const badge = document.getElementById('sidebarStatusBadge');
    badge.innerText = profilDipilih.hubungan;
    
    const mainPic = document.getElementById('mainProfilePic');
    const mainInit = document.getElementById('mainProfileInitials');
    
    if(profilDipilih.foto && profilDipilih.foto !== '') {
        mainPic.src = profilDipilih.foto;
        mainPic.style.display = 'block';
        mainInit.style.display = 'none';
    } else {
        mainPic.style.display = 'none';
        mainInit.style.display = 'block';
        mainInit.innerText = profilDipilih.nama.charAt(0).toUpperCase();
    }

    if(index === 0) {
        document.getElementById('sidebarEmail').innerText = document.getElementById('settingsEmailInput').value;
    } else {
        document.getElementById('sidebarEmail').innerText = "Akun Tanggungan";
    }
}

function updateAuthData() {
    const namaBaru     = document.getElementById('settingsNameInput').value;
    const nikBaru      = document.getElementById('settingsNikInput').value;
    const tglLahirBaru = document.getElementById('settingsTglLahirInput').value;
    const golDarahBaru = document.getElementById('settingsGolDarahInput').value;
    const bpjsBaru     = document.getElementById('settingsBpjsInput').value;
    const alamatBaru   = document.getElementById('settingsAlamatInput').value;
    const emailBaru    = document.getElementById('settingsEmailInput').value;
    const passwordBaru = document.getElementById('settingsPasswordInput').value;
    const fotoFile     = document.getElementById('uploadProfile').files[0];
    
    const btn = document.getElementById('btnSimpanAkun');
    const oldText = 'Simpan Perubahan Akun';

    if (namaBaru.trim() === '' || emailBaru.trim() === '') {
        btn.innerHTML = '<i class="bi bi-x-circle me-2"></i>Nama & Email wajib diisi!';
        btn.classList.replace('btn-teal', 'btn-danger');
        setTimeout(() => { btn.innerHTML = oldText; btn.classList.replace('btn-danger', 'btn-teal'); }, 3000);
        return;
    }

    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan...';
    btn.disabled = true;

    const formData = new FormData();
    formData.append('nama', namaBaru);
    formData.append('nik', nikBaru);
    formData.append('tanggal_lahir', tglLahirBaru);
    formData.append('gol_darah', golDarahBaru);
    formData.append('no_bpjs', bpjsBaru);
    formData.append('alamat', alamatBaru);
    formData.append('email', emailBaru);
    formData.append('password', passwordBaru);
    if (fotoFile) { formData.append('foto_profil', fotoFile); }

    fetch('update_profil.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        btn.disabled = false;
        if (data.trim() === 'sukses') {
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Berhasil Disimpan!';
            btn.classList.replace('btn-teal', 'btn-success');
            
            document.getElementById('settingsPasswordInput').value = ''; 
            
            document.getElementById('sidebarName').innerText = namaBaru; 
            document.getElementById('sidebarNik').innerText = nikBaru || '-';
            document.getElementById('sidebarDarah').innerText = golDarahBaru || '-';
            document.getElementById('sidebarBpjs').innerText = bpjsBaru || '-';
            document.getElementById('sidebarAlamat').innerText = alamatBaru || '-';
            
            const ashName = document.getElementById('ashNameTitle');
            if(ashName) ashName.innerText = namaBaru.split(" ")[0];
            
            dataKeluarga[0].nama = namaBaru;
            dataKeluarga[0].nik = nikBaru;
            dataKeluarga[0].tanggal_lahir = tglLahirBaru;
            dataKeluarga[0].darah = golDarahBaru;
            dataKeluarga[0].alamat = alamatBaru;
            dataKeluarga[0].bpjs = bpjsBaru;
            
            let famMainName = document.querySelector('#settingsFamilyList li:first-child h6');
            if(famMainName) famMainName.innerText = namaBaru;

            setTimeout(() => { btn.innerHTML = oldText; btn.classList.replace('btn-success', 'btn-teal'); }, 3000);
        } else {
            alert("GAGAL MENYIMPAN! Alasan: \n" + data);
            btn.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Gagal Menyimpan';
            btn.classList.replace('btn-teal', 'btn-danger');
            setTimeout(() => { btn.innerHTML = oldText; btn.classList.replace('btn-danger', 'btn-teal'); }, 4000);
        }
    })
    .catch(error => {
        console.error('Error Jaringan:', error);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan';
        btn.classList.replace('btn-teal', 'btn-warning');
        setTimeout(() => { btn.innerHTML = oldText; btn.classList.replace('btn-warning', 'btn-teal'); }, 3000);
    });
}