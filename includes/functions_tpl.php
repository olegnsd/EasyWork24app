<?php
#### Сотрудники


// Список Моих сотрудников
function fill_workers($user_id)
{
	global $site_db, $current_user_id;
	
	$workers_tpl = file_get_contents('templates/workers.tpl');
	
	// Список сотрудников
	$user_list = fill_workers_list($user_id);
	
	if(!$user_list)
	{
		$user_list = 'У вас нет сотрудников.';
	}
	
	$PARS['{USERS_LIST}'] = $user_list;
	
	$PARS['{CURRENT_USER_ID}'] = $current_user_id;
	
	return fetch_tpl($PARS, $workers_tpl);
}

// Формирование списка моих сотрудников
function fill_workers_list($user_id)
{
	global $site_db, $current_user_id;
	
	$worker_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers_item.tpl');
	
	$workers_not_confirm_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers_not_confirm_item.tpl');
	
	$workers_reject_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers_reject_item.tpl');
	
	$worker_item_sep_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/workers_item_sep.tpl');
	
	// Выбор сотрудников
	$sql = "SELECT * FROM tasks_user_in_users i
			LEFT JOIN tasks_users j ON i.invited_user = j.user_id
			WHERE i.invite_user='$user_id' AND i.invited_user_status IN (0,1, 2) ORDER by j.user_surname";
	
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		$PARS = array();
		
		$PARS['{JOB_ID}'] = $row['user_job_id'];
		
		$PARS['{SURNAME}'] = $row['user_surname'];
		
		$PARS['{NAME}'] = $row['user_name'];
		
		$PARS['{MIDDLENAME}'] = $row['user_middlename'];
		
		$PARS['{USER_POSITION}'] = $row['user_position'];
		
		$PARS['{TASKS_COUNT}'] = get_count_tasks_for_user($current_user_id, $row['invited_user']);
		
		$PARS['{USER_ID}'] = $row['invited_user'];
		
		$PARS['{CURRENT_USER_ID}'] = $current_user_id;
		
		// Неподтвержденный шаблон
		if($row['invited_user_status']==0)
		{
			$item_tpl = $workers_not_confirm_item_tpl;
		}
		// Отклоненная заявка на добавление, шаблон
		else if($row['invited_user_status']==2)
		{
			$item_tpl = $workers_reject_item_tpl;
		}
		else
		{
			$item_tpl = $worker_item_tpl;
		}
		$user_list .= fetch_tpl($PARS, $item_tpl).$worker_item_sep_tpl;
	}
	
	return $user_list;
}
?>