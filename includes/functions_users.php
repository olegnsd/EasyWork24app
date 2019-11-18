<?php
// Страница данных пользователя
function fill_personal($user_id)
{
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tasks1.php';
	
	global $site_db, $current_user_id, $user_obj, $max_upload_user_image_resolution, $min_upload_user_image_resolution, $_CURRENT_USER_WORKERS_ARR, $_CURRENT_USER_BOSS_ARR, $current_user_obj;
	
	$personal_tpl = file_get_contents('templates/personal/personal.tpl');
	
	$avatar_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/avatar_block.tpl');
	
	$no_avatar_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/no_avatar_block.tpl');
	
	$avatar_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/avatar_edit_tools.tpl');
	
	$no_avatar_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/no_avatar_edit_tools.tpl');
	
	$remove_from_workers_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/remove_from_workers_block.tpl');
	
	$registered_by_name_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/registered_by_name.tpl');
	
	$personal_is_private_for_user_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/personal_is_private_for_user.tpl');
	
	$personal_edit_tools_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/personal_edit_tools_block.tpl');
	
	$personal_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/personal_edit_tools.tpl');
	
	$personal_user_bdate_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/personal_user_bdate.tpl');
	
	$send_msg_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/send_msg_btn.tpl');
	
	$ext_info_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/ext_info_block.tpl');
	
	$personal_ext_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/personal_ext.tpl');
	 
	$current_user_data = $current_user_obj->get_user_data();  
	
	// Проверяем доступность страницы для пользователя 
	if($user_id!=$current_user_id && !check_user_access_to_user_content($user_id, array(0,1,1,0,1,1)) && !$current_user_data['is_full_access'])
	{
		//return $personal_is_private_for_user_tpl;
	}
	
	if(check_user_access_to_user_content($user_id, array(0,1,0,0,1,1)) || $current_user_id==$user_id || $current_user_data['is_full_access'])
	{
		$PARS['{USER_EXT}'] = $personal_ext_tpl;
		$PARS['{EXT_INFO_BLOCK}'] = $ext_info_block_tpl;
	}
	else
	{
		$PARS['{USER_EXT}'] = '';
		$PARS['{EXT_INFO_BLOCK}'] = '';
	}
	 
	//$tasks_count_for_user = get_count_tasks_in_process_for_user($user_id, 0);
 
	if($user_id==$current_user_id)
	{ 
		$user_workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));
		$user_boss_arr = get_current_user_users_arrs(array(1,0,0,1,0));
	}
	else
	{
		$user_workers_arr = get_user_users_arrs($user_id, array(0,1,0,0,1));
		$user_boss_arr = get_user_users_arrs($user_id, array(1,0,0,1,0));	
	}
	
	// Массив сотрудников пользователя
	$workers_list_arr = fill_personal_users_in_rows_list($user_workers_arr);
	$workers_list = $workers_list_arr['visible'];
	$workers_list_hidden = $workers_list_arr['hidden'];
	$workers_list_more = $workers_list_arr['more_btn'];
	
	// Массив боссов пользователя
	$boss_list_arr = fill_personal_users_in_rows_list($user_boss_arr);
	$boss_list = $boss_list_arr['visible'];
	$boss_list_hidden = $boss_list_arr['hidden'];
	$boss_list_more = $boss_list_arr['more_btn'];
	
	
	
	// Данные пользователя
	$user_obj->fill_user_data($user_id);
	
	// Если указан день рождения
	if($user_obj->get_user_bdate('date_rus'))
	{
		$PARS_1['{BDATE}'] = $user_obj->get_user_bdate('date_rus');
		
		$user_bdate = fetch_tpl($PARS_1, $personal_user_bdate_tpl);
	}
	
	$user_name = $user_obj->get_user_name();
		
	$user_middlename = $user_obj->get_user_middlename();
		
	$user_surname = $user_obj->get_user_surname();
		
	$user_position = $user_obj->get_user_position();
	
	$user_job_id = $user_obj->get_user_job_id();
	
	$user_phone = $user_obj->get_user_phone();
	
 	# Аватар
	// Изображение пользователя
	$user_image = $user_obj->get_user_image();
	  
	// Если нет изображения
	if($user_image)
	{
		$image_name = 'avatar_'.$user_image['image_name'];
		
		$image_src = get_file_dir_url($user_image['date_add'], $image_name);
		
		$avatar_edit_tools = '';
		
		$PARS1['{IMAGE_SRC}'] = $image_src;
		
		$PARS1['{USER_ID}'] = $user_id;
		
		$PARS1['{AVATAR_EDIT_TOOLS}']= $avatar_edit_tools;
		
		$user_avatar = fetch_tpl($PARS1, $avatar_block_tpl);
	}
	else
	{
		$avatar_edit_tools = '';
		
		// Если пользователь является владельцем страницы
		if($user_id==$current_user_id)
		{
			$PARS2['{USER_ID}'] = $user_id;
			
			$avatar_edit_tools = fetch_tpl($PARS2, $no_avatar_edit_tools_tpl);
		}
		
		$PARS1['{AVATAR_EDIT_TOOLS}']= $avatar_edit_tools;
		
		$user_avatar = fetch_tpl($PARS1, $no_avatar_block_tpl);
		 
	}
	
	### Ссылки на редактирование
	$PARS2['{USER_ID}'] = $user_id;
	// Если пользователь является владельцем страницы и аватарка загружена
	if($user_id==$current_user_id && $user_image)
	{	
		$avatar_edit_tool = fetch_tpl($PARS2, $avatar_edit_tools_tpl);
	}
	// Владелец страницы или начальник
	if($user_id==$current_user_id || $current_user_obj->get_is_admin())
	{
		$personal_edit_tool = fetch_tpl($PARS2, $personal_edit_tools_tpl);
	}
	
	// Кнопка отстранить от работы
	//$remove_from_work_tool = fill_remove_from_work_btn($user_id);
	
	$PARS_2['{AVATAR_EDIT}'] = $avatar_edit_tool;
	$PARS_2['{PERSONAL_EDIT}'] = $personal_edit_tool;
	//$PARS_2['{REMOVE_FROM_WORK}'] = $remove_from_work_tool;
	$personal_edit_tools = fetch_tpl($PARS_2, $personal_edit_tools_block_tpl);
	### 
	
	
	/*// Пользователь, который зарегистрировал в системе
	$user_obj_1 = new CUser($site_db);
	 
	// Кто зарегистрировла в системе
	$user_obj_1->fill_user_data($user_obj->get_user_registrated_by_user_id());
	
	$registered_by_user_name = $user_obj_1->get_user_name();
		
	$registered_by_user_middlename = $user_obj_1->get_user_middlename();
		
	$registered_by_user_surname = $user_obj_1->get_user_surname();
		
	$registered_by_user_position = $user_obj_1->get_user_position();
	 
	if($registered_by_user_surname)
	{
		$PARS['{REGISTERED_BY_USER_ID}'] = $user_obj->get_user_registrated_by_user_id();
		$PARS['{REGISTERED_BY_USER_SURNAME}'] = $registered_by_user_surname;
		$PARS['{REGISTERED_BY_USER_NAME}'] = $registered_by_user_name;
		$PARS['{REGISTERED_BY_USER_MIDDLENAME}'] = $registered_by_user_middlename;
		$PARS['{REGISTERED_BY_USER_POSITION}'] = $registered_by_user_position;
		
		$registered_by_user = fetch_tpl($PARS, $registered_by_name_tpl);
	}
	else
	{
		$registered_by_user = 'Нет';
	}*/
	
	if($user_id==$current_user_id || check_user_access_to_user_content($user_id, array(0,1,0,0,1,1)) || $current_user_data['is_full_access'])
	{
		// Блок отзывов
		$comment_block = fill_comments_block($user_id);
		
		// Блок отчетов по кругу обязанностей
		$work_reports_block = fill_work_reports_block($user_id);
		
		// Блок выговоров
		$reprimands_block = fill_reprimands_block_on_main($user_id);
		
		$finances_block = fill_user_finances_block($user_id);
		
		// Календарь планирования пользователя
		$user_planning_calendar = fill_user_planning_calendar($user_id, '', 1);
	}
	 
	
	// Кол-во активных задач  для пользователя
	$tasks_count_for_user = get_user_active_tasks_count($user_id) ;
	
	// Кол-во активных задач, которые пользователь назначил
	$tasks_count_from_user = get_tasks_from_user_count($user_id, 0);
	
	// Количество всех выполненных задач сотрудника
	$tasks_completed_count_all = get_user_completed_tasks_all_count($user_id);
	
	// Количество всех выполненных задач сотрудника за месяц
	$tasks_completed_count_n_days = get_user_completed_tasks_all_count($user_id, 30);
	
	// Среднее значение оценок качества проделанной работы за 30 дней
	$task_average_quality = get_user_tasks_average_rating($user_id, 30, 0);
	
	$task_average_quality = $task_average_quality ? $task_average_quality : 0;

	
	// Блок оценки эффективности
	$user_efficiency_block = fill_user_efficiency_block($user_id);
	
	// Блок ВЫПОЛНЕННЫХ задач
	$user_efficiency_complete_tasks_block = fill_user_efficiency_tasks_block($user_id, 1);
	
	// Блок ПОСТАВЛЕННЫХ задач
	$user_efficiency_tasks_block = fill_user_efficiency_tasks_block($user_id);
	
	if($current_user_id!=$user_id && check_for_send_msg($user_id, $current_user_id))
	{
		$PARS['{USER_ID}'] = $user_id;
		$reg_data_sms = fetch_tpl($PARS, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/send_reg_data_1.tpl'));
	}
	
	if($current_user_id!=$user_id)
	{
		$PARS['{USER_ID}'] = $user_id;
		$send_msg_btn = fetch_tpl($PARS, $send_msg_btn_tpl);
	}
	
	// статус пользователя
	$user_status = fill_users_status_for_worker($user_id);
	
	//
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_calendar_of_events.php';
	$evcal_notice = fill_evcal_notice_block($user_id, 1);
	
	if($current_user_id==$user_id)
	{
		$active_tasks = fill_active_tasks($user_id, 1);
	}
	else
	{
		$active_tasks = fill_active_tasks($user_id);
	}  
	 
	$PARS['{USER_STATUS}'] = $user_status;
	
	$PARS['{REG_DATA_SMS}'] = $reg_data_sms;
	
	$PARS['{USER_PHONE}'] = $user_phone;
	
	$PARS['{SEND_MSG_BTN}'] = $send_msg_btn;
	
	$PARS['{USER_SURNAME}'] = $user_surname;
	$PARS['{USER_NAME}'] = $user_name;
	$PARS['{USER_MIDDLENAME}'] = $user_middlename;
	$PARS['{USER_POSITION}'] = $user_position;
	$PARS['{USER_BDATE}'] = $user_bdate;
	 
	$PARS['{USER_EFFICIENCY_BLOCK}'] = $user_efficiency_block;
	
	$PARS['{USER_EFFICIENCY_TASKS_COMPLETED_BLOCK}'] = $user_efficiency_complete_tasks_block;
	
	$PARS['{USER_EFFICIENCY_TASKS_BLOCK}'] = $user_efficiency_tasks_block;
	
	$PARS['{TASKS_FROM_USER_CHART}'] = $tasks_from_user_chart;
	
	$PARS['{ALL_WORKERS_COUNT}'] = $all_workers_count;
	
	$PARS['{TASKS_AVERAGE_QUALITY}'] = $task_average_quality;
	
	$PARS['{TASKS_COMPLETED_COUNT_ALL}'] = $tasks_completed_count_all;
	
	$PARS['{TASKS_COMPLETED_COUNT_N_DAYS}'] = $tasks_completed_count_n_days;
	
	$PARS['{REMOVE_FROM_WORKERS_BLOCK}'] = $remove_from_workers_block;
		
	$PARS['{TASK_LIST_COMPLETED}'] = $task_completed_list;
	
	$PARS['{REGISTERED_BY_USER}'] = $registered_by_user;
	
	$PARS['{COMMENTS_BLOCK}'] = $comment_block;
	 
	$PARS['{WORK_REPORTS_BLOCK}'] = $work_reports_block;
	
	$PARS['{REPRIMANDS_BLOCK}'] = $reprimands_block;
	
	$PARS['{FINANCES_BLOCK}'] = $finances_block;
	
	$PARS['{TASKS_BLOCK}'] = $tasks_list;
	
	$PARS['{PLANNING_CALENDAR}'] = $user_planning_calendar;
	
	$PARS['{USER_AVATAR_BLOCK}'] = $user_avatar;
	
	$PARS['{PERSONAL_EDIT_TOOLS}'] = $personal_edit_tools;
	
	$PARS['{NAV}'] = $nav;
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{USER_JOB_ID}'] = $user_job_id;
	
	$PARS['{TASKS_COUNT_FOR_USER}'] = $tasks_count_for_user;
	
	$PARS['{TASKS_COUNT_FROM_USER}'] = $tasks_count_from_user;
	
	$PARS['{BOSS_LIST}'] = $boss_list;
	
	$PARS['{BOSS_LIST_HIDDEN}'] = $boss_list_hidden;
	
	$PARS['{BOSS_LIST_MORE}'] = $boss_list_more;
	
	$PARS['{WORKERS_LIST}'] = $workers_list;
	
	$PARS['{WORKERS_LIST_HIDDEN}'] = $workers_list_hidden;
	
	$PARS['{WORKERS_LIST_MORE}'] = $workers_list_more;
	
	$PARS['{MAX_UPLOAD_SIZE}'] = ini_get('max_upload_size');
	
	$PARS['{MAX_IMAGE_RESOLUTION}'] = $max_upload_user_image_resolution;
	
	$PARS['{MIN_IMAGE_RESOLUTION}'] = $min_upload_user_image_resolution;
	
	$PARS['{RAND}'] = rand(1,100000000);
	
	$PARS['{EVCAL_NOTICE}'] = $evcal_notice;
	
	$PARS['{ACTIVE_TASKS}'] = $active_tasks;
	
	return fetch_tpl($PARS, $personal_tpl);
}

