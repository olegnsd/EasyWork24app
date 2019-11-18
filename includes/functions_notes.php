<?php
// Страница заметок
function fill_notes($user_id)
{
	global $site_db, $current_user_id, $_CURRENT_USER_WORKERS_ARR, $_CURRENT_USER_DEPUTY_WORKERS_ARR;
	
	$main_tpl = file_get_contents('templates/notes/notes.tpl');
	
	
	
	
	$notes_content = get_notes_content($_GET['av']);
	
	// Верхнее меню
	$top_menu = fill_notes_top_menu();
	
	// Форма создания заметки
	$add_form = fill_notes_add_form();
		
	$PARS['{TOP_MENU}'] = $top_menu;
	
	$PARS['{ADD_FORM}'] = $add_form;
	
	$PARS['{NOTES_CONT}'] = $notes_content;
	
	return fetch_tpl($PARS, $main_tpl);
	
	
}

function get_notes_content($is_av, $search_words)
{
	global $site_db, $current_user_id;
	
	$more_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/more_notes_btn.tpl');
	
	$notes_cont_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/notes_cont.tpl');
	
	
	if($is_av)
	{
		// список переданных заметок
		$notes_list = fill_avalible_notes_list($current_user_id, 1, $search_words);
		// кол-во переданных заметок
		$notes_count = get_user_avalible_notes_count($current_user_id, $search_words);
		
		$is_av = 1;
	}
	else
	{
		if( $_SESSION['note_delete'])
		{
			 $_SESSION['note_delete'] = '';
		}
		
		// Выбираем последнюю добавленную заметку
		$sql = "SELECT note_id FROM ".NOTES_TB." WHERE deleted<>1 ORDER by note_id DESC LIMIT 1";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['note_id'])
		{
			$_SESSION['last_note_id'] = $row['note_id'];
		}
		
		// Список заметок
		$notes_list = fill_notes_list($current_user_id, 1, $search_words);
		
		$notes_count = get_user_notes_count($current_user_id, $search_words);
		
		$is_av = 0;
	}
	
	// Кол-во страниц
	$pages_count = ceil($notes_count/NOTES_PER_PAGE);
		
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_btn = $more_btn_tpl;
	}
	
	$PARS['{NOTES_LIST}'] = $notes_list;
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{MORE_NOTES}'] = $more_btn;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	$PARS['{IS_AV}'] = $is_av;
	
	return fetch_tpl($PARS, $notes_cont_tpl);
}

// Список заметок сотрудника
function fill_avalible_notes_list($user_id, $page = 1, $search_words)
{
	global $site_db, $current_user_id, $user_obj;
	
	$no_notes_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/no_notes.tpl');
	
	// Удаленные в этой сессии клиенты
	$page = $page ? $page : 1;
	
	// Страничность
	$begin_pos = NOTES_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".NOTES_PER_PAGE;
	
	if($search_words)
	{
		$sql = "SELECT DISTINCT(i.note_id), i.* FROM tasks_notes i
				RIGHT JOIN ".NOTE_ACCESS_TB." z ON i.note_id=z.note_id
				LEFT JOIN tasks_notes_versions j ON i.note_id=j.note_id
				WHERE z.user_id='$user_id' AND i.deleted<>1 AND j.deleted = 0 AND (j.note_text LIKE '%$search_words%' OR  j.note_theme LIKE '%$search_words%')
				ORDER by i.last_date_edit DESC $limit";
				 
	}
	else
	{
		// Выбираем заметки
		$sql = "SELECT i.*, j.user_id as access_to_user_id FROM ".NOTES_TB." i
				RIGHT JOIN ".NOTE_ACCESS_TB." j ON i.note_id=j.note_id
				WHERE j.user_id='$user_id' AND i.deleted<>1  ORDER by i.last_date_edit DESC $limit";
	}
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res, 1))
	{
		$notes_list .= fill_note_list_item($row);
	}
	
	if(!$notes_list)
	{
		$notes_list = $no_notes_tpl;
	}
	
	return $notes_list;
}


