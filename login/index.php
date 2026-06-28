<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MediFlow | Medical Sign In</title>

    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        /* ===== GLOBAL ===== */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body.login-mode {
            background-image: url('../wallpaper/rs11.jpg');
            background-size: 1600px;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            margin: 0; padding: 20px; position: relative;
            font-family: 'Poppins', sans-serif;
        }
        body.login-mode::before {
            content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(ellipse at 80% 20%, rgba(100, 110, 120, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none; z-index: 0;
        }

        .login-wrapper { position: relative; z-index: 10; width: 100%; display: flex; justify-content: center; }
        .login-split {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2), 0 5px 15px rgba(0, 0, 0, 0.05);
            border-radius: 28px; overflow: hidden; background: white;
            display: flex; width: 1100px; max-width: 100%; height: 650px; position: relative;
        }

        /* ===== SISI KIRI (SLIDER) ===== */
        .log-left {
            flex: 1; position: relative; overflow: hidden; padding: 40px;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .slider-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; z-index: 0; }
        .slider-track { display: flex; width: 100%; height: 100%; }
        .slider-track img { width: 100%; height: 100%; object-fit: cover; flex-shrink: 0; }
        .slider-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(40, 45, 50, 0.6) 0%, rgba(20, 25, 30, 0.5) 100%); z-index: 1;
        }
        .cal-logo { display: flex; align-items: center; color: white; font-weight: 600; font-size: 1.4rem; position: relative; z-index: 2; text-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .cal-logo i { font-size: 2rem; margin-right: 12px; color: #38c8e6; }
        .cal-footer { position: relative; z-index: 2; color: rgba(255, 255, 255, 0.85); font-size: 0.75rem; font-weight: 400; letter-spacing: 0.3px; }

        /* ===== SISI KANAN (FORM) ===== */
        .log-right {
            flex: 1.1; padding: 45px 60px; display: flex; flex-direction: column;
            justify-content: flex-start; align-items: stretch; background: #ffffff;
            transition: opacity 0.3s ease-in-out; overflow-y: auto;
        }
        .log-right::-webkit-scrollbar { width: 6px; }
        .log-right::-webkit-scrollbar-track { background: transparent; }
        .log-right::-webkit-scrollbar-thumb { background: #d1e3ea; border-radius: 10px; }
        .log-right::-webkit-scrollbar-thumb:hover { background: #a0b8c2; }

        .form-logo { text-align: center; margin-bottom: 12px; margin-top: auto; }
        .form-logo i { font-size: 4rem; color: #38c8e6; }

        /* ===== INPUT BERSAMA ===== */
        .user-type-select, .cal-input {
            width: 100%; border-radius: 60px !important; padding: 13px 18px 13px 48px !important;
            border: 1.5px solid #e2edf2 !important; font-size: 0.85rem; color: #1e2f3a;
            background-color: #fefefe; transition: all 0.2s;
        }
        
        /* Modifikasi khusus input tanggal bawaan browser */
        input[type="date"].cal-input {
            font-family: 'Poppins', sans-serif;
            color: #4a5c66;
            cursor: text;
        }

        .user-type-select {
            appearance: none; cursor: pointer; user-select: none;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="%238aa4b0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>');
            background-repeat: no-repeat; background-position: right 18px center;
        }
        
        /* PERBAIKAN WARNA CYAN SAAT INPUT DIKLIK (FOKUS) */
        .cal-input:focus, .user-type-select.show {
            border-color: #38c8e6 !important; 
            box-shadow: none !important; 
            outline: none; 
            background-color: #fff;
        }
        .cal-input::placeholder { color: #b9cfda; font-weight: 400; }

        .input-icon { position: relative; margin-bottom: 16px; width: 100%; flex-shrink: 0; }
        .input-icon i.bi { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #a0b8c2; font-size: 1.1rem; z-index: 5; transition: color 0.2s; pointer-events: none; }
        
        /* Saat input fokus, icon di dalamnya juga ikut berubah warna (Opsional, agar makin rapi) */
        .input-icon:focus-within i.bi:not(.toggle-password) { color: #38c8e6; }

        /* Ikon mata untuk password bisa diklik */
        .input-icon i.toggle-password { left: auto; right: 18px; cursor: pointer; pointer-events: auto; }
        .input-icon i.toggle-password:hover { color: #38c8e6; }

        /* ===== DROPDOWN GOLONGAN DARAH ===== */
        .custom-dropdown-menu {
            border-radius: 16px !important; padding: 8px !important; background-color: #ffffff;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.1) !important; border: 1px solid rgba(56, 200, 230, 0.2) !important; margin-top: 8px !important;
        }
        .custom-dd-item { border-radius: 10px; padding: 10px 16px; font-size: 0.85rem; color: #4a5c66; font-weight: 500; transition: all 0.2s ease; margin-bottom: 4px; cursor: pointer; }
        .custom-dd-item:hover { background-color: rgba(56, 200, 230, 0.1) !important; color: #38c8e6 !important; transform: translateX(4px); }
        .custom-dd-item.active { background-color: #38c8e6 !important; color: #ffffff !important; font-weight: 600; box-shadow: 0 4px 10px rgba(56, 200, 230, 0.3) !important; }
        .dropdown-toggle::after { display: none !important; }

        input[type="number"]::-webkit-outer-spin-button, input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type="number"] { -moz-appearance: textfield; }

        .forgot-password { text-align: right; font-size: 0.7rem; margin-top: -6px; margin-bottom: 16px; }
        .forgot-password a { color: #8aa4b0; text-decoration: none; transition: color 0.2s; cursor: pointer; }
        .forgot-password a:hover { color: #38c8e6; text-decoration: underline; }

        .btn-cal {
            background: linear-gradient(95deg, #38c8e6, #25b3d1); color: white; border: none; width: 100%; padding: 14px;
            border-radius: 60px; font-weight: 600; transition: all 0.25s ease; margin-top: 8px; box-shadow: 0 5px 12px rgba(56, 200, 230, 0.3); flex-shrink: 0;
        }
        .btn-cal:hover:not(:disabled) { background: linear-gradient(95deg, #2ab3d1, #1c9bb5); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(56, 200, 230, 0.35); }
        .btn-cal:disabled { background: #b9cfda; cursor: not-allowed; box-shadow: none; }

        .terms-wrapper { text-align: center; font-size: 0.78rem; margin: 12px 0; flex-shrink: 0; }
        .terms-wrapper label { color: #333333; cursor: pointer; font-weight: 500; }
        .terms-wrapper a { color: #38c8e6; text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .terms-wrapper a:hover { color: #25b3d1; text-decoration: underline; }

        .signup-link { text-align: center; font-size: 0.82rem; color: #7d99a6; margin-top: 18px; margin-bottom: auto; flex-shrink: 0; }
        .signup-link a { color: #38c8e6 !important; font-weight: 600; text-decoration: none; cursor: pointer; transition: color 0.2s ease, text-decoration 0.2s ease; }
        .signup-link a:hover { color: #25b3d1 !important; text-decoration: underline; }

        .d-none { display: none !important; }

        /* ===== NOTIFIKASI ===== */
        .notif-container { width: 100%; display: flex; justify-content: center; }
        .alert-error { background-color: #f8d7da; color: #721c24; padding: 8px 15px; border-radius: 8px; font-size: 0.8rem; text-align: center; margin-bottom: 20px; border: 1px solid #f5c6cb; width: 100%; font-weight: 500; }
        .alert-success { background-color: #d4edda; color: #155724; padding: 8px 15px; border-radius: 8px; font-size: 0.8rem; text-align: center; margin-bottom: 20px; border: 1px solid #c3e6cb; width: 100%; font-weight: 500; }

        @media (max-width: 850px) {
            .login-split { flex-direction: column; height: auto; }
            .log-left { min-height: 250px; }
            .log-right { padding: 40px 30px; }
        }
    </style>
</head>

<body class="login-mode">
    <div class="login-wrapper">
        <div class="login-split">

            <!-- ===== SISI KIRI: SLIDER ===== -->
            <div class="log-left">
                <div class="slider-container">
                    <div class="slider-track" id="slider-track"></div>
                </div>
                <div class="slider-overlay"></div>
                <div class="cal-logo"><i class="bi bi-heart-pulse"></i><span>MediFlow</span></div>
                <div class="cal-footer">All Rights Reserved 2026</div>
            </div>

            <!-- ===== SISI KANAN: FORM ===== -->
            <div class="log-right" id="form-container">

                <!-- FORM LOGIN -->
                <form action="login_proses.php" method="POST" id="login-form" style="display:flex; flex-direction:column; height:100%;">
                    <div style="margin-top:auto; display:flex; flex-direction:column; align-items:center;">
                        <div class="form-logo" style="margin-bottom:12px; margin-top:0;"><i class="bi bi-heart-pulse"></i></div>
                        <h3 style="color:#1c3b44; text-align:center; margin-bottom:6px; font-weight:700; font-size:1.8rem;">Masuk</h3>
                        <p style="color:#8aa4b0; text-align:center; font-size:0.85rem; margin-bottom:24px;">Silakan masuk ke akun Anda</p>
                    </div>

                    <div class="notif-container">
                        <?php
                        if (isset($_GET['pesan'])) {
                            if ($_GET['pesan'] == "gagal") {
                                echo "<div class='alert-error'>Login gagal! Email atau kata sandi salah.</div>";
                            } elseif ($_GET['pesan'] == "belum_login") {
                                echo "<div class='alert-error'>Anda harus login terlebih dahulu.</div>";
                            } elseif ($_GET['pesan'] == "daftar_sukses") {
                                echo "<div class='alert-success'>Pendaftaran berhasil! Silakan login.</div>";
                            } elseif ($_GET['pesan'] == "email_terdaftar") {
                                echo "<div class='alert-error'>Email sudah terdaftar. Gunakan email lain.</div>";
                            }
                        }
                        ?>
                    </div>

                    <div class="input-icon">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" id="loginEmail" class="cal-input" placeholder="Alamat Email" required>
                    </div>

                    <div class="input-icon">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" id="loginPassword" class="cal-input" placeholder="Kata Sandi" required>
                        <i class="bi bi-eye-slash toggle-password" data-target="loginPassword"></i>
                    </div>

                    <div class="forgot-password"><a href="#">Lupa kata sandi?</a></div>

                    <div class="terms-wrapper">
                        <input type="checkbox" id="loginTerms" required style="margin-right:6px;">
                        <label for="loginTerms"> Saya menyetujui <a href="#">Ketentuan Penggunaan</a></label>
                    </div>

                    <button type="submit" class="btn-cal" id="btnLogin">Masuk Sistem</button>

                    <div class="signup-link">
                        Belum punya akun? <a onclick="toggleForms('register')">Daftar Sekarang</a>
                    </div>
                </form>

                <!-- FORM REGISTER -->
                <form action="registrasi_proses.php" method="POST" id="register-form" class="d-none" style="display:flex; flex-direction:column; height:100%;">
                    <div style="margin-top:auto; display:flex; flex-direction:column; align-items:center;">
                        <div class="form-logo" style="margin-bottom:12px; margin-top:0;"><i class="bi bi-person-plus"></i></div>
                        <h3 style="color:#1c3b44; text-align:center; margin-bottom:6px; font-weight:700; font-size:1.8rem;">Daftar Akun</h3>
                        <p style="color:#8aa4b0; text-align:center; font-size:0.85rem; margin-bottom:24px;">Bergabung dengan MediFlow</p>
                    </div>

                    <div class="input-icon">
                        <i class="bi bi-person-badge"></i>
                        <input type="text" name="nama" id="regName" class="cal-input" placeholder="Nama Lengkap Sesuai KTP" required>
                    </div>

                    <div class="input-icon">
                        <i class="bi bi-card-heading"></i>
                        <input type="number" name="nik" id="regNik" class="cal-input" placeholder="Nomor Induk Kependudukan (NIK)" required>
                    </div>

                    <div class="input-icon">
                        <i class="bi bi-calendar3"></i>
                        <input type="date" name="tanggal_lahir" id="regTanggalLahir" class="cal-input" required>
                    </div>

                    <div class="input-icon dropdown dropdown-custom-container">
                        <i class="bi bi-droplet"></i>
                        <select name="gol_darah" id="regGolDarah" class="d-none">
                            <option value="-" selected>Golongan Darah (Opsional)</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
                        <div class="user-type-select dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" id="displayRegGolDarah" style="color:#b9cfda;">
                            Golongan Darah (Opsional)
                        </div>
                        <ul class="dropdown-menu w-100 border-0 custom-dropdown-menu">
                            <li><a class="dropdown-item custom-dd-item active" href="#" data-value="-">Pilih Golongan Darah</a></li>
                            <li><a class="dropdown-item custom-dd-item" href="#" data-value="A">A</a></li>
                            <li><a class="dropdown-item custom-dd-item" href="#" data-value="B">B</a></li>
                            <li><a class="dropdown-item custom-dd-item" href="#" data-value="AB">AB</a></li>
                            <li><a class="dropdown-item custom-dd-item" href="#" data-value="O">O</a></li>
                        </ul>
                    </div>

                    <div class="input-icon">
                        <i class="bi bi-geo-alt"></i>
                        <input type="text" name="alamat" id="regAlamat" class="cal-input" placeholder="Alamat Lengkap" required>
                    </div>

                    <div class="input-icon">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" id="regEmail" class="cal-input" placeholder="Alamat Email (misal: @gmail.com)" required>
                    </div>

                    <div class="input-icon">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" id="regPassword" class="cal-input" placeholder="Buat Kata Sandi (Minimal 6 Karakter)" required minlength="6">
                        <i class="bi bi-eye-slash toggle-password" data-target="regPassword"></i>
                    </div>

                    <div class="terms-wrapper">
                        <input type="checkbox" id="regTerms" required style="margin-right:6px;">
                        <label for="regTerms"> Saya menyetujui <a href="#">Kebijakan Privasi Data</a></label>
                    </div>

                    <button type="submit" class="btn-cal" id="btnRegister">Buat Akun Pasien</button>

                    <div class="signup-link" style="margin-bottom:auto;">
                        Sudah memiliki akun? <a onclick="toggleForms('login')">Kembali Masuk</a>
                    </div>
                </form>

            </div><!-- /.log-right -->
        </div><!-- /.login-split -->
    </div><!-- /.login-wrapper -->

    <!-- ===== DEPENDENSI ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- ===== FILE JAVASCRIPT EKSTERNAL ===== -->
    <script src="script.js"></script>

</body>
</html>