<?php $pageTitle = 'Reporte por Alumno'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Reporte por Alumno</h5>
</div>

<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-body">
        <p class="text-muted">Buscá un alumno para ver su reporte de asistencia.</p>
        <form method="GET" action="">
            <div class="input-group">
                <input type="text" id="buscarAlumno" class="form-control" placeholder="Nombre, apellido o DNI...">
                <button class="btn btn-primary" type="button" onclick="buscar()">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        <div id="resultados" class="mt-3"></div>
    </div>
</div>

<script>
function buscar() {
    const q = document.getElementById('buscarAlumno').value;
    if (q.length < 2) return;
    fetch('<?= BASE_URL ?>/alumnos?q=' + encodeURIComponent(q))
        .then(() => {
            // Redirect to alumnos with search
            window.location.href = '<?= BASE_URL ?>/alumnos';
        });
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
