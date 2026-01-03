<?php 
require_once 'config.php';
include 'header.php'; 

// Totais para os cards
$total = $pdo->query("SELECT SUM(amount) FROM debts")->fetchColumn() ?: 0;
$pago = $pdo->query("SELECT SUM(amount) FROM debts WHERE is_paid = 1")->fetchColumn() ?: 0;
$pendente = $total - $pago;

$contas = $pdo->query("SELECT * FROM debts ORDER BY due_date ASC")->fetchAll();
?>


<div class="row text-center mb-4">
    <div class="col-md-4"><div class="card p-3 shadow-sm border-0"><h6>Total</h6><h3 class="text-primary">R$ <?= number_format($total, 2, ',', '.') ?></h3></div></div>
    <div class="col-md-4"><div class="card p-3 shadow-sm border-0"><h6>Pendente</h6><h3 class="text-danger">R$ <?= number_format($pendente, 2, ',', '.') ?></h3></div></div>
    <div class="col-md-4"><div class="card p-3 shadow-sm border-0"><h6>Pago</h6><h3 class="text-success">R$ <?= number_format($pago, 2, ',', '.') ?></h3></div></div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Conta</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($contas as $c): ?>
                <tr>
                    <td><strong><?= $c['name'] ?></strong></td>
                    <td>R$ <?= number_format($c['amount'], 2, ',', '.') ?></td>
                    <td><?= date('d/m/Y', strtotime($c['due_date'])) ?></td>
                    <td><?= $c['is_paid'] ? '<span class="badge bg-success">Pago</span>' : '<span class="badge bg-warning text-dark">Pendente</span>' ?></td>
                    <td>
                        <a href="acoes.php?id=<?= $c['id'] ?>&acao=Pagar" class="btn btn-sm btn-light">Pagar</a>
                        <a href="acoes.php?id=<?= $c['id'] ?>&acao=excluir" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir?')">🗑️</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>