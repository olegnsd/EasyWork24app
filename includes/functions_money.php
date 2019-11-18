<?php
// Внутренние финансы
function fill_user_money()
{
	global $site_db, $current_user_id;
	
	$main_tpl = file_get_contents('templates/money/money.tpl');
	
	$user_id = $_GET['id'] ? $_GET['id'] : $current_user_id;
	
	if($_GET['payments']==1)
	{
		$active_menu_2 = 'menu_active';
	}
	else
	{ 
		$active_menu_1 = 'menu_active';
	}
	
	
	if($_GET['payments'])
	{
		 // Выплаты
		$money_content = fill_user_money_payments($user_id);
	}
	else
	{	
		// Начисления
		$money_content = fill_user_money_accruals($user_id);
		 
	}
	
	// Кол-во новых выплат
	$new_payments_count = get_new_money_for_user($current_user_id);
	// Кол-во новых начислений
	$new_accruals_count = get_new_accruals_count($current_user_id);
	
	
	$new_payments_count = $new_payments_count ? '(+ '.$new_payments_count.')' : '';
	 
	$new_accruals_count = $new_accruals_count ? '(+ '.$new_accruals_count.')' : '';
	 	
	$PARS['{ACTIVE_1}'] = $active_menu_1;
	
	$PARS['{ACTIVE_2}'] = $active_menu_2;
	
	$PARS['{NEW_PAYMENTS_COUNT}'] = $new_payments_count;
	
	$PARS['{NEW_ACCRUALS_COUNT}'] = $new_accruals_count;
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{MONEY_CONTENT}'] = $money_content;
	
	return fetch_tpl($PARS, $main_tpl);
}

function fill_user_money_accruals($user_id)
{
	global $site_db, $current_user_id;
	
	$main_tpl = file_get_contents('templates/money/user_money_accruals.tpl');
	
	$more_accruals_btn_tpl = file_get_contents('templates/money/more_accruals_btn.tpl');
	
	// Очистка массива удаленных финансов
	if($_SESSION['accrual_deleted'])
	{
		$_SESSION['accrual_deleted'] = '';
	}
	
	if(!check_user_access_to_user_content($user_id, array(0,1,0,0,1)) && $user_id!=$current_user_id)
	{
		header('Location: /money');
	}
	
	// Выбираем последние начисления для пользователя
	$sql = "SELECT accrual_id FROM ".MONEY_ACCRUALS_TB." WHERE accrual_id<>1 AND to_user_id='$user_id' ORDER by accrual_id DESC LIMIT 1";
	
	$row = $site_db->query_firstrow($sql);
	 
	if($row['accrual_id'])
	{
		$_SESSION['last_user_accrual_id'] = $row['accrual_id'];
	}
	
	// Для начальника выводим форму добавления начислений
	if($user_id && check_user_access_to_user_content($user_id, array(0,1,0,0,1)))
	{
		$accruals_add_form = fill_accruals_add_form();
	}
	 
	
	// Панель поиска
	$search_panel = fill_money_accruals_search_panel($user_id);
	
	// Список задолженностей
	$accruals_list = fill_accruals_list($user_id);
	
	// Кол-во переданных финансов
	$accruals_count = get_user_accruals_count($user_id);
		
	// Кол-во страниц
	$pages_count = ceil($accruals_count/MONEY_PER_PAGE);
		
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_accruals_tpl = $more_accruals_btn_tpl;
	}
	
	// Блок результирующих начислений
	$user_accruals_result_block = fill_user_accruals_result_block($user_id);
	
	// Выводим по умолчанию все задолженности перед всеми сотрудниками
	if($user_id==$current_user_id)
	{
		// Блок результирующих начислений
		$workers_accruals_result_block = fill_user_accruals_result_block(0,1);
	}
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{ACCRUALS_ADD_FORM}'] = $accruals_add_form;
	
	$PARS['{ACCRUALS_LIST}'] = $accruals_list;
	
	$PARS['{MORE_ACCRUALS}'] = $more_accruals_tpl;
	
	$PARS['{SEARCH_PANEL}'] = $search_panel;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	$PARS['{USER_ACCRUALS_RESULT}'] = $user_accruals_result_block;	

	$PARS['{WORKERS_ACCRUALS_RESULT}'] = $workers_accruals_result_block;
	
	return fetch_tpl($PARS, $main_tpl);
}

// Блок результирующих начислений
function fill_user_accruals_result_block($user_id, $workers=0)
{
	global $site_db, $current_user_id;
	
	$accruals_result_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/accruals_result_block.tpl');
	
	$accruals_workers_result_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/accruals_workers_result_block.tpl');
	
	$accruals_summa_type_debt_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/accruals_summa_type_debt.tpl');
	
	$accruals_summa_type_bonus_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/accruals_summa_type_bonus.tpl');
	
	$accruals_summa_type_fine_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/accruals_summa_type_fine.tpl');
	
	if($workers)
	{
		// Получаем временных и постоянных подчиненных
		$user_workers_arr = get_current_user_users_arrs(array(0,1,0,0,1,1));
		
		if(!$user_workers_arr)
		{
			return '';
		}
		
		$users_ids = implode(',', $user_workers_arr);
		
		// Выбираем начисления пользователя
		$sql = "SELECT * FROM ".MONEY_ACCRUALS_TB." WHERE to_user_id IN($users_ids) AND deleted <> 1 AND paid=0";
		
	}
	else
	{
		// Выбираем начисления пользователя
		$sql = "SELECT * FROM ".MONEY_ACCRUALS_TB." WHERE to_user_id='$user_id' AND deleted <> 1 AND paid=0";
	}
	
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{
		$result_summ[$row['type_id']] += $row['summa'];
		
		// Если штраф
		if($row['type_id']==3)
		{
			$result_accruals_sum -= $row['summa'];
		}
		else
		{
			$result_accruals_sum += $row['summa'];
		}
	}
	 
	// Задолженность
	if($result_summ['1'] > 0)
	{ 
		$PARS['{SUMMA}'] = sum_process($result_summ['1']);
		
		$result_block .= fetch_tpl($PARS, $accruals_summa_type_debt_tpl);
	}
	// Бонус
	if($result_summ['2'] > 0)
	{ 
		$PARS['{SUMMA}'] = sum_process($result_summ['2']);
		
		$result_block .= fetch_tpl($PARS, $accruals_summa_type_bonus_tpl);
	}
	// Штраф
	if($result_summ['3'] > 0)
	{ 
		$PARS['{SUMMA}'] = sum_process($result_summ['3']);
		
		$result_block .= fetch_tpl($PARS, $accruals_summa_type_fine_tpl);
	}
	
	if(!$result_block)
	{
		return '';
	}
	
	$PARS['{RESULT_ACCRUALS_BLOCK}'] = 	$result_block;
	
	$PARS['{RESULT_SUM}'] = sum_process($result_accruals_sum);
	
	if($workers)
	{
		return fetch_tpl($PARS, $accruals_workers_result_block_tpl);
	}
	else return fetch_tpl($PARS, $accruals_result_block_tpl);
}

