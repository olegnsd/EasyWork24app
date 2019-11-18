<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
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
	// Список заданий для сотрудника
	case 'create_folder':
		
		$folder_name = value_proc($_POST['folder_name']);
		
		$is_sharing = $_POST['is_sharing'];
		
		if($folder_name=='')
		{
			$error['folder_name'] = 1;
		}
		if(!$error)
		{
			// Добавляем папку
			$sql = "INSERT INTO ".FOLDERS_TB." SET user_id='$current_user_id', folder_name='$folder_name', is_sharing='$is_sharing', date=NOW()";
			
			$site_db->query($sql);
			
			$sql = "SELECT folder_id FROM ".FOLDERS_TB." ORDER by folder_id DESC LIMIT 1";
			
			$row = $site_db->query_firstrow($sql);
			
			$inserted_folder_id = $row['folder_id'];
			
			// Если папки для пользователя не существует, создаем
			if(!is_dir(PRIVATE_PATH.'/'.$current_user_id))
			{
				mkdir(PRIVATE_PATH.'/'.$current_user_id);
			}
			
			if($is_sharing)
			{
				$folder_path = SHARING_PATH.'/'.$inserted_folder_id;
			}
			else
			{
				$folder_path = PRIVATE_PATH.'/'.$current_user_id.'/'.$inserted_folder_id;
			}
			
			// Создаем папку для файлов
			mkdir($folder_path);
			
			$success = 1;
		}
		echo json_encode(array('success' => $success, 'error' => $error, 'folder_id' => $inserted_folder_id));
	
	break;
	
	// Удалить файл
	case 'delete_file':
		
		$file_id = value_proc($_POST['file_id']);
		// Получаем данные по файлу
		$sql = "SELECT folder_id, file_name, user_id, is_sharing FROM ".FILES_TB." WHERE file_id='$file_id'";
			
		$row = $site_db->query_firstrow($sql);
		
		// Проверка на владельца файла
		if(!is_admin_file($file_id, $current_user_id))
		{
			exit();
		}
		
		if($row['is_sharing'])
		{
			$folder_path = SHARING_PATH;
		}
		else
		{
			$folder_path = PRIVATE_PATH.'/'.$current_user_id;
		}
		if($row['folder_id'])
		{
			$folder_path .= '/'.$row['folder_id'];
		}
			
		unlink($folder_path.'/'.$row['file_name']);
		
		// Удаляем файл из базы
		$sql = "DELETE FROM ".FILES_TB." WHERE file_id='$file_id'";
			
		$site_db->query($sql);
		
		// Удаляем из таблицы доступов
		$sql = "DELETE FROM ".FILES_ACCESS_TB." WHERE file_id='$file_id'";
		
		$site_db->query($sql);
		
		echo 1;
		
	break;
	
	
	// Удалить папку
	case 'delete_folder':
		
		$folder_id = value_proc($_POST['folder_id']);
		
		// Проверка на владельца папки
		if(!is_user_folder($folder_id, $current_user_id))
		{
			exit();
		}
		
		if(is_sharing_folder($folder_id))
		{
			$folder_path = SHARING_PATH;
		}
		else
		{
			$folder_path = PRIVATE_PATH.'/'.$current_user_id;
		}
		
		// Выбираем все файлы в папке
		$sql = "SELECT file_name, file_id FROM ".FILES_TB." WHERE folder_id='$folder_id'";
			
		$res = $site_db->query($sql);
			
		while($row=$site_db->fetch_array($res))
		{ 
			$sql = "DELETE FROM ".FILES_TB." WHERE file_id='".$row['file_id']."'";
			
			$site_db->query($sql);
			
			//echo $folder_path.'/'.$folder_id.'/'.$row['file_name'];
			// Удалеям файл в папке
			unlink($folder_path.'/'.$folder_id.'/'.$row['file_name']);
		}
			
		// Удаляем папку из базы
		$sql = "DELETE FROM ".FOLDERS_TB." WHERE folder_id='$folder_id'";
		
		$site_db->query($sql);
		
		// Удаляем доступы к папке из базы
		$sql = "DELETE FROM ".FILES_ACCESS_TB." WHERE folder_id='$folder_id'";
		
		$site_db->query($sql);
		
		// Удаляем физически папку
		rmdir($folder_path.'/'.$folder_id);
			
		 
			
		echo 1;
		
	break;
	
	// Дать доступ к файлу
	case 'give_access_to_file':
		
		$folder_id = value_proc($_POST['folder_id']);
		
		$file_id = value_proc($_POST['file_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		if($folder_id && !$file_id)
		{
			// Данные файла
			$sql = "SELECT * FROM ".FOLDERS_TB." WHERE folder_id='$folder_id'";
			
			$folder_data = $site_db->query_firstrow($sql);
			
			// Проверяем, открыт ли доступ к папке для пользователя
			$sql = "SELECT id, access_by_user_id FROM ".FILES_ACCESS_TB." WHERE folder_id='$folder_id' AND file_id=0 AND user_id='$user_id'";
			
			$row = $site_db->query_firstrow($sql);
			
			// Если открыт, убираем доступ к папке
			if($row['id'] && ($row['access_by_user_id'] == $current_user_id || $folder_data['user_id']==$current_user_id))
			{
				$sql = "DELETE FROM ".FILES_ACCESS_TB." WHERE folder_id='$folder_id' AND user_id='$user_id'";
				
				$row = $site_db->query($sql);
				
				echo 1;
			}
			else if($row['id'] && $row['access_by_user_id'] != $current_user_id)
			{
				echo '-1';
			}
			else
			{
				$sql = "INSERT INTO ".FILES_ACCESS_TB." SET folder_id='$folder_id', user_id='$user_id', access_by_user_id = '$current_user_id'";
				
				$row = $site_db->query($sql);
				
				echo 2;
			}
		}
		
		if($file_id)
		{
			// Данные файла
			$sql = "SELECT * FROM ".FILES_TB." WHERE file_id='$file_id'";
			
			$file_data = $site_db->query_firstrow($sql);
			
			// Проверяем, открыт ли доступ к папке для пользователя
			$sql = "SELECT id, access_by_user_id FROM ".FILES_ACCESS_TB." WHERE file_id='$file_id' AND user_id='$user_id'";
			
			$row = $site_db->query_firstrow($sql);
			
			// Если открыт, убираем доступ к папке
			if($row['id'] && ($row['access_by_user_id'] == $current_user_id || $file_data['user_id']==$current_user_id))
			{
				$sql = "DELETE FROM ".FILES_ACCESS_TB." WHERE id='".$row['id']."'";
				
				$row = $site_db->query($sql);
				
				echo 1;
			}
			else if($row['id'] && $row['access_by_user_id'] != $current_user_id)
			{
				echo '-1';
			}
			else
			{
				$sql = "INSERT INTO ".FILES_ACCESS_TB." SET file_id='$file_id', folder_id='$folder_id', user_id='$user_id', access_by_user_id = '$current_user_id'";
				
				$row = $site_db->query($sql);
				
				echo 2;
			}
		}
		 
	break;
	
	// Возвращает описание файла или папки
	case 'get_file_desc':
	
		$folder_id = value_proc($_POST['folder_id']);
		
		$file_id = value_proc($_POST['file_id']);
		
		if($folder_id)
		{
			$sql = "SELECT folder_desc FROM ".FOLDERS_TB." WHERE folder_id='$folder_id'";	
			
			$row = $site_db->query_firstrow($sql);
			
			$desc = $row['folder_desc'];
		}
		else
		{
			$sql = "SELECT file_desc FROM ".FILES_TB." WHERE file_id='$file_id'";	
			
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
			$sql = "UPDATE ".FOLDERS_TB." SET folder_desc='$desc' WHERE folder_id='$folder_id'";	
			
			$row = $site_db->query($sql);
		}
		else
		{
			$sql = "UPDATE ".FILES_TB." SET file_desc='$desc' WHERE file_id='$file_id'";	
			
			$row = $site_db->query($sql);

		}
		
		echo 1;
		
	break;
	
	// Возвращает блок статусов для файла
	case 'get_file_statuses':
	
		$folder_id = value_proc($_POST['folder_id']);
		
		$file_id = value_proc($_POST['file_id']);
		
		$file_statuses_block = fill_file_statuses_block($file_id);
		
		echo $file_statuses_block;
	break;
	
	// Добавить статус к документу
	case 'add_file_status':
		
		$file_id = value_proc($_POST['file_id']);
		
		$status_id = value_proc($_POST['status_id']);
		
		$status_text = value_proc($_POST['status_text']);
		
		// Если только оставить комментарий
		if($status_id==4 && $status_text=='')
		{
			$error['status_text'] = 1;
		}
		
		if(!$error)
		{
			// Добавляем запись
			$sql = "INSERT INTO ".FILE_STATUSES_TB." (file_id, status_id, status_text, status_date, user_id) 
					VALUES ('$file_id', '$status_id', '$status_text', NOW(), '$current_user_id')";
					
			$site_db->query($sql);
			
			// Получаем список статусов
			$statuses_list = fill_file_statuses_list($file_id);
			
			if(!mysql_error())
			{
				$success = 1;
			}
		}
		echo json_encode(array('success' => $success, 'error' => $error, 'statuses_list' => iconv('cp1251', 'utf-8', $statuses_list)));
		
	break;
	
	case 'confirm_file':
		
		$file_id = value_proc($_POST['file_id']);
		
		$folder_id = value_proc($_POST['folder_id']);
		
		if($folder_id && !$file_id)
		{
			// Убираем уведомление о новых доступных файлах
			$sql = "UPDATE ".FILES_ACCESS_TB." SET noticed=1 WHERE folder_id='$folder_id' AND user_id='$current_user_id'";
			
			$site_db->query($sql);
		}
		else
		{
			// Убираем уведомление о новых доступных файлах
			$sql = "UPDATE ".FILES_ACCESS_TB." SET noticed=1 WHERE folder_id='$folder_id' AND file_id='$file_id' AND user_id='$current_user_id'";
			
			$site_db->query($sql);
		}
		
		
		// Пересчет кол-ва файлов
		$new_files_count = get_new_files_notice_for_user($current_user_id);
		
		if(!mysql_error())
		{
			$success = 1;
		}
		
		echo json_encode(array('success' => $success, 'new_files_count' => $new_files_count));
		
	break;
	
	case 'hide_file':
		
		$file_id = value_proc($_POST['file_id']);
		
		$folder_id = value_proc($_POST['folder_id']);
		
		if(!mysql_error())
		{
			$success = 1;
		}
		
		if($folder_id && !$file_id)
		{
			// Удаляем доступ к папке и файлам внутри нее
			delete_access_to_folder_files($folder_id, $current_user_id);
		}
		else if($file_id)
		{
			 $sql = "DELETE FROM ".FILES_ACCESS_TB." WHERE file_id='$file_id' AND user_id='$current_user_id'";
				 
			 $site_db->query($sql);
		}
		
		// Пересчет кол-ва файлов
		$new_files_count = get_new_files_notice_for_user($current_user_id);
		
		echo json_encode(array('success' => $success, 'new_files_count' => $new_files_count));
		
	break;
}

?>