<?php
// Просмотр файла
function fill_file()
{
	global $site_db, $current_user_id;
	
	$file_info_tpl = file_get_contents('templates/disk/file_info.tpl');
	
	
	$file_id = value_proc($_GET['f']);
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.File.php');
	
	$fl = new File($site_db);
	 
	$file_data = $fl->get_file_data($file_id);
	
	// Доступ к папке	
	$_f_access =  get_file_access($current_user_id, $file_id, 0);
	// У пользователя есть доступ на совершение операции Изменения файла
	if(f_check_access('edit', $_f_access))
	{
		$edit_priv = 1;
	}
	if(f_check_access('read', $_f_access))
	{
		$read_priv = 1;
	}
		 
	// Если файла не существует
	if(!$file_data['file_id'])
	{
		return fill_404();
	}
	
	if(!$read_priv && !$edit_priv)
	{
		return file_error('no_access_file', 'file', $file_id);
	}
	
	if($_GET['fact']=='v')
	{
		// Версии файла
		$file_content = fill_file_content_versions($file_data);
	}
	else if($_GET['fact']=='c')
	{
		// Редактировать права доступа пользователю нельзя
		if(!$edit_priv)
		{
			return file_error('no_access_file_is_company', 'file', $file_id);
		}
		else if($file_data['is_company'] )
		{
			return file_error('no_access_file_is_company', 'file', $file_id);
		}
		$file_content = fill_file_content_access_file($file_data);
	}
	else
	{
		$file_content = fill_file_content_properties($file_data);
	}
	 
	$nav = fill_disk_nav('file', $file_data['folder_id']);
	
	// Верхнее меню
	$top_menu = fill_file_top_menu($file_data, $edit_priv);
	
	$PARS['{FILE_ID}'] = $file_id;
	
	$PARS['{TOP_MENU}'] = $top_menu; 
	
	$PARS['{FILE_NAME}'] = $file_data['file_name']; 
	
	$PARS['{FILE_CONTENT}'] = $file_content; 
	
	$PARS['{NAV}'] = $nav;
	
	return fetch_tpl($PARS, $file_info_tpl);
	 
}

function file_error($error, $what, $id)
{
	switch($error)
	{
		case 'folder_not_access':
			
			$msg = 'Папка недоступна для просмотра.';
			
		break;
		case 'no_access_file':
			
			$msg = 'Файл недоступен для просмотра.';
			
		break;
		case 'no_access_file_is_company':
			
			$msg = 'Файл компании.';
			
		break;
		
		case 'no_access_file_to_download':
			
			$msg = 'Файл недоступен для скачавания.';
			
		break;
		
		case 'no_access_file_to_edit':
			
			$msg = 'Нет полномочий на редактирование файла.';
			
		break;
	}
	
	return $msg;
}

// Добавить доступ к файлу через интернет 
function save_file_pub($file_id, $time_value, $time_mode, $desc, $is_system=0)
{
	global $site_db, $current_user_id;
	
	$fl = new File($site_db);
	
	$file_data = $fl->get_file_data($file_id);
	
	// Доступ к файлу	
	$_f_access =  get_file_access($current_user_id, $file_id, 0);
	
	// У пользователя нет доступа на совершение операции
	if(!f_check_access('read', $_f_access) && !$is_system)
	{
		return -1;
	}
	
	$date_from = time();
	
	switch($time_mode)
	{
		case 1:
			$time_mode_in_sec = 60 * 60 * 24;
		break;
		case 2:
			$time_mode_in_sec = 60 * 60;
		break;
		case 3:
			$time_mode_in_sec = 60;
		break;
	}
	
	if(is_numeric($time_value))
	{
		$date_to = $date_from + ($time_mode_in_sec * $time_value);
	}
	else
	{
		$date_to = 0;
	}
	 
	
	// Случайный id файла
	$file_name_id = generate_rand_string(40);
	
	// Если создается ссылка на файл для системных нужн, не делаем проверки на наличие других ссылок
	if(!$is_system)
	{
		// Во избежании дублирования публичных ссылок на файл
		$sql = "SELECT id FROM tasks_files_pub WHERE is_system=0 AND file_id='$file_id'";
		$pub_data = $site_db->query_firstrow($sql);
		
		if($pub_data['id'])
		{
			// Удаляем старую запись
			$sql = "DELETE FROM tasks_files_pub WHERE id='".$pub_data['id']."'";
			$site_db->query($sql);
		}
	}
	
	// Добавляем в таблицу публикации файла в интернете
	$sql = "INSERT INTO tasks_files_pub SET file_id='$file_id', file_name_id='$file_name_id', user_id='$current_user_id', date_from='$date_from', date_to='$date_to', `desc`='$desc', is_system='$is_system'";
	
	$res = $site_db->query($sql);
	
	$pub_id = $site_db->get_insert_id();
	
	// Если публичная ссылка была создана для системных нужд
	if(!$is_system)
	{
		// Устаналиваем флаг на файл публичный он или нет
		$fl->file_pub_flag($file_id);
	}
	
	return $pub_id;
}

 
// Выводит блок доступа файла в интернете
function open_file_pub_block($file_id)
{
	global $site_db, $current_user_id;
	
	$fl = new File($site_db);
	
	$file_data = $fl->get_file_data($file_id);
	
	$file_content_pub_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_content_pub.tpl');
	
	// Публичные ссылки на файл
	$file_pub_link = get_file_pub_links($file_id);
	 
	 
	$PARS['{FILE_ID}'] = $file_id; 
	
	$PARS['{FILE_NAME}'] = $file_data['file_name'];
	
	$PARS['{SIZE}'] = formate_filesize($file_data['size']);
	
	$PARS['{FILE_PUB_LIST}'] = $file_pub_link; 
	
	return fetch_tpl($PARS, $file_content_pub_tpl);
}

// Возвращает ссылку на публичный файл
function get_file_pub_url($file_pub_data, $pub_id, $to_download)
{
	global $site_db, $db_host;
	
	// Получаем данные публичной ссылки
	if($pub_id)
	{
		$sql = "SELECT * FROM tasks_files_pub WHERE id='$pub_id'";
	
		$file_pub_data = $site_db->query_firstrow($sql);
	}
	
	if($to_download)
	{
		$pars = "?download=1";
	}
	
	return 'http://'.HOST.'/file/pub/'.$file_pub_data['file_name_id'].$pars;
}
// Ссылка в интернете на файл
function get_file_pub_links($file_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$file_pub_link_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_pub_link_block.tpl');
	
	$sql = "SELECT * FROM tasks_files_pub WHERE file_id='$file_id' AND is_system=0";
	
	$pub_data = $site_db->query_firstrow($sql);
	
	// Если ссылки нет на файл
	if(!$pub_data['id'])
	{
		return '';
	}
	
	if($pub_data['date_to'])
	{
		$access_date_to = ' до '.datetime($pub_data['date_to'], '%d.%m.%Y %H:%i', 1);
	}
	else
	{
		$access_date_to = 'не ограничен';
	}
	
	// Заполянем объект пользователя, кто передал ссылку
	$user_obj->fill_user_data($pub_data['user_id']);
		
	 	
	$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{USER_NAME}'] = $user_obj->get_user_name();
	
	$PARS['{FILE_ID}'] = $file_id;
	
	$PARS['{ACCESS_DATE_TO}'] = $access_date_to;
	
	$PARS['{FILE_NAME_ID}'] = $pub_data['file_name_id'];
	
	$PARS['{LINK}'] = get_file_pub_url($pub_data);
	
	return fetch_tpl($PARS, $file_pub_link_block_tpl);
}

// Блок прав доступа к файлу
function fill_file_content_access_file($file_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$file_content_access_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_content_access.tpl');
	
	$file_access_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_access_list_item.tpl');
	
	$file_id = $file_data['file_id'];
	
	$sql = "SELECT * FROM tasks_files_access WHERE file_id='$file_id' ORDER by id ";
	
	$res = $site_db->query($sql);
	
	$num=1;		 
	while($row=$site_db->fetch_array($res))
	{
		$access_mode_selected = array(0,0,0,0);
		
		$access_mode_selected[$row['access']] = 'selected="selected"';
		
		 
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row['user_id']);
		
		$user_name = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
		 
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{FILE_ID}'] = $file_id;
		
		$PARS['{ELEM}'] = 'file_'.$file_id;
		
		$PARS['{NUM}'] = $num;
		
		$PARS['{USER_ID}'] = $row['user_id'];
		
		$PARS['{USER_NAME}'] = $user_name;
		
		$PARS['{ACCESS_MODE_SELECTED_1}'] = $access_mode_selected[1];
		
		$PARS['{ACCESS_MODE_SELECTED_2}'] = $access_mode_selected[2];
		
		$PARS['{ACCESS_MODE_SELECTED_3}'] = $access_mode_selected[3];
		
		$access_users_list .= fetch_tpl($PARS, $file_access_list_item_tpl);
		
		$num++;
	}
	
	$PARS['{ELEM}'] = 'file_'.$file_id;
	
	$PARS['{ACCESS_LIST}'] = $access_users_list; 
	
	return fetch_tpl($PARS, $file_content_access_tpl);
}
// Блок прав доступа к папке
function fill_file_content_access_folder($folder_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$file_content_access_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_content_access.tpl');
	
	$file_access_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_access_list_item.tpl');
	
	$folder_id = $folder_data['folder_id'];
	
	$sql = "SELECT * FROM tasks_files_folders_access WHERE folder_id='$folder_id' ORDER by id ";
	
	$res = $site_db->query($sql);
	
	$num=1;		 
	while($row=$site_db->fetch_array($res))
	{
		$access_mode_selected = array(0,0,0,0);
		
		$access_mode_selected[$row['access']] = 'selected="selected"';
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row['user_id']);
		
		$user_name = $user_obj->get_user_surname().' '.$user_obj->get_user_name().' '.$user_obj->get_user_middlename();
		 
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{FOLDER_ID}'] = $folder_id;
		
		$PARS['{ELEM}'] = 'folder_'.$folder_id;
		
		$PARS['{NUM}'] = $num;
		
		$PARS['{USER_ID}'] = $row['user_id'];
		
		$PARS['{USER_NAME}'] = $user_name;
		
		$PARS['{ACCESS_MODE_SELECTED_1}'] = $access_mode_selected[1];
		
		$PARS['{ACCESS_MODE_SELECTED_2}'] = $access_mode_selected[2];
		
		$PARS['{ACCESS_MODE_SELECTED_3}'] = $access_mode_selected[3];
		
		$access_users_list .= fetch_tpl($PARS, $file_access_list_item_tpl);
		
		$num++;
	}
	
	$PARS['{ELEM}'] = 'folder_'.$folder_id;
	
	$PARS['{ACCESS_LIST}'] = $access_users_list; 
	
	return fetch_tpl($PARS, $file_content_access_tpl);
}

