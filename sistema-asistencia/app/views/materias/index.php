<?php $pageTitle = 'Materias'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-book me-2 text-primary"></i>Materias</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMateria">
        <i class="bi bi-plus-lg me-1"></i>Nueva materia
    </button>
</div>

<div class="row g-3">
<?php foreach ($materias as $m): ?>
<div class="col-md-4 col-lg-3">
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-journal-text text-primary me-2"></i>
                <span class="fw-semibold"><?= htmlspecialchars($m['nombre']) ?></span>
                <?php if ($m['descripcion']): ?>
                <div class="text-muted small mt-1"><?= htmlspecialchars($m['descripcion']) ?></div>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-1">
                <a href="<?= BASE_URL ?>/materias/<?= $m['id'] ?>/editar" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <a href="<?= BASE_URL ?>/materias/<?= $m['id'] ?>/eliminar"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('¿Eliminar materia?')"><i class="bi bi-trash"></i></a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- Modal nueva materia -->
<div class="modal fade" id="modalMateria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= BASE_URL ?>/materias/crear">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Materia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
