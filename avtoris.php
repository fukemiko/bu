<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/favicon.png" type="image/png">
    <title>Авторизация</title>
</head>
<body>
<?php include 'header.php'; ?>
    <main>
        <div class="wrapper">
        <form action="avt.php" method="post">
            <h2>Авторизация</h2>
            <input type="text" placeholder="Введите логин" name="login" required>
            <input type="password" placeholder="Введите пароль" name="pass" required>
            <button type="submit">Войти</button>
            <p>
               Еще нет аккаунта? <a href="regist.php">Зарегистрироваться</a>
            </p>
        </form>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>