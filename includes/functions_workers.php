<?php
#### Сотрудники


// Список Моих сотрудников
function fill_workers($user_id)
{
	global $site_db, $current_user_id, $current_user_obj;
	
	$workers_tpl = file_get_contents('templates/workers/workers.tpl');
	
	$no_workers_tpl = file_get_contents('templates/workers/no_workers.tpl');
	
	$no_workers_go_to_reg = file_get_contents('templates/workers/no_workers_go_to_reg.tpl');
	
	// Кол-во сотрудников
	$workers_count = get_current_user_users_arrs(array(0,1,0,0,1),0,1);
	 
	// Список сотрудников
	$users_list_arr = fill_workers_list($user_id);
	 
	$users_list = $users_list_arr['users_list'];
	
	// Кол-во сотрудников, которые работают
	$all_workers_is_working_count =  $users_list_arr['workers_is_working_count'];
	
	// Кол-во сотрудников онлайн
	$all_workers_is_online_count =  $users_list_arr['all_workers_is_online_count'];
	
	// Кол-во, которые не вышли на работу
	$all_workers_is_not_working_count = $workers_count ? $workers_count - $all_workers_is_working_count : 0;
	
	if(!$users_list)
	{
		//echo $current_user_obj->get_user_registrated_by_user_id();
		/*if($current_user_obj->get_user_registrated_by_user_id()==0)
		{
			$users_list = $no_workers_go_to_reg;
		}
		else
		{
			$users_list = $no_workers_tpl;
		}*/
		
		$users_list = $no_workers_tpl;
	}
	
	$trigger_show_add_form = value_proc($_GET['sf']);
	
	// Форма создания планерки
	$add_planning_session_form = fill_add_planning_session_form();
	
	$PARS['{USERS_LIST}'] = $users_list;
	
	$PARS['{CURRENT_USER_ID}'] = $current_user_id;
	
	$PARS['{ALL_WORKERS_IS_WORKING_COUNT}'] = $all_workers_is_working_count;
	
	$PARS['{ALL_WORKERS_IS_ONLINE_COUNT}'] = $all_workers_is_online_count;
	
	$PARS['{ALL_WORKERS_IS_NOT_WORKING_COUNT}'] = $all_workers_is_not_working_count;
	
	$PARS['{ADD_PLANNING_SESSION_FORM}'] = $add_planning_session_form;
	
	// открыть по умолч
	$PARS['{TRIGGER_SHOW_ADD_FORM}'] = $trigger_show_add_form;
	
	return fetch_tpl($PARS, $workers_tpl);
}

