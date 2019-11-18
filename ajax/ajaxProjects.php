<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_projects.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_sms.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_email_send.php';

$mode = $_POST['mode'];

if(!$current_user_id)
{
	exit();
}
global $system_sms;
$system_sms=1;
switch($mode)
{
	// Создать проект
	case 'add_project':
		
		$project_name = value_proc($_POST['project_name']);
		
		$project_desc = value_proc($_POST['project_desc'], 1, 1);
		
		$project_head = value_proc($_POST['project_head']);
		
		$project_tasks_arr =  stdToArray(json_decode($_POST['project_tasks_arr']));
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_content_type = value_proc($_POST['files_content_type']); 
		
		// Проверяем задачи
		$error = check_project_tasks($project_tasks_arr);
		
		if($project_name=='')
		{
			$error['project_name'] = 1;
		}
		
		if(!$error)
		{
			foreach($project_tasks_arr as $num => $data)
			{
				$array_project_dates[] = to_mktime(formate_to_norm_date($data['date_start']));
				$array_project_dates[] = to_mktime(formate_to_norm_date($data['date_finish']));
			}
			$date_start = min($array_project_dates);
			$date_finish = max($array_project_dates);
			
			// Добавляем проект
			$sql = "INSERT INTO ".PROJECTS_TB." SET user_id='$current_user_id', project_name='$project_name', project_desc='$project_desc', date_add=NOW(), date_start='$date_start', date_finish='$date_finish', project_head='$project_head'";
			
			$site_db->query($sql);
			
			$project_id = $site_db->get_insert_id(); 
			
			// Добавляем задачи
			foreach($project_tasks_arr as $num => $data)
			{
				$user_id = is_numeric($data['user_id']) ? $data['user_id'] : 0;
				
				$date_start = formate_to_norm_date(value_proc($data['date_start']));
				
				$date_finish = formate_to_norm_date(value_proc($data['date_finish']));
				
				$task_desc = value_proc($data['task_desc']);
				
				$task_id = value_proc($data['task_id']);
				 
				if(!$user_id && !$date_start && !$date_finish &&  !$task_desc)
				{
					continue;
				}
				 
				$sql = "INSERT INTO ".PROJECT_TASKS_TB." SET project_id='$project_id', user_id='$user_id', date_start='$date_start', date_finish='$date_finish', task_desc='$task_desc', added_by_user_id='$current_user_id'";
				
				$site_db->query($sql);
				
				$inserted_task_id = $site_db->get_insert_id();
				
				$tasks_arr[$task_id] = $inserted_task_id;
				
				if($data['after_task_id']!='undefined' && preg_match('/rand/', $data['after_task_id']))
				{
					$tasks_after_arr[] = array('task_id' => $inserted_task_id, 'after_task_id' => $data['after_task_id']);
				}
			}
			
			foreach($tasks_after_arr as $data)
			{
				$task_id = $data['task_id'];
				$after_task_id = $tasks_arr[$data['after_task_id']];
				
				$sql = "UPDATE ".PROJECT_TASKS_TB." SET after_task_id='$after_task_id' WHERE task_id = '$task_id'";
				 
				$site_db->query($sql);
			}
			
			// Привязка файлов к контенту
			attach_files_to_content($project_id, $files_content_type, $files_arr);
			
			// Уведомления
			 project_send_notice_by_email($project_id, 1);
			
			$success = 1;
		}
		 
		echo json_encode(array('success' => $success, 'error' => $error, 'project_id' => $project_id));
	
	break;
	
	case 'save_project_heads':
		
		$project_id = value_proc($_POST['project_id']);
		
		$project_desc = value_proc($_POST['project_desc'], 1, 1);
		
		$project_name = value_proc($_POST['project_name']);
		
		$project_head = value_proc($_POST['project_head']);
		
		// Данные проекта
		$project_data = get_project_data($project_id);
		
		if($project_data['user_id']!=$current_user_id && $project_data['project_head']!=$current_user_id)
		{
			exit();
		}
		
		if(!$project_name)
		{
			$error['project_name'] = 1;
		}
		
		if(!$error)
		{
			if($project_data['project_head']!=$project_head)
			{
				$and_head_confirm = ', project_head_confirmed=0';
			}
			
			// Обновляем даты старта и завершения проекта
			$sql = "UPDATE ".PROJECTS_TB." SET project_name='$project_name', project_desc='$project_desc', project_head='$project_head' $and_head_confirm WHERE project_id='$project_id'";
			
			$site_db->query($sql);
			
			$success = 1;
		}
		
		echo json_encode(array('success' => $success, 'error' => $error));
		
	break;
	
	// Создать проект
	case 'save_project':
		
		$project_id = value_proc($_POST['project_id']);
		
		$project_tasks_arr =  json_decode($_POST['project_tasks_arr'], true);
		
		$deleted_project_tasks = json_decode($_POST['deleted_project_tasks'], true);
		
		$error = check_project_tasks($project_tasks_arr);
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_content_type = value_proc($_POST['files_content_type']); 
		$files_deleted = json_decode(str_replace('\\', '', $_POST['files_deleted']));	
	 
		if(!$error)
		{
			// Данные проекта
			$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id'";
	
			$project_data = $site_db->query_firstrow($sql);
			
	
			// Выбор всех уже сууществующих задач проекта
			$sql = "SELECT * FROM ".PROJECT_TASKS_TB." WHERE project_id='$project_id'";
			
			$res = $site_db->query($sql);
			
			while($task_data=$site_db->fetch_array($res, 1))
			{
				// Массив задач проекта по ключу id задачи
				$_project_tasks_arr[$task_data['task_id']] = $task_data;
				// Массив задач проекта по ключу id пользователя, который участвует в задаче
				$_project_users_participation_arr[$task_data['user_id']] = $task_data['user_id'];
			}
	 
			foreach($project_tasks_arr as $num => $task_data)
			{
				$array_project_dates[] = to_mktime(formate_to_norm_date($task_data['date_start']));
				$array_project_dates[] = to_mktime(formate_to_norm_date($task_data['date_finish']));
			}
			 
			$date_start = min($array_project_dates);
			$date_finish = max($array_project_dates);
			
			
			// Обновляем даты старта и завершения проекта
			$sql = "UPDATE ".PROJECTS_TB." SET date_start='$date_start', date_finish='$date_finish' WHERE project_id='$project_id'";
			
			$site_db->query($sql);
			
			// Привязка файлов к контенту
			attach_files_to_content($project_id, $files_content_type, $files_arr);
			
			// Удаляем прикрепленные файлы
			delete_attached_files_to_content($project_id, $files_content_type, $files_deleted);
			  
			  
			// Обновляем задачи проекта
			foreach($project_tasks_arr as $num => $data)
			{   
				$tasks_arr[$data['task_id']] = $data['task_id'];
				
				$user_id = is_numeric($data['user_id']) ? $data['user_id'] : 0;
				
				$date_start = formate_to_norm_date(value_proc($data['date_start']));
				
				$date_finish = formate_to_norm_date(value_proc($data['date_finish']));
				
				$task_desc = value_proc($data['task_desc']);
				
				$task_after = value_proc($data['task_desc']);
				 
				if(!$user_id && !$date_start && !$date_finish &&  !$task_desc)
				{
					continue;
				}
				
				 
				
				if($project_data['user_id']==$current_user_id || $project_data['project_head']==$current_user_id || $current_user_obj->get_is_admin())
				{
					// Текущий пользователь является владельцем проекта, поэтому даем ему возможность выполнять любые действия
				}
				// Если будет изменена задача и пользователь и -
				// - Текущий пользователь не является ее создателем
				else if($data['task_id'] > 0 && $_project_tasks_arr[$data['task_id']]['added_by_user_id']!=$current_user_id)
				{
					continue;
				}
				// Если требуется создать новую задачу и текущий пользователь не участвует в проекте
				else if(preg_match('/rand/', $data['task_id']) && !$_project_users_participation_arr[$current_user_id])
				{
					continue;
				}
				
				 
				 
				// Если новая задача
				if(preg_match('/rand/', $data['task_id']))
				{
					$sql = "INSERT INTO ".PROJECT_TASKS_TB." SET project_id='$project_id', user_id='$user_id', date_start='$date_start', date_finish='$date_finish', task_desc='$task_desc', added_by_user_id='$current_user_id'";
					 
					$site_db->query($sql);
					
					$inserted_task_id = $site_db->get_insert_id();
					
					// ID задачи, которая обрабатывается в итерации
					$task_id_proc = $inserted_task_id;
					
					$tasks_arr[$data['task_id']] = $inserted_task_id;
				}
				// Если задачу редактируем
				else
				{
					$not_confirm = '';
					// Выбираем задачу
					$sql = "SELECT user_id FROM ".PROJECT_TASKS_TB." WHERE project_id='$project_id' AND task_id='".$data['task_id']."'";
					
					$task_data = $site_db->query_firstrow($sql);
					
					// ID задачи, которая обрабатывается в итерации
					$task_id_proc = $data['task_id'];
					
					 
					
					if($task_data['user_id']!=$user_id)
					{
						$not_confirm = ", task_confirm=0";
					}
					
					// Обновляем задачи
					$sql = "UPDATE ".PROJECT_TASKS_TB." SET user_id='$user_id', date_start='$date_start', date_finish='$date_finish', task_desc='$task_desc' $not_confirm WHERE project_id='$project_id' AND task_id='".$data['task_id']."'";
				
					$site_db->query($sql);
				}
				
				//echo $data['task_id'],' ';
				if($data['after_task_id']!='undefined' && $data['after_task_id']!='')
				{
					$tasks_after_arr[] = array('task_id' => $task_id_proc, 'after_task_id' => $data['after_task_id'], 'task_id_not_processed' => $data['task_id']);
				}
			}
			
			///////////////////////////
			
			foreach($tasks_after_arr as $after_data)
			{
				$task_id = $after_data['task_id'];
				$after_task_id = $tasks_arr[$after_data['after_task_id']];
				
				if($project_data['user_id']!=$current_user_id && $_project_tasks_arr[$task_id]['added_by_user_id']!=$current_user_id && !preg_match('/rand/',$after_data['task_id_not_processed']) && !$current_user_obj->get_is_admin())
				{
					continue;
				}
				 
				$sql = "UPDATE ".PROJECT_TASKS_TB." SET after_task_id='$after_task_id' WHERE task_id = '$task_id'";

				$site_db->query($sql);
			}
			///////////////////////////
			
			
			 // Удаляем задачи
			foreach($deleted_project_tasks as $task_id)
			{
				if($project_data['user_id']!=$current_user_id && $_project_tasks_arr[$task_id]['added_by_user_id']!=$current_user_id && !$current_user_obj->get_is_admin())
				{
					continue;
				}
				
				
				$sql = "DELETE FROM ".PROJECT_TASKS_TB." WHERE task_id='$task_id' AND project_id='$project_id'";
				 
				$site_db->query($sql);
				
				$sql = "UPDATE ".PROJECT_TASKS_TB." SET after_task_id=0 WHERE after_task_id='$task_id' AND project_id='$project_id'";
				
				$site_db->query($sql);
			}
			
			// Уведомления
			 project_send_notice_by_email($project_id, 2);
			
			$success=1;
		}
		 
		echo json_encode(array('success' => $success, 'error' => $error, 'project_id' => $project_id));
	
	break;
	
	case 'get_project_item':
		
		$project_id = value_proc($_POST['project_id']);
		
		if(!check_project_access_for_user($current_user_id, $project_id))
		{
			exit();
		}
		
		// Выбор проектов
		$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id' AND deleted<>1";
	
		$project_data = $site_db->query_firstrow($sql);
	
		$project_item = fill_project_list_item($project_data);
		
		echo $project_item;
		
	break;
	
	case 'delete_project':
		
		$project_id = value_proc($_POST['project_id']);
		
		if(!check_project_access_for_user($current_user_id, $project_id, 1))
		{
			exit();
		}
		
		$sql = "UPDATE ".PROJECTS_TB."  SET deleted=1 WHERE project_id='$project_id'";
		
		 $site_db->query_firstrow($sql);
		
		if(!mysql_error())
		{
			$_SESSION['project_delete'][] = $project_id;
			
			echo 1;
		}
		
	break;
	
	case 'restore_project':
		
		$project_id = value_proc($_POST['project_id']);
		
		if(!check_project_access_for_user($current_user_id, $project_id, 1))
		{
			exit();
		}
		
		$sql = "UPDATE ".PROJECTS_TB." SET deleted=0 WHERE project_id='$project_id'";
		 
		$site_db->query_firstrow($sql);
		
		if(!mysql_error())
		{
			unset($_SESSION['project_delete'][$project_id]);
			
			echo 1;
		}
		
	break;
	
	// Добавляет отчет
	case 'add_project_report':
		
		$project_id = value_proc($_POST['project_id']);
		
		$report_text = value_proc($_POST['report_text'], 1, 1);
		
		if(!check_project_access_for_user($current_user_id, $project_id))
		{
			exit();
		}
			
		if(!$project_id)
		{
			exit();
		}
		
		// Данные проекта
		$project_data = get_project_data($project_id);
		
		// Если проект был закрыт
		if($project_data['project_closed']==1)
		{
			exit();
		}
		
		if($report_text=='')
		{
			$error['report_text'] = 1;
		}
		 
		if(!$error)
		{	
			// Добавляем отчет 
			$sql = "INSERT INTO ".PROJECT_REPORTS_TB." SET project_id='$project_id', user_id='$current_user_id', report_date=NOW(), report_text='$report_text'";
			
			$site_db->query($sql);
			
			$report_id = $site_db->get_insert_id(); 
						
			// Уведомления
			 project_send_notice_by_email($project_id, 3, array('project_report_text' => $report_text));
						
			$success = 1;
		}
		
		
	echo json_encode(array('success' => $success, 'error' => $error, 'report_id' => $report_id));
	
	break;
	
	// 
	case 'get_more_project_reports':
		
		$page = value_proc($_POST['page']);
		
		$project_id = value_proc($_POST['project_id']);
		
		// Выбор проектов
		$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id' AND deleted<>1";
	
		$project_data = $site_db->query_firstrow($sql);
		
		if(!check_project_access_for_user($current_user_id, $project_id))
		{
			exit();
		}
			
		// Список отчетов
		$reports_list = fill_project_report_list($project_data, $page);
		
		echo $reports_list;
		
	break;
	
	case 'get_project_report_item':
		
		$report_id = value_proc($_POST['report_id']);
		
		$project_id = value_proc($_POST['project_id']);
		
		if(!check_project_access_for_user($current_user_id, $project_id))
		{
			exit();
		}
		
		// Выбор проектов
		$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id' AND deleted<>1";
	
		$project_data = $site_db->query_firstrow($sql);
		
		// Выбираем все отчеты для данной работы
		$sql = "SELECT * FROM ".PROJECT_REPORTS_TB." WHERE report_id='$report_id' AND project_id='$project_id'";
		
		$report_data = $site_db->query_firstrow($sql);
		
		// Комментарий
		$report_item = fill_project_reports_item($project_data, $report_data);
		
		echo $report_item;
		
	break;
	
	// Принять отчет
	case 'confirm_project_report':
		
		$report_id = value_proc($_POST['report_id']);
		
		$project_id = value_proc($_POST['project_id']);
		
		$confirm_all = value_proc($_POST['confirm_all']);
		 
		if(!check_project_access_for_user($current_user_id, $project_id, 1))
		{
			exit();
		}
		
		if($confirm_all)
		{
			$sql = "UPDATE ".PROJECT_REPORTS_TB." SET report_confirm=1 WHERE report_confirm=0 AND project_id='$project_id'";
		
			$site_db->query($sql);
		}
		else
		{
			$sql = "UPDATE ".PROJECT_REPORTS_TB." SET report_confirm=1 WHERE report_id='$report_id' AND project_id='$project_id'";
		
			$site_db->query($sql);
		}
		
		
		echo 1;
		
	break;
	
	case 'delete_project_report':
		
		$report_id = value_proc($_POST['report_id']);
		
		$project_id = value_proc($_POST['project_id']);
		
		// Выбор проектов
		$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id' AND deleted<>1";
	
		$project_data = $site_db->query_firstrow($sql);
		
		// Выбираем все отчеты для данной работы
		$sql = "SELECT * FROM ".PROJECT_REPORTS_TB." WHERE report_id='$report_id' AND project_id='$project_id'";
		
		$report_data = $site_db->query_firstrow($sql);
		
		if($project_data['user_id'] != $current_user_id && $report_data['user_id'] != $current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".PROJECT_REPORTS_TB." SET deleted=1 WHERE report_id='$report_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			$_SESSION['project_report_delete'][] = $report_id;
			
			echo 1;
		}
		
	break;
	
	case 'restore_project_report':
		
		$report_id = value_proc($_POST['report_id']);
		
		$project_id = value_proc($_POST['project_id']);
		
		// Выбор проектов
		$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id' AND deleted<>1";
	
		$project_data = $site_db->query_firstrow($sql);
		
		// Выбираем все отчеты для данной работы
		$sql = "SELECT * FROM ".PROJECT_REPORTS_TB." WHERE report_id='$report_id' AND project_id='$project_id'";
		
		$report_data = $site_db->query_firstrow($sql);
		
		if($project_data['user_id'] != $current_user_id && $report_data['user_id'] != $current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".PROJECT_REPORTS_TB." SET deleted=0 WHERE report_id='$report_id'";
		
	 	$site_db->query($sql);
		
		if(!mysql_error())
		{
			unset($_SESSION['project_report_delete'][$report_id]);
			
			echo 1;
		}
		
	break;
	
	case 'recount_project_notice':
		
		$new_reports_count = get_new_projects_reports_counts($current_user_id);
		
		$new_projects_count = get_new_projects_count($current_user_id);
		$new_projects_count += get_new_task_completed_counts($current_user_id);
		$new_projects_count += get_project_task_new_reports_count('', 'user_projects');
		$new_projects_count += get_project_task_new_reports_count('', 'user_part_projects');
		
		echo json_encode(array('new_reports_count' => $new_reports_count, 'new_projects_count' => $new_projects_count, 'count' => $new_reports_count+$new_projects_count));
		
	break;
	
	case 'project_task_complete':
	
		$task_id = value_proc($_POST['task_id']);
		
		$project_id = value_proc($_POST['project_id']);
		
		$completed = value_proc($_POST['completed']);
		
		// Выбор проектов
		$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id'";
	
		$project_data = $site_db->query_firstrow($sql);
		
		// Выбор проектов
		$sql = "SELECT * FROM ".PROJECT_TASKS_TB." WHERE project_id='$project_id' AND task_id='$task_id'";
		$task_data = $site_db->query_firstrow($sql);
		
		if(($task_data['user_id']==$current_user_id && in_array($completed, array(0,1))))
		{
			 
		}
		else if(($task_data['added_by_user_id']==$current_user_id && in_array($completed, array(0,1,2,-1))))
		{
			 
		}
		else
		{
			exit();
		}
		
		 
		switch($completed)
		{
			case '-1':
				//
				$sql = "UPDATE ".PROJECT_TASKS_TB." SET task_completed='0', task_date_finished=0 WHERE task_id='$task_id'";
				$site_db->query($sql);
			break;
			default:
				//
				$sql = "UPDATE ".PROJECT_TASKS_TB." SET task_completed='$completed', task_date_finished=NOW() WHERE task_id='$task_id'";
				$site_db->query($sql);
			break;
		}

		
		 
		
		// Уведомляем по смс подтверждение выполнения задачи проекта 
		if($completed==2 || $completed=='-1')
		{
			// Заполянем объект пользователя
			$user_data = $user_obj->fill_user_data($task_data['user_id']);
	
			$user_phone = $user_obj->get_user_phone();
			
			if($completed==2)
			{
				### sms body
				$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/project_task_complete_confirm.tpl');
			}
			else if($completed=='-1')
			{
				### sms body
				$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/project_task_not_complete_confirm.tpl');
			}
			
			$project_name_sms = strlen($project_data['project_name']) > 20 ? substr($project_data['project_name'],0,20).'...' : $project_data['project_name'];
			
			
			$user_obj->fill_user_data($current_user_id);
					
			$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
					
			$PARS['{USER_NAME}'] = $user_obj->get_user_name();
					
			$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
					
			$PARS['{TASK_TEXT}'] = $sms_text;
			
			$PARS['{PROJECT_NAME}'] = $project_name_sms;
			 
			$sms_text = fetch_tpl($PARS, $sms_tpl);
			
			###\ sms body
			
			// Отправка смс сообщения
			send_sms_msg($user_phone, $sms_text);
			
		}
			
		if(!mysql_error())
		{
			// Выбор обновленной задачи
			$sql = "SELECT * FROM ".PROJECT_TASKS_TB." WHERE project_id='$project_id' AND task_id='$task_id'";
			$task_data = $site_db->query_firstrow($sql);
		
			$complete_btn = get_project_task_complete_btn_tpl($task_data, $project_data);
		 
		 	$success = 1;
		}  
		
		echo json_encode(array('success' => $success, 'complete_btn' => iconv('windows-1251','UTF-8',$complete_btn)));
		
	break;
	
	case 'project_confirm':
		
		$project_id = value_proc($_POST['project_id']);
		
		// Данные проекта
		$project_data = get_project_data($project_id);
		 
		//Если не является создателем проекта либо человеком, ответственным за задачу
		if(!check_project_access_for_user($current_user_id, $project_id))
		{
			exit();
		}
		
		$sql = "UPDATE ".PROJECT_TASKS_TB." SET task_confirm=1 WHERE project_id='$project_id' AND user_id='$current_user_id'";
		
		$site_db->query($sql);
		
		// снимаем уведомление для ответственного за проект
		if($project_data['project_head']==$current_user_id && $project_data['project_head_confirmed']==0)
		{
			$sql = "UPDATE ".PROJECTS_TB." SET project_head_confirmed=1 WHERE project_id='$project_id'";
			$site_db->query($sql);
		}
		
		if(!mysql_error())
		{
			echo 1;
		}
		
	break;
	
	case 'get_project_tasks_list':
		
		$project_id = value_proc($_POST['project_id']);
		
		// Выбор проектов
		$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id'";
	
		$project_data = $site_db->query_firstrow($sql);
		
		if(!check_project_access_for_user($current_user_id, $project_id))
		{
			exit();
		}
		
		$tasks_list = fill_project_tasks_list($project_data);
		
		echo $tasks_list;
		
	break;
	
	case 'get_more_projects':
		
		$page = value_proc($_POST['page']);
		
		$is_part = value_proc($_POST['is_part']);
		
		$closed = value_proc($_POST['closed']);
		
		
		if($is_part==5)
		{
			// Список проектов
			$projects_list = fill_projects_all_list($page);
		}
		else if($is_part==1)
		{
			// Список проектов
			$projects_list = fill_participation_projects_list($current_user_id, $closed, $page);
		}
		else
		{
			// Список проектов
			$projects_list = fill_projects_list($current_user_id, $closed, $page);
		}
		
		echo $projects_list;
	break;
	
	case 'project_close':
		
		$project_id = value_proc($_POST['project_id']);
		
		$status = value_proc($_POST['status']);
		
		if(!check_project_access_for_user($current_user_id, $project_id, 1))
		{
			exit();
		}
		
		if($status=='open')
		{
			$project_closed = 0;
		}
		else
		{
			$project_closed = 1;
		}
		// Закрываем проект
		$sql = "UPDATE ".PROJECTS_TB." SET project_closed = '$project_closed' WHERE project_id='$project_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			$status_btn_arr = to_iconv_array(get_project_close_btn('', $project_id));
			
			// Возвращаем кнопку закрытия или открытия проекта
			echo json_encode($status_btn_arr);
		}
			
	break;
	
	case 'show_projects_content':
		
		$closed = value_proc($_POST['show_closed']);
		
		$_GET['part'] = value_proc($_POST['is_part']);
		
		$projects_list_content = fill_projects_list_content($closed);
		
		echo $projects_list_content;
		
	break;
	
	case 'get_project_task_comments_block':
		
		$task_id = value_proc($_POST['task_id']);
		
		$comments_block = get_project_task_comments_block($task_id);
		
		echo $comments_block;
		
	break;
	
	// Добавляет отчет
	case 'add_project_task_report':
		
		$task_id = value_proc($_POST['task_id']);
		
		$report_text = value_proc($_POST['report_text'], 1, 1);
		
		// Выбираем задачу
		$sql = "SELECT * FROM ".PROJECT_TASKS_TB." WHERE task_id='$task_id'";
					
		$task_data = $site_db->query_firstrow($sql);
	 
		if(!$task_data['task_id'])
		{ 
			exit();
		}
		
		$project_id = $task_data['project_id'];
		 
		if(!check_project_access_for_user($current_user_id, $project_id))
		{
			exit();
		}
			
		if(!$project_id)
		{
			exit();
		}
		
		// Данные проекта
		$project_data = get_project_data($project_id);
		
		// Если проект был закрыт
		if($project_data['project_closed']==1)
		{
			//exit();
		}
		
		if($report_text=='')
		{
			$error['report_text'] = 1;
		}
		 
		if(!$error)
		{	
			$sql = "SELECT * FROM tasks_projects_tasks WHERE task_id='$task_id'";
			$project_task_data = $site_db->query_firstrow($sql);
			
			// Добавляем отчет 
			$sql = "INSERT INTO ".PROJECTS_TASKS_REPORTS_TB." SET project_id='$project_id', task_id='$task_id', user_id='$current_user_id', report_date=NOW(), report_text='$report_text'";
			
			$site_db->query($sql);
			
			$report_id = $site_db->get_insert_id(); 
			
			$task_title = strlen($project_task_data['task_desc']) > 40 ? substr($project_task_data['task_desc'], 0, 40).'..' : $project_task_data['task_desc'];
			
			$task_title = $task_title ? '"'.$task_title.'"' : '';
			
			// Уведомления
			 project_send_notice_by_email($project_id, 4, array('project_report_text' => $report_text, 'task_title' => $task_title));
			
			$success = 1; 
		}
		
		
	echo json_encode(array('success' => $success, 'error' => $error, 'report_id' => $report_id));
	
	break;
	
	case 'get_project_task_report_item':
		
		$report_id = value_proc($_POST['report_id']);
		
		$task_id = value_proc($_POST['task_id']);
		
		// Выбираем задачу
		$sql = "SELECT * FROM ".PROJECT_TASKS_TB." WHERE task_id='$task_id'";
					
		$task_data = $site_db->query_firstrow($sql);
		
		
		$project_id = $task_data['project_id'];
		
		// Данные проекта
		$project_data = get_project_data($project_id);
		
		if(!check_project_access_for_user($current_user_id, $project_id))
		{
			exit();
		}
		
		// Выбор проектов
		$sql = "SELECT * FROM ".PROJECTS_TB." WHERE project_id='$project_id' AND deleted<>1";
	
		$project_data = $site_db->query_firstrow($sql);
		
		// Выбираем все отчеты для данной работы
		$sql = "SELECT * FROM ".PROJECTS_TASKS_REPORTS_TB." WHERE report_id='$report_id' AND task_id='$task_id'";
		
		$report_data = $site_db->query_firstrow($sql);
		
		// Комментарий
		$report_item = fill_project_task_reports_item($task_data, $report_data);
		
		echo $report_item;
		
	break;
	
	case 'delete_project_task_report':
		
		$report_id = value_proc($_POST['report_id']);
		
		$task_id = value_proc($_POST['task_id']);
		
		// Выбираем все отчеты для данной работы
		$sql = "SELECT * FROM ".PROJECTS_TASKS_REPORTS_TB." WHERE report_id='$report_id' AND task_id='$task_id'";
		
		$report_data = $site_db->query_firstrow($sql);
		
		if($report_data['user_id'] != $current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".PROJECTS_TASKS_REPORTS_TB." SET deleted=1 WHERE report_id='$report_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			echo 1;
		}
		
	break;
	
	case 'restore_project_task_report':
		
		$report_id = value_proc($_POST['report_id']);
		
		$task_id = value_proc($_POST['task_id']);
		
		// Выбираем все отчеты для данной работы
		$sql = "SELECT * FROM ".PROJECTS_TASKS_REPORTS_TB." WHERE report_id='$report_id' AND task_id='$task_id'";
		
		$report_data = $site_db->query_firstrow($sql);
		
		if($report_data['user_id'] != $current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".PROJECTS_TASKS_REPORTS_TB." SET deleted=0 WHERE report_id='$report_id'";
		
	 	$site_db->query($sql);
		
		if(!mysql_error())
		{
			echo 1;
		}
		
	break;
	
	// Принять отчет
	case 'confirm_project_task_report':
		
		$report_id = value_proc($_POST['report_id']);
		
		$task_id = value_proc($_POST['task_id']);
		
		$confirm_all = value_proc($_POST['confirm_all']);
		 
		 
		
		if($confirm_all)
		{
			//$sql = "UPDATE ".PROJECTS_TASKS_REPORTS_TB." SET report_confirm=1 WHERE report_confirm=0 AND project_id='$project_id'";
		
			//$site_db->query($sql);
		}
		else
		{
			$sql = "UPDATE ".PROJECTS_TASKS_REPORTS_TB." SET report_confirm=1 WHERE report_id='$report_id' AND task_id='$task_id'";
		
			$site_db->query($sql);
		}
		
		
		echo 1;
		
	break;
	
	case 'get_project_task_new_reports_count':
		
		$task_id = value_proc($_POST['task_id']);
		
		// Выбираем задачу
		$sql = "SELECT * FROM ".PROJECT_TASKS_TB." WHERE task_id='$task_id'";
					
		$task_data = $site_db->query_firstrow($sql);
		
		$task_reports_new_count = '';
		
		if($task_data['user_id']==$current_user_id || $task_data['added_by_user_id']==$current_user_id)
		{
			$task_reports_new_count = get_new_project_task_reports_count($task_id);
		}
		
		echo $task_reports_new_count;
		
	break;
	
	case 'get_project_edit_form':
		
		$project_id = value_proc($_POST['project_id']);
		
		$edit_form = fill_project_edit_form($project_id);
		
		echo $edit_form;
	break;
	
}

?>