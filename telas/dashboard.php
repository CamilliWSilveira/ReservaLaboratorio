<?php
session_start();
require '../includes/conexao.inc.php';
require '../vendor/enviarEmail.php'; // Inclua a função de envio de e-mail

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$usuario = $_SESSION['usuario'];

// Buscar laboratórios
$sqlLaboratorios = "SELECT id, nome FROM laboratorio";
$resultLaboratorios = $conn->query($sqlLaboratorios);

// Processar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laboratorioId = $_POST['laboratorio_id'];
    $data = $_POST['data'];
    $horaInicio = $_POST['hora_inicio'];
    $horaFim = $_POST['hora_fim'];
    $descricao = $_POST['descricao'];

    // Verificar se o horário está disponível
    $sql = "SELECT * FROM reserva WHERE laboratorio_id = ? AND data = ? AND (hora_inicio < ? AND hora_fim > ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isss', $laboratorioId, $data, $horaFim, $horaInicio);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $mensagem = "Horário indisponível!";
    } else {
        $sql = "INSERT INTO reserva (pessoa_id, laboratorio_id, data, hora_inicio, hora_fim, descricao) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iissss', $usuario['id'], $laboratorioId, $data, $horaInicio, $horaFim, $descricao);
        $stmt->execute();
        $mensagem = "Reserva realizada com sucesso!";

        // Enviar e-mail de confirmação
        $sqlLaboratorio = "SELECT nome FROM laboratorio WHERE id = ?";
        $stmtLab = $conn->prepare($sqlLaboratorio);
        $stmtLab->bind_param('i', $laboratorioId);
        $stmtLab->execute();
        $laboratorio = $stmtLab->get_result()->fetch_assoc();

        // Verifique se o e-mail e o nome do usuário estão disponíveis
        if (!empty($usuario['email']) && !empty($usuario['nome'])) {
            enviarEmailReserva($usuario['email'], $usuario['nome'], $laboratorio['nome'], $data, $horaInicio, $horaFim, $descricao);
        } else {
            echo 'Informações do usuário incompletas para enviar o e-mail.';
        }
    }
}


// Buscar reservas
$sql = "SELECT r.id, r.data, r.hora_inicio, r.hora_fim, l.nome AS laboratorio_nome, p.nome AS usuario_nome, r.pessoa_id
        FROM reserva r
        JOIN laboratorio l ON r.laboratorio_id = l.id
        JOIN pessoa p ON r.pessoa_id = p.id";
$reservas = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/main.css">
    <title>Painel de Reservas</title>
</head>
<body>
    <div class="container">
        <h1>Reservas de Laboratórios</h1>
        <?php if (isset($mensagem)) echo "<p class='mensagem'>$mensagem</p>"; ?>
        <form method="POST">
            <label>Laboratório</label>
            <select name="laboratorio_id" required>
                <?php while ($laboratorio = $resultLaboratorios->fetch_assoc()): ?>
                    <option value="<?php echo $laboratorio['id']; ?>">
                        <?php echo $laboratorio['nome']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label>Data</label>
            <input type="date" name="data" required>
            <label>Hora Início</label>
            <input type="time" name="hora_inicio" required>
            <label>Hora Fim</label>
            <input type="time" name="hora_fim" required>
            <label>Descrição</label>
            <textarea name="descricao"></textarea>
            <button type="submit">Reservar</button>
        </form>

        <h2>Reservas Existentes</h2>
        <table>
            <tr>
                <th>Laboratório</th>
                <th>Data</th>
                <th>Hora Início</th>
                <th>Hora Fim</th>
                <th>Usuário</th>
                <?php if ($usuario['tipo'] === 'A'): ?>
                <th>Ações</th>
                <?php endif; ?>
            </tr>
            <?php while ($reserva = $reservas->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $reserva['laboratorio_nome']; ?></td>
                    <td><?php echo $reserva['data']; ?></td>
                    <td><?php echo $reserva['hora_inicio']; ?></td>
                    <td><?php echo $reserva['hora_fim']; ?></td>
                    <td><?php echo $reserva['usuario_nome']; ?></td>
                    <?php if ($usuario['tipo'] === 'A' || $usuario['id'] === $reserva['pessoa_id']): ?>
                    <td>
                        <a href="editarReserva.php?id=<?php echo $reserva['id']; ?>">Editar</a>
                        <a href="excluirReserva.php?id=<?php echo $reserva['id']; ?>">Excluir</a>
                    </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </table>

        <form action="sair.php" method="post">
    <button type="submit">Sair</button>
</form>

<a href="index.php">Voltar para o início</a>


    </div>
</body>
</html>