function fill_active_tasks($user_id, $own)
{ 
	$active_tasks_tpl = file_get_contents('templates/personal/active_tasks.tpl');
	
	$tasks_list_no_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/tasks_list_no.tpl');
	
	if($own)
	{
		// список задач
		$tasks_list = fill_tasks_list(2, 1, 1);
	}
	else
	{
		// список задач
		$tasks_list = fill_tasks_list(5, 1, 1, '', $user_id);
	}
	 
	
	// Если список задач пустой
	if(!$tasks_list)
	{   
		$tasks_list = $tasks_list_no_tpl;
	}
	
	$PARS['{TASKS_LIST}'] = $tasks_list;
	
	return fetch_tpl($PARS, $active_tasks_tpl);
}

function fill_user_finances_block($user_id)
{
	$user_finances_block_tpl = file_get_contents('templates/personal/user_finances_block.tpl');
	
	// Блок переданных финансов
	$money_block = fill_money_block_on_main($user_id);
		
	// Блок переданных финансов
	$accruals_block = fill_accruals_block_on_main($user_id);
		
	// Блок задолженности перед сотрудником
	$accruals_not_paid_block = fill_accruals_not_paid_block_on_main($user_id);
	
	if(!$money_block && !$accruals_block && !$accruals_not_paid_block)
	{
		return '';
	}
	
	$PARS['{MONEY_BLOCK}'] = $money_block;
	
	$PARS['{ACCRUALS_BLOCK}'] = $accruals_block;
	
	$PARS['{ACCRUALS_NOT_PAID_BLOCK}'] = $accruals_not_paid_block;
	
	return fetch_tpl($PARS, $user_finances_block_tpl);
}
// График поставленных задач 
function fill_chart_tasks_from_user($user_id)
{
	global $site_db, $current_user_id;
	
	$chart_tasks_from_user_30day_tpl = file_get_contents('templates/personal/chart_tasks_from_user_30day.tpl');
	
	$tmp_date = time() - 3600 * 24 * 30;
	
	$date_from = date('Y-m-d 00:00:00', $tmp_date);
	
	$date_to = date('Y-m-d 23:59:59');
	 
	// Выбираем все задачи, который пользователь выставлял
	$sql = "SELECT task_date_add FROM ".TASKS_TB." i
			WHERE i.task_deleted<>1 AND i.task_from_user='$user_id' AND task_to_user <> '$user_id'
			AND task_date_add>='$date_from' AND task_deleted <> 1";
					
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res, 1))
	{
		// Выбираем дату без времени
		$date_hour = substr($row['task_date_add'],0,10);
		 
		$tasks_arr[$date_hour] += 1;
	}
	
	// Не выводим пустой график
	if(!$tasks_arr)
	{
		return '';
	}
	
	// Первый день на графике 
	$start_day = to_mktime($date_from); 
	
	// Формируем ось х
	for($i=0; $i<31; $i++)
	{
		$date_s = date('Y-m-d', $start_day);
		 
		// Если за текущий час есть обновленные статусы, то суммируем их, иначе ставим 0
		if($tasks_arr[$date_s])
		{
			$SERIES_DATA_ARR[] = series_data($date_s, $tasks_arr[$date_s]);
		}
		else
		{
			$SERIES_DATA_ARR[] = series_data($date_s, 0);
		}
		
		// Увеличиваем на 1 день
		$start_day += 24 * 3600;
	}
	 
	  
	if($SERIES_DATA_ARR)
	{
		$series = '['.implode(',', $SERIES_DATA_ARR).']';
	}
	else
	{
		return '';
	}
	
 	// Отображать в графике время старта 
	$date_start = get_date_utc_for_js_object($date_from);
	 
	$PARS['{SERIES}'] = $series;
	 
	$PARS['{SERIES_DATE_START}'] = $date_start;
	
	return fetch_tpl($PARS, $chart_tasks_from_user_30day_tpl);
}