// Список начислений
function fill_accruals_list($user_id, $page=1)
{
	global $site_db, $current_user_id;
	
	$no_accruals_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/no_accruals.tpl');
	
	$page = !$page ? 1 : $page;
	
	// Удаленные в этой сессии клиенты
	$deleted_accruals_ids = implode(', ', $_SESSION['accrual_deleted']);
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_user_accrual_id'])
	{
		$and_accrual_id = " AND accrual_id <= '".$_SESSION['last_user_accrual_id']."' ";
	}
	
	if($deleted_accruals_ids)
	{
		$and_deleted_accruals = " OR accrual_id IN($deleted_accruals_ids) ";
	}
 	
	
	// Страничность
	$begin_pos = MONEY_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".MONEY_PER_PAGE;
	
	// Если начальник просматривает начисления
	if(check_user_access_to_user_content($user_id, array(0,1,0,0,1)))
	{
		$sql = "SELECT *, if(is_rate=1, if(paid=0,1,0),0) as paid_order FROM ".MONEY_ACCRUALS_TB." WHERE to_user_id='$user_id' AND (deleted<>1 $and_deleted_accruals) $and_accrual_id ORDER by paid_order DESC, accrual_id DESC $limit";
	}
	else
	{ 
		$sql = "SELECT *, if(is_rate=1, if(paid=0,1,0),0) as paid_order FROM ".MONEY_ACCRUALS_TB." WHERE to_user_id='$current_user_id' AND (deleted<>1 $and_deleted_accruals) $and_accrual_id  ORDER by paid_order DESC, accrual_id DESC $limit";
	}
	 
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		$accruals_list .= fill_accruals_list_item($row);
	}
	
	if(!$accruals_list)
	{
		$accruals_list = $no_accruals_tpl;	
	}
	
	return $accruals_list;
}

// Заполнение элемента списка начисления
function fill_accruals_list_item($accrual_data, $on_personal_page=0)
{
	global $site_db, $current_user_id, $user_obj;
	
	if($on_personal_page)
	{
		$accruals_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/accruals_list_item_for_personal.tpl');
	}
	else
	{
		$accruals_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/accruals_list_item.tpl');
	}
	
	$accruals_status_paid_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/accruals_status_paid.tpl');
	
	// Название типа начисления
	$type_name = get_accrual_type_name_by_type_id($accrual_data['type_id']);
	
	// Клас для начисления
	$type_class = get_accruals_color_class($accrual_data['type_id']);
	
	if(!$accrual_data['confirm'] && in_array($current_user_id, array($accrual_data['from_user_id'], $accrual_data['to_user_id'])))
	{
		$not_confirm_class = 'not_confirm_row';
	}
	
	// Блок действий над начислением
	$accrual_action_block = fill_accrual_action_block($accrual_data);
	
	if($accrual_data['paid']==1)
	{
		$accrual_status = $accruals_status_paid_tpl;
	}
	
	// Заполянем объект пользователя
	$user_obj->fill_user_data($accrual_data['from_user_id']);
	
	$PARS['{FROM_USER_ID}'] = $accrual_data['from_user_id'];
	
	$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
	$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
	$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	
	$PARS['{ACCRUAL_ID}'] = $accrual_data['accrual_id'];
	
	$PARS['{NOT_CONFIRM_CLASS}'] = $not_confirm_class;
	
	$PARS['{TYPE_CLASS}'] = $type_class;
	
	$PARS['{DATE}'] = datetime($accrual_data['date'], '%j.%m.%y');
	
	$PARS['{SUMMA}'] = number_format($accrual_data['summa'], 2, '.', ' ');
	
	$PARS['{TYPE_NAME}'] = $type_name;
	
	$PARS['{DESC}'] = nl2br($accrual_data['description']);
	
	$PARS['{ACCRUAL_ACTION_BLOCK}'] = $accrual_action_block;
	
	$PARS['{STATUS}'] = $accrual_status;
	
	return fetch_tpl($PARS, $accruals_list_item_tpl);
}

// Блок действий над начислениями
function fill_accrual_action_block($accrual_data)
{
	global $site_db, $current_user_id;
	
	$accruals_confirm_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/accruals_confirm_btn.tpl');
	$accruals_delete_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/accruals_delete_btn.tpl');
	
	if($current_user_id==$accrual_data['to_user_id'])
	{
		if(!$accrual_data['confirm'])
		{
			$acrual_action .= $accruals_confirm_btn_tpl;
		}
	}
	else if($current_user_id==$accrual_data['from_user_id'])
	{
		$acrual_action .= $accruals_delete_btn_tpl;
		
	}
	
	$PARS['{ACCRUAL_ID}'] = $accrual_data['accrual_id'];
	
	return  fetch_tpl($PARS, $acrual_action);
}

