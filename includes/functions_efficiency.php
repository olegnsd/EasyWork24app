<?php
//// Возвращщает обработанный массив задач пользователя для оценки эффективности
//function user_efficiency_tasks_array($user_id, $last_days_count)
//{
//	global $site_db, $current_user_id, $user_obj;
//	
//	$date_from = date('Y-m-d', time() - 3600 * 24 * $last_days_count);
//	
//	$date_to = date('Y-m-d H:i:s');
//	
//	$date_to_without_time = date('Y-m-d');
//	
//	/*$sql = "SELECT * FROM ".TASKS_TB." 
//			WHERE task_to_user='$user_id' AND task_to_user<>task_from_user AND task_deleted<>1 
//			AND ((task_finished_date>='$date_from' AND  task_finished_date <= '$date_to' AND task_finished_confirm = 1) OR
//			( task_status=5 AND task_finished_fail_date >='$date_from' AND task_finished_fail_date <= '$date_to')  OR
//			(task_finished_confirm<>1 AND task_status<>5 AND task_date='$date_to_without_time')
//			)";*/
//	
//	$sql = "SELECT * FROM ".TASKS_TB." 
//			WHERE task_to_user='$user_id' AND task_to_user<>task_from_user AND task_deleted<>1 
//			AND ((task_finished_date>='$date_from' AND  task_finished_date <= '$date_to' AND task_finished_confirm = 1) OR
//			( task_status=5 AND task_finished_fail_date >='$date_from' AND task_finished_fail_date <= '$date_to'))";
//		 
//	$res = $site_db->query($sql);
//		 
//	while($row=$site_db->fetch_array($res,1))
//	{
//		// Выполнение задание было подтверждено и 
//		if(!$row['task_finished_confirm'] && $row['task_status']!=5)
//		{
//			$dates_tasks[$row['task_date']][] = $row;
//		}
//		else if($row['task_status']==5)
//		{
//			if(!preg_match('/0000/', $row['task_finished_fail_date']))
//			{
//				$dates_tasks[substr($row['task_finished_fail_date'],0,10)][] = $row;
//			}
//		}
//		else
//		{
//			if(!preg_match('/0000/', $row['task_finished_date']))
//			{
//				$dates_tasks[substr($row['task_finished_date'],0,10)][] = $row;
//			}
//		}
//	}
//	
//	 
//	return $dates_tasks;
//}
//
//function fill_user_efficiency_block($user_id)
//{
//	global $site_db, $current_user_id, $user_obj;
//	
//	$user_efficiency_tpl = file_get_contents('templates/efficiency/user_efficiency.tpl');
//
//	// Строка навигации
//	$nav = fill_nav('efficiency');
//	
//	$user_obj->fill_user_data($boss_user_id);
//	
//	// За какой период находим задачи, кол-во дней
//	$last_days_count = 30;
//	
//	$date_from = date('Y-m-d', time() - 3600 * 24 * $last_days_count);
//	
//	$date_to = date('Y-m-d H:i:s');
//	
//	$date_to_without_time = date('Y-m-d');
//	
//	// Начальная точка в графике
//	$SERIES_DATA_ARR[$date_from] = series_data($date_from, 'null');
//	 
//	// Конечная точка в графике
//	$SERIES_DATA_ARR[$date_to_without_time] = series_data($date_to_without_time, 'null');
//	
//	// Ось х - даты
//	for($i=31; $i>=0; $i--)
//	{ 
//		$date = time() - 3600 * 24 * $i;
//		
//		$date_array[] = iconv('cp1251', 'utf-8', seconds_to_date_min_rus($date, 1));
//		
//		$dates_points_arr[] = date('Y-m-d', $date);
//	}
//	if($date_array)
//	{
//		$categories = json_encode(($date_array));
//	}
//	else
//	{
//		$categories = "''";
//	}
//	
//	// Получаем обработанный массив задач пользователя за прошедние дни
//	$dates_tasks = user_efficiency_tasks_array($user_id, $last_days_count);
//	
//	$tusersarr = array(12,16,5,8,10,9,7);
//	if(preg_match('/m-corp/', $_SERVER['HTTP_HOST']) && in_array($user_id, $tusersarr))
//	{
//		$now_dat = time() - 3600 * 24 * 30;
//		
//		if($user_id==12)
//	 		$ef_1 = array(45,45,56,60,55,65,40,55,55,45,15,45,56,70,75,65,25,25,65,45,15,45,66,80,75,75,55,5,65,45);
//		
//		if($user_id==16)
//	 		$ef_1 = array(95,95,100,100,100,98,95,95,90,90,100,100,97,95,100,90,90,98,95,100,100,100,100,98,95,100,100,98,95,100);	
//		
//		if($user_id==5)
//	 		$ef_1 = array(95,95,100,100,100,98,98,95,95,95,100,100,97,95,100,95,95,98,95,100,100,100,100,98,95,100,100,98,95,100);
//		
//		if($user_id==8)
//	 		$ef_1 = array(85,85,100,100,100,88,85,85,85,85,100,100,97,95,100,85,85,88,85,100,100,100,100,88,85,100,100,98,95,100);
//			
//		if($user_id==10)
//	 		$ef_1 = array(30,70,95,75,95,88,70,30,40,70,95,95,97,95,95,60,40,88,70,95,95,95,95,58,70,95,95,88,95,90);
//		
//		if($user_id==9)
//	 		$ef_1 = array(85,85,95,95,95,88,85,85,70,70,95,95,97,85,95,90,90,88,85,95,95,95,95,98,85,95,95,88,85,85);
//		
//		if($user_id==7)
//	 		$ef_1 = array(95,85,95,95,95,88,85,85,80,80,95,95,97,85,95,90,90,88,85,95,95,95,95,98,85,95,95,88,85,85);				
//		
//		$dates_tasks_new = array();
//		for($i=0; $i<30; $i++)
//		{
//			$nd = date('Y-m-d', $now_dat);
//			
//			$dates_tasks_new[$nd] = 40;
//			
//			$now_dat += 3600 * 24;
//		}
//		// echo "<pre>",print_r($dates_tasks_new),"</pre>";
//		
//		$num = 0;
//		// Проходим по датам и отмечаем точки эффективности
//		foreach($dates_tasks_new as $date => $ef)
//		{ 
//			$num++	; 
//			if($num%2==0) continue;
//			if(in_array(date('w', to_mktime($date)), array(0,6)))
//			continue;
//			
//			$SERIES_DATA_ARR[$date] = series_data($date, $ef_1[$num]);
//			 
//		}
//	}
//	else
//	{
//		// Проходим по датам и отмечаем точки эффективности
//		foreach($dates_tasks as $date => $task_data)
//		{ 
//			$SERIES_DATA_ARR[$date] = series_data($date, get_user_efficiency($task_data, $date));	 
//		}
//	}
//	ksort($SERIES_DATA_ARR); 
// 	
//	if($date_array && $SERIES_DATA_ARR)
//	{
//		$series = '['.implode(',', $SERIES_DATA_ARR).']';
//	}
//	else
//	{
//		$series = "''";
//	}
//
//	// Заполянем объект пользователя
//	$user_obj->fill_user_data($user_id);
//	
//	$PARS['{USER_ID}'] = $boss_user_id;
//		
//	$PARS['{NAME}'] = $user_obj->get_user_name();
//		
//	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
//		
//	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
//		
//	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
//	
//	$PARS['{NAV}'] = $nav;
//	
//	$PARS['{CATEGORIES}'] = $categories;
//	
//	$PARS['{SERIES}'] = $series;
//	
//	return fetch_tpl($PARS, $user_efficiency_tpl);
//}

