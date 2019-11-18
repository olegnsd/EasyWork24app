<?php
header ( "Cache-control: no-cache" );
include_once $_SERVER['DOCUMENT_ROOT'].'/client/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/client/includes/functions_files.php';

// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $_SESSION['user_id'];

// id папки
$folder_id = $_GET['folder_id'];
$client_id = $_GET['client_id'];

$from_user_id = $_GET['from_user_id'];
$from_client_id = $_GET['from_client_id'];

if(!$from_user_id && !$from_client_id)
{
	exit();
}
 
// Расширение файла
$file_type = substr($_FILES['uploadfile']['name'],strrpos($_FILES['uploadfile']['name'], '.'),10);



$folder_id = $folder_id ? $folder_id : 0;


// Если папки для пользователя не существует, создаем
if(!is_dir(CLIENTS_PATH.'/'.$client_id))
{
	mkdir(CLIENTS_PATH.'/'.$client_id);
	mkdir(CLIENTS_PATH.'/'.$client_id.'/in');
	mkdir(CLIENTS_PATH.'/'.$client_id.'/out');
}

if($from_client_id)
{
	$folder_path = CLIENTS_PATH.'/'.$client_id.'/out';
}
else if($from_user_id)
{
	$folder_path = CLIENTS_PATH.'/'.$client_id.'/in';
}


// Если файл принадлежит папке
if($folder_id)
{
	$folder_path .= '/'.$folder_id;	
}

$uploaddir = $folder_path; 
 
// Название файла
$file_name =  strip_tags(htmlspecialchars($_FILES['uploadfile']['name'])); 

// Размер файла
$filesize = round(filesize($_FILES['uploadfile']['tmp_name']) / 1000);

if($filesize > (int) ini_get('upload_max_filesize') * 1000)
{
	echo '3';
	exit();
}

if($from_client_id)
{
	// Проверка имени
	$sql = "SELECT file_id FROM ".CLIENTS_FILES_TB." WHERE file_name='$file_name' AND folder_id='$folder_id' AND client_id='$client_id' AND from_client_id='$client_id'";
}
else
{
	// Проверка имени
	$sql = "SELECT file_id FROM ".CLIENTS_FILES_TB." WHERE file_name='$file_name' AND folder_id='$folder_id' AND client_id='$client_id' AND from_user_id='$current_user_id'";
	
}
$row = $site_db->query_firstrow($sql);

if($row['file_id'])
{
	echo '0';
	exit();
	
}
// Конечная директория файла
$file = $uploaddir.'/'.$file_name;   
 
// Запрещенные варианты расширений файла

$blacklist = array(".php", ".phtml", ".php3", ".php4");

foreach ($blacklist as $item)
{
	if(preg_match("/$item\$/i", $_FILES['uploadfile']['name'])) {
		echo '1';
		exit;
	}
}

// Успешное копирование
if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file))
{
	$file_name = addslashes($file_name);
	
	if($from_client_id)
	{
		$sql = "INSERT INTO ".CLIENTS_FILES_TB." SET client_id='$client_id', file_name='$file_name', folder_id='$folder_id', date=NOW(),
				from_user_id=0, from_client_id='$client_id'";
		
		$row = $site_db->query_firstrow($sql);
	
		if(!mysql_error())
		{
			echo 2;
		}
	}
	else if($from_user_id)
	{
		$sql = "INSERT INTO ".CLIENTS_FILES_TB." SET client_id='$client_id', file_name='$file_name', folder_id='$folder_id', date=NOW(),
				from_user_id='$current_user_id', from_client_id='0'";
				
		$row = $site_db->query_firstrow($sql);
	
		if(!mysql_error())
		{
			echo 2;
		}
	}
}
 

 
  
?>
