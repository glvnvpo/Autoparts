<?php

require_once("master_page_header.php");
require_once("config.php");

$query = "SELECT KIND_NAME FROM KIND ORDER BY KIND_NAME ASC"; //формируем запрос

$stmt_kind = $mysqli->prepare($query);
$stmt_kind->execute();
$stmt_kind->bind_result($kind_name); //присваеваем результат переменным
$stmt_kind->store_result(); //передает результирующий набор последнего запроса

//считаем количество строк в таблице ВИД
$res = mysqli_query($mysqli, 'SELECT COUNT(*) FROM KIND');
$array = mysqli_fetch_array($res);
$count = $array[0];

//проверка авторизации пользователя на корректность
if (isset($_COOKIE["email_cookie"]) && isset($_COOKIE["salt_cookie"])) { //отображаем колонку КОЛИЧЕСТВО только если пользователь авторизован и все заказы закрыты (оплачены и доставлены)
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
          
          if($array[0]==$salt) { //соли совпали
              $auth = 1; //идентификатор авторизации
          }
          else {
              $auth = 0;
          }
        }
        else {
            $auth = 0;
        }
}
else {
    $auth = 0;
}

echo '<style>
        .filter_table {
           font-family: Arial;
           font-size: 16pt;
           color: white;
           margin-left: 20px;
           border: solid blue 2px;
       }
       select {
           color: black;
       }
       .catalogue_table {
            margin-left: 20px;
            margin-top: 40px;
        }
       </style>
       <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
       <script>
        function change_kind(){
            var n = document.getElementById("select_kind").options.selectedIndex; //получаем индекс выбранного вида
            var txt = document.getElementById("select_kind").options[n].text; //получаем текст (значение) выбранного вида
            document.cookie = "kind="+txt;
            document.location.href = "catalogue.php";
        }
        
        function change_supplier(){
            var n = document.getElementById("select_supplier").options.selectedIndex; //получаем индекс выбранного поставщика
            var txt = document.getElementById("select_supplier").options[n].text; //получаем текст (значение) выбранного поставщика
            document.cookie = "supplier="+txt;
            document.location.href = "catalogue.php";
        }
        
        function get_cookie (cookie_name) //функция для получения значения куки
        {
            var results = document.cookie.match ( "(^|;) ?" + cookie_name + "=([^;]*)(;|$)" );

            if ( results )
              return ( unescape ( results[2] ) );
            else
              return null;
        }
        
        
        $(document).ready(function(){
            
            $(".button_pm").click(function(){
                $.ajax({
                    type: "POST",
                    url: "show.php",
                    data: "idcat="+$(18).val(),
                    success: function(html){
                        $(".content").html(html);
                    }
                });
                    return false;
            });
        });
        
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
                },
                error: function(request, status, errorT) {
                    alert("error");
                }
            });
        }
        
        </script>
       <div class="content"></div>
       <table border="0" class="filter_table">
        <tr>
            <td width="610" align="left"><b>Фильтры</b></td>
            <td width="100" align="right">Вид:</td>
            <td>
                <select onchange="change_kind()" id="select_kind">
                <option>Любой</option>';

//вывод таблицы с фильтрами
$k=0;
while($k!=($count))
    {          
    $stmt_kind->data_seek($k); //перемещение на выбранную строку
    $stmt_kind->fetch(); //извлечение строки

    
    if ((isset($_COOKIE['kind'])) && ($kind_name == $_COOKIE['kind']))   //если в куки значение поставщика=Любой
    {   
        echo '<option selected>'.$kind_name.'</option>';  
    }
    else {
       echo '<option>'.$kind_name.'</option>'; 
    }
    
    $k = $k+1;
};
$stmt_kind->close();

echo  '</select>
            </td>
            <td width="145" align="right">Поставщик:</td>
            <td>
                <select onchange="change_supplier()" id="select_supplier">
                    <option selected>Любой</option>';

$query = "SELECT SUPPLIER_NAME FROM SUPPLIER ORDER BY SUPPLIER_NAME ASC"; //формируем запрос

$stmt_supplier = $mysqli->prepare($query);
$stmt_supplier->execute();
$stmt_supplier->bind_result($supplier_name); //присваеваем результат переменным
$stmt_supplier->store_result(); //передает результирующий набор последнего запроса

