<?php
require_once __DIR__ . '/helper/utils.php';
require_role('moderator');

$db = db_connect();
$message = '';

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);
if ($action && $id) {
    if ($action === 'aprovar') {
        $stmt = $db->prepare("UPDATE postagens SET status='aprovado' WHERE id=?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $message = 'Postagem aprovada com sucesso!';
        }
    } elseif ($action === 'rejeitar') {
        $stmt = $db->prepare("UPDATE postagens SET status='rejeitado' WHERE id=?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $message = 'Postagem rejeitada.';
        }
    } elseif ($action === 'resolver') {
        $stmt = $db->prepare("UPDATE postagens SET status='resolvido', resolvido_em=NOW() WHERE id=?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $message = 'Postagem marcada como resolvida!';
        }
    }
    if (!$message) {
        header('Location: moderator.php');
        exit;
    }
}

$pending = $db->query("SELECT p.*, c.nome as categoria_nome, a.nome as andar_nome, s.nome as sala_nome, u.nome as usuario_nome
                       FROM postagens p
                       LEFT JOIN categorias c ON p.categoria_id=c.id
                       LEFT JOIN andares a ON p.andar_id=a.id
                       LEFT JOIN salas s ON p.sala_id=s.id
                       LEFT JOIN usuarios u ON p.usuario_id=u.id
                       WHERE p.status='pendente' ORDER BY p.criado_em ASC");

$others = $db->query("SELECT p.*, c.nome as categoria_nome FROM postagens p LEFT JOIN categorias c ON p.categoria_id=c.id WHERE p.status!='pendente' ORDER BY p.criado_em DESC");
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Moderador</title>
    <link rel="stylesheet" href="../public/assets/styles.css">
</head>

<body>
    <div class="wrap">
        <h1>Moderador - Gerenciar Postagens</h1>
        <nav class="top-nav">
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="report.php">📊 Relatórios</a>
            <a href="logout.php">🚪 Sair</a>
        </nav>
        <?php if ($message): ?>
            <div class="success-message"><?= e($message) ?></div>
        <?php endif; ?>

        <section>
            <h2>Itens Pendentes de Aprovação</h2>
            <?php if ($pending->num_rows > 0): ?>
                <ul>
                    <?php while ($p = $pending->fetch_assoc()): ?>
                        <li>
                            <strong><?= e($p['titulo']) ?></strong>
                            <span style="color:#5e503f;">(<?= e($p['tipo']) ?>)</span><br>
                            <small>
                                📍 <?= e($p['andar_nome']) ?>/<?= e($p['sala_nome']) ?> |
                                📎 <?= e($p['categoria_nome']) ?> |
                                👤 <?= e($p['usuario_nome']) ?>
                            </small><br>
                            <div style="margin-top:8px;">
                                <a href="moderator.php?action=aprovar&id=<?= e($p['id']) ?>" class="btn-action" style="background:#22a722;">✓ Aprovar</a>
                                <a href="moderator.php?action=rejeitar&id=<?= e($p['id']) ?>" class="btn-action" style="background:#c62828;">✗ Rejeitar</a>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>✓ Nenhuma postagem pendente no momento.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Itens Aprovados/Rejeitados/Resolvidos</h2>
            <?php if ($others->num_rows > 0): ?>
                <ul>
                    <?php while ($o = $others->fetch_assoc()): ?>
                        <li>
                            <strong><?= e($o['titulo']) ?></strong>
                            <span style="color:#5e503f;">[<?= e($o['status']) ?>]</span> -
                            <?= e($o['categoria_nome']) ?>
                            <?php if ($o['status'] !== 'resolvido'): ?>
                                | <a href="moderator.php?action=resolver&id=<?= e($o['id']) ?>">✓ Marcar resolvido</a>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Nenhuma postagem neste status.</p>
            <?php endif; ?>
        </section>

    </div>
</body>

</html>