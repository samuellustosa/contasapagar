<?php
namespace App\Controllers;

use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController {
    
    public function login() {
        $erro = null;
        $base = "/contasapagar/public";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new User();
            $user = $userModel->findByEmail($_POST['email']);

            if ($user && password_verify($_POST['senha'], $user['senha'])) {
                if ($user['ativo'] == 1) {
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['logado'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    header("Location: $base/home");
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
        $base = "/contasapagar/public";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            $mail->Username   = ''; //
            $mail->Password   = '';        //
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Destinatário
            $mail->setFrom('samuelllsousa579@gmail.com', 'Contas a Pagar');
            $mail->addAddress($email);

            // Conteúdo do e-mail
            $link = "http://localhost/contasapagar/public/ativar?token=" . $token;
            $mail->isHTML(true);
            $mail->Subject = 'Ative sua conta - Sistema de Contas';
            $mail->Body    = "<h1>Ativação de Conta</h1>
                              <p>Clique no link abaixo para ativar sua conta e começar a usar o sistema:</p>
                              <a href='$link'>$link</a>";

            $mail->send();
        } catch (Exception $e) {
            // Em localhost, se falhar o envio, você pode logar o erro
            error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        }
    }

    public function ativar() {
        $token = $_GET['token'] ?? null;
        $base = "/contasapagar/public";
        
        if ($token) {
            $userModel = new User();
            if ($userModel->activate($token)) { //
                header("Location: $base/login?msg=ativado");
                exit;
            }
        }
        echo "Token inválido ou conta já ativada.";
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: /contasapagar/public/login");
        exit;
    }
}