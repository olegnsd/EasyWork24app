<?php
// Список Моих коллег
function fill_grhy($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$main_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/grhy.tpl');
	
	$grhy_visible_cont_menu_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/grhy_visible_cont_menu.tpl');
	
	$no_workers_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/no_workers.tpl');
	
	$scheme = in_array($_GET['scheme'], array(1,2)) ?  $_GET['scheme'] : 1;
	
	if($scheme==1)
	{
		$workers_row_1_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/workers_row_1.tpl');
		$workers_group_1_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/workers_group_1.tpl');
		$user_item_1_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_item_1.tpl');
		$user_item_boss_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_item_boss.tpl');
		$workers_group_sep_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/workers_group_sep_1.tpl');
	}
	else if($scheme==2)
	{
		$workers_row_1_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/workers_row_2.tpl');
		$workers_group_1_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/workers_group_2.tpl');
		$user_item_1_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_item_2.tpl');
		$user_item_boss_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_item_boss.tpl');
		$workers_group_sep_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/workers_group_sep_2.tpl');
	}
	
	// Дерево
	if($scheme==1)
	{
		$scheme_active_1 = 'grhy_title_link_selected';
		// Выводим меню для выдачи информации по пользователям
		$visible_user_cont_menu = $grhy_visible_cont_menu_tpl;
	}
	// Статусы
	else if($scheme==2)
	{
		$scheme_active_2 = 'grhy_title_link_selected';
		$visible_user_cont_menu = '';
	}
	
	// Первоначально запихиваем переданного пользователя в массив пользователей
	$invited_users_arr[] = $current_user_id;
	
	$result_users_ids = array();
	
	$checked_users_arr = array();
	
	$i = 1;
	
	$result[$i][] = array($current_user_id);
	
	// Делаем цикл, и формируем массив дерева подчиненных
	while(!$stop && $current_user_id)
	{
		$i++;
		
		$invited_users_ids = '';
		
		if($invited_users_arr)
		{
			$users_not_checked_arr = array_diff($invited_users_arr, $checked_users_arr);
			
			$checked_users_arr = array_merge($checked_users_arr, $invited_users_arr);
		 
		 	// Формируем список для запроса
			$invited_users_ids = implode(',', $users_not_checked_arr);
		
			// Обнуляем массив пользователей
			$invited_users_arr = array();
			$invited_link = array();
		}
			
		if($invited_users_ids)
		{
			// Выбираем пользователей, которые приглашали других в свои подчиненные
			$sql = "SELECT invite_user, invited_user FROM ".WORKERS_TB." WHERE invite_user IN($invited_users_ids) 
					AND ((invited_user_status=1 AND deputy_id = 0) OR deputy_id>0) AND deleted = 0";
			  
			$res = $site_db->query($sql);
				
			while($row=$site_db->fetch_array($res))
			{
				//$result_users_ids[$row['invited_user']] = $row['invited_user'];
				
				$invited_users_arr[] = $row['invited_user'];
				
				$invited_link[$row['invite_user']][] = $row['invited_user'];
			}
		}
		
		$result[$i] = $invited_link;
		
		// Если пройдено вниз по дереву подчиненных, останавливаем цикл
		if(!$invited_users_arr || $num>10000)
		{ 
			$stop = true;
		}
		 
		$num++; 
	}
	
	// Проходим по дереву подчиненных 
	foreach($result as $i => $user_subdata)
	{
		  
		$workers_group = array();
		$workers_count = 0;
		$workers_group_count = 0;
		 
		foreach($user_subdata as $boss_id => $workers_arr)
		{ 
			$worker_item_arr = array();
			
			foreach($workers_arr as $worker_id)
			{	
				// Для схемы статусов
				if($scheme==2)
				{
					// Последний активный статус пользователя
					$user_activity_status = get_last_user_activity_status($worker_id);
					
					$sql = "SELECT user_last_visit_date FROM ".USERS_TB." WHERE user_id='$worker_id'";
			
					$row = $site_db->query_firstrow($sql);
			
					$user_last_v_time = $row['user_last_visit_date'];
					
					// Если пользователь был онлайн в течение часа
					if(user_is_online($user_id, $user_last_v_time, 60))
					{
						$online_last_time = 1;	
					}
					else
					{
						$online_last_time = 0;
					}
					
					// Пользователь был в течение часа онлайн и работает
					if($online_last_time && $user_activity_status==1)
					{
						$status = 1;
					}
					// Пользователь онлайн или работает
					else if(user_is_online($user_id, $user_last_v_time,60) || $user_activity_status==1)
					{
						$status = 2;
					}
					else
					{
						$status= 3;
					}
				}
								
				$PARS['{WORKER_ID}'] = $worker_id;
				
				$worker_item_arr[$status.'_'.$worker_id] = fetch_tpl($PARS, $user_item_1_tpl);
				 
			}
			
			// Для схемы статусов, сортируем иконки статусов в порядке убывания активности
			if($scheme==2)
			{
				ksort($worker_item_arr);
			}
			
			$workers_count += count($worker_item_arr);
			
			$PARS['{BOSS_ID}'] = $boss_id;
			$PARS['{ROW}'] = $i;
			$PARS['{WORKERS_LIST}'] = implode('', $worker_item_arr);
			 
			 
			$workers_group[] = fetch_tpl($PARS, $workers_group_1_tpl);
			 
		}
		
		if(!$workers_group) continue;
		
		$workers_group_count = count($workers_group);
		
		if($scheme==1)
		{
			$worker_group_css_width = $workers_group_count*50 + $workers_count * 167 - 50;
		}
		else if($scheme==2)
		{
			$worker_group_css_width = $workers_group_count*16 + $workers_count * 18 - 16;
		}
		 
		

		
		$all_workers_rows_width[] = $worker_group_css_width;
		
		$PARS['{ROW}'] = $i;
		$PARS['{WORKERS_GROUPS_LIST}'] = implode($workers_group_sep_tpl,$workers_group);
		$PARS['{GROUPS_COUNT}'] = $workers_group_count;
		$PARS['{WORKERS_COUNT}'] = $workers_count;
		$PARS['{GROUP_CSS_WIDTH}'] = $worker_group_css_width;
		
		if($i!=1) {
			$grhy_list .= fetch_tpl($PARS, $workers_row_1_tpl);
		}
	}
	
	// Если нет подчиненных
	if(!$grhy_list)
	{
		$grhy_list = $no_workers_tpl;
	}
	
	$cont_width = max($all_workers_rows_width);
	$center_cont = round($cont_width / 2) - 465;
	
	$cont_width = $cont_width < 960 ? 960 : $cont_width;
	
 
	// Заполянем объект пользователя
	$user_obj->fill_user_data($current_user_id);
	
	$PARS['{AVATAR_SRC}'] = get_user_preview_avatar_src($current_user_id, $user_obj->get_user_image());
	
	$PARS['{USER_ID}'] = $current_user_id;
					
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
					
	$PARS['{LIST}'] = $grhy_list;
	
	$PARS['{GRHY_CONT_WIDTH}'] = $cont_width;
	
	$PARS['{CENTER_CONT}'] = $center_cont;
	
	$PARS['{SCHEME}'] = $scheme;
	
	$PARS['{SCHEME_ACTIVE_1}'] = $scheme_active_1;
	
	$PARS['{SCHEME_ACTIVE_2}'] = $scheme_active_2;
	
	$PARS['{VISIBLE_USER_CONT_MENU}'] = $visible_user_cont_menu;
	 
	return fetch_tpl($PARS, $main_tpl);
}

