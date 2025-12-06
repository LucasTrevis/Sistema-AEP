# Documentação Técnica - Sistema AEP

## Arquitetura do Sistema

### Padrão de Desenvolvimento
O sistema segue um padrão MVC simplificado:
- **Model**: Queries SQL diretas com prepared statements
- **View**: Templates PHP com HTML/CSS
- **Controller**: Lógica de negócios nos arquivos principais (admin.php, operator.php, etc.)

### Fluxo de Autenticação

1. Usuário acessa `login.php`
2. Credenciais verificadas com `login_check()` em `utils.php`
3. Senha validada com `password_verify()`
4. Sessão criada com dados do usuário
5. Redirecionamento baseado em role (admin/moderator/operator)

### Controle de Acesso

#### Níveis de Usuário
- **Admin**: Acesso total, gerencia categorias, andares e salas
- **Moderator**: Aprova/rejeita/resolve postagens
- **Operator**: Cria postagens de achados/perdidos

#### Funções de Proteção
- `require_login()`: Verifica se usuário está autenticado
- `require_role($role)`: Verifica role específico

---

## Banco de Dados

### Tabelas Principais

#### usuarios
```sql
id INT PRIMARY KEY AUTO_INCREMENT
nome VARCHAR(255)
email VARCHAR(255) UNIQUE
senha_hash VARCHAR(255)
tipo_usuario_id INT
criado_em TIMESTAMP
```

#### tipos_usuarios
```sql
id INT PRIMARY KEY AUTO_INCREMENT
nome VARCHAR(50) -- 'Administrador', 'Moderador', 'Operador'
slug VARCHAR(50) -- 'admin', 'moderator', 'operator'
```

#### postagens
```sql
id INT PRIMARY KEY AUTO_INCREMENT
usuario_id INT
categoria_id INT
tipo ENUM('achado', 'perdido')
titulo VARCHAR(255)
descricao TEXT
andar_id INT
sala_id INT
status ENUM('pendente', 'aprovado', 'rejeitado', 'resolvido')
criado_em TIMESTAMP
resolvido_em TIMESTAMP
```

#### categorias
```sql
id INT PRIMARY KEY AUTO_INCREMENT
nome VARCHAR(100)
```

#### andares
```sql
id INT PRIMARY KEY AUTO_INCREMENT
nome VARCHAR(50)
```

#### salas
```sql
id INT PRIMARY KEY AUTO_INCREMENT
andar_id INT
nome VARCHAR(50)
```

### Relacionamentos
- postagens.usuario_id → usuarios.id
- postagens.categoria_id → categorias.id
- postagens.andar_id → andares.id
- postagens.sala_id → salas.id
- salas.andar_id → andares.id
- usuarios.tipo_usuario_id → tipos_usuarios.id

---

## Segurança

### Prevenção de Vulnerabilidades

#### SQL Injection
- **Método**: Prepared Statements
- **Exemplo**:
```php
$stmt = $db->prepare('INSERT INTO postagens (...) VALUES (?,?,?)');
$stmt->bind_param('iss', $id, $titulo, $descricao);
$stmt->execute();
```

#### XSS (Cross-Site Scripting)
- **Método**: Escape de HTML
- **Função**: `e($string)` - wrapper para `htmlspecialchars()`
- **Exemplo**:
```php
<?= e($user['nome']) ?>
```

#### Autenticação
- **Método**: password_hash() e password_verify()
- **Algoritmo**: PASSWORD_DEFAULT (bcrypt)
- **Exemplo**:
```php
$hash = password_hash($password, PASSWORD_DEFAULT);
if (password_verify($input, $hash)) { /* autenticado */ }
```

#### Sessões
- **Iniciadas**: Automaticamente em `utils.php`
- **Verificação**: Em cada página protegida
- **Destruição**: Via `logout.php`

---

## Fluxos de Trabalho

### Fluxo de Criação de Postagem

1. **Operador** acessa `operator.php`
2. Preenche formulário com:
   - Título
   - Descrição
   - Tipo (achado/perdido)
   - Categoria
   - Andar
   - Sala
