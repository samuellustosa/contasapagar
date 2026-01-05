<?php 
require_once 'config.php';

// Adicionar novo membro com segurança reforçada
if (isset($_POST['add_member'])) {
    // Limpa espaços extras e remove tags HTML por segurança
    $nome = strip_tags(trim($_POST['name']));
    $emoji = strip_tags(trim($_POST['emoji']));

    if (!empty($nome) && !empty($emoji)) {
        // Utiliza Prepared Statements com bindParam para evitar SQL Injection
        $stmt = $pdo->prepare("INSERT INTO family_members (name, emoji) VALUES (:nome, :emoji)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':emoji', $emoji);
        $stmt->execute();
        
        // Redireciona com mensagem de sucesso
        header("Location: membros.php?msg=membro_add");
        exit;
    }
}

$membros = $pdo->query("SELECT * FROM family_members ORDER BY name ASC")->fetchAll();
include 'header.php'; 
?>


<div id="skeleton-loader" class="d-none">
    <div class="row g-3 mt-1"> 
        <div class="col-12 col-md-4">
            <div class="card p-4 shadow-sm border-0">
                <div class="skeleton mb-3" style="height: 25px; width: 60%;"></div>
                <div class="skeleton mb-2" style="height: 15px; width: 30%;"></div>
                <div class="skeleton mb-3" style="height: 45px; border-radius: 8px;"></div>
                <div class="skeleton mb-2" style="height: 15px; width: 30%;"></div>
                <div class="skeleton mb-4" style="height: 45px; border-radius: 8px;"></div>
                <div class="skeleton" style="height: 45px; border-radius: 8px;"></div>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <div class="skeleton" style="height: 25px; width: 30%;"></div>
                </div>
                <div class="p-3">
                    <div class="skeleton mb-3" style="height: 50px; border-radius: 5px;"></div>
                    <div class="skeleton mb-3" style="height: 50px; border-radius: 5px;"></div>
                    <div class="skeleton mb-3" style="height: 50px; border-radius: 5px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3"> 
    
    <div class="col-12 col-md-4">
        <div class="card p-3 p-md-4 shadow-sm border-0 h-100">
            <h5 class="mb-3"> Novo Membro</h5>
            <form method="POST">
                <input type="hidden" name="add_member" value="1">
                <div class="mb-3">
                    <label class="form-label text-muted small">Nome</label>
                    <input type="text" name="name" class="form-control form-control-lg fs-6" placeholder="Ex: Samuel" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Emoji</label>
                    <select name="emoji" class="form-select form-select-lg fs-6">
                        <option value="👨">👨</option>
                        <option value="👩">👩</option>
                        <option value="🧑">🧑</option>
                        <option value="👦">👦</option>
                        <option value="👧">👧</option>
                        <option value="👴">👴</option>
                        <option value="👵">👵</option>
                    </select>
                </div>
                <button class="btn btn-primary w-100 mt-2 py-2 shadow-sm">Adicionar</button>
            </form>
        </div>
    </div>

    <div class="col-12 col-md-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0"> Família</h5>
            </div>
            <div class="card-body p-0"> 
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 ps-md-4">Emoji</th>
                                <th>Nome</th>
                                <th class="text-end pe-3 pe-md-4">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($membros as $m): ?>
                            <tr>
                                <td class="ps-3 ps-md-4" style="font-size: 1.5rem;"><?= $m['emoji'] ?></td>
                                <td class="text-truncate" style="max-width: 120px;">
                                    <strong><?= $m['name'] ?></strong>
                                </td>
                                <td class="text-end pe-3 pe-md-4">
                                    <a href="acoes.php?id=<?= $m['id'] ?>&acao=excluir_membro" 
                                       class="btn btn-sm btn-outline-danger px-3 rounded-pill" 
                                       onclick="return confirm('Excluir membro?')">
                                       <span class="d-none d-md-inline">Excluir</span>
                                       <span class="d-md-none">Excluir</span>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($membros)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Nenhum membro.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>