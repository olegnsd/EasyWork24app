<?php
// Список Моих коллег
function fill_ofdocs($user_id)
{
	global $site_db, $current_user_id;
	
	$main_tpl = file_get_contents('templates/ofdocs/ofdocs.tpl');
	
	$more_btn_tpl = file_get_contents('templates/ofdocs/more_btn.tpl');
	
	// Очистка массива удаленных контактов
	if($_SESSION['ofdoc_delete'])
	{
		$_SESSION['ofdoc_delete'] = '';
	}
	
	// Форма добавления документа
	if($_GET['wks'])
	{
		//noticed_ofdoc_by_user_id($current_user_id);
		
		$ofdocs_list = fill_workers_ofdocs_list($current_user_id);
		
		$ofdocs_count = get_workers_ofdoc_count($current_user_id);
		
		$is_wks = 1;
	}
	else
	{
		// Выбираем последнее добавленное имущество
		$sql = "SELECT ofdoc_id FROM ".OFDOSC_TB." WHERE deleted<>1 ORDER by ofdoc_id DESC LIMIT 1";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['ofdoc_id'])
		{
			$_SESSION['last_ofdoc_id'] = $row['ofdoc_id'];
		}
	
		$add_form = fill_ofdocs_add_form();
		
		$ofdocs_list = fill_ofdocs_list($user_id);
		
		$ofdocs_count = get_ofdoc_count($user_id);
		
		$is_wks = 0;
	}
	
	// Кол-во страниц
	$pages_count = ceil($ofdocs_count/OFDOCS_PER_PAGE);
		
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_btn = $more_btn_tpl;
	}
	 
	
	// Список сотрудников
	//$colleagues_list = fill_colleagues_list($current_user_id);
	
	$top_menu = fill_ofdocs_top_menu();
	
	$PARS['{ADD_FORM}'] = $add_form;
	
	$PARS['{OFDOCS_LIST}'] = $ofdocs_list;
	
	$PARS['{MORE_OFDOCS}'] = $more_btn;

	$PARS['{TOP_MENU}'] = $top_menu;
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	$PARS['{IS_WKS}'] = $is_wks;
	
	return fetch_tpl($PARS, $main_tpl);
}


// Список документов
function fill_workers_ofdocs_list($user_id, $page=1)
{
	global $site_db, $current_user_id, $user_obj;
	
	$no_ofdocs_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/no_ofdocs.tpl');
	
	// Страничность
	$begin_pos = OFDOCS_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".OFDOCS_PER_PAGE;
	
	// Выводим список документов, которым пользователь получил доступ
	$sql = "SELECT i.* FROM ".OFDOSC_TB." i
			RIGHT JOIN ".OFDOCS_ACCESS_TB." j ON j.ofdoc_id=i.ofdoc_id
			WHERE j.user_id='$user_id'  AND (i.deleted<>1 $and_deleted_ofdocs) $and_ofdoc_id ORDER by i.ofdoc_id DESC $limit";
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$ofdocs_list .= fill_ofdocs_list_item($row);
	}
	
	if(!$ofdocs_list)
	{
		$ofdocs_list = $no_ofdocs_tpl;
	}
	
	return $ofdocs_list;
}

// Снять все уведомления по новым документам
function noticed_ofdoc_by_user_id($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "UPDATE  ".OFDOCS_ACCESS_TB." SET noticed=1 WHERE user_id='$user_id'";	
	
	$site_db->query($sql);
}

