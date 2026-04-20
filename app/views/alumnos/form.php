<?php
$editar      = isset($alumno);
$esDirectivo = ($_SESSION['user_rol'] === 'directivo');
$pageTitle   = $editar ? 'Editar Alumno' : 'Nuevo Alumno';
require __DIR__ . '/../layout/header.php';
?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/alumnos" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><?= $pageTitle ?></h5>
    <?php if ($esDirectivo): ?>
        <span class="badge bg-info">Vista Directivo — campos básicos</span>
    <?php endif; ?>
</div>

<?php if ($error ?? null): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="max-width:620px">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">

            <!-- CAMPOS QUE VEN TODOS (directivo y admin) -->
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($alumno['nombre'] ?? '') ?>" required autofocus>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                    <input type="text" name="apellido" class="form-control"
                           value="<?= htmlspecialchars($alumno['apellido'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">DNI / Cédula <span class="text-danger">*</span></label>
                    <input type="text" name="dni" class="form-control"
                           value="<?= htmlspecialchars($alumno['dni'] ?? '') ?>"
                           placeholder="Número de cédula" required maxlength="20">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control"
                           value="<?= $alumno['fecha_nacimiento'] ?? '' ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="curso_id" class="form-select">
                        <option value="">Sin asignar</option>
                        <?php foreach ($cursos as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= ($alumno['curso_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($editar && !$esDirectivo): ?>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="activo" class="form-select">
                        <option value="1" <?= ($alumno['activo'] ?? 1) ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= !($alumno['activo'] ?? 1) ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>

            <!-- DATOS DEL ENCARGADO — todos los roles -->
            <hr class="my-4">
            <p class="fw-semibold mb-3">
                <i class="bi bi-people-fill text-primary me-2"></i>Padre, madre o encargado
            </p>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Nombre completo del encargado</label>
                    <input type="text" name="nombre_encargado" class="form-control"
                           value="<?= htmlspecialchars($alumno['nombre_encargado'] ?? '') ?>"
                           placeholder="Nombre y apellido del padre, madre o tutor">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Cédula del encargado <span class="text-danger">*</span></label>
                    <input type="text" name="cedula_padre" class="form-control"
                           value="<?= htmlspecialchars($alumno['cedula_padre'] ?? '') ?>"
                           placeholder="Número de cédula" maxlength="20" required>
                    <div class="form-text text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>Obligatorio — el alumno la necesita para registrarse.
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Teléfono del encargado</label>
                    <input type="text" name="telefono_encargado" class="form-control"
                           value="<?= htmlspecialchars($alumno['telefono_encargado'] ?? '') ?>"
                           placeholder="Ej: 0991234567">
                </div>
            </div>

            <!-- CAMPOS EXTRA — solo admin -->
            <?php if (!$esDirectivo): ?>
            <hr class="my-4">
            <p class="text-muted small fw-semibold mb-3">
                <i class="bi bi-shield-fill text-danger me-1"></i>Campos adicionales (solo Admin)
            </p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($alumno['email'] ?? '') ?>"
                           placeholder="correo@ejemplo.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" name="telefono" class="form-control"
                           value="<?= htmlspecialchars($alumno['telefono'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Dirección</label>
                    <input type="text" name="direccion" class="form-control"
                           value="<?= htmlspecialchars($alumno['direccion'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Cédula del padre/tutor</label>
                    <input type="text" name="cedula_padre" class="form-control"
                           value="<?= htmlspecialchars($alumno['cedula_padre'] ?? '') ?>"
                           placeholder="Cédula del responsable">
                </div>
            </div>
            <?php endif; ?>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i><?= $editar ? 'Actualizar' : 'Registrar alumno' ?>
                </button>
                <a href="<?= BASE_URL ?>/alumnos" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
