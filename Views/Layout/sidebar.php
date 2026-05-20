<style>
    .sidebar {
        width: var(--sidebar-w);
        background: var(--sidebar-bg);
        height: 100vh;
        position: fixed;
        left: 0; top: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Brand */
    .brand {
        height: var(--topbar-h);
        padding: 0 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        flex-shrink: 0;
    }
    .brand-icon {
        width: 30px;
        height: 30px;
        background: var(--color-primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #fff;
        flex-shrink: 0;
    }
    .brand-text { line-height: 1.2; }
    .brand-name {
        font-size: 14px;
        font-weight: 700;
        color: #f1f5f9;
        letter-spacing: -0.01em;
    }
    .brand-sub {
        font-size: 10px;
        color: var(--sidebar-text);
        font-weight: 400;
    }

    /* Nav */
    .nav {
        flex: 1;
        overflow-y: auto;
        padding: 16px 12px;
        scrollbar-width: none;
    }
    .nav::-webkit-scrollbar { display: none; }

    .nav-section {
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(148,163,184,0.5);
        padding: 16px 8px 6px;
    }
    .nav-section:first-child { padding-top: 4px; }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 8px;
        text-decoration: none;
        color: var(--sidebar-text);
        font-size: 13px;
        font-weight: 500;
        transition: all 0.15s;
        margin-bottom: 2px;
    }
    .nav-item:hover {
        background: var(--sidebar-hover);
        color: var(--sidebar-text-active);
    }
    .nav-item.active {
        background: var(--sidebar-active-bg);
        color: #60a5fa;
        font-weight: 600;
    }
    .nav-item i {
        width: 16px;
        text-align: center;
        font-size: 13px;
        flex-shrink: 0;
        opacity: 0.8;
    }
    .nav-item.active i { opacity: 1; }

    /* Footer */
    .sidebar-footer {
        padding: 14px 20px;
        border-top: 1px solid rgba(255,255,255,0.06);
        flex-shrink: 0;
    }
    .sidebar-footer-text {
        font-size: 10px;
        color: rgba(148,163,184,0.4);
        line-height: 1.5;
    }
</style>

<?php
$uri = $_SERVER['REQUEST_URI'];
function isActive($path) {
    global $uri;
    return strpos($uri, $path) !== false ? 'active' : '';
}
?>

<aside class="sidebar">
    <div class="brand">
        <div class="brand-icon"><i class="fas fa-graduation-cap"></i></div>
        <div class="brand-text">
            <div class="brand-name">EduCore</div>
            <div class="brand-sub">Gestión Académica</div>
        </div>
    </div>

    <nav class="nav">
        <div class="nav-section">General</div>
        <a href="../Dashboard/home.php" class="nav-item <?= isActive('Dashboard') ?>">
            <i class="fas fa-house"></i> Inicio
        </a>

        <?php if ($_SESSION['rol'] == 'Docente'): ?>
            <div class="nav-section">Aula</div>
            <a href="../Asistencia/index.php" class="nav-item <?= isActive('Asistencia') ?>">
                <i class="fas fa-clipboard-check"></i> Asistencia
            </a>
            <a href="../Notas/index.php" class="nav-item <?= isActive('Notas') ?>">
                <i class="fas fa-pen-to-square"></i> Calificaciones
            </a>

        <?php else: ?>
            <div class="nav-section">Configuración</div>
            <a href="../Periodo_academico/periodo_form.php" class="nav-item <?= isActive('Periodo_academico') ?>">
                <i class="fas fa-calendar-days"></i> Periodo Académico
            </a>
            <a href="../Grado_Seccion/index.php" class="nav-item <?= isActive('Grado_Seccion') ?>">
                <i class="fas fa-layer-group"></i> Grados y Secciones
            </a>

            <div class="nav-section">Estudiantes</div>
            <a href="../Gestion_Estudiantes/directorio.php" class="nav-item <?= isActive('Gestion_Estudiantes/directorio') ?>">
                <i class="fas fa-users"></i> Directorio
            </a>
            <a href="../Gestion_Estudiantes/nueva_matricula.php" class="nav-item <?= isActive('Gestion_Estudiantes/nueva_matricula') ?>">
                <i class="fas fa-user-plus"></i> Nueva Matrícula
            </a>
            <a href="../Boleta_Notas/index.php" class="nav-item <?= isActive('Boleta_Notas') ?>">
                <i class="fas fa-file-lines"></i> Boletas de Notas
            </a>

            <div class="nav-section">Administración</div>
            <a href="../Administracion/index.php" class="nav-item <?= isActive('Administracion') ?>">
                <i class="fas fa-users-gear"></i> Personal
            </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-footer-text">EduCore v1.0<br>© 2026 Todos los derechos reservados</div>
    </div>
</aside>

<script>
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.user-menu')) {
            document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('open'));
        }
    });
</script>