// Версии файлов
function fill_file_content_versions($file_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$file_content_versions_tpl = file_get_contents('templates/disk/file_content_versions.tpl');
	
	$no_contents_1_tpl = file_get_contents('templates/disk/no_contents_1.tpl');
	
	$file_version_list_item_tpl = file_get_contents('templates/disk/file_version_list_item.tpl');
	
	$file_version_list_actual_tpl = file_get_contents('templates/disk/file_version_list_actual.tpl');
	
	$file_version_tools_tpl = file_get_contents('templates/disk/file_version_tools.tpl');
	
	
	$file_id = $file_data['file_id'];
	
	// Доступ к папке	
	$_f_access =  get_file_access($current_user_id, $file_id, 0);
	
	// У пользователя есть доступ на совершение операции
	if(f_check_access('edit', $_f_access))
	{
		//Форма добавления версии файла
		$file_add_version_form = fill_upload_add_form(0, $file_id);
		
		$edit_priv = 1;
	}
	 
	
	// Актуальная версия файла
	$actual_file_version = $file_data['version_id'];
	 
	
	// Список всех версий файла
	$sql = "SELECT *  FROM tasks_files_versions WHERE file_id='$file_id' AND deleted != 1 ORDER by version_id DESC";
	
	$res = $site_db->query($sql);
			 
	while($row=$site_db->fetch_array($res))
	{		
		// Если у пользователя есть привелеги на редактирвоание файла
		if($edit_priv== 1)
		{
			$PARS['{VERSION_ID}'] = $row['version_id'];
			$tools_list = fetch_tpl($PARS, $file_version_tools_tpl);
		}
		 
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		// ссылка на скачивание
		$download_link = get_file_download_link($row['version_id'], 'file_version'); 
		
		// Автор
		$author = get_formate_user_name($row['user_id']);
		
		$PARS['{DOWNLOAD_LINK}'] = $download_link;
		
		$PARS['{FILE_ID}'] = $file_id;
	
		$PARS['{VERSION_ID}'] = $row['version_id'];
		
		$PARS['{NAME}'] = $row['file_name'];
		
		$PARS['{SIZE}'] = formate_filesize($row['size']);
		
		$PARS['{DATE_EDIT}'] = datetime($row['date_add'], '%d.%m.%Y %H:%i');
		
		$PARS['{AUTHOR}'] = $author;
		
		$PARS['{TOOLS_LIST}'] = $tools_list;
		
		// Не выводим актуальную версию файла
		if($actual_file_version==$row['version_id'])
		{
			$versions_list .= fetch_tpl($PARS, $file_version_list_actual_tpl);
		}
		else
		{
			$versions_list .= fetch_tpl($PARS, $file_version_list_item_tpl);
		}
		 
	}
	
	if(!$versions_list)
	{
		$versions_list = $no_contents_1_tpl;
	}
	
	// Кол-во версий файла
	$versions_count = get_file_versions_count($file_id);
	
	$PARS['{VERSIONS_LIST}'] = $versions_list;
	
	$PARS['{VERSIONS_COUNT}'] = $versions_count;
	
	$PARS['{ADD_VERSION_FORM}'] = $file_add_version_form; 
	
	return fetch_tpl($PARS, $file_content_versions_tpl);
}

 
// Кол-во версий файла
function get_file_versions_count($file_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "SELECT COUNT(*) as count FROM tasks_files_versions WHERE file_id='$file_id' AND deleted != 1";
	
	$row = $site_db->query_firstrow($sql);
	
	$count = $row['count'] >= 0 ? $row['count'] : 0;
	
	return $count;
}

// Свойства файла
function fill_file_content_properties($file_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$file_content_properties_tpl = file_get_contents('templates/disk/file_content_properties.tpl');
	
	$edit_desc_tool_tpl = file_get_contents('templates/disk/edit_desc_tool.tpl');
	
	$file_id = $file_data['file_id'];
	
	// Доступ к папке	
	$_f_access =  get_file_access($current_user_id, $file_id, 0);
	
	// У пользователя есть доступ на совершение Редактирования
	if(f_check_access('edit', $_f_access))
	{
		// Кнопка редактирования описания
		$PARS['{FILE_ID}'] = $file_id;
		$edit_desc_tool = fetch_tpl($PARS, $edit_desc_tool_tpl);
	}
	 
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($file_data['user_id']);
	
	$PARS['{AVATAR_SRC}'] = get_user_preview_avatar_src($file_data['user_id'], $user_obj->get_user_image());
	
	$PARS['{USER_ID}'] = $current_user_id;
					
	$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
	
	$PARS['{USER_NAME}'] = $user_obj->get_user_name();
	
	$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
	
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	
	
	$PARS['{FILE_CONTENT}'] = $file_content;
	
	$PARS['{FILE_ID}'] = $file_id; 
	
	$PARS['{FILE_NAME}'] = $file_data['file_name'];
	
	$PARS['{FILE_SIZE}'] = formate_filesize($file_data['size']);
	
	$PARS['{DATE_ADD}'] = datetime($file_data['date_add'], '%d.%m.%Y в %H:%i');
	
	$PARS['{DATE_EDIT}'] =datetime($file_data['date_edit'], '%d.%m.%Y в %H:%i');  
	 
	$PARS['{FILE_DESC}'] = nl2br($file_data['file_desc']);
	
	$PARS['{EDIT_DESC_TOOL}'] = $edit_desc_tool; 
	
	
	return fetch_tpl($PARS, $file_content_properties_tpl);
	
}

// Верхнее меню при просмотре файла
function fill_file_top_menu($file_data, $edit_priv)
{
	$file_top_menu_tpl = file_get_contents('templates/disk/file_top_menu.tpl');
	
	$file_top_menu_access_tpl = file_get_contents('templates/disk/file_top_menu_access.tpl');
	
	$file_id = $file_data['file_id'];
	
	$PARS['{FILE_ID}'] = $file_id;
	
	// Файл не является файлом компании и у пользователя есть права на его изменения - Выводим пункт меню - "права доступа"
	if(!$file_data['is_company'] && $edit_priv)
	{
		$access_top = $file_top_menu_access_tpl;
	}
	
	$PARS['{ACCESS_TOP}'] = $access_top; 
	$file_top_menu_tpl =  fetch_tpl($PARS, $file_top_menu_tpl);
	
	
	if($_GET['fact']=='v')
	{
		$active_2 = 'menu_active';
	}
	else if($_GET['fact']=='c')
	{
		$active_3 = 'menu_active';
	}
	else
	{
		$active_1 = 'menu_active';
	}
	
	
	// Возвращает параметры
	$pars = file_get_href_parameters_part();
	 
	$PARS['{ACTIVE_1}'] = $active_1;
	$PARS['{ACTIVE_2}'] = $active_2; 
	$PARS['{ACTIVE_3}'] = $active_3;
	 
	
	$PARS['{PARS}'] = $pars; 
	
	return fetch_tpl($PARS, $file_top_menu_tpl);
}

function file_get_href_parameters_part($amp)
{
	$true_pars = array('act', 'navto', 'fid', 'f');
	
	foreach($_GET as $i => $j)
	{
		if(in_array($i, $true_pars) && $j)
		{
			$pars[] = $i.'='.$j;
		}
	}
	
	if($pars)
	{
		$pars =  implode('&', $pars);
	}
	else return '';
	 
	if($amp)
	{
		return '&'.$pars;
	}
	else
	{
		return $pars;
	}
}

