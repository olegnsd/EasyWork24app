<?php
// Страница отзывов о сотруднике
function fill_cam($user_id)
{
	global $site_db, $current_user_id;
	
	if($user_id!=$current_user_id)
	{
		header('Location: /cam/'.$current_user_id);
	}
	$main_tpl = file_get_contents('templates/cam/cam.tpl');
	
	// Строка навигации
	$nav = fill_nav('camera');
	
	$cam_list = fill_cam_list($user_id);
	
	$PARS['{NAV}'] = $nav;
	
	$PARS['{CAM_LIST}'] = $cam_list;
	
	return fetch_tpl($PARS, $main_tpl);
}

// Список айфреймов видеонаблюдений
function fill_cam_list($user_id)
{
	global $site_db, $current_user_id;
	
	$cam_item_tpl = file_get_contents('templates/cam/cam_item.tpl');
	
	$cam_iframe_tpl = file_get_contents('templates/cam/cam_iframe.tpl');
	
	// Выбор камеры пользоваетля
	$sql = "SELECT * FROM ".CAMERAS_TB." WHERE user_id='$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	 
	$iframe_display = $row['camera_src'] ? '' : 'none';
	
	if($row['camera_src'])
	{
		$iframe_src = preg_match('/http/i', $row['camera_src']) ? $row['camera_src'] : '';
		
		$PARS_1['{CAMERA_ID}'] = $row['camera_id'];
		
		$PARS_1['{IFRAME_SRC}'] = $iframe_src.'?s='.rand(1,100000);
		
		$iframe_block = fetch_tpl($PARS_1, $cam_iframe_tpl);
	}
	 
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{CAMERA_ID}'] = $row['camera_id'];
	
	$PARS['{VALUE_SRC}'] = $row['camera_src'];
	
	$PARS['{CAM_TEXT}'] = $row['camera_text'];
	
	$PARS['{IFRAME_BLOCK}'] = $iframe_block;
	
	return fetch_tpl($PARS, $cam_item_tpl);
}

// Добавить в базу значение по умолчанию для пользователя
function set_default_camera_for_user($user_id)
{
	global $site_db, $current_user_id;
	
	// Добавляем запись по умолчанию
	$sql = "INSERT INTO ".CAMERAS_TB." (camera_src, user_id) VALUES ('".CAMERA_IFRAME_SRC_DEFAULT."', '$user_id') ";
	
	$site_db->query($sql);
	
	if(mysql_error())
	{
		set_default_camera_for_user($user_id);
	}
}
?>