// Кнопка отстранить от работы или вернуть к работе 
function fill_remove_from_work_btn($user_id)
{
	global $site_db, $current_user_id;
	
	$personal_remove_from_work_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/personal_remove_from_work.tpl');
	
	$personal_not_remove_from_work_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/personal_not_remove_from_work.tpl');
	
	// Проверяем, отстранен ли пользователь от работы
	$sql = "SELECT * FROM ".REMOVE_FROM_WORK_TB." WHERE user_id='$user_id'";
		
	$row = $site_db->query_firstrow($sql);
	
	$PARS['{USER_ID}'] = $user_id;
	
	// Начальнику выводим кнопку Отстранить от работы
	if($current_user_id!=$user_id && check_user_access_to_user_content($user_id, array(0,1,0,0,1)) && !$row['id'])
	{
		$remove_from_work_tool = fetch_tpl($PARS, $personal_remove_from_work_tpl);
	}
	else if($current_user_id!=$user_id && check_user_access_to_user_content($user_id, array(0,1,0,0,1)))
	{ 
		$remove_from_work_tool = fetch_tpl($PARS, $personal_not_remove_from_work_tpl);	
	}
	
	return $remove_from_work_tool;
}

// Блок активных задач
function fill_tasks_list_on_main($user_id)
{
	global $site_db, $user_obj, $current_user_id;
	
	$mk_time_days_from = mktime() - 3600 * 24 * 30;
	
	$date_s = date('Y-m-d', $mk_time_days_from);
	
	$block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/tasks_block.tpl');
	
	$more_btn = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/tasks_more_btn.tpl');
	
	//fill_worker_tasks_list();
	$actual_date = date('Y-m-d');
	// Переданные внутринние финансы
	// Задачи пользователя
		$sql = "SELECT *, if(task_status=3,1,0) as order_by_task_status FROM ".TASKS_TB." 
				WHERE task_to_user='$user_id' AND task_deleted<> 1 AND task_status IN (0,1,2)
				ORDER by task_id DESC";	
	  
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{
		$tasks_list_arr[] = fill_worker_tasks_list_item($row, 1);
	}
	// Разбиваем две части - которую выводим и которую прячем
	if(count($tasks_list_arr) > LIST_PER_PAGE_ON_MAIN)
	{
		$list_visible = array_slice($tasks_list_arr,0,LIST_PER_PAGE_ON_MAIN);
		$list_hidden = array_slice($tasks_list_arr, LIST_PER_PAGE_ON_MAIN);
		$list_visible = implode('', $list_visible);
		$list_hidden = implode('', $list_hidden);
	}
	else
	{
		$list_visible = implode('', $tasks_list_arr);
	}
	
	if(!$list_visible)
	{
		return '';
	}
	
	if($list_hidden)
	{
		$completed_btn = $more_btn;
	}
	
	$PARS['{TASKS_LIST_VISIBLE}'] = $list_visible;
	
	$PARS['{TASKS_LIST_HIDDEN}'] = $list_hidden;
	
	$PARS['{MORE_BTN}'] = $completed_btn;
	
	return fetch_tpl($PARS, $block_tpl);
}

