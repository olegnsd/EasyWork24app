<?php
function fill_ucontrol()
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	$main_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/ucontrol.tpl');
	
	$top_menu_settings_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/top_menu_settings.tpl');
	
	$current_user_data = $current_user_obj->get_user_data(); 
	
	// Модуль контроль выводим людям, которые имеют полный доступ к профилям сотрудников
	if(!$current_user_data['is_full_access'])
	{
		header('Location: /');
		exit();
	}
	
	if($current_user_obj->get_is_admin())
	{
		$settings_top_menu = $top_menu_settings_tpl;
	}
	
	if(!$current_user_obj->get_is_admin() && $_GET['settings']==1)
	{
		header('Location: /ucontrol');
		exit();
	}
	
	// настройки
	if($_GET['settings']==1)
	{
		$content = fill_ucontrol_settings();
		
		$active_tab_2 = 'active';
		
		 
	}
	// контроль пользователя
	else
	{
		$user_id = value_proc($_GET['id']);
		
		$content = fill_ucontrol_content($user_id);
		
		$active_tab_1 = 'active';
	}
	
	$PARS['{SETTINGS_TOP_MENU}'] = $settings_top_menu;
	$PARS['{ACTIVE_TAB_1}'] = $active_tab_1;
	$PARS['{ACTIVE_TAB_2}'] = $active_tab_2;
	 
	
	$PARS['{CONTENT}'] = $content;
	 
	return fetch_tpl($PARS, $main_tpl);
}

// Страница статистики по пользователю
function fill_ucontrol_content($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$selected_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/selected_form.tpl');
	
	$ucontrol_content_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/ucontrol_content.tpl');
	
	$tabs_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/tabs.tpl');
	
	if($user_id)
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($user_id);
		
		$user_data = $user_obj->get_user_data();
		
		// сипюни телефон
		$sipuni_phone = $user_obj->get_user_data_par($user_data['data'], 'sipuni_phone');
		 
		$PARS['{USER_ID}'] = $client_data['user_id'];
			
		$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
		$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{SIPUNI_PHONE}'] = $sipuni_phone;
		
		$PARS['{USER_ID}'] = $user_id;
		
		$selected_form = fetch_tpl($PARS, $selected_form_tpl);
		$tabs = fetch_tpl($PARS, $tabs_tpl);
	}
	
	$PARS['{SELECTED_FORM}'] = $selected_form;
	
	$PARS['{CHART}'] = '';
	
	$PARS['{TABS}'] = $tabs;
	
	return fetch_tpl($PARS, $ucontrol_content_tpl);
}

// Страница редактирования настроек модуля контроля
function fill_ucontrol_settings($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	$ucontrol_settings_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/ucontrol_settings.tpl');
	
	$sql = "SELECT * FROM tasks_ucontrol_settings WHERE name='sipuni'";
		
	$row = $site_db->query_firstrow($sql);
	
	$sipuni_row = unserialize($row['settings']);
	 
	$PARS['{SIPUNI_USER_ID}'] = $sipuni_row['option_sipuni_id'];
	
	$PARS['{SIPUNI_SECRET_KEY}'] = $sipuni_row['option_secret_key'];
	
	$PARS['{TABS}'] = $tabs;
	
	return fetch_tpl($PARS, $ucontrol_settings_tpl);
}

function ucontrol_show_stat($user_id, $date, $to_chart)
{
	global $site_db, $current_user_id, $user_obj;
	
	$show_stat_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/show_stat.tpl');
	
	$chart = stat_one_day_charts($user_id, $date, $to_chart);
	
	$PARS['{CHART}'] = $chart;
	
	return fetch_tpl($PARS, $show_stat_tpl);
}

