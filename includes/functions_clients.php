<?php
// Страница клиентов сотрудника
function fill_clients($user_id)
{
	global $site_db, $current_user_id, $_CURRENT_USER_WORKERS_ARR, $_CURRENT_USER_DEPUTY_WORKERS_ARR, $current_user_obj;
	
	$main_tpl = file_get_contents('templates/clients/clients.tpl');
	
	$clients_top_menu_tpl = file_get_contents('templates/clients/clients_top_menu.tpl');
	
	$workers_clients_top_menu_tpl = file_get_contents('templates/clients/workers_clients_top_menu.tpl');
	
	$client_add_form_tpl = file_get_contents('templates/clients/client_add_form.tpl');
	
	$no_client_tpl  = file_get_contents('templates/clients/no_clients.tpl');
	
	$more_clients_btn_tpl = file_get_contents('templates/clients/more_clients_btn.tpl');
	
	$clients_search_form_tpl = file_get_contents('templates/clients/clients_search_form.tpl');
	
	$clients_top_menu_clients_search_tpl = file_get_contents('templates/clients/clients_top_menu_clients_search.tpl');
	
	if($_GET['all']=='1' && !$current_user_obj->get_user_is_dept_head())
	{
		header('Location: /clients/'.$current_user_id);
	}
	
	
	// Выбираем последнего добавленного пользователем клиента
	$sql = "SELECT client_id FROM ".CLIENTS_TB." WHERE user_id='$user_id' AND client_deleted<>1 ORDER by client_id DESC LIMIT 1";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['client_id'])
	{
		$_SESSION['last_user_client_id'] = $row['client_id'];
	}
	
	### Верхнее меню
	// Подсвечивает табы
	if($current_user_id==$user_id)
	{
		$active_menu_1 = 'menu_active';
	}
	else if($_GET['av'])
	{
		$active_menu_4 = 'menu_active';
	}
	else if($_GET['wks'])
	{
		$active_menu_2 = 'menu_active';
	}
	else if(!$user_id)
	{
		$active_menu_3 = 'menu_active';
	}
	
	// Если у пользователя нет сотрудников, не выводим раздел "Клиенты моих сотурдников"
	if($current_user_obj->get_user_is_dept_head())
	{ 
		$workers_clients_top_menu = $workers_clients_top_menu_tpl;
		$client_search_top_menu = $clients_top_menu_clients_search_tpl;
	}
	
	$PARS_1['{WORKERS_CLIENTS_TOP_MENU}'] = $workers_clients_top_menu;
	
	$PARS_1['{CLIENT_SEARCH_TOP_MENU}'] = $client_search_top_menu;
	
	$PARS_1['{ACTIVE_1}'] = $active_menu_1;
	
	$PARS_1['{ACTIVE_2}'] = $active_menu_2; 
	
	$PARS_1['{ACTIVE_3}'] = $active_menu_3;
	
	$PARS_1['{ACTIVE_4}'] = $active_menu_4;
	
	$top_menu = fetch_tpl($PARS_1, $clients_top_menu_tpl);
	
	### 

	
	if($_GET['wks'] && (!$_CURRENT_USER_WORKERS_ARR && !$_CURRENT_USER_DEPUTY_WORKERS_ARR))
	{
		header('Location: /clients');
		exit();
	}
	// Очистка массива удаленных контактов
	if($_SESSION['client_deleted'])
	{
		$_SESSION['client_deleted'] = '';
	}
	
	// Не выводим форму поиска при просмотре всех клиентов сотрудников
	if(!$_GET['wks'])
	{
		$client_search_form = $clients_search_form_tpl;
	}

	// Выводим форму добавления клиента для своей страницы
	if($current_user_id==$user_id)
	{
		// Заполняем список типов организаций
		$client_organizations_type_list = fill_client_organizations_types_list(0);
		
		$PARS_1['{CLIENT_ORGANIZATIONS_TYPE_LIST}'] = $client_organizations_type_list;
		
		$client_add_form = fetch_tpl($PARS_1, $client_add_form_tpl);
	}
	
	if($_GET['wks']==1)
	{
		$client_list_type = 'wks';
	}
	if($_GET['av']==1)
	{
		$client_list_type = 'av';
	}
	if($_GET['all']==1)
	{
		$client_list_type = 'all';
	}
	
	// Клиенты всех сотрудников пользователя
	if($client_list_type=='wks')
	{
		// Список клиентов
		$client_list = fill_all_user_workers_clients_list($current_user_id);
	}
	else
	{
		// Список клиентов
		$client_list = fill_clients_list($client_list_type, 1, '');
	}

	
	
	// Если клиенты не найдены
	if(!$client_list)
	{
		$client_list = $no_client_tpl;
	}
	

	
	
	// Не выполняем блок при просмотре всех клиентов сотрудника
	if($client_list_type!='wks')
	{
		// Кол-во клиентов
		$clients_count = get_user_clients_count($client_list_type);
		
		// Кол-во страниц
		$pages_count = ceil($clients_count/CLIENTS_PER_PAGE);
		
		// Если страниц больше 1
		if($pages_count > 1)
		{
			$more_clients = $more_clients_btn_tpl;
		}
	}
	
	
	$PARS['{TOP_MENU}'] = $top_menu;
	
	$PARS['{CLIENTS_LIST}'] = $client_list;
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{MORE_CLIENTS}'] = $more_clients;
	
	$PARS['{CLIENT_ADD_FORM}'] = $client_add_form;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	$PARS['{CURRENT_USER_ID}'] = $current_user_id;
	
	$PARS['{CLIENT_SEARCH_FORM}'] = $client_search_form;
	
	$PARS['{CLIENT_LIST_TYPE}'] = $client_list_type;
	
	return fetch_tpl($PARS, $main_tpl);
	
	
}

