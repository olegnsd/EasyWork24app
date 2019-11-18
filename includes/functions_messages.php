<?php
// Страница диалогов пользователя
function fill_messages_dialog()
{
	global $site_db, $current_user_id;
	
	$messages_dialogs_tpl = file_get_contents('templates/messages/messages_dialogs.tpl');
	
	// список диалогов
	$dialog_block = fill_msges_dialogs_list_block();
	
	
	
	$PARS['{DIALOG_BLOCK}'] = $dialog_block;

	
	return fetch_tpl($PARS, $messages_dialogs_tpl);
}

// блок списков диалогов
function fill_msges_dialogs_list_block($new_dialogs=0)
{
	global $site_db, $current_user_id, $user_obj;
	
	$messages_dialogs_no_messages_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/messages_dialogs_no_messages.tpl');
	
	$more_dialogs_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/more_dialogs.tpl');
	
	$dialogs_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/dialogs_block.tpl');
	
	$dialog_obj = new CDialogs($site_db, $current_user_id);
	
	// Диалоги
	$dialog_list = fill_messages_dialog_list(1, $new_dialogs);
	
	if($new_dialogs)
	{
		return $dialog_list;
	}
	
	// Кол-во диалогов
 	$dialog_count = $dialog_obj->get_user_dialogs_count();
	 
	if(!$dialog_list)
	{
		$dialog_list = $messages_dialogs_no_messages_tpl;
	}
	
	// Кол-во страниц
	$pages_count = ceil($dialog_count/DIALOGS_PER_PAGE);
		 
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_btn = $more_dialogs_tpl;
	}
	
	$PARS['{CURRENT_USER_ID}'] = $current_user_id;
	
	$PARS['{DIALOG_LIST}'] = $dialog_list;
	
	$PARS['{NO_DIALOG_MSG_TPL}'] = $messages_dialogs_no_messages_tpl;
	 
	$PARS['{MORE_DIALOG_BTN}'] = $more_btn;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	return fetch_tpl($PARS, $dialogs_block_tpl);
}

//  Список диалогов пользователя
function fill_messages_dialog_list($page=1, $new_dialogs)
{
	global $site_db, $current_user_id, $user_obj;
	
	$dialog_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/dialog_list_item.tpl');
	
	$last_msg_container_from_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/last_msg_container_from_user.tpl');
	
	$last_msg_container_to_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/last_msg_container_to_user.tpl');
	
	$dialog_obj = new CDialogs($site_db, $current_user_id);
 
	$user_dialogs_arr = $dialog_obj->get_user_dialogs(0, 'last_edit_date', 1, $page, $new_dialogs);
  
	// Заполянем объект пользователя
	$user_obj->fill_user_data($current_user_id);
	
	$current_user_surname = $user_obj->get_user_surname();
		
	$current_user_name = $user_obj->get_user_name();
		
	$current_user_middlename = $user_obj->get_user_middlename();
		
	$current_user_position = $user_obj->get_user_position();
	
	$current_user_image = $user_obj->get_user_image();
	
	$current_user_avatar = get_user_preview_avatar_src($current_user_id, $current_user_image);	
	
	// Получаем пользователей диалога
	//$user_dialog_arr = get_msgs_dialog_arr($user_id);
	
	$msgs_list_arr = array();
	
	 //  echo "<pre>", print_r($user_dialogs_arr), "</pre>";
	foreach($user_dialogs_arr as $key => $dialog_data)
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($dialog_data['dialog_user_id']);

		$PARS['{DIALOG_USER_ID}'] = $dialog_data['dialog_user_id'];
		
		$PARS['{DIALOG_SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{DIALOG_NAME}'] = $user_obj->get_user_name();
		
		$PARS['{DIALOG_MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{DIALOG_USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{DIALOG_USER_AVATAR_SRC}'] = get_user_preview_avatar_src($dialog_data['dialog_user_id'], $user_obj->get_user_image());
		
		
		//echo "<pre>", print_r($dialog_data); 
		
		
		//$last_dialog_msg_data = $dialog_obj->get_last_dialog_message_for_user($dialog_data['dialog_id']);
		
		$last_mesage_id = $dialog_data['mesage_id'];
		
		// Данные сообщения 
		$last_dialog_message_data_arr = $dialog_obj->get_dialog_messages_arr($dialog_data['dialog_id'], $dialog_data['dialog_user_id'], 0, $dialog_data['message_id']);
		
		$last_dialog_message_data = current($last_dialog_message_data_arr);
		//  echo "<pre>", print_r($last_dialog_message_data);
				
		//echo $last_dialog_message_data['receiver']['status']; 
		
		$PARS_1 = array();
		
		$dialog_msg_not_read = '';
		
		// Если текущий пользователь является отправителем
		if($last_dialog_message_data['sender']['user_id']==$current_user_id)
		{ 
			$msg_not_read = '';
			 
			// echo $last_dialog_message_data['receiver']['status'];
			// Если сообщение не прочитано получателем
			if($last_dialog_message_data['receiver']['status']==0)
			{
				$msg_not_read = 'msg_my_not_read';
			}
			
			// Заполянем объект пользователя
			$user_obj->fill_user_data($current_user_id);
			
			$PARS_1['{USER_AVATAR_SRC}'] = $current_user_avatar;
			
			$PARS_1['{FROM_USER_ID}'] = $current_user_id;
			
			$PARS_1['{FROM_SURNAME}'] = $current_user_surname;
			
			$PARS_1['{FROM_NAME}'] = $current_user_name;
			
			$PARS_1['{FROM_MIDDLENAME}'] = $current_user_middlename;
			
			$PARS_1['{FROM_USER_POSITION}'] = $current_user_position;
			
			$PARS_1['{MSG_TEXT}'] = cut_msg_dialog_text($last_dialog_message_data['message']['message_text']);
			
			$PARS_1['{MSG_NOT_READ}'] = $msg_not_read;
			
			$msg_text_container = fetch_tpl($PARS_1, $last_msg_container_from_user_tpl);
			
		}
		// Если текущий пользователь является получателем
		else if($last_dialog_message_data['receiver']['user_id']==$current_user_id)
		{
			$dialog_msg_not_read = '';
			// Если сообщение не прочитано получателем
			if($last_dialog_message_data['receiver']['status'] == 0)
			{ 
				$dialog_msg_not_read = 'dialog_msg_not_read';
			}
			
			$PARS_1['{MSG_TEXT}'] = cut_msg_dialog_text($last_dialog_message_data['message']['message_text']);
			
			$msg_text_container = fetch_tpl($PARS_1, $last_msg_container_to_user_tpl);
		}
		
		$PARS['{DIALOG_MSG_TEXT_CONTAINER}'] = $msg_text_container;
		
		$PARS['{DIALOG_MSG_NOT_READ}'] = $dialog_msg_not_read;
		
		$PARS['{DIALOG_LAST_DATE}'] = datetime($last_dialog_message_data['message']['message_date'], '%j %M в %H:%i', 1);
		
		if($new_dialogs)
		{
			$msgs_list_arr[$dialog_data['dialog_user_id']] = iconv('cp1251', 'utf-8', fetch_tpl($PARS, $dialog_list_item_tpl));
		}
		else
		{
			$msgs_list .= fetch_tpl($PARS, $dialog_list_item_tpl);
		}
		 
	}
	
	if($new_dialogs)
	{
		//$msgs_list = array_reverse($msgs_list,1);
		return $msgs_list_arr;
	}
	else
	{
		return $msgs_list;	
	}
	 	
}

