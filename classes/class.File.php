<?php
class File 
{
	public $db;
	
	
	function __construct($site_db)
	{
		global $current_user_id;
		
		$this->db = $site_db;
		
		$this->current_user_id = $current_user_id;
	}
	
	
	// Устанавливает на папку флаг есть ли на флаг публичный доступ
	public function file_pub_flag($file_id)
	{
		// Во избежании дублирования публичных ссылок на файл
		$sql = "SELECT id FROM tasks_files_pub WHERE is_system=0 AND file_id='$file_id'";
		$pub_data = $this->db->query_firstrow($sql);
		
		if($pub_data['id'])
		{
		   // Файл публичный
		   $sql = "UPDATE tasks_files SET is_pub=1 WHERE file_id='$file_id'";
		}
		else
		{
		   // Файл не публичный
		   $sql = "UPDATE tasks_files SET is_pub=0 WHERE file_id='$file_id'"; 
		}
		 
		 $this->db->query($sql);
	}
	
	// Устанавливает на папку флаг расшарена она или нет
	public function file_sharing_flag($file_id)
	{
		 if($this->get_file_access_count($file_id) > 0)
		 {
			 // Файл был передан кому-то
			 $sql = "UPDATE tasks_files SET is_sharing=1 WHERE file_id='$file_id'";
		 }
		 else
		 {
			 // Файл не был передан кому-то
			 $sql = "UPDATE tasks_files SET is_sharing=0 WHERE file_id='$file_id'"; 
		 }
		 
		 $this->db->query($sql);
	}
	
	// Устанавливает на папку флаг расшарена она или нет
	public function folder_sharing_flag($folder_id)
	{
		 if($this->get_folder_access_count($folder_id)  > 0)
		 {
			 // Папка была передана кому-то
			 $sql = "UPDATE tasks_files_folders SET is_sharing=1 WHERE folder_id='$folder_id'";
		 }
		 else
		 {
			 // Папка не была передана кому-то
			 $sql = "UPDATE tasks_files_folders SET is_sharing=0 WHERE folder_id='$folder_id'"; 
		 }
		 
		 
		 $this->db->query($sql);
	}
	
	// Кол-во выданных доступов к файлу
	public function get_file_access_count($file_id)
	{
		$sql = "SELECT COUNT(*) as count FROM tasks_files_access WHERE file_id='$file_id'";
		
		$row = $this->db->query_firstrow($sql);
		
		return $row['count'];
	}
	
	// Кол-во выданных доступов к папке
	public function get_folder_access_count($folder_id)
	{
		$sql = "SELECT COUNT(*) as count FROM tasks_files_folders_access WHERE folder_id='$folder_id'";
		
		$row = $this->db->query_firstrow($sql);
		
		return $row['count'];
	}
	
	// Удалить папку
	public function delete_folder($folder_id)
	{
		$folders_arr[] = $folder_id;
		
		// Удаляем все дерево папок и файлов
		while(!$stop)
		{
			$folder_ids = implode(',', $folders_arr);
			
			// Проходим по папкам и выбираем все файлы, которые в ней есть и УДАЛЯЕМ
			foreach($folders_arr as $q_folder_id)
			{
				$sql = "SELECT file_id FROM tasks_files WHERE folder_id='$q_folder_id' AND deleted=0";
				
				$res_f = $this->db->query($sql);
				
				while($row=$this->db->fetch_array($res_f))
				{
					$this->delete_file($row['file_id'], 1); 
					$this->delete_all_file_access($row['file_id']);
				}
			}
			
			$folders_arr = array();
			
			// Выбираем папки, для которых эта категория является родительской
			$sql = "SELECT * FROM tasks_files_folders WHERE parent_folder_id IN ($folder_ids)";
			
			$res = $this->db->query($sql);
				
			while($row=$this->db->fetch_array($res))
			{
				$folders_arr[] = $row['folder_id'];
			}
			
			// Удаляем папки
			$sql = "UPDATE  tasks_files_folders SET deleted = 1, is_sharing=0 WHERE folder_id IN ($folder_ids)";
			
			$this->db->query($sql);
			
			// Удаляем права доступа к папке
			$sql = "DELETE FROM tasks_files_folders_access WHERE folder_id IN ($folder_ids) ";
			
			$this->db->query($sql);
			
			
			if(!$folders_arr)
			{
				$stop = true;
			}
			 
		}
		
		
		if(!mysql_error()) return 1;
	}
	
	
	// Удалить файл
	public function delete_file($file_id, $not_access_delete=0)
	{
		// Удаляем файл
		$sql = "UPDATE tasks_files SET deleted=1, is_sharing=0 WHERE file_id='$file_id'";
		
		$this->db->query($sql);
		
		if(!$not_access_delete)
		{
			$this->delete_all_file_access($file_id);
		}
		
		if(!mysql_error()) return 1;
	}
	
