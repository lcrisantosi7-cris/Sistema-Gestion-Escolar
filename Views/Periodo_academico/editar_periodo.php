<?php
ob_start();
// MANTENEMOS TU LÓGICA PHP INTACTA
require_once '../../Controllers/Gestion_Institucional/PeriodoController.php';
$control = new PeriodoController();

$datos = $control->obtenerDatosCrudos();

if (empty($datos['idPeriodo'])) {
    echo "<div class='card' style='margin:20px; text-align:center; padding:50px;'>
            <i class='fas fa-exclamation-circle' style='font-size:48px; color:#ef4444; margin-bottom:20px;'></i>
            <h3 style='color:#1e293b; font-weight:700;'>No hay un periodo activo para editar actualmente.</h3>
            <a href='periodo_form.php' style='color:#3b82f6; text-decoration:none; font-weight:600;'>Volver a Gestión</a>
          </div>";
    exit;
}

$idPeriodo = $datos['idPeriodo'];
$anio = $datos['anio'];
$inicioPeriodo = $datos['fechaInicio'];
$finPeriodo = $datos['fechaFin'];
$bi = $datos['bimestres']; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $control->GuardarEdicion($_POST);
}

function sel($actual, $valor) {
    return ($actual == $valor) ? 'selected' : '';
}

require_once '../Layout/header.php'; 
?>

<style>
    /* CONTENEDOR PRINCIPAL */
    .card-edit { 
        background: white; 
        max-width: 950px; 
        margin: 20px auto; 
        padding: 40px; 
        border-radius: 24px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
        border: 1px solid #f1f5f9;
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    .header-title { 
        display: flex; align-items: center; justify-content: center; gap: 15px;
        margin-bottom: 40px; color: #1e293b; font-size: 24px; font-weight: 800;
    }
    .header-title i { color: #f59e0b; } /* Color ámbar para edición */

    /* SECCIONES */
    .form-section {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 25px;
        position: relative;
    }
    .form-section h4 {
        color: #1e293b;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 0;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .row { display: flex; gap: 20px; margin-bottom: 15px; flex-wrap: wrap; }
    .col { flex: 1; min-width: 150px; }
    
    label { display: block; margin-bottom: 8px; font-weight: 700; color: #64748b; font-size: 12px; text-transform: uppercase; }
    
    input[type="text"], input[type="date"], select {
        width: 90%;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        color: #1e293b;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    input:focus, select:focus {
        outline: none;
        border-color: #f59e0b;
        background: white;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
    }

    /* FILAS DE BIMESTRES */
    .bi-row {
        display: flex; 
        align-items: center; 
        gap: 20px; 
        padding: 20px; 
        background: #fffcf5; /* Fondo sutil ámbar */
        border-radius: 12px; 
        margin-bottom: 12px;
        border-left: 5px solid #f59e0b;
        transition: transform 0.2s;
    }
    .bi-row:hover { transform: scale(1.01); }
    
    .bi-title { width: 120px; font-weight: 800; font-size: 14px; color: #92400e; }
    
    /* BOTONES */
    .footer-actions { display: flex; justify-content: flex-end; align-items: center; gap: 20px; margin-top: 30px; }
    
    .btn-save {
        background-color: #f59e0b; 
        color: white; 
        border: none; 
        padding: 14px 30px; 
        font-weight: 700; 
        border-radius: 12px; 
        cursor: pointer;
        font-size: 15px;
        display: flex; align-items: center; gap: 10px;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        transition: all 0.3s;
    }
    .btn-save:hover { background-color: #d97706; transform: translateY(-2px); }

    .btn-cancel {
        color: #64748b; 
        text-decoration: none; 
        font-weight: 700;
        font-size: 14px;
        transition: color 0.2s;
    }
    .btn-cancel:hover { color: #1e293b; }

    /* ESTADOS EN SELECT */
    select { cursor: pointer; font-weight: 600; }
</style>

<div class="card-edit">
    <div class="header-title">
        <i class="fas fa-calendar-day"></i>
        <span>Actualización del Periodo Lectivo</span>
    </div>
    
    <form action="" method="post">
        <input type="hidden" name="idPeriodo" value="<?= $idPeriodo ?>">

        <div class="form-section">
            <h4><i class="fas fa-sliders" style="color:#64748b; font-size:14px;"></i> Parámetros Principales</h4>
            <div class="row">
                <div class="col">
                    <label>Año Académico</label> 
                    <input type="text" value="<?= $anio ?>" readonly disabled style="background:#f1f5f9; color:#94a3b8; border-style:dashed;"> 
                </div>
                <div class="col">
                    <label>Fecha de Apertura</label>
                    <input type="date" name="txtfecha1" value="<?= $inicioPeriodo ?>" required>
                </div>
                <div class="col">
                    <label>Fecha de Clausura</label>
                    <input type="date" name="txtfecha2" value="<?= $finPeriodo ?>" required>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h4><i class="fas fa-list-check" style="color:#64748b; font-size:14px;"></i> Cronograma y Estados de Unidad</h4>

            <?php 
            $nombresBimestres = ["I Bimestre", "II Bimestre", "III Bimestre", "IV Bimestre"];
            for ($i = 0; $i < 4; $i++): 
                $num = $i + 1;
            ?>
                <input type="hidden" name="idBi<?= $num ?>" value="<?= $bi[$i]['idBimestre'] ?>">
                <div class="bi-row">
                    <div class="bi-title"><?= $nombresBimestres[$i] ?></div>
                    
                    <div class="col">
                        <label>Inicio</label>
                        <?php if ($i == 0): ?>
                            <input type="text" value="Vinculado a Apertura" readonly disabled style="font-size:12px; background:#f1f5f9;">
                        <?php else: ?>
                            <input type="date" name="txtIni<?= $num ?>" value="<?= $bi[$i]['fechaInicio'] ?>" required>
                        <?php endif; ?>
                    </div>

                    <div class="col">
                        <label>Término</label>
                        <?php if ($i == 3): ?>
                            <input type="text" value="Vinculado a Clausura" readonly disabled style="font-size:12px; background:#f1f5f9;">
                        <?php else: ?>
                            <input type="date" name="txtFin<?= $num ?>" value="<?= $bi[$i]['fechaFin'] ?>" required>
                        <?php endif; ?>
                    </div>

                    <div class="col">
                        <label>Estado Actual</label>
                        <select name="estadoBi<?= $num ?>">
                            <option value="Pendiente" <?= sel($bi[$i]['estado'], 'Pendiente') ?>>🕒 Pendiente</option>
                            <option value="Activo"    <?= sel($bi[$i]['estado'], 'Activo') ?>>🟢 Activo</option>
                            <option value="Inactivo"  <?= sel($bi[$i]['estado'], 'Inactivo') ?>>🔴 Finalizado</option>
                        </select>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <div class="footer-actions">
            <a href="periodo_form.php" class="btn-cancel">Descartar cambios</a>
            <button type="submit" class="btn-save">
                <i class="fas fa-cloud-arrow-up"></i> Aplicar Actualización
            </button>
        </div>
    </form>
</div>

</body>
</html>