<?php
// Отправка смс сообщения
function send_sms_msg($phone, $sms_text, $sender_text_name='EasyWork24')
{ 
	//	ini_set('display_errors', 1);
	//include_once $_SERVER['DOCUMENT_ROOT']."/classes/class.Sms.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/ssms_su.php";
	//include_once $_SERVER['DOCUMENT_ROOT']."/classes/transport.php";
	 
	//$api = new Transport(); 
	
	// Валидация тел. номера
	$phone = valid_user_phone_for_sms($phone);
	
	 
	if(!$phone)
	{
		return false;
	}
 
	$email = "easywork24-clients";
	$password = "9zm2kgoq";
	
	if(preg_match('/\+38/', $phone) || $sender_text_name)
	{
		$sender_phone = 'EasyWork24';
	}
	else
	{
		$sender_phone = TRANSACTION_SMS; //ROUND_SENDER_NAME;
	}
	  
	// Лог
	sms_to_log($phone, $sms_text);
	
	
	// Если включена опция отправки смс
	if(!SMS)
	{
		return false;
	}
	
	if(!$sender_text_name)
	{
		$sms_text = substr($sms_text,0,70);
	}
	
	##### transport.php
	//if($sender_text_name)
//	{
//		$api->send(array('text' => iconv('cp1251', 'utf-8',$sms_text), 'source' => 'easywork24'), array($phone));
//	}
//	else
//	{
//		$api->send(array('text' => iconv('cp1251', 'utf-8',$sms_text), 'onlydelivery' => 1, 'use_alfasource' => 0), array($phone));
//	}
	####
 
	
	$r = smsapi_push_msg_nologin($email, $password, $phone, iconv('cp1251', 'utf-8',$sms_text), array("sender_name"=>$sender_phone));

 // print_r($r);

}

// Запись в лог
function sms_to_log($phone, $text)
{
	global $site_db, $current_user_id;
	
	//$text = value_proc($text, 0);
	
	$sql = "INSERT INTO ".SMS_LOG_TB." (date, user_id, phone, text) VALUES (NOW(), '$current_user_id', '$phone', '$text')";
	 
	$site_db->query($sql);
}

// Валидация номера телефона для отправки через смс
function valid_user_phone_for_sms($phone)
{
	return $phone;
	
	
	$phone = preg_replace('/[^\+0-9]+/', '', $phone);
	
	if(strlen($phone)==11 && $phone[0]==8)
	{
		$phone = substr($phone,1,11);
	}
	if(strlen($phone)==10)
	{
		$phone = '+7'.$phone;
	}
	
	// Если номер телефона некорректно задан, возвращаем false
	 
	return $phone;
	
}
?>
