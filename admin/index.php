<?php
if (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

session_start();

// Функция для проверки авторизации пользователя
function isAuthorized() {
  // Если в сессии есть переменная authorized и она равна true, то пользователь авторизован
  if (isset($_SESSION["authorized"]) && $_SESSION["authorized"] === true) {
    return true;
  }
  return false;
}

// Если пользователь не авторизован, перенаправляем на страницу авторизации
if (!isAuthorized()) {
  header("Location: /authorize");
  exit();
}
// соединяемся с базой данных
$db = new mysqli('localhost', 'root', 'root', '9_may');
if ($db->connect_errno) {
  exit('Failed to connect to MySQL: (' . $db->connect_errno . ') ' . $db->connect_error);
}

// обработка кнопок Accept и Decline
if (isset($_POST['accept'])) {
  $id = intval($_POST['id']);
  $db->query("UPDATE images SET status=true WHERE id={$id}");
} elseif (isset($_POST['decline'])) {
  $id = intval($_POST['id']);
  $result = $db->query("SELECT name FROM images WHERE id={$id}");
  $image = $result->fetch_assoc();
  $filename = $image['name'];
  $db->query("DELETE FROM images WHERE id={$id}");
  unlink("/uploads/{$filename}");
}

// запрос изображений со статусом false
$result = $db->query("SELECT id, name FROM images WHERE status=false");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Админ-панель</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
        crossorigin="anonymous">
  <style>
    .img_show {
      max-width: 250px;
      max-height: 250px;
    }
  </style>
</head>
<body>
<div class="container">
  <p>Добро пожаловать, <?php echo $admin_username; ?>!</p>
  <p><a href="/logout">Выйти</a></p>
  <table class="table table-striped">
    <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($image = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $image['id']; ?></td>
        <td><?php echo $image['name']; ?></td>
        <td>
          <div class="btn-group">
            <button type="button" class="btn btn-secondary"
                    onclick="document.getElementById('<?php echo $image['id'];?>').style.display = 'block'; this.remove();">View Image</button>
            <img class="img_show" onclick="this.remove();" id="<?php echo $image['id']; ?>" src="/uploads/<?php echo $image['name'] ?>"
                 style="display: none;" alt="">
            <form method="post" onsubmit="return confirm('Вы уверены?');">
              <input type="hidden" name="id" value="<?php echo $image['id']; ?>">
              <button type="submit" class="btn btn-success" name="accept">Accept</button>
              <button type="submit" class="btn btn-danger" name="decline">Decline</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>


