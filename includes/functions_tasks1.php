<?php

function spam()
{
	global $site_db, $current_user_id, $user_obj;
	//ini_set('display_errors',1);
	for($i=0; $i<10000; $i++)
	{
		$work = rand(1,2);
		
		if($work==1)
		{
			$step = rand(0,3);
		}
		if($work==2)
		{
			$step = rand(4,5);
		}
			
		$sql = "INSERT INTO tasks_tasks SET task_theme='задача $i', work_status='$work', step_status='$step'";
		
		$site_db->query($sql);
		
		$task_id = $site_db->get_insert_id();
		
		$user_rand = rand(1,105);
		$sql = "INSERT INTO tasks_tasks_users SET task_id='$task_id', user_id='$user_rand', role=1";
		$site_db->query($sql);
		
		for($j=0;$j<rand(0,2); $j++)
		{
			$noticed = rand(0,1);
			$notice_type = rand(1,4);
			$sql = "INSERT INTO tasks_tasks_notices SET task_id='$task_id', user_id='$user_rand', notice_type='$notice_type', noticed='$noticed'";
			$site_db->query($sql);
		}
		 
		
		
		$user_rand = rand(1,105);
		$sql = "INSERT INTO tasks_tasks_users SET task_id='$task_id', user_id='$user_rand', role=2";
		$site_db->query($sql);
		
		for($j=0;$j<rand(0,2); $j++)
		{
			$noticed = rand(0,1);
			$notice_type = rand(1,4);
			$sql = "INSERT INTO tasks_tasks_notices SET task_id='$task_id', user_id='$user_rand', notice_type='$notice_type', noticed='$noticed'";
			$site_db->query($sql);
		}
		
		$user_rand = rand(1,105);
		$sql = "INSERT INTO tasks_tasks_users SET task_id='$task_id', user_id='$user_rand', role=3";
		$site_db->query($sql);
		for($j=0;$j<rand(0,2); $j++)
		{
			$noticed = rand(0,1);
			$notice_type = rand(1,4);
			$sql = "INSERT INTO tasks_tasks_notices SET task_id='$task_id', user_id='$user_rand', notice_type='$notice_type', noticed='$noticed'";
			$site_db->query($sql);
		}
		
		$user_rand = rand(1,105);
		$sql = "INSERT INTO tasks_tasks_users SET task_id='$task_id', user_id='$user_rand', role=3";
		$site_db->query($sql);
		for($j=0;$j<rand(0,4); $j++)
		{
			$noticed = rand(0,1);
			$notice_type = rand(1,4);
			$sql = "INSERT INTO tasks_tasks_notices SET task_id='$task_id', user_id='$user_rand', notice_type='$notice_type', noticed='$noticed'";
			$site_db->query($sql);
		}
		
		
		$user_rand = rand(1,105);
		$sql = "INSERT INTO tasks_tasks_users SET task_id='$task_id', user_id='$user_rand', role=4";
		$site_db->query($sql);
		for($j=0;$j<rand(0,2); $j++)
		{
			$noticed = rand(0,1);
			$notice_type = rand(1,4);
			$sql = "INSERT INTO tasks_tasks_notices SET task_id='$task_id', user_id='$user_rand', notice_type='$notice_type', noticed='$noticed'";
			$site_db->query($sql);
		}
		
		 
	}
	echo 2;
}

// просмотр задачи
function fill_task($task_id)
{
	global $site_db, $current_user_id, $user_obj;
	  
	$show_task_tpl = file_get_contents('templates/tasks1/show_task.tpl');
	$edit_block_info_tpl = file_get_contents('templates/tasks1/edit_block_info.tpl');
	$option_fcbk_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl');
	$task_max_date_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_max_date_block.tpl');
	
	// выбор задачи
	$sql = "SELECT * FROM tasks_tasks WHERE task_id='$task_id'";
	
	$task_data = $site_db->query_firstrow($sql);
	
	if(!$task_data['task_id'] || $task_data['deleted']==1 )
	{
		header('Location: /tasks');
		exit();
	}
	
	// Исполнитель задачи
	$to_user_id = get_task_user_role_2($task_id); 
	
	// если пользователь относится к задаче или просмтаривает руководитель того, кто является главным исполнителем
	if(!check_user_in_task_role($task_id, $current_user_id, array(1,2,3,4)) && !check_user_access_to_user_content($to_user_id, array(0,1,0,0,1)))
	{
		 
		header('Location: /tasks');
		exit();
	}
			
	// если задача редактировалась
	if(is_date_exists($task_data['date_edit']))
	{
		$PARS['{DATE}'] = datetime($task_data['date_edit'], '%d.%m.%Y в %H:%i');
		
		$edit_block_info = fetch_tpl($PARS, $edit_block_info_tpl);
	}
	
	// максимальное время выполнения
	if(is_date_exists($task_data['task_max_date']))
	{
		// статус ПРОСРОЧНОСТИ задачи
		$task_expired_status_arr = get_task_expired_status($task_data);
		$task_expired_status = $task_expired_status_arr['status'];
		
		// задача просрочена
		if($task_expired_status==1)
		{
			$task_list_max_date_over_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_list_max_date_over_1.tpl');
			
			
			$PARS['{TIME}'] = $task_expired_status_arr['time_expired'];
			$PARS['{DATE}'] = $task_max_date;
			$task_max_date_expired = fetch_tpl($PARS, $task_list_max_date_over_tpl); 
			$expired_class = 'task_ext_status_warning';
			 
		}
		
		if(datetime($task_data['task_max_date'], '%H')!='00' || datetime($task_data['task_max_date'], '%i')!='00')
		{
			$PARS['{DATE}'] = datetime($task_data['task_max_date'], '%H:%i %d.%m.%Y');
		}
		else
		{
			$PARS['{DATE}'] = datetime($task_data['task_max_date'], '%d.%m.%Y');
		}
		
		$PARS['{EXPIRED_DATE}'] = $task_max_date_expired;
		$PARS['{CLASS}'] = $expired_class;
		
		$task_max_date = fetch_tpl($PARS, $task_max_date_block_tpl);
	}
	
	// Сложность задачи
	$task_difficulty =  get_difficulty_name_by_id($task_data['task_difficulty']);
		
	// Название приоритета
	$task_priority = get_priority_name_by_id($task_data['task_priority']);	
	
	// блок ролей задачи
	$task_roles = fill_task_roles_block($task_data);

	// кнопки управления задачей
	$task_btns = fill_task_btns($task_data);
	
	// статусы задачи
	$task_status_bar = fill_task_status_bar($task_data);
	
	// блок отчета о задаче
	$task_report_block = fill_task_report_block($task_data);
	
	// Список файлов для отчета
	$files_list = get_attached_files_to_content($task_data['task_id'], 6);
	
	// блок уведомлений для задачи
	$task_notice_block = fill_task_notice_block($task_data['task_id']);
	
	// убираем все уведомления для задачи пользователю
	delete_task_notice($task_id, 0, 0, $current_user_id, 1, 'by_user');
	
	$PARS['{TASK_ID}'] = $task_data['task_id'];
	
	$PARS['{TASK_THEME}'] = $task_data['task_theme'];
	
	$PARS['{TASK_TEXT}'] = nl2br($task_data['task_text']);
	
	$PARS['{TASK_DIFFICULTY}'] = $task_difficulty;
	
	$PARS['{TASK_PRIORITY}'] = $task_priority;
	
	$PARS['{TASK_DATE_ADD}'] = datetime($task_data['date_add'], '%d.%m.%Y в %H:%i');
	
	$PARS['{TASK_ROLES}'] = $task_roles;
	
	$PARS['{EDIT_BLOCK_INFO}'] = $edit_block_info;
	
	$PARS['{TASK_BTNS}'] = $task_btns;
	
	$PARS['{TASK_STATUS_BAR}'] = $task_status_bar;
	
	$PARS['{TASK_REPORT_BLOCK}'] = $task_report_block;
	
	$PARS['{TASK_MAX_DATE}'] = $task_max_date;
	
	$PARS['{FILES_LIST}'] = $files_list;
	 
	$PARS['{TASK_NOTICE_BLOCK}'] = $task_notice_block;
	
	
	return fetch_tpl($PARS, $show_task_tpl);
}

// блок уведомлений для задачи
function fill_task_notice_block($task_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$task_notice_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_notice_block.tpl');
	
	// выбор всех уведомлений для пользователя по задаче
	$sql = "SELECT * FROM tasks_tasks_notices WHERE task_id='$task_id' AND user_id='$current_user_id' AND noticed=0";
	
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res, 1))
	{  
		if($row['notice_type']==1)
		{
			$notices[$row['notice_type']] = 'Добавлен новый отчет';
		}
		if($row['notice_type']==2)
		{
			$notices[$row['notice_type']] = 'Изменился статус задачи';
		}
	}
	
	if($notices)
	{  
		$PARS['{NOTICES}'] = implode('<div class="sep"></div>',$notices);
	
		return fetch_tpl($PARS, $task_notice_block_tpl);
	} 
}


// блок отчета задачи
function fill_task_report_block($task_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$task_status_bar_wrap_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_report_block.tpl');
	
	$task_report_add_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_report_add_form.tpl');
	
	$task_id = $task_data['task_id'];
	
	// форму добавления отчета выводим пользователям, которые имеют отношение к задаче
	if(check_user_in_task_role($task_id, $current_user_id, array(1,2,3,4)))
	{
		$PARS['{TASK_ID}'] = $task_id;
		
		$add_form = fetch_tpl($PARS, $task_report_add_form_tpl);
	}
	
	$PARS['{TASK_ID}'] = $task_id;
	
	$PARS['{ADD_FORM}'] = $add_form;
	
	$PARS['{REPORTS_PER_PAGE}'] = PER_PAGE;
	
	return fetch_tpl($PARS, $task_status_bar_wrap_tpl);
}

