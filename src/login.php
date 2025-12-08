<?php
require_once __DIR__ . '/helper/utils.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email === '' || $password === '') {
        $err = 'Preencha email e senha.';
    } else {
        $user = login_check($email, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            // redireciona para dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            $err = 'Email ou senha inválidos.';
        }
    }
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link rel="stylesheet" href="../public/assets/styles.css">
</head>

<body>
    <div class="box">
        <h1 style="text-align:center;border:none;">Sistema AEP</h1>
        <p style="text-align:center;color:#5e503f;margin-bottom:24px;">Achados e Perdidos</p>
        <?php if ($err): ?><div class="error"><?= e($err) ?></div><?php endif; ?>
        <form method="post">
            <label>Email
                <input name="email" type="email" required placeholder="seu@email.com">
            </label>
            <label>Senha
                <input type="password" name="password" required placeholder="••••••••">
            </label>
            <button type="submit">Entrar no Sistema</button>
        </form>
        <p style="margin-top:20px;"><a href="report.php" class= "btn-public">📊 Ver relatórios públicos</a></p>
    </div>
</body>

</html>