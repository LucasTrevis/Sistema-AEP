# 🚀 Guia Rápido de Início - Sistema AEP

## ⚡ Início Rápido em 5 Minutos

### 1️⃣ Configurar Banco de Dados (2 min)
```
1. Abra phpMyAdmin: http://localhost/phpmyadmin
2. Crie banco: achados_perdidos
3. Importe: src/database/achados_perdidos.sql
```

### 2️⃣ Criar Usuários (1 min)
```
1. Acesse: http://localhost/PHPLTP/Sistema%20AEP/src/scripts/create_users.php
2. Execute UMA VEZ
3. DELETE o arquivo create_users.php
```

### 3️⃣ Acessar Sistema (2 min)
```
URL: http://localhost/PHPLTP/Sistema%20AEP/public/

LOGINS PADRÃO:
┌─────────────┬──────────────────────────┬──────────┐
│ Perfil      │ Email                    │ Senha    │
├─────────────┼──────────────────────────┼──────────┤
│ Admin       │ admin@example.com        │ admin    │
│ Moderador   │ moderator@example.com    │ moderator│
│ Operador    │ operator@example.com     │ operator │
└─────────────┴──────────────────────────┴──────────┘
```

---

## 📋 Fluxo de Uso

### Como Operador 👤
1. Login → operator@example.com / operator
2. Criar Postagem → Preencher formulário
3. Aguardar aprovação do moderador

### Como Moderador 👮
1. Login → moderator@example.com / moderator
2. Ver itens pendentes
3. Aprovar/Rejeitar/Resolver postagens

### Como Admin 👑
1. Login → admin@example.com / admin
2. Cadastrar:
   - Categorias (Eletrônicos, Documentos, etc.)
   - Andares (Térreo, 1º andar, etc.)
   - Salas (101, 102, Biblioteca, etc.)

---

## 🔧 Configuração Opcional

### Alterar Credenciais do Banco
```php
// Edite: src/database/connection.php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'achados_perdidos');
```

---

## ❓ Problemas Comuns

### ❌ CSS não carrega
**Causa**: Caminho incorreto  
**Solução**: Já corrigido! Deve funcionar.

### ❌ Erro de conexão
**Causa**: Banco não criado  
**Solução**: Criar banco e importar SQL

### ❌ Usuários não existem
**Causa**: Script não executado  
**Solução**: Executar create_users.php

### ❌ Página em branco
**Causa**: Erro PHP  
**Solução**: Verificar logs do Apache/PHP

---

## 📞 Precisa de Ajuda?

1. **Documentação Completa**: `docs/DOCUMENTACAO_TECNICA.md`
2. **README Detalhado**: `Readme.md`
3. **Changelog**: `docs/CHANGELOG.md`

---

## ✅ Checklist de Instalação

- [ ] XAMPP iniciado (Apache + MySQL)
- [ ] Banco `achados_perdidos` criado
- [ ] SQL importado
- [ ] Usuários criados via script
- [ ] Script create_users.php DELETADO
- [ ] Sistema acessível via navegador
- [ ] Login funcionando
- [ ] CSS carregando corretamente

---

**Pronto! Sistema configurado e funcionando! 🎉**