// кол-во отчетов
function get_task_reports_count($task_id)
{
	global $site_db,  $user_obj, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM tasks_tasks_reports WHERE task_id='$task_id' AND deleted=0 ";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Список отчетов для задания
function fill_task_reports_list($task_id, $page)
{
	global $site_db,  $user_obj, $current_user_id;
	
	// Страничность
	$begin_pos = PER_PAGE * ($page);
	$limit = " LIMIT ".$begin_pos.",".PER_PAGE;
	 
	// Выбор отчетов для задания
	$sql = "SELECT * FROM tasks_tasks_reports WHERE task_id='$task_id' AND deleted=0 ORDER by report_id DESC $limit";
	
	$res = $site_db->query($sql);
	
	
	while($report_data=$site_db->fetch_array($res, 1))
	{ 
		$task_report_list .= fill_task_report_item($report_data);
		
		//$task_report_list .= fetch_tpl($PARS, $tasks_report_item_tpl);
	}
	
	if(!$task_report_list)
	{
		$task_report_list = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/tasks_report_no_reports.tpl');;
	}
	
	return $task_report_list;
}

// данные отчета
function get_task_report_data($report_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "SELECT * FROM tasks_tasks_reports WHERE report_id='$report_id'";
		
	return $site_db->query_firstrow($sql);
}

// элемент списка отчетов
function fill_task_report_item($report_data, $form)
{
	global $site_db, $current_user_id, $user_obj;
	
	$tasks_report_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/tasks_report_item.tpl');
	
	$tasks_report_item_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/tasks_report_item_edit.tpl');
	
	$report_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/report_edit_tools.tpl');
	
	$edit_tools = '';
	// панель управления отчетом
	if($current_user_id==$report_data['report_user_id'])
	{
		$PARS['{REPORT_ID}'] = $report_data['report_id'];
		$edit_tools = fetch_tpl($PARS, $report_edit_tools_tpl);
	}
	
	$report_not_confirm = '';
		
	$confirm_btn = '';
		
	$user_obj->fill_user_data($report_data['report_user_id']);
	
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($report_data['report_user_id'], $user_obj->get_user_image());
  	
	// для редактирования
	if($form==1)
	{
		// Список файлов для отчета
		$files_list = get_attached_files_to_content($report_data['report_id'], 7, 2);
	}
	else
	{
		// Список файлов для отчета
		$files_list = get_attached_files_to_content($report_data['report_id'], 7);
	}
	
	 
	
	$PARS['{FILES_LIST}'] = $files_list;
	
	$PARS['{USER_ID}'] = $report_data['report_user_id'];
	
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;
	
	$PARS['{TASK_DATE}'] = datetime($report_data['report_date'], '%j %M в %H:%i');
	
	$PARS['{TASK_TEXT}'] = nl2br($report_data['report_text']);
	
	$PARS['{TASK_TEXT_EDIT}'] = $report_data['report_text'];
	
	$PARS['{REPORT_ID}'] = $report_data['report_id'];
	
	$PARS['{TASK_ID}'] = $report_data['task_id'];
	
	$PARS['{REPORT_NOT_CONFIRM_CLASS}'] = $report_not_confirm;
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	// форма редактирования
	if($form==1)
	{
		return fetch_tpl($PARS, $tasks_report_item_edit_tpl);
	}
	else return fetch_tpl($PARS, $tasks_report_item_tpl);
}

// статус бар задачи
function fill_task_status_bar($task_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$task_status_bar_wrap_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_status_bar_wrap.tpl');
	
	$task_status_bar_1_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_status_bar_1.tpl');
	$task_status_bar_2_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_status_bar_2.tpl');
	$task_status_bar_2_1_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_status_bar_2_1.tpl');
	$task_status_bar_3_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_status_bar_3.tpl');
	$task_status_bar_4_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_status_bar_4.tpl');
	$task_status_bar_5_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_status_bar_5.tpl');
	$task_status_bar_6_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_status_bar_6.tpl');
	
	// задача в работе
	if($task_data['work_status']==1)
	{
		// не принятла
		if($task_data['step_status']==0) {$status_class='status_1'; $status = $task_status_bar_1_tpl;}
		
		// принята
		if($task_data['step_status']==1 && is_date_exists($task_data['date_status_2']))
		{
			$status_class='status_3'; $status = $task_status_bar_2_1_tpl;
		}
		else if($task_data['step_status']==1) {$status_class='status_2'; $status = $task_status_bar_2_tpl;}
		
		 
		// выполняется
		if($task_data['step_status']==2) {$status_class='status_3'; $status = $task_status_bar_3_tpl;}
		// ждет подтверждения выполнения
		if($task_data['step_status']==3) {$status_class='status_4'; $status = $task_status_bar_4_tpl;}
	}
	// задача завершена
	else if($task_data['work_status']==2)
	{
		// успешное выполнение задачи
		if($task_data['step_status']==4) {$status_class='status_5'; $status = $task_status_bar_5_tpl;}
		
		// с задачей не справился
		if($task_data['step_status']==5) {$status_class='status_6'; $status = $task_status_bar_6_tpl;}
	}
	
	// расширенная информация по времени
	$time_list = fill_task_time_rusult_block($task_data);
	// выводим кнопку подробнее
	$display_more_inf_btn = $time_list ? '' : 'none';
	 
	
	$PARS['{STATUS_CLASS}'] = $status_class;
	$PARS['{STATUS}'] = $status;
	$PARS['{MORE_INFO}'] = $time_list;
	$PARS['{DISPLAY_MORE_INF_BTN}'] = $display_more_inf_btn;
		
	return fetch_tpl($PARS, $task_status_bar_wrap_tpl);
}

// блок затраченного времени
function fill_task_time_rusult_block($task_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$task_result_time_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_result_time_item.tpl');
	
	// Не принято
	if($task_data['work_status']==1 && $task_data['step_status']==0)
	{
		$date_result_raznost = time() - to_mktime($task_data['date_add']);
		$date_result_arr =  sec_to_date_words($date_result_raznost);
		$date_result = $date_result_arr['string'];
		
		$PARS['{STATUS}'] = "<b>Не ознакомлен</b> с задачей ".$date_result.'.';
		
		$status .= fetch_tpl($PARS, $task_result_time_item_tpl);
	}
		 
	// Не принято
	if(is_date_exists($task_data['date_status_1']))
	{
		$date_result_raznost = to_mktime($task_data['date_status_1']) - to_mktime($task_data['date_add']);
		$date_result_arr =  sec_to_date_words($date_result_raznost);
		$date_result = $date_result_arr['string'];
		
		$PARS['{STATUS}'] = "Понадобилось ".$date_result." на <b>  принятие задания</b> после постановки.";
		
		$status .= fetch_tpl($PARS, $task_result_time_item_tpl);
	}
	
	// Ушло на начало выполнения
	if(is_date_exists($task_data['date_status_1']) && is_date_exists($task_data['date_status_2']))
	{
		$date_result_raznost = to_mktime($task_data['date_status_2']) - to_mktime($task_data['date_status_1']);
		$date_result_arr =  sec_to_date_words($date_result_raznost);
		$date_result = $date_result_arr['string'];
		
		$PARS['{STATUS}'] = "Понадобилось ".$date_result." на <b> начало выполнения</b> после принятия.";
		
		$status .= fetch_tpl($PARS, $task_result_time_item_tpl);
	}
	
	// Задача выполняется
	if(is_date_exists($task_data['date_status_2']) && in_array($task_data['step_status'], array(1,2)) && $task_data['work_status']==1)
	{
		if($task_data['step_status']==1)
		{
			$in_pause_proc = 'Приостановлена. ';
		}
		
		$date_result_raznost = time() - to_mktime($task_data['date_status_2']);
		$date_result_arr =  sec_to_date_words($date_result_raznost);
		$date_result = $date_result_arr['string'];
		
		$PARS['{STATUS}'] = $in_pause_proc."<b>Выполняется </b>".$date_result.".";
		
		$status .= fetch_tpl($PARS, $task_result_time_item_tpl);
	}		 
	 
	
	// Выполнена
	if(($task_data['step_status']==3 || $task_data['work_status']==2) && is_date_exists($task_data['date_status_3']) && is_date_exists($task_data['date_status_2']))
	{
		$date_result_raznost = to_mktime($task_data['date_status_3']) - to_mktime($task_data['date_status_2']);
		$date_result_arr =  sec_to_date_words($date_result_raznost);
		$date_result = $date_result_arr['string'];
		
		if($task_data['step_status']==5)
		{
			$PARS['{STATUS}'] = "Понадобилось <span style='color:#e46c4c'><b>".$date_result."</b></span> <b>от выполнения к завершению</b> выполнения задачи.";
		}
		else
		{
			$PARS['{STATUS}'] = "Понадобилось <span style='color:#27b50b'><b>".$date_result."</b></span> <b>от выполнения к завершению</b> выполнения задачи.";
		}
		$status .= fetch_tpl($PARS, $task_result_time_item_tpl);
	}
	
	return $status;
}

// Кнопки задачи
function fill_task_btns($task_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	## Кнопки
	
	// Принял
	$task_read_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_read_status_btn.tpl');
	// Выполняется
	$task_process_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_process_status_btn.tpl');
	// Не выполняется
	$task_not_process_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_not_process_status_btn.tpl');
	// Не могу выполнить
	$task_cant_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_cant_status_btn.tpl');
	// Выполнено
	$task_complete_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_complete_status_btn.tpl');
	// Не выполнено
	$task_not_complete_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_not_complete_status_btn.tpl');

	// Подтвердить выполнение
	$task_confirm_own_complete_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_confirm_own_complete_status_btn.tpl');
	
	
	// для постновщика
	// С заданием не справился
	$task_finished_fail_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_finished_fail.tpl');
	// Задание выполнено
	$task_to_finish_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_to_finish_status_btn.tpl');
	// Возобновить выполнение задачи
	$task_resume_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_resume_status_btn.tpl');
	// Подтвердить выполнение
	$task_confirm_complete_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_confirm_complete_status_btn.tpl');
	// Не родтвердить выполнение, дорабатывать
	$task_not_confirm_complete_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_not_confirm_complete_status_btn.tpl');
	
	
	$task_edit_btns_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_edit_btns_tools.tpl');
	
	$task_delegate_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_delegate_btn.tpl');
	
	$task_ext_btn_wrap_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_ext_btn_wrap.tpl');	 
	
	
	$task_btns_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_btns.tpl');
	
	$status_btn = array();
	
	// роли пользователя
	$user_roles_arr = get_user_task_roles($current_user_id, $task_data['task_id']);
	
	// задача выставлена САМОМУ СЕБЕ
	if($user_roles_arr[1]==1 && $user_roles_arr[2]==1)
	{
		if($task_data['work_status']==1)
		{
			// ЗАДАЧА ЕЩЕ В ПРОЦЕССЕ ВЫПОЛНЕНИЯ. С задачей не справился.  
			if($task_data['step_status']==1) { $status_btn[] = $task_process_status_btn_tpl; }
			if($task_data['step_status']==2) { $status_btn[] = $task_confirm_own_complete_status_btn_tpl.$task_not_process_status_btn_tpl.$task_finished_fail_tpl; }
		}
		
		if($task_data['work_status']==2)
		{
			// ЗАДАЧА ЕЩЕ В ПРОЦЕССЕ ВЫПОЛНЕНИЯ. С задачей не справился.  
			$status_btn[] = $task_resume_status_btn_tpl;
			
		}
		 
	}
	// пользователь является ПОСТАНОВЩИКОМ задачи
	else if($user_roles_arr[1]==1)
	{
		// ПОдтвердить или не подтвердить выполнение задачи
		if($task_data['step_status']==3) { $status_btn[] = $task_confirm_complete_status_btn_tpl.$task_not_confirm_complete_status_btn_tpl; }
		
		if($task_data['step_status']!=3 && $task_data['work_status']==1) { $status_btn[] = $task_to_finish_status_btn_tpl; }
		 
		// ЗАДАЧА ЕЩЕ В ПРОЦЕССЕ ВЫПОЛНЕНИЯ. С задачей не справился.  
		if($task_data['work_status']==1) { $status_btn[] = $task_finished_fail_tpl; }
		
		// ЗАДАЧА УЖЕ ВЫПОЛНЕНА. Возобновить выполнение задачи.
		if($task_data['work_status']==2) { $status_btn[] = $task_resume_status_btn_tpl; }
		
		// Блок оценки качества проделанной работы
		if($task_data['work_status']==2) { $status_btn[] = get_task_rating_block($task_data, 'edit');}
		
	}
	// пользователь является ИСПОЛНИТЕЛЕМ задачи
	else if($user_roles_arr[2]==1)
	{
		// Если еще не прочитано 
		if($task_data['step_status']==0) { $status_btn[] = $task_read_status_btn_tpl; }
		// Прочитано
		else if($task_data['step_status']==1) { $status_btn[] = $task_process_status_btn_tpl; }
		// Выполняется
		else if($task_data['step_status']==2){$status_btn[] = $task_complete_status_btn_tpl.$task_not_process_status_btn_tpl; }			
		// Выполнено
		else if($task_data['step_status']==3) { $status_btn[] = $task_not_complete_status_btn_tpl; }
		
		// Блок оценки качества проделанной работы
		if($task_data['work_status']==2) { $status_btn[] = get_task_rating_block($task_data, 'str'); }
	}
	
	// кнопка делегировать задачу для ИСПОЛНИТЕЛЯ
	if($user_roles_arr[2]==1)
	{
		$ext_status_btn .= $task_delegate_btn_tpl;
	}
	
	// кнопка редактирования задачи ДЛЯ ПОСТАНОВЩИКА ЗАДАЧИ
	if($user_roles_arr[1]==1)
	{
		$ext_status_btn .= $task_edit_btns_tools_tpl;
	}
	
	$btns = implode('', $status_btn);
	
	// доп кнопки редактирование и делегирование
	if($ext_status_btn)
	{
		$PARS['{BTNS}'] = $ext_status_btn;
		
		$btns .= fetch_tpl($PARS, $task_ext_btn_wrap_tpl);
	}
	
	// инициализация основных кнопок
	$PARS['{TASK_ID}'] = $task_data['task_id'];
	
	$btns =  fetch_tpl($PARS, $btns);
	
	
	
	$PARS['{BTNS}'] = $btns;
	
	return fetch_tpl($PARS, $task_btns_tpl);
}

// Блок оценки качества
function get_task_rating_block($task_data, $form='edit')
{
	global $site_db;
	
	$task_rating_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_rating.tpl');
	
	$task_rating_result_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_rating_result.tpl');
	
	$quality_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_quality.tpl');
	
	if($form=='edit')
	{
		$quality_list_arr = array(1,2,3,4,5);
		
		foreach($quality_list_arr as $quality)
		{
			$selected = $quality == $task_data['task_rating'] ? 'selected="selected"' : '';
			
			$PARS1['{NAME}'] = $quality;
			
			$PARS1['{VALUE}'] = $quality;
			
			$PARS1['{SELECTED}'] = $selected;
			
			$quality_list .= fetch_tpl($PARS1, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl'));
		}
		
		$PARS['{RATING_LIST}'] = $quality_list;
		
		$PARS['{TASK_ID}'] = $task_data['task_id'];
		
		$rating_block = fetch_tpl($PARS, $task_rating_tpl);
	}
	else if($form=='str')
	{
		$PARS1['{TASK_RATING}'] = $task_data['task_rating'] ? $task_data['task_rating'] : 'Не выставлена';
			
		$rating_block = fetch_tpl($PARS1, $task_rating_result_tpl);
		
	}
	
	return  $rating_block;
}

// форма делегирования
function fill_task_delegate_form($task_id)
{
	$task_delegate_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_delegate_form.tpl');
	
	$PARS['{TASK_ID}'] = $task_id;
	
	return fetch_tpl($PARS, $task_delegate_form_tpl);
}

// выбор ролей пользователя у задачи
function get_user_task_roles($user_id, $task_id)
{
	global $site_db;
	
	// выбор ролей пользователя у задачи
	$sql = "SELECT * FROM tasks_tasks_users WHERE task_id='$task_id' AND user_id='$user_id'";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$roles_arr[$row['role']] = 1;
	}
	
	return $roles_arr;
}

// возвращает постановщика задачи
function get_task_user_role_1($task_id)
{
	global $site_db;
	
	// выбор ролей пользователя у задачи
	$sql = "SELECT * FROM tasks_tasks_users WHERE task_id='$task_id' AND role=1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['user_id'];
}

// возвращает исполнителя задачи
function get_task_user_role_2($task_id)
{
	global $site_db;
	
	// выбор ролей пользователя у задачи
	$sql = "SELECT * FROM tasks_tasks_users WHERE task_id='$task_id' AND role=2";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['user_id'];
}

// выбор всех пользователей задачи
function get_task_role_user($task_id, $role)
{
	global $site_db;
	
	// выбор ролей пользователя у задачи
	$sql = "SELECT * FROM tasks_tasks_users WHERE task_id='$task_id' AND role='$role'";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$users_arr[] = $row['user_id'];
	}
	
	return $users_arr;
}

