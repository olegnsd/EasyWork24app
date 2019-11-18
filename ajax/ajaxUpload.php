<?php
header ( "Cache-control: no-cache" );
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once($_SERVER['DOCUMENT_ROOT'].'/includes/functions_disk.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.Upload.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.File.php');

if(!$current_user_id)
{
	exit();
}

$mode = $_POST['mode'];

switch($mode)
{
	case 'get_content_file_upload_form':
		
		$id = value_proc($_POST['id']);
		
		$content_type = value_proc($_POST['content_type']);
		
		$file_form = fill_content_file_form($id, $content_type);
		
		echo $file_form;
		
	break;
	case 'open_file_edit_popup':
		
		$file_id = value_proc($_POST['file_id']);
		
		$rand = generate_rand_string(20);
		
		$_SESSION['op_doc_edit'][$file_id] = $rand;
		
		echo $rand;
		
	break;
	case 'save_file_pub':
		
		$file_id = value_proc($_POST['file_id']);
		$time_value = value_proc($_POST['time_value']);
		$time_mode = value_proc($_POST['time_mode']);
		$desc = value_proc($_POST['desc']);
		
		$pub_id = save_file_pub($file_id, $time_value, $time_mode, $desc); 
		
		$pub_file_link = get_file_pub_links($file_id);
		
		echo $pub_file_link;
		
	break;
	
	case 'delete_file_pub_link':
		
		$file_id = value_proc($_POST['file_id']);
		
		$file_pub_id = value_proc($_POST['file_id']);
		
		$fl = new File($site_db);
		
		// Доступ к файлу	
		$_f_access =  get_file_access($current_user_id, $file_id, 0);
		
		// У пользователя есть доступ на совершение операции
		if(f_check_access('read', $_f_access))
		{
			// Удаляем публичную ссылку на файл
			$sql = "DELETE FROM tasks_files_pub WHERE file_id='$file_id'";
			
			$site_db->query($sql);
			
			$fl->file_pub_flag($file_id);
			
			echo 1;
		}
			
	break;
	case 'delete_file':
		
		$elem = value_proc($_POST['elem']);
		
		// Выявляем, что 
		$tmp = split('_', $elem);
		$what = $tmp[0];
		$id = $tmp[1];
		
		$fl = new File($site_db);
		
		if($what=='file')
		{
			// Доступ к файлу	
			$_f_access =  get_file_access($current_user_id, $id, 0);
			
			// У пользователя есть доступ на совершение операции
			if(f_check_access('edit', $_f_access))
			{
				$result = $fl->delete_file($id);
			}
			 
		}
		else if($what=='folder')
		{
			// Доступ к папке	
			$_f_access =  get_file_access($current_user_id, 0, $id);
			
			// У пользователя есть доступ на совершение операции
			if(f_check_access('edit', $_f_access))
			{
				$result = $fl->delete_folder($id);
			}
			 
		}
		 
		echo $result;
		
	break;
	case 'cancel_update_doc_version':
	
		$file_id = value_proc($_POST['file_id']);
		$time = value_proc($_POST['time']);
		
		delete_doc_from_gdrive(0, $file_id, $current_user_id, $time);
		
		echo 1;
		
	break;
	case 'update_doc_version':
		
		$file_id = value_proc($_POST['file_id']);
		$time = value_proc($_POST['time']);
		
		$result = update_doc_version($file_id, $time);
		
		echo $result;
		
	break;
	case 'show_file_popup':
		
		$id = value_proc($_POST['id']);
		
		$cont_id = value_proc($_POST['cont_id']);
		
		echo get_file_popup_content($id, $cont_id);
		
	break;
	case 'recount_new_files':
		
		$by_user_id = value_proc($_POST['by_user_id']); 
		
		$recount_mode = value_proc($_POST['recount_mode']);
		
		// Кол-во новых файлов и папок от ВСЕХ пользователей
		$all_new_available_file_count = new_file_available_count($current_user_id);
		
		// Кол-во новых файлов и папок от  пользователя $by_user_id
		$by_user_count = new_file_available_count($current_user_id, $by_user_id);
		
		echo json_encode(array('all_new'=>$all_new_available_file_count, 'by_user_count'=> $by_user_count));
		
	break;
	case 'file_confirm':
		
		$id = value_proc($_POST['id']);
		
		$what = value_proc($_POST['what']);
		
		if($what=='folder')
		{
			$sql = "UPDATE tasks_files_folders_access SET noticed=1 WHERE folder_id='$id' AND user_id='$current_user_id'";
		}
		else if($what=='file')
		{
			$sql = "UPDATE tasks_files_access SET noticed=1 WHERE file_id='$id' AND user_id='$current_user_id'";
		}
		 
		$site_db->query($sql);
		
		if(!mysql_error())
		{
			echo 1;
		}
		
	break;
	case 'show_more_available_files':
		
		$user_id = value_proc($_POST['user_id']);
		
		$page = value_proc($_POST['page']);
		
		$list = get_files_list_available_by_user_id_arr($user_id, $page);
		
		echo $list['list'];
		
	break;
	case 'get_file_access_block':
	
		$elem = value_proc($_POST['elem']);
		
		// Выявляем, что 
		$tmp = split('_', $elem);
		$what = $tmp[0];
		$id = $tmp[1];
		
		if($what=='file')
		{
			$fl = new File($site_db);
			
			$file_data = $fl->get_file_data($id);
			
			$access_block = fill_file_content_access_file($file_data);
		}
		if($what=='folder')
		{
			$fl = new File($site_db);
			
			$folder_data = $fl->get_folder_data($id);
			
			$access_block = fill_file_content_access_folder($folder_data);
		}
		
		echo  $access_block;
		
	break;
	case 'open_file_pub_block':
	
		$file_id = value_proc($_POST['file_id']);
		
		$fl = new File($site_db);
			
		$file_data = $fl->get_file_data($id);
			
		// блок файла
		$pub_block = open_file_pub_block($file_id);
		
		echo  $pub_block;
		
	break;
	case 'save_access':
		
		$elem = value_proc($_POST['elem']);
		
		$access = (array)json_decode($_POST['access']);
		
		$deleted_access = (array)json_decode($_POST['deleted_access']);
		
		// Выявляем, что 
		$tmp = split('_', $elem);
		$what = $tmp[0];
		$id = $tmp[1];
		
		$fl = new File($site_db);
		
		if($what=='file')
		{
			$file_data = $fl->get_file_data($id);
			
			// Доступ к папке	
			$_f_access =  get_file_access($current_user_id, $id, 0);
		}
		else if($what=='folder')
		{
			$folder_data = $fl->get_folder_data($id);
			
			// Доступ к папке	
			$_f_access =  get_file_access($current_user_id, 0, $id);
		}
		 
		// У пользователя есть доступ на совершение операции
		if(f_check_access('edit', $_f_access) && (!$folder_data['is_company'] && !$file_data['is_company']))
		{
			$fl = new File($site_db);
			
			$result = $fl->save_file_access($id, $what, $access, $deleted_access);
			
			echo 1;
		}
		
		 
		
	break;
	case 'file_version_restore':
		
		$file_id = value_proc($_POST['file_id']);
		$version_id = value_proc($_POST['version_id']);
		
		$fl = new File($site_db);
		
		
		$result = $fl->file_version_restore($file_id, $version_id);
		
		echo $result;
		 
		//echo json_encode(array('success'=>1, 'count'=> get_file_versions_count($file_id)));
	
	break;
	case 'delete_version_file':
		
		$file_id = value_proc($_POST['file_id']);
		$version_id = value_proc($_POST['version_id']);
		
		$fl = new File($site_db);
		
		// Доступ к файлу	
		$_f_access =  get_file_access($current_user_id, $file_id, 0);
		
		// У пользователя есть доступ на совершение операции
		if(f_check_access('edit', $_f_access))
		{
			$result = $fl->delete_version_file($file_id, $version_id);
		}
		
		 
		echo json_encode(array('success'=>$result, 'count'=> get_file_versions_count($file_id)));
		
	break;
	case 'save_file_desc':
		
		$file_id = value_proc($_POST['file_id']);
		$desc = value_proc($_POST['desc']);
		
		$fl = new File($site_db);
		
		// Доступ к файлу	
		$_f_access =  get_file_access($current_user_id, $file_id, 0);
		
		// У пользователя есть доступ на совершение операции
		if(f_check_access('edit', $_f_access))
		{
			$result = $fl->update_file_desc($file_id, $desc);
		}
	 
		
		echo $result;
		
	break;
	case 'update_name':
	
		$elem = value_proc($_POST['elem']);
		$name = value_proc($_POST['name']);
		
		$tmp = split('_', $elem);
		
		$what = $tmp[0];
		
		$id = $tmp[1];
		
		$fl = new File($site_db);
		
		
		if($what=='file')
		{  
			// Доступ к файлу	
			$_f_access =  get_file_access($current_user_id, $id, 0);
			
			// У пользователя есть доступ на совершение операции
			if(f_check_access('edit', $_f_access) && (!$folder_data['is_company'] && !$file_data['is_company']))
			{
				$result = $fl->update_file_name($id, $name);
			}
		}
		if($what=='folder')
		{
			// Доступ к папке	
			$_f_access =  get_file_access($current_user_id, 0, $id);
			
			// У пользователя есть доступ на совершение операции
			if(f_check_access('edit', $_f_access) && (!$folder_data['is_company'] && !$file_data['is_company']))
			{
				$result = $fl->update_folder_name($id, $name);
			}
		}
		
		
		echo $result;
		
	break;
	case 'check_file':
	
		$file_name = value_proc($_POST['file_name']);
		
		$selected_file_name = value_proc($_POST['_selected_file_name']);
		
		$folder_id = value_proc($_POST['folder_id']);
		
		$check_for = value_proc($_POST['check_for']);
		
		$upload_version_file = value_proc($_POST['upload_version_file']);
		 
		$act = $_POST['act'];
		
		// Загрузка файла для компании
		if($act=='co')
		{
			$is_company = 1;
		}
		else if($act=='content' && $check_for=='exists')
		{
			// Для файла, который привязываем к чему то, не делаем проверку на совпадение имен
			echo json_encode(array('check_result' => '')); 
			exit();
		}
		
		 
		if($check_for=='exists')
		{  
			$file_name = !$file_name ?   $selected_file_name : $file_name;
		}
		
		$fl = new File($site_db);
		$upl = new Upload($site_db);
		
		switch($check_for)
		{
			case 'exists':
			
				if($fl->check_file_name_for_exists(0, $folder_id, $file_name, $is_company))
				{
					$check_result =  'file_exists';
				}
				
			break;
			
			default:
				
				if($upl->is_file_ext_in_blacklist($file_name))
				{
					$check_result = 'in_blacklist';
				}
				else if($fl->check_file_name_for_exists(0, $folder_id, $file_name, $is_company) && !$upload_version_file)
				{
					$check_result =  'file_exists';
				}
		
			break;
		}
		
		echo json_encode(array('check_result' => $check_result)); 
		 
		
	break;
	
	case 'create_folder':
		
		$folder_parent_id = value_proc($_POST['folder_id']);
		
		$folder_name = value_proc($_POST['folder_name']);
		
		 
		
		$act = value_proc($_POST['act']);
		
		// Загрузка файла для компании
		if($act=='co')
		{
			$is_company = 1;
		}
		
		
		if($folder_parent_id)
		{
			// Доступ к папке	
			$_f_access =  get_file_access($current_user_id, 0, $folder_parent_id);
		}
		
		// У пользователя есть доступ на совершение операции
		if(f_check_access('edit', $_f_access) || $is_company || !$folder_parent_id)
		{
			$fl = new File($site_db);
		
			$result = $fl->create_folder($folder_name, $folder_parent_id, $is_company);
		}
			
		 
		
		echo $result;
		
	break;
	
	case 'upload_file':
		
		$upload_version_file = value_proc($_POST['upload_version_file']);
		
		$file_name = value_proc($_POST['file_name']);
		 
		$file_desc = value_proc($_POST['file_desc']);
		$folder_id = value_proc($_POST['folder_id']);
		 
		$timestamp = $_POST['timestamp'];
		$token = $_POST['token'];
		
		$act = $_POST['act'];
		
		
		
		// Загрузка файла для компании
		if($act=='co')
		{
			$is_company = 1;
		}
		
		// Загрузить версию файла
		if($upload_version_file)
		{
			// Имя файла в базе будет настоящее имя файла
			$file_name = value_proc($_FILES['upload_file']['name']);
		}
		else
		{
			// Если имя передано вручную(указал в инпуте) то записываем в базу его, иначе  имя самого файла
			$file_name = $file_name ? $file_name : value_proc($_FILES['upload_file']['name']);
		}
		 
	 
		// Создаем проверочный токен
		$created_token = create_token($timestamp);
		
		if($created_token!=$token)
		{
			exit();
		}
		
		// Объект файла
		$fl = new File($site_db);
		// Объект загрузчика
		$upl = new Upload($site_db);
		
		// Если загружаем файл как новую версию файла, принудительно из раздела Свойства
		if(!$upload_version_file)
		{
			// Проверяем, есть ли такой файл
			$file_id = $fl->check_file_name_for_exists(0, $folder_id, $file_name, $is_company);
		}
		
		// Если загружаем файл 
		if($act=='content')
		{ // is_content_file
			// Загружаем новый файл
			$file_id = $upl->upload_file(0, 1);
			
			echo $file_id;
			
		}
		// При загрузке нового файла, если файл уже сущестует, загружаем новую версию файла
		else if($file_id)
		{
			// Доступ к папке	
			$_f_access =  get_file_access($current_user_id, $file_id, 0);
			
			// У пользователя есть доступ на совершение операции
			if(f_check_access('edit', $_f_access))
			{
				// Добавляем версию файла
				$version_id = $upl->add_file_version($file_id, $_FILES['upload_file']);
				if($version_id > 0)
				{
					echo 1;
				}
			}
		}
		//Если загружаем версию файла из раздела Свойства файла
		else if($upload_version_file)
		{
			// Доступ к папке	
			$_f_access =  get_file_access($current_user_id, $upload_version_file, 0);
			
			
			// У пользователя есть доступ на совершение операции
			if(f_check_access('edit', $_f_access))
			{
				// Добавляем версию файла
				$version_id = $upl->add_file_version($upload_version_file, $_FILES['upload_file'], 1);
				if($version_id > 0)
				{
					echo 1;
				}
			}
		}
		// Загружаем новый файл
		else
		{
			
			// Доступ к папке	
			$_f_access =  get_file_access($current_user_id, 0, $folder_id);
		
		
			// У пользователя есть доступ на совершение операции
			if(f_check_access('edit', $_f_access) || $is_company || !$folder_id)
			{
				// Загружаем новый файл
				$file_id = $upl->upload_file($is_company);
				if($file_id > 0)
				{
					echo 1;
				}
			}
		
		
			 
		}
		
		 
 
	break;

	case 'upload_file_api':
        if(is_uploaded_file($_FILES["Filedata"]["tmp_name"])){
//            $myecho = json_encode($_FILES["Filedata"]["tmp_name"]);
//            `echo " tmp_name:    " >>/tmp/qaz`;
//            `echo "$myecho" >>/tmp/qaz`;
            
            $user_id = value_proc($_POST["user_id"]);
            $file_parts = pathinfo($_FILES['Filedata']['name']);
            $tmp = value_proc($_FILES['Filedata']['tmp_name']);
            // Расширение файла
			$extension = $file_parts['extension'];
            $size = value_proc($_FILES["Filedata"]["size"]);
            $error = value_proc($_FILES["Filedata"]["error"]);
            //расширение не wav
            if($extension != 'wav' || !(preg_match("(WAVE)", file_get_contents($tmp)))){
                echo '-1';
                exit;
            }//размер файла больше 10М
            if($size > 10485760){
                echo '-2';
                exit;
            }
            if($error != 0){
                echo '-3';
                exit;
            }

            $file_name = value_proc($_FILES["Filedata"]["name"]);
            $targetFolder = '/temp/audio';
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
            $targetFile = rtrim($targetPath,'/') . '/' . value_proc($_FILES['Filedata']['name']);
            
            // Сохраняем api settings
            $sql = "SELECT user_id, file_name FROM ".API_CALLS_TB;//." WHERE user_id='$user_id'";
			$row = $site_db->query_firstrow($sql);
            if(!isset($row['user_id'])){
                $sql = "INSERT INTO ".API_CALLS_TB." 
					(user_id, file_name) 
                    VALUES 
                    ('$user_id','$file_name')
					";
                $site_db->query($sql);  
            }
            else{
                unlink($targetPath . '/' . $row['file_name']);
                $sql = "UPDATE ".API_CALLS_TB." SET
					file_name = '$file_name'";
                    //WHERE user_id='$user_id'";
                $site_db->query($sql);  
            }
            
            move_uploaded_file($tmp,$targetFile);			 
   
            echo $targetFolder . '/' . $_FILES['Filedata']['name'];
        }
        else{
           $myecho = "error";
            `echo " error:    " >>/tmp/qaz`;
            `echo "$myecho" >>/tmp/qaz`; 
            echo '-3';
        }
    break;
    
    case 'upload_file_handly':
        if(is_uploaded_file($_FILES["Filedata"]["tmp_name"])){
            $user_id = value_proc($_POST["user_id"]);
            $file_parts = pathinfo($_FILES['Filedata']['name']);
            $tmp = value_proc($_FILES['Filedata']['tmp_name']);
            // Расширение файла
			$extension = $file_parts['extension'];
            $size = value_proc($_FILES["Filedata"]["size"]);
            $error = value_proc($_FILES["Filedata"]["error"]);
            //расширение не wav
            if($extension != 'wav' || !(preg_match("(WAVE)", file_get_contents($tmp)))){
                echo '-1';
                exit;
            }//размер файла больше 100М
            if($size > 104857600){
                echo '-2';
                exit;
            }
            if($error != 0){
                echo '-3';
                exit;
            }

            $file_name = value_proc($_FILES["Filedata"]["name"]);
            $targetFolder = '/temp/audio/' . $user_id;
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
            chmod($targetFolder, 0777);
            mkdir($targetPath);
            $targetFile = rtrim($targetPath,'/') . '/' . 'base.wav';
            
            // Сохраняем файл
            unlink($targetPath . '/' . '*.wav');	
            
            move_uploaded_file($tmp,$targetFile);
            
            echo $targetFolder . '/' . $_FILES['Filedata']['name'];
        }
        else{
           $myecho = "error";
            `echo " error:    " >>/tmp/qaz`;
            `echo "$myecho" >>/tmp/qaz`; 
            echo '-3';
        }
    break;
        
    case 'upload_file_csv':
        if(is_uploaded_file($_FILES["Filedata"]["tmp_name"])){
            $user_id = value_proc($_POST["user_id"]);
            $file_parts = pathinfo($_FILES['Filedata']['name']);
            $tmp = value_proc($_FILES['Filedata']['tmp_name']);
            // Расширение файла
			$extension = $file_parts['extension'];
            $size = value_proc($_FILES["Filedata"]["size"]);
            $error = value_proc($_FILES["Filedata"]["error"]);
            //файл не csv
            if($extension != 'csv' || !(preg_match('/(\;{1,1}\+?\d{11,}\;{1,1}){1,}/', file_get_contents($tmp)))){
                echo '-1';
                exit;
            }//размер файла больше 100М
            if($size > 104857600){
                echo '-2';
                exit;
            }
            if($error != 0){
                echo '-3';
                exit;
            }

            $file_name = value_proc($_FILES["Filedata"]["name"]);
            $targetFolder = '/temp/csv/' . $user_id;
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
            chmod($targetFolder, 0777);
            mkdir($targetPath);
            $targetFile = rtrim($targetPath,'/') . '/' . 'base.csv';//value_proc($_FILES['Filedata']['name']);
            
            // Сохраняем file
			unlink($targetPath . '/' . '*.csv');
            
            move_uploaded_file($tmp,$targetFile);
            
            echo $targetFolder . '/' . $_FILES['Filedata']['name'];
        }
        else{
           $myecho = "error";
            `echo " error:    " >>/tmp/qaz`;
            `echo "$myecho" >>/tmp/qaz`; 
            echo '-3';
        }
    break;
}
 
  
?>
