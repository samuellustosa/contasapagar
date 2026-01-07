<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance === null) {
            // Configurações do seu XAMPP Local
            $host = 'localhost'; 
            $db   = 'contass'; 
            $user = 'root';      
            $pass = '';          

            try {
                self::$instance = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erro ao conectar no banco local: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}