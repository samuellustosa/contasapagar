<?php include 'header.php'; ?>
<div class="card shadow-sm p-4 mx-auto" style="max-width: 500px;">
    <h3>Nova Conta</h3>
    <form method="POST" action="/nova-conta">
        <div class="mb-3">
            <label class="form-label">Nome da Conta</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Valor R$</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Data de Vencimento</label>
            <input type="date" name="due_date" class="form-control" required>
        </div>
        <label>Dividir com:</label>
        <div class="mb-3">
            <?php foreach($membros as $m): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="debtors[]" value="<?= $m['id'] ?>">
                    <label class="form-check-label"><?= $m['emoji'] ?> <?= $m['name'] ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary w-100">Salvar</button>
    </form>
</div>
<?php include 'footer.php'; ?>