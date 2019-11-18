<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_ucontrol.php';


$mode = $_POST['mode'] ? $_POST['mode'] : $_GET['mode'];

if(!$current_user_id)
{
	exit();
}


switch($mode)
{
	// Добавить клиента  сотрудника
	case 'show_stat':
		 
		$user_id = value_proc($_POST['user_id']);
		
		$to_chart = (array) json_decode(str_replace('\\', '', $_POST['to_chart']), 1);
		
		$date = value_proc($_POST['date']);

		
		$current_user_data = $current_user_obj->get_user_data(); 
	
		// Модуль контроль выводим людям, которые имеют полный доступ к профилям сотрудников
		if(!$current_user_data['is_full_access'])
		{
			exit();
		}
		
		if(!date_rus_validate($date))
		{
			$error['date'] = 1;
		}
		
		$date = formate_to_norm_date($date);
		
		if(!$error)
		{
			$content = ucontrol_show_stat($user_id, $date, $to_chart);
		}
		
		// Возвращаем результат
		echo json_encode(array('error' => $error, 'content' => iconv('cp1251', 'utf-8', $content)));
			
	break;
	
	case 'get_sipuni_stat':
		
		$user_id = value_proc($_POST['user_id']);
		
		$sipuni_phone = value_proc($_POST['sipuni_phone']);
		
		$date_from = value_proc($_POST['date_from']);
		
		$date_to = value_proc($_POST['date_to']);
		
		$current_user_data = $current_user_obj->get_user_data(); 
	
		// Модуль контроль выводим людям, которые имеют полный доступ к профилям сотрудников
		if(!$current_user_data['is_full_access'])
		{
			exit();
		}
	
	
		
		if(!date_rus_validate($date_from) && $date_from)
		{
			$error['date_from'] = 1;
		}
		
		if(!date_rus_validate($date_to) && $date_to)
		{
			$error['date_to'] = 1;
		}
		
		if(!$date_from && !$date_to)
		{
			$error['date'] = 1;
		}
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($user_id);
			
		$user_data = $user_obj->get_user_data();
		
		$user_sipuni_phone = $user_obj->get_user_data_par($user_data['data'], 'sipuni_phone');
		 
		if(!$user_sipuni_phone)
		{
			$error['sipuni_phone'] = 1;
		}
		
		if(!$error)
		{
			 $content = get_sipuni_stat($user_id, $user_sipuni_phone, $date_from, $date_to);
		}
		
		 
		// Возвращаем результат
		echo json_encode(array('error' => $error, 'content' => iconv('cp1251', 'utf-8', $content)));
		
	break;
	
	case 'save_user_options':
		
		$user_id = value_proc($_POST['user_id']);
		
		$sipuni_phone = value_proc($_POST['sipuni_phone']);
		
		$current_user_data = $current_user_obj->get_user_data(); 
	
		// Модуль контроль выводим людям, которые имеют полный доступ к профилям сотрудников
		if(!$current_user_data['is_full_access'])
		{
			exit();
		}
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($user_id);
			
		$user_data = $user_obj->get_user_data();
		
		$user_sipuni_phone = $user_obj->get_user_data_par($user_data['data'], 'sipuni_phone');
		
		// запоминаем телефон
		if($user_sipuni_phone!=$sipuni_phone)
		{ 
			$user_obj->set_user_data_par($user_id, array('sipuni_phone' => $sipuni_phone));
		}
		
		$success = 1;
		
		// Возвращаем результат
		echo json_encode(array('error' => $error, 'success' => $success, 'content' => iconv('cp1251', 'utf-8', $content)));
		
	break;
	
	case 'save_ucontrol_settings':
		
		$option_sipuni_id = value_proc($_POST['option_sipuni_id']);
		
		$option_secret_key = value_proc($_POST['option_secret_key']);
		
		if(!$current_user_obj->get_is_admin())
		{
			exit();
		}
		
		$sql = "SELECT * FROM tasks_ucontrol_settings WHERE name='sipuni'";
		
		$sipuni_row = $site_db->query_firstrow($sql);
		
		$sipuni_data = serialize(array('option_sipuni_id' => $option_sipuni_id, 'option_secret_key' => $option_secret_key));
		
		if($sipuni_row['id'])
		{
			$sql = "UPDATE tasks_ucontrol_settings SET settings='$sipuni_data' WHERE id='".$sipuni_row['id']."'";
			
			$site_db->query($sql);
		}
		else
		{
			$sql = "INSERT INTO tasks_ucontrol_settings SET name='sipuni', settings='$sipuni_data'";
			
			$site_db->query($sql);
		}
		$success = 1;
		echo json_encode(array('error' => $error, 'success' => $success));
		
	break;
	
	
	
	
}

?>