function fill_show_client($client_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT * FROM ".CLIENTS_TB." WHERE client_id='$client_id'";
		
	$client_data = $site_db->query_firstrow($sql);
	// fill_client_list_item($client_data, $edit_form=0, $search_word, $num, $is_workers_list=0, $is_show)
	
	if($client_data['client_deleted']==1)
	{
		header('Location: /clients');
		exit();
	}
	
	if(!is_client_open_for_edit_for_user($current_user_id, $client_data) && !check_client_for_available($current_user_id, $client_data['client_id'], $client_data, 1))
	{  
		header('Location: /clients');
	}
	
	// Заполнение элемента клиента
	$client_item = fill_client_list_item($client_data, 0,'',0,0,1);
	 
	return $client_item;
}

// Список отзывов 
function fill_clients_list($client_list_type, $page=1, $search_word='')
{
	global $site_db, $current_user_id;
	
	// Страничность
	$begin_pos = CLIENTS_PER_PAGE * ($page-1);
	$limit = " LIMIT ".$begin_pos.",".CLIENTS_PER_PAGE;
	
	// Удаленные в этой сессии клиенты
	$deleted_clients_ids = implode(', ',$_SESSION['client_deleted']);
	
	if($deleted_clients_ids)
	{
		$and_deleted_clients = " OR client_id IN($deleted_clients_ids) ";
	}
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_user_client_id'])
	{
		$and_clients_id = " AND client_id <= '".$_SESSION['last_user_client_id']."' ";
	}
	
	// При поиске по слову
	if($search_word)
	{
		// Часть запроса
		$search_word_s = get_part_query_search_words_for_clients($search_word);
	}
	
	
	// Выбираем переданные клиенты пользователя
	if($client_list_type=='av')
	{
		$sql = "SELECT i.* FROM ".CLIENTS_TB." i
				LEFT JOIN ".CLIENT_USER_ACCESS_TB." j ON i.client_id = j.client_id
				WHERE j.user_id='$current_user_id' AND i.client_deleted<>1 $search_word_s ORDER by j.id DESC $limit"; 
	}
	// Все контагенты
	else if($client_list_type=='all')
	{
		$sql = "SELECT * FROM ".CLIENTS_TB." WHERE (client_deleted<>1 $and_deleted_clients) $search_word_s ORDER by client_id DESC $limit";
	}
	// Клиенты пользователя
	else
	{
		// Выбираем контакты пользователя
		$sql = "SELECT * FROM ".CLIENTS_TB." WHERE user_id='$current_user_id' AND (client_deleted<>1 $and_deleted_clients) $and_clients_id $search_word_s ORDER by client_id DESC $limit"; 
	}
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		// Заполнение элемента клиента
		$clients_list .= fill_client_list_item($row, 0, $search_word, '');
	}
	
	return 	$clients_list;
}

// Список клиентов моих сотрудников 
function fill_all_user_workers_clients_list($user_id)
{
	global $site_db, $current_user_id, $user_obj, $_CURRENT_USER_WORKERS_ARR;;
	
	$worker_client_block_tpl = file_get_contents('templates/clients/worker_client_block.tpl');
	
	// Сливаем в единый массив временных и постоянных подчиненных
	$workers_users = get_current_user_users_arrs(array(0,1,0,0,1,1));
	
	$workers_users_ids = implode(',', $workers_users);
	
	// Контакты пользователя
	if($workers_users_ids)
	{
		// Выбираем клиентов пользователя
		$sql = "SELECT * FROM ".CLIENTS_TB." WHERE user_id IN($workers_users_ids) AND client_deleted<>1 ORDER by client_id DESC";
	}
	
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res, 1))
	{
		$workers_clients_arr[$row['user_id']][] = $row;
	}
	
	//echo "<pre>", print_r($workers_clients_arr), "</pre>";
	foreach($workers_clients_arr as $worker_id => $worker_clients_arr)
	{
		$clients_list = '';
		
		foreach($worker_clients_arr as $client_data)
		{
			// Заполнение элемента клиента
			$clients_list .= fill_client_list_item($client_data, 0, '', '', 1);
		}
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($worker_id);
		
		$PARS['{USER_ID}'] = $client_data['user_id'];
			
		$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
		$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
		$PARS['{USER_USER_POSITION}'] = $user_obj->get_user_position();
	
		$PARS['{CLIENTS_LIST}'] = $clients_list;
		
		$workers_clients_list .= fetch_tpl($PARS, $worker_client_block_tpl);
	}
	
	return 	$workers_clients_list;
}

