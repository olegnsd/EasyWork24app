<?php
// Список Моих коллег
function fill_evcal($user_id)
{
	global $site_db, $current_user_id;
	
	$show_event_id = value_proc($_GET['event_id']);
	
	$main_tpl = file_get_contents('templates/evcal/evcal.tpl');
	
	
	$evcal_body_tpl = file_get_contents('templates/evcal/evcal_body.tpl');
	
	$evcal_body_no_access_tpl = file_get_contents('templates/evcal/evcal_body_no_access.tpl');
	
	
	$evcal_user_id = $_GET['id'] ? $_GET['id'] : $current_user_id;
	
	// Форма добавления
	$add_form = get_evcal_add_form(0,0,$evcal_user_id);
	
	// Список сотрудников для просмотра их календарей
	$workers_select = fill_workers_list_for_select($evcal_user_id);
	
	if($evcal_user_id==$current_user_id)
	{
		// Блок настроек
		$options_block = fill_options_block($current_user_id);
		 
		$PARS['{EVCAL_USER_ID}'] = $evcal_user_id;
		$PARS['{EVCAL_WORKERS_SELECT}'] = $workers_select;
		 
		$evcal_body = fetch_tpl($PARS, $evcal_body_tpl);
	}
	// Проверяем доступность просмотра календаря событий сотрудника
	else if($evcal_user_id && !check_evcal_access($evcal_user_id))
	{
		$PARS['{EVCAL_WORKERS_SELECT}'] = $workers_select;
		return fetch_tpl($PARS, $evcal_body_no_access_tpl);
	}
	else if($evcal_user_id && check_evcal_access($evcal_user_id))
	{ 
		$options_block = '';
		
		$PARS['{EVCAL_USER_ID}'] = $evcal_user_id;
		$PARS['{EVCAL_WORKERS_SELECT}'] = $workers_select;
		$evcal_body = fetch_tpl($PARS, $evcal_body_tpl);
	} 
	
	// если требуется показ события сразу
	if($show_event_id)
	{
		$sql = "SELECT * FROM tasks_calendar_of_events WHERE event_id='$show_event_id' AND user_id='$evcal_user_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		$show_event_date_start = date('Y-m-d', $row['event_start_date']);
	}
	
	// форма категорий 
	$categories_form = fill_evcal_cats_form($evcal_user_id);
	
	if($evcal_user_id==$current_user_id)
	{
		// форма предложенных событий 
		$offers_form = fill_event_offers_form($current_user_id);
	}
	 
	
	 
	$PARS['{EVCAL_WORKERS_SELECT}'] = $workers_select;
	
	$PARS['{IS_SF}'] = value_proc($_GET['sf']);
	
	$PARS['{OFFERS_FORM}'] = $offers_form;
	 
	$PARS['{ADD_FORM}'] = $add_form;
	
	$PARS['{CATEGORIES_FORM}'] = $categories_form;

	$PARS['{WORKERS_LIST}'] = $workers_list;
	
	$PARS['{EVCAL_USER_ID}'] = $evcal_user_id;
	
	$PARS['{EVCAL_BODY}'] = $evcal_body;
	 
	$PARS['{OPTIONS_BLOCK}'] = $options_block;
	
	$PARS['{SHOW_EVENT_ID}'] = $show_event_id;
	$PARS['{SHOW_EVENT_DATE_START}'] = $show_event_date_start;
	 
	
	return fetch_tpl($PARS, $main_tpl);
}

function fill_event_offers_form($user_id)
{
	global $site_db, $current_user_id;
	
	$offer_events_block_tpl = file_get_contents('templates/evcal/offer_events_block.tpl');
	
	$offer_events_block_item_tpl = file_get_contents('templates/evcal/offer_events_block_item.tpl');
	
	$sql = "SELECT j.*, i.offer_id FROM tasks_calendar_of_events_offers i
			LEFT JOIN tasks_calendar_of_events j ON i.event_id=j.event_id
			WHERE j.deleted=0 AND i.user_id='$user_id'";
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$event_date_start = date('d.m.Y в H:i', $row['event_start_date']);
		$event_date_finish = date('d.m.Y в H:i', $row['event_finish_date']);
		
		$PARS['{OFFER_ID}'] = $row['offer_id'];
		$PARS['{EVENT_ID}'] = $row['event_id'];
		$PARS['{EVENT_NAME}'] = $row['event_name'];
		$PARS['{EVENT_DATE_START}'] = $event_date_start;
		$PARS['{EVENT_DATE_FINISH}'] = $offer_events_block_item_tpl;
		
		$list .= fetch_tpl($PARS, $offer_events_block_item_tpl);
	}
	
	if(!$list)
	{
		return '';
	}
	
	$PARS['{EVENTS_LIST}'] = $list;
	
	return fetch_tpl($PARS, $offer_events_block_tpl);
}
// Форма категорий
function fill_evcal_cats_form($user_id)
{
	global $site_db, $current_user_id;
	
	$categories_form_tpl = file_get_contents('templates/evcal/categories_form.tpl');
	
	$categories_list_item_tpl = file_get_contents('templates/evcal/categories_list_item.tpl');
	
	$categories_edit_tools_tpl = file_get_contents('templates/evcal/categories_edit_tools.tpl');
	
	$categories_add_btn_tpl = file_get_contents('templates/evcal/categories_add_btn.tpl');
	
	$sql = "SELECT * FROM tasks_calendar_of_events_categories WHERE user_id='$user_id' AND deleted=0 ORDER by category_id";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$edit_tools = '';
		if($row['user_id']==$current_user_id)
		{
			$PARS['{CATEGORY_ID}'] = $row['category_id'];
			$edit_tools = fetch_tpl($PARS, $categories_edit_tools_tpl);
		}
		
		$PARS['{CATEGORY_ID}'] = $row['category_id'];
		$PARS['{CATEGORY_NAME}'] = $row['category_name'];
		$PARS['{CATEGORY_COLOR}'] = $row['category_color'];
		$PARS['{EDIT_TOOLS}'] = $edit_tools;
		
		$evcal_categories_list .= fetch_tpl($PARS, $categories_list_item_tpl);
	}
	
	if($user_id==$current_user_id)
	{
		$add_btn = $categories_add_btn_tpl;
	}
	
	$PARS['{ADD_BTN}'] = $add_btn;
	
	$PARS['{EVCAL_CATEGORIES_LIST}'] = $evcal_categories_list;
	
	return fetch_tpl($PARS, $categories_form_tpl);
}

