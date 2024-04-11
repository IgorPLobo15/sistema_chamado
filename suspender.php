<?php
// Verifica se o formulário foi enviado
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inclui o arquivo de conexão com o banco de dados
    require_once "db_connection.php";

    // Recupera a justificativa do formulário
    $justification = $_POST['justification'];

    // Recupera o ID do chamado a ser suspenso
    $ticket_id = $_GET['id']; // Certifique-se de que o ID do chamado está disponível via GET

    // Insere a justificativa no banco de dados
    $sql = "UPDATE tickets SET justifica_suspen = '$justification', status = 'suspended' WHERE id = $ticket_id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Chamado suspenso com sucesso!'); window.location.href = 'admin_dashboard.php';</script>";
    } else {
        echo "Erro ao suspender o chamado: " . $conn->error;
    }

    // Fecha a conexão com o banco de dados
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suspender Chamado</title>
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
    <div class="container">
        <h1>Suspender Chamado</h1>
        <form method="post">
            <label for="justification">Justificativa:</label>
            <textarea name="justification" id="justification" placeholder="Digite a justificativa para suspensão do chamado"></textarea>
            <input type="submit" value="Suspender Chamado">
        </form>
    </div>
</body>
</html>