// Список заметок сотрудника
function fill_notes_list($user_id, $page = 1, $search_words)
{
	global $site_db, $current_user_id, $user_obj;
	
	$no_notes_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/no_notes.tpl');
	
	// Удаленные в этой сессии клиенты
	$deleted_notes_ids = implode(', ', $_SESSION['note_delete']);
	
	if($deleted_notes_ids)
	{
		$and_deleted_notes = " OR i.note_id IN($deleted_notes_ids) ";
	}
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_note_id'])
	{
		$and_note_id = " AND i.note_id <= '".$_SESSION['last_note_id']."' ";
	}
	
	$page = $page ? $page : 1;
	
	// Страничность
	$begin_pos = NOTES_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".NOTES_PER_PAGE;
	
	if($search_words)
	{
		// Выбираем заметки
		$sql = "SELECT DISTINCT(i.note_id), i.* FROM tasks_notes_versions j
				LEFT JOIN tasks_notes i ON i.note_id=j.note_id
				WHERE i.user_id='$user_id' AND (i.deleted<>1 $and_deleted_notes) AND j.deleted = 0  $and_note_id  AND (j.note_text LIKE '%$search_words%' OR  j.note_theme LIKE '%$search_words%')
				ORDER by i.last_date_edit DESC $limit";
	}
	else
	{
		// Выбираем заметки
		$sql = "SELECT * FROM ".NOTES_TB." i WHERE i.user_id='$user_id' AND (i.deleted<>1 $and_deleted_notes) $and_note_id   ORDER by i.last_date_edit DESC $limit";
	}
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$notes_list .= fill_note_list_item($row);
	}
	
	if(!$notes_list)
	{
		$notes_list = $no_notes_tpl;
	}
	
	return $notes_list;
}


// Элемент заметки
function fill_note_list_item($note_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$note_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/note_list_item.tpl');
	
	$edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/edit_tools.tpl');
	
	$edit_tools_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/edit_tools_edit.tpl');
	
	$edit_tools_delete_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/edit_tools_delete.tpl');

	$reprimand_confirm_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/reprimand_confirm_btn.tpl');
	
	$users_access_block_hide_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/users_access_block_hide.tpl');
	
	// Выбираем версию по умолчанию, которую выведем в заметке
	$note_version_item = get_note_version_for_user($note_data['note_id'], $note_data);
	 

	// Автору выговора выводим элементы редактирования
	if($current_user_id==$note_data['user_id'])
	{
		$PARS['{REPRIMAND_ID}'] = $reprimand_data['reprimand_id'];
		
		
	}
	$PARS['{EDIT}'] = $delete_too['reprimand_id'];
	$PARS['{DELETE}'] = $reprimand_data['reprimand_id'];
	//$edit_tools = fetch_tpl($PARS, $edit_tools_tpl);
		
		
	$date_add = datetime($note_data['date'], '%j %M в %H:%i');
	
	// Список версий заметки
	$note_versions_list = fill_note_versions_list($note_data['note_id']);
	
	//$note_access_block = fill_note_access_block($note_data['note_id'], $note_data);
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($note_data['user_id']);
	
	$hide_note = '';
	// Добавляем ссылку Скрыть заметку, которую передали пользователю
	if($note_data['access_to_user_id']==$current_user_id)
	{
		$PARS['{NOTE_ID}'] = $note_data['note_id'];
		
		$hide_note = fetch_tpl($PARS, $users_access_block_hide_tpl);
	}
	
	$PARS['{HIDE_NOTE}'] = $hide_note;
	
	$PARS['{USER_ID}'] = $note_data['user_id'];
	
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;
			
	$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
	$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
	$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{NOTE_ID}'] = $note_data['note_id'];
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	$PARS['{DATE_ADD}'] = $date_add;
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	$PARS['{NOTE_VERSIONS_LIST}'] = $note_versions_list;
	
	$PARS['{NOTE_VERSION_ITEM}'] = $note_version_item;
	
	$PARS['{NOTE_ACCESS_BLOCK}'] = $note_access_block;
	
	return fetch_tpl($PARS, $note_list_item_tpl);
}

