<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_auto.php';
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

switch($mode)
{
	// Обновить фрейм
	case 'save_auto_iframe_data':
		
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		}
		
		$auto_id = value_proc($_POST['auto_id']);
		
		$iframe_src = value_proc($_POST['iframe_src']);
		
		$iframe_text = value_proc($_POST['iframe_text']);
		
		// Данные по камере
		$sql = "SELECT user_id FROM ".AUTO_TB." WHERE auto_id='$auto_id'";
		
		$row = $site_db->query_firstrow($sql);
		 
		if($row['user_id']!=$current_user_id)
		{
			exit();
		}
		
		// Обновляем данные
		$sql = "UPDATE ".AUTO_TB." SET auto_src='$iframe_src', auto_text='$iframe_text' WHERE auto_id='$auto_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			$success = 1;
		}
		
		echo $success;
			
	break;
	
}

?>