<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Notas/NotaController.php';
$control = new NotaController();
$data    = $control->index();
$cursos  = $data['cursos'];
?>
<style>
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }
    .course-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: all 0.15s;
        position: relative;
        overflow: hidden;
    }
    .course-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--color-success);
    }
    .course-card:hover {
        border-color: var(--color-success);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }
    .course-name { font-size: 14px; font-weight: 700; color: var(--text-primary); }
    .course-meta { display: flex; flex-direction: column; gap: 5px; }
    .course-meta-item { display: flex; align-items: center; gap: 7px; font-size: 12px; color: var(--text-secondary); }
    .course-meta-item i { color: var(--color-success); width: 13px; }
    .course-cta {
        margin-top: auto;
        padding: 8px;
        background: var(--color-success-bg);
        color: var(--color-success);
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        transition: all 0.15s;
    }
    .course-card:hover .course-cta {
        background: var(--color-success);
        color: #fff;
    }
</style>

<div class="page-header">
    <div>
        <div class="page-title"><i class="fas fa-pen-to-square"></i> Registro de Calificaciones</div>
        <div class="page-subtitle">Selecciona un curso para ingresar o editar notas</div>
    </div>
</div>

<?php if (empty($cursos)): ?>
    <div class="card" style="padding:0;">
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <p>No tienes cursos asignados para el periodo actual</p>
        </div>
    </div>
<?php else: ?>
    <div class="courses-grid">
        <?php foreach ($cursos as $c): ?>
            <a href="registro.php?id=<?= $c['idAsignacion'] ?>" class="course-card">
                <div class="course-name"><?= htmlspecialchars(mb_strtoupper($c['nombreCurso'])) ?></div>
                <div class="course-meta">
                    <div class="course-meta-item">
                        <i class="fas fa-layer-group"></i>
                        <?= htmlspecialchars($c['nombreGrado']) ?> &middot; Sección <?= htmlspecialchars($c['nombreSeccion']) ?>
                    </div>
                    <div class="course-meta-item">
                        <i class="fas fa-graduation-cap"></i>
                        <?= htmlspecialchars($c['nivel']) ?>
                    </div>
                </div>
                <div class="course-cta">Ingresar notas <i class="fas fa-arrow-right" style="font-size:10px;"></i></div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

</div></main></body></html>
