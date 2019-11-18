<?php
ini_set("display_errors","1");

if($_GET['token']!=='9287')die('token');

// Опознание сервера
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/db_mysql.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_servers.php';
server_detect();

include_once $_SERVER['DOCUMENT_ROOT'].'/config.php'; //Íàñòðîéêè
include_once $_SERVER['DOCUMENT_ROOT'].'/config_tables.php'; //Íàñòðîéêè
include_once $_SERVER['DOCUMENT_ROOT'].'/global.php'; //Ñîçäàíèå îáúåêòà ÁÄ

// Выбираем сделки пользователя
	$sql = "SELECT * FROM ".DEALS_TB." i
			WHERE i.deal_deleted<>1 ORDER BY i.user_id ASC";

    $res = $site_db->query($sql);
    
    echo('SELECT: '.$res);
    echo('<br>');
    echo($sql);
    echo('<br>');
			
	while($deal_data=$site_db->fetch_array($res))
    {
        //кнопка скачивания записи звонка
        $deal_phone = $deal_data['deal_phone'];
        $deal_phone = preg_replace("/[^0-9]/", '', $deal_phone);
        if(strlen($deal_phone) == 11)$deal_phone = substr($deal_phone, 1);

        $rec_call_path = REC_CALL_PATH.'/'.$deal_data['user_id'];
        $file = $rec_call_path."/".$deal_phone.".m4a";//$file = $_SERVER['DOCUMENT_ROOT']."/temp/rec_call/".$deal_phone.".m4a";//79260001026

        echo($file);
        
        if (file_exists($file)) {
            $sql = "UPDATE ".DEALS_TB." SET deal_status='-1' WHERE deal_id=".$deal_data['deal_id'];
            $res1 = $site_db->query($sql);
            
            echo('<br>');
            echo('UPDATE: '.$res1);
            echo('<br>');
            echo($sql);
        }
        echo('<br>');
        echo('next');
        echo('<br>');
    }

    
