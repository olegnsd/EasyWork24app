<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_sms.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/classes/class.Dialogs.php';
include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.Upload.php');
	
	
$mode = $_POST['mode'];
global $system_sms;
        $system_sms=1;
if(!$current_user_id)
{
	exit();
}

switch($mode)
{
	// Список заданий для сотрудника
	case 'add_new_msg':
		// Проверка авторизации
				
		$from_user_id = $current_user_id;
		
		$to_user_id = value_proc($_POST['to_user_id']);
		
		$to_sms = value_proc($_POST['to_sms']);
		
		$default_text = value_proc($_POST['default_text']);
		
		$msg_text = value_proc($_POST['msg_text']);
		
		$msg_theme = value_proc($_POST['msg_theme']);
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_content_type = value_proc($_POST['files_content_type']);
		
		$users_group = json_decode(str_replace('\\', '', $_POST['users_group']));
		
		if(!check_for_send_msg($to_user_id, $current_user_id))
		{
			$error['access_denied'] = 1;
		}
		
		if($msg_text=='' || $msg_text==$default_text)
		{
			$error['msg_text'] = 1;
		}
		
		if(!$to_user_id || !is_numeric($to_user_id))
		{
			exit();
		}
		
		$upl = new Upload($site_db);
		
		if(!$error)
		{
			$dialog_obj = new CDialogs($site_db, $current_user_id);
	
			// получаем ID диалога
			$dialog_id = $dialog_obj->get_users_dialog(array($current_user_id, $to_user_id));
			$dialog_obj->set_dialog_id($dialog_id);
			
			// добавляем сообщение
			$message_id = $dialog_obj->add_message_to_dialog($to_user_id, $msg_text, $msg_theme);
			 
			// Привязка файлов к контенту
			attach_files_to_content($message_id, $files_content_type, $files_arr, $current_user_id);
			
			foreach($users_group as $user_id)
			{
				if(!$user_id)
				{
					continue;
				}
				
				if(!check_for_send_msg($user_id, $current_user_id))
				{
					continue;
				}
		
				if($current_user_id==$user_id || $to_user_id==$user_id)
				{
					continue;
				}
				
				
				// получаем ID диалога
				$dialog_id = $dialog_obj->get_users_dialog(array($current_user_id, $user_id), 1);
				$dialog_obj->set_dialog_id($dialog_id);
				
				// добавляем сообщение
				$message_id = $dialog_obj->add_message_to_dialog($user_id, $msg_text, $msg_theme);
				
				// Привязка файлов к контенту
				attach_files_to_content($message_id, $files_content_type, $files_arr, $current_user_id, 1);
				
				// Отправляем смс сообщение
				if($to_sms)
				{
					send_sms_by_sms($user_id, $from_user_id, $msg_text);
				}
			
				//$users_group
			}
			
			// Отправляем смс сообщение
			if($to_sms)
			{
				send_sms_by_sms($to_user_id, $from_user_id, $msg_text);
			}
				
			$success = 1;
		}
		 
		echo json_encode(array('success' => $success, 'error' => $error, 'message_id' => $message_id));
	
	break;
	
	// Кол-во новых сообщений
	case 'get_new_msgs_count':
		
		// Кол-во новых сообщений для пользователя
		$new_msgs_count = get_new_user_messages_count();
		
		echo $new_msgs_count;
		
	break;
	
	// Кол-во новых сообщений
	case 'check_new_msgs':
		
		$user_id = value_proc($_POST['user_id']);
		
		$last_msg_id = value_proc($_POST['lmid']);
		
		$dialog_obj = new CDialogs($site_db, $current_user_id); 
		$user_dialogs_arr = $dialog_obj->get_user_dialogs(1);
		
		$last_msg_id = $dialog_obj->get_last_message_id_to_user($user_dialogs_arr); 
		 
		// Кол-во новых сообщений для пользователя
		$new_msgs_count = get_new_user_messages_count($user_id, $user_dialogs_arr);
		
		echo json_encode(array('new_msgs_count' => $new_msgs_count, 'last_msg_id' => $last_msg_id));
		
	break;
	
	// Получает больше сообщений
	case 'get_more_msgs':
			
		$to_user_id = value_proc($_POST['to_user_id']);
		
		$show = value_proc($_POST['show']);
		
		$page = value_proc($_POST['page']);
		
		$dialog_obj = new CDialogs($site_db, $current_user_id);
		
		// получаем ID диалога
		$dialog_id = $dialog_obj->get_users_dialog(array($current_user_id, $to_user_id));
		$dialog_obj->set_dialog_id($dialog_id);
		
		// Если показали все страницы, убираем ссылку "показать больше сообщений"
		if($page >= $_SESSION['msgs_count_page'])
		{
			$not_any_more = 1;
		}
		
		$msgs_list = fill_dialog_messages_list($dialog_id, $to_user_id, $page);
		
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $msgs_list), 'not_any_more' => $not_any_more));
		
	break;
	
	// Получает сообщение по id 
	case 'get_msg':
		
		$message_id = value_proc($_POST['message_id']);
		
		$from_user_id = value_proc($_POST['from_user_id']);
		
		$to_user_id = value_proc($_POST['to_user_id']);
		
		
		$dialog_obj = new CDialogs($site_db, $current_user_id);
		
		// получаем ID диалога
		$dialog_id = $dialog_obj->get_users_dialog(array($current_user_id, $to_user_id));
		$dialog_obj->set_dialog_id($dialog_id);
			
		$message_item = fill_dialog_messages_list($dialog_id, $to_user_id, '', $message_id);
		
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $message_item)));
	
	break;
	
	// Отметка о том, что сообщение прочитано
	case 'msg_read':
		
		$messages = value_arr_proc($_POST['messages']);
		
		$dialog_obj = new CDialogs($site_db, $current_user_id);
		
		$dialog_obj->dialog_messages_read($messages); 
		
		echo json_encode(array('success' => 1));
		
		
	break;
	
	// Удаление сообщений
	case 'delete_messages':
	
		$messages_arr = (array)json_decode(str_replace('\\','', $_POST['messages_arr']));
		
		$dialog_obj = new CDialogs($site_db, $current_user_id);
		
		$dialog_obj->delete_dialog_messages($messages_arr); 
		
		
		echo 1;
	break;
	
	// Восстановить сообщение
	case 'restore_msg':
		
		$message_id = value_proc($_POST['message_id']);
		
		$dialog_obj = new CDialogs($site_db, $current_user_id);
		
		$dialog_obj->restore_dialog_messages($message_id); 
	
		
		// Убираем из удаленных сообщения
		//unset($_SESSION['deleted_messages_ids'][$message_id]);
			
		echo 1;
	break;
	
	// Проверка на новые сообщения и вывод их
	case 'refresh_new_messages':
		
		$user_id = value_proc($_POST['user_id']);
		
		$dialog_obj = new CDialogs($site_db, $current_user_id);
		
		// получаем ID диалога
		$dialog_id = $dialog_obj->get_users_dialog(array($current_user_id, $user_id));
		$dialog_obj->set_dialog_id($dialog_id);
			
		$messages_list = fill_dialog_messages_list($dialog_id, $user_id, 0, 0, 1); 
		
		// Новые сообщения диалога
		$dialog_new_messages_ids_arr =  $dialog_obj->get_dialog_new_messages_ids_arr($dialog_id);
	
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $messages_list), 'dialog_new_messages_ids_arr' => $dialog_new_messages_ids_arr));
		
	break;
	
	// Удалить диалог с пользователем
	case 'delete_dialog':
		
		$dialog_user_id = value_proc($_POST['dialog_user_id']);
		
		$dialog_obj = new CDialogs($site_db, $current_user_id);
		
		// получаем ID диалога
		$dialog_id = $dialog_obj->get_users_dialog(array($current_user_id, $dialog_user_id));
		$dialog_obj->set_dialog_id($dialog_id);
		$dialog_obj->delete_dialog();
		
		echo 1;
	break;
	
	case 'dialogs_search':
		
		$search_word = value_proc($_POST['search_word']);
		
		$users_list = search_users_for_dialog($search_word);
		
		echo $users_list;

	break;
	
	case 'get_more_dialogs':
	
		$page = value_proc($_POST['page']);
		
		$dialogs_list = fill_messages_dialog_list($page);
		
		echo $dialogs_list ;
		
	break;
	
	
	case 'search_messages':
		
		$user_id = value_proc($_POST['to_user_id']);
		
		$search_words = value_proc($_POST['search_words']);
		
		$date_from = value_proc($_POST['date_from']);
		$date_to = value_proc($_POST['date_to']);
		
		$page = value_proc($_POST['page']);
		
		$messages_list = fill_search_dialog_messages($user_id, $page, $search_words, $date_from, $date_to);
		
		echo json_encode(array('messages_list' => iconv('cp1251', 'utf-8', $messages_list)));
				
	break;
	
	case 'dialog_messages':
		
		$message_id = value_proc($_POST['message_id']);
		
		$user_id = value_proc($_POST['to_user_id']);
		
		 
		// получаем ID диалога
	//	$dialog_id = $dialog_obj->get_users_dialog(array($current_user_id, $to_user_id));
	//	$dialog_obj->set_dialog_id($dialog_id);
			
		$messages_list = fill_dialog_content($user_id, $message_id);
		
		echo $messages_list;
	break;
	
	case 'add_user_to_msgs_group':
		
		$user_id = value_proc($_POST['user_id']);
		
		$group_id = value_proc($_POST['group_id']);
		
		if(!$user_id)
		{
			$error = '1';
		}
		
		/*if(!check_msgs_group_id_owner($group_id, $current_user_id))
		{
			exit();
		}*/
		
		// Данные планерки
		$sql = "SELECT * FROM tasks_messages_groups WHERE group_id='$group_id'";
		$msgs_group_data = $site_db->query_firstrow($sql);
		
		// Все пользователи диалога
		$sql = "SELECT * FROM tasks_messages_groups_users WHERE group_id='$group_id'";
		
		$res = $site_db->query($sql);
	 
		while($row=$site_db->fetch_array($res))
		{
			$users_arr[] = $row['user_id'];
		}
		
		// Пользователь уже состоит в диалоге
		if(in_array($user_id, $users_arr))
		{
			$error = '2';
		}
		
		if(!$error)
		{
			// Добавляем пользователя к диалогу
			$sql = "INSERT INTO tasks_messages_groups_users SET group_id='$group_id', user_id='$user_id', date_add = NOW()";
			$site_db->query($sql);
			
			
			### Уведомление по смс SMS
			// Заполянем объект пользователя
			$user_data = $user_obj->fill_user_data($user_id);
			$user_phone = $user_obj->get_user_phone();
			
			### sms body
			$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/planning_session_add_user.tpl');
			$sms_text = strlen($group_desc)>300 ? substr($group_desc,0,300).'...' : $group_desc;
			
			// Заполянем объект текущего пользователя
			$user_obj->fill_user_data($current_user_id);
			
			$boss_surname = $user_obj->get_user_surname();
			$boss_name = $user_obj->get_user_name();
			$boss_middlename = $user_obj->get_user_middlename();
			
			$PARS['{USER_SURNAME}'] = $boss_surname;
			$PARS['{USER_NAME}'] = $boss_name;
			$PARS['{USER_MIDDLENAME}'] = $boss_middlename;
			$PARS['{DESC}'] = $msgs_group_data['group_desc'] ?  '"'.$msgs_group_data['group_desc'].'"' : '';
			$sms_text = fetch_tpl($PARS, $sms_tpl);
			###\ sms body
			
			// Отправка смс сообщения
			send_sms_msg($user_phone, $sms_text);
					
			$success = 1;
		}
		
		echo json_encode(array('error' => $error, 'success' => $success));
		
	break;
	// Создать группу для сообщений
	case 'add_msgs_group':
		
		$user_id = value_proc($_POST['user_id']);
		
		$group_desc = value_proc($_POST['group_desc']);
		
		$users_arr = (array)json_decode(str_replace('\\','', $_POST['users_arr']));
		 
		if(!$users_arr)
		{
			$error['users'] = 1;
		}
		
		
		if(!$error)
		{
			// Заполянем объект текущего пользователя
			$user_obj->fill_user_data($user_id);
			
			$PARS['{USER_ID}'] = $user_id;
			
			$boss_surname = $user_obj->get_user_surname();
				
			$boss_name = $user_obj->get_user_name();
				
			$boss_middlename = $user_obj->get_user_middlename();
		
			// Создаем группу для сообщений
			$sql = "INSERT INTO ".MSGS_GROUPS_TB." (user_id, date_add, group_desc, group_is_online) VALUES ('$user_id', NOW(), '$group_desc', 1)";
			
			$site_db->query($sql);
			
			$msgs_group_id = $site_db->get_insert_id();
			
			$users_arr[] = $current_user_id;
			
			foreach($users_arr as $user)
			{
				// Добавляем пользователей для группы
				$sql = "INSERT INTO ".MSGS_GROUPS_USERS_TB." (group_id, user_id, date_add) VALUES('$msgs_group_id', '$user', NOW())";
				
				$site_db->query($sql);
				
				// Не уведомляем организатора планерки
				if($user!=$user_id)
				{
					### Уведомление по смс SMS
					// Заполянем объект пользователя
					$user_data = $user_obj->fill_user_data($user);
					$user_phone = $user_obj->get_user_phone();
					
					### sms body
					$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/planning_session.tpl');
					$sms_text = strlen($group_desc)>300 ? substr($group_desc,0,300).'...' : $group_desc;
					
					$PARS['{USER_SURNAME}'] = $boss_surname;
					$PARS['{USER_NAME}'] = $boss_name;
					$PARS['{USER_MIDDLENAME}'] = $boss_middlename;
					$PARS['{DESC}'] = $PARS['{DESC}'] = $group_desc ?  '"'.$group_desc.'"' : '';
					$sms_text = fetch_tpl($PARS, $sms_tpl);
					###\ sms body
					
					// Отправка смс сообщения
					send_sms_msg($user_phone, $sms_text);
				}
			 
			}
			
			// Если есть описание, добавляем его первым сообщением
			if($group_desc)
			{
				// Добавляем сообщение
				$sql = "INSERT INTO ".MSGS_GROUPS_MSGS_TB." (group_id, user_id, message_date, message_text) 
						VALUES ('$msgs_group_id', '$user_id', NOW(), '$group_desc')";
				 
				$site_db->query($sql);
				
				 
			}
			
			// Сообщение для группы
			set_msg_group_notices($msgs_group_id, $msgs_id);
			
			$success = 1;
		}
		
		echo json_encode(array('error' => $error, 'success' => $success, 'msgs_group_id' => $msgs_group_id));
	break;
	
	// Список заданий для сотрудника
	case 'add_new_msg_to_msgs_group':
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		} 
		
		$from_user_id = $current_user_id;
		
		$group_id = value_proc($_POST['group_id']);
		
		$default_text = value_proc($_POST['default_text']);
		
		$msg_text = value_proc($_POST['msg_text']);
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_content_type = value_proc($_POST['files_content_type']);
		
		if($msg_text=='' || $msg_text==$default_text)
		{
			$error['msg_text'] = 1;
		}
		
		// Проверка, на наличие созданной группы для сообщений
		$sql = "SELECT * FROM ".MSGS_GROUPS_TB." WHERE group_id='$group_id'";
		
		$msgs_group_data = $site_db->query_firstrow($sql);
		
		// Планерка завершена
		if(!$msgs_group_data['group_is_online'])
		{
			$error['offline'] = 1;
		}
		
		if(!$from_user_id)
		{
			exit();
		}
		
		if(!$error)
		{
			// Добавляем сообщение
			$sql = "INSERT INTO ".MSGS_GROUPS_MSGS_TB." (group_id, user_id,  	message_date, message_text) 
					VALUES ('$group_id', '$from_user_id', NOW(), '$msg_text')";
			 
			$site_db->query($sql);
			
			$msgs_id = $site_db->get_insert_id($sql);
			
			// Привязка файлов к контенту
			attach_files_to_content($msgs_id, $files_content_type, $files_arr, $current_user_id);
			
			set_msg_group_notices($group_id, $msgs_id);
				
			$success = 1;
		}
		 
		echo json_encode(array('success' => $success, 'error' => $error, 'message_id' => $msgs_id));
	
	break;
	
	// Получает больше сообщений
	case 'get_more_msgs_group':
		
		$group_id = value_proc($_POST['group_id']);
		
		$page = value_proc($_POST['page']);
		
		$msgs_list = fill_messages_group_list($group_id, $page);
		
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $msgs_list)));
		
	break;
	
	// Получает сообщение по id 
	case 'get_msg_group':
		
		$message_id = value_proc($_POST['message_id']);
		
		$group_id = value_proc($_POST['group_id']);
		
		$message_item = fill_messages_group_list($group_id, '', $message_id);
		
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $message_item)));
	
	break;
	
	// Проверка на новые сообщения и вывод их
	case 'refresh_new_messages_group':
		
		$group_id = value_proc($_POST['group_id']);
		
		// удаляем уведомления для пользовтаеля о новых сообщениях
		delete_group_messages_notices($group_id, $current_user_id);
	
		$sql = "SELECT message_id FROM ".MSGS_GROUPS_MSGS_TB." WHERE group_id='$group_id' AND message_id > '".$_SESSION['message_last_update_group_message_id']."' ORDER by message_id ASC";
	 
		$res = $site_db->query($sql);
	 
		while($row=$site_db->fetch_array($res))
		{
			// Собираем в список новые сообщения диалога
			$messages_list .= fill_messages_group_list($group_id, 0, $row['message_id']);
			
			$last_message_id = $row['message_id'];
		}
		
		// Последнее новое сообщение
		if($last_message_id)
		{
			$_SESSION['message_last_update_group_message_id'] = $last_message_id;
		}
		
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $messages_list)));
		
	break;
	
	case 'close_planning_session':
		
		$group_id = value_proc($_POST['group_id']);
		
		// Проверка, на наличие созданной группы для сообщений
		$sql = "SELECT * FROM ".MSGS_GROUPS_TB." WHERE group_id='$group_id'";
		
		$msgs_group_data = $site_db->query_firstrow($sql);
		
		if($msgs_group_data['user_id']!=$current_user_id)
		{
			exit();
		}
		
		// Закрываем планерку
		$sql = "UPDATE ".MSGS_GROUPS_TB." SET group_is_online=0 WHERE group_id='$group_id'";
		
		$site_db->query_firstrow($sql);
		
		if(!mysql_error())
			echo 1;
		
	break;
	
	case 'dialog_list_refresh':
		
		$dialog_block = fill_msges_dialogs_list_block(1);
		 
		// print_r( $dialog_block);
		echo  json_encode($dialog_block);
		  
		// echo $dialog_block;
		
	break;
	
	case 'search_in_messages':
		
		$search_words = value_proc($_POST['search_words']);
		
		$date_from = value_proc($_POST['date_from']);
		$date_to = value_proc($_POST['date_to']);
		
		$search_result = search_in_messages(1, $search_words, $date_from, $date_to);
		
		echo $search_result;
	break;
	
	case 'get_search_messages_more':
		
		$search_words = value_proc($_POST['search_words']);
		$date_from = value_proc($_POST['date_from']);
		$date_to = value_proc($_POST['date_to']);
		
		$page = value_proc($_POST['page']);
		
		$list = get_search_messages_list($page, $search_words, $date_from, $date_to);
		
		echo $list;
		
	break;
	
	case 'get_search_dialog_messages_more':
		
		$search_words = value_proc($_POST['search_words']);
		$date_from = value_proc($_POST['date_from']);
		$date_to = value_proc($_POST['date_to']);
		$to_user_id = value_proc($_POST['to_user_id']);
		
		$page = value_proc($_POST['page']);
		
		
		$list = fill_search_dialog_messages($to_user_id, $page, $search_words, $date_from, $date_to);
		
		echo $list;
		
	break;
	
	case 'show_session_planning':
		
		// Уведомление о новых планерках
		$planning_session_notice = fill_planning_session_notice();
	
		echo $planning_session_notice;
		
	break;
	
	case 'get_ps_history_count':
		
		$count = get_ps_history_count($current_user_id);
		
		echo $count;
		
	break;
	
	case 'get_ps_history_list':
		
		$page = value_proc($_POST['page']);
		
		$list = get_ps_history_list($current_user_id, $page);
		
		echo $list;
		
	break;
}

?>