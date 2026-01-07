<?php
require_once '../vendor/autoload.php';

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
    case 'logout':
        (new AuthController())->logout();
        break;
    case 'membros':
        (new MemberController())->index();
        break;
    case 'nova-conta':
        (new DebtController())->create();
        break;
    case 'relatorios':
        (new DebtController())->report();
        break;
    case 'pagar':
        (new HomeController())->pagar();
        break;
    case 'excluir-conta':
        (new HomeController())->excluir();
        break;
    default:
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}