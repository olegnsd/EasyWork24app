<?php
// Страница сообщений с клиентом
function fill_client_messages($client_id, $show_msg_list_from_client, $show_msg_list_from_user)
{
	global $site_db, $current_client_id, $current_user_id;
	
	if($user_id==$current_user_id)
	{
		//header('Location: /client/msgs');
	}
	
	$_SESSION['client_msgs_show_from'] = array('show_msg_list_from_client' => $show_msg_list_from_client, 'show_msg_list_from_user' => $show_msg_list_from_user);
	
	$messages_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/messages/messages_list.tpl');
	
	$more_msgs_link_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/messages/more_msgs_link.tpl');
	
	// Кол-во сообщений
	$msgs_count = get_client_msgs_count($client_id);
	
	// Кол-во страниц 
	$msgs_count_page = ceil($msgs_count/MSG_PER_PAGE);
	
	$_SESSION['client_msgs_count_page'] = $msgs_count_page;
	
	// Выбираем id последнего сообщения в базе
	$sql = "SELECT message_id FROM ".CLIENT_MSGS_TB." ORDER by message_id DESC LIMIT 1";
	
	$row = $site_db->query_firstrow($sql);
	 
	$message_last_id = $row['message_id'];
	 
	$_SESSION['client_message_last_id'] = $message_last_id;
	$_SESSION['client_message_last_update_message_id'] = $message_last_id;
	
	// Список сообщений 
	$msgs_list = fill_client_messages_list($client_id, 0, 0, $show_msg_list_from_client, $show_msg_list_from_user);
	
	if($msgs_count_page > 1)
	{
		$more_msgs_link = $more_msgs_link_tpl;
	}
	
	if($show_msg_list_from_client)
	{
		$dialog_title = 'Клиенты';
	}
	else
	{
		// Данные клиента
		$client_data = get_client_data($client_id);
		
		$client_type_name = $client_data['client_organization_type_id'] ? $client_data['type_name'] : '';
		$dialog_title = $client_type_name.' '.$client_data['client_name'];
	}
	
	$PARS['{DIALOG_TITLE}'] = $dialog_title;
	 
	$PARS['{FROM_CLIENT_ID}'] = $show_msg_list_from_client ? $current_client_id : 0;
	
	$PARS['{FROM_USER_ID}'] = $show_msg_list_from_user ? $current_user_id : 0;
	
	$PARS['{MSGS_LIST}'] = $msgs_list;
	
	$PARS['{CLIENT_ID}'] = $client_id;
	
	$PARS['{CURRENT_USER_ID}'] = $current_user_id;
	
	$PARS['{MORE_MSGS_LINK}'] = $more_msgs_link;
	
	return fetch_tpl($PARS, $messages_list_tpl);
}

// Список сообщений между пользователями и клиентом
function fill_client_messages_list($client_id, $page=0, $message_id=0, $show_msg_list_from_client, $show_msg_list_from_user)
{
	
	global $site_db, $current_user_id, $user_obj;

	$messages_bind_over_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/messages/messages_bind_over.tpl');
	
	$no_messages_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/messages/no_messages.tpl');
	
	$msg_read_date_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/messages/msg_read_date.tpl');

	$page = !$page ? 1 : $page;
	
	// Данные клиента
	$sql = "SELECT i.*, j.type_name FROM ".CLIENTS_TB." i
			LEFT JOIN ".CLIENTS_TYPES_DATA." j ON i.client_organization_type_id=j.type_id 
			WHERE client_id='$client_id'"; 
			
	$client_data = $site_db->query_firstrow($sql, 1);
	
	$begin_pos = MSG_PER_PAGE * ($page - 1);
	
	$limit = " LIMIT $begin_pos, ".MSG_PER_PAGE;
	
	// При подгрузке большего количества сообщений, не выводим те, которые добавлялись в открытом окне диалога
	if($page>1)
	{
		if($_SESSION['client_message_last_id'])
		{
			$and_not_messages = " AND message_id <= '".$_SESSION['client_message_last_id']."'";
		}
	}

	// Если возвратить только 1 сообщение по ID
	if($message_id)
	{
		$sql = "SELECT * FROM ".CLIENT_MSGS_TB." 
				WHERE message_id='$message_id' LIMIT 1";
	}
	else
	{
		$sql = "SELECT * FROM ".CLIENT_MSGS_TB." 
				WHERE  message_to_client_id='$client_id'
				$and_not_messages
				ORDER by message_id DESC $limit"; 
	}
	 	 
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res, 1))
	{
		$bind_over_msg_read = '';
		
		// Если сообщение, которое отсылал текущий пользователь
		if($row['message_from_user_id'] > 0 && !$row['message_from_client_id'])
		{
			$msg_item = fill_message_item_from_user($row, $row['message_from_user_id'], $show_msg_list_from_client, $show_msg_list_from_user);
			
		}
		else
		{
			$msg_item = fill_message_item_from_client($row, $client_id, $client_data, $show_msg_list_from_client, $show_msg_list_from_user);
		}

		$msgs_list = $msg_item.$msgs_list;
	}
	
	if(!$msgs_list)
	{
		$msgs_list = $no_messages_tpl;
	}
	
	return $msgs_list;
}