//// Возвращщает обработанный массив задач пользователя для оценки эффективности
//function user_efficiency_tasks_array($user_id, $last_days_count)
//{
//	global $site_db, $current_user_id, $user_obj;
//	
//	$date_from = date('Y-m-d', time() - 3600 * 24 * $last_days_count);
//	
//	$date_to = date('Y-m-d H:i:s');
//	
//	$date_to_without_time = date('Y-m-d');
//	
//	/*$sql = "SELECT * FROM ".TASKS_TB." 
//			WHERE task_to_user='$user_id' AND task_to_user<>task_from_user AND task_deleted<>1 
//			AND ((task_finished_date>='$date_from' AND  task_finished_date <= '$date_to' AND task_finished_confirm = 1) OR
//			( task_status=5 AND task_finished_fail_date >='$date_from' AND task_finished_fail_date <= '$date_to')  OR
//			(task_finished_confirm<>1 AND task_status<>5 AND task_date='$date_to_without_time')
//			)";*/
//	
//	$sql = "SELECT * FROM ".TASKS_TB." 
//			WHERE task_to_user='$user_id' AND task_to_user<>task_from_user AND task_deleted<>1 
//			AND ((task_finished_date>='$date_from' AND  task_finished_date <= '$date_to' AND task_finished_confirm = 1) OR
//			( task_status=5 AND task_finished_fail_date >='$date_from' AND task_finished_fail_date <= '$date_to'))";
//		 
//	$res = $site_db->query($sql);
//		 
//	while($row=$site_db->fetch_array($res,1))
//	{
//		// Выполнение задание было подтверждено и 
//		if(!$row['task_finished_confirm'] && $row['task_status']!=5)
//		{
//			$dates_tasks[$row['task_date']][] = $row;
//		}
//		else if($row['task_status']==5)
//		{
//			if(!preg_match('/0000/', $row['task_finished_fail_date']))
//			{
//				$dates_tasks[substr($row['task_finished_fail_date'],0,10)][] = $row;
//			}
//		}
//		else
//		{
//			if(!preg_match('/0000/', $row['task_finished_date']))
//			{
//				$dates_tasks[substr($row['task_finished_date'],0,10)][] = $row;
//			}
//		}
//	}
//	
//	 
//	return $dates_tasks;
//}

