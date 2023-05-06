<?php
if (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

session_start();

// Удаляем переменную authorized из сессии
unset($_SESSION["authorized"]);

// Перенаправляем на страницу авторизации
header("Location: /authorize");
exit();
?>