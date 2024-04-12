<?php
// Verifica se o ID do chamado a ser reaberto foi fornecido
if(isset($_GET['ticket_id'])) {
    // ID do chamado a ser reaberto
    $ticket_id = $_GET['ticket_id'];

    // Consulta SQL para reabrir o chamado
    require_once "db_connection.php"; // Inclui o arquivo de conexão com o banco de dados
    $sql_reopen = "UPDATE closed_tickets SET closed_at = NULL WHERE id = $ticket_id";
    if ($conn->query($sql_reopen) === TRUE) {
        // Move o chamado da tabela 'closed_tickets' para 'tickets'
        $sql_move_to_open = "INSERT INTO tickets (id, title, description, user_id, created_at) 
                     SELECT id, title, description, user_id, NOW() AS created_at 
                     FROM closed_tickets WHERE id = $ticket_id";
        if ($conn->query($sql_move_to_open) === TRUE) {
            // Remove o chamado da tabela 'closed_tickets'
            $sql_remove_from_closed = "DELETE FROM closed_tickets WHERE id = $ticket_id";
            if ($conn->query($sql_remove_from_closed) === TRUE) {
                // Chamado reaberto e movido com sucesso, redireciona de volta para a página de chamados fechados
                header("Location: closed_tickets.php");
                exit();
            } else {
                echo "Erro ao remover o chamado da tabela 'closed_tickets': " . $conn->error;
            }
        } else {
            echo "Erro ao mover o chamado para a tabela 'tickets': " . $conn->error;
        }
    } else {
        echo "Erro ao reabrir o chamado: " . $conn->error;
    }
} else {
    // Se o ID do chamado não foi fornecido, redireciona para a página de chamados fechados
    header("Location: closed_tickets.php");
    exit();
}
?>