function get_note_version_for_user($note_id, $note_data)
{
	global $site_db, $current_user_id;
	
	// Выбираем версию, которую редактировал пользователь, если такая есть
	$sql = "SELECT * FROM ".NOTE_VERISONS_TB." WHERE note_id='".$note_id."' AND deleted<>1 ORDER by date_edit DESC";
	
	$version_data = $site_db->query_firstrow($sql);	
	
	/*// Если нет версии пользователя, выводи оригинальную
	if(!$version_data['version_id'])
	{
		$sql = "SELECT * FROM ".NOTE_VERISONS_TB." WHERE note_id='".$note_id."' AND  is_original=1";
	
		$version_data = $site_db->query_firstrow($sql);	
	}*/
	
	$note_version_item = fill_note_version_item($version_data, 0, $note_data);
	
	return $note_version_item;
}

// блок передачи заметки
function fill_note_access_block($note_data)
{
	global $site_db, $current_user_id, $user_obj, $_CURRENT_USER_BOSS_ARR, $_CURRENT_USER_WORKERS_ARR;
	
	$users_access_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/users_access_block.tpl');
	
	$users_access_user_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/users_access_user_item.tpl');
	
	$no_users_to_access_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/no_users_to_access.tpl');
	
	$users_access_block_hide_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/users_access_block_hide.tpl');
	
	$option_fcbk_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl');
	
	$user_access_select_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/user_access_select.tpl');
	
	$note_id = $note_data['note_id'];
	 
	$sql = "SELECT * FROM ".NOTE_ACCESS_TB." WHERE note_id='$note_id' AND user_id!='$current_user_id' ORDER by id ";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$user_obj->fill_user_data($row['user_id']); 
	
		$user_name = $user_obj->get_user_name();
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_name = $user_obj->get_user_surname().' '.$user_name[0].'. '.$user_middlename[0].', '.$user_obj->get_user_position(); 
		
		$PARS['{ACCESS_ID}'] = $row['id'];
		$PARS['{NOTE_ID}'] = $row['note_id'];
		$PARS['{CLASS}'] = 'selected';
		$PARS['{VALUE}'] = $row['user_id'];
		$PARS['{NAME}'] = $user_name;
		$users_access_list .= fetch_tpl($PARS, $user_access_select_tpl);
		
		
	}
	
	/*// Документ можно передать и начальнику и подчиненному
	$users_for_access_arr = get_current_user_users_arrs(array(1,1,0,1,1), 1);
 
	foreach($users_for_access_arr as $user_data)
	{ 
	 
		if($note_data['user_id']==$user_data['user_id'])
		{
			continue;
		}
		
		$access_active = '';
		
		// Проверяем доступность документа начальнику
		$sql = "SELECT id FROM ".NOTE_ACCESS_TB." WHERE user_id='".$user_data['user_id']."' AND note_id='$note_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			$access_active = 'access_active';
		}
		
		$user_obj->fill_user_data($user_data['user_id']);
		
		$user_name = $user_obj->get_user_name();
		
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_surname = $user_obj->get_user_surname();
		
		$user_position = $user_obj->get_user_position();

		$PARS1['{NOTE_ID}'] = $note_id;
		
		$PARS1['{ACCESS_ACTIVE}'] = $access_active;

		$PARS1['{USER_ID}'] = $user_data['user_id'];
		
		$PARS1['{SURNAME}'] = $user_surname;
		
		$PARS1['{NAME}'] = $user_name;
				
		$PARS1['{MIDDLENAME}'] = $user_middlename;
				
		$PARS1['{USER_POSITION}'] = $user_position;
		  
		$users_access_list .= fetch_tpl($PARS1, $users_access_user_item_tpl);
	}*/
	
	if(!$users_access_list)
	{
		//return '';
	}
	
	
	
	$PARS['{NOTE_ID}'] = $note_id;
	
	$PARS['{USERS_LIST}'] = $users_access_list;
	
	 
	
	return  fetch_tpl($PARS, $users_access_block_tpl);
}

