# Sistema de Achados e Perdidos - AEP

Este projeto implementa um sistema completo de Achados e Perdidos para instituições educacionais, com foco em organização, aprovação de postagens e rastreamento preciso de objetos.

---

## Funcionalidades Principais

### 1. Administração
- Cadastro e gerenciamento de:
  - Categorias de objetos
  - Salas
  - Andares
- Controle estrutural do ambiente físico utilizado pelas postagens

### 2. Moderação
- Aprovação ou rejeição de novas postagens
- Marcação de itens como **resolvidos** (objeto recuperado)
- Curadoria geral para manter as informações consistentes e confiáveis

### 3. Operações
- Criação de postagens de itens **achados** ou **perdidos**
- Associação dos itens à sala, andar e categoria correspondente
- Anexação de informações adicionais (descrição, horário aproximado, etc.)

---

## Mapa e Localização

O sistema apresenta um **mapa aproximado da planta baixa**, exibindo:
- Salas cadastradas
- Andares vinculados
- Posição aproximada do local onde o item foi perdido/encontrado

Essa visualização auxilia usuários e moderadores a localizar rapidamente ambientes relacionados ao item.

---

## Consultas e Relatórios

O sistema permite filtros e extração de relatórios por:
- Objeto
- Tipo/Categoria
- Sala
- Andar
- Status (pendente, resolvido)
- Intervalo de datas

Esses relatórios podem ser utilizados para análises internas, controle de fluxo e auditoria.

---

## Instalação e Configuração

### Pré-requisitos
- XAMPP (ou outro servidor Apache + MySQL + PHP)
- PHP 7.4 ou superior
- MySQL 5.7 ou superior

### Passos de Instalação

1. **Clone ou copie o projeto** para a pasta do XAMPP:
   ```
   d:\XAMPP\htdocs\PHPLTP\Sistema AEP\
   ```

2. **Configure o banco de dados**:
   - Acesse o phpMyAdmin: `http://localhost/phpmyadmin`
   - Crie um banco de dados chamado `achados_perdidos`
   - Importe o arquivo SQL: `src/database/achados_perdidos.sql`

3. **Configure a conexão**:
   - Edite o arquivo `src/database/connection.php`
   - Ajuste as credenciais se necessário:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'achados_perdidos');
     ```

4. **Crie os usuários do sistema**:
   - Acesse: `http://localhost/PHPLTP/Sistema%20AEP/src/scripts/create_users.php`
   - Execute o script **apenas uma vez**
   - **IMPORTANTE**: Delete o arquivo `create_users.php` por segurança após a execução

5. **Acesse o sistema**:
   - URL principal: `http://localhost/PHPLTP/Sistema%20AEP/public/`
   - Credenciais padrão:
     - **Admin**: admin@example.com / admin
     - **Moderador**: moderator@example.com / moderator
     - **Operador**: operator@example.com / operator

---

## Estrutura de Arquivos

```
Sistema AEP/
├── config/                  # Configurações adicionais
├── docs/                    # Documentação
├── public/                  # Pasta pública (ponto de entrada)
│   ├── index.php           # Redirecionamento inicial
│   └── assets/
│       └── styles.css      # Estilos CSS
├── src/                    # Código-fonte principal
│   ├── admin.php           # Painel administrativo
│   ├── moderator.php       # Painel de moderação
│   ├── operator.php        # Painel do operador
│   ├── report.php          # Relatórios públicos
│   ├── login.php           # Página de login
│   ├── logout.php          # Logout
│   ├── db.php              # [DEPRECADO] Use connection.php
│   ├── database/
│   │   ├── connection.php  # Conexão com banco de dados
│   │   └── achados_perdidos.sql  # Script SQL
│   ├── helper/
│   │   └── utils.php       # Funções auxiliares
│   └── scripts/
│       └── create_users.php  # Criar usuários iniciais
├── estrutura_projeto.md    # Estrutura do projeto
└── Readme.md              # Este arquivo
```

---

## Objetivos do Projeto

- Facilitar o processo de registro e recuperação de objetos
- Melhorar a comunicação entre alunos, funcionários e setores administrativos
- Reduzir o tempo de resolução de casos de perda
- Garantir um fluxo seguro e verificável das informações registradas

---

## Segurança

- Todas as senhas são armazenadas com hash usando `password_hash()`
- Validação de sessão em todas as páginas protegidas
- Prepared statements para prevenir SQL injection
- Escape de HTML para prevenir XSS
- Controle de acesso baseado em roles (admin, moderator, operator)

---

## Manutenção

### Alteração de Credenciais do Banco
Edite o arquivo `src/database/connection.php` com as novas credenciais.

### Adicionar Novos Usuários
Acesse o banco de dados diretamente ou crie um painel de gerenciamento de usuários no admin.

### Backup
Faça backup regularmente do banco de dados `achados_perdidos` através do phpMyAdmin.

---

## Suporte e Contribuições

Para reportar problemas ou sugerir melhorias, entre em contato com o desenvolvedor do projeto.

