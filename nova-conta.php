<?php 
require_once 'config.php';
$membros = $pdo->query("SELECT * FROM family_members")->fetchAll();

$erro = ""; // Variável para armazenar mensagens de erro

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Limpeza e Captura dos dados (Segurança contra SQL Injection)
    $nome_conta = strip_tags(trim($_POST['name']));
    $valor = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $data_vencimento = $_POST['due_date'];

    // 2. Validações de Dados
    if (empty($nome_conta)) {
        $erro = "O nome da conta não pode estar vazio.";
    } elseif ($valor <= 0) {
        $erro = "O valor da conta deve ser maior que zero.";
    } elseif (empty($data_vencimento)) {
        $erro = "A data de vencimento é obrigatória.";
    } elseif (empty($_POST['debtors'])) { 
        $erro = "Você deve selecionar pelo menos uma pessoa para dividir a conta.";
    } else {
        // Se passar em todas, executa o código de salvar...
        // Se passar nas validações, executa o INSERT
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
        
        header("Location: ./?mes=$mes&ano=$ano&msg=sucesso");
        exit;
    }
}
include 'header.php'; 
?>

<div class="card shadow-sm p-4 mx-auto" style="max-width: 500px;">
    <h3>Nova Conta</h3>
    
    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nome da Conta</label>
            <input type="text" name="name" class="form-control" placeholder="Ex: Aluguer" value="<?= $_POST['name'] ?? '' ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Valor R$</label>
            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" value="<?= $_POST['amount'] ?? '' ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Data de Vencimento</label>
            <input type="date" name="due_date" class="form-control" value="<?= $_POST['due_date'] ?? '' ?>" required>
        </div>

        <label>Dividir com:</label>
        <div class="mb-3">
            <?php foreach($membros as $m): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="debtors[]" value="<?= $m['id'] ?>" 
                        <?= (isset($_POST['debtors']) && in_array($m['id'], $_POST['debtors'])) ? 'checked' : '' ?>>
                    <label class="form-check-label"><?= $m['emoji'] ?> <?= $m['name'] ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">Salvar</button>
    </form>
</div>

<?php include 'footer.php'; ?>