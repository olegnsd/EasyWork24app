<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_ofdocs.php';

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
	case 'add_ofdoc':
		
		$to_user_id = value_proc($_POST['to_user_id']);
		
		$ofdocs_type = value_proc($_POST['ofdocs_type']);
		
		$ofdocs_text = value_proc($_POST['ofdocs_text']);
		
		if(!$to_user_id || !$ofdocs_type)
		{
			exit();
		}
		if(!$ofdocs_text)
		{
			$error['text'] = 1;
		}
		
		
		if(!$error)
		{
			// Добавляем запись
			$sql = "INSERT INTO ".OFDOSC_TB." (type_id, user_id, date, ofdoc_text, to_user_id) VALUES ('$ofdocs_type', '$current_user_id', NOW(), '$ofdocs_text', '$to_user_id')";
			
			$site_db->query($sql);
			
			$ofdoc_id = $site_db->get_insert_id();
			
			// Добавляем доступ к документу начальнику
			$sql = "INSERT INTO ".OFDOCS_ACCESS_TB." (ofdoc_id, user_id, access_by_user_id, date) VALUES ('$ofdoc_id', '$to_user_id', '$current_user_id', NOW())";
			
			$site_db->query($sql); 
			
			$success = 1;
		}
			
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'ofdoc_id' => $ofdoc_id));
	
	break;
	
	// Добавить оф документ
	case 'save_ofdoc':
		
		$ofdoc_id = value_proc($_POST['ofdoc_id']);
		
		$boss_id = value_proc($_POST['boss_id']);
		
		$ofdocs_type = value_proc($_POST['ofdocs_type']);
		
		$ofdocs_text = value_proc($_POST['ofdocs_text']);
		
		$user_id = value_proc($_POST['user_id']);
		
		if(!$ofdocs_type)
		{
			exit();
		}
		if(!$ofdocs_text)
		{
			$error['text'] = 1;
		}
		
		
		if(!$error)
		{
			// Добавляем запись
			$sql = "UPDATE ".OFDOSC_TB." SET type_id = '$ofdocs_type', ofdoc_text = '$ofdocs_text' WHERE ofdoc_id='$ofdoc_id' AND user_id='$user_id'";
			$site_db->query($sql);
			
			//
			$sql = "SELECT * FROM tasks_ofdocs_statuses WHERE ofdoc_id='$ofdoc_id' AND status_id!=4";
			
			$res = $site_db->query($sql);
			
			while($row=$site_db->fetch_array($res))
			{
				$sql = "DELETE FROM tasks_ofdocs_statuses WHERE id='".$row['id']."'";
			
				$site_db->query($sql);
				
				// удаляем уведомления
				ofdoc_delete_notice($ofdoc_id, 0, $row['id'], 'by_status');
			}
		
			
			
			 
			
			$success = 1;
		}
			
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error));
	
	break;
	
	// Возвращает список клиентов
	case 'get_more_ofdocs':
		
		$user_id = value_proc($_POST['user_id']);
		 
		$page = value_proc($_POST['page']);
		
		$is_wks = value_proc($_POST['is_wks']);
		
		// Оф документы сотрудников
		if($is_wks)
		{
			// Список документов подчиненных
			$dosc_list = fill_workers_ofdocs_list($current_user_id, $page);
		}
		else
		{
			// Список документов сотрудника
			$dosc_list = fill_ofdocs_list($user_id, $page);
			
		}
		echo $dosc_list;
		
	break;
	
	case 'delete_ofdoc':
		
		$ofdoc_id = value_proc($_POST['ofdoc_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "UPDATE ".OFDOSC_TB." SET deleted=1 WHERE ofdoc_id='$ofdoc_id' AND user_id='$user_id'";
		
		$site_db->query($sql);
		
		$_SESSION['ofdoc_delete'][] = $ofdoc_id;
		
		echo 1;
	break;
	
	case 'restore_ofdoc':
		
		$ofdoc_id = value_proc($_POST['ofdoc_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "UPDATE ".OFDOSC_TB." SET deleted=0 WHERE ofdoc_id='$ofdoc_id' AND user_id='$user_id'";
		
		$site_db->query($sql);
		
		$_SESSION['ofdoc_delete'][$ofdoc_id]=='';
		
		echo 1;
	break;
	
	case 'get_ofdoc_item':
		
		$ofdoc_id = value_proc($_POST['ofdoc_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$form = value_proc($_POST['form']);
		
		// Данные документа
		$sql = "SELECT * FROM ".OFDOSC_TB." WHERE user_id='$user_id'  AND ofdoc_id='$ofdoc_id'";
		
		$ofdoc_data = $site_db->query_firstrow($sql);
		
		$ofdoc_item = fill_ofdocs_list_item($ofdoc_data, $form);
		
		echo $ofdoc_item;
		
	break;
	
	case 'give_access_to_ofdoc':
		
		$ofdoc_id = value_proc($_POST['ofdoc_id']);
		
		$user_id = value_proc($_POST['user_id']);
	
		if(!check_user_access_to_user_content($user_id, array(1,1,0,0,0)))
		{
			exit();
		}
		
		// Данные документа
		$sql = "SELECT * FROM ".OFDOSC_TB." WHERE ofdoc_id='$ofdoc_id'";
		
		$ofdoc_data = $site_db->query_firstrow($sql);
		
		
		// Выбор всех сотрудников, имеющих доступ к файлу
		$sql = "SELECT * FROM ".OFDOCS_ACCESS_TB." WHERE ofdoc_id='$ofdoc_id'";
		
		$res = $site_db->query($sql);
			
		while($row=$site_db->fetch_array($res))
		{
			$users_access_arr[] = $row['user_id'];
		}
		
		if(!in_array($current_user_id, $users_access_arr) && $current_user_id!=$ofdoc_data['user_id'])
		{
			exit();
		}
		
		// Проверяем, есть ли доступ у пользователя
		$sql = "SELECT id FROM ".OFDOCS_ACCESS_TB." WHERE user_id='$user_id' AND ofdoc_id='$ofdoc_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			$sql = "DELETE FROM ".OFDOCS_ACCESS_TB." WHERE id='".$row['id']."'";
			
			$site_db->query($sql);
			
			// удаляем уведомления
			ofdoc_delete_notice($ofdoc_id, $user_id, 0, 'by_user');
			
			echo 1;
		}
		else
		{
			$sql = "INSERT INTO ".OFDOCS_ACCESS_TB." (ofdoc_id, user_id, access_by_user_id, date) VALUES ('$ofdoc_id', '$user_id', '$current_user_id', NOW())";
			
			$site_db->query($sql);
			
			echo 2;
		}
		
	break;
	
	// Добавить статус к документу
	case 'add_ofdoc_status':
		
		$ofdoc_id = value_proc($_POST['ofdoc_id']);
		
		$status_id = value_proc($_POST['status_id']);
		
		$status_text = value_proc($_POST['status_text']);
		
		// Если только оставить комментарий
		if($status_id==4 && $status_text=='')
		{
			$error['status_text'] = 1;
		}
		
		if(!$error)
		{
			// Добавляем запись
			$sql = "INSERT INTO ".OFDOCS_STATUSES_TB." (ofdoc_id, status_id, status_text, status_date, user_id) 
					VALUES ('$ofdoc_id', '$status_id', '$status_text', NOW(), '$current_user_id')";
					
			$site_db->query($sql);
			
			$status_id = $site_db->get_insert_id();
			
			// делаем уведомления
			ofdoc_set_user_notice($ofdoc_id, $status_id);
			
			// Получаем список статусов
			$statuses_list = fill_ofdoc_statuses_list($ofdoc_id);
			
			if(!mysql_error())
			{
				$success = 1;
			}
		}
		echo json_encode(array('success' => $success, 'error' => $error, 'statuses_list' => iconv('cp1251', 'utf-8', $statuses_list)));
		
	break;
	case 'ofdoc_show_statuses_list':
		
		$ofdoc_id = value_proc($_POST['ofdoc_id']);
		
		$statuses_list = fill_ofdoc_statuses_list($ofdoc_id);
		
		ofdoc_delete_notice($ofdoc_id, $current_user_id, 0, 'by_user');
		
		echo $statuses_list;
		
	break;
	
	case 'ofdoc_recount_new_notices':
		
		$new_ofdocs_count = get_new_ofdocs_count($current_user_id);
		 
		$new_accessed_count = get_new_ofdocs_statuses_count($current_user_id, 'accessed');
		
		$new_own_count = get_new_ofdocs_statuses_count($current_user_id, 'own');
		
		$all_count = $new_ofdocs_count+$new_accessed_count+$new_own_count;
		
		echo json_encode(array('all_count' => $all_count, 'new_accessed_count' => $new_accessed_count, 'new_own_count' => $new_own_count));
	break;
	
	case 'get_ofdoc_access_block':
		
		$ofdoc_id = value_proc($_POST['ofdoc_id']);
		
		 
		// данные документа
		$sql = "SELECT * FROM ".OFDOSC_TB." WHERE ofdoc_id='$ofdoc_id'";
	
		$ofdoc_data = $site_db->query_firstrow($sql);
		
		$access_block = fill_ofdoc_access_block($ofdoc_data);
		
		echo $access_block;
		
	break;
	
	case 'save_ofdoc_user_access':
		
		$ofdoc_id = value_proc($_POST['ofdoc_id']);
		$access_users = (array)json_decode(str_replace('\\', '', $_POST['access_users']), 1);
		 
		// Данные заметки
		$sql = "SELECT * FROM tasks_users_ofdosc WHERE ofdoc_id='$ofdoc_id'";
		
		$ofdoc_data = $site_db->query_firstrow($sql);
		
		
		// Выбор всех сотрудников, имеющих доступ к заметке
		$sql = "SELECT * FROM tasks_users_ofdocs_access WHERE ofdoc_id='$ofdoc_id'";
		
		$res = $site_db->query($sql);
		
		$users_access_arr = array();
			
		while($row=$site_db->fetch_array($res))
		{
			$users_access_arr[$row['user_id']] = $row['user_id'];
		}
		
		if(!in_array($current_user_id, $users_access_arr) && $current_user_id!=$ofdoc_data['user_id'])
		{
			exit();
		}

		
		$to_delete = array_diff($users_access_arr, $access_users);
		$to_add = array_diff($access_users, $users_access_arr);
		 
		foreach($to_delete as $user_id)
		{
			if(!$user_id)
			{
				//continue;
			}
			
			if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1,1)) && $ofdoc_data['user_id']!=$current_user_id)
			{
				continue;
			}
			
			$sql = "DELETE FROM tasks_users_ofdocs_access WHERE user_id='$user_id' AND ofdoc_id='$ofdoc_id'";
			
			$site_db->query($sql);
		}
		 
		foreach($to_add as $user_id)
		{  
			if(!$user_id || $user_id==$ofdoc_data['user_id'])
			{
				continue;
			}
			
			if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1,1)))
			{
				 continue;
			}
		
			$sql = "INSERT INTO tasks_users_ofdocs_access SET user_id='$user_id', ofdoc_id='$ofdoc_id', access_by_user_id='$current_user_id', date=NOW()";
			
			$site_db->query($sql);
		}
		
		echo 1;
		
	break;
}

?>