function ghrh_get_user_cont($users_arr, $cont_type)
{
	global $site_db, $current_user_id, $user_obj;
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_efficiency.php';
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_money.php';

	$user_item_1_efficiency_cont_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_item_1_efficiency_cont.tpl');
	$user_item_1_cont_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_item_1_cont.tpl');
	
	foreach($users_arr as $user_id)
	{  
		// Заполянем объект пользователя
		$user_obj->fill_user_data($user_id);
		
		$name = $user_obj->get_user_name();
		
		$middlename = $user_obj->get_user_middlename();
		
		$surname = $user_obj->get_user_surname();
		
		$position = $user_obj->get_user_position();
		
		$avatar = get_user_preview_avatar_src($user_id, $user_obj->get_user_image());
		
		// Отображение информации по сотруднику
		switch($cont_type)
		{
			// Финансы
			case 'money_accruals':
			case 'money':
				$user_visible_cont = get_user_grhy_cont_money($user_id, $cont_type);
			break;
			// Кол-во задач по типам
			case 'tasks_active':
			case 'tasks_finished_all':
			case 'tasks_finished_30':
				$user_visible_cont = get_user_grhy_cont_tasks($user_id, $cont_type);
			break;
			// КПД сотрудника за месяц
			case 'kpd':
				$user_visible_cont = get_user_grhy_cont_kpd($user_id);
			break;
			
			// По умолчанию
			default:
				$user_visible_cont = get_user_grhy_cont_info($user_id);
			break;
		}

		$PARS['{USER_ID}'] = $user_id;
		
		$PARS['{AVATAR_SRC}'] = $avatar;
		
		$PARS['{USER_NAME}'] = $name[0].'.';
	
		$PARS['{USER_MIDDLENAME}'] = $middlename[0].'.';;
	
		$PARS['{USER_SURNAME}'] = $surname;
		
		$PARS['{USER_POSITION}'] = $position;
		
		$PARS['{USER_VISIBLE_CONT}'] =  $user_visible_cont;
	
		$worker_item_arr[$user_id]['info'] = iconv('cp1251', 'utf-8', fetch_tpl($PARS, $user_item_1_cont_tpl));
		//$worker_item_arr[$user_id]['status'] = 1;
	}
	 
	 
	return $worker_item_arr;
}