// Список версий заметки
function fill_note_versions_list($note_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$note_version_title_original_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/note_version_title_original_item.tpl');
	
	$note_version_title_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/note_version_title_item.tpl');
	
	$version_date_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/version_date_edit.tpl');
	
	// Выбор всех версий заметок
	$sql = "SELECT *, if(is_original,1,0) as ori_order FROM ".NOTE_VERISONS_TB." WHERE deleted<>1 AND note_id='$note_id' ORDER by date_edit DESC";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		### Блок даты при развороте версии
		if(!preg_match('/0000/', $row['date_edit']))
		{
			$PARS_2['{DATE_EDIT}'] = datetime($row['date_edit'], '%d.%m.%y');
			$date_block = fetch_tpl($PARS_2, $version_date_edit_tpl);
		}
		else
		{
			$date_block = datetime($row['date'], '%d.%m.%y');
		}
		## \
	
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row['user_id']);
		
		$PARS['{USER_ID}'] = $note_data['user_id'];
		
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
				
		$PARS['{USER_NAME}'] = $user_obj->get_user_name();
			
		$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{VERSION_ID}'] = $row['version_id'];
		
		$PARS['{NOTE_ID}'] = $row['note_id'];
		
		$PARS['{DATE_BLOCK}'] = $date_block;
	
		if($row['is_original'])
		{
			$versions_list .= fetch_tpl($PARS, $note_version_title_original_item_tpl);
		}
		else
		{
			$versions_list .= fetch_tpl($PARS, $note_version_title_item_tpl);
		}
	}
	
	return $versions_list;
}

