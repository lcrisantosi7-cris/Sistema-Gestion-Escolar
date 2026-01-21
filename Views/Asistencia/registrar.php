<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Asistencia/AsistenciaController.php';

$control = new AsistenciaController();
$idAsig = $_GET['idAsig'] ?? null;
$idSec = $_GET['idSec'] ?? null;
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = $control->guardar($_POST);
}

$alumnos = $control->nueva($idAsig, $idSec);
?>

<style>
    /* Cabecera y botón volver 🔙 */
    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .btn-back {
        text-decoration: none;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 14px;
        transition: 0.2s;
    }
    .btn-back:hover { color: var(--primary); }

    /* Contenedor de Fecha 📅 */
    .date-container {
        background: white;
        padding: 15px 25px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    /* Tabla Estilizada 📊 */
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .table-modern th {
        background: #f8fafc;
        padding: 15px;
        text-align: left;
        font-size: 12px;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-modern td {
        padding: 12px 15px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .table-modern tr:last-child td { border-bottom: none; }

    /* Grupos de Radio Buttons Personalizados 🔘 */
    .radio-group { display: flex; gap: 20px; }
    .radio-label { 
        display: flex; 
        align-items: center; 
        gap: 6px; 
        cursor: pointer; 
        font-size: 14px; 
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
        transition: 0.2s;
    }
    .radio-label:hover { background: #f1f5f9; }
    
    .asistio { color: #10b981; }   /* Verde */
    .falto { color: #ef4444; }     /* Rojo */
    .justifico { color: #f59e0b; } /* Ámbar */

    /* Botón Guardar 💾 */
    .btn-submit {
        background: var(--primary);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 25px;
        transition: 0.3s;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.4);
    }
    .btn-submit:hover {
        background: #2563eb;
        transform: translateY(-2px);
    }
</style>

<div class="section-container">
    <div class="header-actions">
        <div>
            <h2 style="margin:0; color:var(--text-main);">Registrar Asistencia</h2>
            <p style="color:var(--text-muted); margin:5px 0 0 0;">Complete el listado de los alumnos presentes.</p>
        </div>
        <a href="index.php" class="btn-back">
            <i class="fas fa-chevron-left"></i> Volver al listado
        </a>
    </div>

    <?php if($mensaje): ?>
        <div style="background:#fee2e2; color:#b91c1c; padding:15px; border-radius:10px; margin-bottom:20px; border:1px solid #fecaca;">
            <i class="fas fa-exclamation-triangle"></i> <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="idAsignacion" value="<?= $idAsig ?>">
        
        <div class="date-container">
            <label style="font-weight:700; color:var(--text-main);"><i class="far fa-calendar-alt"></i> Fecha de Registro:</label>
            <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" 
                   style="border:1px solid #e2e8f0; padding:8px 12px; border-radius:8px; outline:none; font-family:inherit;">
        </div>

        <table class="table-modern">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Apellidos y Nombres</th>
                    <th width="350">Estado de Asistencia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($alumnos as $i => $alum): ?>
                <tr>
                    <td style="color:var(--text-muted);"><?= $i+1 ?></td>
                    <td style="font-weight:600; color:var(--text-main);">
                        <?= mb_strtoupper($alum['apellidoPaterno']." ".$alum['apellidoMaterno'].", ".$alum['nombres']) ?>
                    </td>
                    <td>
                        <div class="radio-group">
                            <label class="radio-label asistio">
                                <input type="radio" name="asistencia[<?= $alum['idMatricula'] ?>]" value="Asistio" checked> Asistió
                            </label>
                            <label class="radio-label falto">
                                <input type="radio" name="asistencia[<?= $alum['idMatricula'] ?>]" value="Falto"> Faltó
                            </label>
                            <label class="radio-label justifico">
                                <input type="radio" name="asistencia[<?= $alum['idMatricula'] ?>]" value="Justifico"> Justificó
                            </label>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" class="btn-submit">
            <i class="fas fa-save"></i> GUARDAR ASISTENCIA
        </button>
    </form>
</div>