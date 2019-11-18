<?php
//  
function fill_posttr($user_id)
{

	global $site_db, $current_user_id;
	
	$main_tpl = file_get_contents('templates/post_tracking/post_tracking.tpl');
	
	$more_btn_tpl = file_get_contents('templates/post_tracking/more_btn.tpl');
	
	// Очистка массива удаленных контактов
	if($_SESSION['posttr_delete'])
	{
		$_SESSION['posttr_delete'] = '';
	}
	
	// Выбираем последний добавленный выговор
	$sql = "SELECT tracking_id FROM ".POSTTR_TB." WHERE deleted=0 ORDER by tracking_id DESC LIMIT 1";
	
	$row = $site_db->query_firstrow($sql);
	
	if($row['tracking_id'])
	{
		$_SESSION['last_tracking_id'] = $row['tracking_id'];
	}
	
	// Архив 
	$archive_checked = $_GET['archive'] ? 'checked="checked"' : '';
	$is_archive = $_GET['archive'] ? 1 : 0;
	
	// Ключевое слово
	$key_words = $_GET['key_words'];
	
	// Тип списка трекингов
	$list_type = $_GET['list_type'] ? $_GET['list_type'] : 1;
	
	// Тип списка трекингов
	$status = $_GET['status'] ? $_GET['status'] : 0;
	
		
	// Форма добавления
	$add_form = get_posttr_add_form();
	
	// Список трекингов
	$tracking_list = fill_post_tracking_list(1, array(), $is_archive, $list_type, $key_words, $status);
	
	// Кол-во трекингов
	$post_tracking_count = get_post_tracking_count($is_archive, $list_type, $key_words, $status);
	
	// Кол-во страниц
	$pages_count = ceil($post_tracking_count/POSTTR_PER_PAGE);
		
	// Если страниц больше 1
	if($pages_count > 1)
	{
		$more_btn = $more_btn_tpl;
	}
	
 	$archive_count = get_post_tracking_count(1, $list_type, $key_words, $status);
	
	
	
	$PARS['{ADD_FORM}'] = $add_form;
	
	$PARS['{TRACKING_LIST}'] = $tracking_list;
	
	$PARS['{MORE_BTN}'] = $more_btn;
	
	$PARS['{PAGES_COUNT}'] = $pages_count;
	
	$PARS['{ARCHIVE_CHECKED}'] = $archive_checked;
	
	$PARS['{IS_ARCHIVE}'] = $is_archive;
	
	$PARS['{ARCHIVE_COUNT}'] = $archive_count;
	
	$PARS['{LIST_TYPE}'] = $list_type;
	
	$PARS['{KEY_WORDS}'] = $key_words;
	
	$PARS['{STATUS}'] = $status;
	
	 
	
	return fetch_tpl($PARS, $main_tpl);
}