// Заполняет элемент клиента
// mode = 1  - просмотр клиентов моих сотрудников
function fill_client_list_item($client_data, $edit_form=0, $search_word, $num, $is_workers_list=0, $is_show)
{
	global $site_db, $current_user_id, $user_obj;
	
	setlocale(LC_ALL, 'ru_RU.CP1251');
	
	// 
	/*if($mode==1)
	{
		$client_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/clients_list_workers_item.tpl');
	}
	else
	{
		$client_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/clients_list_item.tpl');
	}*/
	
	if($is_show)
	{
		$client_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/clients_show_item.tpl');
		
		$client_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/clients_show_edit_tools.tpl');
		$clients_edit_tools_edit_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/clients_show_edit_tools_edit_btn.tpl');
		$clients_edit_tools_delete_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/clients_show_edit_tools_delete_btn.tpl');	
	}
	else
	{
		$client_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/clients_list_item.tpl');
		
		$client_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/clients_edit_tools.tpl');
		$clients_edit_tools_edit_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/clients_edit_tools_edit_btn.tpl');
		$clients_edit_tools_delete_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/clients_edit_tools_delete_btn.tpl');
		$client_show_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/client_show_btn.tpl');
	}
	
	
	$client_list_item_edit_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/clients_list_item_edit.tpl');
	
	$clients_list_item_edit_private_options_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/clients_list_item_edit_private_options.tpl');
	
	$client_added_by_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/client_added_by.tpl');
	
	$access_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/access_btn.tpl');
	
	foreach($client_data as $i => $j)
	{
		$client_data[$i] = stripslashes($j);
	}
	
	// Проверка, является ли пользователь автором сделки
	if($current_user_id==$client_data['user_id'])
	{
		// Автор контакта
		$is_client_master = 1;
	}
	
	
	// Для создателя клиента, начальников или всем(если не отмечен чекбокс запретить редактирование всем, крмое вышестоящих сотрудников) выводим форму редактирования. Кнопка редактирования не выводится так же, если отмечен чекбокс НЕ ПОКАЗЫВАТЬ ДАННЫЕ
	if(is_client_open_for_edit_for_user($current_user_id, $client_data) && $is_show==1)
	{
		$PARS_1['{CLIENT_ID}'] =  $client_data['client_id'];
		
		$edit_tool_btns_arr[] = fetch_tpl($PARS_1, $clients_edit_tools_edit_btn_tpl);
	}	
	// Для автора клиента и его начальников выводим кнопку удалить
	if(is_client_open_for_delete_for_user($current_user_id, $client_data))
	{
		$PARS_1['{CLIENT_ID}'] =  $client_data['client_id'];
		
		$edit_tool_btns_arr[] = fetch_tpl($PARS_1, $clients_edit_tools_delete_btn_tpl);
	}
	
	// Если есть кнопки редактирования
	if($edit_tool_btns_arr)
	{
		$edit_tool_btns = implode('', $edit_tool_btns_arr);
		
		$PARS_2['{TOOL_BTNS}'] =  $edit_tool_btns;
		
		$edit_tools = fetch_tpl($PARS_2, $client_edit_tools_tpl);
	}
	
	
	// Подсвечиваем слова
	$client_name = !$edit_form ? light_words($client_data['client_name'], $search_word) : $client_data['client_name'];
	$client_inn = $client_data['client_inn'];
	$client_address_actual = $client_data['client_address_actual'];
	$client_address_legal = $client_data['client_address_legal'];
	$client_phone = $client_data['client_phone'];
	$client_fax = $client_data['client_fax'];
	$client_email = $client_data['client_email'];
	$client_bank_name = $client_data['client_bank_name'];
	$client_bik = $client_data['client_bik'];
	$client_bank_account = $client_data['client_bank_account'];
	$client_desc =  $client_data['client_desc'];
	$client_contact_person =  $client_data['client_contact_person'];
	
	// Для формы редактирвоания
	if($edit_form)
	{
		// Заполняем список типов организаций
		$client_organizations_type_list = fill_client_organizations_types_list($client_data['client_organization_type_id']);
	}
	else
	{
		// Название типа клиента
		$client_organization_type = $client_data['client_organization_type_id'] ? get_client_organization_type_by_type_id($client_data['client_organization_type_id']) : '';
	}
	
	 
	// Не показывать данные клиента, всем, кроме вышестоящих сотрудников
	if(!$is_client_master && $client_data['client_private_edit'] && !check_user_access_to_user_content($client_data['user_id'], array(0,1,0,0,1)) && !$edit_form && !check_client_for_available($current_user_id, $client_data['client_id'], $client_data, 1))
	{
		$client_inn = $client_inn == '' ? '' : '**********';
		$client_contact_person = $client_contact_person == '' ? '' : '**********';
		$client_address_actual = $client_address_actual == '' ? '' : '**********';
		$client_address_legal = $client_address_legal == '' ? '' : '**********';
		$client_fax = $client_fax == '' ? '' : '**********';
		$client_phone = $client_phone == '' ? '' : '**********';
		$client_email = $client_email == '' ? '' : '**********';
		$client_bank_name = $client_bank_name == '' ? '' : '**********';
		$client_bik = $client_bik == '' ? '' : '**********';
		$client_bank_account = $client_bank_account == '' ? '' : '**********';
		$client_desc = $client_desc == '' ? '' : '**********';
		

	}
	
	$client_private_edit_checked = $client_data['client_private_edit'] ? 'checked' : '';
	//$client_private_show_checked = $client_data['client_private_show'] ? 'checked' : '';
	
	// не выводим пустые поля
	$client_name_display = $client_name == '' ? 'display:none' : '';
	$client_inn_display = $client_inn == '' ? 'display:none' : '';
	$client_address_actual_display = $client_address_actual == '' ? 'display:none' : '';
	$client_address_legal_display = $client_address_legal == '' ? 'display:none' : '';
	$client_fax_display = $client_fax == '' ? 'display:none' : '';
	$client_phone_display = $client_phone == '' ? 'display:none' : '';
	$client_email_display = $client_email == '' ? 'display:none' : '';
	$client_bank_name_display = $client_bank_name == '' ? 'display:none' : '';
	$client_bik_display = $client_bik == '' ? 'display:none' : '';
	$client_bank_account_display = $client_bank_account == '' ? 'display:none' : '';
	$client_desc_display = $client_desc == '' ? 'display:none' : '';
	$client_contact_person_display = $client_contact_person == '' ? 'display:none' : '';
	$client_organization_type_display = $client_organization_type == '' ? 'display:none' : '';
	
	// Блок сделок для клиента
	$client_deals_block = fill_client_deals_block($client_data['client_id']);
	
	// Блок финансов для клиента
	$client_finances_block = fill_client_finances_block($client_data['client_id']);
		
	// Приватные опции при редактировании
	if($client_data['user_id']==$current_user_id)
	{
		//$PARS_1['{CLIENT_PRIVATE_SHOW_CHECKED}'] = $client_private_show_checked;
		$PARS_1['{CLIENT_PRIVATE_EDIT_CHECKED}'] = $client_private_edit_checked;
		$edit_private_options = fetch_tpl($PARS_1, $clients_list_item_edit_private_options_tpl);
	}
	
	// Кол-во новых сообщений от клиента
	$client_new_msgs_count = get_new_client_msgs_count($client_data['client_id']);
	$client_new_msgs_count = $client_new_msgs_count ? "(+ ".$client_new_msgs_count.")" : ''; 
	
	$client_new_files_count = get_new_client_files_for_users_count($client_data['client_id']);
	$client_new_files_count = $client_new_files_count ? "(+ ".$client_new_files_count.")" : ''; 
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_post_tracking.php';
	// Блок трекинга для клиента
	$tracking_block = fill_post_tracking_block_in_linked_content(array('client_id'=>$client_data['client_id']));
	
	
	if(check_client_for_available($current_user_id, $client_data['client_id'], $client_data,1))
	{
		$PARS['{CLIENT_ID}'] = $client_data['client_id'];
		$client_show_btn = fetch_tpl($PARS, $client_show_btn_tpl);
	}
	 
	
	$PARS['{CLIENT_SHOW_BTN}'] = $client_show_btn;
	
	$PARS['{TRACKING_BLOCK}'] = $tracking_block;
	
	$PARS['{CLIENT_ID}'] = $client_data['client_id'];
	$PARS['{CLIENT_NAME}'] = $client_name;
	$PARS['{CLIENT_INN}'] = $client_inn;
	$PARS['{CLIENT_CONTACT_PERSON}'] = $client_contact_person;
	$PARS['{CLIENT_ADDRESS_ACTUAL}'] = $client_address_actual;
	$PARS['{CLIENT_ADDRESS_LEGAL}'] = $client_address_legal;
	$PARS['{CLIENT_FAX}'] = $client_fax;
	$PARS['{CLIENT_PHONE}'] = $client_phone;
	$PARS['{CLIENT_EMAIL}'] = $client_email;
	$PARS['{CLIENT_BANK}'] = $client_bank_name;
	$PARS['{CLIENT_BIK}'] = $client_bik;
	$PARS['{CLIENT_BANK_ACCOUNT}'] = $client_bank_account;
	$PARS['{CLIENT_DESC}'] = !$edit_form ? nl2br($client_desc) : $client_desc;
	$PARS['{CLIENT_ORGANIZATIONS_TYPE_LIST}'] = $client_organizations_type_list;
	$PARS['{CLIENT_ORGANIZATION_TYPE}'] = $client_organization_type;
	
	
	$PARS['{CLIENT_NAME_DISPLAY}'] = $client_name_display;
	$PARS['{CLIENT_INN_DISPLAY}'] = $client_inn_display;
	$PARS['{CLIENT_CONTACT_PERSON_DISPLAY}'] = $client_contact_person_display;
	$PARS['{CLIENT_ADDRESS_ACTUAL_DISPLAY}'] = $client_address_actual_display;
	$PARS['{CLIENT_ADDRESS_LEGAL_DISPLAY}'] = $client_address_legal_display;
	$PARS['{CLIENT_PHONE_DISPLAY}'] = $client_phone_display;
	$PARS['{CLIENT_FAX_DISPLAY}'] = $client_fax_display;
	$PARS['{CLIENT_EMAIL_DISPLAY}'] = $client_email_display;
	$PARS['{CLIENT_BANK_DISPLAY}'] = $client_bank_name_display;
	$PARS['{CLIENT_BIK_DISPLAY}'] = $client_bik_display;
	$PARS['{CLIENT_BANK_ACCOUNT_DISPLAY}'] = $client_bank_account_display;
	$PARS['{CLIENT_DESC_DISPLAY}'] = $client_desc_display;
	$PARS['{CLIENT_TYPE_ORGANIZATION_DISPLAY}'] = $client_organization_type_display;
	
	$PARS['{EDIT_TOOLS}'] = $client_name;
	$PARS['{PRIVATE_OPTIONS}'] = $edit_private_options;
	
	$PARS['{CLIENT_NEW_MSGS_COUNT}'] = $client_new_msgs_count;
	$PARS['{CLIENT_NEW_FILES_COUNT}'] = $client_new_files_count;
	
	$PARS['{DEALS_BLOCK}'] = $client_deals_block;
	
	$PARS['{FINANCES_BLOCK}'] = $client_finances_block;
	
	// Если просмотр списка клиентов сотрубников
	if(!$is_workers_list)
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($client_data['user_id']);
		
		$PARS['{USER_ID}'] = $client_data['user_id'];
			
		$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
		$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
		$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
		$PARS['{USER_USER_POSITION}'] = $user_obj->get_user_position();
		
		$added_by_block = fetch_tpl($PARS, $client_added_by_tpl);
	}
	
	
	// вывод кнопки "показать клиента"
	if($current_user_id == $client_data['user_id'] || is_client_open_for_edit_for_user($current_user_id, $client_data) || check_client_for_available($current_user_id, $client_data['client_id'], $client_data))
	{
		// блок передачи доступа к клиенту
		//$client_access_block = fill_client_access_block($client_data);
		
		$PARS['{CLIENT_ID}'] = $client_data['client_id'];
		$client_access_btn = fetch_tpl($PARS, $access_btn_tpl);
	}
	
	
	$PARS['{ADDED_BY}'] = $added_by_block;
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	//$PARS['{CLIENT_ACCESS_BLOCK}'] = $client_access_block;
	
	$PARS['{CLIENT_ACCESS_BTN}'] = $client_access_btn;
	 
	
	// Форма для редактирования
	if($edit_form)
	{ 
		return fetch_tpl($PARS, $client_list_item_edit_tpl);
	}
	else
	{
		return fetch_tpl($PARS, $client_list_item_tpl);
	}
}

