<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/client/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/client/includes/functions_files.php'; 
 
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $_SESSION['user_id'];

$current_client_id  = $auth->get_current_client_id();

switch($mode)
{
	// Список заданий для сотрудника
	case 'client_create_folder':
		
		$folder_name = value_proc($_POST['folder_name']);
		
		$client_id = $_POST['client_id'];

		$from_user_id = $_POST['from_user_id'];
		
		$from_client_id = $_POST['from_client_id'];
		
		if($folder_name=='')
		{
			$error['folder_name'] = 1;
		}
		
		if(!$error)
		{
			if($from_client_id)
			{
				// Добавляем папку
				$sql = "INSERT INTO ".CLIENTS_FOLDERS_TB." SET client_id='$client_id', folder_name='$folder_name',
						date=NOW(), from_user_id=0, from_client_id='$client_id'";
			}
			else if($from_user_id)
			{
				// Добавляем папку
				$sql = "INSERT INTO ".CLIENTS_FOLDERS_TB." SET client_id='$client_id', folder_name='$folder_name',
						date=NOW(), from_user_id='$current_user_id', from_client_id='0'";
			} 
			$site_db->query($sql);
			
			$inserted_folder_id = $site_db->get_insert_id();
			
			
			// Если папки для пользователя не существует, создаем
			if(!is_dir(CLIENTS_PATH.'/'.$client_id))
			{
				mkdir(CLIENTS_PATH.'/'.$client_id);
				mkdir(CLIENTS_PATH.'/'.$client_id.'/in');
				mkdir(CLIENTS_PATH.'/'.$client_id.'/out');
			}
			
			if($from_client_id)
			{
				$folder_path = CLIENTS_PATH.'/'.$client_id.'/out/'.$inserted_folder_id;
			}
			else if($from_user_id)
			{
				$folder_path = CLIENTS_PATH.'/'.$client_id.'/in/'.$inserted_folder_id;
			}
			
			// Создаем папку для файлов
			mkdir($folder_path);
			
			$success = 1;
		}
		echo json_encode(array('success' => $success, 'error' => $error, 'folder_id' => $inserted_folder_id));
	
	break;
	
	// Возвращает описание файла или папки
	case 'get_file_desc':
	
		$folder_id = value_proc($_POST['folder_id']);
		
		$file_id = value_proc($_POST['file_id']);
		
		if($folder_id)
		{
			$sql = "SELECT folder_desc FROM ".CLIENTS_FOLDERS_TB." WHERE folder_id='$folder_id'";	
			
			$row = $site_db->query_firstrow($sql);
			
			$desc = $row['folder_desc'];
		}
		else
		{
			$sql = "SELECT file_desc FROM ".CLIENTS_FILES_TB." WHERE file_id='$file_id'";	
			
			$row = $site_db->query_firstrow($sql);
			
			$desc = $row['file_desc'];
		}
		 
		echo $desc;
		 
	break;
	
	// Сохранить описание для файла или папки
	case 'save_file_desc':
		
		$folder_id = value_proc($_POST['folder_id']);
		
		$file_id = value_proc($_POST['file_id']);
		
		$desc = substr(value_proc($_POST['desc']),0, 100);
		
		if($folder_id)
		{
			$sql = "UPDATE ".CLIENTS_FOLDERS_TB." SET folder_desc='$desc' WHERE folder_id='$folder_id'";	
			
			$row = $site_db->query($sql);
		}
		else
		{
			$sql = "UPDATE ".CLIENTS_FILES_TB." SET file_desc='$desc' WHERE file_id='$file_id'";	
			
			$row = $site_db->query($sql);

		}
		
		echo 1;
		
	break;
	
	// Удалить файл
	case 'delete_client_file':
		
		$file_id = value_proc($_POST['file_id']);
		
		// Получаем данные по файлу
		$sql = "SELECT folder_id, file_name, from_client_id, from_user_id, client_id FROM ".CLIENTS_FILES_TB." WHERE file_id='$file_id'";
			
		$row = $site_db->query_firstrow($sql);
		
		// Проверка на владельца файла
		if(!is_admin_file_cl($file_id, $current_client_id, $current_user_id))
		{
			exit();
		}
		
		$folder_path = CLIENTS_PATH.'/'.$row[''];
		
		if($row['from_client_id'])
		{
			$folder_path = CLIENTS_PATH.'/'.$row['client_id'].'/out';
		}
		else if($row['from_user_id'])
		{
			$folder_path = CLIENTS_PATH.'/'.$row['client_id'].'/in';
		}

		if($row['folder_id'])
		{
			$folder_path .= '/'.$row['folder_id'];
		}
		
		unlink($folder_path.'/'.$row['file_name']);
		
		// Удаляем файл из базы
		$sql = "DELETE FROM ".CLIENTS_FILES_TB." WHERE file_id='$file_id'";
			
		$site_db->query($sql);
		
		echo 1;
		
	break;
	
	// Удалить папку
	case 'delete_client_folder':
		
		$folder_id = value_proc($_POST['folder_id']);
		
		// Проверка на владельца папки
		if(!is_admin_folder($folder_id,  $current_client_id, $current_user_id))
		{
			exit();
		}
		
		// Данные папки
		$sql = "SELECT * FROM ".CLIENTS_FOLDERS_TB." WHERE folder_id='$folder_id'";
		
		$folder_data = $site_db->query_firstrow($sql);
		
		$folder_path = CLIENTS_PATH.'/'.$row[''];
		
		if($folder_path['from_client_id'])
		{
			$folder_path = CLIENTS_PATH.'/'.$folder_data['client_id'].'/out';
		}
		else if($folder_path['from_user_id'])
		{
			$folder_path = CLIENTS_PATH.'/'.$folder_data['client_id'].'/in';
		}
		
		// Выбираем все файлы в папке
		$sql = "SELECT file_name, file_id, client_id FROM ".CLIENTS_FILES_TB." WHERE folder_id='$folder_id'";
			
		$res = $site_db->query($sql);
			
		while($row=$site_db->fetch_array($res))
		{ 
			$sql = "DELETE FROM ".CLIENTS_FILES_TB." WHERE file_id='".$row['file_id']."'";
			
			$site_db->query($sql);
			 
			// Удаляем файл в папке
			unlink($folder_path.'/'.$folder_id.'/'.$row['file_name']);
		}
			
		// Удаляем папку из базы
		$sql = "DELETE FROM ".CLIENTS_FOLDERS_TB." WHERE folder_id='$folder_id'";
		
		$site_db->query($sql);
		
		// Удаляем физически папку
		rmdir($folder_path.'/'.$folder_id);
			
		echo 1;
		
	break;
}

?>