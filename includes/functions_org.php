<?php
function fill_structure()
{
	global $site_db, $current_user_id, $depts_list, $current_user_obj;
	
	$main_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/structure.tpl');
	
	// Для админа
	if($current_user_obj->get_is_admin())
	{
		// форма добавления отдела
		$add_form = fill_structure_add_form();
	}
	
	// Схема
	$scheme = fill_structure_scheme(1); 
	
	$PARS['{ADD_FORM}'] = $add_form;
	
	$PARS['{SCHEME}'] = $scheme;

	return fetch_tpl($PARS, $main_tpl);
}

// Данные по отделу
function structure_get_dept_data($depts_arr)
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	$dept_item_cont_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/dept_item_cont.tpl');
	
	$dept_item_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/dept_item_edit_tools.tpl');
	$dept_item_edit_tools_delete_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/dept_item_edit_tools_delete_btn.tpl');
		
	foreach($depts_arr as $dept_id)
	{  
		// данные отдела
		$sql = "SELECT * FROM tasks_company_depts WHERE dept_id='$dept_id'"; 
		
		$dept_data = $site_db->query_firstrow($sql);
		
		// Блок руководителя
		$head_block = fill_dept_head_block($dept_id);
		
		// Блок сотрудников
		$workers_block = fill_dept_workers_block($dept_id);
		
		$dept_edit_tools = '';
		
		// Блок редактирования для админа
		if($current_user_obj->get_is_admin())
		{
			if($dept_id!=1)
			{
				$PARS['{DELETE_BTN}'] = $dept_item_edit_tools_delete_btn_tpl;
			}
			else
			{
				$PARS['{DELETE_BTN}'] = '';
			}
			$PARS['{DEPT_ID}'] = $dept_data['dept_id'];
			$PARS['{DEPT_NAME}'] = $dept_data['dept_name'];
			$dept_edit_tools = fetch_tpl($PARS, $dept_item_edit_tools_tpl);
		}
		
		$PARS['{DEPT_ID}'] = $dept_data['dept_id'];
		
		$PARS['{DEPT_NAME_S}'] = strlen($dept_data['dept_name']) > 20 ? substr($dept_data['dept_name'], 0, 20).'...' : $dept_data['dept_name'];
		
		$PARS['{DEPT_NAME}'] = $dept_data['dept_name'];
		
		$PARS['{HEAD_BLOCK}'] = $head_block;
		
		$PARS['{WORKERS_BLOCK}'] = $workers_block;
		
		$PARS['{DEPT_EDIT_TOOLS}'] = $dept_edit_tools;
	
		$dept_item_arr[$dept_id]['data'] = iconv('cp1251', 'utf-8', fetch_tpl($PARS, $dept_item_cont_tpl));
		
	}
	 
	return $dept_item_arr;
}

// Блок сотрудников отдела
function fill_dept_workers_block($dept_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$dept_item_cont_workers_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/dept_item_cont_workers_block.tpl');
	
	$sql = "SELECT COUNT(*) as count FROM tasks_company_depts_users WHERE dept_id='$dept_id' AND is_head=0";
	 
	$row = $site_db->query_firstrow($sql);
	
	if($row['count']>0)
	{
		$PARS['{DEPT_ID}'] = $dept_id;
		
		$PARS['{COUNT}'] = $row['count'];
		
		$PARS['{STR}'] = numToword($row['count'], array('сотрудник', 'сотрудника', 'сотрудников'));
	 
		return fetch_tpl($PARS, $dept_item_cont_workers_block_tpl);
	}
}

// список сотрудников
function fill_dept_workers_list_in_structure($dept_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$structure_user_workers_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/structure_user_workers_item.tpl');
	
	$sql = "SELECT * FROM tasks_company_depts_users WHERE dept_id='$dept_id' AND is_head=0 ";
	
	$res = $site_db->query($sql);
				 
	while($row=$site_db->fetch_array($res, 1))
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row['user_id']);
		$user_name = $user_obj->get_user_name();
		$user_middlename = $user_obj->get_user_middlename();
		
		$PARS['{AVATAR_SRC}'] = get_user_preview_avatar_src($row['user_id'], $user_obj->get_user_image());
		
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
		$PARS['{USER_NAME}'] = $user_name[0].'.';
		$PARS['{USER_MIDDLENAME}'] = $user_middlename[0].'.';
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		$PARS['{USER_ID}'] = $row['user_id'];
		
		
		$users_list .= fetch_tpl($PARS, $structure_user_workers_item_tpl);
	}
	
	return $users_list;
}

