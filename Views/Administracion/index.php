<?php
ob_start();
require_once '../Layout/header.php';
require_once '../../Controllers/Administracion/PersonalController.php';
$control = new PersonalController();
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $control->eliminar($_GET['id']);
}
$data  = $control->index();
$lista = $data['personal'];
?>

<div class="page-header">
    <div>
        <div class="page-title"><i class="fas fa-users-gear"></i> Personal Institucional</div>
        <div class="page-subtitle">Gestión de usuarios, roles y credenciales de acceso</div>
    </div>
    <a href="form_personal.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo registro</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><i class="fas fa-circle-check"></i> Operación completada exitosamente.</div>
<?php endif; ?>

<!-- Toolbar -->
<div style="margin-bottom:20px;">
    <form method="POST" style="display:flex; gap:10px; align-items:center;">
        <div class="search-bar" style="flex:1; max-width:420px;">
            <i class="fas fa-magnifying-glass"></i>
            <input type="text" name="busqueda" placeholder="Buscar por DNI o apellido…" value="<?= htmlspecialchars($data['busqueda'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Buscar</button>
        <?php if (!empty($data['busqueda'])): ?>
            <a href="index.php" class="btn btn-ghost"><i class="fas fa-xmark"></i> Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Table -->
<div class="card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Rol</th>
                    <th>DNI</th>
                    <th>Nombre completo</th>
                    <th>Contacto</th>
                    <th>Usuario</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($lista): ?>
                    <?php foreach ($lista as $p): ?>
                        <?php
                            $badgeClass = 'badge-gray';
                            if (stripos($p['nombreRol'], 'Director')   !== false) $badgeClass = 'badge-blue';
                            if (stripos($p['nombreRol'], 'Secretaria') !== false) $badgeClass = 'badge-green';
                            if (stripos($p['nombreRol'], 'Docente')    !== false) $badgeClass = 'badge-yellow';
                        ?>
                        <tr>
                            <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($p['nombreRol']) ?></span></td>
                            <td><span class="font-mono fw-600" style="font-size:13px;"><?= htmlspecialchars($p['dni']) ?></span></td>
                            <td>
                                <div class="fw-600"><?= htmlspecialchars($p['apellidoPaterno'] . ' ' . $p['apellidoMaterno']) ?></div>
                                <div style="font-size:12px; color:var(--text-secondary);"><?= htmlspecialchars($p['nombres']) ?></div>
                            </td>
                            <td>
                                <div style="font-size:13px; color:var(--text-secondary);">
                                    <div><i class="fas fa-envelope" style="width:14px; color:var(--text-muted);"></i> <?= htmlspecialchars($p['correo']) ?></div>
                                    <div style="margin-top:2px;"><i class="fas fa-phone" style="width:14px; color:var(--text-muted);"></i> <?= htmlspecialchars($p['telefono']) ?></div>
                                </div>
                            </td>
                            <td>
                                <?php if ($p['username']): ?>
                                    <span class="font-mono" style="font-size:12px; color:var(--color-primary); background:var(--color-primary-bg); padding:3px 8px; border-radius:6px;">@<?= htmlspecialchars($p['username']) ?></span>
                                <?php else: ?>
                                    <span style="font-size:12px; color:var(--text-muted); font-style:italic;">Sin cuenta</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <a href="form_personal.php?id=<?= $p['idPersonal'] ?>" class="btn btn-warning btn-icon" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="index.php?action=delete&id=<?= $p['idPersonal'] ?>"
                                       class="btn btn-danger btn-icon"
                                       title="Eliminar"
                                       onclick="return confirm('¿Confirmar eliminación? Esta acción es irreversible.')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-magnifying-glass"></i>
                                <p>No se encontraron registros</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div></main></body></html>
