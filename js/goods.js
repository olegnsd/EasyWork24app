// Добавление нового имущества
add_good_btn = 0;
function add_new_good()
{
	var good_name, images, good_price;
	
	if(add_good_btn)
	{
		return false;
	}
	
	$('.td_error').html('');
	
	$('.input_text').removeClass('light_error_input');
	
	good_name = $('#good_name').val();
	
	good_price = $('#good_price').val();
	
	images = $.toJSON(images_name_arr);
	
	loading_btn('add_good_btn')
	
	add_good_btn = 1;
	
	$.post('/ajax/ajaxGoods.php', 
	{   
		mode : 'add_new_good',
		good_name : good_name,
		images : images,
		good_price : good_price
		
	
	},
	function(data){ 
		
		loading_btn('add_good_btn', 1)
		
		add_good_btn = 0;
		
		if(data['error'])
		{
			$.each(data['error'], function(i, j){
				error_text = '';
						 
				if(i=='good_name')
				{ 
					error_text = 'Название не может быть пустым';
					light_error_input('good_name');
				}
				
				$("#"+i).next().html(error_text);
			
			})
		}
		else if(data['success']==1)
		{ 
			$('#good_name').val('');
			
			images_name_arr = {};
			
			$('.upload_good_image_bl[default!=1]').remove();
			$('#goods_images_uploaded_image_1').html('');
			$('#goods_images_uploaded_delete_1').html('');
			
			$('.no_contents').remove();
			
			get_good_form(data['good_inserted_id'], 0, 1)
		}
		
	}, 'json');
}


// Получает форму для редактирования клиента
function get_good_form(good_id, form, prepend)
{
	$.post('/ajax/ajaxGoods.php', 
	{   
		mode : 'get_good_form',
		good_id : good_id,
		form : form
	},
	function(data){ 
		
		if(prepend)
		{
			$('#goods_list').prepend(data);
			draw_background_list_item("goods_item");
		}
		
		 
		 
	});
}

// Удаляет контакт
function delete_good(good_id)
{
	$.post('/ajax/ajaxGoods.php', 
	{   
		mode : 'delete_good',
		good_id : good_id
	},
	function(data){ 
		
		if(data==1)
		{
			$('#good_content_'+good_id).hide();
			$('#good_result_'+good_id).html("<div class='success'>Имущество успешно удалено | <a href='javascript:;' onclick='restore_good("+good_id+")'>Восстановить</a> | <a href='javascript:;' onclick='$(\"#good_"+good_id+"\").remove(); draw_background_list_item(\"goods_item\")'>Закрыть</a></div>");
		}
	});
}

// Восстановить контакт
function restore_good(good_id)
{
	$.post('/ajax/ajaxGoods.php', 
	{   
		mode : 'restore_good',
		good_id : good_id
	},
	function(data){ 
		
		if(data==1)
		{
			$('#good_content_'+good_id).show();
			$('#good_result_'+good_id).html('');
		}
	});
}


goods_actual_page = 1;

// Выводит больше имущест
function get_more_goods()
{
	var page, search_word;
	
	page = goods_actual_page + 1;

	$.post('/ajax/ajaxGoods.php', 
	{   
		mode : 'get_more_goods',
		user_id : user_id,
		page : page
	},
	function(data){ 
		
		$('#goods_list').append(data);
		
		draw_background_list_item("goods_item");
		// Актаульная страница
		goods_actual_page++;
		
		if(goods_actual_page>=pages_count)
		{
			$('#more_goods_btn').hide();
		}
	});
}

