<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Asistencia/AsistenciaController.php';
$control = new AsistenciaController();
$idAsig  = $_GET['idAsig'] ?? null;
$idSec   = $_GET['idSec']  ?? null;
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') { $mensaje = $control->guardar($_POST); }
$alumnos = $control->nueva($idAsig, $idSec);
?>
<style>
    .attendance-wrap { max-width: 720px; margin: 0 auto; }
    .date-bar {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }
    .date-bar label { font-size: 12px; font-weight: 600; color: var(--text-secondary); white-space: nowrap; }

    .attendance-table { width: 100%; border-collapse: collapse; }
    .attendance-table th {
        padding: 10px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-secondary);
        background: var(--gray-50);
        border-bottom: 1px solid var(--border);
    }
    .attendance-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--gray-100);
        vertical-align: middle;
    }
    .attendance-table tr:last-child td { border-bottom: none; }
    .attendance-table tr:hover td { background: var(--gray-50); }

    .radio-group { display: flex; gap: 6px; }
    .radio-opt {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.15s;
        user-select: none;
    }
    .radio-opt input[type="radio"] { display: none; }
    .radio-opt.present { color: var(--color-success); }
    .radio-opt.absent  { color: var(--color-danger); }
    .radio-opt.excused { color: var(--color-warning); }
    .radio-opt:has(input:checked).present { background: var(--color-success-bg); border-color: var(--color-success); }
    .radio-opt:has(input:checked).absent  { background: var(--color-danger-bg);  border-color: var(--color-danger); }
    .radio-opt:has(input:checked).excused { background: var(--color-warning-bg); border-color: var(--color-warning); }
</style>

<div class="attendance-wrap">
    <div class="page-header">
        <div>
            <div class="page-title"><i class="fas fa-clipboard-check"></i> Registrar Asistencia</div>
            <div class="page-subtitle">Complete el listado de asistencia del día</div>
        </div>
        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-danger"><i class="fas fa-triangle-exclamation"></i> <?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="idAsignacion" value="<?= $idAsig ?>">

        <div class="date-bar">
            <label><i class="fas fa-calendar-day"></i> Fecha:</label>
            <input class="form-control" type="date" name="fecha" value="<?= date('Y-m-d') ?>" style="max-width:180px;">
        </div>

        <div class="card">
            <div class="table-wrap">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Apellidos y nombres</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alumnos as $i => $alum): ?>
                            <tr>
                                <td style="color:var(--text-muted); font-size:12px;"><?= $i + 1 ?></td>
                                <td class="fw-600"><?= htmlspecialchars(mb_strtoupper($alum['apellidoPaterno'] . ' ' . $alum['apellidoMaterno'] . ', ' . $alum['nombres'])) ?></td>
                                <td>
                                    <div class="radio-group">
                                        <label class="radio-opt present">
                                            <input type="radio" name="asistencia[<?= $alum['idMatricula'] ?>]" value="Asistio" checked>
                                            <i class="fas fa-check"></i> Asistió
                                        </label>
                                        <label class="radio-opt absent">
                                            <input type="radio" name="asistencia[<?= $alum['idMatricula'] ?>]" value="Falto">
                                            <i class="fas fa-xmark"></i> Faltó
                                        </label>
                                        <label class="radio-opt excused">
                                            <input type="radio" name="asistencia[<?= $alum['idMatricula'] ?>]" value="Justifico">
                                            <i class="fas fa-file-lines"></i> Justificó
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top:20px; display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-floppy-disk"></i> Guardar asistencia
            </button>
        </div>
    </form>
</div>

</div></main></body></html>
