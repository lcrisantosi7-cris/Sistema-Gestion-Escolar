<?php
ob_start();
// 1. CARGAR LAYOUT
require_once '../Layout/header.php';

require_once '../../Controllers/Gestion_Institucional/CursoController.php';
$control = new CursoController();

$niveles = $control->cargarNiveles();
$curso = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = $control->guardarCurso($_POST);
    if ($res == "OK") {
        echo "<script>window.location.href='index.php';</script>";
        exit;
    } else {
        $error = $res;
    }
}
?>

<style>
    :root {
        --primary: #3b82f6;
        --success: #10b981;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --bg-soft: #f8fafc;
        --border-color: #e2e8f0;
    }

    .form-card-enterprise {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
        max-width: 650px;
        margin: 50px auto;
    }

    .form-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-main);
        text-align: center;
        margin-bottom: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .form-title i { color: var(--primary); }

    .form-group-ent { margin-bottom: 25px; }

    .form-label-ent {
        display: block;
        font-weight: 700;
        color: var(--text-muted);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    .form-control-ent {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        font-family: inherit;
        font-size: 15px;
        transition: 0.2s;
        box-sizing: border-box;
        background-color: var(--bg-soft);
    }

    .form-control-ent:focus {
        outline: none;
        border-color: var(--primary);
        background-color: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* Caja de Competencias (Estilo Enterprise) */
    .comp-container {
        background: var(--bg-soft);
        border: 1px solid var(--border-color);
        padding: 25px;
        border-radius: 16px;
        position: relative;
        margin-top: 10px;
    }

    .comp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .btn-add-comp-ent {
        background: white;
        color: var(--primary);
        border: 1px solid var(--primary);
        padding: 6px 14px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 700;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-add-comp-ent:hover {
        background: var(--primary);
        color: white;
    }

    .comp-item { 
        display: flex; 
        margin-bottom: 12px; 
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Botones de Acción */
    .actions-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 30px;
    }

    .btn-save-ent { 
        background: var(--success); 
        color: white; 
        border: none;
        padding: 16px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-save-ent:hover { background: #059669; transform: translateY(-2px); }

    .btn-cancel-ent {
        padding: 14px;
        text-align: center;
        text-decoration: none;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 14px;
        border-radius: 12px;
        transition: 0.2s;
    }
    
    .btn-cancel-ent:hover { color: var(--text-main); background: #f1f5f9; }

    .error-alert {
        background: #fef2f2;
        color: #991b1b;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 25px;
        border: 1px solid #fee2e2;
        font-size: 14px;
    }
</style>

<div class="form-card-enterprise">
    <h2 class="form-title">
        <i class="fas fa-graduation-cap"></i> Registrar Nuevo Curso
    </h2>
    
    <?php if(isset($error)): ?>
        <div class="error-alert">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="post">
        
        <div class="form-group-ent">
            <label class="form-label-ent">Nombre del Curso</label>
            <input type="text" name="nombreCurso" required class="form-control-ent" placeholder="Ej: Comunicación Integral">
        </div>

        <div class="form-group-ent">
            <label class="form-label-ent">Nivel Educativo</label>
            <select name="idNivel" required class="form-control-ent">
                <option value="" disabled selected>Seleccione un nivel...</option>
                <?php foreach($niveles as $n): ?>
                    <option value="<?= $n['idNivel'] ?>"><?= $n['nivel'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group-ent">
            <div class="comp-header">
                <label class="form-label-ent" style="margin-bottom: 0;">Competencias del Curso</label>
                <button type="button" onclick="agregarCampo()" class="btn-add-comp-ent">
                    <i class="fas fa-plus"></i> Añadir otra
                </button>
            </div>
            
            <div class="comp-container">
                <div id="lista-competencias">
                    <div class="comp-item">
                        <input type="text" name="nuevasCompetencias[]" class="form-control-ent" placeholder="Ej: Lee diversos tipos de textos...">
                    </div>
                </div>
            </div>
        </div>

        <div class="actions-grid">
            <button type="submit" class="btn-save-ent">
                <i class="fas fa-save"></i> Guardar Curso Académico
            </button>
            <a href="index.php" class="btn-cancel-ent">Volver al listado</a>
        </div>
    </form>
</div>

<script>
    function agregarCampo() {
        const contenedor = document.getElementById('lista-competencias');
        const nuevoItem = document.createElement('div');
        nuevoItem.className = 'comp-item';
        
        nuevoItem.innerHTML = `
            <input type="text" name="nuevasCompetencias[]" 
                   class="form-control-ent" 
                   placeholder="Descripción de la competencia..."
                   style="border-color: var(--primary);">
        `;
        
        contenedor.appendChild(nuevoItem);
        // Enfocar automáticamente el nuevo campo
        nuevoItem.querySelector('input').focus();
    }
</script>