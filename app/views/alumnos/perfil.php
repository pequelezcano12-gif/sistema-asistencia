<?php $pageTitle = 'Mi Perfil'; require __DIR__ . '/../layout/header.php'; ?>

<div class="row g-4">

    <!-- Tarjeta de perfil -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white mx-auto mb-3"
                 style="width:90px;height:90px;font-size:2rem;font-weight:700">
                <?= strtoupper(substr($alumno['nombre'],0,1) . substr($alumno['apellido'],0,1)) ?>
            </div>
            <h5 class="fw-bold mb-1"><?= htmlspecialchars($alumno['apellido'] . ', ' . $alumno['nombre']) ?></h5>
            <p class="text-muted small mb-1">DNI: <?= htmlspecialchars($alumno['dni']) ?></p>
            <p class="text-muted small mb-3">
                <i class="bi bi-building me-1"></i><?= htmlspecialchars($alumno['curso_nombre'] ?? 'Sin curso asignado') ?>
            </p>
            <span class="badge bg-<?= $alumno['activo'] ? 'success' : 'secondary' ?> mb-3">
                <?= $alumno['activo'] ? 'Activo' : 'Inactivo' ?>
            </span>

            <hr>
            <div class="text-start small">
                <?php if ($alumno['email']): ?>
                <div class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i><?= htmlspecialchars($alumno['email']) ?></div>
                <?php endif; ?>
                <?php if ($alumno['telefono']): ?>
                <div class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i><?= htmlspecialchars($alumno['telefono']) ?></div>
                <?php endif; ?>
                <?php if ($alumno['fecha_nacimiento']): ?>
                <div class="mb-2"><i class="bi bi-calendar me-2 text-muted"></i><?= date('d/m/Y', strtotime($alumno['fecha_nacimiento'])) ?></div>
                <?php endif; ?>
                <?php if ($alumno['nombre_encargado']): ?>
                <div class="mb-2"><i class="bi bi-people me-2 text-muted"></i><?= htmlspecialchars($alumno['nombre_encargado']) ?></div>
                <?php endif; ?>
            </div>

            <a href="<?= BASE_URL ?>/logout" class="btn btn-outline-secondary btn-sm mt-3">
                <i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión
            </a>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="col-lg-8">

        <!-- Resumen de asistencia -->
        <div class="row g-3 mb-4">
            <?php
            $total = array_sum($resumen);
            $pctPresente = $total > 0 ? round($resumen['presente'] / $total * 100) : 0;
            $items = [
                ['presente',    'success', 'check-circle-fill', 'Presentes'],
                ['ausente',     'danger',  'x-circle-fill',     'Ausencias'],
                ['tarde',       'warning', 'clock-fill',        'Tardanzas'],
                ['justificado', 'info',    'shield-check-fill', 'Justificados'],
            ];
            foreach ($items as [$key, $color, $icon, $label]):
            ?>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <i class="bi bi-<?= $icon ?> fs-2 text-<?= $color ?>"></i>
                    <div class="fs-2 fw-bold mt-1"><?= $resumen[$key] ?></div>
                    <div class="text-muted small"><?= $label ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Barra de asistencia general -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold">Porcentaje de asistencia <?= date('Y') ?></span>
                    <span class="fw-bold text-<?= $pctPresente >= 75 ? 'success' : ($pctPresente >= 50 ? 'warning' : 'danger') ?>">
                        <?= $pctPresente ?>%
                    </span>
                </div>
                <div class="progress" style="height:12px;border-radius:99px">
                    <div class="progress-bar bg-<?= $pctPresente >= 75 ? 'success' : ($pctPresente >= 50 ? 'warning' : 'danger') ?>"
                         style="width:<?= $pctPresente ?>%;border-radius:99px"></div>
                </div>
                <?php if ($pctPresente < 75): ?>
                <div class="alert alert-warning mt-3 mb-0 py-2 small">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Tu asistencia está por debajo del 75% requerido. Regularizá tu situación.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Últimas asistencias -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold border-0 pt-3 d-flex justify-content-between">
                <span><i class="bi bi-calendar-check me-2 text-primary"></i>Últimas asistencias</span>
                <span class="text-muted small"><?= count($registros) ?> registros</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr><th>Fecha</th><th>Materia</th><th>Estado</th><th>Observación</th></tr>
                        </thead>
                        <tbody>
                        <?php
                        $badges = ['presente'=>'success','ausente'=>'danger','tarde'=>'warning','justificado'=>'info'];
                        foreach (array_slice($registros, 0, 20) as $r):
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($r['fecha'])) ?></td>
                            <td><?= htmlspecialchars($r['materia_nombre'] ?? 'General') ?></td>
                            <td>
                                <span class="badge bg-<?= $badges[$r['estado']] ?>">
                                    <?= ucfirst($r['estado']) ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= htmlspecialchars($r['observaciones'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($registros)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">Sin registros aún.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Justificativos enviados -->
        <?php if (!empty($justificativos)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-0 pt-3">
                <i class="bi bi-file-earmark-text me-2 text-primary"></i>Mis justificativos
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Fecha ausencia</th><th>Motivo</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($justificativos as $j):
                        $jColor = ['pendiente'=>'warning text-dark','aprobado'=>'success','rechazado'=>'danger'][$j['estado']] ?? 'secondary';
                    ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($j['fecha_ausencia'])) ?></td>
                        <td class="small"><?= htmlspecialchars(substr($j['motivo'],0,60)) ?><?= strlen($j['motivo'])>60?'...':'' ?></td>
                        <td><span class="badge bg-<?= $jColor ?>"><?= ucfirst($j['estado']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
