<?php
header ( "Cache-control: no-cache" );
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_files.php';

// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $auth->get_current_user_id();

if(!$current_user_id)
{
	exit();
}

// id папки
$folder_id = $_GET['folder_id'];

// Страница общих файлов?
$is_sharing = $_GET['is_sharing'];

// Расширение файла
$file_type = substr($_FILES['uploadfile']['name'],strrpos($_FILES['uploadfile']['name'], '.'),10);

//echo $file_type;

$folder_id = $folder_id ? $folder_id : 0;
$is_sharing = $is_sharing ? $is_sharing : 0;

// Если папки для пользователя не существует, создаем
if(!is_dir(PRIVATE_PATH.'/'.$current_user_id))
{
	mkdir(PRIVATE_PATH.'/'.$current_user_id);
}
			
if($is_sharing)
{
	$folder_path = SHARING_PATH;
}
else
{
	$folder_path = PRIVATE_PATH.'/'.$current_user_id;
}

// Если файл принадлежит папке
if($folder_id)
{
	$folder_path .= '/'.$folder_id;	
}

$uploaddir = $folder_path; 
 
// Название файла
$file_name =  strip_tags(htmlspecialchars($_FILES['uploadfile']['name'])); 

if($file_name=='image.jpg')
{
	$file_name = 'image_'.date('His_dmy').'.jpg';
}

// Размер файла
$filesize = round(filesize($_FILES['uploadfile']['tmp_name']) / 1000);
$filesize_byte = filesize($_FILES['uploadfile']['tmp_name']);

if($filesize > (int) ini_get('upload_max_filesize') * 1000)
{
	echo '3';
	exit();
}
if(!$is_sharing)
{
	$and_user_id = " AND user_id='".$current_user_id."'";
}
// Файлы в общем доступе и не в папке
// Проверка имени
$sql = "SELECT file_id FROM ".FILES_TB." WHERE file_name='$file_name' AND folder_id='$folder_id' AND is_sharing='$is_sharing' $and_user_id";
 
$row = $site_db->query_firstrow($sql);
// файл с таким именем уже существует
if($row['file_id'])
{
	echo '0';
	exit();
	
}
// Конечная директория файла
$file = $uploaddir.'/'.$file_name;   

// Запрещенные варианты расширений файла

$blacklist = array(".php", ".phtml", ".php3", ".php4", ".exe", ".bat");

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
	
	$sql = "INSERT INTO ".FILES_TB." SET file_name='$file_name', user_id='$current_user_id', folder_id='$folder_id', is_sharing='$is_sharing', date=NOW(), filesize = '$filesize_byte'";
	 
	$row = $site_db->query_firstrow($sql);
	
	echo "2";
	
}
 

 
  
?>
