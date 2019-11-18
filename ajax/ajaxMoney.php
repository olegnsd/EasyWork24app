<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_money.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_colleagues.php';

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
	// Передать деньги
	case 'add_money':
		
		$user_id = value_proc($_POST['user_id']);
		
		$money_summa = value_proc($_POST['money_summa']);
		
		//$money_type = value_proc($_POST['money_type']);
		
		$money_comment = value_proc($_POST['money_comment']);
		
		$money_from = value_proc($_POST['money_from']);
		
		$add_type = value_proc($_POST['add_type']);
		
		$accruals_data = (array) json_decode(str_replace('\\', '', $_POST['accruals_data']));
		
		// Если нет связей между двумя пользователями (не являются между собой начальник-подчиненный)
		if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1)))
		{
			exit();
		}
		
		// Самому себе нельзя передать деньги
		if($user_id == $current_user_id)
		{
			exit();
		}
		
		// Если тип операции не передан
		if(!in_array($add_type, array(1,2)))
		{
			exit();
		}
		
		// Если новая выплата и не передана сумма
		if($add_type==1 && (!$money_summa || !is_numeric($money_summa) || $money_summa<=0))
		{
			$error['money_summa'] = 1;
		}
		
		// Если выплата начислений и их нет
		if($add_type==2 && !$accruals_data)
		{
			$error['accrual_data'] = 1;
		}
		
		if($add_type==2)
		{
			$accrual_error = '';
			
			// Проходим циклом по всем выбранным начислениям и проверяем их
			foreach($accruals_data as $accrual_id)
			{
				$sql = "SELECT paid, to_user_id FROM ".MONEY_ACCRUALS_TB." WHERE accrual_id='$accrual_id'";
				
				$row = $site_db->query_firstrow($sql);
				
				// Если начисление уже оплачено или не соответствие пользователю, выделяем ошибку
				if($row['paid']==1 || $user_id!=$row['to_user_id'])
				{
					$error['accrual'] = 1;
				}
			}
		}
		
		if(!$error)
		{		
			// Если переданы начисления, делаем флаг	
			if($accruals_data)
			{
				$set_accrual = " ,has_accruals=1";
			}
			
			// Добавляем деньги
			$sql = "INSERT INTO ".MONEY_TB." SET money_summa='$money_summa', money_from_user_id='".$current_user_id."', money_to_user_id='$user_id', money_date=NOW(), money_from='$money_from', money_comment='$money_comment' $set_accrual";
			
			$site_db->query($sql);
			
			$inserted_money_id = $site_db->get_insert_id();
			
			// Если передан комментарий к деньгам
			if($money_comment)
			{
				//$sql = "INSERT INTO ".MONEY_REPORTS_TB." SET money_id='$inserted_money_id', report_text='$money_comment', user_id='$current_user_id', report_date=NOW()";
				
				//$site_db->query($sql);
				 
			}
			
			// Добавляем к выплате начисления
			foreach($accruals_data as $accrual_id)
			{
				// Добавляем начисление к платежу
				$sql = "INSERT INTO ".ACCRUALS_IN_PAYMENTS_TB." (money_id, accrual_id) VALUES ('$inserted_money_id', '$accrual_id')";
				
				$site_db->query($sql);
				
				// Отмечаем, что платеж успешно проведен
				$sql = "UPDATE ".MONEY_ACCRUALS_TB." SET paid=1 WHERE accrual_id='$accrual_id'";
				
				$site_db->query($sql);
			}
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'inserted_money_id' => $inserted_money_id));
	
	break;
	
	case 'add_money_accrual':
		
		$user_id = value_proc($_POST['user_id']);
		
		$summa = value_proc($_POST['summa']);
		
		$desc = value_proc($_POST['desc']);
		
		$accrual_type = value_proc($_POST['accrual_type']);
		
		if(!check_user_access_to_user_content($user_id, array(0,1,0,0,1)) || $current_user_id==$user_id || !$accrual_type)
		{
			exit();
		}
		 
		if(!$summa || !is_numeric($summa) || $summa<=0)
		{
			$error['summa'] = 1;	
		}
		
		if(!$error)
		{
			
			$sql = "INSERT INTO ".MONEY_ACCRUALS_TB." (type_id, from_user_id, to_user_id, summa, description, date) VALUES ('$accrual_type','$current_user_id', '$user_id', '$summa', '$desc', NOW())";
			
			$site_db->query($sql);
			
			$accrual_id = $site_db->get_insert_id();
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'accrual_id' => $accrual_id));
		
	break;
	
	case 'get_accrual_item':
		
		$accrual_id = $_POST['accrual_id'];
		
		if(!$accrual_id)
		{
		 	exit();
		}
		
		$sql = "SELECT * FROM ".MONEY_ACCRUALS_TB." WHERE accrual_id='$accrual_id'";
	
		$accrual_data = $site_db->query_firstrow($sql);
		 
		$accruals_item = fill_accruals_list_item($accrual_data);
		
		echo $accruals_item;
	
	break;
	
	// Добавляет отчет
	case 'add_money_report':
		
		$money_id = $_POST['money_id'];
		
		$report_text = value_proc($_POST['report_text']);
		
		if(!$money_id || !$current_user_id)
		{
			exit();
		}
		
		if($report_text=='')
		{
			$error['report_text'] = 1;
		}
		
		if(!$error)
		{
			// Добавляем отчет о задаче
			$sql = "INSERT INTO ".MONEY_REPORTS_TB." SET money_id='$money_id',  user_id='$current_user_id', report_date=NOW(), report_text='$report_text'";
			
			$site_db->query($sql);
			
			$task_data = $site_db->get_insert_id();
			
			$success = 1;
			
		}
		
		
		echo json_encode(array('success' => $success, 'error' => $error));
		
	break;
	
		// Получить список отчетов
	case 'get_money_report_list':
		
		$money_id = $_POST['money_id'];
		
		$sql = "SELECT * FROM ".MONEY_TB." WHERE money_id='$money_id'";
		
		$money_data = $site_db->query_firstrow($sql);
		 
		// Список отчетов
		$reports_list = fill_money_reports_list($money_id, $money_data);
		
		echo $reports_list;

	break;
	
	// Удалить клиента
	case 'delete_money':
		
		$money_id = value_proc($_POST['money_id']);
		
		// Данные клиента
		$sql = "SELECT * FROM ".MONEY_TB." WHERE money_id='$money_id'";
		
		$money_data = $site_db->query_firstrow($sql);
		 
		if($money_data['money_from_user_id']!=$current_user_id)
		{
			exit();	
		}
		
		$sql = "UPDATE ".MONEY_TB." SET  money_deleted='1' WHERE money_id='$money_id'";
		
		$site_db->query($sql);
		
		$_SESSION['money_deleted'][] = $money_id;
		
		echo 1;
	break;
	
	// Восстановить клиента
	case 'restore_money':
		
		$money_id = value_proc($_POST['money_id']);

		$sql = "UPDATE ".MONEY_TB." SET  money_deleted='0' WHERE money_id='$money_id'";
		
		$site_db->query($sql);
		
		$_SESSION['money_deleted'][$money_id]=='';
		
		echo 1;
	break;
	
	// Возвращает блок переданных финансов
	case 'get_money_item':
	
		$money_id = value_proc($_POST['money_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "SELECT i.* FROM ".MONEY_TB." i WHERE money_id='$money_id'";
		
		$money_data = $site_db->query_firstrow($sql);
		
		$money_item = fill_money_list_item($money_data, '', $user_id);
		
		echo $money_item;
		
	break;
	
	// Возвращает список клиентов
	case 'get_more_money':
		
		$user_id = value_proc($_POST['user_id']);
		
		$page = value_proc($_POST['page']);

		// Список контактов
		$money_list = fill_user_money_list($user_id, $page);
		
		echo $money_list;
		
	break;
	
	case 'confirm_money':
		
		$money_id = value_proc($_POST['money_id']);
		
		$sql = "SELECT * FROM ".MONEY_TB." WHERE money_id='$money_id'";
		
		$money_data = $site_db->query_firstrow($sql);
		
		if($row['money_to_user_id'] == $current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".MONEY_TB." SET money_confirm=1 WHERE money_id='$money_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			$success = 1;
		}
		
		// Кол-во новых переданных финансов пользователю
		$new_money_count = get_new_money_for_user($current_user_id);
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'new_money_count' => $new_money_count));
	break;
	
	case 'get_money_accruals_result_block':
		
		$user_id = value_proc($_POST['user_id']);
		
		// Если нет связей между двумя пользователямиs
		if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1)))
		{
			exit();
		}
		
		echo fill_user_accruals_result_block($user_id);
		
	break;
	
	case 'confirm_accrual':
		
		$accrual_id = value_proc($_POST['accrual_id']);
		
		$sql = "SELECT to_user_id FROM ".MONEY_ACCRUALS_TB." WHERE accrual_id='$accrual_id'";
		
		$accrual_data = $site_db->query_firstrow($sql);
		
		if($accrual_data['to_user_id']!=$current_user_id)
		{
			 exit();
		}
		
		$sql = "UPDATE ".MONEY_ACCRUALS_TB." SET confirm=1 WHERE accrual_id='$accrual_id'";
		
	 	$site_db->query($sql);
	
		 
		if(!mysql_error())
		{
			$success = 1;
		}
		
		// Кол-во новых начислений
		$new_accruals_count = get_new_accruals_count($current_user_id);
	
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'new_accruals_count' => $new_accruals_count));
		
	break;
	
	case 'delete_accrual':
		
		$accrual_id = value_proc($_POST['accrual_id']);
		
		$sql = "SELECT from_user_id, confirm FROM ".MONEY_ACCRUALS_TB." WHERE accrual_id='$accrual_id'";
		
		$accrual_data = $site_db->query_firstrow($sql);
		
		if($accrual_data['from_user_id']!=$current_user_id)
		{
			 exit();
		}
		
		// Уже принято
		/*if($accrual_data['confirm'])
		{
			echo '-1';
		}*/
		// Еще не принято конечным пользователем
		//if(!$accrual_data['confirm'])
	//	{
			$sql = "UPDATE ".MONEY_ACCRUALS_TB." SET deleted=1 WHERE accrual_id='$accrual_id'";
		
			$site_db->query($sql);
			 
			if(!mysql_error())
			{
				$_SESSION['accrual_deleted'][] = $accrual_id;
				
				echo 1;
			}
		//}
		 
	break;
	
	case 'restore_accrual':
		
		$accrual_id = value_proc($_POST['accrual_id']);
		
		$sql = "SELECT from_user_id, confirm FROM ".MONEY_ACCRUALS_TB." WHERE accrual_id='$accrual_id'";
		
		$accrual_data = $site_db->query_firstrow($sql);
		
		if($accrual_data['from_user_id']!=$current_user_id)
		{
			 exit();
		}
		
		$sql = "UPDATE ".MONEY_ACCRUALS_TB." SET deleted=0 WHERE accrual_id='$accrual_id'";
		
		$site_db->query($sql);
			 
		if(!mysql_error())
		{
			$_SESSION['accrual_deleted'][$accrual_id] = '';
			
			echo 1;
		}
		
		 
	break;
	
	case 'get_new_money_count':
		
		$new_money_count = get_new_money_for_user($current_user_id);
		$new_money_count += get_new_accruals_count($current_user_id);
	
		echo $new_money_count;
	break;
	
	// Возвращает список клиентов
	case 'get_more_accruals':
		
		$user_id = value_proc($_POST['user_id']);
		
		$page = value_proc($_POST['page']);

		// Список задолженностей
		$accruals_list = fill_accruals_list($user_id, $page);
	
		echo $accruals_list;
		
	break;
	
}

?>