// Блок руководителя
function fill_dept_head_block($dept_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$dept_item_cont_head_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/dept_item_cont_head_block.tpl');


	// Руководитель отдела
	$head_dept_user = get_head_dept_user_id($dept_id);
	
	if($head_dept_user)
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($head_dept_user);
		$user_name = $user_obj->get_user_name();
		$user_middlename = $user_obj->get_user_middlename();
		
		$PARS['{AVATAR_SRC}'] = get_user_preview_avatar_src($head_dept_user, $user_obj->get_user_image());
		
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
		$PARS['{USER_NAME}'] = $user_name[0].'.';
		$PARS['{USER_MIDDLENAME}'] = $user_middlename[0].'.';
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		$PARS['{USER_ID}'] = $head_dept_user;
		
		
		$head_block = fetch_tpl($PARS, $dept_item_cont_head_block_tpl);
	}
	
	return $head_block;
}

// Руководитель отдела
function get_head_dept_user_id($dept_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT user_id FROM tasks_company_depts_users WHERE dept_id='$dept_id' AND is_head=1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['user_id'];
}

// Список Моих коллег
function fill_structure_scheme($dept_id)
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	$main_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/scheme.tpl');
	
	$grhy_visible_cont_menu_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/grhy_visible_cont_menu.tpl');

	
	$dept_row_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/dept_row.tpl');
	$dept_group_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/dept_group.tpl');
	$dept_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/dept_item.tpl');
	$dept_group_sep_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/dept_group_sep.tpl');
	

	// Первоначально запихиваем переданного пользователя в массив пользователей
	$depts_arr[] = $dept_id;
	
	$checked_depts_arr = array();
	
	$i = 1;
	
	$result[$i][] = array($dept_id);
	 
	// Делаем цикл, и формируем массив дерева подчиненных
	while(!$stop && $dept_id)
	{
		$i++;
		
		$depts_ids = '';
		
		if($depts_arr)
		{
			$depts_not_checked_arr = array_diff($depts_arr, $checked_depts_arr);
			
			$checked_depts_arr = array_merge($checked_depts_arr, $depts_arr);
		 
		 	// Формируем список для запроса
			$depts_ids = implode(',', $depts_not_checked_arr);
		
			// Обнуляем массив пользователей
			$depts_arr = array();
			$dept_link = array();
		}
			
		if($depts_ids)
		{
			$sql = "SELECT * FROM tasks_company_depts WHERE dept_parent_id IN($depts_ids)";  
			$res = $site_db->query($sql);
				 
			while($row=$site_db->fetch_array($res, 1))
			{
				$depts_arr[] = $row['dept_id'];
				$dept_link[$row['dept_parent_id']][] = $row;
			}
		}
		
		$result[$i] = $dept_link;
		 
		// Если пройдено вниз по дереву подчиненных, останавливаем цикл
		if(!$depts_arr || $num>100)
		{  
			$stop = true;
		}
		 
		$num++; 
	}
	
	// Проходим по дереву подчиненных 
	foreach($result as $i => $dept_subdata)
	{
		  
		$depts_group = array();
		$depts_count = 0;
		$depts_group_count = 0;
		 
		foreach($dept_subdata as $dept_pid => $depts_childs)
		{ 
			$dept_item_arr = array();
			
			foreach($depts_childs as $dept_data)
			{	
				$PARS['{DEPT_ID}'] = $dept_data['dept_id'];
				$PARS['{NAME}'] = $dept_data['dept_name'];
				
				$dept_item_arr[$dept_data['dept_id']] = fetch_tpl($PARS, $dept_item_tpl);
				 
			}
			
			$depts_count += count($dept_item_arr);
			
			$PARS['{DEPT_PID}'] = $dept_pid;
			$PARS['{ROW}'] = $i;
			$PARS['{DEPTS_LIST}'] = implode('', $dept_item_arr);
			 
			 
			$depts_group[] = fetch_tpl($PARS, $dept_group_tpl);
			 
		}
		
		if(!$depts_group) continue;
		
		$depts_group_count = count($depts_group);
		
		$depts_group_css_width = $depts_group_count*50 + $depts_count * 167 - 50;
		

		
		$all_depts_rows_width[] = $depts_group_css_width;
		
		$PARS['{ROW}'] = $i;
		$PARS['{WORKERS_GROUPS_LIST}'] = implode($dept_group_sep_tpl,$depts_group);
		$PARS['{GROUPS_COUNT}'] = $depts_group_count;
		$PARS['{WORKERS_COUNT}'] = $depts_count;
		$PARS['{GROUP_CSS_WIDTH}'] = $depts_group_css_width;
		
		if($i!=1) {
			$grhy_list .= fetch_tpl($PARS, $dept_row_tpl);
		}
	}
	
	// Если нет отделов
	if(!$grhy_list)
	{
		$grhy_list = $no_workers_tpl;
	}
	
	$cont_width = max($all_depts_rows_width);
	$center_cont = round($cont_width / 2) - 465;
	
	$cont_width = $cont_width < 680 ? 680 : $cont_width;
	
 	
	// Название отдела
	$sql = "SELECT * FROM tasks_company_depts WHERE dept_id='$dept_id'";
	
	$dept_data = $site_db->query_firstrow($sql);
	
	// Блок руководителя
	$head_block = fill_dept_head_block($dept_id);
		 
	$PARS['{DEPT_ID}'] = $dept_id;
	
	$PARS['{DEPT_NAME}'] = $dept_data['dept_name'];
					
	$PARS['{LIST}'] = $grhy_list;
	
	$PARS['{GRHY_CONT_WIDTH}'] = $cont_width;
	
	$PARS['{CENTER_CONT}'] = $center_cont;
	
	$PARS['{SCHEME_ACTIVE_1}'] = $scheme_active_1;
	
	$PARS['{SCHEME_ACTIVE_2}'] = $scheme_active_2;
	
	$PARS['{HEAD_BLOCK}'] = $head_block;
	
	 
	return fetch_tpl($PARS, $main_tpl);
}