// Возвращает название типа начисления по его id
function get_accrual_type_name_by_type_id($type_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT * FROM ".MONEY_ACCRUALS_TYPES_TB." WHERE type_id='$type_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['type_name'];
}

// Форма добавления начисления
function fill_accruals_add_form()
{
	global $site_db, $current_user_id;
	
	$accruals_add_form_tpl = file_get_contents('templates/money/accruals_add_form.tpl');
	
	$option_tag_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	// Выбор типов начислений
	$sql = "SELECT * FROM ".MONEY_ACCRUALS_TYPES_TB."";
	
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		$PARS['{VALUE}'] = $row['type_id'];
		$PARS['{NAME}'] = $row['type_name'];
		$PARS['{SELECTED}'] = '';
		$types_list .= fetch_tpl($PARS, $option_tag_tpl);
	}
	
	$PARS['{ACCRUALS_TYPES}'] = $types_list;
	
	$accruals_add_form = fetch_tpl($PARS, $accruals_add_form_tpl);
	
	return $accruals_add_form;
}

// Список финансов между двумя пользователями пользоваетля
function fill_user_money_payments($user_id)
{
	global $site_db, $current_user_id;
	
	$main_tpl = file_get_contents('templates/money/user_money_payments.tpl');
	
	$money_add_form_tpl = file_get_contents('templates/money/money_add_form.tpl');
	
	$more_money_btn_tpl = file_get_contents('templates/money/more_money_btn.tpl');
	
	$no_money_tpl  = file_get_contents('templates/money/no_money.tpl');
 	
	//$user_id = $_GET['id'] ? $_GET['id'] : $current_user_id;
	
	//$operation = $_GET['operation'];
	
 	// Если нет связей между двумя пользователями (не являются между собой начальник-подчиненный)
	if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1)) && $user_id!=$current_user_id)
	{  
		header('Location: /money');
		exit();
	}
	
	// Выбираем последние переданные финансы
	$sql = "SELECT money_id FROM ".MONEY_TB." WHERE money_deleted<>1 ORDER by money_id DESC LIMIT 1";
	
	$row = $site_db->query_firstrow($sql);
	 
	if($row['money_id'])
	{
		$_SESSION['last_user_money_id'] = $row['money_id'];
	}

	// Очистка массива удаленных финансов
	if($_SESSION['money_deleted'])
	{
		$_SESSION['money_deleted'] = '';
	}
	
	$user_is_boss = 0;
	$user_is_worker = 0;
	
	// Пользователь является начальником
	if(check_user_access_to_user_content($user_id, array(1,0,0,1,0)))
	{
		$is_boss = 1;
	}
	else if(check_user_access_to_user_content($user_id, array(0,1,0,0,1)))
	{
		$user_is_worker = 1;
	}
	
	// Блок добавления денег
	if($user_id!=$current_user_id)
	{
		// Блок начилсений для выплат
		$accruals_block = fill_add_payments_accruals_select_block($user_id);
	
		$PARS['{ACCRUALS_BLOCK}'] = $accruals_block;
		
		$money_add_form = fetch_tpl($PARS, $money_add_form_tpl);
	}
	
	// Кол-во переданных финансов
	$money_count = get_count_money_between_users($user_id);
		
	// Кол-во страниц
	$pages_count = ceil($money_count/MONEY_PER_PAGE);
		
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_money_btn = $more_money_btn_tpl;
	}

	
	// Список переданных денег 
	$money_list = fill_user_money_list($user_id);
	
	if(!$money_list)
	{
		$money_list = $no_money_tpl;
	}
	
	// Панель поиска
	$search_panel = fill_money_payments_search_panel($user_id);
	
	if($user_id!=$current_user_id)
	{
		// Блок результирующих начислений
		$user_accruals_result_block = fill_user_accruals_result_block($user_id);
	}
	
	
	
	$PARS['{USER_ID}'] = $user_id;
	
	$PARS['{OPERATION}'] = $operation;
 	
	$PARS['{MONEY_ADD_FORM}'] = $money_add_form;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	$PARS['{MONEY_LIST}'] = $money_list;
	
	$PARS['{TOP_MENU}'] = $top_menu;
	
	$PARS['{MORE_MONEY}'] = $more_money_btn;	

	$PARS['{SEARCH_PANEL}'] = $search_panel;
	
	$PARS['{USER_ACCRUALS_RESULT}'] = $user_accruals_result_block;
	
	return fetch_tpl($PARS, $main_tpl);

}

// Блок выбора начислений при добавлении выплаты
function fill_add_payments_accruals_select_block($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$add_paymensts_accruals_block_tpl  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/add_paymensts_accruals_block.tpl');
	
	$add_paymensts_accruals_items_bl_tpl  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/add_paymensts_accruals_items_bl.tpl');
	
	$no_add_accruals_tpl  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/no_add_accruals.tpl');
	
	// Выбираем все типы
	$sql = "SELECT * FROM ".MONEY_ACCRUALS_TYPES_TB."";
	
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{
		$accrual_type_arr[$row['type_id']] = $row['type_name'];
	}
	
	// Выбираем начисления
	$sql = "SELECT * FROM ".MONEY_ACCRUALS_TB." WHERE to_user_id='$user_id' AND deleted<>1 AND paid=0";
	
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		$num = $num_arr[$row['type_id']];
		
		$accruals_types_arr[$row['type_id']][] = $row;
		
		$num_arr[$row['type_id']]++;
	}
	
	//echo "<pre>",print_r($num_arr);
	// 
	foreach($accruals_types_arr as $type_id => $accruals_arr)
	{
		$accruals_items = '';
		foreach($accruals_arr as $accrual_data)
		{
			$accruals_items .= fill_accruel_icon_item($accrual_data, $accrual_type_arr);
		}
		
		$PARS['{ACCRUALS_ITEMS}'] = $accruals_items;
		
		$num = $num_arr[$type_id];
		$accruals_select_list[$num.'_'.$type_id] = fetch_tpl($PARS, $add_paymensts_accruals_items_bl_tpl);
	}
	if(!$accruals_select_list)
	{
		return $no_add_accruals_tpl;
	}
	krsort($accruals_select_list);
	//echo "<pre>",print_r($accruals_select_list);
	
	$accruals_select_list = implode('', $accruals_select_list);
	
	$PARS['{ACCRUALS_LIST}'] = $accruals_select_list;
	
	return fetch_tpl($PARS, $add_paymensts_accruals_block_tpl);
}

