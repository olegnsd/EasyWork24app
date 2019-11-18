<?php

if(!$_GET['g']==1)
{
	exit();
}


include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';

get_user_data();

function get_user_data()
{
	global $site_db;
	
	$emails = 'kulakova07@mail.ru
moneyvasich@gmail.com
812sky@gmail.com
alekseiag@gmail.com
kirillonis@gmail.com
frankenikolay@gmail.com
mrcrazyzver@gmail.com
vitaly.hlusov@gmail.com
709773@gmail.com
alexander@shatulov.com
sotnik172@gmail.com
mblxac@gmail.com
daffsh@yandex.ru
bortik59@gmail.com
kuznetsov.228@gmail.com
bolshaksa@gmail.com
sondarium@gmail.com
root@admin.ru.net
compinfo39@mail.ru
reveg@yandex.ru
d.u.banchenko@gmail.com
ivrels@yandex.ru
trudim792@gmail.com
42kira@gmail.com
kalmykan@gmail.com
k0c.p0rn0@gmail.com
my@seowit.ru
timur.ugulava@gmail.com
1963dima@gmail.com
al0736@gmail.com';

	$email_arr = split(chr(13).chr(10), $emails);
	
	$fp = fopen('911_users.txt', 'a');
	foreach($email_arr as $email)
	{
		$email = trim($email);
		
		$sql = "SELECT * FROM mmm.users WHERE email='$email'";
		 
		$row = $site_db->query_firstrow($sql);
		 
		fwrite($fp, $row['email'].' '.$row['name'].' '.$row['phone'].chr(13).chr(10));
	}
}

function get_nums()
{
	global $site_db;
	
	$fp = fopen('911_2.txt', 'a');
	
	$sql = "SELECT * FROM mmm.users WHERE phone LIKE '7911%' AND checked=0 LIMIT 100";
	
	$res = $site_db->query($sql);
				
	while($row=$site_db->fetch_array($res))
	{
		$sql = "UPDATE mmm.users SET checked=1 WHERE num='".$row['num']."'";
		$site_db->query($sql);
		
		fwrite($fp, $row['name'].';'.$row['email'].';'.$row['phone'].chr(13).chr(10));
	}
	
	
	/*$fp = fopen('911.txt', 'a');
	
	$data = file('mmm.csv');
	
	//echo "<pre>", print_r($f);
	$num = 0;
	foreach($data as $val)
	{
		
		list($num, $tmp_name, $phone) = explode(';', $val);
		
		$tmp_name_arr = split('\(', $tmp_name);
		 
		$name = addslashes(trim($tmp_name_arr[0]));
		$email = addslashes(trim(str_replace(')', '', ($tmp_name_arr[1]))));
		$phone = addslashes($phone);
		 
		$sql = "INSERT INTO mmm.users SET name='$name', email = '$email', phone='$phone'";
		
		$site_db->query($sql);
		
		 // 89261018258
		/*if(preg_match('/^7911/', $phone))
		{
			echo $phone,' ';
			$n++;
		}*/
		
		//if($num >115)
		//{
			 // break;
		//}
		//$num++;
	//}
	
	//echo 'off-'.$num.'-'.$n;*/

}

?>