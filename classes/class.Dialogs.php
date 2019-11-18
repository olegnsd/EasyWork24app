<?php
/***
	Диалоги
	
	Ver: 1.0
	Edited: 27.12.2013
*/
class CDialogs {
	
	private $user_id = 0;
	
	private $dialog_id = 0;
	
	private $current_user_id = 0;
	
	public function __construct($db, $current_user_id)
	{
		$this->db = $db;
		$this->current_user_id = $current_user_id;
	}
	
	public function set_dialog_id($id)
	{
		$this->dialog_id = $id;
	}
	
	public function get_user_dialogs_count()
	{
		// Пользователи, к которым есть доступ писать сообщения
		$dialog_access_users_arr = get_current_user_users_arrs(array(1,1,1,1,1));
		
		$dialogs_arr = array();
		 
		if($dialog_access_users_arr)
		{
			//$access_users = implode(',', $dialog_access_users_arr);
		}
		else
		{
			//return 0;
		}
		
		$sql = "SELECT COUNT(DISTINCT(i.dialog_id)) as count
				FROM tasks_dialogs_message_to_user i
				LEFT JOIN tasks_dialogs_users z ON i.dialog_id=z.dialog_id
				LEFT JOIN tasks_dialogs_users x ON z.dialog_id=x.dialog_id
				WHERE i.user_id = '".$this->current_user_id."' AND z.user_id = '".$this->current_user_id."'
				AND i.status <> 2 ";
				 
		$row = $this->db->query_firstrow($sql);	
		
		return $row['count'];	
	}
	
	// Выбор диалогов пользователя
	public function get_user_dialogs($mini=0, $order = '', $limit='', $page=1, $return_new_dialogs)
	{
		// Пользователи, к которым есть доступ писать сообщения
		$dialog_access_users_arr = get_current_user_users_arrs(array(1,1,1,1,1));
		
		$dialogs_arr = array();
		 
		if($dialog_access_users_arr)
		{
			//$access_users = implode(',', $dialog_access_users_arr);
		}
		else
		{
			//return $dialogs_arr;
		}
		
		// Если необходима сортировка
		switch($order)
		{
			case 'last_edit_date':
				$order_by = 'ORDER by last_edit_date DESC';
			break;
		}
		
		$page = !$page ? 1 : $page;
		
		if($limit)
		{
			$begin_pos = DIALOGS_PER_PAGE * ($page - 1);
	
			$limit_s = " LIMIT $begin_pos, ".DIALOGS_PER_PAGE;
		}
		
		if($search_word)
		{
			// Выбор диалогов ПОИСК ПО ИМЕНИ
			$sql = "SELECT DISTINCT(i.dialog_id), i.last_edit_date
					FROM tasks_dialogs i
					LEFT JOIN tasks_dialogs_message_to_user j ON i.dialog_id = j.dialog_id
					LEFT JOIN tasks_dialogs_users z ON i.dialog_id=z.dialog_id
					LEFT JOIN tasks_dialogs_users x ON z.dialog_id=x.dialog_id
					LEFT JOIN tasks.tasks_users u ON x.user_id=u.user_id
					WHERE j.user_id = '".$this->current_user_id."' AND z.user_id = '".$this->current_user_id."' 
					AND j.status <> 2 AND (u.user_surname LIKE '$search_word%' OR u.user_name LIKE '$search_word%')";
					
		}
		else
		{
			$sql = "SELECT DISTINCT(i.dialog_id), i.last_edit_date
					FROM tasks_dialogs i
					LEFT JOIN tasks_dialogs_message_to_user j ON i.dialog_id = j.dialog_id
					LEFT JOIN tasks_dialogs_users z ON i.dialog_id=z.dialog_id
					LEFT JOIN tasks_dialogs_users x ON z.dialog_id=x.dialog_id
					WHERE j.user_id = '".$this->current_user_id."' AND z.user_id = '".$this->current_user_id."'
					AND j.status <> 2 $order_by $limit_s";
		}
		 
		
		$res=$this->db->query($sql);
		 
		while($row = $this->db->fetch_array($res, 1))
		{  
			// Если не требуется вывод просто id диалогов, добавляем в массив некоторые данные
			if(!$mini)
			{
				//Находим последнее сообщение в диалоге для пользователя
				$sql = "SELECT message_id, status FROM tasks_dialogs_message_to_user WHERE dialog_id='".$row['dialog_id']."' AND user_id='".$this->current_user_id."' AND status<> 2 ORDER by id desc";
						
		 
				$dialog_last_message_arr = $this->db->query_firstrow($sql);
				
				// если требуется вывести только новые диалоги
				if($return_new_dialogs && $dialog_last_message_arr['status']!=0)
				{
					continue;
				}
				
				$row['message_id'] = $dialog_last_message_arr['message_id'];
				
				$row['msg_status'] = $dialog_last_message_arr['status'];
				
				// Выбираем пользователя с кем идет диалог
				$sql = "SELECT user_id FROM tasks_dialogs_users WHERE dialog_id='".$row['dialog_id']."' AND user_id!='".$this->current_user_id."'";
				 
				$dialog_users_arr = $this->db->query_firstrow($sql);
				$row['dialog_user_id'] = $dialog_users_arr['user_id'];
				
				
			}
			$dialogs_arr[to_mktime($row['last_edit_date']).'_'.$row['dialog_id']] = $row;
						
			$dialogs_arr_mini_arr[] = $row['dialog_id'];
		}
		
		//krsort($dialogs_arr);
		
		//echo "<pre>", print_R($dialogs_arr);
		 
		if($mini)
		{
			return $dialogs_arr_mini_arr;
		}
		else
		{
			return $dialogs_arr;
		}
	}
	