// Диалог
function fill_dialog($user_id, $show_for_message_id, $show_last_date_read=0)
{
	global $site_db, $current_user_id, $user_obj;
	
	$messages_list_tpl = file_get_contents('templates/messages/messages_list.tpl');
	//$show_for_message_id = 189;
	// Контент диалога
	$dialog_content = fill_dialog_content($user_id, $show_for_message_id, $show_last_date_read);
	
	if($_GET['mid'])
	{
		$to_message_id = value_proc($_GET['mid']);
	}
	
	$PARS['{TO_USER_ID}'] = $user_id;
	
	$PARS['{DIALOG_CONTENT}'] = $dialog_content;
	
	$PARS['{TO_MESSAGE_ID}'] = $to_message_id;
	
	return fetch_tpl($PARS, $messages_list_tpl);
}
// Контент диалога
function fill_dialog_content($user_id, $show_for_message_id, $show_last_date_read)
{
	global $site_db, $current_user_id, $user_obj;
	 
	// Проверка на доступность контента
	//if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1)) || $user_id==$current_user_id)
	
	if($user_id==$current_user_id)
	{
		header('Location: /msgs');
	}

	// Очистка массива удаленных контактов
	if($_SESSION['deleted_messages_ids'])
	{
	 	$_SESSION['deleted_messages_ids'] = '';
	}
	
	$messages_list_dialog_content_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/messages_list_dialog_content.tpl');
	
	$prev_msgs_link_prev = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/prev_msgs_link.tpl');
	
	$next_msgs_link_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/next_msgs_link.tpl');
	
	$dialog_obj = new CDialogs($site_db, $current_user_id);
	
	// получаем ID диалога
	$dialog_id = $dialog_obj->get_users_dialog(array($current_user_id, $user_id), 1);
	$dialog_obj->set_dialog_id($dialog_id);
	
	// Кол-во сообщений
	$msgs_count = $dialog_obj->get_dialog_messages_count();
	 
	// ID последнего сообщения в диалоге
	$message_last_id = $dialog_obj->get_dialog_last_message_id();
	
	// Последнее прочитанное сообщение в диалоге пользователем, отличного от текущего
	$last_read_msg_date = $dialog_obj->get_last_read_dialog_message($user_id);
	 
	// Новые сообщения диалога
	$dialog_new_messages_ids_arr =  $dialog_obj->get_dialog_new_messages_ids_arr($dialog_id);
	 //print_r($dialog_new_messages_ids_arr);
	// Кол-во страниц 
	$msgs_count_page = ceil($msgs_count/MSG_PER_PAGE);
	
	// По умолчанию устанавливаем текущую страницу
	$current_msg_page = 1;
	
	// Если необходимо вывести диалог, в котором будет присутствовать необходимое сообщение
	if($show_for_message_id)
	{
		$msgs_before_count = $dialog_obj->get_dialog_messages_count($show_for_message_id);
		// Кол-во страниц 
		$msgs_before_count_page = ceil($msgs_before_count/MSG_PER_PAGE);
		
		// Кнопка "следующие сообщения"
		if($msgs_before_count_page > 1)
		{
			$next_msgs_link = $next_msgs_link_tpl;
		}
		
		// Делаем текущей страницей страницу, на котрой присутствует необходимое сообщение 
		$current_msg_page = $msgs_before_count_page;
	} 
	// Выводим кнопку Предыдущие сообщения
	if($msgs_count_page > 1 && $current_msg_page<$msgs_count_page)
	{
		$more_msgs_link = $prev_msgs_link_prev;
	}
	
	$_SESSION['message_last_id'] = $message_last_id;
	 
	$_SESSION['message_last_update_message_id'] = $message_last_id;
	
	// Список сообщений 
	$msgs_list = fill_dialog_messages_list($dialog_id, $user_id, $current_msg_page);

	if($show_last_date_read)
	{
		// Блок последнего прочитанного сообщения
 		$last_msg_read_date = fill_last_message_read_date($last_read_msg_date);
	}
	   
	
	$PARS['{MSGS_LIST}'] = $msgs_list;
	
	$PARS['{MORE_MSGS_LINK}'] = $more_msgs_link;
	
	$PARS['{NEXT_MSGS_LINK}'] = $next_msgs_link;
	
	$PARS['{LAST_MSG_READ_DATE}'] = $last_msg_read_date;
	
	$PARS['{DIALOG_NEW_MESSAGES}'] = json_encode($dialog_new_messages_ids_arr);
	
	$PARS['{CURRENT_MSG_PAGE}'] = $current_msg_page;
	
	$PARS['{PAGES_COUNT}'] = $msgs_count_page;
	
	return fetch_tpl($PARS, $messages_list_dialog_content_tpl);
}


