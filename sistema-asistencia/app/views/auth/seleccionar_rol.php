<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar modo — AsistenciaEdu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%); min-height:100vh; display:flex; align-items:center; }
        .auth-card { border-radius: 1.25rem; box-shadow: 0 25px 60px rgba(0,0,0,.3); }
        .rol-card { border: 2px solid #dee2e6; border-radius: 1rem; cursor: pointer; transition: all .2s; padding: 1.5rem; }
        .rol-card:hover { border-color: #1a73e8; background: #f0f4ff; transform: translateY(-2px); }
        .rol-card.selected { border-color: #1a73e8; background: #e8f0fe; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card auth-card p-4 text-center">
                <div style="font-size:2.5rem">👤</div>
                <h4 class="fw-bold mt-2">¿Cómo querés ingresar?</h4>
                <p class="text-muted small">
                    Hola <strong><?= htmlspecialchars($_SESSION['user_nombre']) ?></strong>,
                    detectamos que sos docente y también tenés hijos en el sistema.
                </p>

                <form method="POST">
                    <?= Security::csrfField() ?>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="w-100">
                                <input type="radio" name="modo" value="profesor" class="d-none" checked>
                                <div class="rol-card selected" id="card-profesor" onclick="selectCard('profesor')">
                                    <div style="font-size:2.5rem">👨‍🏫</div>
                                    <div class="fw-bold mt-2">Docente</div>
                                    <div class="text-muted small">Gestionar clases y asistencia</div>
                                </div>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="w-100">
                                <input type="radio" name="modo" value="padre" class="d-none">
                                <div class="rol-card" id="card-padre" onclick="selectCard('padre')">
                                    <div style="font-size:2.5rem">👨‍👧</div>
                                    <div class="fw-bold mt-2">Padre/Madre</div>
                                    <div class="text-muted small">Ver asistencia de mis hijos</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill">
                        <i class="bi bi-arrow-right-circle me-2"></i>Continuar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function selectCard(modo) {
    document.querySelectorAll('.rol-card').forEach(c => c.classList.remove('selected'));
    document.getElementById('card-' + modo).classList.add('selected');
    document.querySelector(`input[value="${modo}"]`).checked = true;
}
</script>
</body>
</html>
