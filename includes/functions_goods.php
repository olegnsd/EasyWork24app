<?php
// Страница клиентов сотрудника
function fill_goods($user_id)
{
	global $site_db, $current_user_id, $max_upload_goods_image_resolution, $min_upload_goods_image_resolution, $_CURRENT_USER_WORKERS_ARR;
	
	$goods_tpl = file_get_contents('templates/goods/goods.tpl');
	
	$goods_top_menu_tpl = file_get_contents('templates/goods/goods_top_menu.tpl');
 
 	$workers_goods_top_menu_tpl = file_get_contents('templates/goods/workers_goods_top_menu.tpl');
	
	$good_add_form_tpl = file_get_contents('templates/goods/good_add_form.tpl');
	
	$more_goods_btn_tpl = file_get_contents('templates/goods/more_goods_btn.tpl');
	
	$no_goods_tpl  = file_get_contents('templates/goods/no_goods.tpl');
	
	### Верхнее меню
	// Подсвечивает табы
	if($current_user_id==$user_id)
	{
		$active_menu_1 = 'menu_active';
	}
	else if($_GET['wks'])
	{
		$active_menu_2 = 'menu_active';
	}
 	// Если у пользователя нет сотрудников, не выводим раздел "Клиенты моих сотурдников"
	if($_CURRENT_USER_WORKERS_ARR)
	{
		$workers_goods_top_menu = $workers_goods_top_menu_tpl;
	}
	
	$PARS_1['{WORKERS_GOODS_TOP_MENU}'] = $workers_goods_top_menu;
	
	$PARS_1['{CURRENT_USER_ID}'] = $current_user_id;
	
	$PARS_1['{ACTIVE_1}'] = $active_menu_1;
	
	$PARS_1['{ACTIVE_2}'] = $active_menu_2; 
	
	// Верхнее меню
	$top_menu = fetch_tpl($PARS_1, $goods_top_menu_tpl);
	
	// Очистка массива удаленных контактов
	if($_SESSION['good_deleted'])
	{
		$_SESSION['good_deleted'] = '';
	}
	
	// Выбираем последнее добавленное имущество
	$sql = "SELECT good_id FROM ".GOODS_TB." WHERE good_deleted<>1 ORDER by good_id DESC LIMIT 1";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['good_id'])
	{
		$_SESSION['last_user_good_id'] = $row['good_id'];
	}
	
	
	
	// Выводим форму добавления клиента для своей страницы
	if($current_user_id==$user_id)
	{  
		$good_add_form = $good_add_form_tpl;
	}	
	
	// Строка навигации
	$nav = fill_nav('goods');
	
	if($_GET['wks'])
	{
		// Список имущества моих сотрудников
		$goods_list = fill_all_user_workers_goods_list($user_id);
		
		if(!$goods_list)
		{
			$goods_list = $no_goods_tpl;
		}
	}
	else
	{
		// Кол-во клиентов
		$goods_count = get_user_goods_count($user_id);
		
		// Кол-во страниц
		$pages_count = ceil($goods_count/GOODS_PER_PAGE);
		
		// Если страниц больше 1
		if($pages_count > 1)
		{
			$more_goods_btn = $more_goods_btn_tpl;
		}
		
		// Список имущества
		$goods_list = fill_goods_list($user_id, 1);
		
		if(!$goods_list)
		{
			$goods_list = $no_goods_tpl;
		}
	}
	 
	
	$PARS['{NAV}'] = $nav;
	
	$PARS['{TOP_MENU}'] = $top_menu;
	
	$PARS['{MORE_GOODS}'] = $more_goods_btn;
	
	$PARS['{GOOD_ADD_FORM}'] = $good_add_form;
	
	$PARS['{GOODS_LIST}'] = $goods_list;
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	$PARS['{MAX_IMAGE_RESOLUTION}'] = $max_upload_goods_image_resolution;
	
	$PARS['{MIN_IMAGE_RESOLUTION}'] = $min_upload_goods_image_resolution;
	
	return fetch_tpl($PARS, $goods_tpl);

}


