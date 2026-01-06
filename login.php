<?php
require_once 'config.php';
$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['senha'], $user['senha'])) {
        if ($user['ativo'] == 1) {
            $_SESSION['logado'] = true;
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit;
        } else {
            $erro = "Sua conta ainda não foi ativada pelo e-mail.";
        }
    } else {
        $erro = "Dados incorretos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <meta name="apple-mobile-web-app-title" content="Contas">
    <meta name="application-name" content="Contas">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <link rel="manifest" href="manifest.json">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <title>Contas a Pagar - Login</title>
    
    <style>
        body {
            background-color: #0d6efd;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .form-control {
            padding: 14px;
            font-size: 16px; /* Evita o zoom automático no iPhone */
            border-radius: 10px;
        }
        .btn-primary {
            padding: 14px;
            font-weight: bold;
            border-radius: 10px;
            background-color: #0d6efd;
            border: none;
        }
        .footer-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="card login-card p-4">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary mb-1">Contas a Pagar</h3>
            <p class="text-muted small">Gerencie suas contas com facilidade</p>
        </div>

        <?php if($erro): ?>
            <div class='alert alert-danger py-2 small text-center'><?= $erro ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">E-mail</label>
                <input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Senha</label>
                <input type="password" name="senha" class="form-control" placeholder="••••••••" required>
            </div>

            <button class="btn btn-primary w-100 shadow-sm mt-2">Entrar</button>
            
            <div class="mt-4 text-center">
                <span class="text-muted small">Não tem conta?</span> 
                <a href="registrar.php" class="text-decoration-none small fw-bold">Criar nova conta</a>
            </div>
        </form>
    </div>

    <footer class="text-center">
        <p class="footer-text">Produzido por <strong>Samuel Lustosa</strong></p>
    </footer>

</body>
</html>