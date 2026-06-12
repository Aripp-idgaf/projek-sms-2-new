// ====================================================
// INISIALISASI SUPABASE
// ====================================================
const SUPABASE_URL = 'https://pvitszavfhmamokazmjb.supabase.co';
const SUPABASE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InB2aXRzemF2ZmhtYW1va2F6bWpiIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODAyMjA5MzgsImV4cCI6MjA5NTc5NjkzOH0.UyKmsvLRZLvrmqr5QVxL7mgJKJCXLJ9_ErRVAv4ygQA';

window.supabase = supabase.createClient(SUPABASE_URL, SUPABASE_KEY);

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

// ====================================================
// SCRIPT PROFIL & SINKRONISASI (DENGAN AUTO-HEAL DATA)
// ====================================================
let currentUserSessionId = null;
let familyProfiles = [];

async function loadUserProfile() {
    try {
        const token = localStorage.getItem('mediflow_token');
        const role = localStorage.getItem('mediflow_role');

        if (!token || role !== 'pasien') {
            localStorage.clear();
            window.location.href = "../login/index.html"; 
            return;
        }

        const refresh = localStorage.getItem('mediflow_refresh') || token;
        await window.supabase.auth.setSession({
            access_token: token,
            refresh_token: refresh
        });

        const { data: { user }, error: userError } = await window.supabase.auth.getUser();

        if (userError || !user) {
            localStorage.clear();
            window.location.href = "../login/index.html"; 
            return;
        }

        currentUserSessionId = user.id;
        document.getElementById('deviceInfo').innerText = navigator.userAgent.substring(0, 30) + "...";
        
        // Ekstraksi Metadata Registrasi dari Supabase Auth
        const meta = user.user_metadata || {};
        const regName = meta.full_name || "Pasien Baru";
        const regNik = meta.nik || "-";
        const regUmur = meta.umur || "-";
        const regGolDarah = meta.gol_darah || "-";
        const regAlamat = meta.alamat || "-";

        // Ambil Data dari Tabel Profiles
        const { data: profiles, error: profileError } = await window.supabase
            .from('profiles')
            .select('*')
            .eq('user_id', currentUserSessionId);

        if (profileError) { 
            console.error("Error ambil profil", profileError); 
            return; 
        }

        familyProfiles = profiles || [];
        let mainProfile = familyProfiles.find(p => p.relationship === 'Anak');
        
        // JIKA BELUM ADA SAMA SEKALI (Buat Baru)
        if (!mainProfile) {
            mainProfile = {
                name: regName,
                relationship: 'Anak',
                nomor_rm: 'RM-' + Math.floor(Math.random() * 900000 + 100000), 
                nik: regNik,
                nomor_bpjs: '-',
                blood_type: regGolDarah,
                umur: regUmur,
                alamat: regAlamat,
                height: '-',
                weight: '-'
            };
            familyProfiles.push(mainProfile);

            window.supabase.from('profiles').insert([{
                user_id: currentUserSessionId,
                name: regName,
                relationship: 'Anak',
                nomor_rm: mainProfile.nomor_rm,
                nik: regNik,
                nomor_bpjs: '-',
                blood_type: regGolDarah,
                umur: parseInt(regUmur) || null,
                alamat: regAlamat
            }]).then(() => console.log("Profil Baru Berhasil Dibuat di Database"));

        } else {
            // SINKRONISASI OTOMATIS: Jika login dengan akun lama yang NIK/Darahnya masih kosong,
            // tarik datanya dari metadata (pendaftaran) dan perbarui databasenya secara permanen.
            let needsDbUpdate = false;

            if ((!mainProfile.nik || mainProfile.nik === '-') && regNik !== '-') { 
                mainProfile.nik = regNik; needsDbUpdate = true; 
            }
            if ((!mainProfile.blood_type || mainProfile.blood_type === '-') && regGolDarah !== '-') { 
                mainProfile.blood_type = regGolDarah; needsDbUpdate = true; 
            }
            if ((!mainProfile.umur || mainProfile.umur === '-') && regUmur !== '-') { 
                mainProfile.umur = regUmur; needsDbUpdate = true; 
            }
            if ((!mainProfile.alamat || mainProfile.alamat === '-') && regAlamat !== '-') { 
                mainProfile.alamat = regAlamat; needsDbUpdate = true; 
            }

            // Jalankan update ke Supabase jika ada data yang kosong sebelumnya
            if (needsDbUpdate) {
                window.supabase.from('profiles').update({
                    nik: mainProfile.nik,
                    blood_type: mainProfile.blood_type,
                    umur: parseInt(mainProfile.umur) || null,
                    alamat: mainProfile.alamat
                }).eq('id', mainProfile.id).then(() => console.log("Auto-Sync Akun Lama Selesai"));
            }
        }

        // Jalankan Update Tampilan UI
        if (mainProfile) {
            updateRightPanelUI(mainProfile, user.email);
            document.getElementById('settingsNameInput').value = mainProfile.name;
            document.getElementById('settingsEmailInput').value = user.email;
            
            if(mainProfile.nomor_bpjs && mainProfile.nomor_bpjs !== '-') {
                document.getElementById('inputSetBpjs').value = mainProfile.nomor_bpjs;
                document.getElementById('statusBpjs').className = "badge bg-success rounded-pill";
                document.getElementById('statusBpjs').innerText = "Terhubung";
            }

            if(familyProfiles.some(p => p.relationship === 'Ayah')) document.getElementById('tabAyah').classList.remove('d-none');
            if(familyProfiles.some(p => p.relationship === 'Ibu')) document.getElementById('tabIbu').classList.remove('d-none');
        }
    } catch (err) { 
        console.error("Error Load:", err); 
        localStorage.clear();
        window.location.href = "../login/index.html"; 
    }
}

