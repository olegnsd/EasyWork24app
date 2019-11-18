create_form_btn = 0;
function create_folder()
{
	var folder_name;
	
	if(create_form_btn==1)
	{
		return;
	}
	
	$('#create_folder_error').html('');
	
	folder_name = $('#folder_name').val();
	
	
	$('#create_folder_error').html('');
	
	loading_btn('add_folder_btn')
	
	create_form_btn = 1;
	
	$.post('/ajax/ajaxFiles.php', 
	{   
		mode : 'create_folder',
		folder_name : folder_name,
		is_sharing : is_sharing
	
	},
	function(data){ 
		
		create_form_btn = 0;
		
		loading_btn('add_folder_btn', 1) 
		
		if(data['error'])
		{
			if(data['error']['folder_name'])
			{  
				$('#create_folder_proc').html('');
				
				$('#folder_name').focus();
				//$('#create_folder_error').html('Название папки не может быть пустым');
			}
		}
		else if(data['success']==1)
		{
			document.location.reload();
		}
		
	}, 'json');
}

// Удалить файл пользователя
function delete_file(file_id)
{
	if(file_id)
	{
		if(!confirm('Вы уверены, что хотите удалить файл?'))
		{
			return false;
		}
			 
	}
	 
	$.post('/ajax/ajaxFiles.php', 
	{   
		mode : 'delete_file',
		file_id : file_id
	},
	function(data){ 
		
		if(data==1)
		{
			document.location.reload();
		}
		
	});
}


// Удалить файл пользователя
function delete_folder(folder_id)
{
	if(folder_id)
	{
		if(!confirm('Вы уверены, что хотите удалить папку?'))
		{
			return false;
		}
	}
	 
	$.post('/ajax/ajaxFiles.php', 
	{   
		mode : 'delete_folder',
		folder_id : folder_id
	
	},
	function(data){ 
		
		if(data==1)
		{
			document.location.reload();
		}
		
	});
}

// Инициализация кнопки загрузки файла
function upload_init()
{
	 
	   new AjaxUpload($('#uploadfile'), {  
		  		    action: '/ajax/ajaxUploadFile.php?is_sharing='+is_sharing+'&folder_id='+folder_id,  
		  		    name: 'uploadfile',  
		  		    onSubmit: function(file, ext){  
						$('#upload_proc').html('<img src="/img/ajax-loader.gif" />')
		  		    },     
		  		    onComplete: function(file, response){  
					
						$('#upload_proc').html('');
						
						if(response=='2'){  
							
							document.location.reload();
						}
						else
						{  
							if(response=='0')
							{
								$('#upload_proc').html('')
								alert('Файл с таким именем уже существует');
							}
							if(response=='1')
							{
								$('#upload_proc').html('')
								alert('Произошла ошибка при загрузке файла');
							}
							if(response=='3')
							{
								$('#upload_proc').html('')
								alert('Достигнуто ограничение на размер загружаемого файла');
							}
						}
		  		    }      
		  		       
		  	});
	 
	
}

// Дать доступ к файлу
function give_access_to_file(folder_id, file_id, user_id)
{
	$('#access_proc_'+folder_id+'_'+file_id).html('<img src="/img/ajax-loader.gif">');
	
	$.post('/ajax/ajaxFiles.php', 
	{   
		mode : 'give_access_to_file',
		folder_id : folder_id,
		file_id : file_id,
		user_id : user_id
	},
	function(data){ 
		
		$('#access_proc_'+folder_id+'_'+file_id).html('');
		
		if(data=='-1')
		{
			alert('Вы не можете убрать разрешение на просмотр файла, так как не Вы давали доступ к нему');
		}
		if(data==1)
		{
			$('#user_'+folder_id+'_'+file_id+'_'+user_id).removeClass('access_active')
		}
		if(data==2)
		{
			$('#user_'+folder_id+'_'+file_id+'_'+user_id).addClass('access_active')
		}
		
	});
}

// Показать блок настройки доступа к файлам и папкам
function show_access_file_block(folder_id, file_id)
{
	close_files_slide_blocks();
	
	$('#owner_block_'+folder_id+'_'+file_id).show();
}

// Показать блок настройки доступа к файлам и папкам
function show_edit_desc_block(folder_id, file_id)
{
	close_files_slide_blocks();
	
	$.post('/ajax/ajaxFiles.php', 
	{   
		mode : 'get_file_desc',
		folder_id : folder_id,
		file_id : file_id
	},
	function(data){ 
		 
		$('#desc_text_'+folder_id+'_'+file_id).val(data);
		$('#edit_file_desc_block_'+folder_id+'_'+file_id).show();
		 
	});
}