function fill_my_files()
{
	global $site_db, $current_user_id;
}
// Страница клиентов сотрудника
function fill_disk($user_id)
{
	global $site_db, $current_user_id;
	
	$main_tpl = file_get_contents('templates/disk/disk.tpl');
	
	$more_btn_tpl = file_get_contents('templates/disk/more_btn.tpl');
	
	$no_contents_tpl = file_get_contents('templates/disk/no_contents.tpl');
	
	$p = value_proc($_GET['p']);
	 
	$p = $p ? $p : 1;
	
	$folder_id = value_proc($_GET['fid']);
	$folder_id = is_numeric($folder_id) ? $folder_id  : 0;
	
	$act = $_GET['act'];
	
	
	$fl = new File($site_db);
	$folder_data = $fl->get_folder_data($folder_id);
	
	if($folder_data['is_company'] && $act!='co')
	{
		header('Location: /disk?act=co&fid='.$folder_id);
	}
	
	$folder_access =  get_file_access($current_user_id, $file_id, $folder_id);
	
	 // echo print_r($folder_access);
	
	// Есть права на изменения
	//echo f_check_access('edit', $folder_access);
	
	if($folder_id && !f_check_access('read', $folder_access))
	{
		return file_error('folder_not_access', 'folder', $folder_id);
	}
	
	// Если личные папки или просмотр папки к которой есть доступ
	if(!$_GET['act'] || ($folder_id && f_check_access('edit', $folder_access)) || ($_GET['act']=='co' && !$folder_id))
	{  
		// Форма добавления
		$upload_add_form = fill_upload_add_form($folder_id);
	}
			
	switch($act)
	{
		// Чужие файлы
		case 'av':
			
			if($folder_id)
			{
				// Кол-во папок 
				$folders_count = get_folders_count($current_user_id, $folder_id);
				// Кол-во файлов
				$files_count = get_files_count($current_user_id, $folder_id);
				
				$all_count = $folders_count+$files_count;
				
				// Список файлов и папок
				$files_list = fill_files_list_block($p, $current_user_id, $folder_id, $folders_count, $files_count);
			}
			else
			{
				// Список файлов и папок
				$files_list = fill_files_list_block_available($p, $current_user_id, $folder_id, $folders_count, $files_count);
			}
			
		break;
		
		// Файлы компании
		case 'co':
			
			// Форма добавления
			//$upload_add_form = fill_upload_add_form($folder_id);
			
			// Кол-во папок 
			$folders_count = get_folders_company_count($folder_id);
			// Кол-во файлов
			$files_count = get_files_company_count($folder_id);
			
			$all_count = $folders_count+$files_count;
			
			// Список файлов и папок - КОМПАНИИ
			$files_list = fill_files_list_block_company($p, $folder_id, $folders_count, $files_count);
	
		break;
		
		// Мои файлы 
		default:
			
			// Форма добавления
			//$upload_add_form = fill_upload_add_form($folder_id);
	
			// Кол-во папок 
			$folders_count = get_folders_count($current_user_id, $folder_id);
			// Кол-во файлов
			$files_count = get_files_count($current_user_id, $folder_id);
			
			$all_count = $folders_count+$files_count;
			
			// Список файлов и папок - ПЕРСОНАЛЬНЫЕ
			$files_list = fill_files_list_block($p, $current_user_id, $folder_id, $folders_count, $files_count);
			
		break;
	}
	 
	
	
	
	// Если списка нет, выводим "данных нет"
	if(!$files_list)
	{
		$files_list = $no_contents_tpl;
	}
	
	// Кол-во страниц
	$pages_count = ceil($all_count/FILES_PER_PAGE);
	 
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_btn = $more_btn_tpl;
	}
	
	if($_GET['act']=='av')
	{
		$active_2 = 'menu_active';
	}
	else if($_GET['act']=='co')
	{
		$active_3 = 'menu_active';
	}
	else
	{
		$active_1 = 'menu_active';
	}
	 
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/includes/functions_pages.php');
	
	$all_count = $folders_count+$files_count;
	
	// Страничность
	$pages = fill_pages($href, $p, $all_count, FILES_PER_PAGE);

	 
	if($pages || $all_count)
	{
		if(!$pages) $pages = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/pages_one.tpl');
		
		$PARS['{PAGES}'] =  $pages;
		
		$pages_wrap = fetch_tpl($PARS, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/pages_wrap.tpl'));
	}
	
	if(!$_GET['act'] || $_GET['act']=='co')
	{
		$PARS['{ALL_COUNT}'] = $all_count; 
		
		$all_count_wrap = fetch_tpl($PARS, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/all_count_wrap.tpl'));
	}
	
	// Кол-во новых файлов от всех пользователей
	$new_file_count = new_file_available_count($current_user_id);
	
	if($new_file_count)
	{
		$new_file_count =  '(+ '.$new_file_count.')';
	}
	else
	{
		$new_file_count = '';
	}
	
	// Навигация
	$nav = fill_disk_nav();
	
	$PARS['{ADD_FORM}'] = $upload_add_form;
	
	$PARS['{UPLOAD_SIZE_LIMIT}'] = UPLOAD_SIZE_LIMIT;
	
	$PARS['{FILES_LIST}'] = $files_list;
	
	$PARS['{MORE_BTN}'] = $more_btn;
	
	$PARS['{FOLDER_ID}'] = $folder_id;
	
	$PARS['{ACTIVE_1}'] = $active_1;
	
	$PARS['{ACTIVE_2}'] = $active_2; 
	
	$PARS['{ACTIVE_3}'] = $active_3;
	
	$PARS['{PAGES_WRAP}'] = $pages_wrap;
	
	$PARS['{ALL_COUNT_WRAP}'] = $all_count_wrap;
	
	$PARS['{NAV}'] = $nav;
	
	$PARS['{NEW_FILE_COUNT}'] = $new_file_count;
	
	return fetch_tpl($PARS, $main_tpl);
}

// Кол-во новых полученных файлов и папок
function new_file_available_count($user_id, $by_user_id)
{
	global $site_db, $current_user_id;
	
	// Кем переданы
	if($by_user_id)
	{
		$and_by_user = " AND by_user_id='$by_user_id'";
	}
	
	$sql = "SELECT COUNT(*) as count FROM tasks_files_access WHERE user_id='$user_id' AND noticed=0 $and_by_user ";
	
	$row = $site_db->query_firstrow($sql);
	
	$count = $row['count'];
	
	$sql = "SELECT COUNT(*) as count FROM tasks_files_folders_access WHERE user_id='$user_id' AND noticed=0 $and_by_user ";
	
	$row = $site_db->query_firstrow($sql);
	
	$count += $row['count'];
	
	return $count;
}

// Проверка доступа
function f_check_access($act='', $access_arr)
{
	$access = false;
	
	switch($act)
	{
		// Проверка на чтение
		case 'read':
			
			// Папка компании ИЛИ корневая папка является папкой пользователя ИЛИ есть доступ
			if($access_arr['is_company']==1 || $access_arr['is_user']==1 || $access_arr['access'] > 0)
			{ 
				$access = true;
			}
			
		break;
		
		// Проверка на изменение
		case 'edit':
			
			// владельцем корневой папки является пользователь
			if($access_arr['is_user']==1)
			{  
				$access = true;
			} 
			// Папка компании и владельцем корневой папки является пользователь
			else if($access_arr['is_company']==1 && $access_arr['is_user']==1)
			{  
				$access = true;
			}
			// Установлен доступ на изменение
			else if($access_arr['access'] == 2)
			{ 
				$access = true;
			}
			
		break;
	}
	 
	return $access;
}
function get_file_access($user_id, $file_id, $folder_id)
{
	global $site_db, $current_user_id;
	
	 
	$q_folder_id = $folder_id;
	
	
	// Если передан файл
	if($file_id && !$folder_id)
	{
		$fl = new File($site_db);
		$file_data = $fl->get_file_data($file_id);
		 
		$is_company = $file_data['is_company'];
		
		// Если пользователь является владельцем файла
		if($file_data['user_id']==$user_id)
		{
			$is_user = 1;
			$stop = true;
		}
		
		$q_folder_id = $file_data['folder_id'];
		
		// Поиск привилегий на файл в таблице доступа
		$sql = "SELECT * FROM tasks_files_access WHERE file_id='$file_id' AND user_id='$user_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'] && $row['access']>0)
		{ 
			$access = $row['access'];
			 
		}
	}
	
	if($q_folder_id)
	{
		
		while(!$stop)
		{
			// Выбираем данные папки 
			$sql = "SELECT * FROM tasks_files_folders WHERE folder_id='".$q_folder_id."'";
			  
			$row = $site_db->query_firstrow($sql);
			
			// Проверяем, является ли пользователь владельцем родительской папки
			if($row['user_id']==$user_id)
			{
				$is_user = 1;
				$stop = true;
			}
			
			// Проверяем, есть ли доступ к этой папке или ее родителю 
			if($row['folder_id'] && !$access)
			{
				$sql = "SELECT * FROM tasks_files_folders_access WHERE folder_id='$q_folder_id' AND user_id='$user_id'";
	 
				$access_row = $site_db->query_firstrow($sql);
				
				if($access_row['id'] && $access_row['access']>0)
				{
					$access = $access_row['access'];
					$stop = true;
				}
			}
			
			
			$q_folder_id = $row['parent_folder_id'];
			
			// Цикд дошел до корневой папки, записываем данные
			if(!$q_folder_id)
			{  
				// Является ли владелец корневой папки пользователь 
				$is_user = $row['user_id'] == $user_id ? 1 : 0;
				$stop = true;
			}
			if($row['folder_id'])
			{
				$folders_arr[$row['folder_id']] = $row['folder_id'];
			}
			
			$is_company = $row['is_company'];
			 
		}
	}
	
	
	$result = array('is_company' => $is_company, 'is_user' => $is_user, 'access' => $access);
	
 	return $result;
	
	// Корневая папка
	//$root_folder_id = end($folders_arr);
	
	//$folders_arr = array_reverse($folders_arr, true);
	
	//print_r($folders_arr);
	
}

//Навигация по папкам
function fill_disk_nav($link_this_folder=0, $folder_id)
{
	global $site_db, $current_user_id, $user_obj;
	 
	$nav_main_tpl = file_get_contents('templates/disk/nav_main.tpl');
	
	$nav_current_tpl = file_get_contents('templates/disk/nav_current.tpl');
	
	$nav_a_tpl = file_get_contents('templates/disk/nav_a.tpl');
	
	$nav_sep_tpl = file_get_contents('templates/disk/nav_sep.tpl');
	
	$nav_block_tpl = file_get_contents('templates/disk/nav_block.tpl');
	
	// номер папки, до которой вычисляем путь и выводим в навигации
	if($_GET['navto'])
	{  
	  $nav_to = '&navto='.$_GET['navto'];
	}	
	else if(!$link_this_folder)
	{
		return '';
	}
	
	 
	$folder_id = $folder_id ?  $folder_id : $_GET['fid'];
	 
	if($folder_id && $nav_to) 
	{
		//$folder_id = $_GET['fid'];
		
		$sql = "SELECT * FROM tasks_files_folders WHERE folder_id='$folder_id'";

		$row = $site_db->query_firstrow($sql);
		 
		if($_GET['navto']!=$folder_id)
		{ 
			while(!$stop)
			{
				 
				$sql = "SELECT * FROM tasks_files_folders WHERE folder_id='".$row['parent_folder_id']."'";
				 
				$row = $site_db->query_firstrow($sql);
				
				if(!$row['parent_folder_id'] || $_GET['navto']==$row['folder_id'])
				{ 
					$stop = true;
				}
				if($row['folder_id'])
				{
					$folders_arr[$row['folder_id']] = $row;
				}
				 
			}
		} 
		
		//echo "<pre>", print_R($folders_arr);
		
		$folders_arr = array_reverse($folders_arr, true);
		 
		foreach($folders_arr as $f_id => $folder_data)
		{		
			$sql = "SELECT folder_name FROM tasks_files_folders WHERE folder_id='$f_id'";
			
			$row = $site_db->query_firstrow($sql);
			
		 	
			$PARS['{TITLE}'] = $row['folder_name'];
				
			$PARS['{HREF}'] = '/disk?fid='.$f_id.'&act='.$_GET['act'].$nav_to;
				
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_a_tpl);
		} 
		
		// Папка, которую просматриваем
		$sql = "SELECT folder_name FROM tasks_files_folders WHERE folder_id='$folder_id'";
			 
		$row = $site_db->query_firstrow($sql);
		
		// Если требуется дать ссылку на конечную папку
		if($link_this_folder)
		{
			$PARS['{TITLE}'] = $row['folder_name'];
			$PARS['{HREF}'] = '/disk?fid='.$folder_id.'&act='.$_GET['act'].$nav_to;
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_a_tpl);
		}
		else
		{
			$PARS['{TITLE}'] = $row['folder_name'];
					
			$nav_string .= $nav_sep_tpl.fetch_tpl($PARS, $nav_current_tpl);
		}
		 
		
		
		 
	
	}
	else
	{
		//$PARS['{TITLE}'] = get_disk_name_by_act();
		//$nav_string .= fetch_tpl($PARS, $nav_current_tpl); 
	}
	
	if($link_this_folder)
	{
		
	}
	
	$PARS['{TITLE}'] = get_disk_name_by_act();
	$PARS['{HREF}'] = '/disk&act='.$_GET['act'];
	$nav_string = fetch_tpl($PARS, $nav_a_tpl).$nav_string;
	 
	 
	  
	$PARS_1['{NAV}'] = $nav_string;
	
	return fetch_tpl($PARS_1, $nav_block_tpl);
	 
}


// Возвращает название категории файлов
function get_disk_name_by_act()
{
	switch($_GET['act'])
	{
		case 'av':
			return 'Чужие файлы';
		break;
		case 'co':
			return 'Файлы компании';
		break;
		default:
			return 'Мои файлы';
		break;
	}
}

