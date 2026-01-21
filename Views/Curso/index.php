<?php
ob_start();
// 1. CARGAR LAYOUT
require_once '../Layout/header.php';

require_once '../../Controllers/Gestion_Institucional/CursoController.php';
$control = new CursoController();

$busqueda = $_GET['q'] ?? "";
$cursos = $control->index($busqueda);

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $control->eliminarCurso($_GET['id']);
}
?>

<style>
    /* Contenedor Principal Enterprise */
    .management-card {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        margin: 20px auto;
        max-width: 1200px;
    }

    .main-title {
        font-size: 1.7rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 15px;
    }

    .main-title i { color: #3b82f6; }

    /* Barra de Acciones y Búsqueda */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .search-group {
        display: flex;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 5px;
        border-radius: 12px;
        flex: 1;
        max-width: 500px;
        transition: 0.3s;
    }

    .search-group:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        background: white;
    }

    .input-enterprise {
        border: none;
        background: transparent;
        padding: 10px 15px;
        width: 100%;
        outline: none;
        font-family: inherit;
        font-size: 15px;
        color: #1e293b;
    }

    .btn-search-ent {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 700;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-search-ent:hover { background: #2563eb; }

    /* Botón Nuevo Curso */
    .btn-add-ent {
        background: #10b981;
        color: white;
        padding: 12px 25px;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: 0.3s;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
    }

    .btn-add-ent:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
    }

    /* Tabla Enterprise */
    .table-enterprise {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-enterprise th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 15px 20px;
        border-bottom: 2px solid #f1f5f9;
    }

    .table-enterprise td {
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 15px;
    }

    .table-enterprise tr:hover td { background: #f8fafc; }

    .level-badge {
        background: #eff6ff;
        color: #3b82f6;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    /* Botones de Acción de Tabla */
    .btn-action-ent {
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
    }

    .btn-details-ent { background: #eff6ff; color: #3b82f6; }
    .btn-details-ent:hover { background: #dbeafe; }

    .btn-del-ent { background: #fef2f2; color: #ef4444; margin-left: 8px; }
    .btn-del-ent:hover { background: #fee2e2; }

</style>

<div class="management-card">
    <h2 class="main-title">
        <i class="fas fa-book"></i> Gestión de Cursos Académicos
    </h2>
    
    <div class="action-bar">
        <form action="" method="get" class="search-group">
            <input type="text" name="q" class="input-enterprise" placeholder="Buscar curso por nombre..." value="<?= htmlspecialchars($busqueda) ?>">
            <button type="submit" class="btn-search-ent">
                <i class="fas fa-search"></i> Buscar
            </button>
        </form>
        
        <a href="form_curso.php" class="btn-add-ent">
            <i class="fas fa-plus-circle"></i> Nuevo Curso
        </a>
    </div>

    <table class="table-enterprise">
        <thead>
            <tr>
                <th>Nombre del Curso</th>
                <th>Nivel Educativo</th>
                <th style="width: 280px; text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($cursos) > 0): ?>
                <?php foreach($cursos as $c): ?>
                <tr>
                    <td style="font-weight: 700; color: #1e293b;"><?= $c['nombreCurso'] ?></td>
                    <td>
                        <span class="level-badge">
                            <?= $c['nombreNivel'] ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <a href="detalles_curso.php?id=<?= $c['idCurso'] ?>" class="btn-action-ent btn-details-ent">
                            <i class="fas fa-list-ul"></i> Competencias
                        </a>
                        <a href="index.php?action=delete&id=<?= $c['idCurso'] ?>" 
                           class="btn-action-ent btn-del-ent" 
                           onclick="return confirm('¿Seguro que deseas eliminar este curso?')">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align:center; padding:50px; color:#94a3b8;">
                        <i class="fas fa-search" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                        No se encontraron cursos registrados.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>