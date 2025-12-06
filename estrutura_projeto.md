# Estrutura do Projeto - Sistema AEP (Achados e Perdidos)

## Visão Geral

Projeto PHP simples com backend e frontend integrados.  
Adequado para equipes pequenas e execução local em XAMPP.

---

## Estrutura Atual de Pastas

```
Sistema AEP/
│
├── .git/                           # Controle de versão Git
├── .gitignore                      # Arquivos ignorados pelo Git
│
├── config/                         # Configurações
│   └── config.example.php         # Exemplo de configuração
│
├── docs/                           # Documentação
│   ├── CHANGELOG.md               # Histórico de alterações
│   ├── DOCUMENTACAO_TECNICA.md    # Documentação técnica completa
│   ├── GUIA_RAPIDO.md            # Guia rápido de início
│   └── verificar.php             # Script de verificação do sistema
│
├── public/                         # Pasta pública (acessível pelo navegador)
│   ├── index.php                  # Redirecionamento inicial
│   └── assets/
│       └── styles.css             # Estilos CSS do sistema
│
├── src/                           # Código-fonte principal
│   ├── admin.php                  # Painel administrativo
│   ├── moderator.php              # Painel de moderação
│   ├── operator.php               # Painel do operador
│   ├── report.php                 # Relatórios públicos
│   ├── login.php                  # Página de login
│   ├── logout.php                 # Processamento de logout
│   ├── db.php                     # [DEPRECADO] Conexão antiga
│   │
│   ├── database/                  # Banco de dados
│   │   ├── connection.php        # Conexão centralizada
│   │   └── achados_perdidos.sql  # Script de criação do BD
│   │
│   ├── helper/                    # Funções auxiliares
│   │   └── utils.php             # Funções de utilidade
│   │
│   └── scripts/                   # Scripts auxiliares
│       └── create_users.php      # Criar usuários iniciais [REMOVER EM PRODUÇÃO]
│
├── estrutura_projeto.md           # Este arquivo
└── Readme.md                      # Documentação principal
```

---

## Explicação Detalhada de Cada Pasta

### `/public/`
**Pasta pública - única acessível pelo navegador**

- **index.php**  
  Ponto de entrada inicial. Redireciona para `report.php`.