	// Получает пользователей диалога
	public function get_dialog_users($dialog_id)
	{
		$sql = "SELECT * FROM tasks_dialogs_users WHERE dialog_id = '$dialog_id'";
		
		$res=$this->db->query($sql);
		
		while($row = $this->db->fetch_array($res, 1))
		{
			
		}
	}
	
	// Добавить сообщение в диалог
	public function add_message_to_dialog($to_user_id, $message_text, $message_theme)
	{
		$date_add = time();
		
		$sql = "INSERT INTO tasks_dialogs_messages SET dialog_id='".$this->dialog_id."', user_id='".$this->current_user_id."', message_text='$message_text', message_theme='$message_theme', message_date='$date_add' ";
		
		$res=$this->db->query($sql);
		
		$message_id = $this->db->get_insert_id();
		
		// Добавляем сообщение для текущего пользователя
		$sql = "INSERT INTO tasks_dialogs_message_to_user SET message_id='$message_id', dialog_id='".$this->dialog_id."', user_id='".$this->current_user_id."', status = 1";
		$res=$this->db->query($sql);
		
		// Добавляем сообщение для получателя
		$sql = "INSERT INTO tasks_dialogs_message_to_user SET message_id='$message_id', dialog_id='".$this->dialog_id."', user_id='$to_user_id', status = 0";
		$res=$this->db->query($sql);
		
		// Обновляем дату последнего события в диалоге
		$sql = "UPDATE tasks_dialogs SET last_edit_date=NOW() WHERE dialog_id = '".$this->dialog_id."'";
		$res=$this->db->query($sql);
		
		return $message_id;
		
	}
	// Кол-во сообщений в диалоге
	public function get_dialog_messages_count($message_id = 0)
	{
		if($message_id)
		{
			$and_msg = " AND message_id >= '$message_id'";
		}
		$sql = "SELECT COUNT(*) as count FROM tasks_dialogs_message_to_user USE INDEX (sort)
				WHERE dialog_id='".$this->dialog_id."' AND user_id='".$this->current_user_id."' AND status <> 2 $and_msg";
					 
		$row = $this->db->query_firstrow($sql);
		
		return $row['count'];
	}
	
