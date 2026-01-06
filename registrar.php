<?php
// 1. Diagnóstico de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Importações
require 'vendor/autoload.php';
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$msg = "";

// 3. Lógica de Registo
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $token = md5(uniqid($email, true));

    try {
        // Verifica se o e-mail já existe para evitar erro de duplicata
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->rowCount() > 0) {
            $msg = "<div class='alert alert-warning small py-2 text-center'>Este e-mail já está registado.</div>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (email, senha, token, ativo) VALUES (?, ?, ?, 0)");
            if ($stmt->execute([$email, $senha, $token])) {
                
                $mail = new PHPMailer(true);
                
                // Configurações do Servidor
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                
                // Credenciais (Corrigidas para evitar erro de autenticação)
                $mail->Username   = "samuelllsousa579@gmail.com";
                $mail->Password   = "amoz mxei xvlr nboi"; // Senha limpa, sem as barras ou quebras de linha
                
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom('samuelllsousa579@gmail.com', 'Sistema de Contas');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Confirma o teu e-mail - Contas a Pagar';
                
                $link = "https://contasapagar.rf.gd/confirmar.php?token=$token";
                
                $mail->Body = "
                    <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px; border-radius: 10px;'>
                        <h2 style='color: #0d6efd;'>Ativa a tua conta</h2>
                        <p>Olá! Para começar a usar o sistema de Contas a Pagar, confirma o teu e-mail clicando no botão abaixo:</p>
                        <a href='$link' style='display: inline-block; padding: 12px 25px; background-color: #198754; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;'>Confirmar E-mail</a>
                        <p style='margin-top: 20px; font-size: 12px; color: #888;'>Se não criaste esta conta, ignora este e-mail.</p>
                    </div>";

                $mail->send();
                $msg = "<div class='alert alert-success small py-2 text-center'>Sucesso! Verifique o seu e-mail para ativar.</div>";
            }
        }
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger small py-2 text-center'>Erro ao enviar e-mail: Usuário ou senha do SMTP incorretos.</div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger small py-2 text-center'>Erro no Banco de Dados.</div>";
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
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Criar Conta - Contas a Pagar</title>
    <style>
        body {
            background-color: #0d6efd;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            justify-content: center;
            align-items: center;
        }
        .register-card {
            width: 90%;
            max-width: 400px;
            border-radius: 15px;
            border: none;
        }
        .form-control {
            padding: 12px;
            font-size: 16px;
        }
        .btn-success {
            padding: 12px;
            font-weight: bold;
            border-radius: 8px;
        }
        .footer-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
            margin-top: 20px;
        }
    </style>
</head>
<body class="p-3">

    <div class="card register-card p-4 shadow-lg">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-success">Criar Conta</h3>
            <p class="text-muted small">Registe-se para organizar os seus gastos</p>
        </div>

        <?= $msg ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">E-mail</label>
                <input type="email" name="email" class="form-control" placeholder="exemplo@gmail.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Escolha uma Senha</label>
                <input type="password" name="senha" class="form-control" placeholder="••••••••" required>
            </div>
            <button class="btn btn-success w-100 shadow-sm">Registar</button>
            
            <div class="mt-4 text-center">
                <a href="login.php" class="text-decoration-none small fw-bold">Já tenho uma conta (Login)</a>
            </div>
        </form>
    </div>

    <footer class="text-center">
        <p class="footer-text">Produzido por <strong>Samuel Lustosa</strong></p>
    </footer>

</body>
</html>