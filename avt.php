<?php
require_once('db.php');
session_start();

$login = $_POST['login'];
$pass = $_POST['pass'];

if(empty($login) || empty($pass)){
    echo "Заполните все поля";
    echo "<br><a href='avtoris.php'>Назад</a>";
} else {
    $sql_admin = "SELECT * FROM admin WHERE login = '$login' AND pass = '$pass'";
    $result_admin = $conn->query($sql_admin);
    
    if($result_admin->num_rows > 0){
        $row = $result_admin->fetch_assoc();
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_login'] = $row['login'];
        $_SESSION['is_admin'] = true;

        header('Location: admin.php');
        exit;
    } else {
        $sql_user = "SELECT * FROM users WHERE login = '$login' AND pass = '$pass'";
        $result_user = $conn->query($sql_user);
        
        if($result_user->num_rows > 0){
            $row = $result_user->fetch_assoc();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['login'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['is_admin'] = false;

            header('Location: index.php');
            exit;
        } else {
            echo "Пользователь не найден";
            echo "<br><a href='avtoris.php'>Назад</a>";
        }
    }
}
?>