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
//if(!$kassa['active'])die('опция оплаты отключена');


$merchant_id = $kassa['id'];
$merchant_secret = $kassa['key2'];

function getIP() {
if(isset($_SERVER['HTTP_X_REAL_IP'])) return $_SERVER['HTTP_X_REAL_IP'];
   return $_SERVER['REMOTE_ADDR'];
}
/*if (!in_array(getIP(), array('136.243.38.147', '136.243.38.149', '136.243.38.150', '136.243.38.151', '136.243.38.189'))) {
    die("hacking attempt!");
}*/

$sign = md5($merchant_id.':'.$_REQUEST['AMOUNT'].':'.$merchant_secret.':'.$_REQUEST['MERCHANT_ORDER_ID']);

if ($sign != $_REQUEST['SIGN']) {
    die('wrong sign');
}
$sql = "SELECT `user_id` FROM ".DEALS_TB." WHERE `deal_id`=".(int)$_REQUEST['MERCHANT_ORDER_ID']."";
$deal = $site_db->fetch_array($site_db->query($sql));
if($deal['user_id']==0)die('wrong deal');
//

$sql = "SELECT * FROM ".DEALS_STATUSES_TB." WHERE deal_id='".(int)$_REQUEST['MERCHANT_ORDER_ID']."' ORDER by id DESC LIMIT 1";
$deal_status_data = $site_db->fetch_array($site_db->query($sql));
$deal_status_data['status_id']=8;


$sql = "INSERT INTO ".DEALS_STATUSES_TB." (`deal_id`, `user_id`, `status_id`, `status_report`, `status_date`) VALUES ('".(int)$deal_status_data['deal_id']."', '".(int)$deal['user_id']."', '".(int)$deal_status_data['status_id']."', '".stripslashes($deal_status_data['status_report'])."\r\n-------\r\nПолучена оплата ".(float)$_REQUEST['AMOUNT']." руб.', now());";

$site_db->query($sql);

$sql = "UPDATE ".DEALS_TB." SET `deal_last_status`='".(int)$deal_status_data['status_id']."' WHERE `deal_id`=".(int)$_REQUEST['MERCHANT_ORDER_ID']."";

$site_db->query($sql);

die('YES');