// Список документов
function fill_ofdocs_list($user_id, $page=1)
{
	global $site_db, $current_user_id, $user_obj;
	
	$no_ofdocs_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/no_ofdocs.tpl');
	
	// Страничность
	$begin_pos = OFDOCS_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".OFDOCS_PER_PAGE;
	
	
	// Удаленные в этой сессии клиенты
	$deleted_ofdoc_ids = implode(', ', $_SESSION['ofdoc_delete']);
	if($deleted_planning_ids)
	{
		$and_deleted_ofdocs = " OR ofdoc_id IN($deleted_ofdoc_ids) ";
	}
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_ofdoc_id'])
	{
		$and_ofdoc_id = " AND ofdoc_id <= '".$_SESSION['last_ofdoc_id']."' ";
	}
	
	if(!$ofdocs_workers)
	{
		$sql = "SELECT * FROM ".OFDOSC_TB." WHERE user_id='$user_id'  AND (deleted<>1 $and_deleted_ofdocs) $and_ofdoc_id ORDER by ofdoc_id DESC $limit";
	}
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$ofdocs_list .= fill_ofdocs_list_item($row);
	}
	
	if(!$ofdocs_list)
	{
		$ofdocs_list = $no_ofdocs_tpl;
	}
	
	return $ofdocs_list;
}

// Статусы документа
function get_ofdoc_statuses($ofdoc_id)
{
    global $site_db;

    $sql = "SELECT * FROM tasks_ofdocs_statuses WHERE ofdoc_id='$ofdoc_id'";

    $res = $site_db->query($sql);

    $statuses = [];

    while($row=$site_db->fetch_array($res))
    {
        $statuses[$row['status_id']] = $row['status_id'];
    }

    return $statuses;
}

