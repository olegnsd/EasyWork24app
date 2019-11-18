<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_post_tracking.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/classes/class.RussianPostAPI.php';

$mode = $_POST['mode'];

if(!$current_user_id)
{
	exit();
}

switch($mode)
{
	// Добавить событие
	case 'add_tracking':
		
		$tracking_barcode = value_proc($_POST['tracking_barcode']);
		$tracking_desc = value_proc($_POST['tracking_desc']);
		$checked_link = value_proc($_POST['checked_link']);
		$tracking_client_id = split('-s-', value_proc($_POST['tracking_client_id']));
		$tracking_deal_id = split('-s-', value_proc($_POST['tracking_deal_id']));
		
		if(!$tracking_barcode)
		{
			$error['tracking_barcode'] = 1;
		}
		else if(!is_valid_tracking_formate($tracking_barcode))
		{
			$error['tracking_barcode'] = 2;
		}
		
		// Привязка к сделке и не выбрана сделка
		if($checked_link==1 && !$tracking_deal_id[1])
		{
			$error['tracking_link'] = 1;
		}
		// Привязка к контагенту и не выбран контагент
		else if($checked_link==2 && !$tracking_client_id[1])
		{
			$error['tracking_link'] = 2;
		}
		
		if(!$error)
		{
			// Привязка к сделке
			if($checked_link==1 && $tracking_deal_id[1])
			{
				$set_link = " ,deal_id='".$tracking_deal_id[1]."'";
			}
			// Привязка к контрагенту
			else if($checked_link==2 && $tracking_client_id[1])
			{
				$set_link = " ,client_id='".$tracking_client_id[1]."'";
			}
			
			// Добавляем запись о заместителе
			$sql = "INSERT INTO ".POSTTR_TB." 
					SET tracking_barcode='$tracking_barcode', tracking_desc='$tracking_desc', user_id='$current_user_id', date_add=NOW() $set_link";
			
			$site_db->query($sql);
			
			$tracking_id = $site_db->get_insert_id(); 
			
			// Получаем статусы трекинга
			check_tracking_status_post_api($tracking_id);
			
			$success = 1;
		}
			
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'tracking_id' => $tracking_id));
	
	break;
	
	case 'get_more_posttr':
		
		$page = value_proc($_POST['page']);
		$is_archive = value_proc($_POST['is_archive']);
		$list_type = value_proc($_POST['list_type']);
		$key_words = value_proc($_POST['key_words']);
		$status = value_proc($_POST['status']);
		
		$post_tracking_list = fill_post_tracking_list($page, array(), $is_archive, $list_type, $key_words, $status);
		
		echo $post_tracking_list;
		
	break;
	
	case 'get_posttr_item':
		
		$tracking_id = value_proc($_POST['tracking_id']);
		
		$tracking_data = get_tracking_data($tracking_id);
		
		$tracking_item = fill_post_tracking_list_item($tracking_data);
		
		echo $tracking_item;
		
	break;
	
	case 'archive_tracking':
		
		$tracking_id = value_proc($_POST['tracking_id']);
		
		$tracking_data = get_tracking_data($tracking_id);
		
		if($tracking_data['user_id']!=$current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".POSTTR_TB." SET archive=1 WHERE tracking_id='$tracking_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			$_SESSION['posttr_delete'][] = $tracking_id;
			echo 1;
		}
		
	break;
	
	case 'restore_tracking_from_archive':
		
		$tracking_id = value_proc($_POST['tracking_id']);
		
		$tracking_data = get_tracking_data($tracking_id);
		
		if($tracking_data['user_id']!=$current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".POSTTR_TB." SET archive=0 WHERE tracking_id='$tracking_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			if($_SESSION['posttr_delete'][$tracking_id])
			{
				unset($_SESSION['posttr_delete'][$tracking_id]);
			}
			 
			echo 1;
		}
		
	break;
	
	case 'delete_tracking':
		
		$tracking_id = value_proc($_POST['tracking_id']);
		
		$tracking_data = get_tracking_data($tracking_id);
		
		if($tracking_data['user_id']!=$current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".POSTTR_TB." SET deleted=1 WHERE tracking_id='$tracking_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			$_SESSION['posttr_delete'][] = $tracking_id;
			echo 1;
		}
		
	break;
	
	case 'restore_tracking':
		
		$tracking_id = value_proc($_POST['tracking_id']);
		
		$tracking_data = get_tracking_data($tracking_id);
		
		if($tracking_data['user_id']!=$current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".POSTTR_TB." SET deleted=0 WHERE tracking_id='$tracking_id'";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			unset($_SESSION['posttr_delete'][$tracking_id]);
			echo 1;
		}
		
	break;
	
	case 'update_tarcking_status':
		
		$tracking_data = value_arr_proc(json_decode(str_replace('//', '', $_POST['tracking_data'])));
		
		foreach($tracking_data as $i => $tracking_id)
		{  
			 
			// Обновляем статус
			check_tracking_status_post_api($tracking_id);
		
			$last_status = fill_tracking_last_status($tracking_id);
			
			// Получаем блок статуса
			$last_status_arr[$tracking_id] = iconv('cp1251', 'utf-8', $last_status);
		}
		
		 
		echo json_encode(array('status_block' => $last_status_arr));
		
	 
		
	break;
	
}

?>
