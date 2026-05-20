<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Gestion_Institucional/AsignacionController.php';
require_once '../../Models/Gestion_Institucional/Seccion.php';
require_once '../../Models/Gestion_Institucional/PeriodoAcademico.php';
$control = new AsignacionController();
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $control->eliminar($_GET['id']);
}
$asignaciones = $control->index();
$modelSec  = new Seccion();
$modelPer  = new PeriodoAcademico();
$perActivo = $modelPer->listar_Periodo_activo();
$listaSecciones = [];
if ($perActivo) { $listaSecciones = $modelSec->listarParaAsignacion($perActivo['idPeriodo']); }
?>
<style>
    .day-badge {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 700;
    }
    .day-Lunes     { background: #eff6ff; color: #1d4ed8; }
    .day-Martes    { background: #f0fdf4; color: #15803d; }
    .day-Miercoles { background: #fffbeb; color: #b45309; }
    .day-Jueves    { background: #faf5ff; color: #7e22ce; }
    .day-Viernes   { background: #fef2f2; color: #b91c1c; }
</style>

<div class="page-header">
    <div>
        <div class="page-title"><i class="fas fa-calendar-check"></i> Carga Académica</div>
        <div class="page-subtitle">Asignación de docentes a cursos y secciones</div>
    </div>
    <a href="form_asignacion.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva asignación</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><i class="fas fa-circle-check"></i> Asignación guardada correctamente.</div>
<?php endif; ?>

<!-- Horario filter -->
<div class="card" style="padding:20px; margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
        <div>
            <div style="font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:4px;">Consultar horario por sección</div>
            <div style="font-size:12px; color:var(--text-secondary);">Visualiza la distribución semanal de clases</div>
        </div>
        <form action="horario_seccion.php" method="GET" style="display:flex; gap:8px; flex:1; max-width:500px;">
            <select name="idSeccion" required class="form-control">
                <option value="">— Seleccionar sección —</option>
                <?php foreach ($listaSecciones as $s): ?>
                    <option value="<?= $s['idSeccion'] ?>">
                        <?= htmlspecialchars($s['nombreGrado'] . ' "' . $s['nombreSeccion'] . '" — ' . $s['nivel']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary"><i class="fas fa-eye"></i> Ver</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Horario</th>
                    <th>Sección</th>
                    <th>Curso</th>
                    <th>Docente</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($asignaciones)): ?>
                    <?php foreach ($asignaciones as $a): ?>
                        <tr>
                            <td><span class="day-badge day-<?= $a['diaSemana'] ?>"><?= substr($a['diaSemana'], 0, 3) ?></span></td>
                            <td style="font-size:13px; font-weight:600;">
                                <?= substr($a['horaInicio'], 0, 5) ?> — <?= substr($a['horaFin'], 0, 5) ?>
                            </td>
                            <td>
                                <span class="badge badge-gray"><?= htmlspecialchars($a['nombreGrado'] . ' "' . $a['nombreSeccion'] . '"') ?></span>
                                <div style="font-size:11px; color:var(--text-muted); margin-top:2px; text-transform:uppercase; font-weight:600;"><?= htmlspecialchars($a['nivel']) ?></div>
                            </td>
                            <td class="fw-600" style="color:var(--color-primary);"><?= htmlspecialchars($a['nombreCurso']) ?></td>
                            <td style="font-size:13px;"><?= htmlspecialchars($a['nombres'] . ' ' . $a['apellidoPaterno']) ?></td>
                            <td>
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <a href="form_asignacion.php?id=<?= $a['idAsignacion'] ?>&idSeccion=<?= $a['idSeccion'] ?>" class="btn btn-warning btn-icon" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="index.php?action=delete&id=<?= $a['idAsignacion'] ?>"
                                       class="btn btn-danger btn-icon" title="Eliminar"
                                       onclick="return confirm('¿Confirmar eliminación de esta sesión?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <p>No hay asignaciones registradas para este periodo</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div></main></body></html>
