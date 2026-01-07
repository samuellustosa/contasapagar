<?php
namespace App\Controllers;
use App\Models\Debt;
use App\Models\Member;
use DateTime;

class DebtController {
    public function create() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['logado'])) { header("Location: /login"); exit; }

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
        if (!isset($_SESSION['logado'])) { header("Location: /login"); exit; }
        
        $mes_selecionado = $_GET['mes'] ?? date('m');
        $ano_selecionado = $_GET['ano'] ?? date('Y');
        
        // Lógica de navegação de datas
        $data_atual = new DateTime("$ano_selecionado-$mes_selecionado-01");
        $anterior = clone $data_atual; $anterior->modify('-1 month');
        $proximo = clone $data_atual; $proximo->modify('+1 month');

        $debtModel = new Debt();
        $dados_relatorio = $debtModel->getReportData($mes_selecionado, $ano_selecionado);
        
        $data = [
            'dados_relatorio' => $dados_relatorio,
            'mes_selecionado' => $mes_selecionado,
            'ano_selecionado' => $ano_selecionado,
            'mes_ant' => $anterior->format('m'),
            'ano_ant' => $anterior->format('Y'),
            'mes_prox' => $proximo->format('m'),
            'ano_prox' => $proximo->format('Y')
        ];

        extract($data);
        require_once '../app/Views/relatorios.php';
    }
}