// Финансы пользователя, выводимое в ячейке пользователя
function get_user_grhy_cont_money($user_id, $cont_type)
{
	global $site_db, $current_user_id, $user_obj;
	
	$user_visible_cont_money_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_visible_cont_money.tpl');
	
	// Отображение информации по сотруднику
	switch($cont_type)
	{
		// Кол-во задач по типам
		case 'money':
		 	$money_sum = get_user_payments_summ_for_period($user_id, 30, 1);
		break;
		case 'money_accruals':
			$money_sum = get_user_accruals_sum($user_id, 1);
		break;
		
	}
	
	$PARS['{USER_ID}'] = $user_id;

	$PARS['{MONEY_SUM}'] = $money_sum;
			
	return  fetch_tpl($PARS, $user_visible_cont_money_tpl);
}
// Задачи пользователя , выводимое в ячейке пользователя
function get_user_grhy_cont_tasks($user_id, $cont_type)
{
	global $site_db, $current_user_id, $user_obj;
	
	$user_visible_cont_tasks_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_visible_cont_tasks.tpl');
	
	// Отображение информации по сотруднику
	switch($cont_type)
	{
		// Кол-во задач по типам
		case 'tasks_active':
		 	$tasks_count_for_user = get_count_tasks_in_process_for_user($user_id, 0);
		break;
		case 'tasks_finished_all':
			$tasks_count_for_user = get_user_tasks_completed_count($user_id,0 ,0);
		break;
		case 'tasks_finished_30':
			$tasks_count_for_user = get_user_tasks_completed_count($user_id, 30, 0);
		break;
	}
	
	
	$PARS['{USER_ID}'] = $user_id;

	$PARS['{TASKS_COUNT}'] = $tasks_count_for_user;
			
	return  fetch_tpl($PARS, $user_visible_cont_tasks_tpl);
}
// КПД пользователя за последнее время, выводимое в ячейке пользователя
function get_user_grhy_cont_kpd($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$user_visible_cont_efficiency_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_visible_cont_efficiency.tpl');
	
	// За какой период находим задачи, кол-во дней
	$last_days_count = 30;
		
	// Получаем обработанный массив задач пользователя за прошедние дни
	$dates_tasks = user_efficiency_tasks_array($user_id, $last_days_count);
	
	// Кол-во дней, в которых имеются дейтсвия над задачами пользователя
	$user_tasks_count = count($dates_tasks);
	$user_efficiency_sum = 0;
	 
	
	foreach($dates_tasks as $date => $task_data)
	{ 
		$user_efficiency_sum += get_user_efficiency($task_data, $date);	 
	}
	
	$user_efficiency = '-';
	if($user_efficiency_sum)
	{
		$user_efficiency = round($user_efficiency_sum / $user_tasks_count);
	}

	$PARS['{USER_ID}'] = $user_id;

	$PARS['{USER_EFFICIENCY}'] = $user_efficiency;
			
	return  fetch_tpl($PARS, $user_visible_cont_efficiency_tpl);
}


