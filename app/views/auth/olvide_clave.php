<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olvide mi contrasena</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%); min-height:100vh; display:flex; align-items:center; }
        .auth-card { border-radius: 1.25rem; box-shadow: 0 25px 60px rgba(0,0,0,.3); }
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
            <div class="card auth-card p-4 text-center">
                <div style="font-size:3rem">&#128273;</div>
                <h4 class="fw-bold mt-2">Olvide mi contrasena</h4>
                <p class="text-muted small">Ingresa tu cedula y te enviaremos un codigo a tu email registrado.</p>
                <form method="POST" class="text-start">
                    <?= Security::csrfField() ?>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Numero de cedula</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-vcard text-muted"></i></span>
                            <input type="text" name="cedula" class="form-control"
                                   placeholder="Tu numero de cedula"
                                   maxlength="20" required autofocus
                                   oninput="this.value=this.value.toUpperCase()">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill">
                        <i class="bi bi-send me-2"></i>Enviar codigo
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>