// форма добавления 
function fill_structure_add_form()
{
	global $site_db, $current_user_id, $depts_list;
	
	fill_depts_list(0,0);
	
	$add_dept_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/add_dept_form.tpl');
	
	$PARS['{DEPST_LIST}'] = $depts_list;
	
	return fetch_tpl($PARS, $add_dept_form_tpl);
}

// Список отделов(для селекта)
function fill_depts_list($dept_parent_id, $lvl, $depts_arr=array())
{
	global $site_db, $current_user_id, $result_structure_arr, $lvl, $depts_list;
	
	$lvl++; 
	
	$option_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
 	$sql="SELECT dept_id,dept_name,dept_parent_id FROM tasks_company_depts WHERE dept_parent_id=".$dept_parent_id." ORDER BY dept_name";
	
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{
		$str = '';
		
		for($i=0;$i<$lvl;$i++)
		{  
			$str .= '.&nbsp;&nbsp;';
		}
		
		$selected = '';
		
		if(in_array($row['dept_id'], $depts_arr))
		{
			$selected = "selected='selected'";
		}
		
		$PARS['{NAME}'] = $str.$row['dept_name'];
		$PARS['{VALUE}'] = $row['dept_id'];
		$PARS['{SELECTED}'] = $selected;
		 
		$depts_list .= fetch_tpl($PARS, $option_tpl); $str.$row['dept_name'].'<br>';
		 
		fill_depts_list($row["dept_id"], $lvl, $depts_arr); 
		$lvl--;
	}
	 
}