// Общая информация, выводимая в ячейке пользователя
function get_user_grhy_cont_info($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$user_visible_cont_default_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_visible_cont_default.tpl');
	
	$user_item_act_online_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_item_act_online.tpl');
	$user_item_act_work_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_item_act_work.tpl');
	$user_item_act_offline_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/grhy/user_item_act_offline.tpl');
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($user_id);
	
	// Последний активный статус пользователя
	$user_activity_status = get_last_user_activity_status($user_id);
	
	// Статус Работает
	$activity_status = $user_activity_status==1 ? $user_item_act_work_tpl : '';
	
	$user_is_online = user_is_online($user_id, $user_obj->get_user_last_visit_date());
	
	// Статус онлайн
	$online_status =  $user_is_online ? $user_item_act_online_tpl : $user_item_act_offline_tpl;
	
	// Если пользователь был онлайн в течение часа
	if(user_is_online($user_id, $user_obj->get_user_last_visit_date(), 60))
	{
		$online_last_time = 1;	
	}
	else
	{
		$online_last_time = 0;
	}
	
	if(!$user_is_online && $user_activity_status!=1)
	{
		// Время последнего захода в систему
		$user_last_visit_date = $user_obj->get_user_last_visit_date();
		
		// Если пользователь заходил на сайт
		if($user_last_visit_date && $user_last_visit_date!='0000-00-00 00:00:00')
		{
			$last_activity_mktime = to_mktime($user_last_visit_date);
			
			// До принятия
			$last_activity_mktime_raznost = time() - $last_activity_mktime;
			
			// Преобразуем в слова секунды
			$last_activity_result =  sec_to_date_words($last_activity_mktime_raznost, 0, 0, 1);
			
			$online_status = $online_status.' <span style="color:#aaaaaa">'.$last_activity_result.'</span>';
		}
	     
	}
		
		
		
	// Пользователь был в течение часа онлайн и работает
	if($online_last_time && $user_activity_status==1)
	{
		$status_class = 'status_1';
	}
	// Пользователь онлайн или работает
	else if(user_is_online($user_id, $user_obj->get_user_last_visit_date(), 60) || $user_activity_status==1)
	{
		$status_class = 'status_2';
	}
	else
	{
		$status_class= 'status_3';
	}
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{STATUS_ONLINE}'] = $online_status;
	
	$PARS['{ACTIVITY_STATUS}'] = $activity_status;
	
	$PARS['{STATUS_CLASS}'] = $status_class;
			
	return  fetch_tpl($PARS, $user_visible_cont_default_tpl);

}
?>