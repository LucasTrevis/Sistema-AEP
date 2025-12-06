<?php

/**
 * Script de Verificação de Integridade do Sistema AEP
 * Execute este arquivo para verificar se tudo está configurado corretamente
 * Acesse: http://localhost/PHPLTP/Sistema%20AEP/docs/verificar.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$checks = [];
$errors = [];
$warnings = [];

// Função auxiliar para adicionar verificação
function addCheck($name, $status, $message, $type = 'info')
{
    global $checks, $errors, $warnings;
    $checks[] = [
        'name' => $name,
        'status' => $status,
        'message' => $message,
        'type' => $type
    ];
    if (!$status && $type === 'error') {
        $errors[] = $name;
    }
    if (!$status && $type === 'warning') {
        $warnings[] = $name;
    }
}

// 1. Verificar estrutura de diretórios
$basePath = dirname(__DIR__);
$requiredDirs = [
    'config',
    'docs',
    'public',
    'public/assets',
    'src',
    'src/database',
    'src/helper',
    'src/scripts'
];

foreach ($requiredDirs as $dir) {
    $fullPath = $basePath . '/' . $dir;
    $exists = is_dir($fullPath);
    addCheck(
        "Diretório: $dir",
        $exists,
        $exists ? "✓ Encontrado" : "✗ Não encontrado",
        'error'
    );
}

// 2. Verificar arquivos essenciais
$requiredFiles = [
    'src/database/connection.php' => 'Conexão com banco de dados',
    'src/helper/utils.php' => 'Funções auxiliares',
    'public/assets/styles.css' => 'Arquivo CSS',
    'public/index.php' => 'Página inicial',
    'src/login.php' => 'Página de login',
    'src/admin.php' => 'Painel admin',
    'src/moderator.php' => 'Painel moderador',
    'src/operator.php' => 'Painel operador',
    'src/report.php' => 'Relatórios'
];

foreach ($requiredFiles as $file => $description) {
    $fullPath = $basePath . '/' . $file;
    $exists = file_exists($fullPath);
    addCheck(
        "$description ($file)",
        $exists,
        $exists ? "✓ Encontrado" : "✗ Não encontrado",
        'error'
    );
}

// 3. Verificar arquivo de criação de usuários (deve ser removido em produção)
$createUsersFile = $basePath . '/src/scripts/create_users.php';
$createUsersExists = file_exists($createUsersFile);
addCheck(
    "Script create_users.php",
    !$createUsersExists,
    $createUsersExists ? "⚠ AVISO: Remova este arquivo em produção!" : "✓ Removido (seguro)",
    'warning'
);

// 4. Verificar conexão com banco de dados
try {
    require_once $basePath . '/src/database/connection.php';
    $db = db_connect();
    addCheck(
        "Conexão com banco de dados",
        true,
        "✓ Conectado com sucesso",
        'info'
    );

    // 5. Verificar tabelas
    $requiredTables = [
        'usuarios',
        'tipos_usuarios',
        'postagens',
        'categorias',
        'andares',
        'salas'
    ];

    foreach ($requiredTables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        $exists = $result && $result->num_rows > 0;
        addCheck(
            "Tabela: $table",
            $exists,
            $exists ? "✓ Existe" : "✗ Não encontrada",
            'error'
        );
    }

    // 6. Verificar usuários
    $usersResult = $db->query("SELECT COUNT(*) as cnt FROM usuarios");
    if ($usersResult) {
        $row = $usersResult->fetch_assoc();
        $hasUsers = $row['cnt'] > 0;
        addCheck(
            "Usuários cadastrados",
            $hasUsers,
            $hasUsers ? "✓ {$row['cnt']} usuário(s) encontrado(s)" : "✗ Nenhum usuário. Execute create_users.php",
            'warning'
        );
    }

    $db->close();
} catch (Exception $e) {
    addCheck(
        "Conexão com banco de dados",
        false,
        "✗ Erro: " . $e->getMessage(),
        'error'
    );
}

// 7. Verificar permissões de escrita (para uploads futuros)
$uploadDir = $basePath . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$writable = is_writable($uploadDir);
addCheck(
    "Permissões de escrita (uploads)",
    $writable,
    $writable ? "✓ Diretório gravável" : "⚠ Sem permissão de escrita",
    'warning'
);

// 8. Verificar versão do PHP
$phpVersion = phpversion();
$phpOk = version_compare($phpVersion, '7.4', '>=');
addCheck(
    "Versão do PHP",
    $phpOk,
    $phpOk ? "✓ PHP $phpVersion" : "✗ PHP $phpVersion (requer 7.4+)",
    'error'
);

// 9. Verificar extensões PHP
$requiredExtensions = ['mysqli', 'session', 'json'];
foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    addCheck(
        "Extensão PHP: $ext",
        $loaded,
        $loaded ? "✓ Carregada" : "✗ Não encontrada",
        'error'
    );
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de Integridade - Sistema AEP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .summary {
            display: flex;
            justify-content: space-around;
            padding: 30px;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .summary-item {
            text-align: center;
        }

        .summary-item .number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-item .label {
            color: #666;
            font-size: 14px;
        }

        .success {
            color: #4CAF50;
        }

        .error-text {
            color: #f44336;
        }

        .warning-text {
            color: #ff9800;
        }

        .checks {
            padding: 30px;
        }

        .check-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            border-left: 4px solid #ddd;
            background: #f8f9fa;
        }

        .check-item.success {
            border-left-color: #4CAF50;
            background: #f1f8f4;
        }

        .check-item.error {
            border-left-color: #f44336;
            background: #ffebee;
        }

        .check-item.warning {
            border-left-color: #ff9800;
            background: #fff8e1;
        }

        .check-icon {
            font-size: 24px;
            margin-right: 15px;
            min-width: 30px;
        }

        .check-content {
            flex: 1;
        }

        .check-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .check-message {
            font-size: 14px;
            color: #666;
        }

        .footer {
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 2px solid #e9ecef;
            text-align: center;
            color: #666;
        }

        .actions {
            padding: 20px 30px;
            background: #fff;
            border-top: 2px solid #e9ecef;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 5px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #45a049;
        }

        .btn-secondary {
            background: #667eea;
        }

        .btn-secondary:hover {
            background: #5568d3;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Verificação de Integridade</h1>
            <p>Sistema de Achados e Perdidos - AEP</p>
        </div>

        <div class="summary">
            <div class="summary-item">
                <div class="number success"><?= count(array_filter($checks, function ($c) {
                                                return $c['status'];
                                            })) ?></div>
                <div class="label">Verificações OK</div>
            </div>
            <div class="summary-item">
                <div class="number error-text"><?= count($errors) ?></div>
                <div class="label">Erros Críticos</div>
            </div>
            <div class="summary-item">
                <div class="number warning-text"><?= count($warnings) ?></div>
                <div class="label">Avisos</div>
            </div>
            <div class="summary-item">
                <div class="number"><?= count($checks) ?></div>
                <div class="label">Total de Verificações</div>
            </div>
        </div>

        <div class="checks">
            <?php foreach ($checks as $check): ?>
                <?php
                $class = $check['status'] ? 'success' : $check['type'];
                $icon = $check['status'] ? '✓' : ($check['type'] === 'error' ? '✗' : '⚠');
                ?>
                <div class="check-item <?= $class ?>">
                    <div class="check-icon"><?= $icon ?></div>
                    <div class="check-content">
                        <div class="check-name"><?= htmlspecialchars($check['name']) ?></div>
                        <div class="check-message"><?= htmlspecialchars($check['message']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="actions">
            <a href="../public/" class="btn">Acessar Sistema</a>
            <a href="../docs/GUIA_RAPIDO.md" class="btn btn-secondary">Ver Guia Rápido</a>
            <a href="javascript:location.reload()" class="btn btn-secondary">Verificar Novamente</a>
        </div>

        <div class="footer">
            <p>Sistema AEP - Versão 1.0 | <?= date('d/m/Y H:i:s') ?></p>
            <?php if (count($errors) === 0): ?>
                <p style="color: #4CAF50; font-weight: bold; margin-top: 10px;">
                    ✓ Sistema pronto para uso!
                </p>
            <?php else: ?>
                <p style="color: #f44336; font-weight: bold; margin-top: 10px;">
                    ✗ Corrija os erros críticos antes de usar o sistema
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>