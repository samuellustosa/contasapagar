<?php
namespace App\Controllers;

use App\Models\Member;

class MemberController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Define a base como vazia para o domínio real
        $base = ""; 
        
        // Bloqueia acesso se não estiver logado
        if (!isset($_SESSION['logado'])) { 
            header("Location: $base/login"); 
            exit; 
        }

        $user_id = $_SESSION['user_id']; // ID do dono da família
        $memberModel = new Member();
        
        // Lógica para Adicionar Membro
        if (isset($_POST['add_member'])) {
            // SEGURANÇA: Validação de Token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Erro de validação de segurança (CSRF).");
            }

            // Passa o nome, emoji e o ID do usuário logado
            $memberModel->create($_POST['name'], $_POST['emoji'], $user_id);
            header("Location: $base/membros?msg=membro_add");
            exit;
        }

        // LISTAR: Busca apenas os membros que ESSE usuário criou
        $membros = $memberModel->getAll($user_id);
        require_once 'app/Views/membros.php';
    }


    public function excluir() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $base = "";

        if (!isset($_SESSION['logado'])) { 
            header("Location: $base/login"); 
            exit; 
        }

        $user_id = $_SESSION['user_id'];
        $id = $_GET['id'] ?? null;

        if ($id) {
            $memberModel = new Member();
            $memberModel->delete($id, $user_id);
            header("Location: $base/membros?msg=excluido");
            exit;
        }
    }
}