// Список файлов и папок, которые доступны
function fill_files_list_block_available($p, $user_id, $folder_id, $folders_count, $files_count)
{
	global $site_db, $current_user_id, $user_obj;
	
	$files_list_group_tpl = file_get_contents('templates/disk/files_list_group.tpl');
	
	$more_group_files_btn_tpl = file_get_contents('templates/disk/more_group_files_btn.tpl');
	
	$not_confirm_count_tpl = file_get_contents('templates/disk/not_confirm_count.tpl');
	
	$bind_confirm_item_tpl = file_get_contents('templates/disk/bind_confirm_item.tpl');
	
	// Поиск переданных папок
	$sql = "SELECT * FROM tasks_files_folders_access WHERE user_id='$user_id' AND access > 0 GROUP by by_user_id";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		 $by_users[$row['by_user_id']] = $row['by_user_id'];
	}
	
	
	// Поиск переданных файлов
	$sql = "SELECT * FROM tasks_files_access WHERE user_id='$user_id' AND access > 0 GROUP by by_user_id";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		 $by_users[$row['by_user_id']] = $row['by_user_id'];
	}
	
	 
	// Для каждого пользователя выводим папки и файлы, которые он передал
	foreach($by_users as $by_user_id)
	{
		$files_list = '';
		
		$p = 1;
		
		// Список файлов
		$files_list_arr = get_files_list_available_by_user_id_arr($by_user_id, $p);
		
		$pages_count = $files_list_arr['pages_count'];
		
		// Кол-во непросмотренных файлов и папок от пользователя
		$not_confirm_count = new_file_available_count($current_user_id, $by_user_id);
		
		// Кнопка Вывести больше папок и файлов 
		$more_group_files_btn = '';
		if($pages_count>1)
		{ 
			$PARS['{USER_ID}'] = $by_user_id;
			$PARS['{PAGES_COUNT}'] = $pages_count;
			$more_group_files_btn = fetch_tpl($PARS, $more_group_files_btn_tpl);
		}
		
		 
		$not_confirm_count_block = '';  
		// Если есть непросмотренные файлы и папки
		if($not_confirm_count)
		{
			$PARS['{USER_ID}'] = $by_user_id;
			$PARS['{COUNT}'] = $not_confirm_count;
			$not_confirm_count_block = fetch_tpl($PARS, $not_confirm_count_tpl);
		}
		 
		 
		// Заполянем объект пользователя
		$user_obj->fill_user_data($by_user_id);
		
		$PARS['{AVATAR_SRC}'] = get_user_preview_avatar_src($by_user_id, $user_obj->get_user_image());
		
		$PARS['{USER_ID}'] = $by_user_id;
						
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
		$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
		$PARS['{FILES_LIST}'] = $files_list_arr['list'];
		
		$PARS['{MORE_FILES}'] = $more_group_files_btn;
		
		$PARS['{NOT_CONFIRM_COUNT_BLOCK}'] = $not_confirm_count_block;
		
		$list .= fetch_tpl($PARS, $files_list_group_tpl);
	}
	
	return $list;
}

// Список разрешенных файлов и папок пользователю
function get_files_list_available_by_user_id_arr($by_user_id, $p)
{
	global $site_db, $current_user_id;
	
	$p = !$p ? 1 : $p;
	
	$folders_count = get_folders_count_available($current_user_id, $by_user_id);
	$files_count = get_files_count_available($current_user_id, $by_user_id);
	
	// Формируем данные по кол-ву выводимых файлов и папок
	$select_limits = select_files_limits($p, $folders_count, $files_count, FILES_GROUP_PER_PAGE);
	
	if($p <= $select_limits['pages_folders_count'])
	{
		// Список папок
		$files_list_arr = fill_folders_list_available($current_user_id, $by_user_id, $select_limits['folders_limit_from'], $select_limits['folders_limit']);
		
		// Список папок
		$files_list .= $files_list_arr['list'];
		
		// Кол-во непросмотренных папок
		$not_confirm_count += $files_list_arr['not_confirm_count'];
	}
	// Список файлов
	if($select_limits['files_limit'])
	{
		$files_list_arr= fill_files_list_available($current_user_id, $by_user_id, $select_limits['files_limit_from'], $select_limits['files_limit']);
		// Список файлов
		$files_list .= $files_list_arr['list'];
		
		// Кол-во непросмотренных файлов
		$not_confirm_count += $files_list_arr['not_confirm_count'];
	}
	
	return array('list' => $files_list, 'pages_count' => $select_limits['pages_count'], 'not_confirm_count' => $not_confirm_count);
}
// Кол-во доступных папок пользователю 
function get_folders_count_available($user_id, $by_user_id)
{
	global $site_db, $current_user_id;
	
	// Получаем список доступных файлов
	$sql = "SELECT COUNT(*) as count FROM tasks_files_folders_access WHERE user_id='$user_id' AND by_user_id='$by_user_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Кол-во доступных папок пользователю 
function get_files_count_available($user_id, $by_user_id)
{
	global $site_db, $current_user_id;
	
	// Получаем список доступных файлов
	$sql = "SELECT COUNT(*) as count FROM tasks_files_access WHERE user_id='$user_id' AND by_user_id='$by_user_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Список доступных папок
function fill_folders_list_available($user_id, $by_user_id, $limit_from, $limit)
{
	global $site_db, $current_user_id, $user_obj;
	
	if(!$limit)
	{
		return '';
	}
	$limit_q = " LIMIT $limit_from, $limit";
	
	// Кол-во неподтвержденных папок (новые папки, которые пользователь еще не просмотрел)
	$list['not_confirm_count'] = 0;
	
	// Получаем список доступных файлов
	$sql = "SELECT i.*, j.noticed, j.by_user_id FROM tasks_files_folders i 
			RIGHT JOIN tasks_files_folders_access j ON i.folder_id=j.folder_id
			WHERE j.user_id='$user_id' AND by_user_id='$by_user_id' $limit_q ";
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{				 
		$list['list'] .= fill_folder_list_item($row, 'av');
		
		$list['not_confirm_count'] += !$row['noticed'] ? 1 : 0;
		
	}
	
	return $list;
}
 

// Список доступных файлов
function fill_files_list_available($user_id, $by_user_id, $limit_from, $limit)
{
	global $site_db, $current_user_id;
	
	if(!$limit)
	{
		return '';
	}
	$limit_q = " LIMIT $limit_from, $limit";
	
	// Кол-во неподтвержденных файлов (новые файлы, которые пользователь еще не просмотрел)
	$list['not_confirm_count'] = 0;
	
	// Получаем список доступных файлов
	$sql = "SELECT i.*, j.noticed, j.by_user_id FROM tasks_files i 
			RIGHT JOIN tasks_files_access j ON i.file_id=j.file_id
			WHERE j.user_id='$user_id' AND by_user_id='$by_user_id' $limit_q";
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$list['list'] .= fill_file_list_item($row, 'av');
		
		$list['not_confirm_count'] += !$row['noticed'] ? 1 : 0;
	}
	
	return $list;
}

// Список файлов Компании
function fill_files_list_block_company($p, $folder_id, $folders_count, $files_count)
{
	global $site_db, $current_user_id;
	
	
	$user_id = $current_user_id;
	
	// Формируем данные по кол-ву выводимых файлов и папок
	$select_limits = select_files_limits($p, $folders_count, $files_count, FILES_PER_PAGE);
	 
	if($p <= $select_limits['pages_folders_count'])
	{
		// Список папок
		$list .= fill_folders_list($user_id, $folder_id, $select_limits['folders_limit_from'], $select_limits['folders_limit'], 1);
	}
	// Список файлов
	if($select_limits['files_limit'])
	{
		$list .= fill_files_list($user_id, $folder_id, $select_limits['files_limit_from'], $select_limits['files_limit'], 1);
	}
	
	return $list;
}

// Список файлов ЛИЧНЫЕ
function fill_files_list_block($p, $user_id, $folder_id, $folders_count, $files_count)
{
	global $site_db, $current_user_id;
	
	$user_id = $current_user_id;
	
	// Формируем данные по кол-ву выводимых файлов и папок
	$select_limits = select_files_limits($p, $folders_count, $files_count, FILES_PER_PAGE);
	 
	if($p <= $select_limits['pages_folders_count'])
	{
		// Список папок
		$list .= fill_folders_list($user_id, $folder_id, $select_limits['folders_limit_from'], $select_limits['folders_limit']);
	}
	// Список файлов
	if($select_limits['files_limit'])
	{
		$list .= fill_files_list($user_id, $folder_id, $select_limits['files_limit_from'], $select_limits['files_limit']);
	}
	
	return $list;
}

// Возвращает массив данных по тому, как выводить страницы с папками и файлами
function select_files_limits($p, $folders_count, $files_count, $per_page)
{
	// Кол-во страниц
	$pages_folders_count = ceil($folders_count/$per_page);
	$pages_files_count = ceil($files_count/$per_page);
	
	$pages_count = ceil(($folders_count+$files_count)/$per_page);
	
	// Если присутствуют папки
	if($pages_folders_count)
	{
		// Если папки занимают все страницы полностью
		if($folders_count % $per_page==0)
		{
			$folders_in_pages = 'all';
			$page_where_need_files = $pages_folders_count + 1; // на какой странице нужны файлы
			$need_files_count = $per_page; // сколько файлов добавляем на странице
		}
		// Если папки занимают не всю страницу целиком и требуется добавить еще файлов для полной страницы
		else
		{
			$folders_in_pages = 'part';
			$page_where_need_files = $pages_folders_count; // на какой странице нужны файлы
			$need_files_count = $page_where_need_files * $per_page - $folders_count; // сколько файлов добавляем на странице
		}
	}
	
 	// echo $need_files_count;
	
	$folders_limit_from = $per_page * ($p-1); // с какой записи начинаем выводить папки
	$folders_limit = $per_page; // сколько выводим папок
		 
	//Вывод файлов совместно с папками
	if($page_where_need_files==$p)
	{ 
		$files_limit_from = 0; // с какой записи начинаем выводить файлы
		$files_limit = $need_files_count; // сколько выводим файлов
	}
	// Вывод файлов на странице уже без папок
	else if($p > $page_where_need_files)
	{  
		$files_limit_from =  $per_page * ($p-$page_where_need_files-1) + $need_files_count; // с какой записи начинаем выводить файлы
		$files_limit = $per_page; // сколько выводим файлов
	}
	
	return array('pages_count' => $pages_count, 'pages_folders_count'=>$pages_folders_count, 'pages_files_count' => $pages_files_count,'files_limit_from'=>$files_limit_from, 'files_limit'=>$files_limit, 'folders_limit_from'=>$folders_limit_from, 'folders_limit'=>$folders_limit, ); 
}

// Список папок
function fill_folders_list($user_id, $folder_id, $limit_from, $limit, $is_company=0)
{
	global $site_db, $current_user_id;
	
	if(!$limit)
	{
		return '';
	}
	$limit_q = " LIMIT $limit_from, $limit";
	
	// Список папок компании
	if($is_company)
	{
		// Список папок
		$sql = "SELECT * FROM tasks_files_folders WHERE is_company='$is_company' AND parent_folder_id='$folder_id' AND deleted=0 ORDER by folder_name ASC $limit_q";
	}
	else
	{
		if($folder_id)
		{
			// Список папок
			$sql = "SELECT * FROM tasks_files_folders WHERE parent_folder_id='$folder_id' AND is_company=0 AND deleted=0 ORDER by folder_name ASC $limit_q";
		}
		else
		{
			// Список папок
			$sql = "SELECT * FROM tasks_files_folders WHERE user_id='$user_id' AND parent_folder_id='$folder_id' AND is_company=0 AND deleted=0  ORDER by folder_name ASC $limit_q";
		}
		 
	}
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		 $list .= fill_folder_list_item($row);
	}
	
	return $list;
}

