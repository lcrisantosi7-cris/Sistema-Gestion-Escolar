<?php
ob_start();
// 1. CARGAR LAYOUT
require_once '../Layout/header.php';

require_once '../../Controllers/Gestion_Institucional/CursoController.php';
$control = new CursoController();

$idCurso = $_GET['id'] ?? null;
if (!$idCurso) echo "<script>window.location.href='index.php';</script>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_competencia'])) {
    $control->guardarCompetencia($_POST);
}

if (isset($_GET['action']) && $_GET['action'] == 'delComp') {
    $control->eliminarCompetencia($_GET['idComp'], $idCurso);
}

$datos = $control->verDetalles($idCurso);
$curso = $datos['curso'];
$competencias = $datos['competencias'];
?>

<style>
    :root {
        --primary: #3b82f6;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --bg-card: #ffffff;
        --border-color: #e2e8f0;
    }

    .enterprise-container {
        max-width: 1100px;
        margin: 30px auto;
        padding: 0 20px;
    }

    /* Cabecera de Información */
    .info-header-ent {
        background: white;
        padding: 30px;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .course-badge {
        background: #eff6ff;
        color: var(--primary);
        padding: 8px 15px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-block;
        margin-top: 8px;
    }

    /* Tabla de Competencias */
    .management-card {
        background: white;
        padding: 35px;
        border-radius: 24px;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
    }

    .table-ent { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 30px; }
    .table-ent th { 
        text-align: left; 
        padding: 15px 20px; 
        background: #f8fafc; 
        color: var(--text-muted); 
        font-size: 12px; 
        text-transform: uppercase; 
        letter-spacing: 1px;
        border-bottom: 2px solid #f1f5f9;
    }

    .table-ent td { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    /* Inputs de Edición */
    .input-edit-ent {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid transparent;
        border-radius: 10px;
        background: #fdfdfd;
        font-family: inherit;
        font-size: 14.5px;
        color: var(--text-main);
        transition: 0.2s;
    }

    .input-edit-ent:focus {
        background: white;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* Botones */
    .btn-ent-sm {
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
        text-decoration: none;
    }

    .btn-update-ent { background: #fffbeb; color: #b45309; }
    .btn-update-ent:hover { background: #fef3c7; }

    .btn-del-ent { background: #fef2f2; color: #ef4444; }
    .btn-del-ent:hover { background: #fee2e2; }

    .btn-back-ent { background: #f1f5f9; color: var(--text-muted); }
    .btn-back-ent:hover { background: #e2e8f0; color: var(--text-main); }

    /* Caja para Agregar Nueva Competencia */
    .quick-add-box {
        background: #f0fdf4;
        padding: 25px;
        border-radius: 18px;
        border: 2px dashed #bbf7d0;
    }

    .input-add-ent {
        flex: 1;
        padding: 14px 20px;
        border: 1px solid #dcfce7;
        border-radius: 14px;
        font-size: 15px;
        outline: none;
    }

    .btn-add-submit-ent {
        background: var(--success);
        color: white;
        padding: 12px 25px;
        border-radius: 14px;
        border: none;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-add-submit-ent:hover { background: #059669; transform: scale(1.02); }
</style>

<div class="enterprise-container">
    
    <div class="info-header-ent">
        <div>
            <h2 style="margin:0; font-weight: 800; color: var(--text-main); letter-spacing: -0.5px;">
                <i class="fas fa-book-open" style="color: var(--primary); margin-right: 10px;"></i>
                <?= $curso['nombreCurso'] ?>
            </h2>
            <div class="course-badge">
                <i class="fas fa-layer-group"></i> Nivel: <?= $curso['nombreNivel'] ?>
            </div>
        </div>
        <a href="index.php" class="btn-ent-sm btn-back-ent">
            <i class="fas fa-chevron-left"></i> Volver al Listado
        </a>
    </div>

    <div class="management-card">
        <h3 style="margin-top: 0; margin-bottom: 25px; font-size: 1.1rem; color: var(--text-main);">
            Gestión de Competencias Curriculares
        </h3>
        
        <table class="table-ent">
            <thead>
                <tr>
                    <th>Descripción Detallada</th>
                    <th width="220" style="text-align: center;">Acciones de Control</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($competencias as $comp): ?>
                <tr>
                    <td>
                        <form action="" method="post" id="form-<?= $comp['idCompetenciaCurso'] ?>" style="margin:0;">
                            <input type="hidden" name="accion_competencia" value="1">
                            <input type="hidden" name="idCurso" value="<?= $idCurso ?>">
                            <input type="hidden" name="idCompetencia" value="<?= $comp['idCompetenciaCurso'] ?>">
                            <input type="text" name="descripcion" 
                                   value="<?= htmlspecialchars($comp['textCompetencia']) ?>" 
                                   class="input-edit-ent">
                        </form>
                    </td>
                    <td style="text-align: center;">
                        <button type="submit" form="form-<?= $comp['idCompetenciaCurso'] ?>" class="btn-ent-sm btn-update-ent">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                        <a href="detalles_curso.php?id=<?= $idCurso ?>&action=delComp&idComp=<?= $comp['idCompetenciaCurso'] ?>" 
                           class="btn-ent-sm btn-del-ent" 
                           onclick="return confirm('¿Está seguro de eliminar esta competencia permanentemente?')">
                           <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($competencias)): ?>
                    <tr>
                        <td colspan="2" style="text-align:center; padding:40px; color: var(--text-muted); font-style: italic;">
                            <i class="fas fa-info-circle" style="display: block; font-size: 2rem; margin-bottom: 10px; opacity: 0.3;"></i>
                            No hay competencias registradas para este curso.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="quick-add-box">
            <h4 style="margin: 0 0 15px 0; font-size: 14px; text-transform: uppercase; color: #15803d; letter-spacing: 1px;">
                <i class="fas fa-plus-circle"></i> Nueva Competencia
            </h4>
            <form action="" method="post" style="display:flex; width:100%; gap:15px;">
                <input type="hidden" name="accion_competencia" value="1">
                <input type="hidden" name="idCurso" value="<?= $idCurso ?>">
                <input type="text" name="descripcion" 
                       placeholder="Escriba aquí la descripción técnica de la competencia..." 
                       required class="input-add-ent">
                <button type="submit" class="btn-add-submit-ent">
                    <i class="fas fa-plus"></i> Registrar
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>