// Возвращает график изменения статусов сделок пользователем
function stat_one_day_charts($user_id, $date, $to_chart)
{
	global $site_db, $current_user_id, $user_obj;
	
	$chart_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/one_day.tpl');
	
	$chart_series_sipuni_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/chart_series_sipuni.tpl');
	$chart_series_inbox_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/chart_series_inbox.tpl');
	$chart_series_outbox_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/chart_series_outbox.tpl');
	$chart_series_task_reports_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/chart_series_task_reports.tpl');
    $chart_series_deals_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/chart_series_deals_edit.tpl');
    $chart_series_deals_add_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/chart_series_deals_add.tpl');
    $chart_work_reports_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/chart_work_reports.tpl');


	// Период времени 
	$date_from = $date.' 00:00:00';
	$date_to = $date.' 23:59:59';
	
	
	
	$mk_fordate = to_mktime($date);
	
	//$date = to_mktime(date('Y-m-d'))- 3600 * 24; //3600*24*30;

	$date_from_mk = to_mktime($date.' 00:00');
	
	$date_to_mk = to_mktime($date.' 23:59');
	
	$series_outbox = '[]';
	
	// Исх. сообщения
	if($to_chart['outbox'])
	{
		$sql = "SELECT * FROM tasks_dialogs_messages WHERE user_id='$user_id' AND message_date >= '$date_from_mk' AND message_date<='$date_to_mk'";
		
		$res = $site_db->query($sql);
		
		$statuses_arr = array();
			   
		while($row=$site_db->fetch_array($res, 1))
		{
			$date_hour = (int)get_part_from_date(date('Y-m-d H:i', $row['message_date']), 'h');
			 
			$statuses_arr[$date_hour] += 1;
		}
		
		$SERIES_DATA_ARR = array();
		
		// Формируем ось х
		for($i=0; $i<24; $i++)
		{
			// Если за текущий час есть обновленные статусы, то суммируем их, иначе ставим 0
			if($statuses_arr[$i])
			{
				$SERIES_DATA_ARR[$i] = $statuses_arr[$i];
			}
			else
			{
				$SERIES_DATA_ARR[$i] = 0;
			}
		}
		
	
		 
		if($SERIES_DATA_ARR)
		{
			$series_outbox = '['.implode(',', $SERIES_DATA_ARR).']';
		}
		
		// вставляем блок сипуни для графика
		$chart_outbox_series = fetch_tpl($PARS, $chart_series_outbox_tpl);
	}
	
	$series_inbox = '[]';
	
	// Вх. сообщения
	if($to_chart['inbox'])
	{
		$sql = "SELECT * FROM tasks_dialogs_messages i
				RIGHT JOIN tasks_dialogs_message_to_user j ON i.message_id=j.message_id
				WHERE j.user_id='$user_id' AND i.user_id!='$user_id' AND i.message_date >= '$date_from_mk' AND i.message_date<='$date_to_mk'";
		 
		$res = $site_db->query($sql);
			
		$statuses_arr = array();
		   
		while($row=$site_db->fetch_array($res, 1))
		{
			$date_hour = (int)get_part_from_date(date('Y-m-d H:i', $row['message_date']), 'h');
			 
			$statuses_arr[$date_hour] += 1;
		}
		
		$SERIES_DATA_ARR = array();
		
		// Формируем ось х
		for($i=0; $i<24; $i++)
		{
			// Если за текущий час есть обновленные статусы, то суммируем их, иначе ставим 0
			if($statuses_arr[$i])
			{
				$SERIES_DATA_ARR[$i] = $statuses_arr[$i];
			}
			else
			{
				$SERIES_DATA_ARR[$i] = 0;
			}
		}
		
	
		 
		if($SERIES_DATA_ARR)
		{
			$series_inbox = '['.implode(',', $SERIES_DATA_ARR).']';
		}
		
		// вставляем блок сипуни для графика
		$chart_inbox_series = fetch_tpl($PARS, $chart_series_inbox_tpl);
	}

	$series_tasks = '[]';
	
	// Отчеты к задачам
	if($to_chart['task_reports'])
	{
		$sql = "SELECT * FROM tasks_tasks_reports WHERE report_user_id='$user_id' AND report_date >= '$date_from' AND report_date<='$date_to'";
		
		$res = $site_db->query($sql);
		
		$reports_arr = array();
			   
		while($row=$site_db->fetch_array($res, 1))
		{
			// Выбираем час из строки даты
			$date_hour = (int)get_part_from_date($row['report_date'], 'h');
			 
			$reports_arr[$date_hour] += 1;
		}
		
	
		
		// Формируем ось х
		for($i=0; $i<24; $i++)
		{
			// Если за текущий час есть обновленные статусы, то суммируем их, иначе ставим 0
			if($reports_arr[$i])
			{
				$SERIES_DATA_TASKS_ARR[$i] = $reports_arr[$i];
			}
			else
			{
				$SERIES_DATA_TASKS_ARR[$i] = 0;
			}
		}
		 
		if($SERIES_DATA_TASKS_ARR)
		{
			$series_tasks = '['.implode(',', $SERIES_DATA_TASKS_ARR).']';
		}
		 
		// вставляем блок сипуни для графика
		$chart_tasks_reports_series = fetch_tpl($PARS, $chart_series_task_reports_tpl);
	}

    $series_edit_deals = '[]';

    // Редактирование сделок
    if($to_chart['edit_deals'])
    {
        $sql = "SELECT * FROM tasks_deals_statuses WHERE user_id='$user_id' AND status_date >= '$date_from' AND status_date<='$date_to'";

        $res = $site_db->query($sql);

        $edit_deals_arr = array();

        while($row=$site_db->fetch_array($res, 1))
        {
            // Выбираем час из строки даты
            $date_hour = (int)get_part_from_date($row['status_date'], 'h');

            $edit_deals_arr[$date_hour] += 1;
        }



        // Формируем ось х
        for($i=0; $i<24; $i++)
        {
            // Если за текущий час есть обновленные статусы, то суммируем их, иначе ставим 0
            if($edit_deals_arr[$i])
            {
                $SERIES_DATA_DEALS_EDIT_ARR[$i] = $edit_deals_arr[$i];
            }
            else
            {
                $SERIES_DATA_DEALS_EDIT_ARR[$i] = 0;
            }
        }

        if($SERIES_DATA_DEALS_EDIT_ARR)
        {
            $series_edit_deals = '['.implode(',', $SERIES_DATA_DEALS_EDIT_ARR).']';
        }

        // вставляем блок для графика
        $chart_deals_edit_series = fetch_tpl($PARS, $chart_series_deals_edit_tpl);
    }

    $series_add_deals = '[]';

    // Добавление сделок
    if($to_chart['add_deals'])
    {
        $sql = "SELECT * FROM tasks_deals WHERE user_id='$user_id' AND deal_date_add >= '$date_from' AND deal_date_add<='$date_to' AND deal_deleted=0";

        $res = $site_db->query($sql);

        $add_deals_arr = array();

        while($row=$site_db->fetch_array($res, 1))
        {
            // Выбираем час из строки даты
            $date_hour = (int)get_part_from_date($row['deal_date_add'], 'h');

            $add_deals_arr[$date_hour] += 1;
        }



        // Формируем ось х
        for($i=0; $i<24; $i++)
        {
            // Если за текущий час есть обновленные статусы, то суммируем их, иначе ставим 0
            if($edit_deals_arr[$i])
            {
                $SERIES_DATA_DEALS_ADD_ARR[$i] = $add_deals_arr[$i];
            }
            else
            {
                $SERIES_DATA_DEALS_ADD_ARR[$i] = 0;
            }
        }

        if($SERIES_DATA_DEALS_ADD_ARR)
        {
            $series_add_deals = '['.implode(',', $SERIES_DATA_DEALS_ADD_ARR).']';
        }

        // вставляем блок для графика
        $chart_deals_add_series = fetch_tpl($PARS, $chart_series_deals_add_tpl);
    }

    $series_work_reports = '[]';

    // Отчеты в круге обязанностей
    if($to_chart['work_reports'])
    {
        $sql = "SELECT * FROM tasks_user_works_reports WHERE report_from_user_id='$user_id' AND report_date >= '$date_from' AND report_date<='$date_to'";

        $res = $site_db->query($sql);

        $work_reports_arr = array();

        while($row=$site_db->fetch_array($res, 1))
        {
            // Выбираем час из строки даты
            $date_hour = (int)get_part_from_date($row['report_date'], 'h');

            $work_reports_arr[$date_hour] += 1;
        }



        // Формируем ось х
        for($i=0; $i<24; $i++)
        {
            // Если за текущий час есть обновленные статусы, то суммируем их, иначе ставим 0
            if($edit_deals_arr[$i])
            {
                $SERIES_DATA_WORK_REPORTS_ARR[$i] = $work_reports_arr[$i];
            }
            else
            {
                $SERIES_DATA_WORK_REPORTS_ARR[$i] = 0;
            }
        }

        if($SERIES_DATA_WORK_REPORTS_ARR)
        {
            $series_work_reports = '['.implode(',', $SERIES_DATA_WORK_REPORTS_ARR).']';
        }

        // вставляем блок для графика
        $chart_work_reports_series = fetch_tpl($PARS, $chart_work_reports_tpl);
    }




	// График время начала работы
	$sql = "SELECT * FROM tasks_users_activity WHERE user_id='$user_id' AND activity_date='$date' AND activity_status=1 ORDER by activity_id"; 
	
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		$hour = (int)date('H', $row['activity_datetime']);
		
		$hour_work_start[$hour] = $hour;
		
		// время начала работы
		if(!$work_start)
		{
			$work_start = date('H:i', $row['activity_datetime']);
		}
	}
	
	
	if(!$work_start)
	{
		$work_start = '-';
	}
	
	// строим данные для графика 
	for($i=0; $i<24; $i++)
	{
		if($hour_work_start[$i])
		{
			$SERIES_DATA_WORK_START_ARR[$i] = 0;
		}
		else
		{
			$SERIES_DATA_WORK_START_ARR[$i] = 'null';
		}
	}
	
	$series_work_start = '['.implode(',', $SERIES_DATA_WORK_START_ARR).']';
	
	// график конца работы
	$sql = "SELECT * FROM tasks_users_activity WHERE user_id='$user_id' AND activity_date='$date' AND activity_status=2 ORDER by activity_id DESC"; 
	
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		$hour = (int)date('H', $row['activity_datetime']);
		
		$hour_work_finish[$hour] = $hour;
		
		// время конца работы
		if(!$work_finish)
		{
			$work_finish = date('H:i', $row['activity_datetime']);
		}
	}
		 
	
	
	if(!$work_finish)
	{
		$work_finish = '-';
	}
	
	// строим данные для графика
	for($i=0; $i<24; $i++)
	{
		if($hour_work_finish[$i])
		{
			$SERIES_DATA_WORK_FINISH_ARR[$i] = 0;
		}
		else
		{
			$SERIES_DATA_WORK_FINISH_ARR[$i] = 'null';
		}
	}
	
	$series_work_finish = '['.implode(',', $SERIES_DATA_WORK_FINISH_ARR).']';
	
	$series_sipuni = '[]';

	// Заполянем объект пользователя
	$user_obj->fill_user_data($user_id);
	$user_data = $user_obj->get_user_data();
		
	// сипюни телефон
	$sipuni_phone = $user_obj->get_user_data_par($user_data['data'], 'sipuni_phone');
		
	if($to_chart['sipuni'] && $sipuni_phone)
	{
		$s_date = date('d.m.Y', $mk_fordate);
		
		// получает данные статистики с сервера
		$sipuni_output_arr = get_sipuni_stat_data($sipuni_phone, $s_date, $s_date);
		
		if($sipuni_output_arr['code']!='200')
		{
			$notices = $sipuni_output_arr['notice'];
		}
		
		foreach($sipuni_output_arr['output'] as $sipuni_data)
		{
			$tmp = split(';', $sipuni_data);
			
			$hour = (int)substr($tmp[2],11,2);
			
			if(!$hour)
			{
				continue;
			}
			
			$sipuni_hours_arr[$hour] += 1;

		}
		
		// Формируем ось х
		for($i=0; $i<24; $i++)
		{
			// Если за текущий час есть обновленные статусы, то суммируем их, иначе ставим 0
			if($sipuni_hours_arr[$i])
			{
				$SERIES_DATA_SIPUNI_ARR[$i] = $sipuni_hours_arr[$i];
			}
			else
			{
				$SERIES_DATA_SIPUNI_ARR[$i] = 0;
			}
		}
		 
		if($SERIES_DATA_SIPUNI_ARR)
		{
			$series_sipuni = '['.implode(',', $SERIES_DATA_SIPUNI_ARR).']';
		}
	
		// вставляем блок сипуни для графика
		$chart_sipuni_series = fetch_tpl($PARS, $chart_series_sipuni_tpl);
	}
	
	 
 	// Отображать в графике время старта 
	$date_start = get_date_utc_for_js_object($date);
	 
	$PARS['{SERIES_OUTBOX}'] = $series_outbox;
	$PARS['{SERIES_INBOX}'] = $series_inbox;
	$PARS['{SERIES_TASKS}'] = $series_tasks;
    $PARS['{SERIES_DEALS_EDIT}'] = $series_edit_deals;
    $PARS['{SERIES_DEALS_ADD}'] = $series_add_deals;
    $PARS['{SERIES_WORK_REPORTS}'] = $series_work_reports;
	 
	$PARS['{SERIES_DATE_START}'] = $date_start;
	
	$PARS['{SERIES_DATE_WORK_START}'] = $series_work_start;
	
	$PARS['{SERIES_DATE_WORK_FINISH}'] = $series_work_finish;
	
	$PARS['{SERIES_DATE_SIPUNI}'] = $series_sipuni;
	
	$PARS['{WORK_START}'] = $work_start;
	
	$PARS['{WORK_FINISH}'] = $work_finish;
	
	$PARS['{SIPUNI_SERIES_JS}'] = $chart_sipuni_series;
	$PARS['{INBOX_SERIES_JS}'] = $chart_inbox_series;
	$PARS['{OUTBOX_SERIES_JS}'] = $chart_outbox_series;
	$PARS['{TASKS_REPORTS_SERIES_JS}'] = $chart_tasks_reports_series;
    $PARS['{DEALS_EDIT_SERIES_JS}'] = $chart_deals_edit_series;
    $PARS['{DEALS_ADD_SERIES_JS}'] = $chart_deals_add_series;
    $PARS['{DEALS_WORK_REPORTS_JS}'] = $chart_work_reports_series;

	$PARS['{NOTICES}'] = $notices;
	
	return fetch_tpl($PARS, $chart_tpl);
}

