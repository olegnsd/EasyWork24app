<?php
// Список Моих коллег
function fill_deputy($user_id)
{
	global $site_db, $current_user_id;
	
	$main_tpl = file_get_contents('templates/deputy/deputy.tpl');
	
	$more_btn_tpl = file_get_contents('templates/deputy/more_btn.tpl');
	
	// Страница "Мои замещения"
	if($_GET['my'])
	{
		// Кого замещает пользователь
		$deputy_list = fill_user_deputy_list($user_id);
	}
	else
	{
		// Форма добавления в замы
		$add_form = fill_deputy_add_form();
		// Список заместителей
		$deputy_list = fill_deputy_list($user_id);
	}
	
	// Верхнее меню
	$top_menu = fill_deputy_top_menu();
	
	$PARS['{ADD_FORM}'] = $add_form;
	
	$PARS['{DEPUTY_LIST}'] = $deputy_list;

	$PARS['{TOP_MENU}'] = $top_menu;
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	$PARS['{IS_WKS}'] = $is_wks;
	
	return fetch_tpl($PARS, $main_tpl);
}


// Список заместителей сотрудника
function fill_user_deputy_list($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$no_deputy_tpl = file_get_contents('templates/deputy/no_deputy_for_user.tpl');
	
	// Список заместителей пользователя
	$sql = "SELECT * FROM ".DEPUTY_TB." WHERE deputy_user_id='$user_id' AND deleted<>1 ORDER by deputy_id DESC ";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$deputy_users_list .= fill_deputy_list_item($row);
	}
	
	if(!$deputy_users_list)
	{
		$deputy_users_list = $no_deputy_tpl;
	}
	
	return $deputy_users_list;
}

// Заполнение элемента заместителя
function fill_deputy_list_item($deputy_data)
{
	global $site_db, $current_user_id, $user_obj;
	 
	$deputy_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deputy/deputy_list_item.tpl');
	
	$deputy_confirm_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deputy/deputy_confirm_btn.tpl');
	
	$deputy_delete_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deputy/deputy_delete_btn.tpl');
	
	$not_confirm_str_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deputy/not_confirm_str.tpl');
	
	// название подразделения
	$dept_name = get_dept_name_by_id($deputy_data['dept_id']);
	
	$date_add = datetime($deputy_data['date'], '%j %M в %H:%i');
	
	if(!$deputy_data['deputy_confirm'])
	{
		if($current_user_id==$deputy_data['deputy_user_id'])
		{
			$PARS['{DEPUTY_ID}'] = $deputy_data['deputy_id'];
		
			$confirm_btn = fetch_tpl($PARS, $deputy_confirm_btn_tpl);
		}
		else if($current_user_id==$deputy_data['by_user_id'])
		{
			$not_confirm_str = $not_confirm_str_tpl;
		}
		
		$not_confirm_back = 'not_confirm';
	 
	}
	
	// Для начальника выводим кнопку удалить зама
	if($current_user_id==$deputy_data['by_user_id'])
	{
		$PARS['{DEPUTY_ID}'] = $deputy_data['deputy_id'];
		
		$deputy_delete_btn = fetch_tpl($PARS, $deputy_delete_btn_tpl);	
	}
	
	// Если пользователь является заместителем, показываем босса
	if($current_user_id==$deputy_data['deputy_user_id'])
	{
		$user_id = $deputy_data['by_user_id'];
	}
	else
	{
		$user_id = $deputy_data['deputy_user_id'];
	}
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($user_id);
		
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($user_id, $user_obj->get_user_image());
		
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{DEPT_NAME}'] = $dept_name;
	
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;
			
	$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
	$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
	$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{DEPUTY_ID}'] = $deputy_data['deputy_id'];
	
	$PARS['{DATE_ADD}'] = $date_add;
	
	$PARS['{WORKERS_LIST}'] = $workers_list;
	
	$PARS['{DEPUTY_CONFIRM_BTN}'] = $confirm_btn;
	
	$PARS['{NOT_CONFIRM_BACK}'] = $not_confirm_back;
	
	$PARS['{DEPUTY_DELETE_BTN}'] = $deputy_delete_btn;
	
	$PARS['{NOT_CONFIRM_STR}'] = $not_confirm_str;
	
	$item = fetch_tpl($PARS, $deputy_list_item_tpl);
	
	return $item;
}