// Внутренние финансы
function fill_org()
{
	global $site_db, $current_user_id, $depts_list, $current_user_obj;
	
	$main_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/org.tpl');
	
	$org_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/org_edit_tools.tpl');
	
	$dept_id = value_proc($_GET['dip']);
	
	fill_depts_list(0,0, array($dept_id));
	
	// Если администратор, то выводим блок редактирования
	if($current_user_obj->get_is_admin())
	{
		$PARS['{DEPT_ID}'] = $dept_id; 
		
		$org_edit_tools = fetch_tpl($PARS, $org_edit_tools_tpl);
	}
	
	// Список пользователей
	$users_list_cont = fill_org_list_cont('', $dept_id);
	
	$PARS['{USERS_LIST_CONT}'] = $users_list_cont;
	
	$PARS['{DEPTS_LIST}'] = $depts_list;
	
	$PARS['{EDIT_TOOLS}'] = $org_edit_tools;
	
	$PARS['{DEPT_NAME}'] = $dept_name;
 
	
	return fetch_tpl($PARS, $main_tpl);
}

function fill_org_list_cont($search_words, $dept_id, $user_is_fired)
{
	global $site_db, $current_user_id;
	
	$list_cont_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/list_cont.tpl');
	
	$more_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/more_btn.tpl');
	
	$list_no_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/list_no.tpl');
	
	// Список пользователей
	$users_list = fill_org_list(1, $search_words, $dept_id, $user_is_fired);
	
	// Кол-во
	$users_count = get_org_users_count($search_words, $dept_id, $user_is_fired); 
	
	// Кол-во страниц
	$pages_count = ceil($users_count/PER_PAGE);
	
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_btn = $more_btn_tpl;
	} 
	
	if(!$users_list)
	{
		$users_list = $list_no_tpl;
	}
	 
	 
	$PARS['{USERS_LIST}'] = $users_list;
	
	$PARS['{MORE_BTN}'] = $more_btn;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	return fetch_tpl($PARS, $list_cont_tpl);
}

// Кол-во пользователей в организации
function get_org_users_count($search_words,  $dept_id, $user_is_fired)
{
	global $site_db, $current_user_id, $user_obj;
	
	if($search_words)
	{
		$and_s = " AND (user_surname LIKE '$search_words%' OR user_name LIKE '$search_words%')";
	}
	
	if($user_is_fired)
	{
		$and_is_fired = " AND is_fired=1";
	}
	else
	{
		$and_is_fired = " AND is_fired=0";
	}
	
	if($dept_id)
	{
		$sql = "SELECT  COUNT(DISTINCT(i.user_id)) as count FROM tasks_users i
				RIGHT JOIN tasks_company_depts_users j ON i.user_id=j.user_id
				WHERE j.dept_id='$dept_id' $and_s $and_is_fired ORDER by i.user_id $limit  ";
	}
	else
	{
		$sql = "SELECT COUNT(*) as count FROM tasks_users  WHERE 1 $and_is_fired $and_s $limit";	
	}
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

function fill_org_list($page, $search_words='', $dept_id, $user_is_fired)
{
	global $site_db, $current_user_id, $user_obj;
	
	$page = !$page ? 1 : $page;
	
	// Страничность
	$begin_pos = PER_PAGE * ($page-1);
	$limit = " LIMIT ".$begin_pos.",".PER_PAGE;
	
	if($search_words)
	{
		$and_s = " AND (user_surname LIKE '$search_words%' OR user_name LIKE '$search_words%')";
	}
	
	if($user_is_fired)
	{
		$and_is_fired = " AND is_fired=1";
	}
	else
	{
		$and_is_fired = " AND is_fired=0";
	}
	
	if($dept_id)
	{
		$sql = "SELECT  DISTINCT(i.user_id), i.* FROM tasks_users i
				RIGHT JOIN tasks_company_depts_users j ON i.user_id=j.user_id
				WHERE j.dept_id='$dept_id' $and_s $and_is_fired ORDER by j.is_head DESC, i.user_id $limit  ";
	}
	else
	{
		$sql = "SELECT * FROM tasks_users WHERE 1 $and_s $and_is_fired ORDER by user_id $limit  ";
	}
	 
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		 $users_list .= fill_org_list_item($row);
		 
	}
	
	return $users_list;
}

