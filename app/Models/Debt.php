<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Debt {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getMonthlyDebts($mes, $ano, $user_id) {
        $sql = "SELECT d.*, 
                GROUP_CONCAT(m.name SEPARATOR ', ') as devedores
                FROM debts d
                LEFT JOIN debt_members dm ON d.id = dm.debt_id
                LEFT JOIN family_members m ON dm.member_id = m.id
                WHERE MONTH(d.due_date) = ? AND YEAR(d.due_date) = ? AND d.user_id = ?
                GROUP BY d.id
                ORDER BY d.due_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mes, $ano, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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

    /**
     * DELETE MELHORADO: Permite excluir o grupo todo ou apenas um item.
     */
    public function delete($id, $user_id, $excluir_grupo = false) {
        if ($excluir_grupo) {
            // Busca o grupo_id antes de deletar
            $stmt = $this->db->prepare("SELECT grupo_id FROM debts WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($item && $item['grupo_id']) {
                $stmt = $this->db->prepare("DELETE FROM debts WHERE grupo_id = ? AND user_id = ?");
                return $stmt->execute([$item['grupo_id'], $user_id]);
            }
        }

        $stmt = $this->db->prepare("DELETE FROM debts WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $user_id]);
    }

    /**
     * CREATE MELHORADO: Nomes mais claros para contas fixas e parceladas.
     */
    public function create($name, $amount, $due_date, $debtors, $user_id, $tipo = 'unica', $total_parcelas = 1) {
        $this->db->beginTransaction();
        try {
            $grupo_id = uniqid(); 
            $loops = ($tipo === 'parcelada') ? (int)$total_parcelas : (($tipo === 'fixa') ? 12 : 1);

            $meses_nome = [
                1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr', 5 => 'Mai', 6 => 'Jun',
                7 => 'Jul', 8 => 'Ago', 9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'
            ];

            for ($i = 0; $i < $loops; $i++) {
                $data_vencimento = new \DateTime($due_date);
                $data_vencimento->modify("+$i month");
                $vencimento_final = $data_vencimento->format('Y-m-d');
                $mes_num = (int)$data_vencimento->format('m');
                
                $nome_exibicao = $name;
                if ($tipo === 'parcelada') {
                    $nome_exibicao .= " (" . ($i + 1) . "/$total_parcelas)";
                } elseif ($tipo === 'fixa') {
                    $nome_exibicao .= " - " . $meses_nome[$mes_num];
                }

                $stmt = $this->db->prepare("INSERT INTO debts (name, amount, due_date, user_id, tipo, parcela_atual, total_parcelas, grupo_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $nome_exibicao, 
                    $amount, 
                    $vencimento_final, 
                    $user_id, 
                    $tipo, 
                    ($tipo === 'parcelada' ? $i + 1 : null), 
                    ($tipo === 'parcelada' ? (int)$total_parcelas : null),
                    $grupo_id
                ]);

                $debt_id = $this->db->lastInsertId();
                $stmt_member = $this->db->prepare("INSERT INTO debt_members (debt_id, member_id) VALUES (?, ?)");
                foreach ($debtors as $mid) {
                    $stmt_member->execute([$debt_id, $mid]);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

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
        $stmt = $this->db->prepare("UPDATE debts SET is_paid = NOT is_paid WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $user_id]);
    }


    public function getAllMemberDebtsDetail($mes, $ano, $user_id) {
        
        $sql = "SELECT m.name as member_name, d.name as debt_name, d.amount, 
                d.due_date, d.tipo, d.parcela_atual, d.total_parcelas,
                (SELECT COUNT(*) FROM debt_members dm2 WHERE dm2.debt_id = d.id) as total_participants
                FROM family_members m
                JOIN debt_members dm ON m.id = dm.member_id
                JOIN debts d ON dm.debt_id = d.id
                WHERE MONTH(d.due_date) = ? AND YEAR(d.due_date) = ? AND d.user_id = ?
                ORDER BY m.name ASC, d.due_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mes, $ano, $user_id]);
        return $stmt->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_ASSOC); 
    }
}