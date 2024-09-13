<?php
session_start(); // Inicia a sessão para que possamos usar variáveis de sessão

require '../includes/conexao.inc.php'; // Inclui o arquivo de conexão com o banco de dados

// Verifica se o método de requisição é POST, indicando que o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email']; 
    $senha = $_POST['senha']; 

    // Consulta no banco para buscar o usuário com o e-mail fornecido
    $sql = "SELECT id, nome, senha, tipo FROM pessoa WHERE email = ?";
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param('s', $email);
    $stmt->execute(); 
    $result = $stmt->get_result(); 
    $usuario = $result->fetch_assoc();

    // Verifica se o usuário existe e se a senha fornecida corresponde à senha armazenada (usando password_verify para verificar senhas hasheadas)
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario'] = $usuario;  
        
        // Atualiza o último login do usuário no banco de dados com a data e hora atuais
        $sql = "UPDATE pessoa SET ultimo_login = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param('i', $usuario['id']); 
        $stmt->execute(); 

        // Verifica o tipo de usuário para redirecionar para a página apropriada
        if ($usuario['tipo'] === 'A') {
           
            header('Location: laboratorio.php');
        } else {
            
            header('Location: dashboard.php');
        }
        exit; 
    } else {
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
        <h1>Seja bem vindo(a)!</h1> 
        <?php if (isset($mensagem)) echo "<p class='mensagem'>$mensagem</p>"; ?> 
        <form method="POST"> 
            <label>Email</label> 
            <input type="email" name="email" required> 
            <label>Senha</label> 
            <input type="password" name="senha" required> 
            <button type="submit">Entrar</button> 
        </form>
        <a href="cadastro.php">Não tem uma conta? Cadastrar-se</a> 
    </div>
</body>
</html>
