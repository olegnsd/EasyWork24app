<?php
// Левое меню
function fill_left_menu($o)
{
	global $site_db, $current_user_id, $users_for_access_to_content, $current_user_obj;
	
	$left_menu_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/left_menu/left_menu.tpl');	
	
	$ucontrol_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/left_menu/ucontrol.tpl');	
	
	$active_array = array('tasks'=>'', 'work'=>'', 'workers'=>'', 'boss'=>'', 'settings'=>'', 'msgs'=>'', 'files'=>'', 'personal' => '', 'contacts' => '', 'task_to_users' => '', 'deals' => '', 'clients' => '', 'goods' => '', 'money' => '', 'wktime' => '', 'cam' => '', 'auto' => '', 'colleagues' => '', 'finances' => '');
	 
	//if(!$_GET['id'])
	//{
		$active_array[$o] = 'left_menu_active';
	//}
	
	 
	if($o=='main')
	{
		$active_array['workers'] = 'left_menu_active';
	}
	if($o=='settings' && $_GET['id'])
	{
		$active_array['settings'] = '';
	}
	if($o=='contacts' && $_GET['user_id']!=$current_user_id)
	{
		$active_array['contacts'] = '';
	}
	if($o=='work' && $_GET['id'])
	{
		$active_array['work'] = '';
	}
	if($o=='tasks' && $_GET['id'])
	{
		$active_array['tasks'] = '';
	}
	if($o=='goods' && $_GET['user_id']!=$current_user_id)
	{
		$active_array['goods'] = '';
	}
	if($o=='money' && $_GET['user_id'] && $_GET['user_id']!=$current_user_id)
	{
		$active_array['money'] = '';
	}
	if($o=='wktime' && $_GET['user_id'] && $_GET['user_id']!=$current_user_id)
	{
		$active_array['wktime'] = '';
	}
	
	$current_user_data = $current_user_obj->get_user_data(); 
	
	// Модуль контроль выводим людям, которые имеют полный доступ к профилям сотрудников
	if($current_user_data['is_full_access'])
	{
		$ucontrol = $ucontrol_tpl;
	}
	 
	$PARS['{UCONTROL}'] = $ucontrol;
	 
	$PARS['{ACTIVE_1}'] = $active_array['tasks'];
	$PARS['{ACTIVE_2}'] = $active_array['work'];
	$PARS['{ACTIVE_3}'] = $active_array['workers'];
	$PARS['{ACTIVE_4}'] = $active_array['boss'];
	$PARS['{ACTIVE_5}'] = $active_array['settings'];
	$PARS['{ACTIVE_6}'] = $active_array['msgs'];
	$PARS['{ACTIVE_7}'] = $active_array['files'];
	$PARS['{ACTIVE_8}'] = $active_array['personal'];
	$PARS['{ACTIVE_9}'] = $active_array['contacts'];
	$PARS['{ACTIVE_10}'] = $active_array['task_to_users'];
	$PARS['{ACTIVE_11}'] = $active_array['clients'];
	$PARS['{ACTIVE_12}'] = $active_array['deals'];
	$PARS['{ACTIVE_13}'] = $active_array['goods'];
	$PARS['{ACTIVE_14}'] = $active_array['money'];
	$PARS['{ACTIVE_15}'] = $active_array['wktime'];
	$PARS['{ACTIVE_16}'] = $active_array['cam'];
	$PARS['{ACTIVE_17}'] = $active_array['auto'];
	$PARS['{ACTIVE_18}'] = $active_array['colleagues'];
	$PARS['{ACTIVE_19}'] = $active_array['finances'];
	$PARS['{ACTIVE_20}'] = $active_array['planning'];
	$PARS['{ACTIVE_21}'] = $active_array['ofdocs'];
	$PARS['{ACTIVE_22}'] = $active_array['reprimand'];
	$PARS['{ACTIVE_23}'] = $active_array['deputy'];
	$PARS['{ACTIVE_24}'] = $active_array['external'];
	$PARS['{ACTIVE_25}'] = $active_array['notes'];
	$PARS['{ACTIVE_26}'] = $active_array['projects'];
	$PARS['{ACTIVE_27}'] = $active_array['grhy'];
	$PARS['{ACTIVE_28}'] = $active_array['cnews'];
	$PARS['{ACTIVE_29}'] = $active_array['evcal'];
	$PARS['{ACTIVE_30}'] = $active_array['posttr'];
	$PARS['{ACTIVE_31}'] = $active_array['disk'];
	$PARS['{ACTIVE_34}'] = $active_array['org'];
	$PARS['{ACTIVE_35}'] = $active_array['c_structure'];
	$PARS['{ACTIVE_36}'] = $active_array['c_structure'];
	$PARS['{ACTIVE_37}'] = $active_array['tasks'];
	$PARS['{ACTIVE_38}'] = $active_array['ucontrol'];
	 
	//// Проверяем, есть ли новые задачи
//	$new_tasks_count = get_new_tasks_count($current_user_id);
//	$new_tasks_count += get_count_notice_new_reports_for_task_for_user($current_user_id);
//	$new_tasks_count = left_menu_new_count_proc($new_tasks_count);
//	
//	// Проверяем, есть ли новые начальники
//	$new_boss_count = get_new_boss_count_for_user($current_user_id);
//	$new_boss_count = left_menu_new_count_proc($new_boss_count);
//	
//	// Данные по акутальной работе пользователя
//	$actual_user_work_data = actual_work_data_arr($current_user_id);
//	$actual_work_id = $actual_user_work_data['work_id'];
//	
//	// Если есть новая постоянная работа
//	if(get_user_work_status_by_work_id($actual_work_id)==0 && $actual_work_id)
//	{
//		$new_actual_work = left_menu_new_count_proc(1);
//	}
//	
//	// Кол-во новых сообщений
//	$new_messages_count = get_new_user_messages_count($current_user_id);
//	$new_messages_count = left_menu_new_count_proc($new_messages_count);
//	
//	
//	// Кол-во новых файлов
//	//$new_files_count = get_new_files_notice_for_user($current_user_id);
//	//$new_files_count = left_menu_new_count_proc($new_files_count);
//	
//	// Кол-во новых отчетов для задач
//	$new_task_reports_count = get_new_count_tasks_to_act($current_user_id);
//	$new_task_reports_count = left_menu_new_count_proc($new_task_reports_count);
//	
//	
//	// Кол-во новых имуществ
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_goods.php';
//	$new_goods_count = get_new_goods_count_for_users($current_user_id);
//	$new_goods_count = left_menu_new_count_proc($new_goods_count);
//	
//	
//	// Кол-во новых финансов
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_money.php';
//	$new_money_count = get_new_money_for_user($current_user_id);
//	$new_money_count += get_new_accruals_count($current_user_id);
//	$new_money_count = left_menu_new_count_proc($new_money_count);
//	
//	// Кол-во новых сообщений от клиентов пользователя
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_clients.php';
//	$new_client_msgs_count = get_new_user_clients_messages_count($current_user_id);
//	$new_client_msgs_count = left_menu_new_count_proc($new_client_msgs_count);
//	
//	// Кол-во новых запросов на добавления в коллеги
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_colleagues.php';
//	$new_colleagues_count = get_new_user_colleagues_count($current_user_id);
//	$new_colleagues_count = left_menu_new_count_proc($new_colleagues_count);
//	
//	// Кол-во финансов
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_finances.php';
//	$new_finances_count = get_new_user_finances_count($current_user_id);
//	$new_finances_count = left_menu_new_count_proc($new_finances_count);
//	
//	// Кол-во планирвоаний
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_planning.php';
//	$new_planning_count = get_new_user_planning_count_for_boss($current_user_id);
//	$new_planning_count += get_new_user_planning_count_for_workers($current_user_id);
//	$new_planning_count = left_menu_new_count_proc($new_planning_count);
//	
//	// Кол-во оф док.
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_ofdocs.php';
//	$new_ofdocs_count = get_new_ofdocs_count($current_user_id);
//	$new_ofdocs_count = left_menu_new_count_proc($new_ofdocs_count);
//	
//	// Кол-во выговоров
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_reprimand.php';
//	$new_reprimand_count = get_new_workers_reprimands_count($current_user_id);
//	$new_reprimand_count = left_menu_new_count_proc($new_reprimand_count);
//	
//	// Кол-во заместителей
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';
//	$new_deputy_count = get_new_deputies($current_user_id);
//	$new_deputy_count = left_menu_new_count_proc($new_deputy_count);
//	
//	// Кол-во заметок
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_notes.php';
//	$new_notes_count = get_new_notes_count_for_user($current_user_id);
//	$new_notes_count = left_menu_new_count_proc($new_notes_count);
//	
//	
//	// Кол-во проектов
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_projects.php';
//	$new_projects_count = get_new_projects_reports_counts($current_user_id);
//	$new_projects_count += get_new_projects_count($current_user_id);
//	$new_projects_count += get_new_task_completed_counts($current_user_id);
//	$new_projects_count += get_project_task_new_reports_count('', 'user_projects');
//	$new_projects_count += get_project_task_new_reports_count('', 'user_part_projects');
//	$new_projects_count = left_menu_new_count_proc($new_projects_count);
//	
//	
//	// Кол-во заметок
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_cnews.php';
//	$new_cnews_count = get_new_cnews_counts($current_user_id);
//	$new_cnews_count = left_menu_new_count_proc($new_cnews_count);
//	
//	// Кол-во заметок
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deals.php';
//	$new_deals_count = get_new_avalible_deals_count($current_user_id);
//	$new_deals_count = left_menu_new_count_proc($new_deals_count);
//	
//	// Кол-во новых файлов
//	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_disk.php';
//	$new_disk_files_count = new_file_available_count($current_user_id);
//	$new_disk_files_count = left_menu_new_count_proc($new_disk_files_count);
	
	 
	$PARS['{NEW_DISK_FILES_COUNT}'] = $new_disk_files_count;
	
	$PARS['{NEW_DEALS_COUNT}'] = $new_deals_count;
	
	$PARS['{NEW_CNEWS_COUNT}'] = $new_cnews_count;
	
	$PARS['{NEW_PROJECTS_COUNT}'] = $new_projects_count;
	
	$PARS['{NEW_NOTES_COUNT}'] = $new_notes_count;
	
	$PARS['{NEW_DEPUTY_COUNT}'] = $new_deputy_count;
	
	$PARS['{NEW_REPRIMAND_COUNT}'] = $new_reprimand_count;
	
	$PARS['{NEW_OFDOCS_COUNT}'] = $new_ofdocs_count;
	
	$PARS['{NEW_PLANNING_COUNT}'] = $new_planning_count;
	
	$PARS['{NEW_COLLEAGUES_COUNT}'] = $new_colleagues_count;
	
	$PARS['{NEW_CLIENT_MSGS_COUNT}'] = $new_client_msgs_count;
	
	$PARS['{NEW_MONEY_COUNT}'] = $new_money_count;
	
	$PARS['{NEW_FILES_COUNT}'] = $new_files_count;
	
	$PARS['{NEW_BOSS_COUNT}'] = $new_boss_count;
	
	$PARS['{NEW_TASKS_COUNT}'] = $new_tasks_count;
	
	$PARS['{NEW_ACTUAL_WORK}'] = $new_actual_work;
	
	$PARS['{NEW_MSGS_COUNT}'] = $new_messages_count;
	
	$PARS['{NEW_TASK_REPORTS_COUNT}'] = $new_task_reports_count;
	
	$PARS['{NEW_GOODS_COUNT}'] = $new_goods_count;
	
	$PARS['{NEW_FINANCES_COUNT}'] = $new_finances_count;
	
	$PARS['{USER_ID}'] = $current_user_id;

	return fetch_tpl($PARS, $left_menu_tpl);
}

// 
function left_menu_new_count_proc($value)
{
	if($value)
	{
		return '(+ '.$value.')';
	}
	else
	{
		return '';
	}
}
?>