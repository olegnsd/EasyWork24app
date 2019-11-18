<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_sms.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_worktime.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_upl.php';

// Класс авторизации
$auth = new CAuth($site_db);
global $system_sms;
$system_sms=1;
$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

if(!$current_user_id)
{
	exit();
}

switch($mode)
{
	// Список заданий для сотрудника
	case 'add_user_work':
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		} 
		
		$from_user_id = $current_user_id;
		
		$to_user_id = $_POST['to_user_id'];
		
		$work_periodicity = $_POST['periodicity'];
		
		$work_text = value_proc($_POST['work_text']);
		
		$work_sms_notice_to_boss = value_proc($_POST['work_sms_notice_to_boss']);
		
		// Если не является подчиненным или временным подчиненным
		if(!check_user_access_to_user_content($to_user_id, array(0,1,0,0,1))) 
		{  
			exit();
		}
		
		if(!$work_text)
		{
			$error['work_text'] = 1;
		}
		
		if(!$error)
		{
			// Обновляем задание
			$sql = "INSERT INTO ".WORK_TB." (work_from_user_id, work_to_user_id, work_date_add, work_text, work_periodicity_report, work_boss_sms_notice) 
					VALUES ('$from_user_id', '$to_user_id', NOW(), '$work_text', '$work_periodicity', '$work_sms_notice_to_boss')";
			
			$site_db->query($sql);
			
			$work_id = $site_db->get_insert_id();
			 
			// если требуется уведомление об отчетах по смс
			if($work_sms_notice_to_boss)
			{
				$sql = "INSERT INTO tasks_work_sms_notice SET user_id='$current_user_id', work_id='$work_id'";
				 
				$site_db->query($sql);
			}
			
			### Отправка SMS
			
			// Заполянем объект пользователя
			$user_obj->fill_user_data($to_user_id);

			$user_phone = $user_obj->get_user_phone();
			
			### sms body						
			$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/work_new_work.tpl');
			
			$sms_work_text = $work_text;
			
			$sms_work_text = strlen($sms_work_text)>50 ? substr($sms_work_text,0,50).'...' : $sms_work_text;
			
			$PARS['{WORK}'] = $sms_work_text;
			 
			$sms_text = fetch_tpl($PARS, $sms_tpl);
			###\ sms body
			
			// Отправка смс сообщения
			send_sms_msg($user_phone, $sms_text);
			
			$success = 1;
		}
		 
		echo json_encode(array('success' => $success, 'work_id'=> $work_id, 'error' => $error));
	
	break;
	
	// Возвращает актуальную работу и отчеты к ней
	case 'get_user_work':
		
		$work_id = $_POST['work_id'];
		
		$actual_work = fill_work_item($work_id);
		
		$actual_work_report = fill_work_reports_list($work_id);
		
		echo json_encode(array('actual_work' => iconv('cp1251', 'utf-8', $actual_work)));
	break;
	
	// Возвращает отчет постоянной работы
	case 'get_actual_work_reports':
		
		$work_id = $_POST['work_id'];
		
		$work_data = actual_work_data_arr($work_id);
		
		// Пользователь, который установил круг обязанностей
		$work_boss_user_id = $work_data['work_from_user_id'];
	
		$actual_work_report = fill_work_reports_list($work_id, $work_boss_user_id);
		
		echo json_encode(array('work_report' => iconv('cp1251', 'utf-8',  $actual_work_report)));
	break;
	
	// Отметка "Новую работу принял"
	case 'actual_work_noticed':
		
		$work_id = $_POST['work_id'];
		
		if(!is_work_for_user($current_user_id, $work_id))
		{
			exit();
		}
		$sql = "UPDATE ".WORK_TB." SET work_status=1 WHERE work_id='$work_id'";
		
		$site_db->query($sql);
		
		echo 1;
	break;
	
	// Добавляет отчет постоянной работы
	case 'add_report_for_work':
		
		$work_id = $_POST['work_id'];
		
		$report_text = value_proc($_POST['report_text']);
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_content_type = value_proc($_POST['files_content_type']);
		
		if(!is_work_for_user($current_user_id, $work_id) || !$work_id)
		{
			exit();
		}
		if($report_text=='')
		{
			$error['report_text'] = 1;
		}
		if(!$error)
		{
			// Данные задания
			$sql = "SELECT * FROM ".WORK_TB." WHERE work_id='$work_id'";
					
			$work_data = $site_db->query_firstrow($sql);
			
			// Текст сообщения отчета в смс
			$sms_report_text = $report_text;
			
			$sms_report_text = strlen($sms_report_text) > 50 ? substr($sms_report_text, 0, 50).'...' : $sms_report_text;
			
			// Добавляем отчет
			$sql = "INSERT INTO ".WORK_REPORTS_TB." SET work_id='$work_id', report_from_user_id='$current_user_id', report_date=NOW(), report_text='$report_text'";
			
			$site_db->query($sql);
			
			$report_id = $site_db->get_insert_id();
			
			// Делаем отметку о том, что работник принял новую работу
			$sql = "UPDATE ".WORK_TB." SET work_status=1 WHERE work_id='$work_id'";
			
			$site_db->query($sql);
			
			// Привязка файлов к контенту
			attach_files_to_content($report_id, $files_content_type, $files_arr, $current_user_id);
			
			
			$work_report_upload_path = WORK_REPORTS_PATH.'/'.$report_id;
			
			/*// Создаем папку для изображений
			if($files_arr)
			{
				mkdir($work_report_upload_path);
			}
			 
			// Добавляем файлы
			foreach($files_arr as $file_new_name => $file_name)
			{
				if($file_name)
				{
					$file_name_s = value_proc($file_name);
					$file_new_name_s = value_proc($file_new_name);
				
					// Добавляем файлы отчетов
					$sql = "INSERT INTO ".WORK_FILES_TB." (file_new_name, file_name, report_id, user_id, date) VALUES ('$file_new_name_s', '$file_name_s', '$report_id', '$current_user_id', NOW())";
					
					$site_db->query($sql);
					
					copy(TEMP_PATH.'/'.$file_new_name, $work_report_upload_path.'/'.$file_new_name);
					
					unlink(TEMP_PATH.'/'.$file_new_name);
				}
			}*/
			
			$boss_arr = get_current_user_users_arrs(array(1,0,0,1,0));
			
			// выбор руководителей, кому присылать отчеты по смс
			$sql = "SELECT * FROM tasks_work_sms_notice WHERE work_id='$work_id'";
			
			$res = $site_db->query($sql);
		 
			while($row=$site_db->fetch_array($res))
			{ 
			  
				if(!in_array($row['user_id'], $boss_arr))
				{
					continue;
				}
				
				$user_obj->fill_user_data($row['user_id']);
				
				$user_phone = $user_obj->get_user_phone();
				
				### sms body
				$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/work_report.tpl');
				
				$user_obj->fill_user_data($current_user_id);
				
				$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
				
				$PARS['{USER_NAME}'] = $user_obj->get_user_name();
				
				$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
				
				$PARS['{REPORT_TEXT}'] = $sms_report_text;
				 
				$sms_text = fetch_tpl($PARS, $sms_tpl);
				###\ sms body
					
				// Отправка смс сообщения
				send_sms_msg($user_phone, $sms_text);
			}
			
			
			
			
			// Ставим отмеку, что в этот день сотрудник работал	
			set_work_activity_at_day();
				
			$success = 1;
		}
		
		echo json_encode(array('success' => $success, 'error' => $error, 'report_id' => $report_id));
		
	break;
	
	// Возвращает кол-во постоянный работ для пользователя
	case 'get_history_work_count':
		
		$user_id = $_POST['user_id'];
		
		$count = get_user_work_count($user_id);
		
		$count = $count ? $count - 1 : $count;
		
		echo json_encode(array('history_count' => $count));
		
	break;
	
	// Получает список истории круга обязанностей
	case 'get_history_work_list':
		
		$user_id = $_POST['user_id'];
		
		$page = $_POST['page'];
		
		// Актуальный круг обязанностей для человека 
		$actual_user_work_data = get_actual_work_id_for_user_arr($user_id);
		$actual_work_id = $actual_user_work_data['work_id'];
		
		// Список кругов обязанностей 
		$history_list = get_history_work_list($user_id, $page, $actual_work_id);
		
		echo $history_list;
		
	break;
	
	// Принять отчет
	case 'confirm_work_report':
		
		$user_id = $_POST['user_id'];
		
		$report_id = value_proc($_POST['report_id']);
		
		$work_id = value_proc($_POST['work_id']);
		
		$sql = "UPDATE ".WORK_REPORTS_TB." SET report_confirm=1 WHERE report_id='$report_id' AND work_id='$work_id'";
		
		$site_db->query($sql);
		
		echo 1;
		
	break;
	
	case 'change_new_work_reports_notice':
		
		$work_id = $_POST['work_id'];
		
		$sms_notice = $_POST['sms_notice'];
		
		if(check_work_sms_notice_for_user($work_id, $current_user_id) && !$sms_notice)
		{
			$sql = "DELETE FROM tasks_work_sms_notice WHERE work_id='$work_id' AND user_id='$current_user_id'";
			$site_db->query($sql);
		}
		else if($sms_notice)
		{
			$sql = "INSERT INTO tasks_work_sms_notice SET work_id='$work_id', user_id='$current_user_id'";
			$site_db->query($sql);
		}
		
		//$sql = "UPDATE ".WORK_TB." SET  work_boss_sms_notice='$sms_notice' WHERE work_id='$work_id' AND work_from_user_id='$current_user_id' ";
		
		//$site_db->query($sql);
	 
		if(!mysql_error())
		{
			echo 1;
		}
		
	break;
	
	// Возвращает список клиентов
	case 'get_more_work_reports':
		
		$user_id = value_proc($_POST['user_id']);
		 
		$page = value_proc($_POST['page']);
		
		$work_id = value_proc($_POST['work_id']);
		
		// Данные задания
		$sql = "SELECT * FROM ".WORK_TB." WHERE work_id='$work_id'";
					
		$work_data = $site_db->query_firstrow($sql);
		
		// Если не является подчиненным или временным подчиненным
		if($current_user_id!=$work_data['work_from_user_id'] && $current_user_id!=$work_data['work_to_user_id'] &&!check_user_access_to_user_content($work_data['work_to_user_id'], array(0,1,0,0,	1))) 
		{  
		 
			exit();
		}
			
		// Список отчетов
		$reports_list = fill_work_reports_list($work_data, $page);
		
		echo $reports_list;
		
	break;
	
	case 'get_work_report_item':
		
		$report_id = value_proc($_POST['report_id']);
		
		// Выбираем все отчеты для данной работы
		$sql = "SELECT * FROM ".WORK_REPORTS_TB." WHERE report_id='$report_id'";
		
		$report_data = $site_db->query_firstrow($sql);
		
		
		// Данные задания
		$sql = "SELECT * FROM ".WORK_TB." WHERE work_id='".$report_data['work_id']."'";
					
		$work_data = $site_db->query_firstrow($sql);
		
		// Если не является подчиненным или временным подчиненным
		if($current_user_id!=$work_data['work_from_user_id'] && $current_user_id!=$work_data['work_to_user_id'] &&!check_user_access_to_user_content($work_data['work_to_user_id'], array(0,1,0,0,1))) 
		{  
		 
			exit();
		}
		
		$report_item = fill_work_report_item($report_data, $work_data);
		
		echo $report_item;
		
	break;
	
}

?>