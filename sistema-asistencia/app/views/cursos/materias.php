<?php $pageTitle = 'Materias del Curso'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/cursos" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">Materias — <?= htmlspecialchars($curso['nombre']) ?></h5>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-0 pt-3">Asignar materia</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Materia</label>
                        <select name="materia_id" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($materias as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profesor asignado</label>
                        <select name="profesor_id" class="form-select">
                            <option value="">Sin asignar</option>
                            <?php foreach ($profesores as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['apellido'] . ', ' . $p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i>Asignar
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-0 pt-3">Materias asignadas</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>Materia</th><th>Profesor</th></tr></thead>
                    <tbody>
                    <?php foreach ($asignadas as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['nombre']) ?></td>
                        <td><?= $a['prof_nombre'] ? htmlspecialchars($a['prof_apellido'] . ', ' . $a['prof_nombre']) : '<span class="text-muted">Sin asignar</span>' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($asignadas)): ?>
                    <tr><td colspan="2" class="text-center text-muted">Sin materias asignadas</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
