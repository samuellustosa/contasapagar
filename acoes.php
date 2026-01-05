<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$acao = strip_tags($_GET['acao'] ?? '');

// Captura o mês e ano para garantir o redirecionamento correto
$mes = $_GET['mes'] ?? date('m');
$ano = $_GET['ano'] ?? date('Y');

if ($id && $acao) {
    if ($acao == 'Pagar') {
        $pdo->prepare("UPDATE debts SET is_paid = NOT is_paid WHERE id = ?")->execute([$id]);
        header("Location: index.php?mes=$mes&ano=$ano");
    } 
    elseif ($acao == 'excluir') {
        $pdo->prepare("DELETE FROM debts WHERE id = ?")->execute([$id]);
        header("Location: index.php?mes=$mes&ano=$ano&msg=excluido"); // Adicionado &msg=excluido
    } 
    elseif ($acao == 'excluir_membro') {
        $pdo->prepare("DELETE FROM family_members WHERE id = ?")->execute([$id]);
        header("Location: membros.php?msg=excluido"); // Adicionado &msg=excluido
    }
}
?>