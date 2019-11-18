<?php
header ( "Cache-control: no-cache" );
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_files.php';

// Класс авторизации
$auth = new CAuth($site_db);
 
$current_user_id = $auth->get_current_user_id();

if(!$current_user_id)
{
	exit();
}

$mode = $_GET['mode'];

// Для загрузки изображений для имущества
if(preg_match('/goods/i', $_SERVER['HTTP_REFERER'])  && $mode=='2')
{
	// Максимальное разрешение загружаемого файла
	$max_upload_image_resolution = $max_upload_goods_image_resolution;
	$min_upload_image_resolution = $min_upload_goods_image_resolution;
	
	// Макс. размеры изображений, до которых изображение будет сжиматься для вывода
	$max_upload_preview_image_width = $max_upload_preview_goods_image_width;
	$max_upload_preview_image_height = $max_upload_preview_goods_image_height;
	
}
// Для загрузки изображений для контактов
if(preg_match('/contacts/i', $_SERVER['HTTP_REFERER'])  && $mode=='3')
{
	// Максимальное разрешение загружаемого файла
	$max_upload_image_resolution = $max_upload_goods_image_resolution;
	$min_upload_image_resolution = $min_upload_goods_image_resolution;
	
	// Макс. размеры изображений, до которых изображение будет сжиматься для вывода
	$max_upload_preview_image_width = $max_upload_preview_contact_image_width;
	$max_upload_preview_image_height = $max_upload_preview_contact_image_height;
	
}
// Для загрузки изображений для ПЕРСОНАЛЬНОЙ страницы
else if(preg_match('/id/i', $_SERVER['HTTP_REFERER']) && $mode=='1')
{
	// Максимальное разрешение загружаемого файла
	$max_upload_image_resolution = $max_upload_user_image_resolution;
	$min_upload_image_resolution = $min_upload_user_image_resolution;
	
	// Макс. размеры изображений, до которых изображение будет сжиматься для вывода
	$max_upload_preview_image_width = $max_upload_preview_user_image_width;
	$max_upload_preview_image_height = $max_upload_preview_user_image_height;
}


// Расширение файла
$file_type = strtolower(substr($_FILES['uploadfile']['name'],strrpos($_FILES['uploadfile']['name'], '.')+1,10));

// Название файла
$file_name =  generate_rand_string(7).'.'.$file_type; 
 
// Размер файла
$filesize = round(filesize($_FILES['uploadfile']['tmp_name']) / 1000);

// Размеры изображения
list($width,$height)=getimagesize($_FILES['uploadfile']['tmp_name']);

// Разрешенные форматы
$true_file_type = array('jpeg','jpg','gif','png');

// ПРоверяем разрешение файлов
if(!in_array($file_type, $true_file_type))
{
	echo '0';
	exit();
}

// Запрещенные к загрузке файлы
$blacklist = array(".php", ".phtml", ".php3", ".php4");

foreach ($blacklist as $item)
{
	if(preg_match("/$item\$/i", $_FILES['uploadfile']['name'])) {
		echo '0';
		exit;
	}
}
 
// Если разрешение слишком велико
if($width > $max_upload_image_resolution || $height > $max_upload_image_resolution)
{
	echo '1';
	exit();
}
else if($width < $min_upload_image_resolution || $height < $min_upload_image_resolution)
{
	echo '1';
	exit();
}


// Если размер слишком велик
if($filesize > (int) ini_get('upload_max_filesize') * 1000)
{
	echo '2';
	exit();
}




// Конечная директория файла
$image_file = TEMP_PATH.'/'.$file_name;   

// Запрещенные варианты расширений файла


// Успешное копирование
if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $image_file))
{
	$file_name = addslashes($file_name);
	
	// горизонтальная фотогарфия
	if($width > $height)
	{
		// Если ширина большая, уменьшаем
		if($width > $max_upload_preview_image_width)
		{
			img_resize($image_file, $image_file, $max_upload_preview_image_width, NULL);
		}
	}
	else
	{
		// Если ширина большая, уменьшаем
		if($height > $max_upload_preview_image_height)
		{
			img_resize($image_file, $image_file, NULL, $max_upload_preview_image_height);
		}
			 
	}
	
	echo "ok|".$file_name;
	
}
 
 
  
?>
