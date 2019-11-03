<?php
//ФАЙЛ НЕ ИСПОЛЬЗУЕТСЯ
if (isset($_COOKIE['kind']))   //удаляем куки с фильтрами из каталога
{   
    setcookie("kind", "", time() - 3600);
}

if (isset($_COOKIE['supplier']))   //удаляем куки с фильтрами из каталога
{   
    setcookie("supplier", "", time() - 3600);
}

require_once("master_page_header.php");
    
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

//        $res = mysqli_query($mysqli, 'SELECT COUNT(*) FROM KIND');
//        $array = mysqli_fetch_array($res);
//        $count = $array[0];

        if ($count==0) { //корзина пуста
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
        else { //в корзине что-то есть
            
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
                        border-radius: 2px;
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

                $query_amount = "SELECT AMOUNT FROM BASKET WHERE ID_PRODUCT=(SELECT ID_PRODUCT FROM PRODUCT WHERE PRODUCT_NAME='".$row['PRODUCT_NAME']."')
                    AND ID_CLIENT=(SELECT ID_CLIENT FROM CLIENT WHERE EMAIL='".$email."')"; //запрос на получение количества текущего товара
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
                <button id="set_order">Оформить заказ</button>
                ';
        }
    }
    else { //соли не совпали, пользователь не авторизован
        echo $not_authorized;
    }
  }
  else {
      echo $not_authorized;
  }        

require_once("master_page_footer.php");

?>