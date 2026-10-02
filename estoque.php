<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mensagemAlerta = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'movimentar') {
    $produto_id = $_POST['produtos_id'];
    $tipo = $_POST['tipo']; // 1 = Entrada, 2 = Saída
    $quantidade = (int)$_POST['quantidade'];
    $data = $_POST['data'];
    $usuario_id = $_SESSION['usuario_id'];

    
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
    $stmt->execute([':id' => $produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto) {
        $saldo_anterior = $produto['estoque_atual'];
        $novo_saldo = ($tipo == 1) ? $saldo_anterior + $quantidade : $saldo_anterior - $quantidade;

        if ($novo_saldo < 0) {
            $mensagemAlerta = "ERRO: Quantidade de saída maior do que o saldo em estoque!";
        } else {
            
            $update = $pdo->prepare("UPDATE produtos SET estoque_atual = :novo_saldo WHERE id = :id");
            $update->execute([':novo_saldo' => $novo_saldo, ':id' => $produto_id]);

        
            $insert = $pdo->prepare("INSERT INTO movimentacoes (tipo, date, quantidade, saldo_anterior, usuarios_id, produtos_id) 
                                     VALUES (:tipo, :date, :quantidade, :saldo_anterior, :usuarios_id, :produtos_id)");
            $insert->execute([
                ':tipo' => $tipo,
                ':date' => $data,
                ':quantidade' => $quantidade,
                ':saldo_anterior' => $saldo_anterior,
                ':usuarios_id' => $usuario_id,
                ':produtos_id' => $produto_id
            ]);

            
            if ($novo_saldo <= $produto['estoque_minimo']) {
                $mensagemAlerta = " ALERTA: O produto '{$produto['nome']}' atingiu o estoque mínimo! Saldo atual: {$novo_saldo} (Mínimo: {$produto['estoque_minimo']}).";
            }
        }
    }
}


$stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Estoque</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <h2>Gestão de Movimentações de Estoque</h2>
    <a href="index.php">Voltar ao Menu Principal</a>

    <?php if (!empty($mensagemAlerta)): ?>
        <div style="padding: 10px; background-color: #f9f8f8; color: #000000; margin: 10px 0; border: 1px solid #9b9292;">
            <strong><?= $mensagemAlerta ?></strong>
        </div>
    <?php endif; ?>

    <hr>

    
    <h3>Registrar Movimentação</h3>
    <form method="POST" action="estoque.php">
        <input type="hidden" name="acao" value="movimentar">
        
        <label>Produtos</label><br>
        <select name="produtos_id" required>
            <option value="">Selecione um produto...</option>
            <?php foreach ($produtos as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?> (Atual: <?= $p['estoque_atual'] ?>)</option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Tipo de Movimentação:</label><br>
        <select name="tipo" required>
            <option value="1">1 - Entrada </option>
            <option value="2">2 - Saída</option>
        </select><br><br>

        <label>Quantidade:</label><br>
        <input type="number" name="quantidade" min="1" required><br><br>

        <label>Data da Operação:</label><br>
        <input type="date" name="data" value="<?= date('Y-m-d') ?>" required><br><br>

        <button type="submit">Confirmar Movimentação</button>
    </form>

    <hr>

    
    <h3>Saldos de Estoque</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Estoque Atual</th>
                <th>Estoque Mínimo</th>
                <th>Situação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $p): 
                $abaixo_minimo = $p['estoque_atual'] <= $p['estoque_minimo'];
            ?>
            <tr style="<?= $abaixo_minimo ? 'background-color: #fffff;' : '' ?>">
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td><?= $p['estoque_atual'] ?></td>
                <td><?= $p['estoque_minimo'] ?></td>
                <td>
                    <?php if ($abaixo_minimo): ?>
                        <strong style="color: black;"> ESTOQUE CRÍTICO</strong>
                    <?php else: ?>
                        <span style="color: black;">OK</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>