<?php
function fill_boss($user_id)
{
	global $site_db, $current_user_id;
	
	$boss_tpl = file_get_contents('templates/boss/boss.tpl');
	
	$notice_item_tpl = file_get_contents('templates/boss/notice_to_add_to_list.tpl');
	
	$notice_to_add_to_list_comment_tpl = file_get_contents('templates/boss/notice_to_add_to_list_comment.tpl');
	
	// Проверяем уведомления о добавление в списки
	/*$sql = "SELECT * FROM  ".WORKERS_TB." i
			LEFT JOIN tasks_users j ON j.user_id=i.invite_user
			WHERE invited_user='$current_user_id' AND invited_user_status=0 AND i.deputy_id = 0";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		
		$PARS_1 = array();
		
		$PARS_1['{INVITE_USER_ID}'] = $row['invite_user'];
		
		$PARS_1['{INVITED_USER_ID}'] = $current_user_id;
		
		$PARS_1['{SURNAME}'] = $row['user_surname'];
		
		$PARS_1['{NAME}'] = $row['user_name'];
		
		$PARS_1['{MIDDLENAME}'] = $row['user_middlename'];
		
		$PARS_1['{USER_POSITION}'] = $row['user_position'];
		
		$PARS_2['{SUB_COMMENT}'] = '';
		
		$comment = '';
		
		if($row['invite_user_comment'])
		{  
			$PARS_2['{COMMENT_TEXT}'] = $row['invite_user_comment'];
			
			$comment = fetch_tpl($PARS_2, $notice_to_add_to_list_comment_tpl);
		}
		
		$PARS_1['{COMMENT}'] =  $comment;
		
		$notice_list .= fetch_tpl($PARS_1, $notice_item_tpl);
	}*/
	
	// Список начальников
	$boss_list = fill_boss_list($user_id);
	
	// Строка навигации
	//$nav = fill_nav('boss');
	
	$PARS['{NAV}'] = $nav;
	
	$PARS['{BOSS_LIST}'] = $boss_list;
	
	$PARS['{NOTICE_LIST}'] = $notice_list;
	
	return fetch_tpl($PARS, $boss_tpl);
}

// Список 
function fill_boss_list($user_id)
{
	global $site_db, $current_user_id, $user_obj, $_CURRENT_USER_DEPUTY_BOSS_ARR;
	
	$no_boss_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/boss/no_boss_list.tpl');
	
	// Список временных начальников
	foreach($_CURRENT_USER_DEPUTY_BOSS_ARR as $boss_id)
	{
		// Формируем массив начальника
		$row['invite_user'] = $boss_id;
		$row['deputy_boss'] = 1;
		$boss_arr[$row['invite_user']] = $row;
	}
	//print_r($deputies_boss_arr);
	
	// прямые начальники
	$boss_arr = get_current_user_users_arrs(array(1,0,0,0,0));
	
	foreach($boss_arr as $boss_id)
	{
		$boss_list .= fill_boss_item($boss_id, 0);
		 
	}
	
	// заместители
	//$boss_arr = get_current_user_users_arrs(array(0,0,0,1,0));
	
	foreach($boss_arr as $boss_id)
	{
		//$boss_list .= fill_boss_item($boss_id, 1);
		 
	}
	
	if(!$boss_list)
	{
		$boss_list = $no_boss_list_tpl;
	}
	return $boss_list;
}

function fill_boss_item($boss_id, $deputy_boss=0)
{
	global $site_db, $current_user_id, $user_obj;
	
	$boss_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/boss/boss_item.tpl');
	
	$deputy_boss_str_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/boss/deputy_boss_str.tpl');
	
	// Если временный начальник
	if($deputy_boss)
	{
		$deputy_boss_str = $deputy_boss_str_tpl;
	}
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($boss_id);
	
	$PARS['{USER_ID}'] = $boss_id;
	
	$PARS['{NAME}'] = $user_obj->get_user_name();

	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
	
	//$PARS['{TASKS_COUNT}'] = get_count_tasks_for_user($boss_id, $current_user_id);
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	 
	$PARS['{AVATAR_SRC}'] = get_user_preview_avatar_src($boss_id, $user_obj->get_user_image());
	
	$PARS['{USER_ONLINE}'] = user_in_online_icon($boss_id);
	
	$PARS['{DEPUTY_BOSS}'] = $deputy_boss_str;
	
	return fetch_tpl($PARS, $boss_item_tpl);
}
// Возвращает кол-во новых начальников
function get_new_boss_count_for_user($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM ".WORKERS_TB." WHERE invited_user='$user_id' AND invited_user_status=0 AND deputy_id = 0";
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
	
}

