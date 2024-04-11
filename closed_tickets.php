<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado e é um usuário comum
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'common' && $_SESSION['role'] !== 'admin')) {
    // Se o usuário não estiver logado ou não for um usuário comum ou administrador, redireciona para a página de login
    header("Location: index.php");
    exit();
}


// Consulta SQL para obter os chamados fechados do usuário atual
require_once "db_connection.php"; // Inclui o arquivo de conexão com o banco de dados
$sql = "SELECT * FROM closed_tickets WHERE user_id = ".$_SESSION['user_id'];
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamados Fechados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }
        button{
            width: 150px;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Botão para voltar ao painel do usuário -->
    <a  href="user_dashboard.php"><button>Voltar ao Painel do Usuário</button></a>

    <!-- Tabela de Chamados Fechados -->
    <h2>Chamados Fechados</h2>
    <table>
        <tr>
            <th>Título</th>
            <th>Descrição</th>
            <th>Data de Fechamento</th>
            <th>Ações</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // Exibe os chamados fechados do usuário atual
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row['title']."</td>";
                echo "<td>".$row['description']."</td>";
                echo "<td>".$row['closed_at']."</td>";
                // Botão para reabrir o chamado
                echo "<td><a href='reopen_ticket.php?ticket_id=".$row['id']."'><button>Reabrir</button></a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Nenhum chamado fechado encontrado.</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
// Fecha a conexão com o banco de dados
$conn->close();
?>