function show_user_dialog_stat($user_id, $date)
{
	global $site_db, $current_user_id;
	
	$date = to_mktime(date('Y-m-d'))- 3600 * 24; //3600*24*30;

	$date_from = to_mktime(date('Y-m-d')) - 3600 * 24 * 30;
	
	$date_to = (to_mktime(date('Y-m-d')) - 3600 * 24 * 1) + (3600 * 24-1);
	
	echo date('y-m-d H:i', $date_from),' ',date('y-m-d H:i', $date_to), '<br><br>';;
	
	
	$sql = "SELECT * FROM tasks_dialogs_messages WHERE user_id='1' AND message_date >= '$date_from' AND message_date<='$date_to'";
	 
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		$words = explode(' ', $row['message_text']);
		
		foreach($words as $word)
		{
			 $words_arr[$word] += 1;
		}
		 
		//echo $row['message_text'],'<br>';
	}
	
	arsort($words_arr);
	//echo "<pre>", print_r($words_arr);
	
	$black = array('не','','все','в','я','на','и','что','это','есть','там','-','будет','по','за','а','с','у','=>','хорошо','как','если','ок','сейчас','Вы','можно','пока','или','понял','к','ну','чтобы','из','=>','него','щас','тут','нужно','давай','но','когда','он','очень','ага', '');
	
	foreach($words_arr as $word => $count)
	{
		if(in_array($word, $black))
		{
			continue;
		}
		if($count >= 1)
		{
			echo  $word.' - '.$count."<br>";
		}
		
		if($count >= 5)
		{
			$r[] = "'".$word."'";
		}
	}
}