// Проверяет, доступен ли клиент пользователю
function check_client_for_available($user_id, $client_id, $client_data, $for_view=0)
{
	global $site_db, $current_user_id, $user_obj, $current_user_obj;
	
	if($client_data['user_id']==$current_user_id)
	{
		return true;
	}
	else
	{
		$sql = "SELECT id FROM ".CLIENT_USER_ACCESS_TB." WHERE client_id='$client_id' AND user_id='$user_id'";
	 
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			return true;
		}
	}
	
	//  доступ для просмотра
	if($for_view && (($current_user_obj->get_user_is_dept_head() && !$client_data['client_private_edit']) || check_user_access_to_user_content($client_data['user_id'], array(0,1,0,0,1,1))))
	{
		return true;
	}
	
	return false;
}

// Доступ для клиентов
function fill_client_access_block($client_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$client_id = $client_data['client_id'];
	
	$users_access_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/users_access_block.tpl');
	$users_access_user_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/users_access_user_item.tpl');
	$no_users_to_access_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/no_users_to_access.tpl');
	$users_access_block_hide_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/users_access_block_hide.tpl');
	$user_access_select_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/user_access_select.tpl');
	
	if($current_user_id != $client_data['user_id'] && !check_client_for_available($current_user_id, $client_data['client_id'], $client_data) && 
	!is_client_open_for_edit_for_user($current_user_id, $client_data) )
	{  
		return '';
	}
	 
	// выбор всех пользоватлей, кому передали документ
	$sql = "SELECT * FROM tasks_clients_users_access WHERE client_id='$client_id' AND user_id!='$current_user_id' ORDER by id ";
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$user_obj->fill_user_data($row['user_id']); 
	
		$user_name = $user_obj->get_user_name();
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_name = $user_obj->get_user_surname().' '.$user_name[0].'. '.$user_middlename[0].', '.$user_obj->get_user_position(); 
		
		$PARS['{ACCESS_ID}'] = $row['id'];
		$PARS['{CLIENT_ID}'] = $row['client_id'];
		$PARS['{CLASS}'] = 'selected';
		$PARS['{VALUE}'] = $row['user_id'];
		$PARS['{NAME}'] = $user_name;
		$users_access_list .= fetch_tpl($PARS, $user_access_select_tpl);
		
	}
	/*
	// Документ можно передать и начальнику и подчиненному
	$users_for_access_arr = get_current_user_users_arrs(array(1,1,1,1,1), 1);
 
	foreach($users_for_access_arr as $user_data)
	{ 
	 	if($client_data['user_id']==$user_data['user_id'])
		{
			continue;
		}
		
		$access_active = '';
		
		// Проверяем доступность документа начальнику
		$sql = "SELECT id FROM ".CLIENT_USER_ACCESS_TB." WHERE user_id='".$user_data['user_id']."' AND client_id='$client_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			$access_active = 'access_active';
		}
		
		$user_obj->fill_user_data($user_data['user_id']);
		
		$user_name = $user_obj->get_user_name();
		
		$user_middlename = $user_obj->get_user_middlename();
		
		$user_surname = $user_obj->get_user_surname();
		
		$user_position = $user_obj->get_user_position();

		$PARS1['{CLIENT_ID}'] = $client_id;
		
		$PARS1['{ACCESS_ACTIVE}'] = $access_active;

		$PARS1['{USER_ID}'] = $user_data['user_id'];
		
		$PARS1['{SURNAME}'] = $user_surname;
		
		$PARS1['{NAME}'] = $user_name;
				
		$PARS1['{MIDDLENAME}'] = $user_middlename;
				
		$PARS1['{USER_POSITION}'] = $user_position;
		  
		$users_access_list .= fetch_tpl($PARS1, $users_access_user_item_tpl);
	}*/
	
	if(!$users_access_list)
	{
		//return '';
	}

	
	$PARS['{CLIENT_ID}'] = $client_id;
	
	$PARS['{USERS_LIST}'] = $users_access_list;
	
	return  fetch_tpl($PARS, $users_access_block_tpl);
}

