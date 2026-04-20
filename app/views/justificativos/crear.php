<?php $pageTitle = 'Enviar Justificativo'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Enviar Justificativo de Ausencia</h5>
</div>

<?php if ($error ?? null): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="max-width:580px">
    <div class="card-body">
        <div class="alert alert-info small">
            <i class="bi bi-info-circle me-1"></i>
            El justificativo será enviado al director para su revisión. Si es aprobado, la ausencia quedará marcada como justificada.
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-semibold">Alumno *</label>
                <select name="alumno_id" class="form-select" required>
                    <option value="">Seleccionar alumno...</option>
                    <?php foreach ($misAlumnos as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= ($alumno_id ?? '') == $a['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['apellido'] . ', ' . $a['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Fecha de ausencia *</label>
                <input type="date" name="fecha_ausencia" class="form-control"
                       value="<?= $_POST['fecha_ausencia'] ?? date('Y-m-d') ?>" required
                       max="<?= date('Y-m-d') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Motivo *</label>
                <textarea name="motivo" class="form-control" rows="4"
                          placeholder="Describí el motivo de la ausencia..." required
                          maxlength="500"><?= htmlspecialchars($_POST['motivo'] ?? '') ?></textarea>
                <div class="form-text">Máximo 500 caracteres.</div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Documento adjunto (opcional)</label>
                <input type="file" name="archivo" class="form-control"
                       accept=".pdf,.jpg,.jpeg,.png">
                <div class="form-text">Podés adjuntar un certificado médico u otro documento (PDF o imagen).</div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>Enviar justificativo
                </button>
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