// форма добавления категории
function get_evcal_category_add_form()
{
	global $site_db, $current_user_id;
	
	$category_add_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/category_add_form.tpl');
	
	$cats_colors_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/cats_colors.tpl');
	
	$PARS['{COLORS}'] = $cats_colors_tpl;
	
	return fetch_tpl($PARS, $category_add_form_tpl);
}

// форма редактирвоания категории
function get_evcal_category_edit_form($category_id)
{
	global $site_db, $current_user_id;
	
	$category_edit_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/category_edit_form.tpl');
	
	$cats_colors_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/cats_colors.tpl');
	
	$sql = "SELECT * FROM tasks_calendar_of_events_categories WHERE category_id='$category_id'";
	
	$row = $site_db->query_firstrow($sql);
	 
	if($row['user_id']!=$current_user_id)
	{
		return '';
	}
	
	$PARS['{COLORS}'] = $cats_colors_tpl;
	
	$PARS['{CATEGORY_ID}'] = $row['category_id'];
	$PARS['{CATEGORY_NAME}'] = $row['category_name'];
	$PARS['{CATEGORY_COLOR}'] = $row['category_color'];
		
	return fetch_tpl($PARS, $category_edit_form_tpl);
}

function fill_options_block($user_id)
{
	global $site_db, $current_user_id;
	
	$options_block_tpl = file_get_contents('templates/evcal/options_block.tpl');
	
	// Проверяем, разрешен ли пользователь доступ к календарю?
	$sql = "SELECT * FROM tasks_calendar_access WHERE user_id='$current_user_id'";
		
	$row = $site_db->query_firstrow($sql);
	
	if($row['id'])
	{
		$public_checked = 'checked="checked"';
		
	}
	
	$PARS['{PUBLIC_CHECKED}'] = $public_checked;
	
	return fetch_tpl($PARS, $options_block_tpl);
}

// Проверка доступа к календарю
function check_evcal_access($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	if(!$user_id)
	{
		return false;
	}
	
	// Проверяем, разрешен ли пользователь доступ к календарю?
	$sql = "SELECT * FROM tasks_calendar_access WHERE user_id='$user_id'";
		
	$row = $site_db->query_firstrow($sql);
	 
	if(($row['id'] && check_user_access_to_user_content($user_id, array(0,1,0,0,1))) || $user_id==$current_user_id)
	{
		return true;
	}
	else return false;
}
// Список сотрудников для показа их календарей событий
function fill_workers_list_for_select($show_user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$workers_select_tpl = file_get_contents('templates/evcal/workers_select.tpl');
	$option_tag_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	$option_disabled_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_disabled.tpl');
	$show_what_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/show_what.tpl');
	$show_what_mine_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/show_what_mine.tpl');
	$change_mine_cal_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/change_mine_cal.tpl');
	
	$workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	
	if(!$workers_arr)
	{
		return '';
	}
	
	foreach($workers_arr as $user_data)
	{
		if($show_user_id==$user_data['user_id'])
		{
			$selected = 'selected="selected"';
		}
		else
		{
			$selected = '';
		}
		
		$user_obj->fill_user_data($user_data['user_id']);
		
		$name = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_position();
		
		$PARS['{SELECTED}'] = $selected;
		
		$PARS['{VALUE}'] = $user_data['user_id'];
		
		$PARS['{NAME}'] = $name;
		
		$workers_list .= fetch_tpl($PARS, $option_tag_tpl);
			 
	}
	
	if($workers_list)
	{
		$PARS['{SELECTED}'] = $selected;
		
		$PARS['{NAME}'] = 'Мои сотрудники';
		
		$workers_list = fetch_tpl($PARS, $option_disabled_tpl).$workers_list;
	}
	
	if($current_user_id==$show_user_id)
	{
		$show_what = fetch_tpl($PARS, $show_what_mine_tpl);
	}
	else
	{
		$user_obj->fill_user_data($show_user_id);
		
		$name = $user_obj->get_user_name();
		$middlename = $user_obj->get_user_middlename();
		
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
		$PARS['{USER_NAME}'] = $name[0].'.';
		$PARS['{USER_MIDDLENAME}'] = $middlename[0].'.';
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$show_what = fetch_tpl($PARS, $show_what_tpl);
	}
	
	// переход в свой календарь
	if($show_user_id!=$current_user_id)
	{
		$change_mine_cal = $change_mine_cal_tpl;
	}
	
	$PARS['{CHANCHE_MINE_CAL}'] = $change_mine_cal;
	
	$PARS['{WORKERS_LIST}'] = $workers_list;
	
	$PARS['{SHOW_WHAT}'] = $show_what;
	
	return fetch_tpl($PARS, $workers_select_tpl);
	 
}

