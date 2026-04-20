<?php
$editar    = isset($curso);
$pageTitle = $editar ? 'Editar Curso' : 'Nuevo Curso';
require __DIR__ . '/../layout/header.php';
?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/cursos" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><?= $pageTitle ?></h5>
</div>

<?php if ($error ?? null): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card border-0 shadow-sm" style="max-width:560px">
    <div class="card-body">
        <form method="POST" id="formCurso">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nivel educativo *</label>
                <select name="nivel" id="nivelSelect" class="form-select" required onchange="actualizarOpciones()">
                    <option value="">Seleccionar...</option>
                    <option value="basica_superior" <?= ($curso['nivel'] ?? '') === 'basica_superior'        ? 'selected' : '' ?>>Educación Básica Superior</option>
                    <option value="bachillerato_informatica" <?= ($curso['nivel'] ?? '') === 'bachillerato_informatica' ? 'selected' : '' ?>>Bachillerato — Informática</option>
                    <option value="bachillerato_diseno"      <?= ($curso['nivel'] ?? '') === 'bachillerato_diseno'      ? 'selected' : '' ?>>Bachillerato — Diseño</option>
                    <option value="bachillerato_sociales"    <?= ($curso['nivel'] ?? '') === 'bachillerato_sociales'    ? 'selected' : '' ?>>Bachillerato — Sociales</option>
                </select>
            </div>

            <div class="mb-3" id="gradoGroup" style="display:none">
                <label class="form-label fw-semibold">Año *</label>
                <select name="grado" id="gradoSelect" class="form-select" onchange="generarNombre()">
                    <option value="">Seleccionar año...</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre generado</label>
                <input type="text" id="nombrePreview" class="form-control bg-light" readonly
                       value="<?= htmlspecialchars($curso['nombre'] ?? '') ?>" placeholder="Se genera automáticamente">
                <input type="hidden" name="nombre" id="nombreHidden" value="<?= htmlspecialchars($curso['nombre'] ?? '') ?>">
            </div>

            <div class="mb-3" id="turnoGroup">
                <label class="form-label fw-semibold">Turno *</label>
                <select name="turno" id="turnoSelect" class="form-select" required>
                    <option value="manana" <?= ($curso['turno'] ?? '') === 'manana' ? 'selected' : '' ?>>Mañana</option>
                    <option value="tarde"  id="optTarde" <?= ($curso['turno'] ?? '') === 'tarde' ? 'selected' : '' ?>>Tarde</option>
                </select>
                <div class="form-text text-warning" id="turnoInfo" style="display:none">
                    <i class="bi bi-info-circle me-1"></i>Educación Básica Superior solo tiene turno mañana.
                </div>
            </div>

            <div class="mb-3" id="especialidadGroup" style="display:none">
                <label class="form-label fw-semibold">Especialidad *</label>
                <select name="especialidad" id="especialidadSelect" class="form-select">
                    <option value="ninguna">Sin especialidad</option>
                    <option value="informatica"  <?= ($curso['especialidad'] ?? '') === 'informatica'  ? 'selected' : '' ?>>Informática</option>
                    <option value="diseno"       <?= ($curso['especialidad'] ?? '') === 'diseno'       ? 'selected' : '' ?>>Diseño</option>
                    <option value="sociales"     <?= ($curso['especialidad'] ?? '') === 'sociales'     ? 'selected' : '' ?>>Ciencias Sociales</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Año lectivo *</label>
                <input type="number" name="anio_lectivo" class="form-control"
                       value="<?= $curso['anio_lectivo'] ?? date('Y') ?>" min="2020" max="2099" required>
            </div>

            <?php if ($editar): ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Estado</label>
                <select name="activo" class="form-select">
                    <option value="1" <?= ($curso['activo'] ?? 1) ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= !($curso['activo'] ?? 1) ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i><?= $editar ? 'Actualizar' : 'Crear curso' ?>
                </button>
                <a href="<?= BASE_URL ?>/cursos" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
const gradosPorNivel = {
    basica_superior:          ['7mo', '8vo', '9no'],
    bachillerato_informatica: ['1ro', '2do', '3ro'],
    bachillerato_diseno:      ['1ro', '2do', '3ro'],
    bachillerato_sociales:    ['1ro', '2do', '3ro']
};

const sufijoPorNivel = {
    basica_superior:          '',
    bachillerato_informatica: ' Informática',
    bachillerato_diseno:      ' Diseño',
    bachillerato_sociales:    ' Sociales'
};

function actualizarOpciones() {
    const nivel    = document.getElementById('nivelSelect').value;
    const turnoSel = document.getElementById('turnoSelect');
    const tardOpt  = document.getElementById('optTarde');
    const turnoInfo= document.getElementById('turnoInfo');
    const gradoGrp = document.getElementById('gradoGroup');
    const gradoSel = document.getElementById('gradoSelect');
    const espGroup = document.getElementById('especialidadGroup');

    // Llenar años según nivel
    gradoSel.innerHTML = '<option value="">Seleccionar año...</option>';
    if (nivel && gradosPorNivel[nivel]) {
        gradoGrp.style.display = '';
        gradosPorNivel[nivel].forEach(g => {
            const opt = document.createElement('option');
            opt.value = g;
            opt.textContent = g;
            gradoSel.appendChild(opt);
        });
    } else {
        gradoGrp.style.display = 'none';
    }

    // Básica superior solo mañana
    if (nivel === 'basica_superior') {
        turnoSel.value   = 'manana';
        tardOpt.disabled = true;
        turnoInfo.style.display = '';
    } else {
        tardOpt.disabled = false;
        turnoInfo.style.display = 'none';
    }

    // Ocultar bloque especialidad separado (ya está en el nivel)
    if (espGroup) espGroup.style.display = 'none';

    generarNombre();
}

function generarNombre() {
    const nivel  = document.getElementById('nivelSelect').value;
    const grado  = document.getElementById('gradoSelect').value;
    if (!nivel || !grado) return;
    const sufijo = sufijoPorNivel[nivel] || '';
    const nombre = grado + sufijo;
    document.getElementById('nombreHidden').value  = nombre;
    document.getElementById('nombrePreview').value = nombre;
}

document.addEventListener('DOMContentLoaded', () => {
    actualizarOpciones();
    <?php if ($editar && !empty($curso['nombre'])): ?>
    // Preseleccionar grado al editar
    setTimeout(() => {
        const gradoSel = document.getElementById('gradoSelect');
        const nombre   = '<?= addslashes($curso['nombre']) ?>';
        Array.from(gradoSel.options).forEach(o => {
            if (o.value && nombre.startsWith(o.value)) gradoSel.value = o.value;
        });
        generarNombre();
    }, 50);
    <?php endif; ?>
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
