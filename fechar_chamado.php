<?php
// Inclua o arquivo de conexão com o banco de dados
require_once "db_connection.php";

// Verifique se o ID do chamado foi passado via GET
if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];

    // Execute a consulta para selecionar os detalhes do chamado a ser encerrado
    $sql = "SELECT * FROM tickets WHERE id = $ticket_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Movendo o chamado para a tabela de chamados encerrados
        $row = $result->fetch_assoc();
        $title = $row['title'];
        $description = $row['description'];
        $user_id = $row['user_id'];
        $created_at = $row['created_at'];

        $insert_sql = "INSERT INTO closed_tickets (title, description, user_id, created_at) VALUES ('$title', '$description', $user_id, '$created_at')";
        if ($conn->query($insert_sql) === TRUE) {
            // Remover o chamado da tabela de chamados
            $delete_sql = "DELETE FROM tickets WHERE id = $ticket_id";
            if ($conn->query($delete_sql) === TRUE) {
                // Exibir popup de alerta após o chamado ser encerrado com sucesso
                echo "<script>alert('Chamado encerrado com sucesso!'); window.location.href = 'admin_dashboard.php';</script>";
                exit(); // Certifique-se de sair do script após o redirecionamento
            } else {
                echo "Erro ao excluir o chamado: " . $conn->error;
            }
        } else {
            echo "Erro ao encerrar o chamado: " . $conn->error;
        }
    } else {
        echo "Chamado não encontrado.";
    }
} else {
    echo "ID do chamado não fornecido.";
}

// Feche a conexão com o banco de dados
$conn->close();
?>
