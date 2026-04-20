<?php $pageTitle = 'Alumnos'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Alumnos</h5>
    <?php if (in_array($_SESSION['user_rol'], ['admin','directivo'])): ?>
    <a href="<?= BASE_URL ?>/alumnos/crear" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>Nuevo alumno
    </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="buscar" class="form-control" placeholder="Buscar por nombre, apellido o DNI...">
            </div>
            <div class="col-md-3">
                <select id="filtroCurso" class="form-select">
                    <option value="">Todos los cursos</option>
                    <?php foreach ($cursos as $c): ?>
                    <option value="<?= htmlspecialchars($c['nombre']) ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select id="filtroEstado" class="form-select">
                    <option value="">Todos</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaAlumnos">
                <thead class="table-light">
                    <tr>
                        <th>Foto</th><th>Apellido y Nombre</th><th>DNI</th>
                        <th>Curso</th><th>Estado</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($alumnos as $a): ?>
                <tr data-nombre="<?= strtolower($a['apellido'].' '.$a['nombre']) ?>"
                    data-dni="<?= $a['dni'] ?>"
                    data-curso="<?= htmlspecialchars($a['curso_nombre'] ?? '') ?>"
                    data-activo="<?= $a['activo'] ?>">
                    <td>
                        <?php if ($a['foto']): ?>
                            <img src="<?= BASE_URL ?>/uploads/alumnos/<?= $a['foto'] ?>" class="rounded-circle" width="40" height="40" style="object-fit:cover">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width:40px;height:40px;font-size:.9rem">
                                <?= strtoupper(substr($a['nombre'],0,1).substr($a['apellido'],0,1)) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="fw-semibold"><?= htmlspecialchars($a['apellido'] . ', ' . $a['nombre']) ?></td>
                    <td><?= htmlspecialchars($a['dni']) ?></td>
                    <td><?= htmlspecialchars($a['curso_nombre'] ?? '—') ?></td>
                    <td>
                        <span class="badge bg-<?= $a['activo'] ? 'success' : 'secondary' ?>">
                            <?= $a['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>/alumnos/<?= $a['id'] ?>" class="btn btn-sm btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>
                        <?php if (in_array($_SESSION['user_rol'], ['admin','directivo'])): ?>
                        <a href="<?= BASE_URL ?>/alumnos/<?= $a['id'] ?>/editar" class="btn btn-sm btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                        <a href="<?= BASE_URL ?>/reportes/alumno/<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary" title="Reporte"><i class="bi bi-bar-chart"></i></a>
                        <a href="<?= BASE_URL ?>/alumnos/<?= $a['id'] ?>/eliminar"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar alumno?')" title="Eliminar"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filtrar() {
    const q = document.getElementById('buscar').value.toLowerCase();
    const curso = document.getElementById('filtroCurso').value.toLowerCase();
    const estado = document.getElementById('filtroEstado').value;
    document.querySelectorAll('#tablaAlumnos tbody tr').forEach(tr => {
        const nombre = tr.dataset.nombre;
        const dni    = tr.dataset.dni;
        const c      = tr.dataset.curso.toLowerCase();
        const a      = tr.dataset.activo;
        const ok = (!q || nombre.includes(q) || dni.includes(q))
                && (!curso || c.includes(curso))
                && (!estado || a === estado);
        tr.style.display = ok ? '' : 'none';
    });
}
document.getElementById('buscar').addEventListener('input', filtrar);
document.getElementById('filtroCurso').addEventListener('change', filtrar);
document.getElementById('filtroEstado').addEventListener('change', filtrar);
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
