<?php
// Страница, мои файлы
function fill_files()
{
	global $site_db, $current_user_id, $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR;
	
	$files_tpl = file_get_contents('templates/files/files_template.tpl');
	
	$file_create_form_tpl = file_get_contents('templates/files/file_create_form.tpl');
	
	$file_create_form_add_folder_tpl = file_get_contents('templates/files/file_create_form_add_folder.tpl');
	
	// Уведомление о чужих файлах
	$new_count_av_files = get_new_files_notice_for_user($current_user_id);
	
	$new_count_av_files_in_top_menu = $new_count_av_files ? '(+ '.$new_count_av_files.')' : '';
	
	// Если общие файлы
	if($_GET['s'])
	{
		$is_sharing = 1;
		$is_avalible = 0;
		$user_id = '';
	}
	// Доступные файлы
	else if($_GET['av'])
	{
		$is_sharing = 0;
		$is_avalible = 1;
		$user_id = $current_user_id;
	}
	else
	{
		$is_sharing = 0;
		$is_avalible = 0;
		$user_id = $current_user_id;
		$show_my_files = 1;
	}
	
	$folder_id = (int)$_GET['folder_id'] ? (int)$_GET['folder_id'] : 0;
	
	// Если просматриваем папку, не выводим блок создания папки в папке
	if($folder_id)
	{
		// Параметры доступа к папке
		$GLOBAL_USER_ACCESS_FOR_FOLDER_ARR = folder_access_for_user($folder_id, $current_user_id);
		
		$add_folder_form_block = '';
	}
	else
	{
		$add_folder_form_block = $file_create_form_add_folder_tpl;
	}
	
	$PARS1['{ADD_FOLDER_FORM}'] = $add_folder_form_block;
	
	$PARS1['{MAX_UPLOAD}'] = (int) ini_get('upload_max_filesize');
	
	//print_r($GLOBAL_USER_ACCESS_FOR_FOLDER_ARR);
	
	if($folder_id)
	{
		// Данные папки
		$sql = "SELECT * FROM ".FOLDERS_TB." WHERE folder_id='$folder_id'";
		
		$folder_data = $site_db->query_firstrow($sql);
		
		// Если папка в общем доступе, то страница просмотра файлов будет так же в общем доступе
		$is_sharing = $folder_data['is_sharing'];
		
	}
	// Форму добавления файлов выводим везде, кроме просмотра Чужих файлов
	if(($show_my_files && !$folder_id) || ($folder_id && $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR['f']) || ($folder_id && $folder_data['is_sharing']) || $_GET['s'])
	{
		// Заполняем блок создания папки и файла
		$file_create_form = fetch_tpl($PARS1, $file_create_form_tpl);
	}
	
	// Список файлов
	$files_list = get_files_list($user_id, $folder_id, $is_sharing, $is_avalible);
	
	// Если файлов нет
	if($files_list=='')
	{
		$files_list = file_get_contents('templates/files/no_files.tpl');
	}
	
	// Подсветка пунктом меню
	
	$active_array = array('files'=>'', 'v_files'=>'', 'sharing'=>'');
	
	if($_GET['s'])
	{
		$active_array['sharing'] = 'menu_active';
	}
	else if($_GET['av'])
	{
		$active_array['v_files'] = 'menu_active';
	}
	else
	{
		$active_array['files'] = 'menu_active';
	}
	
	$PARS['{ACTIVE_1}'] = $active_array['files'];
	$PARS['{ACTIVE_2}'] = $active_array['v_files'];
	$PARS['{ACTIVE_3}'] = $active_array['sharing'];
	
	$PARS['{NEW_COUNT_AV_FILES}'] = $new_count_av_files_in_top_menu;
	
	$PARS['{FILE_CREATE_FORM}'] = $file_create_form;
	
	$PARS['{FILES_LIST}'] = $files_list;
	
	$PARS['{CURRENT_USER_ID}'] = $current_user_id;
	
	$PARS['{FOLDER_ID}'] = $folder_id;
	
	$PARS['{IS_SHARING}'] = $is_sharing;

	return fetch_tpl($PARS, $files_tpl);
}

// Возвращает список файлов и папок
function get_files_list($user_id, $folder_id, $is_sharing = 0, $is_avalible = 0)
{
	global $site_db, $current_user_id, $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR, $users_for_access_arr;
	
	// Получает массив пользователей, относящихся к пользователю (начальники и подчиненные)
	$users_for_access_arr = get_current_user_users_arrs(array(1,1,1,1,1),1);
	
	 
	// Если просмотр папки
	if($folder_id)
	{
		// Список файлов
		$files_list = fill_folder_files_list($folder_id, $user_id);
	}
	// Просмотр файлов и папок открытых для пользователя
	else if($_GET['av'])
	{
		$files_list = fill_avalible_files_for_user_list($user_id);
	}
	// Просмотр файлов пользователя или общих файлов
	else
	{
		$files_list = fill_user_files($current_user_id, $is_sharing);
	}
	
	// Сумарный список файлов
	$files_result_list = $files_list;
	
	return $files_result_list;
}

