<?php
// ATENÇÃO: execute este arquivo uma vez e depois REMOVA por segurança.
// Ex.: abrir http://localhost/.../src/scripts/create_users.php

require_once __DIR__ . '/../database/connection.php';
$db = db_connect();

echo '<h2>Criando estrutura do sistema...</h2>';

// 1. Verificar e criar tipos de usuários
$res = $db->query("SELECT COUNT(*) as c FROM tipos_usuarios");
$r = $res->fetch_assoc();

if ($r['c'] == 0) {
    echo '<p>Criando tipos de usuários...</p>';
    $tipos = [
        'Administrador',
        'Moderador',
        'Operador',
    ];

    $stmt = $db->prepare("INSERT INTO tipos_usuarios (nome) VALUES (?)");
    foreach ($tipos as $tipo) {
        $stmt->bind_param('s', $tipo);
        $stmt->execute();
    }
    echo '<p style="color:green;">✓ Tipos de usuários criados com sucesso!</p>';
} else {
    echo '<p style="color:orange;">⚠ Tipos de usuários já existem.</p>';
}

// 2. Verificar e criar usuários
$res = $db->query("SELECT COUNT(*) as c FROM usuarios");
$r = $res->fetch_assoc();

if ($r['c'] > 0) {
    echo '<p style="color:red;">✗ Usuários já existem. Remova este arquivo por segurança.</p>';
    exit;
}

echo '<p>Criando usuários padrão...</p>';

// inserir usuários: admin / moderator / operator
$users = [
    ['Admin', 'admin@example.com', 'admin', 1],
    ['Moderador', 'moderator@example.com', 'moderator', 2],
    ['Operador', 'operator@example.com', 'operator', 3],
];

$stmt = $db->prepare("INSERT INTO usuarios (nome,email,senha_hash,tipo_usuario_id) VALUES (?,?,?,?)");
foreach ($users as $u) {
    $hash = password_hash($u[2], PASSWORD_DEFAULT);
    $stmt->bind_param('sssi', $u[0], $u[1], $hash, $u[3]);
    $stmt->execute();
    echo '<p style="color:green;">✓ Usuário criado: ' . htmlspecialchars($u[1]) . ' / ' . htmlspecialchars($u[2]) . '</p>';
}

echo '<hr>';
echo '<h3 style="color:green;">✓ Sistema configurado com sucesso!</h3>';
echo '<p><strong>Credenciais de acesso:</strong></p>';
echo '<ul>';
echo '<li>Admin: admin@example.com / admin</li>';
echo '<li>Moderador: moderator@example.com / moderator</li>';
echo '<li>Operador: operator@example.com / operator</li>';
echo '</ul>';
echo '<p style="color:red;"><strong>IMPORTANTE: DELETE ESTE ARQUIVO AGORA POR SEGURANÇA!</strong></p>';
echo '<p><a href="../../public/" style="padding:10px 20px;background:#4CAF50;color:white;text-decoration:none;border-radius:4px;">Acessar o Sistema</a></p>';
