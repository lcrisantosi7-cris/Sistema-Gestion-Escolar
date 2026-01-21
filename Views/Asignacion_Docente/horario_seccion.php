<?php
ob_start();
require_once '../Layout/header.php'; // LAYOUT

require_once '../../Controllers/Gestion_Institucional/AsignacionController.php';
$control = new AsignacionController();

$idSeccion = $_GET['idSeccion'] ?? null;
if (!$idSeccion) {
    echo "
    <div style='max-width:500px; margin: 100px auto; text-align:center; background:white; padding:40px; border-radius:20px; box-shadow:0 10px 25px rgba(0,0,0,0.05);'>
        <h3 style='color:#e11d48; margin-bottom:20px;'>⛔ No se ha seleccionado una sección.</h3>
        <a href='index.php' style='text-decoration:none; color:#3b82f6; font-weight:700;'>← Volver al listado</a>
    </div>";
    exit;
}

$data = $control->verHorarioSeccion($idSeccion);
$info = $data['infoSeccion']; 
$horario = $data['horario'];

// Agrupar por día
$horarioPorDia = ['Lunes'=>[], 'Martes'=>[], 'Miercoles'=>[], 'Jueves'=>[], 'Viernes'=>[]];
foreach ($horario as $h) {
    if (array_key_exists($h['diaSemana'], $horarioPorDia)) {
        $horarioPorDia[$h['diaSemana']][] = $h;
    }
}
?>

<style>
    :root {
        --primary: #3b82f6;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --bg-soft: #f8fafc;
        --border-color: #e2e8f0;
    }

    .enterprise-container {
        max-width: 1300px;
        margin: 30px auto;
        padding: 0 20px;
    }

    .card-ent {
        background: white;
        border-radius: 24px;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    /* Cabecera */
    .header-info-ent {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        padding: 30px 40px;
        border-bottom: 2px solid var(--bg-soft);
    }

    .title-group h2 {
        margin: 0;
        color: var(--text-main);
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .badge-section {
        background: #eff6ff;
        color: var(--primary);
        padding: 6px 14px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        display: inline-block;
        margin-top: 8px;
    }

    /* Grid del Horario */
    .schedule-grid-ent {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1px; /* Para simular bordes finos entre columnas */
        background: var(--border-color);
        padding: 1px;
    }

    .day-col-ent {
        background: white;
        min-height: 500px;
    }

    .day-header-ent {
        background: #f8fafc;
        color: var(--text-main);
        padding: 20px 10px;
        text-align: center;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 1px;
        border-bottom: 2px solid var(--border-color);
    }

    /* Tarjetas de Clase */
    .class-card-ent {
        padding: 20px;
        border-bottom: 1px solid var(--bg-soft);
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 4px solid transparent;
        cursor: default;
    }

    .class-card-ent:hover {
        background: #f0f9ff;
        border-left-color: var(--primary);
        transform: scale(1.02);
        z-index: 10;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }

    .time-badge-ent {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: var(--primary);
        background: #e0f2fe;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 800;
        margin-bottom: 12px;
    }

    .course-title-ent {
        display: block;
        color: var(--text-main);
        font-weight: 800;
        font-size: 14px;
        line-height: 1.4;
        margin-bottom: 8px;
    }

    .teacher-box-ent {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .teacher-box-ent i { font-size: 10px; color: #94a3b8; }

    .empty-msg-ent {
        padding: 60px 20px;
        text-align: center;
        color: #cbd5e1;
        font-size: 13px;
        font-style: italic;
    }

    .btn-back-ent {
        background: #f1f5f9;
        color: #475569;
        padding: 12px 20px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-back-ent:hover { background: #e2e8f0; color: #1e293b; }
</style>

<div class="enterprise-container">
    <div class="card-ent">
        
        <div class="header-info-ent">
            <div class="title-group">
                <h2><i class="fas fa-calendar-alt" style="color: var(--primary); margin-right: 12px;"></i>Visor de Horario Semanal</h2>
                <?php if($info): ?>
                    <div class="badge-section">
                        <i class="fas fa-chalkboard"></i>
                        <?= ($info['nombreGrado'] ?? 'Grado') . " '" . ($info['nombreSeccion'] ?? '?') . "' — " . ($info['nivel'] ?? 'Nivel') ?>
                    </div>
                <?php else: ?>
                    <div class="badge-section" style="background:#fef2f2; color:#b91c1c;">
                        <i class="fas fa-exclamation-circle"></i> Datos de sección no encontrados
                    </div>
                <?php endif; ?>
            </div>
            
            <a href="index.php" class="btn-back-ent">
                <i class="fas fa-chevron-left"></i> Regresar al Directorio
            </a>
        </div>

        <div class="schedule-grid-ent">
            <?php foreach ($horarioPorDia as $dia => $clases): ?>
                <div class="day-col-ent">
                    <div class="day-header-ent"><?= $dia ?></div>
                    
                    <?php if (empty($clases)): ?>
                        <div class="empty-msg-ent">
                            <i class="fas fa-coffee" style="display:block; margin-bottom:10px; font-size:20px;"></i>
                            Sin actividades
                        </div>
                    <?php else: ?>
                        <?php foreach ($clases as $clase): ?>
                            <div class="class-card-ent">
                                <div class="time-badge-ent">
                                    <i class="far fa-clock"></i> 
                                    <?= substr($clase['horaInicio'], 0, 5) ?> - <?= substr($clase['horaFin'], 0, 5) ?>
                                </div>
                                
                                <span class="course-title-ent"><?= $clase['nombreCurso'] ?></span>
                                
                                <div class="teacher-box-ent">
                                    <i class="fas fa-user-tie"></i> 
                                    <span><?= explode(' ', $clase['nombres'])[0] . " " . $clase['apellidoPaterno'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>