// выбор всех пользователей задачи
function get_task_users($task_id, $roles_arr, $return_role_arr=1)
{
	global $site_db;
	
	if($roles_arr)
	{
		$roles = implode(',', $roles_arr);
		$and_roles = " AND role IN($roles)";
	}
	
	// выбор ролей пользователя у задачи
	$sql = "SELECT * FROM tasks_tasks_users WHERE task_id='$task_id' $and_roles ";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$roles_arr[$row['role']][] = $row['user_id'];
		$users_arr[] = $row['user_id'];
	}
	
	if($return_role_arr)
	{
		return $roles_arr;
	}
	else
	{
		return $users_arr;
	}
	 
}

// обновить статус задачи
function set_task_status($task_id, $step_status, $work_status=1)
{
	global $site_db;
	
	// даты
	// date_status_1 - принято
	// date_status_2 - выполняется
	// date_status_3 - завершено
	
	// выбор задачи
	$sql = "SELECT * FROM tasks_tasks WHERE task_id='$task_id'";
		
	$task_data = $site_db->query_firstrow($sql);
	
	// задача принята	
	if($work_status==1 && $step_status==1 && $task_data['work_status']==1 && $task_data['step_status']==0)
	{
		$and_status_time = ' , date_status_1=NOW() ';
	}
	// начало выполнения задачи
	else if($work_status==1 && $step_status==2 && $task_data['work_status']==1 && $task_data['step_status']==1 && preg_match('/0000/',$task_data['date_status_2']))
	{
		$and_status_time = ' , date_status_2=NOW() ';
	}
	// завершения выполнения задачи, если ответственный сам нажал на кнопку выполнено
	else if($work_status==1 && $step_status==3 && $task_data['work_status']==1 && $task_data['step_status']==2)
	{
		$and_status_time = ' , date_status_3=NOW() ';
	}
	// если на "выполнить задание"  нажал сам постановщик задачи
	else if($work_status==2 && $step_status==4 && $task_data['work_status']==1 && $task_data['step_status']!=3)
	{
		$and_status_time = ' , date_status_3=NOW() ';
	}
	// с задачей не справился
	else if($work_status==2 && $step_status==5 && $task_data['work_status']==1 && $task_data['step_status']!=3)
	{
		$and_status_time = ' , date_status_3=NOW() ';
	}
	// с задачей не справился
	else if($work_status==1 && $step_status==2 && !is_date_exists($task_data['date_status_2']))
	{
		$and_status_time = ' , date_status_2=NOW() ';
	}
	
	// обновляем статус задачи
	$sql = "UPDATE tasks_tasks SET step_status='$step_status' $and_status_time WHERE task_id='$task_id'";
	
	$site_db->query($sql);
	
	
	// работа принята, завершаем выполнение задачи
	if($work_status)
	{
		// обновляем статус задачи
		$sql = "UPDATE tasks_tasks SET work_status='$work_status' WHERE task_id='$task_id'";
	
		$site_db->query($sql);
	}
}