function updateRightPanelUI(profile, email) {
    if(!profile) return;
    
    if(profile.relationship === 'Anak') {
        document.getElementById('ashNameTitle').innerText = profile.name ? profile.name.split(' ')[0] : 'Pasien';
    }

    // UPDATE SEMUA TAMPILAN SESUAI ID HTML
    document.getElementById('ashNameProfile').innerText = profile.name;
    document.getElementById('ashEmail').innerText = email || "";
    document.getElementById('ashRm').innerText = profile.nomor_rm || '-';
    document.getElementById('ashNik').innerText = profile.nik || '-';
    document.getElementById('ashUmur').innerText = profile.umur ? profile.umur + ' Thn' : '-';
    document.getElementById('ashAlamat').innerText = profile.alamat || '-';
    document.getElementById('ashBpjs').innerText = profile.nomor_bpjs || '-';
    
    document.getElementById('ashBlood').innerText = profile.blood_type || '-';
    document.getElementById('ashHeight').innerText = profile.height && profile.height !== '-' ? profile.height + ' cm' : '-';
    document.getElementById('ashWeight').innerText = profile.weight && profile.weight !== '-' ? profile.weight + ' kg' : '-';

    const initials = profile.name ? profile.name.match(/\b\w/g) || [] : ['P'];
    const avatarText = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
    document.querySelector('.avatar-circle').innerText = avatarText;
}

function switchProfileTab(relationship, element) {
    document.querySelectorAll('.kk-tab').forEach(el => el.classList.remove('active'));
    element.classList.add('active');

    const targetProfile = familyProfiles.find(p => p.relationship === relationship);
    if(targetProfile) {
        updateRightPanelUI(targetProfile, relationship === 'Anak' ? document.getElementById('settingsEmailInput').value : "Keluarga Terhubung");
    }
}

function loadKeluargaSettings() {
    const listContainer = document.getElementById('settingsFamilyList');
    listContainer.innerHTML = ''; 

    familyProfiles.forEach(prof => {
        const delBtn = prof.relationship === 'Anak' ? 
            `<span class="badge bg-secondary">Utama</span>` : 
            `<i class="bi bi-trash text-danger" style="cursor:pointer" onclick="hapusAnggotaKeluarga('${prof.id}')"></i>`;
        
        listContainer.innerHTML += `
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 small">
                ${prof.name} (${prof.relationship})
                ${delBtn}
            </li>
        `;
    });
}

