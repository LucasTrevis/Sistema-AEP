# Estrutura Recomendada de Pastas para o Projeto (PHP + Front + Back Juntos)

Este documento descreve, de forma detalhada, como organizar um projeto simples em **PHP**, com **backend** e **frontend** no mesmo repositório.  
A proposta é direta, adequada para equipes pequenas e execução local.

---

## 1. Visão Geral da Estrutura

```
/projeto
│
├── /public
│     ├── index.php
│     ├── /assets
│     │      ├── /css
│     │      ├── /js
│     │      └── /img
│     └── .htaccess
│
├── /src
│     ├── /Controllers
│     ├── /Models
│     ├── /Views
│     ├── /Database
│     └── /Helpers
│
├── /config
│     └── config.php
│
├── /vendor
│
├── composer.json
└── README.md
```

---

## 2. Explicação Detalhada de Cada Pasta

### `/public/`
É o único diretório acessível pelo navegador.  
Tudo que o usuário vê e carrega vem daqui.

- **index.php**  
  Arquivo inicial do projeto. Recebe todas as requisições e repassa para o controlador correto.

- **/assets**  
  Armazena arquivos estáticos:
  - `/css` → estilos
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
