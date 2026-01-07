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
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    $mes_selecionado = $_GET['mes'] ?? date('m');
    $ano_selecionado = $_GET['ano'] ?? date('Y');
    
    $debtModel = new Debt();
    $dados_relatorio = $debtModel->getReportData($mes_selecionado, $ano_selecionado);
    
    // Cálculo de totais para o gráfico
    $total_geral = array_sum(array_column($dados_relatorio, 'total_pessoa'));
    $nomes_grafico = array_column($dados_relatorio, 'name');
    $valores_grafico = array_column($dados_relatorio, 'total_pessoa');

    require_once '../app/Views/relatorios.php';
}
}