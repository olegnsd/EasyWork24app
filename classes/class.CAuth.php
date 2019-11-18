<?php
/***
	Авторизация пользователей
	
	Ver: 1.0
	Edited: 29.11.2012
*/
// Авторизация, привилегии пользователей
class CAuth 
{
	private $auth_login; // Логин
	
	private $auth_password; // Пароль
	
	private $auth_sms_code; // Sms код
	
	private $auth_success = 0; // Успешность авторизации
	
	private $auth_remember = 0; // запомнить меня
	
	private $auth_searched_user_id = 0; // Найденный user_id по логину
	
	private $auth_user_data = array(); // Данные найденного пользователя по логину
	
	private $auth_method_proc = ''; // Переданный от js параметр метода авторизации
	
	private $auth_method = 0; // Метод авторизации пользователя
	
	private $restore_by_sms_code = 0; // Восстановление доступа к системе через смс
	
	private $auth_error = ''; // Ошибки
	
	private $auth_fail_time = 3600;
	private $auth_fail_iter_time = 350;
	
	/*
		Tables
	*/
	
	private $usersTb = USERS_TB; // таблица юзеров
	
	private $db;
	
	public function __construct($db)
	{
		$this->db = $db;
		
	}
	
	/*
		set, get $restore_by_sms_code
	*/
	public function get_restore_by_sms_code()
	{
		return $this->restore_by_sms_code;
	}
	public function set_restore_by_sms_code($s)
	{
		$this->restore_by_sms_code= $s;
	}
	
	/*
		set, get $auth_user_data
	*/
	public function get_auth_user_data()
	{
		return $this->auth_user_data;
	}
	public function set_auth_user_data($s)
	{
		$this->auth_user_data= $s;
	}
	
	/*
		set, get $auth_method_proc
	*/
	public function get_auth_method_proc()
	{
		return $this->auth_method_proc;
	}
	public function set_auth_method_proc($s)
	{
		$this->auth_method_proc= $s;
	}
	
	/*
		set, get $auth_method
	*/
	public function get_auth_method()
	{
		return $this->auth_method;
	}
	public function set_auth_method($s)
	{
		$this->auth_method= $s;
	}
	
	/*
		set, get $auth_sms_code
	*/
	public function get_auth_sms_code()
	{
		return $this->auth_sms_code;
	}
	public function set_auth_sms_code($s)
	{
		$this->auth_sms_code= $s;
	}
	
	/*
		set, get $auth_searched_user_id
	*/
	public function get_auth_searched_user_id()
	{
		return $this->auth_searched_user_id;
	}
	public function set_auth_searched_user_id($s)
	{
		$this->auth_searched_user_id= $s;
	}
	
	/*
		set, get $auth_login
	*/
	public function get_auth_remember()
	{
		return $this->auth_remember;
	}
	public function set_auth_remember($s)
	{
		$this->auth_remember= $s;
	}
	
	/*
		set, get $auth_login
	*/
	public function get_auth_login()
	{
		return $this->auth_login;
	}
	public function set_auth_login($s)
	{
		$this->auth_login= $s;
	}
	
	/*
		set, get $auth_password
	*/
	public function get_auth_password()
	{
		return $this->auth_password;
	}
	public function set_auth_password($s)
	{
		$this->auth_password = $s;
	}
	
	/*
		set, get $auth_success
	*/
	public function get_auth_success()
	{
		return $this->auth_success;
	}
	public function set_auth_success($s)
	{
		$this->auth_success = $s;
	}
	
	/*
		set, get $auth_error
	*/
	public function get_auth_error()
	{
		return $this->auth_error;
	}
	public function set_auth_error($s)
	{
		$this->auth_error = $s;
	}

	/*
		Вывод шаблона для авторизации
		out - шаблондля авторизации
	*/
	public function no_auth()
	{
		$html_tpl = file_get_contents('templates/html.tpl');
		
		$no_auth_tpl = file_get_contents('templates/no_auth.tpl');
		
		$where = array('{CONTENT}', '{LOGIN}');
	
		$what = array($no_auth_tpl, $login);
	
		$html = str_replace($where, $what, $html_tpl);
		
		return $no_auth_tpl;
	}

