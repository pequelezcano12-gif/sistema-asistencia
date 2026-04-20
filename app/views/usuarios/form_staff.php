<?php
$editar      = isset($usuario);
$esDirectivo = ($_SESSION['user_rol'] === 'directivo');
$pageTitle   = $editar ? 'Editar Personal' : 'Registrar Personal';
require __DIR__ . '/../layout/header.php';
?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/usuarios" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><?= $pageTitle ?></h5>
</div>

<?php if ($error ?? null): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row g-4">

    <!-- PROFESOR -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white fw-semibold border-0">
                <i class="bi bi-person-badge me-2"></i>Registrar Profesor
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/usuarios/crear-staff">
                    <input type="hidden" name="tipo" value="profesor">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                        <input type="text" name="apellido" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cédula <span class="text-danger">*</span></label>
                        <input type="text" name="cedula" class="form-control"
                               placeholder="Número de cédula" required maxlength="20"
                               oninput="this.value=this.value.toUpperCase()">
                        <div class="form-text">El profesor usará esta cédula para registrarse.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Asignatura <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="materia_nombre" id="materiaInput"
                                   class="form-control" required
                                   placeholder="Ej: Matemática, Lengua, Historia..."
                                   list="materiasExistentes"
                                   autocomplete="off">
                            <datalist id="materiasExistentes">
                                <?php foreach ($materias as $m): ?>
                                <option value="<?= htmlspecialchars($m['nombre']) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Escribí el nombre. Si no existe, se creará automáticamente.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="correo@escuela.com">
                    </div>
                    <div class="alert alert-info small py-2">
                        <i class="bi bi-info-circle me-1"></i>
                        El profesor recibirá un aviso para completar su contraseña al registrarse con su cédula.
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-person-plus me-1"></i>Registrar Profesor
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- DIRECTIVO — solo admin puede crear directivos -->
    <?php if ($_SESSION['user_rol'] === 'admin'): ?>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold border-0 text-white" style="background:#6f42c1">
                <i class="bi bi-star-fill me-2"></i>Registrar Directivo
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/usuarios/crear-staff">
                    <input type="hidden" name="tipo" value="directivo">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                        <input type="text" name="apellido" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cédula <span class="text-danger">*</span></label>
                        <input type="text" name="cedula" class="form-control"
                               placeholder="Número de cédula" required maxlength="20"
                               oninput="this.value=this.value.toUpperCase()">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                               placeholder="correo@escuela.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña temporal <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Mínimo 8 caracteres" required minlength="8">
                        <div class="form-text">El directivo puede cambiarla después.</div>
                    </div>
                    <button type="submit" class="btn w-100 text-white fw-semibold" style="background:#6f42c1">
                        <i class="bi bi-person-plus me-1"></i>Registrar Directivo
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