// Сохранить опсиание файла или папки
function save_file_desc(folder_id, file_id)
{
	var desc;
	
	desc = $('#desc_text_'+folder_id+'_'+file_id).val();
	
	loading_btn('save_file_desc_btn_'+folder_id+'_'+file_id);
	
	$.post('/ajax/ajaxFiles.php', 
	{   
		mode : 'save_file_desc',
		folder_id : folder_id,
		file_id : file_id,
		desc : desc
	},
	function(data){ 
		
		loading_btn('save_file_desc_btn_'+folder_id+'_'+file_id, 1);
		
		if(desc=='')
		{
			desc = 'Без описания.';
		}
		// Обновляем данные в описании при наведении
		if(file_id)
		{
			$('#file_desc_'+file_id).html(desc);
		}
		if(folder_id)
		{
			$('#folder_desc_'+folder_id).html(desc);
		}
		$('#edit_file_desc_block_'+folder_id+'_'+file_id).hide();
		
	});
}

// Показать блок статуса файла
function show_file_status_block(folder_id, file_id)
{
	close_files_slide_blocks();
	
	$.post('/ajax/ajaxFiles.php', 
	{   
		mode : 'get_file_statuses',
		folder_id : folder_id,
		file_id : file_id
	},
	function(data){ 
		 
		 
		$('#file_status_content_'+folder_id+'_'+file_id).html(data);
		$('#status_file_desc_block_'+folder_id+'_'+file_id).show();
		 
	});
}

add_status_btn = 0
// Добавление статуса к документу
function add_file_status(file_id, status_id)
{
	var status_text;
	
	if(add_status_btn)
	{
		return false;
	}
	status_text = $('#status_text_'+file_id).val();
	
	//$('#file_status_proc_'+file_id).html('<img src="/img/loading5.gif">');
	
	add_status_btn = 1;
	
	loading_btn('file_status_btn_'+file_id+'_'+status_id);
	
	$.post('/ajax/ajaxFiles.php', 
	{   
		mode : 'add_file_status',
		file_id : file_id,
		status_id : status_id,
		status_text : status_text
	},
	function(data){ 
		
		add_status_btn = 0;
		
		loading_btn('file_status_btn_'+file_id+'_'+status_id, 1);
		
		if(data['success']==1)
		{
			$('#status_text_'+file_id).val('');
			
			$('#file_statuses_list_'+file_id).html(data['statuses_list'])
			
		}
		 
	}, 'json');
}


function close_files_slide_blocks()
{
	$('.file_hide_block').hide();
}

function confirm_file(folder_id, file_id)
{
	$.post('/ajax/ajaxFiles.php', 
	{   
		mode : 'confirm_file',
		file_id : file_id,
		folder_id : folder_id
	},
	function(data){ 
		
		if(data['success']==1)
		{
			$('#confirm_bl_'+folder_id+'_'+file_id).remove();
			
			if(file_id > 0)
			{
				$('#file_'+file_id).removeClass('not_confirm');
			}
			if(folder_id>0 && file_id == 0)
			{
				if(!$('#folder_'+folder_id).hasClass('not_confirm_file_in_folder'))
				{
					$('#folder_'+folder_id).removeClass('not_confirm');
				}
			}
			
			if(data['new_files_count']>=1)
			{
				$('.new_av_files_count').html('(+ '+data['new_files_count']+')');
			}
			else
			{
				$('.new_av_files_count').html('');
			}
			
		}
		 
	}, 'json');
}

function hide_file(folder_id, file_id)
{
	$.post('/ajax/ajaxFiles.php', 
	{   
		mode : 'hide_file',
		file_id : file_id,
		folder_id : folder_id
	},
	function(data){ 
		
		if(data['success']==1)
		{
			if(file_id > 0)
			{
				$('#file_'+file_id).remove();
			}
			if(folder_id>0 && file_id == 0)
			{
				$('#folder_'+folder_id).remove();
				
			}
			
			if(data['new_files_count']>=1)
			{
				$('.new_av_files_count').html('(+ '+data['new_files_count']+')');
			}
			else
			{
				$('.new_av_files_count').html('');
			}
			
		}
		 
	}, 'json');
}