<?php $pageTitle = 'Pasar Lista'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-clipboard-check me-2 text-primary"></i>Pasar Lista</h5>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($fecha) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Curso</label>
                <select name="curso_id" class="form-select">
                    <option value="">Seleccionar curso...</option>
                    <?php foreach ($cursos as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $curso_id == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nombre']) ?> (<?= $c['turno'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Materia (opcional)</label>
                <select name="materia_id" class="form-select">
                    <option value="">General</option>
                    <?php foreach ($materias as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= $materia_id == $m['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Cargar lista
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($curso_id && !empty($alumnos)): ?>
<!-- Botones de selección rápida -->
<div class="d-flex gap-2 mb-3 flex-wrap">
    <button type="button" class="btn btn-sm btn-success" onclick="marcarTodos('presente')">
        <i class="bi bi-check-all me-1"></i>Todos presentes
    </button>
    <button type="button" class="btn btn-sm btn-danger" onclick="marcarTodos('ausente')">
        <i class="bi bi-x-circle me-1"></i>Todos ausentes
    </button>
    <span class="ms-auto text-muted small align-self-center">
        <?= count($alumnos) ?> alumnos — <?= date('d/m/Y', strtotime($fecha)) ?>
    </span>
</div>

<form method="POST" action="<?= BASE_URL ?>/asistencia/guardar">
    <input type="hidden" name="curso_id" value="<?= $curso_id ?>">
    <input type="hidden" name="materia_id" value="<?= $materia_id ?>">
    <input type="hidden" name="fecha" value="<?= $fecha ?>">

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Alumno</th>
                            <th>Presente</th>
                            <th>Ausente</th>
                            <th>Tarde</th>
                            <th>Justificado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($alumnos as $i => $a):
                        $estadoActual = $asistMap[$a['id']]['estado'] ?? 'presente';
                        $obsActual    = $asistMap[$a['id']]['observaciones'] ?? '';
                    ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($a['foto']): ?>
                                    <img src="<?= BASE_URL ?>/uploads/alumnos/<?= $a['foto'] ?>" class="rounded-circle" width="32" height="32" style="object-fit:cover">
                                <?php else: ?>
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width:32px;height:32px;font-size:.75rem">
                                        <?= strtoupper(substr($a['nombre'],0,1).substr($a['apellido'],0,1)) ?>
                                    </div>
                                <?php endif; ?>
                                <span class="fw-semibold"><?= htmlspecialchars($a['apellido'] . ', ' . $a['nombre']) ?></span>
                            </div>
                        </td>
                        <?php foreach (['presente','ausente','tarde','justificado'] as $est):
                            $colors = ['presente'=>'success','ausente'=>'danger','tarde'=>'warning','justificado'=>'info'];
                        ?>
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input estado-radio" type="radio"
                                    name="estados[<?= $a['id'] ?>]"
                                    value="<?= $est ?>"
                                    data-alumno="<?= $a['id'] ?>"
                                    <?= $estadoActual === $est ? 'checked' : '' ?>
                                    onchange="colorRow(this)">
                            </div>
                        </td>
                        <?php endforeach; ?>
                        <td>
                            <input type="text" name="observaciones[<?= $a['id'] ?>]"
                                class="form-control form-control-sm"
                                placeholder="Opcional..."
                                value="<?= htmlspecialchars($obsActual) ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-save me-2"></i>Guardar asistencia
        </button>
        <a href="<?= BASE_URL ?>/reportes/curso?curso_id=<?= $curso_id ?>" class="btn btn-outline-secondary btn-lg">
            <i class="bi bi-bar-chart me-1"></i>Ver reporte
        </a>
    </div>
</form>

<?php elseif ($curso_id): ?>
    <div class="alert alert-warning">No hay alumnos inscriptos en este curso.</div>
<?php else: ?>
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Seleccioná un curso y fecha para pasar lista.</div>
<?php endif; ?>

<script>
function marcarTodos(estado) {
    document.querySelectorAll(`input[type=radio][value="${estado}"]`).forEach(r => {
        r.checked = true;
        colorRow(r);
    });
}
function colorRow(radio) {
    const colors = {presente:'table-success',ausente:'table-danger',tarde:'table-warning',justificado:'table-info'};
    const tr = radio.closest('tr');
    tr.className = tr.className.replace(/table-\w+/g, '');
    tr.classList.add(colors[radio.value] || '');
}
// Colorear al cargar
document.querySelectorAll('.estado-radio:checked').forEach(colorRow);
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