// Блок суммы всех начислений, которые неоплачены
function fill_accruals_not_paid_block_on_main($user_id)
{
	global $site_db, $user_obj, $current_user_id;
	
	$accruals_not_paid_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/accruals_not_paid_block.tpl');
	
	// Выбираем все неоплаченные начисления для пользователя, которые неоплачены
	$sql = "SELECT * FROM ".MONEY_ACCRUALS_TB." WHERE to_user_id='$user_id' AND deleted<>1 AND paid = 0";
	
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		// Если штраф
		if($row['type_id']==3)
		{
			$result_accruals_sum -= $row['summa'];
		}
		else
		{
			$result_accruals_sum += $row['summa'];
		}
	}
	
	if($result_accruals_sum=='')
	{
		return '';
	}
	
	$PARS['{RESULT_SUM}'] = sum_process($result_accruals_sum);
	
	
	return fetch_tpl($PARS, $accruals_not_paid_block_tpl);
}


// Блок начислений
function fill_accruals_block_on_main($user_id)
{
	global $site_db, $user_obj, $current_user_id;
	
	$mk_time_days_from = mktime() - 3600 * 24 * 30;
	
	$date_s = date('Y-m-d', $mk_time_days_from);
	
	$block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/accruals_block.tpl');
	
	//$block_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/money_list_item.tpl');
	
	$more_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/accruals_more_btn.tpl');
	
	$sql = "SELECT * FROM ".MONEY_ACCRUALS_TB." WHERE to_user_id='$user_id' AND deleted<>1 AND date>='$date_s' ORDER by accrual_id DESC ";
	
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		$accruals_list_arr[] = fill_accruals_list_item($row, 1);
		
		// Если штраф
		if($row['type_id']==3)
		{
			$result_accruals_sum -= $row['summa'];
		}
		else
		{
			$result_accruals_sum += $row['summa'];
		}
	}
	
	// Разбиваем две части - которую выводим и которую прячем
	if(count($accruals_list_arr) > LIST_PER_PAGE_ON_MAIN)
	{
		$list_visible = array_slice($accruals_list_arr,0,LIST_PER_PAGE_ON_MAIN);
		$list_hidden = array_slice($accruals_list_arr, LIST_PER_PAGE_ON_MAIN);
		$list_visible = implode('', $list_visible);
		$list_hidden = implode('', $list_hidden);
	}
	else
	{
		$list_visible = implode('', $accruals_list_arr);
	}
	
	if(!$list_visible)
	{
		return '';
	}
	
	if($list_hidden)
	{
		$more_btn = $more_btn_tpl;
	}
	
	$PARS['{LIST_VISIBLE}'] = $list_visible;
	
	$PARS['{LIST_HIDDEN}'] = $list_hidden;
	
	$PARS['{MORE_BTN}'] = $more_btn;
	
	$PARS['{ACCRUALS_SUM_30_DAYS}'] = sum_process($result_accruals_sum);
	
	return fetch_tpl($PARS, $block_tpl);
}
// Блок переданных финансов
function fill_money_block_on_main($user_id)
{
	global $site_db, $user_obj, $current_user_id;
	
	$mk_time_days_from = mktime() - 3600 * 24 * 30;
	 
	$date_s = date('Y-m-d', $mk_time_days_from);
	
	$block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/money_block.tpl');
	
	$more_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/money_more_btn.tpl');
	
	// Переданные внутринние финансы
	$sql = "SELECT i.* FROM ".MONEY_TB." i
			WHERE  money_to_user_id = '$user_id' AND money_deleted<>1 AND money_date>='$date_s' ORDER by money_id DESC";
	 
	$res = $site_db->query($sql);
	  
	while($row=$site_db->fetch_array($res))
	{ 
		$money_list_arr[] = fill_money_list_item($row, 1);
		
		if($row['has_accruals'])
		{
			// Получаем начисления, которые вошли в выплату
			$accrual_arr = get_accruals_arr_for_money($row['money_id']);
			
			foreach($accrual_arr as $accrual_data)
			{
				// Если штраф
				if($accrual_data['type_id']==3)
				{
					$result_sum -= $accrual_data['summa'];
				}
				else
				{
					$result_sum += $accrual_data['summa'];
				}
			}	
		}
		else
		{
			// Подводим итог
			$result_sum += $row['money_summa'];
		}
	}
	// Разбиваем две части - которую выводим и которую прячем
	if(count($money_list_arr) > LIST_PER_PAGE_ON_MAIN)
	{
		$list_visible = array_slice($money_list_arr,0,LIST_PER_PAGE_ON_MAIN);
		$list_hidden = array_slice($money_list_arr, LIST_PER_PAGE_ON_MAIN);
		$list_visible = implode('', $list_visible);
		$list_hidden = implode('', $list_hidden);
		 
	}
	else
	{
		$list_visible = implode('', $money_list_arr);
	}
	
	if(!$list_visible)
	{
		return '';
	}
	 
	if($list_hidden)
	{
		$more_btn = $more_btn_tpl;
	}
	
	$PARS['{MONEY_LIST_VISIBLE}'] = $list_visible;
	
	$PARS['{MONEY_LIST_HIDDEN}'] = $list_hidden;
	
	$PARS['{MORE_BTN}'] = $more_btn;
	
	$PARS['{RESULT_SUM}'] = sum_process($result_sum);
	
	
	return fetch_tpl($PARS, $block_tpl);
}

// Блок выговоров
function fill_reprimands_block_on_main($user_id)
{
	global $site_db, $user_obj, $current_user_id;
	
	$block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/reprimands_block.tpl');
	
	$more_btn = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/reprimands_more_btn.tpl');
	
	// Выговоры
	$sql = "SELECT * FROM ".REPRIMANDS_TB." WHERE worker_id='$user_id' AND deleted<>1 ORDER by reprimand_id DESC";
	 
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{
		$work_reports_list_arr[] = fill_reprimand_list_item($row, 1);
	}
	// Разбиваем две части - которую выводим и которую прячем
	if(count($work_reports_list_arr) > LIST_PER_PAGE_ON_MAIN)
	{
		$list_visible = array_slice($work_reports_list_arr,0,LIST_PER_PAGE_ON_MAIN);
		$list_hidden = array_slice($work_reports_list_arr, LIST_PER_PAGE_ON_MAIN);
		$list_visible = implode('', $list_visible);
		$list_hidden = implode('', $list_hidden);
	}
	else
	{
		$list_visible = implode('', $work_reports_list_arr);
	}
	
	if(!$list_visible)
	{
		return '';
	}
	
	if($list_hidden)
	{
		$completed_btn = $more_btn;
	}
	
	$PARS['{REPRIMANDS_LIST_VISIBLE}'] = $list_visible;
	
	$PARS['{REPRIMANDS_LIST_HIDDEN}'] = $list_hidden;
	
	$PARS['{MORE_BTN}'] = $completed_btn;
	
	
	return fetch_tpl($PARS, $block_tpl);
}
 
