<?php 
$hostname = "localhost";
$name = "root";
$password = "";
$dbname = "reg";

$conn = mysqli_connect($hostname, $name, $password, $dbname);

if(!$conn){
    die("Ошибка соединения".mysqli_connect_error());
}
?>