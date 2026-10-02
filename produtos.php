<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $codigo = $_POST['codigo'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $fabricante = $_POST['fabricante'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $preco = $_POST['preco'] ?? 0;
    $estoque_atual = $_POST['estoque_atual'] ?? 0;
    $estoque_minimo = $_POST['estoque_minimo'] ?? 0;
    $material = $_POST['material_fabricacao'] ?? '';

    if (!empty($codigo) && !empty($nome) && !empty($fabricante)) {
        $sql = "INSERT INTO produtos (codigo, nome, fabricante, categoria, preco, estoque_atual, estoque_minimo, material_fabricacao) 
                VALUES (:codigo, :nome, :fabricante, :categoria, :preco, :estoque_atual, :estoque_minimo, :material)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':codigo' => $codigo,
            ':nome' => $nome,
            ':fabricante' => $fabricante,
            ':categoria' => $categoria,
            ':preco' => $preco,
            ':estoque_atual' => $estoque_atual,
            ':estoque_minimo' => $estoque_minimo,
            ':material' => $material
        ]);
        header("Location: produtos.php?sucesso=1");
        exit;
    }
}


$busca = $_GET['busca'] ?? '';
$sql = "SELECT * FROM produtos WHERE nome LIKE :busca OR codigo LIKE :busca OR categoria LIKE :busca ORDER BY nome ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':busca' => "%$busca%"]);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produtos</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <h2>Cadastro e Consulta de Recursos</h2>
    <a href="index.php">Voltar</a>

    
    <form method="GET" action="produtos.php">
        <input type="text" name="busca" placeholder="Buscar por código, nome ou categoria..." value="<?= htmlspecialchars($busca) ?>">
        <button type="submit">Buscar</button>
    </form>

    <hr>

    
    <h3>Novo Recurso</h3>
    <form method="POST" action="produtos.php">
        <input type="hidden" name="acao" value="cadastrar">
        <input type="text" name="codigo" placeholder="Código (ex: MAT-001)" required>
        <input type="text" name="nome" placeholder="Nome do Insumo" required>
        <input type="text" name="fabricante" placeholder="Fabricante / Fornecedor" required>
        <input type="text" name="categoria" placeholder="Categoria (ex: Chapas, Fixadores)" required>
        <input type="number" step="0.01" name="preco" placeholder="Preço (R$)" required>
        <input type="number" name="estoque_atual" placeholder="Estoque Inicial" required>
        <input type="number" name="estoque_minimo" placeholder="Estoque Mínimo" required>
        <input type="text" name="material_fabricacao" placeholder="Material (ex: Aço Carbono 1020)">
        <button type="submit">Salvar Insumo</button>
    </form>

    <hr>

    
    <h3>Lista de Recursos Cadastrados</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Fabricante</th>
                <th>Categoria</th>
                <th>Material</th>
                <th>Preço</th>
                <th>Estoque Atual</th>
                <th>Estoque Mínimo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['codigo']) ?></td>
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td><?= htmlspecialchars($p['fabricante']) ?></td>
                <td><?= htmlspecialchars($p['categoria']) ?></td>
                <td><?= htmlspecialchars($p['material_fabricacao']) ?></td>
                <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                <td><?= $p['estoque_atual'] ?></td>
                <td><?= $p['estoque_minimo'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>