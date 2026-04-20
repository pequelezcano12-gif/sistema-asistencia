<?php $pageTitle = 'Registrar Directivo'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/usuarios" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><i class="bi bi-star-fill me-2" style="color:#6f42c1"></i>Registrar Directivo</h5>
</div>

<?php if ($error ?? null): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="max-width:540px">
    <div class="card-header border-0 text-white fw-semibold" style="background:#6f42c1">
        <i class="bi bi-shield-lock me-2"></i>Datos del directivo
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/usuarios/crear-directivo">
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
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control"
                           placeholder="correo@escuela.com" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Contraseña temporal <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Mínimo 8 caracteres" required minlength="8">
                    <div class="form-text">El directivo puede cambiarla después desde su perfil.</div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn text-white fw-semibold" style="background:#6f42c1">
                    <i class="bi bi-person-plus me-1"></i>Registrar directivo
                </button>
                <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
