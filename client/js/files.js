create_form_btn = 0;
function client_create_folder()
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
	
	$.post('/client/ajax/ajaxFiles.php', 
	{   
		mode : 'client_create_folder',
		folder_name : folder_name,
		from_user_id : from_user_id,
		from_client_id : from_client_id,
		client_id : client_id
	
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
function client_files_upload_init()
{
	 
	   new AjaxUpload($('#uploadfile'), {  
		  		    action: '/client/ajax/ajaxUploadFile.php?client_id='+client_id+'&folder_id='+folder_id+'&from_user_id='+from_user_id+'&from_client_id='+from_client_id,  					
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
// Сохранить описание файла или папки
function save_file_desc(folder_id, file_id)
{
	var desc;
	
	desc = $('#desc_text_'+folder_id+'_'+file_id).val();
	
	$.post('/ajax/ajaxFiles.php', 
	{   
		mode : 'save_file_desc',
		folder_id : folder_id,
		file_id : file_id,
		desc : desc
	},
	function(data){ 
		
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
		$('#edit_desc_block_'+folder_id+'_'+file_id).hide();
		
	});
}


// Показать блок настройки доступа к файлам и папкам
function show_edit_desc_block(folder_id, file_id)
{
	close_files_slide_blocks();
	
	$.post('/client/ajax/ajaxFiles.php', 
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
	
	$.post('/client/ajax/ajaxFiles.php', 
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
	 
	$.post('/client/ajax/ajaxFiles.php', 
	{   
		mode : 'delete_client_file',
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
	 
	$.post('/client/ajax/ajaxFiles.php', 
	{   
		mode : 'delete_client_folder',
		folder_id : folder_id
	
	},
	function(data){ 
		
		if(data==1)
		{
			document.location.reload();
		}
		
	});
}

function close_files_slide_blocks()
{
	$('.file_hide_block').hide();
}