function get_sipuni_stat($user_id, $sipuni_phone, $date_from, $date_to)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sipuni_tb_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/sipuni_tb.tpl');
	
	$sipuni_no_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/sipuni_no_list.tpl');
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($user_id);
		
	$user_data = $user_obj->get_user_data();
	
	// получает данные статистики с сервера
	$sipuni_output_arr = get_sipuni_stat_data($sipuni_phone, $date_from, $date_to);

	
	if($sipuni_output_arr['code']!='200')
	{
		$notice = $sipuni_output_arr['notice'];
		
		unset($sipuni_output_arr['output']);
	}
		
	//$output_arr = split('	
//', $output);	
	
	 // echo "<pre>", print_r($output_arr);
	//
	$num=1;
	foreach($sipuni_output_arr['output'] as $str)
	{
		if(!$str)
		{
			continue;
		}
		
		$td_arr = split(';', $str);
		
		//$tds_arr = array_map(function($n){ return "<td>".iconv('utf-8','cp1251//IGNORE', $n)."</td>";}, $tds_arr);
		
		if($num==1)
		{
			$th_arr = array_map(function($n){ if(trim($n)) return "<th>".iconv('utf-8','cp1251//IGNORE', $n)."</th>";}, $td_arr);
		}
		else
		{
			$td_arr = array_map(function($n){ 
				
				$n = iconv('utf-8','cp1251//IGNORE',$n);
				
				if($n=='отвечен')
				{
					$n = "<span class='gr'>$n</span>";
				}
				if($n=='не отвечен')
				{
					$n = "<span class='red'>$n</span>";
				}
				return "<td>".$n."</td>";
				
			}, $td_arr);
			$list .= "<tr class='task_it_row'>".implode('', $td_arr)."</tr>";
		}
		 
		$num++;
		//print_r($tds_arr1);
	}
	
	
	$th_list = "<tr>".implode('', $th_arr)."</tr>";
	
	if(!$list)
	{
		$list = $sipuni_no_list_tpl;
	}
	
	$PARS['{TH_LIST}'] = $th_list;
	
	$PARS['{LIST}'] = $list;
	
	$PARS['{NOTICE}'] = $notice;
	 
	return fetch_tpl($PARS, $sipuni_tb_tpl);

}

