<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Gestion_Institucional/GradoSeccionController.php';
$control = new GradoSeccionController();
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $control->eliminar($_GET['id']);
}
$datos = $control->index();
if (isset($datos['error'])) {
    echo '<div class="alert alert-danger"><i class="fas fa-circle-exclamation"></i> ' . htmlspecialchars($datos['error']) . '</div>';
    echo '</div></main></body></html>';
    exit;
}
$grados  = $datos['grados'];
$periodo = $datos['periodo'];
?>
<style>
    .grades-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }
    .grade-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: box-shadow 0.15s;
    }
    .grade-card:hover { box-shadow: var(--shadow-md); }
    .grade-card-header {
        padding: 14px 16px;
        background: var(--gray-50);
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .grade-name  { font-size: 14px; font-weight: 700; }
    .grade-level { font-size: 11px; color: var(--text-muted); margin-top: 2px; text-transform: uppercase; font-weight: 600; }

    .section-list { list-style: none; }
    .section-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid var(--gray-100);
        transition: background 0.15s;
    }
    .section-item:last-child { border-bottom: none; }
    .section-item:hover { background: var(--gray-50); }
    .section-name { font-size: 13px; font-weight: 600; color: var(--color-primary); }
    .section-meta { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }
    .section-meta i { width: 13px; color: var(--text-muted); }
    .section-actions { display: flex; gap: 5px; }
</style>

<div class="page-header">
    <div>
        <div class="page-title"><i class="fas fa-layer-group"></i> Grados y Secciones</div>
        <div class="page-subtitle">Periodo <?= htmlspecialchars($periodo['anio']) ?></div>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><i class="fas fa-circle-check"></i> Operación realizada con éxito.</div>
<?php endif; ?>

<?php if (empty($grados)): ?>
    <!-- Sin grados: mostrar aviso con instrucción -->
    <div class="card" style="padding:48px; text-align:center;">
        <div style="width:56px;height:56px;background:var(--gray-100);border-radius:14px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
            <i class="fas fa-layer-group" style="font-size:22px;color:var(--text-muted);"></i>
        </div>
        <div style="font-size:15px;font-weight:600;color:var(--text-primary);margin-bottom:6px;">No hay grados registrados</div>
        <div style="font-size:13px;color:var(--text-secondary);margin-bottom:20px;">
            Primero debes cargar los grados en la base de datos.<br>
            Ejecuta el script <code style="background:var(--gray-100);padding:2px 6px;border-radius:4px;">seed_grados.php</code> para cargarlos automáticamente.
        </div>
        <a href="../../seed_grados.php" class="btn btn-primary">
            <i class="fas fa-database"></i> Cargar Grados Ahora
        </a>
    </div>
<?php else: ?>
    <div class="grades-grid">
        <?php foreach ($grados as $g): ?>
            <div class="grade-card">
                <div class="grade-card-header">
                    <div>
                        <div class="grade-name"><?= htmlspecialchars($g['nombreGrado']) ?></div>
                        <div class="grade-level"><?= htmlspecialchars($g['nivel']) ?></div>
                    </div>
                    <a href="seccion_form.php?idGrado=<?= $g['idGrado'] ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Sección
                    </a>
                </div>

                <?php if (count($g['secciones']) > 0): ?>
                    <ul class="section-list">
                        <?php foreach ($g['secciones'] as $sec): ?>
                            <li class="section-item">
                                <div>
                                    <div class="section-name">Sección "<?= htmlspecialchars($sec['nombreSeccion']) ?>"</div>
                                    <div class="section-meta">
                                        <i class="fas fa-users"></i> <?= htmlspecialchars($sec['vacantes']) ?> vacantes
                                        &nbsp;&middot;&nbsp;
                                        <i class="fas fa-user-tie"></i> <?= htmlspecialchars($sec['nombres'] . ' ' . $sec['apellidoPaterno']) ?>
                                    </div>
                                </div>
                                <div class="section-actions">
                                    <a href="seccion_form.php?idSeccion=<?= $sec['idSeccion'] ?>&idGrado=<?= $g['idGrado'] ?>"
                                       class="btn btn-warning btn-icon" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="index.php?action=delete&id=<?= $sec['idSeccion'] ?>"
                                       class="btn btn-danger btn-icon" title="Eliminar"
                                       onclick="return confirm('¿Eliminar la sección <?= htmlspecialchars($sec['nombreSeccion']) ?>?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state" style="padding:30px;">
                        <i class="fas fa-inbox" style="font-size:24px;"></i>
                        <p style="font-size:12px;">Sin secciones — haz clic en <strong>+ Sección</strong></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

</div></main></body></html>
