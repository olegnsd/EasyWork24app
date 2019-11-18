<?php
/***
	Пользователь
	
	Ver: 1.0
	Edited: 5.12.2012
*/
// Авторизация, привилегии пользователей
class CUser
{
	private $user_id = ''; // ID пользователя в базе
	
	private $user_name = ''; // Имя
	
	private $user_surname = ''; // Фамилия
	
	private $user_middlename = ''; // Отчество
	
	private $user_position = ''; // Актальаня должность
	
	private $user_positions_arr = ''; // Массив должностей начиная с первой
	
	private $user_phone = ''; // Телефон
	
	private $user_login = ''; // Логин
	
	private $user_job_id = ''; // ID, рандомное значение
	
	private $user_image = ''; // фотография пользователя
	
	private $user_bdate = ''; // День рождения
	
	private $user_registration_date = ''; // дата регистрации в системе
	
	private $user_registrated_by_user_id = ''; // Пользователь, который зарегистрировал в системе
	
	private $user_registration_privilege = ''; // Может ли пользователь регистрировать сотрудников
	
	private $user_last_visit_date = ''; // Последняя активность пользователя на сайте
	
	private $user_removed_from_work = 0; // Флаг, пользователь отстранен от работы
	
	private $user_auth_method = ''; // Метод авторизации в системе
	
	private $user_sex = ''; // Пол пользователя
	
	private $is_admin = ''; // Администратор
	
	private $is_fired = ''; // Уволен
	
	private $user_limitation = ''; // Ограничения общения
	
	private $user_data = ''; // Ограничения общения
	
	private $user_is_dept_head = 0; // является ли руководителем отдела
	/*
		Tables
	*/
	
	private $db;
	
	public function __construct($db)
	{
		$this->db = $db;
		
	}
	
	public function get_user_is_dept_head()
	{
		return $this->user_is_dept_head;
	}
	
	public function get_user_data()
	{
		return $this->user_data;
	}
	
	public function get_user_limitation()
	{
		return $this->user_limitation;
	}
	
	public function get_is_fired()
	{
		return $this->is_fired;
	}
	
	public function get_is_admin()
	{
		return $this->is_admin;
	}
	
	/*
		set, get $user_sex
	*/
	public function get_user_sex()
	{
		return $this->user_sex;
	}
	public function set_user_sex($s)
	{
		$this->user_sex= $s;
	}
	
	/*
		set, get $user_bdate
	*/
	public function get_user_bdate($what)
	{
		//echo $this->user_bdate['date_rus'];
		return $this->user_bdate[$what];
	}
	public function set_user_bdate($s)
	{
		$this->user_bdate= $s;
	}
	
	/*
		set, get $user_auth_method
	*/
	public function get_user_auth_method()
	{
		return $this->user_auth_method;
	}
	public function set_user_auth_method($s)
	{
		$this->user_auth_method= $s;
	}
	
	/*
		set, get $user_removed_from_work
	*/
	public function get_user_removed_from_work()
	{
		return $this->user_removed_from_work;
	}
	public function set_user_removed_from_work($s)
	{
		$this->user_removed_from_work= $s;
	}
	
	/*
		set, get $user_last_visit_date
	*/
	public function get_user_last_visit_date()
	{
		return $this->user_last_visit_date;
	}
	public function set_user_last_visit_date($s)
	{
		$this->user_last_visit_date= $s;
	}
	
	/*
		set, get $user_registration_privilege
	*/
	public function get_user_registration_privilege()
	{
		return $this->user_registration_privilege;
	}
	public function set_user_registration_privilege($s)
	{
		$this->user_registration_privilege= $s;
	}
	
	/*
		set, get $user_positions_arr
	*/
	public function get_user_positions_arr()
	{
		return $this->user_positions_arr;
	}
	public function set_user_positions_arr($s)
	{
		$this->user_positions_arr= $s;
	}
	
	/*
		set, get $user_registration_date
	*/
	public function get_user_registration_date()
	{
		return $this->user_registration_date;
	}
	public function set_user_registration_date($s)
	{
		$this->user_registration_date= $s;
	}
	
	/*
		set, get $user_registrated_by_user_id
	*/
	public function get_user_registrated_by_user_id()
	{
		return $this->user_registrated_by_user_id;
	}
	public function set_user_registrated_by_user_id($s)
	{
		$this->user_registrated_by_user_id= $s;
	}
	
	/*
		set, get $user_image
	*/
	public function get_user_image()
	{
		return $this->user_image;
	}
	public function set_user_image($s)
	{
		$this->user_image= $s;
	}
	
	/*
		set, get $user_job_id
	*/
	public function get_user_job_id()
	{
		return $this->user_job_id;
	}
	public function set_user_job_id($s)
	{
		$this->user_job_id= $s;
	}
	
	/*
		set, get $user_login
	*/
	public function get_user_login()
	{
		return $this->user_login;
	}
	public function set_user_login($s)
	{
		$this->user_login= $s;
	}
	
	
	/*
		set, get $user_phone
	*/
	public function get_user_phone()
	{
		return $this->user_phone;
	}
	public function set_user_phone($s)
	{
		$this->user_phone= $s;
	}
	
	
	/*
		set, get $user_position
	*/
	public function get_user_position()
	{
		return $this->user_position;
	}
	public function set_user_position($s)
	{
		$this->user_position= $s;
	}
	