	// Получает новые сообщения в диалоге
	public function get_dialog_new_messages_ids_arr($dialog_id)
	{
		// Получаем сообщения пользователя в диалоге		
		$sql = "SELECT message_id FROM tasks_dialogs_message_to_user USE INDEX (sort)
				WHERE dialog_id='$dialog_id' AND user_id='".$this->current_user_id."' AND status = 0";

		$res=$this->db->query($sql);
		
		while($row = $this->db->fetch_array($res, 1))
		{
			$messages_arr[$row['message_id']] = $row['message_id'];
		}
		
		return $messages_arr;
	}
	
	// Кол-во новых сообщений для пользователя
	public function get_new_messages_count_for_user($dialogs_arr = array())
	{
		$users_dialog_arr = $dialogs_arr ? $dialogs_arr : $this->get_user_dialogs(1);
		 
		if($users_dialog_arr)
		{  
			$users_dialogs = implode(',', $users_dialog_arr);
		}
		else
		{
			return 0;
		}
		
		$sql = "SELECT COUNT(*) as count FROM tasks_dialogs_message_to_user WHERE user_id = '".$this->current_user_id."' AND status=0 AND dialog_id IN($users_dialogs)";
		 

		$row = $this->db->query_firstrow($sql);
		
		return $row['count'];
	}
	
	// Кол-во новых сообщений для пользователя
	public function get_last_message_id_to_user($dialogs_arr=array())
	{
		if($dialogs_arr)
		{  
			$users_dialogs = implode(',', $dialogs_arr);
		}
		else
		{
			return 0;
		}
		
		$sql = "SELECT i.message_id FROM tasks_dialogs_message_to_user i
				LEFT JOIN tasks_dialogs_messages j ON j.message_id=i.message_id
				WHERE j.user_id!='".$this->current_user_id."' AND i.user_id = '".$this->current_user_id."' AND i.dialog_id IN($users_dialogs) ORDER by i.id DESC LIMIT 1";
   
		$row = $this->db->query_firstrow($sql);
		
		return $row['message_id'];
	}
	
	// Получаем последнее сообщение в диалоге доступное актуальному пользователю
	public function get_last_dialog_message_for_user($dialog_id)
	{
		// Получаем последнее сообщение в диалоге доступное актуальному пользователю
		$sql = "SELECT i.message_id
				FROM tasks_dialogs_message_to_user i
				WHERE i.dialog_id='".$dialog_id."' AND i.status<>2 AND i.user_id='".$this->current_user_id."' ORDER by i.id DESC LIMIT 1";
				
		 // echo $sql;
		$message_data = $this->db->query_firstrow($sql);
		
		return $message_data;
	}
	
	// поиск сообщений по диалогу
	public function get_dialog_search_messages_arr($dialog_id, $search_words, $date_from, $date_to, $page)
	{
		$messages_arr = array();
		
		if($date_from && date_rus_validate($date_from))
		{
			$date_from = to_mktime(formate_to_norm_date($date_from));
		}
		else
		{
			$date_from = '';
		}
		
		if($date_to && date_rus_validate($date_to))
		{
			$date_to = to_mktime(formate_to_norm_date($date_to));
		}
		else
		{
			$date_to = '';
		}
		
		if($date_to)
		{
			$date_to += 3600*24-1;
		}
		
		if($date_from && $date_to && $date_from != $date_to && $date_to >= $date_from)
		{
			$and_date = " AND j.message_date >= '$date_from' AND j.message_date <='$date_to'";
		}
		else if($date_from && !$date_to)
		{
			$and_date = " AND j.message_date >= '$date_from'";
		}
		else if(!$date_from && $date_to)
		{
			$and_date = " AND j.message_date <= '$date_to'";
		}
		
		$page = !$page ? 1 : $page;
		
		$begin_pos = MSG_PER_PAGE * ($page - 1);
	
		$limit = " LIMIT $begin_pos, ".MSG_PER_PAGE;
	
		// Получаем сообщения пользователя в диалоге		
		$sql = "SELECT j.* FROM tasks_dialogs_message_to_user i USE INDEX (sort)
				LEFT JOIN tasks_dialogs_messages j ON j.message_id=i.message_id
				WHERE (j.message_text LIKE '%$search_words%' OR j.message_theme LIKE '%$search_words%') AND i.dialog_id='$dialog_id' 
				AND i.user_id='".$this->current_user_id."' AND i.status <> 2  $and_date ORDER by j.message_date DESC $limit";
		  
		$res=$this->db->query($sql);
		 
		while($row = $this->db->fetch_array($res, 1))
		{
			$messages_arr[$row['message_id']] = $row;
		}
		
		return $messages_arr;
	}
	