// Заполнение элемента списка - папки
function fill_folder_list_item($folder_data, $act='', $author)
{
	global $site_db, $current_user_id, $user_obj; 
	
	$files_list_item_folder_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/files_list_item_folder.tpl');
	
	$bind_confirm_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/bind_confirm_item.tpl');
	
	$file_access_icon_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_access_icon.tpl');
		
	// Доступ к папке	
	$_f_access =  get_file_access($current_user_id, 0, $folder_data['folder_id']);
	 
	// Выводим панель управления 
	$tools_list = fill_folder_tools_list($folder_data, $_f_access);
	
	// Заполянем объект пользователя
	//$user_obj->fill_user_data($folder_data['user_id']);
		
	//$user_name ='<span style="font-size:9px">'. $user_obj->get_user_surname().'</span>';
	
	if(!$_GET['navto'])
	{  
		$nav_to = $folder_data['folder_id'] ? '&navto='.$folder_data['folder_id'] : '';
	}
	else
	{
		$nav_to = '&navto='.$_GET['navto'];
	}
	
	// Если вывод папок, и папки еще не приняты
	if($act=='av' && !$folder_data['noticed'])
	{
		$notice_class = 'not_confirm_row';
		
		$PARS['{ACCESS_BY_USER_ID}'] = $folder_data['by_user_id'];
		$bind_act = fetch_tpl($PARS, $bind_confirm_item_tpl);
	}
	
	// Выводим иконку, информирует о наличие прав на элемент у других пользователей
	if($act!='av' && $folder_data['is_sharing'])
	{
		$PARS['{ELEM}'] = 'folder_'.$folder_data['folder_id'];
		$access_icon = fetch_tpl($PARS, $file_access_icon_tpl);
	}
	
	// Автор
	$author = get_formate_user_name($folder_data['user_id']); 
		
	$PARS['{FOLDER_ID}'] = $folder_data['folder_id'];
	
	$PARS['{NAME}'] = $folder_data['folder_name'];

	$PARS['{DATE_EDIT}'] = datetime($folder_data['date_add'], '%d.%m.%Y');
	
	$PARS['{TOOLS_LIST}'] = $tools_list;
	
	$PARS['{ACT}'] = $_GET['act'];
	
	$PARS['{NAV_TO_FOLDER_ID}'] = $nav_to;
	
	$PARS['{NOTICE_CLASS}'] = $notice_class;
	
	$PARS['{BIND_ACT}'] = $bind_act;
	
	$PARS['{ACCESS_ICON}'] = $access_icon;
	
	$PARS['{AUTHOR}'] = $author;
	
	 
	
	return fetch_tpl($PARS, $files_list_item_folder_tpl);
}

// Список файлов
function fill_files_list($user_id, $folder_id, $limit_from, $limit, $is_company=0)
{
	global $site_db, $current_user_id;
	
	if(!$limit)
	{
		return '';
	}
	
	$limit_q = " LIMIT $limit_from, $limit";
	
	if($is_company)
	{
		// Получаем список файлов
		$sql = "SELECT * FROM tasks_files WHERE is_company='$is_company' AND folder_id='$folder_id' AND deleted=0 AND is_content_file=0 ORDER by file_name ASC $limit_q";
	}
	else
	{
		if($folder_id)
		{
			// Получаем список файлов
			$sql = "SELECT * FROM tasks_files WHERE folder_id='$folder_id' AND is_company=0 AND deleted=0 AND is_content_file=0 ORDER by file_name ASC $limit_q";
		}
		else
		{
			// Получаем список файлов
			$sql = "SELECT * FROM tasks_files WHERE user_id='$user_id' AND folder_id='$folder_id' AND is_company=0 AND deleted=0 AND is_content_file=0 ORDER by file_name ASC $limit_q";
		}
		 
	}
	 
	
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$list .= fill_file_list_item($row);
	}
	
	return $list;
}

// Заполнение элемента списка - файла
function fill_file_list_item($file_data, $act='', $tpl_mode=0)
{
	global $site_db, $current_user_id, $user_obj;
	
	$files_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/files_list_item_file.tpl');
	
	// Шаблоны для вывода файлов прикрепленных к контенту
	$files_list_item_file_1_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/files_list_item_file_1.tpl');
	$files_list_item_file_2_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/files_list_item_file_2.tpl');
	
	
	$bind_confirm_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/bind_confirm_item.tpl');
	
	$file_access_icon_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_access_icon.tpl');
	
	$file_pub_icon_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_pub_icon.tpl');
	
	// Шаблон для файла
	if($tpl_mode==1)
	{
		$file_item_tpl = $files_list_item_file_1_tpl;
		$doc_not_edit = 1;
	}
	// Шаблон для файла
	else if($tpl_mode==2)
	{
		$file_item_tpl = $files_list_item_file_2_tpl;
		$doc_not_edit = 1;
	}
	else
	{
		$file_item_tpl = $files_list_item_tpl;
		$doc_not_edit = 0;
	}
	
	
	// Доступ к папке	
	$_f_access =  get_file_access($current_user_id, $file_data['file_id'],0);
	
	$tools_list = fill_file_tools_list($file_data, $_f_access);
	
	
	if(!$_GET['navto'])
	{  
		$nav_to = $folder_data['folder_id'] ? '&navto='.$folder_data['folder_id'] : '';
	}
	else
	{
		$nav_to = '&navto='.$_GET['navto'];
	}
	
	// Если вывод файлов, и файлы еще не приняты
	if($act=='av' && !$file_data['noticed'])
	{
		$notice_class = 'not_confirm_row';
		$PARS['{ACCESS_BY_USER_ID}'] = $file_data['by_user_id'];
		$bind_act = fetch_tpl($PARS, $bind_confirm_item_tpl);
	}
	
	
	// Формируем дейтвие по клику на имя файла	
	$file_link = get_file_list_open_action($file_data);
	
	
	// Выводим иконку, информирует о наличие прав на элемент у других пользователей
	if($act!='av' && $file_data['is_sharing'])
	{
		$PARS['{ELEM}'] = 'file_'.$file_data['file_id'];
		$access_icon = fetch_tpl($PARS, $file_access_icon_tpl);
	}
	
	if($file_data['is_pub'])
	{
		$PARS['{FILE_ID}'] = $file_data['file_id'];
		$file_pub_icon = fetch_tpl($PARS, $file_pub_icon_tpl);	
	}
	 
	
	// Автор
	$author = get_formate_user_name($file_data['user_id']); 
		
	$PARS['{FILE_ID}'] = $file_data['file_id'];
	
	$PARS['{SIZE}'] = formate_filesize($file_data['size']);
	
	$PARS['{DATE_EDIT}'] = datetime($file_data['date_edit'], '%d.%m.%Y');
	
	$PARS['{ACT}'] = $_GET['act'];
	
	$PARS['{TOOLS_LIST}'] = $tools_list;
	
	$PARS['{NAV_TO_FOLDER_ID}'] = $nav_to;
	
	$PARS['{NOTICE_CLASS}'] = $notice_class;
	
	$PARS['{BIND_ACT}'] = $bind_act;
	
	$PARS['{FILE_LINK}'] = $file_link;
	
	$PARS['{SHARA_ICON}'] = $shara_icon;
	
	$PARS['{AUTHOR}'] = $author;
	
	$PARS['{PUB_ICON}'] = $file_pub_icon;
	
	$PARS['{ACCESS_ICON}'] = $access_icon;
	
	return fetch_tpl($PARS, $file_item_tpl);
}


// Выводит контент в попапе для просмотра файла
function get_file_popup_content($id, $content_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$file_popup_image_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_popup_image.tpl');
	
	$file_popup_txt_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_popup_txt.tpl');
	
	$file_popup_doc_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_popup_doc.tpl');
	
	$fl = new File($site_db);
	
	// Доступ к папке	
	$_f_access =  get_file_access($current_user_id, $id, 0);
	 
	// У пользователя есть доступ на совершение операции Изменения файла
	if(!f_check_access('read', $_f_access) && !f_check_access('edit', $_f_access))
	{
		//return '';
	}
	 
	// Данные файла
	$file_data = $fl->get_file_data($id);
	
	// Тип файла
	$file_type = get_file_type($file_data);
	
	// ссылка на скачивание
	$download_link = get_file_download_link($id, 'file');  
	
	if($content_id)
	{
		$download_link .= '&cont_id='.$content_id;	
	}
			
	switch($file_type)
	{
		case 'image':
			 
			 
			$PARS['{DOWNLOAD_LINK}'] = $download_link;
			$PARS['{FILE_ID}'] = $id;
			$PARS['{FILE_NAME}'] = $file_data['file_name'];
			return fetch_tpl($PARS, $file_popup_image_tpl);
			
		break;
		
		case 'txt':
			
			// Создаем публичную ссылку на файл
			$pub_id = save_file_pub($id, 5, 3, '', 1);
			 
			// Получаем ссылку на файл
			$url = urlencode(get_file_pub_url('', $pub_id, 1));
			 
			$file_name = iconv('cp1251', 'utf-8', $file_data['file_name']);
			$PARS['{DOWNLOAD_LINK}'] = $download_link;
			$PARS['{URL}'] = $url;
			$PARS['{FILE_NAME}'] = $file_data['file_name'];
			return fetch_tpl($PARS, $file_popup_txt_tpl);
			
		break;
		case 'ms-word':
		case 'ms-excel':
		case 'ms-powerpoint':
		 
			// Создаем публичную ссылку на файл
			$pub_id = save_file_pub($id, 5, 3, '', 1);
			 
			// Получаем ссылку на файл
			$url = urlencode(get_file_pub_url('', $pub_id, 1));
			
			$file_name = iconv('cp1251', 'utf-8', $file_data['file_name']);
			$PARS['{DOWNLOAD_LINK}'] = $download_link;
			$PARS['{FILE_ID}'] = $id;
			$PARS['{URL}'] = $url;
			$PARS['{FILE_NAME}'] = $file_data['file_name'];
			
			// У пользователя нет доступа на операцию изменение
			if(!f_check_access('edit', $_f_access) || $file_data['is_content_file'])
			{
				return fetch_tpl($PARS, $file_popup_txt_tpl);
			}
			else 
			{
				return fetch_tpl($PARS, $file_popup_doc_tpl);
			}
			 
		break;
	}
	
}

