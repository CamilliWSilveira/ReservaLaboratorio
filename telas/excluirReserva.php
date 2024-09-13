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

// Excluir reserva
$sql = "DELETE FROM reserva WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $reservaId);
$stmt->execute();

$mensagem = "Reserva excluída com sucesso!";
header('Location: dashboard.php');
exit;
?>
