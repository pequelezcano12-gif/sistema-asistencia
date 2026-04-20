<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AsistenciaEdu — Sistema de Gestión Escolar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary: #1a73e8; --dark: #0d47a1; }
        body { font-family: 'Segoe UI', sans-serif; }
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: rgba(255,255,255,.05);
            border-radius: 50%;
            top: -200px; right: -200px;
        }
        .hero::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: rgba(255,255,255,.05);
            border-radius: 50%;
            bottom: -150px; left: -100px;
        }
        .hero-content { position: relative; z-index: 1; }
        .logo-icon { font-size: 4rem; }
        .feature-card {
            border: none;
            border-radius: 1rem;
            transition: transform .2s, box-shadow .2s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0,0,0,.1) !important;
        }
        .btn-hero {
            padding: .75rem 2rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 1.05rem;
            transition: all .2s;
        }
        .btn-hero:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.2); }
        .wave {
            position: absolute;
            bottom: 0; left: 0; right: 0;
        }
    </style>
</head>
<body>

<!-- HERO -->
<section class="hero text-white">
    <div class="container hero-content">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="logo-icon mb-3">📋</div>
                <h1 class="display-4 fw-bold mb-3">AsistenciaEdu</h1>
                <p class="lead mb-4 opacity-75">
                    Sistema integral de gestión escolar. Control de asistencia, alumnos,
                    cursos y reportes en un solo lugar.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= BASE_URL ?>/login" class="btn btn-light btn-hero text-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                    </a>
                    <a href="<?= BASE_URL ?>/registrarse" class="btn btn-outline-light btn-hero">
                        <i class="bi bi-person-plus me-2"></i>Registrarse
                    </a>
                </div>
                <p class="mt-3 opacity-50 small">
                    <i class="bi bi-shield-check me-1"></i>
                    Acceso seguro con verificación de identidad
                </p>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center">
                <div style="font-size:12rem;opacity:.15;line-height:1">🏫</div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">¿Qué podés hacer?</h2>
        <div class="row g-4">
            <?php
            $features = [
                ['bi-clipboard-check','primary','Asistencia diaria','Pasá lista fácilmente con estados: presente, ausente, tarde o justificado.'],
                ['bi-people','success','Gestión de alumnos','Registrá alumnos, asignalos a cursos y seguí su historial completo.'],
                ['bi-bar-chart','warning','Reportes','Generá reportes mensuales por alumno o curso y exportalos a Excel.'],
                ['bi-shield-lock','danger','Acceso seguro','Cada usuario accede solo a lo que le corresponde según su rol.'],
                ['bi-bell','info','Notificaciones','Alertas automáticas sobre ausencias y situaciones importantes.'],
                ['bi-person-badge','secondary','Múltiples roles','Admin, Directivo, Docente y Alumno con permisos diferenciados.'],
            ];
            foreach ($features as [$icon, $color, $title, $desc]):
            ?>
            <div class="col-md-4">
                <div class="card feature-card shadow-sm h-100 p-4">
                    <div class="rounded-circle bg-<?= $color ?> bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px">
                        <i class="bi bi-<?= $icon ?> fs-4 text-<?= $color ?>"></i>
                    </div>
                    <h5 class="fw-bold"><?= $title ?></h5>
                    <p class="text-muted mb-0"><?= $desc ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ROLES INFO -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">¿Cómo accedo?</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="bi bi-mortarboard fs-4 text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Alumnos</h5>
                    </div>
                    <p class="text-muted">Si sos alumno, registrate con tu número de cédula. El sistema verificará que estés inscripto y te pedirá la cédula de tu padre/madre/tutor.</p>
                    <a href="<?= BASE_URL ?>/registrarse" class="btn btn-outline-primary mt-auto">
                        <i class="bi bi-person-plus me-1"></i>Registrarme como alumno
                    </a>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-person-badge fs-4 text-success"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Docentes</h5>
                    </div>
                    <p class="text-muted">Si sos docente, el administrador debe haberte pre-registrado. Luego podés completar tu cuenta con tu cédula, email y contraseña.</p>
                    <a href="<?= BASE_URL ?>/registrarse" class="btn btn-outline-success mt-auto">
                        <i class="bi bi-person-plus me-1"></i>Completar mi cuenta
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="py-4 bg-dark text-white text-center">
    <p class="mb-0 opacity-50 small">
        AsistenciaEdu &copy; <?= date('Y') ?> — Sistema de Gestión Escolar Seguro
        <span class="mx-2">·</span>
        <i class="bi bi-shield-check me-1"></i>Protegido con cifrado Argon2id
    </p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
