<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_auto.php';
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

		
// Проверка авторизации
if(!$auth->check_auth())
{
	exit();
}
		
switch($mode)
{
	// Обновить фрейм
	case 'save_external_service_iframe_data':
		
		$user_id = value_proc($_POST['user_id']);
		
		$service_id = value_proc($_POST['service_id']);
		
		$iframe_src = value_proc($_POST['iframe_src']);
		
		$iframe_text = value_proc($_POST['iframe_text']);
		
		if($user_id!=$current_user_id)
		{
			exit();
		}
		
		$sql = "SELECT * FROM ".EXTERNAL_TB." WHERE user_id='$user_id' AND service_id='$service_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{		
			// Обновляем данные
			$sql = "UPDATE ".EXTERNAL_TB." SET service_id='$service_id', iframe_src='$iframe_src', iframe_text = '$iframe_text', date=NOW()
			 		WHERE user_id='$user_id' AND service_id='$service_id'";
			 
			$site_db->query($sql); 
		}
		else
		{
						// Обновляем данные
			$sql = "INSERT INTO ".EXTERNAL_TB." SET service_id='$service_id', user_id='$user_id', iframe_src='$iframe_src', iframe_text = '$iframe_text', date=NOW()";
			
			$site_db->query($sql);
		}
		
		if(!mysql_error())
		{
			$success = 1;
		}
		
		echo $success;
			
	break;
	
}

?>