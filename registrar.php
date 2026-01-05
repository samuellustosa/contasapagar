<?php
// 1. Exibição de erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. IMPORTAR O AUTOLOAD (Caminho correto baseado na sua imagem)
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once 'config.php'; //
$msg = "";

// 3. LÓGICA DE REGISTO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $token = md5(uniqid($email, true));

    try {
        // Salva no banco de dados
        $stmt = $pdo->prepare("INSERT INTO users (email, senha, token, ativo) VALUES (?, ?, ?, 0)");
        if ($stmt->execute([$email, $senha, $token])) {
            
            $mail = new PHPMailer(true);
            
            // Configurações do Servidor
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USER'); 
            $mail->Password   = getenv('SMTP_PASS'); 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;;

            $mail->setFrom('samuelllsousa579@gmail.com', 'Sistema de Contas');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Confirma o teu e-mail - Contas a Pagar';
            
            $link = "https://contasapagar.rf.gd/confirmar.php?token=$token";
            
            $mail->Body = "<h2>Ativa a tua conta</h2><p>Clica abaixo para confirmar seu e-mail:</p><a href='$link'>Confirmar E-mail</a>";

            $mail->send();
            $msg = "<div class='alert alert-success'>Sucesso! Verifique seu e-mail para ativar a conta.</div>";
        }
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger'>Erro ao enviar e-mail: {$mail->ErrorInfo}</div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>Erro no Banco de Dados: " . $e->getMessage() . "</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-primary d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow" style="width: 400px;">
        <h3 class="text-center">Criar Conta</h3>
        <?= $msg ?>
        <form method="POST">
            <input type="email" name="email" class="form-control mb-3" placeholder="E-mail" required>
            <input type="password" name="senha" class="form-control mb-3" placeholder="Senha" required>
            <button class="btn btn-success w-100">Registar</button>
        </form>
    </div>
</body>
</html>