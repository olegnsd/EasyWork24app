<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_reprimand.php';

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
	// Добавить оф документ
	case 'add_reprimand':
		
		$worker_id = value_proc($_POST['worker_id']);
		
		$reprimand_text = value_proc($_POST['reprimand_text']);
		
		$type = value_proc($_POST['type']);
		
		if(!$worker_id)
		{
			exit();
		}
		if(!$reprimand_text)
		{
			$error['text'] = 1;
		}
		
		if(!check_user_access_to_user_content($worker_id, array(0,1,0,0,1)))
		{
			exit();
		}
		
		if(!$error)
		{
			// Добавляем запись
			$sql = "INSERT INTO ".REPRIMANDS_TB." (boss_id, worker_id, reprimand_text, date, type) VALUES ('$current_user_id', '$worker_id', '$reprimand_text', NOW(), '$type')";
			
			$site_db->query($sql);
			
			$reprimand_id = $site_db->get_insert_id(); 
			
			$success = 1;
		}
			
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'reprimand_id' => $reprimand_id));
	
	break;
	
	
	// Возвращает список клиентов
	case 'get_more_reprimands':
		
		$user_id = value_proc($_POST['user_id']);
		 
		$page = value_proc($_POST['page']);
		
		$is_wks = value_proc($_POST['is_wks']);
		
		$type = value_proc($_POST['type']);
		
	
		// Оф документы сотрудников
		if($is_wks)
		{
			// Список выговоров сотрудникам
			$dosc_list = fill_workers_reprimand_list($current_user_id, $page, $type);
		}
		else
		{
			// Список выговоров сотрудника
			$dosc_list = fill_reprimand_list($user_id, $page, $type);
		}
		
		echo $dosc_list;
		
	break;
	
	case 'delete_reprimand':
		
		$reprimand_id = value_proc($_POST['reprimand_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "UPDATE ".REPRIMANDS_TB." SET deleted=1 WHERE reprimand_id='$reprimand_id' AND boss_id='$user_id'";
		
		$site_db->query($sql);
		
		$_SESSION['reprimand_delete'][] = $reprimand_id;
		
		echo 1;
	break;
	
	case 'restore_reprimand':
		
		$reprimand_id = value_proc($_POST['reprimand_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "UPDATE ".REPRIMANDS_TB." SET deleted=0 WHERE reprimand_id='$reprimand_id' AND boss_id='$user_id'";
		
		$site_db->query($sql);
		
		unset($_SESSION['reprimand_delete'][$reprimand_id]);
		
		echo 1;
	break;
	
	case 'get_reprimand_item':
		
		$reprimand_id = value_proc($_POST['reprimand_id']);
		
		// Данные документа
		$sql = "SELECT * FROM ".REPRIMANDS_TB." WHERE reprimand_id='$reprimand_id'";
		
		$reprimand_data = $site_db->query_firstrow($sql);
		
		$reprimand_item = fill_reprimand_list_item($reprimand_data);
		
		echo $reprimand_item;
		
	break;
	
	case 'reprimand_confirm':
		
		$reprimand_id = value_proc($_POST['reprimand_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		if($user_id!=$current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".REPRIMANDS_TB." SET noticed=1 WHERE reprimand_id='$reprimand_id'  AND  	worker_id='$user_id' ";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			$success = 1;
		}
		
		// Колв-о новых выговоров 
		$new_reprimand_count_type_1 = get_new_workers_reprimands_count($current_user_id, 1);
		
		// Колв-о новых выговоров 
		$new_reprimand_count_type_2 = get_new_workers_reprimands_count($current_user_id, 2);
		
		// Колв-о новых выговоров 
		$new_reprimand_count = $new_reprimand_count_type_1 + $new_reprimand_count_type_2;
		 
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'new_reprimand_count' => $new_reprimand_count, 'new_reprimand_count_type_1' => $new_reprimand_count_type_1, 'new_reprimand_count_type_2' => $new_reprimand_count_type_2));
		
	break;
		
}

?>