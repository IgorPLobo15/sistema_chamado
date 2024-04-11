# Sistema de Chamados - Documentação

### Proposta:
- Tela de login, para o usuário comum logar e depois acessar o formulário para abrir seu chamado.
- Nessa mesma área, o usuário comum deve poder consultar o andamento dos seus chamados abertos.
- Se um admin entrar com seu login, ele vai para área de administração, podendo gerenciar os chamados abertos.
- O Admin pode acessar um chamado, ter uma versão de impressão, encaminhar o chamado para algum atendente, atender o chamado, 
colocar como suspenso (justificando), tirar de suspenso, encerrar o chamado, cancelar um chamado (justificando).
- Os admins podem consultar todos os chamados abertos, filtrando por data, atendente, status.
- Um usuário comum pode reabrir um chamado fechado, porém não pode reabrir um chamado cancelado.

## Conexão com Banco de Dados (db_connection.php)
Este arquivo fornece a conexão com o banco de dados MySQL utilizado pelo sistema de chamados.

## Tela de Login (index.php)
A tela de login permite que os usuários acessem o sistema inserindo suas credenciais.

### Funcionamento:
- Os usuários inserem seu nome de usuário e senha.
- Se as credenciais estiverem corretas, eles são redirecionados para o painel correspondente (usuário comum ou administrador).
- Se as credenciais estiverem incorretas, uma mensagem de erro é exibida.

## Função de Logout (logout.php)
A tela de logout permite que os usuários saiam do sistema.

### Funcionamento:
- Ao clicar no botão de logout, a sessão do usuário é encerrada.
- Os usuários são redirecionados de volta para a tela de login.

## Painel Admin Dashboard (admin_dashboard.php)
O painel do administrador fornece acesso a várias funcionalidades administrativas, como visualização de chamados, suspensão de chamados, reabertura de chamados e outras.

### Funcionamento:
- Os administradores podem visualizar uma lista de todos os chamados e realizar ações como suspender, reabrir e cancelar chamados.
- As opções de navegação levam a páginas específicas para executar cada funcionalidade.

## Tela de Impressão de Chamado (print_ticket.php)
A tela de impressão de chamado exibe uma versão formatada do chamado para impressão.

### Funcionamento:
- Os usuários podem acessar esta página para visualizar uma versão de impressão de um chamado específico.
- Um script PHP recupera as informações do chamado do banco de dados e as exibe em um formato adequado para impressão.

## Função de Cancelar Chamado (cancel_ticket.php)
Esta função permite que os administradores cancelem um chamado específico, fornecendo uma justificativa para o cancelamento.

### Funcionamento:
- Os usuários comuns acessam o painel do usuário após o login, onde podem abrir novos chamados e visualizar os chamados abertos por eles.
- Um formulário permite que os usuários abram novos chamados, fornecendo um título e uma descrição.
- Os chamados abertos pelo usuário são exibidos em uma tabela, onde eles podem ver o título e a descrição do chamado e têm a opção de resolver o chamado.
- Um botão de logout permite que os usuários façam logout do sistema.

## Painel do Usuário (user_dashboard.php)
O painel do usuário permite que os usuários comuns visualizem os chamados abertos por eles e abram novos chamados.

### Funcionamento:
- Os administradores podem visualizar uma lista de todos os chamados e realizar ações como suspender, reabrir e cancelar chamados.
- As opções de navegação levam a páginas específicas para executar cada funcionalidade.

## Banco de dados

### Tabelas

### 1. Tabela de Usuários (`users`)

Esta tabela armazena informações sobre os usuários do sistema.

| Coluna    | Tipo          | Descrição                             |
|-----------|---------------|---------------------------------------|
| id        | int(11)       | Identificador único do usuário        |
| username  | varchar(255)  | Nome de usuário                       |
| password  | varchar(255)  | Senha do usuário                      |
| role      | enum('admin','common') | Função do usuário no sistema (admin/comum) |

### 2. Tabela de Chamados (`tickets`)

Esta tabela mantém o registro de todos os chamados no sistema.

| Coluna          | Tipo            | Descrição                                     |
|-----------------|-----------------|-----------------------------------------------|
| id              | int(11)         | Identificador único do chamado                |
| title           | varchar(255)    | Título do chamado                             |
| description     | text            | Descrição do chamado                          |
| user_id         | int(11)         | ID do usuário que criou o chamado             |
| status          | enum('open','resolved','closed','suspended','canceled') | Estado do chamado |
| created_at      | timestamp       | Data e hora de criação do chamado            |
| assigned_to     | int(11)         | ID do usuário responsável pelo chamado        |
| cancellation_reason | text         | Motivo do cancelamento do chamado (se aplicável) |

### 3. Tabela de Chamados Resolvidos (`resolved_tickets`)

Esta tabela armazena informações específicas sobre os chamados resolvidos.

| Coluna       | Tipo        | Descrição                                |
|--------------|-------------|------------------------------------------|
| id           | int(11)     | Identificador único do chamado resolvido |
| ticket_id    | int(11)     | ID do chamado associado                  |
| title        | varchar(255)| Título do chamado                        |
| observation  | text        | Observação sobre a resolução do chamado  |
| resolved_at  | timestamp   | Data e hora de resolução do chamado      |

### 4. Tabela de Chamados Fechados (`closed_tickets`)

Esta tabela armazena informações específicas sobre os chamados fechados.

| Coluna       | Tipo        | Descrição                               |
|--------------|-------------|-----------------------------------------|
| id           | int(11)     | Identificador único do chamado fechado  |
| ticket_id    | int(11)     | ID do chamado associado                 |
| title        | varchar(255)| Título do chamado                       |
| closed_at    | timestamp   | Data e hora de fechamento do chamado    |

### 5. Tabela de Logs (`logs`)

Esta tabela registra as ações realizadas no sistema.

| Coluna          | Tipo        | Descrição                                    |
|-----------------|-------------|----------------------------------------------|
| id              | int(11)     | Identificador único do log                   |
| ticket_id       | int(11)     | ID do chamado associado (se aplicável)       |
| action          | varchar(255)| Ação realizada                               |
| performed_by    | int(11)     | ID do usuário que realizou a ação            |
| performed_at    | timestamp   | Data e hora em que a ação foi realizada     |

## Restrições e Chaves Estrangeiras

- A tabela `tickets` possui chaves estrangeiras para as tabelas `users` (para o criador do chamado e o usuário responsável) e chaves estrangeiras opcionais para as tabelas `resolved_tickets` e `closed_tickets`.
- A tabela `resolved_tickets` possui uma chave estrangeira para a tabela `tickets`.
- A tabela `closed_tickets` possui uma chave estrangeira para a tabela `tickets`.
- A tabela `logs` possui chaves estrangeiras para as tabelas `tickets` (opcional) e `users`.

## Código de Criação do Banco de Dados (sistemahp)

```sql
-- Tabela de Usuários
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','common') NOT NULL DEFAULT 'common',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Chamados
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('open','resolved','closed','suspended','canceled') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_to` int(11) DEFAULT NULL,
  `cancellation_reason` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `assigned_to` (`assigned_to`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Chamados Resolvidos
CREATE TABLE `resolved_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `observation` text NOT NULL,
  `resolved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  CONSTRAINT `resolved_tickets_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Chamados Fechados
CREATE TABLE `closed_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `closed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  CONSTRAINT `closed_tickets_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Logs
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `performed_by` int(11) DEFAULT NULL,
  `performed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `performed_by` (`performed_by`),
  CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`),
  CONSTRAINT `logs_ibfk_2` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




