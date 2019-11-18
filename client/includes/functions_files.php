<?php 
// Страница, мои файлы
function fill_clients_files($client_id, $show_files_list_by='')
{
	global $site_db, $current_client_id, $current_user_id, $GLOBAL_ACCESS_FOR_FOLDER_ARR;
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/client/includes/functions_navigation.php'; // Строка навигации
	
	$files_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/files_template.tpl');
	
	$files_top_menu_clients_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/files_top_menu_clients.tpl');
	
	$files_top_menu_users_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/files_top_menu_users.tpl');
	
	$file_create_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/file_create_form.tpl');
	
	$file_create_form_add_folder_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/file_create_form_add_folder.tpl');
	
	$client_top_name_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/client_top_name.tpl');
	
	$no_files_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/no_files.tpl');
	
	// Данные клиента
	$sql = "SELECT i.*, j.type_name FROM ".CLIENTS_TB." i
			LEFT JOIN ".CLIENTS_TYPES_DATA." j ON i.client_organization_type_id=j.type_id 
			WHERE client_id='$client_id'"; 
			
	$client_data = $site_db->query_firstrow($sql, 1);
	
	// top menu
	if($_GET['cl'])
	{
		$active_array['client'] = 'menu_active';
	}
	else
	{
		$active_array['files'] = 'menu_active';
	}
	
	// Если пользователь просматривает страницу с файлами клиента
	if($show_files_list_by=='user')
	{
		$client_new_files_count = get_new_client_files_block($client_id);
		
	}// Если клиент просматривает страницу с файлами пользователей
	else if($show_files_list_by=='client')
	{
		$users_new_files_count = get_new_users_files_block($client_id);
	}
	
	
	$PARS_1['{CLIENT_NEW_FILES_COUNT}'] = $client_new_files_count;
	$PARS_1['{USERS_NEW_FILES_COUNT}'] = $users_new_files_count;
	$PARS_1['{CLIENT_ID}'] = $client_id;
	$PARS_1['{ACTIVE_1}'] = $active_array['files'];
	$PARS_1['{ACTIVE_2}'] = $active_array['client'];
	
	if($show_files_list_by == 'client')
	{
		$top_menu = fetch_tpl($PARS_1, $files_top_menu_clients_tpl);
	}
	else if($show_files_list_by == 'user')
	{
		$top_menu = fetch_tpl($PARS_1, $files_top_menu_users_tpl);
	}
	
	$folder_id = (int)$_GET['folder_id'] ? (int)$_GET['folder_id'] : 0;
	
	// Если просматриваем папку, не выводим блок создания папки в папке
	if($folder_id)
	{
		$sql = "SELECT FROM ".CLIENTS_FOLDERS_TB." WHERE ";
		
		// Параметры доступа к папке
		$GLOBAL_ACCESS_FOR_FOLDER_ARR = client_folder_access($folder_id, 0, $client_id);
		
		$add_folder_form_block = '';
	}
	else
	{
		$add_folder_form_block = $file_create_form_add_folder_tpl;
	}
	
	$PARS1['{ADD_FOLDER_FORM}'] = $add_folder_form_block;
	
	$PARS1['{MAX_UPLOAD}'] = (int) ini_get('upload_max_filesize');

	// Форму добавления файлов выводим везде, кроме просмотра файлов клиента
	if(!$_GET['cl'] && is_user_have_access_to_add_in_folder($folder_id))
	{
		// Заполняем блок создания папки и файла
		$file_create_form = fetch_tpl($PARS1, $file_create_form_tpl);
	}
	
	// Список файлов
	$files_list = get_client_files_list($client_id, $folder_id, $show_files_list_by);
	
	// Если файлов нет
	if($files_list=='')
	{
		$files_list = $no_files_tpl;
	}
	
	// Строка навигации
	$nav = fill_client_nav('files');
	
	if($current_user_id && !$current_client_id)
	{  
		$client_type = $client_data['client_organization_type_id'] ? $client_data['type_name'] : '';
		
		$PARS_2['{CLIENT_TYPE}'] = $client_type;
		$PARS_2['{CLIENT_NAME}'] = $client_data['client_name'];
	
		$client_name_block = fetch_tpl($PARS_2, $client_top_name_tpl);
	}
	
	$PARS['{CLIENT_NAME_BLOCK}'] = $client_name_block;
	
	$PARS['{TOP_MENU}'] = $top_menu;
	
	$PARS['{CLIENT_ID}'] = $client_id;
	
	$PARS['{FILE_CREATE_FORM}'] = $file_create_form;
	
	$PARS['{FILES_LIST}'] = $files_list;
	
	$PARS['{CURRENT_CLIENT_ID}'] = $current_client_id;
	
	$PARS['{FOLDER_ID}'] = $folder_id;
	
	$PARS['{FROM_CLIENT_ID}'] = $show_files_list_by == 'client' ? $current_client_id : 0;
	
	$PARS['{FROM_USER_ID}'] = $show_files_list_by == 'user' ? $current_user_id : 0;
	
	$PARS['{NAV}'] = $nav;

	return fetch_tpl($PARS, $files_tpl);
}

