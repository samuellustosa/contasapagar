<?php 
require_once 'config.php';
include 'header.php'; 

// 1. Buscar todos os membros
$membros = $pdo->query("SELECT * FROM family_members")->fetchAll();

$nomes_grafico = [];
$valores_grafico = [];
$cores_grafico = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];

// 2. Cálculo da divisão (Lógica vinda do seu banco de dados)
$dados_relatorio = [];
$total_geral = 0;

foreach ($membros as $index => $m) {
    // Busca a fatia de cada conta que pertence a este membro
    $sql = "SELECT d.amount, (SELECT COUNT(*) FROM debt_members dm2 WHERE dm2.debt_id = d.id) as total_participantes 
            FROM debts d 
            JOIN debt_members dm ON d.id = dm.debt_id 
            WHERE dm.member_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$m['id']]);
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
        
        // Preparar dados para o JavaScript do gráfico
        $nomes_grafico[] = $m['name'];
        $valores_grafico[] = $soma_pessoa;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>📊 Relatórios Detalhados</h2>
    <span class="badge bg-primary fs-5">Total Geral: R$ <?= number_format($total_geral, 2, ',', '.') ?></span>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white"><strong>Distribuição de Gastos</strong></div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="width: 100%; max-width: 400px;">
                    <canvas id="meuGrafico"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white"><strong>Resumo por Pessoa</strong></div>
            <div class="card-body">
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
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('meuGrafico').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut', // Gráfico tipo Rosca (mais moderno que pizza)
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
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

<?php include 'footer.php'; ?>