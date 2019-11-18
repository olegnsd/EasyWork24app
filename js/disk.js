Disk = {
	get_content_file_upload_form : function(id, content_type, form_id)
	{
		// form_id - блок для формы
		// content_type - тип контента
		// id - служит внутренним идентификатором для элементов формы добавления файлов
		
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'get_content_file_upload_form',
			content_type : content_type,  
			id : id
		},
		function(data){ 
			 
			 if(form_id)
			 {
				 $('#'+form_id).html(data);
			 }
		}); 
	},
	get_upload_content_files_content_type : function(id)
	{
		return $('#content_file_upload_form_'+id+' #content_file_upload_cont_type_'+id).val();
	},
	
	deleted_content_attached_files : {},
	
	delete_file_content_attached : function(file_id)
	{
		Disk.deleted_content_attached_files[file_id] = file_id;
		$('#cont_file_'+file_id).remove();
	},
	get_content_deleted_files : function()
	{
		return Disk.deleted_content_attached_files;
	},
	// Формирует список файлов, которые были добавлены к контенту
	get_upload_content_files : function(id)
	{
		var result_files = {};
		
		// Биндим кнопку, начать загрузку
		if(Disk.loaded_version=='html5')
		{  
			$('#content_file_upload_form_'+id+' #content_file_upload_'+id+'_queue .uploadifive-queue-item').each(function(i) {
			
				var queue_id = $(this).attr('id');
				
				var queue_num = Disk.get_file_queue_id('', queue_id);
				
				if(Disk.uploads_files[queue_num])
				{
					result_files[queue_num] = Disk.uploads_files[queue_num];
				}
			 
			})
		}
		else if(Disk.loaded_version=='swf')
		{   
			$('#content_file_upload_form_'+id+' #content_file_upload_'+id+'_queue .uploadify-queue-item').each(function(i) {
			
				var queue_id = $(this).attr('id');
				
				var queue_num = Disk.get_file_queue_id_swf('', queue_id);
				 
				if(Disk.uploads_files[queue_num])
				{
					result_files[queue_num] = Disk.uploads_files[queue_num];
				}
			 
			})
		}
		
		
		
		return result_files;
	},
	
	delete_file : function(elem)
	{
		if(!confirm('Действительно удалить?'))
		{
			return '';
		}
		
		 var tmp = elem.split('_');
		
		var id = tmp[1];
		
		var what = tmp[0];
		
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'delete_file',
			elem : elem
		},
		function(data){ 
			 
			 if(data==1)
			 {
				document.location.reload();
			 }
		}); 
	},
	cancel_update_doc_version : function(file_id)
	{
		if(!confirm('Окно будет закрыто и все несохраненные данные будут утеряны'))
		{
			return false;
		}
		$('.fdoc_save_form_notice').hide();
		
		var time = $('#update_doc_version_btn').attr('time');
		
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'cancel_update_doc_version',
			file_id : file_id,
			time : time
		},
		function(data){ 
			 
			 window.close();
			 
		}); 
	},
	update_doc_version : function(file_id)
	{
		simple_loading_btn('update_doc_version_btn');
		
		var time = $('#update_doc_version_btn').attr('time');
		 
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'update_doc_version',
			file_id : file_id,
			time : time
		},
		function(data){ 
			
			simple_loading_btn('update_doc_version_btn', 1);
			$('.fdoc_save_form_notice').hide();
			
			if(data==1)
			{
				alert('Изменения успешно сохранены!');
				window.close();
			}
			if(data!=1)
			{
				alert('Во время сохранения документа произошла неизвестная ошибка');
			}
			
			
		}); 
	},
	open_doc_edit_w : function(id)
	{
		 
 		var params = 'width=1000,height=700,left=200,top=100';
		var time = new Date().getTime();
		
		 
		//$('#update_doc_version_btn').attr('time', time); 
		
		$(document).bind('keyup', function(e){if(e.keyCode==27) {Disk.hide_file_popup()}})
		
		window.open('/disk/doc/edit/'+id+'?first=1&time='+time, 'Редактирование документа', params);
		/*
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'open_file_edit_popup',
			file_id : id
			
		},
		function(data){ 
			
		 	Disk.op(data, id, time)
			 
			
		}); */
		
		 
		 
		
	},
	op : function(t,id, time)
	{
		var params = 'width=900,height=700,left=200,top=100';
		 
	},
	hide_file_popup : function()
	{
		$('#outer_shadow').remove();
		$('body').removeClass('scroll_lock');
		$(document).unbind('keyup');  
	},
	show_file_popup : function(id, what, cont_id)
	{ 
		
		 
			
		var w_height = $(document).height();
		var w_width = $(document).width();
		
		$("#outer_shadow").css('height', w_height);
		  
		$('body').addClass('scroll_lock');  
		$('body').prepend('<div id="outer_shadow"></div>');
		 
		 
		$('#outer_shadow').html('<div class="popup_form" id="popup_1"> \
		<a href="javascript:;" onclick="Disk.hide_file_popup()" class="close"></a> \
		<div class="content"></div> \
		</div>');
		 
		if(what=='doc')
		{ 
			var open_width = 850; 
			var open_height = 860; 
		}
		else
		{
			var open_width = 750; 
			var open_height = ''; 
		}
		 
		
		var top_coord = 20;
		var left_coord =  Number(w_width / 2 - (open_width / 2));
		
		$('.popup_form').css('left', left_coord+'px');
		$('.popup_form').css('top', top_coord ? top_coord+'px' : 'auto');
		
		$('.popup_form').css('width', open_width);
		$('.popup_form').css('height', open_height);
		
		$(document).bind('keyup', function(e){if(e.keyCode==27) {Disk.hide_file_popup()}})
		
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'show_file_popup',
			id : id,
			cont_id : cont_id
			
		},
		function(data){ 
			
			$('#popup_1 .content').html(data);
			
		}); 
		 
	},
	recount_new_files : function(by_user_id)
	{
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'recount_new_files',
			by_user_id : by_user_id,
			recount_mode : 'all'
		},
		function(data){ 
			
			if(data['all_new']) 
			{
				$('#f_all_new_count').html('(+ '+data['all_new']+')');
				$('#new_disk_files_count').html('(+ '+data['all_new']+')');
				 
			}
			else
			{
				$('#f_all_new_count').html('');
				$('#new_disk_files_count').html('');
			}
			
			if(data['by_user_count']) 
			{
				$('#fnew_count_'+by_user_id+' .count').html(data['by_user_count']);
			}
			else
			{ 
				$('#fnew_count_'+by_user_id).remove();
			}
			
		}, 'json'); 
	},
	
	file_confirm : function(elem, by_user_id)
	{
		if($(elem).attr('noticed')==1)
		{
			return '';
		}
		
		var item_str = $(elem).attr('item');
		
		var tmp = item_str.split('_');
		
		var id = tmp[1];
		
		var what = tmp[0];
		
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'file_confirm',
			id : id,
			what : what
		},
		function(data){ 
			
			if(data==1)
			{ 
			 
				$('#item_'+item_str).removeClass('not_confirm_row');
				$('#item_'+item_str).unbind('onclick');
				$('#item_'+item_str).attr('noticed', 1);
				
				Disk.recount_new_files(by_user_id)
			}
			
		}); 
	},
	
	fgroup_actual_pages : {},
	
	show_more_available_files : function(user_id, count_pages)
	{
		if(!Disk.fgroup_actual_pages[user_id])
		{
			Disk.fgroup_actual_pages[user_id] = 2;
		}
		else
		{
			Disk.fgroup_actual_pages[user_id] = Disk.fgroup_actual_pages[user_id] + 1;
		}
		
		var page = Disk.fgroup_actual_pages[user_id];
		
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'show_more_available_files',
			user_id : user_id,
			page : page
		},
		function(data){ 
			
			if(data)
			{
				$('#more_files_tr_'+user_id).before(data);
			}
			
			if(Disk.fgroup_actual_pages[user_id]>=count_pages)
			{
				$('#more_files_tr_'+user_id).remove();
			}
			
			
		});
	},
	
	_sh_time_inp : function(file_id)
	{
		var value = $('#file_pub_time_mode_'+file_id).val();
	
		if(value==0)
		{
			$('#file_pub_time_value_'+file_id).hide();
		}
		else
		{
			$('#file_pub_time_value_'+file_id).show();
		}
	},
	save_file_pub : function(file_id)
	{
		var time_value = $('#file_pub_time_value_'+file_id).val();
		var time_mode = $('#file_pub_time_mode_'+file_id).val();
		var desc = $('#file_pub_desc_'+file_id).val();
		
		loading_btn('save_file_pub_btn_'+file_id);
		
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'save_file_pub',
			file_id : file_id,
			time_value : time_value,
			time_mode : time_mode,
			desc : desc
		},
		function(data){ 
			
		 	loading_btn('save_file_pub_btn_'+file_id, 1);
			
			if(data)
			{
				$('#file_pub_bl_'+file_id).html(data);
				$('#file_pub_form_wrap_'+file_id).hide();
				$('#file_pub_time_mode_'+file_id).val(0);
				$('#file_pub_desc_'+file_id).val('');
				$('#file_pub_time_value_'+file_id).val('');
				$('#item_file_'+file_id+' .name_td .file_pub_icon').remove();
				$('#item_file_'+file_id+' .name_td').prepend('<div class="file_pub_icon" title="Опубликован в интернете"></div>');
			}
			
		});
	},
	delete_file_pub_link : function(file_id, file_name_id)
	{
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'delete_file_pub_link',
			file_id : file_id,
			file_name_id : file_name_id
		},
		function(data){ 
			
		 	if(data==1)
			{
				$('#file_pub_link_wrap_'+file_name_id).remove();
				$('#file_pub_form_wrap_'+file_id).show();
				$('#item_file_'+file_id+' .name_td .file_pub_icon').remove()
			}
			
		});
	},
	open_file_pub_block : function(file_id)
	{
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'open_file_pub_block',
			file_id : file_id
		},
		function(data){ 
			
			Disk.hide_file_tools(0, 1);
			Disk.show_file_act_light('file_'+file_id);
			$('#container_file_'+file_id+' .file_container_form_wrap_in').html(data);
			$('#container_file_'+file_id).show();
			$('#container_file_'+file_id+' .title_form').html('Сделать доступным по ссылке');
			Disk.bind_hide_form(file_id, 'file_container_form');
			
			$('.file_open_popup').bind('click', function(event){
				event.stopPropagation();
			})
			
		});
		
	 
	},
	
	open_access_block : function(elem)
	{
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'get_file_access_block',
			elem : elem
		},
		function(data){ 
			
			Disk.hide_file_tools(0, 1);
			Disk.show_file_act_light(elem);
			$('#container_'+elem+' .file_container_form_wrap_in').html(data);
			$('#container_'+elem).show();
			Disk.bind_hide_form(elem, 'file_container_form');
			
			$('.file_open_popup').bind('click', function(event){
				event.stopPropagation();
			})
			
		});
		
	 
	},
	
	deleted_access : {},
	
	delete_access_user : function(elem, num)
	{
		var user_id = $('#user_access_'+elem+'_'+num+'').val();
		 
		Disk.deleted_access[user_id] = user_id;
		
		$('#user_access_'+elem+'_'+num+'_row').remove();
	},
	save_access : function(elem)
	{
		loading_btn('save_access_btn_'+elem);
		
		var access = {};
		 
		$('#f_access_wrap_'+elem+' .access_row').each(function(){
			 
			var num = $(this).attr('num');
			var user_id = $('#user_access_'+elem+'_'+num).val();
			var access_mode = $('#access_mode_'+elem+'_'+num).val();
			
			access[num] = {'user_id' : user_id, 'access_mode' : access_mode};
		})
		
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'save_access',
			elem : elem,
			access : $.toJSON(access),
			deleted_access : $.toJSON(Disk.deleted_access)
		},
		function(data){ 
			
			loading_btn('save_access_btn_'+elem, 1);
			
			if(data==1)
			{
				document.location.reload();
			}
			 
			 
		});
	},
	add_access_row : function(elem)
	{
		var num = Math.round(Math.random() * 1000000);
		 
		$('#f_access_wrap_'+elem).append('<tr class="access_row" id="user_access_'+elem+'_'+num+'_row" num="'+num+'"> \
				<td> \
			    <select id="user_access_'+elem+'_'+num+'"></select> \
				</td> \
				<td> \
				<div style="position:relative"> \
				<select class="input_text" id="access_mode_'+elem+'_'+num+'"> \
				<option value="1">Чтение</option> \
				<option value="2">Изменение</option> \
				</select></div>\
				</div></td> \
				<td> \
				<div class="edit_tools"><a href="javascript:;" class="delete" onclick="Disk.delete_access_user(\''+elem+'\', \''+num+'\'); "></a></div> \
				</td> \
			</tr>');
			
		
		$('#user_access_'+elem+'_'+num).easycomplete(
		{
			str_word_select : 'Выбрать сотрудника',
			url:'/ajax/ajaxGetUsers.php?by=name',
			width:350,
			trigger : 0
		});
	},
	delete_version_file : function(version_id, file_id)
	{
		if(!confirm('Вы действительно хотите удалить эту версию файла?')) return false;
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'delete_version_file',
			file_id : file_id,
			version_id : version_id
		},
		function(data){ 
			
			 if(data['success']==1)
			 {
				 $('#item_version_'+version_id).remove();
				 $('#file_versions_count').html(data['count']);
			 }
			 
		}, 'json');
	},
	file_version_restore : function(version_id, file_id)
	{
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'file_version_restore',
			file_id : file_id,
			version_id : version_id
		},
		function(data){ 
			
			if(data==1)
			{
				document.location.reload();
			}
			 
		});
	},
	file_edit_desc : function()
	{ 
		var desc = $('#file_desc_text').text();
		 
		$('.file_desc_wrap').hide();
		
		$('#file_desc_text_proc').html('<textarea id="file_desc" class="input_text" style="width:530px">'+desc+'</textarea> \
		<div><br> \
		<a id="save_desc_btn" href="javascript:;" onclick="Disk.save_file_desc()" class="button"><div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a> <div class="button_sep"></div>\
		<a id="" href="javascript:;" onclick="Disk.cancel_save_file_desc()" class="button"><div class="right"></div><div class="left"></div><div class="btn_cont">отменить</div></a> \
		</div>');
		
		$('#file_desc').autoResize();
		
		$('#file_desc').trigger('keydown');
		
		
	},
	save_file_desc : function()
	{
		var desc = $('#file_desc').val();
		
		loading_btn('save_desc_btn');
		
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'save_file_desc' ,
			file_id : file_id,
			desc : desc
		},
		function(data){ 
			
			loading_btn('save_desc_btn', 1);
			
			if(data==1)
			{
				Disk.cancel_save_file_desc(1);
			}
			 
		});
	},
	cancel_save_file_desc : function(update_desc)
	{
		if(update_desc)
		{
			var desc = $('#file_desc').val();
			$('.file_desc_wrap').show();
			$('#file_desc_text').html(desc);
			$('#file_desc_text_proc').html('');
		}
		else
		{
			$('.file_desc_wrap').show();
			$('#file_desc_text_proc').html('');
		}
		  
	},
	update_name : function(elem)
	{
		var name = $('#rename_wrap_'+elem+' input[name="name"]').val();
		
		var tmp = elem.split('_');
		
		var id  = tmp[1];
		var what = tmp[0];
		
		$('#rename_wrap_'+elem+' .proc').html('');
	 
		$.post('/ajax/ajaxUpload.php', 
		{   
			mode : 'update_name' ,
			name : name,
			elem : elem
		},
		function(data){ 
			
			if(data=='-1')
			{
				if(what=='file')
				{
					$('#rename_wrap_'+elem+' .proc').html('<div class="error">Файл с таким названием уже существует.</div>');
				}
				if(what=='folder')
				{
					$('#rename_wrap_'+elem+' .proc').html('<div class="error">Папка с таким названием уже существует.</div>');
				}
			}
			else if(data==1)
			{
				Disk.rename_file_cancel(elem, 1)
			}
			 
		});
	},
	rename_file : function(file_id, folder_id)
	{ 
		var elem = '';
		if(file_id>0)
		{
			elem = 'file_'+file_id;
		}
		else if(folder_id>0)
		{
			elem = 'folder_'+folder_id;
		}
		else return false;
		
		 
		var file_name = $('#name_a_'+elem).text();
		 
		$('#name_a_'+elem).parent().parent('.name_wrap').hide();
		
		$('#act_'+elem).html('<div class="rename_wrap_file" id="rename_wrap_'+elem+'"><input name="name" type="text" class="" style="width:200px" value="'+file_name+'">&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="Disk.update_name(\''+elem+'\')">сохранить</a> <span>|</span> <a href="javascript:;" onclick="Disk.rename_file_cancel(\''+elem+'\')">отмена</a><div class="proc"></div></div>');
		 
		$('#item_'+elem).addClass('row_selected_act');
		
		Disk.hide_file_tools(elem); 
		
	},
	rename_file_cancel : function(elem, update_name)
	{
		// Обновить имя в списке
		if(update_name)
		{
			var name = $('#rename_wrap_'+elem+' input[name="name"]').val();
			$('#name_a_'+elem).text(name);
		}
		
		$('#item_'+elem+' .name_wrap').show();
		$('#rename_wrap_'+elem).html('');
		Disk.hide_file_act_light(elem);
	},
	hide_file_act_light : function(elem)
	{
		$('#item_'+elem).removeClass('row_selected_act');
	},
	show_file_act_light : function(elem)
	{
		$('#item_'+elem).addClass('row_selected');
	},
	hide_file_tools : function(elem, hide_all)
	{
		if(hide_all)
		{
			$('.file_open_popup').hide();
			$('.file_container_form').hide();
			$('.files_tb .f_item').removeClass('row_selected');
		}
		else
		{
			$('#file_tools_'+elem).hide();
			$('#container_file_'+elem).hide();
			$('.files_tb .f_item').removeClass('row_selected');
		}
		 
	},
	 
	show_file_tools : function(elem)
	{
		//$('.file_open_popup').hide();
		
		Disk.hide_file_tools(0, 1);
		
		$('#file_tools_'+elem).show();
		//('.files_tb .f_item').removeClass('row_selected');
		
		Disk.show_file_act_light(elem);
		
		Disk.bind_hide_form(elem, 'file_open_popup');
		
	},
	bind_hide_form : function(elem, class_name)
	{  
	  
		 $('body').unbind('click');
			
			setTimeout(function(){
			 
			$('body').bind('click', function(e,elem) {
				
				e.stopPropagation()
				
				var target = e.target;
				 
				if(!$(target).hasClass(class_name))
				{
					Disk.hide_file_tools(0, 1);
					$('body').unbind('click');
					$('.file_open_popup').unbind('click');
				}
				 
			
			})
			}, 300);
		
	},
	open_create_folder_form: function()
	{
		$('#upload_file_form').hide();
		if($('#upload_folder_create_form').is(":visible"))
		{ 
			$('#upload_folder_create_form').hide();
		}
		else
		{
			$('#upload_create_folder_proc').html('');
			$('#folder_name').val('');
			$('#upload_folder_create_form').show();
		}
	},
	open_file_upload_form : function()
	{  
		$('#upload_folder_create_form').hide();
		if($('#upload_file_form').is(":visible"))
		{ 
			$('#upload_file_form').hide();
			Disk.select_other_file();
		}
		else
		{
			$('#upload_file_form').show();
		}
		 
	},

	
	loaded_version : '',
	
	elem_num : 0,
	
	uploads_files : {},
	init_upload_html5_version : function(elem_id)
	{
		 
		var error_init = 0;
		
		var queueID = elem_id+'_queue'
		
		if(act=='content')
		{   
			var queueSizeLimit = 100;
			var uploadLimit = 100;
			var buttonText = 'Добавить файл';
			var auto = true;
			var multi = true;
			 
			var item_tpl = '<div class="uploadifive-queue-item"><a class="close" href="#">X</a>\
                       <div><span class="filename"></span><span class="fileinfo"></span></div>\
                        <div class="progress">\
                            <div class="progress-bar"></div>\
                        </div>\
                    </div>';
		}
		else
		{
			var queueSizeLimit = 1;	
			var uploadLimit = 1;
			var buttonText = 'Выбрать файл';
			var auto = false;
			var multi = false;
			var item_tpl = '<div class="uploadifive-queue-item">\
                       <div><span class="filename"></span><span class="fileinfo"></span></div>\
                        <div class="progress">\
                            <div class="progress-bar"></div>\
                        </div>\
                    </div>';
		}
	  
		$('#'+elem_id).uploadifive({
			'onUpload' : function() {
				$('#'+elem_id).data('uploadifive').settings.formData = 
					{
						'mode' : 'upload_file',
						'file_name' : act!='content' ? $('#upload_file_name').val() : '', 
						'file_desc' : act!='content' ? $('#upload_file_desc').val() : '',  
						'timestamp' : timestamp,
						'token'     : token,
						'folder_id' : folder_id,
						'upload_version_file' : upload_version_file,
						'act' : act
					}
			},	
			 //'onSelect' : function(queue) { if(queue.errors>0) {Disk.select_file_error('error')}},			   
			'auto'             : auto,
			'multi' : multi,
			'uploadLimit' : uploadLimit,
			'queueSizeLimit' : queueSizeLimit,
			'height' : 20,
			'buttonText' : buttonText,
			'queueID'          : queueID,
			'fileSizeLimit' : max_upload_size_limit+'MB',
			'fileObjName' : 'upload_file',
			'onError' : function() {  }, 
			'onAddQueueItem' : function(file) {
				
				Disk.elem_num++;
				
				if(act!='content') Disk.open_after_on_select(file, Disk.elem_num)
			}, 
			 
			'uploadScript'     : '/ajax/ajaxUpload.php',
			
			'onInit' : function() { Disk.loaded_version='html5' },
			'onFallback' : function() { error_init = 1;},
			'itemTemplate' : item_tpl,
			'onUploadComplete' : function(file, data) {
				
				var queue_id = Disk.get_file_queue_id(file);
				
				// Заполняем массив загрузкой файлов
				Disk.uploads_files[queue_id] = data;
				
				Disk.file_upload_complete(file, data, elem_id, queue_id);
				
			},
			//'onCancel' : function(file){ $.each(file, function(i,j){alert(i+' '+j)}   )}
			
		});
		
		// Если нет ошибки загрузки версии html5
		if(!error_init)
		{
			 //$('#to_upload_file_btn').bind('click', function() {$('#file_upload').uploadifive('upload');});
		}
		 
	},
	
	init_upload_swf_version : function(elem_id)
	{	
		var queueID = elem_id+'_queue'
		
		if(act=='content')
		{  
			var queueSizeLimit = 10;	
			var uploadLimit = 10;
			var auto = true;
		}
		else
		{
			var queueSizeLimit = 1;	
			var uploadLimit = 1;
			var auto = false;
		}
		
		$('#'+elem_id).uploadify({
			'onUploadStart' : function(file) {
		  
			$("#"+elem_id).uploadify("settings", "formData",  
			{
				'mode' : 'upload_file',
				'file_name' : act!='content' ? $('#upload_file_name').val() : '', 
				'file_desc' : act!='content' ? $('#upload_file_desc').val() : '',  
				'timestamp' : timestamp,
				'token'     : token,
				'folder_id' : folder_id,
				'upload_version_file' : upload_version_file,
				'act' : act
			}
			)},
			'queueID'          : queueID,
			'method' : 'post',		   
			'auto' : auto,
			'uploader' : '/ajax/ajaxUpload.php', 
			'swf'      : '/js/upload/uploadify.swf',
			'onSelect' : function(file) {Disk.elem_num++;Disk.open_after_on_select(file, Disk.elem_num)},
			'onInit' : function() { Disk.loaded_version='swf' },
			'onUploadSuccess' : function(file, data) { Disk.file_upload_complete(file, data) }
		});
		
		//$('#to_upload_file_btn').bind('click', function() {$('#file_upload').uploadify('upload', '*');});
	},
	
	init_upload_swf_version11 : function(elem_id)
	{	
		var queueID = elem_id+'_queue'
		
		if(act=='content')
		{  
			 
			 
			
			var queueSizeLimit = 100;
			var uploadLimit = 100;
			var buttonText = 'Добавить файл';
			var auto = true;
			var multi = true;
			 
			var item_tpl = '<div id="${fileID}" class="uploadify-queue-item">\
			<div class="cancel">\
                        <a href="javascript:$(\'#${instanceID}\').uploadify(\'cancel\', \'${fileID}\')">X</a>\
                    </div>\
				<span class="fileName">${fileName} </span><span class="data"></span><span class="fileinfo"></span>\
				<div class="uploadify-progress">\
					<div class="uploadify-progress-bar"></div>\
				</div>\
			</div>';
					
		}
		else
		{
			var queueSizeLimit = 1;	
			var uploadLimit = 1;
			var buttonText = 'Выбрать файл';
			var auto = false;
			
			var multi = false;
			var item_tpl = '<div id="${fileID}" class="uploadify-queue-item">\
				<span class="fileName">${fileName} </span><span class="data"></span><span class="fileinfo"></span>\
				<div class="uploadify-progress">\
					<div class="uploadify-progress-bar"></div>\
				</div>\
			</div>';
		}
	 
		$('#'+elem_id).uploadify({
			'onUploadStart' : function(file) {
			  
			    $("#"+elem_id).uploadify("settings", "formData",  
				{
					'mode' : 'upload_file',
					'file_name' : act!='content' ? $('#upload_file_name').val() : '', 
					'file_desc' : act!='content' ? $('#upload_file_desc').val() : '',  
					'timestamp' : timestamp,
					'token'     : token,
					'folder_id' : folder_id,
					'upload_version_file' : upload_version_file,
					'act' : act
				}
			)},
			 
			//'overrideEvents' : ['onSelectError', 'onUploadError'],
			'method' : 'post',		   
			'auto' : auto,
			'queueSizeLimit' : queueSizeLimit,
			'uploadLimit' : uploadLimit,
			'height' : 20,
			'buttonText' : buttonText, 
			'queueID'          : queueID, 
			'buttonText' : 'Выбрать файл',
			'fileSizeLimit' : max_upload_size_limit+'MB',
			'fileObjName' : 'upload_file',
			'multi' : multi,
			'removeCompleted' : false,
			'onSelect' : function(file) {
				 Disk.elem_num++;Disk.open_after_on_select(file, Disk.elem_num) 
			},
			'uploader' : '/ajax/ajaxUpload.php', 
			'swf'      : '/js/upload/uploadify.swf',
			'onInit' : function() { Disk.loaded_version='swf' },
			'onFallback' : function(){ alert('Ошибка flash')},
			'itemTemplate' : item_tpl,
			'onUploadSuccess' : function(file, data, response) { 
			 
				var queue_id = Disk.get_file_queue_id_swf(file);
				 
				// Заполняем массив загрузкой файлов
				Disk.uploads_files[queue_id] = data;
				  
				Disk.file_upload_complete(file, data);
			}
		});
		
		//$('#to_upload_file_btn').bind('click', function() {$('#file_upload').uploadify('upload', '*');});
	},
	
	get_file_queue_id : function(file, queue_elem_id)
	{
		if(!queue_elem_id)
		{
			var queue = file['queueItem'][0];
		
			var queue_elem_id = $(queue).attr('id');
		}
		
		var tmp = queue_elem_id.split('-');
		
		var queue_id = tmp[3];
		
		return queue_id;
	},
	
	get_file_queue_id_swf : function(file, queue_elem_id)
	{
		 
		
		if(!queue_elem_id)
		{ 
			//var queue = file['queueItem'][0];
		//alert(file['queueItem'][0])
			var queue_elem_id = file['id'];
		}
		
		var tmp = queue_elem_id.split('_');
		
		var queue_id = tmp[2];
		 
		return queue_id;
	},
	
	file_upload_complete : function(file, data, elem_id, queue_id)
	{
		 
		if(act=='content')
		{
			if(data<0)	
			{
				alert('Не удалось загрузить файл. Тип файла не поддерживается.');
			 	
				$('#'+elem_id).uploadifive('cancel', $('#uploadifive-'+elem_id+'-file-'+queue_id).data('file'))
				
				//Disk.cancel_file_upload_aueue(elem_id);
			}
			
			return;
		}
		 
		if(data>0)
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
		
	//	alert(file.name+' '+data)
	},
	select_file_error : function(type)
	{
		if(act=='content')
		{
			return '';
		}
		
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
			Disk.cancel_file_upload_aueue();
		 	$('#upload_file_proc').html('<div class="error_box display">'+err_msg+'</div>');
			 
		}
		
		 
	},
	cancel_file_upload_aueue : function(id, is_content_file)
	{
		var elem_id = 'file_upload';
		
		if(is_content_file)
		{
			elem_id = "content_file_upload_"+id;
		}
		else if(id)
		{
			elem_id = id;
		}
		 

		// Биндим кнопку, начать загрузку
		if(Disk.loaded_version=='html5')
		{  
			$('#'+elem_id).uploadifive('clearQueue');
			  
		}
		else if(Disk.loaded_version=='swf')
		{   
			$('#'+elem_id).uploadify('cancel');
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
	
	open_after_on_select : function(file, elem_num)
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
				// Ошибка
				if(act=='content')
				{ 
					Disk.cancel_upload_queue(elem_num);
				}
				else
				{
					Disk.select_file_error('incorrect_type', elem_num); 
				}
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
	
	cancel_upload_queue : function(id)
	{
		var elem_id = Number(id-1);
	 
		 
		// Биндим кнопку, начать загрузку
		if(Disk.loaded_version=='html5')
		{  
			$('#uploadifive-file_upload-file-'+elem_id).addClass('error');
			$('#uploadifive-file_upload-file-'+elem_id+' .fileinfo').html(' - <span style="color:red">Тип файла не поддерживается</span>');
			$('#uploadifive-file_upload-file-'+elem_id+' .progress').remove();
			 
		}
		else if(Disk.loaded_version=='swf')
		{   
			$('#SWFUpload_0_'+elem_id).addClass('error');
			$('#SWFUpload_0_'+elem_id+' .fileinfo').html(' - <span style="color:red">Тип файла не поддерживается</span>');
			  
		}
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
