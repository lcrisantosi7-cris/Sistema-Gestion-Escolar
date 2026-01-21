<?php require_once '../../Config/session_check.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar | MRCM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --sidebar-bg: #1e293b; 
            --sidebar-hover: #334155;
            --primary: #3b82f6; 
            --bg-body: #f1f5f9;
            --sidebar-w: 270px;
            --topbar-h: 70px;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        body { 
            margin: 0; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-body); 
            display: flex; 
            color: var(--text-main);
        }

        /* Estilos del Main Content */
        .main-content { margin-left: var(--sidebar-w); width: calc(100% - var(--sidebar-w)); min-height: 100vh; }
        
        .topbar { 
            background: white;
            height: var(--topbar-h); 
            padding: 0 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: sticky; top: 0; z-index: 900;
        }

        /* PERFIL DE USUARIO 👤 */
        .user-info { 
            display: flex; align-items: center; gap: 15px; cursor: pointer; 
            padding: 8px 15px; border-radius: 12px; transition: 0.2s; position: relative;
        }
        .user-info:hover { background: #f8fafc; }
        .user-avatar { 
            width: 40px; height: 40px; background: var(--primary); color: white; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; 
        }

        .dropdown-menu { 
            position: absolute; top: 60px; right: 0; background: white; width: 200px; 
            border-radius: 12px; box-shadow: 0 10px 15px rgba(0,0,0,0.1); 
            display: none; border: 1px solid #f1f5f9; padding: 10px; z-index: 1000; 
        }
        .dropdown-menu.show { display: block; }
        .dropdown-menu a { 
            display: flex; align-items: center; gap: 10px; padding: 10px; 
            text-decoration: none; color: var(--text-main); font-size: 14px; border-radius: 8px; 
        }
        .dropdown-menu a:hover { background: #eff6ff; color: var(--primary); }
    </style>
</head>
<body>

<?php include 'sidebar.php'; // Aquí llamamos al menú lateral ⬅️ ?>

<main class="main-content">
    <header class="topbar">
        <div style="color: var(--text-muted); font-weight: 600;">PANEL DE CONTROL</div>
        
        <div class="user-info" onclick="toggleDropdown()">
            <div style="text-align: right; line-height: 1.2;">
                <span style="display:block; font-weight:600;"><?= $_SESSION['nombre_completo'] ?></span>
                <small style="color: var(--primary); font-weight:700;"><?= $_SESSION['rol'] ?></small>
            </div>
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['nombre_completo'], 0, 1)) ?></div>
            
            <div class="dropdown-menu" id="userDropdown">
                <a href="../Dashboard/perfil.php"><i class="fas fa-user-circle"></i> Perfil</a>
                <a href="../Auth/logout.php" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </div>
    </header>

    <div class="page-content" style="padding: 40px;">