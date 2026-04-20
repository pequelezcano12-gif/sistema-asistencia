<?php $pageTitle = 'Registrar Docente'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/usuarios" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Registrar Docente</h5>
</div>

<?php if ($error ?? null): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="max-width:540px">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/usuarios/crear-profesor">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control" required autofocus>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                    <input type="text" name="apellido" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Cédula <span class="text-danger">*</span></label>
                    <input type="text" name="cedula" class="form-control"
                           placeholder="Número de cédula" required maxlength="20"
                           oninput="this.value=this.value.toUpperCase()">
                    <div class="form-text">El docente usará esta cédula para ingresar al sistema.</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Asignatura <span class="text-danger">*</span></label>
                    <input type="text" name="materia_nombre" class="form-control" required
                           placeholder="Ej: Matemática, Lengua, Historia..."
                           list="materiasExistentes" autocomplete="off">
                    <datalist id="materiasExistentes">
                        <?php foreach ($materias as $m): ?>
                        <option value="<?= htmlspecialchars($m['nombre']) ?>">
                        <?php endforeach; ?>
                    </datalist>
                    <div class="form-text">Si la asignatura no existe, se crea automáticamente.</div>
                </div>
            </div>

            <div class="alert alert-info small mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                El docente completa su contraseña la primera vez que ingresa con su cédula.
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i>Registrar docente
                </button>
                <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
