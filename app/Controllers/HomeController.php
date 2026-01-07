<?php
namespace App\Controllers;

use App\Models\Debt;
use DateTime;

class HomeController {
    public function index() {
        $base = "/contasapagar/public";

        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['logado'])) {
            header("Location: $base/login");
            exit;
        }

        // Pega o ID do usuário logado na sessão
        $user_id = $_SESSION['user_id'];
        $debtModel = new Debt();
        
        $mes_selecionado = $_GET['mes'] ?? date('m');
        $ano_selecionado = $_GET['ano'] ?? date('Y');

        $data_atual = new DateTime("$ano_selecionado-$mes_selecionado-01");
        $anterior = clone $data_atual;
        $anterior->modify('-1 month');
        $proximo = clone $data_atual;
        $proximo->modify('+1 month');

        // Passa o user_id para os métodos do Model para filtrar apenas os dados dele
        $data = [
            'contas' => $debtModel->getMonthlyDebts($mes_selecionado, $ano_selecionado, $user_id),
            'resumo' => $debtModel->getTotals($mes_selecionado, $ano_selecionado, $user_id),
            'mes_selecionado' => $mes_selecionado,
            'ano_selecionado' => $ano_selecionado,
            'mes_ant' => $anterior->format('m'),
            'ano_ant' => $anterior->format('Y'),
            'mes_prox' => $proximo->format('m'),
            'ano_prox' => $proximo->format('Y'),
            'meses_nome' => [
                '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
                '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
                '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
            ]
        ];

        extract($data);
        require_once '../app/Views/home.php';
    }

    public function pagar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $base = "/contasapagar/public";
        $id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user_id'];
        
        if ($id) {
            $debtModel = new Debt();
            // Só altera o pagamento se a conta pertencer ao usuário logado
            $debtModel->togglePayment($id, $user_id);
            
            header("Location: $base/home?mes=" . ($_GET['mes'] ?? date('m')) . "&ano=" . ($_GET['ano'] ?? date('Y')));
            exit;
        }
    }

    public function excluir() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $base = "/contasapagar/public";
        $id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user_id'];
        
        if ($id) {
            $debtModel = new Debt();
            // Só deleta se a conta for do usuário logado
            $debtModel->delete($id, $user_id);
            
            header("Location: $base/home?msg=excluido");
            exit;
        }
    }
}