// Список заместителей сотрудника
function fill_deputy_list($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$deputy_list_item_tpl = file_get_contents('templates/deputy/deputy_list_item.tpl');
	
	$no_deputy_tpl = file_get_contents('templates/deputy/no_deputy.tpl');
	
	// Список заместителей пользователя
	$sql = "SELECT * FROM ".DEPUTY_TB." WHERE by_user_id = '$current_user_id' AND deleted<>1 ORDER by deputy_id DESC ";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$deputy_users_list .= fill_deputy_list_item($row);
	}
		
	if(!$deputy_users_list)
	{
		$deputy_users_list = $no_deputy_tpl;
	}
	
	return $deputy_users_list;
}

// Кол-во новых замещений
function get_new_deputies($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM ".DEPUTY_TB." WHERE deputy_user_id='$user_id' AND deputy_confirm<>1 AND deleted <> 1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Верхнее меню
function fill_deputy_top_menu()
{
	global $site_db, $current_user_id;
	
	$top_menu_tpl = file_get_contents('templates/deputy/top_menu.tpl');
	
	$top_menu_ext_tpl = file_get_contents('templates/deputy/top_menu_ext.tpl');
	
	// 
	////if($user_workers_arr)
	//{
		$top_menu_ext = $top_menu_ext_tpl;
	//}
	
	if($_GET['my'])
	{
		$active_menu_2 = 'menu_active';
	}
	else
	{
		$active_menu_1 = 'menu_active';
	}
	
	// Кол-во новых уведомлений заместителя
	$new_deputy_count = get_new_deputies($current_user_id);
	$new_deputy_count_for_user = $new_deputy_count ? ' (+ '.$new_deputy_count.')' : '';
	
	$PARS_1['{TOP_MENU_EXT}'] = $top_menu_ext;
	
	$PARS_1['{ACTIVE_1}'] = $active_menu_1;
	
	$PARS_1['{ACTIVE_2}'] = $active_menu_2;
		
	$PARS_1['{NEW_DEPUTY_COUNT}'] = $new_deputy_count_for_user; 
	
	return fetch_tpl($PARS_1, $top_menu_tpl);

}


// Список потрудников, переданные временному заму
function fill_deputy_users_list($deputy_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$deputy_worker_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deputy/deputy_worker_item.tpl');
	
	$no_deputy_workers_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deputy/no_deputy_workers.tpl');
	
	// Выбираем всех пользователей в подчинении зама
	$sql = "SELECT * FROM ".WORKERS_TB." WHERE deputy_id='".$deputy_id."'";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row['invited_user']);
		 
		$PARS['{USER_ID}'] = $row['invited_user'];
		
		$PARS['{AVATAR_SRC}'] = $user_avatar_src;
				
		$PARS['{USER_NAME}'] = $user_obj->get_user_name();
			
		$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
				
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
				
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$deputy_users_list .= fetch_tpl($PARS, $deputy_worker_item_tpl);
	}
	
	if(!$deputy_users_list)
	{
		$deputy_users_list = $no_deputy_workers_tpl;
	}
	
	return $deputy_users_list;
}

function get_deputy_depts_users_list($dept_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$deputy_users_list_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deputy/deputy_users_list_block.tpl');
	
	$option_tag_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	$sql = "SELECT * FROM tasks_company_depts_users WHERE dept_id='$dept_id' AND user_id != '$current_user_id' AND is_head=0 ";
	
	$res = $site_db->query($sql);
				 
	while($row=$site_db->fetch_array($res, 1))
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row['user_id']);
		
		$name = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename().' '.$user_obj->get_user_position();
		
		// Список для выбора подчиненных в замы
		$PARS['{VALUE}'] = $row['user_id'];
		$PARS['{NAME}'] = $name;
		$PARS['{SELECTED}'] = '';
		
		$users_list .= fetch_tpl($PARS, $option_tag_tpl);
	}
	
	$PARS['{USERS_LIST}'] = $users_list;
	
	return fetch_tpl($PARS, $deputy_users_list_block_tpl);
}