// Блок отчетов за 30 дней
function fill_work_reports_block($user_id)
{
	global $site_db, $user_obj, $current_user_id;
	
	$work_reports_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/work_reports_block.tpl');
	
	$work_reports_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/work_reports_list_item.tpl');
	
	$work_reports_more_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/work_reports_more_btn.tpl');
	
	$mk_time_days_from = mktime() - 3600 * 24 * 30;
	
	$date_s = date('Y-m-d', $mk_time_days_from);
	
	// Выбираем отчеты по кругу обязанностей за последние 30 дней
	$sql = "SELECT * FROM ".WORK_REPORTS_TB." WHERE report_from_user_id='$user_id' AND report_date>='$date_s' ORDER by report_id DESC";
	 
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{
		// Список файлов для отчета
		//$files_list = fill_work_report_files_list($row['report_id']);

        // Список файлов для отчета
        $files_list = get_attached_files_to_content($row['report_id'], 1);

		$PARS['{TEXT}'] = nl2br($row['report_text']);
		
		$PARS['{DATE}'] = datetime($row['report_date'], '%j %M в %H:%i');
		
		$PARS['{FILES_LIST}'] = $files_list;
		
		$work_reports_list_arr[] = fetch_tpl($PARS, $work_reports_list_item_tpl);
	}
	
	// Разбиваем две части - которую выводим и которую прячем
	if(count($work_reports_list_arr) > LIST_PER_PAGE_ON_MAIN)
	{
		$list_visible = array_slice($work_reports_list_arr,0,LIST_PER_PAGE_ON_MAIN);
		$list_hidden = array_slice($work_reports_list_arr, LIST_PER_PAGE_ON_MAIN);
		$list_visible = implode('', $list_visible);
		$list_hidden = implode('', $list_hidden);
	}
	else
	{
		$list_visible = implode('', $work_reports_list_arr);
	}
	
	if(!$list_visible)
	{
		return '';
	}
	
	if($list_hidden)
	{
		$completed_btn = $work_reports_more_btn_tpl;
	}
	
	$PARS['{WORK_REPORTS_COMPLETED_VISIBLE}'] = $list_visible;
	
	$PARS['{WORK_REPORTS_LIST_HIDDEN}'] = $list_hidden;
	
	$PARS['{MORE_BTN}'] = $completed_btn;
	
	
	return fetch_tpl($PARS, $work_reports_block_tpl);
}

// Блок отзывов
function fill_comments_block($user_id)
{
	global $site_db, $user_obj, $current_user_id;
	
	$comment_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/comment_block.tpl');
	
	$comments_more_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/comments_more_btn.tpl');
	
	$comment_add_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/comments/comment_add_form.tpl');
	
	// 
	if($current_user_id==$user_id)
	{
		return '';
	}
	
	// Отзывы
	$comments_list_arr = fill_user_comments_list($user_id, 1);
	$comments_list_visible = $comments_list_arr['visible'];
	$comments_list_hidden = $comments_list_arr['hidden'];
	
	if($comments_list_hidden)
	{
		$more_comments = $comments_more_btn_tpl;
	}
	
	// Если еще не оставлял отзыв данному сотурднику
	if(!is_comment_for_user_by_user($current_user_id, $user_id) && check_user_access_to_user_content($user_id, array(0,1,0,0,1)))
	{  
		$PARS_2['{USER_ID}'] = $user_id;
		
		$comment_add_form = fetch_tpl($PARS_2, $comment_add_form_tpl);
	}
	
	$PARS['{COMMENT_ADD_FORM}'] = $comment_add_form;
	
	$PARS['{COMMENTS_LIST_VISIBLE}'] = $comments_list_visible;
	
	$PARS['{COMMENTS_LIST_HIDDEN}'] = $comments_list_hidden;
	
	$PARS['{COMMENTS_MORE}'] = $more_comments;
	
	return fetch_tpl($PARS, $comment_block_tpl);
}

// Возвращает список выполненных заданий за последние
function fill_tasks_completed_list_for_user_personal_page($user_id, $without_myself_task=1, $limit_days)
{
	global $site_db, $user_obj;
	
	$tasks_completed_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/tasks_completed.tpl');
	
	$personal_user_task_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/personal_user_task_item.tpl');
	
	$completed_more_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/completed_more_btn.tpl');
	
	// Выполненная работа
	$task_completed_arr = fill_user_work_completed_list($user_id, $limit_days, 'date_desc', $without_myself_task);
	
	foreach($task_completed_arr as $task_data)
	{
		// Если не указана тема задания - выводим часть самого задания
		if(trim($task_data['task_theme'])!='')
		{
			$task_text = $task_data['task_theme'];
		}
		else if(strlen($task_data['task_text']) > 200)
		{ 
			if(strpos($task_data['task_text'], ' ', 200) > 0)
			{
				$task_text = nl2br(substr($task_data['task_text'], 0, strpos($task_data['task_text'], ' ', 190)).'..');
			}
			else
			{
				$task_text = nl2br(substr($task_data['task_text'], 0, 200).'..');
			}
		}
		else
		{
			$task_text = nl2br(trim($task_data['task_text']));
		}
		
		$finished_date = datetime($task_data['task_finished_date'], '%j %M в %H:%i');
		
		// Данные пользователя
		$user_obj->fill_user_data($task_data['task_from_user']);
		
		$task_quality = $task_data['task_quality'] ? $task_data['task_quality'] : 'Не выставлена'; 
		 
		$from_user_surname = $user_obj->get_user_surname();
		
		$from_user_name = $user_obj->get_user_name();
			
		$from_user_middlename = $user_obj->get_user_middlename();
			
		$from_user_position = $user_obj->get_user_position();
	
		// Превью аватарки пользователя
		$user_avatar_src = get_user_preview_avatar_src($task_data['task_from_user'], $user_obj->get_user_image());
		
		$PARS['{AVATAR_SRC}'] = $user_avatar_src;
		$PARS['{FROM_USER_ID}'] = $task_data['task_from_user'];
		$PARS['{FROM_USER_SURNAME}'] = $from_user_surname;
		$PARS['{FROM_USER_NAME}'] = $from_user_name;
		$PARS['{FROM_USER_MIDDLENAME}'] = $from_user_middlename;
		$PARS['{FROM_USER_POSITION}'] = $from_user_position;
		
		$PARS['{TASK_QUALITY}'] = $task_quality;
		
		$PARS['{TASK_TEXT}'] =  $task_text;
		
		$PARS['{FINISHED_DATE}'] =  $finished_date;
		
		$task_completed_list[] = fetch_tpl($PARS, $personal_user_task_item_tpl);
	}
	 
	// Разбиваем две части - которую выводим и которую прячем
	if(count($task_completed_list) > TASKS_COMPLETED_ON_MAIN_PER_PAGE)
	{
		$task_completed_list_visible = array_slice($task_completed_list,0,TASKS_COMPLETED_ON_MAIN_PER_PAGE);
		$task_completed_list_hidden = array_slice($task_completed_list, TASKS_COMPLETED_ON_MAIN_PER_PAGE);
		$task_completed_list_visible = implode('', $task_completed_list_visible);
		$task_completed_list_hidden = implode('', $task_completed_list_hidden);
	}
	else
	{
		$task_completed_list_visible = implode('', $task_completed_list);
	}
	
	if(!$task_completed_list_visible)
	{
		return '';
	}
	
	if($task_completed_list_hidden)
	{
		$completed_btn = $completed_more_btn_tpl;
	}
	
	$PARS['{TASK_LIST_COMPLETED_VISIBLE}'] = $task_completed_list_visible;
	
	$PARS['{TASK_LIST_COMPLETED_HIDDEN}'] = $task_completed_list_hidden;
	
	$PARS['{COMPLETED_MORE}'] = $completed_btn;
	
	if($task_completed_list_visible)
	{
		return fetch_tpl($PARS, $tasks_completed_tpl);
	}
	
	else return '';
}