// Возвращщает обработанный массив задач пользователя для оценки эффективности
function user_efficiency_tasks_array($user_id, $last_days_count)
{
	global $site_db, $current_user_id, $user_obj;
	
	$date_from = date('Y-m-d', time() - 3600 * 24 * $last_days_count);
	
	$date_to = date('Y-m-d H:i:s');
	
	$date_to_without_time = date('Y-m-d');
	
	$sql = "SELECT i.* FROM tasks_tasks i
			LEFT JOIN tasks_tasks_users j ON i.task_id=j.task_id
			WHERE i.deleted=0 AND j.role=2 AND j.user_id='$user_id' AND i.work_status=2 AND i.date_status_3>='$date_from' AND  i.date_status_3 <= '$date_to' AND i.is_own=0 ";
		 
	$res = $site_db->query($sql);
		  
	while($row=$site_db->fetch_array($res,1))
	{
		if(is_date_exists($row['date_status_3']))
		{
			$dates_tasks[substr($row['date_status_3'],0,10)][] = $row;
		}
		
	}
	  
	 
	return $dates_tasks;
}

// График выполненных собственных задач
function fill_user_efficiency_tasks_block($user_id, $is_completed)
{
	global $site_db, $current_user_id, $user_obj;
	
	if($is_completed)
	{
		$user_efficiency_tasks_tpl = file_get_contents('templates/efficiency/user_efficiency_tasks_completed.tpl');
	}
	else
	{
		$user_efficiency_tasks_tpl = file_get_contents('templates/efficiency/user_efficiency_tasks.tpl');
	}
	 
	
	$tmp_date = time() - 3600 * 24 * 30;
	
	$date_from = date('Y-m-d 00:00:00', $tmp_date);
	
	$date_to = date('Y-m-d 23:59:59');
	
	if($is_completed)
	{
		// выбор задач, которые пользователь выполнил
		$sql = "SELECT i.* FROM tasks_tasks i
				LEFT JOIN tasks_tasks_users j ON i.task_id=j.task_id
				WHERE j.role=2 AND j.user_id='$user_id'  AND i.work_status=2 AND i.date_status_3>='$date_from' AND  i.date_status_3 <= '$date_to' AND i.deleted=0 ";
	}
	else
	{
		// выбор задач, которые пользователь выполнил
		$sql = "SELECT DISTINCT(i.task_id), i.* FROM tasks_tasks i
				LEFT JOIN tasks_tasks_users j ON i.task_id=j.task_id
				WHERE j.role=1 AND j.user_id='$user_id'  AND i.date_add>='$date_from' AND  i.date_add <= '$date_to' AND i.deleted=0 ";
	}
	 
				 			
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res, 1))
	{
		if($is_completed)
		{  
			$tasks_arr[substr($row['date_status_3'],0,10)][$row['is_own']] += 1;
		}
		else
		{
			$tasks_arr[substr($row['date_add'],0,10)][$row['is_own']] += 1;
		}
		 
	}
	//echo "<pre>", print_r($tasks_arr);
	// Не выводим пустой график
	if(!$tasks_arr)
	{
		return '';
	}
	
	// Первый день на графике 
	$start_day = to_mktime($date_from); 
	
	// Формируем ось х
	for($i=0; $i<31; $i++)
	{
		$date_s = date('Y-m-d', $start_day);
		 
		// Если за текущий час есть обновленные статусы, то суммируем их, иначе ставим 0
		if($tasks_arr[$date_s])
		{
			if($tasks_arr[$date_s][1])
			{
				$SERIES_DATA_TASKS_OWN_ARR[] = series_data($date_s, $tasks_arr[$date_s][1]);
			}
			else
			{
				$SERIES_DATA_TASKS_OWN_ARR[] = series_data($date_s, 0);
			}
			
			if($tasks_arr[$date_s][0])
			{
				$SERIES_DATA_TASKS_ALL_ARR[] = series_data($date_s, $tasks_arr[$date_s][0]);
			}
			else
			{
				$SERIES_DATA_TASKS_ALL_ARR[] = series_data($date_s, 0);
			}
		}
		else
		{
			$SERIES_DATA_TASKS_OWN_ARR[] = series_data($date_s, 0);
			$SERIES_DATA_TASKS_ALL_ARR[] = series_data($date_s, 0);
		}
		
		// Увеличиваем на 1 день
		$start_day += 24 * 3600;
	}
	 
	  
	if($SERIES_DATA_TASKS_OWN_ARR)
	{
		$series_tasks_own = '['.implode(',', $SERIES_DATA_TASKS_OWN_ARR).']';
	}
	
	if($SERIES_DATA_TASKS_ALL_ARR)
	{
		$series_tasks_all = '['.implode(',', $SERIES_DATA_TASKS_ALL_ARR).']';
	} 

	
 	// Отображать в графике время старта 
	$date_start = get_date_utc_for_js_object($date_from);
	 
	$PARS['{SERIES_TASKS_OWN}'] = $series_tasks_own;
	
	$PARS['{SERIES_TASKS_ALL}'] = $series_tasks_all;
	 
	$PARS['{SERIES_DATE_START}'] = $date_start;
	
	return fetch_tpl($PARS, $user_efficiency_tasks_tpl);
	
}

