<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0d6efd">
    <meta name="mobile-web-app-capable" content="yes">
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="Contas a Pagar">
    <meta property="og:description" content="Organize as finanças da sua família de forma simples.">
    <meta property="og:url" content="https://samuellst.cloud/">
    <meta property="og:image" content="https://samuellst.cloud/icon-512x512.png">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">
    <meta property="og:image:type" content="image/png">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Contas a Pagar">
    <meta name="twitter:description" content="Gestão financeira familiar simplificada.">
    <meta name="twitter:image" content="https://samuellst.cloud/icon-512x512.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Contas a Pagar">
    <link rel="apple-touch-icon" href="/icon-192x192.png">
    
    <title>Contas a Pagar</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">

    <script>
        if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js');
        });
        }
    </script>

    
    <style>
        body { padding-top: 70px; }
        .sticky-menu { position: sticky; top: 72px; z-index: 1020; background-color: #f8f9fa; padding-bottom: 10px; }
        .nav-pills .nav-link.active { background-color: #0d6efd; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3); }
        .fab { position: fixed; bottom: 25px; right: 25px; width: 65px; height: 65px; background-color: #0d6efd; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white !important; font-size: 32px; box-shadow: 0 5px 20px rgba(0,0,0,0.4); z-index: 1050; text-decoration: none; }
    
        .skeleton { 
            background-color: #e2e5e7; 
            background-image: linear-gradient(90deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0)); 
            background-size: 200px 100%; 
            animation: skeleton-loading 1.5s infinite;
            display: block;
        }
        @keyframes skeleton-loading { 0% { background-position: -200px 0; } 100% { background-position: calc(200px + 100%) 0; } }
        
        .content-hidden { display: none !important; }
    </style>
</head>
<body class="bg-light pb-5"> 

    <?php 
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $uri = $_SERVER['REQUEST_URI'];
        $m = $_GET['mes'] ?? '';
        $a = $_GET['ano'] ?? '';
        $query = ($m && $a) ? "?mes=$m&ano=$a" : "";
        $base = ""; // Mantenha vazio se os controladores usarem rotas amigáveis da raiz
    ?>

    <nav class="navbar navbar-dark bg-primary shadow-sm py-3 fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="navbar-brand mb-0 h1">Contas a Pagar</span>
            <a href="<?= $base ?>/logout" class="btn btn-sm btn-outline-light">Sair</a>
        </div>
    </nav>

    <div class="sticky-menu">
        <div class="d-flex justify-content-center pt-3">
            <div class="nav nav-pills bg-white p-1 rounded-pill shadow-sm border">
                <?php 
                    // Melhoria na lógica de active para evitar erros de strpos
                    $isHome = (strpos($uri, 'home') !== false || $uri == '/' || $uri == '/index.php');
                    $isRel = (strpos($uri, 'relatorios') !== false);
                    $isMem = (strpos($uri, 'membros') !== false);
                ?>
                <a class="nav-link rounded-pill <?= $isHome ? 'active' : 'text-secondary' ?>" href="<?= $base ?>/home<?= $query ?>">Mural</a>
                <a class="nav-link rounded-pill <?= $isRel ? 'active' : 'text-secondary' ?>" href="<?= $base ?>/relatorios<?= $query ?>">Relatórios</a>
                <a class="nav-link rounded-pill <?= $isMem ? 'active' : 'text-secondary' ?>" href="<?= $base ?>/membros<?= $query ?>">Família</a>
            </div>
        </div>
    </div>

    <a href="<?= $base ?>/nova-conta<?= $query ?>" class="fab" <?= (strpos($uri, 'nova-conta') !== false) ? 'style="display:none"' : '' ?>>+</a>

    <div id="skeleton-loader" class="container mt-3 d-none">
        <div class="skeleton mb-4" style="height: 100px; border-radius: 15px; width: 100%;"></div>
        <div class="row g-2 mb-4">
            <div class="col-4"><div class="skeleton" style="height: 80px; border-radius: 10px;"></div></div>
            <div class="col-4"><div class="skeleton" style="height: 80px; border-radius: 10px;"></div></div>
            <div class="col-4"><div class="skeleton" style="height: 80px; border-radius: 10px;"></div></div>
        </div>
        <div class="skeleton" style="height: 300px; border-radius: 15px; width: 100%;"></div>
    </div>

    <div id="main-content" class="container">
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3 shadow-sm" role="alert">
                <?php
                    $mensagens = [
                        'sucesso' => 'Salvo com sucesso!', 
                        'editado' => 'Alterações gravadas!',
                        'excluido' => 'Removido!', 
                        'membro_add' => 'Membro adicionado!', 
                        'ativado' => 'Conta ativada!',
                        'grupo_excluido' => 'Todo o grupo foi removido!'
                    ];
                    echo htmlspecialchars($mensagens[$_GET['msg']] ?? 'Ação concluída!');
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>