// Проверка, может ли пользователь, который просматривает - удалить клиента приватности для вывода кнопки удаления
function is_client_open_for_delete_for_user($user_id, $client_data)
{
	global $current_user_id;
	
	// Для автора клиента и его начальников есть возможность удалить клиента
	if($user_id==$client_data['user_id'] || check_user_access_to_user_content($client_data['user_id'], array(0,1,0,0,1,1)))
	{
		return true;
	}
	else
	{
		return false;
	}
}

// Проверка, может ли пользователь, который просматривает - редактировать клиента приватности для вывода кнопки удаления
function is_client_open_for_edit_for_user($user_id, $client_data)
{
	global $current_user_id;
	
	if($user_id == $client_data['user_id'])
	{
		return true;
	}
	else if($client_data['client_private_edit'] && check_user_access_to_user_content($client_data['user_id'], array(0,1,0,0,1,1)))
	{
		return true;
	}
	else if(check_user_access_to_user_content($client_data['user_id'], array(0,1,0,0,1,1)))
	{
		return true;
	}
	else if(check_client_for_available($user_id, $client_data['client_id'], $client_data) && !$client_data['client_private_edit'])
	{
		return true;
	}

}
// Формирование части запроса при поиске по словам
function get_part_query_search_words_for_clients($search_word)
{
	$search_word_s = " AND ( client_name LIKE '%$search_word%') ";
	
	return $search_word_s;
}
// Возвращает кол-во контактов пользователя
function get_user_clients_count($client_list_type, $search_word='')
{
	global $site_db, $current_user_id;
	
	 
	// При поиске по слову
	if($search_word)
	{
		$search_word_s = get_part_query_search_words_for_clients($search_word);
	}
	
	// Переданные клиенты
	if($client_list_type=='av')
	{
		$sql = "SELECT COUNT(*) as count FROM ".CLIENTS_TB." i
				LEFT JOIN ".CLIENT_USER_ACCESS_TB." j ON i.client_id = j.client_id
				WHERE j.user_id='$current_user_id' AND i.client_deleted<>1 $search_word_s"; 
	}
	else if($client_list_type=='all')
	{
		$sql = "SELECT COUNT(*) as count FROM ".CLIENTS_TB." WHERE client_deleted<>1 $search_word_s";
	}
	// Список клиентов
	else
	{
		$sql = "SELECT COUNT(*) as count FROM ".CLIENTS_TB." WHERE user_id='$current_user_id' AND client_deleted<>1 $search_word_s";
	}
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Возвращает название клиента по его ID
function get_client_name_by_id($client_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT client_name FROM ".CLIENTS_TB." WHERE client_id='$client_id'";
		
	$row = $site_db->query_firstrow($sql);
		
	return $row['client_name'];
}

// Блок сделок для клиента
function fill_client_deals_block($client_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$client_deals_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/client_deals_block.tpl');
	
	$client_deals_block_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/client_deals_block_item.tpl');
	
	$a_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/a.tpl');
	
	// Выбор всех сделок для клиента
	$sql = "SELECT * FROM ".DEALS_TB." WHERE deal_client_id='$client_id' AND deal_deleted<>1";
	 
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{ 
		$user_id = '';
		$user_name = '';
		$user_surname = '';
		$user_middlename = '';
		$user_position = '';
		$deal_status_name = '';
		$deal_status_date = '';
		
		// Данные последнего статуса сделки
		$deal_status_data_arr = get_last_deal_status_arr($row['deal_id']);
		
		if($deal_status_data_arr['status_id'])
		{
			$deal_status_name = get_deal_status_by_status_id($deal_status_data_arr['status_id']);
		 
			// Заполянем объект пользователя
			$user_obj->fill_user_data($deal_status_data_arr['user_id']);
			
			$user_id = $deal_status_data_arr['user_id'];
			
			$user_name = $user_obj->get_user_name();
			
			$user_surname = $user_obj->get_user_surname();
			
			$user_middlename = $user_obj->get_user_middlename();
			
			$user_position = $user_obj->get_user_position();
		
			$deal_status_date = formate_date($deal_status_data_arr['status_date'], 1);
		}
		else
		{
			$deal_status_name = 'Статус сделки отсутствует';
		}
		
		// Если сделка доступна для редактирования пользователем
		if(is_deal_open_for_edit_for_user($current_user_id, $row) || check_deal_for_available($current_user_id, $row['deal_id'], $row, 1))
		{
			$PARS_2['{NAME}'] = $row['deal_id'];
			$PARS_2['{HREF}'] = '/deals/edit/'.$row['deal_id'];
			$deal_id_block = fetch_tpl($PARS_2, $a_tpl);
		}
		else
		{
			$deal_id_block = $row['deal_id'];
		}
		
		// Класс для подсветки статуса сделки
		$deal_status_back_class = get_deal_status_back_class($deal_status_data_arr['status_id']);
		
		$PARS_1['{USER_ID}'] = $user_id;
			
		$PARS_1['{USER_NAME}'] = $user_name;
		
		$PARS_1['{USER_MIDDLENAME}'] = $user_middlename;
			
		$PARS_1['{USER_SURNAME}'] = $user_surname;
			
		$PARS_1['{USER_USER_POSITION}'] = $user_position;
	
		$PARS_1['{DEAL_ID}'] = $deal_id_block;
		
		$PARS_1['{DEAL_STATUS_DATE}'] = $deal_status_date;
		
		$PARS_1['{DEAL_STATUS_NAME}'] = $deal_status_name;
		
		$PARS_1['{DEAL_STATUS_BACK_CLASS}'] = $deal_status_back_class;
		
		$deals_list .= fetch_tpl($PARS_1, $client_deals_block_item_tpl);
	}
	
	$PARS['{CLIENT_ID}'] = $client_id;
	
	$PARS['{DEALS_LIST}'] = $deals_list;
	
	if(!$deals_list)
	{
		return '';
	}
	else
	{
		return fetch_tpl($PARS, $client_deals_block_tpl);
	}
}

// Блок финансов для клиента
function fill_client_finances_block($client_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_finances.php';
	
	$client_finances_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/client_finances_block.tpl');
	
	$client_finances_block_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/client_finances_block_item.tpl');
	
	$a_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/a.tpl');
	
	// Выбор всех сделок для клиента
	$sql = "SELECT * FROM ".FINANCES_OPERATIONS_TB." WHERE client_id='$client_id' AND operation_returned<>1 ORDER by operation_id DESC";
	 
	$res = $site_db->query($sql);
			
	while($operation_data=$site_db->fetch_array($res))
	{  
		// Если уже получали данные по финансам
		if($operation_data['finance_id'])
		{
			$finance_data[$operation_data['finance_id']] = get_finance_data($operation_data['finance_id']);
			$finance_data = $finance_data[$operation_data['finance_id']];
		}
		else
		{
			$finance_data = $operation_data['finance_id'];
		}
		
		// Валюта
		$currency_value = get_currency_value_by_id($finance_data['currency_id']);
	
		// Операция Постепление
		if($operation_data['operation_type']==1)
		{
			$operation_out_summa = 0;
			
			$operation_in_summa = number_format($operation_data['operation_summa'], 2, '.', ' ');
		}
		// Операция Расход
		else if($operation_data['operation_type']==2)
		{
			$operation_in_summa = 0;
			
			$operation_out_summa = number_format($operation_data['operation_summa'], 2, '.', ' ');
		}
		// Дата операции
		$operation_date = datetime($operation_data['operation_date'], '%d.%m.%y в %H:%i');
		
		// Последний активный статус
		$last_operation_status = get_finance_operation_last_status($operation_data['operation_id']);
		
		// Раскрашиваем фон статуса
		$status_back_color = switch_finance_status_back($last_operation_status);
	 
		// Если у пользователя есть доступ редактировать финансы
		if(check_user_for_access_to_operation($finance_data, $operation_data['finance_id'], $current_user_id))
		{
			$PARS_2['{NAME}'] = $operation_data['finance_id'];
			$PARS_2['{HREF}'] = '/finances/edit/'.$operation_data['finance_id'];
			$finance_id_block = fetch_tpl($PARS_2, $a_tpl);
		}
		else
		{
			$finance_id_block = $operation_data['finance_id'];
		}
		
		$PARS_1['{OPERATION_ID}'] = $operation_data['operation_id']; 
		
		$PARS_1['{OPERATION_IN_SUMMA}'] = $operation_in_summa;
	
		$PARS_1['{OPERATION_OUT_SUMMA}'] = $operation_out_summa;
		
		$PARS_1['{CURRENCY_VALUE}'] = $currency_value;
		
		$PARS_1['{OPERATION_DATE}'] = $operation_date;
		
		$PARS_1['{FINANCE_ID_BLOCK}'] = $finance_id_block;
		
		$PARS_1['{STATUS_BACK_COLOR}'] = $status_back_color;
		
		$finances_list .= fetch_tpl($PARS_1, $client_finances_block_item_tpl);
	}
	
	$PARS['{CLIENT_ID}'] = $client_id;
	
	$PARS['{FINANCES_LIST}'] = $finances_list;
	
	if(!$finances_list)
	{
		return '';
	}
	else
	{
		return fetch_tpl($PARS, $client_finances_block_tpl);
	}
}

function get_org_type_by_type_name($type_name_check, $types_arr)
{
	foreach($types_arr as $type_id => $type_name)
	{  
		if(preg_match('/'.$type_name_check.'/i', $type_name))
		{
			return $type_id;
		}
	}
}

// Заполняет список типов организаций
function fill_client_organizations_types_list($type_id, $return_array=0)
{
	global $site_db, $current_user_id;
	
	// Выбираем все типы
	$sql = "SELECT * FROM ".CLIENTS_TYPES_DATA." ORDER by type_id";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$selected = '';
			
		if($type_id)
		{
			$selected = $row['type_id'] == $type_id ? 'selected' : '';
		}
		else
		{
			$selected = $row['type_id'] == 0 ? 'selected' : '';
		}
			
		$PARS['{NAME}'] = $row['type_name'];
					
		$PARS['{VALUE}'] = $row['type_id'];
					
		$PARS['{SELECTED}'] = $selected;
					
		$types_list .= fetch_tpl($PARS, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl'));
		
		$type_array[$row['type_id']] = $row['type_name'];
	}
	
	if($return_array)
	{
		return $type_array;
	}
	else
	return $types_list;
}

// Название типа клиента по его type_id
function get_client_organization_type_by_type_id($type_id)
{
	global $site_db, $current_user_id;
	
	// Выбираем все типы
	$sql = "SELECT * FROM ".CLIENTS_TYPES_DATA." WHERE type_id='$type_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['type_name'];
}

// Отмечаем последнюю активность на сайте
function set_last_client_visit_date($client_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "UPDATE ".CLIENTS_TB." SET client_last_visit_date=NOW() WHERE client_id='$client_id'";
	
	$site_db->query($sql);
}


// Проверяет находится ли онлайн клиент
function client_in_online_icon($client_id, $client_last_visit_date='')
{
	global $site_db, $current_user_id, $user_obj;
	
	$user_in_online_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/user/user_in_online.tpl');
	 
	// Если передано значение последнего визита пользователя
	if($client_last_visit_date)
	{
		$last_v_time = $client_last_visit_date;
	}
	else
	{
		$sql = "SELECT client_last_visit_date FROM ".CLIENTS_TB." WHERE client_id='$client_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		$last_v_time = $row['client_last_visit_date'];
	}
	
	$last_visit_mktime = to_mktime($last_v_time);
	
	// Промежуток времени, за которым считается, что клиент онлайн
	$in_online_seconds = 60*5;

	if((time()) - $last_visit_mktime < $in_online_seconds)
	{
		return $user_in_online_tpl;	
	}
	else
	{
		return '';
	}
}

// Данные клиента
function get_client_data($client_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	// Данные клиента
	$sql = "SELECT i.*, j.type_name FROM ".CLIENTS_TB." i
			LEFT JOIN ".CLIENTS_TYPES_DATA." j ON i.client_organization_type_id=j.type_id 
			WHERE client_id='$client_id'"; 
			
	$client_data = $site_db->query_firstrow($sql);
	
	return $client_data;
}

function generate_client_password()
{
	return rand(10000, 99999);
}

// Кол-во новых сообщений от клиента
function get_new_client_msgs_count($client_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "SELECT COUNT(*) as count FROM ".CLIENT_MSGS_TB." WHERE  message_from_client_id='$client_id' AND  message_to_user_noticed=0";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Кол-во новых сообщений от клиентов пользователя
function get_new_user_clients_messages_count($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	// Все клиенты пользователя
	$sql = "SELECT client_id FROM ".CLIENTS_TB." WHERE user_id='$user_id' AND client_deleted<>1";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$users_clients_arr[] = $row['client_id'];
	}
	
	// Если найдены клиенты пользователя
	if($users_clients_arr)
	{
		$clients_ids = implode(',', $users_clients_arr);
		
		$sql = "SELECT COUNT(*) as count FROM ".CLIENT_MSGS_TB." WHERE  message_from_client_id IN($clients_ids) AND  message_to_user_noticed=0";
	
		$row = $site_db->query_firstrow($sql);
		
		return $row['count'];
	}
	else
	{
		return 0;
	}
	 
}

// Кол-во новых сообщений от клиентов пользователя
function get_new_client_files_for_users_count($client_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "SELECT COUNT(*) as count FROM ".CLIENTS_FILES_TB." WHERE  client_id='$client_id' AND from_client_id='$client_id' AND file_noticed=0";
	
	$row = $site_db->query_firstrow($sql);
		
	return $row['count'];

}

// Кол-во новых сообщений от клиентов пользователя
function get_new_users_files_for_client_count($client_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$sql = "SELECT COUNT(*) as count FROM ".CLIENTS_FILES_TB." WHERE  client_id='$client_id' AND from_client_id='0' AND file_noticed=0";
	
	$row = $site_db->query_firstrow($sql);
		
	return $row['count'];

}

function get_client_name_by_client_id($client_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT client_name FROM ".CLIENTS_TB." WHERE client_id='$client_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['client_name'];
}

function fill_client_import()
{
	$main_tpl = file_get_contents('templates/clients/clients_import.tpl');
	
	return $main_tpl;
}

function get_import_clients_list_for_preview($import_file)
{
	$clients_top_menu_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/client_import_preview.tpl');
	$client_import_preview_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/client_import_preview_item.tpl');
	
	// Получаем массив клиентов из файла
	$client_arr = get_clients_array_from_import_file($import_file);	
	
	foreach($client_arr as $i => $client_data)
	{
		$PARS['{CLIENT_NAME}'] = $client_data['client_name'];
		
		$PARS['{CLIENT_INN}'] = $client_data['client_inn'];
		$PARS['{CLIENT_CONTACT_PERSON}'] = $client_data['client_contact_person'];
		$PARS['{CLIENT_ADDRESS_ACTUAL}'] = $client_data['client_address_actual'];
		$PARS['{CLIENT_ADDRESS_LEGAL}'] = $client_data['client_address_legal'];
		$PARS['{CLIENT_FAX}'] = $client_data['client_fax'];
		$PARS['{CLIENT_PHONE}'] = $client_data['client_phone'];
		$PARS['{CLIENT_EMAIL}'] = $client_data['client_email'];
		$PARS['{CLIENT_BANK}'] = $client_data['client_bank_name'];
		$PARS['{CLIENT_BIK}'] = $client_data['client_bik'];
		$PARS['{CLIENT_BANK_ACCOUNT}'] = $client_data['client_bank_account'];
		$PARS['{CLIENT_DESC}'] = $client_data['client_desc'];
		$PARS['{CLIENT_ORGANIZATION_TYPE}'] = $client_data['client_organization_type'];
		
		$clients_list .= fetch_tpl($PARS, $client_import_preview_item_tpl);
	}
	
	$PARS['{CLIENTS_LIST}'] = $clients_list;
	$PARS['{IMPORT_FILE}'] = basename($import_file);
	
	return fetch_tpl($PARS, $clients_top_menu_tpl);
	
	 
}


function get_clients_array_from_import_file($import_file, $limit = 0)
{
	/** PHPExcel_IOFactory */
	//include $_SERVER['DOCUMENT_ROOT'].'/libraries/PHPExcel/IOFactory.php';
	
	$sheet_data = file($import_file);
	// Все типы организаций помещаем в архив
	$types_arr = fill_client_organizations_types_list('', 1);		
	 
	$num = 0; 
	foreach($sheet_data as $row => $data)
	{ 
		$cell = split(';', $data);
		 
	 	$clients_arr[$num]['client_name'] = value_proc($cell[0], 0);
		$clients_arr[$num]['client_organization_type'] = value_proc($cell[1], 0);
		$clients_arr[$num]['client_organization_type_id'] = get_org_type_by_type_name($cell[1], $types_arr);
		$clients_arr[$num]['client_inn'] = value_proc($cell[2], 0);
		$clients_arr[$num]['client_contact_person'] = value_proc($cell[3], 0);
		$clients_arr[$num]['client_address_actual'] = value_proc($cell[4], 0);
		$clients_arr[$num]['client_address_legal'] = value_proc($cell[5], 0);
		$clients_arr[$num]['client_phone'] = value_proc($cell[6], 0);
		$clients_arr[$num]['client_fax'] = value_proc($cell[7], 0);
		$clients_arr[$num]['client_email'] = value_proc($cell[8], 0);
		$clients_arr[$num]['client_bank_name'] = value_proc($cell[9], 0);
		$clients_arr[$num]['client_bik'] = value_proc($cell[10], 0);
		$clients_arr[$num]['client_bank_account'] = value_proc($cell[11], 0);
		$clients_arr[$num]['client_desc'] = value_proc($cell[12], 0);
		$num++;
		
		if($limit && $num >=$limit)
		{
			break;
		}
	}
	
	return $clients_arr;
}

// Добавляем контрагента
function insert_import_clients($import_file, $client_private_edit)
{
	global $site_db, $current_user_id;
	
	// Получаем массив клиентов из файла
	$client_arr = get_clients_array_from_import_file($import_file);	
	
	foreach($client_arr as $i => $client_data)
	{
		$client_password = generate_client_password();
		$client_password_hash = password_hash_proc($client_password);
	
		// Добавляем контрагента
		$sql = "INSERT INTO ".CLIENTS_TB." SET 
				client_name='".$client_data['client_name']."', 
				client_inn='".$client_data['client_inn']."',
				client_contact_person='".$client_data['client_contact_person']."',
				client_address_actual='".$client_data['client_address_actual']."', 
				client_address_legal='".$client_data['client_address_legal']."', 	
				client_phone='".$client_data['client_phone']."',
				client_fax='".$client_data['client_fax']."',
				client_email='".$client_data['client_email']."',
				client_bank_name='".$client_data['client_bank_name']."',  
				client_bik='".$client_data['client_bik']."',
				client_bank_account='".$client_data['client_bank_account']."',
				client_desc='".$client_data['client_desc']."',
				client_organization_type_id = '".$client_data['client_organization_type_id']."',
				client_private_edit='$client_private_edit', 
				client_date_add=NOW(), 
				user_id='$current_user_id', 
				client_password = '$client_password_hash'";
				
		$site_db->query($sql);
		
		$success = 1;
	}
	
	if($success==1)
	{
		// Удаляем файл импорта
		unlink($import_file);
	}
	
	return $success;
	
	
}
?>