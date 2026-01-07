<?php
namespace App\Controllers;

use App\Models\Debt;
use DateTime;

class HomeController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['logado'])) {
            header("Location: /login");
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
        $id = $_GET['id'] ?? null;
        if ($id) {
            $debtModel = new Debt();
            $debtModel->togglePayment($id);
            header("Location: /home?mes=" . ($_GET['mes'] ?? date('m')) . "&ano=" . ($_GET['ano'] ?? date('Y')));
            exit;
        }
    }

    public function excluir() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $debtModel = new Debt();
            $debtModel->delete($id);
            header("Location: /home?msg=excluido");
            exit;
        }
    }
}