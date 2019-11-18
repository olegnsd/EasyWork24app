<?php
// Постоянная работа для пользователя
function fill_user_work($user_id)
{
	global $site_db, $current_user_id;
	
	if($user_id!=$current_user_id && !check_user_access_to_user_content($user_id, array(0,1,0,0,1)))
	{
		header('Location: /work');
	}
	
	$work_tpl = file_get_contents('templates/work/user_work.tpl');
	
	$add_work_form_tpl = file_get_contents('templates/work/add_work_form.tpl');
	
	$history_block_tpl = file_get_contents('templates/work/history_block.tpl');

		
	if($user_id!=$current_user_id)
	{
		// Форма добавления новой обязанности
		$PARS_1['{USER_ID}'] =  $user_id;
		
		// Селект периодов для отчетности
		$PARS_1['{PERIODICITY_REPORT_OPTIONS}'] = get_periodicity_report_options();
		
		$add_work_form = fetch_tpl($PARS_1, $add_work_form_tpl);
	}
	
	// Данные по актуальной обязанности
	$work_data = actual_work_data_arr($user_id);
	
	$actual_work_id = $work_data['work_id'];
	
	// Выбираем последний добавленный отчет
	$sql = "SELECT * FROM ".WORK_REPORTS_TB." WHERE work_id='$actual_work_id' ORDER by report_id DESC LIMIT 1";
		
	$row = $site_db->query_firstrow($sql);
	
	if($row['report_id'])
	{
		$_SESSION['last_work_report_id'] = $row['report_id'];
	}
		
	// Пользователь, который установил круг обязанностей
	$work_boss_user_id = $work_data['work_from_user_id'];
	 
	// Список отчетов
	$reports_list = fill_work_reports($work_data, $work_boss_user_id);
	
	// Блок текущей обязанности
	$work_item = fill_work_item($actual_work_id, '');
	
	// Кол-во всех кругов обязанностей для сотрудника
	$user_work_count = get_user_work_count($user_id);
	
	// Выводим блок истории обязанностей
	if($user_work_count > 1)
	{
		$history_block = fill_history_block($user_id);
	}
	
	$PARS['{WORK_ID}'] = $work_data['work_id'];
	
	$PARS['{WORK_ITEM}'] = $work_item;
	
	$PARS['{ADD_WORK_FORM}'] = $add_work_form;
	
	$PARS['{REPORT_BLOCK}'] = $reports_list;
	
	$PARS['{CURRENT_USER_ID}'] = $current_user_id;
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{HISTORY_PER_PAGE}'] = HISTORY_PER_PAGE;
	
	$PARS['{HISTORY_BLOCK}'] = $history_block;
	
	return fetch_tpl($PARS, $work_tpl);
}

