<?php 
require_once 'config.php';

// Adicionar novo membro
if (isset($_POST['add_member'])) {
    $stmt = $pdo->prepare("INSERT INTO family_members (name, emoji) VALUES (?, ?)");
    $stmt->execute([$_POST['name'], $_POST['emoji']]);
}

$membros = $pdo->query("SELECT * FROM family_members ORDER BY name ASC")->fetchAll();
include 'header.php'; 
?>

<div class="row g-4"> <div class="col-md-4">
        <div class="card p-4 shadow-sm border-0 h-100">
            <h5 class="mb-3">Novo Membro</h5>
            <form method="POST">
                <input type="hidden" name="add_member" value="1">
                <div class="mb-3">
                    <label class="form-label text-muted small">Nome</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: Samuel" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Emoji</label>
                    <select name="emoji" class="form-select">
                        <option value="👨">👨</option>
                        <option value="👩">👩</option>
                        <option value="🧑">🧑</option>
                        <option value="👦">👦</option>
                        <option value="👧">👧</option>
                        <option value="👴">👴</option>
                        <option value="👵">👵</option>
                    </select>
                </div>
                <button class="btn btn-primary w-100 mt-2 py-2">Adicionar Membro</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Família Cadastrada</h5>
            </div>
            <div class="card-body p-0"> <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Emoji</th>
                                <th>Nome</th>
                                <th class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($membros as $m): ?>
                            <tr>
                                <td class="ps-4" style="font-size: 1.5rem;"><?= $m['emoji'] ?></td>
                                <td><strong><?= $m['name'] ?></strong></td>
                                <td class="text-end pe-4">
                                    <a href="acoes.php?id=<?= $m['id'] ?>&acao=excluir_membro" 
                                       class="btn btn-sm btn-outline-danger px-3 rounded-pill" 
                                       onclick="return confirm('Tem certeza? Isso removerá o membro das divisões de contas.')">
                                       Excluir
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($membros)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Nenhum membro cadastrado.</td>
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