<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/client/startup.php';

// Класс авторизации
$auth = new CAuth($site_db);

$login = value_proc($_POST['login']);

$password = value_proc($_POST['password']);

// Устанавливаем переменные
$auth->set_auth_login($login);

$auth->set_auth_password($password);

// Авторизация
$auth->auth_proc();

// результаты
$success = $auth->get_auth_success();

$error = $auth->get_auth_error();

// Возвращаем результат
echo json_encode(array('success' => $success, 'error' => iconv("windows-1251", "UTF-8", $error)));
?>