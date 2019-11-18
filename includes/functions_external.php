<?php
// Список Моих коллег
function fill_external()
{
	global $site_db, $current_user_id;
	
	$main_tpl = file_get_contents('templates/external/external.tpl');
	
	$service_id = $_GET['s_id'] > 0 ? $_GET['s_id'] : 1;
 
	// Верхнее меню сервисов
	$top_menu = fill_external_top_menu($service_id);
	
	// Форма редактировани сервиса
	$edit_service_form = fill_external_service_edit_form($current_user_id, $service_id);
	
	// iframe сервиса
	$service_iframe = fill_service_iframe($current_user_id, $service_id);
	
	$PARS['{SERVICE_EDIT_FORM}'] = $edit_service_form;
	
	$PARS['{TOP_MENU}'] = $top_menu;
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{SERVICE_IFRAME}'] = $service_iframe;
	
	return fetch_tpl($PARS, $main_tpl);
}

// Форма редактирования 
function fill_external_service_edit_form($user_id, $service_id)
{
	global $site_db, $current_user_id;
	
	$service_edit_from_tpl = file_get_contents('templates/external/service_edit_from.tpl');
	
	// Выбираем данные по сервисам
	$sql = "SELECT * FROM ".EXTERNAL_TB." WHERE user_id='$user_id' AND service_id='$service_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	$PARS['{IFRAME_SRC}'] = $row['iframe_src'];
	
	$PARS['{IFRAME_TEXT}'] = $row['iframe_text'];
	
	$PARS['{SERVICE_ID}'] = $service_id;
	
	return fetch_tpl($PARS, $service_edit_from_tpl);
}

// Фрейм длясервиса
function fill_service_iframe($user_id, $service_id)
{
	global $site_db, $current_user_id;
	
	$service_frame_tpl = file_get_contents('templates/external/service_frame.tpl');
		
	// Выбираем данные по сервисам
	$sql = "SELECT * FROM ".EXTERNAL_TB." WHERE user_id='$user_id' AND service_id='$service_id'";
	
	$external_data = $site_db->query_firstrow($sql);
	
	// Если пользователь не устанавливал персонально	
	if(!$external_data['iframe_src'])
	{
		// Выбираем ссылку по умолчанию на ресурс
		$sql = "SELECT * FROM ".EXTERNAL_SERVICES_TB." WHERE service_id='$service_id'";
		
		$ext_s_row = $site_db->query_firstrow($sql);
			
		$external_data['iframe_src'] = $ext_s_row['default_url'];
	}
	
	$iframe_src = preg_match('/http|htpps/i', $external_data['iframe_src']) ? $external_data['iframe_src'] : 'http://'.$external_data['iframe_src'];
	
	$PARS['{SERVICE_ID}'] = $service_id;
	
	$PARS['{IFRAME_SRC}'] = $iframe_src.'?s='.rand(1,100000);
	
	return fetch_tpl($PARS, $service_frame_tpl);
}

// Верхнее меню сервисов
function fill_external_top_menu($service_id)
{
	global $site_db, $current_user_id;
	
	$top_menu_tpl = file_get_contents('templates/external/top_menu.tpl');
	
	$top_menu_item_tpl = file_get_contents('templates/external/top_menu_item.tpl');
	
	// Выбор всех сервисов
	$sql = "SELECT * FROM ".EXTERNAL_SERVICES_TB."";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$menu_active = $row['service_id']==$service_id ? 'menu_active' : '';
		$PARS['{SERVICE_ID}'] = $row['service_id'];
		
		$PARS['{SERVICE_NAME}'] = $row['service_name'];
		
		$PARS['{ACTIVE}'] = $menu_active;
		 
		$services_list .= fetch_tpl($PARS, $top_menu_item_tpl);
	}
	
	$PARS['{SERVICES_LIST}'] = $services_list;
	
	return fetch_tpl($PARS, $top_menu_tpl);
}

function get_external_service_name_by_service_id($service_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT * FROM ".EXTERNAL_SERVICES_TB." WHERE service_id='$service_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['service_name'];
}


// Добавить в базу значение по умолчанию для пользователя
/*function set_default_external_services_for_user($user_id)
{
	global $site_db, $current_user_id;
	
	$service_defaults = array(1 => 'http://sipuni.com',
							  2 => 'https://ru.ivideon.com',
							  3 => 'https://ru.ivideon.com',
							  4 => 'http://kk.megafon.ru');
	
	foreach($service_defaults as $service_id => $iframe_src)
	{
		$sql = "INSERT INTO ".EXTERNAL_TB." 
				SET service_id='$service_id', user_id='".$r['user_id']."', iframe_src='$iframe_src', iframe_text = '', date=NOW()";
				
		$site_db->query($sql);			
	}


}*/
?>