<?php
require_once __DIR__ . '/helper/utils.php';
session_unset();
session_destroy();
header('Location: login.php');
exit;
