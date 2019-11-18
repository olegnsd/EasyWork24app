<?php
#### Контакты
function get_contact_upload_path($contact_id)
{
	global $site_db, $current_user_id;
	
	return CONTACTS_PATH.'/'.$contact_id; 
}

function get_contact_photo_url($contact_id)
{
	global $site_db, $current_user_id;
	
	return  '/'.UPLOAD_FOLDER.'/contacts/'.$contact_id;
}

function contact_mkdir($contact_id)
{
	$dir = get_contact_upload_path($contact_id);
	
	if(!is_dir($di))
	{
		mkdir($dir, 0775);
	}
}

function fill_contacts($user_id)
{
	global $site_db, $current_user_id, $max_upload_goods_image_resolution, $min_upload_goods_image_resolution;
	
	setlocale(LC_ALL, 'ru_RU.CP1251');
	 
	$contacts_tpl = file_get_contents('templates/contacts/contacts.tpl');
	
	$contacts_add_form_tpl = file_get_contents('templates/contacts/contacts_add_form.tpl');
	
	$contacts_search_form_tpl = file_get_contents('templates/contacts/contacts_search_form.tpl');
	
	$no_contacts_tpl = file_get_contents('templates/contacts/no_contacts.tpl');
	
	$more_contacts_btn_tpl = file_get_contents('templates/contacts/more_contacts_btn.tpl');
	
	$is_wks = $_GET['wks'] ? 1 : 0;
	
	// Выбираем последний добавленный пользователем контакт
	$sql = "SELECT contact_id FROM ".CONTACTS_TB." WHERE user_id='$user_id' AND contact_deleted<>1 ORDER by contact_id DESC LIMIT 1";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['contact_id'])
	{
		$_SESSION['last_user_contact_id'] = $row['contact_id'];
	}
	
	// Очистка массива удаленных контактов
	if($_SESSION['contact_deleted'])
	{
		$_SESSION['contact_deleted']='';
	}

	if($_GET['wks'])
	{
		// Список контактов Подчиненных
		$contacts_list = fill_workers_contacts_list();
		
		// Кол-во контактов
		$contacts_count = get_workers_contacts_count();
	}
	else
	{
		// Список контактов
		$contacts_list = fill_contacts_list($current_user_id);
		
		// Кол-во контактов пользователя
		$contacts_count = get_user_contacts_count($user_id);
	}

	
	if(!$contacts_list)
	{
		$contacts_list = $no_contacts_tpl;
	}
	
	if($user_id==$current_user_id)
	{
		$PARS_1['{USER_ID}'] = $user_id;
	
		// Форма добавления контакта
		$contact_add_form = fetch_tpl($PARS_1, $contacts_add_form_tpl);
	}
	
	$contacts_search_form = $contacts_search_form_tpl;
		


	
	// Кол-во страниц
	$pages_count = ceil($contacts_count/CONTACTS_PER_PAGE);
	
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_contacts = $more_contacts_btn_tpl;
	} 
	
	// Верхнее меню
	$top_menu = fill_contacts_top_menu();
	
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{MORE_CONTACTS}'] = $more_contacts;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	$PARS['{CONTACTS_SEARCH_FORM}'] = $contacts_search_form;
	
	$PARS['{CONTACTS_LIST}'] = $contacts_list;
	
	$PARS['{CURRENT_USER_ID}'] = $current_user_id;
	
	$PARS['{CONTACT_ADD_FORM}'] = $contact_add_form;
	
	$PARS['{TOP_MENU}'] = $top_menu;
	
	$PARS['{IS_WKS}'] = $is_wks;
	
	// Подсвечивает табы
	if($current_user_id==$user_id)
	{
		$active_menu_1 = 'menu_active';
	}
	else if(!$user_id)
	{
		$active_menu_2 = 'menu_active';
	}
	$PARS['{ACTIVE_1}'] = $active_menu_1;
	$PARS['{ACTIVE_2}'] = $active_menu_2;
	
	$PARS['{MAX_IMAGE_RESOLUTION}'] = $max_upload_goods_image_resolution;
	
	$PARS['{MIN_IMAGE_RESOLUTION}'] = $min_upload_goods_image_resolution;
	
	return fetch_tpl($PARS, $contacts_tpl);
}

// Верхнее меню
function fill_contacts_top_menu()
{
	global $site_db, $current_user_id, $_CURRENT_USER_WORKERS_ARR;
	
	$top_menu_tpl = file_get_contents('templates/contacts/top_menu.tpl');
	
	$top_menu_workers_tpl = file_get_contents('templates/contacts/top_menu_workers.tpl');
	
	// Если у пользователя нет сотрудников, не выводим раздел "Клиенты моих сотурдников"
	if(get_current_user_users_arrs(array(0,1,0,0,1)))
	{
		$workers_top_menu = $top_menu_workers_tpl;
	}
	
	if($_GET['wks'])
	{
		$active_menu_2 = 'menu_active';
	}
	else
	{
		$active_menu_1 = 'menu_active';
		
	}
	
	$PARS_1['{WORKERS_TOP_MENU}'] = $workers_top_menu;
	
	$PARS_1['{ACTIVE_1}'] = $active_menu_1;
	
	$PARS_1['{ACTIVE_2}'] = $active_menu_2;
		
	return fetch_tpl($PARS_1, $top_menu_tpl);
}

