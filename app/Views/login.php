<?php 
$base = ""; 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="theme-color" content="#0d6efd">
    <title>Login - Contas a Pagar</title>
    <style>
        body { background-color: #0d6efd; }
        .login-card { border-radius: 15px; border: none; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow-lg login-card" style="width: 100%; max-width: 400px; margin: 20px;">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary">Contas a Pagar</h3>
            <p class="text-muted small">Que os jogos comecem</p>
        </div>
        
        <?php if(isset($erro) && $erro): ?> 
            <div class='alert alert-danger alert-dismissible fade show border-0 shadow-sm' role='alert'>
                <?= htmlspecialchars($erro) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'ativado'): ?>
            <div class="alert alert-success border-0 shadow-sm small">
                Conta ativada com sucesso! Faça seu login.
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $base ?>/login">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">E-mail</label>
                <input type="email" name="email" class="form-control form-control-lg" placeholder="exemplo@gmail.com" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Senha</label>
                <input type="password" name="senha" class="form-control form-control-lg" placeholder="••••••••" required>
            </div>
            
            <button class="btn btn-primary w-100 py-3 fw-bold shadow-sm mt-2">Entrar</button>
            
            <div class="mt-4 text-center">
                <span class="text-muted small">Não tem uma conta?</span>
                <a href="<?= $base ?>/registrar" class="text-decoration-none small fw-bold">Criar conta agora</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>