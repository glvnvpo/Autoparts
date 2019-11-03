<?php
if(isset($_POST['email']) && isset($_POST['fio']) && 
    isset($_POST['address']) && isset($_POST['password'])) 
{
    $email = htmlentities($_POST['email']);
    $fio = htmlentities($_POST['fio']);
    $address = htmlentities($_POST['address']);
    $password = htmlentities($_POST['password']);
    
    require_once("config.php");

    $query = "SELECT COUNT(*) FROM CLIENT WHERE EMAIL='$email'";  
    $res = mysqli_query($mysqli, $query); 
    $array = mysqli_fetch_array($res);
    $count = $array[0];
    if ($count == 0) { //если пользователя с таким email нет в таблице
    
        function generateSalt() //функция для генерации соли
            {
                    $salt = '';
                    $saltLength = 6; //длина соли
                    for($i=0; $i<$saltLength; $i++) {
                            $salt .= chr(mt_rand(33,126)); //символ из ASCII-table
                    }
                    return $salt;
            }
            
        $salt = generateSalt();
    
        $password = md5($password); //хешируем пароль
    
        $query = "INSERT INTO client SET id_client=NULL, fio='$fio', address='$address', email='$email', passwrd='$password', salt='$salt'";
        if(!mysqli_query($mysqli, $query))
        {
            exit('Ошибка в добавлении нового пользователя.');
        }
        else {
            
            include "master_page_header.php";
            echo "Новый пользователь добавлен!   :) <br> Теперь Вы можете авторизоваться.".'<style> body {font-family: Arial; font-size: 30pt;}</style><script language="JavaScript" type="text/javascript">'
                .'function changeurl(){eval(self.location="index.html");}'
                .'window.setTimeout("changeurl();",3000);'
                .'</script>';
            
        }
    }
    else { //пользователь с таким email уже есть в таблице
        include "master_page_header.php";
        echo "Такой пользователь уже есть!".'<style> body {font-family: Arial; font-size: 30pt;}</style><script language="JavaScript" type="text/javascript">'
                .'function changeurl(){eval(self.location="enter.html");}'
                .'window.setTimeout("changeurl();",3000);'
                .'</script>';;
    };
    
}
else //пользователь ввел что-то не то
{   
    include "master_page_header.php";
    echo "Введенные данные некорректны".'<style> body {font-family: Arial; font-size: 30pt;}</style><script language="JavaScript" type="text/javascript">'
                .'function changeurl(){eval(self.location="enter.html");}'
                .'window.setTimeout("changeurl();",3000);'
                .'</script>';;
}
?>