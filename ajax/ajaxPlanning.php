<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_planning.php';
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

 
// Проверка авторизации
if(!$current_user_id)
{
	exit();
}
		 
switch($mode)
{
	// Добавить имущество сотрудника
	case 'add_planning':
		
	
		$user_id = value_proc($_POST['user_id']);
		
		$boss_id = value_proc($_POST['boss_id']);
		
		$depts = $_POST['depts'];
		
		$type_id = value_proc($_POST['type_id']);
		
		$planning_type_str = value_proc($_POST['planning_type_str']);
		
		$planning_for = value_proc($_POST['planning_for']);
		 
		$dates_one_arr = (array)json_decode(str_replace('\\', '', $_POST['dates_one_arr']));
		
		$dates_period_arr = (array)json_decode(str_replace('\\', '', $_POST['dates_period_arr']));
		 
		if(!$boss_id || !$type_id)
		{
			//exit();
		}
		 
		// Название контакта пустое
		if($user_id!=$current_user_id)
		{
			//exit();
		}
		
		if($planning_for==1)
		{
			if(!$boss_id)
			{
				$error['boss']=1;
			}
		}
		else if($planning_for==2)
		{
			if(!$depts) 
			{
				$error['depts']=1;
			}
		}
		else
		{
			exit();
		}
		
		// Обработка единичной даты
		foreach($dates_one_arr as $date_one)
		{
			$tmp = split('%', $date_one);
			
			$date = $tmp[0];
			
			if(date_rus_validate($date))
			{
				$date = formate_to_norm_date($date);
				$date = to_mktime($date);
				$date = $date + (int)$tmp[1]*3600 + (int)$tmp[2]*60;
				$date = date('Y-m-d H:i', $date);
				
				$new_dates_one_arr[] = $date;
			}
		}
		//print_r($new_dates_one_arr);
		
		// Обработка периода
		foreach($dates_period_arr as $date_period)
		{
			$tmp_period = split('\|', $date_period);
			
			$tmp_date_from = split('%', $tmp_period[0]);
			$tmp_date_to = split('%', $tmp_period[1]);
		 
			if(date_rus_validate($tmp_date_from[0]) && date_rus_validate($tmp_date_to[0]))
			{
				$tmp_date_from[0] = formate_to_norm_date($tmp_date_from[0]);
				$tmp_date_to[0] = formate_to_norm_date($tmp_date_to[0]);
				
				$date_from = to_mktime($tmp_date_from[0]);
				$date_to = to_mktime($tmp_date_to[0]);
					
				if($tmp_date_from[1] || $tmp_date_from[2])
				{
					$date_from = $date_from  + (int)($tmp_date_from[1])*3600 + (int)($tmp_date_from[2])*60;
				}
				else
				{
					$date_from = $date_from;
				}
				
				if($tmp_date_to[1] || $tmp_date_to[2])
				{
					$date_to = $date_to + (int)$tmp_date_to[1]*3600 + (int) ($tmp_date_to[2])*60;
				}
				else
				{
					$date_to = $date_to;
				}
				//echo  $date_from  + (int)($tmp_date_from[1]) + (int)($tmp_date_from[2]),' ',$date_from;
				
				//echo date('Y-m-d H:i', $date_from);;
				// echo $date_from.' '.to_mktime($date_from).' '.to_mktime($date_to);
				if($date_from<=$date_to)
				{
					$date_from = date('Y-m-d H:i', $date_from);
					$date_to = date('Y-m-d H:i', $date_to); 
					 
					$new_dates_period_arr[] = array('date_from' => $date_from, 'date_to' => $date_to);
				}
				 
			}
		}
		
		if(!$new_dates_one_arr && !$new_dates_period_arr)
		{
			$error['date'] = 1;
		}
		
		if(!$error)
		{
			if($depts)
			{
				$depts_str = implode(',', $depts);
			}
			
			if(!$type_id)
			{
				$type_id = 2;
				
			}
			
			// Добавляем планирование
			$sql = "INSERT INTO ".PLANNING_TB." (user_id, boss_id, type_id, date, planning_for, depts, type_str) VALUES ('$current_user_id','$boss_id', '$type_id', NOW(), '$planning_for', '$depts_str', '$planning_type_str')";
			
			$site_db->query($sql);
			
			$planning_id = $site_db->get_insert_id();
			
			// Если уведомляют руководителя
			if($planning_for==1)
			{
				$sql = "INSERT INTO tasks_users_planning_users SET user_id='$boss_id', planning_id='$planning_id'";
				$site_db->query($sql);
			}
			// Если уведомляет сотрудников
			else if($planning_for==2)
			{
				// получение списка сотрудников отделов
				$depts_users = get_depts_users($depts, $current_user_id);
				 
				foreach($depts_users as $user_id)
				{
					$sql = "INSERT INTO tasks_users_planning_users SET user_id='$user_id', planning_id='$planning_id'";
					$site_db->query($sql);
				}
			}
			
			// Добавляем отдельные даты
			foreach($new_dates_one_arr as $date_one)
			{
				//$date_one = formate_to_norm_date($date_one);
				
				$sql = "INSERT INTO ".PLANNING_DATES_TB." (planning_id, date_from, date_to, date_one, date_is_period, user_id) 
						VALUES ('$planning_id', '', '', '$date_one', 0, '$current_user_id')";
				
				$site_db->query($sql);
			}
			
			// ДОбавляем отдельные даты
			foreach($new_dates_period_arr as $date_period)
			{
				$date_from = $date_period['date_from'];
				$date_to = $date_period['date_to'];
				
				$sql = "INSERT INTO ".PLANNING_DATES_TB." (planning_id, date_from, date_to, date_one, date_is_period, user_id) 
						VALUES ('$planning_id', '".$date_from."', '".$date_to."', '', 1, '$current_user_id')";
				
				$site_db->query($sql);
			}
			
			$success = 1;
			
		}
		//echo "<pre>", print_r($new_dates_period_arr), "</pre>";
		
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'planning_id' => $planning_id));
			
	break;
	
	// Возвращает список клиентов
	case 'get_more_planning':
		
		$user_id = value_proc($_POST['user_id']);
		
		$page = value_proc($_POST['page']);
		
		$others = value_proc($_POST['others']);
		 
		if($others)
		{
			// Список планирования
			$planning_list = fill_planning_list($current_user_id, $page, 1);
		}
		else
		{
			// Список планирования
			$planning_list = fill_planning_list($user_id, $page);
			
		}
		echo $planning_list;
		
	break;
	
	case 'get_planning_item':
		
		$planning_id = value_proc($_POST['planning_id']);
		
		$planning_item = fill_planning_list_item('', $planning_id);
		
		echo $planning_item;
		
	break;
	
	case 'delete_planning':
		
		$planning_id = value_proc($_POST['planning_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "UPDATE ".PLANNING_TB." SET deleted=1 WHERE planning_id='$planning_id' AND user_id='$user_id'";
		
		$site_db->query($sql);
		
		$sql = "UPDATE ".PLANNING_DATES_TB." SET deleted=1 WHERE planning_id='$planning_id'";
		
		$site_db->query($sql);
		
		$_SESSION['planning_delete'][] = $planning_id;
		
		echo 1;
	break;
	
	case 'restore_planning':
		
		$planning_id = value_proc($_POST['planning_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "UPDATE ".PLANNING_TB." SET deleted=0 WHERE planning_id='$planning_id'";
		
		$site_db->query($sql);
		
		$sql = "UPDATE ".PLANNING_DATES_TB." SET deleted=0 WHERE planning_id='$planning_id'";
		
		$site_db->query($sql);
		
		$_SESSION['planning_delete'][$planning_id]=='';
		
		echo 1;
	break;
	
	case 'confirm_planning':
		
		$planning_id = value_proc($_POST['planning_id']);
		
		$planning_result = value_proc($_POST['planning_result']);
		
		$sql = "SELECT * FROM ".PLANNING_TB." WHERE planning_id='$planning_id'";
		
		$planning_data = $site_db->query_firstrow($sql);
		
		// если уведомляют начальство, отмечаем метку, которую выставил руководитель (принял или отклонил)
		if($planning_data['planning_for']==1)
		{
			$sql = "UPDATE ".PLANNING_TB." SET planning_result='$planning_result' WHERE planning_id='$planning_id' AND boss_id='$current_user_id'";
		
			$site_db->query($sql);
		}
		
		// снимаем уведомления
		$sql = "UPDATE tasks_users_planning_users SET noticed=1 WHERE planning_id='$planning_id' AND user_id='$current_user_id'";
		
		$site_db->query($sql);
		
		 
		$result = fill_planning_result('', $planning_id);
		
		// Кол-во уведомлений
		//$new_planning_count_for_boss = get_new_user_planning_count_for_boss($current_user_id);
		$new_planning_count_for_others = get_new_user_planning_count_for_others($current_user_id);
		
		$new_planning_count_all = $new_planning_count_for_others;
		
		$success = 1;
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'result' =>  iconv('cp1251', 'utf-8', $result), 'new_planning_count_all' => $new_planning_count_for_others));
		
	break;
}

?>