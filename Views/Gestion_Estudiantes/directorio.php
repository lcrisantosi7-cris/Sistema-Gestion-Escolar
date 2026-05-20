<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Gestion_Estudiantes/DirectorioController.php';
$control    = new DirectorioController();
$data       = $control->index();
$periodo    = $data['periodo'];
$secciones  = $data['secciones'];
$estudiantes = $data['estudiantes'];
$filtro     = $data['filtro'];
?>

<div class="page-header">
    <div>
        <div class="page-title"><i class="fas fa-users"></i> Directorio de Estudiantes</div>
        <div class="page-subtitle">
            Periodo académico: <strong><?= $periodo ? $periodo['anio'] : 'No definido' ?></strong>
            &nbsp;&middot;&nbsp; <?= count($estudiantes) ?> alumnos
        </div>
    </div>
    <a href="nueva_matricula.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Nueva matrícula</a>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'editado'): ?>
    <div class="alert alert-success"><i class="fas fa-circle-check"></i> Matrícula actualizada correctamente.</div>
<?php endif; ?>

<!-- Filter bar -->
<div style="margin-bottom:20px;">
    <form method="GET" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <select name="filtroSeccion" class="form-control" style="max-width:320px;">
            <option value="">Todas las secciones</option>
            <?php foreach ($secciones as $s): ?>
                <option value="<?= $s['idSeccion'] ?>" <?= $filtro == $s['idSeccion'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['nombreGrado'] . ' ' . $s['nivel'] . ' — ' . $s['nombreSeccion']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrar</button>
        <?php if ($filtro): ?>
            <a href="directorio.php" class="btn btn-ghost"><i class="fas fa-xmark"></i> Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Estudiante</th>
                    <th>Grado y sección</th>
                    <th>Fecha de registro</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($estudiantes) > 0): ?>
                    <?php foreach ($estudiantes as $e): ?>
                        <tr>
                            <td><span class="font-mono fw-600" style="font-size:13px;"><?= htmlspecialchars($e['dni']) ?></span></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:32px; height:32px; background:var(--color-primary-bg); color:var(--color-primary); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0;">
                                        <?= substr($e['apellidoPaterno'], 0, 1) . substr($e['nombres'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <div class="fw-600"><?= htmlspecialchars($e['apellidoPaterno'] . ' ' . $e['apellidoMaterno']) ?></div>
                                        <div style="font-size:12px; color:var(--color-primary);"><?= htmlspecialchars($e['nombres']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-blue"><?= htmlspecialchars($e['nombreGrado'] . ' "' . $e['nombreSeccion'] . '"') ?></span>
                                <div style="font-size:11px; color:var(--text-muted); margin-top:3px; text-transform:uppercase; font-weight:600;"><?= htmlspecialchars($e['nivel']) ?></div>
                            </td>
                            <td style="color:var(--text-secondary); font-size:13px;">
                                <?= date('d M Y', strtotime($e['fecha'])) ?>
                            </td>
                            <td style="text-align:center;">
                                <a href="editar_matricula.php?id=<?= $e['idMatricula'] ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-pen"></i> Editar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>No se encontraron estudiantes con los filtros aplicados</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div></main></body></html>
