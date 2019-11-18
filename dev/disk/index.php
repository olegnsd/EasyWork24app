<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.Upload.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.File.php');
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_disk.php'; 
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_files.php'; 

//ini_set('display_errors', 1);
 

if($_GET['c'])
{
	//clear();
	echo 'cl';
	exit();
}
	
create_folders();	
import();
 

function import()
{
	global $site_db;
	
	$upl = new Upload($site_db);
	
	$sql = "SELECT * FROM tasks_users_files WHERE 1 ORDER by file_id  ";
	
	$res = $site_db->query($sql);
			 
	while($row=$site_db->fetch_array($res))
	{  
		$file_id_old = $row['file_id']; 
		$file_path = get_path_to_file($row);
		$is_company = $row['is_sharing'];
		$folder_id = $row['folder_id'];
		$file_name = $row['file_name'];
		$user_id = $row['user_id'];
		$file_desc = $row['file_desc'];
		$filesize_byte = filesize($file_path);
		
		if($row['date']=='0000-00-00 00:00:00')
		{  
			$date_add = date('Y-m-d H:i:s');
		}
		else
		{
			$date_add = $row['date'];
		}
		
		 
		 
		$extension = substr($file_name,strrpos($file_name, '.')+1,20);
		
		
		$new_folder_id = 0;
		if($folder_id)
		{
			$sql = "SELECT * FROM tasks_files_folders WHERE old='".$folder_id."'";
			$row1 = $site_db->query_firstrow($sql);
			$new_folder_id = $row1['folder_id'];
		}
		
		 
		
		 
		
		// Добавляем файл
		$sql = "INSERT INTO tasks_files SET folder_id='$new_folder_id', user_id='".$user_id."', date_add='$date_add', date_edit='$date_add',file_name='$file_name', file_desc='$file_desc', size='$filesize_byte', extension='$extension', is_company='$is_company', is_content_file='0', old='".$file_id_old."'";
		 
		$site_db->query($sql);
		
		$file_id_new = $site_db->get_insert_id();
		
		// Версия
		$file_system_name = $file_id_new.'_'.date('ymdHis', to_mktime($date_add)).'.'.$extension;
		
		$sql = "INSERT INTO tasks_files_versions SET file_id='$file_id_new', file_name='$file_name', file_system_name='$file_system_name', date_add='$date_add', user_id='".$user_id."', extension='$extension', size='$filesize_byte'";
		
		$site_db->query($sql);
		
		$version_id = $site_db->get_insert_id();
		
		
		// Добавляем версию
		$sql = "UPDATE tasks_files SET version_id='$version_id' WHERE file_id='$file_id_new'";
			
		$site_db->query($sql);
		
		priv_file($file_id_old, $file_id_new);
			
		if(file_exists($file_path))
		{
			$file_dir = $upl->get_file_dir($file_id_new);
			
			$file_upload_path = $file_dir.'/'.$file_system_name;
			
			if(!is_dir($file_dir))
			{
				mkdir($file_dir);
				chmod($file_dir, 0776);
			}
			
			copy($file_path, $file_upload_path);
		}
				 
		//echo  $file_system_name, '<br>';
	}
}

function priv_file($file_id_old, $file_id_new)
{
	global $site_db;
	
	$sql = "SELECT * FROM tasks_users_files_access WHERE file_id='$file_id_old' AND folder_id=0";
	
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		// Добавляем запись на права к файлу
		$sql = "INSERT INTO tasks_files_access SET user_id='".$row['user_id']."', by_user_id='".$row['access_by_user_id']."', file_id='$file_id_new', access=1, noticed=1";
		
		$site_db->query($sql);
		
		$sql = "UPDATE tasks_files SET is_sharing=1 WHERE file_id='$file_id_new'";
		$site_db->query($sql);
	}
}

function priv_folder($folder_id_old, $folder_id_new)
{
	global $site_db;
	
	$sql = "SELECT * FROM tasks_users_files_access WHERE file_id='0' AND folder_id='$folder_id_old'";
	
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		// Добавляем запись на права к файлу
		$sql = "INSERT INTO tasks_files_folders_access SET user_id='".$row['user_id']."', by_user_id='".$row['access_by_user_id']."', folder_id='$folder_id_new', access=1, noticed=1";
		
		$site_db->query($sql);
		
		$sql = "UPDATE tasks_files_folders SET is_sharing=1 WHERE folder_id='$folder_id_new'";
		$site_db->query($sql);
	}
}

function create_folders()
{
	global $site_db;
	
	$sql = "SELECT * FROM tasks_users_folders";
	
	$res = $site_db->query($sql);
		 
	while($row=$site_db->fetch_array($res))
	{
		// Создаем папку
		$sql = "INSERT INTO tasks_files_folders SET user_id='".$row['user_id']."', date_add=NOW(), folder_name='".$row['folder_name']."', parent_folder_id='0', is_company='".$row['is_sharing']."', old='".$row['folder_id']."'";
		
		$site_db->query($sql);
		
		$folder_id = $site_db->get_insert_id();
		
		priv_folder($row['folder_id'], $folder_id);
	}
}

function clear()
{
	global $site_db;
	
	$sql = "TRUNCATE TABLE tasks_files";
	$site_db->query($sql);
	$sql = "TRUNCATE TABLE tasks_files_access"; 
	$site_db->query($sql);
	$sql = "TRUNCATE TABLE tasks_files_versions"; 
	$site_db->query($sql);
	$sql = "TRUNCATE TABLE tasks_files_folders";
	$site_db->query($sql);
	$sql = "TRUNCATE TABLE tasks_files_folders_access";
	$site_db->query($sql);
}
?>
