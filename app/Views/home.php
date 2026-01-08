<?php 
include 'header.php'; 
$base = ""; 
?>

<div class="container mt-3">
    <div class="d-flex justify-content-between align-items-center bg-white p-3 shadow-sm rounded mb-4 border">
        <a href="<?= $base ?>/home?mes=<?= $mes_ant ?>&ano=<?= $ano_ant ?>" class="btn btn-outline-primary rounded-pill">
            &larr; <span class="d-none d-md-inline">Anterior</span>
        </a>
        
        <div class="text-center">
            <h5 class="mb-0 fw-bold"><?= htmlspecialchars($meses_nome[$mes_selecionado]) ?></h5>
            <small class="text-muted"><?= $ano_selecionado ?></small>
        </div>

        <a href="<?= $base ?>/home?mes=<?= $mes_prox ?>&ano=<?= $ano_prox ?>" class="btn btn-outline-primary rounded-pill">
            <span class="d-none d-md-inline">Próximo</span> &rarr;
        </a>
    </div>

    <div class="row text-center mb-4 g-2"> 
        <div class="col-4">
            <div class="card p-2 p-md-3 shadow-sm border-0 h-100">
                <h6 class="text-muted mb-1" style="font-size: 0.75rem;">Total</h6>
                <h5 class="text-primary mb-0" style="font-size: 1rem;">R$ <?= number_format($resumo['total'], 2, ',', '.') ?></h5>
            </div>
        </div>
        <div class="col-4">
            <div class="card p-2 p-md-3 shadow-sm border-0 h-100">
                <h6 class="text-muted mb-1" style="font-size: 0.75rem;">Pendente</h6>
                <h5 class="text-danger mb-0" style="font-size: 1rem;">R$ <?= number_format($resumo['pendente'], 2, ',', '.') ?></h5>
            </div>
        </div>
        <div class="col-4">
            <div class="card p-2 p-md-3 shadow-sm border-0 h-100">
                <h6 class="text-muted mb-1" style="font-size: 0.75rem;">Pago</h6>
                <h5 class="text-success mb-0" style="font-size: 1rem;">R$ <?= number_format($resumo['pago'], 2, ',', '.') ?></h5>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'grupo_excluido'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            O grupo de contas foi removido com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Conta</th>
                            <th>Valor</th>
                            <th>Quem deve</th> 
                            <th>Vencimento</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($contas as $c): ?>
                        <tr>
                            <td class="ps-4">
                                <strong><?= htmlspecialchars($c['name']) ?></strong>
                                <?php if($c['tipo'] === 'fixa'): ?> <span class="badge bg-light text-primary border">Fixa</span> <?php endif; ?>
                            </td>
                            <td>R$ <?= number_format($c['amount'], 2, ',', '.') ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars($c['devedores'] ?? 'Ninguém') ?></small></td> 
                            <td><?= date('d/m/Y', strtotime($c['due_date'])) ?></td>
                            <td>
                                <span class="badge <?= $c['is_paid'] ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= $c['is_paid'] ? 'Pago' : 'Pendente' ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= $base ?>/pagar?id=<?= $c['id'] ?>&mes=<?= $mes_selecionado ?>&ano=<?= $ano_selecionado ?>" class="btn btn-sm btn-light border">
                                        <?= $c['is_paid'] ? 'Desmarcar' : 'Pagar' ?>
                                    </a>
                                    <button onclick="confirmarExclusao(<?= $c['id'] ?>, '<?= $c['tipo'] ?>')" class="btn btn-sm btn-outline-danger">
                                        Excluir
                                    </button>
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
                        <h6 class="mb-0"><?= htmlspecialchars($c['name']) ?></h6>
                        <div class="small text-info mb-1"><?= htmlspecialchars($c['devedores'] ?? '') ?></div> 
                        <small class="text-muted"><?= date('d/m/Y', strtotime($c['due_date'])) ?></small>
                    </div>
                    <h6 class="text-primary mb-0">R$ <?= number_format($c['amount'], 2, ',', '.') ?></h6>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <?= $c['is_paid'] ? '<span class="badge bg-success">Pago</span>' : '<span class="badge bg-warning text-dark">Pendente</span>' ?>
                    <div class="btn-group gap-2">
                        <a href="<?= $base ?>/pagar?id=<?= $c['id'] ?>&mes=<?= $mes_selecionado ?>&ano=<?= $ano_selecionado ?>" class="btn btn-sm btn-light border">
                            <?= $c['is_paid'] ? 'Desmarcar' : 'Pagar' ?>
                        </a>
                        <button onclick="confirmarExclusao(<?= $c['id'] ?>, '<?= $c['tipo'] ?>')" class="btn btn-sm btn-outline-danger">
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function confirmarExclusao(id, tipo) {
    let url = "<?= $base ?>/excluir-conta?id=" + id + "&mes=<?= $mes_selecionado ?>&ano=<?= $ano_selecionado ?>";
    
    if (tipo !== 'unica' && tipo !== '') {
     
        if (confirm("Esta conta é do tipo '" + tipo + "'.\n\nClique em OK para excluir TODAS as parcelas deste grupo.\nClique em CANCELAR para excluir apenas este mês.")) {
            url += "&todos=1";
        } else {
            // Se ele cancelar o 'excluir todos', ainda pergunta se quer excluir este item específico
            if (!confirm("Deseja excluir apenas esta conta de <?= htmlspecialchars($meses_nome[$mes_selecionado]) ?>?")) return;
        }
    } else {
        if (!confirm("Deseja excluir esta conta?")) return;
    }
    
    window.location.href = url;
}
</script>

<?php include 'footer.php'; ?>