// Возвращает список файлов и папок
function get_client_files_list($client_id, $folder_id, $show_files_list_by)
{
	global $site_db, $current_user_id, $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR;
	
	// Если просмотр папки
	if($folder_id)
	{
		// Список файлов
		$files_list = fill_folder_files_list_cl($folder_id, $client_id, $show_files_list_by);
	}
	else
	{
		// Список файлов
		$files_list = fill_client_files($client_id, $show_files_list_by);
	}
	
	// Сумарный список файлов
	$files_result_list = $files_list;
	
	return $files_result_list;
}

// Возвращает список файлов в папке
function fill_folder_files_list_cl($folder_id, $user_id)
{
	global $site_db, $current_user_id, $current_client_id, $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR;
	
	$sql = "SELECT * FROM ".CLIENTS_FOLDERS_TB." WHERE folder_id='$folder_id'";
		
	$folder_data = $site_db->query_firstrow($sql);
		
	// 
	if(!$folder_id)
	{
		header('Location: /files');
	}
		
	// Выбор списка файлов пользователя
	$sql = "SELECT * FROM ".CLIENTS_FILES_TB." WHERE folder_id='$folder_id' ORDER by date DESC";
	
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		// Заполняем файл
		$files_list .= fill_client_file_item($row, $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR['f']);
	}
	
	return $files_list;
}


// Список файлов пользователя
function fill_client_files($client_id, $show_files_list_by)
{
	global $site_db, $current_user_id, $current_client_id;
	
	if($show_files_list_by=='client')
	{
		if($_GET['cl'])
		{
			// Выбор списка папок пользователя
			$sql = "SELECT * FROM ".CLIENTS_FOLDERS_TB." WHERE client_id='$client_id' AND from_client_id <> '$client_id' ORDER by date DESC";
		}
		else
		{
			// Выбор списка папок пользователя
			$sql = "SELECT * FROM ".CLIENTS_FOLDERS_TB." WHERE client_id='$client_id' AND from_client_id='$client_id' ORDER by date DESC";
		}
	}
	if($show_files_list_by=='user')
	{
		if($_GET['cl'])
		{
			// Выбор списка папок  клиента
			$sql = "SELECT * FROM ".CLIENTS_FOLDERS_TB." WHERE client_id='$client_id' AND from_client_id='$client_id' ORDER by date DESC";
		}
		else
		{
			// Выбор списка папок пользователя
			$sql = "SELECT * FROM ".CLIENTS_FOLDERS_TB." WHERE client_id='$client_id' AND from_user_id='$current_user_id' ORDER by date DESC";
		}
	}	 
	 
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		// Заполняем папку
		$folders_list .= fill_client_folder_item($row, $users_for_access_arr);
	}
		
	if($show_files_list_by=='client')
	{
		if($_GET['cl'])
		{
			// Выбор списка файлов пользователя
			$sql = "SELECT * FROM ".CLIENTS_FILES_TB." WHERE folder_id='0' AND client_id='$client_id' AND from_client_id <> '$client_id' ORDER by date DESC";
		}
		else
		{
			// Выбор списка файлов пользователя
			$sql = "SELECT * FROM ".CLIENTS_FILES_TB." WHERE folder_id='0' AND client_id='$client_id' AND from_client_id='$client_id' ORDER by date DESC";
		}
	}
	
	if($show_files_list_by=='user')
	{
		if($_GET['cl'])
		{
			// Выбор списка файлов пользователя
			$sql = "SELECT * FROM ".CLIENTS_FILES_TB." WHERE folder_id='0' AND client_id='$client_id' AND from_client_id='$client_id' ORDER by date DESC";
		}
		else
		{
			// Выбор списка файлов пользователя
			$sql = "SELECT * FROM ".CLIENTS_FILES_TB." WHERE folder_id='0' AND client_id='$client_id' AND from_user_id='$current_user_id' ORDER by date DESC";
		}
	}
	 
		 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		// Заполняем файл
		$files_list .= fill_client_file_item($row, $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR['f']);
	}
	
	return $folders_list.$files_list;
}



