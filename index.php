<?php
require_once 'config.php';
session_start();

// Encerrar sessão (Logout)
if (isset($_GET['acao']) && $_GET['acao'] === 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}


$stmt = $pdo->query("SELECT COUNT(*) AS total_critico FROM produtos WHERE estoque_atual <= estoque_minimo AND ativo = 1");
$criticos = $stmt->fetch(PDO::FETCH_ASSOC)['total_critico'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Principal - Gestão de Estoque</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div style="padding: 20px;">
        <header >
            <h1>SISTEMA DE CONTROLE DE ESTOQUE INDUSTRIAL</h1>
            <div>
                <span>Utilizador: <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong></span> | 
                <a href="index.php?acao=logout" style="color: red; font-weight: bold;">Sair</a>
            </div>
        </header>

        <main style="margin-top: 30px;">
            <h2>Painel de Gestão</h2>

            <?php if ($criticos > 0): ?>    
            <?php endif; ?>

            <div>
                <div >
                    <a href="produtos.php"><button > Cadastro</button></a>
                </div>

                <div>
                    <a href="estoque.php"><button>Movimentações</button></a>
                </div>

            </div>
            <br>
            <div style="background-color: #cecccc; color: #000000; padding: 15px; border: 1px solid #f9f9f9; margin-bottom: 20px;">
             <strong>ATENÇÃO:</strong> Existem <strong><?= $criticos ?></strong> rescursos com estoque abaixo ou igual ao limite mínimo!
             </div>
        </main>
    </div>
</body>
</html>