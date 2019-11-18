<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

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
	// 
	case 'add_money':
		
		$user_id = value_proc($_POST['user_id']);
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'inserted_money_id' => $inserted_money_id));
	
	break;
	
	case 'save_computer_name':
		
		$computer_name = value_proc($_POST['computer_name']);
		
		$computer_id = value_proc($_POST['computer_id']);
		
		$computer_authed = value_proc($_POST['computer_authed']);
		
		// Выбор активности пользователя за дату
		$sql = "SELECT user_id as computer_user_id FROM ".COMPS_TB."
				WHERE computer_id='$computer_id'";
		
		$computer_data = $site_db->query_firstrow($sql);
			
		// Массив всех сотрудников для текущего пользователя всех уровней
		$all_workers_arr = get_all_workers_arr_for_user($current_user_id);
		
		// Проверяем, имеет право на редактирование название компьютера
		if(!check_access_for_edit_computer_name($computer_data, $all_workers_arr))
		{
			exit();
		}
		
		if($computer_name=='')
		{
			$error['computer_name'] = 1;
		}
		else
		{
			// Изменить название компьютера
			$sql = "UPDATE ".COMPS_TB." SET computer_name = '$computer_name', computer_authed = '$computer_authed' WHERE computer_id='$computer_id'";
			
			$res = $site_db->query($sql);
			
			if(!mysql_error())
			{
				$success = 1;
			}
		}
		
		echo json_encode(array('success' => $success, 'error' => $error));
	break;
	
	// нажатие на кнопки "работать начал" или "работать закончил"
	case 'change_user_activity_work_status':
		
		$status_id = $_POST['status_id'];
		
		$date = $_POST['date'];
		
		$computer_time_in_seconds = to_mktime($date);
		
		// Првоеряем последний статус ("Начал работь" или "Работаь закончил") за дату
		$user_last_status = get_last_user_activity_status($current_user_id);
		
		if($user_last_status==$status_id)
		{
			exit();
		}
		
		// Делаем отметку статуса
		set_user_work_activity($status_id, $computer_time_in_seconds);
		
		// Возвращаем кнопки
		if($status_id==1)
		{
			$status_btn = fill_activity_start_and_finish_btn(2);
		}
		else
		{
			$status_btn = fill_activity_start_and_finish_btn(1);
		}
		 
		$success = 1;
		
		echo json_encode(array('success' => $success, 'status_btn' => iconv('cp1251', 'utf-8', $status_btn)));
	break;
	
	// Активность пользователя на сайте
	case 'set_u_act':
	
		$date = value_proc($_POST['date']);
		
		$screen_width = value_proc($_POST['screen_width']);
		
		$screen_height = value_proc($_POST['screen_height']);
		
		$computer_time_in_seconds = to_mktime($date);
		
		// Делаем отметку об активности пользователя
		set_user_work_activity(0, $computer_time_in_seconds, $screen_width, $screen_height);
		
		
	break;
	
	case 'change_user_status':
		
		$status_id = value_proc($_POST['status_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		if($current_user_id!=$user_id)
		{
			exit();
		}
		
		// Проверяем на наличие статуса
		$sql = "SELECT * FROM ".USERS_STATUSES_TB." WHERE user_id='$user_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			$sql = "UPDATE ".USERS_STATUSES_TB." SET status_id='$status_id', date=NOW() WHERE id='".$row['id']."'";
			
			$site_db->query($sql);
		}
		else
		{
			$sql = "INSERT INTO ".USERS_STATUSES_TB." (user_id, status_id, date) VALUES ('$user_id', '$status_id', NOW())";
			
			$site_db->query($sql);
		}
		
		echo 1;
		
		
	break;

}

?>