// Заполняет элемент файла в списке
function fill_client_file_item($file_data, $admin_folder=0)
{
	global $current_user_id, $current_client_id;
	
	$files_list_file_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/files_list_file_item.tpl');
	
	$files_list_file_item_delete_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/files_list_file_item_delete.tpl');
	
	$files_list_users_edit_desc_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/files_list_users_edit_desc_block.tpl');
	
	$no_file_desc_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/no_file_desc.tpl');
	 
	// Для создателя папки выводим иконку удалить
	if(check_admin_file_by_file_data($file_data, $current_client_id, $current_user_id) || $admin_folder)
	{
		$PARS1['{FILE_ID}'] = $file_data['file_id'];
		
		$PARS1['{FOLDER_ID}'] = 0;
		
		$delete_block = fetch_tpl($PARS1, $files_list_file_item_delete_tpl);
		
		$edit_desc_block = fetch_tpl($PARS1, $files_list_users_edit_desc_block_tpl);
	}
	
	$PARS['{CLIENT_ID}'] = $file_data['client_id'];
	
	$PARS['{DELETE_BLOCK}'] = $delete_block;
	
	$PARS['{EDIT_DESC}'] = $edit_desc_block;
	
	$PARS['{DESC}'] = $file_data['file_desc'] ? nl2br($file_data['file_desc']) : $no_file_desc_tpl;
	
	$PARS['{FILE_ID}'] = $file_data['file_id'];
			
	$PARS['{FILE_NAME}'] = stripslashes($file_data['file_name']);
	
	$folder_path = '';
			
	// Если файлы в папке, в пути к файлу указываем папку
	if($file_data['folder_id'])
	{
		$folder_path= '/'.$file_data['folder_id'];
	}
	
	// Файл в общем доступе
	if($file_data['from_client_id'])
	{
		$folder_path = '/upload/client/'.$file_data['client_id'].'/out';
	}
	else
	{
		$folder_path = '/upload/client/'.$file_data['client_id'].'/in';
	}
	
	if($file_data['folder_id'])
	{
		$folder_path .= '/'.$file_data['folder_id'];
	}
	
	$file_link = $folder_path.'/'.$file_data['file_name'];
	
	if($current_client_id)
	{
		$file_link = '/client/download/'.$file_data['file_id'];
	}
	else if($current_user_id)
	{
		$file_link = '/cl_download/'.$file_data['file_id'];
	}
			
	$PARS['{FILE_LINK}'] = $file_link;
			
	$file_item = fetch_tpl($PARS, $files_list_file_item_tpl);
	
	return $file_item;
}


// Заполняет элемент папки в списке
function fill_client_folder_item($folder_data)
{
	global $current_client_id, $current_user_id;
	
	$files_list_folder_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/files_list_folder_item.tpl');
	
	$files_list_folder_item_delete_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/files_list_folder_item_delete.tpl');
	
	$files_list_users_edit_desc_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/files_list_users_edit_desc_block.tpl');
	
	$no_file_desc_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/client/templates/files/no_file_desc.tpl');
	
	// Для создателя папки выводим иконку удалить
	if(check_admin_folder_by_folder_data($folder_data, $current_client_id, $current_user_id))
	{
		$PARS1['{FOLDER_ID}'] = $folder_data['folder_id'];
		
		$PARS1['{FILE_ID}'] = 0;
		
		$delete_block = fetch_tpl($PARS1, $files_list_folder_item_delete_tpl);
		
		$edit_desc_block = fetch_tpl($PARS1, $files_list_users_edit_desc_block_tpl);
	}

	$PARS['{CL}'] = $_GET['cl'] ? 1 : 0;
	
	$PARS['{CLIENT_ID}'] = $folder_data['client_id'];
	
	$PARS['{FILE_MODE}'] = $file_link_mode;
	
	$PARS['{EDIT_DESC}'] = $edit_desc_block;
	
	$PARS['{DELETE_BLOCK}'] = $delete_block;
	 
	$PARS['{DESC}'] = $folder_data['folder_desc'] ? nl2br($folder_data['folder_desc']) : $no_file_desc_tpl;
	 
	$PARS['{FOLDER_ID}'] = $folder_data['folder_id'];
			
	$PARS['{FOLDER_NAME}'] = stripslashes($folder_data['folder_name']);
			
	$folder_item = fetch_tpl($PARS, $files_list_folder_item_tpl);
			
	return $folder_item;
}


