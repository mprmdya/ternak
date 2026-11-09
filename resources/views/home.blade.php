@extends('layouts.app')

@section('content')
<div id="dashboardView">
    <div style="margin-bottom: 12px;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">Selamat datang, {{ auth()->user()->name ?? 'Pengguna' }}! 👋</h2>
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 2px;">
            @if(auth()->check() && auth()->user()->isAdmin())
                Anda masuk sebagai <strong>Administrator</strong>. Anda memiliki akses penuh ke seluruh fitur sistem.
            @else
                Anda masuk sebagai <strong>Pengguna</strong>. Anda dapat memantau kondisi lingkungan peternakan secara real-time.
            @endif
        </p>
    </div>

    <div class="hero-status safe" id="heroStatus">
        <div class="hero-icon"><i class="fas fa-shield-check" id="heroIcon"></i></div>
        <div class="hero-title" id="heroTitle">KONDISI AMAN</div>
        <div class="hero-desc" id="heroDesc">Semua parameter lingkungan peternakan berada dalam batas normal. Sirkulasi udara baik dan tidak ada indikasi penumpukan gas beracun.</div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon" style="color: #60A5FA;"><i class="fas fa-wind"></i></div>
            <div class="kpi-info">
                <h4>Kualitas Udara (AQI)</h4>
                <div class="kpi-val" id="kpiAqi">Baik</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="color: #F87171;"><i class="fas fa-temperature-half"></i></div>
            <div class="kpi-info">
                <h4>Suhu & Kelembaban</h4>
                <div class="kpi-val" id="kpiThermal"></div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="color: #FBBF24;"><i class="fas fa-biohazard"></i></div>
            <div class="kpi-info">
                <h4>Level Gas Beracun</h4>
                <div class="kpi-val" id="kpiGas">Aman</div>
            </div>
        </div>
        @if(auth()->check() && auth()->user()->isAdmin())
        <div class="kpi-card">
            <div class="kpi-icon" style="color: #8B5CF6;"><i class="fas fa-users"></i></div>
            <div class="kpi-info">
                <h4>Total User Terdaftar</h4>
                <div class="kpi-val">{{ $userCount ?? 0 }}</div>
            </div>
        </div>
        @endif
    </div>

    <div class="log-panel">
        <div class="section-title"><i class="fas fa-list-ul"></i> Log Aktivitas Terbaru</div>
        <div class="log-list" id="activityLogList">
            <div class="log-item safe">
                <div class="log-time">Sekarang</div>
                <div class="log-msg">Sistem monitoring berhasil diinisialisasi.</div>
            </div>
        </div>
    </div>
</div>

