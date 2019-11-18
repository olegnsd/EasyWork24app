<?php
// Класс для работы с файлами
include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.File.php');
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_disk.php'; 

function fill_content_file_form($id, $content_type)
{
	$add_work_form_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_in_contents/form.tpl');
	
	$timestamp = time();
	
	$token = create_token($timestamp);
	
	$PARS['{UPLOAD_SIZE_LIMIT}'] = UPLOAD_SIZE_LIMIT;
	 
	$PARS['{TOKEN}'] = $token;
	
	$PARS['{TIMESTAMP}'] = $timestamp;
	
	$PARS['{CONTENT_TYPE}'] = $content_type;
	
	$PARS['{ID}'] = $id;
	
	return fetch_tpl($PARS, $add_work_form_tpl);
}

// Удаляет привязанные к контенту файлы
function delete_attached_files_to_content($content_id, $content_type, $files)
{
	global $site_db, $current_user_id;
	
	$fl = new File($site_db);
	
	foreach($files as $file_id)
	{
		$file_data = $fl->get_file_data($file_id);
	
		// Файлы не являются файлами для контента или файл не найден
		if(!$file_data['file_id'] || !$file_data['is_content_file'])
		{
			continue;
		}
		
		if(!check_access_to_content_files($file_data))
		{
			continue;
		}
		
		
		// Привязываем файл к контенту
		$sql = "DELETE FROM tasks_files_in_contents WHERE content_type='$content_type' AND content_id='$content_id' AND file_id='$file_id'";
		
		$site_db->query($sql);
	}
}
// Привязать файлы к контенту
function attach_files_to_content($content_id, $content_type, $files, $user_id, $copy_attach)
{
	global $site_db, $current_user_id;
	
	$content_types = array('1' => 'work_report', '2' => 'finance_operation', '3' => 'msgs', '4' => 'group_msgs', '5' => 'deals', '6' => 'tasks', '7' => 'tasks_reports', '8' => 'projects');
	
	$fl = new File($site_db);
	
	if(!array_key_exists($content_type, $content_types))
	{  
		return;
	}
	
	foreach($files as $file_id)
	{
		$file_data = $fl->get_file_data($file_id);
	
		// Файлы не являются файлами для контента или файл не найден
		if(!$file_data['file_id'] || !$file_data['is_content_file'])
		{
			continue;
		}
		
		// Проверяем, не был ли добавлен файл
		$sql = "SELECT * FROM tasks_files_in_contents WHERE file_id='$file_id'";
		
		$check_row = $site_db->query_firstrow($sql);
		
		
		// Если файл уже привязан к контенту, не привязываем его снова
		if($check_row['id'] && !$copy_attach)
		{
			continue;
		}
		
		// Привязываем файл к контенту
		$sql = "INSERT INTO tasks_files_in_contents SET content_type='$content_type', content_id='$content_id', file_id='$file_id', date_add=NOW(), user_id='$user_id'";
		
		$site_db->query($sql);
	}
	
}

// Возвращает список прикрепленных файлов к контенту
function get_attached_files_to_content($content_id, $content_type, $mode=1)
{
	global $site_db, $current_user_id;
	
	$files_attached_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_in_contents/files_attached.tpl');
	
	$fl = new File($site_db);
	
	// Получаем список файлов, прикрепленных к контенту
	$sql = "SELECT * FROM tasks_files_in_contents i 
			LEFT JOIN tasks_files j ON i.file_id=j.file_id
			WHERE i.content_id='$content_id' AND i.content_type='$content_type'";
			
	$res = $site_db->query($sql);
	
	while($row=$site_db->fetch_array($res))
	{
		// $files_list .= fill_attechec_file_item($row);
		 
		  $files_list .= fill_file_list_item($row, '', $mode);
	}
	
	// Если есть список прикрепленных файлов
	if($files_list)
	{
		$PARS['{FILES_LIST}'] = $files_list;
	
		return fetch_tpl($PARS, $files_attached_tpl);
	}
	 
}

function fill_attechec_file_item($file_data)
{
	$file_attached_item_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/disk/file_in_contents/file_attached_item.tpl');
	
	$PARS['{FILE_ID}'] = $file_data['file_id'];
	
	$PARS['{FILE_NAME}'] = $file_data['file_name'];
	
	return fetch_tpl($PARS, $file_attached_item_tpl);
	
}

