<?php
ob_start();
// 1. CARGAR LAYOUT
require_once '../Layout/header.php';

require_once '../../Controllers/Gestion_Estudiantes/DirectorioController.php';
$control = new DirectorioController();

if(!isset($_GET['id'])) { echo "<script>window.location.href='directorio.php';</script>"; exit(); }

$idMatricula = $_GET['id'];
$data = $control->editar($idMatricula);

$mat = $data['matricula'];
$secciones = $data['secciones'];
$mensaje = $data['mensaje'];
?>

<style>
    .info-student {
        background: #e3f2fd;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        border-left: 5px solid #2196f3;
    }
    
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #444; }
    
    .select-modern {
        width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 6px; 
        font-size: 15px; outline: none; transition: border 0.2s;
    }
    .select-modern:focus { border-color: #2196f3; }
    
    .checklist {
        display: grid; grid-template-columns: 1fr 1fr; gap: 15px; 
        background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #eee;
    }
    
    .check-item { display: flex; align-items: center; gap: 10px; cursor: pointer; }
    .check-item input { width: 18px; height: 18px; cursor: pointer; accent-color: #28a745; }

    .btn-save {
        width: 100%; padding: 14px; background: #28a745; color: white; border: none; 
        border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 20px;
    }
    .btn-save:hover { background: #218838; }
    
    .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
</style>

<div class="card" style="max-width: 600px; margin: 40px auto;">
    <h2 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:15px; text-align:center; color:#333;">
        <i class="fas fa-edit"></i> Editar Matrícula
    </h2>

    <div class="info-student">
        <span style="font-size:12px; color:#1565c0; font-weight:bold; text-transform:uppercase;">Estudiante</span>
        <div style="font-size:18px; font-weight:600; color:#0d47a1; margin-top:5px;">
            <?= $mat['nombres']." ".$mat['apellidoPaterno']." ".$mat['apellidoMaterno'] ?>
        </div>
    </div>

    <?php if($mensaje): ?>
        <div style="background:#ffcdd2; color:#c62828; padding:15px; border-radius:5px; margin-bottom:20px; border:1px solid #ef9a9a;">
            <i class="fas fa-exclamation-triangle"></i> <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>1. Reasignar Sección:</label>
            <select name="idSeccion" required class="select-modern">
                <?php foreach($secciones as $s): ?>
                    <?php 
                        $libre = $s['vacantes'] - $s['inscritos'];
                        if($s['idSeccion'] == $mat['idSeccion']) $libre++; // Contamos su propio cupo
                        
                        $esActual = ($s['idSeccion'] == $mat['idSeccion']);
                        $txt = $esActual ? " (Actual)" : " (Libres: $libre)";
                        $dis = ($libre <= 0 && !$esActual) ? 'disabled' : '';
                        $style = ($libre <= 0 && !$esActual) ? 'color:red' : 'color:black';
                        
                        if($esActual) $style = "font-weight:bold; color:#007bff;";
                    ?>
                    <option value="<?= $s['idSeccion'] ?>" 
                            <?= $esActual ? 'selected' : '' ?>
                            style="<?= $style ?>" <?= $dis ?>>
                        <?= $s['nombreGrado']." ".$s['nivel']." - Sección '".$s['nombreSeccion']."'" . $txt ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small style="color:#666; margin-top:5px; display:block;">* Solo se muestran secciones con vacantes disponibles.</small>
        </div>

        <div class="form-group">
            <label>2. Actualizar Documentación:</label>
            <div class="checklist">
                <label class="check-item">
                    <input type="checkbox" name="doc_ficha" value="1" <?= $mat['doc_ficha_matricula']?'checked':'' ?>> 
                    Ficha de Matrícula
                </label>
                <label class="check-item">
                    <input type="checkbox" name="doc_dni" value="1" <?= $mat['doc_copia_dni']?'checked':'' ?>> 
                    Copia DNI
                </label>
                <label class="check-item">
                    <input type="checkbox" name="doc_certificado" value="1" <?= $mat['doc_certificado_estudios']?'checked':'' ?>> 
                    Certificado Estudios
                </label>
                <label class="check-item">
                    <input type="checkbox" name="doc_partida" value="1" <?= $mat['doc_partida_nacimiento']?'checked':'' ?>> 
                    Partida Nacimiento
                </label>
            </div>
        </div>

        <button type="submit" class="btn-save">Guardar Cambios</button>
        <a href="directorio.php" class="btn-cancel">Cancelar</a>
    </form>
</div>
</body>
</html>