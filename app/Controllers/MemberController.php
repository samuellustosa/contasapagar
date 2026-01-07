<?php
namespace App\Controllers;

use App\Models\Member;

class MemberController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['logado'])) { 
            header("Location: /contasapagar/public/login"); 
            exit; 
        }

        $memberModel = new Member();
        
        if (isset($_POST['add_member'])) {
            $memberModel->create($_POST['name'], $_POST['emoji']);
            header("Location: /contasapagar/public/membros?msg=membro_add");
            exit;
        }

        $membros = $memberModel->getAll();
        require_once '../app/Views/membros.php';
    }


    public function excluir() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['logado'])) { 
            header("Location: /contasapagar/public/login"); 
            exit; 
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $memberModel = new Member();
            $memberModel->delete($id);
            header("Location: /contasapagar/public/membros?msg=excluido");
            exit;
        }
    }
}