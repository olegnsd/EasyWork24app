<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_sms.php';

// Класс авторизации
$auth = new CAuth($site_db);

$login = value_proc($_POST['login']);

$password = value_proc($_POST['password']);

$sms_code = value_proc($_POST['sms_code']);

$is_sms_code = value_proc($_POST['is_sms_code']);

$auth_method_proc = value_proc($_POST['auth_method_proc']);

$restore_by_sms_code = value_proc($_POST['restore_by_sms_code']);

// Устанавливаем переменные
$auth->set_auth_login($login);

$auth->set_auth_password($password);

$auth->set_auth_remember($remember);

$auth->set_auth_sms_code($sms_code);

$auth->set_auth_method_proc($auth_method_proc);

$auth->set_restore_by_sms_code($restore_by_sms_code);

// Авторизация
$auth->pre_auth_proc();

// результаты
$success = $auth->get_auth_success();

$error = $auth->get_auth_error();

// Метод авторизации
$auth_method = $auth->get_auth_method();


// Возвращаем результат
echo json_encode(array('success' => $success, 'auth_method' => $auth_method, 'auth_protect_by_sms'=> $auth_protect_by_sms, 'auth_by_sms' => $auth_by_sms, 'auth_activate_by_sms' => $auth_activate_by_sms, 'error' => iconv("windows-1251", "UTF-8", $error)));
?>