function fill_ofdocs_list_item($ofdoc_data, $is_edit = 0)
{
	global $site_db, $current_user_id, $user_obj;
	
	$ofdocs_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/ofdocs_list_item.tpl');
	
	$ofdocs_list_item_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/ofdocs_list_item_edit.tpl');
	 
	$edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/edit_tools.tpl');

    $ofdoc_to_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/ofdoc_to_user.tpl');
	
	if($is_edit)
	{
		$item_tpl = $ofdocs_list_item_edit_tpl;
		$types_list = fill_ofdocs_types_list($ofdoc_data['type_id']);
	}
	else
	{
		$item_tpl = $ofdocs_list_item_tpl;
		//$ofdoc_users_access = fill_ofdoc_access_block($ofdoc_data['ofdoc_id'], $ofdoc_data);
		
		$comment_btn_only = $ofdoc_data['user_id']==$current_user_id ? 1 : 0;
		
		$status_block = fill_ofdoc_status_block($ofdoc_data['ofdoc_id'], $comment_btn_only);
	}
	
	$date_add = datetime($ofdoc_data['date'], '%j %M в %H:%i');
	
	$ofdoc_type_name = get_ofdoc_type_name_by_type_id($ofdoc_data['type_id']);
	
	// Автору документа выводит элементы редактирования
	if($current_user_id==$ofdoc_data['user_id'])
	{
		$PARS['{OFDOC_ID}'] = $ofdoc_data['ofdoc_id'];
		
		$edit_tools = fetch_tpl($PARS, $edit_tools_tpl);
	}
	
	 
	
	// Заполянем объект пользователя, кому передано
	$user_obj->fill_user_data($ofdoc_data['to_user_id']);
	$to_name = $user_obj->get_user_name();
	$to_surname = $user_obj->get_user_middlename();
	$to_moddlename = $user_obj->get_user_surname();

    $statuses = get_ofdoc_statuses($ofdoc_data['ofdoc_id']);


    if(in_array(5, $statuses)) {
        $color = '#0c99f2';
    }
    else if(in_array(3, $statuses)) {
        $color = '#36d00d';
    }
    else if(in_array(2, $statuses)) {
        $color = '#92e60e';
    }
    else if(in_array(1, $statuses)) {
        $color = '#FFF118';
    }

    $ofdoc_users = get_ofdoc_users($ofdoc_data['ofdoc_id'], 1);
    $users_list = '';

    if($ofdoc_users){

        $to_user_id = key($ofdoc_users);

        $user_obj_1 = new CUser($site_db);

        // Заполянем объект пользователя
        $user_obj_1->fill_user_data($to_user_id);

        // Превью аватарки пользователя
        $user_avatar_src_1 = get_user_preview_avatar_src($to_user_id, $user_obj_1->get_user_image());

        $PARS['{NAME}'] = $user_obj_1->get_user_name();

        $PARS['{SURNAME}'] = $user_obj_1->get_user_surname();

        $PARS['{AVATAR_SRC}'] = $user_avatar_src_1;

        $ofdoc_to_user = fetch_tpl($PARS, $ofdoc_to_user_tpl);


        // Выбор статусов документа
        $sql = "SELECT i.*, j.status_name FROM ".OFDOCS_STATUSES_TB." i, ".OFDOCS_STATUS_DATA_TB." j 
			WHERE i.status_id=j.status_id AND i.ofdoc_id='".$ofdoc_data['ofdoc_id']."' ORDER by i.id DESC";

        $res = $site_db->query($sql);

        $statusesUsers = [];

        while($row=$site_db->fetch_array($res))
        {
            $statusesUsers[$row['user_id']] = $row['user_id'];
        }

        foreach ($statusesUsers as $user) {

            // Заполянем объект пользователя
            $user_obj_1->fill_user_data($user);

            // Превью аватарки пользователя
            $user_avatar_src_1 = get_user_preview_avatar_src($user, $user_obj_1->get_user_image());

            $PARS['{NAME}'] = $user_obj_1->get_user_name();

            $PARS['{SURNAME}'] = $user_obj_1->get_user_surname();

            $PARS['{AVATAR_SRC}'] = $user_avatar_src_1;

            $users_list .= fetch_tpl($PARS, $ofdoc_to_user_tpl).' ';
        }
    }


	// Заполянем объект пользователя
	$user_obj->fill_user_data($ofdoc_data['user_id']);
	
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($ofdoc_data['user_id'], $user_obj->get_user_image());

    $PARS['{USERS_LIST}'] = $users_list;

    $PARS['{OFDOC_TO_USER}'] = $ofdoc_to_user;

	$PARS['{USER_ID}'] = $ofdoc_data['user_id'];
	
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;
			
	$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
	$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
	$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	
	$PARS['{TO_NAME}'] = $to_name;
		
	$PARS['{TO_MIDDLENAME}'] = $to_surname;
			
	$PARS['{TO_SURNAME}'] = $to_moddlename;
			
	
	$PARS['{OFDOC_ID}'] = $ofdoc_data['ofdoc_id'];
	
	$PARS['{DATE_ADD}'] = $date_add;
	
	$PARS['{OFDOC_TYPE}'] = $ofdoc_type_name;
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	$PARS['{OFDOC_TEXT}'] = $is_edit ? $ofdoc_data['ofdoc_text'] :  nl2br($ofdoc_data['ofdoc_text']); 
	
	$PARS['{OFDOCS_TYPE_LIST}'] = $types_list;
	
	$PARS['{USERS_ACCESS}'] = $ofdoc_users_access;
	
	$PARS['{STATUS_BLOCK}'] = $status_block;

    $PARS['{COLOR}'] = $color;
	
	return fetch_tpl($PARS, $item_tpl);
}

function get_ofdoc_users($ofdoc_id)
{
    global $site_db, $current_user_id;

    // выбор всех пользоватлей, кому передали документ
    $sql = "SELECT * FROM tasks_users_ofdocs_access WHERE ofdoc_id='$ofdoc_id'   ORDER by id ";

    $res = $site_db->query($sql);

    $users = [];

    while($row=$site_db->fetch_array($res))
    {
        $users[$row['user_id']] = $row['user_id'];
    }

    return $users;
}