// Формирование списка моих сотрудников
function fill_workers_list($user_id)
{
	global $site_db, $current_user_id, $user_obj, $_CURRENT_USER_DEPUTY_WORKERS_ARR;
	
	$deputy_workers_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/deputy_workers_block.tpl');
	
	$workers_is_working_count = 0; // на работе
	$all_workers_is_online_count = 0; // онлайн
	
	// Временные подчиненные
	$deputies_workers_ids = implode(',', $_CURRENT_USER_DEPUTY_WORKERS_ARR);
	
	/*if($deputies_workers_ids)
	{
		$sql = "SELECT user_id, user_last_visit_date FROM ".USERS_TB." WHERE user_id IN($deputies_workers_ids)";
		
		$res = $site_db->query($sql);
		
		while($row=$site_db->fetch_array($res))
		{
			$last_activity_date = !preg_match('/0000/', $row['user_last_visit_date']) ? to_mktime($row['user_last_visit_date']) : '0' ;
			// Ставим отметку, что подчиненный временный
			$row['deputy_worker'] = 1;
			$workers_arr[$last_activity_date.'_'.$row['user_id']] = $row;
		}
	}*/
	
	// Сотрудники
	$workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	
	/*// Выбор постоянных сотрудников
	$sql = "SELECT i.*, j.user_last_visit_date FROM ".WORKERS_TB." i
			LEFT JOIN ".USERS_TB." j ON i.invited_user = j.user_id
			WHERE i.invite_user='$user_id' AND i.invited_user_status IN (0,1,2) AND i.deputy_id=0 ORDER by i.id DESC";
	
	$res = $site_db->query($sql);
	 
	$workers_is_working_count = 0; // на работе
	$all_workers_is_online_count = 0; // онлайн
		
	while($row=$site_db->fetch_array($res))
	{
		$last_activity_date = !preg_match('/0000/', $row['user_last_visit_date']) ? to_mktime($row['user_last_visit_date']) : '0' ;
		// Ставим отметку, что подчиненный постоянный
		$row['deputy_worker'] = 0;
		$workers_arr[$last_activity_date.'_'.$row['invited_user']] = $row;
	}*/
	
	//krsort($workers_arr);
	 
	foreach($workers_arr as $user_id)
	{
		// Постоянный подчиненный
		/*if($row['deputy_worker']==0)
		{
			$user_arr = fill_workers_list_item($row);
		}
		// Временный подчиненный
		else if($row['deputy_worker']==1)
		{
			$user_arr = fill_deputy_workers_list_item($row['user_id']);
		}*/
		
		$user_arr = fill_workers_list_item($user_id);
		
		//$users_list .= $user_arr['user_item'];
		 
		// Подчиненный онлайн
		if($user_arr['user_is_online'])
		{
			$all_workers_is_online_count++;
		}
		// Подчиненный работает
		if($user_arr['last_activity_status']==1)
		{
			$workers_is_working_count++;
		}
		
		$last_activity_date = !preg_match('/0000/', $user_arr['user_last_visit_date']) ? to_mktime($user_arr['user_last_visit_date']) : '0' ;
		
		$users_list[$last_activity_date.'_'.$user_id] = $user_arr['user_item'];
	}

	krsort($users_list);
	/*if($deputy_users_list)
	{
		$PARS['{DEPUTY_WORKERS_LIST}'] = $deputy_users_list;
		
		$deputy_users_list =  fetch_tpl($PARS, $deputy_workers_block_tpl);
	}*/
	
	// К списку подчиненнных, добавляем список временных подчиненных
	$users_list = implode('', $users_list);
	
	// Воазращаем массив со списком подчиненных, кол-ом работюащих и кол-ом в онлайн
	return array('users_list' => $users_list, 'workers_is_working_count' => $workers_is_working_count, 'all_workers_is_online_count' => $all_workers_is_online_count);
}

// Кол-во всех сотрудников
function get_workers_count($user_id)
{
	/*global $site_db, $current_user_id, $user_obj;
	
	// Выбор сотрудников
	$sql = "SELECT COUNT(*) as count FROM ".WORKERS_TB." WHERE invite_user='$user_id' AND invited_user_status = 1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];*/
}

// Заполняет элемент Мои подчиненные для пользователей, которых передали заместителю
function fill_deputy_workers_list_item($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_worktime.php';
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tasks1.php';
	
	$worker_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/workers_item.tpl');
	
	$workers_not_confirm_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/workers_not_confirm_item.tpl');
	
	$workers_reject_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/workers_reject_item.tpl');
	
	$new_work_reports_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/new_work_reports.tpl');
	
	$new_task_reports_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/new_count.tpl');
	
	$worker_is_working_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/worker_is_working.tpl');
	
	$deputy_worker_str_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/deputy_worker_str.tpl');
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($user_id);
	
	// Блок новых отчетов по кругу обязанностей
	$new_work_report_block = get_new_work_report_block($user_id);
	
	// Отчеты к задачам
	$new_task_report_block = ''; 
	
	// Кол-во новых отчетов для задачи начальника 
	$new_task_reports_count = get_new_task_reports_count_by_users($user_id, $current_user_id);
	
		
	// Если есть новые отчеты, выводим блок счетчика новых отчетов
	if($new_task_reports_count)
	{
		$new_task_report_block = '(+'.$new_task_reports_count.')';
	}
	
	// Метка пользователя онлайн он или нет
	$user_is_online = user_is_online($user_id, $user_obj->get_user_last_visit_date());	

	// Онлайн иконка
	$user_online = user_in_online_icon($user_id, $user_obj->get_user_last_visit_date());
	
	$user_last_activity_block = '';
	
	// Если пользователь оффлайн, показываем блок последней активности
	if(!$user_is_online)
	{
		$user_last_activity_block = fill_user_last_activity($user_id);
	}
	
	// Проверяем последний статус ("Начал работь" или "Работаь закончил") за дату
	$user_last_status = get_last_user_activity_status($user_id);
	
	// Работает
	if($user_last_status==1)
	{
		$user_is_working = $worker_is_working_tpl;
	}
	
	if($user_is_online && $user_is_working)
	{
		$user_online .= ' | ';
	}
	else if($user_last_activity_block && $user_is_working)
	{
		$user_is_working .= '<br>';
	}
	
	 
	
	// Статус сотрудника
	// Название последнего статуса пользователя
	$user_status = fill_users_status_for_worker($user_id);
	
	
	$PARS = array();
	
	$PARS['{JOB_ID}'] = $user_obj->get_user_job_id();
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{AVATAR_SRC}'] = get_user_preview_avatar_src($user_id, $user_obj->get_user_image());
	
	// Кол-во всех заданий у сотрудника $user_id от $current_user_id
	$PARS['{TASKS_COUNT}'] = get_active_tasks_count_to_user($current_user_id, $user_id);
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{CURRENT_USER_ID}'] = $current_user_id;
	
	$PARS['{NEW_WORK_REPORTS}'] = $new_work_report_block;
	
	$PARS['{NEW_TASK_REPORTS}'] = $new_task_report_block;
	
	$PARS['{USER_ONLINE}'] = $user_online;
	
	$PARS['{USER_LAST_ACTIVITY_BLOCK}'] = $user_last_activity_block;
	
	$PARS['{USER_IS_WORKING}'] = $user_is_working;
	
	$PARS['{USER_STATUS}'] = $user_status;
	
	$PARS['{DEPUTY_WORKER}'] = $deputy_worker_str_tpl;
	
	$PARS['{USER_REMOVED_FROM_WORK}'] = $user_removed_from_work;
	
	$item_tpl = $worker_item_tpl;
	
	// Воазращаем массив с заполненным элементом подчиненного, отметки о его нахождение онлайн, статус работы (работает или не работает)
	return array('user_item' => fetch_tpl($PARS, $item_tpl), 'user_is_online' => $user_is_online, 'last_activity_status' => $user_last_status);
}

