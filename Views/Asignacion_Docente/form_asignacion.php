<?php
ob_start();
// 1. CARGAR LAYOUT GLOBAL
require_once '../Layout/header.php'; 

require_once '../../Controllers/Gestion_Institucional/AsignacionController.php';
$control = new AsignacionController();

$asignacion = null;
$idSeccionSel = $_GET['idSeccion'] ?? null;
$mensaje = "";

if (isset($_GET['id'])) {
    $asignacion = $control->obtenerAsignacion($_GET['id']);
    if (!$idSeccionSel && $asignacion) {
        $idSeccionSel = $asignacion['idSeccion'];
    }
}

$data = $control->cargarDatosFormulario($idSeccionSel);
$secciones = $data['secciones'];
$docentes = $data['docentes'];
$cursos = $data['cursos'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = $control->guardar($_POST);
    if ($mensaje) $idSeccionSel = $_POST['idSeccion'];
}
?>

<style>
    :root {
        --primary: #3b82f6;
        --primary-dark: #1d4ed8;
        --success: #10b981;
        --danger: #ef4444;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --bg-soft: #f8fafc;
        --border-color: #e2e8f0;
    }

    .form-enterprise-card {
        background: white;
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
        max-width: 700px;
        margin: 40px auto;
    }

    .form-header-ent {
        text-align: center;
        margin-bottom: 35px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--bg-soft);
    }

    .form-header-ent h2 {
        margin: 0;
        color: var(--text-main);
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    /* Estructura de Secciones */
    .form-section-ent {
        background: var(--bg-soft);
        padding: 25px;
        border-radius: 16px;
        margin-bottom: 25px;
        border: 1px solid var(--border-color);
    }

    .section-title-ent {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--primary-dark);
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Inputs y Labels */
    .form-group-ent { margin-bottom: 20px; }
    
    .label-ent {
        display: block;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text-muted);
        font-size: 12px;
        text-transform: uppercase;
    }

    .input-ent, .select-ent {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        font-size: 14px;
        background: white;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .input-ent:focus, .select-ent:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .row-ent { display: flex; gap: 20px; flex-wrap: wrap; }
    .col-ent { flex: 1; min-width: 250px; }

    /* Botones */
    .btn-save-ent {
        width: 100%;
        padding: 16px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 14px;
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
    }

    .btn-save-ent:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
    }

    .btn-cancel-ent {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: 0.2s;
    }

    .btn-cancel-ent:hover { color: var(--danger); }

    /* Mensajes */
    .alert-ent {
        background: #fef2f2;
        color: #b91c1c;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 25px;
        border: 1px solid #fee2e2;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<div class="form-enterprise-card">
    <div class="form-header-ent">
        <h2>
            <i class="fas <?= $asignacion ? 'fa-edit' : 'fa-plus-circle' ?>" style="color: var(--primary); margin-right: 10px;"></i>
            <?= $asignacion ? 'Editar Asignación Académica' : 'Nueva Asignación Académica' ?>
        </h2>
    </div>

    <?php if($mensaje): ?>
        <div class="alert-ent">
            <i class="fas fa-exclamation-triangle"></i> <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <form action="" method="post">
        <input type="hidden" name="idAsignacion" value="<?= $asignacion['idAsignacion'] ?? '' ?>">

        <div class="form-section-ent">
            <div class="section-title-ent"><i class="fas fa-sitemap"></i> Clasificación Académica</div>
            
            <div class="form-group-ent">
                <label class="label-ent">1. Grado y Sección</label>
                <select name="idSeccion" onchange="recargarPorSeccion(this.value)" required class="select-ent" style="border-left: 4px solid var(--primary);">
                    <option value="">-- Seleccione la Sección --</option>
                    <?php foreach($secciones as $sec): ?>
                        <option value="<?= $sec['idSeccion'] ?>" <?= ($idSeccionSel == $sec['idSeccion']) ? 'selected' : '' ?>>
                            <?= $sec['nombreGrado'] . " '" . $sec['nombreSeccion'] . "' - " . $sec['nivel'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div style="margin-top: 8px; font-size: 11px; color: var(--text-muted);">
                    <i class="fas fa-info-circle"></i> La selección actualizará automáticamente el catálogo de cursos.
                </div>
            </div>

            <div class="row-ent">
                <div class="col-ent">
                    <label class="label-ent">2. Curso / Área Curricular</label>
                    <select name="idCurso" required class="select-ent">
                        <?php if(empty($cursos)): ?>
                            <option value="">(Esperando selección de sección...)</option>
                        <?php else: ?>
                            <option value="">-- Seleccione el Curso --</option>
                            <?php foreach($cursos as $c): ?>
                                <option value="<?= $c['idCurso'] ?>" <?= ($asignacion && $asignacion['idCurso'] == $c['idCurso']) ? 'selected' : '' ?>>
                                    <?= $c['nombreCurso'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-ent">
                    <label class="label-ent">3. Docente Asignado</label>
                    <select name="idDocente" required class="select-ent">
                        <option value="">-- Seleccione al Docente --</option>
                        <?php foreach($docentes as $d): ?>
                            <option value="<?= $d['idPersonal'] ?>" <?= ($asignacion && $asignacion['idPersonal'] == $d['idPersonal']) ? 'selected' : '' ?>>
                                <?= $d['nombres'] . " " . $d['apellidoPaterno'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section-ent" style="border-left: 4px solid var(--success);">
            <div class="section-title-ent" style="color: var(--success);"><i class="fas fa-clock"></i> Programación Horaria</div>

            <div class="form-group-ent">
                <label class="label-ent">Día de la semana</label>
                <select name="diaSemana" required class="select-ent">
                    <?php $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes']; ?>
                    <?php foreach($dias as $dia): ?>
                        <option value="<?= $dia ?>" <?= ($asignacion && $asignacion['diaSemana'] == $dia) ? 'selected' : '' ?>>
                            <?= $dia ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row-ent">
                <div class="col-ent">
                    <label class="label-ent">Hora de Inicio</label>
                    <input type="time" name="horaInicio" value="<?= $asignacion['horaInicio'] ?? '' ?>" required class="input-ent">
                </div>
                <div class="col-ent">
                    <label class="label-ent">Hora de Término</label>
                    <input type="time" name="horaFin" value="<?= $asignacion['horaFin'] ?? '' ?>" required class="input-ent">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-save-ent">
            <i class="fas fa-save"></i> REGISTRAR ASIGNACIÓN
        </button>
        
        <a href="index.php" class="btn-cancel-ent">
            <i class="fas fa-times"></i> Cancelar y volver al listado
        </a>
    </form>

    <script>
        function recargarPorSeccion(idSeccion) {
            if(idSeccion) {
                let url = window.location.href.split('?')[0];
                const params = new URLSearchParams(window.location.search);
                const idAsignacion = params.get('id');

                if (idAsignacion) {
                    window.location.href = url + '?id=' + idAsignacion + '&idSeccion=' + idSeccion;
                } else {
                    window.location.href = url + '?idSeccion=' + idSeccion;
                }
            }
        }
    </script>
</div>

</body>
</html>