<?php
//формирование заказа (была нажата кнопка "Сформировать заказ")
require_once("config.php"); //Подключаем БД

$id_client = $_POST['id_client'];

$res = mysqli_query($mysqli, 'SELECT COUNT(*) FROM BASKET WHERE ID_CLIENT='.$id_client); //считаем количество записей в корзине с текущим авторизованным пользователем
$array = mysqli_fetch_array($res);
$count = $array[0];

$query_products = 'SELECT ID_PRODUCT, AMOUNT FROM BASKET WHERE ID_CLIENT='.$id_client; //выбираем ID_товаров из корзины
$stmt_products = $mysqli->prepare($query_products);
$stmt_products->execute();
$stmt_products->bind_result($id_product, $amount); //присваеваем результат переменным
$stmt_products->store_result(); //передает результирующий набор последнего запроса 

//создаем новый заказ в таблице
$query = "INSERT INTO ORDERS SET ID_CLIENT='$id_client', TOTAL_PRICE=0, PAYED=0, DELIVERED=0";
$stmt2 = $mysqli->query($query);

$query = 'SELECT ID_ORDER FROM ORDERS WHERE ID_CLIENT='.$id_client.' AND PAYED=0 AND DELIVERED=0'; //формируем запрос
$res = mysqli_query($mysqli, $query); 
$row = $res->fetch_assoc(); //используем ассоциативный массив 
$row_order=$row;

$k=0;
$total_summ = 0; //переменная для хранения значения ИТОГО
while($k!=($count)) 
    {          
    $stmt_products->data_seek($k); //перемещение на выбранную строку
    $stmt_products->fetch(); //извлечение строки
    
    $query = "INSERT INTO ORDER_PRODUCT_AMOUNT SET ID_ORDER=".$row_order['ID_ORDER'].", ID_PRODUCT=".$id_product." , AMOUNT = ".$amount;
    $stmt_order_product_amount = $mysqli->query($query);

    //запрос для получения цены товара
    $query = 'SELECT PRICE FROM PRODUCT WHERE ID_PRODUCT='.$id_product.''; //формируем запрос
    $res = mysqli_query($mysqli, $query); //считаем количество записей в корзине с текущим авторизованным пользователем
    $row = $res->fetch_assoc(); //используем ассоциативный массив    
    
    $k = $k+1;
    $total_summ = $total_summ+$row['PRICE']*$amount;
}

$query = "UPDATE ORDERS SET TOTAL_PRICE=".$total_summ." WHERE ID_CLIENT='$id_client' AND PAYED=0 AND DELIVERED=0";
$stmt2 = $mysqli->query($query);

$query = "DELETE FROM BASKET WHERE ID_CLIENT='$id_client'";
$stmt2 = $mysqli->query($query);

?>