// Версия
function fill_note_version_item($version_data, $is_edit=0, $note_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$note_version_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/note_version_item.tpl');
	
	$note_version_edit_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/note_version_edit_item.tpl');
	
	$show_note_version_original_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/show_note_version_original.tpl');
	
	$show_note_version_edited_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/show_note_version_edited.tpl');
	
	$version_date_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/version_date_edit.tpl');
	
	$version_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/version_edit_tools.tpl');
	
	$version_edit_tools_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/version_edit_tools_edit.tpl');
	
	$version_edit_tools_delete_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/version_edit_tools_delete.tpl');
	
	$note_tools_delete_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/notes/note_tools_delete.tpl');
	
	// Заполянем объект пользователя, кто создавал версию  заметки
	$user_obj->fill_user_data($version_data['user_id']);
	$user_surname = $user_obj->get_user_surname();
	$user_name = $user_obj->get_user_name();
	$user_middlename = $user_obj->get_user_middlename();
	$user_position = $user_obj->get_user_position();
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($version_data['user_id'], $user_obj->get_user_image());
	
	// Выводим инструменты управления версией
	//if($version_data['user_id']==$current_user_id)
	//{
		 $version_edit_btn = $version_edit_tools_edit_tpl;
	//}
	// Создателю выводим кнопку удалить полностью заметку
	if($note_data['user_id']==$current_user_id)
	{
		$version_delete_btn = $note_tools_delete_tpl;
	}
	// Кому передавали заметку, выводим только кнопку удалить свою версию
	else if( $version_data['user_id']==$current_user_id)
	{
		$version_delete_btn = $version_edit_tools_delete_tpl;
	}
	
	
	$PARS_3['{EDIT}'] = $version_edit_btn;
	$PARS_3['{DELETE}'] = $version_delete_btn;
	$PARS_3['{VERSION_ID}'] = $version_data['version_id'];
	$PARS_3['{NOTE_ID}'] = $note_data['note_id'];
	$version_edit_tools = fetch_tpl($PARS_3, $version_edit_tools_tpl);
		
		
	### Ссылка для показа блока версии заметки
	if($version_data['is_original'])
	{
		$show_version_tpl = $show_note_version_original_tpl;
	}
	else
	{
		$show_version_tpl = $show_note_version_edited_tpl;
	}
	$PARS_1['{VERSION_ID}'] = $version_data['version_id'];
	$PARS_1['{USER_NAME}'] = $user_name;
	$PARS_1['{USER_MIDDLENAME}'] = $user_middlename;
	$PARS_1['{USER_SURNAME}'] = $user_surname;
	$PARS_1['{DATE_EDIT}'] = datetime($version_data['date_edit'], '%j %M в %H:%i');;
	$show_version_link = fetch_tpl($PARS_1, $show_version_tpl);
	## \
	
	### Блок даты при развороте версии
	if(!preg_match('/0000/', $version_data['date_edit']))
	{
		$PARS_2['{DATE_EDIT}'] = datetime($version_data['date_edit'], '%j %M в %H:%i');
		$date_block = fetch_tpl($PARS_2, $version_date_edit_tpl);
	}
	else
	{
		$date_block = datetime($version_data['date'], '%j %M в %H:%i');
	}
	## \
	 
	// Заголовок заметки 
	if($version_data['note_theme'])
	{
		$note_title = $version_data['note_theme'];
	}
	else
	{
		$note_title = cut_note_title($version_data['note_text'], $note_data['note_id']);
	}
		
	$PARS['{USER_ID}'] = $version_data['user_id'];
	
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;
			
	$PARS['{USER_NAME}'] = $user_name;
		
	$PARS['{USER_MIDDLENAME}'] = $user_middlename;
			
	$PARS['{USER_SURNAME}'] = $user_surname;
			
	$PARS['{USER_POSITION}'] = $user_position;
	
	$PARS['{NOTE_ID}'] = $version_data['note_id'];
	
	$PARS['{VERSION_ID}'] = $version_data['version_id'];
	
	$PARS['{NOTE_TEXT}'] = $is_edit ? $version_data['note_text'] : nl2br($version_data['note_text']);
	
	$PARS['{NOTE_THEME}'] = $version_data['note_theme'];
	
	$PARS['{NOTE_TITLE}'] = $note_title;
	
	$PARS['{EDIT_TOOLS}'] = $version_edit_tools;
	
	$PARS['{SHOW_VERSION_STR}'] = $show_version_str;
	
	$PARS['{DATE_BLOCK}'] = $date_block;
	
	$PARS['{SHOW_VERSION_LINK}'] = $show_version_link;
	
	if($is_edit)
	{
		return fetch_tpl($PARS, $note_version_edit_item_tpl);
	}
	else
	{
		return fetch_tpl($PARS, $note_version_item_tpl);
	}
	 
}

