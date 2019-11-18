<?php

// Установка глобалных массивов для пользователя
function set_current_user_global_array_data()
{
	global $current_user_id;
	global $_CURRENT_USER_BOSS_ARR; // начальники
	global $_CURRENT_USER_WORKERS_ARR; // подчиненные
	global $_CURRENT_USER_COLLEAGUES_ARR; // коллеги
	global $_CURRENT_USER_DEPUTY_BOSS_ARR; // временные начальники
	global $_CURRENT_USER_DEPUTY_WORKERS_ARR; // временные подчиненные
	global $_CURRENT_USER_ALL_CONTACT_USERS_ARR; // слитие всех массивов - то есть с кем контактирует пользователь
	global $_CURRENT_USER_ALL_BOSS_ARR; // массив временных и постоянных начальников с флагами
	global $_CURRENT_USER_ALL_WORKERS_ARR; // массив всех подчиненных, включая подчиненных подчиненных
	 
	// Начальники
	$_CURRENT_USER_BOSS_ARR = get_user_boss_arr($current_user_id);
	
	// Подчиненные
	$_CURRENT_USER_WORKERS_ARR = get_user_workers_arr($current_user_id);
	//print_r($_CURRENT_USER_BOSS_ARR);
	// Коллеги
	$_CURRENT_USER_COLLEAGUES_ARR = get_user_colleagues_arr($current_user_id);
	//$_CURRENT_USER_COLLEAGUES_ARR = array();
	
	// Массив временных подчиненных
	$_CURRENT_USER_DEPUTY_WORKERS_ARR = $_CURRENT_USER_WORKERS_ARR; //get_user_workers_arr($current_user_id, 1);
	
	// Массив временных начальников
	$_CURRENT_USER_DEPUTY_BOSS_ARR = $_CURRENT_USER_BOSS_ARR; //get_user_boss_arr($current_user_id, 1);
	
	// Массив всех подчиненных подчиненных
	$_CURRENT_USER_ALL_WORKERS_ARR = get_all_workers_arr_for_user($current_user_id);
	 
	// Слить все массивы
	$_CURRENT_USER_ALL_CONTACT_USERS_ARR = array_merge($_CURRENT_USER_BOSS_ARR, $_CURRENT_USER_WORKERS_ARR, $_CURRENT_USER_COLLEAGUES_ARR, $_CURRENT_USER_DEPUTY_WORKERS_ARR, $_CURRENT_USER_DEPUTY_BOSS_ARR);	
	
	// Формируем массив временных и постоянных начальников
	foreach($_CURRENT_USER_DEPUTY_BOSS_ARR as $boss_id)
	{
		$_CURRENT_USER_ALL_BOSS_ARR[$boss_id] = array('deputy_boss'=>1);
	}
	foreach($_CURRENT_USER_BOSS_ARR as $boss_id)
	{
		$_CURRENT_USER_ALL_BOSS_ARR[$boss_id] = array('deputy_boss'=>0);
	}
}

function user_is_boss($user_id)
{
	global $_CURRENT_USER_BOSS_ARR;
	
	if(in_array($user_id, $_CURRENT_USER_BOSS_ARR))
	{
		return true;
	}
	else return false;
}
function user_is_worker($user_id)
{
	global $_CURRENT_USER_WORKERS_ARR;
	
	if(in_array($user_id, $_CURRENT_USER_WORKERS_ARR))
	{
		return true;
	}
	else return false;
}
function user_is_colleague($user_id)
{
	global $_CURRENT_USER_COLLEAGUES_ARR;
	
	if(in_array($user_id, $_CURRENT_USER_COLLEAGUES_ARR))
	{
		return true;
	}
	else return false;
}
function user_is_deputy_worker($user_id)
{
	global $_CURRENT_USER_DEPUTY_WORKERS_ARR;
	
	if(in_array($user_id, $_CURRENT_USER_DEPUTY_WORKERS_ARR))
	{
		return true;
	}
	else return false;
}
function user_is_deputy_boss($user_id)
{
	global $_CURRENT_USER_DEPUTY_BOSS_ARR;
	
	if(in_array($user_id, $_CURRENT_USER_DEPUTY_BOSS_ARR))
	{
		return true;
	}
	else return false;
}
// Подчиненный подчиненного
function user_is_all_worker($user_id)
{
	global $_CURRENT_USER_ALL_WORKERS_ARR;
	
	if(in_array($user_id, $_CURRENT_USER_ALL_WORKERS_ARR))
	{
		return true;
	}
	else return false;
}

// Подчиненный временного подчиненного
function user_is_all_deputy_worker($user_id)
{
	global $_CURRENT_USER_ALL_DEPUTY_WORKERS_ARR;
	
	if(in_array($user_id, $_CURRENT_USER_ALL_DEPUTY_WORKERS_ARR))
	{
		return true;
	}
	else return false;
}

// Возвращает TRUE, если одн из проверок положительна
function check_user_access_to_user_content($user_id, $pars)
{
	global $site_db, $current_user_id;
	
	$boss = $pars[0];
	$worker = $pars[1];
	$colleague = $pars[2];
	$deputy_boss = $pars[3];
	$deputy_worker = $pars[4];
	$all_workers = $pars[5];
	$all_deputy_workers = $pars[6];
	
	if($boss && user_is_boss($user_id))
	{
		return true;
	}
	if($worker && user_is_worker($user_id))
	{
		return true;
	}
	if($colleague && user_is_colleague($user_id))
	{
		return true;
	}
	if($deputy_boss && user_is_deputy_boss($user_id))
	{
		return true;
	}
	if($deputy_worker && user_is_deputy_worker($user_id))
	{
		return true;
	}
	if($all_workers && user_is_all_worker($user_id))
	{
		return true;
	}
	if($all_deputy_workers && user_is_all_deputy_worker($user_id))
	{
		return true;
	}

	// Если совпадений нет, возвращаем 0
	return false;
}