// Блок статусов
function fill_ofdoc_status_block($ofdoc_id, $comment_btn_only=0)
{
	global $site_db, $current_user_id, $user_obj;
	
	$status_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/status_block.tpl');
	
	$status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/status_btn.tpl');
	
	// Список статусов
	$sql = "SELECT * FROM ".OFDOCS_STATUS_DATA_TB." ORDER by status_sort ASC";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		// оставляем только кнопку оставить комментарий
		if($comment_btn_only && $row['status_id']!=4)
		{
			continue;
		}
		
		$PARS['{OFDOC_ID}'] = $ofdoc_id;
		
		$PARS['{STATUS_ID}'] = $row['status_id'];
		
		$PARS['{STATUS_NAME}'] = $row['status_name'];
		
		$btns_list .= fetch_tpl($PARS, $status_btn_tpl);
	}
	
	// Список статусов документа
	//$statuses_list = fill_ofdoc_statuses_list($ofdoc_id);
	
	$new_count = get_new_ofdocs_statuses_count($current_user_id, 'ofdoc_id', $ofdoc_id);
	$new_count = $new_count ? ' (+'.$new_count.')' : '';
	
	
	$PARS['{OFDOC_ID}'] = $ofdoc_id;
	
	$PARS['{USERS_LIST}'] = $users_access_list;
	
	$PARS['{STATUS_BTNS}'] = $btns_list;
	
	$PARS['{STATUSES_LIST}'] = $statuses_list;
	
	$PARS['{NEW_COUNT}'] = $new_count;
	
	return  fetch_tpl($PARS, $status_block_tpl);
}

// Список статусов
function fill_ofdoc_statuses_list($ofdoc_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$status_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/status_item.tpl');
	
	$no_doc_status_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/no_doc_status.tpl');
	
	// Выбор статусов документа
	$sql = "SELECT i.*, j.status_name FROM ".OFDOCS_STATUSES_TB." i, ".OFDOCS_STATUS_DATA_TB." j 
			WHERE i.status_id=j.status_id AND i.ofdoc_id='$ofdoc_id' ORDER by i.id DESC";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$status_date = datetime($row['status_date'], '%d.%m.%y в %H:%i');
		
		// Не выводим название статуса Комментария
		$status_name = $row['status_id'] == 4 ? '' : $row['status_name'];
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row['user_id']);
		
		$PARS['{USER_ID}'] = $row['user_id'];
		
		$PARS['{NAME}'] = $user_obj->get_user_name();
		
		$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
		$PARS['{STATUS_ID}'] = $row['status_id'];
		
		$PARS['{STATUS_NAME}'] = $status_name;
		
		$PARS['{TEXT}'] = nl2br($row['status_text']);
		
		$PARS['{STATUS_DATE}'] = $status_date;
		
		$statuses_list .= fetch_tpl($PARS, $status_item_tpl);
	}
	
	if(!$statuses_list)
	{
		$statuses_list = $no_doc_status_tpl;
	}
	
	return $statuses_list;
}

// Получение блока статусов файла
function fill_ofdoc_statuses_block($file_id)
{
	global $site_db, $current_user_id;
	
	$file_statuses_cont_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/files/file_statuses_cont.tpl');
	
	$status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/files/status_btn.tpl');
	
	// Список статусов
	$sql = "SELECT * FROM ".FILE_STATUS_DATA_TB." ORDER by status_sort ASC";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$PARS['{FILE_ID}'] = $file_id;
		
		$PARS['{STATUS_ID}'] = $row['status_id'];
		
		$PARS['{STATUS_NAME}'] = $row['status_name'];
		
		$btns_list .= fetch_tpl($PARS, $status_btn_tpl);
	}
	
	// Список статусов
	$statuses_list = fill_file_statuses_list($file_id);
	
	$PARS['{STATUS_BTNS}'] = $btns_list;
	
	$PARS['{STATUSES_LIST}'] = $statuses_list;
	
	return fetch_tpl($PARS, $file_statuses_cont_tpl);
}