async function tambahAnggotaKeluarga() {
    const rel = prompt("Pilih status: ketik 'Ayah' atau 'Ibu'");
    if(rel !== 'Ayah' && rel !== 'Ibu') { alert("Dibatalkan atau input salah."); return; }
    
    const nama = prompt(`Masukkan nama ${rel} Anda:`);
    if(!nama) return;

    const newRm = 'RM-' + Math.floor(Math.random() * 900000 + 100000);

    const { data, error } = await window.supabase.from('profiles').insert([{
        user_id: currentUserSessionId,
        name: nama,
        relationship: rel,
        nomor_rm: newRm
    }]).select();

    if(!error) {
        alert("Berhasil menambahkan anggota keluarga!");
        familyProfiles.push(data[0]); 
        loadKeluargaSettings(); 
        document.getElementById('tab' + rel).classList.remove('d-none'); 
    } else {
        alert("Gagal menambahkan: " + error.message);
    }
}

async function hapusAnggotaKeluarga(profId) {
    if(!confirm("Yakin ingin menghapus anggota ini?")) return;
    
    const profObj = familyProfiles.find(p => p.id === profId);
    
    const { error } = await window.supabase.from('profiles').delete().eq('id', profId);
    if(!error) {
        familyProfiles = familyProfiles.filter(p => p.id !== profId);
        loadKeluargaSettings();
        document.getElementById('tab' + profObj.relationship).classList.add('d-none'); 
        document.querySelector('.kk-tab').click(); 
    }
}

async function updateAuthData() {
    const newEmail = document.getElementById('settingsEmailInput').value.trim();
    const newPass = document.getElementById('settingsPasswordInput').value.trim();
    
    const updates = {};
    if(newEmail) updates.email = newEmail;
    if(newPass && newPass.length >= 6) updates.password = newPass;

    if(Object.keys(updates).length > 0) {
        const { error } = await window.supabase.auth.updateUser(updates);
        if(error) alert("Gagal update: " + error.message);
        else {
            alert("Autentikasi akun berhasil diubah!");
            document.getElementById('settingsPasswordInput').value = '';
        }
    }
}

async function simpanDataBPJS() {
    const bpjsNumber = document.getElementById('inputSetBpjs').value.trim();
    if(!bpjsNumber) return;

    const { error } = await window.supabase.from('profiles')
        .update({ nomor_bpjs: bpjsNumber })
        .eq('user_id', currentUserSessionId)
        .eq('relationship', 'Anak');

    if(!error) {
        alert("BPJS berhasil tersinkronisasi!");
        document.getElementById('ashBpjs').innerText = bpjsNumber;
        document.getElementById('statusBpjs').className = "badge bg-success rounded-pill";
        document.getElementById('statusBpjs').innerText = "Terhubung";
        
        const childIdx = familyProfiles.findIndex(p => p.relationship === 'Anak');
        if(childIdx !== -1) familyProfiles[childIdx].nomor_bpjs = bpjsNumber;
    }
}

document.addEventListener('DOMContentLoaded', loadUserProfile);

async function logoutSession() {
    localStorage.removeItem('mediflow_token');
    localStorage.removeItem('mediflow_refresh');
    localStorage.removeItem('mediflow_role');
    window.location.href = "../login/index.html"; 
}

function togglePanel() {
    const panel = document.getElementById('rightPanel');
    panel.classList.toggle('closed'); 
}

// ====================================================
// SCRIPT DEEP LINKING (Routing dari Landing Page)
// ====================================================
window.addEventListener('load', () => {
    // Mengecek apakah ada hash di URL (misal: pasien.html#view-jadwal)
    const hash = window.location.hash.substring(1); 
    
    // Jika ada hash dan id tersebut terdaftar sebagai menu
    if (hash && document.getElementById(hash)) {
        // Beri jeda sedikit agar DOM dan Supabase termuat sempurna
        setTimeout(() => {
            switchView(hash);
            // Hapus hash dari URL agar terlihat bersih
            history.replaceState(null, null, ' ');
        }, 500); 
    }
});