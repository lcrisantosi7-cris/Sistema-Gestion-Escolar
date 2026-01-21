<?php
// 1. CARGAR LAYOUT
ob_start();
require_once '../Layout/header.php';

require_once '../../Controllers/Gestion_Institucional/GradoSeccionController.php';
$control = new GradoSeccionController();

$idGrado = $_GET['idGrado'] ?? null;
$idSeccion = $_GET['idSeccion'] ?? null;
$mensaje = "";

// Procesar Guardado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = $control->guardarSeccion($_POST);
    // Recuperamos los IDs para no perder el contexto si hay error
    $idGrado = $_POST['idGrado'];
    $idSeccion = $_POST['idSeccion'];
}

// Cargar datos
$data = $control->formSeccion($idGrado, $idSeccion);
$seccion = $data['seccion'];
$docentes = $data['docentes'];
$periodo = $data['periodo'];

$titulo = $seccion ? "Editar Sección" : "Nueva Sección";
?>

<style>
    :root {
        --primary-color: #3b82f6;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --bg-input: #f8fafc;
        --border-color: #e2e8f0;
    }

    /* Contenedor principal con animación suave */
    .form-card {
        max-width: 550px;
        margin: 50px auto;
        background: white;
        border-radius: 1.25rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
        overflow: hidden;
        animation: slideUp 0.4s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Encabezado del Formulario */
    .form-header {
        padding: 2rem 2rem 1.5rem;
        text-align: center;
        background: linear-gradient(to bottom, #f8fafc, #ffffff);
        border-bottom: 1px solid var(--border-color);
    }

    .form-header h3 {
        margin: 0;
        color: var(--text-main);
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.025em;
    }

    .period-badge {
        display: inline-block;
        margin-top: 0.5rem;
        font-size: 0.813rem;
        color: var(--primary-color);
        background: #dbeafe;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 600;
    }

    /* Estilos de los campos */
    .form-body { padding: 2rem; }

    .form-group { margin-bottom: 1.5rem; }

    .form-group label {
        display: block;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        font-size: 0.95rem;
        background-color: var(--bg-input);
        transition: all 0.2s ease;
        box-sizing: border-box;
        color: var(--text-main);
    }

    .form-control:focus {
        background-color: white;
        border-color: var(--primary-color);
        outline: 0;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* Campo de identificación (Letra) */
    .input-letter {
        text-transform: uppercase;
        text-align: center;
        font-size: 1.5rem !important;
        font-weight: 800;
        letter-spacing: 4px;
        color: var(--primary-color);
        max-width: 120px;
        margin: 0 auto;
        display: block;
    }

    .helper-text {
        color: var(--text-muted);
        font-size: 0.75rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Alerta de error */
    .error-alert {
        margin: 1.5rem;
        padding: 1rem;
        background: #fef2f2;
        color: #991b1b;
        border-radius: 0.75rem;
        border-left: 4px solid var(--danger-color);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Botones */
    .footer-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-custom {
        padding: 0.875rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-save {
        background: var(--success-color);
        color: white;
        border: none;
    }

    .btn-save:hover { background: #059669; transform: translateY(-2px); }

    .btn-cancel {
        background: #f1f5f9;
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }

    .btn-cancel:hover { background: #e2e8f0; color: var(--text-main); }
</style>

<div class="form-card">
    <div class="form-header">
        <h3><?= $titulo ?></h3>
        <span class="period-badge">Periodo Académico <?= $periodo['anio'] ?></span>
    </div>

    <?php if($mensaje): ?>
        <div class="error-alert">
            <i class="fas fa-exclamation-circle"></i> 
            <span><?= $mensaje ?></span>
        </div>
    <?php endif; ?>

    <form action="" method="post" class="form-body">
        <input type="hidden" name="idPeriodo" value="<?= $periodo['idPeriodo'] ?>">
        <input type="hidden" name="idGrado" value="<?= $idGrado ?>">
        <input type="hidden" name="idSeccion" value="<?= $seccion['idSeccion'] ?? '' ?>">

        <div class="form-group">
            <label style="text-align:center;">Identificador de Sección</label>
            <input type="text" name="nombreSeccion" maxlength="1" required 
                   placeholder="A" 
                   value="<?= $seccion['nombreSeccion'] ?? '' ?>" 
                   class="form-control input-letter">
            <div class="helper-text" style="justify-content: center;">
                <i class="fas fa-info-circle"></i> Use una sola letra (Ej: A, B, C)
            </div>
        </div>

        <div class="form-group">
            <label><i class="fas fa-users" style="margin-right: 6px;"></i> Capacidad de Estudiantes</label>
            <input type="number" name="vacantes" required min="1" 
                   value="<?= $seccion['vacantes'] ?? '30' ?>" 
                   class="form-control">
        </div>

        <div class="form-group">
            <label><i class="fas fa-user-tie" style="margin-right: 6px;"></i> Docente Tutor</label>
            <select name="idDocente" required class="form-control">
                <option value="">-- Seleccione un docente responsable --</option>
                <?php foreach($docentes as $d): ?>
                    <?php 
                        $selected = ($seccion && $seccion['idPersonal'] == $d['idPersonal']) ? 'selected' : ''; 
                    ?>
                    <option value="<?= $d['idPersonal'] ?>" <?= $selected ?>>
                        <?= $d['nombres'] . " " . $d['apellidoPaterno'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="helper-text">
                <i class="fas fa-shield-alt"></i> Este docente será el tutor principal de la sección.
            </div>
        </div>

        <div class="footer-actions">
            <a href="index.php" class="btn-custom btn-cancel">
                Cancelar
            </a>
            <button type="submit" class="btn-custom btn-save">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

</body>
</html>