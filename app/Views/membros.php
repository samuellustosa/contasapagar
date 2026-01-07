<?php include 'header.php'; ?>
<div class="row g-3"> 
    <div class="col-12 col-md-4">
        <div class="card p-4 shadow-sm border-0">
            <h5 class="mb-3">Novo Membro</h5>
            <form method="POST" action="/membros">
                <input type="hidden" name="add_member" value="1">
                <div class="mb-3">
                    <label class="form-label text-muted small">Nome</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Emoji</label>
                    <select name="emoji" class="form-select">
                        <option value="👨">👨</option>
                        <option value="👩">👩</option>
                    </select>
                </div>
                <button class="btn btn-primary w-100">Adicionar</button>
            </form>
        </div>
    </div>
    <div class="col-12 col-md-8">
        <div class="card shadow-sm border-0">
            <table class="table align-middle">
                <thead>
                    <tr><th>Emoji</th><th>Nome</th><th class="text-end">Ação</th></tr>
                </thead>
                <tbody>
                    <?php foreach($membros as $m): ?>
                    <tr>
                        <td><?= $m['emoji'] ?></td>
                        <td><strong><?= $m['name'] ?></strong></td>
                        <td class="text-end">
                            <a href="/excluir-membro?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>