// ==========================================
// KUNCI SUPABASE
// ==========================================
const SUPABASE_URL = 'https://pvitszavfhmamokazmjb.supabase.co';
const SUPABASE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InB2aXRzemF2ZmhtYW1va2F6bWpiIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODAyMjA5MzgsImV4cCI6MjA5NTc5NjkzOH0.UyKmsvLRZLvrmqr5QVxL7mgJKJCXLJ9_ErRVAv4ygQA';

window.supabase = supabase.createClient(SUPABASE_URL, SUPABASE_KEY);

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
// PROSES REGISTRASI 
// ==========================================
document.getElementById('btnRegister').addEventListener('click', async function(e) {
    e.preventDefault(); 

    const name = document.getElementById("regName").value.trim();
    const email = document.getElementById("regEmail").value.trim();
    const password = document.getElementById("regPassword").value.trim();
    const termsChecked = document.getElementById("regTerms").checked;
    const btn = document.getElementById("btnRegister");

    if (!name || !email || !password) return alert("❌ Mohon isi semua kolom data!");
    if (password.length < 6) return alert("❌ Kata sandi minimal 6 karakter.");
    if (!termsChecked) return alert("❌ Centang persetujuan terlebih dahulu.");

    const originalText = btn.innerText;
    btn.innerText = "Memproses..."; btn.disabled = true;

    try {
        const response = await fetch(`${SUPABASE_URL}/auth/v1/signup`, {
            method: 'POST',
            headers: { 
                'apikey': SUPABASE_KEY,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                email: email, 
                password: password,
                data: { full_name: name }
            })
        });

        const result = await response.json();

        if (response.ok) {
            alert("✅ Berhasil Mendaftar! Silakan masuk menggunakan email dan sandi barumu.");
            toggleForms('login');
            document.getElementById('loginEmail').value = email; 
            document.getElementById('regPassword').value = '';
        } else {
            alert("❌ Registrasi Gagal: " + (result.msg || result.message || "Email sudah terdaftar."));
        }
    } catch (error) {
        alert("🚨 Gagal menghubungi server Supabase.");
    } finally {
        btn.innerText = originalText; btn.disabled = false;
    }
});

// ==========================================
// PROSES LOGIN (UX Interaktif - Tanpa Alert)
// ==========================================
document.getElementById('btnLogin').addEventListener('click', async function(e) {
    e.preventDefault(); 

    const role = document.getElementById("loginUserType").value;
    const email = document.getElementById("loginEmail").value.trim();
    const password = document.getElementById("loginPassword").value.trim();
    const termsChecked = document.getElementById("loginTerms").checked;
    const btn = document.getElementById("btnLogin");

    if (!email || !password) {
        btn.innerText = "Email/Sandi Kosong!";
        setTimeout(() => { btn.innerText = "Masuk Sistem"; }, 2000);
        return;
    }
    if (!termsChecked) {
        btn.innerText = "Centang Persetujuan!";
        setTimeout(() => { btn.innerText = "Masuk Sistem"; }, 2000);
        return;
    }

    btn.innerText = "Mencocokkan Data..."; btn.disabled = true;

    try {
        const response = await fetch(`${SUPABASE_URL}/auth/v1/token?grant_type=password`, {
            method: 'POST',
            headers: { 
                'apikey': SUPABASE_KEY,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email: email, password: password })
        });

        const data = await response.json();

        if (response.ok && data.access_token) {
            localStorage.setItem('mediflow_token', data.access_token);
            localStorage.setItem('mediflow_refresh', data.refresh_token); 
            localStorage.setItem('mediflow_role', role);

            // Path redirect sudah diperbaiki
            if (role === "pasien") {
                window.location.href = "../pasien/dashboard.pasien.html";
            } else if (role === "dokter") {
                window.location.href = "../dokter/dashboard.dokter.html";
            } else {
                window.location.href = "../admin/dashboard.admin.html";
            }x
        }
    } catch (error) {
        btn.innerText = "Gagal: Server Error!";
    } finally {
        setTimeout(() => {
            if(btn.innerText.includes("Gagal")) {
                btn.innerText = "Masuk Sistem"; 
                btn.disabled = false;
            }
        }, 3000);
    }
});

// ==========================================
// PROSES LUPA KATA SANDI
// ==========================================
async function bukaModalLupaSandi() {
    const emailReset = prompt("Masukkan alamat email Anda yang terdaftar untuk mereset kata sandi:");
    if (emailReset) {
        try {
            const response = await fetch(`${SUPABASE_URL}/auth/v1/recover`, {
                method: 'POST',
                headers: { 
                    'apikey': SUPABASE_KEY,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: emailReset })
            });

            if (response.ok) {
                alert("✅ Tautan reset kata sandi telah dikirim ke email Anda!");
            } else {
                alert("❌ Gagal mengirim tautan.");
            }
        } catch (error) {
            alert("🚨 Gagal menghubungi server Supabase.");
        }
    }
}

// ==========================================
// SLIDER LOGIKA (ANIMASI GESER INFINITE LOOP)
// ==========================================
// Variabel dideklarasikan HANYA 1 KALI di sini.
const sliderTrack = document.getElementById('slider-track');

if (sliderTrack) {
    // Path gambar disesuaikan agar mengambil folder wallpaper 
    const images = [
        "../../wallpaper/rs1.jpg", 
        "../../wallpaper/rs2.jpg", 
        "../../wallpaper/rs3.jpg", 
        "../../wallpaper/rs4.jpg", 
        "../../wallpaper/rs5.png" 
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