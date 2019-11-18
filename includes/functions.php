<?php
// Заполняет шаблон переменными
function fetch_tpl($par, $template)
{
	foreach($par as $key => $val) 
	{
		$where[] = $key;
			 
		$what[] = $val;
	}
	return str_replace($where, $what , $template);

}


 
function seconds_to_date_min_rus($seconds, $mode=0)
{
	$mon_c[1]="янв";
	$mon_c[2]="фев";
	$mon_c[3]="март";
	$mon_c[4]="апр";
	$mon_c[5]="май";
	$mon_c[6]="июнь";
	$mon_c[7]="июль";
	$mon_c[8]="авг";
	$mon_c[9]="сент";
	$mon_c[10]="окт";
	$mon_c[11]="нояб";
	$mon_c[12]="дек";
	
	if($mode==1)
	{
		$date_time_tmp = split(' ', $date);
		
		$tmp_date = split('-', $date_time_tmp[0]);
			
		$formate_date = (int)date('d', $seconds).' '.$mon_c[(int)date('m', $seconds)];
	}
	
	return $formate_date;
}

// Преобразует дату и время к формату "25 мая 2012"
function formate_date_rus($date='', $mode=0, $mon_arr=1)
{
	if(preg_match('/0000/', $date))
	{
		return '';
	}
	
	switch($mon_arr)
	{
		case '1':
			$mon_c[1]="января";
			$mon_c[2]="февраля";
			$mon_c[3]="марта";
			$mon_c[4]="апреля";
			$mon_c[5]="мая";
			$mon_c[6]="июня";
			$mon_c[7]="июля";
			$mon_c[8]="августа";
			$mon_c[9]="сентября";
			$mon_c[10]="октября";
			$mon_c[11]="ноября";
			$mon_c[12]="декабря";
		break;
		case '2':
			$mon_c[1]="янв";
			$mon_c[2]="фев";
			$mon_c[3]="март";
			$mon_c[4]="апр";
			$mon_c[5]="мая";
			$mon_c[6]="июня";
			$mon_c[7]="июля";
			$mon_c[8]="авг";
			$mon_c[9]="сент";
			$mon_c[10]="окт";
			$mon_c[11]="нояб";
			$mon_c[12]="дек";
		break;
	}
	
	switch($mode)
	{
		// 0 = 8 декабря 2012 в 18:34
		case '0':
			$date_time_tmp = split(' ', $date);
			$tmp_date = split('-', $date_time_tmp[0]);
			
			$formate_date = (int)$tmp_date[2].' '.$mon_c[(int)$tmp_date[1]].' '.$tmp_date[0].' в '.substr($date_time_tmp[1],0,5);
		break;
		// 1 = 8 декабря 2012
		case '1':
			$date_time_tmp = split(' ', $date);
			$tmp_date = split('-', $date_time_tmp[0]);
			
			$formate_date = (int)$tmp_date[2].' '.$mon_c[(int)$tmp_date[1]].' '.$tmp_date[0];
		break;
		// 2 = 8 декабря
		case '2':
			$date_time_tmp = split(' ', $date);
			$tmp_date = split('-', $date_time_tmp[0]);
			
			$formate_date = (int)$tmp_date[2].' '.$mon_c[(int)$tmp_date[1]];
		break;
	}
	
	
	return $formate_date;
}
// Форматирование времени

