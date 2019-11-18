<?
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{$_GET['cron']=1;
$h=date('H');
if($h<10)die();
if($h>=22)die();
if(!$_GET['cron']){//$handle = fopen("log.txt", "a+");
		//fwrite($handle,'Поступил запрос: $cron='.(int)$cron.''.print_r($_SERVER,1)." \r\n");
		//fclose($handle);
die();}
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);//E_ALL);


//include_once $_SERVER['DOCUMENT_ROOT'].'/client_config.php'; //
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_mysql.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_servers.php';
server_detect();


include_once $_SERVER['DOCUMENT_ROOT'].'/config.php'; //
include_once $_SERVER['DOCUMENT_ROOT'].'/config_tables.php'; //
include_once $_SERVER['DOCUMENT_ROOT'].'/global.php'; //


include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_sms.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions.php';


global $site_db;



$data1=$site_db->fetch_array($site_db->query("SELECT * FROM ".TASKS_SMS." WHERE `status`=0 ORDER BY `id` LIMIT 1"));
if($data1['sms']){
$data=unserialize($data1['data']);
$query='';
if($data['group']){$query.=' AND `group_id`="'.(int)$data['group'].'"';}
if($data['status']){$query.=' AND `deal_last_status`="'.(int)$data['status'].'"';}
if($data['date1'])$data['date1']=formate_to_norm_date(value_proc($data['date1']));
if($data['date2'])$data['date2']=formate_to_norm_date(value_proc($data['date2']));
if($data['date1'] && $data['date2'])
	{
		$query.=" AND (deal_date_add >= '".$data['date1']."' AND deal_date_add <= '".$data['date2']." 23:59:59')";
	}
	
	else if($data['date1'] && !$data['date2'])
	{
		$query.= " AND deal_date_add >= '".$data['date1']."'";
	}
	else if(!$data['date1'] && $data['date2'])
	{
		$query.=" AND deal_date_add <= '".$data['date2']." 23:59:59'";
	}
$res=$site_db->query("SELECT `deal_id`,`deal_phone` FROM ".DEALS_TB." WHERE `deal_id`>".(int)$data1['next']." ".$query." AND `deal_deleted` = '0' LIMIT 0,100");
$bd=false;
while($row=$site_db->fetch_array($res))
    {$bd=true;
$phone=$row['deal_phone'];
$phone = preg_replace("/^[8]/", '+7', $phone);
$phone = preg_replace("/[^0-9]/", '', $phone);
$phone=substr($phone,0,11);//echo($phone.'<hr>');
if(strlen($phone)==11){
$test=$site_db->fetch_array($site_db->query("SELECT COUNT(*) FROM ".TASKS_SMS_SENT." WHERE `task`='".(int)$data1['id']."' AND `phone`='".$phone."' "));
if($test['COUNT(*)']==0){//echo($phone.'<hr>');


$sql = "INSERT INTO `".TASKS_SMS_SENT."` (`task`, `phone`) VALUES ('".(int)$data1['id']."', '".$phone."');";

$site_db->query($sql);
$sql = "UPDATE `".TASKS_SMS."` SET `next` = '".(int)$row['deal_id']."' WHERE `id`=".(int)$data1['id'].";";
$site_db->query($sql);
$sql = "SELECT * FROM ".DEALS_STATUSES_TB." WHERE deal_id='".(int)$row['deal_id']."' ORDER by id DESC LIMIT 1";
$deal_status_data = $site_db->fetch_array($site_db->query($sql));
$sql = "INSERT INTO ".DEALS_STATUSES_TB." (`deal_id`, `user_id`, `status_id`, `status_report`, `status_date`) VALUES ('".(int)$row['deal_id']."', '".(int)$data1['user']."', '".(int)$deal_status_data['status_id']."', '".stripslashes($deal_status_data['status_report'])."\r\n-------\r\nSMS:\r\n".stripslashes($data1['sms'])."', now());";

$site_db->query($sql);
/*


*/

send_sms_msg($phone,$data1['sms']);


}

}
    $sql = "UPDATE `".TASKS_SMS."` SET `next` = '".(int)$row['deal_id']."' WHERE `id`=".(int)$data1['id'].";";
$site_db->query($sql);
die('Обработана сделка №'.$row['deal_id'].'. Задача '.(int)$data1['id'].' '.date('H:i:s d.m.Y'));
}if(!$bd){$sql = "UPDATE `".TASKS_SMS."` SET `status` = '1' WHERE `id`=".(int)$data1['id'].";";
$site_db->query($sql);die('Задача '.(int)$data1['id'].' завершена. '.date('H:i:s d.m.Y'));}
}
die('Нет активных задач '.date('H:i:s d.m.Y'));}
