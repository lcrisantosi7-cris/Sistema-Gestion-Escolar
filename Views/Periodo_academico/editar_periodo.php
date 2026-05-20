<?php
ob_start();
require_once '../../Controllers/Gestion_Institucional/PeriodoController.php';
$control = new PeriodoController();
$datos   = $control->obtenerDatosCrudos();
if (empty($datos['idPeriodo'])) {
    require_once '../Layout/header.php';
    echo '<div class="alert alert-danger"><i class="fas fa-circle-exclamation"></i> No hay un periodo activo para editar. <a href="periodo_form.php" class="btn btn-secondary btn-sm" style="margin-left:10px;">Volver</a></div>';
    echo '</div></main></body></html>';
    exit;
}
$idPeriodo     = $datos['idPeriodo'];
$anio          = $datos['anio'];
$inicioPeriodo = $datos['fechaInicio'];
$finPeriodo    = $datos['fechaFin'];
$bi            = $datos['bimestres'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') { $control->GuardarEdicion($_POST); }
function sel($actual, $valor) { return ($actual == $valor) ? 'selected' : ''; }
require_once '../Layout/header.php';
?>
<style>
    .editar-wrap { max-width: 820px; margin: 0 auto; }
    .form-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-xl); overflow: hidden; }
    .form-section { padding: 24px; border-bottom: 1px solid var(--border); }
    .form-section:last-of-type { border-bottom: none; }
    .form-section-title { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 18px; display: flex; align-items: center; gap: 7px; }
    .form-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }

    .bim-row {
        display: grid;
        grid-template-columns: 130px 1fr 1fr 140px;
        gap: 12px;
        align-items: end;
        padding: 12px 0;
        border-bottom: 1px solid var(--gray-100);
    }
    .bim-row:last-child { border-bottom: none; }
    .bim-name { font-size: 12px; font-weight: 700; color: var(--text-primary); padding-bottom: 8px; }

    .form-footer { padding: 20px 24px; background: var(--gray-50); border-top: 1px solid var(--border); display: flex; justify-content: space-between; }
</style>

<div class="editar-wrap">
    <div class="page-header">
        <div>
            <div class="page-title"><i class="fas fa-calendar-pen"></i> Editar Periodo <?= htmlspecialchars($anio) ?></div>
            <div class="page-subtitle">Ajusta fechas y estados de los bimestres</div>
        </div>
        <a href="periodo_form.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <div class="form-card">
        <form method="POST">
            <input type="hidden" name="idPeriodo" value="<?= $idPeriodo ?>">

            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-sliders"></i> Parámetros del periodo</div>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label class="form-label">Año académico</label>
                        <input class="form-control" type="text" value="<?= htmlspecialchars($anio) ?>" disabled style="background:var(--gray-50); color:var(--text-muted);">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha de apertura *</label>
                        <input class="form-control" type="date" name="txtfecha1" value="<?= $inicioPeriodo ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha de clausura *</label>
                        <input class="form-control" type="date" name="txtfecha2" value="<?= $finPeriodo ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-list-check"></i> Cronograma y estados</div>
                <?php
                $nombresBi = ['I Bimestre', 'II Bimestre', 'III Bimestre', 'IV Bimestre'];
                for ($i = 0; $i < 4; $i++):
                    $num = $i + 1;
                ?>
                    <input type="hidden" name="idBi<?= $num ?>" value="<?= $bi[$i]['idBimestre'] ?>">
                    <div class="bim-row">
                        <div class="bim-name"><?= $nombresBi[$i] ?></div>
                        <div class="form-group">
                            <label class="form-label">Inicio</label>
                            <?php if ($i == 0): ?>
                                <input class="form-control" type="text" value="Vinculado a apertura" disabled style="background:var(--gray-50); color:var(--text-muted); font-size:12px;">
                            <?php else: ?>
                                <input class="form-control" type="date" name="txtIni<?= $num ?>" value="<?= $bi[$i]['fechaInicio'] ?>" required>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fin</label>
                            <?php if ($i == 3): ?>
                                <input class="form-control" type="text" value="Vinculado a clausura" disabled style="background:var(--gray-50); color:var(--text-muted); font-size:12px;">
                            <?php else: ?>
                                <input class="form-control" type="date" name="txtFin<?= $num ?>" value="<?= $bi[$i]['fechaFin'] ?>" required>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estado</label>
                            <select class="form-control" name="estadoBi<?= $num ?>">
                                <option value="Pendiente" <?= sel($bi[$i]['estado'], 'Pendiente') ?>>Pendiente</option>
                                <option value="Activo"    <?= sel($bi[$i]['estado'], 'Activo') ?>>Activo</option>
                                <option value="Inactivo"  <?= sel($bi[$i]['estado'], 'Inactivo') ?>>Finalizado</option>
                            </select>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="form-footer">
                <a href="periodo_form.php" class="btn btn-ghost">Descartar</a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-floppy-disk"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

</div></main></body></html>
