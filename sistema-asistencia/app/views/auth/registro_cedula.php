<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse — AsistenciaEdu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%); min-height:100vh; display:flex; align-items:center; }
        .auth-card { border-radius: 1.25rem; box-shadow: 0 25px 60px rgba(0,0,0,.3); }
        .step-badge { width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-3">
                <a href="<?= BASE_URL ?>/login" class="text-white opacity-75 text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i>Volver al login
                </a>
            </div>

            <div class="card auth-card p-4">
                <div class="text-center mb-4">
                    <div style="font-size:2.5rem">📋</div>
                    <h4 class="fw-bold mt-2 mb-0">Crear cuenta</h4>
                    <p class="text-muted small">Paso 1 de 2 — Verificación de identidad</p>
                </div>

                <!-- Pasos -->
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="step-badge bg-primary text-white">1</div>
                    <div class="flex-grow-1" style="height:2px;background:#1a73e8"></div>
                    <div class="step-badge bg-light text-muted border">2</div>
                </div>

                <?php if ($error ?? null): ?>
                    <div class="alert alert-danger py-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Ingresá tu número de cédula. El sistema verificará si estás registrado como alumno o docente.
                </div>

                <form method="POST" autocomplete="off">
                    <?= Security::csrfField() ?>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Número de cédula *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-vcard text-muted"></i></span>
                            <input type="text" name="cedula" class="form-control"
                                   placeholder="Ej: 1234567890"
                                   maxlength="20" required autofocus
                                   autocomplete="off"
                                   oninput="this.value=this.value.toUpperCase()">
                        </div>
                        <div class="form-text">Solo podés registrarte si el administrador ya cargó tus datos.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill">
                        <i class="bi bi-search me-2"></i>Verificar cédula
                    </button>
                </form>

                <div class="text-center mt-3">
                    <span class="text-muted small">¿Ya tenés cuenta?</span>
                    <a href="<?= BASE_URL ?>/login" class="small ms-1">Ingresar</a>
                </div>
            </div>

            <p class="text-center text-white opacity-50 small mt-3">
                <i class="bi bi-shield-lock me-1"></i>Tu información está protegida
            </p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
