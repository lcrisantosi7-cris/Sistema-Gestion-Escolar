<?php
require_once '../../Controllers/Gestion_Institucional/PeriodoController.php';
$control = new PeriodoController();
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = $control->Guardar($_POST);
    if ($res === 'OK_GUARDADO') { header('Location: periodo_form.php?msg=exito'); exit; }
    $mensaje = $res;
}
require_once '../Layout/header.php';
?>
<style>
    .periodo-wrap { max-width: 760px; margin: 0 auto; }
    .form-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-xl); overflow: hidden; }
    .form-section { padding: 24px; border-bottom: 1px solid var(--border); }
    .form-section:last-of-type { border-bottom: none; }
    .form-section-title { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 18px; display: flex; align-items: center; gap: 7px; }
    .form-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }

    .bim-row {
        display: grid;
        grid-template-columns: 140px 1fr 1fr;
        gap: 12px;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--gray-100);
    }
    .bim-row:last-child { border-bottom: none; }
    .bim-name { font-size: 12px; font-weight: 700; color: var(--text-primary); }

    .form-footer { padding: 20px 24px; background: var(--gray-50); border-top: 1px solid var(--border); display: flex; justify-content: space-between; }
</style>

<div class="periodo-wrap">
    <div class="page-header">
        <div>
            <div class="page-title"><i class="fas fa-calendar-plus"></i> Nuevo Periodo Académico</div>
            <div class="page-subtitle">Configura el año lectivo y el cronograma de bimestres</div>
        </div>
        <a href="periodo_form.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-danger"><i class="fas fa-circle-exclamation"></i> <?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-info-circle"></i> Datos generales</div>
                <div class="form-grid-3">
                    <div class="form-group">
                        <label class="form-label">Año académico *</label>
                        <input class="form-control" type="number" name="txtAnio" required placeholder="Ej: 2026">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha de apertura *</label>
                        <input class="form-control" type="date" name="txtfecha1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha de clausura *</label>
                        <input class="form-control" type="date" name="txtfecha2" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-list-check"></i> Cronograma de bimestres</div>
                <div style="font-size:11px; color:var(--text-muted); margin-bottom:14px;">
                    El inicio del I Bimestre y el fin del IV Bimestre se vinculan automáticamente a las fechas del periodo.
                </div>

                <div class="bim-row">
                    <div class="bim-name">I Bimestre</div>
                    <input class="form-control" type="text" value="Vinculado a apertura" disabled style="background:var(--gray-50); color:var(--text-muted); font-size:12px;">
                    <div class="form-group"><label class="form-label">Fin *</label><input class="form-control" type="date" name="txtFin1" required></div>
                </div>
                <div class="bim-row">
                    <div class="bim-name">II Bimestre</div>
                    <div class="form-group"><label class="form-label">Inicio *</label><input class="form-control" type="date" name="txtIni2" required></div>
                    <div class="form-group"><label class="form-label">Fin *</label><input class="form-control" type="date" name="txtFin2" required></div>
                </div>
                <div class="bim-row">
                    <div class="bim-name">III Bimestre</div>
                    <div class="form-group"><label class="form-label">Inicio *</label><input class="form-control" type="date" name="txtIni3" required></div>
                    <div class="form-group"><label class="form-label">Fin *</label><input class="form-control" type="date" name="txtFin3" required></div>
                </div>
                <div class="bim-row">
                    <div class="bim-name">IV Bimestre</div>
                    <div class="form-group"><label class="form-label">Inicio *</label><input class="form-control" type="date" name="txtIni4" required></div>
                    <input class="form-control" type="text" value="Vinculado a clausura" disabled style="background:var(--gray-50); color:var(--text-muted); font-size:12px;">
                </div>
            </div>

            <div class="form-footer">
                <a href="periodo_form.php" class="btn btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-floppy-disk"></i> Registrar periodo
                </button>
            </div>
        </form>
    </div>
</div>

</div></main></body></html>