function fill_personal_users_in_rows_list($users_arr)
{
	global $user_obj;
	
	$personal_user_item_row_tpl = file_get_contents('templates/personal/personal_user_item_row.tpl');
	
	$no_users_in_rows_tpl = file_get_contents('templates/personal/no_users_in_rows.tpl');
	
	$users_list_more_btn_tpl = file_get_contents('templates/personal/users_list_more_btn.tpl');
	
	foreach($users_arr as $user_id)
	{
		$user_obj->fill_user_data($user_id);
		
		$PARS1['{USER_ID}'] = $user_id;
		
		$PARS1['{NAME}'] = $user_obj->get_user_name();
		
		$PARS1['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS1['{SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS1['{USER_POSITION}'] = $user_obj->get_user_position();
	
		$users_list[] = fetch_tpl($PARS1, $personal_user_item_row_tpl);
	}
	
	
	$count = count($users_list);
	
	if($count>2)
	{
		$users_list_visible = array_slice($users_list,0,2);
		$users_list_hidden = array_slice($users_list,2);
		
		$users_list_visible = implode('', $users_list_visible);
		$users_list_hidden = implode('', $users_list_hidden);
		
		$more_btn = $users_list_more_btn_tpl;
	}
	else
	{
		$users_list_visible = implode('', $users_list);
	}
	
	if(!$users_list_visible)
	{
		$users_list_visible = $no_users_in_rows_tpl;
	}
	
	return array('visible' => $users_list_visible, 'hidden' => $users_list_hidden, 'more_btn' => $more_btn);
}

// Возвращает форму загрузки\ редактирования фотографии пользователя
function get_user_image_upload_form($user_id)
{
	global $site_db, $current_user_id, $user_obj, $max_upload_user_image_resolution, $min_upload_user_image_resolution;
	
	$user_image_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/personal/user_image_form.tpl');
	
	// Проверяем, есть ли у пользователя загруженная фотография 
	$sql = "SELECT * FROM ".USER_IMAGES_TB." WHERE user_id='$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	$init_crop_coord_x = 50;
	$init_crop_coord_y = 50;
	$init_crop_coord_w = 150;
	$init_crop_coord_h = 150;
	
	$image_form_display = 'none';
	// 
	if($row['image_id'])
	{
		$user_has_image = 1;
		
		 
		//$image_src = '/'.UPLOAD_FOLDER.'/users/'.$user_id.'/'.$row['image_name'];
		 
		$image_src = get_file_dir_url($row['date_add'], $row['image_name']);
		
		
		$sql = "SELECT * FROM ".USER_IMAGES_COORDS_TB." WHERE image_id='".$row['image_id']."'"; 
		
		$row = $site_db->query_firstrow($sql);
		
		$init_crop_coord_x = $row['coord_x'];
		$init_crop_coord_y = $row['coord_y'];
		$init_crop_coord_w = $row['coord_x2'];
		$init_crop_coord_h = $row['coord_y2'];
		$image_form_display = 'block';
		
	}
	
	$PARS['{IMAGE_FORM_DISPLAY}'] = $image_form_display;
	
	$PARS['{INIT_CROP_COORD_X}'] = $init_crop_coord_x;
	
	$PARS['{INIT_CROP_COORD_Y}'] = $init_crop_coord_y;
	
	$PARS['{INIT_CROP_COORD_X2}'] = $init_crop_coord_w;
	
	$PARS['{INIT_CROP_COORD_Y2}'] = $init_crop_coord_h;
	
	$PARS['{IMAGE_SRC}'] = $image_src;
	
	$PARS['{MAX_UPLOAD_SIZE}'] = (int )ini_get('upload_max_filesize');
	
	$PARS['{MAX_IMAGE_RESOLUTION}'] = $max_upload_user_image_resolution;
	
	$PARS['{MIN_IMAGE_RESOLUTION}'] = $min_upload_user_image_resolution;
	
	return fetch_tpl($PARS, $user_image_form_tpl);
}

// Возвращает ссылку на аватарку пользователя
function get_user_preview_avatar_src($user_id, $image_data)
{
	if($image_data['image_id'])
	{
		$image_name = 'preview_avatar_'.$image_data['image_name'];
		
		$user_avatar_src = get_file_dir_url($image_data['date_add'], $image_name);
	 	
		//$user_avatar_src = '/'.UPLOAD_FOLDER.'/users/'.$user_id.'/preview_avatar.jpg';
	}
	else
	{
		$user_avatar_src = '/img/no_preview_avatar.jpg';
	}
	return $user_avatar_src;
}


// Страница настроек пользователя
function fill_user_settings()
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	//echo $_GET['id'];

	$user_id = $_GET['id'] ? $_GET['id'] : $current_user_id;
	
	$settings_tpl = file_get_contents('templates/user/settings.tpl');
    $settings_mielofon_tab_tpl = file_get_contents('templates/user/settings_mielofon_tab.tpl');


	

	if($user_id!=$current_user_id && !$current_user_obj->get_is_admin())
	{
		header('Location: /');
	}
	
	if($_GET['a']=='nt')
	{
		$active_tab_3 = 'active';
		$settings = fill_settings_notices($user_id);
	}
    else if($_GET['a']=='in')
    {
        $active_tab_2 = 'active';
        // Интеграция
        $settings = fill_settings_in($user_id);
    }
	else
	{
		$active_tab_1 = 'active';
		$settings = fill_settings_main($user_id);
	}

	if($current_user_obj->get_is_admin()) {
        $PARS['{ACTIVE_TAB_2}'] = $active_tab_2;
        $mielofon_tab = fetch_tpl($PARS, $settings_mielofon_tab_tpl);
    }
	
	
	 
	$PARS['{SETTINGS}'] = $settings;
	$PARS['{ACTIVE_TAB_1}'] = $active_tab_1;

    $PARS['{MIELOFON_TAB}'] = $mielofon_tab;

	
	return fetch_tpl($PARS, $settings_tpl);
	 
}

// блок настроек уведомлений пользователя
function fill_settings_notices($user_id)
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	$user_setting_notices_tpl = file_get_contents('templates/user/user_setting_notices.tpl');
	
	$sql = "SELECT * FROM tasks_users WHERE user_id='$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	$notices_data = unserialize($row['notices']);
	
	foreach($notices_data as $key => $value)
	{
		$checked = $value == 1 ? 'checked="checked"': '';
		
		$notices_checked[$key] = $checked;
	}

	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{TASKS_CHECKED}'] = $notices_checked['tasks'];
	$PARS['{PROJECTS_CHECKED}'] = $notices_checked['projects'];
	
	$settings =  fetch_tpl($PARS, $user_setting_notices_tpl);
	
	return $settings;
}