// Список файлов пользователя
function fill_user_files($user_id, $is_sharing)
{
	global $site_db, $current_user_id, $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR, $users_for_access_arr;
	
	if($user_id && !$is_sharing)
	{
		$and_user_id = " AND user_id='$user_id'";
	}
	
	// Выбор списка папок пользователя
	$sql = "SELECT * FROM ".FOLDERS_TB." WHERE is_sharing='$is_sharing' $and_user_id ORDER by date DESC";
	 
	$res = $site_db->query($sql);
		
	while($row=$site_db->fetch_array($res))
	{
		// Заполняем папку
		$folders_list .= fill_folder_item($row, $users_for_access_arr);
	}
		
	// Выбор списка файлов пользователя
	$sql = "SELECT * FROM ".FILES_TB." WHERE folder_id='0' AND is_sharing='$is_sharing' $and_user_id ORDER by date DESC";
		 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		// Заполняем файл
		$files_list .= fill_file_item($row, $users_for_access_arr);
	}
	
	return $folders_list.$files_list;
}
// Возвращает список файлов и папок открытых для просмотра пользователю
function fill_avalible_files_for_user_list($user_id)
{
	global $site_db,$user_obj, $current_user_id, $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR, $users_for_access_arr;
	
	$files_av_user_block_tpl = file_get_contents('templates/files/files_av_user_block.tpl');
	 
	// Выбор списка папок открытых для пользователя, для которых открыты файлы
	$sql = "SELECT f.*, i.noticed, i.access_by_user_id, i.user_id as file_access_to_user_id  FROM ".FILES_ACCESS_TB." i
			LEFT JOIN ".FOLDERS_TB." f ON i.folder_id=f.folder_id
			WHERE  i.user_id='$user_id' AND i.folder_id>0 AND i.file_id > 0";
				 
	$res = $site_db->query($sql);
 	
	while($row=$site_db->fetch_array($res, 1))
	{
		// Флаг, что в папке есть файлы, открытые для пользователя
		$row['file_in_folder_access_to_user_id'] = 1;
		
		// Если в папке файл, который не принят, ставим флаг
		if(($row['file_access_to_user_id']==$current_user_id && $row['noticed']=='0') || $folders_and_files_arr[$row['access_by_user_id']]['folders'][$row['folder_name'].'_'.$row['folder_id']]['file_in_folder_not_noticed'])
		{
			$row['file_in_folder_not_noticed'] = 1;
		}
		$folders_and_files_arr[$row['access_by_user_id']]['folders'][$row['folder_name'].'_'.$row['folder_id']] = $row;
	}
				
	// Выбор списка папок открытых для пользователя
	$sql = "SELECT i.*, j.noticed, j.access_by_user_id, j.user_id as folder_access_to_user_id FROM ".FOLDERS_TB." i
			LEFT JOIN ".FILES_ACCESS_TB." j ON j.folder_id=i.folder_id
			WHERE j.user_id='$user_id' AND j.folder_id>0 AND j.file_id=0  ";
		 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res, 1))
	{  
		// Делаем отметку о том, что папка не принята еще
		if($row['folder_access_to_user_id']==$current_user_id && $row['noticed']=='0')
		{ 
			$row['folder_not_noticed'] = 1;
		}
		// Если в папке файл, который не принят, ставим флаг
		if($folders_and_files_arr[$row['access_by_user_id']]['folders'][$row['folder_name'].'_'.$row['folder_id']]['file_in_folder_not_noticed'])
		{
			$row['file_in_folder_not_noticed'] = 1;
		}
		$folders_and_files_arr[$row['access_by_user_id']]['folders'][$row['folder_name'].'_'.$row['folder_id']] = $row;
	}
	
	// Выбор списка файлов открытых пользователя
	$sql = "SELECT i.*, j.access_by_user_id, j.noticed, j.user_id as access_to_user_id FROM ".FILES_TB." i
			LEFT JOIN ".FILES_ACCESS_TB." j ON j.file_id=i.file_id
			WHERE j.user_id='$user_id' AND i.folder_id=0 ORDER by date DESC";
		 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res, 1))
	{
		$folders_and_files_arr[$row['access_by_user_id']]['files'][$row['file_name'].'_'.$row['file_id']] = $row;
	}
	
	foreach($folders_and_files_arr as $user => $files_types)
	{
		$folders_list = '';
		$files_list = '';
		
		// Формируем список папок
		if($files_types['folders'])
		{
			ksort($files_types['folders']);
			 
			foreach($files_types['folders'] as $folder_data)
			{
			 	$folders_list .= fill_folder_item($folder_data, $users_for_access_arr);
			}
		}
		
		// Формируем список файлов
		if($files_types['files'])
		{
			ksort($files_types['files']);
			
			foreach($files_types['files'] as $file_data)
			{
				$files_list .= fill_file_item($file_data, $users_for_access_arr);
			}
		}
		 
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($user);
		
		$PARS['{USER_ID}'] = $user;
		
		$PARS['{AVATAR_SRC}'] = get_user_preview_avatar_src($user, $user_obj->get_user_image());
		
		$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
		$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{FILES_LIST}'] = $folders_list.$files_list;
		
		$data_list .= fetch_tpl($PARS, $files_av_user_block_tpl);
	}
	
	 // echo "<pre>", print_r($folders_and_files_arr), "</pre>";
	 	 
	return $data_list;
}
// Возвращает список файлов в папке
function fill_folder_files_list($folder_id, $user_id)
{
	global $site_db, $current_user_id, $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR, $users_for_access_arr;
	
	$sql = "SELECT * FROM ".FOLDERS_TB." WHERE folder_id='$folder_id'";
		
	$folder_data = $site_db->query_firstrow($sql);
		
		// print_r($GLOBAL_USER_ACCESS_FOR_FOLDER_ARR);
		
	// Есть ли у пользователя просматривать данную папку
	if(!$GLOBAL_USER_ACCESS_FOR_FOLDER_ARR['f'] && !$GLOBAL_USER_ACCESS_FOR_FOLDER_ARR['rf'] && !$GLOBAL_USER_ACCESS_FOR_FOLDER_ARR['rp'] && !$folder_data['is_sharing'])
	{
		header('Location: /files');
	}
	
	// Если пользователь создатель папки или ему разрешен полный ее просмотр
	if($GLOBAL_USER_ACCESS_FOR_FOLDER_ARR['f'] || $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR['rf'] || $folder_data['is_sharing'])
	{
		// Выбор списка файлов пользователя
		$sql = "SELECT * FROM ".FILES_TB." WHERE folder_id='$folder_id' ORDER by date DESC";
				
	}
	// Если в папке есть файлы, открытые для просмотра пользователю
	else if($GLOBAL_USER_ACCESS_FOR_FOLDER_ARR['rp'])
	{
		// Выбор файлов в папке доступных для чтения
		$sql = "SELECT j.* ,i.access_by_user_id, i.noticed, i.user_id as access_to_user_id FROM ".FILES_ACCESS_TB." i
				LEFT JOIN ".FILES_TB." j ON i.file_id=j.file_id
				WHERE  i.user_id='$user_id' AND j.folder_id='$folder_id' AND i.file_id > 0 ORDER by j.date DESC";
			 
	}
 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$files_arr[$row['file_id']] = $row;
		$files_ids_arr[] = $row['file_id'];
	}
	
	// Если просматривается раздел чужих файлов и к папке пользователь имеет полный доступ "rf"
	if($_GET['av']==1 && $files_ids_arr && $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR['rf'])
	{
		$files_ids = implode(',', $files_ids_arr);
		// Выюираем возможные доступы пользователя к файлам, которые лежат в этой папке 
		$sql = "SELECT * FROM ".FILES_ACCESS_TB." WHERE user_id='$current_user_id' AND file_id IN ($files_ids )";
		$res = $site_db->query($sql);
		while($row=$site_db->fetch_array($res))
		{
			$files_arr[$row['file_id']]['access_to_user_id'] = $current_user_id;
			$files_arr[$row['file_id']]['noticed'] = $row['noticed'];
			$files_arr[$row['file_id']]['hide_block_hide'] = 1; // не выводить блок скрыть
		}
	}
	
	// Проходим по списку файлов и создаем список
	foreach($files_arr as $file_data)
	{
		// Заполняем файл
		$files_list .= fill_file_item($file_data, $users_for_access_arr, $GLOBAL_USER_ACCESS_FOR_FOLDER_ARR['f']);
	}
	
	//print_r($files_ids_arr);
	return $files_list;
}

