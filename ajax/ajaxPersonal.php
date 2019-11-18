<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_personal.php';
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'] ? $_POST['mode'] : $_GET['mode'];

$current_user_id = $_SESSION['user_id'];

$tag = iconv('UTF-8', 'windows-1251',$_GET['tag']);

if(!$current_user_id)
{
	exit();
}

switch($mode)
{
	// Форма изменения изображения пользователя
	case 'load_upload_user_image_form':
		
		$user_image_form = get_user_image_upload_form($current_user_id);
		
		echo $user_image_form;
	break;
	
	// Сохранить изображение пользователя
	case 'save_user_image':
		
		$image_name = value_proc($_POST['image_name']);
		
		$image_preview_coordinats = ($_POST['image_preview_coordinats']);
		
		// Координаты превью изображения
		$x = $image_preview_coordinats['x'];
		
		$y = $image_preview_coordinats['y'];
		
		$x2 = $image_preview_coordinats['x2'];
		
		$y2 = $image_preview_coordinats['y2'];
		
		$w = $image_preview_coordinats['w'];
		
		$h = $image_preview_coordinats['h'];
		 
		$crop = array($x, $y, $w, $h);
		
		// Путь папки пользователя
		//$user_upload_path = USERS_PATH.'/'.$current_user_id;
		
		
		$image_temp_file = TEMP_PATH.'/'.$image_name;
		
		
		$date_add = date('Y-m-d H:i:s');
		
		$file_system_name = get_rand_file_system_name($image_name);
		
		$file_dir = create_upload_folder($date_add, 1);
		//$file_upload_path = $file_dir.'/'.$file_system_name;
		
		
		/*if(!is_dir($user_upload_path))
		{
			mkdir($user_upload_path);
		}*/
		
		// Файл аватара
		$file_out_avatar = $file_dir.'/avatar_'.$file_system_name;
		// Файл аватара
		$file_out_original = $file_dir.'/'.$file_system_name;
		// Файл превью аватара
		$file_out_preview = $file_dir.'/preview_avatar_'.$file_system_name;
		
		
		
		// Размеры изображения
		list($width,$height)=getimagesize($_FILES['uploadfile']['tmp_name']);
		
		// Выбираем добавленное изображение
		$sql = "SELECT image_id, image_name, date_add FROM ".USER_IMAGES_TB." WHERE user_id='$current_user_id' ORDER by image_id DESC LIMIT 1 ";
		
		$row = $site_db->query_firstrow($sql);
		
		$user_image_id = $row['image_id'];
		
		$user_image_name = $row['image_name'];
		
		$user_image_date_add = $row['date_add'];
		
		
		if(!$row['image_id'] && !$image_name)
		{
			exit();
		}
		
		if(!$image_name)
		{
			// Файл аватара
			//$file_original = $user_upload_path.'/'.$user_image_name;
			
			$file_original = get_download_dir('', $user_image_date_add, 1).'/'.$user_image_name;
			$file_preview =  get_download_dir('', $user_image_date_add, 1).'/preview_avatar_'.$user_image_name;
			
			// Удаляем координаты
			$sql = "DELETE FROM ".USER_IMAGES_COORDS_TB." WHERE image_id='".$user_image_id."'";
			
			$site_db->query($sql);
			
			// Делаем отметку о присвоении фотографии пользователю
			$sql = "INSERT INTO ".USER_IMAGES_COORDS_TB." SET image_id='".$user_image_id."', coord_x='$x', coord_y='$y', coord_x2='$x2', coord_y2='$y2'";
		 
			$site_db->query($sql);
			
			# Создаем превью
			// Вырезаем превью из большой фотографии
			crop_preview_photo($file_original, $file_preview, $crop, false); 
			
			// Уменьшаем вырезанную копию
			img_resize($file_preview, $file_preview, 100, 100);
		}
		else
		{
			// Если у пользователя загружена фотография, удаляем прежние
			//if($user_image_name)
		//	{
				// Если не существует папка с историей аватарок - создаем
				/*if(!is_dir($user_upload_path.'/hs/'))
				{
					mkdir($user_upload_path.'/hs/');
					chmod($user_upload_path.'/hs',0777); 
				}*/
				// Копируем старую аватарку в историю
				//copy($user_upload_path.'/avatar.jpg', $user_upload_path.'/hs/'.date('His_dmy').'_avatar.jpg');
				//unlink($user_upload_path.'/avatar.jpg');
				//unlink($user_upload_path.'/preview_avatar.jpg');
				//unlink($user_upload_path.'/'.$user_image_name);
			//}
			// Копируем оригинал
			copy($image_temp_file, $file_out_original);
			
			// Копируем для аватарки на странице
			img_resize($image_temp_file, $file_out_avatar, 200, NULL);
			
			# Создаем превью
			// Вырезаем превью из большой фотографии
			crop_preview_photo($image_temp_file, $file_out_preview, $crop, false); 
			
			// Уменьшаем вырезанную копию
			img_resize($file_out_preview, $file_out_preview, 100, 100);
			
			// Удаляем старую фотогарфию, если есть
			$sql = "DELETE FROM ".USER_IMAGES_TB." WHERE user_id='$current_user_id'";
			
			$site_db->query($sql);
			
			// Делаем отметку о присвоении фотографии пользователю
			$sql = "INSERT INTO ".USER_IMAGES_TB." SET image_name='$file_system_name', user_id='$current_user_id', date_add='$date_add'";
		 
			$site_db->query($sql);
			
			$inserted_image_id = $site_db->get_insert_id();
			
			// Делаем отметку о присвоении фотографии пользователю
			$sql = "INSERT INTO ".USER_IMAGES_COORDS_TB." SET image_id='$inserted_image_id', coord_x='$x', coord_y='$y', coord_x2='$x2', coord_y2='$y2'";
		 
			$site_db->query($sql);
			
		}
		echo 1;
		
	break;
	
	// Удаление изображения
	case 'delete_user_image':
		
		$user_id = value_proc($_POST['user_id']);
		
		// Выбираем добавленное изображение
		$sql = "SELECT image_id, image_name FROM ".USER_IMAGES_TB." WHERE user_id='$user_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		// Удаляем изображение
		$sql = "DELETE FROM ".USER_IMAGES_TB." WHERE user_id='$user_id'";
		
		$site_db->query($sql);
		
		// Удаляем координаты
		$sql = "DELETE FROM ".USER_IMAGES_COORDS_TB." WHERE image_id='".$row['image_id']."'";
		
		$site_db->query($sql);
		
		//unlink(USERS_PATH.'/'.$user_id.'/avatar.jpg');
		//unlink(USERS_PATH.'/'.$user_id.'/preview_avatar.jpg');
		//unlink(USERS_PATH.'/'.$user_id.'/'.$row['image_name']);
		
		// Путь папки пользователя
		//$user_upload_path = USERS_PATH.'/'.$current_user_id;
		
		//$image_temp_file = TEMP_PATH.'/'.$image_name;
		echo 1;
	break;
}

 
 
?>