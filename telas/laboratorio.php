<?php
session_start();
require '../includes/conexao.inc.php';

// Verificar se o usuário está logado e é administrador
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] != 'A') {
    header('Location: index.php');
    exit;
}

$mensagem = '';

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $numComputadores = $_POST['num_computadores'];
    $bloco = $_POST['bloco'];
    $sala = $_POST['sala'];

    // Inserir laboratório no banco de dados
    $sql = "INSERT INTO laboratorio (nome, numero_computadores, bloco, sala) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('siss', $nome, $numComputadores, $bloco, $sala);

    if ($stmt->execute()) {
        $mensagem = "Laboratório cadastrado com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar laboratório: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/main.css">
    <title>Cadastro de Laboratórios</title>
</head>
<body>
    <div class="container">
        <h1>Cadastro de Laboratório</h1>
        <?php if (isset($mensagem)) echo "<p class='mensagem'>$mensagem</p>"; ?>
        
        <form method="POST">
            <label>Nome do Laboratório</label>
            <input type="text" name="nome" required>

            <label>Número de Computadores</label>
            <input type="number" name="num_computadores" required>

            <label>Bloco</label>
            <input type="text" name="bloco" required>

            <label>Sala</label>
            <input type="text" name="sala" required>

            <button type="submit">Cadastrar</button>
        </form>

        <a href="dashboard.php">Voltar ao Dashboard</a>
    </div>
</body>
</html>