	/*
		Проверка авторизации
		out - true or false
	*/
	public function check_auth()
	{
		 
		// Ищем по кукам
		if($_COOKIE['ews_uid'] && $_COOKIE['ews_hash'])
		{
			$sql = "SELECT user_id, user_login, user_hash, user_activated FROM ".$this->usersTb." WHERE user_id='".$_COOKIE['ews_uid']."'";
			 
			$r=$this->db->query_firstrow($sql);
			
			//if($_COOKIE['ews_hash']==$r['user_hash'] && $r['user_activated']=='1')
			if($_COOKIE['ews_hash']==$r['user_hash'])
			{
				$_SESSION['user_id'] = $r['user_id'];
				
				return true;
			}
			else
			{ 
				$this->clear_auth_session();
				return false;
			} 
		}
		else
		{
			$this->clear_auth_session();
			return false;
		}
		
	}

	public function clear_auth_session()
	{
		session_unset();
		setcookie('ews_uid','');
		setcookie('ews_hash','');
	}
	
	// Устанавливает в параметр временные данные пользователя найденного по логину
	public function set_auth_user_data_by_user_login($user_id, $login)
	{
		// Проверяем, вводит ли пользователь телефон или нет
		if(preg_match('/^\+[0-9]{10}+/is', $login))
		{
			$or_by_user_phone = " OR user_phone = '$login' ";
		}
		else if(strlen($login)==10)
		{
			$user_phone = '+7'.$login;
		}
		else if(preg_match('/^\+|^[0-9]+/is', $login))
		{
			// Делаем проверка на телефон
			$user_phone = substr($login,strlen($login)-7,strlen($login));
		}
		
		if($user_phone)
		{
			$or_by_user_phone = " OR user_phone = '$user_phone' ";
		}
		
		// Находим юзера
		$sql = "SELECT * FROM ".$this->usersTb." WHERE user_login = '$login' $or_by_user_phone";
		 
		$r=$this->db->query_firstrow($sql);
		
		$this->auth_user_data = $r;
		
	}
	
	// Метод выявления метода авторизации
	public function pre_auth_proc()
	{	
		// Логин
		$login = $this->auth_login;
		
		// Устанавливаем данные пользователя, которого находим по логину
		$this->set_auth_user_data_by_user_login(0, $login);
		
		$auth_user_data = $this -> get_auth_user_data();
		
		
		if($login=='')
		{
			$this->auth_error = 'Введите логин!';
			return '';
		}
		if($auth_user_data['is_fired'])
		{
			$this->auth_error = 'Вы отстранены от работы!';
			return '';
		}
		if(!$auth_user_data['user_id'])
		{
			$this->auth_error = 'Введенный логин отсутствует в базе!';
			return '';
		}
		
		 
		// 2 этап авторизации. Процесс авторизации
		else if($this->auth_method_proc!='')
		{
			$this->auth_proc();
		}
		// Требуется авторизация аккаунта
		/*else if($auth_user_data['user_activated']=='0')
		{
			$this->send_auth_sms_code('activation');
			$this->auth_method = 'activation';
		}*/
		else if($this->restore_by_sms_code==1)
		{
			$sms_result = $this->send_auth_sms_code('by_sms');
			$this->auth_method = 'restore_by_sms_code';
		}
		// Обычная авторизация через пароль
		else if($auth_user_data['user_auth_method']=='0')
		{
			$this->auth_method = 'by_pass';
		}
		// Авторизация посредством смс
		else if($auth_user_data['user_auth_method']=='1')
		{
			$sms_result = $this->send_auth_sms_code('by_sms');
			$this->auth_method = 'by_sms';
		}
		// Авторизация с использованием и пароля и смс кода
		else if($auth_user_data['user_auth_method']=='2')
		{
			$sms_result = $this->send_auth_sms_code('by_sms');
			$this->auth_method = 'by_pass_and_sms';
		}

	}
	