// Заполнение блока задания
function fill_work_item($work_id, $work_data, $is_history)
{
	global $site_db, $current_user_id, $user_obj;
 
	$user_work_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/work/user_work_item.tpl');
	
	$confirm_status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/work/confirm_status_btn.tpl');
	
	$no_work_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/work/no_work.tpl');
	
	$new_work_reports_sms_notice_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/work/new_work_reports_sms_notice.tpl');
	
	// Если передан id обязанности
	if($work_id)
	{
		// Выбор текущей работы
		$sql = "SELECT * FROM ".WORK_TB." WHERE work_id='$work_id'";
		 
		$work_data = $site_db->query_firstrow($sql);
	}	
	
	if(!$work_data)
	{
		return $no_work_tpl;
	}
	
	$work_back_class =   $work_data['work_status']==0 && !$is_history ? 'not_confirm' : '';
	
	$notice_status_btn = '';
	
	$work_not_confirm = '';
	
	if(!$is_history && $work_data['work_to_user_id']!=$current_user_id)
	{
		// Начальнику выводим блок уведомления по смс
		if(check_work_sms_notice_for_user($work_id, $current_user_id))
		{
			// Уведомить по смс начальника о новых отчетах по смс
			$sms_notice_checked = 'checked="checked"';
		}
		
		$PARS_2['{WORK_ID}'] = $work_id;
		$PARS_2['{SMS_NOTICE_CHECKED}'] = $sms_notice_checked;
		$new_reports_sms_notice = fetch_tpl($PARS_2, $new_work_reports_sms_notice_tpl);
	}
		
	
	// Если не принято
	if($work_data['work_to_user_id']==$current_user_id && $work_data['work_status']==0)
	{
		$PARS_1['{WORK_ID}'] = $work_id;
		
		$confirm_work_btn = fetch_tpl($PARS_1, $confirm_status_btn_tpl);
	} 
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($work_data['work_from_user_id']);
	
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($work_data['work_from_user_id'], $user_obj->get_user_image());
	
	$PARS['{FROM_USER_ID}'] = $work_data['work_from_user_id'];
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;
	
	$PARS['{WORK_STATUS}'] = $work_data['work_status']==0 ? 'не принято' : 'принято';
	
	$PARS['{WORK_BACK_CLASS}'] = $work_back_class;
	
	$PARS['{WORK_REPORT_STATUS}'] = get_periodicity_report_name_by_id($work_data['work_periodicity_report']);
	
	$PARS['{NEW_REPORTS_SMS_NOTICE}'] = $new_reports_sms_notice;
	
	$PARS['{WORK_ID}'] = $work_data['work_id'];
	
	$PARS['{WORK_TEXT}'] = nl2br($work_data['work_text']);
	
	$PARS['{DATE}'] = datetime($work_data['work_date_add'], '%j %M в %H:%i');
	
	$PARS['{CONFIRM_BTN}'] = $confirm_work_btn;
	
	return fetch_tpl($PARS, $user_work_item_tpl);

}

function check_work_sms_notice_for_user($work_id, $user_id)
{
	 global $site_db, $current_user_id, $user_obj;
	 
	 $sql = "SELECT * FROM tasks_work_sms_notice WHERE work_id='$work_id' AND user_id='$user_id'";
	 
	 $row = $site_db->query_firstrow($sql);
	 
	 if($row['id']) return true;
	 else return false;
}

// Возвращает актуальную постоянную работу для пользователя
function actual_work_data_arr($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "SELECT * FROM ".WORK_TB."  WHERE work_to_user_id='$user_id' ORDER by work_id DESC LIMIT 1";
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row;
}
// Возвращает id актуальной работы пользователя
function fill_work_reports($work_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$work_report = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/work/work_report.tpl');
	
	$work_report_add_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/work/work_report_add_form.tpl');
	
	$more_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/work/more_btn.tpl');
	
	include_once ($_SERVER['DOCUMENT_ROOT'].'/includes/functions_upl.php');
	 
	// Выводим блок добавления отчета
	if($current_user_id==$work_data['work_to_user_id'])
	{	
		 
		$PARS_1['{WORK_ID}'] = $work_data['work_id'];
		
		$report_add_form = fetch_tpl($PARS_1, $work_report_add_form_tpl);
	}
	
	// Кол-во отчетов
	$reports_count = get_work_reports_count($work_data['work_id']);
	
	// Кол-во страниц
	$pages_count = ceil($reports_count/WORK_REPORTS_PER_PAGE);
		
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_btn = $more_btn_tpl;
	}
	
	// Список отчетов
	$reports_list = fill_work_reports_list($work_data);
	
	if(!$reports_list && $current_user_id!=$work_data['work_to_user_id'])
	{
		return '';
	}
	
	$PARS['{REPORT_ADD_FORM}'] = $report_add_form;
	
	$PARS['{REPORTS_LIST}'] = $reports_list;
	
	$PARS['{MORE_BTN}'] = $more_btn;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	return fetch_tpl($PARS, $work_report);
}