	// Удаляем все права выданые на файл
	public function delete_all_file_access($file_id)
	{
		// Удаляем все права выданые на файл
		$sql = "DELETE FROM tasks_files_access WHERE file_id='$file_id'";
		
		$this->db->query($sql);
	}
	
	// Сохранить права доступа к файлу
	public function save_file_access($id, $what, $access_list, $deleted_access_list)
	{	
		if($what=='file')
		{
			$access_tb = 'tasks_files_access';
			$elem_id_query = "file_id='$id'";
			
			// Данные файла
			$elem_data = $this->get_file_data($id);
			
		}
		else if($what=='folder')
		{
			$access_tb = 'tasks_files_folders_access';
			$elem_id_query = "folder_id='$id'";
			
			// Данные файла
			$elem_data = $this->get_folder_data($id);
		}
		
		 
		
		// Удаляем права доступа к файлу 
		foreach($deleted_access_list as $user_id)
		{
			$sql = "DELETE FROM $access_tb WHERE $elem_id_query AND user_id='$user_id'";
			 
			$this->db->query($sql);
		}
		
		// Все разрешения к файлу
		$file_access_arr = $this->get_users_access_to_file($id, $what);
		
		// Массив пользователей доступа к файлу
		$_file_users_access = $file_access_arr['users'];
		// Подробный массив пользователей доступа к файлу
		$_file_users_access_data = $file_access_arr['users'];
		
		// Обновляем права доступа к файлу
		foreach($access_list as $data)
		{ 
			$data = (array) $data;
			 
			$user_id = $data['user_id'];
			
			$access_mode = $data['access_mode'];
			 
			// Не пустые
			if(!$user_id || !is_numeric($user_id) || !in_array($access_mode, array(0,1,2,3)))
			{ 
				 continue;
			}
			//echo  ';',$elem_data['user_id'], ' ;', $user_id;
			// Не даем выставить права самому себе
			if($this->current_user_id==$user_id || $elem_data['user_id']==$user_id)
			{
				continue;
			}
			 
			// ПРоверяем, есть ли у пользователя доступ к этому файлу
			$sql = "SELECT * FROM $access_tb WHERE $elem_id_query AND user_id='$user_id'";
			// Доступ к файлу у пользователя
			$user_access = $this->db->query_firstrow($sql);
			
			 
		 	// Если доступ ПО умолчанию(0), то удаляем запись из таблицы для пользователя
			if(in_array($user_id, $_file_users_access) && $access_mode==0)
			{
				$sql = "DELETE FROM $access_tb WHERE $elem_id_query AND user_id='$user_id'";
				$this->db->query($sql);
			}
			
			// Если права на файл у пользователя уже есть, обновляем запись, чтобы добавить обновленную
			else if($user_access['id'])
			{
				// Удаляем старую запись
				$sql = "UPDATE $access_tb SET access='$access_mode' WHERE $elem_id_query AND user_id='$user_id'";
				 
				$this->db->query($sql);
			}
			else if($access_mode)
			{
				// Добавляем запись на права к файлу
				$sql = "INSERT INTO $access_tb SET user_id='$user_id', by_user_id='".$this->current_user_id."', $elem_id_query, access='$access_mode'";
				 
				$this->db->query($sql);
			}
			
			 
		}
		
		// Помечаем флагами шаринга
		if($what=='file')
		{
			$this->file_sharing_flag($id);
		}
		else if($what=='folder')
		{
			$this->folder_sharing_flag($id);
		}
		
		 
		
		return 1;
	}
	
	function is_access_override($user_id, $file_accesses, $access_for_user)
	{
		
	}
	
