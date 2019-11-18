<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_left_menu.php';

// Класс авторизации
$auth = new CAuth($site_db);

// Проверка авторизации
if(!$auth->check_auth())
{
	exit();
}

$mode = $_POST['mode'];

switch($mode)
{
	case 'get_data':
		
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tasks1.php'; // Функции заданий
		 // Проверяем, есть ли новые задачи
		$new_tasks_count = get_tasks_notices_count($current_user_id);
		  
		$result['new_tasks_count'] = $new_tasks_count;
		/*
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tasks.php'; // Функции заданий
		 // Проверяем, есть ли новые задачи
		$new_tasks_count = get_new_tasks_count($current_user_id);
		$new_tasks_count += get_count_notice_new_reports_for_task_for_user($current_user_id);
		//$new_tasks_count = left_menu_new_count_proc($new_tasks_count);
		$result['new_tasks_count'] = $new_tasks_count;*/
		
		//	// Кол-во новых сообщений
 		$new_messages_count = get_new_user_messages_count($current_user_id);
 		//$new_messages_count = left_menu_new_count_proc($new_messages_count);
		$result['new_msgs_count'] = $new_messages_count;
		
		// Кол-во новых отчетов для задач
		//$new_task_reports_count = get_new_count_tasks_to_act($current_user_id);
		//$new_task_reports_count = left_menu_new_count_proc($new_task_reports_count);
		//$result['my_tasks_reports_new_count'] = $new_task_reports_count;
		
		
		//include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_boss.php';
		// Проверяем, есть ли новые начальники
		//$new_boss_count = get_new_boss_count_for_user($current_user_id);
		//$new_boss_count = left_menu_new_count_proc($new_boss_count);
		//$result['new_boss_count'] = $new_boss_count;
		
		
		
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_work.php'; //
		// Данные по акутальной работе пользователя
		$actual_user_work_data = actual_work_data_arr($current_user_id);
		$actual_work_id = $actual_user_work_data['work_id'];
		
		// Если есть новая постоянная работа
		if(get_user_work_status_by_work_id($actual_work_id)==0 && $actual_work_id)
		{
			//$new_actual_work = left_menu_new_count_proc(1);
			$new_actual_work = 1;
		}
		
		$result['new_actual_work_count'] = $new_actual_work;
		

		// Кол-во новых имуществ
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_goods.php';
		$new_goods_count = get_new_goods_count_for_users($current_user_id);
		//$new_goods_count = left_menu_new_count_proc($new_goods_count);
		$result['new_goods_count'] = $new_goods_count;
		
		
		// Кол-во новых финансов
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_money.php';
		$new_money_count = get_new_money_for_user($current_user_id);
		$new_money_count += get_new_accruals_count($current_user_id);
		//$new_money_count = left_menu_new_count_proc($new_money_count);
		$result['new_money_count'] = $new_money_count;
		
		
		// Кол-во новых запросов на добавления в коллеги
		//include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_colleagues.php';
		//$new_colleagues_count = get_new_user_colleagues_count($current_user_id);
		//$new_colleagues_count = left_menu_new_count_proc($new_colleagues_count);
		//$result['new_colleague_count'] = $new_colleagues_count;
		
		// Кол-во финансов
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_finances.php';
		$new_finances_count = get_new_user_finances_count($current_user_id);
		//$new_finances_count = left_menu_new_count_proc($new_finances_count);
		$result['new_finances_count'] = $new_finances_count;
		
		
		// Кол-во планирвоаний
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_planning.php';
		$new_planning_count = get_new_user_planning_count_for_others($current_user_id);
		//$new_planning_count += get_new_user_planning_count_for_workers($current_user_id);
		//$new_planning_count = left_menu_new_count_proc($new_planning_count);
		$result['planning_new_count'] = $new_planning_count;
		
		// Кол-во оф док.
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_ofdocs.php';
		$new_ofdocs_count = get_new_ofdocs_count($current_user_id);
		$new_ofdocs_count += get_new_ofdocs_statuses_count($current_user_id, 'all');
		//$new_ofdocs_count = left_menu_new_count_proc($new_ofdocs_count);
		$result['new_ofdocs_count'] = $new_ofdocs_count;
		
		
		// Кол-во выговоров
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_reprimand.php';
		$new_reprimand_count = get_new_workers_reprimands_count($current_user_id,1);
		$new_reprimand_count += get_new_workers_reprimands_count($current_user_id, 2);
		//$new_reprimand_count = left_menu_new_count_proc($new_reprimand_count);
		$result['new_reprimand_count'] = $new_reprimand_count;
		
		
		// Кол-во заместителей
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';
		$new_deputy_count = get_new_deputies($current_user_id);
		//$new_deputy_count = left_menu_new_count_proc($new_deputy_count);
		$result['new_deputy_count'] = $new_deputy_count;
		
		
		// Кол-во заметок
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_notes.php';
		$new_notes_count = get_new_notes_count_for_user($current_user_id);
		//$new_notes_count = left_menu_new_count_proc($new_notes_count);
		$result['notes_new_count'] = $new_notes_count;
		
		// Кол-во проектов
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_projects.php';
		$new_projects_count = get_new_projects_reports_counts($current_user_id);
		$new_projects_count += get_new_projects_count($current_user_id);
		$new_projects_count += get_new_task_completed_counts($current_user_id);
		$new_projects_count += get_project_task_new_reports_count('', 'user_projects');
		$new_projects_count += get_project_task_new_reports_count('', 'user_part_projects');
		//$new_projects_count = left_menu_new_count_proc($new_projects_count);
		$result['new_projects_count'] = $new_projects_count;
		
		// Кол-во заметок
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_cnews.php';
		$new_cnews_count = get_new_cnews_counts($current_user_id);
		//$new_cnews_count = left_menu_new_count_proc($new_cnews_count);
		$result['new_cnews_count'] = $new_cnews_count;
		
		
		// Кол-во заметок
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deals.php';
		$new_deals_count = get_new_avalible_deals_count($current_user_id);
		//$new_deals_count = left_menu_new_count_proc($new_deals_count);
		$result['new_deals_count'] = $new_deals_count;
		
		
		// Кол-во новых файлов
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_disk.php';
		$new_disk_files_count = new_file_available_count($current_user_id);
		//$new_disk_files_count = left_menu_new_count_proc($new_disk_files_count);
		$result['new_disk_files_count'] = $new_disk_files_count;
		
		// Кол-во новых уведомлений в календаре событий
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_calendar_of_events.php';
		$new_evcal_count = get_new_events_notices_count($current_user_id);
		$result['new_evcal_count'] = $new_evcal_count;
		
		echo json_encode($result);
		
	break;
	
	case 'check_new_count':
		
		$what = value_proc($_POST['what']);
		
		$what_arr = split(',', $what);
		
		foreach($what_arr as $page)
		{
			switch($page)
			{
				case 'new_tasks_count':
					
					include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tasks1.php'; // Функции заданий
					
					// Проверяем, есть ли новые задачи
					//$sql = "SELECT task_id FROM  ".TASKS_TB." 
					//		WHERE task_to_user='$current_user_id' AND task_status = 0 AND task_deleted<>1 ORDER by task_id DESC";
	
					//$task_data = $site_db->query_firstrow($sql);
					
					$last_id = get_tasks_notices_last_id();
					
					$new_tasks_count = get_tasks_notices_count();
					// $new_tasks_count = 2;
					$result['new_tasks_count'] = array('count' => $new_tasks_count, 'lid' => $last_id);
					
				break;
				/*case 'new_tasks_count':
					
					// Проверяем, есть ли новые задачи
					$sql = "SELECT task_id FROM  ".TASKS_TB." 
							WHERE task_to_user='$current_user_id' AND task_status = 0 AND task_deleted<>1 ORDER by task_id DESC";
	
					$task_data = $site_db->query_firstrow($sql);
					
					$last_id = $task_data['task_id'];
					
					$new_tasks_count = get_new_tasks_count($current_user_id);
					// $new_tasks_count = 2;
					$result['new_tasks_count'] = array('count' => $new_tasks_count, 'lid' => $last_id);
					
				break;*/
			}
		}
		
		echo json_encode($result);
		
	break;
	
}

?>