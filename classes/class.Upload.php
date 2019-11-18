<?php
class Upload 
{
	 
	
	public $fileTypes = array('jpg', 'jpeg', 'gif', 'png');  
	
	public $db;
	
	public $current_user_id = 0;
	
	public $blacklist = array(".php", ".phtml", ".php3", ".php4", ".exe", ".bat");
	
	 
	function __construct($site_db)
	{
		global $current_user_id;
		
		$this->db = $site_db;
		
		$this->current_user_id = $current_user_id;
	}
	// Загрузка файла
	public function upload_file($is_company, $is_content_file=0)
	{
		$post_file_name = value_proc($_POST['file_name']);
		 
		$file_desc = value_proc($_POST['file_desc']);
		
		$folder_id = value_proc($_POST['folder_id']);
		
		// Добавить версию файла
		$upload_version_file = value_proc($_POST['upload_version_file']);
		
		
		// Перевод в байты
	 	$filesize_byte = filesize($_FILES['upload_file']['tmp_name']);
		
		// Если размер загружаемого файла больше допустимого
		if($filesize_byte > UPLOAD_SIZE_LIMIT_IN_BYTES)
		{
			echo '-1';
			exit();
		}
		
		// Разбивка имени файла на состовляющие
		$file_parts = pathinfo($_FILES['upload_file']['name']);
		
		if($post_file_name)
		{
			$file_name = $post_file_name;
			
			// Расширение файла
			$extension = substr($file_name,strrpos($file_name, '.')+1,20);
		}
		else
		{
			$file_name = value_proc($_FILES['upload_file']['name']);
			// Расширение файла
			$extension = $file_parts['extension'];
		}
		 
		
		// Тип файла разрешен 
		if (!$this->is_file_ext_in_blacklist($_FILES['upload_file']['name'])) {
			
			if($folder_id)
			{
			 
				$fl = new File($this->db);
				$folder_data = $fl->get_folder_data($folder_id);
				$is_company = $folder_data['is_company'];
			}
			
			 
			 
	 
			// Добавляем файл
			$sql = "INSERT INTO tasks_files SET folder_id='$folder_id', version_id='$version_id', user_id='".$this->current_user_id."', date_add=NOW(), date_edit=NOW(),file_name='$file_name', file_desc='$file_desc', size='$filesize_byte', extension='$extension', is_company='$is_company', is_content_file='$is_content_file'";
			
			$this->db->query($sql);
			
			$file_id = $this->db->get_insert_id();
			
			 
			
			// Добавляем версию файла
			$res_add_version = $this->add_file_version($file_id, $_FILES['upload_file']);
			
			// если произошла ошибка
			if($res_add_version=='-1')
			{ 
				$sql = "DELETE FROM tasks_files WHERE file_id='$file_id'";
				 
				$this->db->query($sql);
				
				return '-6';
			}
			
			return $file_id;
		 
		} 
		else 
		{
			return '-2';
			 
		}
		 
	}
	
	// Добавление версиии к файлу
	public function add_file_version($file_id, $_FILE_DATA, $use_post_file_name=0)
	{
		$file_name = value_proc($_POST['file_name']);
		
		$file_parts = pathinfo($_FILE_DATA['name']);
		
		$extension = $file_parts['extension'];
		
		
		// Если требуется импользовать название файла как он передан в запросе
		if($use_post_file_name)
		{
			 
			$file_name = value_proc($_FILE_DATA['name']);
		}
		else
		{
			$file_name = $file_name ? $file_name : value_proc($_FILE_DATA['name']);
		}
		
		$file_system_name = get_rand_file_system_name($file_name);
		 
		// Перевод в байты
	 	$filesize_byte = filesize($_FILE_DATA['tmp_name']);
		
		$date_add = date('Y-m-d H:i:s');
		
		$file_dir = create_upload_folder($date_add);
		$file_upload_path = $file_dir.'/'.$file_system_name;
		
		// перенос файла в папку хранения
		move_uploaded_file($_FILE_DATA['tmp_name'], $file_upload_path);
		
		// Перемещаем файл в папку версий файла
		if(file_exists($file_upload_path))
		{
			 
			$sql = "INSERT INTO tasks_files_versions SET file_id='$file_id', file_name='$file_name', file_system_name='$file_system_name', date_add='$date_add', user_id='".$this->current_user_id."', extension='$extension', size='$filesize_byte'";
			
			$this->db->query($sql);
			
			// Версия
			$version_id = $this->db->get_insert_id();
			
			$sql = "UPDATE tasks_files SET version_id='$version_id', size='$filesize_byte', date_edit=NOW() WHERE file_id='$file_id'";
			
			$this->db->query($sql);
		}
		else 
		{
			return -1;
		} 
		
		return $version_id;
	}
	
	protected function mk_file_dir($file_id)
	{
		$file_dir = $this->get_file_dir($file_id);
		
		if(!is_dir($file_dir))
		{
			mkdir($file_dir);
			chmod($file_dir, 0775);
		}
	}
	
	public function get_file_dir($file_id)
	{
		return FILES_PATH.'/'.$file_id;
	}
	
	
	
	// Проверка расширения файла на блеклист
	function is_file_ext_in_blacklist($file_name)
	{
		// Расширение файла
		$extension = substr($file_name,strrpos($file_name, '.'),20);
		
		 
		if(in_array( $extension, $this->blacklist))
		{
			return 1;
		}
		else return 0;
	}
	
	function copy_file($file_id_old)
	{
		// Выбираем файл
		$sql = "SELECT * FROM tasks_files WHERE file_id='$file_id_old'";
		
		$file_data = $this->db->query_firstrow($sql);	
		
		
		// Копируем файл
		$sql = "INSERT INTO tasks_files SET folder_id='".$file_data['folder_id']."', version_id='', user_id='".$file_data['user_id']."', date_add=NOW(), date_edit=NOW(),file_name='".$file_data['file_name']."', file_desc='".$file_data['file_desc']."', size='".$file_data['size']."', extension='".$file_data['extension']."', is_company='".$file_data['is_company']."', is_content_file='".$file_data['is_content_file']."'";
		
		$this->db->query($sql);
			
		$file_id = $this->db->get_insert_id();
		
		
		// копируем версию файла
		$sql = "SELECT * FROM tasks_files_versions WHERE file_id='$file_id_old'";	
		
		$file_version_data = $this->db->query_firstrow($sql);	
		
		// новое имя файла
		$file_system_name = get_rand_file_system_name($file_version_data['file_name']);
		
		$date_add = date('Y-m-d H:i:s');
		
		$sql = "INSERT INTO tasks_files_versions SET file_id='$file_id', file_name='".$file_version_data['file_name']."', file_system_name='$file_system_name', date_add='$date_add', user_id='".$file_version_data['user_id']."', extension='".$file_version_data['extension']."', size='".$file_version_data['size']."'";
		
		$this->db->query($sql);
		
		// Версия
		$version_id = $this->db->get_insert_id();
		
		
		$file_dir = create_upload_folder($date_add);
		$file_upload_path = $file_dir.'/'.$file_system_name;
		
		
		$in_file_dir = get_download_dir('', $file_version_data['date_add']);
		
		$in_file_path = $in_file_dir.'/'.$file_version_data['file_system_name'];
		
		// Копируем файл
		if(copy($in_file_path, $file_upload_path))
		{
			$sql = "UPDATE tasks_files SET version_id='$version_id', size='".$file_version_data['size']."', date_edit=NOW() WHERE file_id='$file_id'";
			
			$this->db->query($sql);
		}
		
		return $file_id;
	}
}


?>