// Блок трекингов для привязанного к ним контена
function fill_post_tracking_block_in_linked_content($link_content)
{
	// Очистка массива удаленных контактов
	if($_SESSION['posttr_delete'])
	{
		$_SESSION['posttr_delete'] = '';
	}
	if(!$link_content['deal_id'] && !$link_content['client_id'])
	{
		return '';
	}
	$tracking_list_in_linked_content_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/post_tracking/tracking_list_in_linked_content.tpl');
	
	$tracking_list = fill_post_tracking_list(1, $link_content);
	
	// Если трекингов нет
	if(!$tracking_list)
	{
		return '';
	}
	
	$PARS['{TRACKING_LIST}'] = $tracking_list;
	
	return fetch_tpl($PARS, $tracking_list_in_linked_content_tpl);
}
// Возвращает часть запроса для параметров поиска трекингов
function get_post_tracking_part_query($is_archive, $list_type, $key_words, $status)
{
	global $site_db, $current_user_id;
	
	if($status)
	{
		$tmp = split('_', $status);
		
		$operation_type_id = $tmp[0];
		
		$operation_attribute_id = $tmp[1];
		
		if($operation_type_id==1)
		{
			$part_1_arr[] = " (j.operation_type_id = 0 OR (j.operation_type_id = 8 AND j.operation_attribute_id IN (4,5))) ";
		}
		else if($operation_type_id>1 && $operation_attribute_id)
		{
			$part_1_arr[] = " (j.operation_type_id='$operation_type_id' AND j.operation_attribute_id='$operation_attribute_id') ";
		}
		else if($operation_type_id > 1)
		{
			$part_1_arr[] = " j.operation_type_id='$operation_type_id'";
		}
		
		//operation_type_id'] == 8 && $tracking_last_status_arr['operation_attribute_id'
		
		 
	}
	
	// Архив
	if($is_archive)
	{
		$part_1_arr[] = "archive='1'";
	}
	else
	{
		$part_1_arr[] = "archive='0'";
	}
	
	// Тип списка
	// Все трекинги
	if($list_type==2)
	{
		 
	}
	// Трекинги пользователя
	else
	{
		$part_1_arr[] = "user_id='$current_user_id'";
	}
	
	
	if($key_words)
	{
		// Поиск по сделкам
		$sql = "SELECT deal_id FROM tasks_deals WHERE deal_name LIKE '%$key_words%' AND deal_deleted=0";
		
		$res = $site_db->query($sql);
			 
		while($row=$site_db->fetch_array($res))
		{		
			$deals_id[] = $row['deal_id'];
		}
		
		// Поиск по клиентам
		$sql = "SELECT client_id FROM tasks_clients WHERE client_name LIKE '%$key_words%' AND client_deleted=0";
		//echo $sql;
		$res = $site_db->query($sql);
			 
		while($row=$site_db->fetch_array($res))
		{		
			$clients_id[] = $row['client_id'];
		}
		
		if($deals_id)
		{
			$part_2_arr[] = "deal_id IN(".implode(',', $deals_id).")";
		}
		
		if($clients_id)
		{
			$part_2_arr[] = "client_id IN(".implode(',', $clients_id).")";
		}
	
		$key_words = value_proc($key_words, 0);
		
		$part_2_arr[] = "(tracking_barcode = '$key_words' OR tracking_desc LIKE '%$key_words%')";
		 
	}
	
	//$part_2_arr[] = 1;
	
	if($part_1_arr)
	{
		$result_query[] = implode(' AND ', $part_1_arr);
	}
	 
	if($part_2_arr)
	{
		$result_query[] = '( '.implode(' OR ', $part_2_arr).' )';
	}
	
	//print_r($part_2_arr);
	 
	
	$query = " AND ".implode(' AND ', $result_query);
	
	return $query;
}

