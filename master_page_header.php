<?php

echo ' 
<head>
    <title>Автозапчасти</title>
    <link rel="shortcut icon" href="images/icon.png" type="image/png">
    <style>
        body {
            background-color: black;
            color: white;
        }

        #header {
            width: 98%;
            height: 150px;
            position: fixed;
            z-index: 9999;
            background-color: black;
            /*border: solid red;*/
            /*border-radius: 50px;*/
        }

        #headerMain {
            width: 100%;
            height: 150px;
            margin: 0px auto;
            z-index: 0;
        }

        #menu_href {
            font-family: Arial;
            font: 18pt bold;
        }

        footer {
            background-color: black;
        }

        @font-face { /*импорт шрифта*/
            font-family: Roboto;
            src: url(roboto-black-italic.ttf); 
        }

        p {
            font-family: Roboto;
            font-style: italic;
            color: #2438d4;
            font-size: 30pt;
            letter-spacing: 7px;
        }
        .button_pm { //стиль кнопочек КОЛИЧЕСТВО в каталоге и корзине
            width: 30px;
            height: 20px;
            border: solid white 1px;
            border-radius: 2px;
            font: 12pt bold;
            color: white;
            background-color: black;
        }

    </style>
    
<!--    <script>
        alert(localStorage.getItem("email_storage"));
        if (localStorage.getItem("email_storage") !== null) {
            document.getElementById("enter").src = "images/enter.png";
        }
        else if (localStorage.getItem("email_storage") == "empty")
        {
            document.getElementById("enter").src = "images/enter.png";
        }
        else {
            document.getElementById("enter").src = "images/exit.png";
        }
    </script> -->

<script charset="utf-8">
    function enter_exit() {
    
        str = document.getElementById("enter").src;
           
        
        function delete_cookie ( cookie_name ) //функция для удаления куки
            {
              var cookie_date = new Date ( );  
              cookie_date.setTime ( cookie_date.getTime() - 1 );
              document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
            }

            if (str.indexOf("exit.png")!= -1 ) { //в меню была кнопка  ВЫХОД (меняем на ВХОД)
                
                var want_to_log_out = confirm("Вы точно хотите выйти?");
                if (want_to_log_out) {
                     document.getElementById("enter").src = "images/enter.png";  //меняем изображение ВЫХОД на ВХОД             
                     delete_cookie("email_cookie"); //удаляем куки (де-авторизация)
                     delete_cookie("salt_cookie"); 
                     window.location.href = "index.html";
                }
            }
            else { //в меню была кнопка ВХОД (меняем на ВЫХОД)
                window.location.href = "enter.html";
            }
        }

</script>
</head>    
<div id="headerMain">

        <table id="header" border="0">
            <tr>
                <td width="350" align="center"><a href="index.html"><img src="images/logo.gif" alt="АвтоМечта" width="330" /></a></td>
                <td width="140"><a href="catalogue.php" id="menu_href"><img src="images/catalogue.png" alt="Каталог" height="70" /></a></td>
                <td width="140"><a href="payment.html" id="menu_href"><img src="images/payment.png" alt="Оплата" height="70" /></a></td>
                <td width="140"><a href="delivery.html" id="menu_href"><img src="images/delivery.png" alt="Доставка" height="70" /></a></td>
                <td width="140"><a href="basket.php" id="menu_href"><img src="images/basket.png" alt="Корзина" height="70" /></a></td>
                <td width="140"><a href="#" id="menu_href" onclick="enter_exit();"><img src="images/enter1.png" alt="Вход/Выход" height="70" id="enter" /></a></td>                
            </tr>
        </table>
    </div>
';
if (isset($_COOKIE["email_cookie"]) && isset($_COOKIE["salt_cookie"])) {
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
    if($array[0]==$salt) { //соли совпали
        echo '<script>document.getElementById("enter").src = "images/exit.png";</script>';
    }
    else {
        echo '<script>document.getElementById("enter").src = "images/enter.png";</script>';
    }
  } 
  else {
      echo '<script>document.getElementById("enter").src = "images/enter.png";</script>';
  }
  
}
else {
    echo '<script>document.getElementById("enter").src = "images/enter.png";</script>';
}

?>

