<?php
namespace App\Controllers;
use App\Models\Debt;
use App\Models\Member;
use DateTime;

class DebtController {
    private $base = "/contasapagar/public";

    public function create() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['logado'])) { header("Location: {$this->base}/login"); exit; }

        $debtModel = new Debt();
        $memberModel = new Member();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $success = $debtModel->create($_POST['name'], $_POST['amount'], $_POST['due_date'], $_POST['debtors']);
            if ($success) {
                header("Location: {$this->base}/home?msg=sucesso");
                exit;
            }
        }

        $membros = $memberModel->getAll();
        require_once '../app/Views/nova-conta.php';
    }

    public function report() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['logado'])) { header("Location: {$this->base}/login"); exit; }
        
        $mes_selecionado = $_GET['mes'] ?? date('m');
        $ano_selecionado = $_GET['ano'] ?? date('Y');
        
        $data_atual = new DateTime("$ano_selecionado-$mes_selecionado-01");
        $anterior = clone $data_atual; $anterior->modify('-1 month');
        $proximo = clone $data_atual; $proximo->modify('+1 month');

        $debtModel = new Debt();
        $dados_relatorio = $debtModel->getReportData($mes_selecionado, $ano_selecionado);
        
        // ARRAY ADICIONADO PARA RESOLVER O ERRO DA VARIÁVEL INDEFINIDA
        $meses_nome = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
            '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
            '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        ];

        $data = [
            'dados_relatorio' => $dados_relatorio,
            'mes_selecionado' => $mes_selecionado,
            'ano_selecionado' => $ano_selecionado,
            'mes_ant' => $anterior->format('m'),
            'ano_ant' => $anterior->format('Y'),
            'mes_prox' => $proximo->format('m'),
            'ano_prox' => $proximo->format('Y'),
            'meses_nome' => $meses_nome // Enviando para a view
        ];

        extract($data);
        require_once '../app/Views/relatorios.php';
    }
}