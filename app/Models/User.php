<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($email, $password, $token) {
        $stmt = $this->db->prepare("INSERT INTO users (email, senha, token, ativo) VALUES (?, ?, ?, 0)");
        return $stmt->execute([$email, $password, $token]);
    }

    public function activate($token) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE token = ? AND ativo = 0");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            $update = $this->db->prepare("UPDATE users SET ativo = 1, token = NULL WHERE id = ?");
            return $update->execute([$user['id']]);
        }
        return false;
    }
}