// Заполняет элемент Мои подчиненные
function fill_org_list_item($user_data)
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_worktime.php';
	
	$user_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/user_item.tpl');
	$user_item_send_msg_tool_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/user_item_send_msg_tool.tpl');
	$user_item_admin_label_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/user_item_admin_label.tpl');
	$user_item_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/user_item_edit_tools.tpl');
	$user_removed_from_work_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/user_removed_from_work.tpl');
		
	// Заполянем объект пользователя
	$user_obj->fill_user_data($user_data['user_id']);
	 
	
	// Метка пользователя онлайн он или нет
	//$user_is_online = user_is_online($user_data['user_id'], $user_obj->get_user_last_visit_date());	
	
	// Онлайн иконка
	$user_online = user_in_online_icon($user_data['user_id'], $user_obj->get_user_last_visit_date());
	
	$user_last_activity_block = '';
	
	// Если текущий пользователь администратор
	if($current_user_obj->get_is_admin())
	{
		$PARS['{USER_ID}'] = $user_data['user_id']; 
		
		$edit_tools = fetch_tpl($PARS, $user_item_edit_tools_tpl);
	}
	
	// Ссылка написать сообщение
	if($current_user_id!=$user_data['user_id'])
	{
		$PARS['{USER_ID}'] = $user_data['user_id']; 
		
		$send_msg_tool = fetch_tpl($PARS, $user_item_send_msg_tool_tpl);
	}
	
	// Если пользователь администратор, ставим метку
	if($user_obj->get_is_admin())
	{
		$admin_label = $user_item_admin_label_tpl;
	}
	
	// Список подразделений, в которых состоит сотрудник
	$user_depts_list = get_user_depts_list_block($user_data['user_id']); 
	
	 
	// Подпись, если сотрудник отстранен от работы
	//$user_removed_from_work = fill_user_removed_from_work_status_string($user_data['user_id']);
	

	
	// Подпись, если сотрудник отстранен от работы
	if($user_data['is_fired'])
	{
		$user_removed_from_work = $user_removed_from_work_tpl;
	}
	// статус пользователя
	$user_status = fill_users_status_for_worker($user_data['user_id']);
 	
	
	
	// Метка пользователя онлайн он или нет
	$user_is_online = user_is_online($user_data['user_id'], $user_obj->get_user_last_visit_date());	
	
	$user_last_activity_block = '';
	
	// Если пользователь оффлайн, показываем блок последней активности
	if(!$user_is_online)
	{
		$user_last_activity_block = fill_user_last_activity($user_data['user_id']);
	}
	
	
	$PARS['{USER_LAST_ACTIVITY_BLOCK}'] = $user_last_activity_block;
	
	$PARS['{USER_DEPTS_LIST}'] = $user_depts_list;
	
	$PARS['{ADMIN_LABEL}'] = $admin_label;
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	$PARS['{SEND_MSG_TOOL}'] = $send_msg_tool; 
	
	$PARS['{SURNAME}'] = $user_data['user_surname'];
	
	$PARS['{NAME}'] = $user_data['user_name'];
	
	$PARS['{MIDDLENAME}'] = $user_data['user_middlename'];
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{AVATAR_SRC}'] = get_user_preview_avatar_src($user_data['user_id'], $user_obj->get_user_image());
	
	$PARS['{USER_ID}'] = $user_data['user_id'];
	
	$PARS['{USER_ONLINE}'] = $user_online;
	
	$PARS['{USER_STATUS}'] = $user_status;
	
	$PARS['{DEPUTY_WORKER}'] = '';
	
	$PARS['{USER_REMOVED_FROM_WORK}'] = $user_removed_from_work;
	
	$PARS['{USER_STATUS}'] = $user_status;
	
	return fetch_tpl($PARS, $user_item_tpl);
}

// Список подразделений пользователя
function get_user_depts_list_block($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$user_item_dept_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/user_item_dept_list.tpl');
	$user_item_dept_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/user_item_dept_list_item.tpl');
	$user_item_dept_list_item_head_label_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/user_item_dept_list_item_head_label.tpl');
	
	$sql = "SELECT i.*, j.is_head FROM tasks_company_depts i
			LEFT JOIN tasks_company_depts_users j ON i.dept_id=j.dept_id
			WHERE j.user_id='$user_id' ORDER by is_head ASC";
	
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res, 1))
	{
		$PARS['{DEPT_ID}'] = $row['dept_id'];
		$PARS['{DEPT_NAME}'] = $row['dept_name'];
		$PARS['{IS_HEAD}'] = $row['is_head'] ? $user_item_dept_list_item_head_label_tpl : '';
		
		$dept_list[$row['dept_id']] = fetch_tpl($PARS, $user_item_dept_list_item_tpl);
	}
	
	if($dept_list)
	{
		$PARS['{LIST}'] = implode(', ',$dept_list);
		return fetch_tpl($PARS, $user_item_dept_list_tpl);
	}
}

