add_contact_btn = 0;
// Добавить контакт
function add_contact(id)
{
	var contact_user_name, contact_name, contact_phone, contact_organization, contact_job, contact_desc
	
	if(add_contact_btn)
	{
		return false;
	}
	$('.td_error').html('');
	
	$('.input_text').removeClass('light_error_input')
	
	contact_user_name = $('#contact_user_name').val();
	
	contact_name = $('#contact_name').val();
	
	contact_phone = $('#contact_phone').val();
	
	//contact_organization = $('#contact_organization_0').val();
	
	contact_job = $('#contact_job').val();
	
	contact_desc = $('#contact_desc').val();
	
	add_contact_btn = 1;
	
	loading_btn('add_contact_btn');
	
	$.post('/ajax/ajaxContacts.php', 
	{   
		mode : 'add_contact',
		 
		contact_user_name : contact_user_name,
		contact_name : contact_name,
		contact_phone : contact_phone,
		//contact_organization : contact_organization,
		contact_job : contact_job,
		contact_desc : contact_desc,
		image : images_name_arr[0]
	},
	function(data){ 
		
		add_contact_btn = 0;
		
		loading_btn('add_contact_btn', 1);
		
		if(data['error'])
		{ 
			$.each(data['error'], function(i, j){
					
					error_text = '';
					
					if(i=='contact_name')
					{
						error_text = 'Не указано название контакта';
						light_error_input('contact_name');
					}
					
					
					$("#"+i).next().html(error_text)
					
				})
		}
		if(data['success']==1)
		{  
			// Добавляем добавленный контакт
			get_contact_form(data['contact_id'],0,1);
			
			$('.no_contents').remove();
			$('#contact_name').val('');
			$('#contact_phone').val('');
			$('#contact_organization').val('');
			$('#contact_job').val('');
			$('#contact_desc').val('');
			$('#contact_user_name').val('');
			$('#contact_images_uploaded_image_0').html('');
		}
		 
	}, 'json');
}

// Получает форму для редактирования контакта
function get_contact_form(contact_id, form, prepend)
{
	$('#contact_proc_'+contact_id).html('<img src="/img/loading5.gif">');
	 
	$.post('/ajax/ajaxContacts.php', 
	{   
		mode : 'get_contact_form',
		contact_id : contact_id,
		form : form
	},
	function(data){ 
		
		if(prepend)
		{
			$('#contacts_list').prepend(data);
			draw_background_list_item('contacts_item');
		}
		else
		{
			$('#contact_'+contact_id).replaceWith(data);
			draw_background_list_item('contacts_item');
		}
		 
		 
	});
}


// Получает форму для редактирования контакта
function save_contact(contact_id)
{
	var contact_user_name, contact_name, contact_phone, contact_organization, contact_job, contact_desc
	 
	$('.td_error').html('');
	 
	$('.input_text').removeClass('light_error_input')
	 
	contact_name = $('#contact_name_'+contact_id).val();
	
	contact_user_name = $('#contact_user_name_'+contact_id).val();
	
	contact_phone = $('#contact_phone_'+contact_id).val();
	
	//contact_organization = $('#contact_organization_'+contact_id).val();
	
	contact_job = $('#contact_job_'+contact_id).val();
	
	contact_desc = $('#contact_desc_'+contact_id).val();

	loading_btn('save_contact_btn_'+contact_id)
	  
	$.post('/ajax/ajaxContacts.php', 
	{   
		mode : 'save_contact',
		contact_id : contact_id,
		contact_user_name : contact_user_name,
		contact_name : contact_name,
		contact_phone : contact_phone,
		//contact_organization : contact_organization,
		contact_job : contact_job,
		contact_desc : contact_desc,
		image : images_name_arr[contact_id],
		image_deleted : images_deleted[contact_id]
	},
	function(data){ 
		
		if(data['success']==1)
		{  
			 get_contact_form(contact_id, 0);
		} 
		
		loading_btn('save_contact_btn_'+contact_id, 1)
		
		if(data['error'])
		{ 
			$.each(data['error'], function(i, j){
				
				error_text = '';
				
				if(i=='contact_name')
				{
					error_text = 'Не указано название контакта';
					light_error_input('contact_name_'+contact_id);
				}
				
				
				$("#"+i+"_"+contact_id).next().html(error_text)
				
			})
		}
	
	}, 'json');
}

// Удаляет контакт
function delete_contact(contact_id)
{
	
	$.post('/ajax/ajaxContacts.php', 
	{   
		mode : 'delete_contact',
		contact_id : contact_id
	},
	function(data){ 
		
		if(data==1)
		{
			$('.contact_hide_content_'+contact_id).hide();
			$('#contact_result_'+contact_id).html("<div class='success'>Контакт успешно удален | <a href='javascript:;' onclick='restore_contact("+contact_id+")'>Восстановить</a> | <a href='javascript:;' onclick='$(\"#contact_"+contact_id+"\").remove(); draw_background_list_item(\"contacts_item\");'>Закрыть</a></div>");
		}
	});
}

