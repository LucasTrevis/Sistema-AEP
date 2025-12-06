<?php
// Página pública principal - redireciona para relatório público ou dashboard
session_start();

if (isset($_SESSION['user'])) {
    // Usuário logado - redireciona para dashboard
    header('Location: ../src/dashboard.php');
} else {
    // Usuário não logado - redireciona para relatórios públicos
    header('Location: ../src/report.php');
}
exit;