// Добавление сотрудника в отдел
function add_user_to_dept($dept_id, $user_id, $is_head)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "INSERT INTO tasks_company_depts_users SET dept_id='$dept_id', user_id='$user_id', is_head='$is_head'";
	
	$site_db->query($sql);
}

// Массив отделов, к которым пренадлежит пользователь
function get_user_depts($user_id, $is_head=0, $all)
{
	global $site_db;
	
	 
	
	if($all)
	{
		$sql = "SELECT * FROM tasks_company_depts_users i
				LEFT JOIN tasks_company_depts j ON i.dept_id=j.dept_id
				WHERE i.user_id='$user_id' ORDER by j.dept_name";
	}
	else
	{
		$sql = "SELECT * FROM tasks_company_depts_users i
				LEFT JOIN tasks_company_depts j ON i.dept_id=j.dept_id
				WHERE i.user_id='$user_id' AND i.is_head='$is_head' ORDER by j.dept_name";
	}
	
	
	$res = $site_db->query($sql);
		
	$users_depts = array();
				 
	while($row=$site_db->fetch_array($res, 1))
	{
		$users_depts[$row['dept_id']] = $row['dept_id'];
	}
	
	return $users_depts;
}

// Сохранить отделы пользователя
function save_user_depts($user_id, $depts_arr)
{
	global $site_db;
	
	if(!$depts_arr)
	{
		$depts_arr = array();
	}
	
	$sql = "SELECT * FROM tasks_company_depts_users WHERE user_id='$user_id' AND is_head=0";
	
	$res = $site_db->query($sql);
		
	$user_in_depts = array();
				 
	while($row=$site_db->fetch_array($res, 1))
	{
		$user_in_depts[] = $row['dept_id'];
	}
	
	$to_delete_depts = array_diff($user_in_depts, $depts_arr);
	
	$to_add_depts = array_diff($depts_arr, $user_in_depts);
	 
	// удаляем лишние
	foreach($to_delete_depts as $dept_id)
	{
		$sql = "DELETE FROM tasks_company_depts_users WHERE user_id='$user_id' AND dept_id='$dept_id' AND is_head=0";
		$site_db->query($sql);
	}
	
	// добавляем лишние
	foreach($to_add_depts as $dept_id)
	{
		$sql = "INSERT INTO tasks_company_depts_users SET user_id='$user_id', dept_id='$dept_id', is_head=0";
		$site_db->query($sql);
	}
	
}

// удаление подразделения
function delete_company_dept($dept_id)
{
	global $site_db, $current_user_obj;
	
	// Блок редактирования для админа
	if(!$current_user_obj->get_is_admin() || $dept_id==1)
	{
		return;
	}
		
	// список дочерних отделов
	$depts_arr = get_company_dept_childs($dept_id);
	
	
	if($depts_arr)
	{
		$depts_ids = implode(',', $depts_arr);
		
		// удаляем отлелы
		$sql = "DELETE FROM tasks_company_depts WHERE dept_id IN ($depts_ids)";
		
		$site_db->query($sql);
		
		$sql = "DELETE FROM tasks_company_depts_users WHERE dept_id IN ($depts_ids)";
		
		$site_db->query($sql);
	}
	
	if(!mysql_error())
	{
		return 1;
	}
	 
}

// список всех дочерних подразделение
function get_company_dept_childs($dept_id)
{
	global $site_db;
	 
	$dept_arr[] = $dept_id;
	
	$dept_ids = implode(',', $dept_arr);
	
	$depts_result[] = $dept_id;
	
	while(!$stop)
	{
		$dept_ids = implode(',', $dept_arr);
		
		$dept_arr = array();
		
		// Выбираем категории, для которых эта категория является родительской
		$sql = "SELECT * FROM tasks_company_depts WHERE dept_parent_id IN ($dept_ids)";
		 
		$res = $site_db->query($sql);
		  
		while($row=$site_db->fetch_array($res))
		{
			$dept_arr[] = $row['dept_id'];
			
			$depts_result[$row['dept_id']] = $row['dept_id'];
		}
		
		if(!$dept_arr || $n>100)
		{
			$stop = true;
			 
		}
		$n++;
	}
	 
	return $depts_result;
}