// Формирует массив сложенный из необходимых
function get_current_user_users_arrs($pars, $with_user_data, $return_count)
{
	global $user_obj, $_CURRENT_USER_BOSS_ARR, $_CURRENT_USER_WORKERS_ARR, $_CURRENT_USER_COLLEAGUES_ARR, $_CURRENT_USER_DEPUTY_BOSS_ARR, $_CURRENT_USER_DEPUTY_WORKERS_ARR, $_CURRENT_USER_ALL_WORKERS_ARR, $_CURRENT_USER_ALL_DEPUTY_WORKERS_ARR; 
	
	$result_array = array();
	$result_data_array = array();
	
	$boss = $pars[0];
	$worker = $pars[1];
	$colleague = $pars[2];
	$deputy_boss = $pars[3];
	$deputy_worker = $pars[4];
	$all_workers = $pars[5];
	$all_deputy_workers = $pars[6];
	
	if($boss)
	{
		foreach($_CURRENT_USER_BOSS_ARR as $user_id)
		{
			$result_array[$user_id] = $user_id; 
		}
	}
	if($worker)
	{
		foreach($_CURRENT_USER_WORKERS_ARR as $user_id)
		{
			$result_array[$user_id] = $user_id; 
		}
	}
	if($colleague)
	{
		foreach($_CURRENT_USER_COLLEAGUES_ARR as $user_id)
		{
			$result_array[$user_id] = $user_id; 
		}
	}
	if($deputy_boss)
	{
		foreach($_CURRENT_USER_DEPUTY_BOSS_ARR as $user_id)
		{
			$result_array[$user_id] = $user_id; 
		}
	}
	if($deputy_worker)
	{
		foreach($_CURRENT_USER_DEPUTY_WORKERS_ARR as $user_id)
		{
			$result_array[$user_id] = $user_id; 
		}
	}
	if($all_workers)
	{
		foreach($_CURRENT_USER_ALL_WORKERS_ARR as $user_id)
		{
			$result_array[$user_id] = $user_id; 
		}
	}
	if($all_deputy_workers)
	{
		foreach($_CURRENT_USER_ALL_DEPUTY_WORKERS_ARR as $user_id)
		{
			$result_array[$user_id] = $user_id; 
		}
	}
	 
	// Заполнить массив данными пользователей
	if($with_user_data)
	{
		foreach($result_array as $user_id)
		{
			// Заполянем объект пользователя
			$user_obj->fill_user_data($user_id);
			
			$user_data['user_id'] = $user_id;
			
			$user_data['surname'] = $user_obj->get_user_surname();
				
			$user_data['name'] = $user_obj->get_user_name();
				
			$user_data['middlename'] = $user_obj->get_user_middlename();
				
			$user_data['user_position'] = $user_obj->get_user_position();
		
			$user_fio = $user_data['surname'].'_'.$user_id;
			
			$users_list_arr[$user_fio] = $user_data;
		}
		
		ksort($users_list_arr);
		
		// Формируем массив с пользователя, определяя ключи массива как user_id
		foreach($users_list_arr as $fio => $user_data)
		{  
			$result_data_array[$user_data['user_id']] = $user_data;
		}
		 
		
		return $result_data_array;
	}
	else if($return_count)
	{
		return count($result_array);
	}
	else
	{
		return $result_array;
	}
}


// Формирует массив сложенный из необходимых
function get_user_users_arrs($user_id, $pars, $return_count)
{	
	$result_array = array();
	
	$boss = $pars[0];
	$worker = $pars[1];
	$colleague = $pars[2];
	$deputy_boss = $pars[3];
	$deputy_worker = $pars[4];
	$all_workers = $pars[5];
	
	if($boss)
	{
		$user_arr = get_user_boss_arr($user_id);
		
		foreach($user_arr as $user)
		{
			$result_array[$user] = $user; 
		}
	}
	if($worker)
	{
		$user_arr = get_user_workers_arr($user_id);
		
		foreach($user_arr as $user)
		{
			$result_array[$user] = $user; 
		}
	}
	if($colleague)
	{
		$user_arr = get_user_colleagues_arr($user_id);
		
		foreach($user_arr as $user)
		{
			$result_array[$user] = $user; 
		}
	}
	if($deputy_boss)
	{
		$user_arr = get_user_boss_arr($user_id, 1);
		
		foreach($user_arr as $user)
		{
			$result_array[$user] = $user; 
		}
	}
	if($deputy_worker)
	{
		$user_arr = get_user_workers_arr($user_id, 1);
		
		foreach($user_arr as $user)
		{
			$result_array[$user] = $user; 
		}
	}
	if($all_workers)
	{
		$user_arr = get_all_workers_arr_for_user($user_id);
		
		foreach($user_arr as $user)
		{
			$result_array[$user] = $user; 
		}
	}

	
	if($return_count)
	{
		return count($result_array);
	}
	else
	{
		return $result_array;
	}
}
?>