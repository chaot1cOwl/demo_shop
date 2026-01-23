-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Дек 27 2025 г., 07:57
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `shop_db`
--
CREATE DATABASE IF NOT EXISTS `shop_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `shop_db`;

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Название категории',
  `parent` int(11) NOT NULL DEFAULT 0 COMMENT 'ID родительской категории'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Категории товаров';

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent`) VALUES
(1, 'Кофе', 0),
(2, 'Выпечка', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `timecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp() COMMENT 'Дата и время оформления заказа',
  `client_name` varchar(100) NOT NULL COMMENT 'Имя покупателя',
  `client_address` varchar(1000) NOT NULL COMMENT 'Адрес покупателя',
  `status` int(11) NOT NULL DEFAULT 0 COMMENT 'Статус заказа:\r\n0 - новый\r\n1 - выполнен\r\n100 - удален'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Заказы';

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(1000) NOT NULL COMMENT 'Название товара',
  `description` text NOT NULL DEFAULT 'Описание' COMMENT 'Описание товара',
  `image` varchar(4096) NOT NULL DEFAULT 'default.jpg' COMMENT 'Изображение товара',
  `price` int(11) NOT NULL COMMENT 'Цена товара',
  `id_categories` int(11) NOT NULL COMMENT 'ID категории',
  `deleted` int(11) NOT NULL DEFAULT 0 COMMENT 'Флаг удаления товара'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Товары';

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `image`, `price`, `id_categories`) VALUES
(1, 'Круассан', 'Вкусный, воздушный, сладкий', 'croissant.png', 100, 2),
(2, 'Пончик', 'Яркий, сладкий, круглый', 'donut2.png', 75, 2),
(3, 'Эспрессо', 'Заряд бодрости в чашке', 'coffee1.png', 150, 1),
(4, 'Капучино', 'Сладкий заряд бодрости', 'coffee2.png', 200, 1),
(5, 'Черничный пончик', 'Классика с черничным джемом', 'donut1.png', 125, 2),
(6, 'Банановый пончик', 'Как тропики с каплей шоколада', 'donut3.png', 175, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `products_to_orders`
--

CREATE TABLE `products_to_orders` (
  `id` int(11) NOT NULL,
  `id_products` int(11) NOT NULL COMMENT 'ID товара',
  `id_orders` int(11) NOT NULL COMMENT 'ID заказа',
  `count` int(11) NOT NULL COMMENT 'количество'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Связь продуктов и заказов';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `products` ADD FULLTEXT KEY `name` (`name`);

--
-- Индексы таблицы `products_to_orders`
--
ALTER TABLE `products_to_orders`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `products_to_orders`
--
ALTER TABLE `products_to_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
