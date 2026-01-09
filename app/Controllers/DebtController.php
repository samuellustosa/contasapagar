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

            
            $success = $debtModel->create(
                $_POST['name'], 
                $_POST['amount'], 
                $_POST['due_date'], 
                $_POST['debtors'] ?? [], 
                $user_id,
                $_POST['tipo'] ?? 'unica', 
                $_POST['total_parcelas'] ?? 1 
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
            body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.4; }
            .titulo { text-align: center; border-bottom: 1px solid #333; padding-bottom: 10px; margin-bottom: 30px; }
            .titulo h1 { margin: 0; font-size: 22px; text-transform: uppercase; }
            .titulo p { margin: 5px 0 0; font-size: 14px; color: #666; }
            
            .secao-membro { margin-bottom: 40px; page-break-inside: avoid; }
            .nome-membro { font-size: 16px; font-weight: bold; border-bottom: 2px solid #000; margin-bottom: 10px; padding-bottom: 3px; text-transform: uppercase; }
            
            .linha-conta { display: block; border-bottom: 1px dotted #ccc; padding: 6px 0; font-size: 13px; }
            .dia { font-weight: bold; color: #444; margin-right: 10px; }
            .parcela { font-size: 11px; color: #0d6efd; font-weight: bold; margin-left: 5px; }
            .detalhes-valor { color: #777; font-size: 11px; margin-left: 5px; }
            .valor-final { float: right; font-weight: bold; font-size: 14px; }
            
            .total-membro { text-align: right; font-size: 15px; font-weight: bold; margin-top: 10px; padding-top: 5px; color: #000; }
            .footer { position: fixed; bottom: 0; text-align: center; font-size: 9px; color: #999; width: 100%; border-top: 1px solid #eee; padding-top: 5px; }
        </style>

        <div class="titulo">
            <h1>Relatório</h1>
            <p>Referência: <?= $mes ?>/<?= $ano ?></p>
        </div>

        <?php if(empty($dadosAgrupados)): ?>
            <p style="text-align:center;">Nenhum registro encontrado para este período.</p>
        <?php else: ?>
            <?php foreach ($dadosAgrupados as $nomeMembro => $contas): ?>
                <div class="secao-membro">
                    <div class="nome-membro"><?= htmlspecialchars($nomeMembro) ?></div>
                    <?php 
                    $somaMembro = 0;
                    foreach ($contas as $c): 
                        $parcelaValor = $c['amount'] / $c['total_participants'];
                        $somaMembro += $parcelaValor;
                        $dia = date('d', strtotime($c['due_date']));
                    ?>
                        <div class="linha-conta">
                            <span class="dia">Dia <?= $dia ?></span>
                            <?= htmlspecialchars($c['debt_name']) ?>
                            
                            <?php if($c['tipo'] === 'parcelada'): ?>
                                <span class="parcela">(<?= $c['parcela_atual'] ?>/<?= $c['total_parcelas'] ?>)</span>
                            <?php endif; ?>

                            <span class="detalhes-valor">
                                [Total: R$ <?= number_format($c['amount'], 2, ',', '.') ?> / <?= $c['total_participants'] ?> pessoa(s)]
                            </span>

                            <span class="valor-final">R$ <?= number_format($parcelaValor, 2, ',', '.') ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="total-membro">
                        TOTAL A PAGAR: R$ <?= number_format($somaMembro, 2, ',', '.') ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="footer">
            Gerado em <?= date('d/m/Y H:i') ?> | Contas a Pagar
        </div>

        <?php
        $html = ob_get_clean();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Relatorio_Financeiro_{$mes}_{$ano}.pdf", ["Attachment" => false]);
    }



    public function edit() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['logado'])) { header("Location: /login"); exit; }

        $user_id = $_SESSION['user_id'];
        $debtModel = new Debt();
        $memberModel = new Member();
        $id = $_GET['id'] ?? $_POST['id'] ?? null;

        if (!$id) { header("Location: /home"); exit; }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Erro CSRF.");
            }

            // Verifica se o usuário marcou para atualizar o grupo todo
            $update_group = isset($_POST['update_all']) && $_POST['update_all'] == '1';

            $success = $debtModel->update(
                $id,
                $_POST['name'],
                $_POST['amount'],
                $_POST['due_date'],
                $_POST['debtors'] ?? [],
                $user_id,
                $update_group
            );

            if ($success) {
                header("Location: /home?msg=sucesso");
                exit;
            }
        }

        $conta = $debtModel->find($id, $user_id);
        if (!$conta) { die("Conta não encontrada."); }
        
        $membros = $memberModel->getAll($user_id);
        require_once 'app/Views/editar-conta.php';
    }
}