// Кол-во отчетов для круга обязанностей
function get_work_reports_count($work_id)
{
	global $site_db, $current_user_id;
	
	// Выбираем все отчеты для данной работы
	$sql = "SELECT COUNT(*) as count FROM ".WORK_REPORTS_TB." WHERE work_id='$work_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Список отчетов
function fill_work_reports_list($work_data, $page=1)
{
	global $site_db, $current_user_id, $user_obj;
	
	$work_id = $work_data['work_id'];
	 
	$page = $page ? $page : 1;
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_work_report_id'])
	{
		$and_report_id = " AND report_id <= '".$_SESSION['last_work_report_id']."' ";
	}
	
	// Страничность
	$begin_pos = WORK_REPORTS_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".WORK_REPORTS_PER_PAGE;
	
	// Выбираем все отчеты для данной работы
	$sql = "SELECT * FROM ".WORK_REPORTS_TB." WHERE work_id='$work_id' $and_report_id ORDER by report_id DESC $limit";
	
	$res = $site_db->query($sql);
		
	while($report_data=$site_db->fetch_array($res))
	{ 
		$reports_list .= fill_work_report_item($report_data, $work_data);
	}
	
	return $reports_list;
}

// Заполняет элемент отчета
function fill_work_report_item($report_data, $work_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$work_report_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/work/work_report_item.tpl');
	
	$report_confirm_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/work/report_confirm_btn.tpl');
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($report_data['report_from_user_id']);
	
	// Если актуальный пользователь устанавливал круг обязанностей для сотрудника
	// Отчет не подтвержден
	if($current_user_id==$work_data['work_from_user_id'] && !$report_data['report_confirm'])
	{
		$report_not_confirm = 'not_confirm';
			
		$PARS_1['{REPORT_ID}'] = $report_data['report_id'];
			
		$PARS_1['{WORK_ID}'] = $work_data['work_id'];
			
		$confirm_btn = fetch_tpl($PARS_1, $report_confirm_btn_tpl);
	}
	
	// Список файлов для отчета
	//$files_list = fill_work_report_files_list($report_data['report_id']);
	
	// Список файлов для отчета
	$files_list = get_attached_files_to_content($report_data['report_id'], 1);
	
	// Превью аватарки пользователя
	$user_avatar_src = get_user_preview_avatar_src($report_data['report_from_user_id'], $user_obj->get_user_image());
	
	
	$PARS['{USER_ID}'] = $report_data['report_from_user_id'];
	
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{AVATAR_SRC}'] = $user_avatar_src;

	$PARS['{REPORT_TEXT}'] = nl2br($report_data['report_text']);
	
	$PARS['{DATE}'] = datetime($report_data['report_date'], '%j %M в %H:%i');
	
	$PARS['{REPORT_ID}'] = $report_data['report_id'];
	
	$PARS['{CONFIRM_BTN}'] = $confirm_btn;
		 
	$PARS['{REPORT_BACK_CLASS}'] = $report_not_confirm;
	
	$PARS['{FILES_LIST}'] = $files_list;
	
	return fetch_tpl($PARS, $work_report_item_tpl);
}
// Список файлов отчета
function fill_work_report_files_list($report_id)
{
	global $site_db, $current_user_id;
	
	$files_box_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/work/files_box.tpl');
	
	$report_file_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/work/report_file.tpl');
	
	// Выбор файлов отчета
	$sql = "SELECT * FROM ".WORK_FILES_TB." WHERE report_id='$report_id'";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$PARS['{FILE_ID}'] = $row['file_id'];
		
		$PARS['{FILE_NAME}'] = $row['file_name'];
		
		$files_list .= fetch_tpl($PARS, $report_file_tpl);
	}
	
	if(!$files_list)
	{
		return '';
	}
	
	$PARS['{FILES_LIST}'] = $files_list;
	
	return fetch_tpl($PARS, $files_box_tpl);
}