//считаем количество строк в таблице ПОСТАВЩИК
$res = mysqli_query($mysqli, 'SELECT COUNT(*) FROM SUPPLIER');
$array = mysqli_fetch_array($res);
$count = $array[0];

$k=0;
while($k!=($count))
    {          
    $stmt_supplier->data_seek($k); //перемещение на выбранную строку
    $stmt_supplier->fetch(); //извлечение строки

    if ((isset($_COOKIE['supplier'])) && ($supplier_name == $_COOKIE['supplier']))   //если в куки значение поставщика=Любой
    {   
        echo '<option selected>'.$supplier_name.'</option>';  
    }
    else {
       echo '<option>'.$supplier_name.'</option>'; 
    }


  $k = $k+1;
};

echo '</select>
            </td>
        </tr>
    </table>';
$stmt_supplier->close();


//ТАБЛИЦА С ТОВАРАМИ

if (!isset($_COOKIE['kind']))   //если в куки значение вида=Любой
{   
    $selected_kind = "IS NOT NULL";  
}
else if ($_COOKIE['kind']=="Любой") //если в куки значение вида отсутствует
{
    $selected_kind = "IS NOT NULL";  
}
else {
    $selected_kind = '"'.$_COOKIE['kind'].'"'; 
    
    $result = mysqli_query($mysqli, 'SELECT ID_KIND FROM KIND WHERE KIND_NAME = '.$selected_kind);
    $array = mysqli_fetch_array($result);
    $selected_kind = "=".$array[0];
    
    /* очищаем результирующий набор */
    mysqli_free_result($result);  
};


if (!isset($_COOKIE['supplier']))   //если в куки значение поставщика=Любой
{   
    $selected_supplier = "IS NOT NULL";  
}
else if ($_COOKIE['supplier']=="Любой") //если в куки значение поставщика отсутствует
{
    $selected_supplier = "IS NOT NULL";  
}
else {
    $selected_supplier = '"'.$_COOKIE['supplier'].'"'; 
    
    $result = mysqli_query($mysqli, 'SELECT ID_SUPPLIER FROM SUPPLIER WHERE SUPPLIER_NAME = '.$selected_supplier);
    $array = mysqli_fetch_array($result);
    $selected_supplier = "=".$array[0];
    
    /* очищаем результирующий набор */
    mysqli_free_result($result);
};


$query = "SELECT * FROM product WHERE ID_KIND ".$selected_kind." AND ID_SUPPLIER ".$selected_supplier; //формируем запрос


$stmt = $mysqli->prepare($query);
$stmt->execute();
$stmt->bind_result($id_product, $product_name, $price, $id_kind_product, $image, $id_supplier_product); //присваеваем результат переменным
$stmt->store_result(); //передает результирующий набор последнего запроса

    
//считаем количество строк в таблице
$res = mysqli_query($mysqli, "SELECT COUNT(*) FROM product WHERE ID_KIND ".$selected_kind." AND ID_SUPPLIER ".$selected_supplier);
$array = mysqli_fetch_array($res);
$count = $array[0];

//вывод таблицы с товарами
    

echo '<font face="Arial"> <table border="2" bordercolor="blue" class="catalogue_table">
        <tr align="center">
        <th style="font-size: 15pt">Изображение'
        .'</th>
        <th width="300" style="font-size: 15pt">Наименование'
     .'</th>
        <th width="150" style="font-size: 15pt">Цена'
     .'</th>
        <th width="150" style="font-size: 15pt">Вид'
    .'</th> 
        <th width="150" style="font-size: 15pt">Поставщик'
    .'</th>';
        

 
        