// Заполняет элемент папки в списке
function fill_folder_item($folder_data, $users_for_access_arr)
{
	global $current_user_id;
	
	$files_list_folder_item_tpl = file_get_contents('templates/files/files_list_folder_item.tpl');
	
	$files_list_folder_item_delete_tpl = file_get_contents('templates/files/files_list_folder_item_delete.tpl');
	
	$files_list_users_access_block_tpl = file_get_contents('templates/files/files_list_users_access_block.tpl');
	
	$files_list_users_edit_desc_block_tpl = file_get_contents('templates/files/files_list_users_edit_desc_block.tpl');
	
	$no_file_desc_tpl = file_get_contents('templates/files/no_file_desc.tpl');
	
	$files_list_file_item_confirm_tpl = file_get_contents('templates/files/files_list_file_item_confirm.tpl');
	
	$files_list_file_item_hide_tpl = file_get_contents('templates/files/files_list_file_item_hide.tpl');
	
	// Для создателя папки выводим иконку удалить
	if($current_user_id==$folder_data['user_id'])
	{
		$PARS1['{FOLDER_ID}'] = $folder_data['folder_id'];
		
		$PARS1['{FILE_ID}'] = 0;
		
		$delete_block = fetch_tpl($PARS1, $files_list_folder_item_delete_tpl);
		
		$edit_desc_block = fetch_tpl($PARS1, $files_list_users_edit_desc_block_tpl);
		
		/*if(!$folder_data['is_sharing'])
		{
			// Блок доступа к файлам и папкам
			$users_access_block = fill_file_access_block(0, $folder_data['folder_id'], $users_for_access_arr);
		}*/
	}
	
	if(!$folder_data['is_sharing'])
	{
		// Пользователи, которые давали другим пользователям доступы к папке
		$folder_accessed_by_users_arr = get_folder_accessed_by_users_arr($folder_data['folder_id']);
		
		
		// Блок доступа к файлам и папкам
		$users_access_block = fill_file_access_block(0, $folder_data['folder_id'], $users_for_access_arr, $folder_data['user_id'], $folder_accessed_by_users_arr);
	}
		
	if($_GET['av'])
	{
		$file_link_mode = '&av=1';
	}
	if($_GET['s'])
	{
		$file_link_mode = '&s=1';
	}
	
	// Если файл, который в папке доступен и не принят ИЛИ папка передана, но не принята
	if($folder_data['file_in_folder_not_noticed'] || $folder_data['folder_not_noticed'])
	{
		$not_confirm_back = 'not_confirm';
	}
	//  Если файл, который в папке доступен и не принят -  добавляем доп. класс
	if($folder_data['file_in_folder_not_noticed'])
	{
		//$not_confirm_back .= ' not_confirm_file_in_folder';
	}
	// папка передана, но не принята - добавляем кнопку Принять
	if($folder_data['folder_not_noticed'])
	{
		$PARS1['{FILE_ID}'] = 0;
		$PARS1['{FOLDER_ID}'] = $folder_data['folder_id'];
		
		// Принять файл
		$confirm_block =  fetch_tpl($PARS1, $files_list_file_item_confirm_tpl);
	}
	
 
	// Если папка передана пользователю или в папке есть файл, который передан пользователю
	if($folder_data['folder_access_to_user_id']==$current_user_id || $folder_data['file_in_folder_access_to_user_id'])
	{
		$PARS1['{FILE_ID}'] = 0;
		$PARS1['{FOLDER_ID}'] = $folder_data['folder_id'];
		 
		$hide_block = fetch_tpl($PARS1, $files_list_file_item_hide_tpl);
	}
	
	
	$PARS['{FILE_MODE}'] = $file_link_mode;
	
	$PARS['{EDIT_DESC}'] = $edit_desc_block;
	
	$PARS['{USERS_ACCESS_BLOCK}'] = $users_access_block;
	
	$PARS['{DELETE_BLOCK}'] = $delete_block;
	
	$PARS['{CONFIRM_BLOCK}'] = $confirm_block;
	
	$PARS['{HIDE_BLOCK}'] = $hide_block;
		
	$PARS['{NOT_CONFIRM}'] = $not_confirm_back;
	 
	$PARS['{DESC}'] = $folder_data['folder_desc'] ? nl2br($folder_data['folder_desc']) : $no_file_desc_tpl;
	 
	$PARS['{FOLDER_ID}'] = $folder_data['folder_id'];
			
	$PARS['{FOLDER_NAME}'] = stripslashes($folder_data['folder_name']);
			
	$folder_item = fetch_tpl($PARS, $files_list_folder_item_tpl);
			
	return $folder_item;
}