function fill_ofdoc_access_block($ofdoc_data)
{
	global $site_db, $current_user_id, $user_obj, $_CURRENT_USER_BOSS_ARR, $_CURRENT_USER_WORKERS_ARR;
	
	$users_access_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/users_access_block.tpl');
	
	$users_access_user_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/users_access_user_item.tpl');
	
	$no_users_to_access_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/no_users_to_access.tpl');
	
	$option_fcbk_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl');
	
	$user_access_select_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ofdocs/user_access_select.tpl');
	
	$ofdoc_id = $ofdoc_data['ofdoc_id'];
	
	// выбор всех пользоватлей, кому передали документ
	$sql = "SELECT * FROM tasks_users_ofdocs_access WHERE ofdoc_id='$ofdoc_id' AND user_id!='$current_user_id' ORDER by id ";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$user_obj->fill_user_data($row['user_id']); 
	
		$user_name = $user_obj->get_user_name();
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_name = $user_obj->get_user_surname().' '.$user_name[0].'. '.$user_middlename[0].', '.$user_obj->get_user_position(); 
		
		$PARS['{ACCESS_ID}'] = $row['id'];
		$PARS['{OFDOC_ID}'] = $row['ofdoc_id'];
		$PARS['{CLASS}'] = 'selected';
		$PARS['{VALUE}'] = $row['user_id'];
		$PARS['{NAME}'] = $user_name;
		$users_access_list .= fetch_tpl($PARS, $user_access_select_tpl);
		
	}
	
	/*
	
	// Документ можно передать и начальнику и подчиненному
	$users_for_access_arr = array_merge($_CURRENT_USER_BOSS_ARR, $_CURRENT_USER_WORKERS_ARR);
	
	$users_for_access_arr = get_current_user_users_arrs(array(1,1,0,1,1), 1);
	
	foreach($users_for_access_arr as $user_data)
	{ 
		if($ofdoc_data['user_id']==$user_data['user_id'])
		{
			continue;
		}
		
		$access_active = '';
		
		// Проверяем доступность документа начальнику
		$sql = "SELECT id FROM ".OFDOCS_ACCESS_TB." WHERE user_id='".$user_data['user_id']."' AND ofdoc_id='$ofdoc_id'";
		
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

		$PARS1['{OFDOC_ID}'] = $ofdoc_id;
		
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
		//$users_access_list = $no_users_to_access_tpl;
	}
	
	$PARS['{OFDOC_ID}'] = $ofdoc_id;
	
	$PARS['{USERS_LIST}'] = $users_access_list;
	
	return  fetch_tpl($PARS, $users_access_block_tpl);
}
function get_ofdoc_count($user_id)
{
	global $site_db, $current_user_id;
	
	// Кол-во документов
	$sql = "SELECT COUNT(*) as count FROM ".OFDOSC_TB." WHERE user_id='$user_id' AND deleted<>1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

function get_workers_ofdoc_count($user_id)
{
	global $site_db, $current_user_id;
	
	// Кол-во документов
	$sql = "SELECT COUNT(*) as count FROM ".OFDOSC_TB." i
			RIGHT JOIN ".OFDOCS_ACCESS_TB." j ON j.ofdoc_id=i.ofdoc_id
			WHERE j.user_id='$user_id'  AND i.deleted<>1";
 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Кол-во новых оф документов для начальника
function get_new_ofdocs_count($user_id)
{
	global $site_db, $current_user_id;
	
	// Кол-во документов
	$sql = "SELECT COUNT(*) as count FROM ".OFDOSC_TB." i
			RIGHT JOIN ".OFDOCS_ACCESS_TB." j ON j.ofdoc_id=i.ofdoc_id
			WHERE j.user_id='$user_id'  AND i.deleted<>1 AND j.noticed=0";
 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Название типа
function get_ofdoc_type_name_by_type_id($type_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT * FROM ".OFDOSC_TYPES_TB." WHERE type_id='$type_id'";
	
	$type_data = $site_db->query_firstrow($sql);
	
	return $type_data['type_name'];
}

function fill_ofdocs_add_form()
{
	global $site_db, $current_user_id, $user_obj;
	
	$option_tag_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	// Документ можно передать и начальнику и подчиненному
	$users_for_select_arr = get_current_user_users_arrs(array(1,1,1,1,1,1));
	 
	if(!$users_for_select_arr)
	{
		return '';
	}
	
	$add_form_tpl = file_get_contents('templates/ofdocs/add_form.tpl');
	
	 
	
	/*foreach($users_for_select_arr as $user_data)
	{
		$user_obj->fill_user_data($user_data['user_id']);
		
		$user_name = $user_obj->get_user_name();
		
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_surname = $user_obj->get_user_surname();
		
		$user_position = $user_obj->get_user_position();
		
		$PARS['{VALUE}'] = $user_data['user_id'];
		
		$PARS['{NAME}'] = $user_surname.' '.$user_name.' '.$user_middlename;
		
		$PARS['{SELECTED}'] = '';
		
		$users_list .= fetch_tpl($PARS, $option_tag_tpl);
	}*/
	
	$types_list = fill_ofdocs_types_list(0);
	
	$PARS['{OFDOCS_TYPE_LIST}'] = $types_list;
	
	$PARS['{USERS_LIST}'] = $users_list;
	
	return fetch_tpl($PARS, $add_form_tpl);
}

