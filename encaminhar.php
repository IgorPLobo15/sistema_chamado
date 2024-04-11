<?php
// Inicia a sessão
session_start();

// Verifica se o usuário não está logado ou não é um administrador, se não, redireciona para a página de login
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
require_once "db_connection.php";

// Verifica se o ID do chamado foi passado via GET
if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php"); // Redireciona de volta para o dashboard do administrador se o ID do chamado não for fornecido
    exit();
}

// Obtém o ID do chamado a ser encaminhado
$ticket_id = $_GET['id'];

// Consulta para obter informações sobre o chamado
$sql = "SELECT * FROM tickets WHERE id = $ticket_id";
$result = $conn->query($sql);
if ($result->num_rows != 1) {
    // O chamado não foi encontrado, redireciona de volta para o dashboard do administrador
    header("Location: admin_dashboard.php");
    exit();
}
$ticket = $result->fetch_assoc();

// Verifica se o formulário de encaminhamento foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém o ID do novo atendente do formulário
    $new_assignee_id = $_POST['assignee'];

    // Atualiza o chamado com o novo atendente
    $update_sql = "UPDATE tickets SET assigned_to = $new_assignee_id WHERE id = $ticket_id";
    if ($conn->query($update_sql) === TRUE) {
        // Encaminhamento bem-sucedido, redireciona de volta para o dashboard do administrador
        echo "<script>alert('Chamado atribuído com sucesso!'); window.location.href = 'admin_dashboard.php';</script>";
        exit();
    } else {
        // Erro ao encaminhar o chamado
        $error_message = "Erro ao encaminhar o chamado: " . $conn->error;
    }
}

// Consulta para obter a lista de atendentes
$assignees_sql = "SELECT id, username FROM users WHERE role = 'common'";
$assignees_result = $conn->query($assignees_sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encaminhar Chamado</title>
    <!-- Estilos CSS -->
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

        select {
            width: 100%;
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
            transition: background-color 0.3s;
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
    <div class="container">
        <h1>Encaminhar Chamado</h1>
        <?php if(isset($error_message)) { ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php } ?>
        <form method="post">
            <label for="assignee">Selecione o novo atendente:</label>
            <select name="assignee" id="assignee">
                <?php while ($assignee = $assignees_result->fetch_assoc()) { ?>
                    <option value="<?php echo $assignee['id']; ?>"><?php echo $assignee['username']; ?></option>
                <?php } ?>
            </select>
            <input type="submit" value="Encaminhar">
        </form>
    </div>
</body>
</html>
