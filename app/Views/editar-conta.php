<?php 
include 'header.php'; 
$base = ""; 
?>

<div class="container mt-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary">Editar Conta</h5>
            <small class="text-muted">ID do Lançamento: #<?= $conta['id'] ?></small>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="<?= $base ?>/editar-conta">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="id" value="<?= $conta['id'] ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Descrição</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($conta['name']) ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label small fw-bold text-secondary">Valor</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="<?= $conta['amount'] ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label small fw-bold text-secondary">Vencimento</label>
                        <input type="date" name="due_date" class="form-control" value="<?= $conta['due_date'] ?>" required>
                    </div>
                </div>

                <?php if (!empty($conta['grupo_id'])): ?>
                <div class="mb-4 p-3 bg-light border rounded shadow-sm">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="update_all" value="1" id="updateAllSwitch">
                        <label class="form-check-label fw-bold text-primary" for="updateAllSwitch">
                            Aplicar alteração a TODAS as parcelas deste grupo?
                        </label>
                    </div>
                    <p class="small text-muted mb-0 mt-2">
                        <i class="bi bi-info-circle"></i> 
                        Se ativado, o <strong>Nome</strong>, <strong>Valor</strong> e <strong>Responsáveis</strong> serão atualizados em todos os meses vinculados a esta conta. O dia de vencimento de cada mês será preservado.
                    </p>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Quem deve pagar?</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach($membros as $m): ?>
                            <?php $checked = in_array($m['id'], $conta['debtors']) ? 'checked' : ''; ?>
                            <input type="checkbox" class="btn-check" name="debtors[]" value="<?= $m['id'] ?>" id="member_<?= $m['id'] ?>" <?= $checked ?> autocomplete="off">
                            <label class="btn btn-outline-primary btn-sm rounded-pill" for="member_<?= $m['id'] ?>">
                                <?= $m['emoji'] ?> <?= htmlspecialchars($m['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                        <i class="bi bi-check-lg"></i> Guardar Alterações
                    </button>
                    <a href="<?= $base ?>/home" class="btn btn-light w-100 mt-2 text-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>