// Список имущества
function fill_goods_list($user_id, $page=1)
{
	global $site_db, $current_user_id;
	
	// Страничность
	$begin_pos = GOODS_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".GOODS_PER_PAGE;
	
	// Получает массив пользователей, относящихся к пользователю (начальники и подчиненные)
	$users_for_access_arr = get_current_user_users_arrs(array(1,1,0,0,0), 1);
	
	
	// Удаленные в этой сессии клиенты
	$deleted_goods_ids = implode(', ', $_SESSION['good_deleted']);
	
	if($deleted_goods_ids)
	{
		$and_deleted_goods = " OR i.good_id IN($deleted_goods_ids) ";
	}
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_user_good_id'])
	{
		$and_goods_id = " AND i.good_id <= '".$_SESSION['last_user_good_id']."' ";
	}
	
	// Если пользователь просматривает свое имущество
	if($user_id==$current_user_id)
	{
		$or_user_new = " OR good_to_new_owner_id='$current_user_id'";
	}
	
	// Не выводить элементы подтверждения о имуществе, если пользователь просматривает чужую страницу
	$without_confirm = 0;
	if($user_id!=$current_user_id)
	{
		$without_confirm = 1;
	}
	
	$without_take_away = 0;
	if($user_id==$current_user_id)
	{
		$without_take_away = 1;
	}
	
	// Имущество пользователя
	if($user_id)
	{
		
		$sql = "SELECT * FROM ".GOODS_TB." i
				WHERE (good_owner_user_id='$user_id' $or_user_new) AND (i.good_deleted<>1 $and_deleted_goods) $and_goods_id 
				ORDER by good_price DESC $limit";
	}
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		// Заполнение элемента клиента
		$goods_list .= fill_good_list_item($row, 0, 0, $users_for_access_arr, $without_confirm, $without_take_away);
	}
	
	return 	$goods_list;
}

