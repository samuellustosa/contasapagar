<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login</title>
</head>
<body class="bg-primary d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow" style="width: 400px;">
        <h3 class="text-center">Acesso Familiar</h3>
        <?php if($erro) echo "<div class='alert alert-danger'>$erro</div>"; ?>
        <form method="POST">
            <input type="email" name="email" class="form-control mb-3" placeholder="E-mail" required>
            <input type="password" name="senha" class="form-control mb-3" placeholder="Senha" required>
            <button class="btn btn-primary w-100">Entrar</button>
            <div class="mt-3 text-center"><a href="registrar.php">Criar nova conta</a></div>
        </form>
    </div>
</body>
</html>