// Проверка на доступность файлов 
function check_access_to_content_files($file_data, $content_id)
{
	global $site_db, $current_user_id;
	
	if($content_id)
	{
		$and_cont = " AND content_id='$content_id'";
	}
	
	// Данные по прикрепленному Файлу к контенту
	$sql = "SELECT * FROM tasks_files_in_contents WHERE file_id='".$file_data['file_id']."' $and_cont";
	 
	$c_row = $site_db->query_firstrow($sql);
	
	$content_type = $c_row['content_type'];
	
	$content_id = $c_row['content_id'];
	
	$add_by_user_id = $c_row['user_id'];
	
	if(!$content_id)
	{ 
		return false;
	}
	
	
	switch($content_type)
	{
		// Файлы отчетов круга обязанностей
		case 1:
		
			// Данные по отчету о круге обязанностей
			$sql = "SELECT j.*, i.* FROM tasks_user_works_reports i
					LEFT JOIN tasks_user_work j ON i.work_id=j.work_id
					WHERE i.report_id='$content_id'";
					
			$row = $site_db->query_firstrow($sql);
			
			if($row['work_id'])
			{
				if(check_user_access_to_user_content($row['work_to_user_id'], array(0,1,0,0,1)))
				{
					return true;
				}
				else if($row['report_from_user_id']==$current_user_id)
				{
					return true;
				}
			}
			
			
		break;
		
		case 2:
		
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_finances.php';
			
			$sql = "SELECT * FROM tasks_finances_operations i 
					LEFT JOIN tasks_users_finances j ON i.finance_id=j.finance_id
					WHERE i.operation_id='$content_id'";
					
			$row = $site_db->query_firstrow($sql);
			
			if($row['operation_id'])
			{
				// Если у пользователя есть право на редактирование счета
				if(check_user_for_access_to_operation($row, $row['finance_id'], $current_user_id))
				{
					return true;
				}
				
			}
		
		break;
		
		case 3:
			 
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_messages.php';
			
			$dialog_obj = new CDialogs($site_db, $current_user_id);
			
			// id диалога
			$dialog_id = $dialog_obj->get_dialog_id_by_message_id($content_id);
			
			// Проверяем, участвует ли пользователь в диалоге
			if($dialog_obj->check_user_in_dialog($current_user_id, $dialog_id))
			{
				return true;
			}
			
		break;
		
		case 4:
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_messages.php';
			
			// Выявляем id группы
			$sql = "SELECT group_id FROM tasks_messages_groups_messages WHERE message_id='$content_id'";
			 
			$row = $site_db->query_firstrow($sql);
			
			$group_id = $row['group_id'];
			
			// Првоеряем, является ли пользователь участником группы
			$sql = "SELECT * FROM tasks_messages_groups_users WHERE group_id='$group_id' AND user_id='$current_user_id'";
			 
			$row = $site_db->query_firstrow($sql);
			
			if($row['id'])
			{
				return true;
			}
			
		break;
		
		case 5:
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_deals.php';
			
			$sql = "SELECT * FROM tasks_deals WHERE deal_id='$content_id'";
			$deal_data = $site_db->query_firstrow($sql);
			
			//
			if($current_user_id!=$deal_data['user_id'] && $deal_data['deal_private_show'] && !check_user_access_to_user_content($deal_data['user_id'], array(0,1,0,0,1)))
			{
				return false;
			}
			else return true;
			
		break;
		
		case 6:
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tasks1.php';
			
			// исполнитель задачи
			$to_user_id = get_task_user_role_2($content_id); 
			
			// если пользователь относится к задаче или просмтаривает руководитель того, кто является главным исполнителем
			if(check_user_in_task_role($content_id, $current_user_id, array(1,2,3,4)) || check_user_access_to_user_content($to_user_id, array(0,1,0,0,1)))
			{
				return true;
			}
			
		break;
		
		case 7:
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_tasks1.php';
			
			// данные отчета
			$report_data = get_task_report_data($content_id);
			
			// исполнитель задачи
			$from_user_id = get_task_user_role_2($report_data['task_id']); 
			
			// если пользователь относится к задаче или просмтаривает руководитель того, кто является главным исполнителем
			if(check_user_in_task_role($report_data['task_id'], $current_user_id, array(1,2,3,4)) || check_user_access_to_user_content($from_user_id, array(0,1,0,0,1)))
			{
				return true;
			}
			
		break;
		
		case 8:
			
			include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_projects.php';
			
			$sql = "SELECT * FROM tasks_projects WHERE project_id='$content_id'";
			
			$content_data = $site_db->query_firstrow($sql);
			
			if(check_project_access_for_user($current_user_id, $content_id))
			{
				return true;
			}
		break;
	}
	
	//return true;
}
?>