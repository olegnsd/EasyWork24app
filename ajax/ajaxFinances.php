<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_finances.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_clients.php';
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

if(!$current_user_id)
{
	exit();
}
		
switch($mode)
{
	// Создать расчетный счет
	case 'add_finance':
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		}
	
		$finance_summa = value_proc($_POST['finance_summa']);
		
		$finance_currency = value_proc($_POST['finance_currency']);
		
		$finance_name = value_proc($_POST['finance_name']);
		
		$finance_summa = preg_replace('/[^0-9\.]/', '', $finance_summa);
		
		if(!is_numeric($finance_summa))
		{
			$error['finance_summa'] = 2;
		}
		
		if(!$finance_name)
		{
			$error['finance_name'] = 1;
		}
		/*if($finance_summa <= 0) 
		{
			$error['finance_summa'] = 1;
		}*/
		if(!$error)
		{
			$sql = "INSERT INTO ".FINANCES_TB." SET
					user_id='$current_user_id',
					finance_name = '$finance_name',
					finance_summa='$finance_summa',
					currency_id='$finance_currency',
					finance_date_add=NOW()";
			
			$site_db->query($sql);
			
			$inserted_finance_id = $site_db->get_insert_id();
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'finance_id' => $inserted_finance_id));
			
	break;
	
	case 'get_finance_item':
		
		$finance_id = value_proc($_POST['finance_id']);
		
		// Получает массив пользователей, относящихся к пользователю (начальники и подчиненные)
		$users_for_access_arr = get_current_user_users_arrs(array(1,1,0,0,0), 1);
		
		$sql = "SELECT * FROM ".FINANCES_TB." WHERE finance_id='$finance_id'";
		
		$finance_data = $site_db->query_firstrow($sql);
		
		$finance_item = fill_finance_item($finance_data, $users_for_access_arr);
		
		echo $finance_item;
	break;
	
	case 'get_more_finances':
		
		$user_id = value_proc($_POST['user_id']);
		
		$page = value_proc($_POST['page']);
		
		$finance_av = value_proc($_POST['finance_av']);
		
		$finances_list = fill_user_finances_list($user_id, $page, $finance_av);
		
		echo $finances_list;
	break;
	
	// Добавление операции
	case 'add_finance_operation':
		
		$finance_id = value_proc($_POST['finance_id']);
		
		$operation_type = value_proc($_POST['operation_type']);
		
		$operation_client = str_replace('-s-','',value_proc($_POST['operation_client']));
		
		$operation_summa = value_proc($_POST['operation_summa']);
		
		$operation_date = value_proc($_POST['operation_date']);
		
		$operation_time = value_proc($_POST['operation_time']);
		
		$operation_comment = value_proc($_POST['operation_comment']);
		
		$files_arr = json_decode(str_replace('\\', '', $_POST['files_arr']));
		$files_content_type = value_proc($_POST['files_content_type']);
		
		if(!$finance_id) exit();
		
		// Данные счета
		$finance_data = get_finance_data($finance_id); 
		
		if(!check_user_for_access_to_operation($finance_data, $finance_id, $current_user_id))
		{
			exit();
		}
		
		// Название контакта пустое
		if($operation_client=='')
		{
			$error['operation_client'] = 1;
		}
		
		if(!$operation_type)
		{
			$error['operation_type'] =  1;
		}
		
		$client_name = $operation_client;
		$client_id = 0;
		// Если в качестве клиента передан его ID
		if(is_numeric($operation_client))
		{
			// находим такого клиента
			$sql = "SELECT client_id FROM ".CLIENTS_TB." WHERE client_id='$operation_client' AND client_deleted<>1";
			
			$row = $site_db->query_firstrow($sql);
			
			if($row['client_id'])
			{
				$client_name = '';
				$client_id = $operation_client;
			}
			else
			{
				$error['operation_client']==1;
			}
		}
		
		if($operation_summa <= 0)
		{
			$error['operation_summa'] = 1;
		}
		
		if(!is_numeric($operation_summa))
		{
			$error['operation_summa'] = 2;
		}
		
		// Дата
		$operation_norm_date = formate_to_norm_date($operation_date);
		
		$operation_date = join_date_and_time($operation_norm_date, $operation_time);
		 
		if(!$operation_norm_date)
		{
			$error['operation_date'] = 1;
		}
		
		if(!$error)
		{
			// Данные счета
			$finance_data = get_finance_data($finance_id); 
		
			$finance_summa = $finance_data['finance_summa'];
			
			 
			// Обновляем остаток на счете 
			// Если было пополнение
			if($operation_type==1)
			{
				$result_sum = finance_operation_proc($finance_summa, $operation_summa, '+');
			}
			// Если был расход
			else if($operation_type==2)
			{
				$result_sum = finance_operation_proc($finance_summa, $operation_summa, '-');
			}
			
			
			// Добавляем операцию
			$sql = "INSERT INTO ".FINANCES_OPERATIONS_TB." SET
					finance_id = '$finance_id',
					operation_type = '$operation_type',
					user_id = '$current_user_id',
					client_id = '$client_id',
					client_name = '$client_name',
					operation_summa = '$operation_summa',
					operation_date = '$operation_date',
					operation_comment = '$operation_comment',
					finance_summa_after_operation = '$result_sum',
					date_add = NOW()
					";
			
			$site_db->query($sql);
			
			$inserted_operation_id = $site_db->get_insert_id();
			
			// Привязка файлов к контенту
			attach_files_to_content($inserted_operation_id, $files_content_type, $files_arr, $current_user_id);
			
			$finance_summa_operation = " finance_summa = '$result_sum'";
			
			// Обновляем остаток на счете 
			$sql = "UPDATE ".FINANCES_TB." SET $finance_summa_operation WHERE finance_id='$finance_id'";
			
			$site_db->query($sql);
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'operation_id' => $inserted_operation_id, 
		'result_sum' => sum_process($result_sum, ' ', '\.', 1)));
	break;
	
	// Возвращает блок операции
	case 'get_finance_operation_item':
		
		$finance_id = value_proc($_POST['finance_id']);
		
		$operation_id = value_proc($_POST['operation_id']);
		
		// Данные счета
		$sql = "SELECT * FROM ".FINANCES_TB." WHERE finance_id='$finance_id'";
		
		$finance_data = $site_db->query_firstrow($sql);
		
		// Выбираем операцию
		$sql = "SELECT * FROM ".FINANCES_OPERATIONS_TB." WHERE operation_id = '$operation_id' AND finance_id='$finance_id'";
		
		$operation_data = $site_db->query_firstrow($sql);
		
		echo fill_finance_operation_item($operation_data, $finance_data);
		
	break;
	
	// Добавляем статус
	case 'add_finance_operation_status':
		
		if(!$current_user_id)
		{
			exit();
		}
		$finance_id = value_proc($_POST['finance_id']);
		
		$status_id = value_proc($_POST['status_id']);
		
		$status_comment = value_proc($_POST['status_comment']);
		
		$operation_id = value_proc($_POST['operation_id']);
		
		// Данные счета
		$finance_data = get_finance_data($finance_id); 
		
		if(!check_user_for_access_to_operation($finance_data, $finance_id, $current_user_id))
		{
			exit();
		}
		
		if(!$error)
		{
			// Добавляем статус
			$sql = "INSERT INTO ".FINANCES_OPERATIONS_STATUSES_TB." SET
					operation_id = '$operation_id',
					user_id = '$current_user_id',
					status_comment = '$status_comment',
					finance_id = '$finance_id',
					status_id = '$status_id',
					status_date_add = NOW()
					";
			
			$site_db->query($sql);
			
			$inserted_status_id = $site_db->get_insert_id();
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'status_id' => $inserted_status_id));
	break;
	
	// Возвращает блок статуса операции
	case 'get_finance_operation_status_item':
		
		$finance_id = value_proc($_POST['finance_id']);
		
		$operation_id = value_proc($_POST['operation_id']);
		
		// Выбираем последний добавленный статус
		$sql = "SELECT * FROM ".FINANCES_OPERATIONS_STATUSES_TB." WHERE operation_id='$operation_id' ORDER by id DESC LIMIT 1";
	
		$status_data = $site_db->query_firstrow($sql);
		
		// Массив статусов
		$statuses_types_arr = get_finance_operations_statuses_arr();
		
		$status_back_color = switch_finance_status_back($status_data['status_id']);
		
		$status_item = fill_finance_operation_status_item($status_data, $statuses_types_arr);
		
		// Возвращаем результат
		echo json_encode(array('item' => iconv('cp1251', 'utf-8', $status_item), 'status_back_color' => $status_back_color));
	break;
	
	// Отменить финансовую операцию
	case 'finance_operation_return':
		
		$operation_id = value_proc($_POST['operation_id']);
		
		$finance_id = value_proc($_POST['finance_id']);
		
		// Данные операции
		$operation_data =  get_finance_operation_data($operation_id);
		
		// Данные счета
		$finance_data = get_finance_data($finance_id); 
		
		// Операция, которую можно отменить
		$operation_id_can_deleted = get_finance_operation_can_returned($finance_id);
	
		if(!check_user_for_access_to_operation($finance_data, $finance_id, $current_user_id))
		{
			exit();
		}
		
		if(!$operation_id || !$finance_id || $operation_id_can_deleted!=$operation_id)
		{
			exit();
		}
		
		// Вычисляем новый остаток на счете, с учетом отмены операции
		// операция поступления
		if($operation_data['operation_type']==1)
		{
			$result_sum = finance_operation_proc($finance_data['finance_summa'], $operation_data['operation_summa'], '-');
		}
		// Операция расхода
		else if($operation_data['operation_type']==2)
		{
			$result_sum = finance_operation_proc($finance_data['finance_summa'], $operation_data['operation_summa'], '+');
		}
		
		if($result_sum!='')
		{
			// Отмечаем операцию как отмененную		
			$sql = "UPDATE ".FINANCES_OPERATIONS_TB." SET operation_returned=1, operation_returned_by_user_id='$current_user_id', 
					operation_returned_date = NOW()
					WHERE operation_id='$operation_id'";
			
			$site_db->query($sql);
			
			if(!mysql_error())
			{
				// Обновляем остаток на счете с учетом отмены операции
				$sql = "UPDATE ".FINANCES_TB." SET finance_summa='$result_sum' WHERE finance_id='$finance_id'";
				
				$site_db->query($sql);
				
				// Если произошла ошибка, возвращаем операцию в исходное состояние
				if(mysql_error())
				{
					$sql = "UPDATE ".FINANCES_OPERATIONS_TB." SET operation_returned=0, operation_returned_by_user_id='0', 
					WHERE operation_id='$operation_id'";
					
					$site_db->query($sql);
					
				}
				
				$success = 1;
			}
			
			$result_sum = sum_process($result_sum, ' ', '\.', 1);
		 
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'result_sum' => $result_sum));
		
		
		
	return;
	
	// Дать доступ к финансам пользователя
	case 'give_access_to_finance':
	
		$finance_id = value_proc($_POST['finance_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		if(!$finance_id || !$user_id)
		{
			exit();
		}
		
		// Данные счета
		$sql = "SELECT * FROM ".FINANCES_TB." WHERE finance_id='$finance_id'";
		
		$finance_data = $site_db->query_firstrow($sql);
		
		// Пользователи, которым передали управление счетами
		$accessed_users_arr = get_accessed_users_arr_for_finance($finance_id);
	
		if($finance_data['user_id']!=$current_user_id && !in_array($current_user_id, $accessed_users_arr))
		{
			exit();
		}
		
		// Проверяем
		$sql = "SELECT id FROM ".FINANCES_ACCESS." WHERE finance_id='$finance_id' AND user_id='$user_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		// Если пользователю уже был передан доступ на финансы
		if($row['id'])
		{
			$sql = "DELETE FROM ".FINANCES_ACCESS." WHERE id='".$row['id']."'";
			
			$site_db->query($sql);
			
			echo 1;
		}
		else
		{
			// Добавляем разрешения управление финансами
			$sql = "INSERT INTO ".FINANCES_ACCESS." (finance_id, user_id, access_by_user_id) VALUES ('$finance_id', '$user_id', '$current_user_id')";
			
			$site_db->query($sql);
			
			echo 2;
		}
		
	
	break;
	
	case 'get_finance_for_transfer':
	
		$finance_id = value_proc($_POST['finance_id']);
		
		$search_finance_id = value_proc($_POST['search_finance_id']);
		
		if($finance_id==$search_finance_id)
		{
			$error = 1;
		}
		// Данные счета
		$finance_data_from = get_finance_data($finance_id);
		
		// Данные счета
		$finance_data_to = get_finance_data($search_finance_id); 
		
		if(!$finance_data_from['finance_id'] || !$finance_data_to['finance_id'])
		{
			$error = 1;
		}
		
		if(!$error)
		{
			// Форма перевода между счетами
			$transfer_form = fill_finance_transfer_form($finance_data_from, $finance_data_to);
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('transfer_form' => iconv('cp1251', 'utf-8', $transfer_form), 'success' => $success, 'error' => $error));
		
	break;
	
	case 'to_transfer_finance':
		
		$finance_id_from = value_proc($_POST['finance_id_from']);
		
		$finance_id_to = value_proc($_POST['finance_id_to']);
		
		$summa_from = value_proc($_POST['summa_from']);
		
		$summa_to = value_proc($_POST['summa_to']);
		
		$comment = value_proc($_POST['comment']);
		
		if($finance_id_from==$finance_id_to)
		{
			exit();
		}
		// Данные счета
		$finance_data_from = get_finance_data($finance_id_from);
		
		// Данные счета
		$finance_data_to = get_finance_data($finance_id_to);
		
		if(!is_numeric($summa_from) || !is_numeric($summa_to))
		{
			$error['summa'] = 1;
		}
		
		if(!$error)
		{
			// Остаток на счете после перевода
			$result_sum_from = finance_operation_proc($finance_data_from['finance_summa'], $summa_from, '-');
			$result_sum_to = finance_operation_proc($finance_data_to['finance_summa'], $summa_to, '+');
			
			// Перевод от счета
			$sql = "UPDATE ".FINANCES_TB." SET finance_summa='$result_sum_from' WHERE finance_id='$finance_id_from'";
			
			$site_db->query($sql);
			
			// Перевод в счета
			$sql = "UPDATE ".FINANCES_TB." SET finance_summa='$result_sum_to' WHERE finance_id='$finance_id_to'";
			
			$site_db->query($sql);
			
			$comment = "Перевод со счета №$finance_id_from на счет №$finance_id_to.".$comment;
			
			// Добавляем операцию Расхода
			$sql = "INSERT INTO ".FINANCES_OPERATIONS_TB." SET
					finance_id = '$finance_id_from',
					operation_type = '2',
					user_id = '$current_user_id',
					client_id = 0,
					client_name = '',
					operation_summa = '$summa_from',
					operation_date = NOW(),
					operation_comment = '$comment',
					finance_summa_after_operation = '$result_sum_from',
					date_add = NOW(),
					operation_is_transfer = 1
					";
			
			$site_db->query($sql);
			
			// Добавляем операцию Поступления
			$sql = "INSERT INTO ".FINANCES_OPERATIONS_TB." SET
					finance_id = '$finance_id_to',
					operation_type = '1',
					user_id = '$current_user_id',
					client_id = 0,
					client_name = '',
					operation_summa = '$summa_to',
					operation_date = NOW(),
					operation_comment = '$comment',
					finance_summa_after_operation = '$result_sum_to',
					date_add = NOW(),
					operation_is_transfer = 1
					";
			
			$site_db->query($sql);
			
			// Форматирование сумм
			$finance_summa_from = sum_process($result_sum_from, ' ', '\.', 1);
			$finance_summa_to = sum_process($result_sum_to, ' ', '\.', 1);
			
			$success = 1;
			
			
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'finance_summa_from' => $finance_summa_from, 'finance_summa_to' => $finance_summa_to));
	break;
	
	case 'get_finances_access_block':
		
		$finance_id = value_proc($_POST['finance_id']);
		
		$access_block = fill_finance_access_block($finance_id);
		
		echo $access_block;
		
	break;
	
	case 'save_finances_user_access':
		
		$finance_id = value_proc($_POST['finance_id']);
		$access_users = (array)json_decode(str_replace('\\', '', $_POST['access_users']), 1);

		 
		// Данные
		$finance_data = get_finance_data($finance_id);
		
		
		// Выбор всех сотрудников, имеющих доступ к заметке
		$sql = "SELECT * FROM ".FINANCES_ACCESS." WHERE finance_id='$finance_id'";
		
		$res = $site_db->query($sql);
		
		$users_access_arr = array();
			
		while($row=$site_db->fetch_array($res))
		{
			$users_access_arr[$row['user_id']] = $row['user_id'];
		}
		
		if(!in_array($current_user_id, $users_access_arr) && $current_user_id!=$finance_data['user_id'])
		{
			exit();
		}
		


		
		$to_delete = array_diff($users_access_arr, $access_users);
		$to_add = array_diff($access_users, $users_access_arr);
		 
		foreach($to_delete as $user_id)
		{
			if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1,1)) && $finance_data['user_id']!=$current_user_id)
			{
				continue;
			}
			
			$sql = "DELETE FROM ".FINANCES_ACCESS." WHERE user_id='$user_id' AND finance_id='$finance_id'";
			
			$site_db->query($sql);
		}
		 
		foreach($to_add as $user_id)
		{  
			if(!$user_id || $user_id==$finance_data['user_id'])
			{
				continue;
			}
			
			if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1,1)))
			{
				 continue;
			}
		
			$sql = "INSERT INTO ".FINANCES_ACCESS." SET user_id='$user_id', finance_id='$finance_id', access_by_user_id='$current_user_id'";
			
			$site_db->query($sql);
		}
		
		echo 1;
		
	break;
}

?>