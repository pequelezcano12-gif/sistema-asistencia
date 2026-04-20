<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Sistema de Asistencia' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/app.css">
</head>
<body>
<?php
// Rol efectivo: modo dios puede simular otro rol
$rolEfectivo = !empty($_SESSION['modo_dios_rol']) ? $_SESSION['modo_dios_rol'] : ($_SESSION['user_rol'] ?? '');
$esModosDios = !empty($_SESSION['modo_dios_rol']);
?>
<?php if (!empty($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark <?= $esModosDios ? 'bg-warning' : 'bg-primary' ?>">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold <?= $esModosDios ? 'text-dark' : '' ?>" href="<?= BASE_URL ?>/dashboard">
            <i class="bi bi-calendar-check-fill me-2"></i>AsistenciaEdu
            <?php if ($esModosDios): ?>
                <span class="badge bg-dark ms-2" style="font-size:.7rem">
                    <i class="bi bi-lightning-charge-fill me-1"></i>MODO DIOS — <?= strtoupper($rolEfectivo) ?>
                </span>
            <?php endif; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <?php if ($rolEfectivo !== 'alumno'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $esModosDios ? 'text-dark' : '' ?>" href="<?= BASE_URL ?>/dashboard">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <?php endif; ?>

                <?php if (in_array($rolEfectivo, ['admin','directivo','profesor'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $esModosDios ? 'text-dark' : '' ?>" href="<?= BASE_URL ?>/alumnos">
                        <i class="bi bi-people me-1"></i>Alumnos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $esModosDios ? 'text-dark' : '' ?>" href="<?= BASE_URL ?>/cursos">
                        <i class="bi bi-building me-1"></i>Cursos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $esModosDios ? 'text-dark' : '' ?>" href="<?= BASE_URL ?>/asistencia">
                        <i class="bi bi-clipboard-check me-1"></i>Asistencia
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= $esModosDios ? 'text-dark' : '' ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bar-chart me-1"></i>Reportes
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/reportes/curso">Por Curso</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/reportes/alumno">Por Alumno</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (in_array($rolEfectivo, ['admin','directivo'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $esModosDios ? 'text-dark' : '' ?>" href="<?= BASE_URL ?>/materias">
                        <i class="bi bi-book me-1"></i>Materias
                    </a>
                </li>
                <li class="nav-item">
                    <?php
                    $pendJ = 0;
                    try {
                        $pendJ = (int)getDB()->query("SELECT COUNT(*) FROM justificativos WHERE estado='pendiente'")->fetchColumn();
                    } catch (Exception $e) {}
                    ?>
                    <a class="nav-link <?= $esModosDios ? 'text-dark' : '' ?>" href="<?= BASE_URL ?>/justificativos">
                        <i class="bi bi-file-earmark-check me-1"></i>Justificativos
                        <?php if ($pendJ > 0): ?>
                            <span class="badge bg-danger"><?= $pendJ ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $esModosDios ? 'text-dark' : '' ?>" href="<?= BASE_URL ?>/usuarios">
                        <i class="bi bi-person-gear me-1"></i>Usuarios
                    </a>
                </li>
                <!-- MODO DIOS -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-bold <?= $esModosDios ? 'text-dark' : 'text-warning' ?>"
                       href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-lightning-charge-fill me-1"></i>Modo Dios
                    </a>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header">Ver el sistema como...</h6></li>
                        <li>
                            <a class="dropdown-item <?= !$esModosDios ? 'active' : '' ?>" href="<?= BASE_URL ?>/modo-dios/reset">
                                <i class="bi bi-shield-fill me-2 text-danger"></i>Admin (vista normal)
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item <?= $rolEfectivo === 'directivo' ? 'active' : '' ?>" href="<?= BASE_URL ?>/modo-dios/directivo">
                                <i class="bi bi-star-fill me-2" style="color:#6f42c1"></i>Directivo
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?= $rolEfectivo === 'profesor' ? 'active' : '' ?>" href="<?= BASE_URL ?>/modo-dios/profesor">
                                <i class="bi bi-person-badge me-2 text-primary"></i>Profesor
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?= $rolEfectivo === 'alumno' ? 'active' : '' ?>" href="<?= BASE_URL ?>/modo-dios/alumno">
                                <i class="bi bi-mortarboard me-2 text-success"></i>Alumno
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?= $rolEfectivo === 'padre' ? 'active' : '' ?>" href="<?= BASE_URL ?>/modo-dios/padre">
                                <i class="bi bi-people me-2 text-info"></i>Padre / Madre
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (in_array($rolEfectivo, ['padre']) || !empty($_SESSION['es_tambien_padre'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $esModosDios ? 'text-dark' : '' ?>" href="<?= BASE_URL ?>/justificativos/crear">
                        <i class="bi bi-file-earmark-plus me-1"></i>Justificar ausencia
                    </a>
                </li>
                <?php endif; ?>

            </ul>

            <ul class="navbar-nav align-items-center">
                <?php
                $noLeidas = 0;
                try {
                    $stN = getDB()->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id=:u AND leida=FALSE");
                    $stN->execute([':u' => $_SESSION['user_id']]);
                    $noLeidas = (int)$stN->fetchColumn();
                } catch (Exception $e) {}
                ?>
                <li class="nav-item me-2">
                    <a class="nav-link position-relative <?= $esModosDios ? 'text-dark' : '' ?>" href="<?= BASE_URL ?>/notificaciones">
                        <i class="bi bi-bell fs-5"></i>
                        <?php if ($noLeidas > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">
                            <?= $noLeidas ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= $esModosDios ? 'text-dark' : '' ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user_nombre']) ?>
                        <span class="badge <?= $esModosDios ? 'bg-dark' : 'bg-warning text-dark' ?> ms-1">
                            <?= ucfirst($_SESSION['user_rol']) ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout">
                            <i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="container-fluid py-4">
<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
