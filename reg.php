<?php
require_once ('db.php');

$login = $_POST['login']; 
$phone = $_POST['phone'];
$email = $_POST['email'];
$pass = $_POST['pass'];
$reppass = $_POST['reppass'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "ошибка в email";
    echo "<br><a href='regist.php'>Назад</a>";
    exit;
}

if(empty($login) || empty($phone) || empty($email) || empty($pass) || empty($reppass)){
    echo "Заполните все поля";
    echo "<br><a href='regist.php'>Назад</a>";
} else {
    if($pass != $reppass){
        echo "Пароли не совпадают";
        echo "<br><a href='regist.php'>Назад</a>";
    } else {
        $sql = "INSERT INTO `users` (`login`, `phone`, `email`, `pass`) VALUES ('$login', '$phone', '$email', '$pass')";
        if($conn->query($sql) === TRUE){
            echo "Успешная регистрация";
            echo "<br><a href='avtoris.php'>Войти</a>";
        } else {
            echo "Ошибка: " . $conn->error;
            echo "<br><a href='regist.php'>Назад</a>";
        }
    }
}
?>