// Заполнение элемента имущества в списке
// $good_data - данные имущества
// $edit_form - форма для редактирования ?
// $mode - мод
// $users_for_access_arr - список всех пользователей, относящихся к текущему пользователю (начальнки и подчиненные)
// $without_confirm - не выводить элементы для подтверждения принятия имущества
// $without_take_away - не выводить кнопку ЗАБРАТЬ ИМУЩЕСТВО
function fill_good_list_item($good_data, $edit_form=0, $mode = 0, $users_for_access_arr, $without_confirm = 0, $without_take_away)
{
	global $site_db, $current_user_id, $user_obj;
	
	$goods_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/goods_list_item.tpl');
	
	// 
	if($mode==1)
	{
		$goods_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/goods_list_workers_item.tpl');
	}
	else
	{
		$goods_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/goods_list_item.tpl');
	}
	
	$goods_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/goods_list_item.tpl');
	
	$goods_list_item_image_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/goods_list_item_image.tpl');
	
	$goods_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/goods_edit_tools.tpl');
	
	$good_confirm_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/good_confirm_btn.tpl');
	
	$take_away_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/take_away_btn.tpl');
	 
	// Изображения имущества
	$sql = "SELECT * FROM ".GOODS_IMAGES_TB." WHERE good_id='".$good_data['good_id']."' ORDER by image_id ASC ";
	
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{
		$image_src =  get_file_dir_url($row['date_add'], $row['image_name']);
		
		// Если найдено изображение
	 	//$image_src = '/'.UPLOAD_FOLDER.'/goods/'.$good_data['good_id'].'/'.$row['image_name'];
		
		$PARS_1['{GOOD_ID}'] = $good_data['good_id'];
		
		$PARS_1['{IMAGE_SRC}'] = $image_src;
		
		$images_list .= fetch_tpl($PARS_1, $goods_list_item_image_tpl);
	}
	
	// Проверяем на возможность редактировать имущество
	if($current_user_id==$good_data['good_create_user_id'] || check_user_access_to_user_content($good_data['good_create_user_id'], array(0,1,0,0,1)))
	{
		$PARS_2['{GOOD_ID}'] = $good_data['good_id'];
		
		$edit_tools = fetch_tpl($PARS_2, $goods_edit_tools_tpl);
	}
	
	$good_not_confirm_class = '';
	$hidden_owner_block_class='';
	$good_confirm_btn = '';
	
	// Подсвечиваем фон для неподтвержденных имуществ
	if($good_data['good_to_new_owner_id']==$current_user_id && !$without_confirm && !$_GET['wks'])
	{
		$good_not_confirm_class = 'not_confirm';
		
		$hidden_owner_block_class = 'display_none';
		
		$PARS_1['{GOOD_ID}'] = $good_data['good_id'];
		
		$good_confirm_btn = fetch_tpl($PARS_1, $good_confirm_btn_tpl);
	}
	
	if($good_data['good_owner_user_id']==$current_user_id || $good_data['good_to_new_owner_id']==$current_user_id)
	{
		// Блок передачи имущества
		$good_owner_block = fill_good_owner_block($good_data['good_id'], $users_for_access_arr);
	}
	
	// Блок истории имущества
	$good_history_block = fill_good_history_block($good_data['good_id']);
	
	 
	// При просмотре другого пользователя если текущий пользователь является начальником
	if(check_user_access_to_user_content($good_data['good_owner_user_id'], array(0,1,0,0,0)) && !$without_take_away)
	{
		$PARS_1['{GOOD_ID}'] = $good_data['good_id'];
		
		$take_away_btn = fetch_tpl($PARS_1, $take_away_btn_tpl);
	}
	
	// не выводим пустые поля
	$good_price_display = $good_data['good_price'] == '' ? 'display:none' : '';
	
	// Блок отчетов
	$report_block = fill_good_report_block($good_data);
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($good_data['good_create_user_id']);
	
	$create_user_id = $good_data['good_create_user_id'];
	
	$creat_user_surname = $user_obj->get_user_surname();
	
	$creat_user_name = $user_obj->get_user_name();
	
	$creat_user_middlename = $user_obj->get_user_middlename();
	
	$creat_user_position = $user_obj->get_user_position();
	
	$PARS['{CREATER_USER_ID}'] = $create_user_id;
			
	$PARS['{CREATER_USER_NAME}'] = $creat_user_name;
		
	$PARS['{CREATER_USER_MIDDLENAME}'] = $creat_user_middlename;
			
	$PARS['{CREATER_USER_SURNAME}'] = $creat_user_surname;
			
	$PARS['{CREATER_USER_POSITION}'] = $creat_user_position;
	
	$PARS['{GOOD_ID}'] = $good_data['good_id'];
	
	$PARS['{GOOD_NAME}'] = $good_data['good_name'];
	
	$PARS['{GOOD_PRICE}'] =  number_format($good_data['good_price'], 2, '.', ' '); 
	
	$PARS['{IMAGES_LIST}'] = $images_list;
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	$PARS['{GOOD_ACCESS_BLOCK}'] = $good_owner_block;
	
	$PARS['{GOOD_NOT_CONFIRM_CLASS}'] = $good_not_confirm_class;
	
	$PARS['{GOOD_CONFIRM_BTN}'] = $good_confirm_btn;
	
	$PARS['{HIDDEN_OWNER_BLOCK_CLASS}'] = $hidden_owner_block_class;
	
	$PARS['{GOOD_HISTORY}'] = $good_history_block;
	
	$PARS['{TAKE_AWAY_BTN}'] = $take_away_btn;
	
	$PARS['{GOOD_NAME_DISPLAY}'] = $good_price_display;
	
	$PARS['{REPORT_BLOCK}'] = $report_block;
	
	return fetch_tpl($PARS, $goods_list_item_tpl);
	
}

