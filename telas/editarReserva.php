<?php
session_start();
require '../includes/conexao.inc.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$usuario = $_SESSION['usuario'];

// Verificar se o ID da reserva foi fornecido
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$reservaId = $_GET['id'];

// Buscar a reserva
$sql = "SELECT * FROM reserva WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $reservaId);
$stmt->execute();
$reserva = $stmt->get_result()->fetch_assoc();

if (!$reserva) {
    header('Location: dashboard.php');
    exit;
}

// Verificar permissões
if ($usuario['tipo'] !== 'A' && $usuario['id'] !== $reserva['pessoa_id']) {
    header('Location: dashboard.php');
    exit;
}

// Processar atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laboratorioId = $_POST['laboratorio_id'];
    $data = $_POST['data'];
    $horaInicio = $_POST['hora_inicio'];
    $horaFim = $_POST['hora_fim'];
    $descricao = $_POST['descricao'];

    $sql = "UPDATE reserva SET laboratorio_id = ?, data = ?, hora_inicio = ?, hora_fim = ?, descricao = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issssi', $laboratorioId, $data, $horaInicio, $horaFim, $descricao, $reservaId);
    $stmt->execute();

    $mensagem = "Reserva atualizada com sucesso!";
    header('Location: dashboard.php');
    exit;
}

// Buscar laboratórios
$sqlLaboratorios = "SELECT id, nome FROM laboratorio";
$resultLaboratorios = $conn->query($sqlLaboratorios);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/main.css">
    <title>Editar Reserva</title>
</head>
<body>
    <div class="container">
        <h1>Editar Reserva</h1>
        <?php if (isset($mensagem)) echo "<p class='mensagem'>$mensagem</p>"; ?>
        <form method="POST">
            <label>Laboratório</label>
            <select name="laboratorio_id">
                <?php while ($laboratorio = $resultLaboratorios->fetch_assoc()): ?>
                    <option value="<?php echo $laboratorio['id']; ?>" <?php echo $reserva['laboratorio_id'] == $laboratorio['id'] ? 'selected' : ''; ?>>
                        <?php echo $laboratorio['nome']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label>Data</label>
            <input type="date" name="data" value="<?php echo $reserva['data']; ?>" required>
            <label>Hora Início</label>
            <input type="time" name="hora_inicio" value="<?php echo $reserva['hora_inicio']; ?>" required>
            <label>Hora Fim</label>
            <input type="time" name="hora_fim" value="<?php echo $reserva['hora_fim']; ?>" required>
            <label>Descrição</label>
            <textarea name="descricao"><?php echo $reserva['descricao']; ?></textarea>
            <button type="submit">Atualizar</button>
        </form>
        <a href="dashboard.php">Voltar</a>
    </div>
</body>
</html>
