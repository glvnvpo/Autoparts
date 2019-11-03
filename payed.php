<?php
require_once("config.php"); //Подключаем БД

$id_order = $_POST['id_order'];
$query = "UPDATE ORDERS SET PAYED = 1 WHERE ID_ORDER = ".$id_order;
$stmt = $mysqli->query($query);

?>