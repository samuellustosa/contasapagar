<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Contas a Pagar</title>
    <style>
        /* Estilo para deixar o botão ativo moderno */
        .nav-pills .nav-link.active {
            background-color: #0d6efd;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }
        .nav-link {
            transition: all 0.2s ease;
        }

        /* ESTILO DO BOTÃO FLUTUANTE (FAB) */
        .fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .fab:hover {
            background-color: #0b5ed7;
            transform: scale(1.1) rotate(90deg);
            color: white;
        }

        /* Esconde o botão se estiver na página de cadastro para não poluir */
        <?php $pagina_atual = basename($_SERVER['PHP_SELF']); ?>
        <?php if ($pagina_atual == 'nova-conta.php'): ?>
        .fab { display: none; }
        <?php endif; ?>
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-2 shadow-sm">
        <div class="container text-center">
            <a class="navbar-brand" href="index.php">📊 Contas a Pagar</a>
        </div>
    </nav>

    <div class="d-flex justify-content-center my-4">
        <div class="nav nav-pills bg-white p-2 rounded-pill shadow-sm">
            <a class="nav-link rounded-pill px-4 <?= ($pagina_atual == 'index.php') ? 'active' : 'text-secondary' ?>" 
               href="index.php">Mural</a>
            
            <a class="nav-link rounded-pill px-4 <?= ($pagina_atual == 'relatorios.php') ? 'active' : 'text-secondary' ?>" 
               href="relatorios.php">Relatórios</a>
            
            <a class="nav-link rounded-pill px-4 <?= ($pagina_atual == 'membros.php') ? 'active' : 'text-secondary' ?>" 
               href="membros.php">Família</a>
            
            </div>
    </div>

    <a href="nova-conta.php" class="fab" title="Nova Conta">
        +
    </a>

    <div class="container">