// Заполняет элемент файла в списке
function fill_file_item($file_data, $users_for_access_arr, $admin_folder=0)
{
	global $current_user_id;
	//echo "<pre>"print_R($users_for_access_arr);
	$files_list_file_item_tpl = file_get_contents('templates/files/files_list_file_item.tpl');
	
	$files_list_file_item_delete_tpl = file_get_contents('templates/files/files_list_file_item_delete.tpl');
	
	$files_list_users_edit_desc_block_tpl = file_get_contents('templates/files/files_list_users_edit_desc_block.tpl');
	
	$files_list_users_status_block_tpl = file_get_contents('templates/files/files_list_users_status_block.tpl');
	
	$no_file_desc_tpl = file_get_contents('templates/files/no_file_desc.tpl');
	
	$file_size_tpl = file_get_contents('templates/files/file_size.tpl');
	
	$files_list_file_item_confirm_tpl = file_get_contents('templates/files/files_list_file_item_confirm.tpl');
	
	$files_list_file_item_hide_tpl = file_get_contents('templates/files/files_list_file_item_hide.tpl');
	
	// Для создателя папки выводим иконку удалить
	if($current_user_id==$file_data['user_id'] || $admin_folder)
	{
		$PARS1['{FILE_ID}'] = $file_data['file_id'];
		
		$PARS1['{FOLDER_ID}'] = 0;
		
		$delete_block = fetch_tpl($PARS1, $files_list_file_item_delete_tpl);
		
		$edit_desc_block = fetch_tpl($PARS1, $files_list_users_edit_desc_block_tpl);
		 
		/*if(!$file_data['is_sharing'])
		{
			// Блок доступа к файлам и папкам
			$users_access_block = fill_file_access_block($file_data['file_id'], 0, $users_for_access_arr);
		}*/
	}

	if(!$file_data['is_sharing'])
	{
		// Пользователи, которые давали другим пользователям доступы к файлу
		$file_accessed_by_users_arr = get_file_accessed_by_users_arr($file_data['file_id']);
		
		//print_r($file_accessed_by_users_arr);
		// Блок доступа к файлам и папкам
		$users_access_block = fill_file_access_block($file_data['file_id'], $file_data['folder_id'], $users_for_access_arr, $file_data['user_id'], $file_accessed_by_users_arr);
	}
	
	if($file_data['filesize'])
	{
		$fsize = formate_filesize($file_data['filesize']);
	}
	else
	{  
		$fsize = filesize(get_path_to_file($file_data));
		$fsize = formate_filesize($fsize);
		
	}
	$PARS_1['{FILESIZE}'] = $fsize;
	$filesize = fetch_tpl($PARS_1, $file_size_tpl);	
	
	
	$file_upload_dir = UPLOAD_PATH;
	
	$PARS1['{FILE_ID}'] = $file_data['file_id'];
		
	$PARS1['{FOLDER_ID}'] = $file_data['folder_id'];
		
	$file_status_block = fetch_tpl($PARS1, $files_list_users_status_block_tpl);
	
	// Если файл передан актуальному пользователю и он еще не принят, делаем уведомление
	if($file_data['access_to_user_id']==$current_user_id && !$file_data['noticed'])
	{
		$PARS1['{FILE_ID}'] = $file_data['file_id'];
		$PARS1['{FOLDER_ID}'] = $file_data['folder_id'];
		
		// Принять файл
		$confirm_block =  fetch_tpl($PARS1, $files_list_file_item_confirm_tpl);
		
		$not_confirm_back = 'not_confirm';
	}
	
	// Если файл был передан текущему пользователю, выводим кнопку Скрыть
	if($file_data['access_to_user_id']==$current_user_id && !$file_data['hide_block_hide'])
	{
		$PARS1['{FILE_ID}'] = $file_data['file_id'];
		$PARS1['{FOLDER_ID}'] = $file_data['folder_id'];
		
		$hide_block = fetch_tpl($PARS1, $files_list_file_item_hide_tpl);
	}
	
	$PARS['{STATUS_BLOCK}'] = $file_status_block;
	
	$PARS['{USERS_ACCESS_BLOCK}'] = $users_access_block;
	
	$PARS['{CONFIRM_BLOCK}'] = $confirm_block;
	
	$PARS['{HIDE_BLOCK}'] = $hide_block;
	
	$PARS['{NOT_CONFIRM}'] = $not_confirm_back;
	
	$PARS['{DELETE_BLOCK}'] = $delete_block;
	
	$PARS['{EDIT_DESC}'] = $edit_desc_block;
	
	$PARS['{DESC}'] = $file_data['file_desc'] ? nl2br($file_data['file_desc']) : $no_file_desc_tpl;
	
	$PARS['{FILE_ID}'] = $file_data['file_id'];
			
	$PARS['{FILE_NAME}'] = stripslashes($file_data['file_name']);
	
	$PARS['{FILESIZE}'] = $filesize;
	
	$folder_path = '';
			
	// Если файлы в папке, в пути к файлу указываем папку
	if($file_data['folder_id'])
	{
		$folder_path= '/'.$file_data['folder_id'];
	}
	
	// Файл в общем доступе
	if($file_data['is_sharing'])
	{
		$folder_path = '/upload/sh';
	}
	else
	{
		$folder_path = '/upload/pr/'.$file_data['user_id'];
	}
	
	if($file_data['folder_id'])
	{
		$folder_path .= '/'.$file_data['folder_id'];
	}
	
	$file_link = $folder_path.'/'.$file_data['file_name'];
	
	$file_link = '/download/'.$file_data['file_id'];
			
	$PARS['{FILE_LINK}'] = $file_link;
			
	$file_item = fetch_tpl($PARS, $files_list_file_item_tpl);
	
	return $file_item;
}

