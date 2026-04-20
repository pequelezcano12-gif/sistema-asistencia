<?php
$editar    = isset($usuario);
$pageTitle = $editar ? 'Editar Usuario' : 'Nuevo Usuario';
require __DIR__ . '/../layout/header.php';
?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/usuarios" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><?= $pageTitle ?></h5>
</div>

<?php if ($error ?? null): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card border-0 shadow-sm" style="max-width:580px">
    <div class="card-body">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre *</label>
                    <input type="text" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Apellido *</label>
                    <input type="text" name="apellido" class="form-control"
                           value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Cédula *</label>
                    <input type="text" name="cedula" class="form-control"
                           value="<?= htmlspecialchars($usuario['cedula'] ?? '') ?>"
                           placeholder="Número de cédula"
                           <?= $editar ? '' : 'required' ?>>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email *</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Contraseña <?= $editar ? '<span class="text-muted fw-normal">(dejar vacío para no cambiar)</span>' : '*' ?>
                    </label>
                    <input type="password" name="password" class="form-control"
                           <?= $editar ? '' : 'required' ?> minlength="8"
                           placeholder="<?= $editar ? 'Sin cambios' : 'Mínimo 8 caracteres' ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Rol *</label>
                    <select name="rol" id="rolSelect" class="form-select" required onchange="toggleMateria()">
                        <option value="admin"     <?= ($usuario['rol'] ?? '') === 'admin'     ? 'selected' : '' ?>>Administrador</option>
                        <option value="directivo" <?= ($usuario['rol'] ?? '') === 'directivo' ? 'selected' : '' ?>>Directivo</option>
                        <option value="profesor"  <?= ($usuario['rol'] ?? '') === 'profesor'  ? 'selected' : '' ?>>Profesor</option>
                        <option value="alumno"    <?= ($usuario['rol'] ?? '') === 'alumno'    ? 'selected' : '' ?>>Alumno</option>
                    </select>
                </div>

                <!-- Asignatura — solo para profesores -->
                <div class="col-12" id="materiaGroup" style="display:none">
                    <label class="form-label fw-semibold">Asignatura del profesor *</label>
                    <select name="materia_id" id="materiaSelect" class="form-select">
                        <option value="">Seleccionar asignatura...</option>
                        <?php foreach ($materias as $m): ?>
                        <option value="<?= $m['id'] ?>"
                            <?= ($usuario['materia_id'] ?? '') == $m['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($editar): ?>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Estado</label>
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

<script>
function toggleMateria() {
    const rol = document.getElementById('rolSelect').value;
    const grp = document.getElementById('materiaGroup');
    const sel = document.getElementById('materiaSelect');
    if (rol === 'profesor') {
        grp.style.display = '';
        sel.required = true;
    } else {
        grp.style.display = 'none';
        sel.required = false;
    }
}
document.addEventListener('DOMContentLoaded', toggleMateria);
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
