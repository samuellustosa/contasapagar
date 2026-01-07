<?php
namespace App\Models;
use App\Core\Database;
use PDO;

class Member {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // LISTAR: Agora filtra por user_id
    public function getAll($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM family_members WHERE user_id = ? ORDER BY name ASC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CRIAR: Agora salva o user_id
    public function create($name, $emoji, $user_id) {
        $stmt = $this->db->prepare("INSERT INTO family_members (name, emoji, user_id) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $emoji, $user_id]);
    }

    // EXCLUIR: Só exclui se o membro pertencer ao usuário
    public function delete($id, $user_id) {
        $stmt = $this->db->prepare("DELETE FROM family_members WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $user_id]);
    }
}