// блок участников задачи
function fill_task_roles_block($task_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$tasks_role_block_tpl = file_get_contents('templates/tasks1/tasks_role_block.tpl');
	
	$tasks_role_user_item_tpl = file_get_contents('templates/tasks1/tasks_role_user_item.tpl');
	
	$tasks_role_user_role_3_row_tpl = file_get_contents('templates/tasks1/tasks_role_user_role_3_row.tpl');
	
	$tasks_role_user_role_4_row_tpl = file_get_contents('templates/tasks1/tasks_role_user_role_4_row.tpl');
	
	$task_id = $task_data['task_id'];
	
	// выбор пользователей задачи
	$sql = "SELECT * FROM tasks_tasks_users WHERE task_id='$task_id'";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$roles_arr[$row['role']][] = $row['user_id'];
	}
	
	foreach($roles_arr as $role => $users)
	{
		$users_list = '';
		
		foreach($users as $user_id)
		{
			$user_obj->fill_user_data($user_id);
			
			$user_name = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename().', '.$user_obj->get_user_position(); 
			
			$PARS['{USER_ID}'] = $user_id;
			$PARS['{USER_NAME}'] = $user_name;
			
			$users_list .= fetch_tpl($PARS, $tasks_role_user_item_tpl);
		}
		
		switch($role)
		{
			case '1':
				$user_from = $users_list;
			break;
			case '2':
				$user_main_performer = $users_list;
			break;
			case '3':
				$PARS['{LIST}'] = $users_list;
				$user_performers = fetch_tpl($PARS, $tasks_role_user_role_3_row_tpl);
			break;
			case '4':
				$PARS['{LIST}'] = $users_list;
				$user_copies = fetch_tpl($PARS, $tasks_role_user_role_4_row_tpl);
			break;
			
		}
	}
	
	$user_obj->fill_user_data($task_data['user_id']);
	
	$PARS['{USER_ID}'] = $task_data['user_id'];
	$PARS['{USER_NAME}'] = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename().', '.$user_obj->get_user_position(); ;
			
	$user_author = fetch_tpl($PARS, $tasks_role_user_item_tpl);

	
	$PARS['{USER_FROM}'] = $user_from;
	$PARS['{USER_MAIN_PERFORMER}'] = $user_main_performer;
	$PARS['{USERS_PERFORMERS}'] = $user_performers;
	$PARS['{USERS_COPIES}'] = $user_copies;
	$PARS['{USER_AUTHOR}'] = $user_author;
	
	return fetch_tpl($PARS, $tasks_role_block_tpl);
}

#### Задания
// Страница - Заданий сотрудника
function fill_tasks($to_user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$tasks_list_no_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/tasks_list_no.tpl');
	
	$task_menu_workers_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_menu_workers.tpl');
	
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/includes/functions_pages.php');
	
	
	$tasks_tpl = file_get_contents('templates/tasks1/tasks.tpl');
	
	$list_type = value_proc($_GET['l']);
	
	$list_status = value_proc($_GET['s']) ? value_proc($_GET['s']) : 1;
	
	$key = value_proc($_GET['k'], 0);
	 
	$filter_user_id = value_proc($_GET['uid']);
	
	 
	// Вывод задач по статусу( завершенные, в работе)
	if($list_status=='all')
	{
		$filter_checked_status_0 = 'selected="selected"';
	}
	else if($list_status==2)
	{
		$filter_checked_status_2 = 'selected="selected"';
	}
	else
	{
		$filter_checked_status_1 = 'selected="selected"';
	}
	
	if(!$list_type)
	{
		$list_type = 2;
	}
	
	// Список задач 
	$tasks_list = fill_tasks_list($list_type, 1, $list_status, $key, $filter_user_id);
	
	// Если список задач пустой
	if(!$tasks_list)
	{   
		$tasks_list = $tasks_list_no_tpl;
	}
	
	// подсвечиваем активный  пункт меню
	$list_menu_active[$list_type] =  'active';
	 
	// Уведомления для меню
	$menu_new_count_arr = get_tasks_menu_new_count();
	
	// список пользователей для фильтра
	$filter_users_list = fill_filter_users_list($list_type, $filter_user_id);
	
	// Страничность
	//$pages = fill_pages($href, $p, $all_count, FILES_PER_PAGE);
	
	// если есть поиск по ключевому слову, выводим крестик на очистку текстового поля
	if(!$key)
	{
		$clear_key_text_display = "display:none";
	}
	
	// если у пользователя есть сотрудники выводим пункт в верхнее меню
	if(get_current_user_users_arrs(array(0,1,0,0,1)))
	{
		$task_menu_workers = $task_menu_workers_tpl;
	}
	
	$PARS['{TASKS_LIST}'] = $tasks_list;
	
	$PARS['{TASK_MENU_WORKERS}'] = $task_menu_workers;
	$PARS['{LIST_MENU_ACTIVE_1}'] = $list_menu_active[1];
	$PARS['{LIST_MENU_ACTIVE_2}'] = $list_menu_active[2];
	$PARS['{LIST_MENU_ACTIVE_3}'] = $list_menu_active[3];
	$PARS['{LIST_MENU_ACTIVE_4}'] = $list_menu_active[4];
	$PARS['{LIST_MENU_ACTIVE_5}'] = $list_menu_active[5];
	
	$PARS['{NEW_MENU_ACTIVE_1}'] = $menu_new_count_arr[1];
	$PARS['{NEW_MENU_ACTIVE_2}'] = $menu_new_count_arr[2];
	$PARS['{NEW_MENU_ACTIVE_3}'] = $menu_new_count_arr[3];
	$PARS['{NEW_MENU_ACTIVE_4}'] = $menu_new_count_arr[4];
	
	$PARS['{FILTER_CHECKED_STATUS_0}'] = $filter_checked_status_0;
	$PARS['{FILTER_CHECKED_STATUS_1}'] = $filter_checked_status_1;
	$PARS['{FILTER_CHECKED_STATUS_2}'] = $filter_checked_status_2;
	
	$PARS['{LIST_TYPE}'] = $list_type;
	$PARS['{LIST_STATUS}'] = $list_status;
	$PARS['{KEY}'] = $key;
	$PARS['{FILTER_USER_ID}'] = $filter_user_id;
	
	$PARS['{FILTER_USERS_LIST}'] = $filter_users_list;
	$PARS['{CLEAR_KEY_TEXT_DISPLAY}'] = $clear_key_text_display;
	 
	
	$PARS['{PAGES}'] = $pages;
	
	
	return fetch_tpl($PARS, $tasks_tpl);
}

// Блок пользователей для фильтра
function fill_filter_users_list($list_type, $active_user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$option_tag_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	$task_filter_from_users_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_filter_from_users.tpl');
	
	$task_filter_to_users_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_filter_to_users.tpl');
	
	$task_filter_to_workers_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_filter_to_workers.tpl');
	
	// Выводим пользователей, КТО ВЫСТАВЛЯЛ задачи текущему пользователю
	if($list_type==2)
	{
		$tpl = $task_filter_from_users_tpl;
		$sql = "SELECT i.* FROM tasks_tasks_users i
				LEFT JOIN tasks_tasks_users j ON i.task_id=j.task_id
				WHERE i.role=1 AND j.role=2 AND j.user_id='$current_user_id'";
		$res = $site_db->query($sql);
 		while($row=$site_db->fetch_array($res, 1))
		{
			$users_list_arr[] = $row['user_id'];
		}
	
	}
	// Выводим пользователей, КОМУ ВЫСТАВЛЯЛ задачи текущий пользователю
	else if($list_type==1)
	{
		$tpl = $task_filter_to_users_tpl;
		$sql = "SELECT j.* FROM tasks_tasks_users i
				LEFT JOIN tasks_tasks_users j ON i.task_id=j.task_id
				WHERE i.role=1 AND j.role=2 AND i.user_id='$current_user_id'";
		$res = $site_db->query($sql);		
		while($row=$site_db->fetch_array($res, 1))
		{
			$users_list_arr[] = $row['user_id'];
		}
	}
	// Список сотрудников
	else if($list_type==5)
	{
		$tpl = $task_filter_to_workers_tpl;
		
		$users_list_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	}
	else
	{
		return '';
	}
	 
	
	foreach($users_list_arr as $user_id)
	{
		$user_obj->fill_user_data($user_id);
		$user_name = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
		
		$users_arr[$user_obj->get_user_surname().'_'.$user_id] = array('user_id' => $user_id, 'user_name' => $user_name);
	}
	
	ksort($users_arr);
	
	foreach($users_arr as $i => $user_data)
	{
		$selected = '';
		if($active_user_id==$user_data['user_id'])
		{
			$selected = 'selected="selected"';
		}
		
		$PARS['{VALUE}'] = $user_data['user_id'];
		
		$PARS['{NAME}'] = $user_data['user_name'];
		
		$PARS['{SELECTED}'] = $selected;
		
		$users_list .= fetch_tpl($PARS, $option_tag_tpl);
		
	}
	
	if($users_list)
	{  
		$PARS['{LIST}'] = $users_list;
		
		return fetch_tpl($PARS, $tpl);
	}
	
	//print_r($users_arr);
}

