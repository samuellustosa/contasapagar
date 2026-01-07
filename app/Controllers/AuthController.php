<?php
namespace App\Controllers;

use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;

class AuthController {
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new User();
            $user = $userModel->findByEmail($_POST['email']);

            if ($user && password_verify($_POST['senha'], $user['senha'])) {
                if ($user['ativo'] == 1) {
                    session_start();
                    $_SESSION['logado'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    header("Location: /home");
                    exit;
                }
                $erro = "Conta não ativada.";
            } else {
                $erro = "Dados incorretos.";
            }
        }
        require_once '../app/Views/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new User();
            $token = md5(uniqid($_POST['email'], true));
            $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

            if ($userModel->create($_POST['email'], $senha, $token)) {
                $this->sendActivationEmail($_POST['email'], $token);
                $msg = "Verifique o seu e-mail para ativar.";
            }
        }
        require_once '../app/Views/registrar.php';
    }

    private function sendActivationEmail($email, $token) {
        // Aqui movemos a lógica do PHPMailer que estava no registrar.php
        // Use as variáveis do seu ficheiro 'env'
        $mail = new PHPMailer(true);
        // ... (configurações do SMTP que já possui)
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: /login");
    }
}