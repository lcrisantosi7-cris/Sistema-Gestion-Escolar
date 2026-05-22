<?php require_once '../../Config/session_check.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduCore | Sistema de Gestión Académica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ─── DESIGN TOKENS ─────────────────────────────────────────── */
        :root {
            --sidebar-w: 256px;
            --topbar-h: 60px;

            /* Palette */
            --color-primary:     #2563eb;
            --color-primary-dim: #1d4ed8;
            --color-primary-bg:  #eff6ff;
            --color-success:     #16a34a;
            --color-success-bg:  #f0fdf4;
            --color-danger:      #dc2626;
            --color-danger-bg:   #fef2f2;
            --color-warning:     #d97706;
            --color-warning-bg:  #fffbeb;

            /* Neutrals */
            --gray-50:  #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;

            /* Sidebar */
            --sidebar-bg:    #0f172a;
            --sidebar-hover: rgba(255,255,255,0.06);
            --sidebar-active-bg: rgba(37,99,235,0.15);
            --sidebar-active-border: #2563eb;
            --sidebar-text:  #94a3b8;
            --sidebar-text-active: #e2e8f0;

            /* Surfaces */
            --bg-body:   #f1f5f9;
            --bg-card:   #ffffff;
            --border:    #e2e8f0;

            /* Text */
            --text-primary:   #0f172a;
            --text-secondary: #64748b;
            --text-muted:     #94a3b8;

            /* Radius */
            --radius-sm:  6px;
            --radius-md:  10px;
            --radius-lg:  14px;
            --radius-xl:  18px;

            /* Shadow */
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.07), 0 4px 6px -2px rgba(0,0,0,0.04);
        }

        /* ─── RESET & BASE ───────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── LAYOUT ─────────────────────────────────────────────────── */
        .main-content {
            margin-left: var(--sidebar-w);
            width: calc(100% - var(--sidebar-w));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ─── TOPBAR ─────────────────────────────────────────────────── */
        .topbar {
            height: var(--topbar-h);
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .topbar-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        /* User menu */
        .user-menu {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 10px;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: background 0.15s;
            user-select: none;
        }
        .user-menu:hover { background: var(--gray-50); }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--color-primary);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-meta { line-height: 1.3; }
        .user-meta strong { display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .user-meta small  { font-size: 11px; color: var(--color-primary); font-weight: 500; }

        .dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            width: 180px;
            padding: 6px;
            display: none;
            z-index: 1000;
        }
        .dropdown.open { display: block; }
        .dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 500;
            transition: background 0.15s;
        }
        .dropdown a:hover { background: var(--gray-50); }
        .dropdown a.danger { color: var(--color-danger); }
        .dropdown a.danger:hover { background: var(--color-danger-bg); }
        .dropdown-divider { height: 1px; background: var(--border); margin: 4px 0; }

        /* ─── PAGE CONTENT ───────────────────────────────────────────── */
        .page-content {
            padding: 32px;
            flex: 1;
        }

        /* ─── SHARED COMPONENTS ──────────────────────────────────────── */

        /* Card */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
        }

        /* Page header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .page-title i { color: var(--color-primary); font-size: 16px; }
        .page-subtitle { font-size: 13px; color: var(--text-secondary); margin-top: 2px; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .btn-primary   { background: var(--color-primary); color: #fff; }
        .btn-primary:hover { background: var(--color-primary-dim); }
        .btn-secondary { background: var(--gray-100); color: var(--text-primary); border: 1px solid var(--border); }
        .btn-secondary:hover { background: var(--gray-200); }
        .btn-success   { background: var(--color-success); color: #fff; }
        .btn-success:hover { background: #15803d; }
        .btn-danger    { background: var(--color-danger-bg); color: var(--color-danger); }
        .btn-danger:hover { background: #fee2e2; }
        .btn-warning   { background: var(--color-warning-bg); color: var(--color-warning); }
        .btn-warning:hover { background: #fef3c7; }
        .btn-ghost     { background: transparent; color: var(--text-secondary); }
        .btn-ghost:hover { background: var(--gray-100); color: var(--text-primary); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-lg { padding: 11px 22px; font-size: 14px; }
        .btn-icon { padding: 7px; width: 32px; height: 32px; justify-content: center; }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-blue    { background: var(--color-primary-bg); color: var(--color-primary); }
        .badge-green   { background: var(--color-success-bg); color: var(--color-success); }
        .badge-red     { background: var(--color-danger-bg);  color: var(--color-danger); }
        .badge-yellow  { background: var(--color-warning-bg); color: var(--color-warning); }
        .badge-gray    { background: var(--gray-100); color: var(--gray-500); }

        /* Alerts */
        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
        }
        .alert-success { background: var(--color-success-bg); color: var(--color-success); border: 1px solid #bbf7d0; }
        .alert-danger  { background: var(--color-danger-bg);  color: var(--color-danger);  border: 1px solid #fecaca; }
        .alert-info    { background: var(--color-primary-bg); color: var(--color-primary); border: 1px solid #bfdbfe; }
        .alert-warning { background: var(--color-warning-bg); color: var(--color-warning); border: 1px solid #fde68a; }

        /* Table */
        .table-wrap { overflow-x: auto; }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            padding: 11px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text-secondary);
            background: var(--gray-50);
            border-bottom: 1px solid var(--border);
        }
        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--gray-100);
            color: var(--text-primary);
            vertical-align: middle;
        }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover td { background: var(--gray-50); }

        /* Form controls */
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
            letter-spacing: 0.03em;
        }
        .form-control {
            width: 100%;
            padding: 9px 13px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 14px;
            font-family: inherit;
            color: var(--text-primary);
            background: var(--bg-card);
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .form-control[readonly] { background: var(--gray-50); color: var(--text-secondary); cursor: not-allowed; }
        .form-control::placeholder { color: var(--text-muted); }

        /* Search bar */
        .search-bar {
            display: flex;
            align-items: center;
            gap: 0;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            overflow: hidden;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .search-bar:focus-within {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .search-bar i { padding: 0 12px; color: var(--text-muted); font-size: 13px; }
        .search-bar input {
            border: none;
            outline: none;
            padding: 9px 0;
            font-size: 14px;
            font-family: inherit;
            color: var(--text-primary);
            background: transparent;
            flex: 1;
        }
        .search-bar input::placeholder { color: var(--text-muted); }

        /* Divider */
        .divider { height: 1px; background: var(--border); margin: 20px 0; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
        .empty-state i { font-size: 36px; margin-bottom: 12px; display: block; opacity: 0.4; }
        .empty-state p { font-size: 14px; font-weight: 500; }

        /* ─── PAGE LOADER ────────────────────────────────────────────── */
        /* Aparece inmediatamente al navegar, elimina la pantalla blanca */
        #page-loader {
            position: fixed;
            inset: 0;
            background: var(--bg-body);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s;
        }
        #page-loader.visible {
            opacity: 1;
            pointer-events: all;
        }
        .loader-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }
        .loader-logo {
            width: 40px;
            height: 40px;
            background: var(--color-primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
        }
        .loader-bar {
            width: 160px;
            height: 3px;
            background: var(--gray-200);
            border-radius: 99px;
            overflow: hidden;
        }
        .loader-bar::after {
            content: '';
            display: block;
            height: 100%;
            width: 40%;
            background: var(--color-primary);
            border-radius: 99px;
            animation: slide 0.9s ease-in-out infinite;
        }
        @keyframes slide {
            0%   { transform: translateX(-100%); }
            100% { transform: translateX(400%); }
        }

        /* Barra de progreso fina en la parte superior (estilo GitHub/YouTube) */
        #nprogress-bar {
            position: fixed;
            top: 0; left: 0;
            height: 2px;
            background: var(--color-primary);
            z-index: 10000;
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 0 2px 2px 0;
            box-shadow: 0 0 8px var(--color-primary);
        }

        /* Utility */
        .text-muted   { color: var(--text-secondary); }
        .text-primary { color: var(--color-primary); }
        .font-mono    { font-family: 'Courier New', monospace; }
        .fw-600       { font-weight: 600; }
        .fw-700       { font-weight: 700; }
    </style>
</head>
<body>

<!-- Barra de progreso superior -->
<div id="nprogress-bar"></div>

<!-- Loader de página completa -->
<div id="page-loader">
    <div class="loader-inner">
        <div class="loader-logo"><i class="fas fa-graduation-cap"></i></div>
        <div class="loader-bar"></div>
    </div>
</div>

<script>
    // Muestra el loader al salir de la página (clic en enlace o botón submit)
    (function() {
        var bar    = document.getElementById('nprogress-bar');
        var loader = document.getElementById('page-loader');
        var timer;

        function showLoader() {
            // Barra superior: avanza rápido al 70% y luego espera
            bar.style.width = '0%';
            bar.style.transition = 'none';
            setTimeout(function() {
                bar.style.transition = 'width 0.4s ease';
                bar.style.width = '70%';
            }, 10);
            // Loader central: aparece solo si tarda más de 200ms
            timer = setTimeout(function() {
                loader.classList.add('visible');
            }, 200);
        }

        function hideLoader() {
            clearTimeout(timer);
            bar.style.width = '100%';
            loader.classList.remove('visible');
            setTimeout(function() { bar.style.width = '0%'; }, 300);
        }

        // Interceptar clics en enlaces internos
        document.addEventListener('click', function(e) {
            var a = e.target.closest('a[href]');
            if (!a) return;
            var href = a.getAttribute('href');
            // Ignorar: anclas, javascript:, target=_blank, externos
            if (!href || href.startsWith('#') || href.startsWith('javascript')
                || a.target === '_blank' || href.startsWith('http')) return;
            showLoader();
        });

        // Interceptar envío de formularios
        document.addEventListener('submit', function() {
            showLoader();
        });

        // Ocultar cuando la página ya cargó (por si el usuario vuelve con el botón atrás)
        window.addEventListener('pageshow', hideLoader);
    })();
</script>

<?php include 'sidebar.php'; ?>

<main class="main-content">
    <header class="topbar">
        <span class="topbar-label">Panel de Control</span>

        <div class="user-menu" onclick="this.querySelector('.dropdown').classList.toggle('open')">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['nombre_completo'], 0, 1)) ?></div>
            <div class="user-meta">
                <strong><?= htmlspecialchars($_SESSION['nombre_completo']) ?></strong>
                <small><?= htmlspecialchars($_SESSION['rol']) ?></small>
            </div>
            <i class="fas fa-chevron-down" style="font-size:10px; color:var(--text-muted); margin-left:2px;"></i>

            <div class="dropdown" id="userDropdown">
                <a href="../Dashboard/perfil.php"><i class="fas fa-user" style="width:14px;"></i> Mi perfil</a>
                <div class="dropdown-divider"></div>
                <a href="../Auth/logout.php" class="danger"><i class="fas fa-arrow-right-from-bracket" style="width:14px;"></i> Cerrar sesión</a>
            </div>
        </div>
    </header>

    <div class="page-content">
