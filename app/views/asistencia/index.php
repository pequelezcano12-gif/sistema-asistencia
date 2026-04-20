<?php $pageTitle = 'Pasar Lista'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-clipboard-check me-2 text-primary"></i>Pasar Lista</h5>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($fecha) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Curso</label>
                <select name="curso_id" class="form-select">
                    <option value="">Seleccionar curso...</option>
                    <?php foreach ($cursos as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $curso_id == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Materia</label>
                <select name="materia_id" class="form-select">
                    <?php if ($_SESSION['user_rol'] === 'profesor'): ?>
                        <!-- Profesor solo ve su materia -->
                        <?php foreach ($materias as $m): ?>
                        <option value="<?= $m['id'] ?>" selected><?= htmlspecialchars($m['nombre']) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">General</option>
                        <?php foreach ($materias as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= $materia_id == $m['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
<div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
    <button type="button" class="btn btn-sm btn-success" onclick="marcarTodos('presente')">
        <i class="bi bi-check-all me-1"></i>Todos presentes
    </button>
    <button type="button" class="btn btn-sm btn-danger" onclick="marcarTodos('ausente')">
        <i class="bi bi-x-circle me-1"></i>Todos ausentes
    </button>
    <span class="ms-auto text-muted small">
        <?= count($alumnos) ?> alumnos — <?= date('d/m/Y', strtotime($fecha)) ?>
    </span>
</div>

<form method="POST" action="<?= BASE_URL ?>/asistencia/guardar">
    <input type="hidden" name="curso_id"   value="<?= $curso_id ?>">
    <input type="hidden" name="materia_id" value="<?= $materia_id ?>">
    <input type="hidden" name="fecha"      value="<?= $fecha ?>">

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Alumno</th>
                            <th class="text-center text-success">Presente</th>
                            <th class="text-center text-danger">Ausente</th>
                            <th class="text-center text-warning">Tarde</th>
                            <th class="text-center text-info">Justificado</th>
                            <th>Observaciones</th>
                            <th>Nota ausencia</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($alumnos as $i => $a):
                        $estadoActual = $asistMap[$a['id']]['estado'] ?? 'presente';
                        $obsActual    = $asistMap[$a['id']]['observaciones'] ?? '';
                        $asistId      = $asistMap[$a['id']]['id'] ?? null;
                        $notaActual   = $notasMap[$asistId] ?? '';
                    ?>
                    <tr id="fila-<?= $a['id'] ?>">
                        <td class="text-muted small"><?= $i + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($a['foto']): ?>
                                    <img src="<?= BASE_URL ?>/uploads/alumnos/<?= $a['foto'] ?>"
                                         class="rounded-circle" width="32" height="32" style="object-fit:cover">
                                <?php else: ?>
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                         style="width:32px;height:32px;font-size:.75rem">
                                        <?= strtoupper(substr($a['nombre'],0,1).substr($a['apellido'],0,1)) ?>
                                    </div>
                                <?php endif; ?>
                                <span class="fw-semibold"><?= htmlspecialchars($a['apellido'] . ', ' . $a['nombre']) ?></span>
                            </div>
                        </td>
                        <?php foreach (['presente','ausente','tarde','justificado'] as $est): ?>
                        <td class="text-center">
                            <input class="form-check-input estado-radio" type="radio"
                                   name="estados[<?= $a['id'] ?>]"
                                   value="<?= $est ?>"
                                   <?= $estadoActual === $est ? 'checked' : '' ?>
                                   onchange="colorRow(this)">
                        </td>
                        <?php endforeach; ?>
                        <td>
                            <input type="text" name="observaciones[<?= $a['id'] ?>]"
                                   class="form-control form-control-sm"
                                   placeholder="Opcional..."
                                   value="<?= htmlspecialchars($obsActual) ?>">
                        </td>
                        <td>
                            <!-- Nota de ausencia: visible solo si está ausente -->
                            <div class="nota-ausencia-group" id="nota-<?= $a['id'] ?>"
                                 style="<?= $estadoActual === 'ausente' ? '' : 'display:none' ?>">
                                <input type="text" name="nota_ausencia[<?= $a['id'] ?>]"
                                       class="form-control form-control-sm"
                                       placeholder="Motivo de ausencia..."
                                       value="<?= htmlspecialchars($notaActual) ?>">
                            </div>
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
    // Mostrar/ocultar nota de ausencia
    const alumnoId = radio.name.match(/\d+/)[0];
    const notaDiv  = document.getElementById('nota-' + alumnoId);
    if (notaDiv) notaDiv.style.display = radio.value === 'ausente' ? '' : 'none';
}
document.querySelectorAll('.estado-radio:checked').forEach(colorRow);
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
