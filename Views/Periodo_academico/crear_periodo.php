<?php

require_once '../../Controllers/Gestion_Institucional/PeriodoController.php';
$control = new PeriodoController();
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = $control->Guardar($_POST);
    if ($res === "OK_GUARDADO") {
        header("Location: periodo_form.php?msg=exito");
        exit;
    }
    $mensaje = $res;
}   

require_once '../Layout/header.php'; // IMPORTANTE: Cargar Layout
?>

<style>
    /* CONTENEDOR PRINCIPAL ANIMADO */
    .card-form { 
        background: white; 
        max-width: 850px; 
        margin: 20px auto; 
        padding: 40px; 
        border-radius: 24px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
        border: 1px solid #f1f5f9;
        animation: slideUp 0.5s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* TÍTULOS Y SECCIONES */
    .form-title { 
        text-align: center; 
        color: #1e293b; 
        font-weight: 800; 
        font-size: 1.8rem; 
        margin-bottom: 40px; 
        letter-spacing: -0.5px;
    }

    .section-header { 
        display: flex; 
        align-items: center; 
        gap: 12px; 
        font-size: 15px; 
        font-weight: 700; 
        color: #3b82f6; 
        margin-bottom: 25px; 
        padding-bottom: 10px; 
        border-bottom: 2px solid #eff6ff; 
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* INPUTS Y GRUPOS */
    .form-group { margin-bottom: 25px; }
    .form-group label { 
        display: block; 
        font-weight: 700; 
        margin-bottom: 10px; 
        color: #64748b; 
        font-size: 13px; 
        text-transform: uppercase;
    }

    .form-control { 
        width: 100%; 
        padding: 14px 16px; 
        border: 1px solid #e2e8f0; 
        border-radius: 12px; 
        font-size: 15px; 
        color: #1e293b;
        background: #f8fafc;
        transition: all 0.3s;
        box-sizing: border-box;
    }

    .form-control:focus { 
        border-color: #3b82f6; 
        background: white;
        outline: 0; 
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); 
    }

    .form-control:disabled { background: #f1f5f9; color: #94a3b8; font-style: italic; border-color: #e2e8f0; }

    .row { display: flex; gap: 20px; flex-wrap: wrap; }
    .col { flex: 1; min-width: 200px; }

    /* CRONOGRAMA DE BIMESTRES */
    .timeline-container { 
        background: #f8fafc; 
        padding: 30px; 
        border-radius: 20px; 
        border: 1px solid #f1f5f9;
        margin-bottom: 40px;
    }

    .timeline-row { 
        display: flex; 
        align-items: center; 
        gap: 15px; 
        padding: 15px 0;
        border-bottom: 1px dashed #e2e8f0;
    }

    .timeline-row:last-child { border-bottom: none; }

    .timeline-label { flex: 0 0 140px; font-weight: 700; color: #334155; font-size: 14px; }

    /* BOTONES */
    .btn-group { display: flex; gap: 15px; margin-top: 20px; }
    
    .btn-save { 
        flex: 2; 
        background: #3b82f6; 
        color: white; 
        border: none; 
        padding: 16px; 
        border-radius: 14px; 
        font-weight: 700; 
        font-size: 16px; 
        cursor: pointer; 
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }

    .btn-save:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3); }

    .btn-cancel { 
        flex: 1; 
        background: #f1f5f9; 
        color: #64748b; 
        text-decoration: none; 
        padding: 16px; 
        border-radius: 14px; 
        font-weight: 700; 
        text-align: center; 
        transition: all 0.3s;
    }

    .btn-cancel:hover { background: #e2e8f0; color: #1e293b; }

    /* ALERTAS */
    .alert-error { 
        padding: 18px; 
        background: #fee2e2; 
        color: #991b1b; 
        border-radius: 14px; 
        margin-bottom: 30px; 
        border-left: 5px solid #ef4444;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }
</style>

<div class="card-form">
    <h2 class="form-title">Configurar Nuevo Periodo Académico</h2>
    
    <?php if($mensaje): ?>
        <div class="alert-error">
            <i class="fas fa-circle-exclamation" style="font-size: 20px;"></i>
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <form action="" method="post">
        
        <div class="section-header">
            <i class="fas fa-info-circle"></i> 1. Datos Generales del Periodo
        </div>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>Año Académico</label>
                    <input type="number" name="txtAnio" class="form-control" required placeholder="Ej: 2026">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Fecha de Apertura</label>
                    <input type="date" name="txtfecha1" class="form-control" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Fecha de Clausura</label>
                    <input type="date" name="txtfecha2" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="section-header" style="margin-top:20px;">
            <i class="fas fa-clock"></i> 2. Cronograma de Bimestres
        </div>
        
        <div class="timeline-container">
            <div class="timeline-row">
                <div class="timeline-label">I BIMESTRE</div>
                <div style="flex:1;"><input type="text" value="Inicio Automático" disabled class="form-control"></div>
                <div style="flex:1;"><input type="date" name="txtFin1" class="form-control" required></div>
            </div>

            <div class="timeline-row">
                <div class="timeline-label">II BIMESTRE</div>
                <div style="flex:1;"><input type="date" name="txtIni2" class="form-control" required placeholder="Inicio"></div>
                <div style="flex:1;"><input type="date" name="txtFin2" class="form-control" required placeholder="Fin"></div>
            </div>

            <div class="timeline-row">
                <div class="timeline-label">III BIMESTRE</div>
                <div style="flex:1;"><input type="date" name="txtIni3" class="form-control" required placeholder="Inicio"></div>
                <div style="flex:1;"><input type="date" name="txtFin3" class="form-control" required placeholder="Fin"></div>
            </div>

            <div class="timeline-row">
                <div class="timeline-label">IV BIMESTRE</div>
                <div style="flex:1;"><input type="date" name="txtIni4" class="form-control" required placeholder="Inicio"></div>
                <div style="flex:1;"><input type="text" value="Cierre Automático" disabled class="form-control"></div>
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Registrar Periodo Escolar
            </button>
            <a href="periodo_form.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>