<?php $pageTitle = 'Dashboard'; require __DIR__ . '/layout/header.php'; ?>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                    <i class="bi bi-people-fill fs-3 text-primary"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold"><?= $stats['alumnos'] ?></div>
                    <div class="text-muted small">Alumnos activos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="bi bi-building fs-3 text-success"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold"><?= $stats['cursos'] ?></div>
                    <div class="text-muted small">Cursos activos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                    <i class="bi bi-person-badge fs-3 text-warning"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold"><?= $stats['profesores'] ?></div>
                    <div class="text-muted small">Profesores</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 p-3">
                    <i class="bi bi-clipboard-check fs-3 text-info"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold"><?= $stats['hoy'] ?></div>
                    <div class="text-muted small">Registros hoy</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-0 pt-3">
                <i class="bi bi-clock-history me-2 text-primary"></i>Últimas asistencias
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Fecha</th><th>Alumno</th><th>Curso</th><th>Estado</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($ultimasAsistencias as $a): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($a['fecha'])) ?></td>
                                <td><?= htmlspecialchars($a['apellido'] . ', ' . $a['nombre']) ?></td>
                                <td><?= htmlspecialchars($a['curso']) ?></td>
                                <td><?php
                                    $badges = ['presente'=>'success','ausente'=>'danger','tarde'=>'warning','justificado'=>'info'];
                                    $b = $badges[$a['estado']] ?? 'secondary';
                                ?><span class="badge bg-<?= $b ?>"><?= ucfirst($a['estado']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($ultimasAsistencias)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Sin registros aún</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-0 pt-3">
                <i class="bi bi-exclamation-triangle me-2 text-danger"></i>Más ausencias este mes
            </div>
            <div class="card-body">
                <?php foreach ($ausenciasMes as $a): ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><?= htmlspecialchars($a['apellido'] . ', ' . $a['nombre']) ?></span>
                    <span class="badge bg-danger rounded-pill"><?= $a['total'] ?> faltas</span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($ausenciasMes)): ?>
                    <p class="text-muted text-center mb-0">Sin ausencias registradas</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white fw-semibold border-0 pt-3">
                <i class="bi bi-lightning-charge me-2 text-warning"></i>Accesos rápidos
            </div>
            <div class="card-body d-grid gap-2">
                <a href="<?= BASE_URL ?>/asistencia" class="btn btn-outline-primary">
                    <i class="bi bi-clipboard-plus me-2"></i>Pasar lista hoy
                </a>
                <a href="<?= BASE_URL ?>/alumnos/crear" class="btn btn-outline-success">
                    <i class="bi bi-person-plus me-2"></i>Nuevo alumno
                </a>
                <a href="<?= BASE_URL ?>/reportes/curso" class="btn btn-outline-secondary">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Ver reportes
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