// Возвращает список отчетов для имущества
function fill_good_report_block($good_data, $add_form=0, $is_boss)
{
	global $site_db, $current_user_id;
	
	$good_id = $good_data['good_id'];
		
	$report_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/report_block.tpl');
	
	// Список отчетов
	$report_list = fill_good_reports_list($good_id);

	// Не выводим форму добавления отчета другим пользователям
	$add_report_form_display = $current_user_id==$good_data['good_owner_user_id'] ? 'block' : 'none';

	$PARS['{GOOD_ID}'] = $good_id;
	
	$PARS['{REPORT_LIST}'] = $report_list;
	
	$PARS['{ADD_REPORT_FORM_DISPLAY}'] = $add_report_form_display;
	
	$report_block = fetch_tpl($PARS, $report_block_tpl);
	
	return $report_block;
}


// Список отчетов для задания
function fill_good_reports_list($good_id, $report_id)
{
	global $site_db,  $user_obj, $current_user_id;
	
	$report_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/report_item.tpl');
	
	$report_no_reports_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/report_no_reports.tpl');
	
	if($report_id)
	{
		$and_report_id = " AND report_id='$report_id'";
	}
	// Выбор отчетов
	$sql = "SELECT * FROM ".GOODS_REPORTS_TB." WHERE good_id='$good_id' $and_report_id ORDER by report_id ASC";
	
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{ 
		$user_obj->fill_user_data($row['user_id']);
		
		// Превью аватарки пользователя
		$user_avatar_src = get_user_preview_avatar_src($row['user_id'], $user_obj->get_user_image());
	
		$PARS['{USER_ID}'] = $row['user_id'];
		
		$PARS['{NAME}'] = $user_obj->get_user_name();
		
		$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{AVATAR_SRC}'] = $user_avatar_src;
		
		$PARS['{TASK_DATE}'] = datetime($row['report_date'], '%j %M в %H:%i');
		
		$PARS['{TASK_TEXT}'] = stripslashes(nl2br($row['report_text']));
		
		$PARS['{REPORT_ID}'] = $row['report_id'];
		
		$PARS['{TASK_ID}'] = $row['task_id'];
		
		$PARS['{REPORT_CLASS}'] = $report_class;
		
		$PARS['{CONFIRM_BTN}'] = $confirm_btn;
		
		$PARS['{REPORT_NOT_CONFIRM_CLASS}'] = $report_not_confirm;
		
		$report_list .= fetch_tpl($PARS, $report_item_tpl);
	}
	
	if(!$report_list)
	{
		$report_list = $report_no_reports_tpl;
	}
	
	return $report_list;
}

// Список имущества моих сотрудников 
function fill_all_user_workers_goods_list($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$worker_good_block_tpl = file_get_contents('templates/goods/worker_good_block.tpl');
	
	$workers_arr = get_current_user_users_arrs(array(0,1,0,0,0));
	$workers_users_ids = implode(',', $workers_arr);
	
	// Контакты пользователя
	if($workers_users_ids)
	{
		// Выбираем контакты пользователя
		$sql = "SELECT * FROM ".GOODS_TB." WHERE good_owner_user_id IN($workers_users_ids) AND good_deleted<>1 ORDER by good_id DESC";
	}
	else
	{
		return '';
	}
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res, 1))
	{
		$workers_goods_arr[$row['good_owner_user_id']][] = $row;
	}
	
	foreach($workers_goods_arr as $worker_id => $worker_goods_arr)
	{
		$goods_list = '';
		
		foreach($worker_goods_arr as $good_data)
		{
			// Заполнение элемента имущества
			$goods_list .= fill_good_list_item($good_data, 0, 1);
		}
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($worker_id);
		
		$PARS['{USER_ID}'] = $worker_id;
			
		$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
		$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
		$PARS['{USER_USER_POSITION}'] = $user_obj->get_user_position();
	
		$PARS['{GOODS_LIST}'] = $goods_list;
		
		$workers_goods_list .= fetch_tpl($PARS, $worker_good_block_tpl);
	}
	
	return 	$workers_goods_list;
}

