<?php 
// Define a base para o localhost
$base = ""; 

// Garante que a variável $msg exista para não dar erro de "undefined variable"
$msg = $msg ?? ''; 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-title" content="Contas">
    <meta name="application-name" content="Contas">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Criar Conta - Contas a Pagar</title>
    <style>
        body { background-color: #0d6efd; display: flex; flex-direction: column; min-height: 100vh; justify-content: center; align-items: center; }
        .register-card { width: 90%; max-width: 400px; border-radius: 15px; border: none; }
        .form-control { padding: 12px; font-size: 16px; }
        .btn-success { padding: 12px; font-weight: bold; border-radius: 8px; }
        .footer-text { color: rgba(255, 255, 255, 0.8); font-size: 0.85rem; margin-top: 20px; }
    </style>
</head>
<body class="p-3">

    <div class="card register-card p-4 shadow-lg">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-success">Criar Conta</h3>
            <p class="text-muted small">Registe-se para organizar os seus gastos</p>
        </div>

        <?php if($msg): ?>
            <div class="alert alert-info small py-2 text-center shadow-sm">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $base ?>/registrar">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">E-mail</label>
                <input type="email" name="email" class="form-control" placeholder="exemplo@gmail.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Escolha uma Senha</label>
                <input type="password" name="senha" class="form-control" placeholder="••••••••" required>
            </div>
            <button class="btn btn-success w-100 shadow-sm">Registar</button>
            
            <div class="mt-4 text-center">
                <a href="<?= $base ?>/login" class="text-decoration-none small fw-bold">Já tenho uma conta (Login)</a>
            </div>
        </form>
    </div>

    <footer class="text-center">
        <p class="footer-text">Produzido por <strong>Samuel Lustosa</strong></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>