<?php
$editar = isset($curso);
$pageTitle = $editar ? 'Editar Curso' : 'Nuevo Curso';
require __DIR__ . '/../layout/header.php';
?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/cursos" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><?= $pageTitle ?></h5>
</div>

<?php if ($error ?? null): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nombre del curso *</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej: 1°A, 2°B" value="<?= htmlspecialchars($curso['nombre'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Turno *</label>
                <select name="turno" class="form-select" required>
                    <option value="mañana" <?= ($curso['turno'] ?? '') === 'mañana' ? 'selected' : '' ?>>Mañana</option>
                    <option value="tarde"  <?= ($curso['turno'] ?? '') === 'tarde'  ? 'selected' : '' ?>>Tarde</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Año lectivo *</label>
                <input type="number" name="anio_lectivo" class="form-control" value="<?= $curso['anio_lectivo'] ?? date('Y') ?>" min="2020" max="2099" required>
            </div>
            <?php if ($editar): ?>
            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select name="activo" class="form-select">
                    <option value="1" <?= ($curso['activo'] ?? 1) ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= !($curso['activo'] ?? 1) ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
            <?php endif; ?>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $editar ? 'Actualizar' : 'Crear' ?></button>
                <a href="<?= BASE_URL ?>/cursos" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