// Возвращает краткое название заметки
function cut_note_title($note_text, $note_id)
{
	global $site_db, $current_user_id;
	
	$max_note_title_lenght = 200;
	
	if($note_text)
	{
		return strlen($note_text) > $max_note_title_lenght ? substr($note_text,0,$max_note_title_lenght).'...' : $note_text;
	}
	else if($note_id)
	{
		$sql = "SELECT note_text FROM ".NOTE_VERISONS_TB." WHERE is_original = 1 AND note_id='$note_id'";
		 
		$row = $site_db->query_firstrow($sql);
		
		$note_title =  strlen($row['note_text']) > $max_note_title_lenght ? substr($row['note_text'],0,$max_note_title_lenght).'...' : $row['note_text'];
		
		return $note_title;
	}
	 
}
// Кол-во заметок
function get_user_notes_count($user_id, $search_words='')
{
	global $site_db, $current_user_id;
	
	if($search_words)
	{
		// Выбираем заметки
		$sql = "SELECT COUNT(DISTINCT(i.note_id)) as count FROM tasks_notes_versions j
				LEFT JOIN tasks_notes i ON i.note_id=j.note_id
				WHERE i.user_id='$user_id' AND i.deleted<>1 AND j.deleted = 0 AND (j.note_text LIKE '%$search_words%' OR  j.note_theme LIKE '%$search_words%')
				 ";
	}
	else
	{
		// Кол-во заметок
		$sql = "SELECT COUNT(*) as count FROM ".NOTES_TB." WHERE user_id='$user_id' AND deleted<>1";
	}
 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Кол-во заметок
function get_user_avalible_notes_count($user_id, $search_words)
{
	global $site_db, $current_user_id;
	
	if($search_words)
	{
		$sql = "SELECT COUNT(DISTINCT(i.note_id)) as count FROM tasks_notes i
				RIGHT JOIN ".NOTE_ACCESS_TB." z ON i.note_id=z.note_id
				LEFT JOIN tasks_notes_versions j ON i.note_id=j.note_id
				WHERE z.user_id='$user_id' AND i.deleted<>1 AND j.deleted = 0 AND (j.note_text LIKE '%$search_words%' OR  j.note_theme LIKE '%$search_words%')
				 ";
	}
	// Выбираем заметки
	else
	{
		$sql = "SELECT COUNT(*) as count FROM ".NOTES_TB." i
				RIGHT JOIN ".NOTE_ACCESS_TB." j ON i.note_id=j.note_id
				WHERE j.user_id='$user_id' AND i.deleted<>1";
	}
			
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Форма добавления выговора
function fill_notes_add_form()
{
	global $site_db, $current_user_id, $user_obj;
	
	if($_GET['av'])
	{
		return '';
	}
	$add_form_tpl = file_get_contents('templates/notes/add_form.tpl');
	
	return $add_form_tpl;
}

// Верхнее меню
function fill_notes_top_menu()
{
	global $site_db, $current_user_id;
	
	$top_menu_tpl = file_get_contents('templates/notes/top_menu.tpl');
	
	$top_menu_others_tpl = file_get_contents('templates/notes/top_menu_others.tpl');
	
	// Если у пользователя нет сотрудников, не выводим раздел "Клиенты моих сотурдников"
	if($_CURRENT_USER_WORKERS_ARR)
	{
		$workers_top_menu = $top_menu_workers_tpl;
	}

	// Чужие заметки
	if($_GET['av'])
	{
		// Убираем уведомление о новых заметках
		noticed_new_notes($current_user_id);
		
		$active_menu_2 = 'menu_active';
	}
	else
	{
		$active_menu_1 = 'menu_active';
	}
	
	$new_notes_count = get_new_notes_count_for_user($current_user_id);
	$new_notes_count_for_user = $new_notes_count ? ' (+ '.$new_notes_count.')' : '';
	
	$PARS_1['{ACTIVE_1}'] = $active_menu_1;
	
	$PARS_1['{ACTIVE_2}'] = $active_menu_2;
		
	$PARS_1['{NEW_NOTES_COUNT_FOR_USER}'] = $new_notes_count_for_user; 
	
	return fetch_tpl($PARS_1, $top_menu_tpl);

}

// Кол-во новых заметок, переданных пользователю
function get_new_notes_count_for_user($user_id)
{
	global $site_db, $current_user_id;
	
	// Выбираем заметки
	$sql = "SELECT COUNT(*) as count FROM ".NOTES_TB." i
			RIGHT JOIN ".NOTE_ACCESS_TB." j ON i.note_id=j.note_id
			WHERE j.user_id='$user_id' AND i.deleted<>1 AND j.noticed<>1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Добавляем флаг, что заметка прочитана
function noticed_new_notes($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "UPDATE ".NOTE_ACCESS_TB." SET noticed = 1 WHERE user_id='$user_id'";
	
	$site_db->query($sql);
}

?>