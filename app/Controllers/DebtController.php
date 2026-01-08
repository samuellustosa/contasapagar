<?php
namespace App\Controllers;

use App\Models\Debt;
use App\Models\Member;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;

class DebtController {
    private $base = "";

    public function create() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['logado'])) { header("Location: {$this->base}/login"); exit; }

        $user_id = $_SESSION['user_id']; 
        $debtModel = new Debt();
        $memberModel = new Member();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Erro de validação de segurança (CSRF).");
            }

            // ATUALIZADO: Agora enviamos o 'tipo' e 'total_parcelas' para o Model
            $success = $debtModel->create(
                $_POST['name'], 
                $_POST['amount'], 
                $_POST['due_date'], 
                $_POST['debtors'] ?? [], 
                $user_id,
                $_POST['tipo'] ?? 'unica', // Novo campo do formulário
                $_POST['total_parcelas'] ?? 1 // Novo campo do formulário
            );

            if ($success) {
                header("Location: {$this->base}/home?msg=sucesso");
                exit;
            }
        }

        $membros = $memberModel->getAll($user_id); 
        require_once 'app/Views/nova-conta.php';
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
        require_once 'app/Views/relatorios.php';
    }

    public function relatorioGeralPDF() {
        date_default_timezone_set('America/Sao_Paulo');

        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['logado'])) exit;

        $user_id = $_SESSION['user_id'];
        $mes = $_GET['mes'] ?? date('m');
        $ano = $_GET['ano'] ?? date('Y');

        $debtModel = new Debt();
        $dadosAgrupados = $debtModel->getAllMemberDebtsDetail($mes, $ano, $user_id);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        ob_start();
        ?>
        <style>
            body { font-family: sans-serif; color: #333; }
            .titulo { text-align: center; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; margin-bottom: 30px; }
            .secao-membro { margin-bottom: 30px; border: 1px solid #eee; padding: 15px; border-radius: 10px; page-break-inside: avoid; }
            .nome-membro { font-size: 18px; font-weight: bold; color: #0d6efd; border-bottom: 1px solid #0d6efd; margin-bottom: 10px; padding-bottom: 5px; text-transform: uppercase; }
            .linha-conta { display: block; margin: 5px 0; font-size: 13px; border-bottom: 1px dotted #ccc; padding-bottom: 2px; }
            .valor { float: right; font-weight: bold; }
            .total-membro { text-align: right; font-weight: bold; font-size: 16px; margin-top: 10px; padding-top: 5px; color: #198754; }
            .footer { text-align: center; font-size: 10px; color: #777; margin-top: 20px; }
        </style>

        <div class="titulo">
            <h1>Relatório</h1>
            <p>Período: <?= $mes ?>/<?= $ano ?></p>
        </div>

        <?php if(empty($dadosAgrupados)): ?>
            <p style="text-align:center;">Nenhum gasto registrado para este período.</p>
        <?php else: ?>
            <?php foreach ($dadosAgrupados as $nomeMembro => $contas): ?>
                <div class="secao-membro">
                    <div class="nome-membro"><?= htmlspecialchars($nomeMembro) ?></div>
                    <?php 
                    $somaMembro = 0;
                    foreach ($contas as $c): 
                        $parcela = $c['amount'] / $c['total_participants'];
                        $somaMembro += $parcela;
                    ?>
                        <div class="linha-conta">
                            <?= htmlspecialchars($c['debt_name']) ?> 
                            <small style="color:#666;">(Total: R$ <?= number_format($c['amount'], 2, ',', '.') ?> entre <?= $c['total_participants'] ?>)</small>
                            <span class="valor">R$ <?= number_format($parcela, 2, ',', '.') ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="total-membro">
                        TOTAL A PAGAR: R$ <?= number_format($somaMembro, 2, ',', '.') ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="footer">
            Gerado em <?= date('d/m/Y H:i') ?> - Contas a Pagar
        </div>

        <?php
        $html = ob_get_clean();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $dompdf->stream("Relatorio_Familiar_{$mes}_{$ano}.pdf", ["Attachment" => false]);
    }
}