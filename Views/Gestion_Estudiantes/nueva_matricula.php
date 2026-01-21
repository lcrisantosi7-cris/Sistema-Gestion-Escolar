<?php
ob_start();
// 1. CARGAR LAYOUT GLOBAL (Menu lateral + Header)
require_once '../Layout/header.php';

require_once '../../Controllers/Gestion_Estudiantes/MatriculaController.php';
$control = new MatriculaController();
$data = $control->index();

// Variables
$periodo = $data['periodo'];
$secciones = $data['secciones'];
$est = $data['estudiante'];
$sit = $data['situacion'];
$apo = $data['apoderado'];
$msg = $data['mensaje'];
$tipo = $data['tipo_mensaje'];

$modo = $_GET['modo'] ?? 'nuevo';
?>

<style>
    :root {
        --primary: #3b83f6e2;
        --primary-dark: #2563eb;
        --primary-soft: #eff6ff;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --bg-main: #f8fafc;
        --border-color: #e2e8f0;
    }

    *, *::before, *::after {
    box-sizing: border-box;
    }


    .enterprise-wrapper {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
    }

    /* Card Principal */
    .card-ent {
        background: white;
        border-radius: 24px;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    /* Cabecera del Formulario */
    .form-header-ent {
        text-align: center;
        padding: 40px 20px;
        background: white;
        border-bottom: 2px solid var(--bg-main);
    }

    .form-header-ent h2 {
        margin: 0;
        color: var(--text-main);
        font-weight: 800;
        font-size: 1.8rem;
        letter-spacing: -0.5px;
    }

    .period-badge-ent {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--primary-soft);
        color: var(--primary);
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        margin-top: 10px;
    }

    /* Pestañas (Tabs) */
    .nav-tabs-ent {
        display: flex;
        background: #f1f5f9;
        padding: 6px;
        border-radius: 14px;
        margin: 0 40px 30px;
    }

    .nav-link-ent {
        flex: 1;
        text-align: center;
        padding: 12px;
        text-decoration: none;
        color: var(--text-muted);
        font-weight: 700;
        font-size: 14px;
        border-radius: 10px;
        transition: 0.2s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .nav-link-ent.active {
        background: white;
        color: var(--primary);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    /* Secciones del Formulario */
    .form-section-ent {
        padding: 0 40px 40px;
    }

    .section-group {
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 30px;
        margin-bottom: 35px;
        position: relative;
        background: white;
    }

    .section-tag {
        position: absolute;
        top: -14px;
        left: 25px;
        background: white;
        padding: 0 15px;
        color: var(--primary);
        font-weight: 800;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Grid y Controles */
    .row-ent { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; margin-bottom: 20px;  }
    
    label { display: block; margin-bottom: 8px; font-weight: 700; color: var(--text-main); font-size: 13px; }
    
    input, select {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid var(--border-color);
        border-radius: 12px;
        font-size: 14px;
        transition: 0.2s;
        background: #fff;
        color: var(--text-main);
    }

    input:focus, select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    input[readonly] { background: #f8fafc; color: var(--text-muted); cursor: not-allowed; }

    /* Buscador Interno */
    .search-panel-ent {
        background: #f8fafc;
        padding: 20px;
        border-radius: 14px;
        margin-bottom: 25px;
        border: 1px dashed #cbd5e1;
    }

    /* Alertas */
    .alert-ent {
        margin: 20px 40px;
        padding: 16px 20px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .alert-success-ent { background: #ecfdf5; color: #065f46; border: 1px solid #d1fae5; }
    .alert-danger-ent { background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }
    .alert-info-ent { background: #eff6ff; color: #1e40af; border: 1px solid #dbeafe; }

    /* Checkboxes Estilizados */
    .checkbox-grid-ent { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
    .check-card-ent {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        cursor: pointer;
        transition: 0.2s;
    }
    .check-card-ent:hover { background: var(--bg-main); border-color: var(--primary); }
    input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer; }

    /* Botones */
    .btn-submit-ent {
        width: 100%;
        padding: 18px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 16px;
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
    }
    .btn-submit-ent:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.4); }

    .btn-action-ent {
        background: var(--text-main);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        font-size: 13px;
    }
</style>

<div class="enterprise-wrapper">
    <div class="card-ent">
        
        <div class="form-header-ent">
            <h2><i class="fas fa-file-signature" style="color: var(--primary); margin-right: 10px;"></i>Ficha de Matrícula</h2>
            <div class="period-badge-ent">
                <i class="fas fa-calendar-alt"></i>
                Año Académico: <?= $periodo ? $periodo['anio'] : 'INACTIVO' ?>
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg']=='exito'): ?>
            <div class="alert-ent alert-success-ent"><i class="fas fa-check-circle"></i> La matrícula se ha procesado correctamente en el servidor.</div>
        <?php endif; ?>
        
        <?php if($msg): ?>
            <div class="alert-ent <?= $tipo=='ok'?'alert-success-ent':'alert-danger-ent' ?>">
                <i class="fas <?= $tipo=='ok'?'fa-check-circle':'fa-exclamation-triangle' ?>"></i> <?= $msg ?>
            </div>
        <?php endif; ?>

        <?php if(!$periodo): ?>
            <div class="alert-ent alert-danger-ent" style="justify-content: center; margin-bottom: 40px;">
                <i class="fas fa-lock"></i> No hay un periodo académico activo para registrar matrículas.
            </div>
        <?php else: ?>

            <div class="nav-tabs-ent">
                <a href="?modo=nuevo" class="nav-link-ent <?= $modo=='nuevo'?'active':'' ?>">
                    <i class="fas fa-user-plus"></i> Estudiante Nuevo
                </a>
                <a href="?modo=existente" class="nav-link-ent <?= $modo=='existente'?'active':'' ?>">
                    <i class="fas fa-history"></i> Estudiante Antiguo
                </a>
            </div>

            <form method="POST" action="" class="form-section-ent">
                <input type="hidden" name="tipoEstudiante" value="<?= ucfirst($modo) ?>">
                
                <?php if($est): ?><input type="hidden" name="dni_est_hidden" value="<?= $est['dni'] ?>"><?php endif; ?>
                <?php if($apo): ?><input type="hidden" name="dni_apo_hidden" value="<?= $apo['dni'] ?>"><?php endif; ?>

                <div class="section-group">
                    <span class="section-tag">1. Datos del Alumno</span>
                    
                    <?php if($modo == 'existente'): ?>
                        <div class="search-panel-ent">
                            <label><i class="fas fa-search"></i> Localizar expediente por DNI:</label>
                            <div style="display:flex; gap:12px;">
                                <input type="text" name="dni_busqueda" placeholder="8 dígitos" value="<?= $_POST['dni_busqueda']??'' ?>" maxlength="8">
                                <button type="submit" name="btn_buscar_estudiante" class="btn-action-ent">Verificar Alumno</button>
                            </div>
                        </div>

                        <?php if($est): ?>
                            <div class="alert-ent alert-info-ent">
                                <i class="fas fa-user-check"></i>
                                <span><strong>Expediente:</strong> <?= $est['nombres'].' '.$est['apellidoPaterno'].' '.$est['apellidoMaterno'] ?></span>
                            </div>
                            
                            <?php if(isset($sit['estado'])): ?>
                                <?php 
                                    $estilo_sit = ($sit['estado']=='Promovido') ? 'alert-success-ent' : (($sit['estado']=='Repitente') ? 'alert-danger-ent' : 'alert-info-ent');
                                    $icon_sit = ($sit['estado']=='Promovido') ? 'fa-graduation-cap' : 'fa-redo';
                                ?>
                                <div class="alert-ent <?= $estilo_sit ?>" style="margin: 15px 0;">
                                    <i class="fas <?= $icon_sit ?>"></i>
                                    <strong>Situación: <?= strtoupper($sit['estado']) ?></strong>. <?= $sit['estado']=='Promovido' ? 'Habilitado para grado superior.' : 'Debe cursar el mismo grado.' ?>
                                </div>
                            <?php endif; ?>
                            <input type="hidden" name="idEstudianteExistente" value="<?= $est['idEstudiante'] ?>">
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="row-ent">
                            <div><label>DNI / Documento *</label><input type="text" name="est_dni" required maxlength="8"></div>
                            <div><label>Fecha Nacimiento *</label><input type="date" name="est_nacimiento" required></div>
                            <div><label>Edad Actual *</label><input type="number" name="est_edad" required></div>
                        </div>
                        <div class="row-ent">
                            <div><label>Nombres *</label><input type="text" name="est_nombres" required></div>
                            <div><label>Ap. Paterno *</label><input type="text" name="est_paterno" required></div>
                            <div><label>Ap. Materno *</label><input type="text" name="est_materno" required></div>
                        </div>
                        <div class="row-ent">
                            <div>
                                <label>Género *</label>
                                <select name="est_genero">
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                            <div></div> </div>
                    <?php endif; ?>
                </div>

                <div class="section-group">
                    <span class="section-tag">2. Datos del Apoderado</span>
                    
                    <div class="search-panel-ent">
                        <label>Vincular apoderado existente (DNI):</label>
                        <div style="display:flex; gap:12px;">
                            <input type="text" name="apo_dni_busqueda" placeholder="DNI Apoderado" value="<?= $_POST['apo_dni_busqueda']??'' ?>" maxlength="8">
                            <button type="submit" name="btn_buscar_apoderado" class="btn-action-ent">Cargar Perfil</button>
                        </div>
                    </div>

                    <input type="hidden" name="idApoderadoExistente" value="<?= $apo['idApoderado']??'' ?>">
                    <input type="hidden" name="tipoApoderado" value="<?= $apo?'Existente':'Nuevo' ?>">

                    <div class="row-ent">
                        <div><label>DNI *</label><input type="text" name="apo_dni" value="<?= $apo['dni']??'' ?>" <?= $apo?'readonly':'' ?> required maxlength="8"></div>
                        <div><label>Nombres *</label><input type="text" name="apo_nombres" value="<?= $apo['nombres']??'' ?>" <?= $apo?'readonly':'' ?> required></div>
                    </div>
                    <div class="row-ent">
                        <div><label>Ap. Paterno *</label><input type="text" name="apo_paterno" value="<?= $apo['apellidoPaterno']??'' ?>" required></div>
                        <div><label>Ap. Materno *</label><input type="text" name="apo_materno" value="<?= $apo['apellidoMaterno']??'' ?>" required></div>
                    </div>
                    <div class="row-ent">
                        <div><label>Teléfono de Contacto</label><input type="text" name="apo_telefono" value="<?= $apo['telefono']??'' ?>"></div>
                        <div><label>Correo Electrónico</label><input type="email" name="apo_correo" value="<?= $apo['correo']??'' ?>"></div>
                    </div>
                    <div class="row-ent" style="grid-template-columns: 2fr 1fr;">
                        <div><label>Dirección Domiciliaria</label><input type="text" name="apo_direccion" value="<?= $apo['direccion']??'' ?>" required></div>
                        <div><label>Ocupación</label><input type="text" name="apo_ocupacion" value="<?= $apo['ocupacion']??'' ?>"></div>
                    </div>
                </div>

                <div class="section-group" style="background: #fafafa;">
                    <span class="section-tag">3. Ubicación Académica</span>
                    
                    <label style="font-size: 15px; color: var(--primary);">Seleccione Vacante Disponible:</label>
                    <select name="idSeccion" required style="padding:15px; font-size:15px; border:2px solid var(--primary); background: white;">
                        <option value="">-- Elija Grado y Sección --</option>
                        <?php foreach($secciones as $s): ?>
                            <?php 
                                if($modo=='existente' && isset($sit['idGradoSugerido']) && $sit['idGradoSugerido']>0){
                                    if($s['idGrado'] != $sit['idGradoSugerido']) continue; 
                                }
                                $libres = $s['vacantes'] - $s['inscritos'];
                                $dis = ($libres<=0)?'disabled':'';
                                $txt = ($libres<=0) ? " ⛔ (SIN VACANTES)" : " — Vacantes: $libres";
                            ?>
                            <option value="<?= $s['idSeccion'] ?>" <?= $dis ?> style="<?= $libres<=0?'color:#94a3b8':'' ?>">
                                <?= $s['nombreGrado'].' '.$s['nivel'].' - Sección "'.$s['nombreSeccion'].'"'.$txt ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 10px;">
                        <i class="fas fa-info-circle"></i> Solo se listan secciones con aforo disponible en el periodo actual.
                    </p>

                    <div style="margin-top: 30px;">
                        <label>Documentación Consignada:</label>
                        <div class="checkbox-grid-ent">
                            <label class="check-card-ent"><input type="checkbox" name="doc_ficha" value="1"> Ficha Matrícula</label>
                            <label class="check-card-ent"><input type="checkbox" name="doc_dni" value="1"> Copia DNI</label>
                            <?php if($modo=='nuevo'): ?>
                                <label class="check-card-ent"><input type="checkbox" name="doc_certificado" value="1"> Certificado</label>
                                <label class="check-card-ent"><input type="checkbox" name="doc_partida" value="1"> Partida Nac.</label>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <button type="submit" name="btn_registrar" class="btn-submit-ent">
                    <i class="fas fa-save"></i> FINALIZAR REGISTRO DE MATRÍCULA
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>