// Список сообщений диалога
function fill_dialog_messages_list($dialog_id, $dialog_user_id, $page = 1, $message_id = 0, $new = 0)
{
	global $site_db, $current_user_id, $user_obj;
	
	$messages_list_item_receiver_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/messages_list_item_receiver_user.tpl');
	
	$messages_list_item_sender_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/messages_list_item_sender_user.tpl');
	
	$messages_list_item_deleted_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/messages_list_item_deleted.tpl');
	
	$messages_bind_over_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/messages_bind_over.tpl');
	
	$no_messages_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/no_messages.tpl');
	
	$msg_read_date_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/msg_read_date.tpl');
	
	$msg_theme_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/msg_theme.tpl');

	$dialog_obj = new CDialogs($site_db, $current_user_id);
	
	$dialog_obj->set_dialog_id($dialog_id);
	 
	// Массив сообщений в диалоге
	$dialog_messages_arr = $dialog_obj->get_dialog_messages_arr($dialog_id, $dialog_user_id, $page, $message_id, $new);
	
	// Заполянем объект текущего пользователя
	$user_obj->fill_user_data($current_user_id);

	$user_surname[$current_user_id] = $user_obj->get_user_surname();
		
	$user_name[$current_user_id] = $user_obj->get_user_name();
		
	$user_middlename[$current_user_id] = $user_obj->get_user_middlename();
		
	$user_position[$current_user_id] = $user_obj->get_user_position();
	
	$user_image[$current_user_id] = $user_obj->get_user_image();
	// онлайн пользователь?
	$user_is_online[$current_user_id] = user_in_online_icon($current_user_id);
	
	
	
	// Заполянем объект пользователя по переписке
	$user_obj->fill_user_data($dialog_user_id);
	
	$user_surname[$dialog_user_id] = $user_obj->get_user_surname();
		
	$user_name[$dialog_user_id] = $user_obj->get_user_name();
		
	$user_middlename[$dialog_user_id] = $user_obj->get_user_middlename();
		
	$user_position[$dialog_user_id] = $user_obj->get_user_position();
	
	$user_image[$dialog_user_id] = $user_obj->get_user_image();
	// онлайн пользователь?
	$user_is_online[$dialog_user_id] = user_in_online_icon($dialog_user_id);
	 // echo "<pre>", print_r($dialog_messages_arr);
	foreach($dialog_messages_arr as $message_id => $message_data)
	{
		$bind_over_msg_read = '';
		 

		// Текущий пользователь ОТПРАВИТЕЛЬ сообщения
		if($message_data['message']['user_id'] == $current_user_id)
		{  
			$msg_item_tpl = $messages_list_item_sender_user_tpl;
			$select_data_user_id = $current_user_id;
		}
		//  Текущий пользователь ПОЛУЧАТЕЛЬ сообщения
		else if($message_data['message']['user_id'] == $dialog_user_id)
		{ 
			$msg_item_tpl = $messages_list_item_receiver_user_tpl;
			$select_data_user_id = $dialog_user_id; 
		}
		 
		 
		// Событие 
		$bind_over_msg_read = ''; 
		if($message_data['receiver']['user_id']==$current_user_id && $message_data['receiver']['status']==0)
		{   
			$msg_not_read = 'msg_not_read';
			$PARS1['{MESSAGE_ID}'] = $message_data['message']['message_id'];
			$PARS1['{FROM_USER_ID}'] = $message_data['sender']['user_id'];
			$PARS1['{TO_USER_ID}'] = $message_data['receiver']['user_id'];
			$bind_over_msg_read = fetch_tpl($PARS1, $messages_bind_over_tpl);
		}
		
		// Подсвечиваем непрочитанные сообщения
		$msg_not_read = '';
		$read_status = 0;
		if($message_data['sender']['status']==0 || $message_data['receiver']['status']==0)
		{
			$msg_not_read = 'msg_not_read';
		}
		else
		{
			$read_status = 1;
		}
	 	
			 
		// $message_data['sender']['user_id']==$current_user_id && 
		$read_status = $row['message_to_user_noticed'] == 0 ? 0 : 1;
		
		// Превью аватарки пользователя
		$user_avatar_src = get_user_preview_avatar_src($select_data_user_id, $user_image[$select_data_user_id]);
		
		// Иконка пользователя онлайн
		$user_online = $user_is_online[$select_data_user_id];
		
		// Дата прочтения сообщений
		if($message_data['receiver']['status']>0)
		{
			$PARS_2['{READ_DATE}'] = datetime($message_data['receiver']['read_date'], '%d.%m.%y в %H:%i');
			
			$msg_read_date = fetch_tpl($PARS_2, $msg_read_date_tpl); 
		}
		
		$msg_theme = ''; 
		if($message_data['message']['message_theme'])
		{  
			$PARS_2['{THEME}'] = $message_data['message']['message_theme'];
			$msg_theme = fetch_tpl($PARS_2, $msg_theme_tpl);
		}
			
		// Список файлов для отчета
		$files_list = get_attached_files_to_content($message_data['message']['message_id'], 3);
	
		$PARS['{FILES_LIST}'] = $files_list;
	
		$PARS['{USER_ID}'] = $select_data_user_id;
		
		$PARS['{SURNAME}'] = $user_surname[$select_data_user_id];
			
		$PARS['{NAME}'] = $user_name[$select_data_user_id];
			
		$PARS['{MIDDLENAME}'] = $user_middlename[$select_data_user_id];
			
		$PARS['{USER_POSITION}'] = $user_position[$select_data_user_id];
		
		$PARS['{USER_ONLINE}'] = $user_online;
		
		$PARS['{USER_AVATAR_SRC}'] = $user_avatar_src;
		
		$PARS['{MESSAGE_ID}'] = $message_data['message']['message_id'];
		
		$PARS['{MSG_TEXT}'] = str_to_a(stripslashes(nl2br($message_data['message']['message_text'])));
		
		$PARS['{MSG_DATE}'] = datetime($message_data['message']['message_date'], '%d.%m.%y в %H:%i', 1); 
		
		$PARS['{BIND_READ_MSG}'] = $bind_over_msg_read;
		
		$PARS['{READ_STATUS}'] = $read_status;
		
		$PARS['{MSG_NOT_READ}'] = $msg_not_read;
		 
	 	$PARS['{MSG_READ_DATE}'] = $msg_read_date;
		
		$PARS['{MSG_THEME}'] = $msg_theme;
		
		$msgs_list = fetch_tpl($PARS, $msg_item_tpl).$msgs_list;
	}
	
	if(!$msgs_list && !$new)
	{
		$msgs_list = $no_messages_tpl;
	}
	
	return $msgs_list;
}


