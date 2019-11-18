<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_sms.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deputy.php';

// Класс авторизации
$auth = new CAuth($site_db);
global $system_sms;
$system_sms=1;
$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

switch($mode)
{
	// Список заданий для сотрудника
	case 'get_tasks_list':
		
		$user_id = value_proc($_POST['to_user_id']);
		
		$search_word = value_proc($_POST['search_word']);
		
		$date = value_proc($_POST['date']);
		
		$is_tasks_to_users = value_proc($_POST['is_tasks_to_users']);
		
		if($user_id!=$current_user_id && !check_user_access_to_user_content($user_id, array(1,1,0,1,1)) && !$is_tasks_to_users)
		{
			exit();
		}
		
		if($is_tasks_to_users)
		{  
			// Получаем список заданий для пользователя
			$tasks_list = fill_tasks_from_user_to_users_tasks_list($current_user_id, $search_word);
		}
		// Если актуальный пользователь просматривает страницу свои задачи 
		else if($user_id==$current_user_id)
		{
			// Получаем список заданий для пользователя
			$tasks_list = fill_tasks_my_list($user_id, $date, $search_word);
		}
		else
		{
			// Получаем список заданий для пользователя
			$tasks_list = fill_worker_tasks_list($user_id, $date, $search_word);
		}
		
		echo $tasks_list;
	
	break;
	
	// Добавить задание для сотрудника
	case 'add_new_task':
	
		$to_user_id = value_proc($_POST['to_user_id']);
		
		// Тема задачи
		$task_theme = substr(value_proc($_POST['task_theme']),0,80);
		
		// Текст задачи
		$task_text = value_proc($_POST['task_text']);
		
		// Дата задачи
		$task_date = value_proc($_POST['task_date']);
		
		// Максимальное время выполнения 
		$task_max_date = value_proc($_POST['task_max_date']);
		
		// Желаемое время выполнения
		$task_desired_days = value_proc($_POST['task_desired_days']);
		
		// Приоритет задачи
		$task_priority = value_proc($_POST['task_priority']);
		
		// Сложность задачи
		$task_difficulty = value_proc($_POST['task_difficulty']);
		
		// Уведомление начальнкиа по смс
		$task_sms_notice_to_boss = value_proc($_POST['task_sms_notice_to_boss']);
		
		$task_link_deal = str_replace('-s-','',value_proc($_POST['task_link_deal']));
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_content_type = value_proc($_POST['files_content_type']);
		
		// Если сотрудник не является ПОСТОЯННЫМ подчиненным или ВРЕМЕННЫМ подчиненным
		if(!check_user_access_to_user_content($to_user_id, array(0,1,0,0,1)) && $current_user_id!=$to_user_id)
		{
			exit();
		}
		
		// Задание пустое
		if($task_text=='')
		{
			$error['text'] = 1;
		}
		
		// Если дата указана
		if($task_date)
		{
			$date_arr_tmp = split('\.', $task_date);
			
			// Перевод в таймстамп
			$date_mktime = mktime(0,0,0, $date_arr_tmp[1], $date_arr_tmp[0], $date_arr_tmp[2]);
			
			// Актуальная дата в таймстамп
			$now_date_mktime = mktime(0,0,0, date('m'), date('d'), date('Y'));
			
			// Если выставленная дата меньше акутальной
			if($date_mktime < $now_date_mktime)
			{
				 $error['date_start'] = 1;
			}
			
			// Форматируем дату
			$task_date = formate_to_norm_date($task_date, 1);
		}
		else
		{
			$task_date = date('Y-m-d');
		}
			
		// Форматируем дату
		$task_max_date = formate_to_norm_date($task_max_date, 1);
		 
		if($task_max_date)
		{ 
			if(to_mktime($task_max_date) < to_mktime($task_date))
			{ 
				$error['date'] = 3;
			}
			else
			{
				// До конца дня включительно
				$task_max_date .= ' 23:59:00';
			}
		}
		// Желаемое время выполнения задания
		if($task_desired_days)
		{
			$task_desired_date = days_to_date_after_date($task_desired_days, $task_date);
			// До конца дня включительно
			$task_desired_date .= ' 23:59:00';
		}
		if(!$error)
		{
			$task_status = 0;
			$task_in_proc_date = '';
			// Если добавляет задание сам себе, опредялем некоторые переменные
			if($to_user_id==$current_user_id)
			{
				$task_status = 2;
				$task_in_proc_date = date('Y-m-d H:i:s');
			}
		
			// Добавляем задание
			$sql = "INSERT INTO ".TASKS_TB." (task_from_user, task_to_user, task_theme, task_text, task_date_add, task_date, task_date_1, task_priority, task_max_date, task_desired_date, task_difficulty, task_status, task_in_proc_date, task_boss_sms_notice)
					VALUES ('$current_user_id', '$to_user_id', '$task_theme', '$task_text', NOW(),  '$task_date', '$task_date', '$task_priority', '$task_max_date', '$task_desired_date', '$task_difficulty', '$task_status', '$task_in_proc_date', '$task_sms_notice_to_boss')";
					
			$site_db->query($sql);
			
			$inserted_task_id = $site_db->get_insert_id($sql);
			
			if($task_link_deal)
			{
				// Делаем линк на задачу
				$sql = "INSERT INTO ".TASKS_DEALS_LINKS_TB." SET task_id='$inserted_task_id', deal_id='$task_link_deal'";
				
				$site_db->query($sql);
			}
			
			// Привязка файлов к контенту
			attach_files_to_content($inserted_task_id, $files_content_type, $files_arr, $current_user_id);
			
			### Отправка SMS
			if($to_user_id!=$current_user_id)
			{
				// Заполянем объект пользователя
				$user_data = $user_obj->fill_user_data($to_user_id);
		
				$user_phone = $user_obj->get_user_phone();
				
				### sms body
				$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/task_new_task.tpl');
				
				$sms_text = $task_theme ? $task_theme : $task_text;
				
				$sms_text = strlen($sms_text)>50 ? substr($sms_text,0,50).'...' : $sms_text;
				
				$PARS['{TASK_TEXT}'] = $sms_text;
				 
				$sms_text = fetch_tpl($PARS, $sms_tpl);
				###\ sms body
				
				// Отправка смс сообщения
				send_sms_msg($user_phone, $sms_text);
				
			}
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error));
			
	break;

	
	// Удалить задание
	case 'delete_task':
		
		$task_id = value_proc($_POST['task_id']);
		
		// Если не является автором задания
		if(!is_author_of_task($current_user_id, $task_id))
		{
			exit();
		}
		
		$sql = "UPDATE ".TASKS_TB." SET task_deleted=1 WHERE task_from_user='$current_user_id' AND task_id='$task_id'";
		
		$site_db->query($sql);
		
		echo 1;
		
	break;
	
	// Восстановить задание
	case 'restore_task':
		
		$task_id = value_proc($_POST['task_id']);
		
		// Если не является автором задания
		if(!is_author_of_task($current_user_id, $task_id))
		{
			exit();
		}
		
		$sql = "UPDATE ".TASKS_TB." SET task_deleted=0 WHERE task_from_user='$current_user_id' AND task_id='$task_id'";
		
		$site_db->query($sql);
		
		echo 1;
		
	break;
	
	// Форма редактирования задания
	case 'get_edit_task_form':
		
		$task_id = value_proc($_POST['task_id']);
		
		// Если не является автором задания
		if(!is_author_of_task($current_user_id, $task_id))
		{
			exit();
		}
		// Получает форму для редактирования задания
		$task_edit_form = fill_task_edit_form($task_id);
		
		echo $task_edit_form;
		
	break;
	
		// Задания в обычной форме
	case 'get_task_list_item':
		
		$task_id = value_proc($_POST['task_id']);
		
		// Если не является автором задания
		if(!is_author_of_task($current_user_id, $task_id))
		{ 
			exit();
		}
		
		// Получаем задачу
		$sql = "SELECT * FROM ".TASKS_TB." WHERE task_id='$task_id'";
		
		$task_data = $site_db->query_firstrow($sql);
		
		// задание самому себе
		if(is_task_myself($task_data, $current_user_id))
		{
			$task_item = fill_tasks_my_list_item($task_data);
		}
		else
		{
			$task_item = fill_worker_tasks_list_item($task_data);
		}
		 
		
		echo $task_item;
		
	break;
	
	// Сохранить изменения в задаче
	case 'save_edit_task':
		
		$task_id = value_proc($_POST['task_id']);
		
		// Текст задачи
		$task_text = value_proc($_POST['task_text']);
		
		// Тема задачи
		$task_theme = substr(value_proc($_POST['task_theme']),0,80);
		
		// Приоритет задачи
		$task_priority = value_proc($_POST['task_priority']);
		
		// Сложность задачи
		$task_difficulty = value_proc($_POST['task_difficulty']);
		
		// Максимальное время выполнения 
		$task_max_date = value_proc($_POST['task_max_date']);
		
		// Форматируем дату
		$task_max_date = formate_to_norm_date($task_max_date, 1);
		
		$task_sms_notice_to_boss = value_proc($_POST['task_sms_notice_to_boss']);
		
		$task_link_deal = str_replace('-s-','',value_proc($_POST['task_link_deal']));
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_deleted = json_decode(str_replace('\\', '', $_POST['files_deleted']));
		$files_content_type = value_proc($_POST['files_content_type']); 
		
		// Данные задания
		$sql = "SELECT * FROM ".TASKS_TB." WHERE task_id='$task_id'";
					
		$task_data = $site_db->query_firstrow($sql);
			
		if($task_max_date)
		{ 
			if(to_mktime($task_max_date) < to_mktime($task_data['task_date']))
			{ 
				$error['date'] = 3;
			}
			else
			{
				// До конца дня включительно
				$task_max_date .= ' 23:59:00';
			}
		}
		
		// Если не является автором задания
		if(!is_author_of_task($current_user_id, $task_id))
		{
			exit();
		}
		
		// Если задача пустая
		if($task_text=='')
		{
			$error['text'] = 1;
		}
		
		if(!$error)
		{			
			// Обновляем запись
			$sql = "UPDATE ".TASKS_TB." SET task_theme='$task_theme', task_text='$task_text', task_date_edit=NOW(), 
					task_priority='$task_priority', task_difficulty='$task_difficulty',
					task_quality='0', task_max_date = '$task_max_date', task_boss_sms_notice='$task_sms_notice_to_boss'
					WHERE task_id='$task_id' AND task_from_user='$current_user_id'";
			
			$site_db->query($sql);
			
			
			// Привязка файлов к контенту
			attach_files_to_content($task_id, $files_content_type, $files_arr);
			
			// Удаляем прикрепленные файлы
			delete_attached_files_to_content($task_id, $files_content_type, $files_deleted);
			
			
			// Проверяем связь со сделкой
			$sql = "SELECT link_id FROM ".TASKS_DEALS_LINKS_TB." WHERE task_id='$task_id'";
				
			$task_link = $site_db->query_firstrow($sql);
				
			if($task_link_deal>0)
			{
				if($task_link['link_id'])
				{
					// Обновляем линк задачи на сделку
					$sql = "UPDATE ".TASKS_DEALS_LINKS_TB." SET deal_id='$task_link_deal' WHERE link_id='".$task_link['link_id']."'";
					$site_db->query($sql);
				}
				else
				{
					// Делаем линк задачи на сделку
					$sql = "INSERT INTO ".TASKS_DEALS_LINKS_TB." SET task_id='$task_id', deal_id='$task_link_deal'";
			
					$site_db->query($sql);
				}
			}
			// Если связь со сделкой была и ее требуется удалить
			else if($task_link['link_id'] && !$task_link_deal)
			{
				$sql = "DELETE FROM ".TASKS_DEALS_LINKS_TB." WHERE link_id='".$task_link['link_id']."'";	
				
				$site_db->query($sql);
			}
			
			### Отправка SMS
			if(!is_task_myself($task_data, $current_user_id))
			{
				// Заполянем объект пользователя
				$user_data = $user_obj->fill_user_data($task_data['task_to_user']);
		
				$user_phone = $user_obj->get_user_phone();
				
				### sms body
				$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/task_edit_task.tpl');
				
				$sms_text = $task_theme ? $task_theme : $task_text;
				
				$sms_text = strlen($sms_text)>50 ? substr($sms_text,0,50).'...' : $sms_text;
				
				$PARS['{TASK_TEXT}'] = $sms_text;
				 
				$sms_text = fetch_tpl($PARS, $sms_tpl);
				###\ sms body
				
				// Отправка смс сообщения
				send_sms_msg($user_phone, $sms_text);
			}
		
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error));
		
	break;
	
	// Получает список дат задач
	case 'get_tasks_dates':
		
		$to_user_id = value_proc($_POST['to_user_id']);
		
		// Формирование блока календаря
		// Выбираем все даты задач
		$sql = "SELECT task_date FROM tasks_user_tasks WHERE task_from_user = '$current_user_id' AND task_to_user='$to_user_id' AND task_deleted<>1";
		$res = $site_db->query($sql);
			
		while($row=$site_db->fetch_array($res))
		{
			$dates_array[] = "".$row['task_date']."";
		}
		
		echo implode(',', $dates_array);
		
	break;
	
	
	// Отметка о прочтении задания
	case 'task_status':
		
		$task_id = value_proc($_POST['task_id']);
		
		$status = value_proc($_POST['status']);
		
		$is_boss = value_proc($_POST['is_boss']);
		 
		// Данные задания
		$sql = "SELECT * FROM ".TASKS_TB." WHERE task_id='$task_id'";
				
		$task_data = $site_db->query_firstrow($sql);
		
		$sms_task_text = $task_data['task_theme'] ? $task_data['task_theme'] : $task_data['task_text'];
		
		$sms_task_text = strlen($sms_task_text)>50 ? substr($sms_task_text,0,50).'...' : $sms_task_text;
		 
		switch($status)
		{
			// Задание принял
			case '1':
			
				$sql = "UPDATE  ".TASKS_TB." SET task_status = 1, task_confirm_date=NOW() WHERE task_to_user='$current_user_id' AND task_id='$task_id'";
		
				$site_db->query($sql);
				
				// Кол-во новых задач для сотрудника
				$new_tasks_count = get_new_tasks_count($current_user_id);
				
				### Отправка SMS
				if($task_data['task_boss_sms_notice'])
				{
					// Заполянем объект пользователя, кто выдал задание
					$user_obj->fill_user_data($task_data['task_from_user']);
					
					$user_phone = $user_obj->get_user_phone();
					
					### sms body
					$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/task_status_confirm.tpl');
					
					$user_obj->fill_user_data($current_user_id);
					
					$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
					
					$PARS['{USER_NAME}'] = $user_obj->get_user_name();
					
					$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
					
					$PARS['{TASK_TEXT}'] = $sms_task_text;
					 
					$sms_text = fetch_tpl($PARS, $sms_tpl);
					###\ sms body
					
					// Отправка смс сообщения
					send_sms_msg($user_phone, $sms_text);
				}
				 
				// Возвращаем результат
				echo json_encode(array('success' => 1, 'new_tasks_count' => $new_tasks_count));
		
			break;
			
			// Выполняется
			case '2':
				
				// Если задание имеет уже статус НЕ могу выполнить, чтобы не сбрасывать счетчик времени не обновляем дату Выполняется
				if(($task_data['task_status']!=4 ||  $task_data['task_status']==4) && $task_data['task_in_proc_date'] == '0000-00-00 00:00:00')
				{
					$and_task_in_proc_date = ", task_in_proc_date=NOW()";
				}
				
				$sql = "UPDATE  ".TASKS_TB." SET task_status = 2 $and_task_in_proc_date WHERE task_to_user='$current_user_id' AND task_id='$task_id'";
		 
				$site_db->query($sql);
				
				### Отправка SMS
				if($task_data['task_boss_sms_notice'])
				{
					// Заполянем объект пользователя, кто выдал задание
					$user_obj->fill_user_data($task_data['task_from_user']);
					
					$user_phone = $user_obj->get_user_phone();
						
					### sms body
					$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/task_status_proc.tpl');
					
					// Заполянем объект пользователя, кто принял задание
					$user_obj->fill_user_data($current_user_id);
					
					$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
					
					$PARS['{USER_NAME}'] = $user_obj->get_user_name();
					
					$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
					
					$PARS['{TASK_TEXT}'] = $sms_task_text;
					 
					$sms_text = fetch_tpl($PARS, $sms_tpl);
					###\ sms body
					
					// Отправка смс сообщения
					send_sms_msg($user_phone, $sms_text);
				}
				
				// Возвращаем результат
				echo json_encode(array('success' => 1));
				
			break;
			
			// Выполнено
			case '3':
				
				$task_finished_confirm = 0;
				
				// задание самому себе
				if(is_task_myself($task_data, $current_user_id))
				{
					$task_finished_confirm = 1;
				}
				
				$sql = "UPDATE  ".TASKS_TB." SET task_status = 3, task_finished_date=NOW(), task_finished_confirm='$task_finished_confirm' WHERE task_to_user='$current_user_id' AND task_id='$task_id'";
		
				$site_db->query($sql);
				
				### Отправка SMS
				if(!is_task_myself($task_data, $current_user_id) && $task_data['task_boss_sms_notice'])
				{
					// Заполянем объект пользователя, кто выдал задание
					$user_obj->fill_user_data($task_data['task_from_user']);
					
					$user_phone = $user_obj->get_user_phone();
					
					### sms body
					$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/task_status_finish.tpl');
					
					// Заполянем объект пользователя, кто выполнил задание
					$user_obj->fill_user_data($current_user_id);
					
					$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
					
					$PARS['{USER_NAME}'] = $user_obj->get_user_name();
					
					$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
					
					$PARS['{TASK_TEXT}'] = $sms_task_text;
					 
					$sms_text = fetch_tpl($PARS, $sms_tpl);
					###\ sms body
						
					// Отправка смс сообщения
					send_sms_msg($user_phone, $sms_text);
				}
				// Возвращаем результат
				echo json_encode(array('success' => 1));
				
			break;
			
			// "Не могу выполнить задание"
			case '4':
				
				$sql = "UPDATE  ".TASKS_TB." SET task_status = 4 WHERE task_to_user='$current_user_id' AND task_id='$task_id'";
		
				$site_db->query($sql);
				
				### Отправка SMS
				// Заполянем объект пользователя, кто выдал задание
				$user_obj->fill_user_data($task_data['task_from_user']);
				
				$user_phone = $user_obj->get_user_phone();
				
				### sms body
				if($task_data['task_boss_sms_notice'])
				{
					$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/task_status_not_finish.tpl');
					
					$user_obj->fill_user_data($current_user_id);
					
					$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
					
					$PARS['{USER_NAME}'] = $user_obj->get_user_name();
					
					$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
					
					$PARS['{TASK_TEXT}'] = $sms_task_text;
					 
					$sms_text = fetch_tpl($PARS, $sms_tpl);
					###\ sms body
					
					// Отправка смс сообщения
					send_sms_msg($user_phone, $sms_text);
				}
				
				// Возвращаем результат
				echo json_encode(array('success' => 1));
				
			break;
			
			// кнопка для начальника "С заданием не справился"
			case '5':
				
				$sql = "UPDATE  ".TASKS_TB." SET task_status = 5, task_finished_fail_date=NOW(), task_finished_confirm=0
						WHERE task_from_user='$current_user_id' AND task_id='$task_id'";
		
				$site_db->query($sql);
				
				// Принимаем все отчеты по задаче
				confirm_all_task_reports($task_data, $current_user_id);
				
				### Отправка SMS
				// Заполянем объект пользователя, кто выдал задание
				$user_obj->fill_user_data($task_data['task_to_user']);
				
				$user_phone = $user_obj->get_user_phone();
					
				### sms body
				$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/task_fail.tpl');
				
				$PARS['{TASK_TEXT}'] = $sms_task_text;
				 
				$sms_text = fetch_tpl($PARS, $sms_tpl);
				###\ sms body
				
				// Отправка смс сообщения
				send_sms_msg($user_phone, $sms_text);
					
				// Возвращаем результат
				echo json_encode(array('success' => 1));
				
			break;
			
			// НЕ подтвержать выполнение работы и скинуть статус работы до Выполняется
			case '-3':
				
				$sql = "UPDATE  ".TASKS_TB." SET task_status = 2, task_finished_confirm=0 WHERE task_id='$task_id'";
		
				$site_db->query($sql);
				
				### Отправка SMS
				if(!is_task_myself($task_data, $current_user_id))
				{
					if($is_boss)
					{  
						// Заполянем объект пользователя, кто выдал задание
						$user_obj->fill_user_data($task_data['task_to_user']);
						
						$user_phone = $user_obj->get_user_phone();
						
						### sms body
						$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/task_finish_not_confirm.tpl');
						
						// Заполянем объект пользователя, кто отклонил выполнение задание
						$user_obj->fill_user_data($current_user_id);
				
						$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
						
						$PARS['{USER_NAME}'] = $user_obj->get_user_name();
						
						$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
				
						$PARS['{TASK_TEXT}'] = $sms_task_text;
						 
						$sms_text = fetch_tpl($PARS, $sms_tpl);
						###\ sms body
						
						// Отправка смс сообщения
						send_sms_msg($user_phone, $sms_text);
						
					}
					/*else
					{ 
						// Заполянем объект пользователя, кто выдал задание
						$user_obj->fill_user_data($task_data['task_from_user']);
						
						$to_user_phone = $user_obj->get_user_phone();
							
						// Заполянем объект пользователя, кто принял задание
						$user_obj->fill_user_data($current_user_id);
							
							
						$user_name = urlencode(translit($user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename()));
							 
						file_get_contents('http://api.avisosms.ru/sms/get/?username=fridz&password=zuzda240289&destination_address='.$to_user_phone.'&source_address='.SMS_FROM.'&message='.$user_name.'-otklonil_vipolnenie_zadaniya'.$sms_task_text);
					}*/
				}
				// Возвращаем результат
				echo json_encode(array('success' => 1));
				
			break;
			
			// Не выполняется
			case '-2':
				
				// Если задание имеет уже статус НЕ могу выполнить, чтобы не сбрасывать счетчик времени не обновляем дату Выполняется
				if($task_data['task_status']!=4 ||  ($task_data['task_status']==4 && $task_data['task_in_proc_date'] == '0000-00-00 00:00:00'))
				{
					//$and_task_in_proc_date = ", task_in_proc_date=NOW()";
				}
				
				$sql = "UPDATE  ".TASKS_TB." SET task_status = 1 $and_task_in_proc_date WHERE task_to_user='$current_user_id' AND task_id='$task_id'";
		 
				$site_db->query($sql);
				
				### Отправка SMS
				if($task_data['task_boss_sms_notice'])
				{
					// Заполянем объект пользователя, кто выдал задание
					$user_obj->fill_user_data($task_data['task_from_user']);
					
					$user_phone = $user_obj->get_user_phone();
						
					### sms body
					$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/task_status_not_in_proc.tpl');
									
					// Заполянем объект пользователя, кто принял задание
					$user_obj->fill_user_data($current_user_id);
					
					$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
					
					$PARS['{USER_NAME}'] = $user_obj->get_user_name();
					
					$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
					
					$PARS['{TASK_TEXT}'] = $sms_task_text;
					 
					$sms_text = fetch_tpl($PARS, $sms_tpl);
					###\ sms body
					
					// Отправка смс сообщения
					send_sms_msg($user_phone, $sms_text);
				}
				 
				// Возвращаем результат
				echo json_encode(array('success' => 1));
				
			break;
			
		}
		
		
	break;
	
	// Получает статусный блок задачи
	case 'get_task_status_bar':
		
		$task_id = value_proc($_POST['task_id']);
		
		// Данные задания
		$sql = "SELECT * FROM ".TASKS_TB." WHERE task_id='$task_id'";
		
		$task_data = $site_db->query_firstrow($sql);
		
		// задание самому себе
		if(is_task_myself($task_data, $current_user_id))
		{
			$status_bar = fill_task_status_bar1($task_data, 1);
		}
		else
		{
			$status_bar = fill_task_status_bar1($task_data, 0);
		}
		 
		
		echo $status_bar;
		
	break;
	
	// Добавляет отчет
	case 'add_task_report':
		
		$task_id = value_proc($_POST['task_id']);
		
		$report_text = value_proc($_POST['report_text']);
		
		$by_sms = value_proc($_POST['by_sms']);
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_content_type = value_proc($_POST['files_content_type']);
		
		if(!$task_id || !$current_user_id)
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
			$sql = "SELECT * FROM ".TASKS_TB." WHERE task_id='$task_id'";
			
			$task_data = $site_db->query_firstrow($sql);
		
			// Ошибки
			if(($current_user_id!=$task_data['task_from_user'] && $current_user_id!=$task_data['task_to_user']))
			{
				exit();
			}
			
			// Добавляем отчет о задаче
			$sql = "INSERT INTO ".TASKS_REPORTS_TB." SET task_id='$task_id', report_user_id='$current_user_id', report_date=NOW(), report_text='$report_text'";
			
			$site_db->query($sql);
			
			$report_id = $site_db->get_insert_id();
			
			// персчитываем кол-во отчетов с целью установить флаг уведомления о новых отчетах
			if($task_data['task_from_user']!=$task_data['task_to_user'])
			{
				if($task_data['task_from_user']==$current_user_id)
				{
					task_has_new_reports_flag_for_task_to_user($task_id);
				}
				else if($task_data['task_to_user']==$current_user_id)
				{
					task_has_new_reports_flag_for_task_from_user($task_id);
				}
			}
			
			// Привязка файлов к контенту
			attach_files_to_content($report_id, $files_content_type, $files_arr);
			
			$success = 1;
						
			$sms_report_text = $report_text;
					
			if(strlen($sms_report_text)>50)
			{
				$sms_report_text = substr($sms_report_text,0,50).'...';
			}
			
			// Если отмечен чекбокс продублировать по смс
			if($by_sms)
			{
				// Начальник комментирует
				### Отправка SMS
				if($current_user_id==$task_data['task_from_user'] && !is_task_myself($task_data, $current_user_id))
				{
					// Заполянем объект пользователя, кому отправляем отчет
					$user_obj->fill_user_data($task_data['task_to_user']);
					$user_phone = $user_obj->get_user_phone();
					
					### sms body
					$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/task_report_comment.tpl');
					
					$user_obj->fill_user_data($current_user_id);
				
					$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
					
					$PARS['{USER_NAME}'] = $user_obj->get_user_name();
					
					$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
				
					$PARS['{COMMENT}'] = $sms_report_text;
					 
					$sms_text = fetch_tpl($PARS, $sms_tpl);
					###\ sms body
				
					// Отправка смс сообщения
					send_sms_msg($user_phone, $sms_text);
				}
				 
				
				### Отправка SMS
				if($current_user_id!=$task_data['task_from_user'] && $task_data['task_boss_sms_notice'])
				{
					// Заполянем объект пользователя, кому отправляем отчет
					$user_obj->fill_user_data($task_data['task_from_user']);
					$user_phone = $user_obj->get_user_phone();
					
					### sms body
					$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/task_report.tpl');
					
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
			}
		}
		
		
		echo json_encode(array('success' => $success, 'error' => $error));
		
	break;
	
	// Получить список отчетов для задачи
	case 'get_task_report_list':
		
		$task_id = value_proc($_POST['task_id']);
		
		$task_reports_list = fill_task_reports_list1($task_id);
		
		echo $task_reports_list;
		
		
	break;
	
	// выставить качество проделанной работы
	case 'edit_task_quality':
		
		$task_id = value_proc($_POST['task_id']);
		
		$quality = value_proc($_POST['quality']);
		
		if(!$task_id || !is_author_of_task($current_user_id, $task_id))
		{
			exit();
		}
		
		$sql = "UPDATE ".TASKS_TB." SET task_quality='$quality' WHERE task_id='$task_id'";
		
		$site_db->query($sql);
		
		echo 1;
		
	break;
	
	// Подтвердить выполнение задания
	case 'confirm_finished_task':
	
		$task_id = value_proc($_POST['task_id']);
		
		// Получаем задачу
		$sql = "SELECT * FROM ".TASKS_TB." WHERE task_id='$task_id'";
		
		$task_data = $site_db->query_firstrow($sql);
		
		// Если не является автором задания
		if(!is_author_of_task($current_user_id, $task_id))
		{
			exit();
		}
		$sql = "UPDATE ".TASKS_TB." SET task_finished_confirm=1 WHERE task_id='$task_id'";
		
		$site_db->query($sql);
		
		// Принимаем все отчеты к задания
		confirm_all_task_reports($task_data, $current_user_id);
		
		### sms body		
		 
		$sms_task_text = $task_data['task_theme'] ? $task_data['task_theme'] : $task_data['task_text'];
		
		if(strlen($sms_task_text)>50)
		{
			$sms_task_text = substr($sms_task_text,0,50).'...';
		}
		
		// Заполянем объект пользователя, кому отправляем отчет
		$user_obj->fill_user_data($task_data['task_to_user']);
		$user_phone = $user_obj->get_user_phone();
		
		### sms body
		$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/task_finish_confirm.tpl');
		
		$user_obj->fill_user_data($current_user_id);
		
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
		$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{TASK_TEXT}'] = $sms_task_text;
		 
		$sms_text = fetch_tpl($PARS, $sms_tpl);
		###\ sms body
		
		// Отправка смс сообщения
		send_sms_msg($user_phone, $sms_text);
		 
				
		echo 1;
	break;
	
		// Принять отчет
	case 'confirm_task_report':
		
		$user_id = value_proc($_POST['user_id']);
		
		$report_id = value_proc($_POST['report_id']);
		
		$task_id = value_proc($_POST['task_id']);
		
		// Получаем задачу
		$sql = "SELECT * FROM ".TASKS_TB." WHERE task_id='$task_id'";
		
		$task_data = $site_db->query_firstrow($sql);
		 
		$sql = "UPDATE ".TASKS_REPORTS_TB." SET report_confirm=1 WHERE report_id='$report_id' AND task_id='$task_id'";
		
		$site_db->query($sql);
		
		// Если начальник принимает все отчеты для задания, то персчитываем кол-во отчетов для начальника с целью установить флаг уведомления о новых отчетах
		if($task_data['task_from_user']==$current_user_id)
		{
			task_has_new_reports_flag_for_task_from_user($task_id);
		}
		else if($task_data['task_to_user']==$current_user_id)
		{
			task_has_new_reports_flag_for_task_to_user($task_id);
		}
	 
		
		
		$success = 1;
		
		echo $success;
		
		
	break;
	
	// Кол-во новых отчетов о задачах начальника
	case 'get_new_task_reports_count':
	
		$user_id = value_proc($_POST['user_id']);
		
		$task_id = value_proc($_POST['task_id']);
		
		// Кол-во всех новых отчетов по задачам от начальника
		//$new_reports_count = get_new_task_reports_count_by_users(0, $user_id);
		
		// Получаем задачу
		$sql = "SELECT * FROM ".TASKS_TB." WHERE task_id='$task_id'";
		
		$task_data = $site_db->query_firstrow($sql);
		
		// Если начальник
		if($task_data['task_from_user']==$current_user_id)
		{
			$is_boss = 1;
		}
		
		// Кол-во новых отчетов для задачи
		$task_new_reports_count = get_new_task_reports_count_for_user($task_id, $is_boss);
				
		echo json_encode(array('all_report_count' => $new_reports_count, 'task_report_count' => $task_new_reports_count));
		
		
	break;
	
	// Кол-во задач, над которыми необходимы действия со стороны начальства
	case 'get_new_count_tasks_to_act':
	
		$new_count_tasks_to_act = get_new_count_tasks_to_act($current_user_id);
		 
		echo json_encode(array('new_count_tasks_to_act' => $new_count_tasks_to_act));
		
	break;
	
	case 'recount_new_notice_active_tasks':
		
		// Проверяем, есть ли новые задачи
		$new_count = get_new_tasks_count($current_user_id);
		// Есть ли новые комментарии начальников
		$new_count += get_count_notice_new_reports_for_task_for_user($current_user_id);
	
		echo json_encode(array('new_count' => $new_count));
	break;
	
	case 'change_task_sms_notice':
		
		$task_id = $_POST['task_id'];
		
		$sms_notice = $_POST['sms_notice'];
		
		$sql = "UPDATE ".TASKS_TB." SET  task_boss_sms_notice='$sms_notice' WHERE task_id='$task_id' AND task_from_user='$current_user_id' ";
		
		$site_db->query($sql);
	 
		if(!mysql_error())
		{
			echo 1;
		}
		
	break;
	
	case 'get_task_json_data':
		
		$task_id = $_POST['task_id'];
		
		// Данные задания
		$sql = "SELECT * FROM ".TASKS_TB." WHERE task_id='$task_id'";
		
		$task_data = $site_db->query_firstrow($sql, 1);
		
		if($task_data['task_to_user']!=$current_user_id || !$task_data['task_id'])
		{
			exit();
		}
		
		$task_data_iconv = to_iconv_array($task_data);
		
		echo json_encode($task_data_iconv);
		
	break;
}

?>