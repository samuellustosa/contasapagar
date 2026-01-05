<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0d6efd">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Contas">
    <link rel="apple-touch-icon" href="icon-192x192.png">
    <title>Contas a Pagar</title>
    <style>
        /* Ajuste para o conteúdo não ficar escondido sob o header fixo */
        body {
            padding-top: 70px;
        }

        /* Mantém o menu de pílulas fixo logo abaixo do navbar azul */
        .sticky-menu {
            position: sticky;
            top: 72px;
            z-index: 1020;
            background-color: #f8f9fa;
            padding-bottom: 10px;
        }

        .nav-pills {
            font-size: 0.9rem;
        }

        @media (max-width: 576px) {
            .nav-pills .nav-link {
                padding: 8px 12px;
            }
        }

        .nav-pills .nav-link.active {
            background-color: #0d6efd;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }

        .fab {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 65px;
            height: 65px;
            background-color: #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white !important;
            font-size: 32px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.4);
            z-index: 1050;
            text-decoration: none;
            transition: 0.3s;
        }
        
        <?php 
        $pagina_atual = basename($_SERVER['PHP_SELF']); 
        // Captura o mês e ano da URL para manter a navegação consistente
        $m = $_GET['mes'] ?? '';
        $a = $_GET['ano'] ?? '';
        $query = ($m && $a) ? "?mes=$m&ano=$a" : "";
        ?>

        <?php if ($pagina_atual == 'nova-conta.php'): ?>
            .fab { display: none; }
        <?php endif; ?>

                /* Estilo do Skeleton */
        .skeleton {
            background-color: #e2e5e7;
            background-image: linear-gradient(90deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0));
            background-size: 200px 100%;
            background-repeat: no-repeat;
            display: inline-block;
            line-height: 1;
            width: 100%;
            animation: skeleton-loading 1.5s infinite;
        }

        @keyframes skeleton-loading {
            0% { background-position: -200px 0; }
            100% { background-position: calc(200px + 100%) 0; }
        }

        /* Esconde o conteúdo real enquanto o skeleton aparece (opcional para transições) */
        .loading-hidden {
            display: none !important;
        }
    </style>
</head>
<body class="bg-light pb-5"> 
    
    <nav class="navbar navbar-dark bg-primary shadow-sm py-3 fixed-top">
        <div class="container text-center">
            <span class="navbar-brand mb-0 h1">📊 Contas a Pagar</span>
        </div>
    </nav>

    <div class="sticky-menu">
        <div class="d-flex justify-content-center pt-3">
            <div class="nav nav-pills bg-white p-1 rounded-pill shadow-sm border">
                <a class="nav-link rounded-pill <?= ($pagina_atual == 'index.php') ? 'active' : 'text-secondary' ?>" href="./<?= $query ?>">Mural</a>
                <a class="nav-link rounded-pill <?= ($pagina_atual == 'relatorios.php') ? 'active' : 'text-secondary' ?>" href="relatorios.php<?= $query ?>">Relatórios</a>
                <a class="nav-link rounded-pill <?= ($pagina_atual == 'membros.php') ? 'active' : 'text-secondary' ?>" href="membros.php<?= $query ?>">Família</a>
            </div>
        </div>
    </div>

    <a href="nova-conta.php<?= $query ?>" class="fab">+</a>

    <div class="container">
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mt-3" role="alert">
                <?php
                    $mensagens = [
                        'sucesso' => 'Operação realizada com sucesso!',
                        #'pago' => 'Status de pagamento atualizado!',
                        'excluido' => 'Registro removido com sucesso!',
                        'membro_add' => 'Novo membro adicionado à família!',
                    ];
                    echo $mensagens[$_GET['msg']] ?? 'Ação concluída!';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>