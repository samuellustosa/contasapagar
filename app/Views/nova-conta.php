<?php 
include 'header.php'; 
$base = ""; 
?>

<div class="container mt-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary">Cadastrar Nova Conta</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="<?= $base ?>/nova-conta">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Descrição da Conta</label>
                        <input type="text" name="name" class="form-control" placeholder="Ex: Aluguel, Internet, Compra Celular" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label small fw-bold text-secondary">Valor Total</label>
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="0,00" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label small fw-bold text-secondary">Vencimento</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-secondary">Tipo de Cobrança</label>
                        <select name="tipo" id="tipo_conta" class="form-select" onchange="toggleParcelas()">
                            <option value="unica">Conta Única</option>
                            <option value="fixa">Conta Fixa (Mensal)</option>
                            <option value="parcelada">Compra Parcelada</option>
                        </select>
                        <small class="text-muted" id="tipo_help">A conta única vence apenas na data selecionada.</small>
                    </div>

                    <div class="col-md-6 mb-3 d-none" id="campo_parcelas">
                        <label class="form-label small fw-bold text-secondary">Número de Parcelas</label>
                        <input type="number" name="total_parcelas" class="form-control" value="2" min="2" max="48">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Quem deve pagar? (Selecione um ou mais)</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach($membros as $m): ?>
                            <input type="checkbox" class="btn-check" name="debtors[]" value="<?= $m['id'] ?>" id="member_<?= $m['id'] ?>" autocomplete="off">
                            <label class="btn btn-outline-primary btn-sm rounded-pill" for="member_<?= $m['id'] ?>">
                                <?= $m['emoji'] ?> <?= htmlspecialchars($m['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        Gravar Conta
                    </button>
                    <a href="<?= $base ?>/home" class="btn btn-light w-100 mt-2 text-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleParcelas() {
    const tipo = document.getElementById('tipo_conta').value;
    const campo = document.getElementById('campo_parcelas');
    const help = document.getElementById('tipo_help');

    if (tipo === 'parcelada') {
        campo.classList.remove('d-none');
        help.innerText = "O sistema dividirá o valor e criará os próximos meses com (1/x, 2/x...).";
    } else if (tipo === 'fixa') {
        campo.classList.add('d-none');
        help.innerText = "Esta conta será repetida automaticamente para os próximos 12 meses.";
    } else {
        campo.classList.add('d-none');
        help.innerText = "A conta única vence apenas na data selecionada.";
    }
}
</script>

<?php include 'footer.php'; ?>