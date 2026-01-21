<?php 
require_once '../Layout/header.php'; 
require_once '../../Models/Administracion/Personal.php';
require_once '../../Models/Gestion_Institucional/Asignacion.php';
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';

$modPersonal = new Personal();
$datosUser = $modPersonal->obtenerPorId($_SESSION['personal_id']);
$iniciales = strtoupper(substr($datosUser['nombres'], 0, 1) . substr($datosUser['apellidoPaterno'], 0, 1));

$rol = $_SESSION['rol'];
$clasesHoy = [];
$diaActualES = "";

if ($rol == 'Docente') {
    $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sabado', 7 => 'Domingo'];
    $numDia = date('N');
    $diaActualES = $dias[$numDia] ?? 'Domingo';
    $modPer = new PeriodoAcademico();
    $periodo = $modPer->listar_Periodo_activo();
    if ($periodo) {
        $modAsig = new Asignacion();
        $clasesHoy = $modAsig->listarClasesDocenteDia($_SESSION['personal_id'], $diaActualES, $periodo['idPeriodo']);
    }
}
?>

<style>
    .dashboard-container { display: grid; grid-template-columns: 1fr 340px; gap: 30px; }

    /* TARJETAS PRINCIPALES */
    .card-modern { 
        background: white; border-radius: 20px; padding: 30px; 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9; margin-bottom: 30px;
    }

    .welcome-banner { 
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%); 
        color: white; position: relative; overflow: hidden;
    }
    .welcome-banner h2 { margin: 0; font-size: 1.8rem; font-weight: 700; }
    .welcome-banner p { opacity: 0.8; margin: 10px 0 0; }
    .welcome-banner::after {
        content: '\f19d'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
        position: absolute; right: -20px; bottom: -20px; font-size: 120px; opacity: 0.05; transform: rotate(-15deg);
    }

    /* ACCESOS RÁPIDOS */
    .grid-shortcuts { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
    .shortcut-card { 
        padding: 25px; border-radius: 18px; text-decoration: none; transition: 0.3s;
        display: flex; flex-direction: column; gap: 10px; border: 1px solid #f1f5f9; background: white;
    }
    .shortcut-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px -5px rgba(0,0,0,0.05); }
    .shortcut-card i { font-size: 24px; margin-bottom: 5px; }
    .shortcut-card h3 { margin: 0; font-size: 1.1rem; color: var(--text-main); }
    .shortcut-card p { margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.4; }

    .blue-link i { color: #3b82f6; } .green-link i { color: #10b981; } .orange-link i { color: #f59e0b; }

    /* AGENDA DOCENTE */
    .agenda-item { 
        display: flex; gap: 20px; padding: 20px; border-radius: 15px; 
        background: #f8fafc; margin-bottom: 15px; align-items: center; border: 1px solid transparent;
    }
    .agenda-item:hover { border-color: #e2e8f0; background: white; }
    .time-box { 
        background: white; padding: 10px; border-radius: 12px; min-width: 75px; 
        text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    .time-box b { color: var(--primary); font-size: 14px; display: block; }
    .time-box span { font-size: 10px; color: var(--text-muted); font-weight: 700; }

    /* PERFIL LATERAL */
    .profile-sidebar { position: sticky; top: 100px; }
    .id-card { 
        background: white; border-radius: 20px; overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;
    }
    .id-card-header { background: #f8fafc; padding: 30px; text-align: center; border-bottom: 1px dotted #e2e8f0; }
    .id-avatar { 
        width: 100px; height: 100px; background: white; border-radius: 30px; 
        margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;
        font-size: 35px; font-weight: 800; color: var(--sidebar-bg);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
    }
    .id-card-body { padding: 25px; }
    .info-group { margin-bottom: 15px; }
    .info-group label { display: block; font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
    .info-group span { font-size: 14px; font-weight: 600; color: var(--text-main); }

    .social-btns { display: flex; justify-content: center; gap: 15px; margin-top: 20px; }
    .social-btns a { 
        width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        text-decoration: none; font-size: 18px; transition: 0.2s; background: #f1f5f9;
    }
    .social-btns a:hover { transform: scale(1.1); background: var(--primary); color: white; }
</style>

<div class="dashboard-container">
    <div class="main-section">
        <div class="card-modern welcome-banner">
            <h2>¡Hola, <?= explode(' ', $datosUser['nombres'])[0] ?>!</h2>
            <p>Bienvenido al Sistema de Gestión Escolar. Tienes acceso como <b><?= $_SESSION['rol'] ?></b>.</p>
        </div>

        <?php if ($rol == 'Docente'): ?>
            <div class="card-modern">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
                    <h3 style="margin:0;"><i class="fas fa-calendar-day" style="color:var(--primary); margin-right:10px;"></i> Mi Agenda - <?= $diaActualES ?></h3>
                    <span style="font-size:12px; font-weight:700; color:var(--text-muted);"><?= date('d M, Y') ?></span>
                </div>

                <?php if (empty($clasesHoy)): ?>
                    <div style="text-align:center; padding:50px 0;">
                        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" style="width:80px; opacity:0.3; margin-bottom:15px;">
                        <p style="color:var(--text-muted);">No hay sesiones programadas para hoy.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($clasesHoy as $clase): ?>
                        <div class="agenda-item">
                            <div class="time-box">
                                <b><?= substr($clase['horaInicio'], 0, 5) ?></b>
                                <span>A</span>
                                <b><?= substr($clase['horaFin'], 0, 5) ?></b>
                            </div>
                            <div style="flex:1;">
                                <h4 style="margin:0 0 5px; color:var(--text-main);"><?= $clase['nombreCurso'] ?></h4>
                                <div style="display:flex; gap:15px; font-size:12px; color:var(--text-muted);">
                                    <span><i class="fas fa-users"></i> <?= $clase['nombreGrado'] ?> "<?= $clase['nombreSeccion'] ?>"</span>
                                    <span><i class="fas fa-tag"></i> <?= $clase['nivel'] ?></span>
                                </div>
                            </div>
                            <a href="../Asistencia/index.php" class="btn" style="background:var(--primary); color:white; border-radius:10px; padding:10px 15px; text-decoration:none; font-size:13px; font-weight:600;">Pasar Lista</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid-shortcuts">
                <a href="../Gestion_Estudiantes/nueva_matricula.php" class="shortcut-card blue-link">
                    <i class="fas fa-user-plus"></i>
                    <h3>Nueva Matrícula</h3>
                    <p>Inscribir estudiantes en grados y secciones para este periodo.</p>
                </a>
                <a href="../Boleta_Notas/index.php" class="shortcut-card green-link">
                    <i class="fas fa-file-invoice"></i>
                    <h3>Boletas de Notas</h3>
                    <p>Consultar y generar reportes académicos por alumno.</p>
                </a>
                <a href="../Administracion/index.php" class="shortcut-card orange-link">
                    <i class="fas fa-user-gear"></i>
                    <h3>Personal</h3>
                    <p>Control de acceso, roles y datos del equipo docente.</p>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <aside class="profile-sidebar">
        <div class="id-card">
            <div class="id-card-header">
                <div class="id-avatar"><?= $iniciales ?></div>
                <h3 style="margin:0; font-size:1.1rem;"><?= $datosUser['nombres'] ?></h3>
                <p style="margin:5px 0 0; font-size:12px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:1px;"><?= $_SESSION['rol'] ?></p>
                
                <div class="social-btns">
                    <a href="https://meet.google.com/" target="_blank" title="Google Meet"><i class="fas fa-video"></i></a>
                    <a href="https://drive.google.com/" target="_blank" title="Google Drive"><i class="fab fa-google-drive"></i></a>
                    <a href="https://mail.google.com/" target="_blank" title="Gmail"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
            
            <div class="id-card-body">
                <div class="info-group">
                    <label>Documento / Sexo</label>
                    <span><?= $datosUser['genero'] == 'M' ? 'Masculino' : 'Femenino' ?></span>
                </div>
                <div class="info-group">
                    <label>Correo Electrónico</label>
                    <span style="font-size:12px; color:var(--primary);"><?= strtolower($datosUser['correo']) ?></span>
                </div>
                <div class="info-group">
                    <label>Estado Laboral</label>
                    <span style="background:#dcfce7; color:#166534; padding:3px 8px; border-radius:6px; font-size:11px;">ACTIVO</span>
                </div>
            </div>
        </div>
    </aside>
</div>

</div> </main> </body>
</html>