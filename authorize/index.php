<?php
// Ограничение на количество попыток ввода пароля
$limit = 5;

// Время блокировки после превышения лимита, в секундах
$block_time = 60 * 5; // 5 минут

// Имя куки, в которой будем хранить количество неудачных попыток ввода пароля
$cookie_name = "login_attempts";

session_start();

// Если пользователь уже авторизован, перенаправляем на защищенную страницу
if (isset($_SESSION["authorized"]) && $_SESSION["authorized"] === true) {
  header("Location: /admin");
  exit();
}
sleep(3);
// Если пользователь отправил форму с логином и паролем
if (isset($_POST["username"]) && isset($_POST["password"])) {
  // Проверяем логин и пароль
  if ($_POST["username"] === $admin_username && $_POST["password"] === $admin_password) {
    // Устанавливаем переменную authorized в сессии в true
    $_SESSION["authorized"] = true;
    // Перенаправляем на защищенную страницу
    header("Location: /admin");
    exit();
  } else {
    // Если логин или пароль неправильные, увеличиваем количество неудачных попыток ввода пароля
    if (!isset($_COOKIE[$cookie_name])) {
      setcookie($cookie_name, 1, time() + $block_time);
    } else {
      setcookie($cookie_name, $_COOKIE[$cookie_name] + 1, time() + $block_time);
    }
    $attempts = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : 0;
    if ($attempts >= $limit) {
      // Если превышен лимит попыток ввода пароля, блокируем ввод на некоторое время
      echo "Вы превысили лимит попыток ввода пароля. Попробуйте снова через $block_time секунд.";
      exit();
    } else {
      // Если попытки ввода пароля еще не исчерпаны, показываем сообщение об ошибке
      $error_message = "Неправильный логин или пароль";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Авторизация</title>
  <!-- Подключение Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
  <div class="container">
    <h1>Авторизация</h1>
    <?php
    // Если есть сообщение об ошибке, показываем его
    if (isset($error_message)) {
      echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    <form method="post">
      <div class="form-group">
        <label for="username">Логин:</label>
        <input type="text" class="form-control" name="username" id="username">
      </div>
      <div class="form-group">
        <label for="password">Пароль:</label>
        <input type="password" class="form-control" name="password" id="password">
      </div>
      <button type="submit" class="btn btn-primary">Войти</button>
    </form>
  </div>

  <!-- Подключение Bootstrap JS (jQuery и Popper.js уже должны быть подключены) -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>