function fill_user_efficiency_block($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$user_efficiency_tpl = file_get_contents('templates/efficiency/user_efficiency.tpl');

	 
	
	$user_obj->fill_user_data($boss_user_id);
	
	// За какой период находим задачи, кол-во дней
	$last_days_count = 30;
	
	$date_from = date('Y-m-d', time() - 3600 * 24 * $last_days_count);
	
	$date_to = date('Y-m-d H:i:s');
	
	$date_to_without_time = date('Y-m-d');
	
	// Начальная точка в графике
	$SERIES_DATA_ARR[$date_from] = series_data($date_from, 'null');
	 
	// Конечная точка в графике
	$SERIES_DATA_ARR[$date_to_without_time] = series_data($date_to_without_time, 'null');
	
	// Ось х - даты
	for($i=31; $i>=0; $i--)
	{ 
		$date = time() - 3600 * 24 * $i;
		
		$date_array[] = iconv('cp1251', 'utf-8', seconds_to_date_min_rus($date, 1));
		
		$dates_points_arr[] = date('Y-m-d', $date);
	}
	if($date_array)
	{
		$categories = json_encode(($date_array));
	}
	else
	{
		$categories = "''";
	}
	
	// Получаем обработанный массив задач пользователя за прошедние дни
	$dates_tasks = user_efficiency_tasks_array($user_id, $last_days_count);
	
	
	// Проходим по датам и отмечаем точки эффективности
	foreach($dates_tasks as $date => $task_data)
	{ 
		$SERIES_DATA_ARR[$date] = series_data($date, get_user_efficiency($task_data, $date));	 
	}
	
	
	ksort($SERIES_DATA_ARR); 
 	
	if($date_array && $SERIES_DATA_ARR)
	{
		$series = '['.implode(',', $SERIES_DATA_ARR).']';
	}
	else
	{
		$series = "''";
	}

	// Заполянем объект пользователя
	$user_obj->fill_user_data($user_id);
	
	$PARS['{USER_ID}'] = $boss_user_id;
		
	$PARS['{NAME}'] = $user_obj->get_user_name();
		
	$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
	$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{NAV}'] = $nav;
	
	$PARS['{CATEGORIES}'] = $categories;
	
	$PARS['{SERIES}'] = $series;
	
	return fetch_tpl($PARS, $user_efficiency_tpl);
}

