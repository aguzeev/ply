-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: openserver:3306
-- Время создания: Июн 27 2012 г., 14:48
-- Версия сервера: 5.5.24-log
-- Версия PHP: 5.4.3

SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `auth`
--

-- --------------------------------------------------------

--
-- Структура таблицы `appointments`
--

CREATE TABLE IF NOT EXISTS "appointments" (
  "id" int(10) unsigned NOT NULL AUTO_INCREMENT,
  "appointment" varchar(70) NOT NULL,
  "department" smallint(5) unsigned NOT NULL DEFAULT '0',
  "allowedOperationsDefault" text,
  PRIMARY KEY ("id")
) AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `appointments`
--

INSERT INTO `appointments` (`id`, `appointment`, `department`, `allowedOperationsDefault`) VALUES
(1, 'Мастер участка', 10, 'a:3:{i:0;s:1:"6";i:1;s:1:"8";i:2;s:1:"9";}'),
(4, 'Мастер приёмки кряжа', 10, 'a:3:{i:0;s:2:"16";i:1;s:2:"18";i:2;s:2:"19";}'),
(5, 'Директор', 1, 's:4:"null";'),
(6, 'Зам директора', 1, 'a:1:{i:0;s:1:"6";}');

-- --------------------------------------------------------

--
-- Структура таблицы `departments`
--

CREATE TABLE IF NOT EXISTS "departments" (
  "id" smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  "departmentName" varchar(50) NOT NULL,
  PRIMARY KEY ("id")
) AUTO_INCREMENT=26 ;

--
-- Дамп данных таблицы `departments`
--

INSERT INTO `departments` (`id`, `departmentName`) VALUES
(1, 'Управление'),
(2, 'Вахта №1'),
(3, 'Вахта №2'),
(4, 'Общежитие'),
(5, 'Гараж'),
(6, 'Электрики'),
(7, 'РММ'),
(8, 'Стройгруппа'),
(9, 'ПСХ'),
(10, 'Лесопиление'),
(11, 'ИТР и вспомогательные'),
(12, 'Новый пресс'),
(13, 'Рауте'),
(14, 'Сушилка'),
(15, 'Клеевальцы'),
(16, 'Старый пресс'),
(17, 'Опиловка'),
(18, 'Линия сращивания'),
(19, 'Шпонопочинка'),
(20, 'Ножницы'),
(21, 'Упаковка'),
(22, 'Дробилка'),
(23, 'Ребросклейка'),
(24, 'Уборка мусора'),
(25, 'Сортировка шпона');

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE IF NOT EXISTS "groups" (
  "id" int(5) NOT NULL AUTO_INCREMENT,
  "group_name" varchar(60) NOT NULL,
  "redirect_url" varchar(50) DEFAULT NULL,
  PRIMARY KEY ("id")
) AUTO_INCREMENT=21 ;

--
-- Дамп данных таблицы `groups`
--

