<?php $pageTitle = 'Usuarios'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-gear me-2 text-primary"></i>Gestión de Usuarios</h5>
    <?php if ($_SESSION['user_rol'] === 'admin'): ?>
    <a href="<?= BASE_URL ?>/usuarios/crear" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>Nuevo usuario
    </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Usuario</th><th>Email</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                <?php
                $rolColors = ['admin'=>'danger','directivo'=>'purple','profesor'=>'primary','alumno'=>'secondary'];
                $rolIcons  = ['admin'=>'shield-fill','directivo'=>'star-fill','profesor'=>'person-badge','alumno'=>'mortarboard'];
                foreach ($usuarios as $u):
                    $rc = $rolColors[$u['rol']] ?? 'secondary';
                    $ri = $rolIcons[$u['rol']] ?? 'person';
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-<?= $rc ?> bg-opacity-10 d-flex align-items-center justify-content-center" style="width:38px;height:38px">
                                <i class="bi bi-<?= $ri ?> text-<?= $rc ?>"></i>
                            </div>
                            <span class="fw-semibold"><?= htmlspecialchars($u['apellido'] . ', ' . $u['nombre']) ?></span>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="badge" style="background:<?= $u['rol']==='directivo'?'#6f42c1':''; ?>" class="bg-<?= $rc ?>">
                            <?php if ($u['rol'] !== 'directivo'): ?>
                            <span class="badge bg-<?= $rc ?>">
                            <?php else: ?>
                            <span class="badge" style="background:#6f42c1">
                            <?php endif; ?>
                                <?= ucfirst($u['rol']) ?>
                            </span>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?= $u['activo'] ? 'success' : 'secondary' ?>">
                            <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                        <a href="<?= BASE_URL ?>/usuarios/<?= $u['id'] ?>/editar" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <a href="<?= BASE_URL ?>/usuarios/<?= $u['id'] ?>/eliminar"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Desactivar usuario?')"><i class="bi bi-person-x"></i></a>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
