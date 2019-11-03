//мастер-страница
text = "тут пишем html код\
Если переносим строку, то ставим \
Кавычки пишем так \"   ";//конец текста
alert("here!");

text = "<div id=\"headerMain\"> \
    <table id = \"header\" border = \"1\" > \
        <tr> \
            <td width=\"350\" align=\"center\"><a href=\"index.html\"><img src=\"logo.gif\" width=\"330\" /></a></td>\
            <td width=\"140\"><a href=\"http://localhost/Автозапчасти/catalogue.php\" id=\"menu_href\"><img src=\"catalogue.png\" height=\"70\" /></a></td>\
            <td width=\"140\"><a id=\"menu_href\"><img src=\"payment.png\" height=\"70\" /></a></td>\
            <td width=\"140\"><a id=\"menu_href\"><img src=\"delivery.png\" height=\"70\" /></a></td>\
            <td width=\"140\"><a id=\"menu_href\"><img src=\"enter.png\" height=\"70\" /></a></td>\
            <td width=\"140\"><a id=\"menu_href\"><img src=\"shopping_cart.png\" height=\"70\" /></a></td>\
        </tr>\
        </table>\
    </div>";
document.write(text);//печатаем html код