INSERT INTO `groups` (`id`, `group_name`, `redirect_url`) VALUES
(1, 'Дирекция завода', NULL),
(2, 'Дирекция ЦО', NULL),
(3, 'Руководитель проектов в ЦО', ''),
(4, 'Бухгалтерия завода', NULL),
(5, 'Бухгалтерия ЦО', NULL),
(6, 'Специалисты отдела кадров завода', NULL),
(7, 'Контролёр отдела кадров завода', NULL),
(20, 'Администратор системы', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `groups_permitions`
--

CREATE TABLE IF NOT EXISTS "groups_permitions" (
  "id" int(5) unsigned NOT NULL AUTO_INCREMENT,
  "group" int(5) unsigned NOT NULL DEFAULT '0' COMMENT 'ID группы пользователей',
  "area" int(5) unsigned DEFAULT '0' COMMENT 'ID категории материала',
  "permition" int(3) unsigned DEFAULT '0' COMMENT 'Права на категорию для группы',
  PRIMARY KEY ("id")
) AUTO_INCREMENT=36 ;

--
-- Дамп данных таблицы `groups_permitions`
--

INSERT INTO `groups_permitions` (`id`, `group`, `area`, `permition`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 1, 4, 1),
(5, 1, 5, 1),
(6, 2, 1, 1),
(7, 2, 2, 1),
(8, 2, 3, 1),
(9, 2, 4, 1),
(10, 2, 5, 1),
(11, 3, 1, 1),
(12, 3, 2, 1),
(13, 3, 3, 1),
(14, 3, 4, 1),
(15, 3, 5, 1),
(16, 4, 1, 1),
(17, 4, 2, 1),
(18, 4, 3, 1),
(19, 4, 4, 1),
(20, 4, 5, 1),
(21, 5, 1, 1),
(22, 5, 2, 2),
(23, 5, 3, 1),
(24, 5, 4, 2),
(25, 5, 5, 1),
(26, 6, 1, 2),
(27, 6, 2, 1),
(28, 6, 3, 1),
(29, 6, 4, 1),
(30, 6, 5, 1),
(31, 7, 1, 1),
(32, 7, 2, 1),
(33, 7, 3, 2),
(34, 7, 4, 1),
(35, 7, 5, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `operations`
--

CREATE TABLE IF NOT EXISTS "operations" (
  "id" smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  "operation" varchar(50) NOT NULL,
  PRIMARY KEY ("id")
) AUTO_INCREMENT=24 ;

--
-- Дамп данных таблицы `operations`
--

INSERT INTO `operations` (`id`, `operation`) VALUES
(6, 'Лущение 1'),
(8, 'Лущение 2'),
(9, 'Лущение 3'),
(16, 'Лущение 4'),
(18, 'Лущение 5'),
(19, 'Лущение 6'),
(22, 'Лущение 7'),
(23, 'Лущение 8');

-- --------------------------------------------------------

--
-- Структура таблицы `production`
--

CREATE TABLE IF NOT EXISTS "production" (
  "id" int(10) unsigned NOT NULL AUTO_INCREMENT,
  "date" date NOT NULL,
  "worker" int(10) unsigned NOT NULL,
  "operation" smallint(5) unsigned NOT NULL,
  "value" float(5,3) DEFAULT '0.000',
  "cost" float(5,3) DEFAULT '0.000',
  PRIMARY KEY ("id")
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `rates`
--

CREATE TABLE IF NOT EXISTS "rates" (
  "operationId" smallint(5) unsigned NOT NULL,
  "rate1" float(5,3) unsigned DEFAULT NULL,
  "rate2" float(5,3) unsigned DEFAULT NULL,
  "conditionFor2" float(5,3) unsigned DEFAULT NULL,
  "rate3" float(5,3) unsigned DEFAULT NULL,
  "conditionFor3" float(5,3) unsigned DEFAULT NULL,
  PRIMARY KEY ("operationId")
);

-- --------------------------------------------------------

--
-- Структура таблицы `sectors`
--

CREATE TABLE IF NOT EXISTS "sectors" (
  "id" smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  "parentDept" smallint(5) unsigned NOT NULL,
  "sectorName" varchar(50) NOT NULL,
  PRIMARY KEY ("id")
) AUTO_INCREMENT=17 ;

--
-- Дамп данных таблицы `sectors`
--

INSERT INTO `sectors` (`id`, `parentDept`, `sectorName`) VALUES
(1, 10, 'Лесопиление'),
(2, 10, 'ИТР и вспомогательные'),
(3, 10, 'Новый пресс'),
(4, 10, 'Рауте'),
(5, 10, 'Сушилка'),
(6, 10, 'Клеевальцы'),
(7, 10, 'Старый пресс'),
(8, 10, 'Опиловка'),
(9, 10, 'Линия сращивания'),
(10, 10, 'Шпонопочинка'),
(11, 10, 'Ножницы'),
(12, 10, 'Упаковка'),
(13, 10, 'Дробилка'),
(14, 10, 'Ребросклейка'),
(15, 10, 'Уборка мусора'),
(16, 10, 'Сортировка шпона');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS "users" (
  "id" int(10) unsigned NOT NULL AUTO_INCREMENT,
  "login" varchar(20) NOT NULL,
  "name" varchar(50) DEFAULT NULL,
  "group" int(10) unsigned DEFAULT '0',
  "password" varchar(32) NOT NULL,
  "pepper" char(3) NOT NULL,
  "redirect_url" varchar(70) DEFAULT NULL,
  PRIMARY KEY ("id"),
  UNIQUE KEY "login" ("login")
) AUTO_INCREMENT=12 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `name`, `group`, `password`, `pepper`, `redirect_url`) VALUES
(1, 'admin', 'Администратор', 20, 'ed8c68f767e7c94d83fed932287bd4ea', '17w', 'users'),
(2, 'user1', 'Пользователь 1', 1, '45065decdbb93685e41f7bff179a9208', '55,', 'users'),
(5, 'user5', 'Пользователь 5', 5, 'a4e63b1884bf48666bc252771cb94118', '=f*', 'users'),
(9, 'user7', 'User seven', 7, 'edd7bcfd374069171987b32a62a02836', '--p', 'users'),
(11, 'user8', 'User eight', 1, '17ec0c80d3645fd9fe10974568c56d94', '6ti', 'users');

-- --------------------------------------------------------

--
-- Структура таблицы `workers`
--

CREATE TABLE IF NOT EXISTS "workers" (
  "id" int(10) unsigned NOT NULL AUTO_INCREMENT,
  "name_family" varchar(50) DEFAULT NULL,
  "name_first" varchar(50) DEFAULT NULL,
  "name_middle" varchar(50) DEFAULT NULL,
  "sector" int(5) unsigned DEFAULT '0',
  "appointment" smallint(5) unsigned NOT NULL,
  "additionalOperations" text,
  "isActive" tinyint(4) DEFAULT '1',
  PRIMARY KEY ("id")
) AUTO_INCREMENT=21 ;

--
-- Дамп данных таблицы `workers`
--

INSERT INTO `workers` (`id`, `name_family`, `name_first`, `name_middle`, `sector`, `appointment`, `additionalOperations`, `isActive`) VALUES
(1, '', '', '', 1, 0, 'a:2:{i:0;s:1:"6";i:1;s:1:"9";}', 0),
(2, 'Скалозуб', 'Семён', 'Семёнович', 10, 1, '', 1),
(8, 'Ива', 'Ива', 'Ива', 10, 4, '', 1),
(9, 'Петров', 'Петров', 'Петров', 10, 1, '', 1),
(18, 'werw', 'werw', 'wer', 10, 1, 'a:1:{i:0;s:2:"19";}', 1),
(19, 'ewr', 'er', 'er', 10, 1, 'a:1:{i:0;s:1:"9";}', 1),
(20, 'пееа', 'асире', 'офевстим', 10, 1, '', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
