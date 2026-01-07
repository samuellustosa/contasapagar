<?php
namespace App\Controllers;

use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController {
    
    public function login() {
        $erro = null;
        // Definido como vazio para funcionar na raiz do domínio rf.gd
        $base = ""; 

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $userModel = new User();
            $user = $userModel->findByEmail($_POST['email']);

            // Verifica senha e se o usuário existe
            if ($user && password_verify($_POST['senha'], $user['senha'])) {
                if ($user['ativo'] == 1) {
                    $_SESSION['logado'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    ("Location: $base/home");
                    exit;
                }
                $erro = "Conta não ativada. Verifique seu e-mail.";
            } else {
                $erro = "E-mail ou senha incorretos.";
            }
        }
        require_once '../app/Views/login.php';
    }

    public function register() {
        $msg = null;
        $base = "";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $userModel = new User();
            
            // Verifica se e-mail já existe
            if ($userModel->findByEmail($_POST['email'])) {
                $msg = "Este e-mail já está cadastrado.";
            } else {
                $token = md5(uniqid($_POST['email'], true));
                $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

                if ($userModel->create($_POST['email'], $senha, $token)) {
                    $this->sendActivationEmail($_POST['email'], $token);
                    $msg = "Cadastro realizado! Verifique seu e-mail para ativar.";
                } else {
                    $msg = "Erro ao criar conta. Tente novamente.";
                }
            }
        }
        require_once '../app/Views/registrar.php';
    }

    private function sendActivationEmail($email, $token) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'samuelllsousa579@gmail.com'; 
            $mail->Password   = 'SUA_SENHA_DE_APP_AQUI';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('samuelllsousa579@gmail.com', 'Contas a Pagar');
            $mail->addAddress($email);

            
            $link = "http://contasapagar.rf.gd/ativar?token=" . $token;
            
            $mail->isHTML(true);
            $mail->Subject = 'Ative sua conta - Contas a Pagar';
            $mail->Body    = "
                <div style='font-family: sans-serif; color: #333;'>
                    <h2>Bem-vindo ao Sistema!</h2>
                    <p>Falta pouco para organizar suas finanças. Clique no botão abaixo para ativar sua conta:</p>
                    <a href='$link' style='display: inline-block; padding: 10px 20px; color: #fff; background: #0d6efd; text-decoration: none; border-radius: 5px;'>Ativar minha conta</a>
                    <br><br>
                    <small>Se o botão não funcionar, copie o link: $link</small>
                </div>";

            $mail->send();
        } catch (Exception $e) {
            error_log("Erro PHPMailer: {$mail->ErrorInfo}");
        }
    }

    public function ativar() {
        $token = $_GET['token'] ?? null;
        $base = "";
        
        if ($token) {
            $userModel = new User();
            if ($userModel->activate($token)) {
                ("Location: $base/login?msg=ativado");
                exit;
            }
        }
        echo "Link de ativação inválido ou expirado.";
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();
        
        ("Location: /login"); 
        exit;
    }
}