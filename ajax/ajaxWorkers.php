<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_personal.php';
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
	// Добавить в список сотрудников
	case 'add_in_my_workers':
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		}
		
		$worker_id = $_POST['worker_id'];
		
		$comment = value_proc($_POST['comment']);
		
		if($worker_id == $current_user_id)
		{
			exit();
		}
		
		if(!$worker_id)
		{
			exit();
		}
		
		// находим всех сотрудников для пользователя, которого хотим добавить 
		$workers_arr = get_all_workers_arr_for_user($worker_id);
		
		// Если текущий пользователь является подчиненным любого уровня, то не даем добавить в сотрудники человека
		if(in_array($current_user_id, $workers_arr))
		{
			$error = 4;
		}
		
		##### Проверяем, есть ли такой сотрудник в подчинении
		$sql = "SELECT * FROM ".WORKERS_TB." WHERE invite_user='$current_user_id' AND invited_user='$worker_id' AND invited_user_status = 1 AND deputy_id = 0";
		
		$row = $site_db->query_firstrow($sql);
		
		// Такой сотрудник есть уже в подчинении
		if($row['id'])
		{
			$error = 1;
		}
		
		##### Проверяем, не является ли сотрудник моим начальником
		$sql = "SELECT * FROM ".WORKERS_TB." WHERE invite_user='$worker_id' AND invited_user='$current_user_id' AND invited_user_status = 1 AND deputy_id = 0";
		
		$row = $site_db->query_firstrow($sql);
		
		// Мой начальник
		if($row['id'])
		{
			$error = 2;
		}
		 
		
		// Если нет ошибок
		if(!$error)
		{
			// Очищаем записи старые на случай, чтобы не возникало конфликтов
			$sql = "DELETE FROM ".WORKERS_TB." WHERE invite_user='$current_user_id' AND invited_user='$worker_id' AND deputy_id = 0";
			
			$site_db->query($sql);
			
			// Очищаем записи старые на случай, чтобы не возникало конфликтов
			$sql = "DELETE FROM ".WORKERS_TB." WHERE invite_user='$worker_id' AND invited_user='$current_user_id' AND invited_user_status IN(0,2) AND deputy_id = 0";
			
			$site_db->query($sql);
			
			
			// Добавляем запись в базу
			$sql = "INSERT INTO  ".WORKERS_TB." (invite_user, invited_user, invite_date, invite_user_comment)
					VALUES ('$current_user_id', '$worker_id', NOW(), '$comment')";
			
			$site_db->query($sql);
			
			$success = 1;
		}
		
		echo json_encode(array('success' => $success, 'error' => $error));
	
	break;
	
	// Список "Мои сотрудники"
	case 'get_workers_list':
	
		$user_id = $_POST['user_id'];
		
		$workers_list = fill_workers_list($user_id);
		
		echo $workers_list;
		
	break;
	
	// Отклонить добавление в список
	case 'hide_rejected_notice':
		
		$invite_user_id = $_POST['invite_user_id'];
		
		$invited_user_id = $_POST['invited_user_id'];
		
		if($invite_user_id != $current_user_id)
		{  
			exit();
		}
		 
		// Обновляем
		$sql = "DELETE FROM ".WORKERS_TB." WHERE invite_user='$invite_user_id' AND invited_user='$invited_user_id' AND invited_user_status= 2 AND deputy_id = 0";
		
		$site_db->query($sql);
		
		echo 1;
		
	break;
	
	// Убрать пользователя из сотрудников
	case 'remove_user_from_worker':
		
		$user_id = $_POST['user_id'];
		
		$sql = "DELETE FROM ".WORKERS_TB." WHERE invite_user='$current_user_id' AND invited_user='$user_id' AND deputy_id = 0";
		
		$site_db->query($sql);
		
		echo 1;
	break;
	
	case 'get_more':
		
		$page = value_proc($_POST['page']);
		
		$key = value_proc($_POST['key']);
		
		$users_list = fill_workers_list($page, $key);
		
		echo $users_list;
		
	break;
	
	case 'workers_list':
		
		$key = value_proc($_POST['key']);
		
		$workers_list = fill_workers_list_content($key);
		
		echo $workers_list;
		
	break;


}

?>