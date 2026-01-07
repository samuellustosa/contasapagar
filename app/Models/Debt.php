<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Debt {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Busca apenas as contas do usuário logado
    public function getMonthlyDebts($mes, $ano, $user_id) {
        $stmt = $this->db->prepare("SELECT * FROM debts WHERE MONTH(due_date) = ? AND YEAR(due_date) = ? AND user_id = ? ORDER BY due_date ASC");
        $stmt->execute([$mes, $ano, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Calcula os totais filtrando pelo usuário logado
    public function getTotals($mes, $ano, $user_id) {
        $stmt = $this->db->prepare("SELECT 
            SUM(amount) as total,
            SUM(CASE WHEN is_paid = 1 THEN amount ELSE 0 END) as pago
            FROM debts WHERE MONTH(due_date) = ? AND YEAR(due_date) = ? AND user_id = ?");
        $stmt->execute([$mes, $ano, $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $total = $result['total'] ?? 0;
        $pago = $result['pago'] ?? 0;
        
        return [
            'total' => $total,
            'pago' => $pago,
            'pendente' => $total - $pago
        ];
    }

    // Deleta apenas se a conta pertencer ao usuário logado (segurança extra)
    public function delete($id, $user_id) {
        $stmt = $this->db->prepare("DELETE FROM debts WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $user_id]);
    }

    // Insere o user_id ao criar a conta
    public function create($name, $amount, $due_date, $debtors, $user_id) {
        $this->db->beginTransaction();
        try {
            // Agora inclui o campo user_id na query
            $stmt = $this->db->prepare("INSERT INTO debts (name, amount, due_date, user_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $amount, $due_date, $user_id]);
            $debt_id = $this->db->lastInsertId();

            $stmt_member = $this->db->prepare("INSERT INTO debt_members (debt_id, member_id) VALUES (?, ?)");
            foreach ($debtors as $mid) {
                $stmt_member->execute([$debt_id, $mid]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Relatório filtrado por usuário
    public function getReportData($mes, $ano, $user_id) {
        $sql = "SELECT m.name, m.emoji, 
                SUM(d.amount / (SELECT COUNT(*) FROM debt_members dm2 WHERE dm2.debt_id = d.id)) as total_pessoa
                FROM family_members m
                JOIN debt_members dm ON m.id = dm.member_id
                JOIN debts d ON dm.debt_id = d.id
                WHERE MONTH(d.due_date) = ? AND YEAR(d.due_date) = ? AND d.user_id = ?
                GROUP BY m.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mes, $ano, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function togglePayment($id, $user_id) {
        // Verifica o user_id para evitar que alguém desmarque a conta de outro via URL
        $stmt = $this->db->prepare("UPDATE debts SET is_paid = NOT is_paid WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $user_id]);
    }
}