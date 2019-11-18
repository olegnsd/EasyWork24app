<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/client/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/client/includes/functions_messages.php'; 
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_users.php';
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_client_id = $auth->get_current_client_id();

$current_user_id = $_SESSION['user_id'];

switch($mode)
{
	// Список заданий для сотрудника
	case 'add_new_msg':
	
		// Если не авторизованный клиент или пользоваетль
		if(!$current_client_id && !$_SESSION['user_id'])
		{
			exit();
		}
		
		$from_user_id = value_proc($_POST['from_user_id']);
		
		$from_client_id = value_proc($_POST['from_client_id']);
		
		$client_id = value_proc($_POST['client_id']);
		 
		$default_text = value_proc($_POST['default_text']);
		
		$msg_text = value_proc($_POST['msg_text']);
		
		if($from_client_id && $current_client_id!=$from_client_id)
		{
			$error['no_access'] = 1;
		}
		if($msg_text=='' || $msg_text==$default_text)
		{
			$error['msg_text'] = 1;
		}
		
		if(!$error)
		{
			// Добавляем сообщение
			$sql = "INSERT INTO ".CLIENT_MSGS_TB." (message_from_user_id, message_from_client_id, message_date, message_text, message_to_client_id) 
					VALUES ('$from_user_id', '$from_client_id', NOW(), '$msg_text', '$client_id')";
			 
			$site_db->query($sql);
			
			$inserted_id = $site_db->get_insert_id();
			
			if($from_user_id)
			{
				// Отмечаем, как прочитанные сообщения пользователя в диалоге
				$sql = "UPDATE ".CLIENT_MSGS_TB." SET message_to_user_noticed=1 WHERE message_to_client_id='$client_id' 
						AND message_to_client_id > 0 AND message_to_user_noticed <> 1";
			
				$site_db->query($sql);
			}
			
			$_SESSION['client_message_last_update_message_id'] = $inserted_id;
			
			$success = 1;
		}
		 
		echo json_encode(array('success' => $success, 'error' => $error, 'message_id' => $inserted_id));
	
	break;
	
	
	// Получает больше сообщений
	case 'get_more_msgs':
		
		$client_id = $_POST['client_id'];
		
		$page = $_POST['page'];
		
		// Если не авторизованный клиент или пользоваетль
		if(!$current_client_id && !$_SESSION['user_id'])
		{
			exit();
		}
		// Если показали все страницы, убираем ссылку "показать больше сообщений"
		if($page >= $_SESSION['client_msgs_count_page'])
		{
			$not_any_more = 1;
		}
		
		$show_msg_list_from_client = $_SESSION['client_msgs_show_from']['show_msg_list_from_client'];
		$show_msg_list_from_user = $_SESSION['client_msgs_show_from']['show_msg_list_from_user'];

		
		$msgs_list = fill_client_messages_list($client_id, $page, 0, $show_msg_list_from_client, $show_msg_list_from_user);
		
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $msgs_list), 'not_any_more' => $not_any_more));
		
	break;
	
	// Получает сообщение по id 
	case 'get_msg':
		
		$message_id = $_POST['message_id'];
		
		$client_id = $_POST['client_id'];
		$from_user = $_POST['from_user_id'] ? 1 : 0;
		$from_client = $_POST['from_client_id'] ? 1 : 0;
		
		$message_item = fill_client_messages_list($client_id, 0, $message_id, $from_client, $from_user);
		
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $message_item)));
	
	break;
	
	// Проверка на новые сообщения и вывод их
	case 'refresh_new_client_messages':
		
		$client_id = $_POST['client_id'];
		$from_user = $_POST['from_user_id'] ? 1 : 0;
		$from_client = $_POST['from_client_id'] ? 1 : 0;
		
		$sql = "SELECT message_id FROM ".CLIENT_MSGS_TB." WHERE message_to_client_id='$client_id' AND message_id > '".$_SESSION['client_message_last_update_message_id']."' ORDER by message_id ASC";
	 
		$res = $site_db->query($sql);
	 
		while($row=$site_db->fetch_array($res))
		{
			// Собираем в список новые сообщения диалога
			$messages_list .= fill_client_messages_list($client_id, 0, $row['message_id'], $from_client, $from_user);
			
			$last_message_id = $row['message_id'];
		}
		
		// Последнее новое сообщение
		if($last_message_id)
		{
			$_SESSION['client_message_last_update_message_id'] = $last_message_id;
		}
		
		echo json_encode(array('msgs' => iconv('cp1251', 'utf-8', $messages_list)));
		
	break;
	
	// Отметка о том, что сообщение прочитано
	case 'client_msg_read':
		
		$message_id = $_POST['message_id'];
		
		$client_id = $_POST['client_id'];
		
		$sql = "UPDATE ".CLIENT_MSGS_TB." SET message_to_user_noticed=1 WHERE message_id='$message_id' AND message_to_client_id='$client_id'";
		
		$site_db->query($sql);
		
		echo json_encode(array('success' => 1));
		
	break;
	
	// Кол-во новых сообщений
	case 'get_new_client_msgs_count':
		
		$client_id = $_POST['client_id'];
		
		// Кол-во новых сообщений для пользователя
		$new_messages_count = get_new_user_clients_messages_count($current_user_id);
			
		echo $new_messages_count;
	break;
}

?>