<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Boleta_Notas/BoletaController.php';
$control     = new BoletaController();
$data        = $control->index();
$filtroNivel = $_GET['filtroNivel'] ?? '';
?>

<div class="page-header">
    <div>
        <div class="page-title"><i class="fas fa-file-lines"></i> Boletas de Notas</div>
        <div class="page-subtitle">
            Periodo <?= htmlspecialchars($data['periodo']['anio'] ?? '---') ?>
            &nbsp;&middot;&nbsp; <?= count($data['estudiantes'] ?? []) ?> alumnos
        </div>
    </div>
</div>

<!-- Filter -->
<div style="margin-bottom:20px;">
    <form method="GET" style="display:flex; gap:10px; align-items:center;">
        <select name="filtroNivel" class="form-control" style="max-width:220px;">
            <option value="">Todos los niveles</option>
            <option value="Primaria"   <?= $filtroNivel == 'Primaria'   ? 'selected' : '' ?>>Primaria</option>
            <option value="Secundaria" <?= $filtroNivel == 'Secundaria' ? 'selected' : '' ?>>Secundaria</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrar</button>
        <?php if ($filtroNivel): ?>
            <a href="index.php" class="btn btn-ghost"><i class="fas fa-xmark"></i> Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Grado / Sección</th>
                    <th>DNI</th>
                    <th>Estudiante</th>
                    <th style="text-align:center;">Boleta</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['estudiantes'])): ?>
                    <?php foreach ($data['estudiantes'] as $e): ?>
                        <tr>
                            <td>
                                <span class="badge badge-gray"><?= htmlspecialchars($e['nombreGrado'] . ' "' . $e['nombreSeccion'] . '"') ?></span>
                                <div style="font-size:11px; color:var(--text-muted); margin-top:3px; text-transform:uppercase; font-weight:600;"><?= htmlspecialchars($e['nivel']) ?></div>
                            </td>
                            <td><span class="font-mono fw-600" style="font-size:13px;"><?= htmlspecialchars($e['dni']) ?></span></td>
                            <td class="fw-600"><?= htmlspecialchars($e['apellidoPaterno'] . ' ' . $e['apellidoMaterno'] . ', ' . $e['nombres']) ?></td>
                            <td style="text-align:center;">
                                <a href="ver_boleta.php?id=<?= $e['idMatricula'] ?>" class="btn btn-primary btn-sm" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Generar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="fas fa-magnifying-glass"></i>
                                <p>No hay registros disponibles para el filtro seleccionado</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div></main></body></html>
