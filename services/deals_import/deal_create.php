<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

include ('config.php');
include_once '../../startup.php';

$access_id = $_POST['access_id'];

$session_access_id =  $_SESSION['access_id'];

$sql = "SELECT * FROM mielophone_access WHERE access_id='$access_id'";

$row = $site_db->query_firstrow($sql);

if($row['access_id']!=$access_id){
    exit();
}

$user_id = value_proc($_POST['data']['line']['user_id']);
$name = value_proc($_POST['data']['line']['name']);
$ref = value_proc($_POST['data']['line']['ref']);
$phone = value_proc($_POST['data']['line']['phone']);
$email = value_proc($_POST['data']['line']['email']);
$product = value_proc($_POST['data']['line']['product']);

$deal_name = $product.' '.$ref;

$sql = "INSERT INTO tasks_deals SET user_id='$user_id', deal_last_status_date=NOW(),
 deal_date_add=NOW(), deal_other_info='$ref', deal_email='$email', deal_phone='$phone', deal_name='$name', 
 deal_contact_person='$phone', group_id=50, deal_client_name='$phone'";
$res = $site_db->query($sql);


//echo json_encode($_POST['data']);
echo json_encode(['success' => 1]);
exit();