// Скачивание файла
function fill_work_file_download($file_id)
{
	global $site_db, $current_user_id;
	
	if(!$current_user_id)
	{
		exit();
	}
	// Данные файла
	$sql = "SELECT * FROM ".WORK_FILES_TB." WHERE file_id='$file_id'";
		
	$file_data = $site_db->query_firstrow($sql);
	 
	
	if(!$file_data['file_id'] || !$file_id)
	{
		header('Location: /work');
		exit();
	}

	$file_name = WORK_REPORTS_PATH.'/'.$file_data['report_id'].'/'.$file_data['file_new_name'];

	$file_base_name =  iconv( 'cp1251', 'utf-8', $file_data['file_name']);
 
	// Даем файл на скачку
	file_download($file_name, 'application/octet-stream', $file_base_name);
}

// Блок истории круга обязанностей
function fill_history_block($user_id)
{
	$history_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/work/history_block.tpl');
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{HISTORY_PER_PAGE}'] = HISTORY_PER_PAGE;
	
	return fetch_tpl($PARS, $history_block_tpl);
}

// Получает статус постоянной работыпо id
function get_user_work_status_by_work_id($work_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT work_status FROM ".WORK_TB." WHERE work_id='$work_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['work_status'];

}

// Возвращает название параметра периодичности отправки отчета по его id
function get_periodicity_report_name_by_id($periodicity_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT periodicity_value FROM ".PERIODICITY_TB." WHERE periodicity_id='$periodicity_id'";
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['periodicity_value'];
}
// Пункты периодичности отчета для селекта
function get_periodicity_report_options()
{
	global $site_db, $current_user_id;
	
	$option_tag_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');

	$sql = "SELECT * FROM ".PERIODICITY_TB."";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$PARS['{VALUE}'] = $row['periodicity_id'];
		
		$PARS['{NAME}'] = $row['periodicity_value'];
		
		$options_list .= fetch_tpl($PARS, $option_tag_tpl);
	}
	
	return $options_list;
}

// Проверка, является ли работа для пользователя
function is_work_for_user($user_id, $work_id)
{
	global $site_db;
	
	$sql = "SELECT work_id FROM ".WORK_TB." WHERE work_id='$work_id' AND work_to_user_id='$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['work_id'])
	{
		return true;
	}
	else
	{
		return false;
	}
}

// Получает кол-во постоянных работ
function get_user_work_count($user_id)
{
	global $site_db;
	
	$sql = "SELECT COUNT(*) as count FROM ".WORK_TB." WHERE work_to_user_id='$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Возвращает историю постоянной работы
function get_history_work_list($user_id, $page, $actual_work_id)
{
	global $site_db, $user_obj;
	
	if($page>=0)
	{
		$begin_pos = HISTORY_PER_PAGE * $page;
		$limit = " LIMIT ".$begin_pos.",".HISTORY_PER_PAGE;
	}
	if($actual_work_id)
	{
		$not_work = " AND work_id<>'$actual_work_id' ";
	}
	$sql = "SELECT * FROM ".WORK_TB." WHERE work_to_user_id='$user_id' $not_work ORDER by work_date_add DESC $limit";
	
	$res = $site_db->query($sql);
		  
	while($row=$site_db->fetch_array($res))
	{
		$history_list .= fill_work_item(0, $row, 1);
	}
	
	$PARS['{VALUE}'] = $row['periodicity_id'];
	
	return $history_list;
}
// Возвращает новое кол-во отчетов для круга обязанностей
function get_new_work_reports_count($work_id)
{
	global $site_db, $user_obj;
	
	$sql = "SELECT COUNT(*) as count FROM ".WORK_REPORTS_TB." WHERE work_id='$work_id' AND report_confirm=0";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}
// Возвращает актуальную постоянную работу для пользователя
function get_actual_work_id_for_user_arr($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "SELECT work_id, work_from_user_id FROM ".WORK_TB."  WHERE work_to_user_id='$user_id' ORDER by work_id DESC LIMIT 1";
	 
	$row = $site_db->query_firstrow($sql);
	
	return array('work_id' => $row['work_id'], 'work_from_user_id' => $row['work_from_user_id']);
}
?>