// Заполняет элемент Мои подчиненные
function fill_workers_list_item($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_worktime.php';
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tasks1.php';
	
	$worker_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/workers_item.tpl');
	
	$workers_not_confirm_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/workers_not_confirm_item.tpl');
	
	$workers_reject_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/workers_reject_item.tpl');
	
	$new_work_reports_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/new_work_reports.tpl');
	
	$new_task_reports_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/new_count.tpl');
	
	$worker_is_working_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/worker_is_working.tpl');
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($user_id);
	
	// Блок новых отчетов по кругу обязанностей
	$new_work_report_block = get_new_work_report_block($user_id);
	
	// Отчеты к задачам
	$new_task_report_block = ''; 
	
	// Кол-во новых отчетов для задачи начальника 
	//$new_task_reports_count = get_new_task_reports_count_by_users($user_id, $current_user_id);
	
		
	// Если есть новые отчеты, выводим блок счетчика новых отчетов
	if($new_task_reports_count)
	{
		$new_task_report_block = '(+'.$new_task_reports_count.')';
	}
	
	// Метка пользователя онлайн он или нет
	$user_is_online = user_is_online($user_id, $user_obj->get_user_last_visit_date());	
	
	// Онлайн иконка
	$user_online = user_in_online_icon($user_id, $user_obj->get_user_last_visit_date());
	
	$user_last_activity_block = '';
	
	// Если пользователь оффлайн, показываем блок последней активности
	if(!$user_is_online)
	{
		$user_last_activity_block = fill_user_last_activity($user_id);
	}
	
	// Проверяем последний статус ("Начал работь" или "Работаь закончил") за дату
	$user_last_status = get_last_user_activity_status($user_id);
	
	// Работает
	if($user_last_status==1)
	{
		$user_is_working = $worker_is_working_tpl;
	}
	
	if($user_is_online && $user_is_working)
	{
		$user_online .= ' | ';
	}
	else if($user_last_activity_block && $user_is_working)
	{
		$user_is_working .= '<br>';
	}
	
	 
	
	// Статус сотрудника
	// Название последнего статуса пользователя
	$user_status = fill_users_status_for_worker($user_id);
	
	
	$PARS = array();
	
	$PARS['{JOB_ID}'] = $user_obj->get_user_job_id();
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{AVATAR_SRC}'] = get_user_preview_avatar_src($user_id, $user_obj->get_user_image());
	
	// Кол-во всех заданий у сотрудника $user_id от $current_user_id
	$PARS['{TASKS_COUNT}'] = get_active_tasks_count_to_user($user_id);
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{CURRENT_USER_ID}'] = $current_user_id;
	
	$PARS['{NEW_WORK_REPORTS}'] = $new_work_report_block;
	
	$PARS['{NEW_TASK_REPORTS}'] = $new_task_report_block;
	
	$PARS['{USER_ONLINE}'] = $user_online;
	
	$PARS['{USER_LAST_ACTIVITY_BLOCK}'] = $user_last_activity_block;
	
	$PARS['{USER_IS_WORKING}'] = $user_is_working;
	
	$PARS['{USER_STATUS}'] = $user_status;
	
	$PARS['{DEPUTY_WORKER}'] = '';
	
	$PARS['{USER_REMOVED_FROM_WORK}'] = $user_removed_from_work;
	
	// Неподтвержденный шаблон
	/*if($row['invited_user_status']==0)
	{
		$item_tpl = $workers_not_confirm_item_tpl;
	}
	// Отклоненная заявка на добавление, шаблон
	else if($row['invited_user_status']==2)
	{
		$item_tpl = $workers_reject_item_tpl;
	}
	else
	{
		 
	}*/
	
	$item_tpl = $worker_item_tpl;
	
	// Воазращаем массив с заполненным элементом подчиненного, отметки о его нахождение онлайн, статус работы (работает или не работает)
	return array('user_item' => fetch_tpl($PARS, $item_tpl), 'user_last_visit_date' => $user_obj->get_user_last_visit_date(), 'user_is_online' => $user_is_online, 'last_activity_status' => $user_last_status);
}

