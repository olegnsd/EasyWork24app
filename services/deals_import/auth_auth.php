<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

include ('config.php');
include_once '../../startup.php';

$hash = $_POST['hash'];



// Проверка ключа интеграции
$sql = "SELECT * FROM tasks_integration WHERE `type`='mielofon'";

$integration = $site_db->query_firstrow($sql);

if(!$integration['id']) {
    exit();
}

$integrationData = json_decode($integration['data'], 1);

$api_auth_key = $integrationData['key'];

$check_hash = md5($salt . $api_auth_key);

if($hash!=$check_hash){
    exit();
}

$access_id = generate_rand_string(60);

$sql = "INSERT INTO mielophone_access (access_id, `date`) VALUES('$access_id', NOW())";
$res = $site_db->query($sql);

echo json_encode(['access_id' => $access_id]);
exit();