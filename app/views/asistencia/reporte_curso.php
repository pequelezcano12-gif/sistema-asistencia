<?php $pageTitle = 'Reporte por Curso'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>Reporte por Curso</h5>
    <?php if ($curso_id): ?>
    <a href="<?= BASE_URL ?>/asistencia/exportar?curso_id=<?= $curso_id ?>&mes=<?= $mes ?>&anio=<?= $anio ?>"
       class="btn btn-success">
        <i class="bi bi-file-earmark-excel me-1"></i>Exportar CSV
    </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Curso</label>
                <select name="curso_id" class="form-select">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($cursos as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $curso_id == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mes</label>
                <select name="mes" class="form-select">
                    <?php $meses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
                    foreach ($meses as $num => $nombre): ?>
                    <option value="<?= $num ?>" <?= $mes == $num ? 'selected' : '' ?>><?= $nombre ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Año</label>
                <input type="number" name="anio" class="form-control" value="<?= $anio ?>" min="2020" max="2099">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Ver reporte
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($curso_id && !empty($reporte)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold border-0 pt-3">
        <?= htmlspecialchars($curso['nombre']) ?> — Resumen mensual
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Alumno</th>
                        <th class="text-success text-center">Presentes</th>
                        <th class="text-danger text-center">Ausentes</th>
                        <th class="text-warning text-center">Tardanzas</th>
                        <th class="text-info text-center">Justificados</th>
                        <th class="text-center">% Asistencia</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($reporte as $r):
                    $pct = $r['total'] > 0 ? round($r['presentes'] / $r['total'] * 100) : 0;
                    $color = $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
                ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($r['apellido'] . ', ' . $r['nombre']) ?></td>
                    <td class="text-center"><span class="badge bg-success"><?= $r['presentes'] ?></span></td>
                    <td class="text-center"><span class="badge bg-danger"><?= $r['ausentes'] ?></span></td>
                    <td class="text-center"><span class="badge bg-warning text-dark"><?= $r['tardes'] ?></span></td>
                    <td class="text-center"><span class="badge bg-info"><?= $r['justificados'] ?></span></td>
                    <td class="text-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:8px">
                                <div class="progress-bar bg-<?= $color ?>" style="width:<?= $pct ?>%"></div>
                            </div>
                            <span class="small text-<?= $color ?> fw-semibold"><?= $pct ?>%</span>
                        </div>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>/reportes/alumno/<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php elseif ($curso_id): ?>
    <div class="alert alert-info">Sin registros para el período seleccionado.</div>
<?php else: ?>
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Seleccioná un curso para ver el reporte.</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
