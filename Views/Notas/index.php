<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Notas/NotaController.php';

$control = new NotaController();
$data = $control->index();
$cursos = $data['cursos'];
?>

<style>
    .notes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 25px;
    }

    .card-note {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 24px;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .card-note::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 5px;
        background: #10b981; /* Verde esmeralda para notas */
    }

    .card-note:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1);
        border-color: #10b981;
    }

    .course-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .course-meta {
        font-size: 0.9rem;
        color: var(--text-muted);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .meta-item i {
        color: #10b981;
        width: 16px;
    }

    /* Botón de Acción Interno 🚀 */
    .btn-enter-notes {
        margin-top: 20px;
        padding: 10px;
        background: #f0fdf4;
        color: #166534;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        text-align: center;
        transition: 0.2s;
        border: 1px solid #dcfce7;
    }

    .card-note:hover .btn-enter-notes {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }
</style>

<div class="section-container">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:30px;">
        <div>
            <h2 style="margin:0; font-weight:800; color:var(--text-main); font-size:1.6rem;">
                <i class="fas fa-edit" style="color:#10b981; margin-right:10px;"></i>
                Registro de Calificaciones
            </h2>
            <p style="color:var(--text-muted); margin-top:5px;">Seleccione un curso para gestionar las notas del periodo actual.</p>
        </div>
    </div>

    <?php if(empty($cursos)): ?>
        <div style="background:white; border-radius:16px; padding:60px; text-align:center; border: 1px dashed #cbd5e1;">
            <i class="fas fa-clipboard-list" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
            <span style="color: var(--text-muted); font-size: 1.1rem;">No tienes cursos asignados para calificar todavía.</span>
        </div>
    <?php else: ?>
        <div class="notes-grid">
            <?php foreach($cursos as $c): ?>
                <a href="registro.php?id=<?= $c['idAsignacion'] ?>" class="card-note">
                    <div class="course-header">
                        <div class="course-title"><?= mb_strtoupper($c['nombreCurso']) ?></div>
                        <div class="course-meta">
                            <div class="meta-item">
                                <i class="fas fa-layer-group"></i>
                                <span><?= $c['nombreGrado'] ?> "<?= $c['nombreSeccion'] ?>"</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-graduation-cap"></i>
                                <span>Nivel: <?= $c['nivel'] ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn-enter-notes">
                        INGRESAR NOTAS <i class="fas fa-chevron-right" style="margin-left:5px; font-size:10px;"></i>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php 

?>