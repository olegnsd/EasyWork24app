<?php
// Страница отзывов о сотруднике
function fill_auto($user_id)
{
	global $site_db, $current_user_id;
	
	if($user_id!=$current_user_id)
	{
		header('Location: /auto/'.$current_user_id);
	}
	$main_tpl = file_get_contents('templates/auto/auto.tpl');
	
	// Строка навигации
	$nav = fill_nav('auto');
	
	$auto_list = fill_auto_list($user_id);
	
	$PARS['{NAV}'] = $nav;
	
	$PARS['{AUTO_LIST}'] = $auto_list;
	
	return fetch_tpl($PARS, $main_tpl);
}

// Список айфреймов автомобилей
function fill_auto_list($user_id)
{
	global $site_db, $current_user_id;
	
	$auto_item_tpl = file_get_contents('templates/auto/auto_item.tpl');
	
	$auto_iframe_tpl = file_get_contents('templates/auto/auto_iframe.tpl');
	
	// Выбор камеры пользоваетля
	$sql = "SELECT * FROM ".AUTO_TB." WHERE user_id='$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	 
	$iframe_display = $row['auto_src'] ? '' : 'none';
	
	if($row['auto_src'])
	{
		$iframe_src = preg_match('/http/i', $row['auto_src']) ? $row['auto_src'] : '';
		
		$PARS_1['{AUTO_ID}'] = $row['auto_id'];
		
		$PARS_1['{IFRAME_SRC}'] = $iframe_src.'?s='.rand(1,100000);
		
		$iframe_block = fetch_tpl($PARS_1, $auto_iframe_tpl);
	}
	 
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{AUTO_ID}'] = $row['auto_id'];
	
	$PARS['{VALUE_SRC}'] = $row['auto_src'];
	
	$PARS['{AUTO_TEXT}'] = $row['auto_text'];
	
	$PARS['{IFRAME_BLOCK}'] = $iframe_block;
	
	return fetch_tpl($PARS, $auto_item_tpl);
}

// Добавить в базу значение по умолчанию для пользователя
function set_default_auto_for_user($user_id)
{
	global $site_db, $current_user_id;
	
	// Добавляем запись по умолчанию
	$sql = "INSERT INTO ".AUTO_TB." (auto_src, user_id) VALUES ('".AUTO_IFRAME_SRC_DEFAULT."', '$user_id') ";
	
	$site_db->query($sql);
	
	if(mysql_error())
	{
		set_default_auto_for_user($user_id);
	}
}
?>