//Данные по публичной ссылке на файл
function get_pub_file_link_data($file_name_id)
{
	global $site_db, $current_user_id;
	
	// Поиск публичной ссылки на файл
	$sql = "SELECT * FROM tasks_files_pub WHERE file_name_id='$file_name_id'";
	
	$pub_file_data = $site_db->query_firstrow($sql);
	
	if(!$pub_file_data['id'])
	{
		$access = 0;
	}
	
	$actual_time = time();
	
	// Если время доступа еще не просрочено или время ограничения доступа не указано, разрешаем доступ к файлу
	if($pub_file_data['id'] && ($actual_time<$pub_file_data['date_to'] || !$pub_file_data['date_to']))
	{
		$access = 1;	
	}
	else $access = 0;
	
	$pub_file_data['access'] = $access;
	
	return $pub_file_data;
	
	
}

// Удалить публичную ссылку на файл
function delete_file_pub($pub_id)
{
	global $site_db, $current_user_id;
	
	$sql = "DELETE FROM tasks_files_pub WHERE id='$pub_id'";
	 
	$site_db->query($sql);
}

// Показ публичного документа
function fill_pub_document()
{
	global $site_db;
	
	$pub_file_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/pub_file.tpl');
	
	$pub_file_access_denied_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/pub_file_access_denied.tpl');
	
	// Данные публичной ссылки
	$pub_data = get_pub_file_link_data($_GET['id']);
	
	$fl = new File($site_db);
		
	$file_data = $fl->get_file_data($pub_data['file_id']);
	 
	if($file_data['deleted'])
	{
		return $pub_file_access_denied_tpl;
	}
	 // echo print_r($pub_data);
	// Скачать
	if($_GET['download']==1)
	{
		if($pub_data['id'] && $pub_data['access'])
		{
			// Если ссылка была для системных нужд, удаляем ее после первого обращения к файлу
			if($pub_data['is_system'] || $pub_data['access'])
			{
				// Удаляем ссылку
				//delete_file_pub($pub_data['id']);
				
				$force_pre_download_check = 1;
			}
			// Даем скачать файл
			fill_disk_download($pub_data['file_id'], 'file',0, $force_pre_download_check);
		}
		else
		{
			return $pub_file_access_denied_tpl;
		}
	}
	else if(!$pub_data['id'] || !$pub_data['access'])
	{  
		return $pub_file_access_denied_tpl;
	}
	else if($pub_data['id'] && $pub_data['access'])
	{ 		
		$PARS['{FILE_NAME}'] = $file_data['file_name'];
		
		$PARS['{DESC}'] = nl2br($pub_data['desc']);
		
		$PARS['{DOWNLOAD_LINK}'] = get_file_pub_url('', $pub_data['id'], 1);
		
		$PARS['{SIZE}'] = formate_filesize($file_data['size']);
		
		return fetch_tpl($PARS, $pub_file_tpl);
	}
	//echo $pub_data['access'];
	//print_r($pub_data);
	 
	 
	  
}

// Возвращает метод открытия файла по щелчку на его имя в списке файлов
function get_file_list_open_action($file_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$file_link_download_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_link_download.tpl');
	
	$file_link_image_preview_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_link_image_preview.tpl');
	
	$file_link_txt_preview_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_link_txt_preview.tpl');
	
	$file_id = $file_data['file_id'];
	$extension = $file_data['extension'];
	$file_name = $file_data['file_name'];
	
	// Тип файла
	$file_type = get_file_type($file_data);
	
	
	switch($file_type)
	{
		// Просмотр изображения
		case 'image':
		
			$link_tpl = $file_link_image_preview_tpl;
			
		break;
		
		// Просмотр в гугл
		case 'txt':
		case 'ms-word':
		case 'ms-excel':
		case 'ms-powerpoint':
			
			if(USE_G_SERVICES)
				$link_tpl = $file_link_txt_preview_tpl;
			else
				$link_tpl = $file_link_download_tpl;
		break;
		
		// Скачивание файла сразу
		default:
			
			$link_tpl = $file_link_download_tpl;
			
		break;
	}
	
	 
	// ссылка на скачивание
	$download_link = get_file_download_link($file_data['file_id'], 'file'); 
	
	if($file_data['is_content_file'])
	{
		$cont_id = $file_data['content_id'];
		$download_link .= '&cont_id='.$cont_id;
	}
	
	$PARS['{FILE_ID}'] = $file_id;
	
	$PARS['{FILE_NAME}'] = $file_data['file_name'];
	
	$PARS['{FILE_NAME_CUT}'] = strlen($file_data['file_name']) > 40 ? substr($file_data['file_name'],0,40).'...' : $file_data['file_name'];
	
	$PARS['{DOWNLOAD_LINK}'] = $download_link;
	
	$PARS['{CONT_ID}'] = $cont_id;
	
	return fetch_tpl($PARS, $link_tpl);
}

// Проверка типа файла на возможность просмотра через сервис гугла
function is_file_type_open_google($type)
{
	$view_arr = array('txt', 'ms-powerpoint', 'ms-excel', 'ms-word');
	
	if(in_array($type, $view_arr))
	{
		return true;
	}
}

// Получает тип файла, по его расширению
function get_file_type($file_data)
{
	$images_arr =  array('jpg', 'jpeg', 'png', 'gif');
	
	$txt_arr =  array('txt');
	
	$ms_word_doc_arr =  array('docx', 'doc');
	
	$ms_excel_doc_arr =  array('xls', 'xlsx');
	
	$ms_powerpoint_doc_arr =  array('pptx', 'ppt');
	
	// Если файл изображение
	if(in_array(strtolower($file_data['extension']), $images_arr))
	{
		return 'image';
	}
	else if(in_array(strtolower($file_data['extension']), $txt_arr))
	{
		return 'txt';
	}
	else if(in_array(strtolower($file_data['extension']), $ms_word_doc_arr))
	{
		return 'ms-word';
	}
	else if(in_array(strtolower($file_data['extension']), $ms_excel_doc_arr))
	{
		return 'ms-excel';
	}
	else if(in_array(strtolower($file_data['extension']), $ms_powerpoint_doc_arr))
	{
		return 'ms-powerpoint';
	}
}


// Скачивание файла
function fill_disk_download($id, $act, $force_download, $force_pre_download_check=0, $cont_id)
{
	global $site_db, $current_user_id;
	 
	
	 
	$fl = new File($site_db);
	 
	switch($act)
	{
		case 'file':
			
			$file_id = $id;
			
			// Данные файла
			$file_data = $fl->get_file_data($file_id);
			 
			// Данные актуальной версии файла
			$file_version_data = $fl->get_file_version_data($file_data['version_id']);
			
			$version_id = $id;
			
			$file_name = $file_data['file_name'];
		
		break;
		
		case 'fv':
			
			$version_id = $id;
		
			// Данные актуальной версии файла
			$file_version_data = $fl->get_file_version_data($version_id);
		 
			// id файла
			$file_id = $file_version_data['file_id'];
			
			// Данные файла
			$file_data = $fl->get_file_data($file_id);
			
			$file_name = $file_version_data['file_name'];
		
		break;
		
		default:
			 
			return fill_404();
			
		break;
	}

	// Если файл не найден
	if(!$file_id)
	{
		return fill_404();
	}
	
	// Доступ к папке	
	$_f_access =  get_file_access($current_user_id, $file_id, 0);
	  
	// Если не установлен флаг, о необходимости проверки на доступ к файлу, то делаем проверки
	if(!$force_pre_download_check) 
	{
		// Если файл прикреплен к контенту
		if($file_data['is_content_file'])
		{
			if(!check_access_to_content_files($file_data, $cont_id))
			{
				return file_error('no_access_file_to_download', 'file', $file_id);
			}
		}
		// Если файл загружен на диск
		else if(!$file_data['is_content_file'])
		{
			 if(!f_check_access('read', $_f_access) && !f_check_access('edit', $_f_access))
			 {
				 return file_error('no_access_file_to_download', 'file', $file_id);
			 }
		}
	}
	 
	
	 
	 
	
	$upl = new Upload($site_db);
	
	 
	
	// Путь хранения файла
	//$file_dir = $upl->get_file_dir($file_id);
	
	$file_dir = get_download_dir('', $file_version_data['date_add']);
	
	$download_file_path = $file_dir.'/'.$file_version_data['file_system_name'];
	
	// Если файл не найден
	if (!file_exists($download_file_path)) 
	{
		return fill_404();
		 
	}
	 
	//echo $download_file_path,' ';
	
	// Скачивание файла
	start_file_download($download_file_path, '', $file_name, $force_download);
	
	//echo $download_file_path;

}

// Сохранение файла
function start_file_download($filename, $mimetype='application/octet-stream', $file_name_for_out='', $force_download = 0) 
{
	 
	if (!file_exists($filename)) 
	{
	   return false;
	}
	
	if (ob_get_level()) 
	{
		 ob_end_clean();
	}
	
	 
	$file_name_for_out = $file_name_for_out ? $file_name_for_out :  basename($filename); 
	
	// Не загружать, а просто вывести файл
	if(!-1)
	{
		header('Accept-Ranges:	bytes');
		header('Connection:	Keep-Alive');
		header('Content-Length: ' . filesize($filename));
		//header('Content-Type:	image/jpeg');
		header('Content-Type: application/octet-stream');
		header('Connection:	Keep-Alive');
		
		header('Content-Disposition:	attachment; filename="'.$file_name_for_out.'"');
		
	}
	else
	{
		header('Content-Type: application/octet-stream'); 
		header('Content-Disposition: attachment; filename="' . ($file_name_for_out).'" ');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filename));
		 
	}
	  
	/*Access-Control-Allow-Head...	origin, content-type, accept
Access-Control-Allow-Orig...	*
Cache-Control	no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Connection	keep-alive
Content-Disposition	attachment; filename="google1.txt"
Content-Length	13892
Content-Range	0-13891/13892
Content-Type	text/plain
Date	Fri, 21 Nov 2014 21:17:29 GMT
Etag	"f7be0af900adf36a07c6af3d8f154387"
Expires	Thu, 19 Nov 1981 08:52:00 GMT
Last-Modified	Fri, 21 Nov 2014 21:17:29 GMT
P3P	policyref="/bitrix/p3p.xml", CP="NON DSP COR CUR ADM DEV PSA PSD OUR UNR BUS UNI COM NAV INT DEM STA"
Pragma	no-cache
Server	nginx/1.0.15
Strict-Transport-Security	max-age=31536000; includeSubdomains
X-Frame-Options	SAMEORIGIN
X-Powered-By	PHP/5.4.32
X-Powered-CMS	Bitrix Site Manager (1682f9867b9ef36eacf05e345db46f3c)
x-content-type-options	nosniff*/

	readfile($filename);
	
    exit;
	
   
 
}


