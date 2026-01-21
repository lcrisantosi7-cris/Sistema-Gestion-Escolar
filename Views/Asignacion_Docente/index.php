<?php
ob_start();
// 1. CARGAR LAYOUT
require_once '../Layout/header.php';

require_once '../../Controllers/Gestion_Institucional/AsignacionController.php';
// Modelos extra para el filtro visual
require_once '../../Models/Gestion_Institucional/Seccion.php';
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';

$control = new AsignacionController();

// Lógica Eliminar
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $control->eliminar($_GET['id']);
}

// Cargar Datos
$asignaciones = $control->index();

// Cargar Secciones para filtro de horario
$modelSec = new Seccion();
$modelPer = new PeriodoAcademico();
$perActivo = $modelPer->listar_Periodo_activo();
$listaSecciones = [];
if ($perActivo) {
    $listaSecciones = $modelSec->listarParaAsignacion($perActivo['idPeriodo']);
}
?>

<style>
    :root {
        --primary: #3b82f6;
        --primary-dark: #1d4ed8;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --bg-card: #ffffff;
        --border-color: #e2e8f0;
    }

    .enterprise-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 0 20px;
    }

    /* Card Principal */
    .card-ent {
        background: var(--bg-card);
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
        padding: 35px;
    }

    /* Cabecera */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f8fafc;
    }

    .title-ent {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    /* Panel de Filtro Horario */
    .schedule-panel {
        background: #f0f7ff;
        border: 1px solid #dbeafe;
        border-radius: 18px;
        padding: 25px;
        margin-bottom: 35px;
        display: flex;
        align-items: center;
        gap: 25px;
        position: relative;
        overflow: hidden;
    }

    .schedule-panel::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: var(--primary);
    }

    .filter-icon-box {
        background: white;
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: var(--primary);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }

    .select-ent {
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        min-width: 280px;
        font-size: 14px;
        outline: none;
        transition: 0.2s;
    }

    .select-ent:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* Tabla Enterprise */
    .table-ent { 
        width: 100%; 
        border-collapse: separate; 
        border-spacing: 0;
    }

    .table-ent th {
        background: #f8fafc;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 15px 20px;
        border-bottom: 2px solid #f1f5f9;
        text-align: left;
    }

    .table-ent td {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: var(--text-main);
        vertical-align: middle;
    }

    .table-ent tr:hover td { background: #fcfdfe; }

    /* Badges */
    .badge-day-ent {
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-block;
        width: 45px;
        text-align: center;
    }
    .day-Lunes { background: #eff6ff; color: #1d4ed8; }
    .day-Martes { background: #f0fdf4; color: #15803d; }
    .day-Miercoles { background: #fffbeb; color: #b45309; }
    .day-Jueves { background: #faf5ff; color: #7e22ce; }
    .day-Viernes { background: #fef2f2; color: #b91c1c; }

    .tag-section {
        background: #f1f5f9;
        color: #475569;
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 12px;
    }

    /* Botones */
    .btn-ent {
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 14px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
        text-decoration: none;
    }

    .btn-primary-ent { background: var(--primary); color: white; }
    .btn-primary-ent:hover { background: var(--primary-dark); transform: translateY(-2px); }

    .btn-success-ent { background: var(--success); color: white; }
    .btn-success-ent:hover { background: #059669; }

    .action-icon {
        padding: 8px;
        border-radius: 10px;
        transition: 0.2s;
        font-size: 14px;
    }
    .edit-icon { color: var(--warning); background: #fffbeb; }
    .edit-icon:hover { background: #fef3c7; }
    .del-icon { color: var(--danger); background: #fef2f2; }
    .del-icon:hover { background: #fee2e2; }
</style>

<div class="enterprise-container">
    <div class="card-ent">
        
        <div class="header-section">
            <h2 class="title-ent">
                <i class="fas fa-calendar-check" style="color: var(--primary);"></i>
                Carga Académica Institucional
            </h2>
            <a href="form_asignacion.php" class="btn-ent btn-success-ent">
                <i class="fas fa-plus-circle"></i> Nueva Asignación
            </a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div style="background: #ecfdf5; color: #065f46; padding: 15px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #d1fae5; font-weight: 600;">
                <i class="fas fa-check-circle"></i> Sincronización de horario completada.
            </div>
        <?php endif; ?>

        <div class="schedule-panel">
            <div class="filter-icon-box">
                <i class="fas fa-th-list"></i>
            </div>
            <div style="flex:1">
                <h4 style="margin: 0 0 5px 0; color: var(--primary-dark); font-size: 17px; font-weight: 800;">Consultar Horario Gráfico</h4>
                <p style="margin: 0 0 15px 0; font-size: 13px; color: var(--text-muted);">Visualice la distribución semanal de clases por sección.</p>
                
                <form action="horario_seccion.php" method="get" style="display:flex; gap:12px;">
                    <select name="idSeccion" required class="select-ent">
                        <option value="">-- Seleccionar Sección Académica --</option>
                        <?php foreach($listaSecciones as $s): ?>
                            <option value="<?= $s['idSeccion'] ?>">
                                <?= $s['nombreGrado'] . " '" . $s['nombreSeccion'] . "' — " . $s['nivel'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-ent btn-primary-ent">
                        <i class="fas fa-eye"></i> Ver Horario
                    </button>
                </form>
            </div>
        </div>

        <h3 style="margin-bottom:20px; color: var(--text-main); font-size:15px; text-transform: uppercase; letter-spacing: 0.5px;">
            Listado General de Sesiones
        </h3>

        <table class="table-ent">
            <thead>
                <tr>
                    <th width="100">Día</th>
                    <th width="150">Franja Horaria</th>
                    <th>Sección</th>
                    <th>Asignatura / Área</th>
                    <th>Docente Responsable</th>
                    <th width="100" style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($asignaciones)): ?>
                    <?php foreach($asignaciones as $a): ?>
                    <tr>
                        <td>
                            <span class="badge-day-ent day-<?= $a['diaSemana'] ?>">
                                <?= substr($a['diaSemana'], 0, 3) ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                                <i class="far fa-clock" style="color: var(--text-muted); font-size: 12px;"></i>
                                <?= substr($a['horaInicio'],0,5) ?> - <?= substr($a['horaFin'],0,5) ?>
                            </div>
                        </td>
                        <td>
                            <span class="tag-section">
                                <?= $a['nombreGrado'] . " '" . $a['nombreSeccion'] . "'" ?>
                            </span>
                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;"><?= $a['nivel'] ?></div>
                        </td>
                        <td>
                            <div style="color: var(--primary-dark); font-weight: 700;"><?= $a['nombreCurso'] ?></div>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 12px;">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <span style="font-weight: 500;"><?= $a['nombres'] . " " . $a['apellidoPaterno'] ?></span>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="form_asignacion.php?id=<?= $a['idAsignacion'] ?>&idSeccion=<?= $a['idSeccion'] ?>" class="action-icon edit-icon" title="Editar">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <a href="index.php?action=delete&id=<?= $a['idAsignacion'] ?>" class="action-icon del-icon" 
                                   onclick="return confirm('¿Confirmar la eliminación de esta sesión académica?')" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 60px;">
                            <div style="color: var(--text-muted);">
                                <i class="fas fa-folder-open" style="font-size: 40px; margin-bottom: 15px; opacity: 0.3;"></i>
                                <p style="font-weight: 600;">No se registran asignaciones académicas para este periodo.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>