// Получение блока статусов файла
function fill_file_statuses_block($file_id)
{
	global $site_db, $current_user_id;
	
	$file_statuses_cont_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/files/file_statuses_cont.tpl');
	
	$status_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/files/status_btn.tpl');
	
	// Список статусов
	$sql = "SELECT * FROM ".FILE_STATUS_DATA_TB." ORDER by status_sort ASC";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$PARS['{FILE_ID}'] = $file_id;
		
		$PARS['{STATUS_ID}'] = $row['status_id'];
		
		$PARS['{STATUS_NAME}'] = $row['status_name'];
		
		$btns_list .= fetch_tpl($PARS, $status_btn_tpl);
	}
	
	// Список статусов
	$statuses_list = fill_file_statuses_list($file_id);
	
	$PARS['{STATUS_BTNS}'] = $btns_list;
	
	$PARS['{STATUSES_LIST}'] = $statuses_list;
	
	return fetch_tpl($PARS, $file_statuses_cont_tpl);
}

// Список статусов для документа
function fill_file_statuses_list($file_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$file_status_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/files/file_status_item.tpl');
	
	$no_file_status_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/files/no_file_status.tpl');
	
	// Выбор статусов документа
	$sql = "SELECT i.*, j.status_name FROM ".FILE_STATUSES_TB." i, ".FILE_STATUS_DATA_TB." j 
			WHERE i.status_id=j.status_id AND i.file_id='$file_id' ORDER by i.id DESC";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$status_date = datetime($row['status_date'], '%d.%m.%y в %H:%i');
		
		// Не выводим название статуса Комментария
		$status_name = $row['status_id'] == 4 ? '' : $row['status_name'];
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row['user_id']);
		
		$PARS['{USER_ID}'] = $row['user_id'];
		
		$PARS['{NAME}'] = $user_obj->get_user_name();
		
		$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
		$PARS['{STATUS_ID}'] = $row['status_id'];
		
		$PARS['{STATUS_NAME}'] = $status_name;
		
		$PARS['{TEXT}'] = nl2br($row['status_text']);
		
		$PARS['{STATUS_DATE}'] = $status_date;
		
		$statuses_list .= fetch_tpl($PARS, $file_status_item_tpl);
	}
	
	if(!$statuses_list)
	{
		$statuses_list = $no_file_status_tpl;
	}
	
	return $statuses_list;
}


