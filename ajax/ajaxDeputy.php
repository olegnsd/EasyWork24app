<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';

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
	case 'deputy_get_dept_users':
		
		$dept_id = value_proc($_POST['dept_id']);
		
		$users_list = get_deputy_depts_users_list($dept_id);
		
		echo $users_list;
		
	break;
	// Добавить оф документ
	case 'add_deputy':
		
		$dept_id = value_proc($_POST['dept_id']);
		
		$deputy_user_id = value_proc($_POST['deputy_user_id']);
		
		 
		if(!$dept_id)
		{
			$error['dept_id'] = 1;
		}
		if(!$deputy_user_id)
		{
			$error['deputy_user_id'] = 1;
		}
		
		
		if(!$error)
		{
			// Добавляем запись о заместителе
			$sql = "INSERT INTO ".DEPUTY_TB." (dept_id, by_user_id, deputy_user_id, date) VALUES ('$dept_id', '$current_user_id', '$deputy_user_id', NOW())";
			
			$site_db->query($sql);
			
			$deputy_id = $site_db->get_insert_id(); 
			
			
			$success = 1;
		}
			
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'deputy_id' => $deputy_id));
	
	break;
	
	case 'delete_deputy':
		
		$deputy_id = value_proc($_POST['deputy_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		if(!($deputy_id>0))
		{
			exit();
		}
		
		$sql = "SELECT * FROM ".DEPUTY_TB." WHERE deputy_id='$deputy_id'";
		
		$depyty_data = $site_db->query_firstrow($sql);
		
		if($depyty_data['by_user_id']!=$current_user_id)
		{
			exit();
		}
		
		$sql = "DELETE FROM ".DEPUTY_TB." WHERE deputy_id='$deputy_id' AND by_user_id='$current_user_id'";
		
		$site_db->query($sql);

		
		echo 1;
	break;
	
	case 'restore_deputy':
		
		/*$deputy_id = value_proc($_POST['deputy_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "SELECT * FROM ".DEPUTY_TB." WHERE deputy_id='$deputy_id'";
		
		$depyty_data = $site_db->query_firstrow($sql);
		
		if($depyty_data['boss_id']!=$current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".DEPUTY_TB." SET deleted=0 WHERE deputy_id='$deputy_id' AND boss_id='$user_id'";
		
		$site_db->query($sql);
		
		$sql = "UPDATE ".WORKERS_TB." SET deleted=0 WHERE deputy_id='$deputy_id'";
		
		$site_db->query($sql);
		
		echo 1;*/
	break;
	
	case 'get_deputy_item':
		
		$deputy_id = value_proc($_POST['deputy_id']);
		
		// Данные документа
		$sql = "SELECT * FROM ".DEPUTY_TB." WHERE deputy_id='$deputy_id'";
		
		$depyty_data = $site_db->query_firstrow($sql);
		
		$deputy_item = fill_deputy_list_item($depyty_data);
		
		echo $deputy_item;
		
	break;
	
	case 'deputy_confirm':
		
		$deputy_id = value_proc($_POST['deputy_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		if($user_id!=$current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".DEPUTY_TB." SET deputy_confirm=1 WHERE deputy_id='$deputy_id'  AND  deputy_user_id	= '$user_id' ";
		 
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			$success = 1;
		}
		
		// Колв-о новых выговоров 
		$new_deputy_count = get_new_deputies($current_user_id);
		 
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'new_deputy_count' => $new_deputy_count));
		
	break;
		
}

?>