if ($count!=0) { 
              //смотрим, все ли заказы пользователя закрыты (оплачены и доставлены)
              if ($auth==1) {
              $email = $_COOKIE["email_cookie"];
                $res = mysqli_query($mysqli, 'SELECT COUNT(*) FROM ORDERS WHERE ID_CLIENT=(SELECT ID_CLIENT FROM CLIENT WHERE EMAIL="'.$email.'") AND (PAYED=0 OR DELIVERED=0)'); //считаем количество записей в корзине с текущим авторизованным пользователем
                $array = mysqli_fetch_array($res);
                $count_orders = $array[0];

                if ($count_orders==0) {
                    echo '<th>Количество'
                .'</th>
                    </tr>';
                }
          }         
    else {
        echo '</tr>';
    }
          
    $k=0;
    while($k!=($count))
        {          
        $stmt->data_seek($k); //перемещение на выбранную строку
        $stmt->fetch(); //извлечение строки

        $id_kind_product = (int) $id_kind_product;
    
    
        $res = mysqli_query($mysqli, "SELECT kind_name FROM kind WHERE id_kind = '$id_kind_product'");
        $array = mysqli_fetch_array($res); //результатом является одна строка
        $kind_name = $array[0]; //извлекаем результат запроса (определяем ВИД товара)

        $res = mysqli_query($mysqli, "SELECT supplier_name FROM supplier WHERE id_supplier = '$id_supplier_product'");
        $array = mysqli_fetch_array($res);
        $supplier_name = $array[0];  //извлекаем результат запроса (определяем ПОСТАВЩИКА товара)
         
        echo ' <tr align="center">
                <td> <img class="img" id="img" width="200" src="product_images/'.$image.'"/>
                <td> '.$product_name.' 
                <td> '.number_format($price, 0, '', ' ').' руб. 
                <td> '.$kind_name.'          
                <td> '.$supplier_name; 

                  if ($auth==1) {
                            //смотрим, все ли заказы пользователя закрыты (оплачены и доставлены)
                            $res = mysqli_query($mysqli, 'SELECT COUNT(*) FROM ORDERS WHERE ID_CLIENT=(SELECT ID_CLIENT FROM CLIENT WHERE EMAIL="'.$email.'") AND (PAYED=0 OR DELIVERED=0)'); //считаем количество записей в корзине с текущим авторизованным пользователем
                            $array = mysqli_fetch_array($res);
                            $count_orders = $array[0];

                        if ($count_orders==0) { //все заказы пользователя закрыты

                            //есть ли текущий товар в корзине пользователя
                            $query_count = "SELECT COUNT(*) FROM BASKET WHERE ID_PRODUCT=".$id_product." AND ID_CLIENT=(SELECT ID_CLIENT FROM CLIENT WHERE EMAIL='".$email."')"; 
                            $res = mysqli_query($mysqli, $query_count);
                            $array = mysqli_fetch_array($res); //результатом является одна строка
                            $amount = $array[0]; 
                            // 0 - товар отсутсвует в корзине, 1 - товар имеется в корзине пользователя 
                            if ($amount!=0) { //товар есть в корзине пользователя

                                $query_amount = "SELECT AMOUNT FROM BASKET WHERE ID_PRODUCT=".$id_product." AND ID_CLIENT=(SELECT ID_CLIENT FROM CLIENT WHERE EMAIL='".$email."')"; //запрос на получение количества текущего товара
                                $res = mysqli_query($mysqli, $query_amount);
                                $array = mysqli_fetch_array($res); //результатом является одна строка
                                $amount = $array[0];
                            }               
                          echo '<td> 
                                <table>
                                    <tr> <!--onclick="remove_one(.$id_product.)"-->
                                        <td><button class="button_pm" onclick="remove_one('.$id_product.')"><b>−</b></button></td>
                                        <td class="amount_'.$id_product.'" id="amount_'.$id_product.'">'.$amount.'</td>
                                        <td><button class="button_pm" onclick="add_one('.$id_product.')"><b>+</b></button></td>
                                    </tr>
                                </table> 
                            </tr>';
                        }
                  }
                

            else {
                echo '</tr>';
            }

        $k = $k+1;
    }
    echo '</table></font>';

}

else { //в базе нет товаров, удовлетворяющих запросу
    echo '</tr></table></font>
        <style>
        .nothing_found {
            font-family: Arial;
            font-size: 22pt;
            color: white;
            margin-top:30px;
        }</style>
     <div class="nothing_found">По Вашему запросу ничего не найдено!</div>';
};

$stmt->close(); //очищает дескриптор запроса
$mysqli->close(); //закрываем подключение
require_once("master_page_footer.php");

?>