// Формирует иконку выплаты для выбора
function fill_accruel_icon_item($accrual_data, $accrual_type_arr=array(), $icon_ton_act=0)
{
	if(!$icon_ton_act)
	{
		$add_paymensts_accruals_item_tpl  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/add_paymensts_accruals_item.tpl');
	}
	else
	{
		$add_paymensts_accruals_item_tpl  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/add_paymensts_accruals_item_not_action.tpl');
	}
	
	if($accrual_type_arr)
	{
		$accrual_type_name = $accrual_type_arr[$accrual_data['type_id']];
	}
	else
	{
		$accrual_type_name = get_accrual_type_name_by_type_id($accrual_data['type_id']);
	}
	
	switch($accrual_data['type_id'])
	{
		case 1:
			$item_class = 'acc_item_debt';
			$title_class = get_accruals_color_class(1);
		break;
		case 2:
			$item_class = 'acc_item_bonus';
			$title_class = get_accruals_color_class(2);
		break;
		case 3:
			$item_class = 'acc_item_fine';
			$title_class = get_accruals_color_class(3);
		break;
	}
	
	$PARS['{ACCRUAL_ID}'] = $accrual_data['accrual_id'];
	$PARS['{TYPE_ID}'] = $accrual_data['type_id'];
	$PARS['{ITEM_CLASS}'] = $item_class;
	$PARS['{TITLE_CLASS}'] = $title_class;
	$PARS['{SUMMA}'] = sum_process($accrual_data['summa']);
	$PARS['{SUMMA_NUM}'] = $accrual_data['summa'];
	$PARS['{DATE}'] = datetime($accrual_data['date'], '%d.%m.%Y');
	$PARS['{TYPE_NAME}'] = $accrual_type_name;
	
	return fetch_tpl($PARS, $add_paymensts_accruals_item_tpl);
}

// Панель фильтра в Начисления
function fill_money_accruals_search_panel($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$accruals_search_panel_tpl  = file_get_contents('templates/money/accruals_search_panel.tpl');
	
	$option_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	$option_disabled_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_disabled.tpl');
	
	$show_user_payments_link_tpl  = file_get_contents('templates/money/show_user_payments_link.tpl');
	 
	// Для подчиненного сотрудника выводим кнопку просмотра начислений
	if(check_user_access_to_user_content($user_id, array(0,1,0,0,1)))
	{
		$PARS['{USER_ID}'] = $user_id;
		
		$show_user_payments_link = fetch_tpl($PARS, $show_user_payments_link_tpl);	
	}
	
	// Список подчиненных пользователя
	$users_arr['workers'] = get_current_user_users_arrs(array(0,1,0,0,1), 1);
	
	foreach($users_arr as $type => $users)
	{
		if(empty($users))
		{
			continue;	
		}
		$PARS['{NAME}'] = 'Сотрудники';
		$PARS['{VALUE}'] = '0';
		$PARS['{SELECTED}'] = '';
		
		$users_list .= fetch_tpl($PARS, $option_disabled_tpl);
		
		foreach($users as $user_data)
		{
			$selected = $user_data['user_id'] == $_GET['id'] ? 'selected="selected"' : '';
	
			$PARS['{NAME}'] = $user_data['surname'].' '.$user_data['name'].' '.$user_data['middlename'];
				
			$PARS['{VALUE}'] = $user_data['user_id'];
				
			$PARS['{SELECTED}'] = $selected;
			
			$users_list .= fetch_tpl($PARS, $option_tpl);
		}
	}
	
	$PARS['{NAME}'] = 'Мои начисления';
	$PARS['{VALUE}'] = '0';
	$PARS['{SELECTED}'] = '';
	$users_list = fetch_tpl($PARS, $option_tpl).$users_list;
	
	
	$PARS['{USERS_LIST}'] = $users_list;
	
	$PARS['{SHOW_USER_PAYMENTS_LINK}'] = $show_user_payments_link;
	
	return fetch_tpl($PARS, $accruals_search_panel_tpl);
}

