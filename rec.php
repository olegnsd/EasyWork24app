<?php
ini_set('display_errors', 0);

//echo '<pre>'; 
//print_r($_SERVER); 
//echo '</pre>';

include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_mysql.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_servers.php';
server_detect();

include_once $_SERVER['DOCUMENT_ROOT'].'/config.php'; //Íàñòðîéêè
include_once $_SERVER['DOCUMENT_ROOT'].'/config_tables.php'; //Íàñòðîéêè
include_once $_SERVER['DOCUMENT_ROOT'].'/global.php'; //Ñîçäàíèå îáúåêòà ÁÄ

//echo('LOG_REC_PATH: '.LOG_REC_PATH);
//echo(' REC_CALL_PATH: '.REC_CALL_PATH);

$user_id = (int)$_REQUEST['secret'];//$secret = file_get_contents(LOG_REC_PATH."/secret_dwnl.txt");
$sql = "SELECT user_id FROM ".USERS_TB." WHERE user_id='$user_id'";
$row = $site_db->query($sql);
$secret = $site_db->fetch_array($row);
$secret = $secret['user_id'];
if(!$secret)die('err_s');

//создать папки, если нет
$rec_call_path = REC_CALL_PATH.'/'.$user_id;
$log_rec_path = LOG_REC_PATH.'/'.$user_id;
if(!is_dir($rec_call_path))
{
    mkdir($rec_call_path, 0775, true);
}
if(!is_dir($log_rec_path))
{
    mkdir($log_rec_path, 0775, true);
}

$foptmp = fopen($log_rec_path."/log_rec.tmp", "ab");

$rec = json_encode($_REQUEST, JSON_UNESCAPED_UNICODE);
$rec_f = json_encode($_FILES, JSON_UNESCAPED_UNICODE);
$date = date('Y-m-d H:i:s', time());

fwrite($foptmp, "        " . PHP_EOL);
fwrite($foptmp, $date. " client: ". $_SERVER_ID . PHP_EOL);
fwrite($foptmp, $rec . PHP_EOL);
fwrite($foptmp, $rec_f . PHP_EOL);

fclose($foptmp);

$path = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if($path != 'm4a')die('err_f');

$tmp_file = explode('_', $_FILES['file']['name']);
$tmp_file = preg_replace("/[^0-9]/", '', $tmp_file[0]);
$tmp_file1 = $tmp_file;
if(strlen($tmp_file) == 11)$tmp_file = substr($tmp_file, 1);

//$import_file = $rec_call_path."/". $tmp_file.".m4a";
//if(file_exists($import_file)){
    //unlink($import_file);
//}

//move_uploaded_file($_FILES['file']['tmp_name'], $import_file);

// Обновляем последний статус сделки
$ph_ln = strlen($tmp_file);
$phone = str_split($tmp_file);
$phone = implode("{1,1}.*", $phone);
$phone = "^.*". $phone . "{1,1}.*$";
//SELECT * FROM `accounts` WHERE `phone` REGEXP "^.*7{1,1}.*9{1,1}.*0{1,1}.*1{1,1}.*3{1,1}.*0{1,1}.*1{1,1}.*0{1,1}.*1{1,1}.*5{1,1}.*1{1,1}.*$"
//+79013010151

$sql = "SELECT deal_id, user_id FROM ".DEALS_TB." WHERE deal_phone REGEXP '$phone' AND LENGTH(deal_phone)<=('$ph_ln'+6) AND deal_deleted<>1";
$row = $site_db->query($sql);
$res_sel = $site_db->fetch_array($row);
$deal_id = $res_sel['deal_id'];

if($deal_id){
    $sql = "UPDATE ".DEALS_TB." SET deal_last_status_date=NOW(), deal_status='-1' WHERE deal_id='$deal_id'";
    $res = $site_db->query($sql); 
    
    $user_id_base = $res_sel['user_id'];
    
    $sql = "UPDATE ".DEALS_STATUSES_TB." SET status_date=now() WHERE deal_id='$deal_id';";
    $site_db->query($sql);
}else{
    // Добавляем сделку
    $deal_contact_person = '+'.$tmp_file1;
    $deal_phone = '+'.$tmp_file1;
    
    $sql = "INSERT INTO ".DEALS_TB." SET deal_name='', deal_type='0', deal_price='', deal_client_id='0', deal_client_name='', deal_private_edit='0',  deal_private_show='0', deal_date_add=NOW(), user_id='$user_id', deal_other_info = '', deal_contact_person = '$deal_contact_person',
    deal_email='', deal_address='', deal_phone='$deal_phone', group_id='0', deal_status='-1', deal_deleted='0', deal_last_status_date=CURRENT_TIMESTAMP, deal_last_status='0' ";
    $res = $site_db->query($sql);
    
    $deal_id = $site_db->get_insert_id();
    $current_user_id = 1;
    $status_id = 1;
    $status_report = '';
    $sql = "INSERT INTO ".DEALS_STATUSES_TB." (`deal_id`, `user_id`, `status_id`, `status_report`, `status_date`) VALUES ('$deal_id', '".(int)$user_id."', '$status_id', '".stripslashes($status_report)."', now());";
    $site_db->query($sql);
    
    $user_id_base = $user_id;
}


$rec_path_user = REC_CALL_PATH.'/'.$user_id_base;
if(!is_dir($rec_path_user))
{
    mkdir($rec_path_user, 0775, true);
}

$import_file = $rec_path_user."/". $tmp_file.".m4a";//$import_file = "temp/rec_call/". $tmp_file.".m4a";// "temp/rec_call/" REC_CALL_PATH."/"
if(file_exists($import_file)){ 
    unlink($import_file);
}

move_uploaded_file($_FILES['file']['tmp_name'], $import_file);

`echo "        " >>temp/log_rec/qaz`;
$myecho = date("d.m.Y H:i:s");
$myecho2 = $_SERVER_ID;
`echo " date_now : "  $myecho " client: " $myecho2>>temp/log_rec/qaz`;
$myecho = json_encode($sql);
`echo "sql: "  $myecho >>temp/log_rec/qaz`;
$myecho = json_encode($res);
`echo "res: "  $myecho >>temp/log_rec/qaz`;


echo('Ok');
