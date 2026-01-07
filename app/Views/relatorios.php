<?php 
include 'header.php'; 
$base = "/contasapagar/public"; 
?>

<div class="container mt-3">
    <div class="d-flex justify-content-between align-items-center bg-white p-3 shadow-sm rounded mb-4 border">
        <a href="<?= $base ?>/relatorios?mes=<?= $mes_ant ?>&ano=<?= $ano_ant ?>" class="btn btn-outline-primary rounded-pill">
            &larr; <span class="d-none d-md-inline">Anterior</span>
        </a>
        
        <div class="text-center">
            <h5 class="mb-0 fw-bold"><?= $meses_nome[$mes_selecionado] ?></h5>
            <small class="text-muted"><?= $ano_selecionado ?></small>
        </div>

        <a href="<?= $base ?>/relatorios?mes=<?= $mes_prox ?>&ano=<?= $ano_prox ?>" class="btn btn-outline-primary rounded-pill">
            <span class="d-none d-md-inline">Próximo</span> &rarr;
        </a>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white"><strong>Distribuição de Gastos</strong></div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <?php if (empty($dados_relatorio)): ?>
                        <p class="text-muted">Sem dados para este período.</p>
                    <?php else: ?>
                        <canvas id="meuGrafico"></canvas>
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
                            $total_geral = array_sum(array_column($dados_relatorio, 'total_pessoa'));
                            $porcentagem = ($total_geral > 0) ? ($item['total_pessoa'] / $total_geral) * 100 : 0;
                        ?>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span><?= $item['emoji'] ?> <strong><?= $item['name'] ?></strong></span>
                                    <span class="text-muted">R$ <?= number_format($item['total_pessoa'], 2, ',', '.') ?> (<?= round($porcentagem) ?>%)</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-primary" style="width: <?= $porcentagem ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('meuGrafico');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($dados_relatorio, 'name')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($dados_relatorio, 'total_pessoa')) ?>,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverOffset: 4
                }]
            }
        });
    }
</script>

<?php include 'footer.php'; ?>