<div id="monitoringView" style="display: none;">
    <div class="sensor-grid" style="margin-bottom: 24px;">
        <div class="card"><div class="card-header"><i class="fas fa-disease"></i><span class="chip">MQ-136</span></div><div class="sub">H₂S</div><div class="value"><span id="h2sValM">0.00</span><span class="unit">ppm</span></div><div><span class="badge-sm" id="h2sBadgeM">Normal</span></div></div>
        <div class="card"><div class="card-header"><i class="fas fa-wind"></i><span class="chip">MQ-137</span></div><div class="sub">NH₃</div><div class="value"><span id="nh3ValM">0.00</span><span class="unit">ppm</span></div><div><span class="badge-sm" id="nh3BadgeM">Normal</span></div></div>
        <div class="card"><div class="card-header"><i class="fas fa-microchip"></i><span class="chip">BME280</span></div><div class="row-metrics"><div class="metric-box"><div class="metric-value"><span id="tempValM">--</span>°C</div><div class="sub">Suhu</div></div><div class="metric-box"><div class="metric-value"><span id="humValM">--</span>%</div><div class="sub">Kelembaban</div></div><div class="metric-box"><div class="metric-value"><span id="presValM">--</span>hPa</div><div class="sub">Tekanan</div></div></div></div>
        <div class="card"><div class="card-header"><i class="fas fa-smog"></i><span class="chip">PMS5003</span></div><div class="row-metrics"><div class="metric-box"><div class="metric-value"><span id="pm1ValM">0</span>µg</div><div class="sub">PM1</div></div><div class="metric-box"><div class="metric-value"><span id="pm25ValM">0</span>µg</div><div class="sub">PM2.5</div></div><div class="metric-box"><div class="metric-value"><span id="pm10ValM">0</span>µg</div><div class="sub">PM10</div></div></div></div>
    </div>
    <div class="chart-panel">
        <style>
            .time-filter-btn {
                background: var(--bg-metric);
                border: 1px solid var(--border);
                color: var(--text-secondary);
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
                cursor: pointer;
                transition: 0.2s;
            }
            .time-filter-btn.active {
                background: var(--green-main);
                color: #fff;
                border-color: var(--green-main);
                box-shadow: 0 2px 8px rgba(22,163,74,0.3);
            }
            .time-filter-btn:hover:not(.active) {
                background: #E2E8F0;
                color: var(--text-primary);
            }
        </style>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
            <div>
                <div class="section-title" style="margin-bottom: 12px;"><i class="fas fa-chart-line"></i> Analitik Sensor Historis</div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;" id="timeFilterGroup">
                    <button class="time-filter-btn active" data-time="pagi">Pagi (06-12)</button>
                    <button class="time-filter-btn" data-time="siang">Siang (12-18)</button>
                    <button class="time-filter-btn" data-time="sore">Sore (18-24)</button>
                    <button class="time-filter-btn" data-time="malam">Malam (00-06)</button>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 12px;">
                <select id="chartParamSelect" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-card); color: var(--text-primary); font-family: inherit; font-size: 0.85rem; font-weight: 500; outline: none; cursor: pointer; min-width: 200px;">
                    <option value="all">Semua Parameter (Lengkap)</option>
                    <option value="gas">Gas Berbahaya (NH₃ & H₂S)</option>
                    <option value="thermal">Suhu & Kelembapan</option>
                    <option value="pm">Partikulat Udara (PM2.5 & PM10)</option>
                </select>
                <div style="display: flex; gap: 8px;">
                    <button onclick="downloadCSV()" style="background: #4ADE80; color: #0A2F1F; border: none; padding: 6px 14px; border-radius: 20px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s; font-size: 0.85rem;">
                        <i class="fas fa-download"></i> Unduh CSV
                    </button>
                    <button onclick="downloadCSV24Hours()" style="background: #3B82F6; color: #FFFFFF; border: none; padding: 6px 14px; border-radius: 20px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s; font-size: 0.85rem;" title="Unduh data penuh dari jam 00:00 hingga 24:00">
                        <i class="fas fa-file-csv"></i> Unduh 24 Jam
                    </button>
                </div>
            </div>
        </div>
        <canvas id="monitoringChart" width="600" height="220"></canvas>
    </div>

    <div class="chart-panel" style="margin-top: 20px;">
        <div class="section-title"><i class="fas fa-book-open"></i> Panduan Membaca Parameter Sensor</div>
        <p style="font-size: 0.88rem; color: var(--text-secondary); margin-bottom: 16px; line-height: 1.5;">Tabel panduan ini dibuat khusus agar mudah dipahami, membantu Anda mengetahui arti dari setiap angka pada layar dan tindakan apa yang perlu dilakukan jika angka tersebut merah.</p>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 950px; text-align: left; background: var(--bg-card); border-radius: 12px; overflow: hidden; border: 1px solid var(--border);">
                <thead>
                    <tr style="background: var(--bg-metric); border-bottom: 2px solid var(--border);">
                        <th style="padding: 14px; font-weight: 700; color: var(--text-primary); font-size: 0.9rem; width: 15%;">Parameter</th>
                        <th style="padding: 14px; font-weight: 700; color: var(--text-primary); font-size: 0.9rem; width: 20%;">Pengertian</th>
                        <th style="padding: 14px; font-weight: 700; color: var(--text-primary); font-size: 0.9rem; width: 12%;">Rentang Ideal</th>
                        <th style="padding: 14px; font-weight: 700; color: var(--text-primary); font-size: 0.9rem; width: 12%;">Waspada</th>
                        <th style="padding: 14px; font-weight: 700; color: var(--text-primary); font-size: 0.9rem; width: 12%;">Berbahaya</th>
                        <th style="padding: 14px; font-weight: 700; color: var(--text-primary); font-size: 0.9rem; width: 29%;">Referensi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 14px; font-weight: 600; color: var(--text-primary); font-size: 0.85rem;"><i class="fas fa-wind" style="color: #F97316; width: 24px; text-align: center;"></i> Gas Amonia (NH₃)</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">Gas hasil penguraian urin dan kotoran ternak.</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #16A34A; font-weight: 700;">&lt; 10 ppm</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #D97706; font-weight: 600;">10-20 ppm</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #DC2626; font-weight: 600;">&gt; 20 ppm</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">Penelitian pada kandang kambing menunjukkan NH₃ umumnya dijaga di bawah 10 ppm untuk kesehatan ternak.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 14px; font-weight: 600; color: var(--text-primary); font-size: 0.85rem;"><i class="fas fa-disease" style="color: #EAB308; width: 24px; text-align: center;"></i> Gas Hidrogen Sulfida (H₂S)</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">Gas berbau telur busuk dari limbah organik yang membusuk.</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #16A34A; font-weight: 700;">&lt; 1 ppm</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #D97706; font-weight: 600;">1-5 ppm</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #DC2626; font-weight: 600;">&gt; 5 ppm</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">H₂S di kandang normal sangat rendah; konsentrasi meningkat jika ventilasi buruk dan limbah menumpuk.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 14px; font-weight: 600; color: var(--text-primary); font-size: 0.85rem;"><i class="fas fa-temperature-half" style="color: #EF4444; width: 24px; text-align: center;"></i> Suhu</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">Temperatur udara di dalam kandang.</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #16A34A; font-weight: 700;">15-25°C</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #D97706; font-weight: 600;">25-30°C</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #DC2626; font-weight: 600;">&gt; 30°C</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">Sapi dan kambing mulai mengalami heat stress saat suhu meningkat, terutama jika kelembapan tinggi.</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 14px; font-weight: 600; color: var(--text-primary); font-size: 0.85rem;"><i class="fas fa-droplet" style="color: #3B82F6; width: 24px; text-align: center;"></i> Kelembapan</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">Kandungan uap air di udara.</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #16A34A; font-weight: 700;">50-70% RH</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #D97706; font-weight: 600;">70-80%</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #DC2626; font-weight: 600;">&gt; 80%</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">Kelembapan tinggi meningkatkan pertumbuhan mikroba dan pembentukan amonia.</td>
                    </tr>
                    <tr>
                        <td style="padding: 14px; font-weight: 600; color: var(--text-primary); font-size: 0.85rem;"><i class="fas fa-smog" style="color: #8B5CF6; width: 24px; text-align: center;"></i> PM2.5</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">Debu halus dari pakan, alas kandang, dan aktivitas ternak.</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #16A34A; font-weight: 700;">&lt; 25 µg/m³</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #D97706; font-weight: 600;">25-50 µg/m³</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: #DC2626; font-weight: 600;">&gt; 50 µg/m³</td>
                        <td style="padding: 14px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">Penelitian kandang kambing melaporkan PM2.5 normal sekitar 3-13 µg/m³; semakin rendah semakin baik.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(auth()->check() && auth()->user()->isAdmin())
