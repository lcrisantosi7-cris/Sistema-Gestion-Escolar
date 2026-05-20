<?php
require_once '../Layout/header.php';
require_once '../../Models/Administracion/Personal.php';
require_once '../../Models/Gestion_Institucional/Asignacion.php';
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';

$modPersonal = new Personal();
$datosUser   = $modPersonal->obtenerPorId($_SESSION['personal_id']);
$iniciales   = strtoupper(substr($datosUser['nombres'], 0, 1) . substr($datosUser['apellidoPaterno'], 0, 1));
$rol         = $_SESSION['rol'];
$clasesHoy   = [];
$diaActualES = '';

if ($rol == 'Docente') {
    $dias = [1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes',6=>'Sabado',7=>'Domingo'];
    $diaActualES = $dias[date('N')] ?? 'Domingo';
    $modPer  = new PeriodoAcademico();
    $periodo = $modPer->listar_Periodo_activo();
    if ($periodo) {
        $modAsig   = new Asignacion();
        $clasesHoy = $modAsig->listarClasesDocenteDia($_SESSION['personal_id'], $diaActualES, $periodo['idPeriodo']);
    }
}
?>
<style>
    .home-grid {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 24px;
        align-items: start;
    }

    /* Welcome */
    .welcome-card {
        background: var(--gray-900);
        border-radius: var(--radius-xl);
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .welcome-card::after {
        content: '\f19d';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: -16px; bottom: -16px;
        font-size: 100px;
        opacity: 0.04;
    }
    .welcome-card h2 { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
    .welcome-card p  { font-size: 13px; color: rgba(255,255,255,0.55); }
    .welcome-card .role-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(37,99,235,0.4);
        color: #93c5fd;
        padding: 3px 10px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 600;
        margin-top: 10px;
    }

    /* Shortcuts */
    .shortcuts-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }
    .shortcut {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px;
        text-decoration: none;
        color: inherit;
        transition: all 0.15s;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .shortcut:hover {
        border-color: var(--color-primary);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }
    .shortcut-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }
    .shortcut h4 { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .shortcut p  { font-size: 12px; color: var(--text-secondary); line-height: 1.4; }

    /* Agenda */
    .agenda-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        overflow: hidden;
    }
    .agenda-header {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .agenda-header h3 { font-size: 14px; font-weight: 600; }
    .agenda-header span { font-size: 12px; color: var(--text-muted); }

    .agenda-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--gray-100);
        transition: background 0.15s;
    }
    .agenda-item:last-child { border-bottom: none; }
    .agenda-item:hover { background: var(--gray-50); }

    .time-pill {
        background: var(--color-primary-bg);
        color: var(--color-primary);
        border-radius: var(--radius-sm);
        padding: 6px 10px;
        text-align: center;
        min-width: 68px;
        flex-shrink: 0;
    }
    .time-pill b { display: block; font-size: 13px; font-weight: 700; }
    .time-pill span { font-size: 10px; font-weight: 500; opacity: 0.7; }

    .agenda-info h4 { font-size: 13px; font-weight: 600; margin-bottom: 3px; }
    .agenda-info p  { font-size: 12px; color: var(--text-secondary); }

    /* Profile card */
    .profile-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        overflow: hidden;
        position: sticky;
        top: calc(var(--topbar-h) + 16px);
    }
    .profile-top {
        padding: 24px 20px;
        text-align: center;
        border-bottom: 1px solid var(--border);
    }
    .profile-avatar {
        width: 64px;
        height: 64px;
        background: var(--gray-900);
        color: #fff;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 12px;
    }
    .profile-top h3 { font-size: 14px; font-weight: 600; }
    .profile-top p  { font-size: 12px; color: var(--color-primary); font-weight: 500; margin-top: 2px; }

    .profile-body { padding: 16px 20px; }
    .profile-row {
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin-bottom: 14px;
    }
    .profile-row:last-child { margin-bottom: 0; }
    .profile-row label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); }
    .profile-row span  { font-size: 13px; font-weight: 500; color: var(--text-primary); }
</style>

