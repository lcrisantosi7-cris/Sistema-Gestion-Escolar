<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Asistencia/AsistenciaController.php';
$control = new AsistenciaController();
$data    = $control->index();
$cargas  = $data['cargas'];
?>
<style>
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }
    .course-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        transition: all 0.15s;
        position: relative;
        overflow: hidden;
    }
    .course-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--color-primary);
    }
    .course-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
    .course-name { font-size: 14px; font-weight: 700; }
    .course-meta { display: flex; flex-direction: column; gap: 5px; }
    .course-meta-item { display: flex; align-items: center; gap: 7px; font-size: 12px; color: var(--text-secondary); }
    .course-meta-item i { color: var(--color-primary); width: 13px; }
    .card-actions { display: flex; gap: 8px; }
</style>

<div class="page-header">
    <div>
        <div class="page-title"><i class="fas fa-clipboard-check"></i> Control de Asistencia</div>
        <div class="page-subtitle">Selecciona un curso para registrar o revisar la asistencia</div>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'guardado'): ?>
    <div class="alert alert-success"><i class="fas fa-circle-check"></i> Asistencia registrada correctamente.</div>
<?php endif; ?>

<?php if (empty($cargas)): ?>
    <div class="card" style="padding:0;">
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <p>No tienes cursos asignados para el periodo actual</p>
        </div>
    </div>
<?php else: ?>
    <div class="courses-grid">
        <?php foreach ($cargas as $c): ?>
            <div class="course-card">
                <div>
                    <div class="course-name"><?= htmlspecialchars(mb_strtoupper($c['nombreCurso'])) ?></div>
                    <div class="course-meta" style="margin-top:10px;">
                        <div class="course-meta-item">
                            <i class="fas fa-users"></i>
                            <?= htmlspecialchars($c['nombreGrado'] . ' ' . $c['nombreSeccion']) ?>
                        </div>
                        <div class="course-meta-item">
                            <i class="fas fa-clock"></i>
                            <?= htmlspecialchars($c['diaSemana']) ?> &middot; <?= substr($c['horaInicio'], 0, 5) ?>
                        </div>
                    </div>
                </div>
                <div class="card-actions">
                    <a href="registrar.php?idAsig=<?= $c['idAsignacion'] ?>&idSec=<?= $c['idSeccion'] ?>" class="btn btn-primary btn-sm" style="flex:1; justify-content:center;">
                        <i class="fas fa-pen"></i> Marcar hoy
                    </a>
                    <a href="historial.php?idAsignacion=<?= $c['idAsignacion'] ?>" class="btn btn-secondary btn-sm" style="flex:1; justify-content:center;">
                        <i class="fas fa-list-ul"></i> Historial
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

</div></main></body></html>