function fill_search_dialog_messages($user_id, $page, $search_words, $date_from, $date_to)
{
	global $site_db, $current_user_id, $user_obj;
	
	$dialog_searched_message_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/dialog_searched_message_item.tpl');
	
	$dialog_searched_no_items_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/dialog_searched_no_items.tpl');
		
	$prev_search_dialog_messages_link_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/prev_search_dialog_messages_link.tpl');
		
	$dialog_obj = new CDialogs($site_db, $current_user_id);
	// получаем ID диалога
	$dialog_id = $dialog_obj->get_users_dialog(array($current_user_id, $user_id), 1);
	$dialog_obj->set_dialog_id($dialog_id);
	 
	// Массив сообщений в диалоге
	$dialog_messages_arr = $dialog_obj->get_dialog_search_messages_arr($dialog_id, $search_words, $date_from, $date_to, $page);
	
	
	foreach($dialog_messages_arr  as $message_id => $message_data)
	{
		$user_id = $message_data['user_id'];
		
		if(!$users_data[$user_id])
		{
			// Заполянем объект текущего пользователя
			$user_obj->fill_user_data($message_data['user_id']);
		
			$users_data[$user_id]['surname'] =  $user_obj->get_user_surname();
				
			$users_data[$user_id]['name'] = $user_obj->get_user_name();
				
			$users_data[$user_id]['middlename'] = $user_obj->get_user_middlename();
			
			$users_data[$user_id]['avatar'] = get_user_preview_avatar_src($user_id, $user_obj->get_user_image()); 
		 
		}
		
		$msg_theme_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/msg_theme.tpl');
		
		$msg_theme = ''; 
		if($message_data['message_theme'])
		{  
			$PARS_2['{THEME}'] = light_words($message_data['message_theme'], $search_words);
			$msg_theme = fetch_tpl($PARS_2, $msg_theme_tpl);
		}
		
		$PARS['{USER_ID}'] = $user_id;

		$PARS['{SURNAME}'] = $users_data[$user_id]['surname'];
		
		$PARS['{NAME}'] = $users_data[$user_id]['name'];
		
		$PARS['{MIDDLENAME}'] = $users_data[$user_id]['middlename'];

		$PARS['{USER_AVATAR_SRC}'] = $users_data[$user_id]['avatar'];
		
		$PARS['{MESSAGE_ID}'] = $message_id;
		 	
		$PARS['{MSG_TEXT}'] = nl2br(light_words($message_data['message_text'], $search_words)); 
		
		$PARS['{MSG_THEME}'] = $msg_theme;
		
		$PARS['{MSG_DATE}'] = datetime($message_data['message_date'], '%d.%m.%y в %H:%i', 1); 
	
		$messages_list = fetch_tpl($PARS, $dialog_searched_message_item_tpl).$messages_list;
	}
	
	if(!$messages_list)
	{
		 //$messages_list  = $dialog_searched_no_items_tpl;
	}
	
	
	
	return $messages_list;
}

// Блок последнего прочитанного письма
function fill_last_message_read_date($date)
{
	global $site_db, $current_user_id, $user_obj;
	
	$last_msg_read_date_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/last_msg_read_date.tpl');
	 
	if(!$date)
	{
		return '';
	}
	
	$PARS['{DATE_READ}'] = datetime($date, '%d.%m.%y в %H:%i');
	
	return fetch_tpl($PARS, $last_msg_read_date_tpl);	
}

// Кол-во новых сообщений
function get_new_user_messages_count($user_id, $user_dialogs_arr)
{
	global $site_db, $current_user_id, $user_obj, $users_for_access_to_content;
	
	$dialog_obj = new CDialogs($site_db, $current_user_id);
	
	$count = $dialog_obj->get_new_messages_count_for_user($user_dialogs_arr);
 
	return  $count;
}

function search_users_for_dialog($search_word)
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	$dialog_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/dialog_user.tpl');
	
	// Сливаем в единый массив временных и постоянных подчиненных
	$users_arr = get_current_user_users_arrs(array(1,1,1,1,1));
	
	$user_data = $current_user_obj->get_user_data();
	
	// Находим пользователей для диалога
	$sql = "SELECT * FROM ".USERS_TB." WHERE (user_surname LIKE '$search_word%' OR user_name LIKE '$search_word%') AND user_id != '$current_user_id' AND is_fired=0 LIMIT 30"; 
	
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row['user_id']);
		
		// онлайн пользователь?
		$user_is_online = user_in_online_icon($row['user_id'], $row['user_last_visit_date']);
		
		$PARS['{USER_ID}'] = $row['user_id'];
		
		$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{NAME}'] = $user_obj->get_user_name();
		
		$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{USER_AVATAR_SRC}'] = get_user_preview_avatar_src($row['user_id'], $user_obj->get_user_image());
		
		$PARS['{ONLINE}'] = $user_is_online;
		
		 
	
		$users_list .= fetch_tpl($PARS, $dialog_user_tpl); 
		
	}
	
	//$users_list = $search_in_messages_btn_tpl;
	
	return $users_list;
}

