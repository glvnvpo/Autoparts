<?php
require_once("config.php"); //Подключаем БД

$id = $_POST['id'];
$email = $_POST['email'];
$amount_id = $_POST['amount'];
$query = "SELECT AMOUNT FROM BASKET WHERE ID_PRODUCT = ".$id." AND ID_CLIENT = (SELECT ID_CLIENT FROM CLIENT WHERE EMAIL='".$email."')"; //получаем количество текущего товара в корзине

if ($amount_id==0) { //нужно создать запись в корзине
    $amount_id = 1;
    $query = "INSERT INTO BASKET SET ID_CLIENT=(SELECT ID_CLIENT FROM CLIENT WHERE EMAIL='$email'), ID_PRODUCT='$id', AMOUNT='$amount_id'";
    $stmt2 = $mysqli->query($query);
    echo $amount_id;
}
else if ($stmt = $mysqli->query($query)) {
    while ($row = $stmt->fetch_assoc()) {
        $amount = $row['AMOUNT'] +1; //увеличивает количество товара на 1
        echo $amount;
        $query = "UPDATE BASKET SET AMOUNT = '$amount' WHERE ID_PRODUCT = ".$id." AND ID_CLIENT = (SELECT ID_CLIENT FROM CLIENT WHERE EMAIL='".$email."')";
        $stmt2 = $mysqli->query($query);
    }
}
?>