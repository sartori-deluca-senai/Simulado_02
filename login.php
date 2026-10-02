<?php
require_once 'config.php';
session_start();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!empty($email) && !empty($senha)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND ativo = 1");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        
        if ($usuario && ($senha === $usuario['senha'] || password_verify($senha, $usuario['senha']))) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header("Location: index.php");
            exit;
        } else {
            $erro = "E-mail ou senha incorretos! Tente novamente.";
        }
    } else {
        $erro = "Por favor, preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Controle deEstoque Industrial</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc;">
        <h2>Sistema Industrial</h2>
        
        <?php if (!empty($erro)): ?>
            <div style="color: red; margin-bottom: 15px;">
                <strong><?= $erro ?></strong>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label>E-mail:</label><br>
            <input type="email" name="email" required style="width: 100%; margin-bottom: 10px;"><br>

            <label>Senha:</label><br>
            <input type="password" name="senha" required style="width: 100%; margin-bottom: 15px;"><br>

            <button type="submit" style="width: 100%; padding: 10px;">Entrar</button>
        </form>
    </div>
</body>
</html>