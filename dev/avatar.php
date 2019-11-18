<?php

mkdir();

chmod();

include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

$to_path = $_SERVER['DOCUMENT_ROOT'].'/upload/2/d';

 
$sql = "SELECT * FROM tasks_files_versions WHERE version_id > 1200";

$res = $site_db->query($sql);
			
while($row=$site_db->fetch_array($res))
{
	$file_from = UPLOAD_PATH.'/uploads/'.$row['file_id'].'/'.$row['file_system_name'];
	
	$file_to = create_upload_folder($row['date_add']).'/'.$row['file_system_name'];
	
	copy($file_from, $file_to);
	
	
	//echo $file_from;
	//$path = $to_path
	
	//echo 2;
}


echo 23;
/*
$path = $_SERVER['DOCUMENT_ROOT'].'/upload/2/goods';
$to_path = $_SERVER['DOCUMENT_ROOT'].'/upload/2/static/2015/02';


$folders = scandir($path); 

foreach($folders as $user_id)
{
	$folder_path = $path.'/'.$user_id;
	
	if($user_id=='.' || $user_id=='..')
	{
		continue;
	}
	
	if(is_dir($folder_path))
	{
		 
		 $files = scandir($folder_path);
		 
		 $files = array_slice($files, 2);
		 
		 // 
		 $sql = "SELECT * FROM tasks_goods_images WHERE good_id='$user_id'";
			 
		 $image_data = $site_db->query_firstrow($sql);
		 
		 if($image_data['image_id'])
		 {
			 $image_name = '';
			 
			  
			  
			 foreach($files as $file_name)
			 {
				 $file_name = trim($file_name);
				 
				 $system_name = get_rand_file_system_name($file_name);
				 
				// if($file_name==$image_data['contact_image'])
				// {
					// $image_name = $system_name;
				// }
				// else continue;
				 
				 if($file_name)
				 {
					 
				 	copy($folder_path.'/'.$file_name, $to_path.'/'.$system_name);
					
					//if($file_name==$image_data['contact_image'])
					//{
						$sql = "UPDATE  tasks_goods_images SET image_name='$system_name', date_add=NOW() WHERE image_name='$file_name'";
						//if($user_id==29)
						//echo $sql,' ',$image_name,' . <br>';
						
						  $site_db->query($sql);
					//}
					 
				 }
			 }
		 }
		 
		 
		 
	}
}*/


/*
$path = $_SERVER['DOCUMENT_ROOT'].'/upload/2/users';
$to_path = $_SERVER['DOCUMENT_ROOT'].'/upload/1/static/2015/02';


$folders = scandir($path); 

foreach($folders as $user_id)
{
	$folder_path = $path.'/'.$user_id;
	
	if($user_id=='.' || $user_id=='..')
	{
		continue;
	}
	
	if(is_dir($folder_path))
	{
		 
		 $files = scandir($folder_path);
		 
		 $files = array_slice($files, 2);
		 
		 // 
		 $sql = "SELECT * FROM tasks_user_images WHERE user_id='$user_id'";
			 
		 $image_data = $site_db->query_firstrow($sql);
		 
		 if($image_data['user_id'])
		 {
			 $image_name = '';
			 
			 $system_name = get_rand_file_system_name($image_data['image_name']);
			  
			 foreach($files as $file_name)
			 {
				 
				 
				 // echo $file_name,' <br>';
				 if($file_name=='avatar.jpg')
				 {
					 $image_name = 'avatar_'. $system_name;
				 }
				 else if($file_name=='preview_avatar.jpg')
				 {
					 $image_name = 'preview_avatar_'. $system_name;
				 }
				 else if($file_name==$image_data['image_name'])
				 {
					 $image_name = $system_name;
				 }
				 else continue;
				 
				 if($image_name)
				 {
					 
				 	copy($folder_path.'/'.$file_name, $to_path.'/'.$image_name);
					
					if($file_name==$image_data['image_name'])
					{
						$sql = "UPDATE tasks_user_images SET image_name='$system_name', date_add=NOW() WHERE user_id='$user_id'";
						//if($user_id==29)
						//echo $sql,' ',$image_name,' . <br>';
						
						  $site_db->query($sql);
					}
					 
				 }
			 }
		 }
		 
		 
		 
	}
}*/

//echo "<pre>", print_r($files);

?>