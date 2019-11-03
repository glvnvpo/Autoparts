<?php
if(isset($_POST['email_auth']) && isset($_POST['password_auth'])) 
{
    $email = htmlentities($_POST['email_auth']);
    $password = htmlentities($_POST['password_auth']);
    
    require_once("config.php");

    $query = "SELECT COUNT(*) FROM CLIENT WHERE EMAIL='$email'";  
    $res = mysqli_query($mysqli, $query); 
    $array = mysqli_fetch_array($res); //проверяем, есть ли пользователь с таким email в таблице
    $count = $array[0];
    
    if ($count != 0) { //пользователь есть в таблице
    
        $query = "SELECT PASSWRD FROM CLIENT WHERE EMAIL='$email'"; 
        $res = mysqli_query($mysqli, $query);
        $array = mysqli_fetch_array($res); //результатом является одна строка
        $passwrd = $array[0]; //извлекаем результат запроса 


        $password = md5($password);

        if ($password == $passwrd) {
            
            $query = "SELECT SALT FROM CLIENT WHERE EMAIL='$email'"; //выпрашиваем соль из таблицы
            $res = mysqli_query($mysqli, $query);
            $array = mysqli_fetch_array($res); //результатом является одна строка
            $salt = $array[0]; //извлекаем результат запроса 
            
            setcookie("email_cookie", $email, time()+3600);  /* срок действия 1 час */
            setcookie("salt_cookie", $salt, time()+3600);  /* срок действия 1 час */
            
            include "master_page_header.php";
            echo 'Авторизация прошла успешно!   :)'.'<style> body {font-family: Arial; font-size: 30pt;}</style><script language="JavaScript" type="text/javascript">'
                .'function changeurl(){eval(self.location="index.html");};'
                .'window.setTimeout("changeurl();",3000);'
                .'</script>';
        }
        else {           
            
            include "master_page_header.php";
            echo 'Неверный пароль!'.'<style> body {font-family: Arial; font-size: 30pt;}</style><script language="JavaScript" type="text/javascript">'
                .'function changeurl(){eval(self.location="enter.html");}'
                .'window.setTimeout("changeurl();",3000);'
                .'</script>';
                       
        }

    }

    else {
        include "master_page_header.php";
                echo "Пользователь  ".$email." не найден!".'<style> body {font-family: Arial; font-size: 30pt;}</style><script language="JavaScript" type="text/javascript">'
                    .'function changeurl(){eval(self.location="enter.html");}'
                    .'window.setTimeout("changeurl();",3000);'
                    .'</script>';
    }
}
else //пользователя нет в таблице
{   
    echo "Введенные данные некорректны";
}
?>