save_good_btn = 0
function save_good(good_id)
{
	var good_name, images, good_price;
	
	if(save_good_btn)
	{
		return false;
	}
	
	$('.td_error').html('');
	
	$('.input_text').removeClass('light_error_input');
	
	good_name = $('#good_name').val();
	
	good_price = $('#good_price').val();
	
	// новые для загрузки изображения
	images = $.toJSON(images_name_arr);
	
	// Удаленные изображения
	deleted_images = $.toJSON(deleted_good_images);
	
	// Заменяемые изображения
	images_names_replaces = $.toJSON(images_names_replaces);
	
	loading_btn('save_good_btn');
	
	save_good_btn = 1;
	
	$.post('/ajax/ajaxGoods.php', 
	{   
		mode : 'save_good',
		good_id : good_id,
		good_name : good_name,
		images : images,
		deleted_images : deleted_images,
		images_names_replaces : images_names_replaces,
		good_price : good_price
		
	
	},
	function(data){ 
		
		loading_btn('save_good_btn', 1);
		
		save_good_btn = 0;
		
		if(data['error'])
		{
			$.each(data['error'], function(i, j){
				error_text = '';
						 
				if(i=='good_name')
				{ 
					error_text = 'Название не может быть пустым';
					light_error_input('good_name');
				}
				
				$("#"+i).parent().next().html(error_text);
			
			})
		}
		else if(data['success']==1)
		{ 
			$('#edit_good_success').html('<div class="success">Изменения успешно сохранены</div>');
			setTimeout(function(){$('#edit_good_success').html('')},2000);
			images_name_arr = {};
			images_names_replaces = {};
			deleted_good_images = ''
		}
		
	}, 'json');
}
uploaded_start = {};
images_name_arr = {};
images_names_replaces = {}
function goods_image_upload_init(num)
{
	 
	   new AjaxUpload($('#goods_images_upload_'+num), {  
		  		    action: '/ajax/ajaxUploadImage.php?mode=2',  
		  		    name: 'uploadfile',  
		  		    onSubmit: function(file, ext){
						
						if (!(ext && /^(jpg|png|jpeg|gif)$/i.test(ext))){  
							// check for valid file extension  
							alert('Ошибка. Допускаются только файлы форматов .jpg .gif и .png.')
							return false;  
						} 
						
						$('#goods_images_upload_proc_'+num).html('<img src="/img/ajax-loader.gif">');
						
						$('#goods_images_uploaded_image_'+num).html('');
						 						 
						// Блок кнопки, пока не загрузится изображение
						if(uploaded_start[num])
						{
							return false
						}
						
						uploaded_start[num] = 1;
						
						// Если форма для реактирования
						if(edit_form && num < 10)
						{
							 images_names_replaces[num] = $('#goods_images_uploaded_image_'+num).attr('image_id');
						}
						//$('#image_tb_block').hide(); 
						
		  		    },     
		  		    onComplete: function(file, response_data){  
						
						// Разбираем на массив ответ
						response_data_arr = response_data.split('|');
						
						// Стутас загрузки
						response = response_data_arr[0];
						
						// Делаем возможным снова загрузки фотографии
					    uploaded_start[num] = 0;
							
						$('#goods_images_upload_proc_'+num).html('');
						
						// очищаем контейнер загруженного изображения
						delete_good_uploaded_img(num);
						
						if(response=='ok')
						{
							images_name_arr[num] = response_data_arr[1];
							 
							//$('#image_tb_block').show(); 
							$('#goods_images_uploaded_image_'+num).html('<img src="/temp/'+response_data_arr[1]+'" />');
							
							$('#goods_images_uploaded_delete_'+num).html(' | <a href="javascript:;" class="link" onclick="delete_good_uploaded_img('+num+')">Удалить</a>')
								
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
function delete_good_uploaded_img(num)
{
	images_name_arr[num] = '';
	$('#goods_images_uploaded_image_'+num).html('');
	$('#goods_images_uploaded_delete_'+num).html('')
}

deleted_good_images = {};
// Удаляет изображения при редактировании
function delete_edit_good_images(num, image_id)
{
	$('#good_images_bl_'+image_id).remove();

	images_names_replaces[num] = image_id;
}

// Кнопка, добавляет ссылки для загрузки фотографии
function more_upload_good_images()
{
	var lenght = parseInt($('.upload_good_image_bl').length) + 1
	
	var num = Math.round(Math.random() * 10000);
	 
	
	$('#goods_images').append('<div  class="upload_good_image_bl"><a href="javascript:;" class="link" id="goods_images_upload_'+num+'">Загрузить</a> <span id="goods_images_uploaded_delete_'+num+'"></span><div id="goods_images_uploaded_image_'+num+'" style="margin-top:10px"></div><div id="goods_images_upload_proc_'+num+'"></div></div>');
	
	goods_image_upload_init(num);
	
	 
}

// Показать блок настройки доступа к файлам и папкам
function show_good_owner_block(good_id)
{
	$('.file_users_access_block_list').hide();
 
	$('#owner_block_'+good_id).show();
		 
}

// Передать имущество
function give_good_to_user(good_id, user_id)
{
	$('#access_proc_'+good_id).html('<img src="/img/ajax-loader.gif">');
	
	$.post('/ajax/ajaxGoods.php', 
	{   
		mode : 'give_good_to_user',
		good_id : good_id,
		user_id : user_id
	},
	function(data){ 
		
		$('.access_block_item_'+good_id).children().removeClass('access_active')
		
		$('#access_proc_'+good_id).html('');
		
		if(data==0)
		{
			
		}
		
		if(data==1)
		{
			$('#user_'+good_id+'_'+user_id).removeClass('access_active')
		}
		if(data==2)
		{
			$('#user_'+good_id+'_'+user_id).addClass('access_active')
		}
		
	});
}

// Принять имущество
function good_owner_confirm(good_id)
{
	loading_btn('confirm_good_btn_'+good_id)
	
	$.post('/ajax/ajaxGoods.php', 
	{   
		mode : 'good_owner_confirm',
		good_id : good_id
	},
	function(data){ 
		
		loading_btn('confirm_good_btn_'+good_id, 1)
		
		if(data['success']==1)
		{
			$('#good_confirm_btn_'+good_id).remove();
			$('#good_owner_'+good_id).removeClass('display_none');
			$('#good_'+good_id).removeClass('not_confirm');
			$('#add_report_form_'+good_id).show();
			if(data['new_goods_count']>=1)
			{
				$('#new_goods_count').html('(+ '+data['new_goods_count']+')');
			}
			else
			{
				$('#new_goods_count').html('');
			}
		}
		
	}, 'json');
}

// Отклонить предложение имущества
function good_owner_cancel(good_id)
{
	loading_btn('cancel_confirm_good_btn_'+good_id)
	
	$.post('/ajax/ajaxGoods.php', 
	{   
		mode : 'good_owner_cancel',
		good_id : good_id
	},
	function(data){ 
		
		loading_btn('cancel_confirm_good_btn_'+good_id, 1)
		
		if(data['success']==1)
		{
			
			$('#good_'+good_id).remove();
			 
			if(data['new_goods_count']>=1)
			{
				$('#new_goods_count').html('(+ '+data['new_goods_count']+')');
			}
			else
			{
				$('#new_goods_count').html('');
			}
		}
		
	}, 'json');
}

function good_take_away(good_id)
{
	loading_btn('take_away_good_'+good_id)
	
	$.post('/ajax/ajaxGoods.php', 
	{   
		mode : 'good_take_away',
		good_id : good_id
	},
	function(data){ 
		
		loading_btn('take_away_good_'+good_id, 1)
		
		if(data==1)
		{
			
			$('#good_'+good_id).remove();
			draw_background_list_item("goods_item");
			
		}
		
	}, 'json');	
}

good_report_btn = 0;
function add_good_report(good_id)
{
	var report_text;
	
	if(good_report_btn==1)
	{
		return;
	}
	report_text = $('#good_report_'+good_id).val();
	
	good_report_btn = 1;
	
	loading_btn('add_report_btn_'+good_id);
	
	$.post('/ajax/ajaxGoods.php', 
	{   
		mode : 'add_good_report',
		good_id : good_id,
		report_text : report_text
	},
	function(data){ 
		
		loading_btn('add_report_btn_'+good_id, 1);
		
		good_report_btn = 0;
		 
		if(data['error'])
		{  
			if(data['error']['report_text']=='1')
			{   
				$('#good_report_'+good_id).focus();
			 
			}
		}
		if(data['success']==1)
		{ 
			$('#good_report_'+good_id).val('');
			
			get_good_report_item(good_id, data['report_id']);
			
			//get_task_report_list(task_id);
				//$('#task_report_success_'+task_id).html('<div class="success">Отчет</div>');
		}
		
	}, 'json');
	
}

function get_good_report_item(good_id, report_id)
{
	$.post('/ajax/ajaxGoods.php', 
	{   
		mode : 'get_good_report_item',
		report_id : report_id,
		good_id : good_id
	},
	function(data){ 
		
		$('#reports_list_'+good_id).append(data)
		$('#reports_list_'+good_id+' .no_contents').remove();		
	});
}