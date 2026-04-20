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
<?php if (!empty($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/dashboard">
            <i class="bi bi-calendar-check-fill me-2"></i>AsistenciaEdu
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/dashboard"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                </li>
                <?php if (in_array($_SESSION['user_rol'], ['admin','directivo','profesor'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/alumnos"><i class="bi bi-people me-1"></i>Alumnos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/cursos"><i class="bi bi-building me-1"></i>Cursos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/asistencia"><i class="bi bi-clipboard-check me-1"></i>Asistencia</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-bar-chart me-1"></i>Reportes</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/reportes/curso">Por Curso</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/reportes/alumno">Por Alumno</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if (in_array($_SESSION['user_rol'], ['admin','directivo'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/materias"><i class="bi bi-book me-1"></i>Materias</a>
                </li>
                <?php endif; ?>
                <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/usuarios"><i class="bi bi-person-gear me-1"></i>Usuarios</a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user_nombre']) ?>
                        <span class="badge bg-warning text-dark ms-1"><?= ucfirst($_SESSION['user_rol']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout"><i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión</a></li>
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
        <?= $_SESSION['success'] ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['error'] ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