// Получает список событий дня
function get_calendar_day_events_list($date, $user_id, $event_id)
{
	global $site_db, $current_user_id;
	
	$events_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/events_list.tpl');
	
	$tmp_arr = implode('-', $date);
	
	// Секунды даты старта и конца дня
	$start_day = to_mktime($date);
	$finish_day = to_mktime($date)+3600 * 24 - 1;
	
	 
	// Выбор событий
	$sql = "SELECT  i.*, j.category_color FROM ".EVCAL_TB." i
			LEFT JOIN tasks_calendar_of_events_categories j ON j.category_id=i.category_id
			WHERE i.user_id='$user_id' AND i.deleted <> 1 AND
			((i.event_start_date>='$start_day' AND i.event_start_date<='$finish_day') || (i.event_finish_date>='$start_day' AND i.event_finish_date<='$finish_day') || (i.event_start_date<='$start_day' AND i.event_finish_date>='$finish_day'))";
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		// Событие имеет старт в текущий день
		/*if($row['event_start_date']>=$start_day && $row['event_start_date']<=$finish_day)
		{
			$sort = 2;	
		}
		// Событие имеет завершение в текущий день
		else if($row['event_finish_date']>=$start_day && $row['event_finish_date']<=$finish_day)
		{
			$sort = 2;	
		}
		// Событие проходит весь день
		else if($row['event_start_date']<=$start_day && $row['event_finish_date']>=$finish_day)
		{
			$sort = 1;	
		}*/
		
		 $events_list_arr[$row['event_start_date'].'_'.$row['event_id']] =  fill_event_item($row, '', $date, $event_id);
		
		//$events_list_arr[$sort.'_'.$row['event_id']] =  fill_event_item($row, '', $date, $event_id);
	}
	
	if(!$events_list_arr)
	{
		$add_form_display = 'display:block';
		$event_list_display = 'display:none';
	}
	else
	{
		$add_form_display = 'display:none';
		$event_list_display = 'display:block';
	}
	
	// Сортируем по времени
	ksort($events_list_arr);
	
	// echo "<pre>", print_R($events_list_arr);
	$events_list = implode('', $events_list_arr); 
	
	$add_form = get_evcal_add_form(1, $date, $user_id);
	
	$PARS['{DATE_STR}'] = datetime($date, '%j %F %Y');
	
	$PARS['{EVENTS_LIST}'] = $events_list;
	
	$PARS['{ADD_FORM_DISPLAY}'] = $add_form_display;
	
	$PARS['{EVENT_LIST_DISPLAY}'] = $event_list_display;
	
	$PARS['{DATE}'] = $date;
	
	$PARS['{ADD_FORM}'] = $add_form;
		
	return fetch_tpl($PARS, $events_list_tpl);
}