// Панель фильтра в Выплаты
function fill_money_payments_search_panel($user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	$payments_search_panel_tpl  = file_get_contents('templates/money/payments_search_panel.tpl');
	
	$option_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	$option_disabled_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_disabled.tpl');
	
	$show_user_accruals_link_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/show_user_accruals_link.tpl');
	
	// Для подчиненного сотрудника выводим кнопку просмотра начислений
	if(check_user_access_to_user_content($user_id, array(0,1,0,0,1)))
	{
		$PARS['{USER_ID}'] = $user_id;
		
		$show_user_accruals_link = fetch_tpl($PARS, $show_user_accruals_link_tpl);	
	}
	
	// Список начальников пользователя
	$users_arr['boss'] = get_current_user_users_arrs(array(1,0,0,1,0), 1);
	
	// Список подчиненных пользователя
	$users_arr['workers'] = get_current_user_users_arrs(array(0,1,0,0,1), 1);
	
	// Список коллег пользователя
	//$users_arr['colleagues'] = get_current_user_users_arrs(array(0,0,1,0,0), 1);
	
	foreach($users_arr as $type => $users)
	{
		if(empty($users))
		{
			continue;	
		}
		
		if($type=='boss')
		{
			$type_name = 'Руководители';
		}
		if($type=='workers')
		{
			$type_name = 'Сотрудники';
		}
		if($type=='colleagues')
		{
			$type_name = 'Коллеги';
		}
		
		$PARS['{NAME}'] = $type_name;
		$PARS['{VALUE}'] = '0';
		$PARS['{SELECTED}'] = '';
		
		$users_list .= fetch_tpl($PARS, $option_disabled_tpl);
		
		foreach($users as $user_data)
		{
			$selected = $user_data['user_id'] == $_GET['id'] ? 'selected="selected"' : '';
			
			// Начисления
			$user_accruals_sum = get_user_accruals_sum($user_data['user_id'], 1);
			
			$user_accruals_sum = $user_accruals_sum && $user_accruals_sum!='0.00' ? '('.$user_accruals_sum.' руб)' : '';
			
			$PARS['{NAME}'] = $user_data['surname'].' '.$user_data['name'].' '.$user_data['middlename'].' '.$user_accruals_sum;
				
			$PARS['{VALUE}'] = $user_data['user_id'];
				
			$PARS['{SELECTED}'] = $selected;
			
			
			$users_list .= fetch_tpl($PARS, $option_tpl);
		}
	}
	
	$PARS['{NAME}'] = 'Мои выплаты';
	$PARS['{VALUE}'] = '0';
	$PARS['{SELECTED}'] = '';
	$users_list = fetch_tpl($PARS, $option_tpl).$users_list;
	
	
	$PARS['{USERS_LIST}'] = $users_list;
	
	$PARS['{SHOW_USER_ACCRUALS_LINK}'] = $show_user_accruals_link;
	
	return fetch_tpl($PARS, $payments_search_panel_tpl);
}

// Список финансов пользователя
function fill_user_money_list($user_id, $page=1)
{
	global $site_db, $current_user_id;
	
	// Страничность
	$begin_pos = MONEY_PER_PAGE * ($page-1);
	
	$limit = " LIMIT ".$begin_pos.",".MONEY_PER_PAGE;
	
	// Удаленные в этой сессии клиенты
	$deleted_money_ids = implode(', ', $_SESSION['money_deleted']);
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_user_money_id'])
	{
		$and_money_id = " AND money_id <= '".$_SESSION['last_user_money_id']."' ";
	}
	
	if($deleted_money_ids)
	{
		$and_deleted_money = " OR money_id IN($deleted_money_ids) ";
	}
	
	// Если пользователь просматривает внутренние финансы с подчиненным, то показываем ему все финансы, которые подчиненному передавали
	if($user_id && check_user_access_to_user_content($user_id, array(0,1,0,0,1)))
	{
		$sql = "SELECT i.* FROM ".MONEY_TB." i
				WHERE  (money_to_user_id = '$user_id' OR (money_from_user_id = '$user_id' AND money_to_user_id = '$current_user_id') )
				AND (money_deleted<>1 $and_deleted_money) $and_money_id ORDER by money_id DESC $limit";
	}
	// Для просмотра коллег и начальников выводим финансы только между ними
	else if($user_id && check_user_access_to_user_content($user_id, array(1,0,1,1,0)))
	{
		$sql = "SELECT i.* FROM ".MONEY_TB." i
				WHERE  ((money_to_user_id = '$user_id' AND  money_from_user_id = '$current_user_id') OR (money_from_user_id = '$user_id' AND money_to_user_id = '$current_user_id') )
				AND (money_deleted<>1 $and_deleted_money) $and_money_id ORDER by money_id DESC $limit";
	}
	else
	{
		$sql = "SELECT i.* FROM ".MONEY_TB." i
				WHERE (money_to_user_id = '$current_user_id' OR money_from_user_id = '$current_user_id') 
				AND (money_deleted<>1 $and_deleted_money) $and_money_id ORDER by money_id DESC $limit";
	}
	
	 
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		// Заполнение элемента клиента
		$money_list .= fill_money_list_item($row,'',$user_id);
	}
	
	return $money_list;
}