// Список трекингов
function fill_post_tracking_list($page, $link_content=array(), $is_archive, $list_type, $key_words, $status)
{
	global $site_db, $current_user_id;
	
	$tracking_list_no_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/post_tracking/tracking_list_no.tpl');
	
	$page = $page ? $page : 1;
	
	// Если выводится список на странице привязанного контента, то выводим все найденные
	if(!$link_content)
	{
		// Страничность
		$begin_pos = POSTTR_PER_PAGE * ($page-1);
		
		$limit = " LIMIT ".$begin_pos.",".POSTTR_PER_PAGE;
	}
	
	// Удаленные в этой сессии клиенты
	$deleted_trackings_ids = implode(', ', $_SESSION['posttr_delete']);
	
	if($deleted_trackings_ids)
	{
		$and_deleted_trackings = " OR tracking_id IN($deleted_trackings_ids) ";
	}
	
	// Последний добавленный пользователем контакт
	if($_SESSION['last_tracking_id'])
	{
		$and_tracking_id = " AND tracking_id <= '".$_SESSION['last_tracking_id']."' ";
	}
	
	// Если вывести список трекингов для сделки
	if($link_content['deal_id']>0)
	{
		$and_cont_query = " AND deal_id='".$link_content['deal_id']."'";
	}
	// Если вывести список трекингов для контрагента
	else if($link_content['client_id']>0)
	{
		$and_cont_query = " AND client_id='".$link_content['client_id']."'";
	}
	 
	
	// Если вывести трекинг в сделке или клиенте, то флаг архива не имеет значения
	if(!$link_content)
	{
		 
	}
	
	// Часть запроса по параметрам
	$query_part = get_post_tracking_part_query($is_archive, $list_type, $key_words, $status);
	
	// Если вывести список трекингов
	if(!$link_content)
	{
		// Выбор всех трекингов пользователя
		$sql = "SELECT i.* FROM ".POSTTR_TB." i
				LEFT JOIN ".POSTTR_STATUSES_TB." j ON i.status_id=j.status_id
				WHERE (deleted=0 $and_deleted_trackings)  $query_part ORDER by tracking_id DESC $limit";
	}
	//Список трекингов для контента
	else
	{
		// Выбор всех трекингов пользователя
		$sql = "SELECT * FROM ".POSTTR_TB." i WHERE (i.deleted=0) $and_cont_query ORDER by i.tracking_id DESC $limit";
	}
	 
	 
	  
	$res = $site_db->query($sql);
			 
	while($row=$site_db->fetch_array($res))
	{		
		$tracking_list .= fill_post_tracking_list_item($row);
	}
	
	if(!$tracking_list && !$link_content)
	{
		$tracking_list = $tracking_list_no_tpl;
	}
	
	return $tracking_list;
}
// Заполнение элемента списка трекингов
function fill_post_tracking_list_item($tracking_data)
{
	global $site_db, $current_user_id;
	
	$tracking_list_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/post_tracking/tracking_list_item.tpl');
	
	$tracking_list_item_link_deal_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/post_tracking/tracking_list_item_link_deal.tpl');
	
	$tracking_list_item_link_client_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/post_tracking/tracking_list_item_link_client.tpl');
	$from_archive_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/post_tracking/from_archive_btn.tpl');
	
	$to_archive_btn_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/post_tracking/to_archive_btn.tpl');
	
	$edit_tools_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/post_tracking/edit_tools.tpl');
	
	// Название клиента
	if($tracking_data['client_id']>0)
	{
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_clients.php';
		$client_name = get_client_name_by_client_id($tracking_data['client_id']);
		$PARS['{CLIENT_ID}'] = $tracking_data['client_id'];
		$PARS['{CLIENT_NAME}'] = $client_name;
		$posttr_link = fetch_tpl($PARS, $tracking_list_item_link_client_tpl);
		
	}
	// Название сделки
	else if($tracking_data['deal_id']>0)
	{
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deals.php';
		$deal_name = get_deal_name_by_deal_id($tracking_data['deal_id']);
		$PARS['{DEAL_ID}'] = $tracking_data['deal_id'];
		$PARS['{DEAL_NAME}'] = $deal_name;
		$posttr_link = fetch_tpl($PARS, $tracking_list_item_link_deal_tpl);
	}
	
	// Выводим блок редактирования трекинга
	if($tracking_data['user_id']==$current_user_id)
	{
		if($tracking_data['archive'])
		{
			$archive_btn = $from_archive_btn_tpl;
		}
		else
		{
			$archive_btn = $to_archive_btn_tpl;
		}
		$PARS['{ARCHIVE_BTN}'] = $archive_btn;
		$PARS['{TRACKING_ID}'] = $tracking_data['tracking_id'];
		 
		$edit_tools = fetch_tpl($PARS, $edit_tools_tpl);
	}
	
	// Текущий статус трекинга
	$tracking_last_status = fill_tracking_last_status($tracking_data['tracking_id']); 
	
	// 
	$author = get_formate_user_name($tracking_data['user_id']);
	
	
	$PARS['{TRACKING_ID}'] = $tracking_data['tracking_id'];
	
	$PARS['{TRACKING_BARCODE}'] = $tracking_data['tracking_barcode'];
	
	$PARS['{DATE_ADD}'] = datetime($tracking_data['date_add'], '%d.%m.%Y');
	
	$PARS['{TRACKING_DESC}'] = nl2br($tracking_data['tracking_desc']);
	
	$PARS['{POSTTR_LINK}'] = $posttr_link;
	
	$PARS['{EDIT_TOOLS}'] = $edit_tools;
	
	$PARS['{LAST_STATUS}'] = $tracking_last_status;
	 
	$PARS['{AUTHOR}'] = $author;
	 
	return fetch_tpl($PARS, $tracking_list_item_tpl);
}

