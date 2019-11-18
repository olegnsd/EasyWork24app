<?php 
session_name('ews');
session_start();
//-----------------------------------------------------
//--- Start DB ----------------------------------------
//-----------------------------------------------------
include_once 'includes/db_mysql.php';
$site_db = new Db($db_host, $db_user, $db_password, $db_name);
$site_db->query('SET NAMES cp1251');

?>