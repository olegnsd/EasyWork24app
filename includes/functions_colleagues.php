<?php
// Список Моих коллег
function fill_colleagues($user_id)
{
	global $site_db, $current_user_id;
	
	$colleagues_tpl = file_get_contents('templates/colleagues/colleagues.tpl');
	
	$colleague_add_form_tpl = file_get_contents('templates/colleagues/colleague_add_form.tpl');
	
	$colleague_add_form = $colleague_add_form_tpl;
	
	// Список сотрудников
	$colleagues_list = fill_colleagues_list($current_user_id);
	
	$PARS['{COLLEAGUE_ADD_FORM}'] = $colleague_add_form;
	
	$PARS['{COLLEAGUES_LIST}'] = $colleagues_list;
	
	return fetch_tpl($PARS, $colleagues_tpl);
}

// Список коллег пользователя
function fill_colleagues_list($user_id)
{
	global $site_db, $current_user_id;
	
	$no_colleagues_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/colleagues/no_colleagues.tpl');
	
	// Выбираем всех коллег
	$sql = "SELECT * FROM ".COLLEAGUES_TB." WHERE ((invite_user_id='$user_id') OR (invited_user_id='$user_id' AND invited_user_status IN (0,1))) AND  colleague_deleted<>1";
	 
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$colleagues_list .= fill_colleagues_list_item($row);
	}
	
	if(!$colleagues_list)
	{
		$colleagues_list = $no_colleagues_tpl;
	}

	return $colleagues_list;
}

// 
function fill_colleagues_list_item($colleague_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$colleague_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/colleagues/colleague_item.tpl');
	
	$colleague_not_confirm_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/colleagues/colleague_not_confirm_item.tpl');
	
	$colleague_reject_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/colleagues/colleague_reject_item.tpl');
	
	$colleague_new_to_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/colleagues/colleague_new_to_list_item.tpl');
	
	$worker_is_working_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/worker_is_working.tpl');
	
	if($colleague_data['invited_user_id']==$current_user_id)
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($colleague_data['invite_user_id']);
		
		$user_id = $colleague_data['invite_user_id'];
	}
	else
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($colleague_data['invited_user_id']);
		
		$user_id = $colleague_data['invited_user_id'];
	}
	
	// Онлайн иконка
	$user_is_online = user_in_online_icon($user_id, $user_obj->get_user_last_visit_date());
	
	// Проверяем последний статус ("Начал работь" или "Работаь закончил") за дату
	$user_last_status = get_last_user_activity_status($user_id);
	
	// Работает
	if($user_last_status==1)
	{
		$user_is_working = $worker_is_working_tpl;
	}
	
	if($user_is_online && $user_is_working)
	{
		$user_is_online .= ' | ';
	}
	else if($user_last_activity_block && $user_is_working)
	{
		$user_is_working .= '<br>';
	}
	
	
	$PARS['{USER_ID}'] = $user_id;
	 
	$PARS['{INVITE_USER_ID}'] = $colleague_data['invite_user_id'];
	
	$PARS['{INVITED_USER_ID}'] = $colleague_data['invited_user_id'];
		
	$PARS['{JOB_ID}'] = $user_obj->get_user_job_id();
		
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{AVATAR_SRC}'] = get_user_preview_avatar_src($user_id, $user_obj->get_user_image());
	
	$PARS['{USER_ONLINE}'] = $user_is_online;
	
	$PARS['{USER_IS_WORKING}'] = $user_is_working;
	
	// Если просматривает пользователь, которого приглашали в коллеги
	if($colleague_data['invited_user_id']==$current_user_id)
	{
		// Неподтвержденный шаблон
		if($colleague_data['invited_user_status']==0)
		{
			$item_tpl = $colleague_new_to_list_item_tpl;
		}
		else
		{
			$item_tpl = $colleague_item_tpl;
		}
	}
	else
	{
		// Неподтвержденный шаблон
		if($colleague_data['invited_user_status']==0)
		{
			$item_tpl = $colleague_not_confirm_item_tpl;
		}
		// Отклоненная заявка на добавление, шаблон
		else if($colleague_data['invited_user_status']==2)
		{
			$item_tpl = $colleague_reject_item_tpl;
		}
		else
		{
			$item_tpl = $colleague_item_tpl;
		}
	}
	return fetch_tpl($PARS, $item_tpl);
	
}

// Кол-во новых запросов на добавление в коллеги
function get_new_user_colleagues_count($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "SELECT COUNT(*) as count FROM ".COLLEAGUES_TB." WHERE invited_user_id='$user_id' AND invited_user_status = 0 AND colleague_deleted<>1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}


// Массив коллег пользователя
function get_user_colleagues_arr($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$colleagues_arr = array();
	
	// Выбираем отделы, где пользователь является руководителем
	$sql = "SELECT * FROM tasks_company_depts_users WHERE is_head=0 AND user_id='$user_id'";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$depts_arr[] = $row['dept_id'];
	}
	
	if($depts_arr)
	{
		$depts_ids = implode(',', $depts_arr);
		
		// Выбираем сотрудников отдела
		$sql = "SELECT * FROM tasks_company_depts_users WHERE is_head=0 AND dept_id IN($depts_ids) AND user_id!='$user_id'";
		
		$res = $site_db->query($sql);
		
		while($row=$site_db->fetch_array($res))
		{
			$colleagues_arr[$row['user_id']] = $row['user_id'];
		}
	} 
		
	//echo "<pre>", print_r($colleagues_arr);
	
	return $colleagues_arr;
}

/*// Массив коллег пользователя
function get_user_colleagues_arr($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$colleagues_arr = array();
	
	// Список всех коллег
	$sql = "SELECT * FROM ".COLLEAGUES_TB." 
			WHERE (invite_user_id='$user_id' OR invited_user_id='$user_id') AND invited_user_status=1 AND colleague_deleted<>1";
			
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res, 1))
	{
		if($row['invited_user_id']==$user_id)
		{
			$colleagues_arr[] = $row['invite_user_id'];
		}
		else
		{
			$colleagues_arr[] = $row['invited_user_id'];
		}
	}
	
	return $colleagues_arr;
}*/

// Проверка на то, что два человека являются коллегами
function is_users_colleagues($user_id_1, $user_id_2)
{
	global $site_db, $current_user_id;
 
	$sql = "SELECT * FROM ".COLLEAGUES_TB." WHERE ((invite_user_id='$user_id_1' AND invited_user_id='$user_id_2') 
			OR (invite_user_id='$user_id_2' AND invited_user_id='$user_id_1')) AND invited_user_status=1 AND colleague_deleted<>1";
			
	$row = $site_db->query_firstrow($sql);
	
	if($row['id'])
	{
		return true;
	}
	else
	{
		return false;
	}
}
?>