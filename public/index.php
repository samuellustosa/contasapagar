<?php
// 1. Ativação de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Caminho corrigido para a raiz (sem ../)
require_once 'vendor/autoload.php';
session_start();

// Autoload manual para garantir que as classes sejam encontradas na raiz
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Controllers\HomeController;
use App\Controllers\MemberController;
use App\Controllers\AuthController;
use App\Controllers\DebtController;

$url = $_GET['url'] ?? 'home';

switch ($url) {
    case 'home':
        (new HomeController())->index();
        break;
    case 'login':
        (new AuthController())->login();
        break;
    case 'registrar':
        (new AuthController())->register();
        break;
    case 'ativar':
        (new AuthController())->ativar();
        break;
    case 'logout':
        (new AuthController())->logout();
        break;
    case 'membros':
        (new MemberController())->index();
        break;
    case 'excluir-membro':
        (new MemberController())->excluir();
        break;
    case 'nova-conta':
        (new DebtController())->create();
        break;
    case 'relatorios':
        (new DebtController())->report();
        break;
    case 'relatorio-geral-pdf': // ADICIONADO: Rota para o PDF Geral
        (new DebtController())->relatorioGeralPDF();
        break;
    case 'pagar':
        (new HomeController())->pagar(); 
        break;
    case 'excluir-conta':
        (new HomeController())->excluir(); 
        break;
    case 'editar-conta':
        (new App\Controllers\DebtController())->edit();
        break;
    default:
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}