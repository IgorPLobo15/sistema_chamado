<?php
// Verifica se o ID do chamado foi fornecido via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID do chamado não fornecido.";
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
require_once "db_connection.php";

// Recupera o ID do chamado da URL
$ticket_id = $_GET['id'];

// Atualiza o status do chamado no banco de dados para remover a suspensão
$sql = "UPDATE tickets SET status = 'open' WHERE id = $ticket_id AND status = 'suspended'";
if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Supensão removida com sucesso!'); window.location.href = 'admin_dashboard.php';</script>";
} else {
    echo "Erro ao remover a suspensão do chamado: " . $conn->error;
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
