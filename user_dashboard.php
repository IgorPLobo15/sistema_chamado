<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado e é um usuário comum
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'common') {
    // Se o usuário não estiver logado ou não for um usuário comum, redireciona para a página de login
    header("Location: index.php");
    exit();
}

// Verifica se o formulário de abertura de chamados foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os campos título e descrição foram preenchidos
    if (empty($_POST['title']) || empty($_POST['description'])) {
        $error_message = "Por favor, preencha todos os campos.";
    } else {
        // Inclui o arquivo de conexão com o banco de dados
        require_once "db_connection.php";

        // Recupera os valores do formulário de abertura de chamados
        $title = $_POST['title'];
        $description = $_POST['description'];

        // Insere os dados do chamado no banco de dados
        $sql = "INSERT INTO tickets (user_id, title, description, status) VALUES ('".$_SESSION['user_id']."', '$title', '$description', 'open')";
        if ($conn->query($sql) === TRUE) {
            // Chamado aberto com sucesso, redireciona de volta para a página do painel do usuário
            header("Location: user_dashboard.php");
            exit();
        } else {
            // Erro ao abrir o chamado
            $error_message = "Erro ao abrir o chamado: " . $conn->error;
        }

        // Fecha a conexão com o banco de dados
        $conn->close();
    }
}

// Consulta SQL para obter todos os chamados abertos
require_once "db_connection.php"; // Inclui o arquivo de conexão com o banco de dados
$sql = "SELECT * FROM tickets WHERE status = 'open' ORDER BY created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Usuário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        textarea {
            height: 150px;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        button {
            display: block;
            width: 150px;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            text-align: center;
            margin: 0 auto;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: #ff0000;
            margin-top: 10px;
        }

        .logout-form {
            text-align: right;
        }

        .logout-form input[type="submit"] {
            background-color: #dc3545;
        }

        .logout-form input[type="submit"]:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Formulário de Abertura de Chamados -->
        <h2>Formulário de Abertura de Chamados</h2>
        <!-- Formulário para abrir um novo chamado -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="title">Título:</label><br>
            <input type="text" id="title" name="title"><br>
            <label for="description">Descrição:</label><br>
            <textarea id="description" name="description"></textarea><br>
            <input type="submit" value="Abrir Chamado">
        </form>
        <!-- Mensagem de erro, se houver -->
        <?php if(isset($error_message)) { ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php } ?>

        <!-- Consulta de Chamados Abertos -->
        <h2>Consulta de Chamados Abertos</h2>
        <a href="closed_tickets.php" class="button">Ver Chamados Fechados</a>

        <table>
            <tr>
                <th>Título</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                // Exibe os chamados abertos do usuário atual
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row['title']."</td>";
                    echo "<td>".$row['description']."</td>";
                    echo "<td>";
                    // Botão para resolver o chamado
                    echo "<a href='resolve_ticket.php?id=".$row['id']."'>Resolver Chamado</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                // Se não houver chamados abertos
                echo "<tr><td colspan='3'>Nenhum chamado aberto encontrado.</td></tr>";
            }
            ?>
        </table>

        <!-- Botão de Logout -->
        <form method="post" action="logout.php">
            <input type="submit" value="Deslogar">
        </form>
    </div>
</body>
</html>