function fill_ofdocs_types_list($type_id)
{
	global $site_db, $current_user_id;
	
	$option_tag_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	// Типы докумнтов
	$sql = "SELECT * FROM ".OFDOSC_TYPES_TB."";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$selected = $row['type_id'] == $type_id ? 'selected="selected"' : '';
		
		$PARS['{VALUE}'] = $row['type_id'];
		
		$PARS['{NAME}'] = $row['type_name'];
		
		$PARS['{SELECTED}'] = $selected;
		
		$types_list .= fetch_tpl($PARS, $option_tag_tpl);
	}
	
	return $types_list;
}

function fill_ofdocs_top_menu()
{
	global $site_db, $current_user_id;
	
	$top_menu_tpl = file_get_contents('templates/ofdocs/top_menu.tpl');
	
	$top_menu_workers_tpl = file_get_contents('templates/ofdocs/top_menu_workers.tpl');
	
	// Если у пользователя нет сотрудников, не выводим раздел "Клиенты моих сотурдников"
	$workers_top_menu = $top_menu_workers_tpl;
	 
	
	if($_GET['wks'])
	{
		$active_menu_2 = 'menu_active';
		
		// Убираем уведомления
		noticed_ofdoc_by_user_id($current_user_id);
	}
	else
	{
		$active_menu_1 = 'menu_active';
	}
	
	$new_workers_ofdocs_count = get_new_ofdocs_count($current_user_id);
	$new_workers_ofdocs_count += get_new_ofdocs_statuses_count($current_user_id, 'accessed');
	$new_workers_ofdocs_count_for_boss = $new_workers_ofdocs_count ? ' (+ '.$new_workers_ofdocs_count.')' : '';
	
	 
	
	$new_count = get_new_ofdocs_statuses_count($current_user_id, 'own');
	$new_count = $new_count ? ' (+ '.$new_count.')' : '';
	
	$PARS_1['{ALL_TOP_MENU}'] = $workers_top_menu;
	
	$PARS_1['{ACTIVE_1}'] = $active_menu_1;
	
	$PARS_1['{ACTIVE_2}'] = $active_menu_2;
		
	$PARS_1['{NEW_COUNT_FOR_BOSS}'] = $new_workers_ofdocs_count_for_boss; 
	
	$PARS_1['{NEW_COUNT}'] = $new_count; 
	
	return fetch_tpl($PARS_1, $top_menu_tpl);

}
// Создать уведомление
function ofdoc_set_user_notice($ofdoc_id, $status_id)
{
	global $site_db, $current_user_id;
	
	// выбираем всех людей, которым доступен документ
	$sql = "SELECT * FROM ".OFDOCS_ACCESS_TB." WHERE ofdoc_id='$ofdoc_id'";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		if($current_user_id==$row['user_id'])
		{
			continue;
		}
		
		$users[$row['user_id']] = $row['user_id'];
	}
	
	// данные документа
	$sql = "SELECT * FROM ".OFDOSC_TB." WHERE ofdoc_id='$ofdoc_id'";
	
	$ofdoc_data = $site_db->query_firstrow($sql);
	
	if($ofdoc_data['user_id']!=$current_user_id)
	{
		$users[$ofdoc_data['user_id']] = $ofdoc_data['user_id'];
	}
	
	// добавляем уведомления
	foreach($users as $user_id)
	{
		$sql = "INSERT INTO tasks_users_ofdocs_notices SET user_id='$user_id', ofdoc_id='$ofdoc_id', status_id='$status_id'";
		
		$site_db->query($sql);
	}
	
}

