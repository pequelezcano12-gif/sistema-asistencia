<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completar registro — AsistenciaEdu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%); min-height:100vh; display:flex; align-items:center; padding: 2rem 0; }
        .auth-card { border-radius: 1.25rem; box-shadow: 0 25px 60px rgba(0,0,0,.3); }
        .step-badge { width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem; }
        .req { font-size: .78rem; }
        .req.ok { color: #198754; }
        .req.no { color: #6c757d; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card auth-card p-4">
                <div class="text-center mb-3">
                    <div style="font-size:2rem">📋</div>
                    <h4 class="fw-bold mt-1 mb-0">Completar registro</h4>
                    <p class="text-muted small">Paso 2 de 2 — Datos de acceso</p>
                </div>

                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="step-badge bg-success text-white"><i class="bi bi-check"></i></div>
                    <div class="flex-grow-1" style="height:2px;background:#198754"></div>
                    <div class="step-badge bg-primary text-white">2</div>
                </div>

                <?php
                $tipoLabel = match($tipo) {
                    'alumno'                 => ['mortarboard','primary','Alumno'],
                    'profesor_preregistrado' => ['person-badge','success','Docente'],
                    'padre'                  => ['people-fill','info','Padre / Madre / Encargado'],
                    default                  => ['person','secondary','Usuario'],
                };
                ?>
                <div class="alert alert-<?= $tipoLabel[1] ?> py-2 small mb-3">
                    <i class="bi bi-<?= $tipoLabel[0] ?> me-1"></i>
                    Cédula verificada como: <strong><?= $tipoLabel[2] ?></strong>
                    <span class="ms-2 opacity-75">(<?= htmlspecialchars($cedula) ?>)</span>
                </div>

                <?php if ($error ?? null): ?>
                    <div class="alert alert-danger py-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" autocomplete="off" novalidate>
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <?php if ($tipo === 'padre'): ?>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Nombre *</label>
                            <input type="text" name="nombre" class="form-control" required
                                   value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Apellido *</label>
                            <input type="text" name="apellido" class="form-control" required
                                   value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>">
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control"
                                   placeholder="tu@email.com" required
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Contraseña *</label>
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
                        <div class="mt-1 d-flex gap-3 flex-wrap">
                            <span class="req no" id="req-len"><i class="bi bi-circle me-1"></i>8+</span>
                            <span class="req no" id="req-upper"><i class="bi bi-circle me-1"></i>Mayúscula</span>
                            <span class="req no" id="req-num"><i class="bi bi-circle me-1"></i>Número</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Repetir contraseña *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill text-muted"></i></span>
                            <input type="password" name="password2" id="pass2" class="form-control"
                                   placeholder="Repetí la contraseña" required autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary border-start-0"
                                    onclick="togglePass('pass2','eye2')">
                                <i class="bi bi-eye" id="eye2"></i>
                            </button>
                        </div>
                        <div id="matchMsg" class="form-text"></div>
                    </div>

                    <?php if ($tipo === 'alumno'): ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Cédula del padre/madre/tutor *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-people text-muted"></i></span>
                            <input type="text" name="cedula_padre" class="form-control"
                                   placeholder="Cédula del responsable" maxlength="20" required
                                   oninput="this.value=this.value.toUpperCase()"
                                   value="<?= htmlspecialchars($_POST['cedula_padre'] ?? '') ?>">
                        </div>
                    </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill mt-2">
                        <i class="bi bi-check-circle me-2"></i>Crear cuenta
                    </button>
                </form>
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
    checkMatch();
}
function setReq(id, ok) {
    const el = document.getElementById(id);
    el.className = 'req ' + (ok ? 'ok' : 'no');
    el.querySelector('i').className = ok ? 'bi bi-check-circle-fill me-1' : 'bi bi-circle me-1';
}
function checkMatch() {
    const p1 = document.getElementById('pass1').value;
    const p2 = document.getElementById('pass2').value;
    const msg = document.getElementById('matchMsg');
    if (!p2) { msg.textContent = ''; return; }
    msg.innerHTML = p1 === p2
        ? '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Coinciden</span>'
        : '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>No coinciden</span>';
}
document.getElementById('pass2').addEventListener('input', checkMatch);
</script>
</body>
</html>