<div id="actuatorsView" style="display: none;">
    <div class="control-panel">
        <div class="section-title"><i class="fas fa-microchip"></i> Kontrol Aktuator</div>
        <div class="actuator-buttons">
            <div class="actuator-item">
                <div class="actuator-info"><i class="fas fa-sync-alt"></i><span>Restart Device</span></div>
                <button id="restartBtnCtrl" class="toggle-btn" style="border-color: #EF4444; color: #EF4444;"><i class="fas fa-power-off"></i> <span id="restartTextCtrl">RESTART</span></button>
            </div>
        </div>
        <div class="sub" style="margin-top: 24px; text-align: center;">* Kontrol real-time via IoT Gateway</div>
    </div>
</div>
@endif

<div id="historyView" style="display: none;">
    <div class="chart-panel" style="text-align: center;">
        <i class="fas fa-chart-simple" style="font-size: 48px; color: #4ADE80; margin-bottom: 16px;"></i>
        <h3>Riwayat Rata-rata Sensor</h3>
        <p class="sub" style="margin-bottom: 20px;">Pilih rentang waktu untuk melihat rata-rata nilai sensor amonia, hidrogen sulfida, dan debu.</p>
        
        <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; margin-bottom: 24px;" id="historyTimeFilterGroup">
            <button class="time-filter-btn" data-htime="pagi">Pagi (06-12)</button>
            <button class="time-filter-btn" data-htime="siang">Siang (12-18)</button>
            <button class="time-filter-btn" data-htime="sore">Sore (18-24)</button>
            <button class="time-filter-btn" data-htime="malam">Malam (00-06)</button>
            <button class="time-filter-btn active" data-htime="24jam">24 Jam (Rata-rata)</button>
        </div>

        <canvas id="historyChart" width="600" height="200"></canvas>
    </div>