// Возвращает массив начальников пользователя
function get_user_boss_arr($user_id, $is_deputy)
{
	global $site_db, $current_user_id, $user_obj;
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_org.php';
	
	$boss_arr = array();
	
	$depts_arr = array();
	
	// Выбираем отделы, где пользователь является СОТРУДНИКОМ
	/*$sql = "SELECT * FROM tasks_company_depts_users WHERE is_head=0 AND user_id='$user_id'";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$depts_arr[$row['dept_id']] = $row['dept_id'];
	}*/
	
	
	// Выбираем отделы в которых есть сотрудник
	$sql = "SELECT i.*, j.is_head  FROM tasks_company_depts i
			LEFT JOIN tasks_company_depts_users j ON i.dept_id=j.dept_id
			WHERE j.user_id='$user_id'";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		// если является руководителем отдела, выбираем родительский отдел
		// Иначе выбираем тот отдел, в котором он сотрудник
		if($row['is_head']==1)
		{
			$depts_arr[$row['dept_parent_id']] = $row['dept_parent_id'];
			$parents_depts[$row['dept_parent_id']] = $row['dept_parent_id'];
		}
		else
		{
			$depts_arr[$row['dept_id']] = $row['dept_id'];
		}
		 
	}
	
	// если есть вышестоящие отделы
	if($parents_depts)
	{
		$parents_depts_ids = implode(',', $parents_depts);
		 
		// Выбираем заместителей вышестоящих отделов 
		$sql = "SELECT * FROM tasks_deputies WHERE dept_id IN($parents_depts_ids)"; 
		
		$res = $site_db->query($sql);
			
		while($row=$site_db->fetch_array($res))
		{
			// действующий руководитель отдела
			$dept_actual_user_id = get_head_dept_user_id($row['dept_id']);
			
			// руководитель, который дал заместительство над отделом уже не является текущим руководителем отдела, то пропускаем
			if($dept_actual_user_id != $row['by_user_id'])
			{
				continue;
			}
			
			if($current_user_id==$row['deputy_user_id'])
			{
				continue;
			}
			
			$boss_arr[$row['deputy_user_id']] = $row['deputy_user_id'];
		}
		
		//print_r($deputy_depts_users_arr);
	}
	
	// Если найдены отделы, выбираем руководителей этих отделов	 
	if($depts_arr)
	{
		$depts_ids = implode(',', $depts_arr);
		
		// Выбираем сотрудников отдела
		$sql = "SELECT * FROM tasks_company_depts_users WHERE dept_id IN($depts_ids) AND is_head=1 AND user_id!='$user_id'";
		 
		$res = $site_db->query($sql);
		
		while($row=$site_db->fetch_array($res))
		{
			if($current_user_id==$row['user_id'])
			{
				continue;
			}
			$boss_arr[$row['user_id']] = $row['user_id'];
		}
	}
	 
	//$boss_arr[1] = 1;
	// Выбор заместителей
	// Если у начальника, который является прямым, есть заместитель, то выводим его
	if($is_deputy)
	{
		$deputy_boss_arr = array();
		
		// Если у начальника, который является прямым, есть заместитель
		if($boss_arr)
		{
			$boss_ids = implode(',', $boss_arr);
			
			$sql = "SELECT * FROM tasks_deputies WHERE by_user_id IN($boss_ids)";
			
			$res = $site_db->query($sql);
			
			while($row=$site_db->fetch_array($res))
			{
				if(in_array($row['deputy_user_id'], $boss_arr) || $user_id==$row['deputy_user_id'] || !in_array($row['dept_id'], $depts_arr))
				{
					continue;
				}
				
				$deputy_boss_arr[$row['deputy_user_id']] = $row['deputy_user_id'];
			}
			
			
		}
		
		return $deputy_boss_arr;
	
	}
	
	return $boss_arr;
}

/*// Возвращает массив начальников пользователя
function get_user_boss_arr($user_id, $is_deputy)
{
	global $site_db, $current_user_id, $user_obj;
	
	$boss_arr = array();
	
	if($is_deputy)
	{
		$deputy_user = " AND deputy_id > 0";
	}
	else
	{
		$deputy_user = " AND invited_user_status=1 AND deputy_id = 0";
	}
	
	$sql = "SELECT invite_user FROM ".WORKERS_TB." WHERE invited_user='$user_id' AND deleted = 0 $deputy_user";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$boss_arr[] = $row['invite_user'];
	}
	
	return $boss_arr;
}*/

?>