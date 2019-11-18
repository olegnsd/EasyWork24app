<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_sms.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tasks1.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_email_send.php';

// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

if(!$current_user_id)
{
	exit();
}

switch($mode)
{
	// выставить качество проделанной работы
	case 'edit_task_rating':
		
		$task_id = value_proc($_POST['task_id']);
		
		$rating = value_proc($_POST['rating']);
		
		// проверяем, является ли пользователь постановщиком задачи
		if(!check_user_in_task_role($task_id, $current_user_id, array(1)))
		{
			exit();
		}
		
		$sql = "UPDATE tasks_tasks SET task_rating='$rating' WHERE task_id='$task_id'";
		
		$site_db->query($sql);
		
		echo 1;
		
	break;
	case 'get_more':
		
		$list = value_proc($_POST['list']);
		
		$list_status = value_proc($_POST['list_status']);
		
		$key = value_proc($_POST['key']);
		
		$filter_user_id = value_proc($_POST['filter_user_id']);
		
		$page = value_proc($_POST['page']);
		  
		// Список задач 
		$tasks_list = fill_tasks_list($list, $page, $list_status, $key, $filter_user_id);
		
		echo $tasks_list;
		
	break;
	
	case 'delegate_task':
		
		$task_id = value_proc($_POST['task_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		if(!$user_id)
		{
			echo -1;
			exit();	
		}
		
		// проверяем, является ли пользователь главным исполнителем задачи
		if(!check_user_in_task_role($task_id, $current_user_id, array(2)))
		{
			exit();
		}
		
		// добавление ролей по задаче
		save_task_users($task_id, 0, $user_id, 0,array($current_user_id), 1);
		
		echo 1;
			
	break;
	
	case 'get_task_delegate_from':
		
		$task_id = value_proc($_POST['task_id']);
		
		// проверяем, является ли пользователь главным исполнителем задачи
		if(!check_user_in_task_role($task_id, $current_user_id, array(2)))
		{
			exit();
		}
		
		echo fill_task_delegate_form($task_id);
		
	break;
	case 'task_delete':
		
		$task_id = value_proc($_POST['task_id']);
		
		// если пользователь не является постановщиком задачи
		if(!check_user_in_task_role($task_id, $current_user_id, array(1)))
		{
			exit();
		}
		
		// удаление задачи
		$sql = "UPDATE tasks_tasks SET deleted=1 WHERE task_id='$task_id'";
		$site_db->query($sql);
		
		// удаляет все уведомления по задаче
		delete_task_notice($task_id, 0, 0, 0, 1, 'by_task');
		
		echo 1;
		
	break;
	
	case 'get_report_item':
		
		$task_id = value_proc($_POST['task_id']);
		
		$report_id = value_proc($_POST['report_id']);
		
		$form = value_proc($_POST['form']);
		
		// данные отчета
		$report_data = get_task_report_data($report_id);
		
		if($current_user_id!=$report_data['report_user_id'])
		{
			exit();
		}
		
		// данные отчета
		$report_data = get_task_report_data($report_id);
		
		$report_item = fill_task_report_item($report_data, $form);
		
		echo $report_item;
		
	break;
	case 'delete_task_report':
		
		$task_id = value_proc($_POST['task_id']);
		
		$report_id = value_proc($_POST['report_id']);
		
		// данные отчета
		$report_data = get_task_report_data($report_id);
		
		if($current_user_id!=$report_data['report_user_id'])
		{
			exit();
		}
		
		$sql = "UPDATE tasks_tasks_reports SET deleted=1 WHERE task_id='$task_id' AND report_id='$report_id'";
		
		$site_db->query($sql);
		
		// удаление уведомлений
		delete_task_notice($task_id, 1, $report_id);
		
		if(!mysql_error())
		{
			echo 1;
		}
		
	break;
	case 'get_task_report_list':
		
		$task_id = value_proc($_POST['task_id']);
		
		$page = value_proc($_POST['page']);
		
		//$get_new_reports = value_proc($_POST['get_new_reports']);
		
		//$lrid = value_proc($_POST['lrid']);
		
		$reports_list_arr = fill_task_reports_list($task_id, $page);
		
		echo json_encode(array('reports_list' => iconv('cp1251', 'utf-8', $reports_list_arr['task_report_list']), 'lrid' => $reports_list_arr['last_report_id']));
		
	break;
	
	case 'get_new_reports':
		
		$task_id = value_proc($_POST['task_id']);
		
		$lrid = value_proc($_POST['lrid']);
		
		$reports_list_arr = fill_task_reports_list($task_id, $page, 1, $lrid);
		
		echo json_encode(array('reports_list' => iconv('cp1251', 'utf-8', $reports_list_arr['task_report_list']), 'lrid' => $reports_list_arr['last_report_id']));
		
		
	break;
	
	// Возвращает кол-во постоянный работ для пользователя
	case 'get_task_reports_count':
		
		$task_id = value_proc($_POST['task_id']);
		
		$count = get_task_reports_count($task_id);
		 
		
		echo json_encode(array('count' => $count));
		
	break;
	
	case 'save_task_report':
		
		$report_id = value_proc($_POST['report_id']);
		
		$task_id = value_proc($_POST['task_id']);
		
		$report_text = value_proc($_POST['report_text']);
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_deleted = json_decode(str_replace('\\', '', $_POST['files_deleted']));
		$files_content_type = value_proc($_POST['files_content_type']);
		
		// данные отчета
		$report_data = get_task_report_data($report_id);
		
		if($current_user_id!=$report_data['report_user_id'])
		{
			exit();
		}
		
		if($report_text=='')
		{
			$error['report_text'] = 1;
		}

		
		if(!$error)
		{
			$sql = "UPDATE tasks_tasks_reports SET report_text='$report_text' WHERE report_id='$report_id'";
			
			$site_db->query($sql);
			
			// Привязка файлов к контенту
			attach_files_to_content($report_id, $files_content_type, $files_arr);
			
			// Удаляем прикрепленные файлы
			delete_attached_files_to_content($report_id, $files_content_type, $files_deleted);
			
			$success = 1;
		}
		
		echo json_encode(array('success' => $success, 'error' => $error));
		
	break;
	// Добавляет отчет
	case 'add_task_report':
		
		$task_id = value_proc($_POST['task_id']);
		
		$report_text = value_proc($_POST['report_text']);
		
		$by_sms = value_proc($_POST['by_sms']);
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_content_type = value_proc($_POST['files_content_type']);
		
		if(!$task_id || !$current_user_id || !check_user_in_task_role($task_id, $current_user_id, array(1,2,3,4)))
		{
			exit();
		}
		
		if($report_text=='')
		{
			$error['report_text'] = 1;
		}
		
		if(!$error)
		{
			// Получаем задачу
			$sql = "SELECT * FROM tasks_tasks WHERE task_id='$task_id'";
			
			$task_data = $site_db->query_firstrow($sql);
			
			if($task_data['deleted'])
			{
				exit();
			}
			
			// Добавляем отчет о задаче
			$sql = "INSERT INTO tasks_tasks_reports SET task_id='$task_id', report_user_id='$current_user_id', report_date=NOW(), report_text='$report_text'";
			
			$site_db->query($sql);
			
			$report_id = $site_db->get_insert_id();
			
			// уведомляем всех пользователй задачи
			add_task_notice($task_id, $current_user_id, 1, $report_id);
			
			// Привязка файлов к контенту
			attach_files_to_content($report_id, $files_content_type, $files_arr);
			
			// уведомление
			task_send_notice_by_email($task_id, 3);
			
			$success = 1;
			
			 
		}
		
		
		echo json_encode(array('success' => $success, 'error' => $error));
		
	break;
	
	case 'edit_task':
		
		$task_id = value_proc($_POST['task_id']);
		
		$edit_form = get_task_edit_form($task_id);
		
		echo $edit_form;
		
	break;
	// Список заданий для сотрудника
	case 'add_task':
		
		$task_theme = value_proc($_POST['task_theme']);
		$task_text = value_proc($_POST['task_text']);
		$task_max_date = value_proc($_POST['task_max_date']);
		$task_max_date_hours = value_proc($_POST['task_max_date_hours']);
		$task_max_date_minuts = value_proc($_POST['task_max_date_minuts']);
		$task_priority = value_proc($_POST['task_priority']);
		$task_difficulty = value_proc($_POST['task_difficulty']);
		$task_from_user = value_proc($_POST['task_from_user']);
		$task_user_performer_main = value_proc($_POST['task_user_performer_main']);
		$task_users_performers = json_decode(str_replace('\\', '', $_POST['task_users_performers']), 1);
		$task_users_copies = json_decode(str_replace('\\', '', $_POST['task_users_copies']), 1);
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_content_type = value_proc($_POST['files_content_type']);
		
		$pars = value_proc($_POST['pars']);
		
		if(!$task_text)
		{
			$error['task_text'] = 1;
		}
		
		if($task_max_date && !date_rus_validate($task_max_date))
		{
			$error['task_max_date'] = 1;
		}
		if($task_max_date_hours && (!is_numeric($task_max_date_hours) || $task_max_date_hours > 24))
		{
			$error['task_max_date_hours'] = 1;
		}
		
		if($task_max_date_minuts && (!is_numeric($task_max_date_minuts) || $task_max_date_minuts > 60))
		{
			$error['task_max_date_minuts'] = 1;
		}
		if(!$task_from_user)
		{
			$error['task_from_user'] = 1;
		}
		
		if(!$task_user_performer_main)
		{
			$error['task_user_performer_main'] = 1;
		}
		
		if(!$error)
		{
			
			if($task_max_date)
			{
				$task_max_date = to_mktime(formate_to_norm_date($task_max_date));
				
				// добавляем часы
				if($task_max_date_hours)
				{
					$task_max_date += $task_max_date_hours*60*60;
				}
				// добавляем минуты
				if($task_max_date_minuts)
				{
					$task_max_date += $task_max_date_minuts*60;
				}
				
				$task_max_date = date('Y-m-d H:i', $task_max_date);

			}
			
			// добавление задачи
			$sql = "INSERT INTO tasks_tasks 
					SET task_theme='$task_theme', task_text='$task_text', task_max_date='$task_max_date', task_priority='$task_priority',
					task_difficulty='$task_difficulty', date_add=NOW(), user_id='$current_user_id', work_status=1";
			
			$site_db->query($sql);
			
			$task_id = $site_db->get_insert_id();
			
			if($task_from_user==$task_user_performer_main)
			{
				$step_status = 1;
			}
			
			// добавляем связь задачи с другим контентом
			if($pars)
			{
				$tmp_pars = split('\|', $pars);
				
				if($tmp_pars[0])
				{
					$sql = "INSERT INTO tasks_tasks_links SET task_id='$task_id', id='".$tmp_pars[0]."', other_id='".$tmp_pars[1]."', `type`='".$tmp_pars[2]."'";
					$site_db->query($sql);
				}
			}
			
			// Привязка файлов к контенту
			attach_files_to_content($task_id, $files_content_type, $files_arr);
			
			// обновление статуса задачи
			task_status_to_default($task_id, $step_status);
						
			// добавление ролей по задаче
			add_task_users($task_id, $task_from_user, $task_user_performer_main, $task_users_performers, $task_users_copies);
			
			// флаг задачи поставлена ли она сама себе или нет
			task_check_for_own($task_id);
			
			// уведомление о новой задаче
			task_send_notice_by_email($task_id, 1);
			
			$success = 1;
			
		}
		
		echo json_encode(array('error' => $error, 'success' => $success));
		
	break;
	
	// Список заданий для сотрудника
	case 'save_task':
		
		$task_id = value_proc($_POST['task_id']);
		$task_theme = value_proc($_POST['task_theme']);
		$task_text = value_proc($_POST['task_text']);
		$task_max_date = value_proc($_POST['task_max_date']);
		$task_max_date_hours = value_proc($_POST['task_max_date_hours']);
		$task_max_date_minuts = value_proc($_POST['task_max_date_minuts']);
		$task_priority = value_proc($_POST['task_priority']);
		$task_difficulty = value_proc($_POST['task_difficulty']);
		$task_from_user = value_proc($_POST['task_from_user']);
		$task_user_performer_main = value_proc($_POST['task_user_performer_main']);
		$task_users_performers = json_decode(str_replace('\\', '', $_POST['task_users_performers']), 1);
		$task_users_copies = json_decode(str_replace('\\', '', $_POST['task_users_copies']), 1);
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_deleted = json_decode(str_replace('\\', '', $_POST['files_deleted']));
		$files_content_type = value_proc($_POST['files_content_type']);
		
		if(!$task_text)
		{
			$error['task_text'] = 1;
		}
		
		if($task_max_date && !date_rus_validate($task_max_date))
		{
			$error['task_max_date'] = 1;
		}
		
		if($task_max_date_hours && (!is_numeric($task_max_date_hours) || $task_max_date_hours > 24))
		{
			$error['task_max_date_hours'] = 1;
		}
		
		if($task_max_date_minuts && (!is_numeric($task_max_date_minuts) || $task_max_date_minuts > 60))
		{
			$error['task_max_date_minuts'] = 1;
		}
		
		if($task_max_date_hours && !is_numeric($task_max_date_hours))
		{
			$error['task_max_date_hours'] = 1;
		}
		
		if(!$task_from_user)
		{
			$error['task_from_user'] = 1;
		}
		
		if(!$task_user_performer_main)
		{
			$error['task_user_performer_main'] = 1;
		}
		
		if(!$error)
		{
			
			
			if($task_max_date)
			{
				$task_max_date = to_mktime(formate_to_norm_date($task_max_date));
				
				 
				// добавляем часы
				if($task_max_date_hours)
				{
					$task_max_date += $task_max_date_hours*60*60;
				}
				// добавляем минуты
				if($task_max_date_minuts)
				{
					$task_max_date += $task_max_date_minuts*60;
				}
				 
				$task_max_date = date('Y-m-d H:i', $task_max_date);

			}
			
			// обновление данных по задаче
			$sql = "UPDATE tasks_tasks 
					SET task_theme='$task_theme', task_text='$task_text', task_max_date='$task_max_date', task_priority='$task_priority',
					task_difficulty='$task_difficulty', date_edit=NOW() WHERE task_id='$task_id'";
			
			$site_db->query($sql);
			
			// Привязка файлов к контенту
			attach_files_to_content($task_id, $files_content_type, $files_arr);
			
			// Удаляем прикрепленные файлы
			delete_attached_files_to_content($task_id, $files_content_type, $files_deleted);
			
			// добавление ролей по задаче
			save_task_users($task_id, $task_from_user, $task_user_performer_main, $task_users_performers, $task_users_copies);
			
			// уведомление
			task_send_notice_by_email($task_id, 2);
			
			$success = 1;
		}
		
		echo json_encode(array('error' => $error, 'success' => $success));
		
	break;
	
	case 'get_add_form':
		
		$add_form = fill_task_add_form();
		
		echo $add_form;
		
	break;
	
	case 'get_task_status_bar':
		
		$task_id = value_proc($_POST['task_id']);
		
		// данные по задаче
		$sql = "SELECT * FROM tasks_tasks WHERE task_id='$task_id'";
	
		$task_data = $site_db->query_firstrow($sql);
		
		// кнопки
		$task_btn = fill_task_btns($task_data);
		
		// статус
		$status_bar = fill_task_status_bar($task_data);
		
		echo json_encode(array('btns' => iconv('cp1251', 'utf-8', $task_btn), 'status_bar' => iconv('cp1251', 'utf-8',$status_bar)));
		 
		
	break;
	
	// Отметка о прочтении задания
	case 'task_status':
		
		$task_id = value_proc($_POST['task_id']);
		
		$status = value_proc($_POST['status']);
		
		// выбор задачи
		$sql = "SELECT * FROM tasks_tasks WHERE task_id='$task_id'";
		
		$task_data = $site_db->query_firstrow($sql);
		
		if(!check_user_in_task_role($task_id, $current_user_id, array(1,2)))
		{
			exit();
		}
		
		
		switch($status)
		{
			// принято
			case '1':
				if($task_data['work_status']!=1 || $task_data['step_status']!=0) {$r= '-1'; break;}
				set_task_status($task_id, $status);
			break;
			// выполнение начато
			case '2':
				if($task_data['work_status']!=1 || $task_data['step_status']!=1) {$r= '-1'; break;}
				set_task_status($task_id, $status);
			break;
			// приостановлено 
			case '-2':
				if($task_data['work_status']!=1 || $task_data['step_status']!=2) {$r= '-1'; break;}
				set_task_status($task_id, 1);
			break;
			// выполнено
			case '3':
				if($task_data['work_status']!=1 || $task_data['step_status']!=2) {$r= '-1'; break;}
				set_task_status($task_id, $status);
				// уведомляем постановщика задачи
				add_task_notice($task_id, $current_user_id, 2, 3, array(1));
			break;
			// не выполнено, продолжаю выполнять
			case '-3':
				if($task_data['work_status']!=1 || $task_data['step_status']!=3) {$r= '-1'; break;}
				set_task_status($task_id, 2);
				// Убираем уведомления о выполнении
				delete_task_notice($task_id, 2, 3);
			break;
			// подтвердить выполнение задачи
			case '4':
				if($task_data['work_status']!=1 || $task_data['step_status']!=3) {$r= '-1'; break;}
				set_task_status($task_id, $status, 2);
				// уведомляем всех, что задача завершена
				add_task_notice($task_id, $current_user_id, 2, 4);
			break;
			// отклонить выполнение задачи, дорабатывать
			case '-4':
				if($task_data['work_status']!=1 || $task_data['step_status']!=3) {$r= '-1'; break;}
				set_task_status($task_id, 2);
				// уведомляем исполнителя задачи
				add_task_notice($task_id, $current_user_id, 2, -4, array(2));
			break;
			// сзаданием не справился
			case 5:
				if($task_data['work_status']!=1) {$r= '-1'; break;}
				set_task_status($task_id, $status, 2);
				// уведомляем всех, что с задачей не справился
				add_task_notice($task_id, $current_user_id, 2, 5);
			break;
			// Возобновить выполнение задачи
			case '6':
				if($task_data['work_status']!=2) {$r= '-1'; break;}
				set_task_status($task_id, 2, 1);
				add_task_notice($task_id, $current_user_id, 2, 6);
			break;
			// Задача выполнена
			case '7':
				if($task_data['work_status']!=1) {$r= '-1'; break;}
				set_task_status($task_id, 4, 2);
				// уведомляем всех, что задача выполнена
				add_task_notice($task_id, $current_user_id, 2, 4);
			break;
			// Задача выполнена (когда сам себе выставил задачу)
			case '8':
				if($task_data['work_status']!=1 || $task_data['step_status']!=2) {$r= '-1'; break;}
				set_task_status($task_id, 4, 2);
			break;
		}
		
		echo $r;
		
	break;
	
}

?>