// Блок текущего статуса трекинга
function fill_tracking_last_status($tracking_id)
{
	global $site_db, $current_user_id;
	
	$tracking_last_status_block_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/post_tracking/tracking_last_status_block.tpl');
	
	$tracking_no_status_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/post_tracking/tracking_no_status.tpl');
	
	// Получение текущего статуса трекинга
	$tracking_last_status_arr = get_tracking_last_status($tracking_id, 1);
	
	if($tracking_last_status_arr['operation_type_id'] == 8 && $tracking_last_status_arr['operation_attribute_id']==2)
	{
		$status_back = 'postr_status_1';
	}
	else if($tracking_last_status_arr['operation_type_id'] == 2)
	{
		$status_back = 'postr_status_3';
	}
	else if($tracking_last_status_arr['operation_type_id'] == 12)
	{
		$status_back = 'postr_status_2';
	}
	
	$PARS['{TRACKING_ID}'] = $tracking_id; 
	$PARS['{STATUS}'] = $tracking_last_status_arr['operation_type'];
	$PARS['{ATTRIBUTE}'] = $tracking_last_status_arr['operation_attribute'];
	$PARS['{DATE}'] = datetime($tracking_last_status_arr['operation_date'], '%d.%m.%Y в %H:%i');
	$PARS['{STATUS_BACK}'] = $status_back;
	$PARS['{OPERATION_PLACE_NAME}'] = $tracking_last_status_arr['operation_place_name'] ? ', '.$tracking_last_status_arr['operation_place_name'] : '';
	
	if(!$tracking_last_status_arr['operation_type'])
	{
		return fetch_tpl($PARS, $tracking_no_status_tpl);
	}
	else return fetch_tpl($PARS, $tracking_last_status_block_tpl);
}

// Выбор актуального статуса трекинга
function get_tracking_last_status($tracking_id, $return_arr = 0)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT * FROM ".POSTTR_STATUSES_TB." WHERE tracking_id='$tracking_id' ORDER by operation_date DESC LIMIT 1";
	
	$row = $site_db->query_firstrow($sql);
	
	
	if($return_arr)
	{
		return $row;
	}
	else return $row['operation_type'];
}

// Кол-во трекингов пользователя
function get_post_tracking_count($is_archive, $list_type, $key_words, $status)
{
	global $site_db, $current_user_id;
	
	// Часть запроса по параметрам
	$query_part = get_post_tracking_part_query($is_archive, $list_type, $key_words, $status);
	
	// Выбор всех трекингов пользователя
	$sql = "SELECT COUNT(*) as count FROM ".POSTTR_TB." i
			LEFT JOIN ".POSTTR_STATUSES_TB." j ON i.status_id=j.status_id
			WHERE i.deleted=0  $query_part";
		
		 
		
	// Выбор всех трекингов пользователя
	//$sql = "SELECT COUNT(*) as count FROM ".POSTTR_TB." WHERE user_id='$current_user_id' AND deleted=0 $is_archive";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row['count'];
}
 
