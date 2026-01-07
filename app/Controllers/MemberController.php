<?php
namespace App\Controllers;

use App\Models\Member;

class MemberController {
    public function index() {
        session_start();
        if (!isset($_SESSION['logado'])) { header("Location: /login"); exit; }

        $memberModel = new Member();
        
        if (isset($_POST['add_member'])) {
            $memberModel->create($_POST['name'], $_POST['emoji']);
            header("Location: /membros?msg=membro_add");
            exit;
        }

        $membros = $memberModel->getAll();
        require_once '../app/Views/membros.php';
    }

    public function excluir() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            (new Member())->delete($id);
            header("Location: /membros?msg=excluido");
        }
    }
}