// Блок истории имущества
function fill_good_history_block($good_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$good_history_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/good_history_block.tpl');
	
	$good_history_block_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/good_history_block_item.tpl');
	
	$sql = "SELECT * FROM ".GOODS_OWNERS_TB." WHERE good_id='$good_id' ORDER by id DESC";
	
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res, 1))
	{
		$history_users_arr[] = array('user_id' => $row['user_id'], 'data' => $row);
	}
	
	// Не выводим в истории действующего владельца
	$history_users_arr = array_slice($history_users_arr, 1 , count($history_users_arr));
	
	// Формируем историю
	foreach($history_users_arr as $row)
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row['data']['user_id']);
		
		$PARS_1['{USER_ID}'] = $row['data']['user_id'];
			
		$PARS_1['{USER_NAME}'] = $user_obj->get_user_name();
		 
		$PARS_1['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
		$PARS_1['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
		$PARS_1['{USER_POSITION}'] = $user_obj->get_user_position();
		 
		$PARS_1['{DATE}'] = datetime($row['data']['owner_date_confirm'],'%d.%m.%y');
		
		$history_list .= fetch_tpl($PARS_1, $good_history_block_item_tpl);
	}
	
	$PARS['{GOOD_ID}'] = $good_id;
	
	$PARS['{HISTORY_LIST}'] = $history_list;
	
	// Если история пуста
	if(!$history_list)
	{
		return '';
	}
	else
	{
		return fetch_tpl($PARS, $good_history_block_tpl);
	}
}

// Заполняет блок доступа к файлам и папкам
function fill_good_owner_block($good_id, $users_list)
{
	global $site_db, $current_user_id;
	
	$users_owner_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/users_owner_block.tpl');
	
	$users_owner_block_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/users_owner_block_item.tpl');

	foreach($users_list as $user_id => $user_data)
	{ 
		// Выбираем последнего владельца имущества
		$sql = "SELECT * FROM ".GOODS_TB." WHERE good_id='$good_id'";
		
		$good_data = $site_db->query_firstrow($sql);
		
		$access_active = '';
		
		if($good_data['good_to_new_owner_id'] && $good_data['good_to_new_owner_id']==$user_data['user_id'])
		{
			$access_active = 'access_active';
		}
		 
		
		//$access_active = 'files_access_active';
		
		$PARS1['{ACCESS_ACTIVE}'] = $access_active;
		
		$PARS1['{GOOD_ID}'] = $good_id;
		
		$PARS1['{USER_ID}'] = $user_data['user_id'];
		
		$PARS1['{SURNAME}'] = $user_data['surname'];
		
		$PARS1['{NAME}'] = $user_data['name'];
				
		$PARS1['{MIDDLENAME}'] = $user_data['middlename'];
				
		$PARS1['{USER_POSITION}'] = $user_data['user_position'];
		  
		$users_owner_list .= fetch_tpl($PARS1, $users_owner_block_item_tpl);
	}
 
 	$users_list = $users_list ? $users_list : 'Нет пользователей для добавления';
	
	$PARS['{USERS_LIST}'] = $users_owner_list;
	
	$PARS['{GOOD_ID}'] = $good_id;
	
	return fetch_tpl($PARS, $users_owner_block_tpl);
}