function fill_workers_contacts_list($page=1, $search_word='')
{
	global $site_db, $current_user_id;
	
	// Список сотрудников
	$workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	
	// Страничность
	$begin_pos = CONTACTS_PER_PAGE * ($page-1);
	$limit = " LIMIT ".$begin_pos.",".CONTACTS_PER_PAGE;
	
	// Удаленные в этой сессии контакты
	$deleted_contacts_ids = implode(', ',$_SESSION['contact_deleted']);
	
	if($deleted_contacts_ids)
	{
		$and_contacts = " OR contact_id IN($deleted_contacts_ids) ";
	}
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_user_contact_id'])
	{
		$and_contact_id = " AND contact_id <= '".$_SESSION['last_user_contact_id']."' ";
	}
	
	// Если поиск контактов
	$search_word_s = get_part_search_contact_query($search_word);
		
	if($workers_arr)
	{
		$users_ids = implode(',', $workers_arr);
		
		$sql = "SELECT * FROM ".CONTACTS_TB." WHERE user_id IN($users_ids) AND (contact_deleted<>1 $and_contacts) $search_word_s ORDER by contact_id DESC $limit";
	}
	
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$contacts_list .= fill_contacts_list_item($row, 0, $search_word, '');
		
	}
	
	return $contacts_list;
}

// Список контактов
function fill_contacts_list($user_id, $page=1, $search_word='')
{
	global $site_db, $current_user_id;
	
	// Страничность
	$begin_pos = CONTACTS_PER_PAGE * ($page-1);
	$limit = " LIMIT ".$begin_pos.",".CONTACTS_PER_PAGE;
	
	// Удаленные в этой сессии контакты
	$deleted_contacts_ids = implode(', ',$_SESSION['contact_deleted']);
	
	if($deleted_contacts_ids)
	{
		$and_contacts = " OR contact_id IN($deleted_contacts_ids) ";
	}
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_user_contact_id'])
	{
		$and_contact_id = " AND contact_id <= '".$_SESSION['last_user_contact_id']."' ";
	}
	// get_current_user_users_arrs
	// Если поиск контактов
	$search_word_s = get_part_search_contact_query($search_word);

	// Выбираем контакты пользователя
	$sql = "SELECT * FROM ".CONTACTS_TB." WHERE user_id='$user_id' AND (contact_deleted<>1 $and_contacts) $and_contact_id $search_word_s ORDER by contact_id DESC $limit";
	
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$contacts_list .= fill_contacts_list_item($row, 0, $search_word, '');
		
	}
	
	return $contacts_list;
}

