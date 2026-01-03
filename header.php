<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0d6efd">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Contas a Pagar</title>
    <style>
        /* Ajuste para o conteúdo não ficar escondido sob o header fixo */
        body {
            padding-top: 70px; /* Altura aproximada do navbar */
        }

        /* Mantém o menu de pílulas fixo logo abaixo do navbar azul */
        .sticky-menu {
            position: sticky;
            top: 72px; /* Ajuste conforme a altura exata do seu navbar */
            z-index: 1020;
            background-color: #f8f9fa; /* Mesma cor do bg-light */
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
        
        <?php $pagina_atual = basename($_SERVER['PHP_SELF']); ?>
        <?php if ($pagina_atual == 'nova-conta.php'): ?>
            .fab { display: none; }
        <?php endif; ?>
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
                <a class="nav-link rounded-pill <?= ($pagina_atual == 'index.php') ? 'active' : 'text-secondary' ?>" href="index.php">Mural</a>
                <a class="nav-link rounded-pill <?= ($pagina_atual == 'relatorios.php') ? 'active' : 'text-secondary' ?>" href="relatorios.php">Relatórios</a>
                <a class="nav-link rounded-pill <?= ($pagina_atual == 'membros.php') ? 'active' : 'text-secondary' ?>" href="membros.php">Família</a>
            </div>
        </div>
    </div>

    <a href="nova-conta.php" class="fab">+</a>

    <div class="container">