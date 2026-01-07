<?php
namespace App\Controllers;

use App\Models\Debt;
use App\Models\Member;
use DateTime;

class DebtController {
    private $base = "";

    public function create() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['logado'])) { header("Location: {$this->base}/login"); exit; }

        $user_id = $_SESSION['user_id']; // Identifica o dono da conta
        $debtModel = new Debt();
        $memberModel = new Member();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // SEGURANÇA: Validação de Token CSRF (impede ataques externos)
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Erro de validação de segurança (CSRF).");
            }

            // Agora passamos o $user_id para o Model salvar a conta no nome do usuário certo
            $success = $debtModel->create(
                $_POST['name'], 
                $_POST['amount'], 
                $_POST['due_date'], 
                $_POST['debtors'] ?? [], 
                $user_id
            );

            if ($success) {
                header("Location: {$this->base}/home?msg=sucesso");
                exit;
            }
        }

        // Busca apenas os membros da família cadastrados por ESSE usuário
        $membros = $memberModel->getAll($user_id); 
        require_once '../app/Views/nova-conta.php';
    }

    public function report() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['logado'])) { header("Location: {$this->base}/login"); exit; }
        
        $user_id = $_SESSION['user_id'];
        $mes_selecionado = $_GET['mes'] ?? date('m');
        $ano_selecionado = $_GET['ano'] ?? date('Y');
        
        $data_atual = new DateTime("$ano_selecionado-$mes_selecionado-01");
        $anterior = clone $data_atual; $anterior->modify('-1 month');
        $proximo = clone $data_atual; $proximo->modify('+1 month');

        $debtModel = new Debt();
        // Passamos o user_id para o relatório não misturar dados de usuários diferentes
        $dados_relatorio = $debtModel->getReportData($mes_selecionado, $ano_selecionado, $user_id);
        
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
            'meses_nome' => $meses_nome
        ];

        extract($data);
        require_once '../app/Views/relatorios.php';
    }
}