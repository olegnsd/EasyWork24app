<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_clients.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deals.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_sms.php';
// Класс авторизации
$auth = new CAuth($site_db);
global $system_sms;
$system_sms=1;
$mode = $_POST['mode'] ? $_POST['mode'] : $_GET['mode'];


$current_user_id = $auth->get_current_user_id();

if(!$current_user_id)
{
	exit();
}

switch($mode)
{
	// Добавить клиента  сотрудника
	case 'add_new_client':
		// Проверка авторизации
		if(!$auth->check_auth())
		{
			exit();
		}
	
		$client_name = value_proc($_POST['client_name']);
		
		$client_inn = value_proc($_POST['client_inn']);
		
		$client_address_actual = value_proc($_POST['client_address_actual']);
		
		$client_address_legal = value_proc($_POST['client_address_legal']);
		
		$client_phone = value_proc($_POST['client_phone']);
		
		$client_fax = value_proc($_POST['client_fax']);
		
		$client_email = value_proc($_POST['client_email']);
		
		$client_bank_name = value_proc($_POST['client_bank_name']);
		
		$client_bik = value_proc($_POST['client_bik']);
		
		$client_bank_account = value_proc($_POST['client_bank_account']);
		
		$client_desc = value_proc($_POST['client_desc']);
		
		$client_private_edit = value_proc($_POST['client_private_edit']);
		
		$client_private_show = value_proc($_POST['client_private_show']);
		
		$client_contact_person = value_proc($_POST['client_contact_person']);
		
		$client_organization_type  = value_proc($_POST['client_organization_type']);
		
		// Название контакта пустое
		if($client_name=='')
		{
			$error['client_name'] = 1;
		}
		
		// Проверяем на наличие такого клиента
		$sql = "SELECT client_id FROM ".CLIENTS_TB." WHERE client_name='$client_name' AND client_deleted<>1";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['client_id'])
		{
			$error['client_name'] = 2;
		}
		
		
		if(!$error)
		{
			$client_password = generate_client_password();
			
			$client_password_hash = password_hash_proc($client_password);
			
			// Добавляем контакт
			$sql = "INSERT INTO ".CLIENTS_TB." SET client_name='$client_name', client_inn='$client_inn', client_contact_person='$client_contact_person', client_address_actual='$client_address_actual', client_address_legal='$client_address_legal',  	client_phone='$client_phone', client_fax='$client_fax', client_email='$client_email', client_bank_name='$client_bank_name',  client_bik='$client_bik', client_bank_account='$client_bank_account', client_desc='$client_desc', client_private_edit='$client_private_edit', client_private_show='$client_private_show',  client_date_add=NOW(), user_id='$current_user_id', client_organization_type_id = '$client_organization_type', client_password = '$client_password_hash'";
					
			$site_db->query($sql);
			
			// Выбираем доабвленного клиента
			$sql = "SELECT client_id FROM ".CLIENTS_TB." WHERE user_id='$current_user_id' ORDER by client_id DESC LIMIT 1";
			
			$row = $site_db->query_firstrow($sql);
			
			$client_inserted_id = $row['client_id'];
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'client_inserted_id' => $client_inserted_id));
			
	break;
	
	
	// Сохранить клиента  сотрудника
	case 'save_client':
		
		$client_id = value_proc($_POST['client_id']);
		
		$client_name = value_proc($_POST['client_name']);
		
		$client_inn = value_proc($_POST['client_inn']);
		
		$client_address_actual = value_proc($_POST['client_address_actual']);
		
		$client_address_legal = value_proc($_POST['client_address_legal']);
		
		$client_phone = value_proc($_POST['client_phone']);
		
		$client_fax = value_proc($_POST['client_fax']);
		
		$client_email = value_proc($_POST['client_email']);
		
		$client_bank_name = value_proc($_POST['client_bank_name']);
		
		$client_bik = value_proc($_POST['client_bik']);
		
		$client_bank_account = value_proc($_POST['client_bank_account']);
		
		$client_desc = value_proc($_POST['client_desc']);
		
		$client_private_edit = value_proc($_POST['client_private_edit']);
		
		$client_private_show = value_proc($_POST['client_private_show']);
		
		$client_contact_person = value_proc($_POST['client_contact_person']);
		
		$client_organization_type = value_proc($_POST['client_organization_type']);
	
		// Данные клиента
		$sql = "SELECT * FROM ".CLIENTS_TB." WHERE client_id='$client_id'";
		
		$client_data = $site_db->query_firstrow($sql);
	
		// Если пользователь не имеет возможности редактировать клиента - выход!
		if(!is_client_open_for_edit_for_user($current_user_id, $client_data))
		{
			exit();
		}
		
		// Название контакта пустое
		if($client_name=='')
		{
			$error['client_name'] = 1;
		}
		
		// Проверяем на наличие такого клиента
		$sql = "SELECT client_id FROM ".CLIENTS_TB." WHERE client_name='$client_name' AND client_deleted<>1 AND client_id<>'$client_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['client_id'])
		{
			$error['client_name'] = 2;
		}
		
		//echo $client_bik;
		if(!$error)
		{
			if($client_data['user_id']==$current_user_id)
			{
				$and_client_private_edit = ", client_private_edit='$client_private_edit'";
			}
			
			// Добавляем контакт
			$sql = "UPDATE ".CLIENTS_TB." SET client_name='$client_name', client_inn='$client_inn', client_contact_person='$client_contact_person', client_address_actual='$client_address_actual', client_address_legal='$client_address_legal',  client_phone='$client_phone', client_fax='$client_fax', client_email='$client_email', client_bank_name='$client_bank_name',  client_bik='$client_bik', client_bank_account='$client_bank_account', client_desc='$client_desc',  client_date_add=NOW(), client_organization_type_id = '$client_organization_type' $and_client_private_edit
					WHERE client_id='$client_id'";
					 
			$site_db->query($sql);
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error));
			
	break;
	
	
	// Возвращает форму редактирвоания клиента
	case 'get_client_form':
	
		if(!$current_user_id) exit();
		
		$client_id = value_proc($_POST['client_id']);
		
		$client_show = value_proc($_POST['client_show']);
		
		$form = value_proc($_POST['form']);
		
		$sql = "SELECT * FROM ".CLIENTS_TB." WHERE client_id='$client_id'";
		
		$row = $site_db->query_firstrow($sql);
	
		// Получаем форму редактирования
		$client_edit_form = fill_client_list_item($row, $form, '',0,0,$client_show);
		
		echo $client_edit_form;
	break;
	
	// Удалить клиента
	case 'delete_client':
		
		$client_id = value_proc($_POST['client_id']);
		
		// Данные клиента
		$sql = "SELECT * FROM ".CLIENTS_TB." WHERE client_id='$client_id'";
		
		$client_data = $site_db->query_firstrow($sql);
		
		// Если пользователь не имеет возможности удалять клиента - выход!
		if(!is_client_open_for_delete_for_user($current_user_id, $client_data))
		{
			exit();
		}
		$sql = "UPDATE ".CLIENTS_TB." SET  client_deleted='1' WHERE client_id='$client_id'";
		
		$site_db->query($sql);
		
		$_SESSION['client_deleted'][] = $client_id;
		
		echo 1;
	break;
	
	// Восстановить клиента
	case 'restore_client':
		
		$client_id = value_proc($_POST['client_id']);

		$sql = "UPDATE ".CLIENTS_TB." SET  client_deleted='0' WHERE client_id='$client_id'";
		
		$site_db->query($sql);
		
		$_SESSION['client_deleted'][$client_id]=='';
		
		echo 1;
	break;
	
	// Возвращает список клиентов
	case 'get_more_clients':
		
		$user_id = value_proc($_POST['user_id']);
		
		$page = value_proc($_POST['page']);
		
		$search_word = value_proc($_POST['search_word']);
		
		$client_list_type = value_proc($_POST['client_list_type']);
	
		$clients_list = fill_clients_list($client_list_type, $page, $search_word);
		 
		echo $clients_list;
		
	break;
	
	// Поиск по клиентам
	case 'clients_search':
		
		$search_word = value_proc($_POST['search_word']);
		
		$client_list_type = $_POST['client_list_type'];
		
		// Очистка массива удаленных контактов
		if($_SESSION['client_deleted'])
		{
			$_SESSION['client_deleted']='';
		}
	
		// Кол-во найденных клиентов
		$clients_count = get_user_clients_count($client_list_type, $search_word);
			
		// Список клиентов
		$clients_list = fill_clients_list($client_list_type, 1, $search_word);
		 
		// Кол-во страниц
		$pages_count = ceil($clients_count/CLIENTS_PER_PAGE);
		
		if(!$clients_list)
		{
			$clients_list  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/clients/no_searched_clients.tpl');
		}
		
		// Возвращаем результат
		echo json_encode(array('clients_list' => iconv('cp1251', 'utf-8', $clients_list), 'clients_count' => $clients_count, 'pages_count' => $pages_count));

	break;
	
	case 'take_access_to_client':
		
		$client_id = value_proc($_POST['client_id']);
		
		$phone = value_proc($_POST['phone']);
		
		if(!$current_user_id)
		{
			exit();
		}
		
		if(!$phone)
		{
			$error['phone'] = 1;
		}
		else
		{
			### Отправка SMS
			// Заполянем объект пользователя
			$user_data = $user_obj->fill_user_data($to_user_id);
			
			// Генерируем пароль
			$client_password = generate_client_password();
			
			$password_hash = password_hash_proc($client_password);
			
			// Обновляем пароль для клиента
			$sql = "UPDATE ".CLIENTS_TB." SET client_password='$password_hash' WHERE client_id='$client_id'"; 
			
			$site_db->query($sql);
			
			// Добавляем в логи передачи доступа клиентской части
			$sql = "INSERT INTO ".CLIENTS_ACCESSES_TB." (user_id, access_date, phone, client_id) VALUES ('$current_user_id', NOW(), '$phone', '$client_id')";
			$site_db->query($sql);
			
			### sms body
			$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/clients_take_access.tpl');
			
			$PARS['{SAIT}'] = $_SERVER['HTTP_HOST'];
			
			$PARS['{LOGIN}'] = $client_id;
			
			$PARS['{PASS}'] = $client_password;
			 
			$sms_text = fetch_tpl($PARS, $sms_tpl);
			###\ sms body
			
			// Отправка смс сообщения
			send_sms_msg($phone, $sms_text);
			 
			$success = 1;
		}
		// Возвращаем результат
		echo json_encode(array('success' =>$success, 'error' => $error));
		
	break;
	
	case 'give_access_to_client':
		
		$client_id = value_proc($_POST['client_id']);
		
		$user_id = value_proc($_POST['user_id']);
	
		if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1)))
		{
			exit();
		}
		
		// Данные документа
		$sql = "SELECT * FROM ".CLIENTS_TB." WHERE client_id='$client_id'";
		
		$client_data = $site_db->query_firstrow($sql);
		
		
		// Выбор всех сотрудников, имеющих доступ к заметке
		$sql = "SELECT * FROM ".CLIENT_USER_ACCESS_TB." WHERE client_id='$client_id'";
		
		$res = $site_db->query($sql);
			
		while($row=$site_db->fetch_array($res))
		{
			$users_access_arr[] = $row['user_id'];
		}
		
		// Проверка на возможность передать клиента
		if(!in_array($current_user_id, $users_access_arr) && $current_user_id!=$client_data['user_id'])
		{
			exit();
		}
		
		// Проверяем, есть ли доступ у пользователя
		$sql = "SELECT id FROM ".CLIENT_USER_ACCESS_TB." WHERE user_id='$user_id' AND client_id='$client_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			$sql = "DELETE FROM ".CLIENT_USER_ACCESS_TB." WHERE id='".$row['id']."'";
			
			$site_db->query($sql);
			
			if(!mysql_error())
				echo 1;
		}
		else
		{
			$sql = "INSERT INTO ".CLIENT_USER_ACCESS_TB." (client_id, user_id, access_by_user_id, date) VALUES ('$client_id', '$user_id', '$current_user_id', NOW())";
			
			$site_db->query($sql);
			
			if(!mysql_error())
				echo 2;
		}
		
	break;
	
	case 'import_clients':
		
		if(!$_FILES['uploadfile']['tmp_name'])
		{
			exit();
		}
		
		// Расширение файла
		$file_type = strtolower(substr($_FILES['uploadfile']['name'],strrpos($_FILES['uploadfile']['name'], '.')+1,10));
	 
	 	
		// Разрешенные форматы
		$true_file_type = array('csv');
		
		//move_uploaded_file($_FILES['uploadfile']['tmp_name']
		
		// ПРоверяем разрешение файлов
		if(!in_array($file_type, $true_file_type))
		{
			echo '0';
			exit();
		}
		
		// Конечная директория файла
		$import_file = TEMP_PATH.'/'.rand(1,1000).'-'.$_FILES['uploadfile']['name'];   
		
		// Успешное копирование
		if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $import_file))
		{
			$preview = get_import_clients_list_for_preview($import_file);
			
			if($preview)
			{
				echo $preview;
			}
		}
		else
		{
			echo 2;
		}
	break;
	
	case 'client_import_save':
		
		$import_file = value_proc($_POST['import_file']);
		
		$client_private_edit = value_proc($_POST['client_private_edit']);
		
		//$client_private_show = value_proc($_POST['client_private_show']);
		
		$result = insert_import_clients(TEMP_PATH.'/'.$import_file, $client_private_edit);
		
		echo $result;
		
	break;
	
	case 'save_client_user_access':
		
		$client_id = value_proc($_POST['client_id']);
		$access_users = (array)json_decode(str_replace('\\', '', $_POST['access_users']), 1);

		 
		$sql = "SELECT * FROM ".CLIENTS_TB." WHERE client_id='$client_id'";
		
		$client_data = $site_db->query_firstrow($sql);
		
		
		// Выбор всех сотрудников, имеющих доступ к заметке
		$sql = "SELECT * FROM tasks_clients_users_access WHERE client_id='$client_id'";
		
		$res = $site_db->query($sql);
		
		$users_access_arr = array();
			
		while($row=$site_db->fetch_array($res))
		{
			$users_access_arr[$row['user_id']] = $row['user_id'];
		}
		
		//if(!in_array($current_user_id, $users_access_arr) && $current_user_id!=$client_data['user_id'])
		
		if($current_user_id != $client_data['user_id'] && !check_client_for_available($current_user_id, $client_data['client_id'], $client_data) && 
	!is_client_open_for_edit_for_user($current_user_id, $client_data) )
		{
			exit();
		}

		
		$to_delete = array_diff($users_access_arr, $access_users);
		$to_add = array_diff($access_users, $users_access_arr);
		 
		foreach($to_delete as $user_id)
		{
			if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1,1)) && $client_data['user_id']!=$current_user_id)
			{
				continue;
			}
			
			$sql = "DELETE FROM tasks_clients_users_access WHERE user_id='$user_id' AND client_id='$client_id'";
			
			$site_db->query($sql);
		}
		 
		foreach($to_add as $user_id)
		{  
			if(!$user_id || $user_id==$client_data['user_id'])
			{
				continue;
			}
			
			if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1,1)))
			{
				 continue;
			}
		
			$sql = "INSERT INTO tasks_clients_users_access SET user_id='$user_id', client_id='$client_id', access_by_user_id='$current_user_id', date=NOW()";
			
			$site_db->query($sql);
		}
		
		echo 1;
		
	break;
	
	case 'get_client_access_block':
		
		$client_id = value_proc($_POST['client_id']);
		
		$sql = "SELECT * FROM ".CLIENTS_TB." WHERE client_id='$client_id'";
		
		$client_data = $site_db->query_firstrow($sql);
		
		$access_block = fill_client_access_block($client_data);
		
		echo $access_block;
		
	break;
}

?>