// Обрезка текста для вывода в диалоге
function cut_msg_dialog_text($text)
{
	
	$text = stripslashes($text); 
		
	if(strlen($text)>50)
	{
		if(strpos($text, ' ', 50)>0)
		{
			$new_text = substr($text, 0, strpos($text, ' ', 50)).'..';
		}
		else
		{
			$new_text = substr($text, 0, 50).'..';
		}
		 
	}
	else
	{
		$new_text = $text;
	}
	return $new_text;
}





// Создать группу сообщений
function fill_add_group_messages()
{
	global $site_db, $current_user_id, $user_obj, $_CURRENT_USER_WORKERS_ARR;
	
	$main_tpl = file_get_contents('templates/messages/add_messages_group.tpl');
	
	$select_user_for_add_to_msg_group_tpl = file_get_contents('templates/messages/select_user_for_add_to_msg_group.tpl');
	 
	foreach($_CURRENT_USER_WORKERS_ARR as $user_id)
	{
		// Заполянем объект текущего пользователя
		$user_obj->fill_user_data($user_id);
		
		$PARS['{USER_ID}'] = $user_id;
		
		$PARS['{SURNAME}'] = $user_obj->get_user_surname();
			
		$PARS['{NAME}'] = $user_obj->get_user_name();
			
		$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
			
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$workers_for_select_list .= fetch_tpl($PARS, $select_user_for_add_to_msg_group_tpl);
	}
	
	$PARS['{USER_ID}'] = $current_user_id;
	
	$PARS['{USER_WORKERS_LIST}'] = $workers_for_select_list;
	
	return fetch_tpl($PARS, $main_tpl);
	
}

// Группа сообщений
function fill_group_messages($group_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$main_tpl = file_get_contents('templates/messages/messages_group.tpl');
	
	$messages_group_not_access_tpl = file_get_contents('templates/messages/messages_group_not_access.tpl');
	
	$messages_group_user_item_tpl = file_get_contents('templates/messages/messages_group_user_item.tpl');
	
	$more_msgs_link_tpl = file_get_contents('templates/messages/more_msgs_group_link.tpl');
	
	$close_planning_session_btn_tpl = file_get_contents('templates/messages/close_planning_session_btn.tpl');
	
	$session_planning_closed_tpl = file_get_contents('templates/messages/session_planning_closed.tpl');
	
	$add_user_to_group_form_tpl = file_get_contents('templates/messages/add_user_to_group_form.tpl');
	
	
	// Проверка, на наличие созданной группы для сообщений
	$sql = "SELECT * FROM ".MSGS_GROUPS_TB." WHERE group_id='$group_id'";
	
	$msgs_group_data = $site_db->query_firstrow($sql);
	
	if(!$msgs_group_data['group_id'] || !$group_id)
	{
		header('Location: /msgs');
	}
	
	// Выбор пользователей группы сообщений
	$sql = "SELECT * FROM ".MSGS_GROUPS_USERS_TB." WHERE group_id='$group_id'";
	
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{
		$messages_group_users_arr[] = $row['user_id'];
	}
	
	// Планерка не доступна пользователю
	if(!in_array($current_user_id, $messages_group_users_arr))
	{
		return $messages_group_not_access_tpl;
	}
	
	
	
	// Организатору планерки выводим кнопку завершения планерки
	if($current_user_id==$msgs_group_data['user_id'] && $msgs_group_data['group_is_online'])
	{
		$close_planning_session_btn = $close_planning_session_btn_tpl;
	}
	
	// Если планерка закрыта
	if(!$msgs_group_data['group_is_online'])
	{
		$session_planning_closed = $session_planning_closed_tpl;
	}
	
	foreach($messages_group_users_arr as $user)
	{
		// Заполянем объект текущего пользователя
		$user_obj->fill_user_data($user);
		
		// онлайн пользователь?
		$user_is_online = user_in_online_icon($user);
		
		$PARS['{USER_ID}'] = $user;
		
		$PARS['{SURNAME}'] = $user_obj->get_user_surname();
			
		$PARS['{NAME}'] = $user_obj->get_user_name();
			
		$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
			
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{ONLINE}'] = $user_is_online;
		
		// Руководитель планерки
		if($user == $msgs_group_data['user_id'])
		{
			$planning_session_head_user = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
		}
		
		$messages_group_users .= fetch_tpl($PARS, $messages_group_user_item_tpl);
	}
	
	// Выбираем id последнего сообщения в базе
	$sql = "SELECT message_id FROM ".MSGS_GROUPS_MSGS_TB." ORDER by message_id DESC LIMIT 1";
	$row = $site_db->query_firstrow($sql);
	$message_last_id = $row['message_id'];
	$_SESSION['message_group_last_id'] = $message_last_id;
	$_SESSION['message_last_update_group_message_id'] = $message_last_id;
	
	
	// Кол-во сообщений
	$msgs_count = get_messages_group_count($group_id);
	 
	// Кол-во страниц 
	$msgs_count_page = ceil($msgs_count/MSG_PER_PAGE);
	if($msgs_count_page > 1)
	{
		$more_msgs_link = $more_msgs_link_tpl;
	}
	
	// Если текущийпользователь создатель планерки
	//if(check_msgs_group_id_owner($group_id, $current_user_id))
	//{
		$PARS['{GROUP_ID}'] = $group_id;
		$add_user_to_group_form = fetch_tpl($PARS, $add_user_to_group_form_tpl);
	//}
	
	// Список сообщений 
	$msgs_list = fill_messages_group_list($group_id);
	
	//print_r($messages_group_users_arr);
	
	$PARS['{GROUP_ID}'] = $group_id;
	
	$PARS['{ADD_USER_FORM}'] = $add_user_to_group_form;
	
	$PARS['{MESSAGES_GROUP_USERS}'] = $messages_group_users;
	
	$PARS['{USER_WORKERS_LIST}'] = $workers_for_select_list;
	
	$PARS['{PLANNING_SESSION_HEAD_USER}'] = $planning_session_head_user;
	
	$PARS['{MORE_MSGS_LINK}'] = $more_msgs_link;
	
	$PARS['{MSGS_LIST}'] = $msgs_list;
	
	$PARS['{PAGES_COUNT}'] = $msgs_count_page;
	
	$PARS['{CLOSE_PLANNING_SESSION}'] = $close_planning_session_btn;
	
	$PARS['{SESSION_PLANNING_CLOSED}'] = $session_planning_closed;
	
	return fetch_tpl($PARS, $main_tpl);
}

