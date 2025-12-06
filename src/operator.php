<?php
require_once __DIR__ . '/helper/utils.php';
require_role('operator');

$db = db_connect();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $tipo = $_POST['tipo'] ?? 'achado';
    $categoria = intval($_POST['categoria'] ?? 0);
    $andar = intval($_POST['andar'] ?? 0);
    $sala = intval($_POST['sala'] ?? 0);
    $usuario_id = $_SESSION['user']['id'];

    if (!$titulo) {
        $error = 'Título é obrigatório.';
    } elseif (!$descricao) {
        $error = 'Descrição é obrigatória.';
    } elseif (!$categoria) {
        $error = 'Categoria é obrigatória.';
    } else {
        $stmt = $db->prepare('INSERT INTO postagens (usuario_id, categoria_id, tipo, titulo, descricao, andar_id, sala_id, status) VALUES (?,?,?,?,?,?,?,\'pendente\')');
        $stmt->bind_param('iissiii', $usuario_id, $categoria, $tipo, $titulo, $descricao, $andar, $sala);
        if ($stmt->execute()) {
            $message = 'Postagem criada com sucesso e aguardando moderação!';
        } else {
            $error = 'Erro ao criar postagem.';
        }
    }
}

$cats = $db->query('SELECT * FROM categorias');
$andares = $db->query('SELECT * FROM andares');
$allSalas = $db->query('SELECT s.*, a.nome as andar_nome FROM salas s LEFT JOIN andares a ON s.andar_id=a.id ORDER BY s.andar_id, s.nome');
$salasJson = [];
while ($sala = $allSalas->fetch_assoc()) {
    $salasJson[(int)$sala['andar_id']][] = $sala;
}
$myitems = $db->query('SELECT p.*, c.nome as categoria_nome, a.nome as andar_nome, s.nome as sala_nome
                       FROM postagens p
                       LEFT JOIN categorias c ON p.categoria_id=c.id
                       LEFT JOIN andares a ON p.andar_id=a.id
                       LEFT JOIN salas s ON p.sala_id=s.id
                       WHERE p.usuario_id = ' . (int)$_SESSION['user']['id'] . ' ORDER BY p.criado_em DESC');
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operador</title>
    <link rel="stylesheet" href="../public/assets/styles.css">
</head>

<body>
    <div class="wrap">
        <h1>Operador - Criar e Gerenciar Postagens</h1>
        <nav class="top-nav">
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="report.php">📊 Relatórios</a>
            <a href="logout.php">🚪 Sair</a>
        </nav>
        <?php if ($message): ?>
            <div class="success-message"><?= e($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= e($error) ?></div>
        <?php endif; ?>

        <section>
            <h2>Criar Nova Postagem</h2>
            <form method="post">
                <div class="form-grid">
                    <label class="form-full">
                        Título
                        <input name="titulo" required placeholder="Ex: Carteira preta encontrada">
                    </label>

                    <label class="form-full">
                        Descrição
                        <textarea name="descricao" required placeholder="Descreva o item em detalhes..."></textarea>
                    </label>

                    <label>
                        Tipo
                        <select name="tipo">
                            <option value="achado">Achado</option>
                            <option value="perdido">Perdido</option>
                        </select>
                    </label>

                    <label>
                        Categoria
                        <select name="categoria" required>
                            <option value="">Selecione...</option>
                            <?php
                            $cats2 = db_connect()->query('SELECT * FROM categorias');
                            while ($c = $cats2->fetch_assoc()):
                            ?>
                                <option value="<?= e($c['id']) ?>"><?= e($c['nome']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </label>

                    <label>
                        Andar
                        <select name="andar" id="andar" required>
                            <option value="">Selecione...</option>
                            <?php
                            $andares2 = db_connect()->query('SELECT * FROM andares');
                            while ($a = $andares2->fetch_assoc()):
                            ?>
                                <option value="<?= e($a['id']) ?>"><?= e($a['nome']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </label>

                    <label>
                        Sala
                        <select name="sala" id="sala" required>
                            <option value="">Selecione o andar primeiro...</option>
                        </select>
                    </label>
                </div>
                <button type="submit">✓ Criar Postagem</button>
            </form>
        </section>

        <section>
            <h2>Minhas Postagens</h2>
            <?php if ($myitems->num_rows > 0): ?>
                <ul>
                    <?php while ($it = $myitems->fetch_assoc()): ?>
                        <li>
                            <strong><?= e($it['titulo']) ?></strong>
                            <span style="color:#5e503f;">[<?= e($it['status']) ?>]</span> -
                            <?= e($it['tipo']) ?> -
                            <?= e($it['categoria_nome']) ?> -
                            <?= e($it['andar_nome']) ?>/<?= e($it['sala_nome']) ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Você ainda não criou nenhuma postagem.</p>
            <?php endif; ?>
        </section>

    </div>

    <script>
        // Filtro dinâmico de salas por andar
        const salasData = <?= json_encode($salasJson) ?>;
        const andarSelect = document.getElementById('andar');
        const salaSelect = document.getElementById('sala');

        andarSelect.addEventListener('change', function() {
            const andarId = parseInt(this.value);
            salaSelect.innerHTML = '<option value="">Selecione...</option>';

            if (andarId && salasData[andarId]) {
                salasData[andarId].forEach(function(sala) {
                    const option = document.createElement('option');
                    option.value = sala.id;
                    option.textContent = sala.nome;
                    salaSelect.appendChild(option);
                });
            } else {
                salaSelect.innerHTML = '<option value="">Selecione o andar primeiro...</option>';
            }
        });
    </script>
</body>

</html>