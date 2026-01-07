<?php
require_once '../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\MemberController;
use App\Controllers\AuthController;
use App\Controllers\DebtController;

// Captura a URL enviada pelo .htaccess ou via parâmetro GET
$url = $_GET['url'] ?? 'home';

// Roteador Único
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

    case 'excluir-membro':
        (new MemberController())->excluir();
        break;

    case 'excluir-conta':
        (new HomeController())->excluir();
        break;

    case 'nova-conta':
        (new DebtController())->create();
        break;

    case 'relatorios':
        (new DebtController())->report();
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}