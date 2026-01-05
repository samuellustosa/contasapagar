<?php 
require_once 'config.php';
include 'header.php'; 

// 1. Pegar o mês e ano da URL ou usar o atual
$mes_selecionado = $_GET['mes'] ?? date('m');
$ano_selecionado = $_GET['ano'] ?? date('Y');

// --- LÓGICA DAS SETAS ---
// Criar um objeto de data com o mês atual para calcular o próximo e o anterior
$data_atual = new DateTime("$ano_selecionado-$mes_selecionado-01");

$anterior = clone $data_atual;
$anterior->modify('-1 month');
$mes_ant = $anterior->format('m');
$ano_ant = $anterior->format('Y');

$proximo = clone $data_atual;
$proximo->modify('+1 month');
$mes_prox = $proximo->format('m');
$ano_prox = $proximo->format('Y');

$meses_nome = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
    '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
    '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
];
// ------------------------

// 2. Totais filtrados
$stmt_total = $pdo->prepare("SELECT SUM(amount) FROM debts WHERE MONTH(due_date) = ? AND YEAR(due_date) = ?");
$stmt_total->execute([$mes_selecionado, $ano_selecionado]);
$total = $stmt_total->fetchColumn() ?: 0;

$stmt_pago = $pdo->prepare("SELECT SUM(amount) FROM debts WHERE is_paid = 1 AND MONTH(due_date) = ? AND YEAR(due_date) = ?");
$stmt_pago->execute([$mes_selecionado, $ano_selecionado]);
$pago = $stmt_pago->fetchColumn() ?: 0;

$pendente = $total - $pago;

// 3. Buscar contas
$stmt_contas = $pdo->prepare("SELECT * FROM debts WHERE MONTH(due_date) = ? AND YEAR(due_date) = ? ORDER BY due_date ASC");
$stmt_contas->execute([$mes_selecionado, $ano_selecionado]);
$contas = $stmt_contas->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center bg-white p-3 shadow-sm rounded mb-4 border">
    <a href="index.php?mes=<?= $mes_ant ?>&ano=<?= $ano_ant ?>" class="btn btn-outline-primary rounded-pill">
        &larr; <span class="d-none d-md-inline">Anterior</span>
    </a>
    
    <div class="text-center">
        <h5 class="mb-0 fw-bold"><?= $meses_nome[$mes_selecionado] ?></h5>
        <small class="text-muted"><?= $ano_selecionado ?></small>
    </div>

    <a href="index.php?mes=<?= $mes_prox ?>&ano=<?= $ano_prox ?>" class="btn btn-outline-primary rounded-pill">
        <span class="d-none d-md-inline">Próximo</span> &rarr;
    </a>
</div>

<div class="row text-center mb-4 g-2"> 
    <div class="col-4">
        <div class="card p-2 p-md-3 shadow-sm border-0 h-100">
            <h6 class="text-muted mb-1" style="font-size: 0.75rem;">Total</h6>
            <h5 class="text-primary mb-0" style="font-size: 1rem;">R$ <?= number_format($total, 2, ',', '.') ?></h5>
        </div>
    </div>
    <div class="col-4">
        <div class="card p-2 p-md-3 shadow-sm border-0 h-100">
            <h6 class="text-muted mb-1" style="font-size: 0.75rem;">Pendente</h6>
            <h5 class="text-danger mb-0" style="font-size: 1rem;">R$ <?= number_format($pendente, 2, ',', '.') ?></h5>
        </div>
    </div>
    <div class="col-4">
        <div class="card p-2 p-md-3 shadow-sm border-0 h-100">
            <h6 class="text-muted mb-1" style="font-size: 0.75rem;">Pago</h6>
            <h5 class="text-success mb-0" style="font-size: 1rem;">R$ <?= number_format($pago, 2, ',', '.') ?></h5>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 d-none d-md-block">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Conta</th>
                        <th>Valor</th>
                        <th>Vencimento</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($contas as $c): ?>
                    <tr>
                        <td class="ps-4"><strong><?= $c['name'] ?></strong></td>
                        <td>R$ <?= number_format($c['amount'], 2, ',', '.') ?></td>
                        <td><?= date('d/m/Y', strtotime($c['due_date'])) ?></td>
                        <td>
                            <span class="badge <?= $c['is_paid'] ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <?= $c['is_paid'] ? 'Pago' : 'Pendente' ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="acoes.php?id=<?= $c['id'] ?>&acao=Pagar&mes=<?= $mes_selecionado ?>&ano=<?= $ano_selecionado ?>" class="btn btn-sm btn-light border">
                                    <?= $c['is_paid'] ? 'Desmarcar' : 'Marcar como Pago' ?>
                                </a>
                                <a href="acoes.php?id=<?= $c['id'] ?>&acao=excluir&mes=<?= $mes_selecionado ?>&ano=<?= $ano_selecionado ?>" class="btn btn-sm btn-outline-danger">
                                    Excluir
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-md-none">
    <?php foreach($contas as $c): ?>
    <div class="card mb-2 shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="mb-0"><?= $c['name'] ?></h6>
                    <small class="text-muted"><?= date('d/m/Y', strtotime($c['due_date'])) ?></small>
                </div>
                <h6 class="text-primary mb-0">R$ <?= number_format($c['amount'], 2, ',', '.') ?></h6>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <?= $c['is_paid'] ? '<span class="badge bg-success">Pago</span>' : '<span class="badge bg-warning text-dark">Pendente</span>' ?>
                <div class="btn-group gap-2">
                    <a href="acoes.php?id=<?= $c['id'] ?>&acao=Pagar&mes=<?= $mes_selecionado ?>&ano=<?= $ano_selecionado ?>" class="btn btn-sm btn-light border">
                        <?= $c['is_paid'] ? 'Desmarcar' : 'Pagar' ?>
                    </a>
                    <a href="acoes.php?id=<?= $c['id'] ?>&acao=excluir&mes=<?= $mes_selecionado ?>&ano=<?= $ano_selecionado ?>" class="btn btn-sm btn-outline-danger">
                        Excluir
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include 'footer.php'; ?>