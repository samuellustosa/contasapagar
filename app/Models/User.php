<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Busca usuário por e-mail para Login
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Criação de conta com status inativo (0) aguardando ativação por e-mail
    public function create($email, $password, $token) {
        // Garantimos que a query use os nomes exatos das colunas do seu banco
        $stmt = $this->db->prepare("INSERT INTO users (email, senha, token, ativo) VALUES (?, ?, ?, 0)");
        return $stmt->execute([$email, $password, $token]);
    }

    // Ativação via Token enviado por e-mail
    public function activate($token) {
        // Busca se existe um usuário com esse token que ainda não esteja ativo
        $stmt = $this->db->prepare("SELECT id FROM users WHERE token = ? AND ativo = 0");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            // Ativa a conta e remove o token para ele não ser usado novamente
            $update = $this->db->prepare("UPDATE users SET ativo = 1, token = NULL WHERE id = ?");
            return $update->execute([$user['id']]);
        }
        return false;
    }
}