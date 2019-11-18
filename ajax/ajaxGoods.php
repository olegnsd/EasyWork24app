<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_goods.php';
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
	// Добавить имущество сотрудника
	case 'add_new_good':
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		}
	
		$good_name = value_proc($_POST['good_name']);
		
		$good_price = value_proc($_POST['good_price']);
		
		$good_images_arr = json_decode(str_replace('\\', '', $_POST['images']));
		
		// Название контакта пустое
		if($good_name=='')
		{
			$error['good_name'] = 1;
		}
		
		
		
		if(!$error)
		{
			// Добавляем контакт
			$sql = "INSERT INTO ".GOODS_TB." SET good_name='$good_name', good_price='$good_price', good_create_user_id='$current_user_id', good_owner_user_id='$current_user_id', good_date_add=NOW()";
					
			$site_db->query($sql);
			
			// Выбираем доабвленного клиента
			$sql = "SELECT good_id FROM ".GOODS_TB." WHERE good_create_user_id='$current_user_id' ORDER by good_id DESC LIMIT 1";
			
			$row = $site_db->query_firstrow($sql);
			
			$inserted_id = $row['good_id'];
			
			// Добавляем в таблицу держателя имущества
			add_to_good_owners($inserted_id, $current_user_id);
			
			$good_upload_path = GOODS_PATH.'/'.$inserted_id;
			
			// Создаем папку для изображений
			if($good_images_arr)
			{
				mkdir($good_upload_path);
			}
			// добавляем изображения
			foreach($good_images_arr as $i => $image)
			{
				// Добавляем изображение
				if($image)
				{
					$date_add = date('Y-m-d H:i:s');
				
					$file_system_name = get_rand_file_system_name($image);
				
					$file_dir = create_upload_folder($date_add, 1).'/'.$file_system_name;
				
					
					$sql = "INSERT INTO ".GOODS_IMAGES_TB." SET image_name='$file_system_name', good_id='$inserted_id', date_add='$date_add'";
					
					$site_db->query($sql);
					
					copy(TEMP_PATH.'/'.$image, $file_dir);
					
					unlink(TEMP_PATH.'/'.$image);
					
				 
				}
			}
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'good_inserted_id' => $inserted_id));
			
	break;
	
	// Сохранить имущество сотрудника
	case 'save_good':
	
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		}
		
		$good_id = value_proc($_POST['good_id']);
	
		$good_name = value_proc($_POST['good_name']);
		
		$good_price = value_proc($_POST['good_price']);
		 
		$good_images_arr = (array)json_decode(str_replace('\\', '', $_POST['images']), 1);
		
		$deleted_images_arr = json_decode(str_replace('\\', '', $_POST['deleted_images']));
		
		$images_names_replaces_arr = (array)json_decode(str_replace('\\', '', $_POST['images_names_replaces']), 1);
		
		// Название контакта пустое
		if($good_name=='')
		{
			$error['good_name'] = 1;
		}
		
		if(!$error)
		{
			// Добавляем контакт
			$sql = "UPDATE ".GOODS_TB." SET good_name='$good_name', good_price='$good_price' WHERE good_id='$good_id'";
					
			$site_db->query($sql);

			
			$good_upload_path = GOODS_PATH.'/'.$good_id;
			
			// Создаем папку для изображений
			if(!is_dir($good_upload_path))
			{
				mkdir($good_upload_path);
			}
			
			// Проходим по массиву измененных фотографий 
			foreach($images_names_replaces_arr as $num => $image_id)
			{
				// Данные удаляемого изображения
				$sql = "SELECT * FROM ".GOODS_IMAGES_TB." WHERE image_id='$image_id'";
					 
				$img_row = $site_db->query_firstrow($sql);
					
				// Если фотография менялась	
				if(isset($good_images_arr[$num]) && $good_images_arr[$num])
				{	
					$image = $good_images_arr[$num];
					
					
					$date_add = date('Y-m-d H:i:s');
				
					$file_system_name = get_rand_file_system_name($image);
				
					$file_dir = create_upload_folder($date_add, 1).'/'.$file_system_name;
									
					
					$sql = "UPDATE ".GOODS_IMAGES_TB." SET image_name='".$file_system_name."', date_add='$date_add' WHERE image_id='$image_id'";
					
					$site_db->query($sql);
					
				 	if(!mysql_error())
					{
						// Удаляем старое изображение			
						//unlink($good_upload_path.'/'.$img_row['image_name']);
						 
						copy(TEMP_PATH.'/'.$good_images_arr[$num], $file_dir);
						
						unlink(TEMP_PATH.'/'.$good_images_arr[$num]);
						
						$good_images_arr[$num] = '';
					}
				}
				// Если фотография удалена или при изменении была ошибка, повлекшая за собой пустое поле
				else
				{
					$sql = "DELETE FROM ".GOODS_IMAGES_TB." WHERE image_id='$image_id'";
					
					$site_db->query($sql);
					
				}
			}
			// добавляем изображения
			foreach($good_images_arr as $i => $image)
			{
				// Добавляем изображение
				if($image)
				{
					$date_add = date('Y-m-d H:i:s');
				
					$file_system_name = get_rand_file_system_name($image);
				
					$file_dir = create_upload_folder($date_add, 1).'/'.$file_system_name;
					
					$sql = "INSERT INTO ".GOODS_IMAGES_TB." SET image_name='$file_system_name', date_add='$date_add', good_id='$good_id'";
					
					$site_db->query($sql);
					
					copy(TEMP_PATH.'/'.$image, $file_dir);
					
					unlink(TEMP_PATH.'/'.$image);
				}
			}
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'good_inserted_id' => $inserted_id));
			
	break;
	
	// Возвращает форму редактирвоания клиента
	case 'get_good_form':
	
		if(!$current_user_id) exit();
		
		$good_id = value_proc($_POST['good_id']);
		
		$form = value_proc($_POST['form']);
		
		$sql = "SELECT * FROM ".GOODS_TB." WHERE good_id='$good_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		// Получает массив пользователей, относящихся к пользователю (начальники и подчиненные)
		$users_for_access_arr = get_current_user_users_arrs(array(1,1,0,0,0), 1);
	
		// Получаем элемент имущества
		$good_item = fill_good_list_item($row, $form, 0, $users_for_access_arr);
		
		echo $good_item;
	break;
	
	// Удалить имущество
	case 'delete_good':
		
		$good_id = value_proc($_POST['good_id']);
		
		// Данные клиента
		$sql = "SELECT * FROM ".GOODS_TB." WHERE good_id='$good_id'";
		
		$good_data = $site_db->query_firstrow($sql);
		
		// Если пользователь не создатель - выход
		if($current_user_id!=$good_data['good_create_user_id'] && !check_user_access_to_user_content($good_data['good_create_user_id'], array(0,1,0,0,1)))
		{
			exit();
		}
		$sql = "UPDATE ".GOODS_TB." SET  good_deleted='1' WHERE good_id='$good_id'";
		
		$site_db->query($sql);
		
		$_SESSION['good_deleted'][] = $good_id;
		
		echo 1;
	break;
	
	// Восстановить удаленное имущество
	case 'restore_good':
		
		$good_id = value_proc($_POST['good_id']);

		$sql = "UPDATE ".GOODS_TB." SET  good_deleted='0' WHERE good_id='$good_id'";
		
		$site_db->query($sql);
		
		$_SESSION['good_deleted'][$good_id]=='';
		
		echo 1;
	break;
	
	// Возвращает список клиентов
	case 'get_more_goods':
		
		$user_id = value_proc($_POST['user_id']);
		
		$page = value_proc($_POST['page']);

		// Список имущества
		$goods_list = fill_goods_list($user_id, $page);
		
		echo $goods_list;
		
	break;
	
	case 'give_good_to_user':
		
		$good_id = value_proc($_POST['good_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		// Проверяем пользователя, является ли он хозяином имущества
		$sql = "SELECT * FROM ".GOODS_TB." WHERE good_id='$good_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['good_owner_user_id']!=$current_user_id)
		{
			 exit();
		}
		
		// 
		$sql = "SELECT good_to_new_owner_id FROM ".GOODS_TB." WHERE good_id='$good_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['good_to_new_owner_id'] && $row['good_to_new_owner_id']==$user_id)
		{
			// Добавляем 
			$sql = "UPDATE ".GOODS_TB." SET good_to_new_owner_id='0' WHERE good_id='$good_id'";
			
			$site_db->query($sql);
			
			echo 1;
		}
		else
		{
			$sql = "UPDATE ".GOODS_TB." SET good_to_new_owner_id='$user_id' WHERE good_id='$good_id'";
			
			$site_db->query($sql);
			
			echo 2;
		}
		
		 
	break;
	
	// Подтвердить выполнение 
	case 'good_owner_confirm':
		
		$good_id = value_proc($_POST['good_id']);
		
		// Проверяем пользователя, является ли он хозяином имущества
		$sql = "SELECT * FROM ".GOODS_TB." WHERE good_id='$good_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		// Если не является пользователем, которомупередают имущество
		if($row['good_to_new_owner_id']!=$current_user_id)
		{
			exit();
		}
		
		// Обновляем владельца
		$sql = "UPDATE ".GOODS_TB." SET good_owner_user_id=good_to_new_owner_id, good_to_new_owner_id=0 WHERE good_id='$good_id'";
		
		$site_db->query($sql);
		
		// Добавляем в таблицу держателя имущества
		add_to_good_owners($good_id, $current_user_id);
		
		
		// Кол-во новых предложений имущества
		$new_goods_count = get_new_goods_count_for_users($current_user_id);
		
		$success = 1;
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'new_goods_count' => $new_goods_count));
	break;
	
	// Отклонить предложение имущества 
	case 'good_owner_cancel':
		
		$good_id = value_proc($_POST['good_id']);
		
		// Проверяем пользователя, является ли он хозяином имущества
		$sql = "SELECT * FROM ".GOODS_TB." WHERE good_id='$good_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		// Если не является пользователем, которомупередают имущество
		if($row['good_to_new_owner_id']!=$current_user_id)
		{
			exit();
		}
		
		// Обновляем владельца
		$sql = "UPDATE ".GOODS_TB." SET good_to_new_owner_id=0 WHERE good_id='$good_id'";
		
		$site_db->query($sql);
		
		$success = 1;
		
		// Кол-во новых предложений имущества
		$new_goods_count = get_new_goods_count_for_users($current_user_id);
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'new_goods_count' => $new_goods_count));
	break;
	
	// Забрать имущество себе
	case 'good_take_away':
		
		$good_id = value_proc($_POST['good_id']);
		
		// Данные по имуществу
		$sql = "SELECT * FROM ".GOODS_TB." WHERE good_id='$good_id'";
		
		$good_data = $site_db->query_firstrow($sql);
		
		// Проверка на возможность передачи имущества
		if(!check_user_access_to_user_content($good_data['good_owner_user_id'], array(0,1,0,0,0)))
		{
			exit();
		}
		
		$sql = "UPDATE ".GOODS_TB." SET good_owner_user_id='$current_user_id',  good_to_new_owner_id=0 WHERE good_id='$good_id'";
		
		$site_db->query($sql);
		
		// Добавляем в таблицу держателя имущества
		add_to_good_owners($good_id, $current_user_id);
		
		if(!mysql_error())
		{
			echo 1;
		}
	
	break;
	
	// Добавляет отчет
	case 'add_good_report':
		
		$good_id = value_proc($_POST['good_id']);
		
		$report_text = value_proc($_POST['report_text']);
		
		if(!$good_id)
		{
			exit();
		}
		
		if($report_text=='')
		{
			$error['report_text'] = 1;
		}
		
		if(!$error)
		{
			// Получаем имущесвто
			$sql = "SELECT * FROM ".GOODS_TB." WHERE good_id='$good_id'";
			
			$good_data = $site_db->query_firstrow($sql);
		
			// Ошибки
			if(($current_user_id!=$good_data['good_owner_user_id']))
			{
				exit();
			}
			
			// Добавляем отчет
			$sql = "INSERT INTO ".GOODS_REPORTS_TB." SET good_id='$good_id', user_id='$current_user_id', report_date=NOW(), report_text='$report_text'";
			
			$site_db->query($sql);
			
			$report_id = $site_db->get_insert_id();
			
			$success = 1;
			
		}
		
		
		echo json_encode(array('success' => $success, 'error' => $error, 'report_id' => $report_id));
		
	break;
	
	case 'get_good_report_item':
		
		$report_id = value_proc($_POST['report_id']);
		
		$good_id = value_proc($_POST['good_id']);
		
		$report_list = fill_good_reports_list($good_id, $report_id);
		
		echo $report_list;
	break;
	
	case 'get_goods_access_block':
		
		$good_id = value_proc($_POST['good_id']);
		
		$access_block = fill_good_owner_block($good_id);
		
		echo $access_block;
		
	break;
	
	case 'save_good_access':
		
		$good_id = value_proc($_POST['good_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		// Проверяем пользователя, является ли он хозяином имущества
		$sql = "SELECT * FROM ".GOODS_TB." WHERE good_id='$good_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['good_owner_user_id']!=$current_user_id)
		{
			 exit();
		}
		 
		// 
		$sql = "SELECT good_to_new_owner_id FROM ".GOODS_TB." WHERE good_id='$good_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['good_to_new_owner_id']!=$user_id)
		{
			// Добавляем 
			$sql = "UPDATE ".GOODS_TB." SET good_to_new_owner_id='$user_id' WHERE good_id='$good_id'";
			
			$site_db->query($sql);
			
			echo 1;
		}
		
		 
	break;
}

?>