	// Выбор сообщений диалога
	public function get_dialog_messages_arr($dialog_id, $receiver_user_id, $page, $message_id = 0, $new = 0)
	{
		$result_messages_arr = array();
		
		$page = !$page ? 1 : $page;
		
		$begin_pos = MSG_PER_PAGE * ($page - 1);
	
		$limit = " LIMIT $begin_pos, ".MSG_PER_PAGE;
		
		// При подгрузке большего количества сообщений, не выводим те, которые добавлялись в открытом окне диалога
		if($page>=1)
		{
			if($_SESSION['message_last_id'])
			{
				$and_not_messages = " AND message_id <= '".$_SESSION['message_last_id']."'";
			}
		}
	 	
		// Удаленные сообщения
		$deleted_messages_arr = $_SESSION['deleted_messages_ids'];
		$deleted_messages_ids = implode(', ', $deleted_messages_arr);
		
		if($deleted_messages_ids)
		{
			$deleted_messages = " OR message_id IN($deleted_messages_ids) ";
		}
	
		if($new)
		{
			// Получаем сообщения пользователя в диалоге		
			$sql = "SELECT j.* FROM tasks_dialogs_messages i
					LEFT JOIN tasks_dialogs_message_to_user j USE INDEX (sort) ON j.message_id=i.message_id AND j.user_id = '".$this->current_user_id."'
					WHERE i.user_id!='".$this->current_user_id."' AND i.dialog_id='$dialog_id' AND j.status <> 2 AND i.message_id > '".$_SESSION['message_last_update_message_id']."' ORDER by i.message_id DESC";		
					 
					
		}
		else if($message_id)
		{
			// Получаем сообщения пользователя в диалоге		
			$sql = "SELECT * FROM tasks_dialogs_message_to_user
					WHERE message_id='$message_id' AND user_id='".$this->current_user_id."' AND status <> 2";
		}
		else
		{
			// Получаем сообщения пользователя в диалоге		
			$sql = "SELECT * FROM tasks_dialogs_message_to_user USE INDEX (sort)
					WHERE dialog_id='$dialog_id' AND user_id='".$this->current_user_id."' AND (status <> 2 $deleted_messages) $and_not_messages ORDER by message_id DESC $limit";
				 
		}
		
		$res=$this->db->query($sql);
		
		while($row = $this->db->fetch_array($res, 1))
		{
			// Массив строки статуса сообщения текущего пользователя
			$messages_status_arr_for_current_user[$row['message_id']] = $row;
			// ID сообщений, которые будут выводиться текущему пользователю
			$messages_arr[] = $row['message_id'];
			
			$last_message_id = $last_message_id ? $last_message_id : $row['message_id'];
		}
		
		 
		$messages_ids = implode(',', $messages_arr);
		
		if(!$messages_ids)
		{
			return $result_messages_arr;
		}
		
		// Последнее новое сообщение
		if($last_message_id && $new)
		{
			$_SESSION['message_last_update_message_id'] = $last_message_id;
		}
		
		
		// Выбираем строки статуса сообщений пользователя, с которым текущий пользователь в диалоге
		$sql = "SELECT * FROM tasks_dialogs_message_to_user WHERE message_id IN($messages_ids) AND user_id='$receiver_user_id'";
		 
		$res=$this->db->query($sql);
	 
		while($row = $this->db->fetch_array($res, 1))
		{
			// Массив строки статуса сообщения пользователя, с которым текущий пользователь в диалоге
			$messages_status_arr_for_other_user[$row['message_id']] = $row;
		}
		

		// Выбор сообщений, которые будут выводиться текущему пользователю
		$sql = "SELECT * FROM tasks_dialogs_messages WHERE message_id IN($messages_ids) ORDER BY Field(message_id, $messages_ids)";
		  
		 
		$res=$this->db->query($sql);
	   
		while($row = $this->db->fetch_array($res, 1))
		{ 
			// Если текущий пользователь отправлял сообщение, записываем его в массив как Отправитель иначе ПОлучатель
			if($row['user_id']==$this->current_user_id)
			{
				$sender = $messages_status_arr_for_current_user[$row['message_id']];
				$receiver = $messages_status_arr_for_other_user[$row['message_id']];
			}
			else if($row['user_id']!=$this->current_user_id)
			{
				$sender = $messages_status_arr_for_other_user[$row['message_id']];
				$receiver = $messages_status_arr_for_current_user[$row['message_id']];
			}
			
			$result_messages_arr[$row['message_id']]['message'] = $row;
			$result_messages_arr[$row['message_id']]['sender'] = $sender;
			$result_messages_arr[$row['message_id']]['receiver'] = $receiver;
			 
		}
		// echo "<pre>", print_r($result_messages_arr);
		
		return $result_messages_arr;
	}
	