function fill_user_removed_from_work_status_string($user_id)
{
	global $site_db, $current_user_id;
	
	$user_removed_from_work_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/user_removed_from_work.tpl');
	
	// Првоеряем, отстранен ли пользователь от работы
	$sql = "SELECT * FROM ".REMOVE_FROM_WORK_TB." WHERE user_id='$user_id'";
		
	$row = $site_db->query_firstrow($sql);
	
	if($row['id'])
	{
		return $user_removed_from_work_tpl;
	}
}
// Блок новых отчетов по кругу обязанностей
function get_new_work_report_block($user_id)
{
	global $current_user_id;
	
	// ID актуального круга обязанностей для пользователя
	$actual_user_work_data = get_actual_work_id_for_user_arr($user_id);
	$actual_work_id = $actual_user_work_data['work_id'];
	 
	$new_work_report_block = ''; 
	
	// Если существует круг обязанностей для пользователя и актуальный пользователь является его автором
	if($actual_work_id && $actual_user_work_data['work_from_user_id']==$current_user_id)
	{
		// Кол-во новых отчетов для круга обязанностей
		$new_work_reports_count = get_new_work_reports_count($actual_work_id);
		
		// Если есть новые отчеты, выводим блок счетчика новых отчетов
		if($new_work_reports_count)
		{
			$new_work_report_block = '(+'.$new_work_reports_count.')';
		}
	}
	
	return $new_work_report_block;
}

// Формирует блок статуса для сотрулника
function fill_users_status_for_worker($user_id)
{
	$user_status_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers/user_status.tpl');
	
	$status = '';
	
	// Статус сотрудника
	// Название последнего статуса пользователя
	$active_user_status_arr = get_user_last_status($user_id);
	 
	if($active_user_status_arr['status_id'] > 0)
	{
		$PARS['{STATUS_NAME}'] = $active_user_status_arr['status_name'];
		
		$status = fetch_tpl($PARS, $user_status_tpl);
	}
	
	return $status;
}
// Последняя активность на сайте подчиненного
function fill_user_last_activity($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$user_last_activity_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/user/user_last_activity.tpl');
	
	// Актуальное время в юникс секундах
	$now_mktime = time();
	
	$sql = "SELECT user_last_visit_date, user_sex FROM ".USERS_TB." WHERE user_id='$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	// Если пользователь заходил на сайт
	if($row['user_last_visit_date'] && $row['user_last_visit_date']!='0000-00-00 00:00:00')
	{
		$last_activity_mktime = to_mktime($row['user_last_visit_date']);
		
		// До принятия
		$last_activity_mktime_raznost = $now_mktime - $last_activity_mktime;
		
		// Преобразуем в слова секунды
		$last_activity_result =  sec_to_date_words($last_activity_mktime_raznost, 0, 0, 1);
	 	
		
		
		$PARS['{SEX}'] = get_words_end_by_user_sex($row['user_sex']);
		
		$PARS['{LAST_ACTIVITY_DATE}'] = $last_activity_result;
		
		return fetch_tpl($PARS, $user_last_activity_tpl);	
	}
	else
	{
		return '';
	}
	
}