// Проверяет, является ли файл доступным для редактирования пользователем
function is_admin_file($file_id, $user_id)
{
	global $site_db, $current_user_id;
	
	// Данные файла
	$sql = "SELECT file_id, folder_id, user_id FROM ".FILES_TB." WHERE file_id='$file_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['file_id'] && $row['user_id']==$user_id)
	{
		return true;
	}
	// Если файл в папке, проверяем, является ли пользователем создателем папки
	else if($row['file_id'] && $row['folder_id'] > 0)
	{
		$sql = "SELECT user_id FROM ".FOLDERS_TB." WHERE folder_id='".$row['folder_id']."'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['user_id'] && $row['user_id']==$user_id)
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


// Проверка, является ли файл в общем доступе
function is_sharing_file($file_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT is_sharing, folder_id FROM ".FILES_TB." WHERE file_id='$file_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['is_sharing'])
	{
			return true;
	}
	else
	{
			return false;
	}
	
	return false;
}

// Проверяет, может ли просматривать папку пользователь
function folder_access_for_user($folder_id, $user_id)
{
	global $site_db, $current_user_id;
	
	$full_access = 0; // полный доступ к папке
	
	$read_full_access = 0; // папка доступна для просмотра полностью
	
	$read_part_access = 0; // папка доступна для просмотра частично, отображаются только файлы, разрешенные для чтения
	
	$sql = "SELECT user_id, is_sharing FROM ".FOLDERS_TB." WHERE folder_id='$folder_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['user_id']==$user_id)
	{
		$full_access = 1;
		$read_full_access = 1;
		$read_part_access = 1;
	}
	
	if(!$full_access)
	{
		// Проверяем, открыта ли для просмотра папка
		$sql = "SELECT id FROM ".FILES_ACCESS_TB." WHERE folder_id='$folder_id' AND file_id=0 AND user_id='".$user_id."'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			$read_full_access = 1;
			$read_part_access = 1;
		}
	}
	
	if(!$read_full_access)
	{
		// Выбор списка папок открытых для пользователя
		$sql = "SELECT i.id FROM ".FILES_ACCESS_TB." i
				LEFT JOIN ".FILES_TB." j ON i.file_id=j.file_id
				WHERE  i.user_id='$user_id' AND j.folder_id='$folder_id' AND i.file_id> 0 LIMIT 1";
					
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			$read_part_access = 1;
		}
	}
	
	return array('f' => $full_access, 'rf' => $read_full_access, 'rp' => $read_part_access, 's' => $is_sharing);
}
// Проверка, является ли папка в общем доступе
function is_sharing_folder($folder_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT is_sharing, folder_id FROM ".FOLDERS_TB." WHERE folder_id='$folder_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	// Если файл окажется в папке
	if($row['is_sharing'])
	{
		return true;
	}
	else
	{
		return false;
	}
	
}

