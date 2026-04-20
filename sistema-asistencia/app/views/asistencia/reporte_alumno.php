<?php $pageTitle = 'Reporte Alumno'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/alumnos" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">Reporte de Asistencia — <?= htmlspecialchars($alumno['apellido'] . ', ' . $alumno['nombre']) ?></h5>
</div>

<!-- Filtro fechas -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" name="desde" class="form-control" value="<?= $desde ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?= $hasta ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<!-- Resumen -->
<div class="row g-3 mb-4">
    <?php
    $total = array_sum($resumen);
    $items = [
        'presente'   => ['success', 'check-circle', 'Presentes'],
        'ausente'    => ['danger',  'x-circle',     'Ausentes'],
        'tarde'      => ['warning', 'clock',         'Tardanzas'],
        'justificado'=> ['info',    'shield-check',  'Justificados'],
    ];
    foreach ($items as $key => [$color, $icon, $label]):
        $pct = $total > 0 ? round($resumen[$key] / $total * 100) : 0;
    ?>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-<?= $icon ?>-fill fs-2 text-<?= $color ?>"></i>
            <div class="fs-3 fw-bold mt-1"><?= $resumen[$key] ?></div>
            <div class="text-muted small"><?= $label ?></div>
            <div class="progress mt-2" style="height:4px">
                <div class="progress-bar bg-<?= $color ?>" style="width:<?= $pct ?>%"></div>
            </div>
            <div class="text-muted" style="font-size:.7rem"><?= $pct ?>%</div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Detalle -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold border-0 pt-3 d-flex justify-content-between">
        <span>Detalle de asistencias</span>
        <span class="text-muted small"><?= count($registros) ?> registros</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Fecha</th><th>Curso</th><th>Materia</th><th>Estado</th><th>Observaciones</th></tr>
                </thead>
                <tbody>
                <?php foreach ($registros as $r):
                    $badges = ['presente'=>'success','ausente'=>'danger','tarde'=>'warning','justificado'=>'info'];
                ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($r['fecha'])) ?></td>
                    <td><?= htmlspecialchars($r['curso_nombre']) ?></td>
                    <td><?= htmlspecialchars($r['materia_nombre'] ?? 'General') ?></td>
                    <td><span class="badge bg-<?= $badges[$r['estado']] ?>"><?= ucfirst($r['estado']) ?></span></td>
                    <td><?= htmlspecialchars($r['observaciones'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($registros)): ?>
                <tr><td colspan="5" class="text-center text-muted py-3">Sin registros en el período</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