// Кол-во сообщений в группе сообщений
function get_messages_group_count($group_id)
{
	global $site_db, $current_user_id;
	
	// Выбор сообщений группы
	$sql = "SELECT COUNT(*) as count FROM ".MSGS_GROUPS_MSGS_TB." WHERE group_id='$group_id'"; 
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}
// Список сообщений в группе
function fill_messages_group_list($group_id, $page=1, $message_id=0, $msg_updated=1)
{
	global $site_db, $current_user_id, $user_obj;
	
	$messages_group_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/messages_group_item.tpl');
	
	$no_messages_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/no_messages.tpl');

	$page = !$page ? 1 : $page;
		
	
	$begin_pos = MSG_PER_PAGE * ($page - 1);
	
	$limit = " LIMIT $begin_pos, ".MSG_PER_PAGE;
	
	 
	// При подгрузке большего количества сообщений, не выводим те, которые добавлялись в открытом окне диалога
	if($page>1)
	{
		if($_SESSION['message_group_last_id'])
		{
			$and_not_messages = " AND message_id <= '".$_SESSION['message_group_last_id']."'";
		}
	}

	
	// Если возвратить только 1 сообщение по ID
	if($message_id)
	{
		$sql = "SELECT * FROM ".MSGS_GROUPS_MSGS_TB." WHERE group_id='$group_id' AND message_id='$message_id'";
	}
	else
	{
		// Выбор сообщений группы
		$sql = "SELECT * FROM ".MSGS_GROUPS_MSGS_TB." 
				WHERE group_id='$group_id' $and_not_messages ORDER by message_id DESC $limit"; 
	}
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{
		$from_user_id = $row['user_id'];
		
		 
	 	if(!$user_arr_id[$from_user_id])
		{
			$user_arr_id[$from_user_id] = $from_user_id;
			
			// Заполянем объект текущего пользователя
			$user_obj->fill_user_data($from_user_id);
	
			$user_surname[$from_user_id] = $user_obj->get_user_surname();
		
			$user_name[$from_user_id] = $user_obj->get_user_name();
				
			$user_middlename[$from_user_id] = $user_obj->get_user_middlename();
				
			$user_position[$from_user_id] = $user_obj->get_user_position();
			
			$user_image[$from_user_id] = $user_obj->get_user_image();
			
			$user_is_online[$from_user_id] = user_in_online_icon($from_user_id);
			
			$user_image[$from_user_id] = $user_obj->get_user_image();
			
		}
		
		// Превью аватарки пользователя
		$user_avatar_src = get_user_preview_avatar_src($from_user_id, $user_image[$from_user_id]);
		
		// Иконка пользователя онлайн
		$user_online = $user_is_online[$from_user_id];
		
		// Дата прочтения сообщений
		if($row['message_to_user_noticed'])
		{
			$PARS_2['{READ_DATE}'] = datetime($row['message_read_date'], '%d.%m.%y в %H:%i');
			
			$msg_read_date = fetch_tpl($PARS_2, $msg_read_date_tpl); 
		}
		
		
		// Список файлов для отчета
		$files_list = get_attached_files_to_content($row['message_id'], 4);
	
		$PARS['{FILES_LIST}'] = $files_list;
		
		
		$PARS['{USER_ONLINE}'] = $user_online;
		
		$PARS['{USER_AVATAR_SRC}'] = $user_avatar_src;
		
		$PARS['{USER_ID}'] = $from_user_id;
		
		$PARS['{MESSAGE_ID}'] = $row['message_id'];
		
		$PARS['{FROM_USER_ID}'] = $row['user_id'];
		
	 	$PARS['{SURNAME}'] = $user_surname[$from_user_id];
			
		$PARS['{NAME}'] = $user_name[$from_user_id];
			
		$PARS['{MIDDLENAME}'] = $user_middlename[$from_user_id];
			
		$PARS['{USER_POSITION}'] = $user_position[$from_user_id];
			
		$PARS['{MSG_TEXT}'] = stripslashes(nl2br($row['message_text']));
		
		$PARS['{MSG_DATE}'] = datetime($row['message_date'], '%d.%m.%y в %H:%i');
		
		$msgs_list = fetch_tpl($PARS, $messages_group_item_tpl).$msgs_list;
	}
	
	if(!$msgs_list)
	{
		$msgs_list = $no_messages_tpl;
	}
	
	return $msgs_list;
}

// Выбирает создателя группы
function get_msgs_group_id_owner($group_id)
{
	global $site_db;
	
	// Все пользователи диалога
	$sql = "SELECT * FROM tasks_messages_groups_users WHERE group_id='$group_id'";
		
	$row = $site_db->query_firstrow($sql);
	
	return $row['user_id'];
}

