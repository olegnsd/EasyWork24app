<?php
function fill_planning($user_id)
{
	global $site_db, $current_user_id, $_CURRENT_USER_BOSS_ARR;
	
	$main_tpl = file_get_contents('templates/planning/planning.tpl');
	
	$top_menu_tpl = file_get_contents('templates/planning/top_menu.tpl');
	
	$top_menu_workers_tpl = file_get_contents('templates/planning/top_menu_workers.tpl');
	
	$more_btn_tpl = file_get_contents('templates/planning/more_btn.tpl');
	
	// Очистка массива удаленных
	if($_SESSION['planning_delete'])
	{
		$_SESSION['planning_delete'] = '';
	}
	
	// Подчиненные пользователя
	$user_workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	
	// Если у пользователя нет сотрудников, не выводим раздел "Клиенты моих сотурдников"
	if($user_workers_arr)
	{
		$workers_clients_top_menu = $top_menu_workers_tpl;
	}
	
	if($_GET['others'])
	{
		$active_menu_2 = 'menu_active';
	}
	else
	{
		if($_CURRENT_USER_BOSS_ARR)
		{
			//worker_noticed_planning_result($user_id);
		}
		$active_menu_1 = 'menu_active';
	}
	
	// Кол-во уведомлений
	//$new_planning_count_for_boss = get_new_user_planning_count_for_boss($current_user_id);
	//$new_planning_count_for_workers = get_new_user_planning_count_for_workers($current_user_id);
	$new_planning_count_for_others = get_new_user_planning_count_for_others($current_user_id);
	
	//$new_planning_count_for_boss = $new_planning_count_for_boss ? ' (+ '.$new_planning_count_for_boss.')' : '';
	$new_planning_count_for_others = $new_planning_count_for_others ? ' (+ '.$new_planning_count_for_others.')' : '';
	
	$PARS_1['{WORKERS_PLANNING_TOP_MENU}'] = $workers_clients_top_menu;
	
	$PARS_1['{ACTIVE_1}'] = $active_menu_1;
	
	$PARS_1['{ACTIVE_2}'] = $active_menu_2;
	
	$PARS_1['{NEW_COUNT_FOR_WORKER}'] = $new_planning_count_for_workers; 
	
	$PARS_1['{NEW_COUNT_FOR_BOSS}'] = $new_planning_count_for_boss;
	
	$PARS_1['{NEW_COUNT_FOR_OTHERS}'] = $new_planning_count_for_others; 
	
	$top_menu = fetch_tpl($PARS_1, $top_menu_tpl);
	
	
	// Выбираем последнее добавленное имущество
	$sql = "SELECT planning_id FROM ".PLANNING_TB." WHERE deleted<>1 ORDER by planning_id DESC LIMIT 1";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['planning_id'])
	{
		$_SESSION['last_planning_id'] = $row['planning_id'];
	}
	
	if(!$_GET['others'])
	{
		$add_form = fill_planning_add_form($current_user_id);
	}
	
	if($_GET['others'])
	{
		// Список планирования
		$planning_list = fill_planning_list($current_user_id, 1, 1);
		
		$planning_count = get_user_planning_count($current_user_id, 1);
	}
	else
	{
		// Список планирования
		$planning_list = fill_planning_list($current_user_id);
		
		$planning_count = get_user_planning_count($current_user_id);
	}
	
	// Кол-во страниц
	$pages_count = ceil($planning_count/PLANNING_PER_PAGE);
		
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_btn = $more_btn_tpl;
	}
		 
	
	$PARS['{PLANNING_LIST}'] = $planning_list;
	
	$PARS['{TOP_MENU}'] = $top_menu;
	
	$PARS['{ADD_FORM}'] = $add_form;
	
	$PARS['{MORE_PLANNING}'] = $more_btn;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{OTHERS}'] = $_GET['others']; 

	return fetch_tpl($PARS, $main_tpl);
	
}

