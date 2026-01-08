<?php 
include 'header.php'; 
// Define a base para o localhost
$base = ""; 
?>

<div class="container mt-3">
    <div class="row g-3"> 
        <div class="col-12 col-md-4">
            <div class="card p-3 p-md-4 shadow-sm border-0 h-100">
                <h5 class="mb-3">Novo Membro</h5>
                <form method="POST" action="<?= $base ?>/membros">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
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
                        </select>
                    </div>
                    <button class="btn btn-primary w-100 mt-2 py-2 shadow-sm">Adicionar</button>
                </form>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0">Família</h5>
                </div>
                <div class="card-body p-0"> 
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Emoji</th>
                                    <th>Nome</th>
                                    <th class="text-end pe-3">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($membros as $m): ?>
                                <tr>
                                    <td class="ps-3" style="font-size: 1.5rem;"><?= $m['emoji'] ?></td>
                                    <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
                                    <td class="text-end pe-3">
                                        <a href="<?= $base ?>/excluir-membro?id=<?= $m['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-pill" 
                                           >Excluir</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>