// Заполнение элемента переданных финансов 
function fill_money_list_item($money_data, $for_personal=0, $user_id)
{
	global $site_db, $current_user_id, $user_obj;
	
	if($for_personal)
	{
		$money_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/money_list_item_on_personal.tpl');
	}
	else
	{
		$money_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/money_list_item.tpl');
	}
	 
	$money_edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/money_edit_tools.tpl');
	
	$money_confirm_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/money_confirm_btn.tpl');
	
	$money_not_confirm_str_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/money_not_confirm_str.tpl');
	
	$money_operation_type_in_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/money_operation_type_in.tpl');
	$money_operation_type_out_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/money_operation_type_out.tpl');
	
	// В выплату включены начисления
	if($money_data['has_accruals'])
	{
		// Получаем начисления, которые вошли в выплату
		$accrual_arr = get_accruals_arr_for_money($money_data['money_id']);
		
		foreach($accrual_arr as $accrual_data)
		{
			// Если штраф
			if($accrual_data['type_id']==3)
			{
				$money_summa -= $accrual_data['summa'];
			}
			else
			{
				$money_summa += $accrual_data['summa'];
			}
		}
		
		// Блок с начеслениями, которые были сделаны
		$money_accruals_block = fill_payments_accrual_block($money_data['money_id'], $accrual_arr);
		
	}
	else
	{
		$money_summa =  $money_data['money_summa'];
	}
	
	$money_summa = sum_process($money_summa);
	
	if($money_data['money_to_user_id']==$user_id)
	{
		$money_operation_type = $money_operation_type_in_tpl;
	}
	else if($money_data['money_from_user_id']==$user_id)
	{ 
		$money_operation_type = $money_operation_type_out_tpl;
	}
	
	$money_from = $money_data['money_from'];
	
	$money_type = $money_data['money_type'] ? $money_data['type_name'] : '';
	 
	$money_date = datetime($money_data['money_date'], '%j.%m.%y');
	
	
	// Если просматривает тот, кто передал деньги
	if($money_data['money_from_user_id'] == $current_user_id)
	{
		$PARS_1['{MONEY_ID}'] = $money_data['money_id'];
		
		$money_edit_tools = fetch_tpl($PARS_1, $money_edit_tools_tpl);
	}
	
	
	// Если передача денег не подтверждена
	if(!$money_data['money_confirm'])
	{
		$money_not_confirm_str = $money_not_confirm_str_tpl;
		if($current_user_id==$money_data['money_to_user_id'])
		{
			$PARS_1['{MONEY_ID}'] = $money_data['money_id'];
			$money_confirm_btn = fetch_tpl($PARS_1, $money_confirm_btn_tpl);
		}
		else
		{
			 
		}
		$money_not_confirm_class = 'not_confirm_row';
		
	}
	
	if(!$for_personal)
	{
		// отчет о деньгах
		$money_report_block = fill_money_report_block($money_data['money_id'], $money_data);
	}
	  
	if($money_data['money_to_user_id']==$user_id || $for_personal)
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($money_data['money_from_user_id']);
	}
	else if($money_data['money_from_user_id']==$user_id)
	{
		// Заполянем объект пользователя
		$user_obj->fill_user_data($money_data['money_to_user_id']);
	}
	// Превью аватарки пользователя
	//$user_avatar_src = get_user_preview_avatar_src($money_data['money_from_user_id'], $user_obj->get_user_image());
		
	$PARS['{USER_ID}'] = $money_data['money_from_user_id'];
	
	//$PARS['{AVATAR_SRC}'] = $user_avatar_src;
			
	$PARS['{USER_NAME}'] = $user_obj->get_user_name();
		
	$PARS['{USER_MIDDLENAME}'] = $user_obj->get_user_middlename();
			
	$PARS['{USER_SURNAME}'] = $user_obj->get_user_surname();
			
	$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
	
	$PARS['{MONEY_ID}'] = $money_data['money_id'];
		
	$PARS['{MONEY_SUMMA}'] = $money_summa;
	
	$PARS['{MONEY_TYPE}'] = $money_type;
	
	$PARS['{MONEY_FROM}'] = $money_from;
	
	$PARS['{MONEY_OPERATION_TYPE}'] = $money_operation_type;
 
 	$PARS['{MONEY_ACCRUALS_BLOCK}'] = $money_accruals_block;
	
	$PARS['{REPORT_BLOCK}'] = $money_report_block;
	
	$PARS['{NOT_CONFIRM_CLASS}'] = $money_not_confirm_class;
	
	$PARS['{MONEY_CONFIRM_BTN}'] = $money_confirm_btn;
	
	$PARS['{MONEY_DATE}'] = $money_date;
	
	$PARS['{EDIT_TOOLS}'] = $money_edit_tools;
	
	$PARS['{MONEY_FROM}'] = $money_data['money_from'] ? 'Откуда: '.$money_data['money_from']:'';
	
	$PARS['{MONEY_NOT_CONFIRM_STR}'] = $money_not_confirm_str;
	
	$PARS['{MONEY_COMMENT}'] = nl2br($money_data['money_comment']);
	
	return fetch_tpl($PARS, $money_list_item_tpl);
}

// Блок в выплатах начислений, которые были сделаны
function fill_payments_accrual_block($money_id, $accrual_arr)
{
	global $site_db, $current_user_id;
	
	$show_payments_accruals_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/show_payments_accruals.tpl');
	
	foreach($accrual_arr as $accrual_data)
	{
		$accruals_list .= fill_accruel_icon_item($accrual_data, '', 1);
	}
	
	if(!$accruals_list)
	{
		return '';
	}
	
	$PARS['{ACCRUALS_LIST}'] = $accruals_list;
	
	$PARS['{MONEY_ID}'] = $money_id;
	
	return fetch_tpl($PARS, $show_payments_accruals_tpl);
}

// Получает список начислений, которые вошли в выплату
function get_accruals_arr_for_money($money_id)
{
	global $site_db, $current_user_id;
	
	$show_payments_accruals_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/show_payments_accruals.tpl');
	
	$sql = "SELECT * FROM ".ACCRUALS_IN_PAYMENTS_TB." i
			LEFT JOIN ".MONEY_ACCRUALS_TB." j ON i.accrual_id=j.accrual_id
			WHERE i.money_id='$money_id'";
				
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res, 1))
	{
		$accrual_arr[$row['accrual_id']] = $row; 
	}
	
	return $accrual_arr;
}

// Список типов денег для передачи
function fill_money_types_list($type_id, $is_boss)
{
	global $site_db, $current_user_id;
	
	$tags_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option.tpl');
	
	// для начальника
	if($is_boss)
	{
		$and_for = "AND type_for_boss = 1";
	}
	else
	{
		$and_for = "AND type_for_workers = 1";
	}
	 
	$sql = "SELECT * FROM ".MONEY_TYPES_TB." WHERE 1 ";
 
	$res = $site_db->query($sql);
	 
	while($row=$site_db->fetch_array($res))
	{ 
		$selected = '';
		
		if($type_id)
		{
			$selected = $row['type_id'] == $type_id ? 'selected' : '';
		}

		$PARS['{NAME}'] = $row['type_name'];
				
		$PARS['{VALUE}'] = $row['type_id'];
				
		$PARS['{SELECTED}'] = $selected;
				
		$types_list .= fetch_tpl($PARS, $tags_tpl);
	}
	
	return $types_list;
}


