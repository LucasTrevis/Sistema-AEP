<?php
require_once __DIR__ . '/helper/utils.php';
require_login();

$user = $_SESSION['user'];
$db = db_connect();

// Estatísticas
$stats = [];
if ($user['role'] === 'admin') {
    $stats['categorias'] = $db->query("SELECT COUNT(*) as cnt FROM categorias")->fetch_assoc()['cnt'];
    $stats['andares'] = $db->query("SELECT COUNT(*) as cnt FROM andares")->fetch_assoc()['cnt'];
    $stats['salas'] = $db->query("SELECT COUNT(*) as cnt FROM salas")->fetch_assoc()['cnt'];
    $stats['usuarios'] = $db->query("SELECT COUNT(*) as cnt FROM usuarios")->fetch_assoc()['cnt'];
} elseif ($user['role'] === 'moderator') {
    $stats['pendentes'] = $db->query("SELECT COUNT(*) as cnt FROM postagens WHERE status='pendente'")->fetch_assoc()['cnt'];
    $stats['aprovados'] = $db->query("SELECT COUNT(*) as cnt FROM postagens WHERE status='aprovado'")->fetch_assoc()['cnt'];
    $stats['rejeitados'] = $db->query("SELECT COUNT(*) as cnt FROM postagens WHERE status='rejeitado'")->fetch_assoc()['cnt'];
    $stats['resolvidos'] = $db->query("SELECT COUNT(*) as cnt FROM postagens WHERE status='resolvido'")->fetch_assoc()['cnt'];
} else {
    $uid = (int)$user['id'];
    $stats['minhas'] = $db->query("SELECT COUNT(*) as cnt FROM postagens WHERE usuario_id=$uid")->fetch_assoc()['cnt'];
    $stats['pendentes'] = $db->query("SELECT COUNT(*) as cnt FROM postagens WHERE usuario_id=$uid AND status='pendente'")->fetch_assoc()['cnt'];
    $stats['aprovadas'] = $db->query("SELECT COUNT(*) as cnt FROM postagens WHERE usuario_id=$uid AND status='aprovado'")->fetch_assoc()['cnt'];
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema AEP</title>
    <link rel="stylesheet" href="../public/assets/styles.css">
</head>

<body>
    <div class="wrap">
        <h1>Bem-vindo, <?= e($user['nome']) ?>!</h1>
        <p class="subtitle">Perfil: <?= e($user['tipo_nome']) ?></p>

        <nav class="dashboard-nav">
            <?php if ($user['role'] === 'admin'): ?>
                <a href="admin.php" class="nav-btn">
                    <span class="nav-icon">⚙️</span>
                    <span class="nav-text">Gerenciar Sistema</span>
                </a>
            <?php elseif ($user['role'] === 'moderator'): ?>
                <a href="moderator.php" class="nav-btn">
                    <span class="nav-icon">✓</span>
                    <span class="nav-text">Moderar Postagens</span>
                </a>
            <?php else: ?>
                <a href="operator.php" class="nav-btn">
                    <span class="nav-icon">📝</span>
                    <span class="nav-text">Criar Postagem</span>
                </a>
            <?php endif; ?>

            <a href="report.php" class="nav-btn">
                <span class="nav-icon">📊</span>
                <span class="nav-text">Ver Relatórios</span>
            </a>

            <a href="logout.php" class="nav-btn nav-btn-secondary">
                <span class="nav-icon">🚪</span>
                <span class="nav-text">Sair</span>
            </a>
        </nav>

        <section class="stats">
            <h2>Estatísticas Rápidas</h2>
            <div class="stats-grid">
                <?php foreach ($stats as $label => $value): ?>
                    <div class="stat-card">
                        <div class="stat-value"><?= $value ?></div>
                        <div class="stat-label"><?= ucfirst($label) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if ($user['role'] === 'moderator'): ?>
            <section>
                <h2>Ações Rápidas</h2>
                <p>Você tem <strong><?= $stats['pendentes'] ?></strong> postagem(ns) aguardando aprovação.</p>
                <?php if ($stats['pendentes'] > 0): ?>
                    <a href="moderator.php" class="btn-action">Ver Postagens Pendentes</a>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if ($user['role'] === 'operator'): ?>
            <section>
                <h2>Ações Rápidas</h2>
                <p>Você tem <strong><?= $stats['minhas'] ?></strong> postagem(ns) cadastrada(s).</p>
                <a href="operator.php" class="btn-action">Criar Nova Postagem</a>
            </section>
        <?php endif; ?>
    </div>
</body>

</html>