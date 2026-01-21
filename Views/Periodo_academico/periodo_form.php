<?php
require_once '../../Controllers/Gestion_Institucional/PeriodoController.php';
$controller = new PeriodoController();

$errorCierre = "";
if (isset($_GET['action']) && $_GET['action'] == 'finalizar') {
    $datosTemp = $controller->obtenerDatosCrudos();
    if (!empty($datosTemp['idPeriodo'])) {
        $resultado = $controller->IntentarCerrarPeriodo($datosTemp['idPeriodo']);
        if ($resultado == "OK_CERRADO") {
            header("Location: periodo_form.php?msg=periodo_cerrado");
            exit;
        } elseif ($resultado == "ERROR_BIMESTRES_ACTIVOS") {
            $errorCierre = "No se puede finalizar: Aún hay bimestres Activos o Pendientes.";
        }
    }
}

$datos = $controller->obtenerDatosVista();
extract($datos); 

require_once '../Layout/header.php'; 
?>

<style>
    /* CONTENEDORES Y TARJETAS */
    .card-main { 
        background: white; border-radius: 20px; padding: 35px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .page-header { 
        display: flex; justify-content: space-between; align-items: center; 
        margin-bottom: 30px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px;
    }
    .page-header h2 { margin: 0; color: #1e293b; font-weight: 700; font-size: 1.5rem; display: flex; align-items: center; gap: 12px; }
    .page-header i { color: #3b82f6; }

    /* GRID DE INFORMACIÓN RESUMIDA */
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; }
    
    .info-box { 
        background: #f8fafc; padding: 25px; border-radius: 16px; 
        border: 1px solid #e2e8f0; position: relative; overflow: hidden;
    }
    .info-box::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 5px; background: #3b82f6; }
    .info-box.status-box::before { background: #10b981; }
    .info-box.end-box::before { background: #ef4444; }

    .info-box h5 { margin: 0 0 10px; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
    .info-box p { margin: 0; font-size: 20px; font-weight: 700; color: #1e293b; }

    /* BOTONES MODERNOS */
    .action-bar { display: flex; gap: 12px; margin-bottom: 30px; flex-wrap: wrap; }
    .btn-mrcm { 
        padding: 12px 20px; border-radius: 12px; text-decoration: none; 
        font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; 
        gap: 8px; transition: all 0.2s; border: none; cursor: pointer;
    }
    .btn-primary { background: #3b82f6; color: white; box-shadow: 0 4px 10px rgba(59,130,246,0.2); }
    .btn-primary:hover { background: #2563eb; transform: translateY(-2px); }
    
    .btn-outline { background: white; color: #64748b; border: 1px solid #e2e8f0; }
    .btn-outline:hover { background: #f8fafc; color: #1e293b; border-color: #cbd5e1; }

    .btn-danger-soft { background: #fee2e2; color: #991b1b; }
    .btn-danger-soft:hover { background: #fecaca; }

    /* TABLA PROFESIONAL */
    .table-container { border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
    .table-modern { width: 100%; border-collapse: collapse; background: white; }
    .table-modern th { background: #f8fafc; color: #64748b; padding: 16px; text-align: left; font-size: 13px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
    .table-modern td { padding: 16px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-size: 14px; }
    .table-modern tr:hover td { background: #f9fafb; }

    /* BADGES */
    .badge { padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .badge-success { background: #dcfce7; color: #166534; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-danger { background: #fee2e2; color: #991b1b; }

    /* ALERTAS */
    .alert-mrcm { padding: 16px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 500; }
    .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
</style>

<div class="card-main">
    <div class="page-header">
        <h2><i class="fas fa-calendar-check"></i> Gestión del Periodo Académico</h2>
    </div>

    <?php if ($errorCierre): ?>
        <div class="alert-mrcm alert-danger">
            <i class="fas fa-circle-exclamation" style="font-size: 18px;"></i> <?= $errorCierre ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert-mrcm alert-success">
            <i class="fas fa-circle-check" style="font-size: 18px;"></i>
            <?php 
                if($_GET['msg'] == 'exito') echo "¡Periodo académico registrado exitosamente!";
                elseif($_GET['msg'] == 'editado') echo "Los cambios de fechas se guardaron correctamente.";
                elseif($_GET['msg'] == 'periodo_cerrado') echo "El ciclo académico ha sido finalizado y archivado.";
            ?>
        </div>
    <?php endif; ?>

    <div class="action-bar">
        <a href="crear_periodo.php" class="btn-mrcm btn-primary"><i class="fas fa-plus"></i> Iniciar Nuevo Periodo</a>
        <a href="historial_periodos.php" class="btn-mrcm btn-outline"><i class="fas fa-clock-rotate-left"></i> Ver Historial</a>
        
        <?php if (!empty($anio)): ?>
            <a href="editar_periodo.php" class="btn-mrcm btn-outline"><i class="fas fa-calendar-day"></i> Ajustar Fechas</a>
            <a href="periodo_form.php?action=finalizar" 
               class="btn-mrcm btn-danger-soft" 
               onclick="return confirm('¿Está completamente seguro de cerrar el año académico? Esta acción no se puede deshacer.')">
               <i class="fas fa-lock"></i> Finalizar Año
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($anio)): ?>
        <div class="info-grid">
            <div class="info-box">
                <h5>Año Lectivo</h5>
                <p><?= $anio ?></p>
            </div>
            <div class="info-box status-box">
                <h5>Estado del Ciclo</h5>
                <p style="color: #10b981;"><i class="fas fa-circle-play" style="font-size: 14px; margin-right: 5px;"></i> <?= $estado ?></p>
            </div>
            <div class="info-box">
                <h5>Apertura</h5>
                <p><?= $textoInicio ?></p>
            </div>
            <div class="info-box end-box">
                <h5>Clausura Estimada</h5>
                <p><?= $textoFin ?></p>
            </div>
        </div>

        <h3 style="color:#1e293b; font-size: 18px; margin-bottom:20px; font-weight: 700;">Cronograma de Bimestres</h3>
        
        <div class="table-container">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Unidad Académica</th>
                        <th>Fecha de Inicio</th>
                        <th>Fecha de Término</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($bimestres as $bi): ?>
                        <tr>
                            <td style="font-weight:700; color: #334155;"><?= $bi['nombre'] ?></td>
                            <td><i class="far fa-calendar" style="color:#94a3b8; margin-right:8px;"></i><?= $bi['inicioTexto'] ?></td>
                            <td><i class="far fa-calendar-check" style="color:#94a3b8; margin-right:8px;"></i><?= $bi['finTexto'] ?></td>
                            <td>
                                <?php if($bi['estado'] == 'Activo'): ?>
                                    <span class="badge badge-success">● En Curso</span>
                                <?php elseif($bi['estado'] == 'Pendiente'): ?>
                                    <span class="badge badge-warning">● Pendiente</span> 
                                <?php else: ?>
                                    <span class="badge badge-danger">● Finalizado</span> 
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div style="text-align:center; padding:80px 40px; background:#f8fafc; border-radius:20px; border: 2px dashed #e2e8f0;">
            <div style="width: 80px; height: 80px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);">
                <i class="fas fa-calendar-xmark" style="font-size:32px; color:#cbd5e1;"></i>
            </div>
            <h3 style="color:#64748b; margin:0;">No hay un periodo académico activo</h3>
            <p style="color:#94a3b8; margin-top:10px;">Inicie un nuevo periodo para comenzar a gestionar matrículas y notas.</p>
            <a href="crear_periodo.php" class="btn-mrcm btn-primary" style="margin-top:20px;">Configurar Año Escolar</a>
        </div>
    <?php endif; ?>
        
</div>

</body>
</html>