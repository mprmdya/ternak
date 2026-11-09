(function () {
    // Simulasi data sensor
    let lastNh3 = 8.2, lastH2s = 1.0, lastTemp = 28.2, lastHum = 61, lastPm25 = 17, lastPm10 = 34;

    function generateData() {
        let newNh3 = lastNh3 + (Math.random() - 0.5) * 1.6;
        if (Math.random() < 0.1) newNh3 += 5 + Math.random() * 12;
        newNh3 = Math.min(58, Math.max(2, newNh3));
        lastNh3 = newNh3;
        let newH2s = lastH2s + (Math.random() - 0.5) * 0.7;
        if (Math.random() < 0.08) newH2s += 2.2 + Math.random() * 5;
        newH2s = Math.min(12.5, Math.max(0.2, newH2s));
        lastH2s = newH2s;
        let newTemp = lastTemp + (Math.random() - 0.5) * 0.7;
        if (Math.random() < 0.07) newTemp += 1.8;
        newTemp = Math.min(36.8, Math.max(21.5, newTemp));
        lastTemp = newTemp;
        let newHum = lastHum + (Math.random() - 0.5) * 1.3;
        newHum = Math.min(84, Math.max(45, newHum));
        lastHum = newHum;
        let newPress = 1012 + (Math.random() - 0.5) * 12;
        let newPm25 = lastPm25 + (Math.random() - 0.5) * 2.5;
        if (Math.random() < 0.12) newPm25 += 10 + Math.random() * 15;
        newPm25 = Math.min(72, Math.max(5, newPm25));
        lastPm25 = newPm25;
        let newPm10 = lastPm10 + (Math.random() - 0.5) * 5;
        if (Math.random() < 0.1) newPm10 += 18 + Math.random() * 25;
        newPm10 = Math.min(136, Math.max(15, newPm10));
        lastPm10 = newPm10;
        let newPm1 = Math.max(2, (newPm25 * 0.45) + Math.random() * 3);
        newPm1 = Math.min(48, newPm1);
        return { nh3: parseFloat(newNh3.toFixed(2)), h2s: parseFloat(newH2s.toFixed(2)), temp: parseFloat(newTemp.toFixed(1)), hum: parseFloat(newHum.toFixed(1)), press: parseFloat(newPress.toFixed(1)), pm1: parseFloat(newPm1.toFixed(1)), pm25: parseFloat(newPm25.toFixed(1)), pm10: parseFloat(newPm10.toFixed(1)) };
    }

    // Elemen utama
    const h2sSpan = document.getElementById('h2sVal'), nh3Span = document.getElementById('nh3Val');
    const tempSpan = document.getElementById('tempVal'), humSpan = document.getElementById('humVal'), presSpan = document.getElementById('presVal');
    const pm1Span = document.getElementById('pm1Val'), pm25Span = document.getElementById('pm25Val'), pm10Span = document.getElementById('pm10Val');
    const h2sBadge = document.getElementById('h2sBadge'), nh3Badge = document.getElementById('nh3Badge');
    const pm25Badge = document.getElementById('pm25Badge'), pm10Badge = document.getElementById('pm10Badge');
    const timestampPanel = document.getElementById('timestampPanel');

    // Monitoring duplikat
    const h2sM = document.getElementById('h2sValM'), nh3M = document.getElementById('nh3ValM');
    const tempM = document.getElementById('tempValM'), humM = document.getElementById('humValM'), presM = document.getElementById('presValM');
    const pm1M = document.getElementById('pm1ValM'), pm25M = document.getElementById('pm25ValM'), pm10M = document.getElementById('pm10ValM');
    const h2sBadgeM = document.getElementById('h2sBadgeM'), nh3BadgeM = document.getElementById('nh3BadgeM');

    function updateBadges(data, isMain = true) {
        let nh3el = isMain ? nh3Badge : nh3BadgeM;
        let h2sel = isMain ? h2sBadge : h2sBadgeM;
        let pm25el = pm25Badge, pm10el = pm10Badge;
        if (data.nh3 >= 40) { nh3el.innerText = "KRITIS"; nh3el.className = "badge-sm badge-danger"; }
        else if (data.nh3 >= 25) { nh3el.innerText = "WARNING"; nh3el.className = "badge-sm badge-warn"; }
        else { nh3el.innerText = "Normal"; nh3el.className = "badge-sm badge-safe"; }
        if (data.h2s >= 8) { h2sel.innerText = "KRITIS"; h2sel.className = "badge-sm badge-danger"; }
        else if (data.h2s >= 5) { h2sel.innerText = "WARNING"; h2sel.className = "badge-sm badge-warn"; }
        else { h2sel.innerText = "Aman"; h2sel.className = "badge-sm badge-safe"; }
        if (data.pm25 >= 55) { pm25el.innerText = "Berbahaya"; pm25el.className = "badge-sm badge-danger"; }
        else if (data.pm25 >= 35) { pm25el.innerText = "Sedang"; pm25el.className = "badge-sm badge-warn"; }
        else { pm25el.innerText = "Baik"; pm25el.className = "badge-sm badge-safe"; }
        if (data.pm10 >= 100) { pm10el.innerText = "KRITIS"; pm10el.className = "badge-sm badge-danger"; }
        else if (data.pm10 >= 70) { pm10el.innerText = "WARNING"; pm10el.className = "badge-sm badge-warn"; }
        else { pm10el.innerText = "Aman"; pm10el.className = "badge-sm badge-safe"; }
    }

    function updateUI(data) {
        if (h2sSpan) h2sSpan.innerText = data.h2s;
        if (nh3Span) nh3Span.innerText = data.nh3;
        if (tempSpan) tempSpan.innerText = data.temp;
        if (humSpan) humSpan.innerText = data.hum;
        if (presSpan) presSpan.innerText = data.press;
        if (pm1Span) pm1Span.innerText = data.pm1;
        if (pm25Span) pm25Span.innerText = data.pm25;
        if (pm10Span) pm10Span.innerText = data.pm10;
        if (h2sM) { h2sM.innerText = data.h2s; nh3M.innerText = data.nh3; tempM.innerText = data.temp; humM.innerText = data.hum; presM.innerText = data.press; pm1M.innerText = data.pm1; pm25M.innerText = data.pm25; pm10M.innerText = data.pm10; }
        updateBadges(data, true);
        if (h2sBadgeM) updateBadges(data, false);
        const now = new Date();
        if (timestampPanel) timestampPanel.innerText = now.toLocaleTimeString('id-ID');
    }

    // Chart
    const ctx = document.getElementById('gasChart')?.getContext('2d');
    let timeLabels = [], nh3Hist = [], h2sHist = [];
    let gasChart = null;
    if (ctx) {
        gasChart = new Chart(ctx, {
            type: 'line', data: {
                labels: timeLabels, datasets: [
                    { label: 'NH₃ (ppm)', data: nh3Hist, borderColor: '#F97316', backgroundColor: 'rgba(249,115,22,0.05)', tension: 0.2, fill: true, pointRadius: 2 },
                    { label: 'H₂S (ppm)', data: h2sHist, borderColor: '#EAB308', backgroundColor: 'rgba(234,179,8,0.03)', tension: 0.2, fill: true, pointRadius: 2 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { labels: { color: '#CBD5E1' } } }, scales: { y: { grid: { color: '#2A533B' }, ticks: { color: '#AEC9BA' } }, x: { ticks: { color: '#9AB3A8' } } } }
        });
    }

    function addChartData(time, nh3, h2s) {
        if (!gasChart) return;
        timeLabels.push(time); nh3Hist.push(nh3); h2sHist.push(h2s);
        if (timeLabels.length > 12) { timeLabels.shift(); nh3Hist.shift(); h2sHist.shift(); }
        gasChart.data.labels = [...timeLabels]; gasChart.data.datasets[0].data = [...nh3Hist]; gasChart.data.datasets[1].data = [...h2sHist];
        gasChart.update('none');
    }

    // Seed data
    for (let i = 0; i < 12; i++) { let d = generateData(); let t = new Date(Date.now() - (12 - i) * 2500).toLocaleTimeString('id-ID'); timeLabels.push(t); nh3Hist.push(d.nh3); h2sHist.push(d.h2s); if (i === 11) updateUI(d); }
    if (gasChart) gasChart.update();

    setInterval(() => { let d = generateData(); updateUI(d); addChartData(new Date().toLocaleTimeString('id-ID'), d.nh3, d.h2s); }, 2800);

    // Aktuator Toggle (aman jika elemen tidak ada)
    function initToggle(btnId, textId, btnId2 = null) {
        let btn = document.getElementById(btnId);
        if (!btn) return;
        let textSpan = document.getElementById(textId);
        let state = false;
        btn.addEventListener('click', () => {
            state = !state;
            if (state) { btn.classList.add('active'); if (textSpan) textSpan.innerText = "AKTIF"; }
            else { btn.classList.remove('active'); if (textSpan) textSpan.innerText = "MATI"; }
            if (btnId2) { let btn2 = document.getElementById(btnId2); if (btn2) { if (state) btn2.classList.add('active'); else btn2.classList.remove('active'); let span2 = btn2.querySelector('span'); if (span2) span2.innerText = state ? "AKTIF" : "MATI"; } }
        });
    }
    initToggle('fanBtn', 'fanText', 'fanBtn2');
    initToggle('lampBtn', 'lampText', 'lampBtn2');
    initToggle('pumpBtn', 'pumpText', 'pumpBtn2');

    // Navigasi antar view
    const views = {
        dashboard: document.getElementById('dashboardView'),
        monitoring: document.getElementById('monitoringView'),
        actuators: document.getElementById('actuatorsView'),
        history: document.getElementById('historyView')
    };
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
            else if (target === 'monitoring') mainTitleSpan.innerText = 'Monitoring Sensor';
            else if (target === 'actuators') mainTitleSpan.innerText = 'Kontrol Aktuator';
            else if (target === 'history') mainTitleSpan.innerText = 'Riwayat & Log';
            if (target === 'history') {
                const histCtx = document.getElementById('historyChart')?.getContext('2d');
                if (histCtx && !window.historyChartObj) {
                    window.historyChartObj = new Chart(histCtx, { type: 'bar', data: { labels: ['NH₃', 'H₂S', 'PM2.5'], datasets: [{ label: 'Rata-rata 24 jam', data: [12, 2.3, 28], backgroundColor: '#4ADE80' }] }, options: { responsive: true } });
                }
            }
        });
    });
    // Tampilkan dashboard awal
    if (views.dashboard) views.dashboard.style.display = 'block';
})();