// Кол-во планирований у пользователя
function get_user_planning_count($user_id, $planning_workers = 0)
{
	global $site_db, $current_user_id;
	
	if(!$planning_workers)
	{
		// Список планирования
		$sql = "SELECT COUNT(*) as count FROM ".PLANNING_TB." i WHERE i.user_id='$user_id' AND i.deleted<>1";
	}
	else
	{
		// Список планирования
		$sql = "SELECT COUNT(*) as count FROM ".PLANNING_TB." i
				LEFT JOIN tasks_users_planning_users j ON i.planning_id=j.planning_id
				WHERE j.user_id='$user_id' AND deleted<>1";
	}
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Список планирования
function fill_planning_list($user_id, $page=1, $planning_workers = 0)
{
	global $site_db, $current_user_id;
	
	$no_planning_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/planning/no_planning.tpl');
	
	// Страничность
	$begin_pos = PLANNING_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".PLANNING_PER_PAGE;
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_planning_id'])
	{
		$and_planning_id = " AND planning_id <= '".$_SESSION['last_planning_id']."' ";
	}
	
	// Удаленные в этой сессии клиенты
	$deleted_planning_ids = implode(', ', $_SESSION['planning_delete']);
	
	if($deleted_planning_ids)
	{
		$and_deleted_planning = " OR i.planning_id IN($deleted_planning_ids) ";
	}
	
	if(!$planning_workers)
	{
		// Список планирования
		$sql = "SELECT * FROM ".PLANNING_TB." i
				WHERE i.user_id='$user_id' AND (i.deleted<>1 $and_deleted_planning) ORDER by i.planning_id DESC $limit";
	}
	else
	{
		// Список планирования
		$sql = "SELECT i.*, j.noticed FROM ".PLANNING_TB." i
				LEFT JOIN tasks_users_planning_users j ON i.planning_id=j.planning_id
				WHERE j.user_id='$user_id' AND (i.deleted<>1 $and_deleted_planning) ORDER by i.planning_id DESC $limit";
	}
	 
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$planning_list .= fill_planning_list_item($row);
	}
	
	if(!$planning_list)
	{
		$planning_list = $no_planning_tpl;
	}
	
	return $planning_list;
}

