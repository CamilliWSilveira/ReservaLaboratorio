<?php
// Parâmetros de conexão com o banco de dados
$servername = "127.0.0.1:33306";
$username = "root";
$password = "";
$dbname = "reserva";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
