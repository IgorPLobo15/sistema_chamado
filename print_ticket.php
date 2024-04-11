<?php
// Inclui o arquivo de conexão com o banco de dados
require_once "db_connection.php";

// Verifica se o ID do chamado foi passado via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Se não houver um ID de chamado, redireciona para o painel do administrador
    header("Location: admin_dashboard.php");
    exit();
}

// Recupera o ID do chamado da URL
$ticket_id = $_GET['id'];

// Consulta SQL para obter informações do chamado, incluindo o nome do usuário atribuído
$sql_get_ticket = "SELECT t.*, u.username as assigned_to_name FROM tickets t LEFT JOIN users u ON t.assigned_to = u.id WHERE t.id = $ticket_id";
$result = $conn->query($sql_get_ticket);
$ticket = $result->fetch_assoc();

// Fecha a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Versão de Impressão do Chamado</title>
    <style>
        /* Estilos CSS para a versão de impressão */
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .ticket-info {
            margin-bottom: 20px;
        }
        .ticket-info label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Versão de Impressão do Chamado</h1>
        <div class="ticket-info">
            <label for="title">Título:</label>
            <p id="title"><?php echo $ticket['title']; ?></p>
        </div>
        <div class="ticket-info">
            <label for="description">Descrição:</label>
            <p id="description"><?php echo $ticket['description']; ?></p>
        </div>
        <div class="ticket-info">
            <label for="status">Status:</label>
            <p id="status"><?php echo $ticket['status']; ?></p>
        </div>
        <div class="ticket-info">
            <label for="assigned_to">Atribuído a:</label>
            <p id="assigned_to"><?php echo $ticket['assigned_to_name']; ?></p>
        </div>
        <div class="ticket-info">
            <label for="created_at">Data de Criação:</label>
            <p id="created_at"><?php echo $ticket['created_at']; ?></p>
        </div>
    </div>
    <script>
        // Função JavaScript para imprimir a página
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