// заполнение блока сообщения пользователя
function fill_message_item_from_user($message_data, $user_id, $show_msg_list_from_client, $show_msg_list_from_user)
{
	global $site_db, $current_user_id, $user_obj;
	
	$messages_list_item_from_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/messages/messages_list_item_from_user.tpl');
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($user_id);
		
	$user_surname = $user_obj->get_user_surname();
				
	$user_name = $user_obj->get_user_name();
				
	$user_middlename = $user_obj->get_user_middlename();
				
	$user_position = $user_obj->get_user_position();
			
	$user_image = $user_obj->get_user_image(); 
			 
	// онлайн пользователь?
	$user_is_online = user_in_online_icon($user_id, $user_obj->get_user_last_visit_date());
			
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($user_id, $user_image);
	
	$PARS['{MESSAGE_ID}'] = $message_data['message_id'];
	
	$PARS['{USER_ONLINE}'] = $user_is_online;
	$PARS['{FROM_USER_ID}'] = 	$user_id;
	$PARS['{SURNAME}'] = $user_surname;
			
	$PARS['{NAME}'] = $user_name;
			
	$PARS['{MIDDLENAME}'] = $user_middlename;
			
	$PARS['{USER_POSITION}'] = $user_position;
			
	$PARS['{USER_AVATAR_SRC}'] = $user_avatar_src;
		
	$PARS['{MSG_TEXT}'] = stripslashes(nl2br($message_data['message_text']));
	
	$PARS['{MSG_DATE}'] = formate_date_rus($message_data['message_date']);
	
	return fetch_tpl($PARS, $messages_list_item_from_user_tpl);
}

// заполнение блока сообщения клиента
function fill_message_item_from_client($message_data, $client_id, $client_data, $show_msg_list_from_client, $show_msg_list_from_user)
{
	global $site_db, $current_user_id, $user_obj;
	
	$messages_list_item_from_client_tpl=file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/messages/messages_list_item_from_client.tpl');
	$messages_bind_over_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/messages/messages_bind_over.tpl');
	 
	$client_online_icon = client_in_online_icon($client_id, $client_data['client_last_visit_date']);
	
	// Если сообщение не прочитано
	if($message_data['message_to_user_noticed']==0 && $show_msg_list_from_user)
	{
		$msg_not_read = 'msg_not_read';
		
		$PARS1['{MESSAGE_ID}'] = $message_data['message_id'];
		$PARS1['{CLIENT_ID}'] = $client_id;
		$bind_over_msg_read = fetch_tpl($PARS1, $messages_bind_over_tpl);
	}
			
	$read_status = $message_data['message_to_user_noticed'] == 0 ? 0 : 1;
		
	$PARS['{MESSAGE_ID}'] = $message_data['message_id'];
	 
	$PARS['{CLIENT_ID}'] = $client_id;
		
	$PARS['{CLIENT_NAME}'] =  $client_data['client_name'];
	
	$PARS['{CLIENT_TYPE}'] =  $client_data['client_organization_type_id'] ? $client_data['type_name'] : '';
	
	$PARS['{MSG_TEXT}'] = stripslashes(nl2br($message_data['message_text']));
	
	$PARS['{MSG_DATE}'] = formate_date_rus($message_data['message_date']);
	
	$PARS['{CLIENT_ONLINE}'] = $client_online_icon;
	
	$PARS['{BIND_READ_MSG}'] = $bind_over_msg_read;
	
	$PARS['{READ_STATUS}'] = $read_status;
	
	$PARS['{MSG_NOT_READ}'] = $msg_not_read;
	
	return fetch_tpl($PARS, $messages_list_item_from_client_tpl);
}

// Кол-во всех сообщений для клиента
function get_client_msgs_count($client_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "SELECT COUNT(*) as count FROM ".CLIENT_MSGS_TB." WHERE message_to_client_id='$client_id'";
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

?>