function get_tasks_notices_last_id()
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT * FROM tasks_tasks_notices WHERE user_id='$current_user_id' AND noticed=0 ORDER by notice_id DESC LIMIT 1";
	
	$task_data = $site_db->query_firstrow($sql);
	
	
	return $task_data['notice_id'];
}

function get_tasks_notices_count()
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT * FROM tasks_tasks_notices WHERE user_id='$current_user_id' AND noticed=0";
	
	$res = $site_db->query($sql);
 
	while($row=$site_db->fetch_array($res, 1))
	{
		 $new_notice_count[$row['task_id']] = $row;
	}
	
	foreach($new_notice_count as $task_id => $notices)
	{  
		//$new_task_count += 1;
	}
	
	return count($new_notice_count);
}

// вывод уведомлений в задачах для верхнего меню
function get_tasks_menu_new_count()
{
	global $site_db, $current_user_id;
	
	$new_menu_count_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/new_menu_count.tpl');
	
	$sql = "SELECT * FROM tasks_tasks_notices WHERE user_id='$current_user_id' AND noticed=0";
	
	$res = $site_db->query($sql);
 
	while($row=$site_db->fetch_array($res, 1))
	{
		 $new_notice_count[$row['task_id']] = $row;
	}
   //echo "<pre>", print_r($new_notice_count);
	foreach($new_notice_count as $task_id => $notices)
	{  
		$user_roles = get_user_task_roles($current_user_id, $task_id);
		
		$roles_count = 0;
		
		/*foreach($notices as $notice)
		{
			if($notice['notice_type']==2)
			{
				
			}
			else
			{
				 
			}
			 
		}*/
		$roles_count += 1;
		
		if($user_roles[1]==1)
		{
			$role = 1;
		}
		if($user_roles[2]==1)
		{
			$role = 2;
		}
		if($user_roles[3]==1)
		{
			$role = 3;
		}
		if($user_roles[4]==1)
		{
			$role = 4;
		}
		
		$new_task_count[$role] += 1;
	}
	
	
	foreach($new_task_count as $role => $count)
	{
		if(!$count)
		{
			continue;	
		}
		
		$PARS['{COUNT}'] = $count;
		
		$count_menu[$role] = fetch_tpl($PARS, $new_menu_count_tpl);
	}
	
	return $count_menu;
	
	return array('count_menu_1' => $count_menu[1], 'count_menu_2' =>$count_menu_2, 'count_menu_3' =>$count_menu_3, 'count_menu_4' =>$count_menu_4);
}

// Список задач 
function fill_tasks_list($list_type, $page, $list_status, $key, $filter_user_id)
{
	global $site_db, $current_user_id;
	
	if($filter_user_id && $list_type==5 && !check_user_access_to_user_content($filter_user_id, array(0,1,0,0,1,1)))
	{
		return '';
	}
	// Для задача ВСЕ и ЗАВЕРШЕННЫЕ выводим с использованием страничности
	if($list_status==2 || $list_status=='all' || $list_type==5)
	{
		// Страничность
		$begin_pos = PER_PAGE * ($page-1);
		$limit = " LIMIT ".$begin_pos.",".PER_PAGE;
		$show_by_pagination = 1;
	}
	
	// флаг о том, что на странице требуется сортировка задач, основываясь на ее данных
	if(!$key && !$filter_user_id && $list_status==1 && in_array($list_type, array(1,2,3,4)) && !$show_by_pagination)
	{
		$sort_tasks_in_list_by_array = 1;
		 
	}
	
	
	#############
	#### Фильтры
	#############
	
	// в списке ПОРУЧИЛ, не выводим задачи, которые выставил САМ СЕБЕ
	if($list_type==1)
	{
		$and_own = " AND i.is_own!=1 ";
	}
	// Показать задачи, которые завершены
	if($list_status==2)
	{
		$and_work_status = " AND work_status=2";
	}
	// Показать задачи, которые в работе
	else if($list_status==1)
	{
		$and_work_status = " AND work_status=1";
	}
	// Поиск по ключевым словам
	if($key)
	{
		$and_key = " AND (i.task_theme LIKE '%$key%' OR task_text LIKE '%$key%')";
	}
	
	############# \
	
	
	$tasks = array();
	
	
	
	// Находим задачи, у которых есть уведомления, даже если они завершены и добавляем их к списку
	if(!$show_by_pagination && !$key && !$filter_user_id)
	{
		//Выбор задач с уведомлением
		$sql = "SELECT * FROM tasks_tasks i
				LEFT JOIN tasks_tasks_users j ON i.task_id=j.task_id
				RIGHT JOIN tasks_tasks_notices n ON n.task_id=i.task_id
				WHERE n.user_id='$current_user_id' AND n.noticed=0 AND i.work_status=2 AND j.user_id='$current_user_id' AND j.role='$list_type' AND i.deleted=0 $and_own";
		
		$res = $site_db->query($sql);
		
		while($row=$site_db->fetch_array($res))
		{
			$tasks[$row['task_id']] = $row;
		}
	}
	
	####
	#### Вывод задач МОИХ СОТРУДНИКОВ
	####
	if($list_type==5)
	{
		// Если требуется вывести задачу определенного сотрудника
		if($filter_user_id > 0)
		{
			$and_users = " AND j.user_id='$filter_user_id'";
		}
		else
		{
			$users_list_arr = get_current_user_users_arrs(array(0,1,0,0,1));
			
			if($users_list_arr)
			{
				$uids = implode(',',$users_list_arr);
				$and_users = " AND j.user_id IN($uids)";
			}
		}
		
		$sql = "SELECT DISTINCT(i.task_id), i.* FROM  tasks_tasks_users j  
				LEFT JOIN tasks_tasks i ON i.task_id=j.task_id
				WHERE j.role='2' AND i.deleted=0 $and_users $and_work_status $and_key ORDER by i.task_id DESC $limit";
				
				// echo $sql;
	}
	####
	#### Если задачи фильтруются по пользователю
	####
	else if($filter_user_id > 0)
	{
		// для списка ПОРУЧИЛ
		if($list_type==1)
		{
			$and_from_user_role = " AND jj.role=2 ";
		}
		// для списка ВЫПОЛНЯЮ
		else if($list_type==2)
		{
			$and_from_user_role = " AND jj.role=1 ";
		}
		
		$sql = "SELECT DISTINCT(i.task_id),i.* FROM tasks_tasks i 
				LEFT JOIN tasks_tasks_users j ON j.task_id=i.task_id
				RIGHT JOIN tasks_tasks_users jj ON jj.task_id=j.task_id
				WHERE jj.user_id='$filter_user_id' $and_from_user_role AND j.user_id='$current_user_id' AND j.role='$list_type' AND i.deleted=0 $and_own $and_work_status $and_key ORDER by i.task_id DESC  $limit";
		 
	}
	else
	{
		// Выбор задач из списка
		$sql = "SELECT DISTINCT(i.task_id),i.* FROM tasks_tasks i
				RIGHT JOIN tasks_tasks_users j ON i.task_id=j.task_id
				WHERE j.user_id='$current_user_id' AND j.role='$list_type' AND i.deleted=0 $and_own $and_work_status $and_key ORDER by i.task_id DESC  $limit";
	}
	 
	 
	$res = $site_db->query($sql);
 
	while($row=$site_db->fetch_array($res, 1))
	{
		$tasks[$row['task_id']] = $row;
	}
	
	$tasks_list = array();
		 
	foreach($tasks as $task_id => $task_data)
	{
		$order = '';

		
		// Если требуется сортировка задач
		if($sort_tasks_in_list_by_array )
		{
			// флаг уведомлений задачи
			$task_notices = get_task_in_list_notice_types($task_data['task_id']);
		
			// статус ПРОСРОЧНОСТИ задачи
			$task_expired_status_arr = get_task_expired_status($task_data);
			$task_expired_status = $task_expired_status_arr['status'];
			
			// если задача просрочена
			if($task_expired_status==1)
			{  
				$order = 5;
			}
			// скоро будет просроченной
			else if($task_expired_status==2)
			{
				$order = 4;
			}
			// флаг НОВАЯ ЗАДАЧА
			else if($task_notices['new_task'])
			{
				$order = 3;
			}
			else if(is_date_exists($task_data['task_max_date']))
			{ 
			//echo $task_data['task_max_date'].' ';
				$order = 2;
			}
			// у задачи есть УВЕДОМЛЕНИЯ
			else if($task_notices['has_notice'])
			{
				$order = 1;
			}
			// сортировка по умолчанию
			else
			{
				$order = 0;
			}
			
			$order .= '_'.$task_data['task_id'];
		}
		else
		{
			$order = $task_data['task_id'];
		}
		 
		$tasks_list[$order] = fill_tasks_list_item($task_data, $task_notices);
	}
	 
	// сортировка в порядке приоритета вывода задачи
	krsort($tasks_list);
	
	$tasks_list = implode('', $tasks_list);
	
	
	return $tasks_list;
}

