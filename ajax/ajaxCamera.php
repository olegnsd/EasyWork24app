<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_camera.php';
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

switch($mode)
{
	// Обновить фрейм
	case 'save_video_iframe_data':
		
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		}
		
		$camera_id = value_proc($_POST['camera_id']);
		
		$iframe_src = value_proc($_POST['iframe_src']);
		
		$iframe_text = value_proc($_POST['iframe_text']);
		
		// Данные по камере
		$sql = "SELECT user_id FROM ".CAMERAS_TB." WHERE camera_id='$camera_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['user_id']!=$current_user_id)
		{
			exit();
		}
		
		
		// Обновляем данные
		$sql = "UPDATE ".CAMERAS_TB." SET camera_src='$iframe_src', camera_text='$iframe_text' WHERE camera_id='$camera_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			$success = 1;
		}
		
		echo $success;
			
	break;
	
}

?>