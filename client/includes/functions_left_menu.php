<?php
// Левое меню
function fill_left_menu($o)
{
	global $site_db, $current_user_id, $current_client_id;
	
	$left_menu_tpl = file_get_contents('templates/left_menu/left_menu.tpl');	
	
	$active_array = array('msgs'=>'', 'files'=>'');
	
	$active_array[$o] = 'left_menu_active';
	
	 
	if($o=='personal' && $_GET['user_id']!=$current_user_id)
	{
		$active_array['personal'] = '';
	}
	 
	// Кол-во новых отчетов для задач
	$new_files_count = get_new_users_files_for_client_count($current_client_id);
	$new_files_count = left_menu_new_count_proc($new_files_count);
	 
	$PARS['{ACTIVE_1}'] = $active_array['msgs'];
	$PARS['{ACTIVE_2}'] = $active_array['files'];
	
	$PARS['{NEW_FILES_COUNT}'] = $new_files_count;

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