3. Sistema valida campos obrigatórios
4. Postagem criada com status "pendente"
5. Mensagem de sucesso exibida

### Fluxo de Moderação

1. **Moderador** acessa `moderator.php`
2. Visualiza lista de postagens pendentes
3. Para cada postagem:
   - Aprovar: status → "aprovado"
   - Rejeitar: status → "rejeitado"
   - Resolver: status → "resolvido" + timestamp
4. Postagem atualizada no banco
5. Mensagem de confirmação exibida

### Fluxo de Relatório Público

1. Qualquer usuário acessa `report.php`
2. Sistema carrega todas as postagens aprovadas
3. Usuário pode filtrar por:
   - Tipo (achado/perdido)
   - Categoria
   - Andar
   - Sala
4. Resultados exibidos em lista
5. Mapa mostra distribuição por localização

---

## Funções Auxiliares

### utils.php

#### `e($string)`
Escapa HTML para prevenir XSS.
```php
function e($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
```

#### `login_check($email, $password)`
Valida credenciais e retorna dados do usuário.
```php
function login_check($email, $password) {
    // Busca usuário por email
    // Verifica senha com password_verify()
    // Retorna array com dados ou false
}
```

#### `require_login()`
Redireciona para login se não autenticado.

#### `require_role($role)`
Verifica role específico, retorna 403 se não autorizado.

#### `get_counts_by_sala_and_andar()`
Retorna contagem de itens por sala e andar para o mapa.

---

## Padrões de Código

### Nomenclatura
- **Variáveis**: snake_case (`$usuario_id`, `$andar_nome`)
- **Funções**: snake_case (`login_check()`, `require_role()`)
- **Arquivos**: lowercase com underscores ou sem (login.php, utils.php)
- **Classes**: PascalCase (quando implementadas no futuro)

### Estrutura de Arquivos PHP
```php
<?php
// 1. Includes e requires
require_once __DIR__ . '/helper/utils.php';

// 2. Controle de acesso
require_role('admin');

// 3. Conexão com banco
$db = db_connect();

// 4. Processamento de formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // lógica
}

// 5. Queries de consulta
$items = $db->query('SELECT ...');

// 6. Template HTML
?>
<!doctype html>
<html>
...
</html>
```

### Validação de Entrada
```php
// Sanitização
$titulo = trim($_POST['titulo'] ?? '');
$id = intval($_GET['id'] ?? 0);

// Validação
if (!$titulo) {
    $error = 'Título é obrigatório.';
}

// Whitelist
if ($tipo && in_array($tipo, ['achado', 'perdido'])) {
    // processa
}
```

---

## Melhorias Futuras

### Funcionalidades
- [ ] Upload de fotos dos itens
- [ ] Notificações por email
- [ ] Sistema de busca avançada
- [ ] Chat entre usuários
- [ ] Histórico de alterações
- [ ] API REST para integração mobile

### Técnicas
- [ ] Implementar ORM (PDO ou Eloquent)
- [ ] Adicionar testes automatizados
- [ ] Implementar cache (Redis/Memcached)
- [ ] Logs estruturados
- [ ] Rate limiting
- [ ] CSRF tokens

### Interface
- [ ] Design responsivo completo
- [ ] Tema dark mode
- [ ] Internacionalização (i18n)
- [ ] Acessibilidade (WCAG)
- [ ] Progressive Web App (PWA)

---

## Troubleshooting

### Problema: Erro de conexão com banco
**Solução**: Verificar credenciais em `src/database/connection.php`

### Problema: Sessão não persiste
**Solução**: Verificar permissões da pasta de sessão do PHP

### Problema: CSS não carrega
**Solução**: Verificar caminhos relativos (deve ser `../public/assets/styles.css`)

### Problema: Erro ao criar usuários
**Solução**: Importar o SQL primeiro, depois executar create_users.php

### Problema: 403 Forbidden
**Solução**: Verificar role do usuário na tabela tipos_usuarios

---

## Contato e Suporte

Para questões técnicas, consulte a documentação ou entre em contato com o desenvolvedor.
