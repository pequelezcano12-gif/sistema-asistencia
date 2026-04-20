<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva contraseña — AsistenciaEdu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%); min-height:100vh; display:flex; align-items:center; }
        .auth-card { border-radius: 1.25rem; box-shadow: 0 25px 60px rgba(0,0,0,.3); }
        .code-input { font-size: 1.8rem; letter-spacing: .4rem; text-align: center; font-weight: bold; }
        .req { font-size: .78rem; }
        .req.ok { color: #198754; }
        .req.no { color: #6c757d; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card auth-card p-4">
                <div class="text-center mb-3">
                    <div style="font-size:2.5rem">🔒</div>
                    <h4 class="fw-bold mt-2">Nueva contraseña</h4>
                    <p class="text-muted small">Ingresá el código que recibiste y tu nueva contraseña.</p>
                </div>

                <?php if ($error ?? null): ?>
                    <div class="alert alert-danger py-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (defined('MAIL_DEV_MODE') && MAIL_DEV_MODE): ?>
                <div class="alert alert-warning py-2 small">
                    <i class="bi bi-bug me-1"></i><strong>Dev:</strong> código en <code>storage/logs/emails.log</code>
                </div>
                <?php endif; ?>

                <form method="POST" novalidate>
                    <?= Security::csrfField() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Código de verificación</label>
                        <input type="text" name="codigo" class="form-control code-input"
                               placeholder="000000" maxlength="6" required
                               pattern="\d{6}" inputmode="numeric" autofocus
                               oninput="this.value=this.value.replace(/\D/g,'')">
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Nueva contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" id="pass1" class="form-control"
                                   placeholder="Mínimo 8 caracteres" required
                                   oninput="checkStrength(this.value)" autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary border-start-0"
                                    onclick="togglePass('pass1','eye1')">
                                <i class="bi bi-eye" id="eye1"></i>
                            </button>
                        </div>
                        <div class="progress mt-1" style="height:4px">
                            <div id="strengthBar" class="progress-bar" style="width:0%"></div>
                        </div>
                        <div class="mt-1 d-flex gap-3">
                            <span class="req no" id="req-len"><i class="bi bi-circle me-1"></i>8+</span>
                            <span class="req no" id="req-upper"><i class="bi bi-circle me-1"></i>Mayúscula</span>
                            <span class="req no" id="req-num"><i class="bi bi-circle me-1"></i>Número</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Repetir contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill text-muted"></i></span>
                            <input type="password" name="password2" id="pass2" class="form-control"
                                   placeholder="Repetí la contraseña" required autocomplete="new-password">
                        </div>
                        <div id="matchMsg" class="form-text"></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill">
                        <i class="bi bi-check-circle me-2"></i>Cambiar contraseña
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>/login" class="text-muted small text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Volver al login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass(id, eyeId) {
    const p = document.getElementById(id);
    const i = document.getElementById(eyeId);
    p.type = p.type === 'password' ? 'text' : 'password';
    i.className = p.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
function checkStrength(val) {
    const len = val.length >= 8, upper = /[A-Z]/.test(val), num = /[0-9]/.test(val);
    const score = [len, upper, num].filter(Boolean).length;
    const bar = document.getElementById('strengthBar');
    bar.style.width = ['0%','33%','66%','100%'][score];
    bar.className = 'progress-bar bg-' + ['secondary','danger','warning','success'][score];
    setReq('req-len', len); setReq('req-upper', upper); setReq('req-num', num);
}
function setReq(id, ok) {
    const el = document.getElementById(id);
    el.className = 'req ' + (ok ? 'ok' : 'no');
    el.querySelector('i').className = ok ? 'bi bi-check-circle-fill me-1' : 'bi bi-circle me-1';
}
document.getElementById('pass2').addEventListener('input', function() {
    const p1 = document.getElementById('pass1').value;
    const msg = document.getElementById('matchMsg');
    msg.innerHTML = this.value === p1
        ? '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Coinciden</span>'
        : '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>No coinciden</span>';
});
</script>
</body>
</html>