// возвращает статус ПРОСРОЧНОСТИ задачи
function get_task_expired_status($task_data)
{
	global $site_db, $current_user_id;
	
	$is_expired = 0;
	
	if($task_data['work_status']==2 || ($task_data['step_status']==3 && is_date_exists($task_data['date_status_3'])))
	{
		$end_task_time = to_mktime($task_data['date_status_3']); 
	}
	else
	{
		$end_task_time = time();
	}
	
	if(is_date_exists($task_data['task_max_date']))
	{
		$razn = $end_task_time - to_mktime($task_data['task_max_date']);
		
		// просрочена
		if($razn > 0)
		{ 
			$date_result_arr =  sec_to_date_words(abs($razn), 0);
			$date_result = $date_result_arr['string'];
			$str = $date_result_arr['is_days'] ? 'на' : '';
			$time_expired = $str." ".$date_result;
			
			$expired_status = 1;
		}
		// скоро истекает срок
		else if(abs($razn) < 3600 * 24)
		{
			$expired_status = 2;
		}
	}
	
	return array('status' => $expired_status, 'time_expired' => $time_expired);
}

// возвращает флаг новых событий для задачи
function get_task_in_list_notice_types($task_id)
{
	global $site_db, $current_user_id;
	
	// проверяем, есть ли в задаче уведомления
	$sql = "SELECT * FROM tasks_tasks_notices WHERE task_id='$task_id' AND user_id='$current_user_id' AND noticed=0";
	
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{
		if($row['notice_type']==0)
		{
			$new_task = 1;
		}
		else
		{
			$has_notice = 1;
		}
	}
	
	return array('new_task' => $new_task, 'has_notice' => $has_notice);
	
}

// элемент задачи
function fill_tasks_list_item($task_data, $task_notices)
{
	global $site_db, $current_user_id;
	
	$task_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_list_item.tpl');
	
	$task_new_label_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_new_label.tpl');
	
	$task_new_finished_label_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_new_finished_label.tpl');
	
	$task_new_reports_label_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_new_reports_label.tpl');
	
	$task_list_max_date_over_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_list_max_date_over.tpl');
	 
	$task_list_max_date_coming_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_list_max_date_coming.tpl');
	
	$task_list_priority_high_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_list_priority_high.tpl');
	$task_list_priority_middle_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_list_priority_middle.tpl');
	$task_list_priority_low_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/task_list_priority_low.tpl');
	
	$task_id = $task_data['task_id'];
	
	// постановщик задачи
	$from_user = get_task_user_role_1($task_id);
	// исполнитель задачи задачи
	$task_performer_main = get_task_user_role_2($task_id);
	 
	// Автор
	$task_from = get_formate_user_name($from_user);
	
	// Автор
	$task_performer = get_formate_user_name($task_performer_main);
	
	// новая задача
	if($task_notices['new_task']==1)
	{
		$new_task_notice = $task_new_label_tpl;
	}
	// у задачи есть новые события
	if($task_notices['has_notice']==1)
	{
		$new_reports_notice = $task_new_reports_label_tpl;
	}
	
	

	// максимальное время выполнения
	if(is_date_exists($task_data['task_max_date']))
	{
		// статус ПРОСРОЧНОСТИ задачи
		$task_expired_status_arr = get_task_expired_status($task_data);
		$task_expired_status = $task_expired_status_arr['status'];
		
		if(datetime($task_data['task_max_date'], '%H')!='00' || datetime($task_data['task_max_date'], '%i')!='00')
		{
			$task_max_date = datetime($task_data['task_max_date'], '%H:%i %d.%m.%Y');
		}
		else
		{
			$task_max_date = datetime($task_data['task_max_date'], '%d.%m.%Y');
		}
		
		// Если задача ПРОСРОЧЕНА 
		if($task_expired_status==1)
		{
			$PARS['{TIME}'] = $task_expired_status_arr['time_expired'] ;
			$PARS['{DATE}'] = $task_max_date;
			$task_max_date = fetch_tpl($PARS, $task_list_max_date_over_tpl); 
			
			$notice_class = "status_1";
		}
		// Если задача ПРОСРОЧЕНА 
		else if($task_expired_status==2)
		{
			$PARS['{DATE}'] = $task_max_date;
			$task_max_date = fetch_tpl($PARS, $task_list_max_date_coming_tpl); 
			$notice_class = "status_2";
		}
			 
	}
	
	if($task_data['work_status']==2)
	{
		 $notice_class = 'status_3';
	}
	
	// приоритет
	switch($task_data['task_priority'])
	{
		case '1':
			$task_priority = $task_list_priority_low_tpl;
		break;
		case '2':
			$task_priority = $task_list_priority_middle_tpl;
		break;
		case '3':
			$task_priority = $task_list_priority_high_tpl;
		break;
	}
	
	// тема задачи
	if($task_data['task_theme'])
	{
		$task_theme = $task_data['task_theme'];
	}
	else
	{
		$task_theme = substr($task_data['task_text'],0,150);
	}
	
	$task_theme_cut =  strlen($task_theme) > 73 ? substr($task_theme,0,73).'...' : $task_theme;
	
	if($_SERVER['QUERY_STRING'])
	{
		$query_str = str_replace('o=tasks1','', $_SERVER['QUERY_STRING']);
		
		$referer_par = "&rf=".urlencode($query_str);
	}
	 
	
	 
		
	$PARS['{TASK_ID}'] = $task_data['task_id'];
	
	$PARS['{NOTICE_CLASS}'] = $notice_class;
	
	$PARS['{TASK_THEME}'] = $task_theme;
	$PARS['{TASK_THEME_CUT}'] = $task_theme_cut;
	
	$PARS['{TASK_FROM}'] = $task_from;
	
	$PARS['{TASK_PERFORMER_MAIN}'] = $task_performer;
	
	$PARS['{TASK_MAX_DATE}'] = $task_max_date;
	
	$PARS['{NEW_TASK_NOTICE}'] = $new_task_notice;
	
	$PARS['{NEW_REPORTS_NOTICE}'] = $new_reports_notice;
	
	$PARS['{PRIORITY}'] = $task_priority;
	
	$PARS['{ADD_DATE}'] = datetime($task_data['date_add'], '%d.%m.%Y');
	
	$PARS['{REFERER_PAR}'] = $referer_par;
	
	return fetch_tpl($PARS, $task_list_item_tpl); 
}


// Форма доабвления задачи
function fill_task_add_form()
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	$add_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/add_form.tpl');
	$option_fcbk_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl');
	
	
	$task_from_user_name = $current_user_obj->get_user_surname().' '.$current_user_obj->get_user_name().' '.$current_user_obj->get_user_middlename().', '.$current_user_obj->get_user_position(); 
	
	$PARS['{CLASS}'] = 'selected';
	$PARS['{VALUE}'] = $current_user_id;
	$PARS['{NAME}'] = $task_from_user_name;
	$task_from_select = fetch_tpl($PARS, $option_fcbk_tpl);
		
	 
	 
	$PARS['{DIFFICULTY_OPTION_LIST}'] = get_task_difficulty_list();
	
	$PARS['{PRIORITY_OPTION_LIST}'] = get_task_priority_list();
	
	$PARS['{TASK_FROM_SELECT}'] = $task_from_select;
		
	return fetch_tpl($PARS, $add_form_tpl); 
}

// форма редактирования
function get_task_edit_form($task_id)
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	$add_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/edit_form.tpl');
	$option_fcbk_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl');
	$user_select_performers_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/user_select_performers.tpl');
	$user_select_copies_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks1/user_select_copies.tpl');
	
	// данные по задаче
	$sql = "SELECT * FROM tasks_tasks WHERE task_id='$task_id'";
	
	$task_data = $site_db->query_firstrow($sql);
	
	if(!$task_data['task_id'] || $task_data['deleted']==1 )
	{
		fill_404();
	} 
	
	
	// Пользователи задачи
	$sql = "SELECT * FROM tasks_tasks_users WHERE task_id='$task_id'";
	
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{
	 	$user_obj->fill_user_data($row['user_id']);
		$user_name = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename().', '.$user_obj->get_user_position(); 
		
		$PARS['{CLASS}'] = 'selected';
		$PARS['{VALUE}'] = $row['user_id'];
		$PARS['{NAME}'] = $user_name;
		$user_select = fetch_tpl($PARS, $option_fcbk_tpl);
	
		$task_users[$row['role']][] = $user_select;
	}
	
	foreach($task_users as $role => $user_select)
	{
		// постановщик
		if($role==1)
		{ 
			$task_from_select = implode('', $user_select);
		}
		// главнй исполнитель
		if($role==2)
		{ 
			$task_performer_main = implode('', $user_select);
		}
		// соисполнители
		if($role==3)
		{ 
			foreach($user_select as $user)
			{ 
				$PARS['{OPTION}'] = $user;
				$PARS['{TASK_ID}'] = $task_id;
				$PARS['{RAND}'] = rand(10000,99999);
				$task_users_performers .= fetch_tpl($PARS, $user_select_performers_tpl);
			}
			 
		}
		// копия
		if($role==4)
		{ 
			foreach($user_select as $user)
			{ 
				$PARS['{OPTION}'] = $user;
				$PARS['{TASK_ID}'] = $task_id;
				$PARS['{RAND}'] = rand(10000,99999);
				$task_users_copies .= fetch_tpl($PARS, $user_select_copies_tpl);
			}
		}
	}
	 
	if($task_data['task_max_date'])
	{
		$max_date = datetime($task_data['task_max_date'], '%d.%m.%Y');
		$max_date_hours = datetime($task_data['task_max_date'], '%H');
		$max_date_minuts = datetime($task_data['task_max_date'], '%i');
	}
	
	// Список файлов для отчета
	$files_list = get_attached_files_to_content($task_id, 6, 2);
	
	$PARS['{TASK_ID}'] = $task_id;
	 
	$PARS['{FILES_LIST}'] = $files_list;
	
	$PARS['{TASK_THEME}'] = $task_data['task_theme'];
	
	$PARS['{TASK_TEXT}'] = $task_data['task_text'];
	
	$PARS['{MAX_DATE}'] = $max_date;
	
	$PARS['{MAX_DATE_HOURS}'] = $max_date_hours;
	
	$PARS['{MAX_DATE_MINUTS}'] = $max_date_minuts;
	 
	$PARS['{DIFFICULTY_OPTION_LIST}'] = get_task_difficulty_list($task_data['task_difficulty']);
	
	$PARS['{PRIORITY_OPTION_LIST}'] = get_task_priority_list($task_data['task_priority']);
	
	$PARS['{TASK_FROM_SELECT}'] = $task_from_select;
	$PARS['{TASK_PERFORMER_MAIN}'] = $task_performer_main;
	$PARS['{TASK_USERS_PERFORMERS}'] = $task_users_performers;
	$PARS['{TASK_USERS_COPIES}'] = $task_users_copies;
		
	return fetch_tpl($PARS, $add_form_tpl); 
}


