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

// Função para filtrar os chamados com base nos critérios fornecidos
function filterTickets($conn, $date, $assignee, $status) {
    $sql = "SELECT t.id, t.title, t.description, t.status, u.username as assigned_to, t.created_at FROM tickets t LEFT JOIN users u ON t.assigned_to = u.id WHERE 1=1";
    if (!empty($date)) {
        $sql .= " AND DATE(t.created_at) = '$date'";
    }
    if (!empty($assignee)) {
        $sql .= " AND t.assigned_to = $assignee";
    }
    if (!empty($status)) {
        $sql .= " AND t.status = '$status'";
    }
    $result = $conn->query($sql);
    return $result;
}
$sql_users = "SELECT id, username FROM users";
$result_users = $conn->query($sql_users);

// Filtra os chamados com base nos critérios fornecidos (se houver)
$date = isset($_GET['date']) ? $_GET['date'] : '';
$assignee = isset($_GET['assignee']) ? $_GET['assignee'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$filteredTickets = filterTickets($conn, $date, $assignee, $status);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Administrador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            margin-right: 10px;
            font-weight: bold;
        }

        input[type="date"],
        select,
        input[type="submit"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .actions a {
            text-decoration: none;
            color: #007bff;
            margin-right: 5px;
        }

        .actions a:hover {
            text-decoration: underline;
        }

        .logout-form {
            text-align: right;
        }

        .logout-form input[type="submit"] {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .logout-form input[type="submit"]:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <h1>Dashboard do Administrador</h1>
    <!-- Botão de logout -->
    <form class="logout-form" action="logout.php" method="post">
        <input type="submit" value="Deslogar">
    </form>
    <form method="get">
        <label for="date">Data:</label>
        <input type="date" id="date" name="date" value="<?php echo $date; ?>">
        <label for="assignee">Atendente:</label>
        <select name="assignee" id="assignee" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px;">
            <option value="">Todos</option>
            <?php
            // Loop através dos resultados da consulta de usuários e exibe cada um como uma opção no menu suspenso
            while ($row_user = $result_users->fetch_assoc()) {
                echo '<option value="' . $row_user['id'] . '"';
                // Verifica se o usuário selecionado no filtro corresponde ao usuário atual no loop e marca como selecionado, se for o caso
                if ($assignee == $row_user['id']) {
                    echo ' selected';
                }
                echo '>' . $row_user['username'] . '</option>';
            }
            ?>
</select>
        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="">Todos</option>
            <option value="open" <?php if ($status == 'open') echo 'selected'; ?>>Aberto</option>
            <option value="assigned" <?php if ($status == 'assigned') echo 'selected'; ?>>Atribuído</option>
            <option value="suspended" <?php if ($status == 'suspended') echo 'selected'; ?>>Suspenso</option>
            <option value="closed" <?php if ($status == 'closed') echo 'selected'; ?>>Fechado</option>
            <option value="canceled" <?php if ($status == 'canceled') echo 'selected'; ?>>Cancelado</option>
        </select>
        <input type="submit" value="Filtrar">
    </form>
    <a href="closed_tickets.php" class="button">Ver Chamados Fechados</a>

    <!-- Tabela de chamados -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descrição</th>
                <th>Status</th>
                <th>Atribuído a</th>
                <th>Data de Criação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $filteredTickets->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['assigned_to']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td class="actions">
                    <a href="print_ticket.php?id=<?php echo $row['id']; ?>">Versão de Impressão</a>
                    <a href="encaminhar.php?id=<?php echo $row['id']; ?>">Encaminhar</a>
                    <a href="resolve_ticket.php?id=<?php echo $row['id']; ?>">Atender</a>
                    <?php if ($row['status'] == 'suspended') { ?>
                        <a href="remover_suspensao.php?id=<?php echo $row['id']; ?>">Remover Suspensão</a>
                    <?php } else { ?>
                        <!-- Botão de suspender apenas visível se o chamado não estiver suspenso -->
                        <a href="suspender.php?id=<?php echo $row['id']; ?>">Suspender</a>
                    <?php } ?>
                    <a href="fechar_chamado.php?id=<?php echo $row['id']; ?>">Encerrar</a>
                    <a href="cancel_ticket.php?id=<?php echo $row['id']; ?>">Cancelar</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
