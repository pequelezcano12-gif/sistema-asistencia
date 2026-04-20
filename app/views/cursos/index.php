<?php $pageTitle = 'Cursos'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-building me-2 text-primary"></i>Cursos / Grados</h5>
    <?php if (in_array($_SESSION['user_rol'], ['admin','directivo'])): ?>
    <a href="<?= BASE_URL ?>/cursos/crear" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuevo curso
    </a>
    <?php endif; ?>
</div>

<div class="row g-3">
<?php foreach ($cursos as $c): ?>
<div class="col-md-4 col-lg-3">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($c['nombre']) ?></h5>
                </div>
                <span class="text-muted small"><?= $c['anio_lectivo'] ?></span>
            </div>
            <div class="mt-3 text-muted small">
                <i class="bi bi-people me-1"></i><?= $c['total_alumnos'] ?> alumnos
            </div>
        </div>
        <div class="card-footer bg-white border-0 d-flex gap-1">
            <a href="<?= BASE_URL ?>/asistencia?curso_id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary flex-fill">
                <i class="bi bi-clipboard-check"></i> Lista
            </a>
            <?php if (in_array($_SESSION['user_rol'], ['admin','directivo'])): ?>
            <a href="<?= BASE_URL ?>/cursos/<?= $c['id'] ?>/materias" class="btn btn-sm btn-outline-secondary" title="Materias"><i class="bi bi-book"></i></a>
            <a href="<?= BASE_URL ?>/cursos/<?= $c['id'] ?>/editar" class="btn btn-sm btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
            <a href="<?= BASE_URL ?>/cursos/<?= $c['id'] ?>/eliminar" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('¿Eliminar curso?')" title="Eliminar"><i class="bi bi-trash"></i></a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php if (empty($cursos)): ?>
<div class="col-12"><div class="alert alert-info">No hay cursos registrados.</div></div>
<?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