function get_user_efficiency($task_data_arr, $date)
{
	/*
Задение принято не в срок -минус 10% (более 24 часов нетсчитая выходных)
Просрочен желаемый срок - минус 15%
Просрочен крайний срок - минус 25%
Оценка 5 - плюс 5%
Оценка 4 - минус 10%
Оценка 3 минус 25%
Оценка 2 и 1 - минус 50%
Задание не выполнено - минус 80%
Сложное задание - плюс 20%
Легкое задание - минус 10%

Толстых Олег Всеволодович
24 февраля 2013 в 22:30:52
Максимальная эффективность - 100%
Мимальная эффективность думаю стоит делать не меньше 5% чтобы график был виден 
*/
 
	#######
	### Бонусы\штрафы для оценки эффектинвости
	#######
	// Задение принято не в срок -минус (более 24 часов не считая выходных)
	$mark_efficiency_task_date_confirm = -10;
	 
	// Просрочен желаемый срок - минус
	$mark_efficiency_task_desired_date = -15;
	
	// Просрочен крайний срок - минус
	$mark_efficiency_task_desired_date = -25;
	
	//Задание не выполнено - минус
	$mark_efficiency_task_fail = -80;
	
	// Сложность задания
	$mark_efficiency_task_difficulty_arr = array('1' => -10, '2' => 0, '3' => 20);
	
	// Оценки проделанной работы
	$mark_efficiency_task_quality_arr = array('5' => 5, '4' => 0, '3' => -25, '2' => -50, '1' => -50);
	
	
	foreach($task_data_arr as $task_data)
	{
		$k = 100;
		 
		$penalty = 0;
		
		$current_mktime = time();
		
		// дата СОЗДАНИЯ ЗАДАЧИ
		$task_date_add_mktime = to_mktime($task_data['date_add']);
		
		// дата ЗАВЕРШЕНИЯ ЗАДАЧИ
		$task_finished_date_mktime = to_mktime($task_data['date_status_3']);
	
		// Время ПРИНЯТИЕ ЗАДАНИЯ
		$task_confirm_date_mktime = to_mktime($task_data['date_status_1']);
		
	 	// МАКСИМАЛЬНОЕ ВРЕМЯ ВЫПОЛНЕНИЯ
		$task_max_date_mktime = to_mktime($task_data['task_max_date']); 
		
		// Просрочено ПРИНЯТИЕ ЗАДАНИЯ больше суток
		if(is_date_exists($task_data['date_status_1']) && $task_confirm_date_mktime - $task_date_add_mktime > 3600 * 24)
		{
			 $penalty += $mark_efficiency_task_date_confirm;
		}
		
		// Просрочено ВРЕМЯ ВЫПОЛНЕНИЯ
		if(is_date_exists($task_data['task_max_date']) && $task_finished_date_mktime - $task_max_date_mktime > 0)
		{
			$penalty += $mark_efficiency_task_desired_date;
		}
		 
		// С ЗАДАНИЕМ НЕ СПРАВИЛСЯ
		if($task_data['step_status']==5)
		{
			$penalty += $mark_efficiency_task_fail;
		}
		 
		// Оценки качества проделанной работы
		if($task_data['task_rating'])
		{
			$penalty += $mark_efficiency_task_quality_arr[$task_data['task_rating']];
			
			// СЛОЖНОСТЬ ЗАДАНИЯ
			if($task_data['task_difficulty'])
			{ 
				$penalty += $mark_efficiency_task_difficulty_arr[$task_data['task_difficulty']];
			}
		}
		
		 
		// Вычитываем из полного коэффициента штрафы
		$k_result = $k+$penalty > 0 ? $k+$penalty : 0;
		
		 
		// Границы макс и мин значений
		$k_result = $k_result > 100 ? 100 : $k_result;
		$k_result = $k_result < 5 ? 5 : $k_result;
		
		$result_k_arr[] = $k_result;
	 
	}

	// Среднее значение (в день бывает по несколько выполненных заданий)
	$efficiency = round(array_sum($result_k_arr) / count($result_k_arr));
	
	  
	return $efficiency;
}

