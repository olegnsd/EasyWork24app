<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_cnews.php';

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
	// Добавить новость
	case 'add_cnews':
		
		$cnews_text = value_proc($_POST['cnews_text']);
		
		$cnews_theme = value_proc($_POST['cnews_theme']);
		 
		
		// Для админа
		if(!$current_user_obj->get_is_admin())
		{
			exit();
		}
	 
		if($cnews_text=='')
		{
			$error['cnews_text'] = 1;
		}
		if($cnews_theme=='')
		{
			$error['cnews_theme'] = 1;
		}
		
		if(!$error)
		{
			$sql = "INSERT INTO ".CNEWS_TB." SET cnews_text='$cnews_text', cnews_theme='$cnews_theme', user_id='$current_user_id', date_add=NOW()";
			$site_db->query($sql);
			
			$cnews_id = $site_db->get_insert_id();
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'cnews_id' => $cnews_id));
	break;
	
	// Сохранить новость
	case 'save_cnews':
		
		$cnews_id = value_proc($_POST['cnews_id']);
		
		$cnews_text = value_proc($_POST['cnews_text']);
		
		$cnews_theme = value_proc($_POST['cnews_theme']);
		
		$cnews_data = get_cnews_data($cnews_id);
		 
		// Для админа
		if(!$current_user_obj->get_is_admin())
		{
			exit();
		}
		 
		if($cnews_text=='')
		{
			$error['cnews_text'] = 1;
		}
		if($cnews_theme=='')
		{
			$error['cnews_theme'] = 1;
		}
		
		if(!$error)
		{
			$sql = "UPDATE ".CNEWS_TB." SET cnews_text='$cnews_text', cnews_theme='$cnews_theme', date_update=NOW() WHERE cnews_id='$cnews_id'";
			$site_db->query($sql);
			
			$cnews_id = $site_db->get_insert_id();
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'cnews_id' => $cnews_id));
	break;
	
	case 'get_cnews_item':
	
		$cnews_id = value_proc($_POST['cnews_id']);
		
		$form = value_proc($_POST['form']);
		
		$cnews_data = get_cnews_data($cnews_id);
		
		$cnews_item = fill_cnews_item($cnews_data, $form);
		
		echo $cnews_item;
	break;
	
	case 'get_more_cnews':
		
		$page = value_proc($_POST['page']);
		
		$cnews_list = fill_cnews_list($page);
		
		echo $cnews_list;
	break;
	
	case 'delete_cnews':
		
		$cnews_id = value_proc($_POST['cnews_id']);
		
		$cnews_data = get_cnews_data($cnews_id);
		
		// Для админа
		if(!$current_user_obj->get_is_admin())
		{
			exit();
		}
		
		$sql = "UPDATE ".CNEWS_TB." SET deleted=1 WHERE cnews_id='$cnews_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			$_SESSION['cnews_delete'][] = $cnews_id;
				
			echo 1;
			
		}
	
	break;
	
	case 'restore_cnews':
		
		$cnews_id = value_proc($_POST['cnews_id']);
		
		$cnews_data = get_cnews_data($cnews_id);
		
		// Для админа
		if(!$current_user_obj->get_is_admin())
		{
			exit();
		}
		
		$sql = "UPDATE ".CNEWS_TB." SET deleted=0 WHERE cnews_id='$cnews_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			unset($_SESSION['cnews_delete'][$cnews_id]);
				
			echo 1;
		}
	
	break;
}

?>