<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_contacts.php';
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
	// Добавить контакт сотрудника
	case 'add_contact':
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		}
	
		$user_id = value_proc($_POST['user_id']);
		
		$contact_user_name = value_proc($_POST['contact_user_name']);
		
		$contact_name = value_proc($_POST['contact_name']);
		
		$contact_phone = value_proc($_POST['contact_phone']);
		
		//$contact_organization = value_proc($_POST['contact_organization']);
		
		$contact_job = value_proc($_POST['contact_job']);
		
		$contact_desc = value_proc($_POST['contact_desc']);
		
		$image = value_proc($_POST['image']);
		
		// Название контакта пустое
		if($contact_name=='')
		{
			$error['contact_name'] = 1;
		}
		
		
		if(!$error)
		{
			// Добавляем контакт
			$sql = "INSERT INTO ".CONTACTS_TB." (user_id, contact_user_name, contact_name, contact_phone, contact_job, contact_desc)
					VALUES ('$current_user_id', '$contact_user_name', '$contact_name', '$contact_phone', '$contact_job', '$contact_desc')";
					
			$site_db->query($sql);
			
			$contact_id = $site_db->get_insert_id();
			
			  
			// Добавляем изображение
			if($image)
			{
				
				$date_add = date('Y-m-d H:i:s');
				
				$file_system_name = get_rand_file_system_name($image);
				
				$file_dir = create_upload_folder($date_add, 1).'/'.$file_system_name;
				
				
				$sql = "UPDATE ".CONTACTS_TB." SET contact_image='$file_system_name', image_date_add='$date_add' WHERE contact_id='$contact_id'";
				 
				$site_db->query($sql);
				
				copy(TEMP_PATH.'/'.$image, $file_dir);
				
				unlink(TEMP_PATH.'/'.$image);
			}
			
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'contact_id' => $contact_id));
			
	break;
	
	// Сохранить контакт
	case 'save_contact':
		
		
	
		$contact_id = value_proc($_POST['contact_id']);
		
		$contact_user_name = value_proc($_POST['contact_user_name']);
		
		$contact_name = value_proc($_POST['contact_name']);
		
		$contact_phone = value_proc($_POST['contact_phone']);
		
		$contact_job = value_proc($_POST['contact_job']);
		
		$contact_desc = value_proc($_POST['contact_desc']);
		
		$image = value_proc($_POST['image']);
		
		$image_deleted = value_proc($_POST['image_deleted']);
		
		// Название контакта пустое
		if($contact_name=='')
		{
			$error['contact_name'] = 1;
		}
		
		// Данные контакта
		$sql = "SELECT * FROM ".CONTACTS_TB." WHERE contact_id='$contact_id'";
			
		$contact_data = $site_db->query_firstrow($sql);
			
		// Для создателя контакта, выводим форму редактирования
		if($contact_data['user_id'] != $current_user_id)
		{
			exit();
		}
		
		if(!$error)
		{
			 
			
			
			// Обновляем контакт
			$sql = "UPDATE ".CONTACTS_TB." 
					SET  contact_user_name='$contact_user_name', contact_name='$contact_name', contact_phone='$contact_phone', 
					contact_job='$contact_job', contact_desc='$contact_desc' 
					WHERE contact_id='$contact_id'";
					
			$site_db->query($sql);
			
			if($image_deleted>0)
			{
				
				$sql = "UPDATE ".CONTACTS_TB." SET contact_image='$image' WHERE contact_id='$contact_id'";
				 
				$site_db->query($sql);
				
				// Папка загрузок для контактов
				$upload_path = get_contact_upload_path($contact_id);
				unlink($upload_path.'/'.$contact_data['contact_image']);
				 
			}
			
			// Добавляем изображение
			if($image)
			{
				// Если есть старое изображение, удаляем его
				if($contact_data['contact_image'])
				{
					// Папка загрузок для контактов
					//$upload_path = get_contact_upload_path($contact_id);
					//unlink($upload_path.'/'.$contact_data['contact_image']);
				}
				
				
				$date_add = date('Y-m-d H:i:s');
				
				$file_system_name = get_rand_file_system_name($image);
				
				$file_dir = create_upload_folder($date_add, 1).'/'.$file_system_name;
				
				
				
				// Папка загрузок для контактов
				//$upload_path = get_contact_upload_path($contact_id);
			
				// Создаем папку контактов
				//contact_mkdir($contact_id);
			
				$sql = "UPDATE ".CONTACTS_TB." SET contact_image='$file_system_name', image_date_add='$date_add'  WHERE contact_id='$contact_id'";
				 
				$site_db->query($sql);
				
				
				copy(TEMP_PATH.'/'.$image, $file_dir);
				unlink(TEMP_PATH.'/'.$image);
			}
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error));
			
	break;
	
	// Возвращает форму редактирвоания контакта
	case 'get_contact_form':
	
		$contact_id = value_proc($_POST['contact_id']);
		
		$form = value_proc($_POST['form']);
		
		$sql = "SELECT * FROM ".CONTACTS_TB." WHERE contact_id='$contact_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		// Получаем форму редактирования
		$contact_edit_form = fill_contacts_list_item($row, $form);
		
		echo $contact_edit_form;
	break;
	
	// Удалить контакт
	case 'delete_contact':
		
		$contact_id = value_proc($_POST['contact_id']);
		
		// Если пользователь не является автором контакта
		if(!is_contact_user_id($contact_id, $current_user_id))
		{
			exit();
		}
		
		// Добавляем задание
		$sql = "UPDATE ".CONTACTS_TB." SET  contact_deleted='1' WHERE contact_id='$contact_id'";
		
		$site_db->query($sql);
		
		$_SESSION['contact_deleted'][] = $contact_id;
		
		echo 1;
	break;
	
		// Восстановить контакт
	case 'restore_contact':
		
		$contact_id = value_proc($_POST['contact_id']);
		
		// Если пользователь не является автором контакта
		if(!is_contact_user_id($contact_id, $current_user_id))
		{
			exit();
		}
		
		// Добавляем задание
		$sql = "UPDATE ".CONTACTS_TB." SET  contact_deleted='0' WHERE contact_id='$contact_id'";
		
		$site_db->query($sql);
		
		$_SESSION['contact_deleted'][$contact_id]=='';
		
		echo 1;
	break;
	
	// Возвращает список контактов
	case 'get_more_contacts':
		
		$user_id = value_proc($_POST['user_id']);
		
		$page = value_proc($_POST['page']);
		
		$search_word = value_proc($_POST['search_word']);
		
		$is_wks = value_proc($_POST['is_wks']);
		
		
		if($is_wks)
		{
			// Список контактов Подчиненных
			$contacts_list = fill_workers_contacts_list($page, $search_word);
		}
		else
		{
			// Список контактов
			$contacts_list = fill_contacts_list($current_user_id, $page, $search_word);
		}
		
		echo $contacts_list;
		
	break;
	
	// Поиск по контактам
	case 'contact_search':
		
		$search_word = value_proc($_POST['search_word']);
		
		$is_wks = value_proc($_POST['is_wks']);
		
		// Очистка массива удаленных контактов
		if($_SESSION['contact_deleted'])
		{
			$_SESSION['contact_deleted']='';
		}
		
		if($is_wks)
		{
			// Список контактов Подчиненных
			$contacts_list = fill_workers_contacts_list(1, $search_word);
			
			// Кол-во контактов
			$contacts_count = get_workers_contacts_count($search_word);
		}
		else
		{
			// Список контактов
			$contacts_list = fill_contacts_list($current_user_id, 1, $search_word);
			
			// Кол-во контактов пользователя
			$contacts_count = get_user_contacts_count($current_user_id, $search_word);
		}
		
		// Кол-во страниц
		$pages_count = ceil($contacts_count/CONTACTS_PER_PAGE);
		
		if(!$contacts_list)
		{
			$contacts_list  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/contacts/no_searched_contacts.tpl');
		}
		

	
		// Возвращаем результат
		echo json_encode(array('contacts_list' => iconv('cp1251', 'utf-8', $contacts_list), 'contacts_count' => $contacts_count, 'pages_count' => $pages_count));
		
		//echo $contacts_list;
	break;
}

?>