// Возвращает <options> приоритета выполнения задания
function get_task_priority_list($priority)
{
	global $site_db;
	
	$option_tag_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	$sql = "SELECT * FROM ".PRIORITY_TB;
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$PARS['{VALUE}'] = $row['priority_id'];
		
		$PARS['{NAME}'] = $row['priority_name'];
		
		// Если приоритет передан в функцию
		if($priority)
		{
			if($row['priority_id']==$priority)
			{
				$selected = "selected";
			}
			else
			{
				$selected = '';
			}
		}
		else
		{
			if($row['priority_selected'])
			{
				$selected = 'selected="selected"';
			}
			else
			{
				$selected = '';
			}
		}
		$PARS['{SELECTED}'] = $selected;
		
		$priority_list .= fetch_tpl($PARS, $option_tag_tpl);
	}
	
	return $priority_list;
}

// Возвращает <options> сложности выполнения задания
function get_task_difficulty_list($difficulty)
{
	global $site_db;
	
	$option_tag_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	$sql = "SELECT * FROM ".DIFFICULTY_TB." ORDER by difficulty_id ASC";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$PARS['{VALUE}'] = $row['difficulty_id'];
		
		$PARS['{NAME}'] = $row['difficulty_name'];
		
		// Если приоритет передан в функцию
		if($difficulty)
		{
			if($row['difficulty_id']==$difficulty)
			{
				$selected = "selected";
			}
			else
			{
				$selected = '';
			}
		}
		else
		{
			if($row['difficulty_select'])
			{
				$selected = 'selected="selected"';
			}
			else
			{
				$selected = '';
			}
		}
		$PARS['{SELECTED}'] = $selected;
		
		$difficulty_list .= fetch_tpl($PARS, $option_tag_tpl);
	}
	
	return $difficulty_list;
}
// Название приоритета по его id
function get_priority_name_by_id($priority_id)
{
	global $site_db;
	
	$sql = "SELECT priority_name FROM ".PRIORITY_TB." WHERE priority_id='$priority_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['priority_name'];
}

// Название сложности задачи по ее id
function get_difficulty_name_by_id($difficulty_id)
{
	global $site_db;
	
	$sql = "SELECT difficulty_name FROM ".DIFFICULTY_TB." WHERE difficulty_id='$difficulty_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['difficulty_name'];
}

// изменение статуса задачи
function task_status_to_default($task_id, $step_status)
{
	global $site_db, $current_user_id;
	
	$sql = "UPDATE tasks_tasks SET work_status = 1, step_status = '$step_status', date_status_1=0, date_status_2=0, date_status_3=0 
			WHERE task_id='$task_id'";
	
	$site_db->query($sql);
}

// сохранение пользователей для задачи
function save_task_users($task_id, $task_from_user, $task_user_performer_main, $task_users_performers, $task_users_copies, $to_delegate=0)
{
	global $site_db, $current_user_id;
	
	$users_in_tasks[1] = array();
	$users_in_tasks[2] = array();
	$users_in_tasks[3] = array();
	$users_in_tasks[4] = array();
	 
	$sql = "SELECT * FROM tasks_tasks_users WHERE task_id='$task_id'";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$users_in_tasks[$row['role']][] = $row['user_id'];
	}
	
	if($to_delegate)
	{
		// Выявляем изменения в ролях - основной исполнитель задачи
		$to_delete_role_2 = array_diff($users_in_tasks[2], array($task_user_performer_main));
		$to_add_role_2 = array_diff(array($task_user_performer_main), $users_in_tasks[2]);
		$to_add_role_2 = array_slice($to_add_role_2,0,1); // всего один пользователь данной роли
		delete_task_users_iter($task_id, $to_delete_role_2, 2);
		insert_task_users_iter($task_id, $to_add_role_2, 2);
		
		// добавляем текущего пользователя в копию
		insert_task_users_iter($task_id, array(0=>$current_user_id), 4);
		
	}
	else
	{
	
		// Выявляем изменения в ролях - постановщик задачи
		$to_delete_role_1 = array_diff($users_in_tasks[1], array($task_from_user));
		$to_add_role_1 = array_diff(array($task_from_user), $users_in_tasks[1]);
		$to_add_role_1 = array_slice($to_add_role_1,0,1); // всего один пользователь данной роли
		delete_task_users_iter($task_id, $to_delete_role_1, 1);
		insert_task_users_iter($task_id, $to_add_role_1, 1);
		
		
		// Выявляем изменения в ролях - основной исполнитель задачи
		$to_delete_role_2 = array_diff($users_in_tasks[2], array($task_user_performer_main));
		$to_add_role_2 = array_diff(array($task_user_performer_main), $users_in_tasks[2]);
		$to_add_role_2 = array_slice($to_add_role_2,0,1); // всего один пользователь данной роли
		delete_task_users_iter($task_id, $to_delete_role_2, 2);
		insert_task_users_iter($task_id, $to_add_role_2, 2);
		 
		 
		// Выявляем изменения в ролях - соисполнители задачи
		$to_delete_role_3 = array_diff($users_in_tasks[3], $task_users_performers);
		$to_add_role_3 = array_diff($task_users_performers, $users_in_tasks[3]);
		delete_task_users_iter($task_id, $to_delete_role_3, 3);
		insert_task_users_iter($task_id, $to_add_role_3, 3);
		
		
		// Выявляем изменения в ролях - пользователи в копии задачи
		$to_delete_role_4 = array_diff($users_in_tasks[4], $task_users_copies);
		$to_add_role_4 = array_diff($task_users_copies, $users_in_tasks[4]);
		delete_task_users_iter($task_id, $to_delete_role_4, 4);
		insert_task_users_iter($task_id, $to_add_role_4, 4);
	}
	
}

// Удаление повторяющихся ролей.
function delete_task_role_users($task_id, $user_id, $role_array = array())
{
	global $site_db;
	
	$users_arr = array();
	
	if($role_array)
	{
		$roles = implode(',', $role_array);
		
		// Выбор ролей, меньших по приоритету
		$sql = "SELECT * FROM tasks_tasks_users WHERE task_id='$task_id' AND user_id='$user_id' AND role IN($roles)";
		
		$res = $site_db->query($sql);
		
		while($row=$site_db->fetch_array($res))
		{
			// Удаляем пользователей задачи
			delete_task_users_iter($task_id, array($row['user_id']), $row['role']);
			
		}
	}
	
	return $users_arr;
}

// проверяет, имеет ли пользователь роль в задаче
function check_user_in_task_role($task_id, $user_id, $role_array = array())
{
	global $site_db;
	
	$roles = implode(',', $role_array);
		
	$sql = "SELECT * FROM tasks_tasks_users WHERE task_id='$task_id' AND user_id='$user_id' AND role IN($roles)";
		 
	$check_data = $site_db->query_firstrow($sql);
		
	if($check_data['id']) return true;
	else return false;
	
}

// удаляет пользователей у задачи 
function delete_task_users_iter($task_id, $users_arr, $role)
{
	global $site_db;
	
	foreach($users_arr as $i => $user_id)
	{
		if(!$user_id) continue;
		
		$sql = "DELETE FROM tasks_tasks_users WHERE task_id='$task_id' AND role='$role' AND user_id='$user_id'";
		$site_db->query($sql);
		// удаляем все уведомления пользователя
		delete_task_notice($task_id, 0, 0, $user_id, 1, 'by_user');
	}
	 
}