// Проверка, является ли сотрудник подчиненным пользователю
function is_user_subordinate($invite_user, $invited_user)
{
	global $site_db;
	
	// Проверяем, является ли выбранный сотрудник подчиненным
	$sql = "SELECT id FROM ".WORKERS_TB." WHERE invite_user ='$invite_user' AND invited_user='$invited_user' AND invited_user_status=1 ";
	
	$row = $site_db->query_firstrow($sql);
	
	// Если сотрудник является подчиненным
	if($row['id'])
	{
		return true;
	}
	else
	{
		return false;
	}
}

// Список пользователей отсносящихся к пользователю (сотрудники и начальники)
function get_users_arr_for_user($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	// Выбираем начальников и подчиненных для пользователя
	$sql = "SELECT invite_user, invited_user FROM ".WORKERS_TB." WHERE (invite_user='$user_id' OR invited_user='$user_id') AND invited_user_status=1";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$user_data = array();
		
		if($row['invite_user']==$user_id)
		{
			$row_user_id = $row['invited_user'];
		}
		else
		{
			$row_user_id = $row['invite_user'];
		}
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row_user_id);
		
		//$user_fio = $user_data['surname'].'_'.$row_user_id;
		
		$user_data['user_id'] = $row_user_id;
		
		$user_data['surname'] = $user_obj->get_user_surname();
			
		$user_data['name'] = $user_obj->get_user_name();
			
		$user_data['middlename'] = $user_obj->get_user_middlename();
			
		$user_data['user_position'] = $user_obj->get_user_position();
	
		$user_fio = $user_data['surname'].'_'.$row_user_id;
		
		$users_list_arr[$user_fio] = $user_data;
	}
	
	ksort($users_list_arr);
	 
	return $users_list_arr;
		
}

