<?php
#### Дерево приглашений пользователя
function fill_user_tree($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$user_tree_tpl = file_get_contents('templates/tree/user_tree.tpl');
	
	$user_tree_item_tpl = file_get_contents('templates/tree/user_tree_item.tpl');
	
	$tree_item_sep_tpl = file_get_contents('templates/tree/tree_item_sep.tpl');
	
	$user_position_item_tpl = file_get_contents('templates/tree/user_position_item.tpl');
	
	// Строка навигации
	$nav = fill_nav('user_tree');
	
	$registered_user_id = $user_id;
	
	// Проходим по циклу и собираем в массив пользователей 
	while(!$stop)
	{
		// Выбор пользователя
		$sql = "SELECT * FROM ".USERS_TB." WHERE user_id='$registered_user_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		// Зарегистрированный пользователем
		$registered_user_id = $row['registrated_by_user_id'];
		
		if(!$registered_user_id)
		{
			$stop=1;
		}
		if($row['user_id'])
		{
			$users[] = $row['user_id'];
		}
	}
	
	if(!$users)
	{ 
		header('Location: /');
	}
	$users = array_reverse($users);
	
	foreach($users as $user)
	{
		$user_obj->fill_user_data($user);
		
		$user_positions_history_arr = $user_obj->get_user_positions_arr();
		
		$user_positions_history_list = '';
		
		// Количество должностей
		$positions_count = count($user_positions_history_arr);
		
		$i=1;
		// Делаем список истории должностей
		foreach($user_positions_history_arr as $user_position)
		{
			$position_actual_class = '';
			
			$position_actual_class = $positions_count == $i ? 'tree_position_actual' : '';
			
			$PARS_2['{POSITION_ACTUAL_CLASS}'] = $position_actual_class;
			
			$PARS_2['{POSITION_NAME}'] = $user_position['name'];
			
			$PARS_2['{POSITION_DATE}'] = datetime($user_position['date'], '%d.%m.%Y');
			
			$user_positions_history_list .= fetch_tpl($PARS_2, $user_position_item_tpl);
			
			$i++;
		}
		
		$PARS_1['{USER_ID}'] = $user;
		
		$PARS_1['{NAME}'] = $user_obj->get_user_name();
		
		$PARS_1['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS_1['{SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS_1['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS_1['{USER_AVATAR_SRC}'] = get_user_preview_avatar_src($user, $user_obj->get_user_image());
		
		$PARS_1['{REG_DATE}'] = datetime($user_obj->get_user_registration_date(), '%d.%m.%Y'); ;
		
		$PARS_1['{USER_TARGET}'] = $user == $user_id ? 'tree_target_user' : '';
		
		$PARS_1['{USER_POSITIONS_HISTORY}'] = $user_positions_history_list;
		
		$tree[] = fetch_tpl($PARS_1, $user_tree_item_tpl);
	}
	$tree = implode($tree_item_sep_tpl, $tree);
	 
	$PARS['{NAV}'] = $nav;
	
	$PARS['{TREE}'] = $tree;
	

	
	return fetch_tpl($PARS, $user_tree_tpl);
}

?>