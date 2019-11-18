<?
//$h=date('H');
//if($h<10)die();
//if($h>=22)die();

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);//E_ALL);



include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_mysql.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_servers.php';
server_detect();


include_once $_SERVER['DOCUMENT_ROOT'].'/config.php'; //
include_once $_SERVER['DOCUMENT_ROOT'].'/config_tables.php'; //
include_once $_SERVER['DOCUMENT_ROOT'].'/global.php'; //



global $site_db;

$sql = "SELECT * FROM tasks_integration WHERE `type`='kassa'";

    $keyRow = $site_db->query_firstrow($sql);

    if($keyRow['id']) {

        $kassa = unserialize($keyRow['data']);

    }
if(!$kassa['active'])die('опция оплаты отключена');


$merchant_id = $kassa['id'];
$secret_word = $kassa['key1'];


/*if (substr(md5($freekassa[1].$freekassa[0].$freekassa[2].($_GET['s']+$_GET['id'])),0,5) != $_GET['t']) {
    die('wrong sign'.md5($freekassa[1].$freekassa[0].$freekassa[2].($_GET['s']+$_GET['id'])));
}*/
$sql = "SELECT `user_id` FROM ".DEALS_TB." WHERE `deal_id`=".(int)$_GET['id']."";
$deal = $site_db->fetch_array($site_db->query($sql));
if($deal['user_id']==0)die('wrong deal');
if(((float)$_GET['s'])<1)die('wrong s');
$order_id = (int)$_GET['id'];
$order_amount = (float)$_GET['s'];
$sign = md5($merchant_id.':'.$order_amount.':'.$secret_word.':'.$order_id);
$paymentUrl = 'http://www.free-kassa.ru/merchant/cash.php?m='.$merchant_id.'&oa='.$order_amount.'&o='.$order_id.'&s='.$sign.'&lang=ru&i=&em=';
header('Location:'.$paymentUrl);die();
