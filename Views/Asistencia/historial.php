<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Asistencia/AsistenciaController.php';
$control      = new AsistenciaController();
$idAsignacion = $_GET['idAsignacion'] ?? null;
if (!$idAsignacion) echo "<script>window.location.href='index.php';</script>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') { $control->actualizar($_POST); }
$data     = $control->historial($idAsignacion);
$fechas   = $data['fechas'];
$detalle  = $data['detalle'];
$fechaSel = $data['fechaSel'];
?>

<div class="page-header">
    <div>
        <div class="page-title"><i class="fas fa-clock-rotate-left"></i> Historial de Asistencia</div>
        <div class="page-subtitle">Consulta y edita registros de fechas anteriores</div>
    </div>
    <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<!-- Date filter -->
<div style="margin-bottom:20px;">
    <form method="GET" style="display:flex; gap:10px; align-items:center;">
        <input type="hidden" name="idAsignacion" value="<?= $idAsignacion ?>">
        <select name="fecha" class="form-control" style="max-width:240px;">
            <option value="">— Seleccionar fecha —</option>
            <?php foreach ($fechas as $f): ?>
                <option value="<?= $f['fecha'] ?>" <?= $fechaSel == $f['fecha'] ? 'selected' : '' ?>>
                    <?= date('d/m/Y', strtotime($f['fecha'])) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-secondary">Ver lista</button>
    </form>
</div>

<?php if ($fechaSel && !empty($detalle)): ?>
    <div style="margin-bottom:12px;">
        <span class="badge badge-blue" style="font-size:12px; padding:5px 12px;">
            <i class="fas fa-calendar-day"></i> <?= date('d \d\e F Y', strtotime($fechaSel)) ?>
        </span>
    </div>
    <form method="POST">
        <input type="hidden" name="idAsignacion" value="<?= $idAsignacion ?>">
        <input type="hidden" name="fechaOriginal" value="<?= $fechaSel ?>">

        <div class="card">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Estado actual</th>
                            <th width="200">Modificar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalle as $d): ?>
                            <tr>
                                <td class="fw-600"><?= htmlspecialchars(mb_strtoupper($d['apellidoPaterno'] . ' ' . $d['apellidoMaterno'] . ', ' . $d['nombres'])) ?></td>
                                <td>
                                    <?php
                                        $bc = ($d['estado'] == 'Asistio') ? 'badge-green' : (($d['estado'] == 'Falto') ? 'badge-red' : 'badge-yellow');
                                        $bt = ($d['estado'] == 'Asistio') ? 'Asistió' : (($d['estado'] == 'Falto') ? 'Faltó' : 'Justificó');
                                    ?>
                                    <span class="badge <?= $bc ?>"><?= $bt ?></span>
                                </td>
                                <td>
                                    <select name="asistencia[<?= $d['idAsistencia'] ?>]" class="form-control" style="padding:6px 10px; font-size:13px;">
                                        <option value="Asistio"   <?= $d['estado'] == 'Asistio'   ? 'selected' : '' ?>>Asistió</option>
                                        <option value="Falto"     <?= $d['estado'] == 'Falto'     ? 'selected' : '' ?>>Faltó</option>
                                        <option value="Justifico" <?= $d['estado'] == 'Justifico' ? 'selected' : '' ?>>Justificó</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; margin-top:16px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-floppy-disk"></i> Actualizar cambios
            </button>
        </div>
    </form>
<?php elseif ($fechaSel): ?>
    <div class="card" style="padding:0;">
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No hay registros para la fecha seleccionada</p>
        </div>
    </div>
<?php endif; ?>

</div></main></body></html>
