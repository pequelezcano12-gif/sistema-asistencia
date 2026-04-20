<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar — AsistenciaEdu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%); min-height: 100vh; display:flex; align-items:center; }
        .auth-card { border-radius: 1.25rem; box-shadow: 0 25px 60px rgba(0,0,0,.3); }
        .input-group-text { background: #f8f9fa; border-right: none; }
        .form-control { border-left: none; }
        .form-control:focus { box-shadow: none; border-color: #dee2e6; }
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control { border-color: #1a73e8; }
        .btn-primary { background: linear-gradient(135deg, #1a73e8, #0d47a1); border: none; }
        .divider { position: relative; text-align: center; }
        .divider::before { content:''; position:absolute; top:50%; left:0; right:0; height:1px; background:#dee2e6; }
        .divider span { background:#fff; padding:0 1rem; position:relative; color:#6c757d; font-size:.85rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <!-- Back -->
            <div class="text-center mb-3">
                <a href="<?= BASE_URL ?>/" class="text-white opacity-75 text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i>Volver al inicio
                </a>
            </div>

            <div class="card auth-card p-4">
                <!-- Logo -->
                <div class="text-center mb-4">
                    <div style="font-size:2.5rem">📋</div>
                    <h4 class="fw-bold mt-2 mb-0">Ingresar</h4>
                    <p class="text-muted small">AsistenciaEdu</p>
                </div>

                <!-- Alertas -->
                <?php if (!empty($_SESSION['success'])): ?>
                    <div class="alert alert-success py-2 small">
                        <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                <?php if ($error ?? null): ?>
                    <div class="alert alert-danger py-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" autocomplete="off" novalidate>
                    <?= Security::csrfField() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Número de cédula</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-vcard text-muted"></i></span>
                            <input type="text" name="cedula" class="form-control"
                                   placeholder="Ej: 1234567890"
                                   value="<?= htmlspecialchars($_POST['cedula'] ?? '') ?>"
                                   maxlength="20" required autofocus
                                   autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" id="password" class="form-control"
                                   placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" class="btn btn-outline-secondary border-start-0"
                                    onclick="togglePass()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                    </button>
                </form>

                <div class="divider my-3"><span>o</span></div>

                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>/registrarse" class="btn btn-outline-primary rounded-pill">
                        <i class="bi bi-person-plus me-2"></i>Registrarse
                    </a>
                </div>

                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>/olvide-clave" class="text-muted small text-decoration-none">
                        <i class="bi bi-question-circle me-1"></i>Olvidé mi contraseña
                    </a>
                </div>
            </div>

            <p class="text-center text-white opacity-50 small mt-3">
                <i class="bi bi-shield-lock me-1"></i>Conexión segura · Datos cifrados
            </p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass() {
    const p = document.getElementById('password');
    const i = document.getElementById('eyeIcon');
    p.type = p.type === 'password' ? 'text' : 'password';
    i.className = p.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
</body>
</html>
