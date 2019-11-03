<?php
$dblocation = 'localhost';
$dbname = 'autoparts';
$dbuser = 'root';
$dbpassword = '';
$mysqli = new mysqli('localhost', $dbuser, $dbpassword, $dbname) or die('Ошибка установки соединения!');

mysqli_query($mysqli, "SET NAMES 'utf8'");
/*
mysqli_query($mysqli, "SET NAMES 'cp1251'");
mysqli_query($mysqli, "SET collation_connection='cp1251_general_ci'");
mysqli_query($mysqli, "SET collation_server='cp1251_general_ci'");
mysqli_query($mysqli, "SET character_set_client='cp1251'");
mysqli_query($mysqli, "SET character_set_connection='cp1251'");
mysqli_query($mysqli, "SET character_set_results='cp1251'");
mysqli_query($mysqli, "SET character_set_server='cp1251'");
*/

?>