// Форма доабвления события
function get_posttr_add_form()
{
	$add_form_tpl = file_get_contents('templates/post_tracking/add_form.tpl');
	
	$PARS['{ADD_FORM}'] = $is_wks;
	
	return fetch_tpl($PARS, $add_form_tpl);
}

function is_valid_tracking_formate($tracking_number)
{
	if (preg_match('/^[0-9]{14}|[A-Z]{2}[0-9]{9}[A-Z]{2}$/', $tracking_number))
	{
		return true;
    }
	else return false;
}

function get_tracking_data($tracking_id)
{
	global $site_db, $current_user_id;
	
	$sql = "SELECT * FROM ".POSTTR_TB." WHERE tracking_id='$tracking_id'";
	
	$row = $site_db->query_firstrow($sql);
	
	return $row;
}

// Проверка статусов трекинга  через сервис Почты России
function check_tracking_status_post_api($tracking_id)
{
	global $site_db, $current_user_id, $postApiLogin, $postApiPass;
	
	// Данные трекинга
	$tarcking_data = get_tracking_data($tracking_id);
	
	if(!$tarcking_data['tracking_barcode'])
	{
		return '';
	}
	
	// Проеряем последний статус
	$sql = "SELECT * FROM ".POSTTR_STATUSES_TB." WHERE tracking_id='$tracking_id' ORDER by status_id DESC";
	$row = $site_db->query_firstrow($sql);
	 
	
	// проверяем 8 часовой лимит
	if($row['status_id'] && to_mktime($row['date_check'])+3600*8 > time())
	{
		return 0;
	}
	 
	// Выбор статусов трекинга
	$sql = "SELECT * FROM ".POSTTR_STATUSES_TB." WHERE tracking_id='$tracking_id'";
	
	$res = $site_db->query($sql);
	
	$tracking_statuses = array();	
		
	while($row=$site_db->fetch_array($res))
	{	
		// Составляем массив статусов трекинга, которые записаны уже в таблицу
		$key = $row['operation_type_id'].'-'.$row['operation_attribute_id'].'-'.$row['operation_place_postal_code'].'-'.$row['operation_date'];
		array_push($tracking_statuses, $key); 
		 
	}
	
	$login =  'DcKtRvpESRNcJs';
	$password = 'mj9oDWDscd2Y';
	$barcode = $tarcking_data['tracking_barcode'];
	
	$request = '<?xml version="1.0" encoding="UTF-8"?>
                <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:oper="http://russianpost.org/operationhistory" xmlns:data="http://russianpost.org/operationhistory/data" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Header/>
                <soap:Body>
                   <oper:getOperationHistory>
                      <data:OperationHistoryRequest>
                         <data:Barcode>'.$barcode.'</data:Barcode>  
                         <data:MessageType>0</data:MessageType>
                         <data:Language>RUS</data:Language>
                      </data:OperationHistoryRequest>
                      <data:AuthorizationHeader soapenv:mustUnderstand="1">
                         <data:login>'.$login.'</data:login>
                         <data:password>'.$password.'</data:password>
                      </data:AuthorizationHeader>
                   </oper:getOperationHistory>
                </soap:Body>
             </soap:Envelope>';

	$client = new SoapClient("https://tracking.russianpost.ru/rtm34?wsdl",  array('trace' => 1, 'soap_version' => SOAP_1_2));
	
	$xml = $client->__doRequest($request, "https://tracking.russianpost.ru/rtm34", "getOperationHistory", SOAP_1_2);
	
	 //$_SESSION['gg'] = $xml;
	 
	$xml = simplexml_load_string($xml);
	
	// echo "<pre>",print_r($xml->children('S', true)->Body->children('ns7', true)->getOperationHistoryResponse->children('ns3', true));
	
	$i = 0;
	
	$tracking_statuses_api = array();
	
	foreach ($xml->children('S', true)->Body->children('ns7', true)->getOperationHistoryResponse->children('ns3', true)->OperationHistoryData->historyRecord as $item)
	{
		$OperTypeId = value_proc($item->OperationParameters->OperType->Id);
		$OperTypeName = value_proc($item->OperationParameters->OperType->Name);
		$OperOperAttrId = value_proc($item->OperationParameters->OperAttr->Id);
		$OperOperAttrName = value_proc($item->OperationParameters->OperAttr->Name);
		$OperDate =  formate_tracking_date($item->OperationParameters->OperDate);
		$OperationAddressIndex = value_proc($item->AddressParameters->OperationAddress->Index);
		$OperationAddressDescription = value_proc($item->AddressParameters->OperationAddress->Description);
		 
		$api_tracking_data[$i] = array('OperTypeId' => $OperTypeId, 'OperTypeName' => $OperTypeName, 'OperOperAttrId' => $OperOperAttrId, 'OperOperAttrName' => $OperOperAttrName, 'OperDate' => $OperDate, 'OperationAddressIndex' => $OperationAddressIndex, 'OperationAddressDescription' => $OperationAddressDescription); 
		 
		// Составляем массив статусов трекинга
		$key = $OperTypeId.'-'.$OperOperAttrId.'-'.$OperationAddressIndex.'-'.$OperDate;
		$tracking_statuses_api[$i] = $key;
		$i++;
		
	}
	//print_r($tracking_statuses_api);
	
	// Вычисление статуса трекинга, которого нет в таблице
	$statuses_diff = array_diff ($tracking_statuses_api, $tracking_statuses);
		
	
	// Добавляем новый статус
	foreach($statuses_diff as $i => $val)
	{
		add_tracking_status($tracking_id, $api_tracking_data[$i]);
		$status_added = 1;
	}
	
	// Если не был добавлен новый статус,отмечаем последнее обновление по api почты
	if(!$status_added)
	{
		// Находим последний статус трекинга
		$sql = "SELECT * FROM ".POSTTR_STATUSES_TB." WHERE tracking_id='$tracking_id' ORDER by status_id DESC";
		$tr_row = $site_db->query_firstrow($sql);
		
		// Если статус найден, обновляем у него дату
		if($tr_row['status_id'])
		{
			$sql = "UPDATE ".POSTTR_STATUSES_TB." SET date_check=NOW() WHERE status_id='".$tr_row['status_id']."'";
			$site_db->query($sql);
		}
		else
		{
			// Добавляем пустой статус, с указанием даты последнего обращения по api
			add_tracking_status($tracking_id, array());
		}
	}
			
	$success = 1;
	
	return $success;
}

