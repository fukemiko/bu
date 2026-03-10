<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/favicon.png" type="image/png">
    <title>Регистрация</title>
</head>
<body>
<?php include 'header.php'; ?>
    <main>
        <div class="wrapper">
        <form action="reg.php" method="post">
            <h2>Регистрация</h2>
            <input type="text" placeholder="Введите логин" name="login" required>
            <input type="text" placeholder="Введите номер телефона" name="phone" required>
            <input type="email" placeholder="Введите почту" name="email" required>
            <input type="password" placeholder="Введите пароль" name="pass" required>
            <input type="password" placeholder="Повторите пароль" name="reppass" required>
            <button type="submit">Зарегистрироваться</button>
            <p>
                Уже есть аккаунт? <a href="avtoris.php">Авторизоваться</a>
            </p>
        </form>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>