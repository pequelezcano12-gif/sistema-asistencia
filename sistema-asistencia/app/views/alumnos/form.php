<?php
$editar = isset($alumno);
$pageTitle = $editar ? 'Editar Alumno' : 'Nuevo Alumno';
require __DIR__ . '/../layout/header.php';
?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/alumnos" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><?= $pageTitle ?></h5>
</div>

<?php if ($error ?? null): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($alumno['nombre'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Apellido *</label>
                    <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($alumno['apellido'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">DNI *</label>
                    <input type="text" name="dni" class="form-control" value="<?= htmlspecialchars($alumno['dni'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" value="<?= $alumno['fecha_nacimiento'] ?? '' ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Curso</label>
                    <select name="curso_id" class="form-select">
                        <option value="">Sin asignar</option>
                        <?php foreach ($cursos as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($alumno['curso_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre']) ?> (<?= $c['turno'] ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($alumno['email'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($alumno['telefono'] ?? '') ?>">
                </div>
                <?php if ($editar): ?>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="activo" class="form-select">
                        <option value="1" <?= ($alumno['activo'] ?? 1) ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= !($alumno['activo'] ?? 1) ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($alumno['direccion'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto del alumno</label>
                    <?php if (!empty($alumno['foto'])): ?>
                        <div class="mb-2">
                            <img src="<?= BASE_URL ?>/uploads/alumnos/<?= $alumno['foto'] ?>" class="rounded" height="80">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i><?= $editar ? 'Actualizar' : 'Crear alumno' ?>
                </button>
                <a href="<?= BASE_URL ?>/alumnos" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
