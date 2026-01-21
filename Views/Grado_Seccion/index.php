<?php
// 1. CARGAR LAYOUT
ob_start();
require_once '../Layout/header.php';

require_once '../../Controllers/Gestion_Institucional/GradoSeccionController.php';
$control = new GradoSeccionController();

// Manejo de eliminación
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $control->eliminar($_GET['id']);
}

$datos = $control->index();

// Si hay error (no hay periodo activo)
if (isset($datos['error'])) {
    echo "<div class='error-container'><div class='error-box'><h3>⛔ " . $datos['error'] . "</h3></div></div>";
    exit;
}

$grados = $datos['grados'];
$periodo = $datos['periodo'];
?>

<style>
    :root {
        --primary-color: #3b82f6;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --bg-body: #f1f5f9;
        --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    /* Estilo del contenedor principal */
    .main-wrapper {
        padding: 2rem;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .header-dashboard {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .header-title h2 {
        font-size: 1.875rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }

    .period-tag {
        font-size: 0.875rem;
        background: #dbeafe;
        color: #1e40af;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 600;
        margin-left: 0.5rem;
    }

    /* Grid de Grados */
    .grados-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    /* Tarjeta de Grado */
    .grado-card {
        background: white;
        border-radius: 1rem;
        box-shadow: var(--card-shadow);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .grado-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        border-color: var(--primary-color);
    }

    .grado-header {
        padding: 1.25rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-radius: 1rem 1rem 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .grado-title { 
        margin: 0; 
        font-size: 1.125rem; 
        font-weight: 700; 
        color: #1e293b; 
    }

    .grado-badge { 
        font-size: 0.75rem; 
        background: #f1f5f9; 
        padding: 0.2rem 0.6rem; 
        border-radius: 6px; 
        color: #475569; 
        text-transform: uppercase;
        font-weight: 600;
        display: inline-block;
        margin-top: 0.25rem;
    }

    /* Lista de Secciones */
    .seccion-list { list-style: none; padding: 0; margin: 0; }
    
    .seccion-item {
        padding: 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s;
    }

    .seccion-item:last-child { border-bottom: none; }
    .seccion-item:hover { background: #f8fafc; }

    .sec-nombre { 
        font-weight: 700; 
        color: var(--primary-color); 
        font-size: 1rem; 
        display: block;
        margin-bottom: 0.25rem;
    }

    .sec-info { 
        font-size: 0.813rem; 
        color: #64748b; 
        line-height: 1.5;
    }

    .sec-info i { width: 16px; margin-right: 4px; color: #94a3b8; }
    
    /* Botones y Acciones */
    .sec-actions { display: flex; gap: 0.5rem; }

    .btn-action {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        text-decoration: none !important;
    }

    .btn-edit { background: #fffbeb; color: var(--warning-color); }
    .btn-edit:hover { background: var(--warning-color); color: white; }

    .btn-del { background: #fef2f2; color: var(--danger-color); }
    .btn-del:hover { background: var(--danger-color); color: white; }

    .btn-add { 
        background: var(--success-color); 
        color: white !important; 
        padding: 0.5rem 1rem; 
        border-radius: 0.75rem; 
        font-size: 0.875rem; 
        font-weight: 600; 
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-add:hover { background: #059669; transform: scale(1.05); }

    .alert-custom {
        background: #ecfdf5;
        border-left: 4px solid var(--success-color);
        color: #065f46;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .empty-msg { 
        padding: 3rem 1rem; 
        text-align: center; 
        color: #94a3b8; 
        font-size: 0.875rem; 
        font-style: italic; 
    }
</style>

<div class="main-wrapper">
    <div class="header-dashboard">
        <div class="header-title">
            <h2>
                <i class="fas fa-th-large" style="color:var(--primary-color);"></i> 
                Gestión de Secciones 
                <span class="period-tag">Periodo <?= $periodo['anio'] ?></span>
            </h2>
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert-custom">
            <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
            <span>Operación realizada con éxito. Los cambios se han guardado.</span>
        </div>
    <?php endif; ?>

    <div class="grados-container">
        <?php foreach($grados as $g): ?>
            
            <div class="grado-card">
                <div class="grado-header">
                    <div>
                        <h4 class="grado-title"><?= $g['nombreGrado'] ?></h4>
                        <span class="grado-badge"><?= $g['nivel'] ?></span>
                    </div>
                    <a href="seccion_form.php?idGrado=<?= $g['idGrado'] ?>" class="btn-add">
                        <i class="fas fa-plus"></i> Sección
                    </a>
                </div>

                <?php if(count($g['secciones']) > 0): ?>
                    <ul class="seccion-list">
                        <?php foreach($g['secciones'] as $sec): ?>
                            <li class="seccion-item">
                                <div>
                                    <span class="sec-nombre">Sección "<?= $sec['nombreSeccion'] ?>"</span>
                                    <div class="sec-info">
                                        <div><i class="fas fa-users"></i> <strong><?= $sec['vacantes'] ?></strong> vacantes</div>
                                        <div><i class="fas fa-user-tie"></i> <?= $sec['nombres'] . " " . $sec['apellidoPaterno'] ?></div>
                                    </div>
                                </div>
                                <div class="sec-actions">
                                    <a href="seccion_form.php?idSeccion=<?= $sec['idSeccion'] ?>&idGrado=<?= $g['idGrado'] ?>" class="btn-action btn-edit" title="Editar">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a href="index.php?action=delete&id=<?= $sec['idSeccion'] ?>" 
                                       class="btn-action btn-del" title="Eliminar"
                                       onclick="return confirm('¿Seguro que deseas eliminar la sección <?= $sec['nombreSeccion'] ?>?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-msg">
                        <i class="fas fa-info-circle" style="display:block; font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                        No hay secciones registradas
                    </div>
                <?php endif; ?>
            </div>

        <?php endforeach; ?>
    </div>
</div>

</body>
</html>