// Заполняет блок доступа к файлам и папкам
function fill_file_access_block($file_id, $folder_id, $users_list, $create_user_id=0, $file_accessed_by_users_arr)
{
	global $site_db, $current_user_id;
	
	$files_list_users_access_block_tpl = file_get_contents('templates/files/files_list_users_access_block.tpl');
	
	$files_access_users_list_item_tpl = file_get_contents('templates/files/files_access_users_list_item.tpl');
	
	$file_no_users_to_access_tpl = file_get_contents('templates/files/file_no_users_to_access.tpl');
	
	$folder_id = $folder_id ? $folder_id : 0;
	
	$file_id = $file_id ? $file_id : 0;	
	
	foreach($users_list as $user_id => $user_data)
	{ 
		
		// Условия для файла
		if($file_id)
		{ 
			// Не выводим в списках для разрешения создателя файла\папки
			if($user_data['user_id']==$create_user_id && $user_data['user_id'])
			{  
				continue;
			}
		}
		
		// Условия для папки
		else if($folder_id)
		{
			// Не выводим в списках для разрешения создателя файла\папки
			if($user_data['user_id']==$create_user_id && $user_data['user_id'])
			{  
				continue;
			}
			
			// Массив доступа на папку для пользователя
			$folder_access_for_user_arr = folder_access_for_user($folder_id, $current_user_id);
			
			// Если не полный создатель файла и не полный доступ на папку, не выводим пользователей для дачи прав на папку
			if(!$folder_access_for_user_arr['f'] && !$folder_access_for_user_arr['rf'])
			{
				continue;
			}
			 
		}
		
		$access_active = '';
		
		// папка
		if($folder_id && $file_id=='0')
		{
			// Проверяем, открыт ли доступ к папке для пользователя
			$sql = "SELECT id FROM ".FILES_ACCESS_TB." WHERE folder_id='$folder_id' AND file_id=0 AND user_id='".$user_data['user_id']."'";
			
			$row = $site_db->query_firstrow($sql);
			
			if($row['id'])
			{
				$access_active = 'access_active';
			}
		}// файл
		else if($file_id)
		{
			// Проверяем, открыт ли доступ к файлу для пользователя
			$sql = "SELECT id FROM ".FILES_ACCESS_TB." WHERE file_id='$file_id' AND user_id='".$user_data['user_id']."'";
			
			$row = $site_db->query_firstrow($sql);
			
			if($row['id'])
			{
				$access_active = 'access_active';
			}
		}
		
		$PARS1['{ACCESS_ACTIVE}'] = $access_active;
		
		$PARS1['{FOLDER_ID}'] = $folder_id;
		
		$PARS1['{FILE_ID}'] = $file_id;
		
		$PARS1['{USER_ID}'] = $user_data['user_id'];
		
		$PARS1['{SURNAME}'] = $user_data['surname'];
		
		$PARS1['{NAME}'] = $user_data['name'];
				
		$PARS1['{MIDDLENAME}'] = $user_data['middlename'];
				
		$PARS1['{USER_POSITION}'] = $user_data['user_position'];
		  
		$users_access_list .= fetch_tpl($PARS1, $files_access_users_list_item_tpl);
	}
 	
	// Если нет пользователей для разрешения, не выводим блок Разрешения
	if(!$users_access_list)
	{
		return '';
	}
	
 	$users_access_list = $users_access_list ? $users_access_list : $file_no_users_to_access_tpl;
	
	$PARS['{USERS_LIST}'] = $users_access_list;
	
	$PARS['{FOLDER_ID}'] = $folder_id;
	
	$PARS['{FILE_ID}'] = $file_id;
	
	return fetch_tpl($PARS, $files_list_users_access_block_tpl);
}


// Является ли пользователь владельцем папки
function is_user_folder($folder_id, $user_id)
{
	global $site_db, $current_user_id;
	
	// Выбираем файл
	$sql = "SELECT folder_id FROM ".FOLDERS_TB." WHERE folder_id='$folder_id' AND user_id='$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['folder_id'])
	{
		return true;
	}
	else
	{
		return false;
	}
}

// Скачивание файла
function fill_download($file_id)
{
	global $site_db, $current_user_id;
	
	if(!$current_user_id)
	{
		exit();
	}
	// Данные файла
	$sql = "SELECT * FROM ".FILES_TB." WHERE file_id='$file_id'";
		
	$row = $site_db->query_firstrow($sql);
	 
	
	if(!$row['file_id'] || !$file_id)
	{
		header('Location: /files');
		exit();
	}
	
	// Если файл в общем доступе
	if($row['is_sharing'])
	{
		$access_true = 1;
	}
	// Если пользователь является создателем файла
	if($row['user_id']==$current_user_id)
	{
		$access_true = 1;
	}
	// Если файл открыт для доступа пользователю
	if(!$access_true)
	{
		$sql = "SELECT id FROM ".FILES_ACCESS_TB." WHERE file_id='$file_id' AND user_id='$current_user_id'";
				
		$r = $site_db->query_firstrow($sql);
				
		if($r['id'])
		{
			$access_true = 1;
		}
	}
	// Если папка, содержащая файл, открыта полностью для пользователя
	if(!$access_true && $row['folder_id'])
	{
		$folder_access_for_user_arr = folder_access_for_user($row['folder_id'], $current_user_id);
			
		if($folder_access_for_user_arr['rf'])
		{
			$access_true = 1;
		}
	}
	
	if(!$access_true)
	{
		header('Location: /files');
		exit();
	}
	
	// Формируем путь до файла
	$file_name = get_path_to_file($row);
 	
	$file_base_name =  iconv( 'cp1251', 'utf-8', $row['file_name']);
	
	// Даем файл на скачку
	file_download($file_name, '', $file_base_name);
}

