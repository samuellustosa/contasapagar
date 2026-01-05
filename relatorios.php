<?php 
require_once 'config.php';
include 'header.php'; 

// 1. Pegar o mês e ano da URL ou usar o atual
$mes_selecionado = $_GET['mes'] ?? date('m');
$ano_selecionado = $_GET['ano'] ?? date('Y');

// --- LÓGICA DAS SETAS (Igual ao index.php) ---
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
// ----------------------------------------------

// 2. Buscar membros e calcular divisões filtradas por mês/ano
$membros = $pdo->query("SELECT * FROM family_members")->fetchAll();
$dados_relatorio = [];
$total_geral = 0;
$nomes_grafico = [];
$valores_grafico = [];
$cores_grafico = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];

foreach ($membros as $index => $m) {
    $sql = "SELECT d.amount, (SELECT COUNT(*) FROM debt_members dm2 WHERE dm2.debt_id = d.id) as total_participantes 
            FROM debts d 
            JOIN debt_members dm ON d.id = dm.debt_id 
            WHERE dm.member_id = ? AND MONTH(d.due_date) = ? AND YEAR(d.due_date) = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$m['id'], $mes_selecionado, $ano_selecionado]);
    $contas = $stmt->fetchAll();

    $soma_pessoa = 0;
    foreach($contas as $c) {
        $soma_pessoa += ($c['amount'] / $c['total_participantes']);
    }

    if ($soma_pessoa > 0) {
        $dados_relatorio[] = [
            'nome' => $m['name'],
            'emoji' => $m['emoji'],
            'total' => $soma_pessoa,
            'cor' => $cores_grafico[$index % count($cores_grafico)]
        ];
        $total_geral += $soma_pessoa;
        $nomes_grafico[] = $m['name'];
        $valores_grafico[] = $soma_pessoa;
    }
}
?>

<div id="skeleton-loader" class="d-none">
    <div class="skeleton mb-4" style="height: 70px; border-radius: 10px; width: 100%;"></div>

    <div class="d-flex justify-content-between mb-4">
        <div class="skeleton" style="height: 35px; width: 40%;"></div>
        <div class="skeleton" style="height: 35px; width: 20%;"></div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card p-4 shadow-sm border-0">
                <div class="skeleton mx-auto" style="height: 250px; width: 250px; border-radius: 50%;"></div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card p-3 shadow-sm border-0">
                <div class="skeleton mb-3" style="height: 20px; width: 50%;"></div>
                <div class="skeleton mb-4" style="height: 40px; border-radius: 5px;"></div>
                <div class="skeleton mb-4" style="height: 40px; border-radius: 5px;"></div>
                <div class="skeleton mb-4" style="height: 40px; border-radius: 5px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center bg-white p-3 shadow-sm rounded mb-4 border mt-3">
    <a href="relatorios.php?mes=<?= $mes_ant ?>&ano=<?= $ano_ant ?>" class="btn btn-outline-primary rounded-pill">
        &larr; <span class="d-none d-md-inline">Anterior</span>
    </a>
    
    <div class="text-center">
        <h5 class="mb-0 fw-bold"><?= $meses_nome[$mes_selecionado] ?></h5>
        <small class="text-muted"><?= $ano_selecionado ?></small>
    </div>

    <a href="relatorios.php?mes=<?= $mes_prox ?>&ano=<?= $ano_prox ?>" class="btn btn-outline-primary rounded-pill">
        <span class="d-none d-md-inline">Próximo</span> &rarr;
    </a>
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <h2 class="mb-0 mx-auto">Relatórios Detalhados</h2>
    <span class="badge bg-primary fs-6 p-3 p-md-2 shadow-sm">
        Total do Mês: R$ <?= number_format($total_geral, 2, ',', '.') ?>
    </span>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white"><strong>Distribuição de Gastos</strong></div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <?php if (empty($dados_relatorio)): ?>
                    <p class="text-muted">Sem dados para este período.</p>
                <?php else: ?>
                    <div style="width: 100%; max-width: 400px;">
                        <canvas id="meuGrafico"></canvas>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white"><strong>Resumo por Pessoa</strong></div>
            <div class="card-body">
                <?php if (empty($dados_relatorio)): ?>
                    <p class="text-center text-muted py-5">Nenhum gasto neste mês.</p>
                <?php else: ?>
                    <?php foreach ($dados_relatorio as $item): 
                        $porcentagem = ($item['total'] / $total_geral) * 100;
                    ?>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span><?= $item['emoji'] ?> <strong><?= $item['nome'] ?></strong></span>
                                <span class="text-muted">R$ <?= number_format($item['total'], 2, ',', '.') ?> (<?= round($porcentagem) ?>%)</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: <?= $porcentagem ?>%; background-color: <?= $item['cor'] ?>;">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('meuGrafico');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($nomes_grafico) ?>,
                datasets: [{
                    data: <?= json_encode($valores_grafico) ?>,
                    backgroundColor: <?= json_encode(array_slice($cores_grafico, 0, count($nomes_grafico))) ?>,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
</script>

<?php include 'footer.php'; ?>