// Проверка на админа файла
function check_admin_file_by_file_data($file_data, $client_id, $user_id)
{
	global $current_user_id, $current_client_id;
	 
	if($file_data['from_client_id'] && $file_data['from_client_id']==$client_id)
	{
		return true;
	}
	else if($file_data['from_user_id'] && $file_data['from_user_id']==$user_id)
	{
		return true;
	}
	else
	{
		return false;
	}
	
}

// Проверка на админа папки
function check_admin_folder_by_folder_data($folder_data, $client_id, $user_id)
{
	global $current_user_id, $current_client_id;
	
	if($folder_data['from_client_id'] && $folder_data['from_client_id']==$client_id)
	{
		return true;
	}
	else if($folder_data['from_user_id'] && $folder_data['from_user_id']==$user_id)
	{
		return true;
	}
	else
	{
		return false;
	}
	
}
// Проверяет, может ли просматривать папку пользователь
function client_folder_access($folder_id, $client_id, $user_id)
{
	global $site_db, $current_user_id;
	
	$full_access = 0; // полный доступ к папке
	
	$read_full_access = 0; // папка доступна для просмотра полностью
	
	$read_part_access = 0; // папка доступна для просмотра частично, отображаются только файлы, разрешенные для чтения
	
	$sql = "SELECT from_user_id, from_client_id  FROM ".CLIENTS_FOLDERS_TB." WHERE folder_id='$folder_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if(($row['from_user_id'] && $row['from_user_id'] == $user_id) || ($row['from_client_id'] && $row['from_client_id']==$client_id) )
	{
		$full_access = 1;
	}
	else
	{
		$full_access = 0;
	}
	return array('f' => $full_access);
}

// РАзрешает ли добавлять в папку файлы
function is_user_have_access_to_add_in_folder($folder_id)
{
	global $site_db, $current_user_id, $current_client_id;
	
	if(!$folder_id)
	{
		return true;
	}
	
	$sql = "SELECT * FROM ".CLIENTS_FOLDERS_TB." WHERE folder_id='$folder_id'";
	
	$folder_data = $site_db->query_firstrow($sql);
	
	if($folder_data['client_id'] == $current_client_id && $folder_data['from_client_id'] == $current_client_id)
	{
		return true;	
	}
	
	if(!$current_client_id && $current_user_id && !$folder_data['from_client_id'])
	{
		return true;	
	}
	
	return false;
	//echo $folder_data['from_user_id'];
	
}
// Проверяет, является ли файл доступным для редактирования пользователем
function is_admin_file_cl($file_id, $client_id, $user_id)
{
	global $site_db;
	
	// Данные файла
	$sql = "SELECT file_id, folder_id, from_user_id, from_client_id FROM ".CLIENTS_FILES_TB." WHERE file_id='$file_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['file_id'] && $row['from_client_id'] && $row['from_client_id']==$client_id)
	{
		return true;
	}
	elseif($row['file_id'] && $row['from_user_id'] && $row['from_user_id']==$user_id)
	{
		return true;
	}
	// Если файл в папке, проверяем, является ли пользователем создателем папки
	else if($row['file_id'] && $row['folder_id'] > 0)
	{
		$sql = "SELECT from_client_id,  from_client_id FROM ".CLIENTS_FOLDERS_TB." WHERE folder_id='".$row['folder_id']."'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['from_client_id'] && $row['from_client_id']==$client_id)
		{
			return true;
		}
		else if($row['from_user_id'] && $row['from_user_id']==$user_id)
		{
			return true;
		}
		else
		{
			return false;
		}
		 
	}
	else
	{
		return false;
	}
}

