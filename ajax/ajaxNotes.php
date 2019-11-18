<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_notes.php';

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
	// Поиск по клиентам
	case 'notes_search':
		
		$search_word = value_proc($_POST['search_word']);
		
		$is_av = value_proc($_POST['is_av']);
		
		// Очистка массива удаленных контактов
		if($_SESSION['note_delete'])
		{
			$_SESSION['note_delete']='';
		}
		
		$content = get_notes_content($is_av, $search_word);
	 	
		
		// Возвращаем результат
		echo json_encode(array('content' => iconv('cp1251', 'utf-8', $content)));

	break;
	
	// Добавить заметку
	case 'add_note':
		
		$note_text = value_proc($_POST['note_text']);
		
		$note_theme = value_proc($_POST['note_theme']);
		
		
		if(!$note_text)
		{
			$error['text'] = 1;
		}
	
		
		if(!$error)
		{
			// Добавляем запись
			$sql = "INSERT INTO ".NOTES_TB." (user_id, date, last_date_edit) VALUES ('$current_user_id', NOW(), NOW())";
			
			$site_db->query($sql);
			
			$note_id = $site_db->get_insert_id(); 
			
			// Добавляем версию для заметки
			$sql = "INSERT INTO ".NOTE_VERISONS_TB." SET
					note_id='$note_id', user_id='$current_user_id', note_text='$note_text', note_theme='$note_theme', date=NOW(), is_original=1";
			
			$site_db->query($sql);
			
			$success = 1;
		}
			
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error, 'note_id' => $note_id));
	
	break;
	
	
	// 
	case 'get_more_notes':
		
		$page = value_proc($_POST['page']);
		
		$is_av = value_proc($_POST['is_av']);
		
		$search_word = value_proc($_POST['search_word']);
		 
	
		// Чужие заметки
		if($is_av)
		{
			// Список чужих заметок
			$notes_list = fill_avalible_notes_list($current_user_id, $page, $search_word);
		}
		else
		{
			// Список заметок пользователя
			$notes_list = fill_notes_list($current_user_id, $page, $search_word);
		}
		
		echo $notes_list;
		
	break;
	
	case 'get_note_item':
		
		$note_id = value_proc($_POST['note_id']);
		
		// Данные документа
		$sql = "SELECT * FROM ".NOTES_TB." WHERE note_id='$note_id'";
		
		$note_data = $site_db->query_firstrow($sql);
		
		$note_item = fill_note_list_item($note_data);
		
		echo $note_item;
		
	break;
	
	case 'get_note_version_item':
		
		$version_id = value_proc($_POST['version_id']);
		
		$form = value_proc($_POST['form']);
		
		$is_show = value_proc($_POST['is_show']);
	
		// Выбор версии заметки
		$sql = "SELECT * FROM ".NOTE_VERISONS_TB." WHERE version_id='$version_id'";
		
		$version_data = $site_db->query_firstrow($sql);
		
		
		// Выбираем заметки
		$sql = "SELECT * FROM ".NOTES_TB." WHERE note_id='".$version_data['note_id']."' ";
		 
		$note_data = $site_db->query_firstrow($sql);
		
		
		$note_versions_item = fill_note_version_item($version_data, $form, $note_data);
				
		echo $note_versions_item;
		
	break;
	
	case 'get_note_version_for_user':
		
		$note_id = value_proc($_POST['note_id']);
		
		// Данные документа
		$sql = "SELECT * FROM ".NOTES_TB." WHERE note_id='$note_id'";
		
		$note_data = $site_db->query_firstrow($sql);
		
		$note_version_item = get_note_version_for_user($note_data['note_id'], $note_data);
				
		echo $note_version_item;
		
	break;
	
	case 'get_note_versions_list':
		
		$note_id = value_proc($_POST['note_id']);
		
		$note_versions_list = fill_note_versions_list($note_id);
				
		echo $note_versions_list;
		
	break;
	
	case 'save_note_version':
		
		$version_id = value_proc($_POST['version_id']);
		
		$note_id = value_proc($_POST['note_id']);
		
		$note_text = value_proc($_POST['note_text']);
		
		$note_theme = value_proc($_POST['note_theme']);
		
		if(!$note_text)
		{
			$error['text'] = 1;
		}
		
		if(!$error)
		{
			// Выбор версии заметки, которую редактирвоали
			$sql = "SELECT * FROM ".NOTE_VERISONS_TB." WHERE version_id='$version_id'";
			
			$version_data_1 = $site_db->query_firstrow($sql);
			
			// Выбор версии заметки
			$sql = "SELECT * FROM ".NOTE_VERISONS_TB." WHERE note_id='$note_id' AND user_id='$current_user_id' AND deleted<>1";
			
			$version_data = $site_db->query_firstrow($sql);
			
			 
			if(!$version_data['version_id'])
			{
				// Если изменений нет, не создаем новую версию для пользователя
				if($version_data_1['note_text']!=$note_text || $version_data_1['note_text']!=$note_theme)
				{
					// Добавляем версию для заметки
					$sql = "INSERT INTO ".NOTE_VERISONS_TB." SET
							note_id='$note_id', user_id='$current_user_id', note_text='$note_text', note_theme='$note_theme', date=NOW(), date_edit=NOW()";
				
					$site_db->query($sql);
					
					// Добавляем версию для заметки
					$sql = "UPDATE ".NOTES_TB." SET last_date_edit=NOW() WHERE note_id='".$note_id."'";
				 
					$site_db->query($sql);
				}
			}
			else
			{
				// Добавляем версию для заметки
				$sql = "UPDATE ".NOTE_VERISONS_TB." SET
						note_text='$note_text', note_theme='$note_theme', date_edit=NOW() WHERE version_id='".$version_data['version_id']."'";
				 
				$site_db->query($sql);
				
				// Добавляем версию для заметки
				$sql = "UPDATE ".NOTES_TB." SET last_date_edit=NOW() WHERE note_id='".$note_id."'";
				 
				$site_db->query($sql);
				 
			}
						
			$success  = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'error' => $error));
		
	break;
	
	case 'get_note_title':
		
		$note_id = value_proc($_POST['note_id']);
		
		$note_title = cut_note_title('', $note_id);
		
		echo $note_title;
		
	break;
	
	case 'delete_note':
		
		$note_id = value_proc($_POST['note_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "UPDATE ".NOTES_TB." SET deleted=1 WHERE note_id='$note_id' AND user_id='$user_id'";
		
		$site_db->query($sql);
		
		$_SESSION['note_delete'][] = $note_id;
		
		echo 1;
	break;
	
	case 'restore_note':
		
		$note_id = value_proc($_POST['note_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "UPDATE ".NOTES_TB." SET deleted=0 WHERE note_id='$note_id' AND user_id='$user_id'";
		
		$site_db->query($sql);
		
		$_SESSION['note_delete'][$note_id]=='';
		
		echo 1;
	break;
	
	case 'delete_note_version':
		
		$version_id = value_proc($_POST['version_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "UPDATE ".NOTE_VERISONS_TB." SET deleted=1 WHERE version_id='$version_id' AND user_id='$user_id'";
		
		$site_db->query($sql);
		
		echo 1;
	break;
	
	case 'restore_note_version':
		
		$version_id = value_proc($_POST['version_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		$sql = "UPDATE ".NOTE_VERISONS_TB." SET deleted=0 WHERE version_id='$version_id' AND user_id='$user_id'";
		
		$site_db->query($sql);
		
		echo 1;
	break;

	
	case 'reprimand_confirm':
		
		$reprimand_id = value_proc($_POST['reprimand_id']);
		
		$user_id = value_proc($_POST['user_id']);
		
		if($user_id!=$current_user_id)
		{
			exit();
		}
		
		$sql = "UPDATE ".REPRIMANDS_TB." SET noticed=1 WHERE reprimand_id='$reprimand_id'  AND  	worker_id='$user_id' ";
		
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			$success = 1;
		}
		
		// Колв-о новых выговоров 
		$new_reprimand_count = get_new_workers_reprimands_count($current_user_id);
		 
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'new_reprimand_count' => $new_reprimand_count));
		
	break;
	
	case 'give_access_to_note':
		
		$note_id = value_proc($_POST['note_id']);
		
		$user_id = value_proc($_POST['user_id']);
	
		if(!check_user_access_to_user_content($user_id, array(1,1,0,1,1)))
		{
			exit();
		}
		
		// Данные документа
		$sql = "SELECT * FROM ".NOTES_TB." WHERE note_id='$note_id'";
		
		$note_data = $site_db->query_firstrow($sql);
		
		
		// Выбор всех сотрудников, имеющих доступ к заметке
		$sql = "SELECT * FROM ".NOTE_ACCESS_TB." WHERE note_id='$note_id'";
		
		$res = $site_db->query($sql);
			
		while($row=$site_db->fetch_array($res))
		{
			$users_access_arr[] = $row['user_id'];
		}
		
		if(!in_array($current_user_id, $users_access_arr) && $current_user_id!=$note_data['user_id'])
		{
			exit();
		}
		
		// Проверяем, есть ли доступ у пользователя
		$sql = "SELECT id FROM ".NOTE_ACCESS_TB." WHERE user_id='$user_id' AND note_id='$note_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			$sql = "DELETE FROM ".NOTE_ACCESS_TB." WHERE id='".$row['id']."'";
			
			$site_db->query($sql);
			
			echo 1;
		}
		else
		{
			$sql = "INSERT INTO ".NOTE_ACCESS_TB." (note_id, user_id, access_by_user_id, date) VALUES ('$note_id', '$user_id', '$current_user_id', NOW())";
			
			$site_db->query($sql);
			
			echo 2;
		}
		
	break;
	
	case 'hide_accessed_note':
		
		$note_id = value_proc($_POST['note_id']);
		
		$sql = "DELETE FROM ".NOTE_ACCESS_TB." WHERE note_id='$note_id' AND user_id='$current_user_id'";
			
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			echo 1;
		}
	break;
	
	case 'save_note_user_access':
		
		$note_id = value_proc($_POST['note_id']);
		$access_users = (array)json_decode(str_replace('\\', '', $_POST['access_users']), 1);
		 
		// Данные заметки
		$sql = "SELECT * FROM ".NOTES_TB." WHERE note_id='$note_id'";
		
		$note_data = $site_db->query_firstrow($sql);
		
		
		// Выбор всех сотрудников, имеющих доступ к заметке
		$sql = "SELECT * FROM ".NOTE_ACCESS_TB." WHERE note_id='$note_id'";
		
		$res = $site_db->query($sql);
		
		$users_access_arr = array();
			
		while($row=$site_db->fetch_array($res))
		{
			$users_access_arr[$row['user_id']] = $row['user_id'];
		}
		
		if(!in_array($current_user_id, $users_access_arr) && $current_user_id!=$note_data['user_id'])
		{
			exit();
		}

		
		$to_delete = array_diff($users_access_arr, $access_users);
		$to_add = array_diff($access_users, $users_access_arr);
		 
		foreach($to_delete as $user_id)
		{
			if(!$user_id)
			{
				//continue;
			}
			
			if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1,1)) && $note_data['user_id']!=$current_user_id)
			{
				continue;
			}
			
			$sql = "DELETE FROM tasks_notes_access WHERE user_id='$user_id' AND note_id='$note_id'";
			
			$site_db->query($sql);
		}
		 
		foreach($to_add as $user_id)
		{  
			if(!$user_id || $user_id==$note_data['user_id'])
			{
				continue;
			}
			
			if(!check_user_access_to_user_content($user_id, array(1,1,1,1,1,1)))
			{
				 continue;
			}
		
			$sql = "INSERT INTO tasks_notes_access SET user_id='$user_id', note_id='$note_id', access_by_user_id='$current_user_id', date=NOW()";
			
			$site_db->query($sql);
		}
		
		echo 1;
		
	break;
	
	case 'get_note_access_block':
		
		$note_id = value_proc($_POST['note_id']);
		
		 
		// Данные заметки
		$sql = "SELECT * FROM ".NOTES_TB." WHERE note_id='$note_id'";
		
		$note_data = $site_db->query_firstrow($sql);
		
		$access_block = fill_note_access_block($note_data);
		
		echo $access_block;
		
	break;
		
}

?>