// Элемент планирования
function fill_planning_list_item($planning_data, $planning_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$planning_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/planning/planning_list_item.tpl');
	
	$edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/planning/edit_tools.tpl');
	
	$dates_one_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/planning/dates_one_item.tpl');
	
	$dates_period_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/planning/dates_period_item.tpl');
	
	$planning_confirm_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/planning/planning_confirm_btn.tpl');
	
	$planning_confirm_workers_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/planning/planning_confirm_workers_btn.tpl');
	
	$planning_for_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/planning/planning_for_list.tpl');
	
	  
	if(!$planning_data)
	{
		$sql = "SELECT * FROM ".PLANNING_TB." WHERE planning_id='$planning_id'";
		
		$planning_data = $site_db->query_firstrow($sql);
	}
	$date_add = datetime($planning_data['date'], '%j %M в %H:%i');
	
	// Если просматривает тот
	if($planning_data['user_id'] == $current_user_id)
	{
		$PARS_1['{PLANNING_ID}'] = $planning_data['planning_id'];
		
		$edit_tools = fetch_tpl($PARS_1, $edit_tools_tpl);
	}
	
	// Выбираем даты планирования
	$sql = "SELECT * FROM ".PLANNING_DATES_TB." WHERE planning_id='".$planning_data['planning_id']."'";
	
	$res = $site_db->query($sql);
			 
	while($row=$site_db->fetch_array($res))
	{
		if(!$row['date_is_period'])
		{
			if((int)datetime($row['date_one'], '%H') ||(int)datetime($row['date_one'], '%i'))
			{
				$PARS['{DATE_ONE}'] = datetime($row['date_one'], '%d.%m.%Y %H:%i');
			}
			else
			{
				$PARS['{DATE_ONE}'] = datetime($row['date_one'], '%d.%m.%Y');
			}
			
			$dates_one_list .= fetch_tpl($PARS, $dates_one_item_tpl);
			 
		}
		else
		{
			if((int)datetime($row['date_from'], '%H') || (int)datetime($row['date_from'], '%i'))
			{
				$PARS['{DATE_FROM}'] = datetime($row['date_from'], '%d.%m.%Y %H:%i');
			}
			else
			{
				$PARS['{DATE_FROM}'] = datetime($row['date_from'], '%d.%m.%Y');
			}
			
			if((int)datetime($row['date_to'], '%H') || (int)datetime($row['date_to'], '%i'))
			{
				$PARS['{DATE_TO}'] = datetime($row['date_to'], '%d.%m.%Y %H:%i');
			}
			else
			{
				$PARS['{DATE_TO}'] = datetime($row['date_to'], '%d.%m.%Y');
			}
			
			 
			$dates_period_list .= fetch_tpl($PARS, $dates_period_item_tpl);
		}
	}
	
	$planning_dates = $dates_one_list.$dates_period_list;
	
	if($planning_data['type_str'])
	{
		$planning_type = $planning_data['type_str'];
	}
	else
	{
		// Название типа планирования 
		$planning_type = get_planning_type_name_by_planning_type_id($planning_data['type_id']);
	}
	
	
	// Для начальника выводим кнопки подтверждения, если есть новые неутвержденные запросы планирования
	if($planning_data['planning_for']==1 && $planning_data['noticed']==0 && $planning_data['user_id']!=$current_user_id)
	{
		$not_confirm = 'not_confirm';
		
		$PARS_1['{PLANNING_ID}'] = $planning_data['planning_id'];
		
		// Кнопки подтверждения или отмены для начальника
		$planning_confirm_btn = fetch_tpl($PARS_1, $planning_confirm_btn_tpl);
		
	}
	// Для начальника выводим кнопки подтверждения, если есть новые неутвержденные запросы планирования
	if($planning_data['planning_for']==2 && $planning_data['noticed']==0 && $planning_data['user_id']!=$current_user_id)
	{
		$not_confirm = 'not_confirm';
		
		$PARS_1['{PLANNING_ID}'] = $planning_data['planning_id'];
		
		// Кнопки подтверждения или отмены для начальника
		$planning_confirm_btn = fetch_tpl($PARS_1, $planning_confirm_workers_btn_tpl);
		
	}
	
	if($planning_data['planning_for']==2)
	{
		$depts = split(',', $planning_data['depts']);
		
		foreach($depts as $dept_id)
		{
			$depts_str[] = get_dept_name_by_id($dept_id);	
		}
		
		$PARS['{DEPTS_LIST}'] = implode(', ', $depts_str);
		
		$planning_for_list = fetch_tpl($PARS, $planning_for_list_tpl);
	}
	
	
	
	// Возвращает статус планирования в виде строки
	$planning_result = fill_planning_result($planning_data);
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($planning_data['user_id']);
	
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($planning_data['user_id'], $user_obj->get_user_image());
		
	$PARS['{USER_ID}'] = $planning_data['user_id'];
	
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;
			
	$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
	$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
	$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{PLANNING_ID}'] = $planning_data['planning_id'];
	
	$PARS['{DATE_ADD}'] = $date_add;
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	$PARS['{PLANNING_CONFIRM_BTN}'] = $planning_confirm_btn;
	
	$PARS['{PLANNING_DATES}'] = $planning_dates;
	
	$PARS['{PLANNING_TYPE}'] = $planning_type;
	
	$PARS['{PLANNING_NOT_CONFIRM}'] = $not_confirm;
	
	$PARS['{PLANNING_RESULT}'] = $planning_result;
	
	$PARS['{PLANNING_FOR_LIST}'] = $planning_for_list;
	
	return fetch_tpl($PARS, $planning_list_item_tpl);
}

// Блок результата планирвоания
function fill_planning_result($planning_data, $planning_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$planning_confirm_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/planning/planning_confirm.tpl');
	
	$planning_not_confirm = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/planning/planning_not_confirm.tpl');
	
	if(!$planning_data)
	{
		$sql = "SELECT * FROM ".PLANNING_TB." WHERE planning_id='$planning_id'";
		
		$planning_data = $site_db->query_firstrow($sql);
	}
	
	// Подтверждено
	if($planning_data['planning_result']==1)
	{
		return $planning_confirm_tpl;
	}
	// Отменено
	if($planning_data['planning_result']==2)
	{
		return $planning_not_confirm;
	}
}

// Название типа планирования
function get_planning_type_name_by_planning_type_id($type_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "SELECT type_name FROM ".PLANNING_TYPES_TB." WHERE type_id='$type_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['type_name'];
}