	/*
		set, get $user_middlename
	*/
	public function get_user_middlename()
	{
		return $this->user_middlename;
	}
	public function set_user_middlename($s)
	{
		$this->user_middlename= $s;
	}
	
	/*
		set, get $user_surname
	*/
	public function get_user_surname()
	{
		return $this->user_surname;
	}
	public function set_user_surname($s)
	{
		$this->user_surname= $s;
	}
	
	/*
		set, get $user_name
	*/
	public function get_user_name()
	{
		return $this->user_name;
	}
	public function set_user_name($s)
	{
		$this->user_name= $s;
	}
	
	/*
		set, get $user_id
	*/
	public function get_user_id()
	{
		return $this->user_id;
	}
	public function set_user_id($s)
	{
		$this->user_id= $s;
	}
	

	/*
		Заполнение объекта пользователя
	*/
	public function fill_user_data($user_id)
	{
		$sql = "SELECT * FROM ".USERS_TB." WHERE user_id='$user_id'";
		
		$row=$this->db->query_firstrow($sql);
		
		$this->user_data = $row;
		
		$this->user_id = $user_id;
		
		$this->user_name = $row['user_name'];
		
		$this->user_surname = $row['user_surname'];
		
		$this->user_middlename = $row['user_middlename'];
		
		$this->user_phone = $row['user_phone'];
		
		$this->user_login = $row['user_login'];
		
		$this->user_job_id = $row['user_job_id'];
		
		$this->user_registrated_by_user_id = $row['registrated_by_user_id'];
		
		$this->user_registration_date = $row['user_registration_date'];
		
		$this->user_registration_privilege = $row['user_registration_privilege'];
		
		$this->user_last_visit_date = $row['user_last_visit_date'];
		
		$this->user_auth_method = $row['user_auth_method'];
		
		$this->user_sex = $row['user_sex'];
		
		$this->is_admin = $row['is_admin'];
		
		$this->is_fired = $row['is_fired'];
		
		$this->user_limitation = $row['user_limitation'];
		
		$this->user_bdate = array('date' => datetime($row['user_bdate'], '%Y-%m-%d'), 'date_rus' => datetime($row['user_bdate'], '%d %F'));
		
		// Выбираем последнюю должность пользователя
		$sql = "SELECT position_name FROM ".USERS_POSITIONS_TB." WHERE user_id='$user_id' ORDER BY position_id DESC LIMIT 1";
		
		$row=$this->db->query_firstrow($sql);
		
		$this->user_position = $row['position_name'];
		
		// Выбираем все должности пользователя
		$sql = "SELECT position_name, position_date FROM ".USERS_POSITIONS_TB." WHERE user_id='$user_id' ORDER BY position_id ASC";
		
		$res=$this->db->query($sql);
		
		while($row =  $this->db->fetch_array($res))
		{
			$user_positions_arr[] = array('name' => $row['position_name'], 'date' => $row['position_date']);	
		}
		
		$this->user_positions_arr = $user_positions_arr;
		
		
		// Выбираем изображение пользователя
		$sql = "SELECT * FROM ".USER_IMAGES_TB." WHERE user_id='$user_id'";
		
		$row=$this->db->query_firstrow($sql);
		
		$this->user_image = $row;
		
		include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions_org.php';
		// является ли пользователем руководителем отдела
		$user_is_dept_head = user_is_dept_head($user_id);
		
		$this->user_is_dept_head = $user_is_dept_head ? 1 : 0;
	}
	
	function get_user_data_par($data, $par)
	{
		$data_arr = unserialize($data);
		//print_r($data_arr);
		return $data_arr[$par];
	}
	
	function set_user_data_par($user_id, $par_data)
	{		 
		// выбор поля даты у пользователя
		$sql = "SELECT data FROM ".USERS_TB." WHERE user_id='$user_id'";
		
		$row = $this->db->query_firstrow($sql);
		
		if($row['data'])
		{
			$user_data = unserialize($row['data']);
		}
		else
		{
			$user_data = array();
		}
		
		$user_data[key($par_data)] = current($par_data);
		
		$serialize_data = serialize($user_data);
		
		// обновляем дату
		$sql = "UPDATE ".USERS_TB." SET data = '$serialize_data' WHERE user_id='$user_id'";
		
		$this->db->query($sql);
		 
	}
	
	function set_user_notice_par($user_id, $par_data)
	{		 
		// выбор поля даты у пользователя
		$sql = "SELECT notices FROM ".USERS_TB." WHERE user_id='$user_id'";
		
		$row = $this->db->query_firstrow($sql);
		
		if($row['notices'])
		{
			$user_data = unserialize($row['notices']);
		}
		else
		{
			$user_data = array();
		}
		
		foreach($par_data as $key => $value)
		{
			$user_data[$key] = $value;
		}
		
		$serialize_data = serialize($user_data);
		
		// обновляем дату
		$sql = "UPDATE ".USERS_TB." SET notices = '$serialize_data' WHERE user_id='$user_id'";
		
		$this->db->query($sql);
		 
	}
	
	// параметр уведомления пользователя
	function get_user_notice_par($user_id, $par)
	{
		$sql = "SELECT * FROM ".USERS_TB." WHERE user_id='$user_id'";
		
		$row=$this->db->query_firstrow($sql);
		
		$data_arr = unserialize($row['notices']);
		 
		return $data_arr[$par];
	}
	
	
}


?>