// Возвращает массив сотрудников пользователя
function get_user_workers_arr($user_id, $is_deputy=0)
{
	global $site_db, $current_user_id, $user_obj;
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_org.php';
	
	$workers_arr = array();
	
	// ПРЯМЫЕ СОТРУДНИКИ
	if(!$is_deputy)
	{
		// выбор отдела, где пользователь является ЗАМЕСТИТЕЛЕМ
		$sql = "SELECT * FROM tasks_deputies WHERE deputy_user_id = '$user_id'";
		
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
			
			$deputy_depts_arr[$row['dept_id']] = $row['dept_id'];
		}
		 
		// если есть отделы, где пользователь является заместителем
		if($deputy_depts_arr)
		{
			$deputy_depts = implode(',', $deputy_depts_arr);
			
			$and_deputies_depts = " OR (i.dept_id IN($deputy_depts))";
		}
		
		
		// Выбираем отделы, где пользователь является руководителем
		$sql = "SELECT j.* FROM tasks_company_depts i
				LEFT JOIN tasks_company_depts_users j ON i.dept_id=j.dept_id
				WHERE ((j.is_head=1 AND j.user_id='$user_id') $and_deputies_depts)";
		
		$res = $site_db->query($sql);
			//echo $sql;
		while($row=$site_db->fetch_array($res))
		{
			// Все отделы, пользователь является руководителем
			$depts_arr[$row['dept_id']] = $row['dept_id'];
		}
		
		 
		// Если есть отделы, где пользователь является руководителем
		if($depts_arr)
		{
			$depts_ids = implode(',', $depts_arr);
			
			// находим дочерние отделы вышестоящих отделов
			$sql = "SELECT dept_id FROM tasks_company_depts WHERE dept_parent_id IN($depts_ids)";
			
			$res = $site_db->query($sql);
			
			while($row=$site_db->fetch_array($res))
			{
				// Все дочерние отделы отделов, где пользователь является руководителем
				$childs_depts[$row['dept_id']] = $row['dept_id'];
			}
			
			// Если дочерние отделы найдены, добавляем к сотрудникам пользователя еще и руководителей нижестоящих отделов
			if($childs_depts)
			{
				$childs_depts_ids = implode(',', $childs_depts);
				
				// проверяем, выставляли ли руководители заместителей
				$sql = "SELECT * FROM tasks_deputies WHERE dept_id IN($childs_depts_ids) AND deleted=0";
				
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
				
					// ЗАМЕСТИТЕЛИ руководителей нижестоящих отделов
					$deputies_users_head[$row['deputy_user_id']] = $row['deputy_user_id'];
				
				}
				
				$and_childs_head_users = " OR (dept_id IN($childs_depts_ids) AND is_head=1)";
			}
			
			
			// Выбираем сотрудников отдела
			$sql = "SELECT * FROM tasks_company_depts_users WHERE ((dept_id IN($depts_ids) AND is_head=0) $and_childs_head_users) AND user_id!='$user_id'";
			
			$res = $site_db->query($sql);
			
			while($row=$site_db->fetch_array($res))
			{
				if($current_user_id==$row['user_id'])
				{
					continue;
				}
				
				$workers_arr[$row['user_id']] = $row['user_id'];
			}
			
			// добавляем к сотрудникам заместителей руководителей нижестоящих отделов
			foreach($deputies_users_head as $head_user_id)
			{
				$workers_arr[$head_user_id] = $head_user_id;
			}
		} 
	
	}
	
	// ВРЕМЕННЫЕ СОТРУДНИКИ
	/*if($is_deputy)
	{
		$deputy_workers_arr = array();
		
		// выбор отдела, где пользователь является ЗАМЕСТИТЕЛЕМ
		$sql = "SELECT * FROM tasks_deputies WHERE deputy_user_id = '$user_id'";
		
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
			
			$deputy_depts_arr[$row['dept_id']] = $row['dept_id'];
		}
		 
		// если есть отделы, где пользователь является заместителем
		if($deputy_depts_arr)
		{
			$deputy_depts = implode(',', $deputy_depts_arr);
			
			// Выбираем сотрудников отдела
			$sql = "SELECT * FROM tasks_company_depts_users WHERE dept_id IN($deputy_depts) AND is_head=0 AND user_id!='$user_id'";
			
			$res = $site_db->query($sql);
			
			while($row=$site_db->fetch_array($res))
			{
				// не выводим в сотрудники, кто является руководителем
				if(in_array($row['user_id'], get_current_user_users_arrs(array(1,0,0,1,0))) || $row['user_id']==$user_id)
				{
					continue;
				}
				
				$deputy_workers_arr[$row['user_id']] = $row['user_id'];
			}
			
		} // print_r($deputy_workers_arr);
		
		return $deputy_workers_arr;
	}*/
		
	  
	return $workers_arr;
}

/*// Возвращает массив сотрудников пользователя
function get_user_workers_arr($user_id, $is_deputy=0)
{
	global $site_db, $current_user_id, $user_obj;
	
	$workers_arr = array();
	
	if($is_deputy)
	{
		$deputy_user = " AND deputy_id > 0";
	}
	else
	{
		$deputy_user = " AND i.invited_user_status=1 AND deputy_id = 0";
	}
	
	$sql = "SELECT invited_user FROM ".WORKERS_TB." i WHERE i.invite_user='$user_id'  AND i.deleted=0 $deputy_user";
	 
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$workers_arr[$row['invited_user']] = $row['invited_user'];
	}
	
	return $workers_arr;
}*/

// Кол-во всех сотрудников (в счет идут сотрудники сотрудников)
function get_all_workers_count($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	// Первоначально запихиваем переданного пользователя в массив пользователей
	$invited_users_arr[] = $user_id;
	
	while(!$stop)
	{
		// Формируем список для запроса
		$invited_users_ids = implode(',', $invited_users_arr);
		
		// Обнуляем массив пользователей
		$invited_users_arr = array();
		
		// Выбираем пользователей, которые приглашали других в свои подчиненные
		$sql = "SELECT invited_user FROM ".WORKERS_TB." WHERE invite_user IN($invited_users_ids) AND invited_user_status=1";
		
		$res = $site_db->query($sql);
			
		while($row=$site_db->fetch_array($res))
		{
			$result_users_ids[$row['invited_user']] = $row['invited_user'];
			
			$invited_users_arr[] = $row['invited_user'];
		}
		// Если пройдено вниз по дереву подчиненных, останавливаем цикл
		if(!$invited_users_arr || $num>3000)
		{
			$stop = true;
		}
		$num++;  
	}
	 
	return count($result_users_ids);
	 
}