- **/assets/**  
  Arquivos estáticos (CSS, JS, imagens)
  - `styles.css` → Estilos do sistema completo

### `/src/`
**Código-fonte da aplicação**

- **Páginas principais:**
  - `login.php` → Autenticação de usuários
  - `logout.php` → Encerramento de sessão
  - `admin.php` → Gerenciamento de categorias, andares e salas
  - `moderator.php` → Aprovação/rejeição de postagens
  - `operator.php` → Criação de postagens de achados/perdidos
  - `report.php` → Visualização pública de relatórios

- **/database/**
  - `connection.php` → Conexão centralizada com MySQL
  - `achados_perdidos.sql` → Script de criação das tabelas

- **/helper/**
  - `utils.php` → Funções auxiliares:
    - `e()` → Escape de HTML
    - `login_check()` → Validação de login
    - `require_login()` → Proteção de páginas
    - `require_role()` → Controle de acesso por perfil

- **/scripts/**
  - `create_users.php` → Script para criar usuários iniciais
    - ⚠️ **IMPORTANTE**: Executar apenas uma vez e depois REMOVER

### `/config/`
**Configurações do sistema**

- `config.example.php` → Exemplo de configuração
  - Copiar para `config.php` em produção
  - Ajustar credenciais do banco
  - Configurar ambiente

### `/docs/`
**Documentação do projeto**

- `CHANGELOG.md` → Histórico de todas as alterações
- `DOCUMENTACAO_TECNICA.md` → Documentação técnica detalhada
- `GUIA_RAPIDO.md` → Início rápido em 5 minutos
- `verificar.php` → Script de verificação de integridade

---

## Fluxo de Navegação

### Fluxo Público (Não Autenticado)
```
1. public/index.php
   ↓
2. src/report.php (relatórios públicos)
   ↓
3. src/login.php (para acessar áreas protegidas)
```

### Fluxo Admin
```
1. src/login.php (admin@example.com)
   ↓
2. src/admin.php
   ├── Cadastrar categorias
   ├── Cadastrar andares
   └── Cadastrar salas
```

### Fluxo Moderador
```
1. src/login.php (moderator@example.com)
   ↓
2. src/moderator.php
   ├── Ver postagens pendentes
   ├── Aprovar postagens
   ├── Rejeitar postagens
   └── Marcar como resolvido
```

### Fluxo Operador
```
1. src/login.php (operator@example.com)
   ↓
2. src/operator.php
   ├── Criar nova postagem
   ├── Escolher tipo (achado/perdido)
   ├── Selecionar categoria
   ├── Selecionar localização
   └── Ver minhas postagens
```

---

## Arquivos por Funcionalidade

### Autenticação e Sessão
- `src/login.php` → Formulário e validação
- `src/logout.php` → Encerramento de sessão
- `src/helper/utils.php` → Funções de autenticação

### Gestão de Dados
- `src/admin.php` → CRUD de categorias, andares, salas
- `src/operator.php` → CRUD de postagens
- `src/moderator.php` → Aprovação de postagens

### Visualização
- `src/report.php` → Relatórios e mapa público
- `public/assets/styles.css` → Estilos visuais

### Banco de Dados
- `src/database/connection.php` → Conexão MySQL
- `src/database/achados_perdidos.sql` → Estrutura do BD

---

## Padrões de Código

### Estrutura de Arquivo PHP
```php
<?php
// 1. Includes
require_once __DIR__ . '/helper/utils.php';

// 2. Controle de acesso
require_role('admin');

// 3. Conexão
$db = db_connect();

// 4. Processamento POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // lógica
}

// 5. Queries
$items = $db->query('SELECT ...');

// 6. Template HTML
?>
<!doctype html>
<html>
...
</html>
```

### Segurança Implementada
- ✅ Prepared Statements (SQL Injection)
- ✅ password_hash/verify (Senhas)
- ✅ htmlspecialchars (XSS)
- ✅ Controle de sessão
- ✅ Validação de entrada
- ✅ Controle de acesso por perfil

---

## Tecnologias Utilizadas

### Backend
- **PHP 7.4+** → Linguagem principal
- **MySQL 5.7+** → Banco de dados
- **MySQLi** → Driver de conexão

### Frontend
- **HTML5** → Estrutura
- **CSS3** → Estilos
- **JavaScript** → (Mínimo, para futuras melhorias)

### Servidor
- **Apache** → Servidor web (XAMPP)
- **phpMyAdmin** → Gerenciamento do BD

---

## Arquivos Sensíveis (Não Versionar)

⚠️ **Adicionar ao .gitignore:**
```
config/config.php
src/database/connection.php
uploads/
*.log
src/scripts/create_users.php
```

---

## Próximas Melhorias Sugeridas

### Estrutura
- [ ] Separar views em arquivos individuais
- [ ] Implementar autoloader
- [ ] Adicionar pasta `/tests` para testes
- [ ] Criar pasta `/logs` para arquivos de log

### Funcionalidades
- [ ] Upload de imagens
- [ ] Sistema de notificações
- [ ] API REST
- [ ] Painel de estatísticas

### Arquitetura
- [ ] Migrar para MVC completo
- [ ] Implementar ORM
- [ ] Adicionar cache
- [ ] Implementar filas

---

## Comandos Úteis

### Iniciar XAMPP
```bash
# Windows
C:\xampp\xampp-control.exe
```

### Acessar MySQL
```bash
mysql -u root -p
```

### Backup do Banco
```bash
mysqldump -u root -p achados_perdidos > backup.sql
```

### Restaurar Banco
```bash
mysql -u root -p achados_perdidos < backup.sql
```

---

## Links Importantes

- **Sistema**: http://localhost/PHPLTP/Sistema%20AEP/public/
- **phpMyAdmin**: http://localhost/phpmyadmin
- **Verificação**: http://localhost/PHPLTP/Sistema%20AEP/docs/verificar.php

---

## Contato e Suporte

Para questões sobre a estrutura do projeto, consulte:
- `docs/DOCUMENTACAO_TECNICA.md`
- `docs/GUIA_RAPIDO.md`
- `Readme.md`

---

**Última atualização**: 06/12/2025
  - Configurar ambiente
  - `/js` → scripts
  - `/img` → imagens e ícones

- **.htaccess**  
  Utilizado para redirecionamento de rotas (quando se quer URLs amigáveis).  
  Mesmo projetos simples costumam usar este arquivo para direcionar todas as requisições para `index.php`.

---

### `/src/`
Todo o código-fonte principal do sistema fica aqui.

#### `/Controllers`
Responsáveis por:
- receber requisições
- chamar modelos
- enviar dados para views
- controlar fluxo da aplicação

Ex.: `ItemController.php`, `UsuarioController.php`, `CategoriaController.php`.

#### `/Models`
Contém a lógica de acesso aos dados:
- CRUD
- consultas
- regras relacionadas às entidades

Ex.: `Item.php`, `Categoria.php`, `Usuario.php`.

#### `/Views`
Contém os arquivos que produzem HTML, normalmente organizados por módulos.  
Mesmo sem framework, separar HTML do restante deixa o projeto mais limpo.

Ex.:
```
/Views
   /achados
      list.php
      detalhe.php
      criar.php
```

#### `/Database`
Responsável por:
- conexão com banco
- migrações simples (se existirem)
- scripts SQL de inicialização

Arquivos comuns:
- `Connection.php`  
- `schema.sql`

#### `/Helpers`
Funções utilitárias:
- validações
- formatação
- funções repetidas reutilizáveis

---

### `/config/`
Armazena configurações do sistema:
- credenciais do banco
- variáveis de ambiente básicas
- flags do sistema

Arquivo geral:
- `config.php`

---

### `/vendor/`
Gerado automaticamente pelo Composer.  
Armazena dependências externas (bibliotecas).

Nunca editar arquivos aqui manualmente.

---

### `composer.json`
Usado para autoload e dependências do PHP.

Exemplo simples:
```json
{
    "autoload": {
        "psr-4": {
            "App\": "src/"
        }
    }
}
```

Isso permite usar classes com namespace assim:
```php
use App\Controllers\ItemController;
```

---

## 3. Fluxo do Sistema (Como os Arquivos Trabalham Juntos)

### 1. O usuário acessa o navegador
Exemplo:  
`http://localhost/achados/listar`

### 2. O `.htaccess` redireciona a rota para `public/index.php`
Mesmo URL diferentes caem sempre no `index.php`, que decide qual controlador usar.

### 3. `index.php` identifica:
- qual página o usuário quer
- qual controlador chamar
- qual método executar

### 4. O controlador chama um modelo
Exemplo:
```php
$itens = ItemModel->listar();
```

### 5. O controlador envia os dados para uma View
```php
require '../src/Views/achados/list.php';
```

### 6. A View monta o HTML usando os dados fornecidos

---

## 4. Motivos para Usar Essa Estrutura

- Facilita entendimento entre vários desenvolvedores.
- Evita arquivos soltos sem organização.
- Permite expandir o projeto futuramente.
- Mantém separação clara:
  - **controladores = lógica de rota**
  - **modelos = banco de dados**
  - **views = html**
- Suporte nativo a autoload do Composer.
- Permite crescer para um MVC mais robusto no futuro.

---

## 5. Como iniciar o projeto

### Inicializar o Composer
```
composer init
```

### Criar autoload
Adicionar no `composer.json`:
```json
"autoload": {
    "psr-4": {
        "App\": "src/"
    }
}
```

### Atualizar autoload
```
composer dump-autoload
```

### Rodar servidor local PHP
Dentro da pasta do projeto:
```
php -S localhost:8000 -t public
```

---

## 6. Boas práticas recomendadas

- Nunca misturar HTML diretamente com regras de banco nos mesmos arquivos.
- Criar controladores pequenos e focados.
- Criar modelos para cada entidade (Item, Categoria, Usuário, Andar).
- Criar helpers apenas para funções que realmente se repetem.
- Sempre usar `require` organizados, preferindo autoload do Composer.

---

## 7. Conclusão

Essa estrutura é simples, organizada e totalmente adequada para um sistema de Achados e Perdidos com:
- cadastro de salas, andares e categorias
- postagem de achados e perdidos
- autenticação básica
- geração de relatórios
- funcionamento local
