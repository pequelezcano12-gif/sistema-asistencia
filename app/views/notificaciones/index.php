<?php $pageTitle = 'Notificaciones'; require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-bell me-2 text-primary"></i>Notificaciones</h5>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($notificaciones)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-bell-slash fs-1 opacity-25"></i>
                <p class="mt-2">No tenés notificaciones.</p>
            </div>
        <?php else: ?>
        <div class="list-group list-group-flush">
            <?php
            $iconos = [
                'nota_ausencia'          => ['bi-exclamation-triangle-fill','warning'],
                'justificativo'          => ['bi-file-earmark-text-fill','primary'],
                'justificativo_aprobado' => ['bi-check-circle-fill','success'],
                'justificativo_rechazado'=> ['bi-x-circle-fill','danger'],
            ];
            foreach ($notificaciones as $n):
                [$icon, $color] = $iconos[$n['tipo']] ?? ['bi-bell-fill','secondary'];
            ?>
            <div class="list-group-item list-group-item-action d-flex gap-3 py-3">
                <div class="rounded-circle bg-<?= $color ?> bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px">
                    <i class="bi <?= $icon ?> text-<?= $color ?>"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold"><?= htmlspecialchars($n['titulo']) ?></div>
                    <?php if ($n['mensaje']): ?>
                    <div class="text-muted small"><?= htmlspecialchars($n['mensaje']) ?></div>
                    <?php endif; ?>
                    <div class="text-muted" style="font-size:.75rem">
                        <?= date('d/m/Y H:i', strtotime($n['created_at'])) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