//function get_user_efficiency($task_data_arr, $date)
//{
//	/*
//Задение принято не в срок -минус 10% (более 24 часов нетсчитая выходных)
//Просрочен желаемый срок - минус 15%
//Просрочен крайний срок - минус 25%
//Оценка 5 - плюс 5%
//Оценка 4 - минус 10%
//Оценка 3 минус 25%
//Оценка 2 и 1 - минус 50%
//Задание не выполнено - минус 80%
//Сложное задание - плюс 20%
//Легкое задание - минус 10%
//
//Толстых Олег Всеволодович
//24 февраля 2013 в 22:30:52
//Максимальная эффективность - 100%
//Мимальная эффективность думаю стоит делать не меньше 5% чтобы график был виден 
//*/
// 
//	#######
//	### Бонусы\штрафы для оценки эффектинвости
//	#######
//	// Задение принято не в срок -минус (более 24 часов не считая выходных)
//	$mark_efficiency_task_date_confirm = -10;
//	 
//	// Просрочен желаемый срок - минус
//	$mark_efficiency_task_desired_date = -15;
//	
//	// Просрочен крайний срок - минус
//	$mark_efficiency_task_desired_date = -25;
//	
//	//Задание не выполнено - минус
//	$mark_efficiency_task_fail = -80;
//	
//	// Сложность задания
//	$mark_efficiency_task_difficulty_arr = array('1' => -10, '2' => 0, '3' => 20);
//	
//	// Оценки проделанной работы
//	$mark_efficiency_task_quality_arr = array('5' => 5, '4' => -5, '3' => -25, '2' => -50, '1' => -50);
//	
//	
//	foreach($task_data_arr as $task_data)
//	{
//		$k = 100;
//		 
//		$penalty = 0;
//		
//		$current_mktime = time();
//		
//		// дата СОЗДАНИЯ ЗАДАЧИ
//		$task_date_add_mktime = to_mktime($task_data['task_date_add']);
//		
//		// дата ЗАВЕРШЕНИЯ ЗАДАЧИ
//		$task_finished_date_mktime = to_mktime($task_data['task_finished_date']);
//	
//		// Время ПРИНЯТИЕ ЗАДАНИЯ
//		$task_confirm_date_mktime = to_mktime($task_data['task_confirm_date']);
//		
//		// ЖЕЛАЕМОЕ ВРЕМЯ ВЫПОЛНЕНИЯ
//		$task_desired_date_mktime = to_mktime($task_data['task_desired_date']);
//		
//		// МАКСИМАЛЬНОЕ ВРЕМЯ ВЫПОЛНЕНИЯ
//		$task_max_date_mktime = to_mktime($task_data['task_max_date']); 
//		
//		//echo $task_data['task_id'],' task_date_add-',$task_data['task_date_add'],' ',$task_confirm_date_mktime - $task_date_add_mktime,' <br>';
//		
//		// Просрочено ПРИНЯТИЕ ЗАДАНИЯ
//		if($task_confirm_date_mktime - $task_date_add_mktime > 3600 * 24 || (preg_match('/0000/', $task_data['task_confirm_date']) && $current_mktime-$task_date_add_mktime > 3600 * 24))
//		{
//			 $penalty += $mark_efficiency_task_date_confirm;
//		}
//		// ЖЕЛАЕМОЕ ВРЕМЯ ВЫПОЛНЕНИЯ просрочено
//		if((($task_desired_date_mktime-$task_finished_date_mktime<0 && $task_finished_date_mktime) || ($task_desired_date_mktime-$current_mktime<0 && !$task_finished_date_mktime)) && !preg_match('/0000/', $task_data['task_desired_date']))
//		{
//			 $penalty += $mark_efficiency_task_desired_date;
//		}
//		
//		// МАКСИМАЛЬНОЕ ВРЕМЯ ВЫПОЛНЕНИЯ просрочено
//		if((($task_max_date_mktime-$task_finished_date_mktime<0 && $task_finished_date_mktime) || ($task_max_date_mktime-$current_mktime<0 && !$task_finished_date_mktime)) && !preg_match('/0000/', $task_data['task_max_date']))
//		{ 
//			$penalty += $mark_efficiency_task_desired_date;
//		}
//		// С ЗАДАНИЕМ НЕ СПРАВИЛСЯ
//		if($task_data['task_status']==5)
//		{ 
//			 $penalty += $mark_efficiency_task_fail;
//		}
//		
//		// Оценки качества проделанной работы
//		if($task_data['task_status']==3 && $task_data['task_finished_confirm'] && $task_data['task_quality'])
//		{
//			$penalty += $mark_efficiency_task_quality_arr[$task_data['task_quality']];
//			
//			// СЛОЖНОСТЬ ЗАДАНИЯ
//			if($task_data['task_difficulty'] && $task_data['task_quality'] <= 4)
//			{ 
//				$penalty += $mark_efficiency_task_difficulty_arr[$task_data['task_difficulty']];
//			}
//		}
//		
//		 
//		
//		 
//		// Вычитываем из полного коэффициента штрафы
//		$k_result = $k+$penalty > 0 ? $k+$penalty : 0;
//		
//		 
//		// Границы макс и мин значений
//		$k_result = $k_result > 100 ? 100 : $k_result;
//		$k_result = $k_result < 5 ? 5 : $k_result;
//		
//		$result_k_arr[] = $k_result;
//	 
//	}
//
//	// Среднее значение (в день бывает по несколько выполненных заданий)
//	$efficiency = round(array_sum($result_k_arr) / count($result_k_arr));
//	
//	 // echo "<pre>",print_r($efficiency),"</pre>";
//	  
//	return $efficiency;
//}

?>