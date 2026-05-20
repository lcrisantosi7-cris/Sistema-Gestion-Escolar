<?php
require_once '../../Controllers/Gestion_Institucional/PeriodoController.php';
$controller = new PeriodoController();
$errorCierre = '';
if (isset($_GET['action']) && $_GET['action'] == 'finalizar') {
    $datosTemp = $controller->obtenerDatosCrudos();
    if (!empty($datosTemp['idPeriodo'])) {
        $resultado = $controller->IntentarCerrarPeriodo($datosTemp['idPeriodo']);
        if ($resultado == 'OK_CERRADO') { header('Location: periodo_form.php?msg=periodo_cerrado'); exit; }
        elseif ($resultado == 'ERROR_BIMESTRES_ACTIVOS') { $errorCierre = 'No se puede finalizar: aún hay bimestres activos o pendientes.'; }
    }
}
$datos = $controller->obtenerDatosVista();
extract($datos);
require_once '../Layout/header.php';
?>
<style>
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 18px 20px;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
    }
    .stat-card.blue::before   { background: var(--color-primary); }
    .stat-card.green::before  { background: var(--color-success); }
    .stat-card.red::before    { background: var(--color-danger); }
    .stat-card.yellow::before { background: var(--color-warning); }
    .stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin-bottom: 6px; }
    .stat-value { font-size: 18px; font-weight: 700; color: var(--text-primary); }
</style>

<div class="page-header">
    <div>
        <div class="page-title"><i class="fas fa-calendar-days"></i> Periodo Académico</div>
        <div class="page-subtitle">Gestión del año lectivo y cronograma de bimestres</div>
    </div>
    <div style="display:flex; gap:8px;">
        <a href="crear_periodo.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo periodo</a>
        <a href="historial_periodos.php" class="btn btn-secondary"><i class="fas fa-clock-rotate-left"></i> Historial</a>
        <?php if (!empty($anio)): ?>
            <a href="editar_periodo.php" class="btn btn-secondary"><i class="fas fa-calendar-pen"></i> Editar fechas</a>
            <a href="periodo_form.php?action=finalizar" class="btn btn-danger"
               onclick="return confirm('¿Confirmar cierre del año académico? Esta acción no se puede deshacer.')">
               <i class="fas fa-lock"></i> Finalizar año
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($errorCierre): ?>
    <div class="alert alert-danger"><i class="fas fa-circle-exclamation"></i> <?= htmlspecialchars($errorCierre) ?></div>
<?php endif; ?>
<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-circle-check"></i>
        <?php
            if ($_GET['msg'] == 'exito')          echo 'Periodo académico registrado exitosamente.';
            elseif ($_GET['msg'] == 'editado')     echo 'Fechas actualizadas correctamente.';
            elseif ($_GET['msg'] == 'periodo_cerrado') echo 'El ciclo académico ha sido finalizado y archivado.';
        ?>
    </div>
<?php endif; ?>

<?php if (!empty($anio)): ?>
    <div class="stats-row">
        <div class="stat-card blue">
            <div class="stat-label">Año lectivo</div>
            <div class="stat-value"><?= htmlspecialchars($anio) ?></div>
        </div>
        <div class="stat-card green">
            <div class="stat-label">Estado</div>
            <div class="stat-value" style="font-size:14px; color:var(--color-success);"><?= htmlspecialchars($estado) ?></div>
        </div>
        <div class="stat-card yellow">
            <div class="stat-label">Apertura</div>
            <div class="stat-value" style="font-size:14px;"><?= htmlspecialchars($textoInicio) ?></div>
        </div>
        <div class="stat-card red">
            <div class="stat-label">Clausura estimada</div>
            <div class="stat-value" style="font-size:14px;"><?= htmlspecialchars($textoFin) ?></div>
        </div>
    </div>

    <div class="card">
        <div style="padding:18px 20px; border-bottom:1px solid var(--border);">
            <div class="page-title" style="font-size:14px;"><i class="fas fa-list-check"></i> Cronograma de Bimestres</div>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Unidad académica</th>
                        <th>Inicio</th>
                        <th>Término</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bimestres as $bi): ?>
                        <tr>
                            <td class="fw-600"><?= htmlspecialchars($bi['nombre']) ?></td>
                            <td style="color:var(--text-secondary);"><?= htmlspecialchars($bi['inicioTexto']) ?></td>
                            <td style="color:var(--text-secondary);"><?= htmlspecialchars($bi['finTexto']) ?></td>
                            <td>
                                <?php if ($bi['estado'] == 'Activo'): ?>
                                    <span class="badge badge-green">En curso</span>
                                <?php elseif ($bi['estado'] == 'Pendiente'): ?>
                                    <span class="badge badge-yellow">Pendiente</span>
                                <?php else: ?>
                                    <span class="badge badge-gray">Finalizado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php else: ?>
    <div class="card" style="padding:60px; text-align:center;">
        <div style="width:56px; height:56px; background:var(--gray-100); border-radius:14px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:16px;">
            <i class="fas fa-calendar-xmark" style="font-size:22px; color:var(--text-muted);"></i>
        </div>
        <div style="font-size:15px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Sin periodo activo</div>
        <div style="font-size:13px; color:var(--text-secondary); margin-bottom:20px;">Inicia un nuevo periodo para gestionar matrículas y calificaciones.</div>
        <a href="crear_periodo.php" class="btn btn-primary">Configurar año escolar</a>
    </div>
<?php endif; ?>

</div></main></body></html>
