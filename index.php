<?php
date_default_timezone_set('Europe/Moscow');
$start_time = strtotime('9 May 2023 10:00:00 Europe/Moscow');
$interval = 10;
$current_time = time();

while ($current_time < $start_time) {
    sleep(1);
    $current_time = time();
}

$db = new mysqli('localhost', 'root', 'root', '9_may');

$current_id = isset($_COOKIE['current_id']) ? intval($_COOKIE['current_id']) : 1;

if ($current_id <= 0) {
    header("Location: /");
    exit;
}

$prev_id = $current_id;
$prev_status = 1;

while (true) {
    $result = $db->query("SELECT id, name, status FROM images WHERE id = {$current_id}");
    if (!$result || $result->num_rows === 0) {
        $current_id = $prev_id;
        break;
    }

    $image = $result->fetch_assoc();
    if ($image['status'] == 1) {
        $current_image_name = $image['name'];
        setcookie('current_id', $current_id, time()+3600, '/');
        break;
    } elseif ($prev_status == 1) {
        $current_image_name = $image['name'];
        setcookie('current_id', $current_id, time()+3600, '/');
    }

    $prev_id = $current_id;
    $prev_status = $image['status'];
    $current_id++;
}

if (!isset($current_image_name)) {
	setcookie('current_id', 1, time()+3600);
	header("Location: /");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Images</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script type="text/javascript" src="./js/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body style="background-image: url('/back.png?v=1')">
    <div class="container">
        <div class="row justify-content-center mt-3 mb-3">
            <div class="col-md-6">
                <div class="card">
                    <img class="card-img-top img_show" src="uploads/<?php echo $current_image_name; ?>" alt="Image">
                    <div class="card-body">
                        <div class="row justify-content-between mb-8">
                            <div class="justify-content-start">
                                <a href="/upload/"><button class="btn btn-primary">Загрузить фото</button></a>
                            </div>
                            <div class="justify-content-end">
                                <?php 
                                $result = $db->query("SELECT id FROM images WHERE id > {$current_id} AND status = 1 LIMIT 1");
                                if ($result->num_rows > 0): 
                                    $next_image_id = $result->fetch_assoc()['id'];
                                ?>
                                <button onclick="set_cookie(<?php echo $next_image_id ?>)" class="btn btn-primary">далее &raquo;</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div><p class="p-2" style="text-align: center; ">Колледж АНО ВО «Университет «Сириус», Колледж «Сириус»</p></div>

                        <script>
                            setTimeout(function () {
                             	set_cookie(parseInt("<?php echo $current_id + 1 ?>"));
                            }, 10000);
                       	</script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>