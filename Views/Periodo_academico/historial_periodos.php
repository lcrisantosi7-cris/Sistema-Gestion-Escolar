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
    .period-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        margin-bottom: 16px;
        transition: box-shadow 0.15s;
    }
    .period-card:hover { box-shadow: var(--shadow-md); }
    .period-card-header {
        padding: 14px 20px;
        background: var(--gray-50);
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .period-year { font-size: 15px; font-weight: 700; }
    .bim-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        padding: 16px 20px;
    }
    .bim-box {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 12px;
        border-left: 3px solid var(--color-primary);
    }
    .bim-name { font-size: 12px; font-weight: 700; color: var(--color-primary); margin-bottom: 4px; }
    .bim-dates { font-size: 11px; color: var(--text-secondary); }
</style>

<div class="page-header">
    <div>
        <div class="page-title"><i class="fas fa-clock-rotate-left"></i> Historial de Periodos</div>
        <div class="page-subtitle">Registro de todos los años académicos anteriores</div>
    </div>
    <a href="periodo_form.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<?php if (empty($historial)): ?>
    <div class="card" style="padding:0;">
        <div class="empty-state">
            <i class="fas fa-calendar-xmark"></i>
            <p>No hay periodos en el historial</p>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($historial as $p): ?>
        <div class="period-card">
            <div class="period-card-header">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="period-year">Año <?= htmlspecialchars($p['anio']) ?></div>
                    <span class="badge badge-gray"><?= htmlspecialchars($p['estado']) ?></span>
                </div>
                <a href="historial_periodos.php?action=delete&id=<?= $p['idPeriodo'] ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('ATENCIÓN: Se eliminarán todas las secciones, matrículas y notas de este año. ¿Continuar?')">
                    <i class="fas fa-trash"></i> Eliminar
                </a>
            </div>
            <div class="bim-grid">
                <?php foreach ($p['bimestres'] as $b): ?>
                    <div class="bim-box">
                        <div class="bim-name"><?= htmlspecialchars($b['nombreBimestre']) ?></div>
                        <div class="bim-dates">
                            <?= date('d/m/Y', strtotime($b['fechaInicio'])) ?> — <?= date('d/m/Y', strtotime($b['fechaFin'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</div></main></body></html>
