<?php
// 1. Ativar erros para diagnóstico
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// 2. Pegar o token da URL
$token = $_GET['token'] ?? '';

if (!empty($token)) {
    // 3. Verificar se existe um usuário com esse token e que ainda não está ativo
    $stmt = $pdo->prepare("SELECT id FROM users WHERE token = ? AND ativo = 0");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // 4. Se encontrou, ativa o usuário e apaga o token
        $update = $pdo->prepare("UPDATE users SET ativo = 1, token = NULL WHERE id = ?");
        $update->execute([$user['id']]);

        echo "
        <div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
            <h2 style='color:green;'>✔️ E-mail confirmado com sucesso!</h2>
            <p>Sua conta agora está ativa.</p>
            <br>
            <a href='login.php' style='padding:10px 20px; background:#0d6efd; color:white; text-decoration:none; border-radius:5px;'>Ir para o Login</a>
        </div>";
    } else {
        // Caso o link já tenha sido usado ou o token seja inválido
        echo "
        <div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
            <h2 style='color:red;'>❌ Link inválido ou expirado.</h2>
            <p>Sua conta já pode estar ativa ou o token é incorreto.</p>
            <a href='login.php'>Tentar fazer login</a>
        </div>";
    }
} else {
    echo "Nenhum token foi fornecido.";
}
?>