// Список инструментов для файлов 
function fill_file_tools_list($file_data, $_f_access)
{
	$tool_file_open_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_file_open.tpl');
	$tool_file_rename_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_file_rename.tpl');
	$tool_file_security_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_file_security.tpl');
	$tool_file_property_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_file_property.tpl');
	$tool_file_versions_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_file_versions.tpl');
	$tool_file_access_list_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_file_access_list.tpl');
	$tool_file_download_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_file_download.tpl');
	$tool_file_delete_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_file_delete.tpl');
	$tool_file_pub_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_file_pub.tpl');
	 
	// Переименовать
	if(f_check_access('edit', $_f_access))
	{
		$tool_rename = $tool_file_rename_tpl;
		$tool_file_delete = $tool_file_delete_tpl;
	}
	
	// Доступ
	if(f_check_access('edit', $_f_access) && !$file_data['is_company'])
	{
		$tool_security = $tool_file_security_tpl;
		$tool_file_access_list = $tool_file_access_list_tpl;
	}
	
	
	$tools_list = $tool_file_open_tpl.
				$tool_file_download_tpl.
				$tool_rename.
				$tool_security.
				$tool_file_pub_tpl.
				$tool_file_property_tpl.
				$tool_file_versions_tpl.
				$tool_file_access_list.
				$tool_file_delete;
	
	// ссылка на скачивание
	$download_link = get_file_download_link($file_data['file_id'], 'file');
	
	$PARS['{FILE_ID}'] = $file_data['file_id'];
	$PARS['{DOWNLOAD_LINK}'] = $download_link;
	
	 
	// Параметры 
	$PARS['{PARS}'] = file_get_href_parameters_part(1);
	
	return fetch_tpl($PARS, $tools_list);
	 
}

function get_file_download_link($file_id, $what='')
{
	switch($what)
	{
		case 'file':
			return '/disk/download/'.$file_id.'?wh=file';
		break;
		
		case 'file_version':
			return '/disk/download/'.$file_id.'?wh=fv';
		break;
	}
	 
}

// Список инструментов для папок
function fill_folder_tools_list($folder_data, $folder_access)
{
	$tool_folder_open_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_folder_open.tpl');
	$tool_folder_rename_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_folder_rename.tpl');
	$tool_folder_security_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_folder_security.tpl');
	$tool_folder_delete_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/tool_folder_delete.tpl');
	 
	// Переименовать
	if(f_check_access('edit', $folder_access))
	{
		$tool_folder_rename = $tool_folder_rename_tpl;
		$tool_folder_delete = $tool_folder_delete_tpl;
	}
	
	// Доступ
	if(f_check_access('edit', $folder_access) && !$folder_data['is_company'])
	{
		$tool_folder_security = $tool_folder_security_tpl;
	}
	 
	
	$tools_list = $tool_folder_open_tpl.$tool_folder_rename.$tool_folder_security.$tool_folder_delete;
	
	$PARS['{FOLDER_ID}'] = $folder_data['folder_id'];
	
	return fetch_tpl($PARS, $tools_list);
}

