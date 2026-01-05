<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'sql207.infinityfree.com'; 
$db   = 'if0_40820170_contas';        
$pass = 'sous31075';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}

$pagina_atual = basename($_SERVER['PHP_SELF']);
$paginas_publicas = ['login.php', 'registrar.php', 'confirmar.php'];

if (!isset($_SESSION['logado']) && !in_array($pagina_atual, $paginas_publicas)) {
    header("Location: login.php");
    exit;
}
?>