<?php
// Inclua o arquivo de conexão com o banco de dados
require_once "db_connection.php";

// Verifique se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupere os dados do formulário
    $ticket_id = $_POST['ticket_id'];
    $reason = $_POST['reason'];

    // Atualize o chamado no banco de dados com a justificativa de cancelamento
    $sql = "UPDATE tickets SET status = 'canceled', cancellation_reason = '$reason' WHERE id = $ticket_id";
    if ($conn->query($sql) === TRUE) {
        echo "Chamado cancelado com sucesso!";
    } else {
        echo "Erro ao cancelar o chamado: " . $conn->error;
    }
} else {
    echo "Método inválido de requisição.";
}

// Feche a conexão com o banco de dados
$conn->close();
?>
