<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
// Класс авторизации

if(!$current_user_id)
{
	exit();
}

$mode = value_proc($_POST['mode']);

$is_colleague = value_proc($_GET['colleague']);

$current_user_id = $_SESSION['user_id'];

$tag = iconv('UTF-8', 'windows-1251',$_GET['tag']);

$who = value_proc($_GET['who']);

$by = value_proc($_GET['by']);

$result_name = value_proc($_GET['result_name']);

$current_user = value_proc($_GET['current_user']);


// сотрудники
if($who=='workers')
{
	$users_arr = get_current_user_users_arrs(array(0,1,0,0,1,1));
}
else if($who=='workers_main')
{
	$users_arr = get_current_user_users_arrs(array(0,1,0,0,1));
}
// полное дерево
else if($who=='all_tree')
{
	$users_arr = get_current_user_users_arrs(array(1,1,1,1,1,1));
}


if($who=='workers' || $who=='all_tree' || $who=='workers_main')
{
	if($users_arr)
	{
		$and_users = "AND user_id IN(".implode(',', $users_arr).")";
	}
	else
	{
		$and_users = "AND user_id IN(".implode(',', array(0)).")";
	}
}


if($by!='name')
{
	if(strlen($tag)>4)
	{
		$and_phone = " OR user_phone LIKE '%$tag%'";
	}
}

// поиск по фамилии и имени
$user_name = $tag ? " AND (user_surname LIKE '$tag%' OR user_name LIKE '$tag%' $and_phone)" : "";

// не выводить текущего пользователя
$and_current_user = !$current_user ? " AND user_id<>'$current_user_id'" : "";

// Выбираем найденных пользователей
$sql = "SELECT * FROM ".USERS_TB." WHERE is_fired=0 $user_name $and_users $and_current_user 
		ORDER by user_surname LIMIT 20"; 

  
$res = $site_db->query($sql);
    
while($row=$site_db->fetch_array($res)) {
	
	  $user_obj->fill_user_data($row['user_id']);
	  	
	  $tmp = array();
	  
	  if($result_name==1)
	  {
		  $name = $row['user_surname'].' '.$row['user_name'][0].'. '.$row['user_middlename'][0].'.';
	  }
	  else if($result_name==2)
	  {
		  $name = $row['user_surname'].' '.$row['user_name'][0].'. '.$row['user_middlename'][0].'., '.$user_obj->get_user_position();
	  }
	  else
	  {
		  $name = $row['user_surname'].' '.$row['user_name'].' '.$row['user_middlename'].', '.$user_obj->get_user_position();
	  }
	  
	  $tmp['value'] = iconv('windows-1251', 'UTF-8', $name);
	  
	  $tmp['key'] = $row['user_id'];
	  
	  $result[] = $tmp;
}

echo json_encode($result);
 
?>