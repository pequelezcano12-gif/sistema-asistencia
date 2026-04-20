<?php $pageTitle = 'Justificativos'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-check me-2 text-primary"></i>Justificativos de Ausencia</h5>
    <div class="d-flex gap-2">
        <span class="badge bg-warning text-dark fs-6"><?= count(array_filter($justificativos, fn($j) => $j['estado'] === 'pendiente')) ?> pendientes</span>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Alumno</th><th>Fecha ausencia</th><th>Enviado por</th>
                        <th>Motivo</th><th>Archivo</th><th>Estado</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($justificativos as $j):
                    $badgeColor = ['pendiente'=>'warning text-dark','aprobado'=>'success','rechazado'=>'danger'][$j['estado']] ?? 'secondary';
                ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($j['apellido'] . ', ' . $j['nombre']) ?></td>
                    <td><?= date('d/m/Y', strtotime($j['fecha_ausencia'])) ?></td>
                    <td><?= htmlspecialchars($j['padre_apellido'] . ' ' . $j['padre_nombre']) ?></td>
                    <td style="max-width:200px">
                        <span class="text-truncate d-block" title="<?= htmlspecialchars($j['motivo']) ?>">
                            <?= htmlspecialchars(substr($j['motivo'], 0, 60)) ?><?= strlen($j['motivo']) > 60 ? '...' : '' ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($j['archivo']): ?>
                            <a href="<?= BASE_URL ?>/uploads/justificativos/<?= $j['archivo'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-paperclip"></i>
                            </a>
                        <?php else: ?>
                            <span class="text-muted small">Sin archivo</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge bg-<?= $badgeColor ?>"><?= ucfirst($j['estado']) ?></span></td>
                    <td>
                        <?php if ($j['estado'] === 'pendiente'): ?>
                        <a href="<?= BASE_URL ?>/justificativos/<?= $j['id'] ?>/aprobar"
                           class="btn btn-sm btn-success"
                           onclick="return confirm('¿Aprobar este justificativo?')">
                            <i class="bi bi-check-lg"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/justificativos/<?= $j['id'] ?>/rechazar"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Rechazar este justificativo?')">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($justificativos)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No hay justificativos registrados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