</div>

<div id="aboutView" style="display: none;">
    <div class="project-goal">
        <div class="section-title" style="margin-bottom: 16px;"><i class="fas fa-bullseye"></i> Tujuan Project</div>
        <p style="line-height: 1.6; font-size: 1rem;">Membangun sistem monitoring peternakan cerdas berbasis IoT untuk mendeteksi gas beracun (H₂S, NH₃), memantau kualitas udara (suhu, kelembapan, tekanan), serta partikulat debu (PM1.0, PM2.5, PM10) guna menciptakan lingkungan ternak yang sehat dan produktif. Sistem ini juga dilengkapi kontrol aktuator (kipas, lampu, pompa) untuk respons cepat terhadap kondisi tidak normal.</p>
    </div>

    <div class="section-title" style="margin-bottom: 20px;"><i class="fas fa-users"></i> Tim Pengembang</div>
    <div class="team-grid">
        <!-- Card 1: Pembimbing -->
        <div class="team-card" style="display: flex; flex-direction: column; text-align: left; align-items: flex-start;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div class="team-avatar" style="margin: 0; width: 50px; height: 50px; font-size: 20px;"><i class="fas fa-chalkboard-teacher"></i></div>
                <div>
                    <div class="team-name" style="margin-bottom: 2px;">Pembimbing PKM-PMM</div>
                    <div class="team-role">Dosen Pendamping</div>
                </div>
            </div>
            <div class="team-desc" style="width: 100%;">
                <ol style="padding-left: 20px; margin: 0; color: var(--text-primary); font-weight: 500;">
                    <li style="margin-bottom: 6px;">Sri Hariyati Fitriasih, S.Kom., M.Kom</li>
                    <li style="margin-bottom: 6px;">Prihanto, S.Pd., M.Si</li>
                    <li>Dwi Remawato, S.Kom., M.Kom</li>
                </ol>
            </div>
        </div>

        <!-- Card 2: Mahasiswa -->
        <div class="team-card" style="display: flex; flex-direction: column; text-align: left; align-items: flex-start;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div class="team-avatar" style="margin: 0; width: 50px; height: 50px; font-size: 20px;"><i class="fas fa-user-graduate"></i></div>
                <div>
                    <div class="team-name" style="margin-bottom: 2px;">Mahasiswa PKM-PMM</div>
                    <div class="team-role">15 Mahasiswa</div>
                </div>
            </div>
            <div class="team-desc" style="width: 100%;">
                <button onclick="document.getElementById('mahasiswaList').style.display = document.getElementById('mahasiswaList').style.display === 'none' ? 'block' : 'none'" style="background: var(--green-pale); color: var(--green-main); border: 1px solid rgba(34,197,94,0.3); padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; width: 100%; transition: 0.2s; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fas fa-list-ul"></i> Lihat Daftar Anggota
                </button>
                <div id="mahasiswaList" style="display: none; margin-top: 12px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 12px; max-height: 180px; overflow-y: auto;">
                    <ol style="padding-left: 24px; margin: 0; color: var(--text-primary); font-size: 0.85rem; line-height: 1.6;">
                        <li>Muhammad Pramoedya Destyanto</li>
                        <li>Muhammad Alif Faqihuddin</li>
                        <li>Naja Ihsanudin</li>
                        <li>Natan Alexsandria</li>
                        <li>Ryaas Rasyid Al-Fara</li>
                        <li>Hudzaifah</li>
                        <li>Yusuf Cahyo Nugroho</li>
                        <li>Alfirdous Geraldi Agviandra</li>
                        <li>Annisa Sahindar</li>
                        <li>Dika Setiawan</li>
                        <li>Afnan May Kandung Utomo</li>
                        <li>Ahmad Muzaki</li>
                        <li>Ayu Anugrah</li>
                        <li>Gian Fadhil Mahardika</li>
                        <li>Muhammad Ilyas Arrayyan</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Card 3: Mitra -->
        <div class="team-card" style="display: flex; flex-direction: column; text-align: left; align-items: flex-start;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div class="team-avatar" style="margin: 0; width: 50px; height: 50px; font-size: 20px;"><i class="fas fa-handshake"></i></div>
                <div>
                    <div class="team-name" style="margin-bottom: 2px;">Mitra Program</div>
                    <div class="team-role">PKM-PMM</div>
                </div>
            </div>
            <div class="team-desc" style="width: 100%;">
                <ol style="padding-left: 20px; margin: 0; color: var(--text-primary); font-weight: 500;">
                    <li style="margin-bottom: 6px;">Kelompok Peternak</li>
                    <li style="margin-bottom: 6px;">Kelompok Posyandu</li>
                    <li>Pemerintah Desa Jatirejo, Girimarto, Wonogiri</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="chart-panel" style="margin-top: 28px;">
        <div class="section-title"><i class="fas fa-microchip"></i> Teknologi yang Digunakan</div>
        <div style="display: flex; flex-wrap: wrap; gap: 12px; justify-content: center;">
            <span style="background: #F1F5F9; color: #1A2533; border: 1px solid #E2E8F0; padding: 6px 16px; border-radius: 30px; font-size: 0.85rem; font-weight: 500;"><i class="fab fa-laravel" style="color:#F05340;"></i> Laravel 13</span>
            <span style="background: #F1F5F9; color: #1A2533; border: 1px solid #E2E8F0; padding: 6px 16px; border-radius: 30px; font-size: 0.85rem; font-weight: 500;"><i class="fas fa-microchip" style="color:#2563EB;"></i> Arduino R4 Wifi</span>
            <span style="background: #F1F5F9; color: #1A2533; border: 1px solid #E2E8F0; padding: 6px 16px; border-radius: 30px; font-size: 0.85rem; font-weight: 500;"><i class="fas fa-cloud-upload-alt" style="color:#7C3AED;"></i> MQTT</span>
            <span style="background: #F1F5F9; color: #1A2533; border: 1px solid #E2E8F0; padding: 6px 16px; border-radius: 30px; font-size: 0.85rem; font-weight: 500;"><i class="fas fa-chart-line" style="color:#16A34A;"></i> Chart.js</span>
            <span style="background: #F1F5F9; color: #1A2533; border: 1px solid #E2E8F0; padding: 6px 16px; border-radius: 30px; font-size: 0.85rem; font-weight: 500;"><i class="fas fa-database" style="color:#D97706;"></i> MySQL</span>
        </div>
        <div class="sub" style="text-align: center; margin-top: 20px;">Project ini dikembangkan sebagai solusi peternakan modern untuk meningkatkan kesejahteraan hewan dan produktivitas.</div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
<script src="{{ asset('js/farm-sensor.js') }}"></script>
@endsection