<?php
require '../includes/conexao.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmar_senha'];
    $tipo = $_POST['tipo']; // Campo para definir se é admin ou usuário comum

    if ($senha !== $confirmarSenha) {
        $mensagem = "As senhas não coincidem.";
    } else {
        $senhaCriptografada = password_hash($senha, PASSWORD_BCRYPT);
        $sql = "INSERT INTO pessoa (nome, email, senha, tipo) VALUES (?, ?, ?, ?)"; // Inclui o campo tipo
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $nome, $email, $senhaCriptografada, $tipo);

        if ($stmt->execute()) {
            $mensagem = "Cadastro realizado com sucesso!";
        } else {
            $mensagem = "Erro ao cadastrar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/main.css">
    <title>Cadastro - Reserva de Laboratórios</title>
</head>
<body>
    <div class="container">
        <h1>Cadastro de Usuário</h1>
        <?php if (isset($mensagem)) echo "<p class='mensagem'>$mensagem</p>"; ?>
        <form method="POST">
            <label>Nome</label>
            <input type="text" name="nome" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Senha</label>
            <input type="password" name="senha" required>
            <label>Confirmar Senha</label>
            <input type="password" name="confirmar_senha" required>
            
            <!-- Adicione uma opção para o tipo de usuário -->
            <label>Tipo de Usuário</label>
            <select name="tipo" required>
                <option value="U">Usuário Comum</option>
                <option value="A">Administrador</option>
            </select>

            <button type="submit">Cadastrar</button>
        </form>
        <a href="index.php">Voltar para login</a>
    </div>
</body>
</html>