	// Получить все доступы к файлу (папке)
	public function get_users_access_to_file($id, $what)
	{
		if($what=='file')
		{
			$access_tb = 'tasks_files_access';
			$elem_id_query = "file_id='$id'";
			
		}
		else if($what=='folder')
		{
			$access_tb = 'tasks_files_folders_access';
			$elem_id_query = "folder_id='$id'";
		}
		
		// Получить все доступы к файлу 
		$sql = "SELECT * FROM $access_tb WHERE $elem_id_query";
		
		$res = $this->db->query($sql);
			
		while($row=$this->db->fetch_array($res))
		{
			$access_users[] = $row['user_id'];
			$access_data[$row['user_id']] = array('user_id' => $row['user_id'], 'access' => $row['access']);
		}
		
		return array('users' => $access_users, 'data' => $access_data);
	}
	
	// Восстановить версию файла
	public function file_version_restore($file_id, $version_id)
	{
		// Данные версии файла
		$file_version_data = $this->get_version_file_data($version_id);
		
		// Данные файла
		$file_data = $this->get_file_data($file_id);
		
		if($file_version_data['version_id']==$file_data['version_id'] ||  $file_version_data['file_id']!=$file_id)
		{
			return false;
		}
		
		
		//$file_system_name = $file_id.'_'.date('ymdHis').'.'.$file_version_data['extension'];
		
		$date_add = date('Y-m-d H:i:s');
		
		$file_system_name = get_rand_file_system_name($file_version_data['file_name']);
		 
		 
		//$file_version_path = Upload::get_file_dir($file_id).'/'.$file_version_data['file_system_name'];
		//$new_file_version_path = Upload::get_file_dir($file_id).'/'.$file_system_name;
		
		
		
		$file_version_path = get_download_dir('', $file_version_data['date_add']).'/'.$file_version_data['file_system_name'];
		$new_file_version_path = create_upload_folder($date_add).'/'.$file_system_name;
		
		copy($file_version_path, $new_file_version_path); 
		
		// Добавляем версию файла
		$sql = "INSERT INTO tasks_files_versions SET file_id='$file_id', file_name='".$file_version_data['file_name']."', file_system_name='$file_system_name', date_add='$date_add', user_id='".$this->current_user_id."', extension='".$file_version_data['extension']."', size='".$file_version_data['size']."'";
		
		$this->db->query($sql);
		
		$version_id = $this->db->get_insert_id();
		
		// Обновляем данные файла
		//$sql = "UPDATE tasks_files SET version_id='$version_id', file_name='".$file_version_data['file_name']."', extension='".$file_version_data['extension']."', size='".$file_version_data['size']."', date_edit=NOW() WHERE file_id='$file_id'";
		
		$sql = "UPDATE tasks_files SET version_id='$version_id', size='".$file_version_data['size']."', date_edit=NOW() WHERE file_id='$file_id'";
		
		$this->db->query($sql);
		
		return 1;
		
	}
	
	
	// Удалить версию файла
	public function delete_version_file($file_id, $version_id)
	{
		// Данные версии файла
		$file_version_data = $this->get_version_file_data($version_id);
		
		// Данные файла
		$file_data = $this->get_file_data($file_id);
		
		if($file_version_data['version_id']==$file_data['version_id'])
		{
			return false;
		}
		
		// Удаляем версию
		$sql = "UPDATE tasks_files_versions SET deleted=1 WHERE version_id='$version_id' AND file_id='$file_id'";
		
		$this->db->query($sql);
		
		return 1;
	}
	
	// Данные версии файла
	public function get_version_file_data($version_id)
	{
		$sql = "SELECT * FROM tasks_files_versions WHERE version_id='$version_id'";
		
		return  $this->db->query_firstrow($sql);
	}
	
	// Обновление имени файла
	public function update_file_name($file_id, $file_name)
	{
		// Данные файла
		$file_data = $this->get_file_data($file_id);
		
		// Проверка файла на одноименное название
		if($this->check_file_name_for_exists($file_id, $file_data['folder_id'], $file_name, $file_data['is_company']))
		{
			$result = '-1';
		}
		else
		{ 
			$sql = "UPDATE tasks_files SET file_name='$file_name' WHERE file_id='$file_id'";
			 
			$this->db->query($sql);
			
			$result = 1;
		}
		
		return $result;
	}
	