function get_event_data($event_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT i.*, j.category_color FROM ".EVCAL_TB." i
			LEFT JOIN tasks_calendar_of_events_categories j ON j.category_id=i.category_id
			WHERE i.event_id='$event_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row;
}
// Заполняет элемент элемент события в списке
function fill_event_item($event_data, $form, $date, $active_event_id)
{
	global $site_db, $current_user_id;
	
	$events_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/events_list_item.tpl');
	
	$events_list_item_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/events_list_item_edit.tpl');
	
	$edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/edit_tools.tpl');
	
	$edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/edit_tools.tpl');
	
	$event_list_item_link_deal_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/event_list_item_link_deal.tpl');
	
	$event_list_item_link_project_doc_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/event_list_item_link_project_doc.tpl');
	
	$event_item_edit_cat_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/event_item_edit_cat.tpl');
	
	$event_offer_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/event_offer_btn.tpl');
	
	// Секунды даты старта и конца дня
	$start_day = to_mktime($date);
	$finish_day = to_mktime($date) + 3600 * 24 - 1;
	
	$tmp_start_month = datetime($event_data['event_start_date'], '%m');
	$tmp_start_day = datetime($event_data['event_start_date'], '%d');
	
	$tmp_finish_month = datetime($event_data['event_finish_date'], '%m');
	$tmp_finish_day = datetime($event_data['event_finish_date'], '%d');
	
	// Формируем вывод времени начала и конца события
	if($event_data['event_start_date']<=$start_day && $event_data['event_finish_date']>=$finish_day)
	{
		$date_start_formated = 'весь день';
		$date_finish_formated = '';
	}
	else if($event_data['event_start_date']<$start_day && $event_data['event_finish_date']<=$finish_day)
	{
		$date_start_formated = 'Конец';
		$date_finish_formated = datetime($event_data['event_finish_date'], '%G-%i', 1);
	}
	else if($event_data['event_start_date']>$start_day && $event_data['event_finish_date']>$finish_day)
	{
		$date_start_formated = 'Начало';
		$date_finish_formated = datetime($event_data['event_start_date'], '%G-%i', 1);
	}
	else
	{
		if($event_data['event_start_date']==$event_data['event_finish_date'])
		{
			$date_start_formated = datetime($event_data['event_start_date'], '%G-%i', 1);
			$date_finish_formated = '';
		}
		else
		{
			$date_start_formated = datetime($event_data['event_start_date'], '%G-%i', 1);
			$date_finish_formated = datetime($event_data['event_finish_date'], '%G-%i', 1);
		}
		 
	}
	
	 
	// Список часов СТАРТ
	$time_hour_list_start = get_event_hour_list(datetime($event_data['event_start_date'], '%G', 1));
	// Список минут СТАРТ
	$time_minutes_list_start = get_event_minutes_list((int)datetime($event_data['event_start_date'], '%i', 1));
	
	// Список часов ЗАВЕРШЕНИЕ
	$time_hour_finish = get_event_hour_list(datetime($event_data['event_finish_date'], '%G', 1));
	// Список минут ЗАВЕРШЕНИЕ
	$time_minutes_list_finish = get_event_minutes_list((int)datetime($event_data['event_finish_date'], '%i', 1));
	
	// Блок редактирования события 
	if($event_data['added_by_user_id']==$current_user_id)
	{
		$PARS['{EVENT_ID}'] = $event_data['event_id'];
		$edit_tools = fetch_tpl($PARS, $edit_tools_tpl);
	}
	
	// Подсвечиваем событие, по которому был клик
	if($active_event_id==$event_data['event_id'])
	{
		$active_class = "event_active";
	}
	
	// Ссылка на сделку
	if($event_data['event_type']==2 && $event_data['content_id'])
	{
	 	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deals.php';
		
		$deal_name = get_deal_name_by_deal_id($event_data['content_id']);
		$PARS['{DEAL_ID}'] = $event_data['content_id'];
		$PARS['{DEAL_NAME}'] = $deal_name;
		$content_event_name = fetch_tpl($PARS, $event_list_item_link_deal_tpl);
		$event_name = $event_data['event_name'] ? $event_data['event_name'] : '';
	}
	// ссылка на мероприятие в проекте
	else if($event_data['event_type']==3 && $event_data['content_id'])
	{
	 	include_once($_SERVER['DOCUMENT_ROOT'].'/ext/cfr/includes/functions_cfr_projects.php');
			
		$doc_data = cfr_get_case_doc_data($event_data['content_id']);
			
		$PARS['{ID}'] = $event_data['content_id'];
		$PARS['{CASE_ID}'] = $doc_data['case_id'];
		 
		$content_event_name = fetch_tpl($PARS, $event_list_item_link_project_doc_tpl);
		$event_name = $event_data['event_name'] ? $event_data['event_name'] : '';
	}
	else
	{
		$event_name = $event_data['event_name'];
		$content_event_name = '';
	}
		
	
	// Автор
	$author = get_formate_user_name($event_data['added_by_user_id']);
		
	// список категорий
	$categories_list = get_evcal_categories_list($event_data['user_id'], $event_data['category_id']);
	
	
	$event_class = '';
	$category_color = '';
	if(!$event_data['category_id'])
	{
		$event_class = get_event_type_class_by_type_id($event_data['event_type']);
	}
	else
	{
		$category_color = 'background-color:#'.$event_data['category_color'];
	}
	
	if($event_data['event_type']==1)
	{
		// селект выбора категорий
		$PARS['{EVENT_ID}'] = $event_data['event_id'];
		$event_item_edit_cat_row = fetch_tpl($PARS, $event_item_edit_cat_tpl);
	}
	
	if($event_data['user_id']==$current_user_id)
	{
		$PARS['{EVENT_ID}'] = $event_data['event_id'];
		$offer_event_btn =  fetch_tpl($PARS, $event_offer_btn_tpl);
	}
	
	// список - "напомнить за несколько дней"
	$reminder_list = get_reminder_options_list($event_data['reminder_for_days']);
	 
	$PARS['{OFFER_EVENT_BTN}'] = $offer_event_btn;
	
	$PARS['{CATEGORY_ROW}'] = $event_item_edit_cat_row;
	
	$PARS['{CATEGORIES_LIST}'] = $categories_list;
		
	$PARS['{EVENT_ID}'] = $event_data['event_id'];
	
	$PARS['{EVENT_NAME}'] = $event_name;
	$PARS['{CONTENT_EVENT_NAME}'] = $content_event_name;
	
	$PARS['{EVENT_DESC_STR}'] = nl2br($event_data['event_desc']);
	
	$PARS['{ACTIVE_CLASS}'] = $active_class;
	
	$PARS['{EVENT_ID}'] = $event_data['event_id'];
	
	$PARS['{AUTHOR}'] = $author;
	
	$PARS['{EVENT_DESC}'] = $event_data['event_desc'];
	
	$PARS['{DATE_START_FORMATED}'] = $date_start_formated;
	
	$PARS['{DATE_FINISH_FORMATED}'] = $date_finish_formated;
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	$PARS['{TIME_HOURS_LIST_START}'] = $time_hour_list_start;
	$PARS['{TIME_MINUTES_LIST_START}'] = $time_minutes_list_start;
	
	$PARS['{TIME_HOURS_LIST_FINISH}'] = $time_hour_finish;
	$PARS['{TIME_MINUTES_LIST_FINISH}'] = $time_minutes_list_finish;
	
	$PARS['{DATE_START_RUS}'] = datetime($event_data['event_start_date'], '%d.%m.%Y', 1);
	$PARS['{DATE_FINISH_RUS}'] = datetime($event_data['event_finish_date'], '%d.%m.%Y', 1);
	
	$PARS['{EVENT_TYPE_CLASS}'] = $event_class;
	$PARS['{CATEGORY_COLOR}'] = $category_color;
	
	$PARS['{REMINDER_LIST}'] = $reminder_list;
	
	$PARS['{DATE}'] = $date;
	
	if($form==1)
	{
		return fetch_tpl($PARS, $events_list_item_edit_tpl);
	}
	else
	{
		return fetch_tpl($PARS, $events_list_item_tpl);
	}
}



