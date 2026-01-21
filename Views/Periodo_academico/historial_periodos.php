<?php
require_once '../Layout/header.php'; 
require_once '../../Controllers/Gestion_Institucional/PeriodoController.php';
$control = new PeriodoController();

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $control->Eliminar($_GET['id']);
}

$historial = $control->verHistorial();
?>

<style>
    /* 1. Contenedor y Título Principal */
    .history-card {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        max-width: 1000px;
        margin: 20px auto;
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 15px;
    }

    .header-section h2 {
        color: #1e293b;
        font-weight: 800;
        font-size: 1.5rem;
    }

    /* 2. Tarjetas de Periodo */
    .period-item {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        margin-bottom: 25px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .period-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.08);
    }

    .period-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
        padding: 15px 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .period-info {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #334155;
    }

    .period-year {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1e293b;
    }

    .status-badge {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
        background: #e0f2fe;
        color: #0369a1;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    /* 3. Botón Eliminar */
    .btn-delete-all {
        color: #ef4444;
        background: #fef2f2;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-delete-all:hover {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* 4. Grid de Bimestres */
    .bimestres-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        padding: 20px;
        background: white;
    }

    .bimestre-box {
        border: 1px solid #f1f5f9;
        padding: 15px;
        border-radius: 12px;
        background: #fcfcfc;
        text-align: center;
        border-left: 4px solid #3b82f6; /* Detalle de color */
    }

    .bi-name {
        font-weight: 700;
        color: #3b82f6;
        font-size: 13px;
        margin-bottom: 5px;
    }

    .bi-dates {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }
</style>

<div class="history-card">
    <div class="header-section">
        <h2><i class="fas fa-history" style="color:#64748b; margin-right:10px;"></i> Historial de Periodos</h2>
        <a href="periodo_form.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <?php foreach($historial as $p): ?>
        <div class="period-item">
            
            <div class="period-header">
                <div class="period-info">
                    <i class="fas fa-calendar-check" style="color: #3b82f6;"></i>
                    <span class="period-year">Año <?= $p['anio'] ?></span>
                    <span class="status-badge"><?= $p['estado'] ?></span>
                </div>
                
                <a href="historial_periodos.php?action=delete&id=<?= $p['idPeriodo'] ?>" 
                   onclick="return confirm('ATENCIÓN: Se borrarán todas las secciones, matrículas y notas de este año. ¿Continuar?')"
                   class="btn-delete-all">
                   <i class="fas fa-trash-alt"></i> Eliminar Historial
                </a>
            </div>

            <div class="bimestres-grid">
                <?php foreach($p['bimestres'] as $b): ?>
                    <div class="bimestre-box">
                        <div class="bi-name"><?= $b['nombreBimestre'] ?></div>
                        <div class="bi-dates">
                            <i class="far fa-clock" style="font-size:10px;"></i>
                            <?= date("d/m/Y", strtotime($b['fechaInicio'])) ?> - <?= date("d/m/Y", strtotime($b['fechaFin'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>