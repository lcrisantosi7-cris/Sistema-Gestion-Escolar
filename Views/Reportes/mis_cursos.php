<?php
require_once '../Layout/header.php';
require_once '../../Controllers/Reportes/MisCursosController.php';

$control = new MisCursosController();
$data = $control->index();
$periodo = $data['periodo'];
$horario = $data['horario'];
?>

<style>
    /* Grid de 5 columnas para días */
    .schedule-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr); 
        gap: 15px;
        margin-top: 25px;
    }

    /* Columna de Día */
    .day-column {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        min-height: 400px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }

    .day-header {
        background: #343a40;
        color: white;
        text-align: center;
        padding: 12px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 14px;
        letter-spacing: 1px;
    }

    /* Tarjeta de Clase */
    .class-card {
        padding: 15px;
        border-bottom: 1px solid #eee;
        border-left: 4px solid transparent;
        transition: all 0.2s ease;
        position: relative;
    }

    .class-card:hover {
        background: #f0f7ff;
        border-left-color: #007bff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .time-badge {
        display: inline-block;
        background: #e9ecef;
        color: #555;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        margin-bottom: 6px;
    }

    .course-name {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: #0d47a1;
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .section-info {
        font-size: 12px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 5px;
    }

    .empty-day {
        padding: 50px 10px;
        text-align: center;
        color: #ccc;
        font-style: italic;
        font-size: 13px;
        background: #fafafa;
        height: 100%;
    }
</style>

<div class="card" style="max-width: 1300px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px;">
        <div>
            <h2 style="margin: 0; color: #333;">
                <i class="fas fa-book-reader" style="color: #007bff;"></i> Mis Cursos y Horarios
            </h2>
            <?php if($periodo): ?>
                <span style="color: #666; font-size: 14px; margin-top: 5px; display: block;">
                    Periodo Académico: <strong><?= $periodo['anio'] ?> (Activo)</strong>
                </span>
            <?php endif; ?>
        </div>
        
        <a href="../Dashboard/home.php" class="btn" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: 600; font-size: 14px;">
            <i class="fas fa-home"></i> Volver al Inicio
        </a>
    </div>

    <?php if (!$periodo): ?>
        <div style="text-align: center; padding: 50px; color: #dc3545; background: #fff5f5; border-radius: 8px; margin-top: 20px;">
            <h3>⛔ No hay un periodo académico activo para mostrar horarios.</h3>
        </div>
    <?php else: ?>
        
        <div class="schedule-grid">
            <?php foreach ($horario as $dia => $clases): ?>
                <div class="day-column">
                    <div class="day-header"><?= $dia ?></div>
                    
                    <?php if (empty($clases)): ?>
                        <div class="empty-day">
                            <i class="far fa-smile-beam" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                            Sin clases
                        </div>
                    <?php else: ?>
                        <?php foreach ($clases as $c): ?>
                            <div class="class-card">
                                <span class="time-badge">
                                    <i class="far fa-clock"></i> 
                                    <?= substr($c['horaInicio'], 0, 5) ?> - <?= substr($c['horaFin'], 0, 5) ?>
                                </span>
                                
                                <span class="course-name"><?= $c['nombreCurso'] ?></span>
                                
                                <div class="section-info">
                                    <i class="fas fa-users"></i>
                                    <strong><?= $c['nombreGrado'] . " '" . $c['nombreSeccion'] . "'" ?></strong>
                                    <span style="font-size:11px; margin-left:5px;">(<?= substr($c['nivel'], 0, 4) ?>.)</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>

</body>
</html>