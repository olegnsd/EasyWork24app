<?php
// Опознание сервера
include_once $_SERVER['DOCUMENT_ROOT'].'/bills_config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_mysql.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions.php';
 
$site_db = new Db($db_bills_host, $db_bills_user, $db_bills_password, $db_bills_name);
$site_db->query('SET NAMES cp1251');
  
$state = split('-', $_GET['state']) ;

$client_id = value_proc($state[3]);

// Поиск клиента по его хосту
$sql = "SELECT * FROM easy_bills.bills_clients_hosts WHERE client_id='$client_id'";
	 
$client_host_data = $site_db->query_firstrow($sql);

$client_host_data['host'];
 
if($client_host_data['host'])
{
	$q_str = $_SERVER['QUERY_STRING'];
	 
	header('Location: http://'.$client_host_data['host'].'/disk/gdrive/auth?'.$q_str);
}
else
{
	die('Error 404');
}

//echo $client_id;

//header('Location: http://tt.erp2crm.ru/disk/gdrive/auth?'.$str);
?>