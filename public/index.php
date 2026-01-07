<?php
require_once '../vendor/autoload.php';
session_start(); // Inicia aqui para valer em todo o site

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
    case 'excluir-membro': // ROTA ADICIONADA
        (new MemberController())->excluir();
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