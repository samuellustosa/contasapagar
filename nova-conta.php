<?php 
require_once 'config.php';
$membros = $pdo->query("SELECT * FROM family_members")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_conta = strip_tags(trim($_POST['name']));
    $valor = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $data_vencimento = $_POST['due_date'];

    $stmt = $pdo->prepare("INSERT INTO debts (name, amount, due_date) VALUES (:name, :amount, :due_date)");
    $stmt->bindParam(':name', $nome_conta);
    $stmt->bindParam(':amount', $valor);
    $stmt->bindParam(':due_date', $data_vencimento);
    $stmt->execute();
    
    $debt_id = $pdo->lastInsertId();

    if (!empty($_POST['debtors'])) {
        foreach ($_POST['debtors'] as $mid) {
            $stmt_membro = $pdo->prepare("INSERT INTO debt_members (debt_id, member_id) VALUES (:debt_id, :member_id)");
            $stmt_membro->bindParam(':debt_id', $debt_id);
            $stmt_membro->bindParam(':member_id', $mid);
            $stmt_membro->execute();
        }
    }

    $mes = date('m', strtotime($data_vencimento));
    $ano = date('Y', strtotime($data_vencimento));
    header("Location: ./?mes=$mes&ano=$ano");
    exit;
}
include 'header.php'; 
?>

<div class="card shadow-sm p-4 mx-auto" style="max-width: 500px;">
    <h3>Nova Conta</h3>
    <form method="POST">
        <input type="text" name="name" class="form-control mb-3" placeholder="Nome da Conta" required>
        <input type="number" step="0.01" name="amount" class="form-control mb-3" placeholder="Valor R$" required>
        <input type="date" name="due_date" class="form-control mb-3" required>
        <label>Dividir com:</label>
        <div class="mb-3">
            <?php foreach($membros as $m): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="debtors[]" value="<?= $m['id'] ?>">
                    <label class="form-check-label"><?= $m['emoji'] ?> <?= $m['name'] ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary w-100">Salvar</button>
    </form>
</div>

<?php include 'footer.php'; ?>