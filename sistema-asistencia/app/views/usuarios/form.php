<?php
$editar = isset($usuario);
$pageTitle = $editar ? 'Editar Usuario' : 'Nuevo Usuario';
require __DIR__ . '/../layout/header.php';
?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/usuarios" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><?= $pageTitle ?></h5>
</div>

<?php if ($error ?? null): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div class="card border-0 shadow-sm" style="max-width:550px">
    <div class="card-body">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Apellido *</label>
                    <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contraseña <?= $editar ? '(dejar vacío para no cambiar)' : '*' ?></label>
                    <input type="password" name="password" class="form-control" <?= $editar ? '' : 'required' ?> minlength="6">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rol *</label>
                    <select name="rol" class="form-select" required>
                        <option value="admin"     <?= ($usuario['rol'] ?? '') === 'admin'     ? 'selected' : '' ?>>Admin</option>
                        <option value="directivo" <?= ($usuario['rol'] ?? '') === 'directivo' ? 'selected' : '' ?>>Directivo</option>
                        <option value="profesor"  <?= ($usuario['rol'] ?? '') === 'profesor'  ? 'selected' : '' ?>>Profesor</option>
                        <option value="alumno"    <?= ($usuario['rol'] ?? '') === 'alumno'    ? 'selected' : '' ?>>Alumno</option>
                    </select>
                </div>
                <?php if ($editar): ?>
                <div class="col-md-6">
                    <label class="form-label">Estado</label>
                    <select name="activo" class="form-select">
                        <option value="1" <?= ($usuario['activo'] ?? 1) ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= !($usuario['activo'] ?? 1) ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i><?= $editar ? 'Actualizar' : 'Crear usuario' ?>
                </button>
                <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
