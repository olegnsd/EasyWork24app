<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_calendar_of_events.php';

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
		
	// Добавить событие
	case 'add_event':
		
		$event_name = value_proc($_POST['event_name']);
		$event_desc = value_proc($_POST['event_desc']);
		$event_start = value_proc($_POST['event_start']);
		$event_finish = value_proc($_POST['event_finish']);
		$event_start_hour = value_proc($_POST['event_start_hour']);
		$event_start_minute = value_proc($_POST['event_start_minute']);
		$event_finish_hour = value_proc($_POST['event_finish_hour']);
		$event_finish_minute = value_proc($_POST['event_finish_minute']);
		$evcal_user_id = value_proc($_POST['evcal_user_id']);
		$category_id = value_proc($_POST['category_id']);
		$event_reminder_for_days = value_proc($_POST['event_reminder']);
		
		$evcal_user_id = value_proc($_POST['evcal_user_id']);
		
		$pars = value_proc($_POST['pars']);
		
		// Проверяем, доступен ли календарь
		if(!check_evcal_access($evcal_user_id))
		{
			return false;
		}
		
		if(!$event_name)
		{
			$error['event_name'] = 1;
		}
				
		if(($event_start && !date_rus_validate($event_start)) || ($event_finish && !date_rus_validate($event_finish)) )
		{
			$error['date'] = 0;
		}
		
		else if(!$event_start)
		{
			$error['date'] = 1;
		}
		
		if(!$event_finish)
		{
			$event_finish = $event_start;
		}
		
		// Переводим в секунды даты старта и завершения события
		$event_start_date = to_mktime(formate_to_norm_date($event_start))+$event_start_hour+$event_start_minute;
		$event_finish_date = to_mktime(formate_to_norm_date($event_finish))+$event_finish_hour+$event_finish_minute;
		
		if($event_start_date > $event_finish_date && $event_finish)
		{
			$error['date'] = 2;
		}
		
		if(!$error)
		{
			$event_type = 1;
			
			// если параметр
			if($pars)
			{
				$tmp_pars = split('\|', $pars);
				
				// привязка к мероприятию проекта
				if($tmp_pars[1]==1000)
				{
					$event_type = 3;
					$content_id = $tmp_pars[0];
				}
			}
			
			// дата напоминания
			$reminder_date = get_reminder_date($event_start_date, $event_reminder_for_days); 
			
			// Добавляем запись о заместителе
			$sql = "INSERT INTO ".EVCAL_TB." 
					SET event_name='$event_name', event_desc='$event_desc', event_type='$event_type', user_id='$evcal_user_id', event_start_date='$event_start_date', event_finish_date='$event_finish_date', date_add=NOW(), added_by_user_id='$current_user_id', content_id='$content_id', category_id='$category_id', reminder_date='$reminder_date', reminder_for_days='$event_reminder_for_days'";
			
			$site_db->query($sql);
			
			$event_id = $site_db->get_insert_id(); 
			
			// добавляем связь задачи с другим контентом
			if($pars)
			{
				$tmp_pars = split('\|', $pars);
				
				if($tmp_pars[0])
				{
					//$sql = "INSERT INTO tasks_calendar_of_events_links SET event_id='$event_id', id='".$tmp_pars[0]."', other_id='0', `type`='".$tmp_pars[1]."'";
					//$site_db->query($sql);
				}
			}
			
			$success = 1;
		}
			
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'event_id' => $event_id));
	
	break;
	
	// Сохранить событие
	case 'save_event':
		
		$event_id = value_proc($_POST['event_id']);
		$event_name = value_proc($_POST['event_name']);
		$event_desc = value_proc($_POST['event_desc']);
		$event_start = value_proc($_POST['event_start']);
		$event_finish = value_proc($_POST['event_finish']);
		$event_start_hour = value_proc($_POST['event_start_hour']);
		$event_start_minute = value_proc($_POST['event_start_minute']);
		$event_finish_hour = value_proc($_POST['event_finish_hour']);
		$event_finish_minute = value_proc($_POST['event_finish_minute']);
		$category_id = value_proc($_POST['category_id']);
		$event_reminder_for_days = value_proc($_POST['event_reminder']);
		
		// Даныне события
		$event_data = get_event_data($event_id);
		
		if($event_data['added_by_user_id']!=$current_user_id)
		{
			exit();	
		}
		
		if(!$event_name)
		{
			$error['event_name'] = 1;
		}
				
		if(($event_start && !date_rus_validate($event_start)) || ($event_finish && !date_rus_validate($event_finish)) )
		{
			$error['date'] = 0;
		}
		
		else if(!$event_start)
		{
			$error['date'] = 1;
		}
		
		if(!$event_finish)
		{
			$event_finish = $event_start;
		}
		
		// Переводим в секунды даты старта и завершения события
		$event_start_date = to_mktime(formate_to_norm_date($event_start))+$event_start_hour+$event_start_minute;
		$event_finish_date = to_mktime(formate_to_norm_date($event_finish))+$event_finish_hour+$event_finish_minute;
		
		if($event_start_date > $event_finish_date && $event_finish)
		{
			$error['date'] = 2;
		}
		
		if(!$error)
		{		
			// дата напоминания
			$reminder_date = get_reminder_date($event_start_date, $event_reminder_for_days);
			
			if($event_data['reminder_date']!=$reminder_date)
			{
				$and_noticed = ", noticed=0";
			}
			
			// Добавляем запись о заместителе
			$sql = "UPDATE ".EVCAL_TB." 
					SET event_name='$event_name', event_desc='$event_desc', event_start_date='$event_start_date', 	
						event_finish_date='$event_finish_date', category_id='$category_id', reminder_date='$reminder_date', reminder_for_days='$event_reminder_for_days' $and_noticed WHERE event_id='$event_id'";
			
			$site_db->query($sql);
			
			$success = 1;
		}
			
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error));
	
	break;
	
	// Список событий даты
	case 'get_events_list':
		
		$date = value_proc($_POST['date']);
		
		$evcal_user_id = value_proc($_POST['evcal_user_id']);
		
		$event_id = value_proc($_POST['event_id']);
		
		// Проверяем, доступен ли календарь
		if(check_evcal_access($evcal_user_id))
		{
			// Получаем события
			$events_list = get_calendar_day_events_list($date, $evcal_user_id, $event_id);
		}
		
		 
		
		echo $events_list;
		
	break;
	
	case 'get_month_events':
		
		$date = value_proc($_POST['date']);
		
		$evcal_user_id = value_proc($_POST['evcal_user_id']);
		
		$types_option = json_decode(str_replace('\\', '', $_POST['types_option']), 1);
		$types_default = json_decode(str_replace('\\', '', $_POST['types_default']), 1);
			 
		$events = array();
		
		// Проверяем, доступен ли календарь
		if(check_evcal_access($evcal_user_id))
		{
			$events = get_month_events($date, $evcal_user_id, $types_option, $types_default);
		}
		
		echo json_encode(array('events' => $events));
		
	break;
	
	case 'get_event_item':
		
		$event_id = value_proc($_POST['event_id']);
		
		$form = value_proc($_POST['form']);
		
		$date = value_proc($_POST['date']);
		
		// Даныне события
		$event_data = get_event_data($event_id);
		
		// Заполнение элемента 
		$event_item = fill_event_item($event_data, $form, $date);
		
		echo $event_item;
	break;
	
	case 'delete_event':
		
		$event_id = value_proc($_POST['event_id']);
		
		// Даныне события
		$event_data = get_event_data($event_id);
		
		if($event_data['added_by_user_id']!=$current_user_id)
		{
			exit();	
		}
		
		$sql = "UPDATE ".EVCAL_TB." SET deleted=1 WHERE event_id='$event_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			echo 1;
		}
	break;
	
	case 'restore_event':
		
		$event_id = value_proc($_POST['event_id']);
		
		// Даныне события
		$event_data = get_event_data($event_id);
		
		if($event_data['added_by_user_id']!=$current_user_id)
		{
			exit();	
		}
		
		$sql = "UPDATE ".EVCAL_TB." SET deleted=0 WHERE event_id='$event_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			echo 1;
		}
	break;
	
	case 'get_notice':
		
		// Блок уведомления текущих и ближайших событий
		$notice_block = fill_evcal_notice_block($current_user_id);
		
		echo $notice_block;
		
	break;
	
	case 'public_evcal':
		
		$is_public = value_proc($_POST['is_public']);
		
		// Проверяем, разрешен ли доступ к календарю?
		$sql = "SELECT * FROM tasks_calendar_access WHERE user_id='$current_user_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'] && $is_public==0)
		{
			 $sql = "DELETE FROM tasks_calendar_access WHERE user_id='$current_user_id'";
			 
			 $site_db->query($sql);
		}
		else if($is_public==1)
		{
			// Добавляем публичный просмотр календарю
			$sql = "INSERT INTO tasks_calendar_access SET user_id='$current_user_id', date=NOW()";
			
			$site_db->query($sql);
		}
		
		echo 1;
		
	break;
	
	case 'add_evcal_cat':
		
		$name = value_proc($_POST['name']);
		
		$color = value_proc($_POST['color']);
		
		if(!$name)
		{
			$error['name'] = 1;
		}
		
		if(!$error)
		{
			$sql = "INSERT INTO tasks_calendar_of_events_categories SET category_name='$name', category_color = '$color', user_id='$current_user_id'";
			
			$site_db->query($sql);
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error));
		
	break;
	case 'save_evcal_cat':
		
		$category_id = value_proc($_POST['category_id']);
		
		$name = value_proc($_POST['name']);
		
		$color = value_proc($_POST['color']);
		
		$sql = "SELECT * FROM tasks_calendar_of_events_categories WHERE category_id='$category_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['user_id']!=$current_user_id)
		{
			exit();
		}
		
		if(!$name)
		{
			$error['name'] = 1;
		}
		
		if(!$error)
		{
			$sql = "UPDATE tasks_calendar_of_events_categories SET category_name='$name', category_color = '$color' WHERE category_id='$category_id'";
			
			$site_db->query($sql);
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error));
		
	break;
	 
	case 'edit_evcal_category':
		
		$category_id = value_proc($_POST['category_id']);
		 
		$edit_form = get_evcal_category_edit_form($category_id);
		
		echo $edit_form;
		
	break;
	
	case 'get_evcal_category_add_form':
		
		$add_form = get_evcal_category_add_form();
		
		echo $add_form;
		
	break;
	
	case 'delete_evcal_category':
		
		$category_id = value_proc($_POST['category_id']);
		
		$sql = "SELECT * FROM tasks_calendar_of_events_categories WHERE category_id='$category_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['user_id']!=$current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE tasks_calendar_of_events_categories SET deleted=1 WHERE category_id='$category_id'";
		
		$site_db->query($sql);
		
		$sql = "UPDATE tasks_calendar_of_events SET category_id=0 WHERE category_id='$category_id' AND user_id='$current_user_id'";
		$site_db->query($sql);
		
		echo 1;
		
	break;
	
	case 'event_offer_form':
			
		$event_id = value_proc($_POST['event_id']);
		
		$form = get_offer_event_form($event_id);
		
		echo $form;
		
	break;
	
	case 'add_event_offer':
		
		$event_id = value_proc($_POST['event_id']);
		
		$users = json_decode(str_replace('\\', '', $_POST['users']), 1);
		
		foreach($users as $user_id)
		{
			if(!$user_id)
			{
				continue;
			}
			
			$users_exists = 1;
		}
		
		if(!$users_exists)
		{
			$error = 1;
		}
		
		if(!$error)
		{
			foreach($users as $user_id)
			{
				if(!$user_id)
				{
					continue;
				}
				
				$sql = "INSERT INTO tasks_calendar_of_events_offers SET user_id='$user_id', event_id='$event_id', from_user_id='$current_user_id'";
				
				$site_db->query($sql);
			}
			 
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error));
		
	break;
	
	case 'to_delete_offer_event':
		
		$offer_id = value_proc($_POST['offer_id']);
		
		echo delete_event_offer($offer_id, $current_user_id);
		 
	break;
	
	case 'to_add_offer_event':
		
		$offer_id = value_proc($_POST['offer_id']);
		
		$sql = "SELECT j.*, i.offer_id FROM tasks_calendar_of_events_offers i
				LEFT JOIN tasks_calendar_of_events j ON i.event_id=j.event_id
				WHERE i.offer_id='$offer_id'";
		
		$event_data = $site_db->query_firstrow($sql);
		
		$sql = "INSERT INTO tasks_calendar_of_events SET event_name='".$event_data['event_name']."', event_desc='".$event_data['event_desc']."',event_type='".$event_data['event_type']."',content_id='".$event_data['content_id']."',user_id='$current_user_id',event_start_date='".$event_data['event_start_date']."',event_finish_date='".$event_data['event_finish_date']."',date_add=NOW(),added_by_user_id='$current_user_id'";
		
		$site_db->query($sql);
		
		// удаляем уведомление
		delete_event_offer($offer_id, $current_user_id);
		
		echo 1;
		
		
	break;
	
	case 'get_new_notices_count':
		
		$count = get_new_events_notices_count($current_user_id);
		
		echo $count;
		
	break;
	
	case 'show_evcal_popup':
		
		// Уведомление о новых планерках
		$evcal_popup = fill_evcal_popup();
	
		echo $evcal_popup;
		
	break;
	
	case 'hide_history_item':
		
		$event_id = value_proc($_POST['event_id']);
		
		// Даныне события
		$event_data = get_event_data($event_id);
		
		if($event_data['user_id']!=$current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".EVCAL_TB." SET hide=1 WHERE event_id='$event_id'";
		
		$site_db->query($sql);
		
		echo 1;
		
	break;
}

?>