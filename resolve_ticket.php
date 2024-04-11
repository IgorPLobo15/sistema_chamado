<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado e é um administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'common') {
    // Se não for um administrador, redireciona para a página de login
    header("Location: index.php");
    exit();
}

// Verifica se o ID do chamado foi passado via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Se não houver um ID de chamado, redireciona para o painel do administrador
    header("Location: admin_dashboard.php");
    exit();
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se a observação foi preenchida
    if (empty($_POST['observation'])) {
        $error_message = "Por favor, forneça uma observação.";
    } else {
        // Inclui o arquivo de conexão com o banco de dados
        require_once "db_connection.php";

        // Recupera o ID e o título do chamado
        $ticket_id = $_GET['id'];
        $title = $_POST['title'];
        
        // Escapa os dados do formulário para prevenir SQL injection
        $observation = $conn->real_escape_string($_POST['observation']);

        // Atualiza o status do chamado para "resolvido" na tabela principal
        $sql_update_ticket = "UPDATE tickets SET status = 'resolved' WHERE id = $ticket_id";
        $conn->query($sql_update_ticket);

        // Insere a observação na tabela de chamados atendidos
        $sql_insert_observation = "INSERT INTO resolved_tickets (ticket_id, title, observation) VALUES ($ticket_id, '$title', '$observation')";
        if ($conn->query($sql_insert_observation) === TRUE) {
            // Chamado atendido com sucesso, redireciona de volta para o painel do administrador
            header("Location: admin_dashboard.php");
            exit();
        } else {
            // Erro ao inserir observação
            $error_message = "Erro ao adicionar observação: " . $conn->error;
        }

        // Fecha a conexão com o banco de dados
        $conn->close();
    }
}

// Recupera o ID do chamado da URL
$ticket_id = $_GET['id'];

// Consulta SQL para obter informações do chamado
require_once "db_connection.php"; // Inclui o arquivo de conexão com o banco de dados
$sql_get_ticket = "SELECT * FROM tickets WHERE id = $ticket_id";
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
    <title>Atender Chamado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 20px;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Atender Chamado</h1>
    <h2><?php echo $ticket['title']; ?></h2>
    <form method="post">
        <label for="observation">Observação:</label><br>
        <textarea name="observation" id="observation" rows="4" cols="50"></textarea><br>
        <input type="hidden" name="title" value="<?php echo $ticket['title']; ?>">
        <input type="submit" value="Atender Chamado">
    </form>
    <?php if(isset($error_message)) { ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php } ?>
</body>
</html>
