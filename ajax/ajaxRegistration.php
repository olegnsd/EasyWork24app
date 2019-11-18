<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_external.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_sms.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_users.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_org.php';

// Класс авторизации
$auth = new CAuth($site_db);
global $system_sms;
$system_sms=1;
// Проверка авторизации
if(!$auth->check_auth())
{
	exit();
}

$current_user_id = $auth->get_current_user_id();

// Блок редактирования для админа
if(!$current_user_obj->get_is_admin())
{
	exit();
}
	
$user_obj->fill_user_data($current_user_id);



$surname = value_proc($_POST['surname']);
$name = value_proc($_POST['name']);
$middlename = value_proc($_POST['middlename']);
$login = value_proc($_POST['login']);
$pass = value_proc($_POST['pass']);
$pass1 = value_proc($_POST['pass1']);
$phone = value_proc($_POST['phone']);
$position = value_proc($_POST['position']);
$user_dept = $_POST['user_dept'];
$user_is_admin = value_proc($_POST['user_is_admin']);
$user_limitation = value_proc($_POST['user_limitation']);
$user_is_full_access = value_proc($_POST['user_is_full_access']);
$email = value_proc($_POST['email']);
 
// Приводим к правильному формату номер телефона
$phone = convert_to_valid_phone_number($phone);

if($surname=='')
{
	$error['surname'] = 1;
}
if($name=='')
{
	$error['name'] = 1;
}
if($middlename=='')
{
	$error['middlename'] = 1;
}
if($login=='')
{
	$error['login'] = 1;
}
if($pass=='')
{
	$error['pass'] = 1;
}
if($pass1=='')
{
	$error['pass1'] = 1;
}
if($position=='')
{
	$error['position'] = 1;
}

if($phone=='')
{
	$error['phone'] = 2;
}

if($pass!=$pass1)
{
	$error['pass'] = 2;
	$error['pass1'] = 2;
}

if($email && !email_valid($email))
{
	$error['email'] = 1;
}

if($email && check_user_email_for_exists($email))
{
	$error['email'] = 2;
}

// Проверка на такой же логин
$sql = "SELECT * FROM ".USERS_TB." WHERE user_login='$login'";

$row = $site_db->query_firstrow($sql);

// Пользователь с таким логином уже существует
if($row['user_id'])
{
	$error['login'] = 2;
}

if($phone)
{
	// Проверка на такой же телефон
	$sql = "SELECT * FROM ".USERS_TB." WHERE user_phone='$phone'";
	
	$row = $site_db->query_firstrow($sql);
	
	// Пользователь с таким логином уже существует
	if($row['user_id'])
	{
		$error['phone'] = 1;
	}
}

$user_full_name = $surname.' '.$name.' '.$middlename;

// Определяем пол
$user_sex = get_user_sex_by_user_full_name($user_full_name);



// Если ошибок не обнаружено - добавляем пользователя
if(!$error)
{
	// Добавляем пользователя
	
	/*$user_job_id_tmp = rand(1000,9999);
	
	$rand_stop = false;
	
	while(!$rand_stop)
	{
		// Проверяем на рандомное значение id пользователя
		$sql = "SELECT * FROM ".USERS_TB." WHERE user_job_id='$user_job_id_tmp'";
		
		$row = $site_db->query_firstrow($sql);
		
		if(!$row['user_id'])
		{
			$rand_stop = true;
			$user_job_id = $user_job_id_tmp;
		}
	}*/
	
	$password_hash = password_hash_proc($pass);
	
	// Добавляем сотрудника
	$sql = "INSERT INTO ".USERS_TB." (user_name, user_middlename, user_surname, user_phone, user_login, user_password, registrated_by_user_id, user_registration_privilege, user_registration_date, user_sex, user_activated, is_admin, user_limitation,is_full_access,user_email) 
			VALUES('$name', '$middlename', '$surname', '$phone', '$login', '$password_hash', '$current_user_id', '$registration_privilege', NOW(), '$user_sex', 1, '$user_is_admin', '$user_limitation', '$user_is_full_access', '$email')";
	
	$row = $site_db->query($sql);
	
	$inserted_user_id = $site_db->get_insert_id();
	
	// Сохраняем отделы пользователя
	save_user_depts($inserted_user_id, $user_dept);
				
	// Выбор добавленного пользователя
	//$sql = "SELECT * FROM ".USERS_TB." ORDER by user_id DESC LIMIT 1";
	
	//$row = $site_db->query_firstrow($sql);
	
	//$inserted_user_id = $row['user_id'];
	
	//$inserted_user_job_id = $row['user_job_id'];
	
	// Добавляем должность
	$sql = "INSERT INTO ".USERS_POSITIONS_TB." SET position_name='$position', user_id='$inserted_user_id', position_date=NOW()";
		
	$site_db->query($sql);
	
	
	
	
	$success = 1;
	
	
	$success_msg = iconv('cp1251', 'utf-8', "Сотрудник <b>".$surname." ".$name." ".$middlename."</b> успешно зарегистрирован в системе".$notice_ext.".</b> <a href='/msgs?id=$inserted_user_id'>Написать сообщение</a> или <a href='/registration'>Зарегистрировать еще сотрудника</a>");
	
	
	$user_phone = $phone;
	
	### sms body
	$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/registration.tpl');
	
	// Адрес сайта
	$site_addr = str_replace('www.', '', $_SERVER['HTTP_HOST']);
	
	$PARS['{USER_NAME}'] = $name;
	
	$PARS['{USER_MIDDLENAME}'] = $middlename;
	
	$PARS['{LOGIN}'] = $login;
	
	$PARS['{PASSWORD}'] = $pass1;
	
	$PARS['{SITE}'] = $site_addr;
	 
	$sms_text = fetch_tpl($PARS, $sms_tpl);
	###\ sms body
	
	// Отправка смс сообщения
	send_sms_msg($phone, $sms_text, 1);

}

// Возвращаем результат
echo json_encode(array('success' => $success, 'success_msg' =>$success_msg,  'error' => $error));

?>