function datetime($date, $formate='%Y-%m-%d %H-%i-%s', $in_mktime=0)
{
	if($in_mktime || is_numeric($date))
	{
		$date = date('Y-m-d H:i:s', $date);
	}
	
	if(preg_match('/0000/', $date))
	{
		return '';
	} 
	
	$mon_rf[1]="января";
	$mon_rf[2]="февраля";
	$mon_rf[3]="марта";
	$mon_rf[4]="апреля";
	$mon_rf[5]="мая";
	$mon_rf[6]="июня";
	$mon_rf[7]="июля";
	$mon_rf[8]="августа";
	$mon_rf[9]="сентября";
	$mon_rf[10]="октября";
	$mon_rf[11]="ноября";
	$mon_rf[12]="декабря";

	$mon_rm[1]="янв";
	$mon_rm[2]="фев";
	$mon_rm[3]="март";
	$mon_rm[4]="апр";
	$mon_rm[5]="мая";
	$mon_rm[6]="июня";
	$mon_rm[7]="июля";
	$mon_rm[8]="авг";
	$mon_rm[9]="сент";
	$mon_rm[10]="окт";
	$mon_rm[11]="нояб";
	$mon_rm[12]="дек";
		 
	$weekday_c[0]="воскресенье";
	$weekday_c[1]="понедельник";
	$weekday_c[2]="вторник";
	$weekday_c[3]="среда";
	$weekday_c[4]="четверг";
	$weekday_c[5]="пятница";
	$weekday_c[6]="суббота";
		
	$tmp_date = split(' ', $date);
	$date_arr = split('-', $tmp_date[0]);
	$time_arr = split(':', $tmp_date[1]);
	
	// 'Y-m-d-H-i-s'
	 
	// Год
	$PARS['%Y'] = $date_arr[0];
	$PARS['%y'] = substr($date_arr[0],2);
	
	// Месяц 
	$PARS['%F'] = $mon_rf[(int)$date_arr[1]];
	$PARS['%M'] = $mon_rm[(int)$date_arr[1]];
	$PARS['%m'] = strlen($date_arr[1]) < 2 ? '0'.$date_arr[1] : $date_arr[1];
	$PARS['%n'] = (int)$date_arr[1]; 
	
 	// День
	$PARS['%d'] = strlen($date_arr[2]) < 2 ? '0'.$date_arr[2] : $date_arr[2];
	$PARS['%j'] = (int)$date_arr[2];
	$PARS['%l'] = $weekday_c[date('w', $date_mktime)];
	
	// Время
	$PARS['%H'] = $time_arr[0];
	$PARS['%G'] = (int)$time_arr[0];
	$PARS['%i'] = $time_arr[1];
	$PARS['%s'] = $time_arr[2];
	
	return fetch_tpl($PARS, $formate);
}

// Перевод к стандартному виду "2012-12-28"
function formate_to_norm_date($date)
{
	if(!$date)
	{
		return '';
	}
	$date_time_tmp = split('\.', $date);
			
	return $date_time_tmp[2].'-'.$date_time_tmp[1].'-'.$date_time_tmp[0];
}
// Форматирование даты и времени
function formate_date($date, $mode=0)
{
	if(!$date || preg_match('/0000/', $date))
	{
		return '';
	}
	
	switch($mode)
	{
		// к виду 24.1.2012 в 12:11
		case '0':
			
			$date_time_tmp = split(' ', $date);
			$tmp_date = split('-', $date_time_tmp[0]);
			
			$formate_date = $tmp_date[2].'.'.$tmp_date[1].'.'.$tmp_date[0].' в '.substr($date_time_tmp[1],0,5);
		
		break;
		// к виду 24.1.2012
		case '1':
			
			$date_time_tmp = split(' ', $date);
			$tmp_date = split('-', $date_time_tmp[0]);
			
			$formate_date = $tmp_date[2].'.'.$tmp_date[1].'.'.$tmp_date[0];
		
		break;
	}
	
	return $formate_date;
}

function value_arr_proc($array)
{
	foreach($array as $k => $v)
	{
		$new[value_proc($k)] = value_proc($v);
	}
	
	return $new;
}
// Обработка переданных значений в запросах
function value_proc($value, $iconv=1, $allowable_tags)
{
	if($allowable_tags)
	{
		$value = trim(htmlspecialchars(strip_tags($value, "<h1><h2><h3><h4><h5><h6><strong><em><sup><sub><blockquote><div></pre><p><table><thead><th><tbody><tr><td>")));
	}
    else 
	{
		$value = trim(htmlspecialchars(strip_tags($value)));
	}
	
	if (!get_magic_quotes_gpc()) 
	{
		$value = addslashes($value);
	}
	
	if($iconv)
	{
		$value = iconv('utf-8//IGNORE', 'cp1251//IGNORE', $value);
	}
	 
	return $value;
}


