<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemantauan Peternakan | IoT Peternakan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* =====================
           LIGHT MODE COLOR TOKENS
        ===================== */
        :root {
            --bg-page:       #F0F4F8;
            --bg-sidebar:    #FFFFFF;
            --bg-header:     #FFFFFF;
            --bg-card:       #FFFFFF;
            --bg-panel:      #FFFFFF;
            --bg-metric:     #F4F7FA;
            --bg-chip:       #E8F5EE;

            --border:        #E2E8F0;
            --border-green:  #22C55E;

            --text-primary:  #1A2533;
            --text-secondary:#4A6680;
            --text-muted:    #8BA0B5;

            --green-main:    #16A34A;
            --green-light:   #22C55E;
            --green-pale:    #DCFCE7;

            --accent-blue:   #2563EB;
            --accent-amber:  #D97706;
            --accent-red:    #DC2626;

            --shadow-sm:     0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md:     0 4px 16px rgba(0,0,0,0.08);
            --shadow-lg:     0 8px 30px rgba(0,0,0,0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Space Grotesk', sans-serif;
            background: var(--bg-page);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* =====================
           APP LAYOUT
        ===================== */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* =====================
           SIDEBAR
        ===================== */
        .sidebar {
            width: 260px;
            min-width: 260px;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease, min-width 0.3s ease;
            position: sticky;
            top: 0;
            height: 100vh;
            z-index: 200;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .sidebar.collapsed {
            width: 76px;
            min-width: 76px;
        }

        /* Logo */
        .sidebar-logo {
            padding: 20px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            overflow: hidden;
        }

        .logo-icon {
            background: linear-gradient(135deg, #16A34A, #22C55E);
            min-width: 38px;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(34,197,94,0.3);
        }

        .logo-text {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            transition: opacity 0.2s, width 0.3s;
            overflow: hidden;
        }

        .sidebar.collapsed .logo-text { opacity: 0; width: 0; }

        /* Nav */
        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 14px 10px;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            color: var(--text-secondary);
            transition: all 0.2s ease;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-item i {
            font-size: 1.1rem;
            min-width: 22px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-text { transition: opacity 0.2s; overflow: hidden; }

        .sidebar.collapsed .nav-text { opacity: 0; width: 0; display: none; }

        .nav-item.active {
            background: var(--green-pale);
            color: var(--green-main);
            font-weight: 600;
        }

        .nav-item.active i { color: var(--green-main); }

        .nav-item:hover:not(.active) {
            background: #F1F5F9;
            color: var(--text-primary);
        }

        /* =====================
           MAIN WRAPPER
        ===================== */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow-x: hidden;
        }

        /* TOP HEADER */
        .top-header {
            height: 64px;
            background: var(--bg-header);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-shrink: 0;
            box-shadow: var(--shadow-sm);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .hamburger-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            font-size: 1.3rem;
            cursor: pointer;
            width: 38px;
            height: 38px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, color 0.2s;
            flex-shrink: 0;
        }

        .hamburger-btn:hover {
            background: var(--green-pale);
            color: var(--green-main);
        }

        .page-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.2px;
        }

        /* Live Badge */
        .live-status {
            background: var(--green-pale);
            padding: 6px 14px;
            border-radius: 40px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .live-dot {
            width: 8px;
            height: 8px;
            min-width: 8px;
            background: var(--green-light);
            border-radius: 50%;
            box-shadow: 0 0 6px var(--green-light);
            animation: livePulse 1.5s infinite alternate;
        }

        @keyframes livePulse {
            from { opacity: 0.5; transform: scale(0.85); }
            to   { opacity: 1;   transform: scale(1.2); }
        }

        /* =====================
           MAIN CONTENT
        ===================== */
        .main-content {
            padding: 16px 20px;
            flex: 1;
            background: var(--bg-page);
        }

        /* =====================
           CARDS & SENSOR GRID
        ===================== */
        .sensor-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .card {
            background: var(--bg-card);
            border-radius: 20px;
            border: 1px solid var(--border);
            padding: 26px 22px;
            transition: all 0.25s;
            box-shadow: var(--shadow-sm);
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: #86EFAC;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .card-header i {
            font-size: 32px;
            color: var(--green-main);
        }

        .chip {
            background: var(--green-pale);
            border: 1px solid rgba(34,197,94,0.25);
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--green-main);
            letter-spacing: 0.3px;
        }

        .value {
            font-size: 3.2rem;
            font-weight: 800;
            font-family: 'Space Grotesk', monospace;
            letter-spacing: -1px;
            margin: 8px 0 4px;
            line-height: 1;
            color: var(--text-primary);
        }

        .unit {
            font-size: 0.9rem;
            font-weight: 400;
            color: var(--text-muted);
            margin-left: 4px;
        }

        .sub {
            font-size: 0.72rem;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 0.6px;
            margin-bottom: 6px;
        }

        .badge-sm {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 11px;
            border-radius: 20px;
            margin-top: 8px;
        }

        .badge-safe   { background: #DCFCE7; color: #15803D; border: 1px solid #86EFAC; }
        .badge-warn   { background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; }
        .badge-danger { background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; }

        .row-metrics {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 10px;
        }

        .metric-box {
            flex: 1;
            background: var(--bg-metric);
            border-radius: 14px;
            padding: 12px 6px;
            text-align: center;
            border: 1px solid var(--border);
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* =====================
           PANELS (chart, log, about)
        ===================== */
        .chart-panel, .about-panel, .log-panel {
            background: var(--bg-panel);
            border-radius: 20px;
            border: 1px solid var(--border);
            padding: 16px;
            margin-top: 12px;
            box-shadow: var(--shadow-sm);
        }

        /* =====================
           DASHBOARD COMMAND CENTER
        ===================== */
        .hero-status {
            background: linear-gradient(135deg, #F0FDF4, #DCFCE7);
            border: 1px solid #86EFAC;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            margin-bottom: 16px;
            transition: all 0.5s ease;
            box-shadow: var(--shadow-sm);
        }

        .hero-status.safe   { background: linear-gradient(135deg, #F0FDF4, #DCFCE7); border-color: #4ADE80; box-shadow: 0 4px 20px rgba(74,222,128,0.15); }
        .hero-status.warn   { background: linear-gradient(135deg, #FFFBEB, #FEF3C7); border-color: #FCD34D; box-shadow: 0 4px 20px rgba(251,191,36,0.15); }
        .hero-status.danger { background: linear-gradient(135deg, #FFF1F2, #FEE2E2); border-color: #FCA5A5; box-shadow: 0 4px 20px rgba(248,113,113,0.15); }

        .hero-icon { font-size: 36px; margin-bottom: 8px; }
        .hero-status.safe   .hero-icon { color: #16A34A; }
        .hero-status.warn   .hero-icon { color: #D97706; }
        .hero-status.danger .hero-icon { color: #DC2626; }

        .hero-title {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
            color: var(--text-primary);
        }

        .hero-desc {
            font-size: 0.85rem;
            color: var(--text-secondary);
            max-width: 580px;
            margin: 0 auto;
            line-height: 1.5;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .kpi-card {
            background: var(--bg-card);
            border-radius: 18px;
            padding: 16px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-sm);
            transition: box-shadow 0.2s;
        }

        .kpi-card:hover { box-shadow: var(--shadow-md); }

        .kpi-icon {
            width: 42px; height: 42px; min-width: 42px;
            border-radius: 12px;
            display: flex; justify-content: center; align-items: center;
            font-size: 18px;
        }

        .kpi-info h4 {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kpi-val {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        /* Log */
        .log-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-height: 180px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .log-list::-webkit-scrollbar { width: 5px; }
        .log-list::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }

        .log-item {
            background: var(--bg-metric);
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border-left: 3px solid var(--green-light);
        }

        .log-item.warn   { border-left-color: #FBBF24; background: #FFFBEB; }
        .log-item.danger { border-left-color: #F87171; background: #FFF1F2; }

        .log-time { font-size: 0.72rem; color: var(--text-muted); min-width: 48px; padding-top: 2px; }
        .log-msg  { font-size: 0.82rem; color: var(--text-primary); flex: 1; line-height: 1.3; }

        /* Section Title */
        .section-title {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-left: 3px solid var(--green-main);
            padding-left: 10px;
            color: var(--text-primary);
        }

        canvas { max-height: 300px; width: 100%; }

        /* =====================
           KONTROL PANEL
        ===================== */
        .control-panel {
            background: var(--bg-card);
            border-radius: 20px;
            border: 1px solid var(--border);
            padding: 24px;
            box-shadow: var(--shadow-sm);
        }

        .actuator-buttons { display: flex; flex-direction: column; gap: 14px; }

        .actuator-item {
            background: var(--bg-metric);
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            border: 1px solid var(--border);
        }

        .actuator-info {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 1rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .actuator-info i { font-size: 24px; color: var(--green-main); }

        .toggle-btn {
            background: #E2E8F0;
            border: none;
            padding: 10px 22px;
            border-radius: 40px;
            color: var(--text-secondary);
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
        }

        .toggle-btn.active {
            background: var(--green-main);
            color: #fff;
            box-shadow: 0 2px 12px rgba(22,163,74,0.35);
        }

        /* =====================
           ABOUT / TEAM
        ===================== */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-top: 18px;
        }

        .team-card {
            background: var(--bg-metric);
            border-radius: 18px;
            padding: 22px;
            text-align: center;
            border: 1px solid var(--border);
            transition: 0.25s;
        }

        .team-card:hover { box-shadow: var(--shadow-md); border-color: #86EFAC; }

        .team-avatar {
            width: 68px; height: 68px;
            background: var(--green-pale);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
            font-size: 26px;
            color: var(--green-main);
            border: 2px solid #86EFAC;
        }

        .team-name { font-size: 1.05rem; font-weight: 700; margin-bottom: 4px; color: var(--text-primary); }
        .team-role { font-size: 0.76rem; color: var(--text-muted); margin-bottom: 10px; }
        .team-desc { font-size: 0.82rem; color: var(--text-secondary); line-height: 1.45; }

        .project-goal {
            background: var(--green-pale);
            border-radius: 18px;
            padding: 20px 22px;
            margin-bottom: 22px;
            border-left: 4px solid var(--green-main);
        }

        .footer {
            text-align: center;
            font-size: 0.68rem;
            color: var(--text-muted);
            margin-top: 32px;
            padding: 16px 0;
            border-top: 1px solid var(--border);
        }

        /* =====================
           MOBILE OVERLAY
        ===================== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.35);
            z-index: 150;
        }

        .sidebar-overlay.active { display: block; }

        /* =====================
           RESPONSIVE
        ===================== */
        @media (max-width: 1024px) {
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0; top: 0; bottom: 0;
                transform: translateX(-100%);
                width: 260px !important;
                min-width: 260px !important;
                z-index: 300;
            }

            .sidebar.mobile-open { transform: translateX(0); }
            .sidebar.mobile-open .logo-text { opacity: 1; width: auto; }
            .sidebar.mobile-open .nav-text  { display: block; opacity: 1; width: auto; }

            .main-content { padding: 20px 16px; }
            .top-header   { padding: 0 16px; }

            .sensor-grid { grid-template-columns: 1fr; }
            .kpi-grid    { grid-template-columns: 1fr; }
            .value       { font-size: 2.5rem; }
            .hero-title  { font-size: 1.5rem; }
            .hero-status { padding: 24px 18px; }
        }

        @media (max-width: 480px) {
            .row-metrics { flex-wrap: wrap; }
            .metric-box  { min-width: calc(50% - 5px); }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="app-wrapper">

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon"><i class="fas fa-leaf"></i></div>
            <span class="logo-text">Farm<span style="font-weight:400">Modern</span></span>
        </div>

        <nav class="nav-menu">
            <div class="nav-item active" data-nav="dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span class="nav-text">Dashboard</span>
            </div>
            <div class="nav-item" data-nav="monitoring">
                <i class="fas fa-chart-line"></i>
                <span class="nav-text">Monitoring</span>
            </div>
           @if(auth()->user() && auth()->user()->isAdmin())
            <div class="nav-item" data-nav="actuators">
                <i class="fas fa-sliders-h"></i>
                <span class="nav-text">Kontrol</span>
            </div>
            @endif
            <div class="nav-item" data-nav="history">
                <i class="fas fa-history"></i>
                <span class="nav-text">Riwayat</span>
            </div>
            <div class="nav-item" data-nav="about">
                <i class="fas fa-info-circle"></i>
                <span class="nav-text">Tentang</span>
            </div>
        </nav>

        <div style="padding: 14px 12px; border-top: 1px solid var(--border); margin-top: auto;">
            <div style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 12px; background: var(--bg-metric); overflow: hidden; white-space: nowrap;">
                <div style="width:34px; height:34px; min-width:34px; border-radius: 50%; background: var(--green-pale); display:flex; align-items:center; justify-content:center; color: var(--green-main); font-weight:700; font-size: 0.85rem;">
                    {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : '?' }}
                </div>
                <div class="nav-text" style="overflow:hidden;">
                    <div style="font-size: 0.82rem; font-weight: 600; color: var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name ?? 'Guest' }}</div>
                    <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">{{ auth()->user()->role ?? '' }}</div>
                </div>
            </div>
        </div>
    </aside>

    <div class="main-wrapper">

        <header class="top-header">
            <div class="header-left">
                <button class="hamburger-btn" id="toggleSidebar" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="page-title" id="mainTitle">Dashboard Peternakan</span>
            </div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="live-status">
                    <div class="live-dot"></div>
                    <span style="font-size:0.78rem; color:#16A34A; font-weight:600;">Live</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 0.82rem; color: var(--text-secondary); font-weight: 500;">
                        {{ auth()->user()->name ?? '' }}
                        @if(auth()->check() && auth()->user()->isAdmin())
                            <span style="background: var(--green-pale); color: var(--green-main); padding: 2px 8px; border-radius: 6px; font-size: 0.68rem; font-weight: 700; margin-left: 4px;">ADMIN</span>
                        @endif
                    </span>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="hamburger-btn" title="Logout" style="color: var(--accent-red); font-size: 1.1rem;">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="main-content">
            @yield('content')

            <div class="footer">
                <i class="fas fa-leaf"></i> Universitas Tiga Serangkai &middot; Sensor MQ-136, MQ-137, BME280, PMS5003
            </div>
        </main>
    </div>
</div>

<script>
    const sidebar   = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    const isMobile  = () => window.innerWidth <= 768;

    toggleBtn.addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
        }
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

    window.addEventListener('resize', () => {
        if (!isMobile()) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        }
    });
</script>

@yield('scripts')
</body>
</html>