// удаление уведомлений изменений статусов документов
function ofdoc_delete_notice($ofdoc_id, $user_id, $status_id, $delete_mode)
{
	global $site_db, $current_user_id;
	
	if($delete_mode=='all')
	{
		$sql = "DELETE FROM tasks_users_ofdocs_notices WHERE ofdoc_id='$ofdoc_id'";
		
		$site_db->query($sql);
	}
	else if($delete_mode=='by_status')
	{
		$sql = "DELETE FROM tasks_users_ofdocs_notices WHERE ofdoc_id='$ofdoc_id' AND status_id='$status_id'";
		 
		$site_db->query($sql);
	}
	else if($delete_mode=='by_user')
	{
		$sql = "DELETE FROM tasks_users_ofdocs_notices WHERE user_id='$user_id' AND ofdoc_id='$ofdoc_id'";
		
		$site_db->query($sql);
	}
}

// кол-во новых уведомлений
function get_new_ofdocs_statuses_count($user_id, $mode, $ofdoc_id)
{
	global $site_db, $current_user_id;
	
	// все уведомления для пользователя
	if($mode=='all')
	{
		$sql = "SELECT COUNT(*) as count FROM tasks_users_ofdosc i
				RIGHT JOIN tasks_users_ofdocs_notices j ON j.ofdoc_id=i.ofdoc_id
				WHERE j.user_id='$user_id' AND i.deleted!=1";
		
		$row = $site_db->query_firstrow($sql);
		
		$count = $row['count'];
	}
	// уведомления для раздела "мои офдоки"
	else if($mode=='own')
	{
		$sql = "SELECT COUNT(*) as count FROM tasks_users_ofdosc i
				RIGHT JOIN tasks_users_ofdocs_notices j ON j.ofdoc_id=i.ofdoc_id
				WHERE i.user_id='$user_id' AND j.user_id='$user_id' AND i.deleted!=1";
		 
		$row = $site_db->query_firstrow($sql);
		
		$count = $row['count'];
	}
	// уведомления для раздела "документы организации"
	else if($mode=='accessed')
	{
		$sql = "SELECT COUNT(*) as count FROM tasks_users_ofdosc i
				RIGHT JOIN tasks_users_ofdocs_notices j ON j.ofdoc_id=i.ofdoc_id
				LEFT JOIN ".OFDOCS_ACCESS_TB." a ON a.ofdoc_id=j.ofdoc_id
				WHERE i.user_id!='$user_id' AND j.user_id='$user_id' AND a.user_id='$user_id' AND i.deleted!=1";
		 
		$row = $site_db->query_firstrow($sql);
		
		$count = $row['count'];
	}
	// уведомления для конкретного дока
	else if($mode=='ofdoc_id')
	{
		$sql = "SELECT COUNT(*) as count FROM tasks_users_ofdosc i
				RIGHT JOIN tasks_users_ofdocs_notices j ON j.ofdoc_id=i.ofdoc_id
				WHERE j.user_id='$user_id' AND j.ofdoc_id='$ofdoc_id' AND i.deleted!=1";
		 
		$row = $site_db->query_firstrow($sql);
		
		$count = $row['count'];
	}
	
	return $count;
}

?>