// Выбирает создателя группы
function check_msgs_group_id_owner($group_id, $user_id)
{
	global $site_db;
	
	// Все пользователи диалога
	$sql = "SELECT * FROM tasks_messages_groups WHERE group_id='$group_id' AND user_id='$user_id'";
		
	$row = $site_db->query_firstrow($sql);
	 
	if($row['user_id'])
	{
		return true;
	}
	else return false;
}
// Проверка на возможность общения внутри человека
function check_for_send_msg($to_user_id)
{
	global $site_db, $current_user_id, $current_user_obj;
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/classes/class.Dialogs.php';
	
	
	 
	if($dialog_id > 0)
	{
		return true;
	}
	// полный доступ
	else if($current_user_obj->get_user_limitation()==0)
	{
		return true;
	}
	// общение со своим сотрудником
	else if(check_user_access_to_user_content($to_user_id, array(0,1,0,0,1,1)))
	{
		return true;
	}
	// общение только с руководством
	else if($current_user_obj->get_user_limitation()==1 && check_user_access_to_user_content($to_user_id, array(1,0,0,1,0)))
	{
		return true;
	}
	// общение внутри отдела
	else if($current_user_obj->get_user_limitation()==2 && check_user_access_to_user_content($to_user_id, array(1,1,1,1,1)))
	{
		return true;
	}
	// установлены ограничения на общение, но можно дать ответ любому, кто написал ему
	else if(in_array($current_user_obj->get_user_limitation(), array(1,2)))
	{
		$dialog_obj = new CDialogs($site_db, $current_user_id);
	
		// получаем ID диалога
		$dialog_id = $dialog_obj->get_users_dialog(array($current_user_id, $to_user_id));
		
		
		$dialog_obj->set_dialog_id($dialog_id);
		$last_id = $dialog_obj->get_dialog_last_message_id();
		
		if($last_id > 0)
		{
			return true;
		}
		else return false;
	}
	
	return false;
	
}

// Поиск по сообщениям
function search_in_messages($page, $search_words, $date_from, $date_to)
{
	global $site_db, $current_user_id, $current_user_obj, $user_obj;
	
	$messages_search_content_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/messages_search_content.tpl');
	
	$prev_search_msgs_link_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/prev_search_dialogs_msgs_link.tpl');
	
	$no_messages_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/no_messages_search.tpl');
	 
	// список сообщений по ключевым словам
	$msgs_list = get_search_messages_list($page, $search_words, $date_from, $date_to);
	
	if(!$msgs_list)
	{
		$msgs_list = $no_messages_tpl;
	}
	else
	{
		$more_msgs_link = $prev_search_msgs_link_tpl;
	}
	
	$PARS['{MSGS_LIST}'] = $msgs_list;
	
	$PARS['{MORE_MSGS_LINK}'] = $more_msgs_link;
	
	return fetch_tpl($PARS, $messages_search_content_tpl); 

}

// список сообщений найденных по ключевым словам
function get_search_messages_list($page, $search_words, $date_from, $date_to)
{
	global $site_db, $current_user_id, $current_user_obj, $user_obj;
	
	$message_searched_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/message_searched_item.tpl');
	
	$msg_theme_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/msg_theme.tpl');
	
	$msg_for_str_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/msg_for_str.tpl');
	
	$page = !$page ? 1 : $page;
	
	if($date_from && date_rus_validate($date_from))
	{
		$date_from = to_mktime(formate_to_norm_date($date_from));
	}
	else
	{
		$date_from = '';
	}
	
	if($date_to && date_rus_validate($date_to))
	{
		$date_to = to_mktime(formate_to_norm_date($date_to));
	}
	else
	{
		$date_to = '';
	}
	
	if($date_to)
	{
		$date_to += 3600*24-1;
	}
	
	if($date_from && $date_to && $date_from != $date_to && $date_to >= $date_from)
	{
		$and_date = " AND i.message_date >= '$date_from' AND i.message_date <='$date_to'";
	}
	else if($date_from && !$date_to)
	{
		$and_date = " AND i.message_date >= '$date_from'";
	}
	else if(!$date_from && $date_to)
	{
		$and_date = " AND i.message_date <= '$date_to'";
	}
	
	$begin_pos = MSG_PER_PAGE * ($page - 1);
	
	$limit = " LIMIT $begin_pos, ".MSG_PER_PAGE;
	
		
	$sql = "SELECT DISTINCT(i.message_id), i.* FROM tasks_dialogs_messages i
			LEFT JOIN tasks_dialogs_message_to_user j ON i.message_id=j.message_id
			WHERE (i.message_text LIKE '%$search_words%' OR i.message_theme LIKE '%$search_words%') AND j.user_id='$current_user_id' AND j.status!=2   			$and_date ORDER by i.message_date DESC $limit";
	 
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{
		// выбор пользователя, с кем идет диалог
		$sql = "SELECT * FROM tasks_dialogs_users WHERE user_id!='$current_user_id' AND dialog_id='".$row['dialog_id']."'";
		
		$dialog_user_row = $site_db->query_firstrow($sql);
		
		// с кем идет диалог
		$dialog_user_id = $dialog_user_row['user_id'];
		
		
		$user_id = $row['user_id'];
		 
		$msg_from_str = '';
		
		if($row['user_id']==$current_user_id)
		{
			$msg_from_str = $msg_for_str_tpl;
			 
		}
		
		if(!$users_data[$dialog_user_id])
		{
			// Заполянем объект текущего пользователя
			$user_obj->fill_user_data($dialog_user_id);
		
			$users_data[$dialog_user_id]['surname'] =  $user_obj->get_user_surname();
				
			$users_data[$dialog_user_id]['name'] = $user_obj->get_user_name();
				
			$users_data[$dialog_user_id]['middlename'] = $user_obj->get_user_middlename();
			
			$users_data[$dialog_user_id]['avatar'] = get_user_preview_avatar_src($dialog_user_id, $user_obj->get_user_image()); 
		 
		}
		
		$msg_theme = ''; 
		if($row['message_theme'])
		{  
			$PARS_2['{THEME}'] = light_words($row['message_theme'], $search_words);
			$msg_theme = fetch_tpl($PARS_2, $msg_theme_tpl);
		}
		
		 
		
		$PARS['{USER_ID}'] = $user_id;

		$PARS['{SURNAME}'] = $users_data[$dialog_user_id]['surname'];
		
		$PARS['{NAME}'] = $users_data[$dialog_user_id]['name'];
		
		$PARS['{MIDDLENAME}'] = $users_data[$dialog_user_id]['middlename'];

		$PARS['{USER_AVATAR_SRC}'] = $users_data[$dialog_user_id]['avatar'];
		
		$PARS['{DIALOG_USER_ID}'] = $dialog_user_id;
		
		$PARS['{MESSAGE_ID}'] = $row['message_id'];
		$PARS['{USER_ONLINE}'] = $user_online;
		$PARS['{MSG_TEXT}'] = nl2br(light_words($row['message_text'], $search_words));
		$PARS['{MSG_THEME}'] = $msg_theme;
		$PARS['{MSG_DATE}'] = datetime($row['message_date'], '%d.%m.%y в %H:%i', 1); 
		$PARS['{MSG_FROM_STR}'] = $msg_from_str;
		
		$msgs_list = fetch_tpl($PARS, $message_searched_item_tpl).$msgs_list; 
	}
	
	return $msgs_list;
}
function send_sms_by_sms($to_user_id, $from_user_id, $msg_text)
{
	global $site_db, $current_user_id, $current_user_obj, $user_obj;
	
	// Заполянем объект пользователя, кому написали сообщение
	$user_obj->fill_user_data($to_user_id);
	
	$user_phone = $user_obj->get_user_phone();
	
	### sms body
	$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/new_message.tpl');
	
	$sms_text = $msg_text;
	
	$sms_text = strlen($sms_text)>50 ? substr($sms_text,0,50).'...' : $sms_text;
	
	// Заполянем объект пользователя, кто принял задание
	$user_obj->fill_user_data($from_user_id);
	
	$surname = 	$user_obj->get_user_surname();
	
	$name = 	$user_obj->get_user_name();
	
	$middlename = 	$user_obj->get_user_middlename();
	
	$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{USER_NAME}'] = $name[0].'.';
	
	$PARS['{USER_MIDDLENAME}'] = $middlename[0].'.';
	
	$PARS['{MSG_TEXT}'] = $sms_text;
	 
	$sms_text = fetch_tpl($PARS, $sms_tpl);
	###\ sms body
	
	// Отправка смс сообщения
	send_sms_msg($user_phone, $sms_text);
}

