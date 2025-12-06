<?php

/**
 * Arquivo de conexão com o banco de dados
 * Configurações podem ser ajustadas conforme o ambiente
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'achados_perdidos');

/**
 * Estabelece conexão com o banco de dados MySQL
 * @return mysqli Objeto de conexão
 */
function db_connect()
{
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        die('Erro ao conectar ao banco de dados: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}
