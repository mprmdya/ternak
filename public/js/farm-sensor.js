(function () {
    // FUNGSI UNDUH CSV
    window.downloadCSV = function () {
        if (!timeLabels || timeLabels.length === 0) {
            alert("Belum ada data untuk diunduh!");
            return;
        }

        let csvContent = "data:text/csv;charset=utf-8,\uFEFF";
        csvContent += "Waktu;NH3 (ppm);H2S (ppm)\n";

        for (let i = 0; i < timeLabels.length; i++) {
            let row = `${timeLabels[i]};${nh3Hist[i]};${h2sHist[i]}`;
            csvContent += row + "\n";
        }

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `Data_Monitoring_${new Date().toISOString().slice(0, 10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    // FIREBASE INITIALIZATION
    const firebaseConfig = {
        databaseURL: "https://ternak-iot-default-rtdb.asia-southeast1.firebasedatabase.app"
    };
    if (!window.firebase.apps.length) {
        window.firebase.initializeApp(firebaseConfig);
    }
    const database = window.firebase.database();

    // ELEMEN DOM Dashboard (Command Center)
    const heroStatus = document.getElementById('heroStatus');
    const heroIcon = document.getElementById('heroIcon');
    const heroTitle = document.getElementById('heroTitle');
    const heroDesc = document.getElementById('heroDesc');

    const kpiAqi = document.getElementById('kpiAqi');
    const kpiThermal = document.getElementById('kpiThermal');
    const kpiGas = document.getElementById('kpiGas');
    const activityLogList = document.getElementById('activityLogList');

    // Monitoring
    const h2sM = document.getElementById('h2sValM'), nh3M = document.getElementById('nh3ValM');
    const tempM = document.getElementById('tempValM'), humM = document.getElementById('humValM'), presM = document.getElementById('presValM');
    const pm1M = document.getElementById('pm1ValM'), pm25M = document.getElementById('pm25ValM'), pm10M = document.getElementById('pm10ValM');
    const h2sBadgeM = document.getElementById('h2sBadgeM'), nh3BadgeM = document.getElementById('nh3BadgeM');

    let lastState = 'safe';

    function addLog(message, type = 'safe') {
        if (!activityLogList) return;
        const timeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        const newLog = document.createElement('div');
        newLog.className = `log-item ${type}`;
        newLog.innerHTML = `<div class="log-time">${timeStr}</div><div class="log-msg">${message}</div>`;
        activityLogList.prepend(newLog);

        // Batasi maksimal 15 log
        if (activityLogList.children.length > 15) {
            activityLogList.removeChild(activityLogList.lastChild);
        }
    }

    function updateBadges(data, isMain = true) {
        let nh3el = nh3BadgeM;
        let h2sel = h2sBadgeM;
        if (!nh3el || !h2sel) return;

        if (data.nh3 >= 40) { nh3el.innerText = "KRITIS"; nh3el.className = "badge-sm badge-danger"; }
        else if (data.nh3 >= 25) { nh3el.innerText = "WARNING"; nh3el.className = "badge-sm badge-warn"; }
        else { nh3el.innerText = "Normal"; nh3el.className = "badge-sm badge-safe"; }

        if (data.h2s >= 8) { h2sel.innerText = "KRITIS"; h2sel.className = "badge-sm badge-danger"; }
        else if (data.h2s >= 5) { h2sel.innerText = "WARNING"; h2sel.className = "badge-sm badge-warn"; }
        else { h2sel.innerText = "Aman"; h2sel.className = "badge-sm badge-safe"; }
    }

    function updateUI(data) {
        // UPDATE MONITORING VIEW
        if (h2sM) {
            h2sM.innerText = data.h2s; nh3M.innerText = data.nh3; tempM.innerText = data.temp;
            humM.innerText = data.hum; presM.innerText = data.press; pm1M.innerText = data.pm1;
            pm25M.innerText = data.pm25; pm10M.innerText = data.pm10;
        }
        if (h2sBadgeM) updateBadges(data, false);
        if (document.getElementById('timestampPanel')) document.getElementById('timestampPanel').innerText = new Date().toLocaleTimeString('id-ID');

        // UPDATE DASHBOARD COMMAND CENTER
        if (!heroStatus) return;

        // Logika Status
        let currentState = 'safe';
        let aqiStatus = 'Baik';
        let thermalStatus = 'Normal';
        let gasStatus = 'Aman';

        // Cek AQI
        if (data.pm25 >= 55 || data.pm10 >= 100) { aqiStatus = 'Buruk'; currentState = 'danger'; }
        else if (data.pm25 >= 35 || data.pm10 >= 70) { aqiStatus = 'Sedang'; if (currentState !== 'danger') currentState = 'warn'; }

        // Cek Termal (Suhu & Kelembapan)
        let sSuhu = (data.temp > 35) ? 2 : ((data.temp >= 30 || data.temp < 15) ? 1 : 0);
        let sHum = (data.hum > 80) ? 2 : ((data.hum >= 70 || data.hum < 40) ? 1 : 0);
        let sTherm = Math.max(sSuhu, sHum);
        if (sTherm === 2) { thermalStatus = 'Bahaya'; currentState = 'danger'; }
        else if (sTherm === 1) { thermalStatus = 'Waspada'; if (currentState !== 'danger') currentState = 'warn'; }

        // Cek Gas
        if (data.nh3 >= 40 || data.h2s >= 8) { gasStatus = 'Kritis'; currentState = 'danger'; }
        else if (data.nh3 >= 25 || data.h2s >= 5) { gasStatus = 'Waspada'; if (currentState !== 'danger') currentState = 'warn'; }

        // Update KPI
        if (kpiAqi) kpiAqi.innerText = aqiStatus;
        if (kpiThermal) kpiThermal.innerText = `${data.temp}°C (${thermalStatus})`;
        if (kpiGas) kpiGas.innerText = gasStatus;

        // Update Hero
        if (heroStatus.className !== `hero-status ${currentState}`) {
            heroStatus.className = `hero-status ${currentState}`;
            if (currentState === 'safe') {
                heroIcon.className = 'fas fa-shield-check';
                heroTitle.innerText = 'KONDISI AMAN';
                heroDesc.innerText = 'Semua parameter lingkungan peternakan berada dalam batas normal. Sirkulasi udara baik dan tidak ada indikasi penumpukan gas beracun.';
                addLog('Kondisi peternakan kembali stabil dan aman.', 'safe');
            } else if (currentState === 'warn') {
                heroIcon.className = 'fas fa-exclamation-triangle';
                heroTitle.innerText = 'WASPADA LINGKUNGAN';
                heroDesc.innerText = `Beberapa parameter berada di atas batas optimal (AQI: ${aqiStatus}, Gas: ${gasStatus}). Pastikan kipas ventilasi menyala.`;
                addLog(`Peringatan: Kualitas udara menurun (${aqiStatus}) atau Gas meningkat (${gasStatus}).`, 'warn');
            } else if (currentState === 'danger') {
                heroIcon.className = 'fas fa-radiation';
                heroTitle.innerText = 'KRITIS! BAHAYA GAS/UDARA';
                heroDesc.innerText = 'Peringatan Darurat! Konsentrasi gas beracun atau partikulat debu sangat tinggi. Segera evakuasi area atau nyalakan blower maksimal!';
                addLog(`KONDISI KRITIS! Kadar Amonia/H2S atau Debu melebihi batas toleransi ternak!`, 'danger');
            }
        }

        lastState = currentState;
    }

    // ==========================================
    // HISTORICAL CHART (PAGI, SIANG, SORE, MALAM)
    // ==========================================

    // Data riwayat (History) sekarang diambil dari Firebase /SensorLogs
    let histData = {
        pagi: { labels: [], data: { nh3: [], h2s: [], temp: [], hum: [], pm25: [], pm10: [] } },
        siang: { labels: [], data: { nh3: [], h2s: [], temp: [], hum: [], pm25: [], pm10: [] } },
        sore: { labels: [], data: { nh3: [], h2s: [], temp: [], hum: [], pm25: [], pm10: [] } },
        malam: { labels: [], data: { nh3: [], h2s: [], temp: [], hum: [], pm25: [], pm10: [] } }
    };

    // Fungsi untuk mengubah ID unik Firebase (Push ID) menjadi Waktu/Jam asli
    const PUSH_CHARS = '-0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
    function decodePushId(id) {
        let timestamp = 0;
        for (let i = 0; i < 8; i++) {
            timestamp = timestamp * 64 + PUSH_CHARS.indexOf(id.charAt(i));
        }
        return new Date(timestamp);
    }

    // Ambil 1000 data terakhir dari Firebase (Maksimal untuk menghindari browser berat)
    const historyRef = database.ref('SensorLogs').limitToLast(1000);
    historyRef.on('value', (snapshot) => {
        // Reset struktur data sebelum memuat ulang
        histData = {
            pagi: { labels: [], data: { nh3: [], h2s: [], temp: [], hum: [], pm25: [], pm10: [] } },
            siang: { labels: [], data: { nh3: [], h2s: [], temp: [], hum: [], pm25: [], pm10: [] } },
            sore: { labels: [], data: { nh3: [], h2s: [], temp: [], hum: [], pm25: [], pm10: [] } },
            malam: { labels: [], data: { nh3: [], h2s: [], temp: [], hum: [], pm25: [], pm10: [] } }
        };

        const todayStr = new Date().toDateString();

        snapshot.forEach(child => {
            let key = child.key;
            let val = child.val();
            
            // Dapatkan waktu asli saat data ini masuk ke Firebase
            let date = decodePushId(key);
            
            // Hanya tampilkan data histori untuk hari ini saja (Today)
            if (date.toDateString() !== todayStr) return;

            let hour = date.getHours();
            let timeStr = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            
            let period = 'malam';
            if (hour >= 6 && hour < 12) period = 'pagi';
            else if (hour >= 12 && hour < 18) period = 'siang';
            else if (hour >= 18 && hour <= 23) period = 'sore';

            histData[period].labels.push(timeStr);
            histData[period].data.nh3.push(parseFloat(val.NH3 || 0).toFixed(2));
            histData[period].data.h2s.push(parseFloat(val.H2S || 0).toFixed(2));
            histData[period].data.temp.push(parseFloat(val.Temperature || 0).toFixed(1));
            histData[period].data.hum.push(parseFloat(val.Humidity || 0).toFixed(1));
            histData[period].data.pm25.push(parseInt(val.PM25 || 0));
            histData[period].data.pm10.push(parseInt(val.PM10 || 0));
        });

        // Perbarui chart jika sudah dibuat
        if (monitoringChart) updateChart();
    });


    let currentTimeFilter = 'pagi';
    let currentParamFilter = 'gas';
    let monitoringChart = null;

    // Define Dataset Configurations
    const datasetConfigs = {
        all: [
            { label: 'NH₃ (ppm)', key: 'nh3', borderColor: '#F97316', backgroundColor: 'rgba(249,115,22,0.1)' },
            { label: 'H₂S (ppm)', key: 'h2s', borderColor: '#EAB308', backgroundColor: 'rgba(234,179,8,0.1)' },
            { label: 'Suhu (°C)', key: 'temp', borderColor: '#EF4444', backgroundColor: 'rgba(239,68,68,0.1)' },
            { label: 'Kelembapan (%)', key: 'hum', borderColor: '#3B82F6', backgroundColor: 'rgba(59,130,246,0.1)' },
            { label: 'PM2.5 (µg/m³)', key: 'pm25', borderColor: '#8B5CF6', backgroundColor: 'rgba(139,92,246,0.1)' },
            { label: 'PM10 (µg/m³)', key: 'pm10', borderColor: '#6366F1', backgroundColor: 'rgba(99,102,241,0.1)' }
        ],
        gas: [
            { label: 'NH₃ (ppm)', key: 'nh3', borderColor: '#F97316', backgroundColor: 'rgba(249,115,22,0.1)' },
            { label: 'H₂S (ppm)', key: 'h2s', borderColor: '#EAB308', backgroundColor: 'rgba(234,179,8,0.1)' }
        ],
        thermal: [
            { label: 'Suhu (°C)', key: 'temp', borderColor: '#EF4444', backgroundColor: 'rgba(239,68,68,0.1)' },
            { label: 'Kelembapan (%)', key: 'hum', borderColor: '#3B82F6', backgroundColor: 'rgba(59,130,246,0.1)' }
        ],
        pm: [
            { label: 'PM2.5 (µg/m³)', key: 'pm25', borderColor: '#8B5CF6', backgroundColor: 'rgba(139,92,246,0.1)' },
            { label: 'PM10 (µg/m³)', key: 'pm10', borderColor: '#6366F1', backgroundColor: 'rgba(99,102,241,0.1)' }
        ]
    };

    function updateChart() {
        const ctx = document.getElementById('monitoringChart');
        if (!ctx) return;

        const timeData = histData[currentTimeFilter];
        const config = datasetConfigs[currentParamFilter];

        const datasets = config.map(cfg => ({
            label: cfg.label,
            data: timeData.data[cfg.key],
            borderColor: cfg.borderColor,
            backgroundColor: cfg.backgroundColor,
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 2,
            pointHoverRadius: 5
        }));

        if (monitoringChart) {
            monitoringChart.data.labels = timeData.labels;
            monitoringChart.data.datasets = datasets;
            monitoringChart.update();
        } else {
            monitoringChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: { labels: timeData.labels, datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { labels: { color: '#4A6680', font: { family: 'Inter', weight: 600 } } },
                        tooltip: { backgroundColor: '#1A2533', titleFont: { family: 'Inter' }, bodyFont: { family: 'Inter' }, padding: 10, cornerRadius: 8 }
                    },
                    scales: {
                        y: { grid: { color: '#E2E8F0', drawBorder: false }, ticks: { color: '#8BA0B5', font: { family: 'Inter' } } },
                        x: { grid: { display: false }, ticks: { color: '#8BA0B5', font: { family: 'Inter' } } }
                    }
                }
            });
        }
    }

    // Initialize Chart
    setTimeout(updateChart, 100); // slight delay to ensure canvas is ready

    // Event Listeners for Filters
    const timeBtns = document.querySelectorAll('.time-filter-btn');
    timeBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            timeBtns.forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            currentTimeFilter = e.target.getAttribute('data-time');
            updateChart();
        });
    });

    const paramSelect = document.getElementById('chartParamSelect');
    if (paramSelect) {
        paramSelect.addEventListener('change', (e) => {
            currentParamFilter = e.target.value;
            updateChart();
        });
    }

    window.downloadCSV = function () {
        const timeData = histData[currentTimeFilter];
        const config = datasetConfigs[currentParamFilter];

        let csvContent = "data:text/csv;charset=utf-8,\uFEFF";
        let headers = ["Waktu", ...config.map(c => c.label)];
        csvContent += headers.join(";") + "\n";

        for (let i = 0; i < timeData.labels.length; i++) {
            let row = [timeData.labels[i]];
            config.forEach(cfg => {
                row.push(timeData.data[cfg.key][i]);
            });
            csvContent += row.join(";") + "\n";
        }

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `Data_${currentParamFilter}_${currentTimeFilter}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    window.downloadCSV24Hours = function () {
        // Collect 24 hours of data
        const periods = ['malam', 'pagi', 'siang', 'sore']; // Urutan kronologis: 00-06, 06-12, 12-18, 18-24
        const config = datasetConfigs['all']; // Ambil semua konfigurasi parameter

        let csvContent = "data:text/csv;charset=utf-8,\uFEFF";
        let headers = ["Waktu", ...config.map(c => c.label)];
        csvContent += headers.join(";") + "\n";

        periods.forEach(period => {
            const timeData = histData[period];
            // Untuk menghindari duplikasi jam yang sama (misal 06:00 ada di malam dan pagi)
            let startIdx = (period !== 'malam') ? 1 : 0;

            for (let i = startIdx; i < timeData.labels.length; i++) {
                let row = [timeData.labels[i]];
                config.forEach(cfg => {
                    row.push(timeData.data[cfg.key][i]);
                });
                csvContent += row.join(";") + "\n";
            }
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `Data_Semua_Sensor_24Jam.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    const sensorRef = database.ref('Sensor');
    sensorRef.on('value', (snapshot) => {
        const data = snapshot.val();
        if (data) {
            updateUI({
                nh3: parseFloat(data.NH3 || 0),
                h2s: parseFloat(data.H2S || 0),
                temp: parseFloat(data.Temperature || 0),
                hum: parseFloat(data.Humidity || 0),
                press: 1012.5, // Firebase tidak menyediakan data tekanan udara (Pressure)
                pm1: parseInt(data.PM1 || 0),
                pm25: parseInt(data.PM25 || 0),
                pm10: parseInt(data.PM10 || 0)
            });
        } else {
            console.warn("Tidak ada data ditemukan di path '/Sensor' Firebase.");
        }
    });

    // TOGGLE BUTTON ACTUATORS
    function initToggle(btnId, textId) {
        let btn = document.getElementById(btnId);
        if (!btn) return;
        let textSpan = document.getElementById(textId);
        let state = false;
        btn.addEventListener('click', () => {
            state = !state;
            if (state) { btn.classList.add('active'); if (textSpan) textSpan.innerText = "AKTIF"; }
            else { btn.classList.remove('active'); if (textSpan) textSpan.innerText = "MATI"; }
        });
    }
    initToggle('fanBtnCtrl', 'fanTextCtrl');
    initToggle('lampBtnCtrl', 'lampTextCtrl');
    initToggle('pumpBtnCtrl', 'pumpTextCtrl');

    // RESTART MICROCONTROLLER VIA FIREBASE
    const restartBtn = document.getElementById('restartBtnCtrl');
    const controlRef = database.ref('Control');
    
    if (restartBtn) {
        restartBtn.addEventListener('click', () => {
            if (confirm('Apakah Anda yakin ingin me-restart mikrokontroler?')) {
                controlRef.update({ restart: true }).then(() => {
                    // Optional alert or toast
                    console.log('Perintah restart terkirim.');
                }).catch((error) => {
                    console.error('Gagal mengirim perintah restart:', error);
                    alert('Gagal mengirim perintah restart.');
                });
            }
        });

        // Listen for restart state changes from Firebase
        controlRef.child('restart').on('value', (snapshot) => {
            const isRestarting = snapshot.val();
            let textSpan = document.getElementById('restartTextCtrl');
            if (textSpan && restartBtn) {
                if (isRestarting) {
                    textSpan.innerText = "RESTARTING...";
                    restartBtn.style.backgroundColor = '#EF4444';
                    restartBtn.style.color = 'white';
                } else {
                    textSpan.innerText = "RESTART";
                    restartBtn.style.backgroundColor = 'transparent';
                    restartBtn.style.color = '#EF4444';
                }
            }
        });
    }

    // NAVIGASI TOP BAR
    const views = {
        dashboard: document.getElementById('dashboardView'),
        monitoring: document.getElementById('monitoringView'),
        actuators: document.getElementById('actuatorsView'),
        history: document.getElementById('historyView'),
        about: document.getElementById('aboutView')
    };

    // HISTORY AVERAGES CHART
    const historyAverages = {
        pagi: { label: 'Rata-rata Pagi', data: [8.5, 1.2, 18], color: '#3B82F6' },
        siang: { label: 'Rata-rata Siang', data: [14.2, 2.8, 35], color: '#EF4444' },
        sore: { label: 'Rata-rata Sore', data: [11.0, 2.0, 25], color: '#F97316' },
        malam: { label: 'Rata-rata Malam', data: [9.5, 1.5, 20], color: '#8B5CF6' },
        '24jam': { label: 'Rata-rata 24 Jam', data: [12, 2.3, 28], color: '#16A34A' }
    };

    window.historyChartObj = null;

    function updateHistoryChart(period) {
        const histCtx = document.getElementById('historyChart')?.getContext('2d');
        if (!histCtx) return;

        const avgData = historyAverages[period];
        if (window.historyChartObj) {
            window.historyChartObj.data.datasets[0].label = avgData.label;
            window.historyChartObj.data.datasets[0].data = avgData.data;
            window.historyChartObj.data.datasets[0].backgroundColor = avgData.color;
            window.historyChartObj.update();
        } else {
            window.historyChartObj = new Chart(histCtx, {
                type: 'bar',
                data: {
                    labels: ['NH₃', 'H₂S', 'PM2.5'],
                    datasets: [{ label: avgData.label, data: avgData.data, backgroundColor: avgData.color }]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });
        }
    }

    const histTimeBtns = document.querySelectorAll('#historyTimeFilterGroup .time-filter-btn');
    histTimeBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            histTimeBtns.forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            updateHistoryChart(e.target.getAttribute('data-htime'));
        });
    });

    const navItems = document.querySelectorAll('.nav-item');
    const mainTitleSpan = document.getElementById('mainTitle');
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            const target = item.getAttribute('data-nav');
            Object.keys(views).forEach(v => { if (views[v]) views[v].style.display = 'none'; });
            if (views[target]) views[target].style.display = 'block';
            navItems.forEach(nav => nav.classList.remove('active'));
            item.classList.add('active');
            if (target === 'dashboard') mainTitleSpan.innerText = 'Dashboard Peternakan';
            else if (target === 'monitoring') mainTitleSpan.innerText = 'Monitoring Sensor & Gas';
            else if (target === 'actuators') mainTitleSpan.innerText = 'Kontrol Aktuator';
            else if (target === 'history') mainTitleSpan.innerText = 'Riwayat & Log';
            else if (target === 'about') mainTitleSpan.innerText = 'Tentang Project';

            if (target === 'history') {
                updateHistoryChart(document.querySelector('#historyTimeFilterGroup .time-filter-btn.active').getAttribute('data-htime') || '24jam');
            }
        });
    });
    if (views.dashboard) views.dashboard.style.display = 'block';
})();
