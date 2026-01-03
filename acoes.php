<?php
require_once 'config.php';

$id = $_GET['id'] ?? null;
$acao = $_GET['acao'] ?? null;

if ($id && $acao) {
    if ($acao == 'Pagar') {
        $pdo->prepare("UPDATE debts SET is_paid = NOT is_paid WHERE id = ?")->execute([$id]);
        header("Location: index.php");
    } 
    elseif ($acao == 'excluir') {
        $pdo->prepare("DELETE FROM debts WHERE id = ?")->execute([$id]);
        header("Location: index.php");
    } 
    elseif ($acao == 'excluir_membro') {
        // Exclui o membro da família
        $pdo->prepare("DELETE FROM family_members WHERE id = ?")->execute([$id]);
        header("Location: membros.php");
    }
} else {
    header("Location: index.php");
}
?>