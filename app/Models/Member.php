<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Member {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM family_members ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name, $emoji) {
        $stmt = $this->db->prepare("INSERT INTO family_members (name, emoji) VALUES (:name, :emoji)");
        return $stmt->execute([':name' => $name, ':emoji' => $emoji]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM family_members WHERE id = ?");
        return $stmt->execute([$id]);
    }
}