// Кол-во папок
function get_folders_company_count($folder_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM tasks_files_folders WHERE is_company='1' AND parent_folder_id='$folder_id' AND deleted=0 ";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}
// Кол-во файлов в папке
function get_files_company_count($folder_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM tasks_files WHERE is_company='1' AND folder_id='$folder_id' AND deleted=0 AND is_content_file=0";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Кол-во папок
function get_folders_count($user_id, $folder_id)
{
	global $site_db, $current_user_id;
	
	// Если просмотр папки
	if($folder_id)
	{
		$sql = "SELECT COUNT(*) as count FROM tasks_files_folders WHERE parent_folder_id='$folder_id' AND is_company=0 AND deleted=0 ";
	}
	else
	{
		$sql = "SELECT COUNT(*) as count FROM tasks_files_folders WHERE user_id='$user_id' AND parent_folder_id='$folder_id' AND is_company=0 AND deleted=0 ";
	}
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Кол-во файлов в папке
function get_files_count($user_id, $folder_id)
{
	global $site_db, $current_user_id;
	
	// Если просмотр папки
	if($folder_id)
	{
		$sql = "SELECT COUNT(*) as count FROM tasks_files WHERE folder_id='$folder_id'  AND is_company=0 AND deleted=0 AND is_content_file=0";
	}
	else
	{
		$sql = "SELECT COUNT(*) as count FROM tasks_files WHERE user_id='$user_id' AND folder_id='$folder_id'  AND is_company=0 AND deleted=0 AND is_content_file=0";
	}
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}


// Форма добавления файла и создания папки
function fill_upload_add_form($folder_id, $upload_version_file=0)
{
	global $site_db, $current_user_id;
	
	$add_form_tpl = file_get_contents('templates/disk/add_form.tpl');
	
	$file_add_version_form_tpl = file_get_contents('templates/disk/file_add_version_form.tpl');
	
	$timestamp = time();
	
	$token = create_token($timestamp);
	
	 
	$PARS['{UPLOAD_SIZE_LIMIT}'] = UPLOAD_SIZE_LIMIT;
	
	$PARS['{TOKEN}'] = $token;
	
	$PARS['{TIMESTAMP}'] = $timestamp;
	
	$PARS['{FOLDER_ID}'] = $folder_id;
	
	$PARS['{UPLOAD_VERSION_FILE}'] = $upload_version_file;
	
	$PARS['{ACT}'] = $_GET['act'];
	
	// Если требуется форма для добавления версии к файлу
	if($upload_version_file)
	{
		return fetch_tpl($PARS, $file_add_version_form_tpl);
	}
	else
	{
		return fetch_tpl($PARS, $add_form_tpl);
	}
}

// Инициализация клиента гугл
function init_google_client()
{
	global $google_client_id, $google_secret, $google_redirect_uri;
	
	require_once $_SERVER['DOCUMENT_ROOT'].'/classes/google/src/Google/Client.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/classes/google/src/Google/Service/Drive.php';
	
	$client = new Google_Client();
	$client->setClientId($google_client_id);
	$client->setClientSecret($google_secret);
	$client->setRedirectUri($google_redirect_uri);
	$client->setScopes(array('https://www.googleapis.com/auth/drive'));
	 
	 
	if($_SESSION['upload_token'])
	{
		try {
			$client->setAccessToken($_SESSION['upload_token']);
		}
		catch (Exception $e) 
		{
			$non_token = 1;
			unset($_SESSION['upload_token']);
		}
		
		if ($client->isAccessTokenExpired()) 
		{
			$non_token = 1;
			unset($_SESSION['upload_token']);
				
		 
		}
	}
	
	return $client;
}
// Редактирование документа
function disk_doc_edit($file_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	// При первом обращении к скрипту, выводим страницу ожидания загрузки
	if($_GET['first']==1)
	{
		$window_token = generate_rand_string(20);
		
		// Добавляем ключи проверки, что пользователь открывал окно редактирования
		if($_SESSION['window_doc_token'])
		{
			$window_ses = unserialize($_SESSION['window_doc_token']);
		}
		$window_ses[$file_id] = $window_token;
		// Добавляем в сессию токен окна для редактирования документа
		$_SESSION['window_doc_token'] = serialize($window_ses);
		
		 
		$PARS['{TIME}'] = $_GET['time'];
		$PARS['{ID}'] = $_GET['id'];
		$PARS['{T}'] = $window_token;
		 
		echo fetch_tpl($PARS, file_get_contents('templates/disk/file_upload_to_gdrive.tpl'));
		 
	}
	
	 
	// Проверка, что окно открывал пользователь
	$window_ses = unserialize($_SESSION['window_doc_token']);
	$window_token = $window_ses[$file_id];
	
	if(!$_GET['t'] || $window_token!=$_GET['t'])
	{
		return file_error('no_access_file_to_edit', 'file', $file_id);
	}
	
	$fl = new File($site_db);
	$upl = new Upload($site_db);
	 
	// Данные файла
	$file_data = $fl->get_file_data($file_id);
	
	// Данные актуальной версии файла
	$file_version_data = $fl->get_file_version_data($file_data['version_id']);
			 
	// Доступ к папке	
	$_f_access =  get_file_access($current_user_id, $file_id, 0);
	 //return fetch_tpl($PARS, file_get_contents('templates/disk/doc_edit.tpl'));
	// У пользователя есть доступ на совершение операции Изменения файла
	if(!f_check_access('edit', $_f_access))
	{
		return file_error('no_access_file_to_edit', 'file', $file_id);
	}
	
	// Инициализация клиента гугл
	$google_client =  init_google_client();
	 
	 
	if(!$_SESSION['upload_token'])
	{ 
		if(!$_GET['gdauthed'])
		{
			disk_gdrive_to_auth($file_id, $_GET['time'], $window_token);
		}
		 
		return 'Во время загрузки документа произошла ошибка.'; 
	}
	else
	{ 
		
		// Проверяем, существует ли документ на сервере гугл. Проверка выполняется из внутренний таблицы, где есть идентификатор документа, если он редактировался ранее 
		$gdrive_row = is_file_in_googled($file_data['file_id']);
		 
		if($gdrive_row['google_file_id'])
		{
			// Проверяем, есть ли в таблице запись о том, что этот документ редактирует текущий пользователь
			$sql = "SELECT * FROM tasks_files_gdrive WHERE user_id='$current_user_id' AND file_id='".$file_data['file_id']."' AND google_file_id='".$gdrive_row['google_file_id']."'";
			
			$g_row = $site_db->query_firstrow($sql);
			
			// Если такой отметки нет, то добавляем ее
			if(!$g_row['id'])
			{
				// Вставляем в таблицу запись о том, что данный документ редактируется пользователем
				$sql = "INSERT INTO tasks_files_gdrive SET file_id='".$file_data['file_id']."', user_id='$current_user_id', google_file_id='".$gdrive_row['google_file_id']."'";
				$site_db->query($sql);
			}
	
			// Ссылка на документ
			$redirect_uri = gdrive_edit_app_by_extension($file_data['extension'], $gdrive_row['google_file_id']);
			
			 
			
			//header('Location: '.$redirect_uri);
		}
		else
		{ 
			// Загружаем документ на сервер гугл диск 
			$redirect_uri = doc_to_gdrive($file_data, $file_version_data); 
			
			//$file_upload_to_gdrive_tpl = file_get_contents('templates/disk/file_upload_to_gdrive.tpl');
			
			//echo $file_upload_to_gdrive_tpl;
			//header('Location: '.$redirect_uri);
		}
		
		$PARS['{FILE_ID}'] = $file_id;
		$PARS['{URL}'] = $redirect_uri;
		return fetch_tpl($PARS, file_get_contents('templates/disk/doc_edit.tpl'));
	}
	
	 
}

function is_file_in_googled($file_id)
{
	global $site_db, $current_user_id;
	
	// проверяем, в процессе ли редактирование документа
	$sql = "SELECT google_file_id FROM  tasks_files_gdrive WHERE file_id='".$file_id."' ORDER by id DESC";
		
	$gdrive_row = $site_db->query_firstrow($sql);
	
	return $gdrive_row;
}

// Формируем линк для авторизации на сервере
function disk_gdrive_to_auth($file_id, $time, $window_token)
{ 
	global $_SERVER_ID;
	
	//$file_info_tpl = file_get_contents('templates/disk/file_.tpl');
	
	// Инициализация клиента гугл
	$google_client =  init_google_client();
	
	$google_client->setState($file_id.'-'.$time.'-'.$window_token.'-'.$_SERVER_ID);
	$google_client->setAccessType('offline');
	$authUrl = $google_client->createAuthUrl();
	
	//$f = file_get_contents($authUrl);
	header('Location: '.$authUrl);
}
// Получаем ключ авторизации от сервиса гугл
function disk_gdrive_auth()
{  
	// Инициализация клиента гугл
	$google_client =  init_google_client();
	
	try 
	{
		$google_client->authenticate($_GET['code']);
		$accessToken = $google_client->getAccessToken();
		$_SESSION['upload_token'] = $accessToken;
	}
	catch (Exception $e) 
	{
		return 'Требуется повторная авторизация. Закройте это окно и откройте новое.';
		//header('Location: '.$authUrl);
	}
	 
	
	 
	$state = split('-', $_GET['state']) ;
	
	$file_id = $state[0];
	$time = $state[1];
	$window_token = $state[2];
	
	// Переводим на страницу редактирвоания документа
	header('Location: /disk/doc/edit/'.$file_id.'?first=1&t='.$window_token.'&time='.$time.'&gdauthed=1');
	 
	exit();
}

// Закачивает документ на сервер google drive
function doc_to_gdrive($file_data, $file_version_data)
{
	global $site_db, $current_user_id;
	
	$fl = new File($site_db);
	$upl = new Upload($site_db);
	 
	// Инициализация клиента гугл
	$google_client =  init_google_client();
	
	// Инициализация Сервис Гугл Диск
	$service = new Google_Service_Drive($google_client);
	
	$file = new Google_Service_Drive_DriveFile();
	$file->setTitle(substr($file_data['file_name'],0, strrpos($file_data['file_name'], '.')));
	$file->setDescription($file_data['file_name']);
	//$file->setMimeType('application/vnd.google-apps.document');
	
	// Путь хранения файла
	//$file_dir = $upl->get_file_dir($file_data['file_id']);
	
	$file_dir = get_download_dir('', $file_version_data['date_add']);
	
	
	$file_path = $file_dir.'/'.$file_version_data['file_system_name'];

	$data = file_get_contents($file_path);
	
	// mime type
	$mime_type = get_mime_type_by_extension($file_data['extension']);
	
	$createdFile = $service->files->insert(
		$file,
		array(
		  'data' => $data,
		  'mimeType' => $mime_type,
		  'uploadType' => 'multipart',
		  'convert' => true
		)
	);
	
	$google_file_id = $createdFile['id'];
	
	$newPermission = new Google_Service_Drive_Permission();
	$newPermission->setWithLink(true);
	$newPermission->setType('anyone');
	$newPermission->setRole('writer');
	try 
	{
		$service->permissions->insert($google_file_id, $newPermission);
	} 
	catch (Exception $e) 
	{
		
	}
	
	$time = $_GET['time'];
	
	// Добавляем отметку в базу о том, что пользователь начал редактировать документ
	$sql = "INSERT INTO tasks_files_gdrive SET file_id='".$file_data['file_id']."', user_id='$current_user_id', google_file_id='$google_file_id', `time`='$time'";
  	$site_db->query($sql);
	
	  // echo "<pre>", print_r($createdFile);
	 
	//unset($_SESSION['upload_token']);	 
	
	// Возвращаем ссылки на редактирвоание документа
	
	return gdrive_edit_app_by_extension($file_data['extension'], $google_file_id); 
	 
	
}

function gdrive_edit_app_by_extension($extension, $google_file_id)
{
	switch(get_mime_type_by_extension($extension))
	{
		case 'application/msword':
			return 'https://docs.google.com/document/d/'.$google_file_id.'/edit';
		break;
		case 'application/vnd.ms-excel':
			return 'https://docs.google.com/spreadsheets/d/'.$google_file_id.'/edit';
		break;
		case 'application/vnd.ms-powerpoint':
			return 'https://docs.google.com/presentation/d/'.$google_file_id.'/edit';
		break;
	}
}


// Сохраняет версию документа, который редактировали через гугл
function update_doc_version($file_id, $time)
{
	global $site_db, $current_user_id;
	
	$fl = new File($site_db);
	$upl = new Upload($site_db);
	
	// Данные файла
	$file_data = $fl->get_file_data($file_id);
	
	// Гугл данные по файлу, которые редактируем 
	$gdrive_row = get_doc_gdrive_file_data($file_id, $current_user_id, $time);
	 
	if($gdrive_row['id'])
	{
		$google_file_id = $gdrive_row['google_file_id'];
	}
	else
	{
		return -11;
	}
	
	//unset($_SESSION['upload_token']);	 
	switch(get_mime_type_by_extension($file_data['extension']))
	{
		case 'application/msword':
			// Файл скачивания
			$file_export = 'https://docs.google.com/feeds/download/documents/export/Export?id='.$google_file_id.'&exportFormat='.$file_data['extension'];
		break;
		case 'application/vnd.ms-excel':
			// Файл скачивания
			$file_export = 'https://docs.google.com/spreadsheets/export?id='.$google_file_id.'&exportFormat='.$file_data['extension'];
		break;
		case 'application/vnd.ms-powerpoint':
			// Файл скачивания
			$file_export = 'https://docs.google.com/feeds/download/presentations/Export?id='.$google_file_id.'&exportFormat='.$file_data['extension'];
		break;
	}
	 
	
	//echo $file_export;
	 
	
	 
	// Путь хранения файла
	//$file_dir = $upl->get_file_dir($file_id);
	
	$date_add = date('Y-m-d H:i:s');
	
	$file_system_name = get_rand_file_system_name($file_data['file_name']);
	
	$file_dir = create_upload_folder($date_add);
	$file_path = $file_dir.'/'.$file_system_name;
		
	 
		
	//$file_system_name = $file_id.'_'.date('ymdHis').'.'.$file_data['extension'];
	
	//$file_path = $file_dir.'/'.$file_system_name;
	
	// Скачиваем документ 
	if(copy($file_export, $file_path))
	{
		// Перевод в байты
		$filesize_byte = filesize($file_path);
		
		$extension = $file_data['extension'];
		
		// Добавляем версию файла		
		$sql = "INSERT INTO tasks_files_versions SET file_id='$file_id', file_name='".$file_data['file_name']."', file_system_name='$file_system_name', date_add=NOW(), user_id='".$current_user_id."', extension='$extension', size='$filesize_byte'";
		
		$site_db->query($sql);
		
		// Новая версия файла
		$version_id = $site_db->get_insert_id();
			
		// Обновляем версию файла в таблице файла
		$sql = "UPDATE tasks_files SET version_id='$version_id', size='$filesize_byte', date_edit=NOW() WHERE file_id='$file_id'";
				
		$site_db->query($sql);
	
		// Удаляем гугл документ на диске
		delete_doc_from_gdrive($google_file_id, $file_id, $current_user_id, $time);
		
		return 1;
	}
	else return -1;
	
}

// Возвращает Гугл данные по файлу
function get_doc_gdrive_file_data($file_id, $user_id, $time)
{
	global $site_db;
	
	// Данные по файлу, который редактируется через гугл докс
	$sql = "SELECT * FROM tasks_files_gdrive WHERE file_id='$file_id' ORDER by id DESC LIMIT 1";
	 
	$gdrive_row = $site_db->query_firstrow($sql);
	
	return $gdrive_row;
}

// Удалить версию файла на гугл диске
function delete_doc_from_gdrive($google_file_id, $file_id, $user_id, $time)
{
	global $site_db, $current_user_id;
	
	// Если id гугл документа не передан
	if($google_file_id==0)
	{  
	    // Гугл данные по файлу, которые редактируем 
		$gdrive_row = get_doc_gdrive_file_data($file_id, $user_id, $time);
		$google_file_id = $gdrive_row['google_file_id'];
	}
	
	// Удаляем флаг из таблицы о редактирование документа пользователем
	$sql = "DELETE FROM tasks_files_gdrive WHERE file_id='$file_id' AND user_id='$current_user_id' AND google_file_id='".$google_file_id."'";
	$site_db->query($sql);
		
		
	// Проверяем, есть ли в таблице запись о том, что этот документ редактирует кто-то другой
	$sql = "SELECT * FROM tasks_files_gdrive WHERE file_id='$file_id' AND google_file_id='".$google_file_id."'";
	$g_row = $site_db->query_firstrow($sql);
	
	// Если есть пользователи, которые редактируют этот документ, то не удаляем
	if(!$g_row['id'])
	{
		// Удаляем флаг из таблицы о редактирование документа пользователем
		$sql = "DELETE FROM tasks_files_gdrive WHERE file_id='$file_id' AND user_id='$current_user_id'";
		$site_db->query($sql);
		 
		// Инициализация клиента гугл
		$google_client =  init_google_client();
		
		// Инициализация Сервис Гугл Диск
		$service = new Google_Service_Drive($google_client);
		
		//$file = new Google_Service_Drive_DriveFile();
		
		// Удаляем документ с сервера гугл 
		try {
			$service->files->delete($google_file_id);
			 return 1;
		} catch (Exception $e) {
				return -1;
		} 
	}
}

// Возвращает mime тип
function get_mime_type_by_extension($extension)
{
	switch($extension)
	{
		case 'docx':
		case 'doc':
			return 'application/msword';
		break;
		
		case 'pptx':
		case 'ppt':
			return 'application/vnd.ms-powerpoint';
		break;
		
		case 'xlsx':
		case 'xls':
			return 'application/vnd.ms-excel';
		break;
		
	}
	 
}
?>