<div class="home-grid">
    <!-- Main column -->
    <div>
        <div class="welcome-card">
            <h2>Hola, <?= htmlspecialchars(explode(' ', $datosUser['nombres'])[0]) ?></h2>
            <p>Bienvenido de vuelta al panel de gestión académica.</p>
            <div class="role-pill"><i class="fas fa-circle-dot" style="font-size:8px;"></i> <?= htmlspecialchars($rol) ?></div>
        </div>

        <?php if ($rol == 'Docente'): ?>
            <div class="agenda-card">
                <div class="agenda-header">
                    <h3><i class="fas fa-calendar-day" style="color:var(--color-primary); margin-right:8px;"></i>Agenda — <?= $diaActualES ?></h3>
                    <span><?= date('d M Y') ?></span>
                </div>

                <?php if (empty($clasesHoy)): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-xmark"></i>
                        <p>Sin sesiones programadas para hoy</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($clasesHoy as $clase): ?>
                        <div class="agenda-item">
                            <div class="time-pill">
                                <b><?= substr($clase['horaInicio'], 0, 5) ?></b>
                                <span><?= substr($clase['horaFin'], 0, 5) ?></span>
                            </div>
                            <div class="agenda-info" style="flex:1;">
                                <h4><?= htmlspecialchars($clase['nombreCurso']) ?></h4>
                                <p><?= htmlspecialchars($clase['nombreGrado']) ?> &middot; Sección <?= htmlspecialchars($clase['nombreSeccion']) ?> &middot; <?= htmlspecialchars($clase['nivel']) ?></p>
                            </div>
                            <a href="../Asistencia/index.php" class="btn btn-primary btn-sm">Pasar lista</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="shortcuts-grid">
                <a href="../Gestion_Estudiantes/nueva_matricula.php" class="shortcut">
                    <div class="shortcut-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-user-plus"></i></div>
                    <h4>Nueva Matrícula</h4>
                    <p>Inscribir estudiantes al periodo activo</p>
                </a>
                <a href="../Boleta_Notas/index.php" class="shortcut">
                    <div class="shortcut-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-file-lines"></i></div>
                    <h4>Boletas de Notas</h4>
                    <p>Generar reportes académicos</p>
                </a>
                <a href="../Administracion/index.php" class="shortcut">
                    <div class="shortcut-icon" style="background:#fffbeb; color:#d97706;"><i class="fas fa-users-gear"></i></div>
                    <h4>Personal</h4>
                    <p>Gestión de usuarios y roles</p>
                </a>
                <a href="../Gestion_Estudiantes/directorio.php" class="shortcut">
                    <div class="shortcut-icon" style="background:#fdf4ff; color:#9333ea;"><i class="fas fa-users"></i></div>
                    <h4>Directorio</h4>
                    <p>Consultar estudiantes matriculados</p>
                </a>
                <a href="../Periodo_academico/periodo_form.php" class="shortcut">
                    <div class="shortcut-icon" style="background:#fff1f2; color:#e11d48;"><i class="fas fa-calendar-days"></i></div>
                    <h4>Periodo</h4>
                    <p>Configurar año académico</p>
                </a>
                <a href="../Grado_Seccion/index.php" class="shortcut">
                    <div class="shortcut-icon" style="background:#f0f9ff; color:#0284c7;"><i class="fas fa-layer-group"></i></div>
                    <h4>Secciones</h4>
                    <p>Grados y secciones del periodo</p>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar profile -->
    <aside>
        <div class="profile-card">
            <div class="profile-top">
                <div class="profile-avatar"><?= $iniciales ?></div>
                <h3><?= htmlspecialchars($datosUser['nombres'] . ' ' . $datosUser['apellidoPaterno']) ?></h3>
                <p><?= htmlspecialchars($rol) ?></p>
            </div>
            <div class="profile-body">
                <div class="profile-row">
                    <label>Correo</label>
                    <span style="font-size:12px; color:var(--color-primary);"><?= htmlspecialchars(strtolower($datosUser['correo'])) ?></span>
                </div>
                <div class="profile-row">
                    <label>Teléfono</label>
                    <span><?= htmlspecialchars($datosUser['telefono']) ?></span>
                </div>
                <div class="profile-row">
                    <label>Estado</label>
                    <span><span class="badge badge-green">Activo</span></span>
                </div>
                <div style="margin-top:16px;">
                    <a href="../Dashboard/perfil.php" class="btn btn-secondary" style="width:100%; justify-content:center;">
                        <i class="fas fa-user-pen"></i> Editar perfil
                    </a>
                </div>
            </div>
        </div>
    </aside>
</div>

</div></main></body></html>
