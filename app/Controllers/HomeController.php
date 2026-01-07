<?php
namespace App\Controllers;

use App\Models\Debt;

class HomeController {
    public function index() {
        // Verifica se o utilizador está logado (pode ser melhorado depois com um Middleware)
        session_start();
        if (!isset($_SESSION['logado'])) {
            header("Location: /login");
            exit;
        }

        $debtModel = new Debt();
        
        // Lógica de datas vinda do seu index.php original
        $mes_selecionado = $_GET['mes'] ?? date('m');
        $ano_selecionado = $_GET['ano'] ?? date('Y');

        $contas = $debtModel->getMonthlyDebts($mes_selecionado, $ano_selecionado);
        $resumo = $debtModel->getTotals($mes_selecionado, $ano_selecionado);

        // Nomes dos meses para a View
        $meses_nome = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
            '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
            '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        ];

        // Carrega a View e passa os dados
        require_once '../app/Views/home.php';
    }

    
    public function excluir() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $debtModel = new \App\Models\Debt();
            $debtModel->delete($id);
            
            $mes = $_GET['mes'] ?? date('m');
            $ano = $_GET['ano'] ?? date('Y');
            header("Location: /home?mes=$mes&ano=$ano&msg=excluido");
            exit;
        }
    }

        // Método para Alternar Pagamento (Pagar/Desmarcar)
    public function pagar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $debtModel = new \App\Models\Debt();
            $debtModel->togglePayment($id); // Certifica-te que este método existe no Model Debt
            
            $mes = $_GET['mes'] ?? date('m');
            $ano = $_GET['ano'] ?? date('Y');
            header("Location: /home?mes=$mes&ano=$ano");
            exit;
        }
    }

}