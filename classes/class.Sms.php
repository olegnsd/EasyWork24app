<?php
/***
	Отправка СМС-сообщений
	
	Ver: 1.0
	Edited: 26.12.2011
*/
class CSms {
	
	private $sms_login = 'tol271'; // Логин
	
	private $sms_password = 'Qw1905s'; // Пароль
	
	private $sms_to = ''; //Номер получателя в международном формате
	
	private $sms_from = '+74996475461'; //Номер отправителя, максимально доя 11 цифр или лат.  букв
	
	private $sms_coding = '2'; // 0 - латинские символы, 1 - бинарное сообщение, 2 - кириллистические символы
	
	private $sms_text = ''; // Текс сообщения (до 765 латинских симв. или 335 кирилл. симв)
	
	private $sms_priority = 0; //Приоритет сообщения, от 0 до 3
	
	private $sms_mclass = 1; // Класс сообщения, 0 - флеш, 1 - обычное смс 
	
	private $sms_dlrmask = 31; // Уведомление о доставке, 0  - выключен, 31 - включен, по умолчанию 31
	
	private $sms_deferred = 0; // Интервал, после которого следует отправить сообщение в минутах
	
	private $sms_send_result_arr = ''; // Данные результата по отправке запроса на отправку смс-сообщения
	
	public function __construct()
	{
		  
	}
	
	/*
		set, get $sms_send_result_arr
	*/
	public function getSmsSendResultArr()
	{
		return $this->sms_send_result_arr;
	}
	public function setSmsSendResultArr($s)
	{
		$this->sms_send_result_arr = $s;
	}
	
	/*
		set, get $sms_deferred
	*/
	public function getSmsDeferred()
	{
		return $this->sms_deferred;
	}
	public function setSmsDeferred($s)
	{
		$this->sms_deferred = $s;
	}
	
	/*
		set, get $sms_dlrmask
	*/
	public function getSmsDlrmask()
	{
		return $this->sms_dlrmask;
	}
	public function setSmsDlrmask($s)
	{
		$this->sms_dlrmask = $s;
	}
	
	/*
		set, get $sms_mclass
	*/
	public function getSmsMclass()
	{
		return $this->sms_mclass;
	}
	public function setSmsMclass($s)
	{
		$this->sms_mclass = $s;
	}
	
	/*
		set, get $sms_priority
	*/
	public function getSmsPriority()
	{
		return $this->sms_priority;
	}
	public function setSmsPriority($s)
	{
		$this->sms_priority = $s;
	}
	
	/*
		set, get $sms_text
	*/
	public function getSmsText()
	{
		return $this->sms_text;
	}
	public function setSmsText($s)
	{
		$this->sms_text = $s;
	}
	
	/*
		set, get $sms_coding
	*/
	public function getSmsCoding()
	{
		return $this->sms_coding;
	}
	public function setSmsCoding($s)
	{
		$this->sms_coding = $s;
	}
	
	/*
		set, get $sms_from
	*/
	public function getSmsFrom()
	{
		return $this->sms_from;
	}
	public function setSmsFrom($s)
	{
		$this->sms_from = $s;
	}
	
	/*
		set, get $sms_to
	*/
	public function getSmsTo()
	{
		return $this->sms_to;
	}
	public function setSmsTo($s)
	{
		$this->sms_to = $s;
	}
	
	/*
		set, get $sms_login
	*/
	public function getSmsLogin()
	{
		return $this->sms_login;
	}
	public function setSmsLogin($s)
	{
		$this->sms_login = $s;
	}
	
	/*
		set, get $sms_password
	*/
	public function getSmsPassword()
	{
		return $this->sms_password;
	}
	public function setSmsPassword($s)
	{
		$this->sms_password = $s;
	}
	
	// Отправляет запрос на отправу смс сообщения
	//
	public function send_sms()
	{
		// xml смс запроса
		
		$xml_sms_body ='<?xml version="1.0" encoding="windows-1251"?>
						<message>
						<username>'.$this->sms_login.'</username>
						<password>'.$this->sms_password.'</password>
						<from>'.$this->sms_from.'</from>
						<to>'.$this->sms_to.'</to>
						<coding>'.$this->sms_coding.'</coding>
						<dlrmask>'.$this->sms_dlrmask.'</dlrmask>
						<text>'.$this->sms_text.'</text>
						</message>';
		
		
		//инициализируем сеанс
		$curl = curl_init();
		 
		//уcтанавливаем урл, к которому обратимся
		curl_setopt($curl, CURLOPT_URL, 'https://www.stramedia.ru/modules/xml_send_sms.php');
		 
		//включаем вывод заголовков
		curl_setopt($curl, CURLOPT_HEADER, 0);
		 
		//передаем данные по методу post
		curl_setopt($curl, CURLOPT_POST, 1);
		 
		//теперь curl вернет нам ответ, а не выведет
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		 
		curl_setopt($curl, CURLOPT_HTTPHEADER, 
        array('Content-Type: text/xml; charset=utf-8', 
              'Content-Length: '.strlen($xml_sms_body)));
			  
		curl_setopt($curl, CURLOPT_POSTFIELDS, $xml_sms_body);
			  
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		 
		$result = curl_exec($curl);
		 
		curl_close($curl);
		
		// заносим в параметр результат выполнения запроса
		$this->sms_send_result_arr = $result;
	}
	