// Восстановить контакт
function restore_contact(contact_id)
{
	$('#contact_proc_'+contact_id).html('<img src="/img/loading5.gif">');
	
	$.post('/ajax/ajaxContacts.php', 
	{   
		mode : 'restore_contact',
		contact_id : contact_id
	},
	function(data){ 
		
		$('#contact_proc_'+contact_id).html('');
		if(data==1)
		{
			$('.contact_hide_content_'+contact_id).show();
			$('#contact_result_'+contact_id).html('');
		}
	});
}

// отмена сохранения контакт
function cancel_save_contact(contact_id)
{
	loading_btn('cancel_save_contact_btn_'+contact_id)
	
	get_contact_form(contact_id,0,0);
}

contact_actual_page = 1;
default_search_text = '';
// Выводит больше контактов
function get_more_contacts()
{
	var page, search_word;
	
	page = contact_actual_page + 1;

	search_word = $('#contact_search_text').val();
	
	if(search_word == default_search_text)
	{
		search_word = '';
	}
	 
	$.post('/ajax/ajaxContacts.php', 
	{   
		mode : 'get_more_contacts',
		user_id : user_id,
		page : page,
		search_word : search_word,
		is_wks : is_wks
		
	},
	function(data){ 
		
		$('#contacts_list').append(data);
		
		draw_background_list_item('contacts_item');
		
		// Актаульная страница
		contact_actual_page++;
		
		if(contact_actual_page>=pages_count)
		{
			$('#more_contacts_btn').hide();
		}
	});
}

// Поиск контактов
function contact_search()
{
	var search_word;
	
	search_word = $('#contact_search_text').val();
	
	if(search_word == default_search_text)
	{
		search_word = '';
	}
	
	$.post('/ajax/ajaxContacts.php', 
	{   
		mode : 'contact_search',
		search_word : search_word,
		is_wks : is_wks
	},
	function(data){ 
		
		$('#contacts_list').html(data['contacts_list']);
		
		draw_background_list_item('contacts_item');
		
		if(data['pages_count'] > 1)
		{
			$('#more_contacts_btn').show();
		}
		else
		{
			$('#more_contacts_btn').hide();
		}
		pages_count = data['pages_count'];
		contact_actual_page = 1;
		
	}, 'json');
}

 
images_name_arr = {};
images_deleted = {}
function contact_image_upload_init(id)
{
	 
	   new AjaxUpload($('#contact_images_upload_'+id), {  
		  		    action: '/ajax/ajaxUploadImage.php?mode=3',  
		  		    name: 'uploadfile',  
		  		    onSubmit: function(file, ext){
						
						if (!(ext && /^(jpg|png|jpeg|gif)$/i.test(ext))){  
							// check for valid file extension  
							alert('Ошибка. Допускаются только файлы форматов .jpg .gif и .png.')
							return false;  
						} 
						
						$('#contact_images_uploaded_image_'+id).html('<img src="/img/ajax-loader.gif">');
						
						
		  		    },     
		  		    onComplete: function(file, response_data){  
						
						// Разбираем на массив ответ
						response_data_arr = response_data.split('|');
						
						// Стутас загрузки
						response = response_data_arr[0];
						
						 
							
						$('#contact_images_uploaded_image_'+id).html('');
						
						
						images_name_arr[id] = response_data_arr[1];	
						 
						
						if(response=='ok')
						{
							$('#contact_images_uploaded_image_'+id).html('<img src="/temp/'+response_data_arr[1]+'" />');
							
							$('#contact_images_uploaded_image_'+id).append(' <br><a href="javascript:;" class="link" onclick="delete_contact_uploaded_img('+id+')">Удалить</a>')
								
						}
						else if(response=='0')
						{
							alert('Ошибка. Допускаются только файлы форматов .jpg .gif и .png.');
						}
						else if(response=='1')
						{
							alert('Ошибка. Фотография должна иметь размер не меньше '+min_image_resolution+' точек и не больше '+max_image_resolution+' точек по каждой из сторон.');
						}
						else if(response=='2')
						{
							alert('Произошла ошибка при загрузке файла');
						}
							 
							
						}
			}); 
	 
	
}

// Удаляет загруженную картинку
function delete_contact_uploaded_img(id)
{
	images_name_arr[id] = '';
	images_deleted[id] = id;
	$('#contact_images_uploaded_image_'+id).html('');
	$('#contact_images_uploaded_image_'+id).html('')
}
function show_contact_photo_preview(contact_id, close_all)
{
	close_contact_photo_preview(1);
	$('#contact_photo_prev_wrap_'+contact_id+' .prev_block').show(100); 
}
function close_contact_photo_preview(close_all, contact_id)
{
	if(close_all)
	{
		$('.contact_image_wrap .prev_block').hide(100);
	}
	else
	{
		$('#contact_photo_prev_wrap_'+contact_id+' .prev_block').hide(100); 
	}
	 
}