// Блок сотрудников пользователя
function fill_money_workers_block($user_id)
{
	global $site_db, $user_obj, $_CURRENT_USER_WORKERS_ARR, $_CURRENT_USER_DEPUTY_WORKERS_ARR;
	
	$workers_list_tpl = file_get_contents('templates/money/workers_list.tpl');
	
	$user_for_select_item_tpl = file_get_contents('templates/money/user_for_select_item.tpl');
 
 	// Получаем временных и постоянных подчиненных
	$user_workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));
	
	foreach($user_workers_arr as $worker_id)
	{
		$PARS[''] = '';
		
		// Кол-во новых переданных финансов между пользователю от пользователя
		$new_count_money = get_new_count_money_from_user_to_user($user_id, $worker_id); 
		
		$new_count_money = $new_count_money ? '(+ '.$new_count_money.')' : '';
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($worker_id);
		
		$PARS['{USER_ID}'] = $worker_id;
		
		$PARS['{NAME}'] = $user_obj->get_user_name();
	
		$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{NEW_MONEY_COUNT}'] = $new_count_money;
		
		$workers_list .= fetch_tpl($PARS, $user_for_select_item_tpl);
	}
 
	$PARS['{USERS_LIST}'] = $workers_list;
	
	if($workers_list)
	{
		return fetch_tpl($PARS, $workers_list_tpl);
	}
	else
	{
		return '';
	}
}


// Блок коллег пользователя
function fill_money_colleagues_block($user_id)
{
	global $site_db, $user_obj, $_CURRENT_USER_COLLEAGUES_ARR;
	
	$colleagues_list_tpl = file_get_contents('templates/money/colleagues_list.tpl');
	
	$user_for_select_item_tpl = file_get_contents('templates/money/user_for_select_item.tpl');
	 
	foreach($_CURRENT_USER_COLLEAGUES_ARR as $colleagues_id)
	{
		$PARS[''] = '';
		
		// Кол-во новых переданных финансов между пользователю от пользователя
		$new_count_money = get_new_count_money_from_user_to_user($user_id, $colleagues_id); 
		
		$new_count_money = $new_count_money ? '(+ '.$new_count_money.')' : '';
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($colleagues_id);
		
		$PARS['{USER_ID}'] = $colleagues_id;
		
		$PARS['{NAME}'] = $user_obj->get_user_name();
	
		$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{NEW_MONEY_COUNT}'] = $new_count_money;
		
		$colleagues_list .= fetch_tpl($PARS, $user_for_select_item_tpl);
	}
	 
 
	$PARS['{USERS_LIST}'] = $colleagues_list;
	
	if($colleagues_list)
	{
		return fetch_tpl($PARS, $colleagues_list_tpl);
	}
	else
	{
		return '';
	}
}


// Блок начальников пользователя
function fill_money_boss_block($user_id)
{
	global $site_db, $user_obj;
	
	$bosses_list_tpl = file_get_contents('templates/money/bosses_list.tpl');
	
	$user_for_select_item_tpl = file_get_contents('templates/money/user_for_select_item.tpl');
	
	// Список начальников пользователя
	$boss_arr = get_current_user_users_arrs(array(1,0,0,1,0));
	
	foreach($boss_arr as $boss_id)
	{
		$PARS[''] = '';
		
		// Кол-во новых переданных финансов между пользователю от пользователя
		$new_count_money = get_new_count_money_from_user_to_user($user_id, $boss_id); 
		
		$new_count_money = $new_count_money ? '(+ '.$new_count_money.')' : '';
		 
		// Заполянем объект пользователя
		$user_obj->fill_user_data($boss_id);
		
		$PARS['{USER_ID}'] = $boss_id;
		
		$PARS['{NAME}'] = $user_obj->get_user_name();
	
		$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{NEW_MONEY_COUNT}'] = $new_count_money;
		
		$boss_list .= fetch_tpl($PARS, $user_for_select_item_tpl);
	}
	
	$PARS['{USERS_LIST}'] = $boss_list;
	
	if($boss_list)
	{
		return fetch_tpl($PARS, $bosses_list_tpl);
	}
	else
	{
		return '';
	}
}
// Блок списка отчетов для переданных финансов
function fill_money_report_block($money_id, $money_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$money_list_item_report_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/money_list_item_report_block.tpl');
	
	$money_report_no_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/money_report_no.tpl');
	
	$money_report_add_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/money_report_add_form.tpl');
	
	// Форма добавления отчета
	$PARS_1['{MONEY_ID}'] = $money_data['money_id'];
	
	$add_task_report_btn_value = $is_boss ? 'Добавить комментарий к отчету' : 'Добавить отчет';
	
	$PARS_1['{ADD_REPORT_BTN_VALUE}'] = $add_task_report_btn_value;
	
	$money_report_add_form = fetch_tpl($PARS_1, $money_report_add_form_tpl);
	
	// Список отчетов
	$reports_list = fill_money_reports_list($money_id, $money_data);
	
	if(!$reports_list)
	{
		$reports_list = $money_report_no_tpl;
	}
	
	$PARS['{MONEY_REPORT_ADD_FORM}'] = $money_report_add_form;
	
	$PARS['{MONEY_ID}'] = $money_data['money_id'];
	
	$PARS['{REPORTS_LIST}'] = $reports_list;
		
	return fetch_tpl($PARS, $money_list_item_report_block_tpl);
	
	
	
}

// Список отчетов для переданных финансов
function fill_money_reports_list($money_id, $money_data)
{
	global $site_db, $current_user_id, $user_obj;
	
	$money_list_item_report_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/money/money_list_item_report_item.tpl');
	
	// Выбор всех отчетов для 
	$sql = "SELECT * FROM ".MONEY_REPORTS_TB." WHERE money_id='$money_id'";
	
	$res = $site_db->query($sql);
			
	while($row=$site_db->fetch_array($res))
	{
		$report_date =  datetime($row['report_date'], '%j %M в %H:%i');
		
		// Комментарий к отчету сотруднику
		if($row['user_id'] == $money_data['money_from_user_id'])
		{  
			$report_class = 'task_report_boss';
		}
		// Отчет сотрудника
		else
		{
			$report_class = 'task_report_worker';
		}
		
		// Заполянем объект пользователя
		$user_obj->fill_user_data($row['user_id']);
		// Превью аватарки пользователя
		$user_avatar_src = get_user_preview_avatar_src($row['user_id'], $user_obj->get_user_image());
	 
		$PARS['{USER_ID}'] = $row['user_id'];
		
		$PARS['{AVATAR_SRC}'] = $user_avatar_src;
		
		$PARS['{NAME}'] = $user_obj->get_user_name();
	
		$PARS['{MIDDLENAME}'] = $user_obj->get_user_middlename();
		
		$PARS['{SURNAME}'] = $user_obj->get_user_surname();
		
		$PARS['{USER_POSITION}'] = $user_obj->get_user_position();
		
		$PARS['{REPORT_ID}'] = $row['report_id'];
		
		$PARS['{REPORT_CLASS}'] = $report_class;
		
		$PARS['{REPORT_DATE}'] = $report_date;
		
		$PARS['{REPORT_TEXT}'] = nl2br($row['report_text']);
		
		$PARS['{MONEY_ID}'] = $money_data['money_id'];
		
		$reports_list .= fetch_tpl($PARS, $money_list_item_report_item_tpl);
	}
	
	return $reports_list;
}

