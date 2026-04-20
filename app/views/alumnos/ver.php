<?php $pageTitle = 'Perfil Alumno'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/alumnos" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">Perfil del Alumno</h5>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <?php if ($alumno['foto']): ?>
                <img src="<?= BASE_URL ?>/uploads/alumnos/<?= $alumno['foto'] ?>" class="rounded-circle mx-auto mb-3" width="120" height="120" style="object-fit:cover">
            <?php else: ?>
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white mx-auto mb-3" style="width:120px;height:120px;font-size:2.5rem">
                    <?= strtoupper(substr($alumno['nombre'],0,1).substr($alumno['apellido'],0,1)) ?>
                </div>
            <?php endif; ?>
            <h5 class="fw-bold"><?= htmlspecialchars($alumno['apellido'] . ', ' . $alumno['nombre']) ?></h5>
            <p class="text-muted">DNI: <?= htmlspecialchars($alumno['dni']) ?></p>
            <span class="badge bg-<?= $alumno['activo'] ? 'success' : 'secondary' ?> mb-3">
                <?= $alumno['activo'] ? 'Activo' : 'Inactivo' ?>
            </span>
            <?php if (in_array($_SESSION['user_rol'], ['admin','directivo'])): ?>
            <div class="d-grid gap-2">
                <a href="<?= BASE_URL ?>/alumnos/<?= $alumno['id'] ?>/editar" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
                <a href="<?= BASE_URL ?>/reportes/alumno/<?= $alumno['id'] ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-bar-chart me-1"></i>Ver reporte
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold border-0 pt-3">Datos personales</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6"><small class="text-muted">Email</small><div><?= htmlspecialchars($alumno['email'] ?: '—') ?></div></div>
                    <div class="col-6"><small class="text-muted">Teléfono</small><div><?= htmlspecialchars($alumno['telefono'] ?: '—') ?></div></div>
                    <div class="col-6"><small class="text-muted">Fecha de nacimiento</small><div><?= $alumno['fecha_nacimiento'] ? date('d/m/Y', strtotime($alumno['fecha_nacimiento'])) : '—' ?></div></div>
                    <div class="col-6"><small class="text-muted">Curso actual</small><div><?= htmlspecialchars($alumno['curso_nombre'] ?? '—') ?></div></div>
                    <div class="col-12"><small class="text-muted">Dirección</small><div><?= htmlspecialchars($alumno['direccion'] ?: '—') ?></div></div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-0 pt-3">Inscripciones</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>Curso</th><th>Turno</th><th>Año</th></tr></thead>
                    <tbody>
                    <?php foreach ($inscripciones as $i): ?>
                        <tr>
                            <td><?= htmlspecialchars($i['curso_nombre']) ?></td>
                            <td><?= ucfirst($i['turno']) ?></td>
                            <td><?= $i['anio_lectivo'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($inscripciones)): ?>
                        <tr><td colspan="3" class="text-center text-muted">Sin inscripciones</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
