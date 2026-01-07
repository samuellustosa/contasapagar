<?php 
include 'header.php'; 

$base = ""; 
?>

<div class="card shadow-sm p-4 mx-auto" style="max-width: 500px; border-radius: 15px;">
    <h3 class="fw-bold mb-4">Nova Conta</h3>
    
    <form method="POST" action="<?= $base ?>/nova-conta">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        
        <div class="mb-3">
            <label class="form-label small fw-bold">Nome da Conta</label>
            <input type="text" name="name" class="form-control" placeholder="Ex: Aluguel, Luz..." required>
        </div>
        
        <div class="mb-3">
            <label class="form-label small fw-bold">Valor R$</label>
            <input type="number" step="0.01" name="amount" class="form-control" placeholder="0,00" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label small fw-bold">Data de Vencimento</label>
            <input type="date" name="due_date" class="form-control" required>
        </div>
        
        <label class="small fw-bold mb-2">Dividir com:</label>
        <div class="mb-4 p-3 border rounded bg-light" style="max-height: 200px; overflow-y: auto;">
            <?php if(empty($membros)): ?>
                <p class="text-muted small mb-0">Nenhum membro cadastrado.</p>
            <?php else: ?>
                <?php foreach($membros as $m): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="debtors[]" value="<?= $m['id'] ?>" id="membro_<?= $m['id'] ?>">
                        <label class="form-check-label" for="membro_<?= $m['id'] ?>">
                            <?= htmlspecialchars($m['emoji']) ?> <?= htmlspecialchars($m['name']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">Salvar Conta</button>
        <div class="text-center mt-3">
            <a href="<?= $base ?>/home" class="text-decoration-none small text-muted">Cancelar e voltar</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>