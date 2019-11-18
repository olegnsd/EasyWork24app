<?php
// ¬озвращает верхнюю панель управлени€
function fill_top_panel($o)
{
	global $site_db, $current_user_id, $user_obj;
	
	$top_panel_tpl = file_get_contents('templates/top_panel/top_panel.tpl');
	
	$top_panel_registration_btn_tpl = file_get_contents('templates/top_panel/top_panel_registration_btn.tpl');

	$user_obj->fill_user_data($current_user_id);
	
	// ≈сли у сотрудника есть возможность регистрироватьновых, выводим кнопку дл€ регистрации
	/*if($user_obj->get_user_registration_privilege())
	{
		$registration_btn = $top_panel_registration_btn_tpl;
		$margin_left_add_in_workers_block = '290px';
	}
	else
	{
		$margin_left_add_in_workers_block = '485px';
	}*/
	
	
	$PARS['{NAME}'] = $user_obj->get_user_name();
	
	$PARS['{CURRENT_USER}'] = $current_user_id;
	
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{USERSURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{USER_JOB_ID}'] = $user_obj->get_user_job_id();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{REGISTRATION_BTN}'] = $registration_btn;
	
	$PARS['{MAGRIN_LEFT_ADD_BLOCK}'] = $margin_left_add_in_workers_block;
	
	$PARS['{PLANNING_SESSION_NOTICE}'] = $planning_session_notice;
	
	return fetch_tpl($PARS, $top_panel_tpl);
}

// есть ли на данный момент текущие планерки
function get_actual_planning_sessions_count_label($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$notice_count_planning_session_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/top_panel/notice_count_planning_session.tpl');
	
	// ¬ыбор планерок, которые онлайн
	$sql = "SELECT COUNT(*) as count FROM ".MSGS_GROUPS_TB." i
			LEFT JOIN ".MSGS_GROUPS_USERS_TB." j ON i.group_id=j.group_id
			WHERE i.group_is_online = 1 AND j.user_id='$current_user_id'";
			
	$row = $site_db->query_firstrow($sql);
	
	$count = $row['count'];
	
	if(!$count)
	{
		return '';
	}
	 
	$PARS['{COUNT}'] = $count;
	
	
	return fetch_tpl($PARS, $notice_count_planning_session_tpl);
	
}

// есть ли на данный момент текущие планерки
function get_evcal_count_label($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$notice_count_cal_of_event_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/top_panel/notice_count_cal_of_event.tpl');
	
	$actual_time = time();
	$s_time = time()-3600*24*3;
	// ƒата будущего уведомлени€
	$will_time = time() + 3600 * 24;
	
	// Ѕудушие событи€
	$sql = "SELECT * FROM ".EVCAL_TB." WHERE deleted<>1 AND user_id='$user_id' 
			AND ((reminder_date > 0 AND reminder_date < '$actual_time' AND event_start_date > '$actual_time')
			OR (reminder_date=0 AND event_start_date > '$actual_time' AND event_start_date < '$will_time'))
			";

	 
	$res = $site_db->query($sql);
	
	$count_not_noticed = 0;
	$count_noticed = 0;
	$count = 0;
			 
	while($event_data=$site_db->fetch_array($res))
	{
		// еще не уведомлены о событии
		if($event_data['noticed']==0)
		{
			$count_not_noticed += 1;
		}
		// уведомлены о событии
		else if($event_data['noticed'])
		{
			$count_noticed += 1;
		}
		// общее число будущих событий
		$count += 1;
	}
	
	
	// к будущим событи€м добавл€ем текущие
	// “екущие событи€
	$sql = "SELECT * FROM ".EVCAL_TB." WHERE deleted<>1 AND user_id='$user_id' 
			AND event_start_date<'$actual_time' AND event_finish_date>'$actual_time'";
	
	$res = $site_db->query($sql);
	
	while($event_data=$site_db->fetch_array($res))
	{
		// еще не уведомлены о событии
		if(!$event_data['noticed'] || $event_data['noticed']!=2)
		{
			$count_not_noticed += 1;
		}
		// уведомлены о событии
		else if($event_data['noticed'])
		{
			$count_noticed += 1;
		}
		// общее число будущих событий
		$count += 1;
	}
 	
	
	// ѕрошедшие событи€
	$sql = "SELECT * FROM ".EVCAL_TB." WHERE deleted<>1 AND user_id='$user_id' 
			AND event_finish_date<'$actual_time' AND event_finish_date>'$s_time' AND hide!=1";
	
	$res = $site_db->query($sql);
			
	while($event_data=$site_db->fetch_array($res))
	{
		// еще не уведомлены о событии
		if(!$event_data['noticed'] || $event_data['noticed']!=2)
		{
			$count_not_noticed += 1;
		}
		// уведомлены о событии
		else if($event_data['noticed'])
		{
			$count_noticed += 1;
		}
		// общее число будущих событий
		//$count += 1;
	}
	
 	// если есть новые уведомлени€ 
 	if($count_not_noticed)
	{
		$nt_class = 'tn_nt';
		$count = $count_not_noticed;
	}
	 
	if(!$count)
	{
		return '';
	}
	 
	$PARS['{COUNT}'] = $count;
	$PARS['{NT_CLASS}'] = $nt_class;
	
	
	return fetch_tpl($PARS, $notice_count_cal_of_event_tpl);
	
}


function fill_top_panel_worktime_btn()
{
	global $site_db, $current_user_id, $user_obj;
	
	$wk_start_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/top_panel/wk_start.tpl');
	
	$wk_finish_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/top_panel/wk_finish.tpl');
	
	// ѕрвоер€ем последний статус ("Ќачал работь" или "–аботаь закончил")
	$user_last_status = get_last_user_activity_status($current_user_id);
	
	// ¬озвращаем кнопку "–аботать начал"
	if($user_last_status==1)
	{   
		$btn_tpl = $wk_finish_tpl;
	}
	// ¬озвращаем кнопку "–аботать закончил"
	else if($user_last_status==2 || !$user_last_status)
	{  
		$btn_tpl = $wk_start_tpl;
	}
	
	return $btn_tpl;
}
?>