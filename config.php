<?php
$host = 'localhost';
$db   = 'contas'; // Alterado de 'contas_a_pagar' para 'contas'
$user = 'root';
$pass = ''; 

try {
    // A conexão agora buscará o banco chamado 'contas'
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conectado com sucesso!"; // Tire o comentário para testar
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}
?>