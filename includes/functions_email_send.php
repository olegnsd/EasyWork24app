<?php
// Отправка сообщения
function send_email_msg($to_mail, $subject, $text)
{
	if(!$to_mail)
	{
		return '';
	}
	
	$text = iconv('cp1251', 'utf-8', $text);
	$subject = iconv('cp1251', 'utf-8', $subject);
	$mail_from = iconv('cp1251', 'utf-8', MAIL_FROM_EMAIL);
	$mail_from_name = iconv('cp1251', 'utf-8', MAIL_FROM_NAME);
	
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/phpmailer/class.phpmailer.php');

$mail             = new PHPMailer(); // defaults to using php "mail()"

$body             = $text;
$body             = eregi_replace("[\]",'',$body);

$mail->CharSet = 'utf-8';
$mail->ContentType = 'text/html';

$mail->AddReplyTo($mail_from, $mail_from_name);

$mail->SetFrom($mail_from, $mail_from_name);

$mail->AddReplyTo($mail_from, $mail_from_name);


$address = $to_mail;
$mail->AddAddress($address);

$mail->Subject    = $subject;


$mail->MsgHTML($body);



if(!$mail->Send()) {
  return true;
} else {
  return false;
}
    
	
	
	//$subject = 'Новый контакт';
	/*$charset = 'cp1251';
	
	//$encoded_subject = "ssd" ;
	$headers = "From: ".MAIL_FROM_NAME." <".MAIL_FROM_EMAIL.">\r\n" . "Content-Type: text/html; charset=$charset; format=flowed\n" . "MIME-Version: 1.0\n" . "Content-Transfer-Encoding: 8bit\n" . "X-Mailer: PHP/" . phpversion () . "\n";
	
	if(mail($to_mail, $subject, $text, $headers) )
	{ 
		return true;
	}
	else
	{
		return false;
	}*/
		
}
?>