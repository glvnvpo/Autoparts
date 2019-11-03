-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Сен 11 2019 г., 19:36
-- Версия сервера: 10.1.30-MariaDB
-- Версия PHP: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `autoparts`
--

-- --------------------------------------------------------

--
-- Структура таблицы `basket`
--

CREATE TABLE `basket` (
  `ID_CLIENT` int(11) NOT NULL,
  `ID_PRODUCT` int(11) NOT NULL,
  `AMOUNT` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `basket`
--

INSERT INTO `basket` (`ID_CLIENT`, `ID_PRODUCT`, `AMOUNT`) VALUES
(25, 10, 1),
(25, 17, 5),
(30, 7, 1),
(30, 10, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `client`
--

CREATE TABLE `client` (
  `ID_CLIENT` int(11) NOT NULL,
  `FIO` text NOT NULL,
  `ADDRESS` text NOT NULL,
  `PASSWRD` text,
  `EMAIL` text NOT NULL,
  `SALT` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `client`
--

INSERT INTO `client` (`ID_CLIENT`, `FIO`, `ADDRESS`, `PASSWRD`, `EMAIL`, `SALT`) VALUES
(1, 'Тыковкин Аристарх Игоревич', 'г.Москва, ул. Удальцова, д.11, кв.33', 'b800085ef52b77b688ba7a7cc97c4f14', 'super_tykva@inbox.ru', 'gzzVVL'),
(2, 'Лаврушина Мария Кондратьевна', 'поселок Совхоза имена Ленина. д.20, кв.60', '2844018b3fc9c6bc9fb76166bee2c87d', 'lavrusha@gmail.com', 'ZuQgXW'),
(3, 'Щербаковский Семен Викторович', 'г.Москва, ул.Кржижановского, д.17, корп.3, кв.205', 'd7604cac69753c55ede5634055e27325', 'semen01031988@yandex.ru', 'j5JVzA'),
(4, 'АДМИН', 'Администрация', '21232f297a57a5a743894a0e4a801fc3', 'admin@ya.ru', '6nI32a'),
(25, 'Васильев Василий Иванович', 'г.Москва, ул.Мусы Джалиля, д.15, корп.3, кв.107', '202cb962ac59075b964b07152d234b70', 'v@ya.ru', 'H3imM4'),
(29, 'Грачев Владимир Егорович', 'г.Москва, Загорьевская улица д.10к2, кв.20', 'a342328c519e837d55b72f856a82855c', 'vladimir_grach@mail.ru', 'AAl9ws'),
(30, 'Рябинин Святослав Ростиславович', 'г.Москва, Михневский проезд, д.8, кв.11', '73df663d7cad64e9ee6ec4ad6940c813', 'ryabinin@ya.ru', 'kUycv3'),
(31, 'Митрошина Евгения Алексеевна', 'МО, г.Орехово-Зуево, ул. Кооперативная, д.12, кв.80', '202cb962ac59075b964b07152d234b70', 'mitroshina_ea@inbox.ru', 'LiIEVK'),
(32, 'Пчеловечкин Игорь Панкратович', 'г.Москва, ул.Медовая, д.3, корп.1, кв.10', '202cb962ac59075b964b07152d234b70', 'pchelovek@mail.ru', 'l:OSDj');

-- --------------------------------------------------------

--
-- Структура таблицы `kind`
--

CREATE TABLE `kind` (
  `ID_KIND` int(11) NOT NULL,
  `KIND_NAME` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `kind`
--

INSERT INTO `kind` (`ID_KIND`, `KIND_NAME`) VALUES
(1, 'Муфта'),
(2, 'Сцепление'),
(3, 'Подшипник'),
(4, 'Зеркало'),
(5, 'Амортизатор'),
(6, 'Диск колесный');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `ID_ORDER` int(11) NOT NULL,
  `ID_CLIENT` int(11) NOT NULL,
  `TOTAL_PRICE` double NOT NULL,
  `PAYED` tinyint(1) DEFAULT NULL,
  `DELIVERED` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`ID_ORDER`, `ID_CLIENT`, `TOTAL_PRICE`, `PAYED`, `DELIVERED`) VALUES
(1, 25, 1300, 1, 1),
(3, 25, 1010, 1, 1),
(4, 30, 4400, 1, 1),
(5, 25, 9700, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `order_product_amount`
--

CREATE TABLE `order_product_amount` (
  `ID_ORDER` int(11) NOT NULL,
  `ID_PRODUCT` int(11) NOT NULL,
  `AMOUNT` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `order_product_amount`
--

INSERT INTO `order_product_amount` (`ID_ORDER`, `ID_PRODUCT`, `AMOUNT`) VALUES
(1, 16, 2),
(3, 10, 2),
(3, 15, 1),
(4, 11, 2),
(4, 18, 1),
(5, 17, 5),
(5, 18, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `product`
--

CREATE TABLE `product` (
  `ID_PRODUCT` int(11) NOT NULL,
  `PRODUCT_NAME` text NOT NULL,
  `PRICE` int(11) NOT NULL,
  `ID_KIND` int(11) NOT NULL,
  `IMAGE` text,
  `ID_SUPPLIER` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `product`
--

INSERT INTO `product` (`ID_PRODUCT`, `PRODUCT_NAME`, `PRICE`, `ID_KIND`, `IMAGE`, `ID_SUPPLIER`) VALUES
(4, 'Муфта эластичная ВАЗ 2101-07 БРТ 2101-2202120Р', 360, 1, 'муфтаваз2101.jpg', 1),
(7, 'Сцепление в сборе ВАЗ 2123 KRAFTTECH', 3600, 2, 'сцеплениеваз2123.jpg', 2),
(8, 'Муфта синхронизатора КПП ВАЗ 2101-07 5-й пер в упак LADA 21070-1701176-00', 840, 1, 'муфтасинхронизатора.jpg', 3),
(9, 'Подшипник коленвала ВАЗ 2101-07, 180502, 62202 ', 60, 3, 'подшипникколенвалаваз2.jpg', 3),
(10, 'Зеркало внутрисалонное ВАЗ 2108-09 сферическое Рекардо RR01076 ', 140, 4, 'зеркаловнутрисалонноеВАЗ2108.jpg', 3),
(11, 'Муфта соединительная тормозных трубок для а/м ГАЗ-3110 24-3506094 ', 100, 1, 'муфтасоединительнаятормозныхтрубок.jpg', 3),
(13, 'Задний амортизатор со шлангом (1998 to 2000) (pd29717pd)', 169280, 5, 'заднийамортизатор1.jpg', 4),
(14, '19-дюймовый пятиспицевый колесный Диск колёсный', 127530, 6, 'колесныйдиск1.jpg', 4),
(15, 'Диск сцепления STELLOX', 730, 2, 'дисксцепления1.jpg', 5),
(16, 'Амортизатор масляный, задний, STELLOX', 650, 5, 'амортизаторзадний1.jpg', 5),
(17, 'Диск колеса LADA Largus «ТЗСК» R15 (Серебристый)', 1100, 6, 'дискштампованныйлада1.png', 3),
(18, 'Сцепление в сборе KRAFTTECH W01220B9 ', 4200, 2, 'сцеплениевсборе1.jpg', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `supplier`
--

CREATE TABLE `supplier` (
  `ID_SUPPLIER` int(11) NOT NULL,
  `SUPPLIER_NAME` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Дамп данных таблицы `supplier`
--

INSERT INTO `supplier` (`ID_SUPPLIER`, `SUPPLIER_NAME`) VALUES
(1, 'БРТ'),
(2, 'KRAFTTECH'),
(3, 'LADA'),
(4, 'Bentley'),
(5, 'STELLOX');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `basket`
--
ALTER TABLE `basket`
  ADD PRIMARY KEY (`ID_CLIENT`,`ID_PRODUCT`),
  ADD KEY `ID_PRODUCT` (`ID_PRODUCT`);

--
-- Индексы таблицы `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`ID_CLIENT`);

--
-- Индексы таблицы `kind`
--
ALTER TABLE `kind`
  ADD PRIMARY KEY (`ID_KIND`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`ID_ORDER`),
  ADD KEY `ID_CLIENT` (`ID_CLIENT`);

--
-- Индексы таблицы `order_product_amount`
--
ALTER TABLE `order_product_amount`
  ADD PRIMARY KEY (`ID_ORDER`,`ID_PRODUCT`,`AMOUNT`),
  ADD KEY `ID_PRODUCT` (`ID_PRODUCT`);

--
-- Индексы таблицы `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ID_PRODUCT`),
  ADD KEY `ID_KIND` (`ID_KIND`),
  ADD KEY `ID_SUPPLIER` (`ID_SUPPLIER`);

--
-- Индексы таблицы `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`ID_SUPPLIER`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `client`
--
ALTER TABLE `client`
  MODIFY `ID_CLIENT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT для таблицы `kind`
--
ALTER TABLE `kind`
  MODIFY `ID_KIND` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `ID_ORDER` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `product`
--
ALTER TABLE `product`
  MODIFY `ID_PRODUCT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `supplier`
--
ALTER TABLE `supplier`
  MODIFY `ID_SUPPLIER` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `basket`
--
ALTER TABLE `basket`
  ADD CONSTRAINT `basket_ibfk_1` FOREIGN KEY (`ID_CLIENT`) REFERENCES `client` (`ID_CLIENT`),
  ADD CONSTRAINT `basket_ibfk_2` FOREIGN KEY (`ID_PRODUCT`) REFERENCES `product` (`ID_PRODUCT`);

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`ID_CLIENT`) REFERENCES `client` (`ID_CLIENT`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `order_product_amount`
--
ALTER TABLE `order_product_amount`
  ADD CONSTRAINT `order_product_amount_ibfk_1` FOREIGN KEY (`ID_ORDER`) REFERENCES `orders` (`ID_ORDER`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `order_product_amount_ibfk_2` FOREIGN KEY (`ID_PRODUCT`) REFERENCES `product` (`ID_PRODUCT`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`ID_KIND`) REFERENCES `kind` (`ID_KIND`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`ID_SUPPLIER`) REFERENCES `supplier` (`ID_SUPPLIER`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
