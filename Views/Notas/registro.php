<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Notas/NotaController.php';

$control = new NotaController();
$idAsignacion = $_GET['id'] ?? null;
if(!$idAsignacion) header("Location: index.php");

// Procesar Guardado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $control->guardar($_POST);
}

// Cargar Datos
$data = $control->registro($idAsignacion);
$info = $data['info'];
$competencias = $data['competencias'];
$bimestres = $data['bimestres'];
$alumnos = $data['alumnos'];
$notas = $data['notas'];
?>

<style>
    /* Cabecera Fija Estilizada 📌 */
    .sticky-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 100;
        padding: 20px 0;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Contenedor de Alumno 👨‍🎓 */
    .student-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    .student-name-tag {
        background: #f8fafc;
        padding: 12px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-main);
        font-weight: 700;
    }

    /* Grilla de Competencias 📋 */
    .comp-item {
        display: grid;
        grid-template-columns: 1fr auto;
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
        align-items: center;
        gap: 20px;
    }
    .comp-item:last-child { border-bottom: none; }

    .comp-info {
        font-size: 13px;
        color: #475569;
        line-height: 1.4;
    }

    /* Entradas de Notas ✍️ */
    .grades-wrapper {
        display: flex;
        gap: 8px;
    }

    .input-box {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .bim-label {
        font-size: 10px;
        font-weight: 800;
        color: #94a3b8;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .grade-input {
        width: 42px;
        height: 38px;
        text-align: center;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        transition: 0.2s;
        border: 1px solid #cbd5e1;
    }

    /* Colores según estado 🎨 */
    .input-active {
        background: white;
        color: var(--primary);
        border-color: var(--primary);
    }
    .input-active:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .input-locked {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
        border: 1px solid #e2e8f0;
    }

    /* Botones ⚡ */
    .btn-save-main {
        background: #10b981;
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        border: none;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: 0.3s;
    }
    .btn-save-main:hover {
        background: #059669;
        transform: translateY(-2px);
    }
</style>

<div class="section-container" style="max-width: 1000px; margin: 0 auto;">
    
    <div class="sticky-header">
        <div>
            <h2 style="margin:0; color:var(--text-main);">Registro de Calificaciones</h2>
            <div style="margin-top:4px;">
                <span class="badge" style="background:var(--primary); color:white; padding:4px 8px; border-radius:6px; font-size:11px;">
                    <?= $info['nombreCurso'] ?>
                </span>
                <span style="color:var(--text-muted); font-size:13px; margin-left:5px;">
                    <?= $info['nombreGrado'] ?> "<?= $info['nombreSeccion'] ?>"
                </span>
            </div>
        </div>
        <div style="display:flex; gap:12px; align-items:center;">
            <a href="index.php" style="text-decoration:none; color:var(--text-muted); font-weight:600; font-size:14px;">Cancelar</a>
            <button type="submit" form="formNotas" class="btn-save-main">
                <i class="fas fa-save"></i> GUARDAR CALIFICACIONES
            </button>
        </div>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg']=='ok'): ?>
        <div style="background:#ecfdf5; color:#065f46; padding:15px; border-radius:10px; margin-bottom:25px; border:1px solid #a7f3d0; display:flex; align-items:center; gap:10px;">
            <i class="fas fa-check-circle"></i> Notas guardadas correctamente en el sistema.
        </div>
    <?php endif; ?>

    <form id="formNotas" method="POST">
        <input type="hidden" name="idAsignacion" value="<?= $idAsignacion ?>">
        
        <?php foreach($alumnos as $alum): ?>
            <div class="student-card">
                <div class="student-name-tag">
                    <i class="fas fa-user-circle" style="color:var(--primary); font-size:1.2rem;"></i>
                    <?= mb_strtoupper($alum['apellidoPaterno']." ".$alum['apellidoMaterno'].", ".$alum['nombres']) ?>
                </div>

                <?php foreach($competencias as $comp): ?>
                    <div class="comp-item">
                        <div class="comp-info">
                            <span style="font-weight:700; color:var(--text-main); display:block; margin-bottom:2px;">Competencia:</span>
                            <?= $comp['textCompetencia'] ?>
                        </div>
                        
                        <div class="grades-wrapper">
                            <?php foreach($bimestres as $bi): 
                                $idMat = $alum['idMatricula'];
                                $idComp = $comp['idCompetenciaCurso'];
                                $idBim = $bi['idBimestre'];
                                $valorNota = $notas[$idMat][$idComp][$idBim] ?? '';
                                $esEditable = ($bi['estado'] == 'Activo');
                            ?>
                                <div class="input-box">
                                    <span class="bim-label"><?= substr($bi['nombreBimestre'], 0, 3) ?></span>
                                    <input type="text" 
                                           name="notas[<?= $idMat ?>][<?= $idComp ?>][<?= $idBim ?>]" 
                                           value="<?= $valorNota ?>" 
                                           class="grade-input <?= $esEditable ? 'input-active' : 'input-locked' ?>" 
                                           <?= $esEditable ? '' : 'readonly' ?>
                                           maxlength="2"
                                           autocomplete="off">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        
        <?php if(empty($alumnos)): ?>
            <div style="text-align:center; padding:50px; background:white; border-radius:12px; color:var(--text-muted); border:1px dashed #cbd5e1;">
                <i class="fas fa-users-slash" style="font-size:2rem; margin-bottom:10px; display:block;"></i>
                No hay alumnos matriculados en esta sección.
            </div>
        <?php endif; ?>
    </form>
</div>