// получить параметр из настроек
function get_ucontrol_setting($name)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "SELECT * FROM tasks_ucontrol_settings WHERE name='$name'";	
	
	$row = $site_db->query_firstrow($sql);
	
	$settings = unserialize($row['settings']);
	
	return $settings;
}

// получает данные по сипуни с сервера
function get_sipuni_stat_data($sipuni_phone, $date_from, $date_to)
{
	$sipuni_notice_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/ucontrol/sipuni_notice.tpl');
	
	$sipuni_settings_arr = get_ucontrol_setting('sipuni');
	
	$user = ''.$sipuni_settings_arr['option_sipuni_id'].''; //'060957';
	$from = $date_from;
	$to = $date_to;
	$type = '0';
	$state = '0';
	$tree = '';
	$fromNumber = "".$sipuni_phone.""; //997
	$toNumber = '';
	$toAnswer = '';
	$anonymous = '1';
	$firstTime = '0';
	$secret = ''.$sipuni_settings_arr['option_secret_key'].''; //'izp811apthk9be29';
	
	$hashString = join('+', array($anonymous, $firstTime, $from, $fromNumber, $state, $to, $toAnswer, $toNumber, $tree, $type, $user, $secret));
	$hash = md5($hashString);
	

	$url = 'http://sipuni.com/api/statistic/export';
	$query = http_build_query(array(
		'anonymous' => $anonymous,
		'firstTime' => $firstTime,
		'from' => $from,
		'fromNumber' => $fromNumber,
		'state' => $state,
		'to' => $to,
		'toAnswer' => $toAnswer,
		'toNumber' => $toNumber,
		'tree' => $tree,
		'type' => $type,
		'user' => $user,
		'hash' => $hash,
	));
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$output = curl_exec($ch);
	
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	 
	if($httpcode!='200')
	{
		$sipuni_notice = $sipuni_notice_tpl;
	}
	
	curl_close($ch);
	 
	// file_put_contents($_SERVER['DOCUMENT_ROOT'].'/ff.csv', $output);
	
	/*$output = "Тип;Статус;Время;Схема;Откуда;Куда;Кто ответил;Продолжительность звонка;Продолжительность разговора;Время ответа;Оценка;ID записи;Метка;ID заказа звонка;;
	исходящий;отвечен;08.10.2015 16:31:03;8-499 KSRI Москва;997;89521152137;;180;171;9;;1444311063.117599;; 
	исходящий;отвечен;08.10.2015 15:50:35;8-499 KSRI Москва;997;74996867364;;13;9;4;;1444308635.116853;; 
	исходящий;отвечен;08.10.2015 15:34:57;8-499 KSRI Москва;997;89521152137;;33;3;30;;1444307697.116528;; 
	исходящий;отвечен;08.10.2015 15:17:44;8-499 KSRI Москва;997;84852544049;;764;751;13;;1444306664.116210;; 
	исходящий;отвечен;08.10.2015 14:51:20;8-499 KSRI Москва;997;89236615587;;35;20;15;;1444305080.115693;; 
	исходящий;отвечен;08.10.2015 14:27:30;8-499 KSRI Москва;997;89236615587;;179;162;17;;1444303650.115025;; 
	исходящий;отвечен;08.10.2015 13:40:29;8-499 KSRI Москва;997;89304040855;;265;244;21;;1444300829.113863;; 
	исходящий;отвечен;08.10.2015 13:35:59;8-499 KSRI Москва;997;89236615587;;150;142;8;;1444300559.113788;; 
	исходящий;отвечен;08.10.2015 13:35:22;8-499 KSRI Москва;997;89236615587;;27;16;11;;1444300522.113763;; 
	исходящий;отвечен;08.10.2015 13:06:09;8-499 KSRI Москва;997;79610704664;;343;331;12;;1444298769.113003;; 
	исходящий;отвечен;08.10.2015 12:00:39;8-499 KSRI Москва;997;89236615587;;638;626;12;;1444294839.111244;; 
	исходящий;отвечен;08.10.2015 11:59:05;8-499 KSRI Москва;997;89297473159;;71;49;22;;1444294745.111193;; 
	исходящий;отвечен;08.10.2015 11:37:15;8-499 KSRI Москва;997;89304040855;;800;782;18;;1444293435.110574;; 
	исходящий;отвечен;08.10.2015 11:22:13;8-499 KSRI Москва;997;89171725100;;816;803;13;;1444292533.110204;; 
	исходящий;не отвечен;08.10.2015 11:20:37;8-499 KSRI Москва;997;79084959445;;41;;;;1444292437.110169;; 
	исходящий;не отвечен;08.10.2015 11:18:53;8-499 KSRI Москва;997;79212619965;;47;;;;1444292333.110148;;
	 исходящий;отвечен;08.10.2015 11:05:38;8-499 KSRI Москва;997;79521152137;;91;69;22;;1444291538.109742;; 
	 исходящий;отвечен;08.10.2015 10:59:25;8-499 KSRI Москва;997;79632335224;;308;298;10;;1444291165.109544;;";*/
	
	//echo $output;
	
	$output_arr = split(chr(10), $output);
	
	return array('output' => $output_arr, 'code' => $httpcode,'notice' => $sipuni_notice);
}

function check_user_for_access_control($user_id)
{
	if(!$user_id)
	{
		return false;
	}
	
	return true;
}
?>