	// ID последнего сообщения в диалоге
	public function get_dialog_last_message_id()
	{
		$sql = "SELECT message_id FROM tasks_dialogs_messages WHERE dialog_id='".$this->dialog_id."' ORDER by message_id DESC LIMIT 1";
		
		$row = $this->db->query_firstrow($sql);
		
		return $row['message_id'];
	}
	
	// Дата последнего прочитанного сообщение в диалоге пользователем, отличного от текущего
	public function get_last_read_dialog_message($user_id)
	{
		$sql = "SELECT i.id, i.read_date 
				FROM tasks_dialogs_message_to_user i USE INDEX (sort)
				LEFT JOIN tasks_dialogs_messages j ON j.message_id=i.message_id
				WHERE j.user_id='".$this->current_user_id."' AND i.user_id='$user_id' AND i.dialog_id='".$this->dialog_id."' AND i.status<>0 ORDER by i.message_id DESC LIMIT 1";
		 
		$row = $this->db->query_firstrow($sql);
		
		if(!$row['id'] || preg_match('/0000/', $row['read_date']))
		{
			return  '';
		}
		else
		{
			return $row['read_date'];
		}
	}
	
	// Получает id диалога между пользователями
	public function get_users_dialog($users_arr, $create_if_not_exists=0)
	{
		$num = 1;
		
		foreach($users_arr as $user)
		{
			$join_tb .= " LEFT JOIN tasks_dialogs_users j$num ON j$num.dialog_id = i.dialog_id ";
			$where[] = "j$num.user_id='$user'";
			$num++;
		}
		
		// Проверяем 
		$sql = "SELECT i.dialog_id FROM tasks_dialogs i ".$join_tb." WHERE ".implode(' AND ', $where);
		
		$dialog_row = $this->db->query_firstrow($sql);
		
		if($dialog_row['dialog_id'])
		{
			return $dialog_row['dialog_id'];
		}
		else if($create_if_not_exists)
		{
			$dialog_id = $this->create_dialog($users_arr);
			
			return $dialog_id;
		}
		
		
	}
	