// Форма редактирования отдела
function fill_dept_edit_form($dept_id)
{
	global $site_db, $depts_list, $user_obj;
	
	$edit_dept_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/edit_dept_form.tpl');
	
	$edit_dept_form_dept_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/edit_dept_form_dept_list.tpl');
	
	$option_fcbk_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl');
	
	// ТЕКУЩИЙ ОТДЕЛ
	$sql = "SELECT * FROM tasks_company_depts WHERE dept_id='$dept_id'";
	
	$dept_data = $site_db->query_firstrow($sql);
	
	// отдел родитель
	$sql = "SELECT * FROM tasks_company_depts WHERE dept_id='".$dept_data['dept_parent_id']."'";
	
	$dept_parent_data = $site_db->query_firstrow($sql);
	
	// список организаций
	fill_depts_list(0,0, array($dept_parent_data['dept_id']));
	
	if($dept_id!=1)
	{
		$PARS['{DEPST_LIST}'] = $depts_list;
		
		$depts_list_row = fetch_tpl($PARS, $edit_dept_form_dept_list_tpl);
	}
	
	// Руководитель отдела
	$head_dept_user = get_head_dept_user_id($dept_id);
	
	if($head_dept_user)
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($head_dept_user);
		$user_name = $user_obj->get_user_name();
		$user_middlename = $user_obj->get_user_middlename();
		
		$name =  $user_obj->get_user_surname().' '. $user_obj->get_user_name().' '. $user_obj->get_user_middlename().', '.$user_obj->get_user_position();
		
		
		$PARS['{CLASS}'] = 'selected';
		$PARS['{VALUE}'] = $head_dept_user;
		$PARS['{NAME}'] = $name;
		$head_selected = fetch_tpl($PARS, $option_fcbk_tpl);
	}
	
	$PARS['{DEPTS_LIST_ROW}'] = $depts_list_row;
	
	$PARS['{DEPT_ID}'] = $dept_id;
	
	$PARS['{DEPT_NAME}'] = $dept_data['dept_name'];
	
	$PARS['{HEAD_SELECTED}'] = $head_selected;
	 
	
	return fetch_tpl($PARS, $edit_dept_form_tpl);
}

function get_dept_name_by_id($dept_id)
{
	global $site_db, $depts_list, $user_obj;
	
	$sql= "SELECT dept_name FROM tasks_company_depts WHERE dept_id='$dept_id'";
	 
	$dept_data = $site_db->query_firstrow($sql);
	
	return $dept_data['dept_name'];
}
// получение списка пользователей
function get_depts_users($depts_arr = array(), $without_user)
{
	global $site_db, $user_obj;
	
	if(!$depts_arr)
	{
		exit();
	}
	
	if($without_user)
	{
		$and_user = " AND user_id<>'$without_user' ";
	}
	
	$depts_ids = implode(',', $depts_arr);
	
	$users_arr = array();
	
	$sql = "SELECT * FROM tasks_company_depts_users WHERE dept_id IN($depts_ids) $and_user";
	 
	$res = $site_db->query($sql);
				 
	while($row=$site_db->fetch_array($res, 1))
	{
		$users_arr[$row['user_id']] = $row['user_id'];
	}
	
	return $users_arr;
}

// является ли сотрудник руководителем какого-нибудь отдела
function user_is_dept_head($user_id)
{
	global $site_db, $user_obj;
	
	$sql = "SELECT * FROM tasks_company_depts_users WHERE user_id='$user_id' AND is_head=1 limit 1";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['id'])
	{
		return true;
	}
	
	$sql = "SELECT * FROM tasks_deputies WHERE deputy_user_id='$user_id'";
				
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
		 
		// является временным руководителем
		return true;
	
	}
	
	return false;
}
?>