// ВОзвращает массив дней месяца, в которых есть события
function get_month_events($date, $user_id, $types_option, $types_default)
{
	global $site_db, $current_user_id;
	
	$event_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/event_item.tpl');
	
	 
	$tmp = split('-', $date);
	
	$start_date = to_mktime($date);
	$finish_date = to_mktime($tmp[0].'-'.$tmp[1].'-'.date('t', to_mktime($date)).' 23:59:59');
	
	if($types_option)
	{
		$types_filter[] = ' i.category_id IN('.implode(',', $types_option).')';
	}
	
	if($types_default)
	{
		$types_filter[] = ' i.event_type IN('.implode(',', $types_default).') AND i.category_id=0';
	}
	
	if($types_filter)
	{
		$and_types = ' AND ('.implode(' OR ', $types_filter).')';
	}
	
	if(!$types_filter)
	{
		return array();
	}
	 
	// Выбор событий месяца
	$sql = "SELECT i.*, j.category_color FROM ".EVCAL_TB." i
			LEFT JOIN tasks_calendar_of_events_categories j ON j.category_id=i.category_id
			WHERE i.user_id='$user_id' AND i.deleted <> 1 AND ((i.event_start_date>='$start_date' AND i.event_start_date<='$finish_date') || (i.event_finish_date>='$start_date' AND i.event_finish_date<='$finish_date') || (i.event_start_date<='$start_date' AND i.event_finish_date>='$finish_date')) $and_types";
	 
	$res = $site_db->query($sql);
	 
	$events = array();
			
	while($row=$site_db->fetch_array($res))
	{//echo date('Y-m-d H:i:s', $row['event_finish_date']);
		
		$event_class = '';
		$category_color = '';
		if(!$row['category_id'])
		{
			$event_class = get_event_type_class_by_type_id($row['event_type']);
		}
		else
		{
			$category_color = 'background-color:#'.$row['category_color'];
		}
		
		$PARS['{EVENT_ID}'] = $row['event_id'];
		
		$PARS['{EVENT_NAME}'] = $row['event_name'];
		
		$PARS['{EVENT_TYPE_CLASS}'] =  $event_class;
		
		$PARS['{CATEGORY_COLOR}'] =  $category_color;
		 
		$event_item = fetch_tpl($PARS, $event_item_tpl);
		
		$events[] = array('date_start' => date('Y-m-d H:i:s', $row['event_start_date']), 'date_finish' => date('Y-m-d H:i:s', $row['event_finish_date']), 'event_item' => to_iconv($event_item));
		
		//array_push($events, array(date('Y-m-d H:i:s', $row['event_start_date']), date('Y-m-d H:i:s', $row['event_finish_date'])));
	}
	
	return $events;
}

// Вовзращет класс для элемента события в календаре
function get_event_type_class_by_type_id($type)
{
	switch($type)
	{
		case 3:
			return 'ev_item_3';
		break;
		
		case 2:
			return 'ev_item_2';
		break;
		
		default:
			return 'ev_item_1';
		break;
	}
}

