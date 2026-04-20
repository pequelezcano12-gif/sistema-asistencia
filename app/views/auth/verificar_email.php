<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar email — AsistenciaEdu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%); min-height:100vh; display:flex; align-items:center; }
        .auth-card { border-radius: 1.25rem; box-shadow: 0 25px 60px rgba(0,0,0,.3); }
        .code-input { font-size: 2rem; letter-spacing: .5rem; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card auth-card p-4 text-center">
                <div style="font-size:3rem">📧</div>
                <h4 class="fw-bold mt-2">Verificá tu email</h4>
                <p class="text-muted small">Te enviamos un código de 6 dígitos. Revisá tu bandeja de entrada.</p>

                <?php if ($error ?? null): ?>
                    <div class="alert alert-danger py-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (defined('MAIL_DEV_MODE') && MAIL_DEV_MODE): ?>
                <div class="alert alert-warning py-2 small text-start">
                    <i class="bi bi-bug me-1"></i><strong>Modo desarrollo:</strong>
                    El código está en <code>storage/logs/emails.log</code>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <?= Security::csrfField() ?>
                    <div class="mb-4">
                        <input type="text" name="codigo" class="form-control code-input"
                               placeholder="000000" maxlength="6" required
                               pattern="\d{6}" inputmode="numeric" autofocus
                               oninput="this.value=this.value.replace(/\D/g,'')">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill">
                        <i class="bi bi-check-circle me-2"></i>Verificar código
                    </button>
                </form>

                <div class="mt-3">
                    <a href="<?= BASE_URL ?>/login" class="text-muted small text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Volver al login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
