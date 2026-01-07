<?php 
// Define a base para o localhost
$base = "/contasapagar/public"; 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login - Contas a Pagar</title>
</head>
<body class="bg-primary d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow" style="width: 400px; border-radius: 15px;">
        <h3 class="text-center mb-4 fw-bold">Acesso Familiar</h3>
        
        <?php if(isset($erro) && $erro): ?> 
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <?= $erro ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $base ?>/login">
            <div class="mb-3">
                <label class="form-label small fw-bold">E-mail</label>
                <input type="email" name="email" class="form-control" placeholder="exemplo@gmail.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Senha</label>
                <input type="password" name="senha" class="form-control" placeholder="••••••••" required>
            </div>
            <button class="btn btn-primary w-100 py-2 fw-bold shadow-sm">Entrar</button>
            
            <div class="mt-4 text-center">
                <a href="<?= $base ?>/registrar" class="text-decoration-none small fw-bold">Criar nova conta</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>