<?php
ini_set("display_errors","0");
header("Content-Type: text/html; charset=windows-1251");
// Опознание сервера
include_once $_SERVER['DOCUMENT_ROOT'].'/servers_config.php'; //
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_servers.php';
server_detect();

include_once $_SERVER['DOCUMENT_ROOT'].'/config.php'; //Настройки
include_once $_SERVER['DOCUMENT_ROOT'].'/config_tables.php'; //Настройки
include_once $_SERVER['DOCUMENT_ROOT'].'/global.php'; //Создание объекта БД

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions.php'; // Функциии
include_once $_SERVER['DOCUMENT_ROOT'].'/client/includes/functions_head.php'; // Функциии тега <HEAD>
include_once $_SERVER['DOCUMENT_ROOT'].'/client/includes/functions_left_menu.php'; // Функциии левого меню
include_once $_SERVER['DOCUMENT_ROOT'].'/client/includes/functions_top_panel.php'; // Функциии верней панели
include_once $_SERVER['DOCUMENT_ROOT'].'/client/includes/functions_navigation.php'; // Строка навигации
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_clients.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_error.php';

// Класс авторизации
include_once $_SERVER['DOCUMENT_ROOT'].'/client/classes/class.CAuth.php';
$auth_obj = new CAuth($site_db);

// Класс пользователя
include_once $_SERVER['DOCUMENT_ROOT'].'/classes/class.CUser.php';
$user_obj = new CUser($site_db);




?>