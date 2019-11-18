<?php
#### Задания
// Страница - Заданий сотрудника
function fill_worker_tasks($to_user_id)
{
	global $site_db, $current_user_id, $user_obj;

	// Если сотрудник не является ПОСТОЯННЫМ подчиненным или ВРЕМЕННЫМ
	if(!check_user_access_to_user_content($to_user_id, array(0,1,0,0,1)))
	{
		header('Location: /');
		exit();
	}
	
	$worker_tasks_list_tpl = file_get_contents('templates/tasks/worker_tasks_list.tpl');
	
	$add_new_task_form_tpl = file_get_contents('templates/tasks/add_new_task_form.tpl');
	
	$no_tasks_tpl = file_get_contents('templates/tasks/no_tasks.tpl');
	
	// Если задачи за выбранную дату
	if($_GET['date'])
	{
		$date = $_GET['date'];
	}
	// Список задач
	$tasks_list = fill_worker_tasks_list($to_user_id, $date);
	
	if(!$tasks_list)
	{
		$tasks_list = $no_tasks_tpl;
	}
	
	// Блок календаря
	$calendar_block = fill_calendar_block($to_user_id, 'worker_task');
	
	$PARS = array();
	
	// Для выбранной даты не выводим блок добавления задания
	if(!isset($_GET['date']))
	{
		$PARS_1['{TO_USER_ID}'] = $to_user_id;
		
		for($i=1;$i<31;$i++)
		{
			$PARS2['{NAME}'] = $i.' '.numToword($i, array('день', 'дня', 'дней'));
		
			$PARS2['{VALUE}'] = $i;
		
			$PARS2['{SELECTED}'] = '';
		
			$task_desired_list .= fetch_tpl($PARS2, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl'));
		
		}
		
		$PARS_1['{TASK_COPY}'] = $_GET['copy'];
		
		$PARS_1['{DIFFICULTY_OPTION_LIST}'] = fill_difficulty_options();
	
		$PARS_1['{PRIORITY_OPTION_LIST}'] = fill_priority_options();
	
		$PARS_1['{TASK_DESIRED_LIST}'] = $task_desired_list;
		
		$add_new_task_form = fetch_tpl($PARS_1, $add_new_task_form_tpl);
	}
	
	// Панель поиска
	$tasks_search_form = fill_tasks_search_form($to_user_id);
	
	$PARS = array();
	
	// Данные пользователя
	// Заполянем объект пользователя
	$user_obj->fill_user_data($to_user_id);
		
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{USERSURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{TASKS_LIST}'] = $tasks_list;
	
	$PARS['{TO_USER_ID}'] = $to_user_id;
	
	$PARS['{ADD_TASK_FORM}'] = $add_new_task_form;  
	
	$PARS['{CALENDAR_BLOCK}'] = $calendar_block;

	$PARS['{DATE}'] = $date;
	
	$PARS['{TASK_SEARCH}'] = $tasks_search_form;
	
	return fetch_tpl($PARS, $worker_tasks_list_tpl);
}



// Список заданий для сотрудника
function fill_worker_tasks_list($to_user_id, $date, $search_word='')
{
	global $site_db, $current_user_id, $_CURRENT_USER_ALL_BOSS_ARR;
	
	$task_group_by_date_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_group_by_date.tpl');
	 
	// Если выбор для конкретной даты
	if($date)
	{
		$and_date = " AND task_date='$date' ";
	}
	
	$actual_date = date('Y-m-d');
	
	
	$date = value_proc($date);

	
	if($search_word)
	{
		// Задачи пользователя
		$sql = "SELECT *, if(task_status=3,1,0) as order_by_task_status FROM ".TASKS_TB." 
				WHERE task_to_user='$to_user_id' AND task_deleted<> 1
				AND (task_theme LIKE '%$search_word%' OR  task_text LIKE '%$search_word%')
				ORDER by order_by_task_status, task_priority DESC, task_desired_date ASC, task_id DESC";		
	}
	else if($date)
	{
		// Выбор задач
		$sql = "SELECT *, if(task_status=3,1,0) as order_by_task_status FROM ".TASKS_TB." 
				WHERE task_to_user='$to_user_id' AND task_deleted<> 1  $and_without 
					  AND (
					  		(task_finished_confirm = 1 AND task_finished_date LIKE '$date%')
							OR
							(task_finished_confirm = 0 AND task_status=5 AND task_finished_fail_date LIKE '$date%')
							OR
							(task_finished_confirm = 0 AND task_date = '$date')
						   )  $and_search_words
				ORDER by order_by_task_status, task_priority DESC, task_desired_date ASC, task_id DESC";
				 
	}
	else
	{
		// Задачи пользователя
		$sql = "SELECT *, if(task_status=3,1,0) as order_by_task_status FROM ".TASKS_TB." 
				WHERE task_to_user='$to_user_id' AND task_deleted<> 1 $and_without
				AND (
						(task_finished_confirm = 0 AND task_status<>5) 
						OR 
						(task_finished_confirm=0 AND task_status=5 AND task_finished_fail_date>='$actual_date') 
						OR 
						(task_finished_confirm=1 AND task_finished_date>='$actual_date')
					) $and_search_words
				ORDER by order_by_task_status, task_priority DESC, task_desired_date ASC, task_id DESC";			
				 
	}
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{ 
		$tasks_list_arr[$row['task_date']] .= fill_worker_tasks_list_item($row);
	}
	
	krsort($tasks_list_arr);
	
	foreach($tasks_list_arr as $date => $list)
	{
		$PARS['{LIST}'] = $list;
		
		$PARS['{DATE}'] = $date;
		
		$PARS['{DATE_RUS}'] = datetime($date, '%j %F');
		
		$tasks_list .= fetch_tpl($PARS, $task_group_by_date_tpl);
	}
	
	return $tasks_list;
}

// Заполнение элемента списка заданий данными
function fill_worker_tasks_list_item($task_data, $for_personal=0)
{
	global $user_obj, $current_user_id, $_CURRENT_USER_ALL_BOSS_ARR;
	
	$tasks_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_list_item.tpl');
	
	$tasks_list_item_on_personal_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_list_item_on_personal.tpl');
	
	$tasks_myself_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_myself_list_item.tpl');
	
	$task_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_edit_tools.tpl');
	
	$task_confirm_finished_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_confirm_finished.tpl');
	
	$task_finished_fail_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_finished_fail.tpl');
	
	$tasks_quality_result_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_quality_result.tpl');
	
	$no_tasks_quality_result_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/no_tasks_quality_result.tpl');
	
	$task_max_date_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_max_date.tpl');
	
	$task_desired_date_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_desired_date.tpl');
	
	$task_theme_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_theme.tpl');
	
	$admin_task_status_btn_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/admin_task_status_btn_block.tpl');
	
	$btn_sep_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/btn_sep.tpl');
	
	$task_date_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_date_eidt.tpl');
	
	$task_item_status_bar_more_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_item_status_bar_more.tpl');
	
	$a_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/a.tpl');
	
	$new_tasks_reports_sms_notice_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/new_tasks_reports_sms_notice.tpl');
	
	// Автор задания
	if($task_data['task_from_user'] == $current_user_id)
	{
		$task_admin = 1;
	}
	// Автору задачи выводим блоки управления заданием
	if($task_admin)
	{
		$PARS_1['{TASK_ID}'] = $task_data['task_id'];
		
		$task_edit_tools = fetch_tpl($PARS_1, $task_edit_tools_tpl);
	} 
	
	// Расширенный блок статуса о выполнении задания
	$task_extend_status_arr = fill_task_extend_status_block($task_data);
	 
	// Статус панель задания
	$status_bar = fill_task_status($task_data['task_id'], $task_data['task_status'], $task_data['task_date_add'], $task_data['task_confirm_date'], $task_data['task_in_proc_date'], $task_data['task_finished_date']);
	
	
	$task_extend_status_warning = $task_extend_status_arr['desired_left'].$task_extend_status_arr['max_left'];
	$task_extend_status_fail = $task_extend_status_arr['desired_over'].$task_extend_status_arr['max_over'];
	$task_extend_status_info = $task_extend_status_arr['confirm'].$task_extend_status_arr['proc'].$task_extend_status_arr['complete'];
	
	// Если есть данные в расширенном статусе задания, делаем кнопкой статус задания, чтобы можно было раскрыть список
	if($task_extend_status_warning || $task_extend_status_fail || $task_extend_status_info)
	{
		$PARS_2['{TASK_ID}'] = $task_data['task_id'];
		
		$status_bar .= fetch_tpl($PARS_2, $task_item_status_bar_more_tpl);
		
	}
	
	
	$edit_date = '';
	//Если было редактирование записи
	if($task_data['task_date_edit'] != '0000-00-00 00:00:00')
	{
		$PARS_3['{DATE}'] =  datetime($task_data['task_date_edit'], '%j %M в %H:%i');
		 
		$edit_date = fetch_tpl($PARS_3, $task_date_edit_tpl);
	}
	
	$task_report_block = '';
	
	// Не выводит ьформу добавления отчета
	$task_report_add_form = 0;
	

	// Если статус задания выполняется, принял или не принято - не выводим форму добавления комментария к отчету
	if($task_data['task_from_user'] == $current_user_id || $task_data['task_to_user'] == $current_user_id)
	{
		$task_report_add_form = 1;
	}
	
	// Список отчетов
	$task_report_block = fill_task_report_block1($task_data, 1, 1);
	
	// Оценка качестов проделанной работы
	$task_quality = '';
	
	// Если задание уже выполнено, выводим блок начальнику, для оценки качества выполненной работы
	if($task_data['task_status']==3)
	{
		// Если просматривает автор задания
		if($task_admin)
		{
			// Блок оценки качества проделанной работы
			$task_quality = get_task_quality_block($task_data['task_id']);
				
			// Если выполнение работы было подтверждено
			if($task_data['task_finished_confirm']==1)
			{
				$task_quality_display = '';
			}
			else 
			{
				$task_quality_display = 'display:none';
				
				$PARS_1['{TASK_ID}'] = $task_data['task_id'];
				
				// Блок оценки качества проделанной работы
				$task_confirm_finished = fetch_tpl($PARS_1, $task_confirm_finished_tpl);
			}
		}
		else
		{
			// Если задание выполнено, выводим блок оценки задания
			if($task_data['task_finished_confirm']==1)
			{  
				$PARS1['{TASK_QUALITY}'] = $task_data['task_quality'] ? $task_data['task_quality'] : $no_tasks_quality_result_tpl;
				
				$task_quality = fetch_tpl($PARS1, $tasks_quality_result_tpl);
				
			}
		}
	}
	
	// Кнопка "С заданием несправился"
	if($task_data['task_status']!=5 && $task_data['task_from_user']==$current_user_id && $task_data['task_finished_confirm']!=1)
	{
		$PARS_1['{TASK_ID}'] = $task_data['task_id'];
		$task_finished_fail = fetch_tpl($PARS_1, $task_finished_fail_tpl);
	}
	
	// Если есть кнопки "С заданием не справился" "Принять работу" или "С работой не справился.."
	if($task_confirm_finished || $task_finished_fail)
	{
		$PARS_2['{TASK_ID}'] = $task_data['task_id'];
		
		$PARS_2['{TASK_CONFIRM_FINISHED}'] = $task_confirm_finished;
		
		$PARS_2['{TASK_FINISHED_FAIL}'] = $task_finished_fail;
		
		$task_admin_statuses_btn_block = fetch_tpl($PARS_2, $admin_task_status_btn_block_tpl);
	}
	
	// Тема задания
	if($task_data['task_theme'])
	{
		$PARS_1['{TASK_THEME}'] = $task_data['task_theme'];
		
		$task_theme = fetch_tpl($PARS_1, $task_theme_tpl);
	}
	// Если указана дата крайнего срока
	if(!preg_match('/0000/', $task_data['task_max_date']))
	{
		$PARS_1['{MAX_DATE}'] = datetime($task_data['task_max_date'], '%d.%m.%y');
		$task_max_date_block = fetch_tpl($PARS_1, $task_max_date_block_tpl);
	}
	
	// Если указана дата крайнего срока
	if(!preg_match('/0000/', $task_data['task_desired_date']))
	{
		$PARS_1['{DESIRED_DATE}'] = formate_date($task_data['task_desired_date'],1);
		$task_desired_date_block = fetch_tpl($PARS_1, $task_desired_date_block_tpl);
	}
	
	// Начальнику выводим блок уведомления по смс
	if($task_data['task_from_user']==$current_user_id)
	{
		// Уведомить по смс начальника о новых отчетах по смс
		$sms_notice_checked = $task_data['task_boss_sms_notice'] ? 'checked="checked"' : '';
	
		$PARS_2['{TASK_ID}'] = $task_data['task_id'];
		$PARS_2['{SMS_NOTICE_CHECKED}'] = $sms_notice_checked;
		$new_reports_sms_notice = fetch_tpl($PARS_2, $new_tasks_reports_sms_notice_tpl);
		 
	}
	
	// Сложность задачи
	$task_difficulty =  get_difficulty_name_by_difficulty_id($task_data['task_difficulty']);
		
	// Название приоритета
	$task_priority = get_priority_name_by_priority_id($task_data['task_priority']);	
	 
	// Превью текст задания
	$task_preview = fill_task_preview_text($task_data);
	
	// кол-во новых отчетов\комментариев для задания
	$new_reports_count_block = fill_new_reports_count_block($task_data);
	
	// Определяет цвет блока задания
	$task_back_class = get_task_back_class($task_data);
	
	// Блок начальника, которого замещают
	$deputy_boss_block = fill_task_deputy_boss_block($task_data['task_from_user'], $task_data['task_to_user']);
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($task_data['task_from_user']);
	 
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($task_data['task_from_user'], $user_obj->get_user_image());
	
	// Уведомить по смс начальника о новых событиях над задачей
	$task_actions_to_notice_boss_by_sms = $task_data['task_boss_sms_notice'] ? 'Да' : 'Нет'; 
	
	// Блок связи со сделками
	$task_link_content_block = fill_task_link_content_block($task_data['task_id']);
	 
	// Список файлов для отчета
	$files_list = get_attached_files_to_content($task_data['task_id'], 6);
	
	$PARS['{FILES_LIST}'] = $files_list;
	 
	$PARS['{USER_ID}'] = $task_data['task_from_user'];
		
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;
	
	$PARS['{DEPUTY_BOSS_BLOCK}'] = $deputy_boss_block;
	
	$PARS['{TASK_EDIT_TOOLS}'] = $task_edit_tools;
	
	$PARS['{TASK_ID}'] = $task_data['task_id'];
		
	$PARS['{TASK_DATE}'] = formate_date_rus($task_data['task_date'], 1);
	
	$PARS['{TASK_DATE_ADD}'] = datetime($task_data['task_date_add'], '%j %M в %H:%i');
			
	$PARS['{TASK_PREVIEW_TEXT}'] = $task_preview;
	
	$PARS['{TASK_THEME}'] = $task_theme;
	
	$PARS['{TASK_TEXT}'] = stripslashes(nl2br($task_data['task_text']));
			
	$PARS['{TASK_STATUS}'] = $status_bar;
	
	$PARS['{TASK_BACK_CLASS}'] = $task_back_class;
	
	$PARS['{TASK_EXTEND_STATUS_WARNING}'] = $task_extend_status_warning;
	
	$PARS['{TASK_EXTEND_STATUS_FAIL}'] = $task_extend_status_fail;
	
	$PARS['{TASK_EXTEND_STATUS_INFO}'] = $task_extend_status_info;
	
	$PARS['{TASK_EXPIRED_CLASS}'] = $task_expired_class;
	
	$PARS['{EDIT_DATE}'] = $edit_date;
	
	$PARS['{TASK_MAX_DATE}'] = $task_max_date_block;
	
	$PARS['{TASK_DESIRED_DATE}'] = $task_desired_date_block;
	
	$PARS['{TASK_QUALITY_DISPLAY}'] = $task_quality_display;
	
	$PARS['{TASK_CONFIRM_FINISHED}'] = $task_confirm_finished;
	
	$PARS['{TASK_FINISHED_FAIL}'] = $task_finished_fail;
	
	$PARS['{TASK_ADMIN_STATUSES_BTN_BLOCK}'] = $task_admin_statuses_btn_block;
	
	$PARS['{TASK_DIFFICULTY}'] = $task_difficulty;
	
	$PARS['{TASK_PRIORITY}'] = $task_priority;
	
	$PARS['{TASK_QUALITY}'] = $task_quality;
	
	$PARS['{NEW_REPORTS_SMS_NOTICE}'] = $new_reports_sms_notice;
	
	$PARS['{TASK_REPORT_BLOCK}'] = $task_report_block;
	
	$PARS['{NEW_REPORTS_COUNT_BLOCK}'] = $new_reports_count_block;
	
	$PARS['{TASK_LINK_CONTENT}'] = $task_link_content_block;
	
	if($for_personal)
	{
		return  fetch_tpl($PARS, $tasks_list_item_on_personal_tpl);
	}
	// Если задание было выставлено самому себе
	else if($task_data['task_from_user']!=$task_data['task_to_user'])
	{
		return  fetch_tpl($PARS, $tasks_list_item_tpl);
	}
	else
	{
		return  fetch_tpl($PARS, $tasks_myself_list_item_tpl);
	}
}

// Блок связи со сторонним контентом
function fill_task_link_content_block($task_id)
{
	global $site_db, $user_obj, $_CURRENT_USER_ALL_BOSS_ARR;
	
	$task_link_deal_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_link_deal.tpl');
	
	// Выбор связи со сделкой
	$sql = "SELECT * FROM ".TASKS_DEALS_LINKS_TB." WHERE task_id='$task_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['link_id'] && $row['deal_id'] > 0)
	{
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deals.php';
		$deal_name = get_deal_name_by_deal_id($row['deal_id']);
		$PARS['{DEAL_ID}'] = $row['deal_id'];
		$PARS['{DEAL_NAME}'] = $deal_name;
		$cont_link = fetch_tpl($PARS, $task_link_deal_tpl);
	}
	
	return $cont_link;
}
// Блок начальника, которого замещают
function fill_task_deputy_boss_block($task_from_user, $task_to_user)
{
	global $site_db, $user_obj, $_CURRENT_USER_ALL_BOSS_ARR;
	
	$deputy_boss_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/deputy_boss_block.tpl');
	
	// Проверяем на заместителя
	$sql = "SELECT invite_user, deputy_id FROM ".WORKERS_TB."
			WHERE invite_user='$task_from_user' AND invited_user='$task_to_user'  AND deleted<>1 
			AND ((invited_user_status=1 AND deputy_id = 0) OR deputy_id>0) ORDER by deputy_id ";
				
	$deputy_row = $site_db->query_firstrow($sql);		 
	
	//echo "<pre>",print_r($_CURRENT_USER_ALL_BOSS_ARR);
	if($deputy_row['invite_user'] && $deputy_row['deputy_id'] > 0)
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($deputy_row['invite_user']);
	
		$PARS['{USER_ID}'] = $task_from_user;
			
		$PARS['{NAME}'] = $user_obj->get_user_name();
		
		$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
			
		return fetch_tpl($PARS, $deputy_boss_block_tpl);
	}
	else return '';
}

// Подсвечиваем бэкграунд блока задания исходя из его статуса
function get_task_back_class($task_data)
{
	$task_status = $task_data['task_status'];
	
	// Данные о просроченных датах задания
	$task_expired_arr = task_expired_arr($task_data);
	
	### Подсвечиваем задания исходя из его статуса
	
	switch($task_status)
	{
		case 0:
			$back_class = 'not_confirm';
		break;
		case 3:
			$back_class = 'cont_completed';
		break;
		case 4:
		case 5:
			$back_class = 'cont_fail';
		break;
		
	}
	
	if(!$back_class && ($task_expired_arr['task_max_date_expired']==1 || $task_expired_arr['task_desired_date_expired']==1))
	{
		$back_class = 'cont_fail';
	}
	
	return $back_class;
}

// Возвращает превью текст задания
function fill_task_preview_text($task_data)
{
	// Если есть название темы к заданию
	if($task_data['task_theme'])
	{
		$preview_text = $task_data['task_theme'];
	}
	else
	{
		// Выбираем часть строки из текста задачи
		if(strlen($task_data['task_text'])>80)
		{
			$preview_text = substr($task_data['task_text'],0,80).'...';
		}
		else
		{
			$preview_text = $task_data['task_text'];
		}
	}
	
	return strip_tags($preview_text);
}
// Блок оценки качества
function get_task_quality_block($task_id)
{
	global $site_db;
	
	$quality_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_quality.tpl');
	
	$quality_list_arr = array(1,2,3,4,5);
	
	// Выбираем значение из базы
	$sql = "SELECT task_quality FROM ".TASKS_TB." WHERE task_id='$task_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	foreach($quality_list_arr as $quality)
	{
		$selected = $quality == $row['task_quality'] ? 'selected="selected"' : '';
		
		$PARS1['{NAME}'] = $quality;
		
		$PARS1['{VALUE}'] = $quality;
		
		$PARS1['{SELECTED}'] = $selected;
		
		$quality_list .= fetch_tpl($PARS1, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl'));
	}
	
	$PARS['{QUALITY_LIST}'] = $quality_list;
	
	$PARS['{TASK_ID}'] = $task_id;
	
	$quality_block = fetch_tpl($PARS, $quality_tpl);
	
	return  $quality_block;
}

// Возврашает блок для исполнителя управление статусом задачи
function fill_task_status($task_id, $status, $task_date_add, $task_confirm_date, $task_in_proc_date, $task_finished_date)
{
	global $site_db;
	
	$task_item_actual_status_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_item_actual_status.tpl');
	
	// Если еще не прочитано
	if($status==0)
	{
		$formate_date = date_passing($task_date_add);
		 
		$status_bar = 'не принято '.$formate_date;
	}
	// Прочитано
	else if($status==1)
	{
		$formate_date = date_passing($task_confirm_date);
		
		$status_bar = "принято ".$formate_date;
	}
	// Выполняется
	else if($status==2)
	{
		$formate_date = date_passing($task_in_proc_date);
		
		$status_bar = "Выполняется ".$formate_date;
	}
	// Выполнено
	else if($status==3)
	{
		
		$status_bar = "выполнено ".formate_date_rus($task_finished_date);
		
	}
	// Не может быть выполнено
	else if($status==4)
	{
		$status_bar = "не может быть выполнено";
	}
	else if($status==5)
	{
		$status_bar = "с заданием не справился";
	}
	
	$PARS['{STATUS}'] = $status_bar;
	
	return fetch_tpl($PARS, $task_item_actual_status_tpl);
}

// Страница - Мои задачи
function fill_my_tasks($user_id)
{
	global $site_db;
	
	$tasks_tpl = file_get_contents('templates/tasks/tasks.tpl');
	
	$calendar_block_tpl = file_get_contents('templates/calendar/calendar_block.tpl');
	
	$add_new_task_form_tpl = file_get_contents('templates/tasks/add_new_my_task_form.tpl');
	
	$no_tasks_tpl = file_get_contents('templates/tasks/no_tasks.tpl');
	
	// Для выбранной даты не выводим блок добавления задания
	if(!isset($_GET['date']))
	{
		$PARS_2['{TO_USER_ID}'] = $user_id;
		
		$add_new_task_form = fetch_tpl($PARS_2, $add_new_task_form_tpl);
	}
	
	// Формирование блока календаря
	$calendar_block = fill_calendar_block($user_id, 'my_tasks');
	
	if($_GET['date'])
	{
		$date = $_GET['date'];
	}
	 
	// Список задач
	$tasks_list = fill_tasks_my_list($user_id, $date);
	
	if(!$tasks_list)
	{
		$tasks_list = $no_tasks_tpl;
	}
	
	// Панель поиска
	$tasks_search_form = fill_tasks_search_form($user_id);
	
	$PARS['{TASKS_LIST}'] = $tasks_list;
	
	$PARS['{ADD_TASK_FORM}'] = $add_new_task_form;  
	
	$PARS['{CALENDAR_BLOCK}'] = $calendar_block;
	
	$PARS['{TASK_SEARCH}'] = $tasks_search_form;
	
	$PARS['{DATE}'] = $date;
	
	return fetch_tpl($PARS, $tasks_tpl);
}

// Форма поиска заданий
function fill_tasks_search_form($user_id, $is_tasks_to_users = '')
{
	global $site_db, $current_user_id;
	
	$task_search_form_tpl = file_get_contents('templates/tasks/task_search_form.tpl');
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{TASKS_TO_USERS}'] = $is_tasks_to_users;
	
	return fetch_tpl($PARS, $task_search_form_tpl);
}

// Формирование списка задач для сотрудника
function fill_tasks_my_list($user_id, $date, $search_word='')
{
	global $site_db, $current_user_id;
	
	$task_group_by_date_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_group_by_date.tpl');
	
	if($date)
	{
		$and_date = " AND task_date='$date' ";
	}
	 
	$actual_date = date('Y-m-d');
	
	$date = value_proc($date);
	
	// Поиск по ключевым словам
	if($search_word)
	{
		// Задачи пользователя
		$sql = "SELECT *, if(task_status=3,1,0) as order_by_task_status FROM ".TASKS_TB." 
				WHERE task_to_user='$user_id' AND task_deleted<> 1
				AND (task_theme LIKE '%$search_word%' OR  task_text LIKE '%$search_word%')
				ORDER by order_by_task_status, task_priority DESC, task_desired_date ASC, task_id DESC";		
	}
	else if($date)
	{ 
		// Выбор задач
		$sql = "SELECT *, if(task_status=3,1,0) as order_by_task_status FROM ".TASKS_TB." 
				WHERE task_to_user='$user_id' AND task_deleted<> 1
					  AND (
					  		(task_finished_confirm = 1 AND task_finished_date LIKE '$date%')
							OR
							(task_finished_confirm = 0 AND task_status=5 AND task_finished_fail_date LIKE '$date%')
							OR
							(task_finished_confirm = 0 AND task_date = '$date')
						   ) $and_search_words
				ORDER by order_by_task_status, task_priority DESC, task_desired_date ASC, task_id DESC";
	}
	else
	{
		// Задачи пользователя
		$sql = "SELECT *, if(task_status=3,1,0) as order_by_task_status FROM ".TASKS_TB." 
				WHERE task_to_user='$user_id' AND task_deleted<> 1
				AND (
						(task_finished_confirm = 0 AND task_status<>5) 
						OR 
						(task_finished_confirm=0 AND task_status=5 AND task_finished_fail_date>='$actual_date') 
						OR 
						(task_finished_confirm=1 AND task_finished_date>='$actual_date')
						OR task_has_new_reports_for_to_user=1
					) $and_search_words
				ORDER by order_by_task_status, task_priority DESC, task_desired_date ASC, task_id DESC";		
	 
	}
		
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{
		$tasks_data[] = $row;	 
	}
	
	foreach($tasks_data as $task_data)
	{
		$tasks_list_arr[$task_data['task_date']] .= fill_tasks_my_list_item($task_data);
	}
	
	krsort($tasks_list_arr);
	 
	foreach($tasks_list_arr as $date => $list)
	{
		
		$PARS['{LIST}'] = $list;
		
		$PARS['{DATE}'] =$date;
		
		$PARS['{DATE_RUS}'] = datetime($date, '%j %F');
		
		$tasks_list .= fetch_tpl($PARS, $task_group_by_date_tpl);
	}
	 
	return $tasks_list;
}

// Формирование списка задач для сотрудника
function fill_tasks_my_list_item($task_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$task_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_edit_tools.tpl');
	
	$tasks_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_my_list_item.tpl');
	
	$tasks_list_item_myself_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_my_list_myself_item.tpl');
	
	$tasks_quality_result_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_quality_result.tpl');
	
	$task_max_date_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_max_date.tpl');
	
	$task_desired_date_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_desired_date.tpl');
	
	$task_theme_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_theme.tpl');
	
	$task_date_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_date_eidt.tpl');
	
	$task_item_status_bar_more_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_item_status_bar_more.tpl');
	
	$a_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/a.tpl');
	
	// Создатель задания
	$task_admin = 0;
	// Задание самому себе
	$task_myself = 0;
	
	// Автор задания
	if($task_data['task_from_user'] == $current_user_id)
	{
		$task_admin = 1;
	}
	// Автору задачи выводим блоки управления заданием
	if($task_admin)
	{
		$PARS_1['{TASK_ID}'] = $task_data['task_id'];
		
		$task_edit_tools = fetch_tpl($PARS_1, $task_edit_tools_tpl);
	}
	
	// Задание самому себе
	if($task_data['task_from_user'] == $current_user_id && $task_data['task_to_user'] == $current_user_id)
	{
		$task_myself = 1;
	}
	
	
	$task_max_date_block = '';
	$task_desired_date_block = '';
	$edit_date = '';
	$task_report_block = '';
	

	// Если указана дата крайнего срока
	if(!preg_match('/0000/', $task_data['task_max_date']))
	{
		$PARS_1['{MAX_DATE}'] = formate_date($task_data['task_max_date'],1);
		$task_max_date_block = fetch_tpl($PARS_1, $task_max_date_block_tpl);
	}
			
	// Если указана дата крайнего срока
	if(!preg_match('/0000/', $task_data['task_desired_date']))
	{
		$PARS_1['{DESIRED_DATE}'] = formate_date($task_data['task_desired_date'],1);
		$task_desired_date_block = fetch_tpl($PARS_1, $task_desired_date_block_tpl);
	}
		
	// Название приоритета
	$task_priority = get_priority_name_by_priority_id($task_data['task_priority']);
		
	 // Сложность задачи
	$task_difficulty =  get_difficulty_name_by_difficulty_id($task_data['task_difficulty']);
		
	// Данные о просроченных датах задания
	$task_expired_arr = task_expired_arr($task_data);
		
	// Если задание, которое не сам себе выставлял
	if(!$task_myself)
	{
		$task_status_bar = fill_task_status_bar1($task_data, 0);
	}
	else
	{
		$task_status_bar = fill_task_status_bar1($task_data, 1);
	}
	
	// Расширенный блок статуса о выполнении задания
	$task_extend_status_arr = fill_task_extend_status_block($task_data);
	
	// Статус панель задания
	$status_bar = fill_task_status($task_data['task_id'], $task_data['task_status'], $task_data['task_date_add'], $task_data['task_confirm_date'], $task_data['task_in_proc_date'], $task_data['task_finished_date']);
	
	$task_extend_status_warning = $task_extend_status_arr['desired_left'].$task_extend_status_arr['max_left'];
	$task_extend_status_fail = $task_extend_status_arr['desired_over'].$task_extend_status_arr['max_over'];
	$task_extend_status_info = $task_extend_status_arr['confirm'].$task_extend_status_arr['proc'].$task_extend_status_arr['complete'];
	
	// Если есть данные в расширенном статусе задания, делаем кнопкой статус задания, чтобы можно было раскрыть список
	if($task_extend_status_warning || $task_extend_status_fail || $task_extend_status_info)
	{
		$PARS_2['{TASK_ID}'] = $task_data['task_id'];
		
		$status_bar .= fetch_tpl($PARS_2, $task_item_status_bar_more_tpl);
	}
	
	######################
	
	
	### Подсвечиваем задания исходя из его статуса
	if($task_expired_arr['task_max_date_expired'] || $task_expired_arr['task_desired_date_expired'] || $task_data['task_status']==5)
	{
		$task_expired_class = 'task_fail_class';
	}
	
	
		
	//Если было редактирование записи
	if($task_data['task_date_edit'] != '0000-00-00 00:00:00')
	{
		$PARS_3['{DATE}'] = datetime($task_data['task_date_edit'], '%j %M в %H:%i');
		
		$edit_date = fetch_tpl($PARS_3, $task_date_edit_tpl);
	}
		
	// Тема задания
	if($task_data['task_theme'])
	{
		$PARS_1['{TASK_THEME}'] = $task_data['task_theme'];
			
		$task_theme = fetch_tpl($PARS_1, $task_theme_tpl);
	}
	
	// кол-во новых отчетов\комментариев для задания
	$new_reports_count_block = fill_new_reports_count_block($task_data);
	
	// Определяет цвет блока задания
	$task_back_class = get_task_back_class($task_data);
	
	// Превью текст задания
	$task_preview = fill_task_preview_text($task_data);
	
	// Кол-во отчетов для задания
	$task_report_count = get_count_task_reports($task_data['task_id']);
	
	// Блок начальника, которого замещают
	$deputy_boss_block = fill_task_deputy_boss_block($task_data['task_from_user'], $task_data['task_to_user']);
	
	// Блок копирования
	$task_copy_block = fill_task_to_copy_block($task_data);
		
	// Заполянем объект пользователя
	$user_obj->fill_user_data($task_data['task_from_user']);
	$task_quality = '';
		
	// Если задание выполнено, выводим блок оценки задания
	if($task_data['task_status']==3 && $task_data['task_finished_confirm']==1)
	{  
		$PARS1['{TASK_QUALITY}'] = $task_data['task_quality'] ? $task_data['task_quality'] : 'Не выставлена';
			
		$task_quality = fetch_tpl($PARS1, $tasks_quality_result_tpl);
			
	}
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($task_data['task_from_user'], $user_obj->get_user_image());
	
	$task_link_content_block = fill_task_link_content_block($task_data['task_id']);
	
	// Список файлов для отчета
	$files_list = get_attached_files_to_content($task_data['task_id'], 6);
	
	$PARS['{FILES_LIST}'] = $files_list;
	
	$PARS['{TASK_LINK_CONTENT}'] = $task_link_content_block;
	
	$PARS['{TASK_COPY_BLOCK}'] = $task_copy_block;
	
	$PARS['{USER_ID}'] = $task_data['task_from_user'];
	
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{DEPUTY_BOSS_BLOCK}'] = $deputy_boss_block;
	
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;
	
	$PARS['{TASK_ID}'] = $task_data['task_id'];
		
	$PARS['{TASK_DATE}'] = formate_date_rus($task_data['task_date'], 1);
		
	$PARS['{TASK_DATE_ADD}'] = formate_date($task_data['task_date_add']);
		
	$PARS['{TASK_EXPIRED_CLASS}'] = $task_expired_class;
	
	$PARS['{TASK_TEXT}'] = stripslashes(nl2br($task_data['task_text']));
		
	$PARS['{TASK_PREVIEW_TEXT}'] = $task_preview;
		
	$PARS['{TASK_THEME}'] = $task_theme;
		
	$PARS['{TASK_PRIORITY}'] = $task_priority;
		
	$PARS['{TASK_DIFFICULTY}'] = $task_difficulty;
	
	$PARS['{TASK_QUALITY}'] = $task_quality;
	
	$PARS['{TASK_STATUS}'] = $status_bar;
	
	$PARS['{TASK_BACK_CLASS}'] = $task_back_class;
	
	$PARS['{TASK_EXTEND_STATUS_WARNING}'] = $task_extend_status_warning;
	
	$PARS['{TASK_EXTEND_STATUS_FAIL}'] = $task_extend_status_fail;
	
	$PARS['{TASK_EXTEND_STATUS_INFO}'] = $task_extend_status_info;
	
	$PARS['{TASK_STATUS_BAR}'] = $task_status_bar;
		
	$PARS['{TASK_MAX_DATE}'] = $task_max_date_block;
	
	$PARS['{TASK_DESIRED_DATE}'] = $task_desired_date_block;
	
	$PARS['{EDIT_DATE}'] = $edit_date;
	
	$PARS['{TASK_EDIT_TOOLS}'] = $task_edit_tools;
	
	$PARS['{NEW_REPORTS_COUNT_BLOCK}'] = $new_reports_count_block;
		
	$task_report_display = 'none';
	$task_report_add_form = 0;
		
	// Если есть какие-то отчеты или статус задания - выполняю или выполнено 
	if($task_report_count || $task_data['task_status']==2 || $task_data['task_status']==3 || $task_data['task_status']==5)
	{ 
		$task_report_display = 'block';
	}
		 
	// Если статус задания принял и выполняется
	if($task_data['task_to_user']==$current_user_id && $task_data['task_finished_confirm']!=1)
	{  
		$task_report_add_form = 1;
	}
		
		
	// Список отчетов о задании
	$task_report_block = fill_task_report_block1($task_data, $task_data, 0);
		
		
	$PARS['{TASK_REPORT_BLOCK}'] = $task_report_block;
	
	$PARS['{TASK_REPORT_DISPLAY}'] = $task_report_display;	
		
	if(!$task_myself)
	{
		return fetch_tpl($PARS, $tasks_list_item_tpl);
	}
	else
	{
		return fetch_tpl($PARS, $tasks_list_item_myself_tpl);
	}
	 
}

// Доступ для клиентов
function fill_task_to_copy_block($task_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$task_id = $task_data['task_id'];
	
	$users_copy_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/users_copy_block.tpl');
	$users_copy_user_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/users_copy_user_item.tpl');
	
	
	 
	// Документ можно передать и начальнику и подчиненному
	$users_for_access_arr = get_current_user_users_arrs(array(0,1,0,0,1));
 
	foreach($users_for_access_arr as $user)
	{ 
	 	if($client_data['user_id']==$user)
		{
			continue;
		}
		
		$access_active = '';
		
		$user_obj->fill_user_data($user);
		
		$user_name = $user_obj->get_user_name();
		
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_surname = $user_obj->get_user_surname();
		
		$user_position = $user_obj->get_user_position();

		$PARS1['{TASK_ID}'] = $task_id;
		
		$PARS1['{USER_ID}'] = $user;
		
		$PARS1['{SURNAME}'] = $user_surname;
		
		$PARS1['{NAME}'] = $user_name;
				
		$PARS1['{MIDDLENAME}'] = $user_middlename;
				
		$PARS1['{USER_POSITION}'] = $user_position;
		  
		$users_access_list .= fetch_tpl($PARS1, $users_copy_user_item_tpl);
	}
	
	if(!$users_access_list)
	{
		return '';
	}

	
	$PARS['{TASK_ID}'] = $task_id;
	
	$PARS['{USERS_LIST}'] = $users_access_list;
	
	return  fetch_tpl($PARS, $users_copy_block_tpl);
}

// Блок новых отчетов\комментарией для задания
function fill_new_reports_count_block($task_data)
{
	global $current_user_id;
	
	$new_task_reports_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/new_task_reports_block.tpl');
	
	if($task_data['task_to_user'] == $current_user_id)
	{  
		// Кол-во новых отчетов для задания
		$new_reports_count = get_new_task_reports_count_for_user($task_data['task_id']);
		
	}
	// Показываем новые отчеты для автора задания
	else if($task_data['task_from_user'] == $current_user_id)
	{
		// Кол-во новых отчетов для задания
		$new_reports_count = get_new_task_reports_count_for_user($task_data['task_id'], 1);
	}
	else
	{
		return '';
	}
	
	// Если есть новые отчеты для задания, выводим их
	if($new_reports_count)
	{
		$PARS_2['{TASK_ID}'] = $task_data['task_id'];
			
		$PARS_2['{NEW_REPORTS_COUNT}'] = $new_reports_count;
			
		$new_reports_count_block = fetch_tpl($PARS_2, $new_task_reports_block_tpl);
		
		return $new_reports_count_block;
	}
}

// Возвращает кол-во отчетов для задания
function get_count_task_reports($task_id)
{
	global $site_db;
	
	$sql = "SELECT COUNT(*) as count FROM ".TASKS_REPORTS_TB." WHERE task_id='$task_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Возвращает список отчетов для задачи
function fill_task_report_block1($task_data, $add_form=0, $is_boss)
{
	global $site_db, $current_user_id;
	
	$task_id = $task_data['task_id'];
	
	$tasks_list_item_report_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_list_item_report_block.tpl');
	
	$tasks_list_item_report_block_for_boss_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_list_item_report_block_for_boss.tpl');
	
	// Список отчетов
	$task_report_list = fill_task_reports_list1($task_id, $is_boss);
	
	// Форма добавления нового отчета
	if(($task_data['task_to_user']==$current_user_id || $task_data['task_from_user'] == $current_user_id))
	{
		$display_add_task_report_form = 'block';
	}
	else
	{
		$display_add_task_report_form = 'none';
	}
	
	if($task_data['task_from_user']==$task_data['task_to_user'])
	{
		$display_by_sms = 'display:none';
	}
	
	//$display_add_task_report_form = 'block';
	$add_task_report_btn_value = $is_boss ? 'добавить комментарий к отчету' : 'добавить отчет';
	
	$PARS['{ADD_REPORT_TASK_BTN_VALUE}'] = $add_task_report_btn_value;
	
	$PARS['{DISPLAY_ADD_TASK_REPORT_FORM}'] = $display_add_task_report_form;
	
	$PARS['{TASK_ID}'] = $task_id;
	
	$PARS['{TASK_REPORT_LIST}'] = $task_report_list;
	
	$PARS['{ADD_REPORT_FORM}'] = $tasks_report_add_form;
	
	$PARS['{BY_SMS_DESPLAY}'] = $display_by_sms;
	
	$task_report_block = fetch_tpl($PARS, $tasks_list_item_report_block_tpl);
	
	return $task_report_block;
}

// Список отчетов для задания
function fill_task_reports_list1($task_id, $is_boss)
{
	global $site_db,  $user_obj, $current_user_id;
	
	$tasks_report_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_report_item.tpl');
	
	$report_confirm_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/report_confirm_btn.tpl');
	
	// данные задания
	$sql = "SELECT * FROM ".TASKS_TB." WHERE task_id='$task_id'";
	
	$task_data = $site_db->query_firstrow($sql);
	 
	 
	
	// Выбор отчетов для задания
	$sql = "SELECT * FROM ".TASKS_REPORTS_TB." WHERE task_id='$task_id' ORDER by report_date ASC";
	
	$res = $site_db->query($sql);
	
	
	while($report_data=$site_db->fetch_array($res, 1))
	{ 
		$report_not_confirm = '';
			
		$confirm_btn = '';
			 
		// Отчет не подтвержден
		if(!$report_data['report_confirm'] && (($task_data['task_to_user']==$current_user_id && $report_data['report_user_id']==$task_data['task_from_user']) || ($task_data['task_from_user']==$current_user_id && $report_data['report_user_id']==$task_data['task_to_user'])) && $task_data['task_to_user'] != $task_data['task_from_user'])
		{
			// Подписываем кнопку принять отчет
			if($task_data['task_from_user']==$current_user_id)
			{
				$confirm_btn_str = 'принять отчет';
			}
			else
			{
				$confirm_btn_str = 'ознакомлен';
			}
			$report_not_confirm = 'not_confirm';
							
			$PARS_1['{REPORT_ID}'] = $report_data['report_id'];
				
			$PARS_1['{TASK_ID}'] = $report_data['task_id'];
			
			$PARS_1['{REPORT_CONFIRM_STR}'] = $confirm_btn_str;
				
			$confirm_btn = fetch_tpl($PARS_1, $report_confirm_btn_tpl);
		}
			
		$user_obj->fill_user_data($report_data['report_user_id']);
		
		// Превью аватарки пользователя
		$user_avatar_src = get_user_preview_avatar_src($report_data['report_user_id'], $user_obj->get_user_image());
	
		// Список файлов для отчета
		$files_list = get_attached_files_to_content($report_data['report_id'], 7);
	
		$PARS['{FILES_LIST}'] = $files_list;
		
		$PARS['{USER_ID}'] = $report_data['report_user_id'];
		
		$PARS['{NAME}'] = $user_obj->get_user_name();
		
		$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{AVATAR_SRC}'] = $user_avatar_src;
		
		$PARS['{TASK_DATE}'] = datetime($report_data['report_date'], '%j %M в %H:%i');
		
		
		$PARS['{TASK_TEXT}'] = stripslashes(nl2br($report_data['report_text']));
		
		$PARS['{REPORT_ID}'] = $report_data['report_id'];
		
		$PARS['{TASK_ID}'] = $report_data['task_id'];
		
		$PARS['{CONFIRM_BTN}'] = $confirm_btn;
		
		$PARS['{REPORT_NOT_CONFIRM_CLASS}'] = $report_not_confirm;
		
		$task_report_list .= fetch_tpl($PARS, $tasks_report_item_tpl);
	}
	
	if(!$task_report_list)
	{
		$task_report_list = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_report_no_reports.tpl');;
	}
	
	return $task_report_list;
}
// Возврашает блок для исполнителя управление статусом задачи
function fill_task_status_bar1($task_data, $task_myself=0)
{
	global $site_db;
	
	## Кнопки
	
	// Принял
	$task_read_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_read_status_btn.tpl');
	// Выполняется
	$task_process_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_process_status_btn.tpl');
	// Не выполняется
	$task_not_process_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_not_process_status_btn.tpl');
	// Не могу выполнить
	$task_cant_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_cant_status_btn.tpl');
	// Выполнено
	$task_complete_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_complete_status_btn.tpl');
	// Не выполнено
	$task_not_complete_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_not_complete_status_btn.tpl');

	
	// Если еще не прочитано
	if($task_data['task_status']==0)
	{
		$status_btn[] = $task_read_status_btn_tpl;
	}
	// Прочитано
	else if($task_data['task_status']==1)
	{
		$status_btn[] = $task_process_status_btn_tpl.$task_cant_status_btn_tpl;
	}
	// Выполняется
	else if($task_data['task_status']==2)
	{
		// Задание самому себе
		if($task_myself)
		{
			$status_btn[] = $task_complete_status_btn_tpl;
		}
		else
		{
			$status_btn[] = $task_not_process_status_btn_tpl.$task_complete_status_btn_tpl.$task_cant_status_btn_tpl;
		}
	}
	// Выполнено
	else if($task_data['task_status']==3)
	{
		if($task_data['task_finished_confirm']!=1 || $task_myself)
		{
			 $status_btn[] = $task_not_complete_status_btn_tpl;
		}
	}
	// Не может быть выполнено
	else if($task_data['task_status']==4)
	{
		$status_btn[] = $task_process_status_btn_tpl;
	}
	// Не может быть выполнено
	else if($task_data['task_status']==5)
	{
		$status_bar = $task_finished_fail_status_tpl;
	}
	
	$btns = implode(' ', $status_btn);
	
	$PARS['{TASK_ID}'] = $task_data['task_id'];
	
	return fetch_tpl($PARS, $btns);
}
// Форму редактирования отдельной задачи
function fill_task_edit_form($task_id)
{
	global $site_db, $current_user_id;
	
	$task_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_list_item_edit.tpl');
	
	$task_edit_myself_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_myself_list_item_edit.tpl');
	
	// Выбор задачи
	$sql = "SELECT * FROM ".TASKS_TB." WHERE task_id='$task_id'";
		 
	$task_data = $site_db->query_firstrow($sql);
	
	$task_max_date_norm = !preg_match('/0000/', $task_data['task_max_date']) ? formate_date($task_data['task_max_date'],1) : '';
	
	// Проверяем привязку задачи к сделке
	$sql = "SELECT * FROM ".TASKS_DEALS_LINKS_TB." WHERE task_id='$task_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['link_id'] && $row['deal_id'])
	{
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deals.php';
		$deal_linked_selected = get_selected_easycomplete($row['deal_id'], get_deal_name_by_deal_id($row['deal_id']));
		 
	}
	
	// Список файлов для отчета
	$files_list = get_attached_files_to_content($task_data['task_id'], 6, 2);
	
	$PARS['{FILES_LIST}'] = $files_list;
	
	$PARS['{TASK_ID}'] = $task_data['task_id'];
		
	$PARS['{TASK_DATE}'] = formate_date_rus($task_data['task_date'], 1);
	
	$PARS['{TASK_MAX_DATE}'] = $task_max_date_norm;
	
	$PARS['{TASK_THEME}'] = $task_data['task_theme'];
			
	$PARS['{TASK_TEXT}'] = $task_data['task_text'];
	
	$PARS['{TASK_BOSS_SMS_NOTICE_CHECKED}'] = $task_data['task_boss_sms_notice'] == 1 ? 'checked="checked"' : '';
	
	$PARS['{DIFFICULTY_OPTION_LIST}'] = fill_difficulty_options($task_data['task_difficulty']);
	
	$PARS['{PRIORITY_OPTION_LIST}'] = fill_priority_options($task_data['task_priority']);
	
	$PARS['{DEAL_LINKED_SELECTED}'] = $deal_linked_selected;
	
	
	// Задача себе
	if(is_task_myself($task_data, $current_user_id))
	{
		$tasks_edit = fetch_tpl($PARS, $task_edit_myself_tpl);
	}
	else
	{
		$tasks_edit = fetch_tpl($PARS, $task_edit_tpl);
	}
	
	return $tasks_edit;
}




##########
### Вспомогательные методы
##########

// Кол-во новых задач для пользователя
function get_new_tasks_count($to_user_id)
{
	global $site_db;
	
	// Проверяем, есть ли новые задачи
	$sql = "SELECT COUNT(*) as count FROM  ".TASKS_TB." WHERE task_to_user='$to_user_id' AND task_status = 0 AND task_deleted<>1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}


// Возвращает количество всех задач для сотрудника от начальница
function get_count_tasks_for_user($from_user_id, $to_user_id)
{
	global $site_db;
	
	$actual_date = date('Y-m-d');
	
	// Задачи пользователя
	$sql = "SELECT COUNT(*) as count FROM ".TASKS_TB." 
			WHERE task_to_user='$to_user_id' AND task_from_user='$from_user_id' AND task_deleted<> 1
			AND (
					(task_finished_confirm = 0 AND task_status<>5) 
					OR 
					(task_finished_confirm=0 AND task_status=5 AND task_finished_fail_date>='$actual_date') 
					OR 
					(task_finished_confirm=1 AND task_finished_date>='$actual_date')
				)";	
				 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Возвращает количество активных задач для сотрудника
function get_count_tasks_in_process_for_user($user_id, $without_myself_task=1)
{
	global $site_db; 
	
	// Если брать в расчет задания, которые пользователь выставлял сам себе
	if(!$without_myself_task)
	{
		$and_without = "AND task_from_user!=task_to_user";
	}
	
	$actual_date = date('Y-m-d');
	
	// Выбор задач
	$sql = "SELECT COUNT(*) as count FROM ".TASKS_TB." 
			WHERE task_to_user='$user_id' AND task_deleted<>1 
			AND (task_finished_confirm=0 OR (task_finished_confirm=1 AND task_finished_date>='$actual_date')) 
			AND task_date>='$actual_date' $and_without";

	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Возвращает количество активных задач, которые пользователь дал
function get_count_tasks_in_process_from_user($user_id, $without_myself_task=1)
{
	global $site_db;
	
	// Если брать в расчет задания, которые пользователь выставлял сам себе
	if(!$without_myself_task)
	{
		$and_without = "AND task_from_user!=task_to_user";
	}
	
	$actual_date = date('Y-m-d');
	
	// Массив подчиненных
	$workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	
	if($workers_arr)
	{
		$workers_ids = implode(',', $workers_arr);
		
		// Выбор задач
		$sql = "SELECT COUNT(*) as count FROM ".TASKS_TB." 
				WHERE task_from_user='$user_id' AND task_deleted<>1 
				AND task_to_user IN($workers_ids)
				AND (task_finished_confirm=0 OR (task_finished_confirm=1 AND task_finished_date>='$actual_date')) 
				AND task_date>='$actual_date' $and_without";
	 
		$row = $site_db->query_firstrow($sql);
		
		return $row['count'];
	}
	else return 0;
	 
}

// Проверка, является ли сотрудник автором задания
function is_author_of_task($user_id, $task_id)
{
	global $site_db;
	
	$sql = "SELECT task_id FROM ".TASKS_TB." WHERE task_from_user='$user_id' AND task_id='$task_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['task_id'])
	{
		return true;
	}
	else
	{
		return false;
	}
	return $row['count'];
}

// Возвращает <options> приоритета выполнения задания
function fill_priority_options($priority)
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
function fill_difficulty_options($difficulty)
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

// Список выполненной работы сотрудника
function fill_user_work_completed_list($user_id, $limit_days, $order, $without_myself_task=1)
{
	global $site_db;
	
	$mk_time_days_from = mktime() - 3600 * 24 * $limit_days;
	
	$date_s = date('Y-m-d', $mk_time_days_from);
	
	// Если брать в расчет задания, которые пользователь выставлял сам себе
	if(!$without_myself_task)
	{
		$and_without = "AND task_from_user!=task_to_user";
	}
	// Сортировка
	if($order=='date_desc')
	{
		$order_by = ' ORDER by task_finished_date DESC ';
	}
	$sql = "SELECT * FROM ".TASKS_TB." WHERE task_to_user='$user_id' AND task_deleted<>1 
			AND task_finished_date>='$date_s' AND task_finished_confirm=1 $and_without $order_by";
	 
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{
		$tasks_arr[] = $row;
	}
	
	return $tasks_arr;
}

// Кол-во выполненных задач
function get_user_tasks_completed_count($user_id, $limit_days=0, $without_myself_task=1)
{
	global $site_db;
	
	if($limit_days)
	{
		$mk_time_days_from = mktime() - 3600 * 24 * $limit_days;
		
		$date_s = date('Y-m-d', $mk_time_days_from);
		
		$and_date = "AND task_finished_date>='$date_s'";
	
	}
	
	// Если брать в расчет задания, которые пользователь выставлял сам себе
	if(!$without_myself_task)
	{
		$and_without = "AND task_from_user!=task_to_user";
	}
	
	$sql = "SELECT COUNT(*) as count FROM ".TASKS_TB." WHERE task_to_user='$user_id' AND task_finished_confirm = 1 AND task_deleted<>1 $and_date $and_without";
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Средний балл оценок с начала даты
function get_user_tasks_average_quality($user_id, $limit_days=0 , $without_myself_task=1)
{
	global $site_db;
	
	$mk_time_days_from = mktime() - 3600 * 24 * $limit_days;
		
	$date_s = date('Y-m-d', $mk_time_days_from);
		
	$and_date = "AND task_finished_date>='$date_s'";
	
	
	// Если брать в расчет задания, которые пользователь выставлял сам себе
	if(!$without_myself_task)
	{
		$and_without = "AND task_from_user!=task_to_user";
	}
	
	// Выбираем среднее кол-во оценок
	$sql = "SELECT ROUND(AVG(task_quality),2) as avg_quality FROM ".TASKS_TB." WHERE task_to_user='$user_id' AND task_finished_confirm = 1 AND task_deleted<>1 
			AND task_finished_date>='$date_s' AND task_quality > 0 $and_without";
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['avg_quality'];
}

// Название приоритета по его id
function get_priority_name_by_priority_id($priority_id)
{
	global $site_db;
	
	$sql = "SELECT priority_name FROM ".PRIORITY_TB." WHERE priority_id='$priority_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['priority_name'];
}

// Название сложности задачи по ее id
function get_difficulty_name_by_difficulty_id($difficulty_id)
{
	global $site_db;
	
	$sql = "SELECT difficulty_name FROM ".DIFFICULTY_TB." WHERE difficulty_id='$difficulty_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['difficulty_name'];
}

// Заполняет расширенный блок статуса выполнения задания
function fill_task_extend_status_block($task_data)
{
	global $site_db;
	/* - Сколько времени ушло на принятие задания
 Сколько времени ушло на начало выполнения
 Сколько ушло от выполнения к завершению выполнения
 Сколько осталось дней до желаемого выполнения
 Сколько осталось дней до крайнего срока*/
	
	$ext_status_warning_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/ext_status_warning.tpl');
	
	$ext_status_fail_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/ext_status_fail.tpl');
	
	$ext_status_info_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/ext_status_info.tpl');
	
	if(in_array($task_data['task_status'], array(1,2,3,4,5)) && $task_data['task_from_user']!=$task_data['task_to_user'] && !preg_match('/0000/', $task_data['task_confirm_date']))
	{
		// До принятия
		$date_result_raznost = to_mktime($task_data['task_confirm_date']) - to_mktime($task_data['task_date_add']);
			 
		$date_result_arr =  sec_to_date_words($date_result_raznost);
		
		$date_result = $date_result_arr['string'];
		
		$PARS['{STATUS}'] = "Ушло ".$date_result." на принятие задания";
		
		$status['confirm'] = fetch_tpl($PARS, $ext_status_info_tpl);
	}
	
	// От принятия до начала выполнения
	if(in_array($task_data['task_status'], array(2,3, 5)) && $task_data['task_from_user']!=$task_data['task_to_user'] && !preg_match('/0000/', $task_data['task_in_proc_date']))
	{
		$date_result_raznost = to_mktime($task_data['task_in_proc_date']) - to_mktime($task_data['task_confirm_date']);
			 
		$date_result_arr =  sec_to_date_words($date_result_raznost);
			
		$date_result = $date_result_arr['string'];
		
		$PARS['{STATUS}'] = "Ушло ".$date_result." на начало выполнения";
		
		$status['proc'] = fetch_tpl($PARS, $ext_status_info_tpl);
	}
	
	// От начала выполнения до завершения
	if($task_data['task_status']==3 && $task_data['task_from_user']!=$task_data['task_to_user'])
	{
		$date_result_raznost = to_mktime($task_data['task_finished_date']) - to_mktime($task_data['task_in_proc_date']);
			 
		$date_result_arr =  sec_to_date_words($date_result_raznost, 1);
		
		$date_result = $date_result_arr['string'];
			
		$PARS['{STATUS}'] = "Ушло ".$date_result." от выполнения к завершению выполнения";
		
		$status['complete'] = fetch_tpl($PARS, $ext_status_info_tpl);
	}
	
	
	
	// Желаемое время
	if(!preg_match('/0000/', $task_data['task_desired_date']) && $task_data['task_from_user']!=$task_data['task_to_user'])
	{
		// Если задание уже выполнено
		if($task_data['task_finished_confirm']==1)
		{  
			$date_result_raznost = to_mktime($task_data['task_desired_date']) - to_mktime($task_data['task_finished_date']);
		}
		else
		{
			$date_result_raznost = to_mktime($task_data['task_desired_date']) - time();
		}
 
		// Желаемое время просрочено
		if($date_result_raznost<0)
		{
			$date_result_arr =  sec_to_date_words(abs($date_result_raznost), 1);
			
			$date_result = $date_result_arr['string'];
			
			$str = $date_result_arr['is_days'] ? 'на' : '';
			
			$PARS['{STATUS}'] = "Желаемое время выполнения просрочено $str ".$date_result;
			
			$status['desired_over'] = fetch_tpl($PARS, $ext_status_fail_tpl);
		}
		else
		{
			$date_result_arr =  sec_to_date_words($date_result_raznost, 1);
			
			$date_result = $date_result_arr['string'];
			
			$PARS['{STATUS}'] = "Осталось ".$date_result." до желаемого срока выполнения";
			
			$status['desired_left'] = fetch_tpl($PARS, $ext_status_warning_tpl);
		}
	}
	
	// Крайний срок
	if(!preg_match('/0000/', $task_data['task_max_date']))
	{
		// Если задание уже выполнено
		if($task_data['task_finished_confirm']==1)
		{  
			$date_result_raznost =  to_mktime($task_data['task_max_date']) - to_mktime($task_data['task_finished_date']);
		}
		else
		{ 
			$date_result_raznost =  to_mktime($task_data['task_max_date']) - time();
		}
		 
		// Крайний срок просрочен
		if($date_result_raznost<0)
		{
			$date_result_arr =  sec_to_date_words(abs($date_result_raznost), 1);
			 
			$date_result = $date_result_arr['string'];
			
			$str = $date_result_arr['is_days'] ? 'на' : '';
			
			$PARS['{STATUS}'] = "Крайний срок выполнения просрочен $str ".$date_result."";
			
			$status['max_over'] = fetch_tpl($PARS, $ext_status_fail_tpl);
		}
		else
		{
			$date_result_arr =  sec_to_date_words($date_result_raznost, 1);
			 
			$date_result = $date_result_arr['string'];
			
			$PARS['{STATUS}'] = "Осталось ".$date_result." до крайнего срока";
			
			$status['max_left'] = fetch_tpl($PARS, $ext_status_warning_tpl);
		}
	}
	
	return $status;
	
	//return fetch_tpl($PARS, $task_extend_status_tpl);
}

// Возвращает массив данных о просроченных датах выполнения
function task_expired_arr($task_data)
{
	global $site_db;
	
	// Флаг - задание просрочено
	$task_desired_date_expired = 0;
	$task_max_date_expired = 0;
	
	
	// Желаемое время
	if(!preg_match('/0000/', $task_data['task_desired_date']))
	{
		$date_result_raznost = to_mktime($task_data['task_desired_date']) - (time());
		
		// Желаемое время просрочено
		if($date_result_raznost<0)
		{
			$task_desired_date_expired = 1;
		}
		
	}
	// Крайний срок
	if(!preg_match('/0000/', $task_data['task_max_date']))
	{
		$date_result_raznost =  to_mktime($task_data['task_max_date']) - time();
		
		// Желаемое время просрочено
		if($date_result_raznost<0)
		{
			$task_max_date_expired = 1;
		}
		
	}
	
	// Если задание было выполнено
	if($task_data['task_status']==3 && $task_data['task_finished_confirm']==1)
	{
		$task_desired_date_expired = 0;
		$task_max_date_expired = 0;
	}
	
	return array('task_max_date_expired' => $task_desired_date_expired, 'task_desired_date_expired' => $task_max_date_expired);
}


// Страница поставленных задач сотрудникам
function fill_tasks_from_user_to_users($user_id)
{
	global $site_db, $current_user_id, $user_obj, $_CURRENT_USER_DEPUTY_WORKERS_ARR, $_CURRENT_USER_WORKERS_ARR;
	
	$tasks_from_user_to_users_tpl = file_get_contents('templates/tasks/tasks_from_user_to_users.tpl');
		
	
	$actual_date = date('Y-m-d');
	
	$active_class = 'menu_active';
	
	// Вывести задания за неделю
	if($_GET['week'])
	{
		$active_2 = $active_class;
		
	}
	// Сегодняшний день
	else if($_GET['today'])
	{
		$active_1 = $active_class;
	}
	// ЗАдачи требующие действия со стороны начальника
	else if($_GET['act']==1)
	{
		$active_4 = $active_class;
	}
	else
	{
		$active_3 = $active_class;	
		
		// Панель поиска
		$tasks_search_form = fill_tasks_search_form($to_user_id, 1);
	}
	
	$task_list = fill_tasks_from_user_to_users_tasks_list($user_id);
	
	// Кол-во задач, над которыми требуются действия со стороны начальства
	$new_task_to_act_count = get_new_count_tasks_to_act($current_user_id);
	$new_task_to_act_count = $new_task_to_act_count ? "(+ ".$new_task_to_act_count.")" : '';
	
	$new_task_reports_count_block= "(+".$new_task_reports_count.")";

	
	$PARS['{TASKS_LIST}'] = $task_list;
	
	$PARS['{ACTIVE_1}'] = $active_1;
	
	$PARS['{ACTIVE_2}'] = $active_2;
	
	$PARS['{ACTIVE_3}'] = $active_3;
	
	$PARS['{ACTIVE_4}'] = $active_4;
	
	$PARS['{TASK_SEARCH}'] = $tasks_search_form;
	
	$PARS['{NEW_TASK_TO_ACT_COUNT}'] = $new_task_to_act_count;
	
	return fetch_tpl($PARS, $tasks_from_user_to_users_tpl);
}

// Список Поставленных задач
function fill_tasks_from_user_to_users_tasks_list($user_id, $search_word='')
{
	global $site_db, $current_user_id, $user_obj;
	
	$tasks_from_user_to_users_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_from_user_to_users_item.tpl');
	
	$tasks_from_user_to_users_item_task_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_from_user_to_users_item_task_item.tpl');
	
	$tasks_from_user_to_users_no_tasks_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/tasks_from_user_to_users_no_tasks.tpl');
	
	$task_group_by_date_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_group_by_date.tpl');
	
	$actual_date = date('Y-m-d');
	
	$active_class = 'menu_active';
	 
	// Вывести задания за неделю
	if($_GET['week'])
	{
		$date_from = time() - 3600 * 24 * 7;
		
		$date_from = date('Y-m-d', $date_from);
		
		$and_date = " AND i.task_date_add>='$date_from' AND i.task_date_add<='$actual_date 23:59:59' ";	
		
	}
	// Сегодняшний день
	else if($_GET['today'])
	{
		$and_date = " AND i.task_date_add>='$actual_date' ";	
	}
	// ЗАдачи требующие действия со стороны начальника
	else if(!$_GET['act'])
	{
		$and_date = " AND i.task_date>='$actual_date'";
	}
	
	// Массив подчиненных
	$workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	
	if($workers_arr)
	{
		$workers_ids = implode(',', $workers_arr);
		
		if($search_word)
		{
			// Выбираем все задачи, который пользователь выставлял
			$sql = "SELECT i.* FROM ".TASKS_TB." i
					WHERE i.task_deleted<>1 AND i.task_from_user='$user_id' AND task_to_user IN($workers_ids)
					AND (task_theme LIKE '%$search_word%' OR  task_text LIKE '%$search_word%')";
		}
		// Требуют действия
		else if($_GET['act']==1)
		{
			// Выбираем все задачи над которыми требуются действия со стороны начальника
			$sql = "SELECT i.* FROM ".TASKS_TB." i
					WHERE i.task_deleted<>1 AND i.task_from_user='$user_id' AND task_to_user IN($workers_ids)
					AND (task_has_new_reports_for_from_user=1 OR (task_finished_confirm=0 AND task_status=3) OR task_status=4)";					 
		}
		else
		{	 
			// Выбираем все задачи, который пользователь выставлял
			$sql = "SELECT i.* FROM ".TASKS_TB." i
					WHERE i.task_deleted<>1 AND i.task_from_user='$user_id' AND task_to_user IN($workers_ids)
					AND (task_finished_confirm=0 OR (task_finished_confirm=1 AND task_finished_date>='$actual_date')) $and_date";
		}
		
		$res = $site_db->query($sql);
		
		// Формируем массив пользователей с поставленными им задачами 
		while($row=$site_db->fetch_array($res,1))
		{
			$tasks_arr[$row['task_to_user']][$row['task_id']] = $row;
		}
	}
	
	
	// Формируем список сотрудников с задачами
	foreach($tasks_arr as $user => $task_data_arr)
	{
		$user_obj->fill_user_data($user);
		
		$user_name = $user_obj->get_user_name();
		
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_surname = $user_obj->get_user_surname();
		
		$user_position = $user_obj->get_user_position();
		
		// Превью аватарки пользователя
		$user_avatar_src = get_user_preview_avatar_src($user, $user_obj->get_user_image());
		
		$user_task_list = '';
		$new_task_reports_count_block = '';
		$new_task_reports_count = '';
	
		  
	 	$tasks_list_arr = array();
		
		// Формируем список задач сотрудника
		foreach($task_data_arr as $task_data)
		{
			$tasks_list_arr[$task_data['task_date']] .=  fill_worker_tasks_list_item($task_data);
		}
		
		
		$user_tasks_list = '';
		
		// Проходим массиву заданий и формируем группированный список по дате
		foreach($tasks_list_arr as $date => $list)
		{
			$PARS_2['{LIST}'] = $list;
			
			$PARS_2['{DATE}'] = $date;
			
			$PARS_2['{DATE_RUS}'] = datetime($date, '%j %F');
			
			$user_tasks_list .= fetch_tpl($PARS_2, $task_group_by_date_tpl);
		}
		
		$PARS_1['{USER_ID}'] = $user;
		
		$PARS_1['{NAME}'] = $user_name;
		
		$PARS_1['{MIDDLENAME}'] = $user_middlename;
		
		$PARS_1['{SURNAME}'] = $user_surname;
		
		$PARS_1['{USER_POSITION}'] = $user_position;
		
		$PARS_1['{AVATAR_SRC}'] = $user_avatar_src;
		
		$PARS_1['{USER_TASKS_LIST}'] = $user_tasks_list;
		
		$PARS_1['{NEW_TASK_REPORTS_COUNT}'] = $new_task_reports_count_block;
		
		$task_list .= fetch_tpl($PARS_1, $tasks_from_user_to_users_item_tpl);
	}
	
	// Если нет поставленных задач
	if(!$task_list)
	{
		$task_list = $tasks_from_user_to_users_no_tasks_tpl;
	}
	
	return $task_list;
}


// Невыполненные задачи прошлых дат переводит на новую дату, когда зашел юзер
function tasks_date_to_actual($user_id)
{
	global $site_db, $current_user_id;
	
	$now_date = date('Y-m-d');
	
	// Обновляем у подчиненного
	$sql = "UPDATE ".TASKS_TB." SET task_date='$now_date' WHERE task_to_user='$user_id' AND task_deleted <> 1 AND task_date<'$now_date' AND task_finished_confirm=0 AND task_status not in (4,5)";
	
	$res = $site_db->query($sql);
	
	// У начальника
	$sql = "UPDATE ".TASKS_TB." SET task_date='$now_date' WHERE task_from_user='$user_id' AND task_deleted <> 1 AND task_date<'$now_date' AND task_finished_confirm=0 AND task_status not in (4,5)";
	
	 
	$res = $site_db->query($sql);
}

// Проверяет, задание самому себе?
function is_task_myself($task_data, $user_id)
{
	global $site_db, $current_user_id;
	
	// задание самому себе
	if($user_id==$task_data['task_from_user'] && $user_id == $task_data['task_to_user'])
	{
		return true;
	}
	else
	{
		return false;
	}
				
}

// Возвращает новое кол-во отчетов для всех задач сотруднику от начальника
function get_new_task_reports_count_by_users($worker_id, $boss_id)
{
	global $site_db, $user_obj;
	
	// Если передан сотрудник, то ищем кол-во отчетов от этого сотрудника для задач начальника
	if($worker_id)
	{
		$sql = "SELECT COUNT(*) as count FROM ".TASKS_REPORTS_TB." i, ".TASKS_TB." j
				WHERE i.task_id=j.task_id AND j.task_from_user='$boss_id' AND i.report_user_id='$worker_id' AND i.report_confirm=0 AND j.task_deleted<>1";
	}
	else
	{
		// Массив подчиненных
		$workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));
		
		if($workers_arr)
		{
			$workers_ids = implode(',', $workers_arr);
			
			$sql = "SELECT COUNT(*) as count FROM ".TASKS_REPORTS_TB." i, ".TASKS_TB." j  
					WHERE i.task_id=j.task_id AND j.task_from_user='$boss_id' AND i.report_user_id<>'$boss_id' AND i.report_confirm=0 AND j.task_deleted<>1 AND j.task_to_user IN ($workers_ids)";
		}
				
	}
 
	
	$row = $site_db->query_firstrow($sql);	
	
	return $row['count'];
	
	 
}

// Возвращает кол-во новых отчетов для задания для начальника
function get_new_task_reports_count_for_user($task_id, $for_boss)
{
	global $site_db, $user_obj, $current_user_id;
	
	if($for_boss)
	{
		$sql = "SELECT COUNT(*) as count FROM ".TASKS_REPORTS_TB." i
				LEFT JOIN ".TASKS_TB." j ON i.task_id=j.task_id
				WHERE i.task_id='$task_id' AND j.task_from_user <> i.report_user_id AND i.report_confirm=0 ";
	}
	else
	{
		$sql = "SELECT COUNT(*) as count FROM ".TASKS_REPORTS_TB." i
				LEFT JOIN ".TASKS_TB." j ON i.task_id=j.task_id
				WHERE i.task_id='$task_id' AND j.task_to_user <> i.report_user_id AND i.report_confirm=0 ";
	}
	
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}


// Блок календаря
function fill_calendar_block($user_id, $mode)
{
	global $site_db, $current_user_id;
	
	$calendar_block_tpl = file_get_contents('templates/calendar/calendar_block.tpl');
	
	if($mode=='worker_task')
	{
		// Если не ввляется подчиненным, то не выводим задачи подчиненного, какие он сам себе выставлял
		if(!check_user_access_to_user_content($user_id, array(0,1,0,0,0)))
		{
			$and_without = "AND task_from_user!=task_to_user ";
		}
		 
	}
	if($mode=='my_tasks')
	{
		$where_s = "";
	}
	
	// Формирование блока календаря
	// Выбираем все даты задач
	$sql = "SELECT task_id, task_finished_confirm, task_status, task_date, task_finished_date, task_finished_fail_date 
			FROM ".TASKS_TB." 
			WHERE task_to_user='$user_id' AND task_deleted<>1 $and_without";

				
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res, 1))
	{
		if($row['task_finished_confirm']==1)
		{
			$dt = substr($row['task_finished_date'],0,10);
			$dates_array[$dt] = $dt;
		}
		else if($row['task_finished_confirm']==0 && $row['task_status']==5)
		{
			$dt = substr($row['task_finished_fail_date'],0,10);
			$dates_array[$dt] = $dt;
		}
		else
		{
			$dt = substr($row['task_date'],0,10);
			$dates_array[$dt] = $dt;
		}
		 
	}
	
	//$dates_array = array_values($dates_array); 
	
	  
	$PARS['{INIT_DATE}'] = $_GET['date'] ? $_GET['date'] : date('Y-m-d');
	
	$PARS['{ARRAY_DATES}'] = json_encode($dates_array);
	
	$PARS['{TO_USER_ID}'] = $user_id;
	
	$PARS['{PAGE}'] = $mode;
	
	$calendar_block = fetch_tpl($PARS, $calendar_block_tpl);
	
	return $calendar_block;
}

function confirm_all_task_reports($task_data, $confirmed_by_user_id)
{
	global $site_db, $current_user_id;
	
	// Принимаем все отчеты по задаче
	$sql = "UPDATE ".TASKS_REPORTS_TB." SET report_confirm=1 WHERE task_id='".$task_data['task_id']."' AND report_user_id<>'$confirmed_by_user_id'";
				
	$site_db->query($sql);
	
	// Если начальник принимает все отчеты для задания, то персчитываем кол-во отчетов для начальника с целью установить флаг уведомления о новых отчетах
	if($task_data['task_from_user']==$confirmed_by_user_id)
	{
		task_has_new_reports_flag_for_task_from_user($task_data['task_id']);
	}
	else if($task_data['task_to_user']==$confirmed_by_user_id)
	{
		task_has_new_reports_flag_for_task_to_user($task_data['task_id']);
	}
	 
}


// Пересчитывает кол-во непрочитанных отчетов по задаче и если есть такие, то ставит флаг, что есть непрочитанные отчеты. Для пользователя, который выставлял задание
function task_has_new_reports_flag_for_task_from_user($task_id)
{
	global $site_db, $current_user_id;
	
	if(get_new_task_reports_count_for_user($task_id, 1))
	{
		// Ставим флаг, что у задачи есть непрочитанные отчеты о задаче
		$sql = "UPDATE ".TASKS_TB." SET task_has_new_reports_for_from_user = 1 WHERE task_id='".$task_id."'";
			
		$site_db->query($sql);
	}
	else
	{
		// Ставим флаг, что у задачи есть непрочитанные отчеты о задаче
		$sql = "UPDATE ".TASKS_TB."  SET task_has_new_reports_for_from_user = 0 WHERE task_id='".$task_id."'";
			
		$site_db->query($sql);
	}
}

// Пересчитывает кол-во непрочитанных отчетов по задаче и если есть такие, то ставит флаг, что есть непрочитанные отчеты. Для пользователя, которому задание выставляли
function task_has_new_reports_flag_for_task_to_user($task_id)
{
	global $site_db, $current_user_id;
	
	if(get_new_task_reports_count_for_user($task_id))
	{
		// Ставим флаг, что у задачи есть непрочитанные отчеты о задаче
		$sql = "UPDATE ".TASKS_TB." SET task_has_new_reports_for_to_user = 1 WHERE task_id='".$task_id."'";
			
		$site_db->query($sql);
	}
	else
	{
		// Ставим флаг, что у задачи есть непрочитанные отчеты о задаче
		$sql = "UPDATE ".TASKS_TB."  SET task_has_new_reports_for_to_user = 0 WHERE task_id='".$task_id."'";
			
		$site_db->query($sql);
	}
}

// Кол-во задач, над которыми необходимы действия со стороны начальства
function get_new_count_tasks_to_act($user_id)
{
	global $site_db, $current_user_id;
	
	// Массив подчиненных
	$workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	
	if($workers_arr)
	{
		$workers_ids = implode(',', $workers_arr);
		// Выбираем все задачи над которыми требуются действия со стороны начальника
			$sql = "SELECT COUNT(*) as count FROM ".TASKS_TB." i
					WHERE i.task_deleted<>1 AND i.task_from_user='$user_id' AND task_to_user IN($workers_ids)
					AND (task_has_new_reports_for_from_user=1 OR (task_finished_confirm=0 AND task_status=3) OR task_status=4)";
	}
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Кол-во заданий, для которых есть непросмотренные комментарии от начальства
function get_count_notice_new_reports_for_task_for_user($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM ".TASKS_TB." WHERE task_to_user='$user_id' AND task_has_new_reports_for_to_user=1 AND task_deleted<>1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

function fill_tasks_in_linked_content($link_content)
{
	global $site_db, $current_user_id;
	
	$task_in_linked_content_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tasks/task_in_linked_content.tpl');
	
	if(!$link_content['deal_id'])
	{
		return '';
	}
	
	// Проверка, была ли активная задача пользователя привязана к сделке
	$sql = "SELECT j.* FROM ".TASKS_DEALS_LINKS_TB." i
			LEFT JOIN ".TASKS_TB." j ON j.task_id=i.task_id
			WHERE i.deal_id='".$link_content['deal_id']."' AND j.task_to_user='$current_user_id'";
			
	$row = $site_db->query_firstrow($sql);

/*// Задачи пользователя
		$sql = "SELECT *, if(task_status=3,1,0) as order_by_task_status FROM ".TASKS_TB." 
				WHERE task_to_user='$to_user_id' AND task_deleted<> 1 $and_without
				AND (
						(task_finished_confirm = 0 AND task_status<>5) 
						OR 
						(task_finished_confirm=0 AND task_status=5 AND task_finished_fail_date>='$actual_date') 
						OR 
						(task_finished_confirm=1 AND task_finished_date>='$actual_date')
					) $and_search_words
				ORDER by order_by_task_status, task_priority DESC, task_desired_date ASC, task_id DESC";	*/
				
	if($row['task_id'] && !$row['task_finished_confirm'] && $row['task_status'] != 5)
	{
		$task_theme = fill_task_preview_text($row);
		
		$PARS['{TASK_ID}'] = $row['task_id'];
		
		$PARS['{TASK_THEME}'] = $task_theme;
		
		return fetch_tpl($PARS, $task_in_linked_content_tpl);
	}
}
?>