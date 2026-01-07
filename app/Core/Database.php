<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance === null) {
            
            $host = 'sql207.infinityfree.com'; 
            $db   = 'if0_40820170_contas'; 
            $user = 'if0_40820170';      
            $pass = 'sous31075';          

            try {
                self::$instance = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erro ao conectar no banco: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}