// Форма добавления заместителей
function fill_deputy_add_form()
{
	global $site_db, $current_user_id, $_CURRENT_USER_WORKERS_ARR, $_CURRENT_USER_COLLEAGUES_ARR, $user_obj;
	
	$add_form_tpl = file_get_contents('templates/deputy/add_form.tpl');
	
	$no_add_deputy_tpl = file_get_contents('templates/deputy/no_add_deputy.tpl');
	
	$option_tag_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	$option_disabled_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_disabled.tpl');
	
	$users_for_select_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/deputy/users_for_select_item.tpl');
	
	 
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_worktime.php';
	
	$user_depts = get_user_depts($current_user_id, 1);	
 
	foreach($user_depts as $dept_id)
	{   
		// Список для выбора подчиненных в замы
		$PARS['{VALUE}'] = $dept_id;
		$PARS['{NAME}'] = get_dept_name_by_id($dept_id);
		$PARS['{SELECTED}'] = '';
		$depts_list .= fetch_tpl($PARS, $option_tag_tpl); 
	}
	
	// если нет отделов
	if(!$depts_list)
	{
		return $no_add_deputy_tpl;
	}
	
	// Список подчиненных
	foreach($_CURRENT_USER_WORKERS_ARR as $user_id)
	{
		$user_obj->fill_user_data($user_id);
		
		$user_name = $user_obj->get_user_name();
		
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_surname = $user_obj->get_user_surname();
		
		$user_position = $user_obj->get_user_position();
		
		// Список для выбора подчиненных в замы
		$PARS['{VALUE}'] = $user_id;
		$PARS['{NAME}'] = $user_surname.' '.$user_name.' '.$user_middlename;
		$PARS['{SELECTED}'] = '';
		$workers_list .= fetch_tpl($PARS, $option_tag_tpl);
		
		
		// Список для выбора подчиненных
		$PARS['{USER_ID}'] = $user_id;
		$PARS['{SURNAME}'] = $user_surname;
		$PARS['{NAME}'] = $user_name;
		$PARS['{MIDDLENAME}'] = $user_middlename;
		$PARS['{USER_POSITION}'] = $user_position;
		$workers_for_select_list .= fetch_tpl($PARS, $users_for_select_item_tpl);
	}
	
	if($workers_list)
	{
		$PARS['{VALUE}'] = '';
		$PARS['{NAME}'] = 'Мои сотрудники';
		$PARS['{SELECTED}'] = '';
		$workers_list = fetch_tpl($PARS, $option_disabled_tpl).$workers_list;
	}
	
	$users_list = $colleagues_list.$workers_list;
	
	$PARS['{USERS_LIST}'] = $users_list;
	
	$PARS['{WORKERS_FOR_SELECT}'] = $workers_for_select_list;
	
	$PARS['{DEPTS_LIST}'] = $depts_list;
	
	return fetch_tpl($PARS, $add_form_tpl);
	
}

// Список сотрудников, которые передаются в подчинение
function get_deputies_workers_arr($user_id)
{
	global $site_db, $current_user_id;
	
	// Выбор замещений для пользователя
	$sql = "SELECT * FROM ".DEPUTY_TB." WHERE deputy_user_id='$user_id' AND deleted<>1 AND deputy_confirm=1";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$deputies_ids_arr[] = $row['deputy_id'];
	}
	if($deputies_ids_arr)
	{
		$deputies_ids = implode(',', $deputies_ids_arr);
	}
	
	if(!$deputies_ids)
	{
		return array();
	}
	
	$deputies_workers_arr = array();
	
	// Выбираем пользователей, которых передали заместителю
	$sql = "SELECT * FROM ".WORKERS_TB." WHERE deputy_id IN($deputies_ids)";
	 
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$deputies_workers_arr[$row['invited_user']] = $row['invited_user'];
	}
	 
	return $deputies_workers_arr;
}

// Список временных начальников
function get_deputies_boss_arr($user_id)
{
	global $site_db, $current_user_id;
	
	$deputies_boss_ids_arr = array();
	
	// Выбор замещений для пользователя
	$sql = "SELECT i.deputy_user_id FROM ".DEPUTY_TB." i
			RIGHT JOIN ".WORKERS_TB." j ON i.deputy_id=j.deputy_id
			WHERE j.invited_user='$user_id' AND i.deleted<>1 AND i.deputy_confirm=1 AND j.deputy_id > 0";
	
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		$deputies_boss_ids_arr[] = $row['deputy_user_id'];
	}
	
	return $deputies_boss_ids_arr;
}

?>