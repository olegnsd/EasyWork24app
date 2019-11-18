<?php
// Блок видео инструкций
function fill_video_instructions_block($site_page, $method)
{
	$video_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/video_instructions/video_block.tpl');
	
	// Ссылка на видео
	$video_href_id = get_video_ins_href_id($site_page, $method);
	
	// Флаг, просмотрел ли пользователь видео инструкцию
	$is_video_showed = is_user_view_this_video_ins($site_page);
	
	// Если пользователю не требуется выводить на автомате видео раздела
	if($method == 'show_auto' && $is_video_showed)
	{
		return NULL;
	}
	else if(!$is_video_showed)
	{
		// Устанавливаем метку о просмотре видео инструкции
		set_video_ins_view_by_user($site_page);
	}
	
	
	
	// Получение списка видео
	$video_list = get_vidio_instructions_list();
	
	$PARS['{VIDEO_LIST_1}'] = $video_list[0];
	$PARS['{VIDEO_LIST_2}'] = $video_list[1];
	$PARS['{VIDEO_HREF_ID}'] = $video_href_id;
	$PARS['{METHOD}'] = $method;
	
	$video_block = fetch_tpl($PARS, $video_block_tpl);
	
	return array('video_block' => iconv('cp1251', 'utf-8', $video_block), 'video_href_id' => $video_href_id);
}

// Отметка о просмотре видео инстуркции
function set_video_ins_view_by_user($site_page)
{
	global $site_db, $current_user_id;
	
	if(!$site_page || !$current_user_id)
	{
		return '';
	}
	
	if($site_page)
	{
		// Проверяем, есть ли видео для данного раздела
		$sql = "SELECT * FROM ".VIDEO_INS_TB." WHERE site_page='$site_page'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['video_id'])
		{
			$sql = "INSERT INTO ".VIDEO_INS_VIEWS_TB." SET user_id='$current_user_id', site_page='$site_page'";
	 
			$site_db->query($sql);
		}
	}
	
}

// Проверяем, выводилось ли пользователю видео как помощ при первом заходе в модуль
function is_user_view_this_video_ins($site_page)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT * FROM ".VIDEO_INS_VIEWS_TB." WHERE user_id='$current_user_id' AND site_page='$site_page'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['id'])
	{
		return true;
	}
	else return false;
}

function get_video_ins_href_id($site_page, $method)
{	
	global $site_db;
	
	$sql = "SELECT * FROM ".VIDEO_INS_TB." WHERE site_page='$site_page'";
	
	$video_arr = $site_db->query_firstrow($sql);
	
	if($site_page && $video_arr['video_id'])
	{
		return $video_arr['video_href_id'];
	}
	else if($method=='show_auto')
	{
		return '';
	}
	else 
	{
		$sql = "SELECT * FROM ".VIDEO_INS_TB." WHERE site_page='vvedenie'";
		
		$video_arr = $site_db->query_firstrow($sql);
		
		return $video_arr['video_href_id'];
	}  
}

// Список видео инструкций
function get_vidio_instructions_list()
{
	global $site_db;
	
	$video_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/video_instructions/video_list_item.tpl');

	
	// Выбираем список видео
	$sql = "SELECT * FROM ".VIDEO_INS_TB." ORDER by sort";
	
	$res = $site_db->query($sql);
		
	$num = 1;
	
	while($row=$site_db->fetch_array($res))
	{
		$PARS['{NUM}'] = $num;
		
		$PARS['{SITE_PAGE}'] = $row['site_page'];
		
		$PARS['{VIDEO_HREF_ID}'] = $row['video_href_id'];
		
		$PARS['{VIDEO_NAME}'] = $row['video_name'];
		
		// На два блока
		if($num < 12)
		{
			$video_list_1 .= fetch_tpl($PARS, $video_list_item_tpl);
		}
		else
		{
			$video_list_2 .= fetch_tpl($PARS, $video_list_item_tpl);
		}
		
		$num++;
	}
	
	return array($video_list_1, $video_list_2);
}
?>