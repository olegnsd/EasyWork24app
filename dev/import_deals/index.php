<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';


function proc($str)
{
	return htmlspecialchars(addslashes($str));
}
    // insert_deals('155_leads.csv');
function insert_deals($file_name)
{
	global $site_db;
	
	//$fp = fopen('/var/virtual/eks.me/www/dev/import/log.txt', 'w');
	
	//fwrite($fp, '1');
	
	//exit();
	//$data = file('intensiv_koaching_2013.xlsx');
	
	//$data = array_slice($data, 1);
	
	//$data = file('webinar_2013.csv');
	
	 
	//$data = file('traders-union.ru.csv');
	
	
   $data = file($_SERVER['DOCUMENT_ROOT'].'/temp/'.$file_name, false);
 
 
	 
	   $data = array_slice($data, 1);
	
	 //echo "<pre>", print_r($data);
	
	//exit();
	
	 
	 
	 
	foreach($data as $row)
	{ 
		 
		
		 // echo $row, '<br>';
		 
		$tmp_row1 = split(';', $row);
		
		$tmp_row = array();
		foreach($tmp_row1 as $i=> $dta)
		{
			$tmp_row[$i] = substr(trim($dta),1, strlen(trim($dta))-2);
		}
		
		 
		 
		//echo "<pre>", print_r($tmp_row) ; 
		 
		$email = trim($tmp_row[5]);
		$email = str_replace('"','',$email);
		 
		$phone = trim($tmp_row[2]);
		$phone = str_replace('"','',$phone);
		
		 
		
		//$name = (trim( iconv('utf-8', 'cp1251', $tmp_row[2])));
		
		
		$deal_name = proc($tmp_row[0]);
		
 		$deal_email = proc($tmp_row[25].' '.$tmp_row[26].' '.$tmp_row[27]);
		
		$deal_phone = proc($tmp_row[19].' '.$tmp_row[20].' '.$tmp_row[21].' '.$tmp_row[23].' '.$tmp_row[24]);
		
		$deal_price = proc($tmp_row[1]);
		
		$date_add = split(' ', $tmp_row[3]);
		$date_add = formate_to_norm_date($date_add[0]).' '.$date_add[1] ;
		
		
		$date_edit = split(' ', $tmp_row[5]);
		$date_edit = formate_to_norm_date($date_edit[0]).' '.$date_edit[1] ;
		
		$deal_other_info = '';
		$deal_other_info = proc($tmp_row[8].' '.$tmp_row[9].' '.$tmp_row[10].' '.$tmp_row[11].' '.$tmp_row[12].' ');
		
		if(trim($tmp_row[28]))
		{
			$deal_other_info.= 'Skype '.proc($tmp_row[28]);
		}
		if(trim($tmp_row[29]))
		{
			$deal_other_info.= 'ICQ '.proc($tmp_row[29]);
		}
		if(trim($tmp_row[30]))
		{
			$deal_other_info.= 'Jabber '.proc($tmp_row[30]);
		}
		if(trim($tmp_row[31]))
		{
			$deal_other_info.= 'Google Talk '.proc($tmp_row[31]);
		}
		if(trim($tmp_row[32]))
		{
			$deal_other_info.= 'MSN '.proc($tmp_row[32]);
		}
		if(trim($tmp_row[33]))
		{
			$deal_other_info.= 'Другой IM '.proc($tmp_row[33]);
		}
		
		$deal_contact_person = proc($tmp_row[14].' '.$tmp_row[15].' '.$tmp_row[17].' '.$tmp_row[18]);
				
			
		
		//echo $deal_other_info, '<br>';
		
		
		// 05.09.2014 16:31
		 // echo formate_to_norm_date($date_edit[0]), ' ' ;
			 
			//  echo $date_add,' ';
		//$desc = $tmp_row[6]; 
		
		//$deal_contact_person = 	 $tmp_row[3].' '.$tmp_row[4];  
		
	 	 
		 
		 
		if(1)
		{
			$user_id = 10;
			$client_id = 21;
			// Добавляем сделку
			$sql = "INSERT INTO client_266.".DEALS_TB." SET deal_name='$deal_name', deal_type='$deal_type', deal_price='$deal_price', deal_client_id='$client_id', deal_client_name='$deal_client_name', deal_private_edit='$deal_private_edit',  deal_private_show='$deal_private_show', deal_date_add='$date_add', user_id='$user_id', deal_other_info = '$deal_other_info', deal_contact_person = '$deal_contact_person',
			deal_email='$deal_email', deal_address='$deal_address', deal_phone='$deal_phone', group_id='0', deal_last_status_date='$date_edit'";
					
			$site_db->query($sql);
			
			$deal_id = $site_db->get_insert_id($sql);
			
			
			
			$st_arr = array('Первичный контакт'=>1, 'Переговоры'=>5, 'Принимают решение'=>2, 'Согласование договора'=>6, 'Успешно реализовано'=>9, 'Закрыто и не реализовано' => 10);
		
			$deal_status = trim($tmp_row[13]);
			
			
			if($st_arr[$deal_status])
			{
				$deal_status = $st_arr[$deal_status];
				
				// Добавляем статус и отчет
				$sql = "INSERT INTO client_266.".DEALS_STATUSES_TB." SET  deal_id='$deal_id', user_id='$user_id', status_id='$deal_status', status_report='$deal_report', status_date='$date_edit'";
					
				$site_db->query($sql);
			}
		
		
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
 
function dt()
{
	global $site_db;
}
 

?>

<?php
if($_POST['act']=='go')
{
	$name = rand(100,200).'_leads.csv';
	
	if(copy($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/temp/'.$name)){
		
		insert_deals($name);
		
		echo "Импорт успешно завершен!";
		exit();
	}
	
	
}
else
{
?>

<style>
html
{
	font-family:Tahoma, Geneva, sans-serif
}
</style>
<h3>Ипортирование сделок в систему Easywork24</h3>
<div style="background-color:#E8E8E8; border-radius:5px; padding:20px; border:1px solid #BBB">
<form action="" method="post" enctype='multipart/form-data' >
<input type="hidden" value="go" name="act" />
Файл импорта: <input type="file" name="file" />
<br /><br />
<input type="submit" value="Начать импорт" />
</form>
<div style="font-size:12px; color:#232323; font-style:italic"><b>!</b> На странице "Сделки" в верхнем правом углу нажать на кнопку <b>"... ЕЩЕ->Экспорт"</b>. В появившемся окне выбрать пункт <b>"Экспорт всех сделок(ASCII)"</b> в разделе "Экспорт в CSV-файл". </div>
</div>

<?php
}
?>