// Страница релактирования имущества
function fill_good_edit($good_id)
{
	global $site_db, $current_user_id, $max_upload_goods_image_resolution, $min_upload_goods_image_resolution;
	
	$main_tpl = file_get_contents('templates/goods/good_edit.tpl');
	
	$good_edit_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/good_edit_form.tpl');
	
	$good_edit_images_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/goods/good_edit_images_list_item.tpl');
	
	// Строка навигации
	$nav = fill_nav('goods');
	
	// Выбораем данные сделки
	$sql = "SELECT * FROM ".GOODS_TB." WHERE good_id='$good_id'";
	
	$good_data = $site_db->query_firstrow($sql);
	
	if(!$good_data['good_id'] || $good_data['good_deleted']==1)
	{
		header('Location: /goods');
	}
	

	if($current_user_id!=$good_data['good_create_user_id'] && !check_user_access_to_user_content($good_data['good_create_user_id'], array(0,1,0,0,1)))
	{
		header('Location: /goods/'.$current_user_id);
	}
	
	
	// Изображения имущества
	$sql = "SELECT * FROM ".GOODS_IMAGES_TB." WHERE good_id='$good_id' ORDER by image_id ASC ";
	
	$res = $site_db->query($sql);
	
	$num = 1;
	
	while($row=$site_db->fetch_array($res))
	{
		// Если найдено изображение
	 
	 	$image_src =  get_file_dir_url($row['date_add'], $row['image_name']);
		
		//$image_src = '/'.UPLOAD_FOLDER.'/goods/'.$good_data['good_id'].'/'.$row['image_name'];
		
		$PARS_1['{GOOD_ID}'] = $good_data['good_id'];
		
		$PARS_1['{IMAGE_SRC}'] = $image_src;
		
		$PARS_1['{IMAGE_ID}'] = $row['image_id'];
		
		$PARS_1['{NUM}'] = $num;
		
		$num++;
		
		$images_list .= fetch_tpl($PARS_1, $good_edit_images_list_item_tpl);
	}
	
	$PARS['{GOOD_ID}'] = $good_id;
	
	$PARS['{GOOD_NAME}'] = $good_data['good_name'];
	
	$PARS['{GOOD_PRICE}'] = $good_data['good_price'];
	
	$PARS['{IMAGES_LIST}'] = $images_list;
	
	// Заполнение формы редактирования имущества
	$good_edit_form = fetch_tpl($PARS, $good_edit_form_tpl);
		
		
		
	$PARS['{NAV}'] = $nav;
	
	$PARS['{GOOD_EDIT_FORM}'] = $good_edit_form;

	$PARS['{MAX_IMAGE_RESOLUTION}'] = $max_upload_goods_image_resolution;
	
	$PARS['{MIN_IMAGE_RESOLUTION}'] = $min_upload_goods_image_resolution;
	
	return fetch_tpl($PARS, $main_tpl);
}

// Проверка, может ли пользователь, который просматривает - редактировать клиента приватности для вывода кнопки удаления
function is_good_open_for_edit_for_user($user_id, $good_data)
{
	global $current_user_id;
	
	// Для создателя сделки, начальников или всем(если не отмечен чекбокс запретить редактирование всем, крмое вышестоящих сотрудников) выводим форму редактирования. Кнопка редактирования не выводится так же, если отмечен чекбокс НЕ ПОКАЗЫВАТЬ ДАННЫЕ
	if($user_id == $good_data['good_create_user_id'])
	{
		return true;
	}
	else
	{
		return false;
	}
}

// Кол-во имуществ, которыми владеет пользователь
function get_user_goods_count($user_id)
{
	global $site_db, $current_user_id;
	
	// Если пользователь просматривает свое имущество
	if($user_id==$current_user_id)
	{
		$or_user_new = " OR good_to_new_owner_id='$current_user_id'";
	}
	
	$sql = "SELECT COUNT(*) as count FROM ".GOODS_TB." WHERE (good_owner_user_id='$user_id' $or_user_new) AND good_deleted<>1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Кол-во новых имуществ для пользователя
function get_new_goods_count_for_users($user_id)
{
	global $site_db;
	
	$sql = "SELECT COUNT(*) as count FROM ".GOODS_TB." WHERE good_to_new_owner_id='$user_id' AND good_deleted<>1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

function add_to_good_owners($good_id, $user_id)
{
	global $site_db;
	
	// Добавляем в таблицу держателя имущества
	$sql = "INSERT INTO ".GOODS_OWNERS_TB." SET good_id='$good_id', user_id='$user_id', owner_date_confirm = NOW()";
			
	$site_db->query($sql);
}
?>