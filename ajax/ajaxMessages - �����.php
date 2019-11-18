<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_sms.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/classes/class.Dialogs.php';

$mode = $_POST['mode'];

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
		
		if($msg_text=='' || $msg_text==$default_text)
		{
			$error['msg_text'] = 1;
		}
		
		if(!$to_user_id)
		{
			exit();
		}
		
		if(!$error)
		{
			$dialog_obj = new CDialogs($site_db, $current_user_id);
	
			// получаем ID диалога
			$dialog_id = $dialog_obj->get_users_dialog(array($current_user_id, $to_user_id));
			$dialog_obj->set_dialog_id($dialog_id);
			
			$message_id = $dialog_obj->add_message_to_dialog($to_user_id, $msg_text);
			
			// Добавляем сообщение
			$sql = "INSERT INTO ".MSGS_TB." (message_from_user_id, message_to_user_id, 	message_date, message_text) 
					VALUES ('$from_user_id', '$to_user_id', NOW(), '$msg_text')";
			 
			$site_db->query($sql);
			
			$sql = "SELECT message_id FROM ".MSGS_TB." WHERE message_from_user_id='$from_user_id' AND message_to_user_id='$to_user_id' ORDER BY message_id DESC LIMIT 1";
			
			$row = $site_db->query_firstrow($sql);
			
			$inserted_id = $row['message_id'];
			
			// Отмечаем, как прочитанные сообщения пользователя в диалоге
			$sql = "UPDATE ".MSGS_TB." SET message_to_user_noticed=1, message_read_date=NOW() WHERE message_to_user_id='$from_user_id' AND message_from_user_id='$to_user_id' AND message_to_user_noticed <> 1";
		
			$site_db->query($sql);
			
			// Отправляем смс сообщение
			if($to_sms)
			{
				// Заполянем объект пользователя, кому написали сообщение
				$user_obj->fill_user_data($to_user_id);
				
				$user_phone = $user_obj->get_user_phone();
				
				### sms body
				$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/new_message.tpl');
				
				$sms_text = $msg_text;
				
				$sms_text = strlen($sms_text)>50 ? substr($sms_text,0,50).'...' : $sms_text;
				
				// Заполянем объект пользователя, кто принял задание
				$user_obj->fill_user_data($from_user_id);
					
				$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
				
				$PARS['{USER_NAME}'] = $user_obj->get_user_name();
				
				$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
				
				$PARS['{MSG_TEXT}'] = $sms_text;
				 
				$sms_text = fetch_tpl($PARS, $sms_tpl);
				###\ sms body
				
				// Отправка смс сообщения
				send_sms_msg($user_phone, $sms_text);
			}
				
			$success = 1;
		}
		 
		echo json_encode(array('success' => $success, 'error' => $error, 'message_id' => $inserted_id));
	
	break;
	
	// Кол-во новых сообщений
	case 'get_new_msgs_count':
		
		$user_id = value_proc($_POST['user_id']);
		
		// Кол-во новых сообщений для пользователя
		$new_msgs_count = get_count_user_new_messages($user_id);
		
		echo $new_msgs_count;
		
	break;
	
	// Кол-во новых сообщений
	case 'check_new_msgs':
		
		$user_id = value_proc($_POST['user_id']);
		
		$last_msg_id = value_proc($_POST['lmid']);
		
		$sql = "SELECT message_id FROM ".MSGS_TB." WHERE message_to_user_id='$current_user_id' ORDER by message_id DESC LIMIT 1";
		
		$row = $site_db->query_firstrow($sql);
		
		$last_msg_id = $row['message_id'];
		 
		// Кол-во новых сообщений для пользователя
		$new_msgs_count = get_count_user_new_messages($user_id);
		
		echo json_encode(array('new_msgs_count' => $new_msgs_count, 'last_msg_id' => $last_msg_id));
		
	break;
	
	// Получает больше сообщений
	case 'get_more_msgs':
		
		$from_user_id = $current_user_id;
		
		$to_user_id = value_proc($_POST['to_user_id']);
		
		$page = value_proc($_POST['page']);
		
		// Если показали все страницы, убираем ссылку "показать больше сообщений"
		if($page >= $_SESSION['msgs_count_page'])
		{
			$not_any_more = 1;
		}
		
		$msgs_list = fill_messages_list($from_user_id, $to_user_id, $page);
		
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $msgs_list), 'not_any_more' => $not_any_more));
		
	break;
	
	// Получает сообщение по id 
	case 'get_msg':
		
		$message_id = value_proc($_POST['message_id']);
		
		$from_user_id = value_proc($_POST['from_user_id']);
		
		$to_user_id = value_proc($_POST['to_user_id']);
		
		$message_item = fill_messages_list($from_user_id, $to_user_id, 0, $message_id);
		
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $message_item)));
	
	break;
	
	// Отметка о том, что сообщение прочитано
	case 'msg_read':
		
		$message_id = value_proc($_POST['message_id']);
		
		$from_user_id = value_proc($_POST['from_user_id']);
		
		$to_user_id = value_proc($_POST['to_user_id']);
		
		$sql = "UPDATE ".MSGS_TB." SET message_to_user_noticed=1, message_read_date=NOW() WHERE message_id='$message_id' AND message_from_user_id='$from_user_id' AND message_to_user_id='$to_user_id'";
		
		$site_db->query($sql);
		
		echo json_encode(array('success' => 1));
		
		
	break;
	
	// Удаление сообщений
	case 'delete_messages':
	
		$messages_arr = json_decode(str_replace('\\','', $_POST['messages_arr']));
		
		foreach($messages_arr as $message_id)
		{
			$sql = "SELECT message_from_user_id, message_to_user_id FROM ".MSGS_TB." WHERE message_id='$message_id'";
			
			$row = $site_db->query_firstrow($sql);
			
			if($row['message_from_user_id']==$current_user_id)
			{
				$sql = "UPDATE ".MSGS_TB." SET message_from_user_deleted=1 WHERE message_id='$message_id'";
				
				$site_db->query($sql);
			}
			else
			{
				$sql = "UPDATE ".MSGS_TB." SET message_to_user_deleted=1 WHERE message_id='$message_id'";
				
				$site_db->query($sql);
			}
		 
		}
		
		echo 1;
	break;
	
	// Восстановить сообщение
	case 'restore_msg':
		
		$message_id = value_proc($_POST['message_id']);
		
		$sql = "SELECT message_from_user_id, message_to_user_id FROM ".MSGS_TB." WHERE message_id='$message_id'";
		
		$row = $site_db->query_firstrow($sql);
			
		if($row['message_from_user_id']==$current_user_id)
		{
			$sql = "UPDATE ".MSGS_TB." SET message_from_user_deleted=0 WHERE message_id='$message_id'";
				
			$site_db->query($sql);
		}
		else
		{
			$sql = "UPDATE ".MSGS_TB." SET message_to_user_deleted=0 WHERE message_id='$message_id'";
				
			$site_db->query($sql);
		}
		
		// Убираем из удаленных сообщения
		unset($_SESSION['deleted_messages_ids'][$message_id]);
			
		echo 1;
	break;
	
	// Проверка на новые сообщения и вывод их
	case 'refresh_new_messages':
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "SELECT message_id, message_from_user_id FROM ".MSGS_TB." WHERE message_to_user_id='$current_user_id' AND message_from_user_id='$user_id' AND message_id > '".$_SESSION['message_last_update_message_id']."' ORDER by message_id ASC";
	 
		$res = $site_db->query($sql);
	 
		while($row=$site_db->fetch_array($res))
		{
			// Собираем в список новые сообщения диалога
			$messages_list .= fill_messages_list($current_user_id, $row['message_from_user_id'], 0, $row['message_id']);
			
			$last_message_id = $row['message_id'];
		}
		
		// Последнее новое сообщение
		if($last_message_id)
		{
			$_SESSION['message_last_update_message_id'] = $last_message_id;
		}
		
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $messages_list)));
		
	break;
	
	// Удалить диалог с пользователем
	case 'delete_dialog':
		
		$dialog_user_id = value_proc($_POST['dialog_user_id']);
		
		$sql = "UPDATE ".MSGS_TB." SET message_from_user_deleted = 2 WHERE message_from_user_id='$current_user_id' AND message_to_user_id='$dialog_user_id'";
		
		$site_db->query($sql);
		
		$sql = "UPDATE ".MSGS_TB." SET message_to_user_deleted = 2 WHERE message_to_user_id='$current_user_id' AND message_from_user_id='$dialog_user_id'";
		
		$site_db->query($sql);
		
		echo 1;
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
					$PARS['{DESC}'] = $group_desc;
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
				
			$success = 1;
		}
		 
		echo json_encode(array('success' => $success, 'error' => $error, 'message_id' => $msgs_id));
	
	break;
	
	// Получает больше сообщений
	case 'get_more_msgs_group':
		
		$group_id = value_proc($_POST['group_id']);
		
		$page = value_proc($_POST['page']);
		
		// Если показали все страницы, убираем ссылку "показать больше сообщений"
		if($page >= $_SESSION['msgs_count_page'])
		{
			$not_any_more = 1;
		}
		
		$msgs_list = fill_messages_group_list($group_id, $page);
		
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $msgs_list), 'not_any_more' => $not_any_more));
		
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
}

?>