	// Обновление имени папки
	public function update_folder_name($folder_id, $folder_name)
	{
		$folder_data = $this->get_folder_data($folder_id);
		
		// Проверка папки на одноименное название
		if($this->check_folder_name_for_exists($folder_id, $folder_data['parent_folder_id'], $folder_name, $folder_data['is_company']))
		{
			$result = '-1';
		}
		else
		{ 
			$sql = "UPDATE tasks_files_folders SET folder_name='$folder_name' WHERE folder_id='$folder_id'";
		 
			$this->db->query($sql);
			
			$result = 1;
		} 
		
		return $result;
	}
	
	// Проверка папки на одноименное название в папке
	public function check_folder_name_for_exists($folder_id, $parent_folder_id, $folder_name, $is_company)
	{
		if($is_company)
		{
			$sql = "SELECT folder_id FROM tasks_files_folders WHERE folder_name = '$folder_name' AND folder_id!='$folder_id' AND parent_folder_id='$parent_folder_id' AND is_company='1' AND deleted=0 ";
		}
		else
		{
			$sql = "SELECT folder_id FROM tasks_files_folders WHERE folder_name = '$folder_name' AND folder_id!='$folder_id' AND parent_folder_id='$parent_folder_id' AND user_id='".$this->current_user_id."' AND deleted=0 ";
		}
		 
		 
		$row =  $this->db->query_firstrow($sql);
		
		if($row['folder_id'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	// Проверка файла на одноименное название в папке
	public function check_file_name_for_exists($file_id, $folder_id, $file_name, $is_company)
	{
		// Проверка в файлах компании
		if($is_company)
		{
			$sql = "SELECT file_id FROM tasks_files WHERE file_name = '$file_name' AND file_id!='$file_id' AND folder_id='$folder_id' AND is_company='$is_company' AND deleted=0 AND is_content_file=0 ";
		}
		else
		{
			// Если папка не указана, проверяем на наличие файла в корне 
			if(!$folder_id)
			{
				$and_user_id = " AND user_id='".$this->current_user_id."' ";
			}
			$sql = "SELECT file_id FROM tasks_files WHERE file_name = '$file_name' AND file_id!='$file_id' AND folder_id='$folder_id' AND is_company='0' AND deleted=0 AND is_content_file=0 $and_user_id ";
		}
		 
		$row =  $this->db->query_firstrow($sql);
		
		if($row['file_id'])
		{
			return $row['file_id'];
		}
		else
		{
			return 0;
		}
	}
	
	// Даныне файла
	public function get_file_data($file_id)
	{
		if(!is_numeric($file_id))
		{
			return array();
		}
		$sql = "SELECT * FROM tasks_files WHERE file_id='$file_id'";
		 
		return $this->db->query_firstrow($sql);
	}
	
	// Данные версии файла
	public function get_file_version_data($version_id)
	{
		$sql = "SELECT * FROM tasks_files_versions WHERE version_id='$version_id'";
		
		return $this->db->query_firstrow($sql);
	}
	
	// Даныне папки
	public function get_folder_data($folder_id)
	{
		$sql = "SELECT * FROM tasks_files_folders WHERE folder_id='$folder_id'";
		
		return $this->db->query_firstrow($sql);
	}
	
	// Создание папки
	public function create_folder($folder_name, $parent_folder_id, $is_company)
	{
		if(!$folder_name)
		{
			return '-1';
		}
		
		if($parent_folder_id)
		{
			$parent_folder_data = $this->get_folder_data($parent_folder_id);
			$is_company = $parent_folder_data['is_company'];
		}
		
		if($this->check_folder_name_for_exists($folder_id, $parent_folder_id, $folder_name, $is_company))
		{
			return '-2';
		}
		
		// Создаем папку
		$sql = "INSERT INTO tasks_files_folders SET user_id='".$this->current_user_id."', date_add=NOW(), folder_name='$folder_name', parent_folder_id='$parent_folder_id', is_company='$is_company'";
			
		$this->db->query($sql);
		
		$folder_id = $this->db->get_insert_id();
		
		return $folder_id;
	}
	
	public function update_file_desc($file_id, $desc)
	{
		$this->get_file_data($file_id);
		
		$sql = "UPDATE tasks_files SET file_desc='$desc' WHERE file_id='$file_id'";
	 
		$this->db->query($sql);
		
		return 1;
	}
}

?>
