<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_clients.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deals.php';
include_once($_SERVER['DOCUMENT_ROOT'].'/includes/functions_calendar_of_events.php'); 
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_REQUEST['mode'];

$current_user_id = $auth->get_current_user_id();

if(!$current_user_id)
{
	exit();
}

switch($mode)
{
	// Добавить сделку сотрудника
	case 'add_new_deal':
		
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		}
	
		$deal_name = value_proc($_POST['deal_name']);
		
		$deal_type = value_proc($_POST['deal_type']);
		
		$deal_client = str_replace('-s-','',value_proc($_POST['deal_client']));
		
		$deal_status = value_proc($_POST['deal_status']);
		
		$deal_report = value_proc($_POST['deal_report']);
		
		$deal_private_edit = value_proc($_POST['deal_private_edit']);
		
		$deal_private_show = value_proc($_POST['deal_private_show']);
		
		$deal_other_info = value_proc($_POST['deal_other_info']);
		
		$deal_contact_person = value_proc($_POST['deal_contact_person']);
		
		$deal_email = value_proc($_POST['deal_email']);
		$deal_address = value_proc($_POST['deal_address']);
		$deal_phone = value_proc($_POST['deal_phone']);
		
		$deal_price = value_proc($_POST['deal_price']);
		 
		$group_id = value_proc($_POST['group_id']);
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_content_type = value_proc($_POST['files_content_type']);
		 
		// Название контакта пустое
		if($deal_client=='')
		{
			$error['deal_client'] = 1;
		}
		
		$deal_client_name = $deal_client;
		$deal_client_id = 0;
		// Если в качестве клиента передан его ID
		if(is_numeric($deal_client))
		{
			// находим такого клиента
			$sql = "SELECT client_id FROM ".CLIENTS_TB." WHERE client_id='$deal_client' AND client_deleted<>1";
			
			$row = $site_db->query_firstrow($sql);
			
			if($row['client_id'])
			{
				$deal_client_name = '';
				$deal_client_id = $deal_client;
			}
		}
		
		
		if(!$error)
		{
			if(preg_match('/-s-/', $group_id))
			{
				$group_id = str_replace('-s-', '', $group_id);
			}
			else
			{
				$group_id = check_deal_group($group_id, 1);
			}
			
			
			
			// Добавляем сделку
			$sql = "INSERT INTO ".DEALS_TB." SET deal_name='$deal_name', deal_type='$deal_type', deal_price='$deal_price', deal_client_id='$deal_client_id', deal_client_name='$deal_client_name', deal_private_edit='$deal_private_edit',  deal_private_show='$deal_private_show', deal_date_add=NOW(), user_id='$current_user_id', deal_other_info = '$deal_other_info', deal_contact_person = '$deal_contact_person',
			deal_email='$deal_email', deal_address='$deal_address', deal_phone='$deal_phone', group_id='$group_id '";
					
			$site_db->query($sql);
			
			$deal_inserted_id = $site_db->get_insert_id();
			
			// Добавляем статус и отчет
			$sql = "INSERT INTO ".DEALS_STATUSES_TB." SET  deal_id='$deal_inserted_id', user_id='$current_user_id', status_id='$deal_status', status_report='$deal_report', status_date=NOW()";
				
			$site_db->query($sql);
			
			$deal_status_inserted_id = $site_db->get_insert_id();
			
			// Обновляем последний статус сделки
			update_deals_status($deal_inserted_id, $deal_status);
			 
			// Привязка файлов к контенту
			attach_files_to_content($deal_inserted_id, $files_content_type, $files_arr, $current_user_id);
			 
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'deal_id' => $deal_inserted_id));
			
	break;
	
	// Импортировать сделки сотрудника
	case 'import_new_deal':
		
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		}
		
	
		$deal_name = value_proc($_POST['deal_name']);
		
		$deal_type = value_proc($_POST['deal_type']);
		
		$deal_client = str_replace('-s-','',value_proc($_POST['deal_client']));
		
		$deal_status = value_proc($_POST['deal_status']);
		
		$deal_report = value_proc($_POST['deal_report']);
		
		$deal_private_edit = value_proc($_POST['deal_private_edit']);
		
		$deal_private_show = 0;//value_proc($_POST['deal_private_show']);
		
		$deal_other_info = value_proc($_POST['deal_other_info']);
		