	/*
		Авторизация пользователя
	*/
	public function auth_proc()
	{
		// Логин
		$login = $this->auth_login;
		 
		// Пароль
		$password = $this->auth_password;
		 
		// Смс код
		$sms_code = $this->auth_sms_code;
		 
		if(!$login)
		{ 
			$this->auth_error = 'Не введен логин!';
			return '';
		}
		
		// Данные пользователя
		$auth_user_data = $this -> get_auth_user_data();
		

		// Требуется активация аккаунта
		/*if(!$auth_user_data['user_activated'])
		{
			if($sms_code == $auth_user_data['user_auth_sms_code'])
			{
				// Если пользователь не был активирован
				if($auth_user_data['user_activated']==0)
				{
					$sql = "UPDATE ".USERS_TB." SET user_activated=1 WHERE user_id='".$auth_user_data['user_id']."'";
					
					$this->db->query($sql);
				}
				// Подтверждаем авторизацию
				$this->user_auth_success();
			}
			else
			{
				$this->auth_error = 'Вы неверно указали код!';
			}
		}*/
		
		if($this->check_auth_for_max_iter($auth_user_data['user_id']))
		{
			$this->auth_error = 'Достигнуто максимальное число попыток. Попробуйте позже.';
		}
		else if($this->restore_by_sms_code==1)
		{
			if($sms_code == $auth_user_data['user_auth_sms_code'])
			{
				// Подтверждаем авторизацию
				$this->user_auth_success();
			}
			else
			{
				$this->auth_error = 'Вы неверно указали код!';
				$this->set_user_auth_iter($auth_user_data['user_id']);
			}
		}
		// Обычная авторизация через пароль
		else if($auth_user_data['user_auth_method'] == '0')
		{
			$password_hash = password_hash_proc($password);
			
			if($auth_user_data['user_id'] && $auth_user_data['user_password'] == $password_hash)
			{
				// Подтверждаем авторизацию
				$this->user_auth_success();
			}
			else
			{
				$this->auth_error = 'Вы неверно указали пароль!';
				
				$this->set_user_auth_iter($auth_user_data['user_id']);
			}
		}
		//Авторизация посредством смс
		else if($auth_user_data['user_auth_method'] == '1')
		{
			if($sms_code == $auth_user_data['user_auth_sms_code'])
			{
				// Подтверждаем авторизацию
				$this->user_auth_success();
			}
			else
			{
				$this->auth_error = 'Вы неверно указали код!';
				
				$this->set_user_auth_iter($auth_user_data['user_id']);
			}
		}
		//Авторизация с использованием пароля и посредством смс
		else if($auth_user_data['user_auth_method'] == '2')
		{
			$password_hash = password_hash_proc($password);
			
			if($sms_code == $auth_user_data['user_auth_sms_code'] && $auth_user_data['user_password'] == $password_hash)
			{
				// Подтверждаем авторизацию
				$this->user_auth_success();
			}
			else if($sms_code != $auth_user_data['user_auth_sms_code'] && $auth_user_data['user_password'] != $password_hash)
			{
				$this->auth_error = 'Вы неверно указали пароль и смс-код!';
				$this->set_user_auth_iter($auth_user_data['user_id']);
			}
			else if($sms_code == $auth_user_data['user_auth_sms_code'] && $auth_user_data['user_password'] != $password_hash)
			{
				$this->auth_error = 'Вы неверно указали пароль!';
				$this->set_user_auth_iter($auth_user_data['user_id']);
			}
			else if($sms_code != $auth_user_data['user_auth_sms_code'] && $auth_user_data['user_password'] == $password_hash)
			{
				$this->auth_error = 'Вы неверно указали смс-код!';
				$this->set_user_auth_iter($auth_user_data['user_id']);
			}
		}

	}
	
	 
	
	private function check_auth_for_max_iter($user_id)
	{
		$sql = "SELECT * FROM tasks_users WHERE user_id='$user_id'";
		
		$row=$this->db->query_firstrow($sql);
		
		$razn = time() - to_mktime($row['auth_last_iter_date']);
		
		if($row['auth_iter']>=5 && $razn < $this->auth_fail_time)
		{
			return true;
		}
		
	}
	
	private function set_user_auth_iter($user_id, $to_clear)
	{
		$actual_date = date('Y-m-d H:i:s');
		
		$sql = "SELECT * FROM tasks_users WHERE user_id='$user_id'";
		
		$row=$this->db->query_firstrow($sql);
		
		$razn = time() - to_mktime($row['auth_last_iter_date']);
		
		if($to_clear)
		{
			$sql = "UPDATE tasks_users SET auth_iter = 0, auth_last_iter_date='0' WHERE user_id='$user_id'";
		
			$this->db->query($sql);
		}
		// Если вышло время между попытками, то сбрасываем счетчик
		else if($row['auth_iter'] > 0 && $razn > $this->auth_fail_iter_time)
		{
			$sql = "UPDATE tasks_users SET auth_iter = 1, auth_last_iter_date='$actual_date' WHERE user_id='$user_id'";
		
			$this->db->query($sql);
		}
		else
		{
			// увеличиваем счетчик неудачных попыток ввода пароля
			$sql = "UPDATE tasks_users SET auth_iter = auth_iter + 1, auth_last_iter_date='$actual_date' WHERE user_id='$user_id'";
		
			$this->db->query($sql);
		}
		
		
		 
	}
	
