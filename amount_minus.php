<?php
require_once("config.php"); //Подключаем БД

$id = $_POST['id'];
$email = $_POST['email'];
$query = "SELECT AMOUNT FROM BASKET WHERE ID_PRODUCT = ".$id." AND ID_CLIENT = (SELECT ID_CLIENT FROM CLIENT WHERE EMAIL='".$email."')"; //получаем количество текущего товара в корзине

$result = $mysqli->query($query);
$rows = mysqli_num_rows($result);

if (empty($rows)) { // запрос вернул пустой результат (товар в корзине отсутствует, нет возможности уменьшить его количество)
    echo "0";
}
if ($stmt = $mysqli->query($query)) {
    while ($row = $stmt->fetch_assoc()) {
        $amount = $row['AMOUNT'] - 1; //уменьшаем количество товара на 1      
        if ($amount==0){ //если количество товара стало 0, то убираем его из корзины
            $query = "DELETE FROM BASKET WHERE ID_PRODUCT=".$id." AND ID_CLIENT = (SELECT ID_CLIENT FROM CLIENT WHERE EMAIL='".$email."')"; 
            $stmt2 = $mysqli->query($query);
            echo 0;
        }
        else if ($amount<0) { //если изначальное количество было = 0 (а сейчас равно -1)
            $amount = 0;
            echo $amount;
        }
        else {
            echo $amount;
            $query = "UPDATE BASKET SET AMOUNT = '$amount' WHERE ID_PRODUCT = ".$id." AND ID_CLIENT = (SELECT ID_CLIENT FROM CLIENT WHERE EMAIL='".$email."')";
            $stmt2 = $mysqli->query($query);
        }
        
    } 
}
?>