//		$deal_contact_person = value_proc($_POST['deal_contact_person']);
		
		$deal_email = value_proc($_POST['deal_email']);
		$deal_address = value_proc($_POST['deal_address']);
		$deal_phone = value_proc($_POST['deal_phone']);
		
		$deal_price = value_proc($_POST['deal_price']);
		 
		$group_id = value_proc($_POST['group_id']);
		
		$import_data = json_decode(str_replace('\\', '', $_FILES['import_data']));
//		$files_content_type = value_proc($_POST['files_content_type']);
		 
		// Название контакта пустое
		if($deal_client=='')
		{
			$error['deal_client'] = 1;
		}
		
		$deal_client_name = $deal_client;
		$deal_client_id = 0;
		// Если в качестве клиента передан его ID
		if(is_numeric($deal_client))
		{
			// находим такого клиента
			$sql = "SELECT client_id FROM ".CLIENTS_TB." WHERE client_id='$deal_client' AND client_deleted<>1";
			
			$row = $site_db->query_firstrow($sql);
			
			if($row['client_id'])
			{
				$deal_client_name = '';
				$deal_client_id = $deal_client;
			}
		}
		
		
		if(!$error)
		{
			if(preg_match('/-s-/', $group_id))
			{
				$group_id = str_replace('-s-', '', $group_id);
			}
			else
			{
				$group_id = check_deal_group($group_id, 1);
			}
			
			$import_data = file($_FILES["import_data"]["tmp_name"]);
			foreach($import_data as $phone){
				if(strlen($phone) < 11){
					continue;
				}
				$deal_client_name = value_proc($phone);
				$deal_contact_person = value_proc($phone);
				$deal_phone = value_proc($phone);
				// Добавляем сделку
				$sql = "INSERT INTO ".DEALS_TB." SET deal_name='$deal_name', deal_type='$deal_type', deal_price='$deal_price', deal_client_id='$deal_client_id', deal_client_name='$deal_client_name', deal_private_edit='$deal_private_edit',  deal_private_show='$deal_private_show', deal_date_add=NOW(), user_id='$current_user_id', deal_other_info = '$deal_other_info', deal_contact_person = '$deal_contact_person',
				deal_email='$deal_email', deal_address='$deal_address', deal_phone='$deal_phone', group_id='$group_id', deal_status='0', deal_deleted='0', deal_last_status_date=CURRENT_TIMESTAMP, deal_last_status='0' ";

				$site_db->query($sql);

				$deal_inserted_id = $site_db->get_insert_id();

				// Добавляем статус и отчет
				$sql = "INSERT INTO ".DEALS_STATUSES_TB." SET  deal_id='$deal_inserted_id', user_id='$current_user_id', status_id='$deal_status', status_report='$deal_report', status_date=NOW()";

				$site_db->query($sql);

				$deal_status_inserted_id = $site_db->get_insert_id();

				// Обновляем последний статус сделки
				update_deals_status($deal_inserted_id, $deal_status);

	//			// Привязка файлов к контенту
	//			attach_files_to_content($deal_inserted_id, $files_content_type, $files_arr, $current_user_id);
			}
			 
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'deal_id' => $deal_inserted_id, 'user_id' => $current_user_id));
			
	break;
	
	// Сохранить сделку
	case 'save_deal':
		
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		}
		$deal_id = value_proc($_POST['deal_id']);
	
		$deal_name = value_proc($_POST['deal_name']);
		
		$deal_type = value_proc($_POST['deal_type']);
		
		$deal_client = str_replace('-s-','',value_proc($_POST['deal_client']));
		
		$deal_price = value_proc($_POST['deal_price']);
		
		$deal_status = value_proc($_POST['deal_status']);
		
		$deal_report = value_proc($_POST['deal_report']);
		
		$deal_private_edit = value_proc($_POST['deal_private_edit']);
		
		$deal_private_show = value_proc($_POST['deal_private_show']);
		
		$deal_contact_person = value_proc($_POST['deal_contact_person']);
		
		$deal_other_info = value_proc($_POST['deal_other_info']);
		$deal_email = value_proc($_POST['deal_email']);
		$deal_address = value_proc($_POST['deal_address']);
		$deal_phone = value_proc($_POST['deal_phone']);
		
		$group_id = value_proc($_POST['group_id']);
		 
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_deleted = json_decode(str_replace('\\', '', $_POST['files_deleted']));
		$files_content_type = value_proc($_POST['files_content_type']); 
		
		
		// Данные по сделке
		$sql = "SELECT * FROM ".DEALS_TB." WHERE deal_id='$deal_id'";
			
		$deal_data = $site_db->query_firstrow($sql);
		
		// Массив всех сотрудников всех уровней
		$all_workers_arr = get_all_workers_arr_for_user($current_user_id);
		
		// Проверка возможности удаления сделки
		if(!is_deal_open_for_edit_for_user($current_user_id, $deal_data))
		{ 
			exit();
		}
		
		// Название контакта пустое
		if($deal_client=='')
		{
			$error['deal_client'] = 1;
		}
		
		$deal_client_name = $deal_client;
		$deal_client_id = 0;
		
		// Если в качестве клиента передан его ID
		if(is_numeric($deal_client))
		{
			// находим такого клиента
			$sql = "SELECT client_id FROM ".CLIENTS_TB." WHERE client_id='$deal_client' AND client_deleted<>1";
			
			$row = $site_db->query_firstrow($sql);
			
			if($row['client_id'])
			{
				$deal_client_name = '';
				$deal_client_id = $deal_client;
			}
		}
		
		
		if(!$error)
		{
			if(preg_match('/-s-/', $group_id))
			{
				$group_id = str_replace('-s-','', $group_id);
			}
			else
			{
				$group_id = check_deal_group($group_id, 1);
			}
			
			
			// Выбираем последний статус сделки
			$deal_status_data_arr = get_last_deal_status_arr($deal_id);
			
			// Если последний статус != новому
			//if($deal_status_data_arr['status_id']!=$deal_status && $deal_status)
			// Если статус изменялся, заносим в лог
			if(($deal_status_data_arr['status_id']!=$deal_status || $deal_status_data_arr['status_report']!=$deal_report))
			{
				$sql = "INSERT INTO ".DEALS_STATUSES_TB." SET  deal_id='$deal_id', user_id='$current_user_id', status_id='$deal_status', status_report='$deal_report', status_date=NOW()";
				
				$site_db->query($sql);
				
				// Обновляем последний статус сделки
				update_deals_status($deal_id, $deal_status);

			}
			
			// Класс для подсветки статуса сделки
			$deal_status_class = get_deal_status_back_class($deal_status);
			
			if($deal_data['user_id']==$current_user_id)
			{
				$and_deal_private_edit = ", deal_private_edit='$deal_private_edit'";
			}
			
			// Сохраняем сделку
			$sql = "UPDATE ".DEALS_TB." SET deal_name='$deal_name', deal_type='$deal_type',deal_price='$deal_price', 
			deal_client_id='$deal_client_id', deal_client_name='$deal_client_name', 
			deal_private_show='$deal_private_show', deal_contact_person = '$deal_contact_person', deal_other_info = '$deal_other_info',
			deal_email='$deal_email', deal_address='$deal_address', deal_phone='$deal_phone', group_id='$group_id' $and_deal_private_edit WHERE deal_id='$deal_id'";
					
			$site_db->query($sql);
			
			// Привязка файлов к контенту
			attach_files_to_content($deal_id, $files_content_type, $files_arr);
			
			// Удаляем прикрепленные файлы
			delete_attached_files_to_content($deal_id, $files_content_type, $files_deleted);
			 
			
			// Блок истории статусов
			$deal_history_status_block = fill_deal_history_block($deal_id);
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'deal_id' => $deal_inserted_id, 'deal_history_status_block' => iconv('cp1251', 'utf-8', $deal_history_status_block), 'deal_status_class'=>$deal_status_class));
			
	break;
	
	// Удалить клиента
	case 'delete_deal':
		
		$deal_id = value_proc($_POST['deal_id']);
		
		// Данные по сделке
		$sql = "SELECT * FROM ".DEALS_TB." WHERE deal_id='$deal_id'";
			
		$deal_data = $site_db->query_firstrow($sql);
		
		// Массив всех сотрудников всех уровней
		$all_workers_arr = get_all_workers_arr_for_user($current_user_id);
		
		// Проверка возможности удаления сделки
		if(!is_deal_open_for_delete_for_user($current_user_id, $deal_data, $all_workers_arr))
		{
			exit();
		}
		
		$sql = "UPDATE ".DEALS_TB." SET  deal_deleted='1' WHERE deal_id='$deal_id'";
		
		$site_db->query($sql);
		
		$_SESSION['deal_deleted'][] = $deal_id;
		
		echo 1;
	break;
	
	// Восстановить клиента
	case 'restore_deal':
		
		$deal_id = value_proc($_POST['deal_id']);

		$sql = "UPDATE ".DEALS_TB." SET  deal_deleted='0' WHERE deal_id='$deal_id'";
		
		$site_db->query($sql);
		
		$_SESSION['deal_deleted'][$deal_id]=='';
		
		echo 1;
	break;
	
	// Поиск по сделкам
	case 'deals_search':
		
		$search_word = value_proc($_POST['search_word']);
		
		$user_id = $_POST['user_id'];
		
		$deal_list_type = $_POST['deal_list_type'];
		
		// Очистка массива удаленных сделок
		if($_SESSION['deals_deleted'])
		{
			$_SESSION['deals_deleted']='';
		}
		
		// Кол-во найденных сделок
		$deals_count = get_current_user_deals_count($deal_list_type, $search_word);
			
		// Список сделок
		$deals_list = fill_deals_list($deal_list_type, 1, $search_word);
		
		// Кол-во страниц
		$pages_count = ceil($deals_count/DEALS_PER_PAGE);
		
		if(!$deals_list)
		{
			$deals_list  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deals/no_searched_deals.tpl');
		}
		
		// Возвращаем результат
		echo json_encode(array('deals_list' => iconv('cp1251', 'utf-8', $deals_list), 'deals_count' => $deals_count, 'pages_count' => $pages_count));

	break;
	
	// Возвращает список сделок
	case 'get_more_deals':
		
		$user_id = value_proc($_POST['user_id']);
		
		$page = value_proc($_POST['page']);
		
		$search_word = value_proc($_POST['search_word']);
		
		$deal_list_type = value_proc($_POST['deal_list_type']);
		 
		// Список контактов
		$deals_list = fill_deals_list($deal_list_type, $page, $search_word);
		
		echo $deals_list;
		
	break;
	
	case 'get_deals_groups':
		
		$mode = $_POST['mode'];

		$current_user_id = $_SESSION['user_id'];
		
		$tag = iconv('UTF-8', 'windows-1251',$_REQUEST['tag']);
		
		// Выбираем найденных пользователей
		$sql = "SELECT * FROM ".DEALS_GROUPS_TB." WHERE group_name LIKE '%$tag%' LIMIT 20";
		  
		$res = $site_db->query($sql);
			
		while($row=$site_db->fetch_array($res)) {
			  $tmp = array();
			  
			  $tmp['value'] = iconv('windows-1251', 'UTF-8', $row['group_name']);
			  
			  $tmp['key'] = '-s-'.$row['group_id'];
			  
			  $result[] = $tmp;
		}
		
		echo json_encode($result);
		
	break;
	
	case 'give_access_to_deal':
		
		$deal_id = value_proc($_POST['deal_id']);
		
		$user_id = value_proc($_POST['user_id']);
	
		if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1)))
		{
			exit();
		}
		
		// Данные документа
		$sql = "SELECT * FROM ".DEALS_TB." WHERE deal_id='$deal_id'";
		
		$deal_data = $site_db->query_firstrow($sql);
		
		
		// Выбор всех сотрудников, имеющих доступ к заметке
		$sql = "SELECT * FROM ".DEALS_ACCESSES_TB." WHERE deal_id='$deal_id'";
		
		$res = $site_db->query($sql);
			
		while($row=$site_db->fetch_array($res))
		{
			$users_access_arr[] = $row['user_id'];
		}
		
		// Проверка на возможность передать клиента
		if(!in_array($current_user_id, $users_access_arr) && $current_user_id!=$deal_data['user_id'])
		{
			exit();
		}
		
		// Проверяем, есть ли доступ у пользователя
		$sql = "SELECT id FROM ".DEALS_ACCESSES_TB." WHERE user_id='$user_id' AND deal_id='$deal_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			$sql = "DELETE FROM ".DEALS_ACCESSES_TB." WHERE id='".$row['id']."'";
			
			$site_db->query($sql);
			
			if(!mysql_error())
				echo 1;
		}
		else
		{
			$sql = "INSERT INTO ".DEALS_ACCESSES_TB." (deal_id, user_id, access_by_user_id, date) VALUES ('$deal_id', '$user_id', '$current_user_id', NOW())";
			
			$site_db->query($sql);
			
			if(!mysql_error())
				echo 2;
		}
		
	break;
	
	// Возвращает список сделок
	case 'get_user_more_deals':
		
		$user_id = value_proc($_POST['user_id']);
		
		$page = value_proc($_POST['page']);
		
		// Список сделок 
		$deals_list_arr = get_user_deals_list($user_id, $page);
		
		$deals_list = $deals_list_arr['list'];
		
		// Кол-во сделок пользователя
		$user_deals_count = get_user_deals_count($user_id);
		
		// Кол-во страниц
		$pages_count = ceil($user_deals_count/WORKERS_DEALS_PER_PAGE);
		 
		// Если страниц больше 1
		if($page >= $pages_count)
		{
			// Убираем кнопку - вывести еще сделки
			$hide_more_pages_btn = 1;
		}
		
	 
		// Возвращаем результат
		echo json_encode(array('deals_list' => iconv('cp1251', 'utf-8', $deals_list), 'hide_more_pages_btn'=>$hide_more_pages_btn));
		
	break;
	
	case 'get_deals':
		
		$mode = $_POST['mode'];
		
		$tag = iconv('UTF-8', 'windows-1251',$_REQUEST['tag']);
		
		$sql = "SELECT * FROM ".DEALS_TB." WHERE (deal_name LIKE '%$tag%' OR deal_id='$tag') AND deal_deleted=0 LIMIT 20";
		
		$res = $site_db->query($sql);
		
		while($row=$site_db->fetch_array($res)) {
			
			  $tmp = array();
			  
			  $tmp['value'] = iconv('windows-1251', 'UTF-8', '№'.$row['deal_id'].' '.$row['deal_name']);
			  
			  $tmp['key'] = '-s-'.$row['deal_id'];
			  
			  $result[] = $tmp;
		}
		
		echo json_encode($result);
		
	break;	
	
	case 'set_deal_notice_date':
		 
		$deal_id = $_POST['deal_id'];
		
		$reminder_date = $_POST['reminder_date'];
		
		$reminder_date_hour = $_POST['reminder_date_hour'];
		
		$reminder_date_minute = $_POST['reminder_date_minute'];
		
		if(!date_rus_validate($reminder_date))
		{
			$error['reminder_date'] = 1;
		}
		
		
		 
		
		//$reminder_date_norm = formate_to_norm_date($reminder_date);
		
		//$reminder_date = to_mktime($reminder_date_norm);
		
		// Переводим в секунды даты старта и завершения события
		$reminder_date = to_mktime(formate_to_norm_date($reminder_date))+$reminder_date_hour+$reminder_date_minute;
		
		// Ставим уведомление на 9 часов утра
		$date_start_to_evcal = $reminder_date;
				
		if($reminder_date<to_mktime(date('Y-m-d')))
		{
			$error['reminder_date'] = 1;
		}
		
		if(!$error)
		{			
			// Данные по сделке
			$sql = "SELECT * FROM ".DEALS_TB." WHERE deal_id='$deal_id'";
				
			$deal_data = $site_db->query_firstrow($sql);
			
			// Выбор напоминания для сделки
			$sql = "SELECT reminder_id FROM ".DEALS_REMINDERS_TB." WHERE deal_id='$deal_id' AND user_id='$current_user_id'";
	 
			$row = $site_db->query_firstrow($sql);
			
			if($row['reminder_id'])
			{
				$reminder_id = $row['reminder_id'];
				
				$sql = "UPDATE ".DEALS_REMINDERS_TB." SET reminder_date='$reminder_date' WHERE reminder_id='$reminder_id'";
				
				$site_db->query($sql);
				
				// Удаляем старое событие в календаре
				delete_evcal_content_event(2, $deal_id, $current_user_id);
				
				// Ставим уведомление о напоминание сделки в календарь
				add_evcal_content_event(2, $deal_id, $current_user_id, $current_user_id, $deal_data['deal_name'], '', $date_start_to_evcal, $date_start_to_evcal);
			}
			else
			{
	
				// Добавить напоминание
				$sql = "INSERT INTO ".DEALS_REMINDERS_TB." SET deal_id='$deal_id', user_id='$current_user_id', reminder_date='$reminder_date'";
				
				$site_db->query($sql);
				
				$reminder_id = $site_db->get_insert_id();
				
				// Ставим уведомление о напоминание сделки в календарь
				add_evcal_content_event(2, $deal_id, $current_user_id, $current_user_id, $deal_data['deal_name'], '', $date_start_to_evcal, $date_start_to_evcal);
			}
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'reminder_id' => $reminder_id));
		
	break;
	
	case 'delete_deal_reminder':
		
		$reminder_id = $_POST['reminder_id'];
		
		$sql = "SELECT * FROM tasks_deals_reminders WHERE reminder_id='$reminder_id'";
		$row = $site_db->query_firstrow($sql);
		
		$deal_id = $row['deal_id'];
		
		$sql = "DELETE FROM ".DEALS_REMINDERS_TB." WHERE reminder_id='$reminder_id' AND user_id='$current_user_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			// Удаляем старое событие в календаре
			delete_evcal_content_event(2, $deal_id, $current_user_id);
			
			echo 1;	
		}
		
	break;
	
	case 'get_deal_reminder_block':
		
		$deal_id = $_POST['deal_id'];
		
		echo fill_deal_reminder_block($deal_id);
		
	break;
	
	case 'save_deal_user_access':
		
		$deal_id = value_proc($_POST['deal_id']);
		$access_users = (array)json_decode(str_replace('\\', '', $_POST['access_users']), 1);

		 
		// Данные документа
		$sql = "SELECT * FROM ".DEALS_TB." WHERE deal_id='$deal_id'";
		
		$deal_data = $site_db->query_firstrow($sql);
		
		
		// Выбор всех сотрудников, имеющих доступ к заметке
		$sql = "SELECT * FROM tasks_deals_users_access WHERE deal_id='$deal_id'";
		
		$res = $site_db->query($sql);
		
		$users_access_arr = array();
			
		while($row=$site_db->fetch_array($res))
		{
			$users_access_arr[$row['user_id']] = $row['user_id'];
		}
		
		//if(!in_array($current_user_id, $users_access_arr) && $current_user_id!=$deal_data['user_id'])
		if($current_user_id != $deal_data['user_id'] && !is_deal_open_for_edit_for_user($current_user_id, $deal_data) && !check_deal_for_available($current_user_id, $deal_id, $deal_data))
		{
			exit();
		}

		
		$to_delete = array_diff($users_access_arr, $access_users);
		$to_add = array_diff($access_users, $users_access_arr);
		 
		foreach($to_delete as $user_id)
		{
			if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1,1)) && $deal_data['user_id']!=$current_user_id)
			{
				continue;
			}
			
			$sql = "DELETE FROM tasks_deals_users_access WHERE user_id='$user_id' AND deal_id='$deal_id'";
			
			$site_db->query($sql);
		}
		 
		foreach($to_add as $user_id)
		{  
			if(!$user_id || $user_id==$deal_data['user_id'])
			{
				continue;
			}
			
			if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1,1)))
			{
				 continue;
			}
		
			$sql = "INSERT INTO tasks_deals_users_access SET user_id='$user_id', deal_id='$deal_id', access_by_user_id='$current_user_id', date=NOW()";
			
			$site_db->query($sql);
		}
		
		echo 1;
		
	break;
	
	case 'get_deal_access_block':
		
		$deal_id = value_proc($_POST['deal_id']);
		
		// Выбораем данные сделки
		$sql = "SELECT * FROM ".DEALS_TB." WHERE deal_id='$deal_id'";
	
		$deal_data = $site_db->query_firstrow($sql);
		
		$access_block = fill_deal_access_block($deal_data);
		
		echo $access_block;
		
	break;
}

?>