// Заполняет элемент контакта
function fill_contacts_list_item($contact_data, $edit_form=0, $search_word, $num)
{
	global $site_db, $current_user_id, $user_obj;
	
	$contacts_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/contacts/contacts_list_item.tpl');
	
	$contacts_list_item_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/contacts/contacts_list_item_edit.tpl');
	
	$contact_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/contacts/contact_edit_tools.tpl');
	
	$image_delete_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/contacts/image_delete_btn.tpl');
	
	$image_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/contacts/image_block.tpl');
	
	foreach($contact_data as $i => $j)
	{
		$contact_data[$i] = stripslashes($j);
	}
	
	// Для создателя контакта, выводим форму редактирования
	if($contact_data['user_id'] == $current_user_id)
	{
		$PARS_1['{CONTACT_ID}'] =  $contact_data['contact_id'];
		
		$PARS_1['{USER_ID}'] = $contact_data['user_id'];
		
		$contact_edit_tools = fetch_tpl($PARS_1, $contact_edit_tools_tpl);
	}
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($contact_data['user_id']);
		
	$PARS['{USER_NAME}'] = $user_obj->get_user_name();
	
	$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
		
	$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
		
	$PARS['{USER_USER_POSITION}'] = $user_obj->get_user_position();
		
	// Подсвечиваем слова
	$contact_user_name = !$edit_form ? $contact_data['contact_user_name'] : $contact_data['contact_user_name'];
	
	$contact_name = !$edit_form ? $contact_data['contact_name']: $contact_data['contact_name'];
	
	$contact_phone = !$edit_form ? $contact_data['contact_phone']: $contact_data['contact_phone'];
	
	$contact_organization = !$edit_form ? $contact_data['contact_organization']: $contact_data['contact_organization'];
	
	$contact_desc = !$edit_form ? $contact_data['contact_desc']: $contact_data['contact_desc'];
	
	// не выводим пустые поля
	$user_name_display = $contact_data['contact_user_name'] == '' ? 'display:none' : '';
	$contact_name_display = $contact_data['contact_name'] == '' ? 'display:none' : '';
	$phone_display = $contact_data['contact_phone'] == '' ? 'display:none' : '';
	$organization_display = $contact_data['contact_organization'] == '' ? 'display:none' : '';
	$job_display = $contact_data['contact_job'] == '' ? 'display:none' : '';
	$desc_display = $contact_data['contact_desc'] == '' ? 'display:none' : '';

	// Изображение контакта
	if($contact_data['contact_image'])
	{
		
		//$image_src = get_contact_photo_url($contact_data['contact_id']).'/'.$contact_data['contact_image'];
		//$file_date, $image_name
		$image_src =  get_file_dir_url($contact_data['image_date_add'], $contact_data['contact_image']);
		
		// Изображение
		$image = '<image src="'.$image_src.'">';
		
		// Блок изображения при просмотре контакта
		$PARS['{CONTACT_ID}'] = $contact_data['contact_id'];
		$PARS['{IMAGE_SRC}'] = $image_src;
		
		$image_block = fetch_tpl($PARS, $image_block_tpl);
		
		// Кнопка удалить изображение
		$edit_image_delete = fetch_tpl($PARS, $image_delete_btn_tpl);
	}
	
	
	$PARS['{EDIT_IMAGE_DELETE}'] = $edit_image_delete;
	$PARS['{USER_NAME_DISPLAY}'] = $user_name_display;
	$PARS['{CONTACT_NAME_DISPLAY}'] = $contact_name_display;
	$PARS['{PHONE_DISPLAY}'] = $phone_display;
	$PARS['{ORGANIZATION_DISPLAY}'] = $organization_display;
	$PARS['{JOB_DISPLAY}'] = $job_display;
	$PARS['{DESC_DISPLAY}'] = $desc_display;
	
	$PARS['{EDIT_TOOLS}'] = $contact_edit_tools;
	
	$PARS['{CONTACT_ID}'] = $contact_data['contact_id'];
	
	$PARS['{USER_ID}'] = $contact_data['user_id'];
	
	$PARS['{CONTACT_USER_NAME}'] = $contact_user_name;
	
	$PARS['{CONTACT_NAME}'] = $contact_name;
	
	$PARS['{CONTACT_PHONE}'] = $contact_phone;
	
	$PARS['{CONTACT_ORGANIZATION}'] = $contact_organization;
	
	$PARS['{CONTACT_JOB}'] = $contact_data['contact_job'];
	
	$PARS['{CONTACT_DESC}'] = !$edit_form ? nl2br($contact_desc) : $contact_desc;
	
	$PARS['{LIST_ITEM_CLASS}'] = $list_item_class;
	
	$PARS['{IMAGE}'] = $image;
	
	$PARS['{IMAGE_BLOCK}'] = $image_block;
	
	// Форма для редактирования
	if($edit_form)
	{ 
		return fetch_tpl($PARS, $contacts_list_item_edit_tpl);
	}
	else
	{
		return fetch_tpl($PARS, $contacts_list_item_tpl);
	}
}


// ПРоверка, является ли пользователь создателем контакта
function is_contact_user_id($contact_id, $user_id)
{
	global $site_db, $current_user_id;
	
	// Выбираем контакт
	$sql = "SELECT contact_id FROM ".CONTACTS_TB." WHERE contact_id='$contact_id' AND user_id='$user_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['contact_id'])
	{
		return true;
	}
	else
	{
		return false;
	}
}

// Возвращает кол-во всех контактов
function get_all_contacts_count($search_word)
{
	global $site_db, $current_user_id;
	
	// Если поиск контактов
	$search_word_s = get_part_search_contact_query($search_word);
	
	$sql = "SELECT COUNT(*) as count FROM ".CONTACTS_TB." WHERE contact_deleted<>1 $search_word_s";

	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Возвращает кол-во всех контактов
function get_workers_contacts_count($search_word)
{
	global $site_db, $current_user_id;
	
	// Список сотрудников
	$workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	
	$count = 0;
	
	// Если поиск контактов
	$search_word_s = get_part_search_contact_query($search_word);
	
	if($workers_arr)
	{
		$users_ids = implode(',', $workers_arr);
		
		$sql = "SELECT COUNT(*) as count FROM ".CONTACTS_TB." WHERE user_id IN($users_ids) AND contact_deleted<>1 $search_word_s";
	 
		$row = $site_db->query_firstrow($sql);
	
		$count =  $row['count'];
	}
	
	return $count;
	
}

// Возвращает кол-во контактов пользователя
function get_user_contacts_count($user_id, $search_word)
{
	global $site_db, $current_user_id;
	
	// Если поиск контактов
	$search_word_s = get_part_search_contact_query($search_word);
	
	$sql = "SELECT COUNT(*) as count FROM ".CONTACTS_TB." WHERE user_id='$user_id' AND contact_deleted<>1 $search_word_s";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

function get_part_search_contact_query($search_word)
{
	if($search_word)
	{
		$search_word_s = " AND (contact_user_name LIKE '%$search_word%' OR contact_name LIKE '%$search_word%' OR contact_phone LIKE '$search_word%' OR contact_organization LIKE '$search_word%' OR contact_desc LIKE '%$search_word%') ";
		
		return $search_word_s;
	}
}
?>