// Проверка статусов трекинга  через сервис Почты России
function check_tracking_status_post_api1($tracking_id)
{
	global $site_db, $current_user_id, $postApiLogin, $postApiPass;
	
	// Данные трекинга
	$tarcking_data = get_tracking_data($tracking_id);
	
	if(!$tarcking_data['tracking_barcode'])
	{
		return '';
	}
	
	// Проеряем последний статус
	$sql = "SELECT * FROM ".POSTTR_STATUSES_TB." WHERE tracking_id='$tracking_id' ORDER by status_id DESC";
	$row = $site_db->query_firstrow($sql);
	 
	 //echo to_mktime($row['date_check'])+33,' ',time();
	// проверяем 8 часовой лимит
	if($row['status_id'] && to_mktime($row['date_check'])+3600*8 > time())
	{
		return 0;
	}
	 
	// Выбор статусов трекинга
	$sql = "SELECT * FROM ".POSTTR_STATUSES_TB." WHERE tracking_id='$tracking_id'";
	
	$res = $site_db->query($sql);
	
	$tracking_statuses = array();	
		
	while($row=$site_db->fetch_array($res))
	{	
		// Составляем массив статусов трекинга, которые записаны уже в таблицу
		$key = $row['operation_type_id'].'-'.$row['operation_attribute_id'].'-'.$row['operation_place_postal_code'].'-'.$row['operation_date'];
		array_push($tracking_statuses, $key); 
		 
	}
	 
	// Пробуем подключиться к Russian Post Api и получить данные трекинга 
	try {
		
		$client = new RussianPostAPI('','',$postApiLogin, $postApiPass);
		// Получение операции по трекингу
		$response = $client->getOperationHistory($tarcking_data['tracking_barcode']);
		 
		foreach($response as $i => $status_data)
		{
			$date = formate_tracking_date($status_data['operationDate']);
			
			// Составляем массив статусов трекинга
			$key = $status_data['operationTypeId'].'-'.$status_data['operationAttributeId'].'-'.$status_data['operationPlacePostalCode'].'-'.$date;
			$tracking_statuses_api[$i] = $key;
		}
		
		// Вычисление статуса трекинга, которого нет в таблице
		$statuses_diff = array_diff ($tracking_statuses_api, $tracking_statuses);
		 
		// Добавляем новый статус
		foreach($statuses_diff as $i => $val)
		{
			add_tracking_status($tracking_id, $response[$i]);
			$status_added = 1;
		}
		 
		// Если не был добавлен новый статус,отмечаем последнее обновление по api почты
		if(!$status_added)
		{
			// Находим последний статус трекинга
			$sql = "SELECT * FROM ".POSTTR_STATUSES_TB." WHERE tracking_id='$tracking_id' ORDER by status_id DESC";
			$tr_row = $site_db->query_firstrow($sql);
			
			// Если статус найден, обновляем у него дату
			if($tr_row['status_id'])
			{
				$sql = "UPDATE ".POSTTR_STATUSES_TB." SET date_check=NOW() WHERE status_id='".$tr_row['status_id']."'";
				$site_db->query($sql);
			}
			else
			{
				// Добавляем пустой статус, с указанием даты последнего обращения по api
				add_tracking_status($tracking_id, array());
			}
		}
		
		 
		// echo "<pre>", print_r($response);
		$success = 1;
		
	} 
	catch(RussianPostException $e) {
		$success = 0;
		//die('Something went wrong: ' . $e->getMessage() . "\n");
	}
	
	return $success;
}
//Форматирует дату и время к нормальному виду
function formate_tracking_date($dt)
{
	$date = substr($dt,0,10);
	$time = substr($dt,11,8);
	return $date.' '.$time;
}
// Добавляем статус трекинга
function add_tracking_status($tracking_id, $status_data)
{
	global $site_db, $current_user_id;
	
	//$date = formate_tracking_date($status_data['operationDate']);
	
	//array('OperTypeId' => $OperTypeId, 'OperTypeName' => $OperTypeName, 'OperOperAttrId' => $OperOperAttrId, 'OperOperAttrName' => $OperOperAttrName, 'OperDate' => $OperDate, 'OperationAddressIndex' => $OperationAddressIndex, 'OperationAddressDescription' => $OperationAddressDescription); 
	
	 
	$sql = "INSERT INTO ".POSTTR_STATUSES_TB." set tracking_id='$tracking_id', operation_type='".$status_data['OperTypeName']."',
					operation_type_id='".$status_data['OperTypeId']."', operation_attribute='".$status_data['OperOperAttrName']."',
					operation_attribute_id='".$status_data['OperOperAttrId']."', operation_place_postal_code='".$status_data['OperationAddressIndex']."', operation_date='".$status_data['OperDate']."', operation_place_name='".$status_data['OperationAddressDescription']."', date_check=NOW()";
					
	$row = $site_db->query($sql);
	
	$status_id = $site_db->get_insert_id();
	
	$last_status_arr = get_tracking_last_status($tracking_id, 1);
	
	$last_status_id = $last_status_arr['status_id'];
	
	// Обновляем статус в таблице трекингов
	$sql = "UPDATE ".POSTTR_TB." SET status_id='$last_status_id' WHERE tracking_id='$tracking_id'";
	$row = $site_db->query($sql);
}
?>