// Интеграция
function fill_settings_in()
{
    global $site_db, $current_user_id, $user_obj, $current_user_obj;

    $user_setting_in_tpl = file_get_contents('templates/user/user_setting_in.tpl');

    $sql = "SELECT * FROM tasks_integration WHERE `type`='mielofon'";

    $keyRow = $site_db->query_firstrow($sql);

    $active = 0;

    if($keyRow['id']) {

        $data = json_decode($keyRow['data'], 1);

        $mielofon_key = $data['key'];

        $active = $data['active'];
    }


    $PARS['{MIELOFON_KEY}'] = $mielofon_key;

    $PARS['{HOST}'] = $_SERVER['HTTP_HOST'];

    $PARS['{MIELOFON_ACTIVE}'] = $active;
if(isset($_POST['kassa'])){
 $sql = "SELECT * FROM tasks_integration WHERE `type`='kassa'";

        $r=$site_db->query_firstrow($sql);


        
        $data = serialize($_POST['kassa']);

        if($r['id']) {

            $sql = "UPDATE tasks_integration SET data='$data' WHERE `type` = 'kassa'";

            $site_db->query($sql);

        }
        else {

            $sql = "INSERT INTO tasks_integration SET `type` = 'kassa', data='$data'";

            $site_db->query($sql);

        }
}
$sql = "SELECT * FROM tasks_integration WHERE `type`='kassa'";

    $keyRow = $site_db->query_firstrow($sql);

    if($keyRow['id']) {

        $data = unserialize($keyRow['data']);

if($data['active'])$kassa_active=' CHECKED';
$kassa_id=htmlspecialchars($data['id']);
$kassa_key1=htmlspecialchars($data['key1']);
$kassa_key2=htmlspecialchars($data['key2']);

    }


    $PARS['{KASSA_ACTIVE}'] = $kassa_active;
    $PARS['{KASSA_ID}'] = $kassa_id;
    $PARS['{KASSA_KEY1}'] = $kassa_key1;
    $PARS['{KASSA_KEY2}'] = $kassa_key2;

    return fetch_tpl($PARS, $user_setting_in_tpl);
}

// блок основных настроек пользователя
function fill_settings_main($user_id)
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	$user_setting_tpl = file_get_contents('templates/user/user_setting.tpl');
	
	$user_setting_boss_change_tpl = file_get_contents('templates/user/user_setting_boss_change.tpl');
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($user_id);
	
	$user_data = $user_obj->get_user_data();
	
	// Даем возможность изменять свое имя для людей, над которыми нет начальников 
	if(!$current_user_obj->get_is_admin() && $user_id==$current_user_id)
	{
		// Для генерального директора есть возможность изменить имя
		$position_disabled = 'disabled="disabled"';
		$user_name_disabled = 'disabled="disabled"';
	}
	
	
	$auth_method_arr = array(0 => '', 1 => '', 2 => '');
	
	$auth_method_arr[$user_obj->get_user_auth_method()] = 'checked="checked"';
 	
	if($_GET['chp']==1)
	{
		$change_pass_notice = file_get_contents('templates/user/change_password_notice.tpl');
	}
	
	// Список подразделений
	global $depts_list;
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_org.php';
	
	// Отделы пользователя
	$user_depts = get_user_depts($user_id);
	
	// Кнопка выслать рег данные
	if(check_for_send_msg($user_id, $current_user_id))
	{
		$PARS['{USER_ID}'] = $user_id;
		$send_reg_btn = fetch_tpl($PARS, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/user/send_reg_btn.tpl'));
	}
	 
	fill_depts_list(0,0,$user_depts);
	
	$add_dept_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/org/add_dept_form.tpl');
	
	$PARS['{DEPST_LIST}'] = $depts_list;
	
	
	$PARS['{send_reg_btn}'] = $send_reg_btn;
	
	$PARS['{CHANGE_PASSWORD_NOTICE}'] = $change_pass_notice;
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
	$PARS['{NAME}'] = $user_obj->get_user_name();
		
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
	$PARS['{POSITION}'] = $user_obj->get_user_position();
		
	$PARS['{LOGIN}'] = $user_obj->get_user_login();
	
	$PARS['{PHONE}'] = $user_obj->get_user_phone();
	
	$PARS['{EMAIL}'] = $user_data['user_email'];
	
	//$PARS['{BDATE}'] = datetime($user_obj->get_user_bdate('date'),'%d.%m.%Y');
	$PARS['{BDATE}'] = $user_obj->get_user_bdate('date');
	
	$PARS['{USER_ID}'] = $user_id;
		
	$PARS['{CURRENT_USER_ID}'] = $current_user_id;
	
	$PARS['{USER_NAME_DISABLED}'] = $user_name_disabled;
	
	$PARS['{POSITION_DISABLED}'] = $position_disabled;
	
	$PARS['{POSITION_DISABLED}'] = $position_disabled;
	
	$PARS['{AUTH_METHOD_0}'] = $auth_method_arr[0];
	
	$PARS['{AUTH_METHOD_1}'] = $auth_method_arr[1];
	
	$PARS['{AUTH_METHOD_2}'] = $auth_method_arr[2];
	
	//$PARS['{USER_REGISTRATION_PRIVILE_CHECKED}'] =  $user_obj->get_user_registration_privilege() ? 'checked="checked"' : '';
	
	$PARS['{USER_IS_ADMIN_CHECKED}'] =  $user_obj->get_is_admin() ? 'checked="checked"' : '';
	
	$PARS['{USER_IS_FIRED_CHECKED}'] =  $user_obj->get_is_fired() ? 'checked="checked"' : '';
	
	 
	$PARS['{USER_IS_FULL_ACCESS_CHECKED}'] =   $user_data['is_full_access'] ? 'checked="checked"' : '';
	
	$PARS['{USER_IS_ADMIN_DISABLED}'] =  $user_obj->get_is_admin() && $current_user_id==$user_id  ? 'disabled="disabled"' : '';
	
	$PARS['{AUTH_METHOD_2}'] = $auth_method_arr[2];
	
	$limitation_checked[$user_obj->get_user_limitation()] = 'checked="checked"';
	
	$PARS['{LIMITATION_CHECKED_0}'] = $limitation_checked[0];
	$PARS['{LIMITATION_CHECKED_1}'] = $limitation_checked[1];
	$PARS['{LIMITATION_CHECKED_2}'] = $limitation_checked[2];

	 
	
	if($current_user_obj->get_is_admin())
	{
		$settings =  fetch_tpl($PARS, $user_setting_boss_change_tpl);
	}
	else
	{

#livan
	$settings =  fetch_tpl($PARS, $user_setting_tpl);
	}
	
	return $settings;
}

