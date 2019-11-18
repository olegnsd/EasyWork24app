<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

if(!$current_user_id)
{
	exit();
}

$tag = iconv('UTF-8', 'windows-1251',$_GET['tag']);

// Выбираем найденных пользователей
$sql = "SELECT * FROM ".CLIENTS_TB." WHERE (client_name LIKE '%$tag%' OR client_id='$tag') AND client_deleted=0 LIMIT 10";
  
$res = $site_db->query($sql);
    
while($row=$site_db->fetch_array($res)) {
	  $tmp = array();
	  
	  $tmp['value'] = iconv('windows-1251', 'UTF-8', '№'.$row['client_id'].' '.$row['client_name']);
	  
	  $tmp['key'] = '-s-'.$row['client_id'];
	  
	  $result[] = $tmp;
}

echo json_encode($result);
 
?>