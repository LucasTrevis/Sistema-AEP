<?php
require_once __DIR__ . '/helper/utils.php';
$db = db_connect();

$tipo = $_GET['tipo'] ?? '';
$categoria = intval($_GET['categoria'] ?? 0);
$andar = intval($_GET['andar'] ?? 0);
$sala = intval($_GET['sala'] ?? 0);

$where = [];
if ($tipo && in_array($tipo, ['achado', 'perdido'])) {
    $where[] = "p.tipo = '" . $db->real_escape_string($tipo) . "'";
}
if ($categoria) $where[] = "p.categoria_id = " . (int)$categoria;
if ($andar) $where[] = "p.andar_id = " . (int)$andar;
if ($sala) $where[] = "p.sala_id = " . (int)$sala;

// Adicionar filtro para mostrar apenas itens aprovados no mapa
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) . " AND p.status = 'aprovado'" : "WHERE p.status = 'aprovado'";

$sql = "SELECT p.*, c.nome as categoria_nome, a.nome as andar_nome, s.nome as sala_nome, u.nome as usuario_nome
        FROM postagens p
        LEFT JOIN categorias c ON p.categoria_id=c.id
        LEFT JOIN andares a ON p.andar_id=a.id
        LEFT JOIN salas s ON p.sala_id=s.id
        LEFT JOIN usuarios u ON p.usuario_id=u.id
        $where_sql
        ORDER BY p.criado_em DESC";

$items = $db->query($sql);
$cats = $db->query('SELECT * FROM categorias');
$andares = $db->query('SELECT * FROM andares');
$salas = $db->query('SELECT * FROM salas');

$map_rows = get_counts_by_sala_and_andar();
$map_by_andar = [];
foreach ($map_rows as $m) {
    $fid = $m['andar_id'] ?: 0;
    if (!isset($map_by_andar[$fid])) $map_by_andar[$fid] = ['andar_nome' => $m['andar_nome'], 'salas' => []];
    $map_by_andar[$fid]['salas'][] = $m;
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
    <link rel="stylesheet" href="../public/assets/styles.css">
</head>

<body>
    <div class="wrap">
        <h1>Relatório - Achados e Perdidos</h1>
        <nav class="top-nav">
            <a href="login.php">🔐 Fazer Login</a>
            <?php if (isset($_SESSION['user'])): ?>
                <a href="dashboard.php">🏠 Dashboard</a>
                <a href="logout.php">🚪 Sair</a>
            <?php endif; ?>
        </nav>

        <section>
            <h2>Filtros de Pesquisa</h2>
            <form method="get">
                <label>Tipo
                    <select name="tipo">
                        <option value="">Todos</option>
                        <option value="achado" <?= ($tipo == 'achado') ? 'selected' : '' ?>>Achado</option>
                        <option value="perdido" <?= ($tipo == 'perdido') ? 'selected' : '' ?>>Perdido</option>
                    </select>
                </label>
                <label>Categoria
                    <select name="categoria">
                        <option value="0">Todas</option>
                        <?php
                        $cats2 = db_connect()->query('SELECT * FROM categorias');
                        while ($c = $cats2->fetch_assoc()):
                        ?>
                            <option value="<?= e($c['id']) ?>" <?= ($categoria == $c['id']) ? 'selected' : '' ?>>
                                <?= e($c['nome']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </label>
                <label>Andar
                    <select name="andar">
                        <option value="0">Todos</option>
                        <?php
                        $fa = db_connect()->query('SELECT * FROM andares');
                        while ($ff = $fa->fetch_assoc()):
                        ?>
                            <option value="<?= e($ff['id']) ?>" <?= ($andar == $ff['id']) ? 'selected' : '' ?>>
                                <?= e($ff['nome']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </label>
                <label>Sala
                    <select name="sala">
                        <option value="0">Todas</option>
                        <?php
                        $fs = db_connect()->query('SELECT * FROM salas');
                        while ($rr = $fs->fetch_assoc()):
                        ?>
                            <option value="<?= e($rr['id']) ?>" <?= ($sala == $rr['id']) ? 'selected' : '' ?>>
                                <?= e($rr['nome']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </label>
                <button>🔍 Filtrar</button>
            </form>
        </section>

        <section>
            <h2>Resultados (<?= $items->num_rows ?> encontrados)</h2>
            <ul>
                <?php if ($items->num_rows > 0): ?>
                    <?php while ($it = $items->fetch_assoc()): ?>
                        <li>
                            <strong><?= e($it['titulo']) ?></strong>
                            <span style="color:#5e503f;">(<?= e($it['tipo']) ?>)</span><br>
                            <small>
                                📦 <?= e($it['categoria_nome']) ?> |
                                📍 <?= e($it['andar_nome']) ?>/<?= e($it['sala_nome']) ?> |
                                Status: <strong><?= e($it['status']) ?></strong>
                            </small>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>Nenhum resultado encontrado com os filtros aplicados.</li>
                <?php endif; ?>
            </ul>
        </section>

        <section>
            <h2>Mapa de Localização (Itens Aprovados)</h2>
            <?php if (count($map_by_andar) > 0): ?>
                <?php foreach ($map_by_andar as $fid => $data): ?>
                    <?php if (!empty($data['salas'])): ?>
                        <h3>📍 <?= e($data['andar_nome']) ?></h3>
                        <ul>
                            <?php foreach ($data['salas'] as $rm): ?>
                                <?php if ($rm['cnt'] > 0): ?>
                                    <li>
                                        <strong><?= e($rm['sala_nome']) ?></strong> -
                                        <?= e($rm['cnt']) ?> item(ns) aprovado(s) -
                                        <a href="report.php?andar=<?= e($fid) ?>&sala=<?= e($rm['sala_id']) ?>">🔍 Ver itens</a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum item aprovado cadastrado ainda no sistema.</p>
            <?php endif; ?>
        </section>
    </div>
</body>

</html>