// Отмечаем последнюю активность на сайте
function set_last_user_visit_date($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "UPDATE ".USERS_TB." SET user_last_visit_date=NOW() WHERE user_id='$user_id'";
	
	$site_db->query($sql);
}

// Проверяет находится ли онлайн пользователь
function user_in_online_icon($user_id, $user_last_visit_date='')
{
	global $site_db, $current_user_id, $user_obj;
	
	$user_in_online_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/user/user_in_online.tpl');
	
	// Если пользователь онлайн
	if(user_is_online($user_id, $user_last_visit_date))
	{
		return $user_in_online_tpl;	
	}
	else
	{
		return '';
	}
}

// Проверяет онлайн ли пользователь
function user_is_online($user_id, $user_last_visit_date, $minuts = 5)
{
	global $site_db, $current_user_id, $user_obj;
	
	// Если передано значение последнего визита пользователя
	if($user_last_visit_date)
	{
		$last_v_time = $user_last_visit_date;
	}
	else
	{
		$sql = "SELECT user_last_visit_date FROM ".USERS_TB." WHERE user_id='$user_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		$last_v_time = $row['user_last_visit_date'];
	}
	
	$last_visit_mktime = to_mktime($last_v_time);
	
	// Промежуток времени, за которым считается, что пользователь онлайн
	$user_in_online_seconds = 60*$minuts;

	if((time()) - $last_visit_mktime < $user_in_online_seconds)
	{
		return true;	
	}
	else
	{
		return false;
	}
}

// Определение пола пользователя
function get_user_sex_by_user_full_name($name)
{
	$name = iconv('cp1251', 'utf-8', $name);
	
	// Определяем пол
	require $_SERVER['DOCUMENT_ROOT'].'/libraries/ncl/NCLNameCaseRu.php';
	
	$nc = new NCLNameCaseRu();
	
	return $nc->genderDetect($name);
}

function get_words_end_by_user_sex($user_sex)
{
	if($user_sex==1)
	{
		return '';
	}
	else if($user_sex==2)
	{
		return 'а';
	}
}

// Перенаправляет пользователя на нужные страницы при необходимости
function redirect_user_to_page($user_id)
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	 
	if($_GET['o']=='exit' || $_GET['o'] == 'personal')
	{
		return '';
	}
	
	// Если пользователь отстранен от работы
	if($current_user_obj->get_is_fired())
	{
		header('Location: /exit');
		exit();
	}

	## Проверка на новые выговоры
	// Проверяем новые выговоры
	$new_workers_reprimand_count = get_new_workers_reprimands_count($user_id);
		
	if($new_workers_reprimand_count)
	{
		if($_GET['o']!='reprimand')
		{
			header('Location: /reprimand');
			exit();
		}
		return '';
	}
	
	## Проверка на наличие начальников
	/*// Данные пользователя
	$sql = "SELECT registrated_by_user_id FROM ".USERS_TB." WHERE user_id='$user_id'";
		
	$row = $site_db->query_firstrow($sql);
		
	// Если нет начальников и пользователь не является генеральным перенаправляем на страницу начальников
	if(!get_current_user_users_arrs(array(1,0,0,1,0)) && $row['registrated_by_user_id'])
	{
		if($_GET['o']!='boss')
		{
			header('Location: /id'.$current_user_id);
			exit();
		}
		return '';
	}*/

}

// Страница уведомления об отстранение пользователя от работы
function fill_user_removed_from_work_notice()
{
	global $site_db, $current_user_obj;
	
	$user_removed_from_work_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/user/user_removed_from_work.tpl');
	
	// Если пользователь отстранен от работы
	if(!$current_user_obj->get_user_removed_from_work())
	{
		header('Location: /');
		exit();
	}

	return $user_removed_from_work_tpl;
}

// Форматирование имени автора папки или файла
function get_formate_user_name($user_id)
{
	global $site_db, $current_user_id, $user_obj, $autor_cache_arr;
	
	if($user_id==$current_user_id)
	{
		$author = 'Я';
	}
	else if($autor_cache_arr[$user_id])
	{  
		$author = $autor_cache_arr[$user_id];
		 
	}
	else if(!$autor_cache_arr[$user_id])
	{  
		// Заполянем объект пользователя
		$user_obj->fill_user_data($user_id); 
		$name = $user_obj->get_user_name();
		$author =  $user_obj->get_user_surname().' '.$name[0];
		 
		$autor_cache_arr[$user_id] = $author;
	}
	 
	return $author;
}

function send_to_user_reg_data_by_sms($user_id)
{
	global $site_db, $current_user_obj;
	
	$sql = "SELECT * FROM tasks_users WHERE user_id='$user_id'";
	
	$user_data = $site_db->query_firstrow($sql);
	
	if(!$user_data['user_id'])
	{
		return '';
	}
	else
	{
		$phone = $user_data['user_phone'];
		
		$login = $user_data['user_login'];
		### sms body
		$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/reg_data.tpl');
		
		// Адрес сайта
		$site_addr = str_replace('www.', '', $_SERVER['HTTP_HOST']);
		
		$user_name = $current_user_obj->get_user_name();
		
		$user_surname = $current_user_obj->get_user_surname();
	
		$PARS['{FROM_USER_SURNAME}'] = $user_surname;
		$PARS['{FROM_USER_NAME}'] = $user_name[0].'.';
		
		$PARS['{USER_NAME}'] = $user_data['user_name'];
	
		$PARS['{USER_MIDDLENAME}'] = $user_data['user_middlename'];
	
		$PARS['{LOGIN}'] = $login;
		
		$PARS['{SITE}'] = $site_addr;
		 
		$sms_text = fetch_tpl($PARS, $sms_tpl);
		###\ sms body
		 
		// Отправка смс сообщения
		send_sms_msg($phone, $sms_text, 1, 1);
		
		return 1;
	}
}
function check_user_email_for_exists($user_email, $user_id)
{
	global $site_db;
	
	// Проверка на такой же логин
	$sql = "SELECT * FROM ".USERS_TB." WHERE user_email='$user_email' AND user_id<>'$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	// Пользователь с таким логином уже существует
	if($row['user_id'])
	{
		return true;
	}
	else
	{
		return false;
	}
}
?>
