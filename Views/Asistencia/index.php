<?php
ob_start();
// Asegúrate de que la ruta al header sea la correcta según tu estructura
require_once '../Layout/header.php'; 
require_once '../../Controllers/Asistencia/AsistenciaController.php';

$control = new AsistenciaController();
$data = $control->index();
$cargas = $data['cargas'];
?>

<style>
    /* Extendemos los estilos del sistema para las tarjetas de cursos */
    .attendance-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }

    .card-course {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .card-course:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        border-color: var(--primary);
    }

    /* Indicador visual de curso activo */
    .card-course::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 4px;
        background: var(--primary);
        opacity: 0.7;
    }

    .course-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .course-info {
        margin-bottom: 20px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 6px;
    }

    .info-item i {
        color: var(--primary);
        width: 16px;
    }

    .btn-group-attendance {
        display: flex;
        gap: 12px;
    }

    .btn-action {
        flex: 1;
        padding: 10px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-mark {
        background: var(--primary);
        color: white;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }

    .btn-mark:hover {
        background: #2563eb;
        transform: scale(1.02);
    }

    .btn-history {
        background: #f8fafc;
        color: var(--text-main);
        border: 1px solid #e2e8f0;
    }

    .btn-history:hover {
        background: #f1f5f9;
    }

    /* Alertas con estilo moderno */
    .custom-alert {
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        animation: slideIn 0.3s ease-out;
    }

    .alert-success {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="section-header" style="margin-bottom: 30px;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin: 0;">
        <i class="fas fa-calendar-check" style="color: var(--primary); margin-right: 10px;"></i> 
        Control de Asistencia
    </h1>
    <p style="color: var(--text-muted); margin: 5px 0 0 35px;">Selecciona un curso para registrar o revisar la asistencia de hoy.</p>
</div>

<?php if(isset($_GET['msg']) && $_GET['msg']=='guardado'): ?>
    <div class="custom-alert alert-success">
        <i class="fas fa-check-circle"></i>
        Asistencia registrada correctamente en el sistema.
    </div>
<?php endif; ?>

<?php if(empty($cargas)): ?>
    <div style="background: white; border-radius: 16px; padding: 60px; text-align: center; border: 1px dashed #cbd5e1;">
        <i class="fas fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
        <span style="color: var(--text-muted); font-size: 1.1rem;">No tienes cursos asignados para el periodo académico actual.</span>
    </div>
<?php else: ?>
    <div class="attendance-grid">
        <?php foreach($cargas as $c): ?>
            <div class="card-course">
                <div class="course-info">
                    <div class="course-title"><?= mb_strtoupper($c['nombreCurso']) ?></div>
                    
                    <div class="info-item">
                        <i class="fas fa-users"></i>
                        <span><?= $c['nombreGrado'] . " " . $c['nombreSeccion'] ?></span>
                    </div>
                    
                    <div class="info-item">
                        <i class="far fa-clock"></i>
                        <span><?= $c['diaSemana'] ?> | <?= substr($c['horaInicio'], 0, 5) ?></span>
                    </div>
                </div>

                <div class="btn-group-attendance">
                    <a href="registrar.php?idAsig=<?= $c['idAsignacion'] ?>&idSec=<?= $c['idSeccion'] ?>" class="btn-action btn-mark">
                        <i class="fas fa-edit"></i> Marcar Hoy
                    </a>
                    <a href="historial.php?idAsignacion=<?= $c['idAsignacion'] ?>" class="btn-action btn-history">
                        <i class="fas fa-list-ul"></i> Historial
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php 
// No cerramos el body ni el html aquí porque ya lo hace el footer del sistema
// Si no tienes un footer separado, puedes agregarlos al final.
?>