// Кол-во всех неачислений для пользователя
function get_user_accruals_count($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM ".MONEY_ACCRUALS_TB." WHERE to_user_id='$user_id' AND deleted <> 1";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Количество переданных финансов пользователю
function get_count_money_between_users($user_id, $user_2)
{
	global $site_db, $current_user_id;
	
	// Если пользователь просматривает внутренние финансы с подчиненным, то показываем ему все финансы, которые подчиненному передавали
	if(check_user_access_to_user_content($user_id, array(0,1,0,0,1)))
	{
		$sql = "SELECT COUNT(*) as count FROM ".MONEY_TB." i
				WHERE  (money_to_user_id = '$user_id' OR (money_from_user_id = '$user_id' AND money_to_user_id = '$current_user_id') )
				AND money_deleted<>1";
	}
	else if(check_user_access_to_user_content($user_id, array(1,0,1,1,0)))
	{
		$sql = "SELECT COUNT(*) as count FROM ".MONEY_TB." i
				WHERE  ((money_to_user_id = '$user_id' AND money_from_user_id = '$current_user_id') OR (money_from_user_id = '$user_id' AND money_to_user_id = '$current_user_id') )
				AND money_deleted<>1";
	}
	else
	{
		$sql = "SELECT COUNT(*) as count FROM ".MONEY_TB."
				WHERE (money_from_user_id = '$current_user_id' OR money_to_user_id = '$current_user_id') AND money_deleted<>1";
	}
	
 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

// Кол-во новых переданных финансов пользователю
function get_new_money_for_user($user_id)
{
	global $site_db, $current_user_id;
	
	// Массив пользователей, с которыми контактирует пользователь
	$users_arr = get_current_user_users_arrs(array(1,1,1,1,1));
	
	if($users_arr)
	{
		$and_users_from = " AND money_from_user_id IN(".implode(',', $users_arr).")";
	}
	$sql = "SELECT COUNT(*) as count FROM ".MONEY_TB." WHERE money_to_user_id='$user_id' AND money_deleted<>1 AND money_confirm=0 $and_users_from";
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

function get_new_count_money_from_user_to_user($to_user_id, $from_user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM ".MONEY_TB." 
			WHERE money_to_user_id='$to_user_id' AND money_from_user_id='$from_user_id' AND money_deleted<>1 AND money_confirm=0";
	 
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}

//Кол-во новых начислений пользователю
function get_new_accruals_count($user_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT COUNT(*) as count FROM ".MONEY_ACCRUALS_TB." WHERE to_user_id='$user_id' AND deleted <> 1 AND confirm=0";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
	
}

function get_accruals_color_class($type_id)
{
	switch($type_id)
	{
		case '1':
			$type_class = 'accruals_debt'; // задолженность
		break;
		case '2':
			$type_class = 'accruals_bonus'; // бонус
		break;
		case '3':
			$type_class = 'accruals_fine'; // штраф
		break;
	}
	
	return $type_class;
}

// Сумма начислений перед сотрудником
function get_user_accruals_sum($user_id, $formate=0)
{
	global $site_db, $current_user_id;
	
	$result_accruals_sum = 0;
	
	// Выбираем начисления пользователя
	$sql = "SELECT * FROM ".MONEY_ACCRUALS_TB." WHERE to_user_id='$user_id' AND deleted <> 1 AND paid=0";
	
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{
		$result_summ[$row['type_id']] += $row['summa'];
		
		// Если штраф
		if($row['type_id']==3)
		{
			$result_accruals_sum -= $row['summa'];
		}
		else
		{
			$result_accruals_sum += $row['summa'];
		}
	}
	if($formate)
	{
		return number_format($result_accruals_sum, 2, '.', ' ');
	}
	else
	{
		return $result_accruals_sum;
	}
}

// Сумма выплат
function get_user_payments_summ_for_period($user_id, $days , $formate=0)
{
	global $site_db, $user_obj, $current_user_id;
	
	$mk_time_days_from = mktime() - 3600 * 24 * $days;
	 
	$date_s = date('Y-m-d', $mk_time_days_from);
	
	$result_sum = 0;
	
	// Переданные внутринние финансы
	$sql = "SELECT i.* FROM ".MONEY_TB." i
			WHERE  money_to_user_id = '$user_id' AND money_deleted<>1 AND money_date>='$date_s' ORDER by money_id DESC";
	 
	$res = $site_db->query($sql);
	  
	while($row=$site_db->fetch_array($res))
	{ 
		if($row['has_accruals'])
		{
			// Получаем начисления, которые вошли в выплату
			$accrual_arr = get_accruals_arr_for_money($row['money_id']);
			
			foreach($accrual_arr as $accrual_data)
			{
				// Если штраф
				if($accrual_data['type_id']==3)
				{
					$result_sum -= $accrual_data['summa'];
				}
				else
				{
					$result_sum += $accrual_data['summa'];
				}
			}	
		}
		else
		{
			// Подводим итог
			$result_sum += $row['money_summa'];
		}
	}
	
	if($formate)
	{
		return number_format($result_sum, 2, '.', ' ');
	}
	else
	{
		return $result_sum;
	}
}
?>