// Список часов для селекта
function get_event_hour_list($hour)
{
	$seconds = 0;
	
	for($i=0; $i < 24; $i++)
	{
		$time_arr = sec_to_date_words($seconds, 0, 1);
		
		$time =  $time_arr['hours'];
		
		$time =  $time_arr['hours'] < 10 ? '0'.$time_arr['hours'] : $time_arr['hours'];
		 
		$selected = '';
		
		if($hour!='' && $hour==$time_arr['hours'])
		{
			$selected = 'selected="selected"';
		}
		else if($hour=='')
		{
			 $selected = $i == 9 ? 'selected="selected"' : '';
		}
		
		$PARS2['{NAME}'] = $time;
				
		$PARS2['{VALUE}'] = $seconds;
				
		$PARS2['{SELECTED}'] = $selected;
				
		$time_hour_list .= fetch_tpl($PARS2, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl'));
		
		$seconds += 3600;
	}
	
	return $time_hour_list;
}
// Список минут для селекта
function get_event_minutes_list($minutes)
{
	$seconds = 0;
	for($i=0; $i < 12; $i++)
	{
		$time_arr = sec_to_date_words($seconds, 0, 1);
		
		$time =  $time_arr['min'] < 10 ? '0'.$time_arr['min'] : $time_arr['min'];
		  
		$selected = '';
		if($minutes!='' && $time_arr['min']==$minutes)
		{
			$selected = 'selected="selected"';
		}
		
		$PARS2['{NAME}'] = $time;
				
		$PARS2['{VALUE}'] = $seconds;
				
		$PARS2['{SELECTED}'] = $selected;
				
		$time_minutes_list .= fetch_tpl($PARS2, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl'));
		
		$seconds += 300;
	}
	
	return $time_minutes_list;
}

// Форма доабвления события
function get_evcal_add_form($form, $date, $user_id, $link_content = array())
{
	$add_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/add_form.tpl');
	$add_form_1_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/add_form_1.tpl');
	$add_form_event_cell_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/add_form_event_cell.tpl');
	
	// Список часов
	$time_hour_list = get_event_hour_list();
	// Список минут
	$time_minutes_list = get_event_minutes_list();
	
	// список категорий
	$categories_list = get_evcal_categories_list($user_id);
	
	// список - "напомнить за несколько дней"
	$reminder_list = get_reminder_options_list();
	
	$PARS['{REMINDER_LIST}'] = $reminder_list;
	
	$PARS['{ADD_FORM}'] = $is_wks;
	
	$PARS['{DATE}'] = $date;
	
	$PARS['{DATE_RUS}'] = datetime($date, '%d.%m.%Y');
	
	$PARS['{TIME_HOURS_LIST}'] = $time_hour_list;
	
	$PARS['{TIME_MINUTES_LIST}'] = $time_minutes_list;
	
	$PARS['{PARS}'] = $link_content['id'].'|'.$link_content['type'];
	
	$PARS['{EVCAL_USER_ID}'] = $user_id;
	
	$PARS['{CATEGORIES_LIST}'] = $categories_list;
	
	if($form==1)
	{
		return fetch_tpl($PARS, $add_form_event_cell_tpl);
	}
	else if($form==2)
	{
		return fetch_tpl($PARS, $add_form_1_tpl);
	}
	else
	{
		return fetch_tpl($PARS, $add_form_tpl);
	}
	 
}

function get_evcal_categories_list($user_id, $category_id)
{
	global $site_db, $current_user_id;
	
	$option_tag_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	$sql = "SELECT * FROM tasks_calendar_of_events_categories WHERE user_id='$user_id' AND deleted=0 ORDER by category_id";
	
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{ 
		if($row['category_id']==$category_id)
		{
			$selected = 'selected="selected"';
		}
		else
		{
			$selected = '';
		}
		$PARS['{SELECTED}'] = $selected;
		
		$PARS['{VALUE}'] = $row['category_id'];
		
		$PARS['{NAME}'] = $row['category_name'];
		
		$list .= fetch_tpl($PARS, $option_tag_tpl);
	}
	
	return $list;
}

// Блок уведомления о новых и ближайших событиях
function fill_evcal_notice_block($user_id, $tpl_mode)
{
	global $site_db, $current_user_id;
	
	if($tpl_mode==1)
	{
		$notice_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/notice_block_1.tpl');
	}
	else
	{
		$notice_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/notice_block.tpl');
	}
	
	$notice_block_will_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/notice_block_will_item.tpl');
	
	$notice_block_now_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/notice_block_now_item.tpl');
	
	$notice_block_now_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/notice_block_now.tpl');
	$notice_block_will_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/notice_block_will.tpl');
	
	// Текущая дата
	$actual_time = time();
	
	// Дата будущего уведомления
	$will_time = time() + 3600 * 24;
	
	// Выбор текущих и предстоящих событий
	$sql = "SELECT * FROM ".EVCAL_TB." WHERE deleted<>1 AND user_id='$user_id' 
	AND (
	(event_start_date<'$actual_time' AND event_finish_date>'$actual_time') 
	OR 
	(event_start_date>'$actual_time' AND event_start_date < '$will_time')
	)";
	 
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$events_arr[$row['event_id']] = $row;
	}
	
	foreach($events_arr as $event_id => $event_data)
	{
		// События сейчас
		if($event_data['event_start_date']<$actual_time && $event_data['event_finish_date']>$actual_time)
		{  
			$PARS['{EVENT_DATE}'] = date('d.m.Y в H:i', $event_data['event_start_date']);
	
			$PARS['{EVENT_NAME}'] = $event_data['event_name'];
			
			$events_list_now[$event_data['event_start_date'].'_'.$event_id] = fetch_tpl($PARS, $notice_block_now_item_tpl);
		}
		// Будущие события
		else if($event_data['event_start_date'] > $actual_time && $event_data['event_start_date'] < $will_time)
		{
			$PARS['{EVENT_DATE}'] = date('d.m.Y в H:i', $event_data['event_start_date']);
	
			$PARS['{EVENT_NAME}'] = $event_data['event_name'];
	
			$events_list_will[$event_data['event_start_date'].'_'.$event_id] = fetch_tpl($PARS, $notice_block_will_item_tpl);
		}
	}
	
	if($events_list_now)
	{
		asort($events_list_now);
		$PARS['{EVENTS_LIST}'] = implode('', $events_list_now);
		$events_list_now = fetch_tpl($PARS, $notice_block_now_tpl);
	}
	if($events_list_will)
	{
		asort($events_list_will);
		$PARS['{EVENTS_LIST}'] = implode('', $events_list_will);
		$events_list_will = fetch_tpl($PARS, $notice_block_will_tpl);
	}
	
	
	if(!$events_list_now && !$events_list_will && !$tpl_mode)
	{
		return '';
	}
	else if(!$events_list_now && !$events_list_will && $tpl_mode==1)
	{
		$events_list_now = 'Ближайших событий нет.';
	}
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{EVENTS_LIST_NOW}'] = $events_list_now;
	
	$PARS['{EVENTS_LIST_WILL}'] = $events_list_will;
	
	return fetch_tpl($PARS, $notice_block_tpl);
}

// Удаляем контентное событие из календаря
function delete_evcal_content_event($type, $content_id, $user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "DELETE FROM ".EVCAL_TB." WHERE content_id='$content_id' AND event_type='$type' AND user_id='$user_id'";
	$site_db->query($sql);
}

// Добавляем контентное(напоминание сделок и тп) событие в календарь
function add_evcal_content_event($type, $content_id, $user_id, $by_user_id, $event_name, $event_desc, $event_start_date, $event_finish_date)
{
	global $site_db, $current_user_id;
	
	$array_types = array('evcal' => 1, 'deals' => 2);
	
	
	// Добавляем запись о заместителе
	$sql = "INSERT INTO ".EVCAL_TB." 
			SET event_name='$event_name', content_id='$content_id', event_desc='$event_desc', event_type='$type', user_id='$user_id', event_start_date='$event_start_date', event_finish_date='$event_finish_date', date_add=NOW(), added_by_user_id='$by_user_id'";
	
	$site_db->query($sql);
}

function get_offer_event_form($event_id)
{
	global $site_db, $current_user_id;
	
	$event_offer_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/event_offer_form.tpl');
	
	$event_data = get_event_data($event_id);
	
	if($event_data['user_id']!=$current_user_id)
	{
		return '';
	}
	
	$PARS['{EVENT_ID}'] = $event_id;
	
	$PARS['{EVENT_NAME}'] = $event_data['event_name'];
	
	return fetch_tpl($PARS, $event_offer_form_tpl);
}

function delete_event_offer($offer_id, $user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "DELETE FROM tasks_calendar_of_events_offers WHERE offer_id='$offer_id' AND user_id='$current_user_id'";
		 
	$site_db->query($sql);
	
	if(!mysql_error())
	{
		return 1;
	}
}

function get_new_events_notices_count($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM tasks_calendar_of_events_offers i
			LEFT JOIN tasks_calendar_of_events j ON i.event_id=j.event_id
			WHERE j.deleted=0 AND i.user_id='$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Уведомление планерки
function fill_evcal_popup()
{
	global $site_db, $current_user_id, $user_obj;
	
	$evcal_popup_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/evcal_popup.tpl');
	
	// Блок уведомления текущих и ближайших событий
	$notice_block = get_evcal_notice_block($current_user_id, 1);
		 
	$PARS['{NOTICE_BLOCK}'] = $notice_block; 
	
	$PARS['{USER_ID}'] = $current_user_id;
	
	return fetch_tpl($PARS, $evcal_popup_tpl);
}

// отметить уведомление события
function noticed_event($event_id, $noticed_type)
{
	global $site_db, $current_user_id;
	
	if($noticed_type)
	{
		$noticed = $noticed_type;
	}
	else
	{
		$noticed = 1;
	}
	
	$sql = "UPDATE tasks_calendar_of_events SET noticed='$noticed' WHERE event_id='$event_id'";
	
	$res = $site_db->query($sql);
}
// Блок уведомления о новых и ближайших событиях
function get_evcal_notice_block($user_id, $tpl_mode)
{
	global $site_db, $current_user_id;
	
	$popup_future_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/popup_future_list.tpl');
	$popup_actual_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/popup_actual_list.tpl');
	$popup_past_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/popup_past_list.tpl');
    $notice_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/notice_block_1.tpl');
	$popup_list_sep_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/popup_list_sep.tpl');
	$evcal_popup_no_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/evcal_popup_no.tpl');

	
	// Начинаем с даты 3 дня назад, чтобы показать прошедшие события
	$s_time = time()-3600*24*3;
	$actual_time = time();
	
	// Дата будущего уведомления
	$will_time = time() + 3600 * 24;
	
	// Будушие события
	$sql = "SELECT * FROM ".EVCAL_TB." WHERE deleted<>1 AND user_id='$user_id' 
			AND ((reminder_date > 0 AND reminder_date < '$actual_time' AND event_start_date > '$actual_time')
			OR (reminder_date=0 AND event_start_date > '$actual_time' AND event_start_date < '$will_time'))
			";

	 
	$res = $site_db->query($sql);
			 
	while($event_data=$site_db->fetch_array($res))
	{
		if(!$event_data['noticed'])
		{
			noticed_event($event_data['event_id']);
		}
		
		$not_noticed = 0;
		
		if(!$event_data['noticed'])
		{
			$not_noticed = 1;
		}
		
		$events_list_will[$event_data['event_start_date'].'_'.$event_data['event_id']] = fill_evcal_notice_block_list_item($event_data, $not_noticed);
	}
	
	// Текущие события
	$sql = "SELECT * FROM ".EVCAL_TB." WHERE deleted<>1 AND user_id='$user_id' 
			AND event_start_date<'$actual_time' AND event_finish_date>'$actual_time'";
	
	$res = $site_db->query($sql);
			
	while($event_data=$site_db->fetch_array($res))
	{
		if(!$event_data['noticed'] || $event_data['noticed']!=2)
		{
			noticed_event($event_data['event_id'], 2);
		}
		
		$not_noticed = 0;
		
		if(!$event_data['noticed'] || $event_data['noticed']!=2)
		{
			$not_noticed = 1;
		}
		
		$events_list_now[$event_data['event_start_date'].'_'.$event_data['event_id']] = fill_evcal_notice_block_list_item($event_data, $not_noticed);
	}
	
	// Прошедшие события
	$sql = "SELECT * FROM ".EVCAL_TB." WHERE deleted<>1 AND user_id='$user_id' 
			AND event_finish_date<'$actual_time' AND event_finish_date>'$s_time' AND hide!=1";
	
	$res = $site_db->query($sql);
			
	while($event_data=$site_db->fetch_array($res))
	{
		$not_noticed = 0;
		
		if(!$event_data['noticed'] || $event_data['noticed']!=2)
		{
			noticed_event($event_data['event_id'], 2);
		}
		
		if(!$event_data['noticed'] || $event_data['noticed']!=2)
		{
			$not_noticed = 1;
		}
		
		$events_list_past[$event_data['event_start_date'].'_'.$event_data['event_id']] = fill_evcal_notice_block_list_item($event_data,$not_noticed,1);
	}
	
	
	if($events_list_will)
	{
		ksort($events_list_will);
		$PARS['{EVENTS_LIST_FUTURE}'] = implode('', $events_list_will);
		$future_list = fetch_tpl($PARS, $popup_future_list_tpl);
	}
	
	if($events_list_now)
	{
		ksort($events_list_now);
		$PARS['{EVENTS_LIST_ACTUAL}'] = implode('', $events_list_now);
		$actual_list = fetch_tpl($PARS, $popup_actual_list_tpl);
	}
	
	if($events_list_past)
	{
		krsort($events_list_past);
		$PARS['{EVENTS_LIST_PAST}'] = implode('', $events_list_past);
		$past_list = fetch_tpl($PARS, $popup_past_list_tpl);
	}
	
	// разделители
	if($events_list_will && $events_list_now)
	{
		$sep_1 = $popup_list_sep_tpl;
	}
	
	if(($events_list_will || $events_list_now) && $events_list_past)
	{
		$sep_2 = $popup_list_sep_tpl;
	}
	
	if($events_list_will || $events_list_now || $events_list_past)
	{
		$sep_3 = $popup_list_sep_tpl;
	}
	
	if(!$events_list_will && !$events_list_now && !$events_list_past)
	{
		return $evcal_popup_no_tpl;
	}
	return  $actual_list.$sep_1.$future_list.$sep_2.$past_list.$sep_3;
}

// элемент события
function fill_evcal_notice_block_list_item($event_data, $visible_not_confirm, $is_history)
{
	$popup_notice_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/popup_notice_item.tpl');
	$hide_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/evcal/hide_btn.tpl');
	
	if($visible_not_confirm)
	{
		$notice_class = 'not_confirm';	
	}
	
	if($is_history)
	{
		$PARS['{EVENT_ID}'] = $event_data['event_id'];
		$hide_btn = fetch_tpl($PARS, $hide_btn_tpl);
	}
	
	$PARS['{EVENT_ID}'] = $event_data['event_id'];
	$PARS['{EVENT_DATE}'] = date('d.m.Y в H:i', $event_data['event_start_date']);
	$PARS['{EVENT_NAME}'] = $event_data['event_name'];
	$PARS['{NOTICE_CLASS}'] = $notice_class;
	$PARS['{HIDE_BTN}'] = $hide_btn;
	
	return fetch_tpl($PARS, $popup_notice_item_tpl);
}

// Опции уведомления
function get_remind_data()
{
	return array('1' => 1, '2' => 2, '3' => 3);
}
// список опций по выбору напоминаний
function get_reminder_options_list($id)
{
	global $site_db, $current_user_id;
	
	// опции по уведомлению
	$data = get_remind_data();
	
	foreach($data as $i)
	{
		$selected = $id == $i ? 'selected="selected"' : '';
		
		$PARS2['{NAME}'] = $i;
				
		$PARS2['{VALUE}'] = $i;
				
		$PARS2['{SELECTED}'] = $selected;
				
		$list .= fetch_tpl($PARS2, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl'));
	}
	
	return $list;
}

//дата 
function get_reminder_date($event_start_date, $event_reminder)
{
	// опции по уведомлению
	$data = get_remind_data();
	
	if(!array_key_exists($event_reminder, $data))
	{
		// первый элемент
		$event_reminder = key($event_reminder);
	}
	
	$reminder_date = $event_start_date - ($event_reminder * 3600 * 24);
	 
	return $reminder_date;
}
?>