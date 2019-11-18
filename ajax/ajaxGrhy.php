<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_grhy.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_worktime.php';

// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

if(!$current_user_id)
{
	exit();
}

switch($mode)
{
	// Добавить контакт сотрудника
	case 'get_user_info':
	
		$users_arr = json_decode(str_replace('\\', '', $_POST['users_arr']));
		
		$scheme = value_proc($_POST['scheme']);
		
		$cont_type = value_proc($_POST['cont_type']);
		
		$users_cont_arr = ghrh_get_user_cont($users_arr, $cont_type);

		// iconv('cp1251', 'utf-8', 
		
		echo json_encode(array('users_data' => $users_cont_arr));
		
	break;
}

?>