// Форма добавления планирования
function fill_planning_add_form($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$user_boss_arr = get_current_user_users_arrs(array(1,0,0,1,0));
	
	if(!$user_boss_arr)
	{
		//return '';
	}
	
	$add_form_tpl = file_get_contents('templates/planning/add_form.tpl');
	
	$add_form_to_boss_tpl = file_get_contents('templates/planning/add_form_to_boss.tpl');
	
	$add_form_to_workers_tpl = file_get_contents('templates/planning/add_form_to_workers.tpl');
	
	$option_tag_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	// Типы планирований
	$sql = "SELECT * FROM ".PLANNING_TYPES_TB."";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$PARS['{VALUE}'] = $row['type_id'];
		
		$PARS['{NAME}'] = $row['type_name'];
		
		$PARS['{SELECTED}'] = '';
		
		$types_list .= fetch_tpl($PARS, $option_tag_tpl);
	}
	
	foreach($user_boss_arr as $boss_id)
	{
		$user_obj->fill_user_data($boss_id);
		
		$user_name = $user_obj->get_user_name();
		
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_surname = $user_obj->get_user_surname();
		
		$user_position = $user_obj->get_user_position();
		
		$PARS['{VALUE}'] = $boss_id;
		
		$PARS['{NAME}'] = $user_surname.' '.$user_name.' '.$user_middlename;
		
		$PARS['{SELECTED}'] = '';
		
		$boss_list .= fetch_tpl($PARS, $option_tag_tpl);
	}
	
	if(!$boss_list)
	{
		$PARS['{VALUE}'] = 0;
		$PARS['{NAME}'] = '- У вас нет руководителя -';
		$PARS['{SELECTED}'] = '';
		$boss_list .= fetch_tpl($PARS, $option_tag_tpl);
	}
	
	$PARS['{PLANNING_TYPE_LIST}'] = $types_list;
	$PARS['{BOSS_LIST}'] = $boss_list;
	$form_for_boss = fetch_tpl($PARS, $add_form_to_boss_tpl);
	
	
	// уведомить управление
	$user_depts = get_user_depts($current_user_id,0,1);	
  
	foreach($user_depts as $dept_id)
	{   
		// Список для выбора подчиненных в замы
		$PARS['{VALUE}'] = $dept_id;
		$PARS['{NAME}'] = get_dept_name_by_id($dept_id);
		$PARS['{SELECTED}'] = '';
		$depts_list .= fetch_tpl($PARS, $option_tag_tpl); 
	}
	
	if(!$depts_list)
	{
		$PARS['{VALUE}'] = 0;
		$PARS['{NAME}'] = '- Подразделений нет';
		$PARS['{SELECTED}'] = ''; 
		$depts_list = fetch_tpl($PARS,  file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_disabled.tpl')); 
	}
	
	$PARS['{DEPTS_LIST}'] = $depts_list; 
	$form_for_workers = fetch_tpl($PARS, $add_form_to_workers_tpl);
	 
	
	$PARS['{FORM_FOR_BOSS}'] = $form_for_boss;
	
	$PARS['{FORM_FOR_WORKERS}'] = $form_for_workers;
	
	return fetch_tpl($PARS, $add_form_tpl);
}

// Подсчет новых уведомлений для начальника
function get_new_user_planning_count_for_boss($user_id)
{
	global $site_db, $current_user_id;
	
	// Массив пользователей, с которыми контактирует пользователь
	$users_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	
	if($users_arr)
	{
		$and_users_from = " AND user_id IN(".implode(',', $users_arr).")";
	}
	
	$sql = "SELECT COUNT(*) as count FROM ".PLANNING_TB." WHERE boss_id='$user_id' AND planning_result = 0 AND deleted <> 1 $and_users_from";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];			
}

// Подсчет новых откликов для подчиненных
function get_new_user_planning_count_for_workers($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM ".PLANNING_TB." WHERE user_id='$user_id' AND planning_result_noticed=0 AND planning_result IN(1,2) AND deleted <> 1";
 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];			
}

// Подсчет новых откликов для подчиненных
function get_new_user_planning_count_for_others($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM ".PLANNING_TB." i
			LEFT JOIN tasks_users_planning_users j ON i.planning_id=j.planning_id
			WHERE j.user_id='$user_id' AND j.noticed=0 AND i.deleted <> 1";
 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];			
}

// Снять все уведомления для подчиненного по планированию, если таковы имеются
function worker_noticed_planning_result($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "UPDATE ".PLANNING_TB." SET planning_result_noticed=1 WHERE user_id='$user_id' AND planning_result IN(1,2)";
	
	$site_db->query($sql);
}
?>