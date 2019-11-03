<?php




if (isset($_COOKIE['kind']))   //удаляем куки с фильтрами из каталога
{   
    setcookie("kind", "", time() - 3600);
}

if (isset($_COOKIE['supplier']))   //удаляем куки с фильтрами из каталога
{   
    setcookie("supplier", "", time() - 3600);
}

require_once("master_page_header.php");

echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>';

$not_authorized = '<style>
       footer {
       position: absolute;
       bottom: -20px;
       }
       .attention_text {
           font-family: Roboto;
           letter-spacing: 2px;
           color: #567FE4;
           font-size: 25pt;
       }
       .attention_table {
           margin-top: 70px;
           margin-left: 50px;
       }
   </style>
   <table class="attention_table" border="0">
        <tr>
            <td width="250" align="center"> <img src="images/attention.png" width="230" alt="Кредитная карта" /></td>

            <td>
                <div class="attention_text">                  
                    Корзина доступна только авторизованным пользователям!
                </div>
            </td>
        </tr>
    </table>';


if (isset($_COOKIE["email_cookie"]) && isset($_COOKIE["salt_cookie"]))   
{   // пользователь авторизован
       
  require_once("config.php");
  $email = $_COOKIE["email_cookie"];
  //проверяем, есть ли email из куки в таблице
  $query = "SELECT EXISTS(SELECT EMAIL FROM CLIENT WHERE EMAIL='$email')"; //запрос на наличие email в таблице
  $res = mysqli_query($mysqli, $query);
  $array = mysqli_fetch_array($res); //результатом является одна строка
  
  if($array[0]==1) {//email существует в таблице
    $salt = $_COOKIE["salt_cookie"];
    //проверяем, правильная ли соль записана в куки
    $query = "SELECT SALT FROM CLIENT WHERE EMAIL='$email'"; //выпрашиваем соль из таблицы
    $res = mysqli_query($mysqli, $query);
    $array = mysqli_fetch_array($res); //результатом является одна строка
    
    if($array[0]==$salt) { //соли совпали, пользователь действительно авторизован   
         //сначала получить id клиента (по email), потом посмотреть его записи в корзине
        $res = mysqli_query($mysqli, 'SELECT COUNT(*) FROM BASKET WHERE ID_CLIENT=(SELECT ID_CLIENT FROM CLIENT WHERE EMAIL="'.$email.'")'); //считаем количество записей в корзине с текущим авторизованным пользователем
        $array = mysqli_fetch_array($res);
        $count = $array[0];
        
        //смотрим, все ли заказы пользователя закрыты (оплачены и доставлены)
        $res = mysqli_query($mysqli, 'SELECT COUNT(*) FROM ORDERS WHERE ID_CLIENT=(SELECT ID_CLIENT FROM CLIENT WHERE EMAIL="'.$email.'") AND (PAYED=0 OR DELIVERED=0)'); //считаем количество записей в корзине с текущим авторизованным пользователем
        $array = mysqli_fetch_array($res);
        $count_orders = $array[0]; 

        if ($count==0 && $count_orders==0) { //корзина пуста
            echo '<style>
       footer {
       position: absolute;
       bottom: -20px;
       }
       .attention_text {
           font-family: Roboto;
           letter-spacing: 2px;
           color: #567FE4;
           font-size: 25pt;
       }
       .attention_table {
           margin-top: 70px;
           margin-left: 50px;
       }
   </style>
   <table class="attention_table" border="0">
        <tr>
            <td width="250" align="center"> <img src="images/empty_basket.png" width="230" alt="Корзина" /></td>

            <td>
                <div class="attention_text">                  
                    Ваша корзина пуста!
                </div>
            </td>
        </tr>
    </table>';
        }
        else if ($count!=0 && $count_orders==0) { //все заказы закрыты, в корзине что-то есть
            
            $query_products = 'SELECT ID_PRODUCT FROM BASKET WHERE ID_CLIENT=(SELECT ID_CLIENT FROM CLIENT WHERE EMAIL="'.$email.'")'; //выбираем ID_товаров из корзины
            $stmt_products = $mysqli->prepare($query_products);
            $stmt_products->execute();
            $stmt_products->bind_result($id_product); //присваеваем результат переменным
            $stmt_products->store_result(); //передает результирующий набор последнего запроса 
            
             //шапка таблицы
            echo '
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
                <script>
                
                    function get_cookie (cookie_name) //функция для получения значения куки
                        {
                            var results = document.cookie.match ( "(^|;) ?" + cookie_name + "=([^;]*)(;|$)" );

                            if ( results )
                              return ( unescape ( results[2] ) );
                            else
                              return null;
                        }
                    
                    function add_one(id) {
                        
                        amount = document.getElementById("amount_"+id).innerHTML;
                        
                        email = get_cookie("email_cookie");                       

                        $.ajax({
                            type: "POST",
                            url: "amount_plus.php",
                            data: { id : id, email : email, amount : amount },
                            success: function(data) {
                                $(".amount_"+id).html(data); 
                            },
                            error: function(request, status, errorT) {
                                alert("error");
                            }
                        });
                        
                        amount = document.getElementById("amount_"+id).innerHTML;
                        price = document.getElementById("price_"+id).innerHTML.replace(/\D+/g,"");
                        res = (Number.parseInt(amount)+1)*Number.parseInt(price);
                        document.getElementById("price_amount_"+id).innerText = String(res).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, \'$1 \')+" руб.";   
                        total = document.getElementById("total").innerHTML.replace(/\D+/g,"");
                        document.getElementById("total").innerText = String(Number.parseInt(total)+Number.parseInt(price)).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, \'$1 \')+" руб.";  
                    
                    } 
                    
                    function remove_one(id) {
                            
                            email = get_cookie("email_cookie");
                            amount = document.getElementById("amount_"+id).innerHTML;
                            

                            $.ajax({
                                type: "POST",
                                url: "amount_minus.php",
                                data: { id : id, email : email, amount : amount },
                                success: function(data) {
                                    $(".amount_"+id).html(data); 
                                    
                                    if (amount>0) { //чтобы ИТОГО не уходило в минус
                                        
                                        amount = document.getElementById("amount_"+id).innerHTML;
                                        price = document.getElementById("price_"+id).innerHTML.replace(/\D+/g,"");
                                        res = (Number.parseInt(amount))*Number.parseInt(price);
                                        document.getElementById("price_amount_"+id).innerText = String(res).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, \'$1 \')+" руб.";   
                                        total = document.getElementById("total").innerHTML.replace(/\D+/g,"");
                                        document.getElementById("total").innerText = String(Number.parseInt(total)-Number.parseInt(price)).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, \'$1 \')+" руб.";  

                                    }
                                    
                                },
                                error: function(request, status, errorT) {
                                    alert("error");
                                }
                            });                                                  
                    }                    
                </script>
                <style>
                    #set_order {
                        width: 250px;
                        height: 50px;
                        border: solid blue 3px;
                        border-radius: 8px;
                        font-family: Arial;
                        font-size: 18pt;                        
                        color: white;
                        background-color: black;
                        margin-top: 30px;
                        margin-left: 40%;
                    }
                </style>
                <font face="Arial"> <table border="2" bordercolor="blue" class="catalogue_table"> 
                    <tr align="center">
                    <th style="font-size: 15pt">Изображение'
                    .'</th>
                    <th width="300" style="font-size: 15pt">Наименование'
                 .'</th>
                    <th width="150" style="font-size: 15pt">Цена'
                .'</th>
                    <th width="150" style="font-size: 15pt">Количество'
                .'</th>
                    <th width="150" style="font-size: 15pt">Стоимость'
                .'</th></tr>';
            
            $k=0;
            $total_summ = 0; //переменная для хранения значения ИТОГО
            while($k!=($count)) 
                {          
                $stmt_products->data_seek($k); //перемещение на выбранную строку
                $stmt_products->fetch(); //извлечение строки
            
            
                $query = 'SELECT PRODUCT_NAME, PRICE, IMAGE FROM PRODUCT WHERE ID_PRODUCT='.$id_product.''; //формируем запрос
                $res = mysqli_query($mysqli, $query); //считаем количество записей в корзине с текущим авторизованным пользователем

                $row = $res->fetch_assoc(); //используем ассоциативный массив 
                
                $query = 'SELECT ID_CLIENT FROM CLIENT WHERE EMAIL="'.$email.'"'; //формируем запрос
                $res = mysqli_query($mysqli, $query); //считаем количество записей в корзине с текущим авторизованным пользователем
                $row_client = $res->fetch_assoc(); //используем ассоциативный массив 

                $query_amount = "SELECT AMOUNT FROM BASKET WHERE ID_PRODUCT=(SELECT ID_PRODUCT FROM PRODUCT WHERE PRODUCT_NAME='".$row['PRODUCT_NAME']."')
                    AND ID_CLIENT=".$row_client["ID_CLIENT"]; //запрос на получение количества текущего товара
                $res = mysqli_query($mysqli, $query_amount);
                $array = mysqli_fetch_array($res); //результатом является одна строка
                $amount = $array[0];
                
                

                echo ' <tr align="center">
                <td> <img width="200" src="product_images/'.$row['IMAGE'].'"/>
                <td> '.$row['PRODUCT_NAME'].' 
                <td id="price_'.$id_product.'"> '.number_format($row['PRICE'], 0, '', ' ').' руб.
                <td> 
                    <table>
                        <tr>
                            <td><button class="button_pm" onclick="remove_one('.$id_product.')"><b>−</b></button></td>
                            <td class="amount_'.$id_product.'" id="amount_'.$id_product.'">'.$amount.'</td>
                            <td><button class="button_pm" onclick="add_one('.$id_product.')"><b>+</b></button></td>                               
                        </tr>
                    </table> 
                <td id="price_amount_'.$id_product.'"> '.number_format($row['PRICE']*$amount, 0, '', ' ').' руб.
                </tr>';               
                $k = $k+1;
                $total_summ = $total_summ+$row['PRICE']*$amount;
            }
            echo '<tr>
            <td colspan="4" align="right" style="font-size: 15pt">Итого</td>
            <td align="center" id="total">'.number_format($total_summ, 0, '', ' ').' руб.</td></tr>';
            echo '</table></font> 
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
                        <script>
                            function form_order(id_client) {                                                           
                                $.ajax({
                                  type: "POST",
                                  url: "form_order.php",
                                  data: { id_client : id_client},
                                  success: function() {
                                    alert("Заказ сформирован!");
                                    window.location.reload();                                          
                                  },
                                  error: function(request, status, errorT) {
                                      alert("error");
                                    }
                                });
                            }
                </script>
                <button id="set_order" onclick="form_order('.$row_client["ID_CLIENT"].')">Оформить заказ</button>
                ';
        }
        else if ($count_orders!=0) { //у пользователя имеется незавершенный заказ

             $query = 'SELECT * FROM ORDERS WHERE ID_CLIENT=(SELECT ID_CLIENT FROM CLIENT WHERE EMAIL="'.$email.'") AND (DELIVERED=0 OR (PAYED=0 AND DELIVERED=0))'; //формируем запрос
             $res = mysqli_query($mysqli, $query); 
             $row = $res->fetch_assoc(); //используем ассоциативный массив 
             $row_order=$row;
             
//                $query = 'SELECT * FROM ORDERS WHERE ID_CLIENT=(SELECT ID_CLIENT FROM CLIENT WHERE EMAIL="'.$email.'") AND PAYED=0 AND DELIVERED=0'; //формируем запрос
//                $res = mysqli_query($mysqli, $query); 
//                $row_order = $res->fetch_assoc(); //используем ассоциативный массив 
                
                $query = 'SELECT * FROM CLIENT WHERE EMAIL="'.$email.'"'; //формируем запрос
                $res = mysqli_query($mysqli, $query); 
                $client = $res->fetch_assoc(); //используем ассоциативный массив 
                
                $res = mysqli_query($mysqli, 'SELECT COUNT(*) FROM ORDER_PRODUCT_AMOUNT WHERE ID_ORDER='.$row_order["ID_ORDER"]); 
                $array = mysqli_fetch_array($res);
                $count_products = $array[0]; 
                
                $query_order = 'SELECT * FROM ORDER_PRODUCT_AMOUNT WHERE ID_ORDER='.$row_order["ID_ORDER"]; 
                $stmt_order = $mysqli->prepare($query_order);
                $stmt_order->execute();
                $stmt_order->bind_result($id_order, $id_product, $amount); //присваеваем результат переменным
                $stmt_order->store_result(); //передает результирующий набор последнего запроса 
                
                //информация о заказе
                echo '<font face="Arial">
                     <table width="100%" border="0">
                     <tr>
                        <td style="font-size: 20pt">
                            Ваш заказ
                        </td>
                        <td align="center" style="font-size: 20pt">
                           Номер заказа: #'.$row_order["ID_ORDER"].'
                        </td>
                     </tr>
                 </table></font>';
                //таблица с заказом
                echo '<style> 
                        #info {
                            position: absolute;
                            top: 230px;
                            right: 150px;
                            font-size: 15pt;
                            font-family: Arial;
                        }
                        #pay {
                            width: 250px;
                            height: 50px;
                            border: solid blue 3px;
                            border-radius: 8px;
                            font-family: Arial;
                            font-size: 18pt;                        
                            color: white;
                            background-color: black;
                            margin-top: 30px;
                            margin-left: 40%;
                        }
                        #deliver {
                            width: 300px;
                            height: 50px;
                            border: solid blue 3px;
                            border-radius: 8px;
                            font-family: Arial;
                            font-size: 18pt;                        
                            color: white;
                            background-color: black;
                            margin-top: 30px;
                            margin-left: 40%;
                        }
                    </style>
                     <table id="info" border="0" cellspacing="3" cellpadding="6">
                     <tr>
                        <td>
                            <b>ФИО</b>
                        </td>
                     </tr>
                     <tr>
                        <td>
                            '.$client["FIO"].'
                        </td>
                     </tr>
                     <tr>
                        <td>
                            <b>Адрес доставки</b>
                        </td>
                     </tr>
                     <tr>
                        <td>
                            '.$client["ADDRESS"].'
                        </td>
                     </tr>
                     <tr>
                        <td>
                            <b>Email</b>
                        </td>
                     </tr>
                     <tr>
                        <td>
                            '.$email.'
                        </td>
                     </tr>
                     <tr>
                        <td style="font-size: 20pt; color: #FF7F50" id="payed_or_not">
                            <b>НЕ ОПЛАЧЕН</b>
                        </td>
                     </tr>
                 </table>';
                
                //шапка таблицы
                echo '<font face="Arial"> <table border="2" bordercolor="blue" class="catalogue_table"> 
                    <tr align="center">
                    <th style="font-size: 15pt">Изображение
                    </th>
                    <th width="300" style="font-size: 15pt">Наименование
                 </th>
                    <th width="150" style="font-size: 15pt">Цена
                </th>
                    <th width="150" style="font-size: 15pt">Количество
                </th>
                    <th width="150" style="font-size: 15pt">Стоимость
                </th></tr>';
            
            $k=0;
            $total_summ = 0; //переменная для хранения значения ИТОГО
            while($k!=($count_products))  {
                $stmt_order->data_seek($k); //перемещение на выбранную строку
                $stmt_order->fetch(); //извлечение строки
            
            
                $query = 'SELECT PRODUCT_NAME, PRICE, IMAGE FROM PRODUCT WHERE ID_PRODUCT='.$id_product; //формируем запрос
                $res = mysqli_query($mysqli, $query); 

                $row_product = $res->fetch_assoc(); //используем ассоциативный массив   

                echo ' <tr align="center">
                <td> <img width="200" src="product_images/'.$row_product['IMAGE'].'"/>
                <td> '.$row_product['PRODUCT_NAME'].' 
                <td id="price_'.$id_product.'"> '.number_format($row_product['PRICE'], 0, '', ' ').' руб.
                <td>'.$amount.'
                <td id="price_amount_'.$id_product.'"> '.number_format($row_product['PRICE']*$amount, 0, '', ' ').' руб.
                </tr>';               
                $k = $k+1;
                $total_summ = $total_summ+$row_product['PRICE']*$amount;                               
            }
            echo '<tr>
                <td colspan="4" align="right" style="font-size: 15pt">Итого</td>
                <td align="center" id="total">'.number_format($total_summ, 0, '', ' ').' руб.</td></tr>';
             
            if ($row['PAYED']==0 && $row['DELIVERED']==0) { //заказ НЕ оплачен и НЕ доставлен
//                echo "есть неоплаченный заказ";                               
                echo '</table></font> 
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
                        <script>
                            function payed(id_order) {
                                
                             
                                $.ajax({
                                  type: "POST",
                                  url: "payed.php",
                                  data: { id_order : id_order},
                                  success: function() {
                                    alert("Заказ #"+id_order+" оплачен!");
                                    window.location.reload();                                          
                                  },
                                  error: function(request, status, errorT) {
                                      alert("error");
                                    }
                                });

                            }
                        </script>
                        <button id="pay" onclick="payed('.$row_order["ID_ORDER"].')">Оплатить заказ</button> 
                        ';//ЗДЕСЬ ЗАБАЦАТЬ AJAX-ЗАПРОС
                
             }
             else if ($row['PAYED']==1 && $row['DELIVERED']==0) { //заказ оплачен, но НЕ доставлен
//                echo "есть недоставленный заказ";
                echo '</table></font> 
                        
                        <script>
                            function delivered(id_order) {
                                
                                
                                $.ajax({
                                  type: "POST",
                                  url: "delivered.php",
                                  data: { id_order : id_order},
                                  success: function() {
                                    alert("Заказ #"+id_order+" получен!");
                                    window.location.reload();                                         
                                  },
                                  error: function(request, status, errorT) {
                                      alert("error");
                                    }
                                });
                            }
                            document.getElementById("payed_or_not").innerHTML = "<b>ОПЛАЧЕН</b>";
                        </script>
                        <button id="deliver" onclick="delivered('.$row_order["ID_ORDER"].')">Подтвердить получение</button> 
                        ';
             }
             
        }
    }
    else { //соли не совпали, пользователь не авторизован
        echo $not_authorized;
    }
  }
  else {
      echo $not_authorized;
  }        
}
else { //пользователь не авторизован
    echo $not_authorized;   
}

require_once("master_page_footer.php");

?>