// Является ли пользователь владельцем папки
function is_admin_folder($folder_id, $client_id, $user_id)
{
	global $site_db, $current_user_id;
	
	// Выбираем файл
	$sql = "SELECT * FROM ".CLIENTS_FOLDERS_TB." WHERE folder_id='$folder_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['from_client_id'] && $row['from_client_id']==$client_id)
	{
		return true;
	}
	else if($row['from_user_id'] && $row['from_user_id']==$user_id)
	{
		return true;
	}
}


// Скачивание файла
function fill_download_cl($file_id)
{
	global $site_db, $current_user_id, $current_client_id;
	
	if(!$current_user_id && !$current_client_id)
	{  
		exit();
	}
	
	// Данные файла
	$sql = "SELECT * FROM ".CLIENTS_FILES_TB." WHERE file_id='$file_id'";
		
	$row = $site_db->query_firstrow($sql);
	 
	if(!$row['file_id'] || !$file_id)
	{
		header('Location: /client/files');
		exit();
	}
	
	if($row['from_client_id'])
	{
		$folder_path = CLIENTS_PATH.'/'.$row['client_id'].'/out';
	}
	else if($row['from_user_id'])
	{ 
		$folder_path = CLIENTS_PATH.'/'.$row['client_id'].'/in';
	}
	
	if($row['folder_id'])
	{
		$folder_path .= '/'.$row['folder_id'];
	}	
		 
	$file_name = $folder_path.'/'.$row['file_name'];
  
	// Даем файл на скачку
`echo $folder_path>>/qaz.txt`;
	file_download_cl($file_name);
}

// Сохранение файла
function file_download_cl($filename, $mimetype='application/octet-stream') {
	
   if (file_exists($filename)) 
   {
    if (ob_get_level()) {
      ob_end_clean();
    }
	
		// заставляем браузер показать окно сохранения файла
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . basename($filename));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filename));
		// читаем файл и отправляем его пользователю
		readfile($filename);
		exit;
   } 
   else 
   {
     header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
     header('Status: 404 Not Found');
   }
  // exit;
}

// Получает кол-во новых файлов клиента и производит обнуление счетчика новых файлов при необходимости
function get_new_client_files_block($client_id)
{
	global $site_db, $current_user_id;
	
	// Кол-во новых файлов клиента
	$client_new_files_count = get_new_client_files_for_users_count($client_id);
	
	// Обнуляем счетчик новых файлов, если пользователь просматривает файлы клиента
	if($client_new_files_count && $_GET['cl'])
	{
		// Обнуляем счетчик новых файлов
		client_new_files_noticed($client_id);
			
		$client_new_files_count = 0;
	}
	$client_new_files_count = $client_new_files_count ? "(+ ".$client_new_files_count.")" : ''; 
	
	return $client_new_files_count;
}

// Обнуляем счетчик новых файлов
function client_new_files_noticed($client_id)
{
	global $site_db, $current_user_id;
	
	$sql = "UPDATE ".CLIENTS_FILES_TB." SET file_noticed = 1 WHERE  client_id='$client_id' AND from_client_id='$client_id' AND file_noticed=0";
	
	$site_db->query($sql);
}


// Получает кол-во новых файлов пользователей для клиента и производит обнуление счетчика новых файлов при необходимости
function get_new_users_files_block($client_id)
{
	global $site_db, $current_user_id;
	
	// Кол-во новых файлов клиента
	$users_new_files_count = get_new_users_files_for_client_count($client_id);
	 
	// Обнуляем счетчик новых файлов, если пользователь просматривает файлы клиента
	if($users_new_files_count && $_GET['cl'])
	{
		// Обнуляем счетчик новых файлов
		client_users_new_files_noticed($client_id);
			
		$users_new_files_count = 0;
	}
	$users_new_files_count = $users_new_files_count ? "(+ ".$users_new_files_count.")" : ''; 
	
	return $users_new_files_count;
}

// Обнуляем счетчик новых файлов пользователей для клиента
function client_users_new_files_noticed($client_id)
{
	global $site_db, $current_user_id;
	
	$sql = "UPDATE ".CLIENTS_FILES_TB." SET file_noticed = 1 WHERE  client_id='$client_id' AND from_client_id=0 AND file_noticed=0";
	
	$site_db->query($sql);
}
?>