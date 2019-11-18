<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
include ('config.php');

$login = $_POST['login'];

if($login!='api') {
    exit();
}
echo json_encode(['salt' => $salt]);

exit();