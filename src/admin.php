<?php
require_once __DIR__ . '/helper/utils.php';
require_role('admin');

$db = db_connect();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    if ($type === 'categoria') {
        $nome = trim($_POST['nome'] ?? '');
        if ($nome) {
            $stmt = $db->prepare('INSERT INTO categorias (nome) VALUES (?)');
            $stmt->bind_param('s', $nome);
            if ($stmt->execute()) {
                $message = 'Categoria adicionada com sucesso!';
            }
        }
    } elseif ($type === 'andar') {
        $nome = trim($_POST['nome'] ?? '');
        if ($nome) {
            $stmt = $db->prepare('INSERT INTO andares (nome) VALUES (?)');
            $stmt->bind_param('s', $nome);
            if ($stmt->execute()) {
                $message = 'Andar adicionado com sucesso!';
            }
        }
    } elseif ($type === 'sala') {
        $nome = trim($_POST['nome'] ?? '');
        $andar_id = intval($_POST['andar_id'] ?? 0);
        if ($nome && $andar_id) {
            $stmt = $db->prepare('INSERT INTO salas (andar_id, nome) VALUES (?,?)');
            $stmt->bind_param('is', $andar_id, $nome);
            if ($stmt->execute()) {
                $message = 'Sala adicionada com sucesso!';
            }
        }
    }
    if (!$message) {
        header('Location: admin.php');
        exit;
    }
}

$cats = $db->query('SELECT * FROM categorias ORDER BY id DESC');
$andares = $db->query('SELECT * FROM andares ORDER BY id');
$salas = $db->query('SELECT s.*, a.nome as andar_nome FROM salas s LEFT JOIN andares a ON s.andar_id=a.id ORDER BY s.id');
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin</title>
    <link rel="stylesheet" href="../public/assets/styles.css">
</head>

<body>
    <div class="wrap">
        <h1>Admin - Painel de Administração</h1>
        <nav class="top-nav">
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="report.php">📊 Relatórios</a>
            <a href="logout.php">🚪 Sair</a>
        </nav>
        <?php if ($message): ?>
            <div class="success-message"><?= e($message) ?></div>
        <?php endif; ?>

        <section>
            <h2>Categorias</h2>
            <form method="post">
                <input type="hidden" name="type" value="categoria">
                <label>Nome da Categoria
                    <input name="nome" required placeholder="Ex: Eletrônicos, Documentos...">
                </label>
                <button>➕ Adicionar Categoria</button>
            </form>
            <ul>
                <?php
                $cats2 = db_connect()->query('SELECT * FROM categorias ORDER BY id DESC');
                if ($cats2->num_rows > 0):
                    while ($c = $cats2->fetch_assoc()):
                ?>
                        <li><?= e($c['nome']) ?></li>
                    <?php endwhile;
                else: ?>
                    <li>Nenhuma categoria cadastrada.</li>
                <?php endif; ?>
            </ul>
        </section>

        <section>
            <h2>Andares</h2>
            <form method="post">
                <input type="hidden" name="type" value="andar">
                <label>Nome do Andar
                    <input name="nome" required placeholder="Ex: Térreo, 1º Andar...">
                </label>
                <button>➕ Adicionar Andar</button>
            </form>
            <ul>
                <?php
                $andares2 = db_connect()->query('SELECT * FROM andares ORDER BY id');
                if ($andares2->num_rows > 0):
                    while ($a = $andares2->fetch_assoc()):
                ?>
                        <li><?= e($a['nome']) ?></li>
                    <?php endwhile;
                else: ?>
                    <li>Nenhum andar cadastrado.</li>
                <?php endif; ?>
            </ul>
        </section>

        <section>
            <h2>Salas</h2>
            <form method="post">
                <input type="hidden" name="type" value="sala">
                <div class="form-grid">
                    <label>Nome da Sala
                        <input name="nome" required placeholder="Ex: Sala 101, Biblioteca...">
                    </label>
                    <label>Andar
                        <select name="andar_id" required>
                            <option value="">Selecione...</option>
                            <?php
                            $db2 = db_connect();
                            $res = $db2->query('SELECT * FROM andares');
                            while ($row = $res->fetch_assoc())
                                echo '<option value="' . (int)$row['id'] . '">' . e($row['nome']) . '</option>';
                            ?>
                        </select>
                    </label>
                </div>
                <button>➕ Adicionar Sala</button>
            </form>
            <ul>
                <?php
                $salas2 = db_connect()->query('SELECT s.*, a.nome as andar_nome FROM salas s LEFT JOIN andares a ON s.andar_id=a.id ORDER BY s.id');
                if ($salas2->num_rows > 0):
                    while ($s = $salas2->fetch_assoc()):
                ?>
                        <li><?= e($s['nome']) ?> <span style="color:#5e503f;">(<?= e($s['andar_nome']) ?>)</span></li>
                    <?php endwhile;
                else: ?>
                    <li>Nenhuma sala cadastrada.</li>
                <?php endif; ?>
            </ul>
        </section>

    </div>
</body>

</html>