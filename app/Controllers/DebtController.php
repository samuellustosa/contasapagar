<?php
namespace App\Controllers;
use App\Models\Debt;
use App\Models\Member;

class DebtController {
    public function create() {
        session_start();
        $debtModel = new Debt();
        $memberModel = new Member();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $success = $debtModel->create($_POST['name'], $_POST['amount'], $_POST['due_date'], $_POST['debtors']);
            if ($success) {
                header("Location: /home?msg=sucesso");
                exit;
            }
        }

        $membros = $memberModel->getAll();
        require_once '../app/Views/nova-conta.php';
    }

    public function report() {
        session_start();
        $mes = $_GET['mes'] ?? date('m');
        $ano = $_GET['ano'] ?? date('Y');
        
        $debtModel = new Debt();
        $dados = $debtModel->getReportData($mes, $ano);
        
        // Processa totais para o gráfico do Chart.js
        $nomes = array_column($dados, 'name');
        $valores = array_column($dados, 'total_pessoa');

        require_once '../app/Views/relatorios.php';
    }
}