// добавляет пользователей к задаче
function insert_task_users_iter($task_id, $users_arr, $role)
{
	global $site_db, $current_user_id;
	
	foreach($users_arr as $i => $user_id)
	{
		if($role==1 || $role==2)
		{
			// удаляем этих пользователей, которые занимают другие роли в задаче
			delete_task_role_users($task_id, $user_id, array(3,4));
		}
		
		// проверяем, занимает ли пользователь роль выше по приоритету(постановщик, главный исполнитель)
		if($role==3 && check_user_in_task_role($task_id, $user_id, array(1,2,3)))
		{
			 continue;
		}
		// проверяем, занимает ли пользователь роль выше по приоритету(постановщик, главный исполнительб соисполнитель)
		if($role==4 && check_user_in_task_role($task_id, $user_id, array(1,2,3,4)))
		{
			 continue;
		}
		
		
		if(!$user_id) continue;
		
		// не уведомляем самого себя
		if($current_user_id==$user_id) { $and_noticed = " ,noticed=1";}
		
		$sql = "INSERT INTO tasks_tasks_users SET task_id='$task_id', role='$role', user_id='$user_id', date_add = NOW() $and_noticed";
		$site_db->query($sql);
		
		// сбрасываем статус задачи, если меняется исполнитель
		if($role==2)
		{
			// проверяем роли пользователя у задачи
			$roles_in_tasks = get_user_task_roles($user_id, $task_id);
			
			$step_status = 0;
			if($roles_in_tasks[1]==1 && $roles_in_tasks[2]==1)
			{
				$step_status = 1;
			}
			// обновление статуса задачи
			task_status_to_default($task_id, $step_status);
		}
		
		// уведомляем добавленного к задаче пользователя
		add_task_notice($task_id, $current_user_id, 0, 0, 0, $user_id);
		
		if($role==1 || $role==2)
		{
			task_check_for_own($task_id);
		}
		 
	}
}

// обновляет флаг задачи - ЗАДАЧА САМОМУ СЕБЕ
function task_check_for_own($task_id)
{
	global $site_db, $current_user_id;
	
	$from_user = get_task_user_role_1($task_id);
	$to_user = get_task_user_role_2($task_id);
	
	if($from_user==$to_user)
	{
		$sql = "UPDATE tasks_tasks SET is_own=1 WHERE task_id='$task_id'";
		$site_db->query($sql);
	}
	else
	{
		$sql = "UPDATE tasks_tasks SET is_own=0 WHERE task_id='$task_id'";
		$site_db->query($sql);
	}
}



// добавление ролей по задаче
function add_task_users($task_id, $task_from_user, $task_user_performer_main, $task_users_performers, $task_users_copies)
{
	global $site_db, $current_user_id;
	
	$inserted_users = array();
	
	// постановщий задачи
	if($task_from_user)
	{
		// не уведомляем самого себя
		//if($current_user_id==$task_from_user) { $and_noticed = " ,noticed=1";}
		
		
		$sql = "INSERT INTO tasks_tasks_users SET task_id='$task_id', user_id='$task_from_user', role=1, date_add=NOW() $and_noticed";
		$site_db->query($sql);
		
		$inserted_users[] = $task_from_user;
		
		// уведомляем пользователя
		add_task_notice($task_id, $current_user_id, 0, 0, 0, $task_from_user);
	}
	
	// исполнитель задачи
	if($task_user_performer_main)
	{
		// не уведомляем самого себя
		if($current_user_id==$task_user_performer_main) { $and_noticed = " ,noticed=1";}
		
		$sql = "INSERT INTO tasks_tasks_users SET task_id='$task_id', user_id='$task_user_performer_main', role=2, date_add=NOW() $and_noticed";
		$site_db->query($sql);
		
		$inserted_users[] = $task_user_performer_main;
		
		// уведомляем пользователя
		add_task_notice($task_id, $current_user_id, 0, 0, 0, $task_user_performer_main);
	}
	
	// соисполнители
	foreach($task_users_performers as $user_id)
	{
		if(in_array($user_id, $inserted_users) || !$user_id)
		{
			continue;
		}
		
		$and_noticed = '';
		// не уведомляем самого себя
		if($current_user_id==$user_id) { $and_noticed = " ,noticed=1";}
		
		$sql = "INSERT INTO tasks_tasks_users SET task_id='$task_id', user_id='$user_id', role=3, date_add=NOW() $and_noticed";
		$site_db->query($sql);
		
		$inserted_users[] = $user_id;
		
		// уведомляем пользователя
		add_task_notice($task_id, $current_user_id, 0, 0, 0, $user_id);
	}
	
	// копия
	foreach($task_users_copies as $user_id)
	{
		if(in_array($user_id, $inserted_users) || !$user_id)
		{
			continue;
		}
		
		$and_noticed = '';
		// не уведомляем самого себя
		if($current_user_id==$user_id) { $and_noticed = " ,noticed=1";}
		
		$sql = "INSERT INTO tasks_tasks_users SET task_id='$task_id', user_id='$user_id', role=4, date_add=NOW() $and_noticed";
		$site_db->query($sql);
		
		$inserted_users[] = $user_id;
		
		// уведомляем пользователя
		add_task_notice($task_id, $current_user_id, 0, 0, 0, $user_id);
	}
}

// данные задачи
function get_task_data($task_id)
{
	global $site_db, $current_user_id;
	
	// выбор задачи
	$sql = "SELECT * FROM tasks_tasks WHERE task_id='$task_id'";
	
	$task_data = $site_db->query_firstrow($sql);
	
	return $task_data;
}

// добавление уведомления к задачам для пользователей
function add_task_notice($task_id, $from_user_id, $notice_type, $id, $notice_only_roles=array(), $only_user_id)
{
	global $site_db, $current_user_id;
	
	// types:
	// 0 - новая задача
	// 1 - новый отчет
	// 2 - уведомление начальника о выполнении
	
	if($only_user_id)
	{
		$users_arr[] = $only_user_id;
	}
	else if($notice_only_roles)
	{ 
		// отдельные пользователи по ролям
		$users_arr = get_task_users($task_id, $notice_only_roles, 0);
	}
	else
	{
		// список пользователей задачи
		$users_arr = get_task_users($task_id, '', 0);
	}
	
	$inserted_users = array();
	
	// добавляем уведомление каждому пользователю
	foreach($users_arr as $user_id)
	{
		// не добавляем уведомление пользователю, кем было съиницировано действие
		if($user_id==$from_user_id)
		{
			continue;
		}
		
		if(in_array($user_id, $inserted_users) || !$user_id)
		{
			continue;
		}
		
		$sql = "INSERT INTO tasks_tasks_notices SET task_id='$task_id', user_id='$user_id', notice_type='$notice_type', id='$id'";
	
		$site_db->query($sql);
		
		$inserted_users[] = $user_id;
	}	
	
}

// удаление уведомления
function delete_task_notice($task_id, $notice_type, $id, $user_id, $delete_all=0, $delete_all_type)
{
	global $site_db, $current_user_id;
	
	if($notice_type==1)
	{
		$and_id = " AND id='$id'";
	}
	
	// удаление всех уведомлений пользователя
	if($delete_all && $delete_all_type=='by_user')
	{
		$sql = "DELETE FROM tasks_tasks_notices WHERE task_id='$task_id' AND user_id='$user_id'";
	}
	// удаление всех уведомлений задачи
	else if($delete_all && $delete_all_type=='by_task')
	{
		$sql = "DELETE FROM tasks_tasks_notices WHERE task_id='$task_id'";
	}
	else
	{
		$sql = "DELETE FROM tasks_tasks_notices WHERE task_id='$task_id' AND notice_type='$notice_type' $and_id";
	}
	
	$site_db->query($sql);
}

// кол-во активных задач
function get_user_active_tasks_count($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(DISTINCT(i.task_id)) as count FROM tasks_tasks i
			LEFT JOIN tasks_tasks_users j ON i.task_id=j.task_id
			WHERE i.deleted=0 AND j.user_id='$user_id' AND j.role=2 AND i.work_status=1";
			
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// кол-во поставленных задач
function get_tasks_from_user_count($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(DISTINCT(i.task_id)) as count FROM tasks_tasks i
			LEFT JOIN tasks_tasks_users j ON i.task_id=j.task_id
			WHERE i.deleted=0 AND j.user_id='$user_id' AND j.role=1";
			
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// кол-во поставленных задач
function get_active_tasks_count_to_user($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(DISTINCT(i.task_id)) as count FROM tasks_tasks i
			LEFT JOIN tasks_tasks_users j ON i.task_id=j.task_id
			WHERE i.deleted=0 AND i.work_status=1 AND j.user_id='$user_id' AND j.role=2";
			
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// кол-во выполенных задач
function get_user_completed_tasks_all_count($user_id, $limit_days)
{
	global $site_db, $current_user_id;
	
	if($limit_days)
	{
		$mk_time_days_from = mktime() - 3600 * 24 * $limit_days;
		
		$date_s = date('Y-m-d', $mk_time_days_from);
		
		$and_date = "AND date_status_3>='$date_s'";
	
	}
	
	$sql = "SELECT COUNT(DISTINCT(i.task_id)) as count FROM tasks_tasks i
			LEFT JOIN tasks_tasks_users j ON i.task_id=j.task_id
			WHERE i.deleted=0 AND j.user_id='$user_id' AND j.role=2 AND i.work_status=2 AND i.step_status=4 $and_date";
			
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Средний балл оценок с начала даты
function get_user_tasks_average_rating($user_id, $limit_days=0 )
{
	global $site_db;
	
	$mk_time_days_from = mktime() - 3600 * 24 * $limit_days;
		
	$date_s = date('Y-m-d', $mk_time_days_from);
		
	$and_date = "AND date_status_3>='$date_s'";
	
	
	// Выбираем среднее кол-во оценок
	$sql = "SELECT ROUND(AVG(task_rating),2) as avg_rating FROM tasks_tasks i
			LEFT JOIN tasks_tasks_users j ON i.task_id=j.task_id
			WHERE i.deleted=0 AND j.user_id='$user_id' AND j.role=2 AND i.work_status=2 AND i.step_status=4 AND i.task_rating>0 $and_date";
			 
	$row = $site_db->query_firstrow($sql);
	
	return $row['avg_rating'];
}
 
?>