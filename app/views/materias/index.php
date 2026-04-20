<?php $pageTitle = 'Asignaturas'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-book me-2 text-primary"></i>Asignaturas</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMateria">
        <i class="bi bi-plus-lg me-1"></i>Nueva asignatura
    </button>
</div>

<?php if (empty($materias)): ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    No hay asignaturas registradas. Se crean automáticamente al registrar docentes,
    o podés agregarlas manualmente con el botón de arriba.
</div>
<?php else: ?>
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
                <a href="<?= BASE_URL ?>/materias/<?= $m['id'] ?>/eliminar"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('¿Eliminar esta asignatura?')">
                    <i class="bi bi-trash"></i>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Modal nueva asignatura -->
<div class="modal fade" id="modalMateria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= BASE_URL ?>/materias/crear">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Asignatura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre *</label>
                        <input type="text" name="nombre" class="form-control"
                               placeholder="Ej: Matemática, Lengua, Historia..." required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción (opcional)</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear asignatura</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