	// ОТмечает прочтение сообщения
	public function dialog_messages_read($messages_arr)
	{
		$message_ids = implode(',',$messages_arr);
		
		if(!$message_ids)
		{
			return '';	
		}
		
		$sql = "UPDATE tasks_dialogs_message_to_user SET status = 1, read_date=NOW() WHERE message_id IN($message_ids) AND user_id='".$this->current_user_id."' AND status=0";
		
		$this->db->query($sql);
	}
	
	
	// Создать диалог
	private function create_dialog($users_arr)
	{
		$sql = "INSERT INTO tasks_dialogs SET dialog_date_add=NOW()";
		
		$this->db->query($sql);
		 
		// ID созданного диалога 
		$dialog_id =  $this->db->get_insert_id();
		
		foreach($users_arr as $user)
		{
			$this->add_user_to_dialog($dialog_id, $user);
		}
		
		return $dialog_id;
	}
	
	// Добавить пользователя в диалог
	private function add_user_to_dialog($dialog_id, $user_id)
	{
		if(!$dialog_id || !$user_id)
		{
			return '';
		}
		
		$sql = "INSERT INTO tasks_dialogs_users SET dialog_id = '$dialog_id', user_id='$user_id'"; 
	
		$this->db->query($sql);
	}
	
	// Удалить сообщения
	public function delete_dialog_messages($messages_arr)
	{
		$message_ids = implode(',', $messages_arr);
		
		if(!$message_ids)
		{
			return '';	
		}
		
		$sql = "UPDATE tasks_dialogs_message_to_user SET status = 2 WHERE message_id IN($message_ids) AND user_id='".$this->current_user_id."' AND status!=3";
		 
		$this->db->query($sql);
		
		if(!$_SESSION['deleted_messages_ids'])
		{
			$_SESSION['deleted_messages_ids'] = $messages_arr;
		}
		else
		{
			$_SESSION['deleted_messages_ids'] += $messages_arr;
		}
		 
	}
	
	// Удалить диалог
	public function delete_dialog($dialog_id)
	{
		// Для того, чтобы удалить диалог, достаточно удалить все сообщения в нем для пользователя и он не будет выводиться
		$sql = "UPDATE tasks_dialogs_message_to_user SET status=2 WHERE user_id='".$this->current_user_id."' AND dialog_id='".$this->dialog_id."' AND status IN(0,1)";
		
		$this->db->query($sql);
	}
	
	// Удалить сообщения
	public function restore_dialog_messages($message_id)
	{
		if(!$message_id)
		{
			return '';	
		}
		
		$sql = "UPDATE tasks_dialogs_message_to_user SET status = 1 WHERE message_id='$message_id' AND user_id='".$this->current_user_id."' AND status=2";
		
		$this->db->query($sql);
		
		unset($_SESSION['deleted_messages_ids'][$message_id]);
		 
	}
	
	public function get_dialog_id_by_message_id($message_id)
	{
		$sql = "SELECT dialog_id FROM tasks_dialogs_messages WHERE message_id='$message_id'";
		
		$row = $this->db->query_firstrow($sql);	
		
		return $row['dialog_id'];	
	}
	
	// Проверяет, участвует ли пользователь в диалоге
	public function check_user_in_dialog($user_id, $dialog_id)
	{
		$sql = "SELECT id FROM tasks_dialogs_users WHERE user_id='$user_id' AND dialog_id='$dialog_id'";
		 
		$row = $this->db->query_firstrow($sql);	
		
		if($row['id'])
		{
			return true;
		}
		else return false;
	}
	// Возвращает диалоги пользователей
	public function get_user_dialogs_arr($user_id)
	{
		$dialogs_arr = array();
		
		$sql = "SELECT * FROM tasks_dialogs_users WHERE user_id='$user_id'";
		
		$res=$this->db->query($sql);
		
		while($row = $this->db->fetch_array($res, 1))
		{
			$dialogs_arr[$row['dialog_id']] = $row['dialog_id'];
		}
		
		return $dialogs_arr;
	}
}

?>