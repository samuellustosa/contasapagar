<?php
namespace App\Controllers;

use App\Models\Debt;
use DateTime;

class HomeController {
    public function index() {
        // Define a base do localhost para redirecionamentos
        $base = "/contasapagar/public";

        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Correção da URL de redirecionamento para o login
        if (!isset($_SESSION['logado'])) {
            header("Location: $base/login");
            exit;
        }

        $debtModel = new Debt();
        $mes_selecionado = $_GET['mes'] ?? date('m');
        $ano_selecionado = $_GET['ano'] ?? date('Y');

        // Lógica das setas para navegação entre meses
        $data_atual = new DateTime("$ano_selecionado-$mes_selecionado-01");
        $anterior = clone $data_atual;
        $anterior->modify('-1 month');
        $proximo = clone $data_atual;
        $proximo->modify('+1 month');

        $data = [
            'contas' => $debtModel->getMonthlyDebts($mes_selecionado, $ano_selecionado),
            'resumo' => $debtModel->getTotals($mes_selecionado, $ano_selecionado),
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

        // Extrai as variáveis para a view as reconhecer diretamente
        extract($data);
        require_once '../app/Views/home.php';
    }

    public function pagar() {
        $base = "/contasapagar/public";
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $debtModel = new Debt();
            $debtModel->togglePayment($id);
            
            // Correção do redirecionamento após marcar como pago
            header("Location: $base/home?mes=" . ($_GET['mes'] ?? date('m')) . "&ano=" . ($_GET['ano'] ?? date('Y')));
            exit;
        }
    }

    public function excluir() {
        $base = "/contasapagar/public";
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $debtModel = new Debt();
            $debtModel->delete($id);
            
            // Correção do redirecionamento após excluir
            header("Location: $base/home?msg=excluido");
            exit;
        }
    }
}