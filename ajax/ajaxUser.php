<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_users.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_sms.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_org.php';

// Класс авторизации
$auth = new CAuth($site_db);

// Проверка авторизации
if(!$auth->check_auth())
{
	exit();
}

$mode = $_POST['mode'];

switch($mode)
{
	case 'send_reg_data_to_user':
		
		$user_id = value_proc($_POST['user_id']);
		
		if(!check_for_send_msg($user_id, $current_user_id))
		{
			exit();
		}
		
		// Отсылаем регистрационные данные по смс
		$result = send_to_user_reg_data_by_sms($user_id);
		
		echo $result; 
		
	break;
	case 'save_profile_settings':
	
		$current_user_id = $auth->get_current_user_id();
		$user_id = value_proc($_POST['user_id']);
		$surname = value_proc($_POST['surname']);
		$name = value_proc($_POST['name']);
		$middlename = value_proc($_POST['middlename']);
		$login = value_proc($_POST['login']);
		$pass = value_proc($_POST['pass']);
		$pass1 = value_proc($_POST['pass1']);
		$phone = value_proc($_POST['phone']);
		$position = value_proc($_POST['position']);
		$phone = convert_to_valid_phone_number($phone);
		$registration_privilege = value_proc($_POST['registration_privilege']);
		$auth_method = value_proc($_POST['auth_method']);
		
		$bdate_day = value_proc($_POST['bdate_day']);
		$bdate_month = value_proc($_POST['bdate_month']);
		$bdate_year = value_proc($_POST['bdate_year']);
		
		$user_dept = ($_POST['user_dept']);
		$user_is_admin = value_proc($_POST['user_is_admin']);
		$user_is_fired = value_proc($_POST['user_is_fired']);
		$user_is_full_access = value_proc($_POST['user_is_full_access']);
		
		$user_limitation = value_proc($_POST['user_limitation']);
		
		$email = value_proc($_POST['email']);
		
		$auth_method = !in_array($auth_method, array(0,1,2)) ? 0 : $auth_method;
		
		if(!$user_id)
		{
			exit();
		}
		// Проверка на ошибки
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
		
		if($position=='')
		{
			$error['position'] = 1;
		}
		
		if($pass && $pass!=$pass1)
		{
			$error['pass'] = 2;
			$error['pass1'] = 2;
		}
		if($phone=='')
		{
			$error['phone'] = 2;
		}
		
		if($email && !email_valid($email))
		{
			$error['email'] = 1;
		}
		
		if($email && check_user_email_for_exists($email, $user_id))
		{
			$error['email'] = 2;
		}
		
		// Проверка на такой же логин
		$sql = "SELECT * FROM ".USERS_TB." WHERE user_login='$login' AND user_id<>'$user_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		// Пользователь с таким логином уже существует
		if($row['user_id'])
		{
			$error['login'] = 2;
		}
		
		if($phone)
		{
			// Проверка на такой же телефон
			$sql = "SELECT * FROM ".USERS_TB." WHERE user_phone='$phone' AND user_id!='$user_id'";
			
			$row = $site_db->query_firstrow($sql);
			
			// Пользователь с таким логином уже существует
			if($row['user_id'])
			{
				$error['phone'] = 1;
			}
		}
		
		// Влидация даты
		$user_bdate = proc_splited_date($bdate_year, $bdate_month, $bdate_day);
		
		if(!$user_bdate && ($bdate_year || $bdate_month || $bdate_day))
		{
			$error['bdate'] = 1;
		}
		
		// Если ошибок не обнаружено - сохраняем изменения профиля
		if(!$error)
		{			
			// Если требуется изменить пароль
			if($pass && $pass==$pass1)
			{
				// Хеш идентификатор пользователя
				$user_hash = password_hash_proc(generate_rand_string(60));
			
				$password_hash = password_hash_proc($pass);
				
				$set_pass = ", user_password = '$password_hash', user_hash='$user_hash', auth_iter=0, auth_last_iter_date=0 ";
				
				// Пароль изменен
				$password_chenge = 1;
			}
			
			// Заполняем объект пользователя
			$user_obj->fill_user_data($current_user_id);
			
			if($current_user_obj->get_is_admin())
			{
				// удаление пользователя из всех отделов, если он был уволен
				if($user_is_fired)
				{
					$sql = "DELETE FROM tasks_company_depts_users WHERE user_id='$user_id'";
					$site_db->query($sql);
				}
				else
				{
					// Сохраняем отделы пользователя
					save_user_depts($user_id, $user_dept);
				}
				 
				
				$user_full_name = $surname.' '.$name.' '.$middlename;
							
				// Определяем пол
				$user_sex = get_user_sex_by_user_full_name($user_full_name);
		
				$set = ", user_name ='$name', user_middlename='$middlename', user_surname='$surname', is_admin = '$user_is_admin', is_fired='$user_is_fired', user_limitation='$user_limitation', is_full_access='$user_is_full_access'";
			}
			
			// Если последняя должность отличается от переданной, добавляем в таблицу должностей
			// Редактировать должность может либо генеральный либо начальник у подчиненного
			if($position!=$user_obj->get_user_position() && $current_user_obj->get_is_admin() )
			{
				$sql = "INSERT INTO ".USERS_POSITIONS_TB." SET position_name='$position', user_id='$user_id', position_date=NOW()";
				
				$site_db->query($sql);
			}
			
			// Сохраняем данные сотрудника
			$sql = "UPDATE ".USERS_TB." SET
					user_phone = '$phone', 
					user_login = '$login',
					user_auth_method = '$auth_method',
					user_bdate = '$user_bdate',
					user_email = '$email'
					$set_pass
					$set
					WHERE user_id='$user_id'";
			
			$site_db->query($sql);
			
			 
			
			if(!mysql_error() && $password_chenge && $current_user_id==$user_id)
			{
				$auth->set_cookie_user_hash($user_hash);
			}
			
			$success = 1;
		}
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'success_msg' =>$success_msg,  'error' => $error));
	
	break;

	case 'save_api_calls_settings': 
        $user_id = value_proc($_POST['user_id']);
        $api_key = value_proc($_POST['api_key']);
        $timefrom = value_proc($_POST['timefrom']);
        $timeto = value_proc($_POST['timeto']);
        $prefix = value_proc($_POST['prefix']);
        $prior = value_proc($_POST['prior']);
        $caller = mb_strtoupper(value_proc($_POST['caller']));
        $sms_enable = value_proc($_POST['sms_enable']);
        $sms_text = value_proc($_POST['sms_text']);
        
        if(!$user_id)
		{
			exit();
		}
		// Проверка на ошибки
        $error = false;
		if($api_key=='')
		{
			$error['api_key'] = 1;
		}
        if($timefrom < '10:00')
		{
			$error['timefrom'] = 1;
		}
        if($timefrom == '')
		{
			$error['timefrom'] = 2;
		}
        if($timeto > '21:00')
		{
			$error['timeto'] = 1;
		}
        if($timeto == '')
		{
			$error['timeto'] = 2;
		}
        if($timefrom > $timeto)
		{
			$error['timefromto'] = 1;
		}
		if(strlen($prefix) > 5)
		{
			$error['prefix'] = 1;
		}
        if(!preg_match('/^\d+\+$/', $prefix))
		{
			$error['prefix'] = 2;
		}
		if($prefix == ''){
            unset($error['prefix']);
        }
        if(strlen($caller) > 4)
		{
			$error['caller'] = 1;
		}
        if(preg_match('/\W+/', $caller))
		{
			$error['caller'] = 2;
		}
		if(strlen($caller) == '')
		{
			$error['caller'] = 3;
		}
		if($sms_enable == 1 && $sms_text == '')
        {
            $error['sms_text'] = 1;
        }
        
        // Если ошибок не обнаружено - сохраняем изменения профиля
		if(!$error)
		{
            // Сохраняем api settings
            $sql = "SELECT user_id FROM ".API_CALLS_TB;//." WHERE user_id='$user_id'";
			$row = $site_db->query_firstrow($sql);
            if(!isset($row['user_id'])){
                 $sql = "INSERT INTO ".API_CALLS_TB." 
					(user_id,
                    api_key, 
					timefrom,
					timeto,
					prefix,
					prior,
                    caller,
					sms_enable,
					sms_text) 
                    VALUES 
                    ('$user_id',
                    '$api_key', 
					'$timefrom',
					'$timeto',
					'$prefix',
					'$prior',
                    '$caller',
					'$sms_enable',
					'$sms_text'
					)";
                $site_db->query($sql);  
            }
            else{
                $sql = "UPDATE ".API_CALLS_TB." SET
					api_key = '$api_key', 
					timefrom = '$timefrom',
					timeto = '$timeto',
					caller = '$caller',
                    prefix = '$prefix',
                    prior = '$prior',
					sms_enable = '$sms_enable',
					sms_text = '$sms_text'"; 
					//WHERE user_id='$user_id'";
                $site_db->query($sql);  
            }			 
			
			if(!mysql_error() && $password_chenge && $current_user_id==$user_id)
			{
				$auth->set_cookie_user_hash($user_hash);
			}
			
			$success = 1;
        }
        // Возвращаем результат
        echo json_encode(array('success' => $success, 'success_msg' =>$success_msg,  'error' => $error));
        
    break;
    
    //ручная постановка задачи на обзвон
    case 'send_api_task': 
        $user_id = value_proc($_POST['user_id']);
        $api_key = value_proc($_POST['api_key']);
        $comment = value_proc($_POST['comment']);
        $timefrom = value_proc($_POST['timefrom']);
        $timeto = value_proc($_POST['timeto']);
        $prior = value_proc($_POST['prior']);
        $caller = mb_strtoupper(value_proc($_POST['caller']));
        $sms_enable = value_proc($_POST['sms_enable']);
        $sms_text = value_proc($_POST['sms_text']);
        
        if(!$user_id)
		{
			exit();
		}
		// Проверка на ошибки
        $error = false;
		if($api_key=='')
		{
			$error['api_key'] = 1;
		}
        //if(preg_match('/\W+/', $comment))
		//{
			//$error['comment'] = 1;
		//}
        if($timefrom < '10:00')
		{
			$error['timefrom'] = 1;
		}
        if($timefrom == '')
		{
			$error['timefrom'] = 2;
		}
        if($timeto > '21:00')
		{
			$error['timeto'] = 1;
		}
        if($timeto == '')
		{
			$error['timeto'] = 2;
		}
        if($timefrom > $timeto)
		{
			$error['timefromto'] = 1;
		}
        if(strlen($caller) > 4)
		{
			$error['caller'] = 1;
		}
        if(preg_match('/\W+/', $caller))
		{
			$error['caller'] = 2;
		}
        if(strlen($caller) == '')
		{
			$error['caller'] = 3;
		}
        if($sms_enable == 1 && $sms_text == '')
		{
			$error['sms_text'] = 1;
		}
        
        // Если ошибок не обнаружено - ставим задачу
		if(!$error)
		{
            $sql = "SELECT * FROM ".API_CALLS_TB;//.";WHERE user_id = ".$current_user_id; //1985-10-03
            $res = $site_db->query_firstrow($sql);
            
            //случайный клиент
			$client_id = strval(mt_rand(1, 320));
            if(!$curl = curl_init()){
               die(); 
            }
            $sms_text = iconv("CP1251", "UTF8//IGNORE", $sms_text);
            $comment = iconv("CP1251", "UTF8//IGNORE", $comment);
            $comment = "HRM API: " . $comment;
            $cfile_wav = new CURLFile("../temp/audio/$user_id/" . 'base.wav','audio/x-wav','10wav');
            $cfile_csv = new CURLFile("../temp/csv/$user_id/" . 'base.csv','mybase1');
            $query = array(
                'comment' => $comment,
                'caller' => $caller,
                'client_id' => $client_id,
                'timefrom' => $timefrom,
                'timeto' => $timeto,
                'prior' => $prior,
                'sleep' => '75',
                'typebase' => 'file',
                'sms_enable' => $sms_enable,
                'sms_text' => $sms_text,
                'email_enable' => '0',
                'email_text' => '',
                'file' => $cfile_csv,
                'range1' => '9260000000',
                'range2' => '9269999999',
                'sound' => $cfile_wav,
                'email_notify' => '',
                'url_notify' => '',
            );

            curl_setopt($curl, CURLOPT_URL, 'https://call.holding.bz/task/save/'.$res['api_key']);//32b748942f69e9e841dc812be6b1e578
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $query); 
            curl_setopt($curl, CURLOPT_ENCODING, '');
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: multipart/form-data',
            ));

            $out = curl_exec($curl);

            if(curl_errno($curl)){
                $msg = curl_error($curl);
            }else{
            $msg = "File upload successfully";
            }

            curl_close($curl);

            $return = array('msg' => $msg);
			
			if(!mysql_error() && $password_chenge && $current_user_id==$user_id)
			{
				$auth->set_cookie_user_hash($user_hash);
			}
			
			$success = 1;
        }
        // Возвращаем результат
        echo json_encode(array('success' => $success, 'success_msg' =>$success_msg,  'error' => $error));
        
    break;
	
	case 'user_remove_from_work':
		
		$user_id = value_proc($_POST['user_id']);
		
		// Начальнику выводим кнопку Отстранить от работы
		if($current_user_id==$user_id || !check_user_access_to_user_content($user_id, array(0,1,0,0,1)))
		{
			exit();
		}
		
		$sql = "SELECT * FROM ".REMOVE_FROM_WORK_TB." WHERE user_id='$user_id'";
		
		$row = $site_db->query_firstrow($sql);
		
		if($row['id'])
		{
			$sql = "DELETE FROM ".REMOVE_FROM_WORK_TB." WHERE user_id='$user_id'";
			
			$site_db->query($sql);
		}
		else
		{
			$sql = "INSERT INTO ".REMOVE_FROM_WORK_TB." (user_id, remove_by_user_id, date) VALUES ('$user_id', '$current_user_id', NOW())";
			
			$site_db->query($sql);
		}
		
		if(!mysql_error())
		{
			$success= 1;
		}
		
		// Кнопка вернуть или отстранить от работы
		$remove_from_work_btn = fill_remove_from_work_btn($user_id);
		
		// Возвращаем результат
		echo json_encode(array('success' => $success, 'remove_from_work_btn' => iconv('cp1251', 'utf-8', $remove_from_work_btn)));
		 
	break;
	
	case 'save_settings_notices':
		
		$user_id = value_proc($_POST['user_id']);
		
		$pars = value_arr_proc($_POST['pars']);
		
		//print_r($pars);
		
		$user_obj->set_user_notice_par($user_id, $pars);
		
		$success = 1;
		
		// Возвращаем результат
		echo json_encode(array('success' => $success));
		
	break;	
}

?>
