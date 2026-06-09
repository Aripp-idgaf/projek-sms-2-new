let activePatient = null; 

        function triggerMercon() {
            console.log("Welcome effect triggered");
        }

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

        function updateDate() {
            const today = new Date();
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const formattedDate = `${today.getDate()} ${months[today.getMonth()]} ${today.getFullYear()}`;
            const bannerBadge = document.getElementById('dynamic-banner-date');
            if (bannerBadge) bannerBadge.innerHTML = `<i class="bi bi-calendar-check me-2"></i>${formattedDate}`;
            const locationDate = document.getElementById('dynamic-banner-location-date');
            if (locationDate) {
                let sesi = 'Sesi Pagi';
                const hour = today.getHours();
                if (hour >= 11 && hour < 15) sesi = 'Sesi Siang';
                else if (hour >= 15 && hour < 18) sesi = 'Sesi Sore';
                else if (hour >= 18) sesi = 'Sesi Malam';
                locationDate.innerHTML = `<i class="bi bi-cloud-sun fs-5"></i> Semarang, ${formattedDate} | ${sesi}`;
            }
            const jadwalDate = document.getElementById('dynamic-jadwal-date');
            if (jadwalDate) jadwalDate.innerHTML = `Periode Pekan Ini (${months[today.getMonth()]} ${today.getFullYear()})`;
        }

        function markActiveDay() {
            const today = new Date();
            const dayIndex = today.getDay(); 
            const dayMap = { 1: 'SENIN', 2: 'SELASA', 3: 'RABU', 4: 'KAMIS', 5: 'JUMAT', 6: 'SABTU', 0: 'MINGGU' };
            const activeDayName = dayMap[dayIndex];
            if (!activeDayName) return;
            
            document.querySelectorAll('#jadwalDaysContainer .day-column-card').forEach(card => {
                card.classList.remove('active-day');
            });
            
            const targetCol = document.querySelector(`#jadwalDaysContainer .col[data-day="${activeDayName}"] .day-column-card`);
            if (targetCol) {
                targetCol.classList.add('active-day');
            }
        }

        document.addEventListener("DOMContentLoaded", () => { 
            updateDate(); 
            setTimeout(() => {
                triggerMercon();
                playDoctorWelcomeEffect();
                playBubbleEffect();
            }, 500);
            const doctorTrigger = document.getElementById('hero-doctor-trigger');
            if(doctorTrigger) doctorTrigger.addEventListener('mouseenter', triggerMercon);
        });

        function triggerConflictSimulation() { alert("⚠️ VALIDATION SYSTEM ALERT: Jam bentrok dengan jadwal aktif!"); }
        function triggerBlockSmartSimulation() { alert("🔒 SMART BLOCKING: Gagal. Sesi berada pada jam Istirahat Dokter."); }
        function triggerHistoryPopUpSim() { alert("📋 REKAM MEDIS TERAKHIR - Diagnosa: Dermatitis Kontak Alergi (L23.9)\nTerapi: Hidrokortison Krim 1%."); }

        function switchView(viewId) {
            if(viewId === 'view-dashboard') {
                const docWrapper = document.querySelector('.hero-doctor-wrapper');
                if(docWrapper) {
                    docWrapper.style.animation = 'none';
                    docWrapper.offsetHeight;
                    docWrapper.style.animation = 'fadeInOnly 0.6s ease forwards';
                }
                setTimeout(() => {
                    triggerMercon();
                    playDoctorWelcomeEffect();
                    playBubbleEffect();
                }, 150);
            }
            document.querySelectorAll('.view-section').forEach(view => view.classList.add('d-none'));
            document.getElementById(viewId).classList.remove('d-none');
            
            if(viewId === 'view-pasien') {
                if(activePatient !== null) {
                    document.getElementById('pasien-empty-state').classList.add('d-none');
                    document.getElementById('pasien-active-state').classList.remove('d-none');
                } else {
                    document.getElementById('pasien-empty-state').classList.remove('d-none');
                    document.getElementById('pasien-active-state').classList.add('d-none');
                }
            }
            if(viewId === 'view-jadwal') {
                markActiveDay();
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function periksaPasien(id, name, reason, category) {
            activePatient = { id: id, name: name, reason: reason, category: category };
            document.getElementById('emr-name').textContent = name;
            document.getElementById('emr-reason').value = `Pasien mengeluhkan gejala terkait ${reason}.`;
            document.querySelectorAll('.queue-item-pasien').forEach(el => {
                el.classList.remove('active', 'opacity-75');
                if(el.id !== 'queue-' + id) el.classList.add('opacity-75');
            }); 
            const activeQueue = document.getElementById('queue-' + id);
            if(activeQueue) activeQueue.classList.add('active');
            switchView('view-pasien');
        }

        function resetPeriksa() {
            activePatient = null;
            document.querySelectorAll('.queue-item-pasien').forEach(el => el.classList.remove('active', 'opacity-75'));
            switchView('view-pasien');
        }

        function selesaiPeriksa() {
            alert('Rekam medis berhasil disimpan ke Database!');
            activePatient = null;
            switchView('view-dashboard'); 
        }
        
        function logout() { 
            if(confirm('Keluar sistem portal?')) {
                window.location.href = '../../index.html'; 
            }
        }

        function updateClock() {
            const now = new Date();
            document.getElementById('realtime-clock').textContent = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0') + ' WIB';
        }
        setInterval(updateClock, 1000); updateClock(); 
        
        function showDetailRiwayat(nama, rm, tipe, tgl, keluhan, diagnosa, terapi, golDarah) {
            let inisial = nama.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();
            
            document.getElementById('modalAvatar').innerText = inisial;
            document.getElementById('modalNama').innerText = nama;
            document.getElementById('modalRM').innerText = rm;
            document.getElementById('modalDocId').innerText = "DOC-" + rm.split('-')[1];
            document.getElementById('modalTipe').innerText = tipe;
            document.getElementById('modalTgl').innerText = tgl;
            document.getElementById('modalKeluhan').innerText = keluhan;
            document.getElementById('modalDiagnosa').innerText = diagnosa;
            document.getElementById('modalTerapi').innerText = terapi;
            document.getElementById('modalGolDarah').innerText = golDarah;
            
            const detailModal = new bootstrap.Modal(document.getElementById('modalDetailRiwayat'));
            detailModal.show();
        }
        
        function toggleDoctorStatus() {
            const isChecked = document.getElementById('doctorStatusToggle').checked;
            const statusLabel = document.getElementById('statusLabel');
            
            if(isChecked) {
                statusLabel.textContent = "Menerima Pasien";
                statusLabel.classList.remove('status-off');
                statusLabel.classList.add('status-on');
            } else {
                statusLabel.textContent = "Sedang Istirahat";
                statusLabel.classList.remove('status-on');
                statusLabel.classList.add('status-off');
            }
        }

        // FUNGSI BARU UNTUK TOGGLE PENGUMUMAN ("...Selengkapnya")
        function togglePengumuman(btn) {
            const textElement = document.getElementById('teksPengumuman');
            if(textElement.classList.contains('expanded')) {
                textElement.classList.remove('expanded');
                btn.innerText = '...Selengkapnya';
            } else {
                textElement.classList.add('expanded');
                btn.innerText = 'Sembunyikan';
            }
        }