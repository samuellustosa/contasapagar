<?php include 'header.php'; ?>

<div class="container mt-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary">Editar Conta</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="/editar-conta">
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
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">Guardar Alterações</button>
                    <a href="/home" class="btn btn-light w-100 mt-2 text-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>