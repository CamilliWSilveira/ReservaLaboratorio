<?php
session_start();
require '../includes/conexao.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Consulta no banco para buscar o usuário
    $sql = "SELECT id, nome, senha, tipo FROM pessoa WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    // Verifica se o usuário existe e se a senha está correta
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario'] = $usuario;  // Armazena o usuário na sessão
        
        // Atualiza o último login no banco de dados
        $sql = "UPDATE pessoa SET ultimo_login = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $usuario['id']);
        $stmt->execute();

        // Verifica o tipo de usuário
        if ($usuario['tipo'] === 'A') {
            // Se for administrador, redireciona para a página de administração (laboratório)
            header('Location: laboratorio.php');
        } else {
            // Se for usuário comum, redireciona para a página de cadastro ou usuário
            header('Location: dashboard.php');
        }
        exit;
    } else {
        // Mensagem de erro para e-mail ou senha incorretos
        $mensagem = "E-mail ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/main.css">
    <title>Login - Reserva de Laboratórios</title>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <?php if (isset($mensagem)) echo "<p class='mensagem'>$mensagem</p>"; ?>
        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Senha</label>
            <input type="password" name="senha" required>
            <button type="submit">Entrar</button>
        </form>
        <a href="cadastro.php">Cadastrar-se</a>
    </div>
</body>
</html>