function get_path_to_file($file_data)
{
	if($file_data['is_sharing'])
	{
		$folder_path = SHARING_PATH;
	}
	else
	{
		$folder_path = PRIVATE_PATH.'/'.$file_data['user_id'];
	}
	if($file_data['folder_id'])
	{
		$folder_path .= '/'.$file_data['folder_id'];
	}
	
	$file_name = $folder_path.'/'.$file_data['file_name'];
	
	return $file_name;
}
function file_download1($filename, $mimetype='application/octet-stream') {
  if (file_exists($filename)) {
// Отправляем требуемые заголовки
    header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
    header('Content-Type: ' . $mimetype);  
    header('Last-Modified: ' . gmdate('r', filemtime($filename)));
    header('ETag: ' . sprintf('%x-%x-%x', fileinode($filename), filesize($filename), filemtime($filename)));
    header('Content-Length: ' . (filesize($filename)));
    header('Connection: close');
	header('Content-Transfer-Encoding: binary');
    header('Content-Disposition: attachment; filename="sdsdf sdfgd fg g.jpg";');
  //  header('Content-Disposition: attachment; filename="' . basename($filename) . '";');
    echo file_get_contents($filename);
	
  } else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    header('Status: 404 Not Found');
  }
  exit;
}

// Сохранение файла
function file_download($filename, $mimetype='application/octet-stream', $file_name_for_out='') {
	
   if (file_exists($filename)) 
   {
	
 	if (ob_get_level()) {
      ob_end_clean();
    }
	
	 
	 
	$file_name_for_out = $file_name_for_out ? $file_name_for_out :  basename($filename); 
	
	 //$file_name_for_out = 'новый магнат.jpg';
	 
	  //$file_name_for_out = iconv( 'cp1251', 'utf-8',$file_name_for_out);
	 
	// заставляем браузер показать окно сохранения файла
   // header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream'); ///
 	header('Content-Disposition: attachment; filename="' . ($file_name_for_out).'" ');
	//header('Content-Disposition: attachment; filename="новый магнат.jpg" ');
    header('Content-Transfer-Encoding: binary');
	 
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    // читаем файл и отправляем его пользователю
    readfile($filename);
    exit;
	
   }
	/*
	
	
	
	
	 $file_name_for_out = $file_name_for_out ? $file_name_for_out :  basename($filename); 
     header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
	 
	 header("Pragma: public"); 
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // нужен для некоторых браузеров

     
header("Content-Type: $ctype");
header('Accept-Ranges: bytes'); 
 
 
header("Content-Length: ".filesize($filename)); // необходимо доделать подсчет размера файла по абсолютному пути
header ('Connection: close');
header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
file_get_contents("$filename");
	 exit();
	 
	 
	// Открываем искомый файл
     $f=fopen($filename, 'r');
     while(!feof($f)) 
	 {
	// Читаем килобайтный блок, отдаем его в вывод и сбрасываем в буфер
       echo fread($f, 1024);
       flush();
     }
	// Закрываем файл
     fclose($f);
   } 
   else 
   {
     header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
     header('Status: 404 Not Found');
   }*/
  // exit;
}

// Возвращает массив пользователей, которые давали другим пользователям доступ к папке
function get_folder_accessed_by_users_arr($folder_id)
{
	global $site_db, $current_user_id;
	 
	$sql = "SELECT access_by_user_id FROM ".FILES_ACCESS_TB." WHERE folder_id='$folder_id' AND file_id=0";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$users_access_arr[] = $row['access_by_user_id'];
	}
	
	return $users_access_arr;
}

// Возвращает массив пользователей, которые давали другим пользователям доступ к файлу
function get_file_accessed_by_users_arr($file_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT access_by_user_id FROM ".FILES_ACCESS_TB." WHERE file_id='$file_id'";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$users_access_arr[] = $row['access_by_user_id'];
	}
	
	return $users_access_arr;
}

// Кол-во новых доступных файлов для пользователя
function get_count_user_new_files($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM ".FILES_ACCESS_TB." WHERE user_id='$user_id' AND noticed = 0";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
	
}

// Возвращает 1, если есть новые уведомления по чужим файлам
function get_new_files_notice_for_user($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM ".FILES_ACCESS_TB." WHERE user_id='$user_id' AND noticed = 0";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['count'])
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

// Удалить доступ к файлам для пользователя, которые в папке
function delete_access_to_folder_files($folder_id, $user_id)
{
	global $site_db, $current_user_id;
	
	// Выбираем папку и все файлы, которые могли быть переданы пользователю, находящиеся в этой папке
	$sql = "SELECT * FROM ".FILES_ACCESS_TB." WHERE folder_id='$folder_id' AND user_id='$current_user_id'";
			
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$sql = "DELETE FROM ".FILES_ACCESS_TB." WHERE id='".$row['id']."'";
				 
		$site_db->query($sql);
	}
	
}
?>