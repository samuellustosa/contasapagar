<?php
namespace App\Controllers;

use App\Models\Debt;
use DateTime;

class HomeController {
    public function index() {
        $base = "";

        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['logado'])) {
            header("Location: $base/login");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $debtModel = new Debt();
        
        $mes_selecionado = $_GET['mes'] ?? date('m');
        $ano_selecionado = $_GET['ano'] ?? date('Y');

        $data_atual = new DateTime("$ano_selecionado-$mes_selecionado-01");
        $anterior = clone $data_atual;
        $anterior->modify('-1 month');
        $proximo = clone $data_atual;
        $proximo->modify('+1 month');

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
        require_once 'app/Views/home.php';
    }

    public function pagar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $base = "";
        $id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user_id'];
        
        if ($id) {
            $debtModel = new Debt();
            $debtModel->togglePayment($id, $user_id);
            
            header("Location: $base/home?mes=" . ($_GET['mes'] ?? date('m')) . "&ano=" . ($_GET['ano'] ?? date('Y')));
            exit;
        }
    }

    public function excluir() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $base = "";
        $id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user_id'];
        
        // Se 'todos=1' vier na URL, apaga o grupo_id inteiro
        $excluir_grupo = isset($_GET['todos']) && $_GET['todos'] == '1';
        
        if ($id) {
            $debtModel = new Debt();
            // Chama o delete do Model passando se deve ou não apagar o grupo todo
            $debtModel->delete($id, $user_id, $excluir_grupo);
            
            $msg = $excluir_grupo ? "grupo_excluido" : "excluido";
            header("Location: $base/home?msg=$msg&mes=" . ($_GET['mes'] ?? date('m')) . "&ano=" . ($_GET['ano'] ?? date('Y')));
            exit;
        }
    }
}