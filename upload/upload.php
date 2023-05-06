<?php
// Подключение к базе данных
$dsn = 'mysql:host=localhost;dbname=9_may';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->exec("CREATE TABLE IF NOT EXISTS images (
                  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                  name VARCHAR(255) NOT NULL,
                  status TINYINT(1) NOT NULL DEFAULT '0',
                  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
} catch (PDOException $e) {
    echo 'Ошибка подключения к базе данных: ' . $e->getMessage();
    exit();
}

// Ограничение на размер файла
$limit_size = 3 * 1024 * 1024; // 15 Мб

// Папка, в которую сохраняются загруженные изображения
$upload_dir = '../uploads/';

// Если папка не существует, создаем ее
if (!is_dir($upload_dir)) {
    mkdir($upload_dir);
}

// Если форма была отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, был ли выбран файл
    if (!empty($_FILES['image']['name'])) {
        $image_name = $_FILES['image']['name'];
        $image_size = $_FILES['image']['size'];
        $image_type = $_FILES['image']['type'];
        $image_tmp_name = $_FILES['image']['tmp_name'];

        // Проверяем тип файла
        if (($image_type !== 'image/png') && ($image_type !== 'image/jpg') && ($image_type !== 'image/jpeg')) {
            echo 'Допустимы только изображения в форматах PNG, JPG и JPEG.';
        } elseif ($image_size > $limit_size) {
            echo 'Максимальный размер файла - 3 Мб.';
        } else {
            // Генерируем уникальное имя файла
            $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $image_new_name = uniqid() . '.' . $image_extension;
            // Сохраняем файл в папку на сервере
            move_uploaded_file($image_tmp_name, $upload_dir . $image_new_name);

            // Сохраняем имя файла в базу данных
            $stmt = $pdo->prepare("INSERT INTO images (name, status) VALUES (:name, false)");
            $stmt->bindParam(':name', $image_new_name);
            $stmt->execute();

            header("Location: /upload");
        }
    } else {
        echo 'Выберите изображение для загрузки.';
    }
}
