<?php
require_once __DIR__ . '/../database/connection.php';
session_start();

function e($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * login_check(email, password)
 * retorna array usuário (id,nome,email,tipo_id,role_nome) ou false
 * Assumimos que senha_hash foi gerada com password_hash()
 */
function login_check($email, $password)
{
    $db = db_connect();
    $sql = "SELECT u.id, u.nome, u.email, u.senha_hash, u.tipo_usuario_id, t.nome as tipo_nome
            FROM usuarios u
            LEFT JOIN tipos_usuarios t ON u.tipo_usuario_id = t.id
            WHERE u.email = ? LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if (password_verify($password, $row['senha_hash'])) {
            // normalizar role para 'admin','moderator','operator' (slug)
            $tipo = strtolower($row['tipo_nome']);
            $role = 'operator';
            if (strpos($tipo, 'admin') !== false || strpos($tipo, 'administrador') !== false) $role = 'admin';
            elseif (strpos($tipo, 'moder') !== false) $role = 'moderator';
            // retornar dados úteis
            return [
                'id' => (int)$row['id'],
                'nome' => $row['nome'],
                'email' => $row['email'],
                'tipo_usuario_id' => (int)$row['tipo_usuario_id'],
                'tipo_nome' => $row['tipo_nome'],
                'role' => $role
            ];
        }
    }
    return false;
}

function require_login()
{
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function require_role($role)
{
    require_login();
    if ($_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        die('Acesso negado.');
    }
}

/**
 * Conta itens aprovados por andar e sala (para mapa)
 */
function get_counts_by_sala_and_andar()
{
    $db = db_connect();
    $sql = "SELECT a.id as andar_id, a.nome as andar_nome, s.id as sala_id, s.nome as sala_nome,
                   COUNT(p.id) as cnt
            FROM andares a
            LEFT JOIN salas s ON s.andar_id = a.id
            LEFT JOIN postagens p ON p.sala_id = s.id AND p.status = 'aprovado'
            GROUP BY a.id, s.id
            ORDER BY a.id, s.id";
    $res = $db->query($sql);
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    return $rows;
}
