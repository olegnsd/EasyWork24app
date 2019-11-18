File = {
	
	loaded_version : '',
	
	init_upload_html5_version : function()
	{
		// 
		var error_init = 0;
		
	 	
		$('#file_upload').uploadifive({
			'onUpload' : function() {
				$('#file_upload').data('uploadifive').settings.formData = 
					{
						'mode' : 'upload_file',
						'file_name' : $('#upload_file_name').val(), 
						'file_desc' : $('#upload_file_desc').val(),  
						'timestamp' : timestamp,
						'token'     : token,
						'folder_id' : folder_id,
						'upload_version_file' : upload_version_file,
						'act' : act
					}
			},	
			 //'onSelect' : function(queue) { if(queue.errors>0) {Disk.select_file_error('error')}},			   
			'auto'             : false,
			'multi' : false,
			'uploadLimit' : 10,
			'queueSizeLimit' : 1,
			'height' : 20,
			'buttonText' : 'Выбрать файл',
			'queueID'          : 'queue',
			'fileSizeLimit' : max_upload_size_limit+'MB',
			'fileObjName' : 'upload_file',
			'onError' : function() {  }, 
			'onAddQueueItem' : function(file, data) {Disk.open_after_on_select(file, data)}, 
			'uploadScript'     : '/ajax/ajaxUpload.php',
			
			'onInit' : function() { Disk.loaded_version='html5' },
			'onFallback' : function() { error_init = 1; Disk.init_upload_swf_version()},
			'itemTemplate' : '<div class="uploadifive-queue-item">\
                       <div><span class="filename"></span><span class="fileinfo"></span></div>\
                        <div class="progress">\
                            <div class="progress-bar"></div>\
                        </div>\
                    </div>',
			'onUploadComplete' : function(file, data) { Disk.file_upload_complete(file, data) }
			
		});
		
		// Если нет ошибки загрузки версии html5
		if(!error_init)
		{
			 //$('#to_upload_file_btn').bind('click', function() {$('#file_upload').uploadifive('upload');});
		}
		 
	},
	
	init_upload_swf_version : function()
	{	
		$('#file_upload').uploadify({
			'onUploadStart' : function(file) {
			  
			    $("#file_upload").uploadify("settings", "formData",  
				{
					'mode' : 'upload_file',
					'file_name' : $('#upload_file_name').val(), 
					'file_desc' : $('#upload_file_desc').val(),  
					'timestamp' : timestamp,
					'token'     : token,
					'folder_id' : folder_id,
					'upload_version_file' : upload_version_file,
					'act' : act
				}
			)
						 
			},
			 
			//'overrideEvents' : ['onSelectError', 'onUploadError'],
			'method' : 'post',		   
			'auto' : false,
			'queueSizeLimit' : 1,
			'uploadLimit' : 10,
			'height' : 20,
			'buttonText' : 'Выбрать файл', 
			'queueID'          : 'queue', 
			'buttonText' : 'Выбрать файл',
			'fileSizeLimit' : max_upload_size_limit+'MB',
			'fileObjName' : 'upload_file',
			'multi' : false,
			'removeCompleted' : false,
			'onSelect' : function(file) {Disk.open_after_on_select(file)},
			'uploader' : '/ajax/ajaxUpload.php', 
			'swf'      : '/js/upload/uploadify.swf',
			'onInit' : function() { Disk.loaded_version='swf' },
			'onFallback' : function(){ alert('Ошибка flash')},
			'itemTemplate' : '<div id="${fileID}" class="uploadify-queue-item">\
				<span class="fileName">${fileName} </span><span class="data"></span>\
				<div class="uploadify-progress">\
					<div class="uploadify-progress-bar"><!--Progress Bar--></div>\
				</div>\
			</div>',
			'onUploadSuccess' : function(file, data) { Disk.file_upload_complete(file, data) }
		});
		
		 
	},
	
	file_upload_complete : function(file, data)
	{
		 
		if(data==1)
		{
			document.location.reload();
		}
		else if(data=='-1')
		{  
			Disk.select_other_file();
			Disk.select_file_error('size_limit');
		}
		else if(data=='-2')
		{
			Disk.select_other_file();
			Disk.select_file_error('incorrect_type');
		}
		else
		{
			Disk.select_other_file();
			Disk.select_file_error('error');
		}
	},
	select_file_error : function(type)
	{
		var err_msg = '';
		
		switch(type)
		{
			case 'error':
				
				err_msg = 'При загрузке файла возникла ошибка. Попробуйте перезагрузить страницу.';
				
			break;
			case 'incorrect_type':
				err_msg = 'Не удалось загрузить файл. Тип файла не поддерживается.';
				 
			break;
			
			case 'size_limit':
			 
				err_msg = 'Файл слишком большой.';
				 
			break;
			
			case 'file_exists':
			
				err_msg = 'Файл уже существует.';
				
			break;
		}
		
		if(err_msg)
		{
		 	// Биндим кнопку, начать загрузку
			if(Disk.loaded_version=='html5')
			{  
				$('#file_upload').uploadifive('clearQueue');
				  
			}
			else if(Disk.loaded_version=='swf')
			{   
				$('#file_upload').uploadify('cancel');
			}
			 
			$('#upload_file_proc').html('<div class="error_box display">'+err_msg+'</div>');
			 
		}
		
		 
	},
	check_file_for_exists : function()
	{
		 
		
		var file_name = $('#upload_file_name').val();
		
		// Проверка разрешения файла
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'check_file',
			check_for : 'exists',
			file_name : file_name,
			folder_id : folder_id,
			selected_file_name : Disk._selected_file_name,
			act : act
		},
		function(data){ 
			
			if(data['check_result']=='file_exists')
			{  
				$('#upload_notice').html('<div style="padding:8px; border:1px solid; border-radius:4px"><b style="color:red">!</b> Файл с таким именем уже существует и будет загружена его новая версия или вы можете <a href="javascript:;" onclick="$(\'#upload_file_info_btn\').trigger(\'click\')">изменить его название</a>.</div>');
				
				$('#to_upload_file_btn span').html(Disk._new_version_upload_btn_name);
			}
			else
			{
				$('#upload_notice').html('');
				$('#to_upload_file_btn span').html(Disk._default_upload_btn_name);
			}
			
		}, 'json');
	},
	
	_default_upload_btn_name : 'Загрузить выбранный файл',
	_new_version_upload_btn_name : 'Загрузить новую версию файла',
	
	_selected_file_name : '',
	
	open_after_on_select : function(file)
	{
		$('#upload_file_proc').html('');
		
		Disk._selected_file_name = file['name'];
		$('#upload_file_name').val(file['name']);
		
		Disk.check_file_for_exists();
		
		// Проверка разрешения файла
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'check_file',
			file_name : file['name'],
			folder_id : folder_id,
			upload_version_file : upload_version_file,
			act : act
		},
		function(data){ 
			
			if(data['check_result']=='in_blacklist')
			{
				Disk.select_file_error('incorrect_type'); 
			}
			else
			{	
				$('#upload_file_info_btn').show();
				
				$('#after_upl_on_select').show();
		
				$('#upl_wrap_btn').css('height', '0px');
				$('#upl_wrap_btn').css('overflow', 'hidden');
				
				//$('#upload_file_info').show();
				
				
				// Биндим кнопку, начать загрузку
				if(Disk.loaded_version=='html5')
				{  
					$('#to_upload_file_btn').bind('click', function() {$('#file_upload').uploadifive('upload');});
				}
				else if(Disk.loaded_version=='swf')
				{   
					$('#to_upload_file_btn').bind('click', function() {$('#file_upload').uploadify('upload', '*');});
				}
			 
			}
		}, 'json');
		 
	},
	select_other_file : function(file)
	{
		$('#to_upload_file_btn span').html(Disk._default_upload_btn_name);
		
		$('#upload_notice').html('');
		
		$('#upload_file_proc').html('');
		
		$('#to_upload_file_btn').unbind('click');
		
		if(Disk.loaded_version=='html5')
		{
			$('#file_upload').uploadifive('cancel', $('.uploadifive-queue-item').data('file'));
		}
		else if(Disk.loaded_version=='swf')
		{  
			$('#file_upload').uploadify('cancel', '*');
		}
		
		$('#upl_wrap_btn').css('height', 'auto');
		$('#upl_wrap_btn').css('overflow', 'hidden');
		 
		$('#after_upl_on_select').hide();
		 
		$('#upload_file_info_btn').hide();
		$('#upload_file_info').hide();
		
		$('#upload_file_name').val('');
		$('#upload_file_desc').val('');
	},
	
	create_folder : function()
	{
		$('#upload_create_folder_proc').html('');
		
		folder_name = $('#folder_name').val();
		
		loading_btn('create_folder_btn');
		
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'create_folder',
			folder_name : folder_name,
			folder_id : folder_id,
			act:act
		},
		function(data){ 
			
			loading_btn('create_folder_btn', 1);
			
			if(data>0)
			{
				document.location.reload();
			}
			else if(data=='-1')
			{
				$('#folder_name').focus();
			}
			else if(data=='-2')
			{
				$('#upload_create_folder_proc').html('<div class="error_box display">Папка с таким именем уже существует</div>');
			}
			
		});
	},
	
	
	
	
	
}
