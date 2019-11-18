<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_POST['mode'];

$current_user_id = $_SESSION['user_id'];

$tag = iconv('UTF-8', 'windows-1251',$_GET['tag']);

$result = array();

// Список подчиненных
$workers_arr = get_current_user_users_arrs(array(0,1,0,0,1));

// в поиск добавляем подчиненных
if($workers_arr)
{
	$workers_ids = implode(',', $workers_arr);
	
	$sql = "SELECT * FROM ".USERS_TB." WHERE user_surname LIKE '$tag%' AND user_id IN ($workers_ids) LIMIT 10";
	
	$res = $site_db->query($sql);
    
	while($row=$site_db->fetch_array($res)) 
	{
		$tmp = array();
		 
		$tmp['value'] = iconv('windows-1251', 'UTF-8', $row['user_surname'].' '. $row['user_name'].' '.$row['user_middlename']);
		
		$tmp['key'] = iconv('windows-1251', 'UTF-8', $row['user_surname'].' '. $row['user_name'].' '.$row['user_middlename']);
	  
	 	$result[] = $tmp;
	}
}


// Выбираем найденных пользователей
$sql = "SELECT * FROM ".CLIENTS_TB." WHERE client_name LIKE '%$tag%' AND client_deleted<>1 LIMIT 10";
  
$res = $site_db->query($sql);
    
while($row=$site_db->fetch_array($res)) {
	
	  $tmp = array();
	  
	  $tmp['value'] = iconv('windows-1251', 'UTF-8', $row['client_name']);
	  
	  $tmp['key'] = '-s-'.$row['client_id'];
	  
	  $result[] = $tmp;
}


echo json_encode($result);
 
?>