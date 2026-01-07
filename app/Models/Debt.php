<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Debt {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    

    // Procura todas as contas de um mês e ano específicos
    public function getMonthlyDebts($mes, $ano) {
        $stmt = $this->db->prepare("SELECT * FROM debts WHERE MONTH(due_date) = ? AND YEAR(due_date) = ? ORDER BY due_date ASC");
        $stmt->execute([$mes, $ano]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Calcula os totais (Total, Pago, Pendente)
    public function getTotals($mes, $ano) {
        $stmt = $this->db->prepare("SELECT 
            SUM(amount) as total,
            SUM(CASE WHEN is_paid = 1 THEN amount ELSE 0 END) as pago
            FROM debts WHERE MONTH(due_date) = ? AND YEAR(due_date) = ?");
        $stmt->execute([$mes, $ano]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $total = $result['total'] ?? 0;
        $pago = $result['pago'] ?? 0;
        
        return [
            'total' => $total,
            'pago' => $pago,
            'pendente' => $total - $pago
        ];
    }

    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM debts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function create($name, $amount, $due_date, $debtors) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("INSERT INTO debts (name, amount, due_date) VALUES (?, ?, ?)");
            $stmt->execute([$name, $amount, $due_date]);
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

    public function getReportData($mes, $ano) {
        // Lógica extraída do seu relatorios.php
        $sql = "SELECT m.name, m.emoji, 
                SUM(d.amount / (SELECT COUNT(*) FROM debt_members dm2 WHERE dm2.debt_id = d.id)) as total_pessoa
                FROM family_members m
                JOIN debt_members dm ON m.id = dm.member_id
                JOIN debts d ON dm.debt_id = d.id
                WHERE MONTH(d.due_date) = ? AND YEAR(d.due_date) = ?
                GROUP BY m.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mes, $ano]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
