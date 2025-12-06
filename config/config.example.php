<?php

/**
 * Arquivo de configuração de exemplo
 * Copie este arquivo para config.php e ajuste as configurações conforme necessário
 */

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'achados_perdidos');
define('DB_CHARSET', 'utf8mb4');

// Configurações de Sessão
define('SESSION_NAME', 'AEP_SESSION');
define('SESSION_LIFETIME', 3600); // 1 hora em segundos

// Configurações de Ambiente
define('ENVIRONMENT', 'development'); // development ou production
define('DEBUG_MODE', true); // true para desenvolvimento, false para produção

// Configurações de Upload (para futuras implementações)
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB em bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Configurações de Email (para futuras implementações)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@example.com');
define('SMTP_PASS', 'password');
define('SMTP_FROM', 'noreply@example.com');
define('SMTP_FROM_NAME', 'Sistema AEP');

// URLs do Sistema
define('BASE_URL', 'http://localhost/PHPLTP/Sistema%20AEP/');
define('PUBLIC_URL', BASE_URL . 'public/');
define('ASSETS_URL', PUBLIC_URL . 'assets/');

// Timezone
date_default_timezone_set('America/Sao_Paulo');