	// Успешное завершение авторизации
	private function user_auth_success()
	{
		$auth_user_data = $this -> get_auth_user_data();
		
		if(!$auth_user_data['user_hash'])
		{
			// Хеш идентификатор пользователя
			$user_hash = password_hash_proc(generate_rand_string(60));
		
			// Добавляем хеш идентификатор
			$sql = "UPDATE ".$this->usersTb." SET user_hash='".$user_hash."' WHERE user_id='".$auth_user_data['user_id']."'";
		
			$this->db->query($sql);
		}
		else
		{
			$user_hash = $auth_user_data['user_hash'];
		}
		 
		// Сбрасываем одноразовый пароль авторизации
		$sql = "UPDATE ".USERS_TB." SET user_auth_sms_code='' WHERE user_id='$searched_user_id'";
		$this->db->query($sql);
		
		// Очищаем запись в сессии последней активности пользователя на сайте
		$_SESSION['last_user_activity_datetime'] = '';
	 
		$this->set_cookie_uid($auth_user_data['user_id']);
		$this->set_cookie_user_hash($user_hash);

		$this->auth_success = 1;
		
		$this->set_user_auth_iter($auth_user_data['user_id'], 1);
	}
	
	public function set_cookie_uid($uid)
	{
		setcookie("ews_uid", $uid, time() + 604800, "/");
	}
	public function set_cookie_user_hash($user_hash)
	{
		setcookie("ews_hash", $user_hash, time() + 604800, "/");
	}
	
	// Отправка смс кода
	private function send_auth_sms_code($method='')
	{       global $system_sms;
                $system_sms=1;
		// найденный пользователь по логину
		$auth_user_data = $this -> get_auth_user_data();
		
		// Генерируем одноразовый пароль для авторизации
		$sms_auth_pass = rand(1000,9999);
		
		// Проверяем последнюю дату запроса на смс оповещение
		$sql = "SELECT last_sms_code_date FROM ".$this->usersTb." WHERE user_id='".$auth_user_data['user_id']."'";
		
		$time_row=$this->db->query_firstrow($sql);
		
		$time_in_mktime = to_mktime($time_row['last_sms_code_date']);
		
		$seconds_left = time() - $time_in_mktime;
		
		// Время до повторной отправки смс кода
		$time_left = 300;
		
		// Если повтораня отправка смс оповещения еще не разрешена
		if($seconds_left < $time_left)
		{
			$time = $time_left - $seconds_left;
			$time = $time.' '.numToword($time, array('секунду', 'секунды', 'секунд'));
			
			$this->auth_error = 'Повторный запрос возможен через '.$time.'';
			return array('success' => 0, 'error' => 1, 'seconds_left' => $seconds_left);
		}
		
		// Обновляем одноразовый пароль для авторизации пользователя
		$sql = "UPDATE ".$this->usersTb." SET user_auth_sms_code='$sms_auth_pass', last_sms_code_date=NOW() WHERE user_id='".$auth_user_data['user_id']."'";
		 
		$this->db->query($sql);
	
		
		### sms body
		// Для активации аккаунта
		if($method=='activation')
		{
			$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/auth_code.tpl');
		}
		else if($method=='by_sms')
		{
			$sms_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/sms/auth_pass.tpl');
		}
		
		$user_phone = $auth_user_data['user_phone'];			 
		
		$PARS['{SMS_CODE}'] = $sms_auth_pass;
					 
		$sms_text = fetch_tpl($PARS, $sms_tpl);
		###\ sms body
					
		// Отправка смс сообщения
		send_sms_msg($user_phone, $sms_text, 1);
		
		$success = 1;
		
		return array('success' => $success);

	}
	
	// Выход авторизованного пользователя
	public function auth_exit()
	{
		// Добавляем хеш идентификатор
		$sql = "UPDATE ".$this->usersTb." SET user_hash='' WHERE user_id='".$_SESSION['user_id']."'";
			
		$this->db->query($sql);
		
		$this->clear_auth_session();
		
		header('Location: /auth');
		
	}
	
	public function get_current_user_id()
	{
		return $_SESSION['user_id'];
	}
	
}


?>