// Преобразует номер телефона к стандартному виду
function convert_to_valid_phone_number($phone)
{
	//$phone = preg_replace('/[^0-9]+/', '', $phone);
	
	//if(strlen($phone)==11 && ($phone[0]==8 || $phone[0]==7))
	//{
		//$phone = substr($phone,1,10);
	//}
	$phone = preg_replace('/[^\+0-9]+/', '', $phone);
	
	return $phone;
}

// Перевод
function to_mktime($date, $only_date = 0)
{
	if(preg_match('/0000/', $date))
	{
		return '';
	}
	$date_time_tmp = split(' ', $date);
	 
	$tmp_date = split('-', $date_time_tmp[0]);
	
	if($date_time_tmp[1])
	{
		$time_arr = split(':', $date_time_tmp[1]);
	}
	
	if(!$time_arr || $only_date)
	{
		return mktime(0, 0, 0, $tmp_date[1], $tmp_date[2], $tmp_date[0]);
	}
	else
	{
		return mktime($time_arr[0], $time_arr[1], $time_arr[2], $tmp_date[1], $tmp_date[2], $tmp_date[0]);
	}
}


// Изменение размеров изображения
function img_resize($src, $out, $width, $height) {
	
    if (!file_exists($src)) {
		return false;
    }

	// Массив данных о изображение
    $size = getimagesize($src);

    // Исходя из формата (mime) картинки, узнаем с каким форматом имеем дело

    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
    $picfunc = 'imagecreatefrom'.$format;

    // Вычилсить соотношения сторонн
    $gor = $width  / $size[0];
    $ver = $height / $size[1];

    // Если не задана высота
    if ($height == 0) {
        $ver = $gor;
        $height  = $ver * $size[1];
    }
	// Если не задана ширина
	elseif ($width == 0) {
        $gor = $ver;
        $width   = $gor * $size[0];
    }

    // Формируем размер изображения
    $ratio = min($gor, $ver);
	   if ($gor == $ratio)
        $use_gor = true;
    else
        $use_gor = false;

    $new_width   = $use_gor  ? $width  : floor($size[0] * $ratio);
    $new_height  = !$use_gor ? $height : floor($size[1] * $ratio);
	
    $picsrc  = $picfunc($src);
    // Создание изображения в памяти
    $picout = imagecreatetruecolor($new_width, $new_height);
	// Заполнение цветом
   // imagefill($picout, 0, 0, 0xFFFFFF);
    // Нанесение старого на новое
    imagecopyresampled($picout, $picsrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
	// Создание файла изображения
    imagejpeg($picout, $out, 100);

    // Очистка памяти
    imagedestroy($picsrc);
    imagedestroy($picout);
 	return true;
}


// Генерация строки
function generate_rand_string($number)
{

    $arr = array('a','b','c','d','e','f',

                 'g','h','i','j','k','l',

                 'm','n','o','p','r','s',

                 't','u','v','x','y','z',

                 'A','B','C','D','E','F',

                 'G','H','I','J','K','L',

                 'M','N','O','P','R','S',

                 'T','U','V','X','Y','Z',

                 '1','2','3','4','5','6',

                 '7','8','9','0');
	$pass = "";

    for($i = 0; $i < $number; $i++)
	{

      // Вычисляем случайный индекс массива
	  $index = rand(0, count($arr) - 1);

      $pass .= $arr[$index];
	}

    return $pass;

}


// Режется изображение для превью
function crop_preview_photo($file_input, $file_output, $crop = 'square',$percent = false) 
{
	list($w_i, $h_i, $type) = getimagesize($file_input);

    $types = array('','gif','jpeg','png');
    $ext = $types[$type];
    if ($ext) {
    	$func = 'imagecreatefrom'.$ext;
    	$img = $func($file_input);
    }
	if ($crop == 'square') {
		$min = $w_i;
		if ($w_i > $h_i) $min = $h_i;
		$w_o = $h_o = $min;
	} else {
		list($x_o, $y_o, $w_o, $h_o) = $crop;

	}
	$img_o = imagecreatetruecolor($w_o, $h_o);
	imagefill($img_o, 0, 0, 0x281430);
	imagecopy($img_o, $img, 0, 0, $x_o, $y_o, $w_o, $h_o);
	if ($type == 2) {
		return imagejpeg($img_o,$file_output,100);
	} else {
		$func = 'image'.$ext;
		return $func($img_o,$file_output);
	}
}

// транслит текста
function translit($str)
{
	    $tr = array(
        "а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"e","ж"=>"zh",
        "з"=>"z","и"=>"i","й"=>"i","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
        "ы"=>"y","ь"=>"","э"=>"je","ю"=>"ju","я"=>"ja", 
		
		"А"=>"a","Б"=>"b",
        "В"=>"v","Г"=>"g","Д"=>"d","Е"=>"e","Ё"=>"e","Ж"=>"zh",
        "З"=>"z","И"=>"i","Й"=>"i","К"=>"k","Л"=>"l",
        "М"=>"m","Н"=>"n","О"=>"o","П"=>"p","Р"=>"r",
        "С"=>"s","Т"=>"t","У"=>"u","Ф"=>"f","Х"=>"h",
        "Ц"=>"c","Ч"=>"ch","Ш"=>"sh","Щ"=>"shh","Ъ"=>"",
        "Ы"=>"y","Ь"=>"","Э"=>"je","Ю"=>"ju","Я"=>"ja"
    );
    return strtr($str,$tr);
}


// Подсвечивает слова
function light_words($string, $words='', $first_char=0)
{
	
	if($words)
	{
		// Подсвечивае слова
		if($first_char)
		{
			$string = preg_replace("/^($words)/i", '<span class="word_selected">$1</span>', $string);
		}
		else
		{
			$string = preg_replace("/($words)/i", '<span class="word_selected">$1</span>', $string);
		}
	}


	return $string;
}

// Приводит дату и время
function date_passing($date, $only_in_days=0)
{
	$date_time_tmp = split(' ', $date);
	
	$tmp_date = split('-', $date_time_tmp[0]);
	
	$time_arr = split(':', $date_time_tmp[1]);
		
	// Переданное время в формате	
	$date_mktime =  mktime($time_arr[0], $time_arr[1], $time_arr[2], $tmp_date[1], $tmp_date[2], $tmp_date[0]);
	 
	// Актуальное время в формате
	$actual_mktime = time();
	
	$string = $actual_mktime -  $date_mktime;
	 
	//echo date('Y-m-d-H-i-s', $string);
	
	$date_string_arr = sec_to_date_words($string, $only_in_days);
	
	$date_string = $date_string_arr['string'];

	return $date_string;

}

// Переводит секунды в кол-во дней, часов, минут
function sec_to_date_words($seconds, $only_in_days = 0, $with_seconds=0, $return_str_result=0)
{
	if($only_in_days)
	{
		$day=round($seconds/86400);
		
		$hours=round(($seconds/3600)-$day*24); 
		
		$min=round(($seconds-$hours*3600-$day*86400)/60); 
		
		$sec=$seconds-($min*60+$hours*3600+$day*86400); 
	}
	else
	{
		$day=floor($seconds/86400);
		
		$hours=floor(($seconds/3600)-$day*24); 
		
		$min=floor(($seconds-$hours*3600-$day*86400)/60); 
		
		$sec=$seconds-($min*60+$hours*3600+$day*86400); 
	}
	
	if(!$with_seconds)
	{
		$min = $min ? $min : 1;
	}
 	 
	// Секунд >= 1 дня
	$is_days = 0; 
	 
	$days_string =  $day.' '.numToword($day, array('день', 'дня', 'дней'));
	$hours_string =  $hours.' '.numToword($hours, array('час', 'часа', 'часов'));
	$min_string =  $min.' '.numToword($min, array('минуту', 'минуты', 'минут'));
	$sec_string =  $sec.' '.numToword($sec, array('секунда', 'секунды', 'секунд'));
	
	
	// Показывать дни, часы, минуты
	if(!$only_in_days)
	{
		if($day)
		{
			$date_string .= $days_string;
			$is_days = 1; 
		}
		if($hours)
		{
			$date_string .= ' '.$hours_string;
		}
		if($min)
		{
			$date_string .= ' '.$min_string;
		}
		if($with_seconds)
		{
			$date_string .= ' '.$sec_string;
		}
	}
	// Показывать только дни
	else
	{
		if($day)
		{
			$date_string .= $day.' '.numToword($day, array('день', 'дня', 'дней'));
			$is_days = 1; 
		}
		else
		{
			$date_string .= 'меньше дня';
		}
	} 
	
	if($return_str_result)
	{
		// Формируем строку вывода последнего захода на сайт
		if($day)
		{
			return  $days_string;
		}
		else if($hours)
		{
			return $hours_string;
		}
		else if($min)
		{
			return $min_string;
		}
	}
	
	return array('string' => $date_string, 'is_days' => $is_days, 'days_string' => $days_string, 'hours_string' => $hours_string, 'min_string' => $min_string, 'sec_string' => $sec_string, 'day' => $day, 'hours' => $hours, 'min' => $min, 'sec' => $sec);
}
// К актуальной дате прибавляет дни и возвращает дату
function days_to_date_after_date($days, $date)
{
	
	$days_in_seconds = $days * 3600 * 24;
 
	$date_in_seconds = $days_in_seconds + to_mktime($date);
	
	return date('Y-m-d', $date_in_seconds);
	
}

// Слагает слова
function numToword($num, $words)
{
	$num = $num % 100;
	if ($num > 19) 
	{
		$num = $num % 10;
	}
	switch ($num) {
		case 1: {
			return($words[0]);
		}
		case 2: case 3: case 4: {
			return($words[1]);
		}
		default: {
			return($words[2]);
		}
	}
}

// Перевод ip адреса в число
function ip_to_number($IPaddr)
{
	if ($IPaddr == "")
	{
	        return 0;
	} 
	else 
	{
		$ips = split ("\.", "$IPaddr");
		return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
	}
}

// Формирование пароля по формуле
function password_hash_proc($string)
{
	return md5(md5($string).md5(KEY_WORD));
}

// Формирование данных точек
function series_data($date, $value, $other_value, $with_time=0)
{
	$tmp_date = split(' ', $date);
	$year = substr($tmp_date[0],0,4);
	$month = (int)substr($tmp_date[0],5,2)-1;
	$day = (int)substr($tmp_date[0],8,2);
	
	if($with_time)
	{
		$tmp_time = split(':', $tmp_date[1]);
		$hour = (int)$tmp_time[0];
		$minutes = (int)$tmp_time[1];
		$seconds = (int)$tmp_time[2];
	}
	else
	{
		$hour = 0;
		$minutes = 0;
		$seconds = 0;
	}
	 
			
	$data = '[Date.UTC('.$year.','.$month.','.$day.', '.$hour.','.$minutes.','.$seconds.'), '.$value.', "'.$other_value.'"]'; ;
	
	return $data;		
}
function get_date_utc_for_js_object($date)
{
	$year = substr($date,0,4);
	$month = (int)substr($date,5,2)-1;
	$day = (int)substr($date,8,2);
			
	return 'Date.UTC('.$year.','.$month.','.$day.')';
}

// Преобразует число к красивому виду виду 1 000 00
function sum_process($string, $sep=' ', $split='\.', $with_kopek)
{
	return number_format($string, 2, '.', ' ');
}

// Выводит дату и время в виде 2012-11-11 14:11
function join_date_and_time($date, $time)
{	
	$time = $time=='' ? '00:00' : $time;
	
	if($date=='')
	{
		return '';
	}
	
	$time_tmp = split(':', $time);

	if(count($time_tmp)!=2)
	{
		return '';
	}
	
	if(!is_numeric($time_tmp[0]) || !is_numeric($time_tmp[1]))
	{
		return '';
	}
	
	if($time_tmp[0] < 0 || $time_tmp[0] > 23 || $time_tmp[1] < 0 || $time_tmp[1] > 59)
	{
		return '';
	}
	
	return $date.' '.$time;
}

// Возвращает часть даты
function get_part_from_date($date, $need)
{
	$tmp_date = split(' ', $date);
	$date_arr = split('-', $tmp_date[0]);
	$time_arr = split(':', $tmp_date[1]);
	
	switch($need)
	{
		case 'y':
			return $date_arr[0];
		break;
		case 'm':
			return $date_arr[1];
		break;
		case 'd':
			return $date_arr[2];
		break;
		case 'h':
			return $time_arr[0];
		break;
		case 'min':
			return $time_arr[1];
		break;
		case 's':
			return $time_arr[2];
		break;
	}
}

// Формирует массив Н-количества прошлых дней от текущего дня
function fill_array_num_days_ago_from_actual_date($num, $date_to)
{
	// Формируем массив 31 дня от текущего
	$start_day = time() - 3600 * 24 * $num;
	while(!$stop)
	{
		$date_s = date('Y-m-d', $start_day);
		$days_arr[$date_s] = '0';
		if($date_s==$date_to)
		{
			$stop=1;
		}
		$start_day += 24 * 3600;
		
		if($i>400)
		{
			$stop=1;
		}
		$i++;
	}
	
	return $days_arr;
}


// Формирует массив Н-количества прошлых  месяцев от текущего месяца 
function fill_array_num_month_ago_from_actual_date($num, $date_to)
{
	
	$year = substr($date_to,0,4);
	$month = (int)substr($date_to,5,2);
	
	for($i=0; $i<=$num; $i++)
	{
		$month_str = strlen($month)==1 ? '0'.$month : $month;
		$days_arr[$year.'-'.$month_str] = '0';
		
		$month -= 1;
		
		if($month==0)
		{
			$month = 12;
			$year -= 1;
		}
		
	}
	$days_arr = array_reverse($days_arr);
	return $days_arr;
}

// Возвращает название недели по дате
function get_dayweek_name_by_date($date)
{
	$date_mktime = to_mktime($date);
	
	$weekday_c[0]="воскресенье";
	$weekday_c[1]="понедельник";
	$weekday_c[2]="вторник";
	$weekday_c[3]="среда";
	$weekday_c[4]="четверг";
	$weekday_c[5]="пятница";
	$weekday_c[6]="суббота";
	
	return $weekday_c[date('w', $date_mktime)];
}

function formate_filesize($bytes)
{
	if ($bytes > 0)
    {
        $unit = intval(log($bytes, 1024));
		 
        $units = array('Б', 'Кб', 'Мб', 'Гб');
		
		  if (array_key_exists($unit, $units) === true)
        {
			if($unit>=2)
			{
				$res =  sprintf('%01.1f %s', $bytes / pow(1024, $unit), $units[$unit]);
			}
			else
			{
           		$res =   sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
			}
        }
    }
	
	$res = str_replace(',', '.', $res);

    return $res;
}

// Проверка на правильность даты
function date_rus_validate($date)
{
	if(!preg_match('/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}/', $date))
	{
		return false;
	}
	else
	{
		return true;
	}
}
function stdToArray($obj){
  $rc = (array)$obj;
  foreach($rc as $key => &$field){
    if(is_object($field))$field = stdToArray($field);
  }
  return $rc;
}

function to_iconv_array($arr)
{
	foreach($arr as $key => $val)
	{
		$new_arr[$key] = iconv('windows-1251','UTF-8', $val);
	}
	
	return $new_arr;
}

function to_iconv($val, $to_win)
{
	if($to_win)
	{
		return iconv('UTF-8', 'windows-1251', $val);
	}
	else
	{
		return iconv('windows-1251','UTF-8', $val);
	}
	 
}

function get_selected_easycomplete($value, $name)
{
	$option_fcbk_tpl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/tags/option_fcbk.tpl');
	
	// название клиента для поля фсбк
	$PARS['{CLASS}'] = 'selected';
	$PARS['{VALUE}'] = '-s-'.$value;
	$PARS['{NAME}'] = $name;
	return  fetch_tpl($PARS, $option_fcbk_tpl);
}
function proc_splited_date($year, $month, $day)
{
	if(!$year>0 || !$month> 0 || !$day>0)
	{
		return '';
	}
	else
	{
		return $year.'-'.$month.'-'.$day;
	}	
}

function create_token($timestamp)
{
	return md5('_-`13WERwgfg43#$%4532423412423tfc:#$%232' . $timestamp);
}

// Проверка расширения файла на блеклист
function is_file_ext_in_blacklist($file_name)
{
	$blacklist = array(".php", ".phtml", ".php3", ".php4", ".exe", ".bat");
	
	// Расширение файла
	$extension = substr($file_name,strrpos($file_name, '.'),20);
	
	 
	if(in_array( $extension, $blacklist))
	{
		return 1;
	}
	else return 0;
}

function create_upload_folder($date, $static=0)
{
	if($date)
	{
		$mktime = to_mktime($date);
	}
	else
	{
		$mktime = time();
	}
	
	if($static)
	{
		$upload_path = UPLOAD_PATH.'/static';
	}
	else
	{
		$upload_path = UPLOAD_PATH.'/d';
	}
	 
	
	$year = date('Y', $mktime);
	
	$month = date('m', $mktime);
	
	$day = date('d', $mktime);
	
	$y_dir = $upload_path.'/'.$year;
	$m_dir = $upload_path.'/'.$year.'/'.$month;
	$d_dir = $upload_path.'/'.$year.'/'.$month.'/'.$day;
	
  
	if(!is_dir($y_dir))
	{
		mkdir($y_dir);
		chmod($y_dir, 0775);
	}
	
	if(!is_dir($m_dir))
	{
		mkdir($m_dir);
		chmod($m_dir, 0775);
	}
	
	/*if(!is_dir($d_dir))
	{
		mkdir($d_dir);
		chmod($d_dir, 0775);
	}*/
	
	return $m_dir;
}

// Возвращает абсолютный путь путь до файла
function get_download_dir($upload_path, $file_date, $static=0)
{
	if($static)
	{
		$upload_path = UPLOAD_PATH.'/static';
	}
	else
	{
		$upload_path = UPLOAD_PATH.'/d';
	}
	
	$date_mktime = to_mktime($file_date);
	
	$year = date('Y', $date_mktime);
	
	$month = date('m', $date_mktime);
	
	$day = date('d', $date_mktime);
	
	return $upload_path.'/'.$year.'/'.$month;
}

function get_file_dir_url($file_date, $image_name)
{
	
	//return '/dl/'.$image_name.'?t='.$type.'&i='.$id;
	
	$date_mktime = to_mktime($file_date);
	
	$year = date('Y', $date_mktime);
	
	$month = date('m', $date_mktime);
	
	$day = date('d', $date_mktime);
	
	return '/'.UPLOAD_FOLDER.'/static/'.$year.'/'.$month.'/'.$image_name;
}

function get_rand_file_system_name($file_name)
{
	$file_parts = pathinfo($file_name);
		
	$extension = $file_parts['extension'];
	 
	return date('ymdHis').'_'.rand(1000000000,9999999999).'.'.$extension;
}


// Сохранение файла
function file_download_start($filename, $file_name_for_out='') {
	
	 
   if (file_exists($filename)) 
   {
	
 		header('Accept-Ranges:	bytes');
		header('Connection:	Keep-Alive');
		header('Content-Length: ' . filesize($filename));
		//header('Content-Type:	image/jpeg');
		header('Content-Type: application/octet-stream');
		header('Connection:	Keep-Alive');
		
		header('Content-Disposition:	attachment; filename="'.$file_name_for_out.'"');
		
		readfile($filename);
	
    	exit;
	
   }
}

function is_date_exists($date)
{
	// Если указана дата крайнего срока
	if(!preg_match('/0000/', $date))
	{
		return true;
	}
	else return false;
}
function str_to_a($text)
{
	// http://ruseller.com/lessons.php?rub=37&id=662
	$text= preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" target=\"_blank\" class=\"link\">$3</a>", $text);
 
    $text= preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" target=\"_blank\" class=\"link\">$3</a>", $text);
	
	return($text);
}
// Проверка почты
function email_valid($email)
{
	if(!preg_match('/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i', $email))
	{
		return false;
	}
	else
	{
		return true;
	}
}
?>