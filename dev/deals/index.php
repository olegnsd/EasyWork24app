<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
 
  insert_users();
function insert_users()
{
	global $site_db;
	
	//$fp = fopen('/var/virtual/eks.me/www/dev/import/log.txt', 'w');
	
	//fwrite($fp, '1');
	
	//exit();
	//$data = file('intensiv_koaching_2013.xlsx');
	
	//$data = array_slice($data, 1);
	
	//$data = file('webinar_2013.csv');
	
	 
	//$data = file('traders-union.ru.csv');
	
	
   $data = file('deals.csv');
 
 
	 
	//  $data = array_slice($data, 1, 10);
	
	 //echo "<pre>", print_r($data);
	
	//exit();
	
	 
	 
	foreach($data as $row)
	{ 
		//echo $row, '<br>';
		 
		$tmp_row = split(';', $row);
		 
		$email = trim($tmp_row[5]);
		$email = str_replace('"','',$email);
		 
		$phone = trim($tmp_row[2]);
		$phone = str_replace('"','',$phone);
		
		 
		
		//$name = (trim( iconv('utf-8', 'cp1251', $tmp_row[2])));
		
		
		$deal_name = htmlspecialchars(addslashes($tmp_row[2]));
		
 		$deal_email = $tmp_row[11];
		
		$deal_phone = $tmp_row[8].' '.$tmp_row[9];
		
		$deal_address = $tmp_row[6];
			 
		//$desc = $tmp_row[6]; 
		
		$deal_contact_person = 	 $tmp_row[3].' '.$tmp_row[4];  
		
	 	 
		 
		 
		if(1)
		{
			$user_id = 206;
			
			// Добавляем сделку
			$sql = "INSERT INTO ".DEALS_TB." SET deal_name='$deal_name', deal_type='$deal_type', deal_price='$deal_price', deal_client_id='101', deal_client_name='$deal_client_name', deal_private_edit='$deal_private_edit',  deal_private_show='$deal_private_show', deal_date_add=NOW(), user_id='$user_id', deal_other_info = '$deal_other_info', deal_contact_person = '$deal_contact_person',
			deal_email='$deal_email', deal_address='$deal_address', deal_phone='$deal_phone', group_id='26'";
					
			$site_db->query($sql);
		
		}
		else
		{
			$nn++;
			//echo $email,' <br>';
		}
		 
		// if($num>10) break;
		
		$num++;
	}
	
	 
}
 
?>