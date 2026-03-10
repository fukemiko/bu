<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/favicon.png" type="image/png">
    <title><?php echo $page_title ?? 'Butoshka - Магазин букетов'; ?></title>
</head>
<body>
<header>
    <a href="index.php">BUTOSHKA</a>
    <nav>
        <a href="index.php">Главная</a>
        <a href="korzina.php">Корзина</a>
        <a href="my_orders.php">Мои заказы</a>

        <?php
        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
            echo '<a href="admin.php" style="color: #ecde8e;">Админ панель</a>';
        }
        if (isset($_SESSION['user_name'])) {
            echo '<a href="logout.php">Выйти</a>';
        } else {
            echo '<a href="avtoris.php">Войти</a>';
        }
        ?>
    </nav>
</header>