// Уведомление планерки
function fill_planning_session_notice()
{
	global $site_db, $current_user_id, $user_obj;
	
	$planning_session_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/planning_session_block.tpl');
	$actual_planning_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/actual_planning_list.tpl');
	$history_planning_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/history_planning_list.tpl');
	$planning_sep_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/planning_sep.tpl');
	$planning_add_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/planning_add_btn.tpl');
	$no_ps_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/no_ps.tpl');
	$add_ps_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/add_ps_btn.tpl');
	
	
	// Выбор АКТУАЛЬНЫХ планерок
	$sql = "SELECT i.group_id, i.user_id as group_boss_id, i.date_add,i.group_desc FROM ".MSGS_GROUPS_TB." i
			LEFT JOIN ".MSGS_GROUPS_USERS_TB." j ON i.group_id=j.group_id
			WHERE i.group_is_online = 1 AND j.user_id='$current_user_id' ORDER by i.group_id DESC";
	 
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{		 
		$actual_list .= fill_planning_session_item($row);
	
	}
	
	// актуальные планерки
	if($actual_list)
	{
		$PARS['{SESSION_PLANNINGS_LIST}'] = $actual_list;
		
		$actual_plannings = fetch_tpl($PARS, $actual_planning_list_tpl);
	}
	
	// блок прошедших планерок
	$history_plannings = fetch_tpl($PARS, $history_planning_list_tpl);
	
	
	if(get_current_user_users_arrs(array(0,1,0,0,1)))
	{
		$add_btn = $add_ps_btn_tpl;
	}
	
	$PARS['{ACTUAL_PLANNINGS_LIST}'] = $actual_plannings;
	$PARS['{HISTORY_PLANNINGS_LIST}'] = $history_plannings;
	$PARS['{SEP}'] = $sep;
	$PARS['{ADD_BTN}'] = $add_btn;
	$PARS['{PS_PER_PAGE}'] = PS_PER_PAGE; 
	 
	
	return fetch_tpl($PARS, $planning_session_block_tpl);
}

// кол-во планерок в истории
function get_ps_history_count($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	// Выбор планерок ИСТОРИИ 10 последних штук
	$sql = "SELECT COUNT(*) as count FROM ".MSGS_GROUPS_TB." i
			LEFT JOIN ".MSGS_GROUPS_USERS_TB." j ON i.group_id=j.group_id
			WHERE i.group_is_online = 0 AND j.user_id='$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// список истории планерок
function get_ps_history_list($user_id, $page)
{
	global $site_db, $current_user_id, $user_obj;
	
	$page = $page ? $page : 0;
	
	// Страничность
	$begin_pos = PS_PER_PAGE * ($page);
	$limit = " LIMIT ".$begin_pos.",".PS_PER_PAGE;
	
	// Выбор планерок ИСТОРИИ 10 последних штук
	$sql = "SELECT i.group_id, i.user_id as group_boss_id, i.date_add,i.group_desc FROM ".MSGS_GROUPS_TB." i
			LEFT JOIN ".MSGS_GROUPS_USERS_TB." j ON i.group_id=j.group_id
			WHERE i.group_is_online = 0 AND j.user_id='$user_id' ORDER by i.group_id DESC $limit";
	 
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{		 
		$history_list .= fill_planning_session_item($row);
	}
	
	return $history_list;
}

function fill_planning_session_item($data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$planning_session_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/messages/planning_session_item.tpl');
	
	// Заполянем объект текущего пользователя
	$user_obj->fill_user_data($data['group_boss_id']);
	
	$user_name = $user_obj->get_user_name();
	$user_middlename= $user_obj->get_user_middlename();
	
	$group_desc = strlen($data['group_desc']) > 50 ? substr($data['group_desc'],0,50).'...' : $data['group_desc'];
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
	$PARS['{NAME}'] =  $user_name[0].'.';
		
	$PARS['{MIDDLENAME}'] = $user_middlename[0].'.';
		
	$PARS['{GROUP_ID}'] = $data['group_id'];
	
	$PARS['{GROUP_DESC}'] = $group_desc ? '"'.$group_desc.'"' : '';
	
	$PARS['{DATE_ADD}'] = datetime($data['date_add'], '%d.%m.%Y в %H:%i');
	
	return fetch_tpl($PARS, $planning_session_item_tpl);
}

?>