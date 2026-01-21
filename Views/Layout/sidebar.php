<style>
    .sidebar { 
        width: var(--sidebar-w); background: var(--sidebar-bg); height: 100vh; 
        position: fixed; left: 0; top: 0; display: flex; flex-direction: column; 
    }
    .brand { 
        height: var(--topbar-h); padding: 0 25px; display: flex; 
        align-items: center; gap: 12px; background: rgba(0,0,0,0.2); 
    }
    .brand i { color: #60a5fa; font-size: 24px; }
    .brand span { color: white; font-weight: 700; font-size: 1.1rem; }

    .menu { list-style: none; padding: 20px 15px; margin: 0; overflow-y: auto; }
    .menu-label { 
        color: #64748b; font-size: 11px; font-weight: 700; 
        text-transform: uppercase; padding: 20px 15px 10px; 
    }
    .menu a { 
        display: flex; align-items: center; gap: 12px; padding: 12px 15px; 
        color: #cbd5e1; text-decoration: none; border-radius: 10px; margin-bottom: 4px; transition: 0.2s;
    }
    .menu a:hover { background: var(--sidebar-hover); color: white; }
    .menu a i { width: 20px; text-align: center; }
    .menu a:hover { 
        background: var(--sidebar-hover); 
        color: white; 
    }
    .menu a:hover i { transform: scale(1.2); color: #60a5fa; }
    .menu a.active { 
        background: rgba(59, 130, 246, 0.15); 
        color: #60a5fa; 
        font-weight: 700;
        box-shadow: inset 4px 0 0 0 #3b82f6;
    }
    .menu a.active i { color: #60a5fa; }
</style>
<?php
$archivo_actual = basename($_SERVER['PHP_SELF']);
?>


<aside class="sidebar">
    <div class="brand">
        <i class="fas fa-graduation-cap"></i>
        <span>I.E. MRCM</span>
    </div>
    
    <ul class="menu">
        <li><a href="../Dashboard/home.php" class="<?= (strpos($_SERVER['REQUEST_URI'], 'Dashboard') !== false ? 'active' : '') ?>">
            <i class="fas fa-chart-pie"></i> Inicio</a></li>

        <?php if ($_SESSION['rol'] == 'Docente'): ?>
            <div class="menu-label">Aula Virtual</div>
            <li><a href="../Asistencia/index.php" class="<?= (strpos($_SERVER['REQUEST_URI'], 'Asistencia') !== false ? 'active' : '') ?>">
                <i class="fas fa-user-check"></i> Asistencia</a></li>
            <li><a href="../Notas/index.php" class="<?= (strpos($_SERVER['REQUEST_URI'], 'Notas') !== false ? 'active' : '') ?>">
                <i class="fas fa-star"></i> Notas</a></li>

        <?php else: // Para Directores y Administrativos 🏛️ ?>
            <div class="menu-label">Configuración</div>
            <li><a href="../Periodo_academico/periodo_form.php" class="<?= (strpos($_SERVER['REQUEST_URI'], 'Periodo_academico') !== false ? 'active' : '') ?>">
                <i class="fas fa-calendar"></i> Periodo</a></li>
            <li><a href="../Grado_Seccion/index.php" class="<?= (strpos($_SERVER['REQUEST_URI'], 'Grado_Seccion') !== false ? 'active' : '') ?>">
                <i class="fas fa-th-large"></i> Grados</a></li>
            
            <div class="menu-label">Gestión</div>
            <li><a href="../Gestion_Estudiantes/directorio.php" class="<?= (strpos($_SERVER['REQUEST_URI'], 'Gestion_Estudiantes/directorio') !== false ? 'active' : '') ?>">
                <i class="fas fa-users"></i> Estudiantes</a></li>
            <li><a href="../Gestion_Estudiantes/nueva_matricula.php" class="<?= (strpos($_SERVER['REQUEST_URI'], 'Gestion_Estudiantes/nueva_matricula') !== false ? 'active' : '') ?>">
                <i class="fas fa-user-plus"></i> Matricular</a></li>
            <li><a href="../Boleta_Notas/index.php" class="<?= (strpos($_SERVER['REQUEST_URI'], 'Boleta_Notas') !== false ? 'active' : '') ?>">
                <i class="fas fa-file-pdf"></i> Boletas</a></li>

            <div class="menu-label">Usuarios</div>
            <li><a href="../Administracion/index.php" class="<?= (strpos($_SERVER['REQUEST_URI'], 'Administracion') !== false ? 'active' : '') ?>">
                <i class="fas fa-user-cog"></i> Usuarios</a></li>
            <li><a href="../Gestion_Estudiantes/directorio.php" class="<?= (strpos($_SERVER['REQUEST_URI'], 'Gestion_Estudiantes/directorio') !== false ? 'active' : '') ?>">
                <i class="fas fa-users"></i> Estudiantes</a></li>

        <?php endif; ?>
    </ul>
    
</aside>

<script>
    function toggleDropdown() {
        document.getElementById("userDropdown").classList.toggle("show");
    }
    // Cerrar si se hace clic fuera
    window.onclick = function(e) {
        if (!e.target.closest('.user-info')) {
            document.getElementById("userDropdown").classList.remove('show');
        }
    }
</script>