/*
// Подчиненные подчиненных, которые являются временными
function get_all_deputy_workers_arr_for_user($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$invited_users_arr = array();
	$result_users_ids = array();
	
	// Выбираем начальника, для которого текущий пользователь является заместителем
	$sql = "SELECT deputy_id FROM ".DEPUTY_TB." WHERE deputy_user_id='$user_id' AND deleted<>1 AND deputy_confirm=1";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		// Выбираем всех пользователей в подчинении зама
		$sql = "SELECT * FROM ".DEPUTY_USERS_TB." WHERE deputy_id='".$row['deputy_id']."'";
		
		$res_1 = $site_db->query($sql);
		
		while($users_row=$site_db->fetch_array($res_1))
		{
			$invited_users_arr[] = $users_row['user_id'];
			$result_users_ids[$users_row['user_id']] = $users_row['user_id'];
		}
		 
	}

	
	while(!$stop && $invited_users_arr)
	{
		// Формируем список для запроса
		$invited_users_ids = implode(',', $invited_users_arr);
		
		// Обнуляем массив пользователей
		$invited_users_arr = array();
		
		// Выбираем пользователей, которые приглашали других в свои подчиненные
		$sql = "SELECT invited_user FROM ".WORKERS_TB." WHERE invite_user IN($invited_users_ids) AND invited_user_status=1";
		  
		$res = $site_db->query($sql);
			
		while($row=$site_db->fetch_array($res))
		{
			$result_users_ids[$row['invited_user']] = $row['invited_user'];
			
			$invited_users_arr[] = $row['invited_user'];
		}
		
		 
		// Если пройдено вниз по дереву подчиненных, останавливаем цикл
		if(!$invited_users_arr || $num>3000)
		{
			$stop = true;
		}
		 
		$num++; 
	}
	  
	return $result_users_ids;
}*/
// Возвращает массив всех подчиненных на всех уровнях
function get_all_workers_arr_for_user($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$workers_arr = array();
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_org.php';
	 
	$user_head_depts = worker_get_user_all_depts($user_id);
	
 
	$user_depts = array(); 
	 
	foreach($user_head_depts as $v => $dept_id)
	{  
		$user_depts  = array_merge(get_company_dept_childs($dept_id), $user_depts);
	}
	
	if($user_depts)
	{
		$depts_ids = implode(',', $user_depts);
		
		// Выбираем сотрудников отдела
		$sql = "SELECT * FROM tasks_company_depts_users WHERE dept_id IN($depts_ids) AND is_head=0 AND user_id!='$user_id'";
		
		$res = $site_db->query($sql);
		 
		while($row=$site_db->fetch_array($res))
		{
			$workers_arr[$row['user_id']] = $row['user_id'];
		}
		
	}
	 
	return $workers_arr;
	 
}

// Массив отделов, к которым пренадлежит пользователь
function worker_get_user_all_depts($user_id)
{
	global $site_db;
	
	$users_depts = array();
	 
	// выбор отдела, где пользователь является ЗАМЕСТИТЕЛЕМ
	$sql = "SELECT * FROM tasks_deputies WHERE deputy_user_id = '$user_id'";
	
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
		
		$users_depts[$row['dept_id']] = $row['dept_id'];
	}
	 
	
	// Выбор отделов, где пользователь является руководителем
	$sql = "SELECT * FROM tasks_company_depts_users i
			LEFT JOIN tasks_company_depts j ON i.dept_id=j.dept_id
			WHERE i.user_id='$user_id' AND i.is_head='1' ORDER by j.dept_name";

	
	$res = $site_db->query($sql);

				 
	while($row=$site_db->fetch_array($res, 1))
	{
		$users_depts[$row['dept_id']] = $row['dept_id'];
	}
	
	return $users_depts;
}

// Форма добавления планерки
function fill_add_planning_session_form()
{
	global $site_db, $current_user_id, $user_obj, $_CURRENT_USER_WORKERS_ARR, $_CURRENT_USER_DEPUTY_WORKERS_ARR;
	
	$main_tpl = file_get_contents('templates/workers/add_planning_session_form.tpl');
	
	$select_user_for_add_to_msg_group_tpl = file_get_contents('templates/workers/select_user_for_add_to_msg_group.tpl');
	
	if(!$_CURRENT_USER_WORKERS_ARR && !$_CURRENT_USER_DEPUTY_WORKERS_ARR)
	{
		return '';
	}
	
	// Массив подчиненных
	$users_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	
	foreach($users_arr as $user_id)
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

?>