	// Результат выполнения запроса на отправку смс-сообщения 
	public function get_responce_send_text()
	{
		// Данные по ошибкам
		$array_errors = array("Error: Invalid xml" => "Проверьте наличие всех необходимых параметров в запросе",
		"Error: Invalid username or password or user is blocked" => "Проверьте логин и пароль и то, что ваш логин не заблокирован",
		"Error: Invalid or missing 'from' address" => "Проверьте наличие и формат номера получателя", 
		"Error: Invalid or missing 'to' address" => "Проверьте наличие и длину адреса отправителя",
		"Error: Invalid or missing coding" => "Проверьте наличие и значение параметра coding",
		"Error: Missing text" => "Проверьте наличие параметра text",
		"Error: Text too long" => "Проверьте длину параметра text", 
		"Error: IP not allowed" => "Ваш IP блокирован, обратитесь к администратору системы",
		"Error: Max limit exceeded" => "Вы достигли максимального числа sms, обратитесь к администратору системы", 
		"Error: Insufficient balance" => "У вас не достаточный баланс");
		
		if(preg_match('/Success: Message accepted for sending/', $this->sms_send_result_arr))
		{
			return 'succuss';
		}
		 
		// Массив данных по отправке запроса
		$result_arr = new SimpleXMLElement($this->sms_send_result_arr);
		
		// Результат выполнения
		$result_text = trim($result_arr->text);
		 
		//Ошибка
		return $array_errors[$result_text];
		 
	}
	
	// Проверка статуса отправленного смс-сообщения
	// in - id sms сообщения, code(в каком виде возвратить,0-код, 1-текст)
	public function get_sms_status($sms_id, $code=0)
	{
		// xml смс запроса
		$xml_sms_status_body ='<?xml version="1.0" encoding="windows-1251"?>
						<message>
						<username>'.$this->sms_login.'</username>
						<password>'.$this->sms_password.'</password>
						<id>'.$sms_id.'</id>
						</message>';
		
		$host = 'www.stramedia.ru';
		
 		$fp = fsockopen($host, 80);
		
		fputs($fp, "POST https://www.stramedia.ru/modules/xml_sms_sta HTTP/1.1\r\n");
		fputs($fp, "Host: $host\r\n");
		fputs($fp, "Content-type: text/xml; charset=utf-8\r\n");
		fputs($fp, "Content-length: ". strlen($xml_sms_status_body) ."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $xml_sms_status_body);
		
		$result = ''; 
		
		// Ответ
		while(!feof($fp))
		{
			$result .= fgets($fp, 128);
		}
		
		// Массив данных по отправке запроса
		$result_status_arr = new SimpleXMLElement($result);
		
		// Статусы
		$array_status = array('0' => 'Сообщение передано шлюзу',
							  '1' => 'Успешно доставлено до получателя',
							  '2' => 'Аппарат получателя отклонил SMS',
							  '4' => 'Сообщение в очереди у оператора связи',
							  '8' => 'Оператор связи принял SMS',
							  '16' => 'Оператор связи отклонил SMS',
							  '32' => 'Шлюз отклонил SMS');
							  
		$result_status_arr->status;
		
		// Возвращаем статус доставки в виде кода
		if($code==0)
		{
			return 	$result_status_arr->status;	
		}
		// Возвращаем статус доставки в удобмном виде
		if($code==1)
		{
			return 	$array_status[$result_status_arr->status];	
		}
				  
	}
	
	// Перевод сообщения в транслит
	// in - текст
	public function  translit_sms_text($text)
	{
		$tr = array(
        "а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"zh",
        "з"=>"z","и"=>"i","й"=>"i","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
        "ы"=>"y","ь"=>"","э"=>"je","ю"=>"ju","я"=>"ja");
		
    	return strtr($text,$tr);
	}

	// Проверяет телефон на корректность
	// in - номер телефона
	public function check_phone_number($phone)
	{
		// Длина номера телефона
		$phone_length = strlen($phone);
		
		// Если числовое значение и необходимая длина
		if(is_numeric($phone) && $phone_length==11)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

?>