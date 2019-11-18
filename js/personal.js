// Инициализация кнопки загрузки файла
uploaded_image = '';
uploaded_start = 0;
image_name = '';
image_is_change = 0;
function personal_image_upload_init()
{
	 
	   new AjaxUpload($('#upload_image_btn'), {  
		  		    action: '/ajax/ajaxUploadImage.php?mode=1',  
		  		    name: 'uploadfile',  
		  		    onSubmit: function(file, ext){ 
						if (!(ext && /^(jpg|png|jpeg|gif)$/i.test(ext))){  
							// check for valid file extension  
							alert('Ошибка. Допускаются только файлы форматов .jpg .gif и .png.')
							return false;  
						}  
						$('#upload_proc').html('<img src="/img/loading_line.gif">');
						$('#uploaded_image').html('');
						$('#user_image_preview_avatar_block').html('');
						$('#upload_error').html('');
						 
						// Блок кнопки, пока не загрузится изображение
						if(uploaded_start)
						{
							return false
						}
						uploaded_start = 1;
						
						$('#image_tb_block').hide(); 
						
		  		    },     
		  		    onComplete: function(file, response_data){  
						
						// Разбираем на массив ответ
						response_data_arr = response_data.split('|');
						
						// Стутас загрузки
						response = response_data_arr[0];
						
						// Делаем возможным снова загрузки фотографии
					    uploaded_start = 0;
							
						$('#upload_proc').html('');
						
						if(response=='ok')
						{
								 
							uploaded_image = file;
							
							image_name = response_data_arr[1];
							 
							$('#image_tb_block').show(); 
							$('#uploaded_image').html('<img id="user_image" src="/temp/'+image_name+'" />');
							$('#user_image_preview_avatar_block').html('<img src="/temp/'+image_name+'" id="user_image_preview_avatar" />');
								
							// После загрузки изображения инициализируем crop 
							$('#user_image').ready(function()
							{
								image_crop_init();
							})
								 
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
							$('#upload_error').html('')
							alert('Произошла ошибка при загрузке файла');
						}
							 
							
						}
			}); 
	 
	
}

image_preview_coordinats = '';
 
// Инициализация crop
function image_crop_init()
{
	// Create variables (in this scope) to hold the API and image size
	var jcrop_api, boundx, boundy;
		  
	$('#user_image').Jcrop({
		//setSelect: [ init_crop_coord_x, init_crop_coord_y, init_crop_coord_x2, init_crop_coord_y2],
		onChange: updatePreview ,
		 
		aspectRatio: 1,
		minSize : Array(100,100)
	},function(){ 
	 
	 	 
		// Use the API to get the real image size
		var bounds = this.getBounds();
		 
		boundx = bounds[0];
		boundy = bounds[1];
		// Store the API in the jcrop_api variable
		jcrop_api = this;
		jcrop_api.animateTo([ init_crop_coord_x, init_crop_coord_y, init_crop_coord_x2, init_crop_coord_y2]);
	});
			
	function updatePreview(c)
	{  
		res = ''
		$.each(c, function(i, j) {
			res += i+'-'+j+'   ; ';
		})
		 
	 
		image_preview_coordinats = c;
		if (parseInt(c.w) > 0)
		{  
			var rx = 100 / c.w;
			var ry = 100 / c.h;
		 
			$('#user_image_preview_avatar').css({
				width: Math.round(rx * boundx) + 'px',
				height: Math.round(ry * boundy) + 'px',
				marginLeft: '-' + Math.round(rx * c.x) + 'px',
				marginTop: '-' + Math.round(ry * c.y) + 'px'
			});
		}
	
	};
   
}

// Сохранение изображения
function save_user_image()
{
	loading_btn('save_user_image_btn');
	
	$.post('/ajax/ajaxPersonal.php', 
	{   
		mode : 'save_user_image',
		image_name : image_name,
		image_preview_coordinats : image_preview_coordinats
	},
	function(data){ 
		
		if(data==1)
		{
			document.location.reload();
		}
		else
		{
			alert('При сохранении изображения произошла ошибка');
			$('#save_proc').html('')
		}
		 
		
	});
}

// Удалить изображение
function delete_user_image(user_id)
{
	if(!confirm('Удалить изображение?'))
	{
		return false;
	}
	$.post('/ajax/ajaxPersonal.php', 
	{   
		mode : 'delete_user_image',
		user_id : user_id
	},
	function(data){ 
		
		if(data==1)
		{
			document.location.reload();
		}
		
	});
}

function show_completed_task_list()
{
	$('#show_task_completed_list_a').hide();
	$('#hide_task_completed_list_a').show();
	$('#task_completed_list').slideDown(200);
}

function hide_completed_task_list()
{
	$('#show_task_completed_list_a').show();
	$('#hide_task_completed_list_a').hide();
	$('#task_completed_list').slideUp(200);
}

// Убрать сотрудника из подченения
function remove_user_from_worker($user_id)
{
	$.post('/ajax/ajaxWorkers.php', 
	{   
		mode : 'remove_user_from_worker',
		user_id : user_id
	},
	function(data){ 
		
		if(data==1)
		{
			document.location.reload();
		}
		
	});
}

function show_users_list_in_personal_block(elem)
{
	$(elem).parent().hide();
	$(elem).parent().prev().show();
}

function close_users_list_in_personal_block(elem)
{
	$(elem).parent().parent().hide();
	 
	$(elem).parent().parent().next().show();
}



add_comment_btn = 0;
// Добавить отзыв
function add_comment(user_id)
{
	var comment_text;
	
	 
	comment_text = $('#comment_text').val();
	
	if(comment_text=='')
	{
		$('#comment_text').focus();
		return
	}
	
	if(add_comment_btn==1)
	{
		return;	
	}
	
	add_comment_btn = 1;
	
	loading_btn('add_comment_btn');
	 
	$.post('/ajax/ajaxComment.php', 
	{   
		mode : 'add_comment',
		to_user_id : user_id,
		comment_text : comment_text
	},
	function(data){ 
 
		add_comment_btn = 0;
		
		//$('#add_comment_error').html('');
		
		 
		loading_btn('add_comment_btn', 1);
		 
		if(data['error'])
		{
			if(data['error']['comment_text']==1) 
			{  
				$('#comment_text').focus();
			}
		}
		if(data['success']==1)
		{ 
			$('#no_comemnts').remove();
			
			// Выводим добавленный отзыва
			get_comment_item(data['comment_id'], 1);
			
			$('#comment_add_form').html('');
			
			$('#comment_text').val('');
			
			$('#comment_add_form').remove();
		}
		
	}, 'json');
 
}
// Возвращает блок отзыва
function get_comment_item(comment_id, mode)
{
	$.post('/ajax/ajaxComment.php', 
	{   
		mode : 'get_comment_item',
		comment_id : comment_id
	},
	function(data){ 
		
		if(data)
		{
			if(mode=='replace')
			{
				$('#comment_'+comment_id).replaceWith(data);
			}
			else
			{
				$('#no_comments').remove();
				$('#comments_list').prepend(data);
				
			}
			$('#comemnt_list_proc').html('');
			 
		}
		
	});
}

// Удаление отзыва
function delete_comment(comment_id)
{
	$.post('/ajax/ajaxComment.php', 
	{   
		mode : 'delete_comment',
		comment_id : comment_id
	},
	function(data){ 
		
		if(data)
		{
			$('.cont_hide_after_act_'+comment_id).hide();
			$('#comment_content_'+comment_id).hide();
			$('#comment_notice_'+comment_id).html('Отзыв успешно удален | <a class="link" href="javascript:;" onclick="restore_comment('+comment_id+')">Восстановить</a>');
		}
		
	});
}

// Восстановление отзыва
function restore_comment(comment_id)
{
	$.post('/ajax/ajaxComment.php', 
	{   
		mode : 'restore_comment',
		comment_id : comment_id
	},
	function(data){ 
		
		if(data)
		{
			$('.cont_hide_after_act_'+comment_id).show();
			$('#comment_content_'+comment_id).show();
			$('#comment_notice_'+comment_id).html('');
		}
		
	});
}

// Форма для редактирования отзыва
function get_comment_edit_form(comment_id)
{
	$.post('/ajax/ajaxComment.php', 
	{   
		mode : 'get_comment_edit_form',
		comment_id : comment_id
	},
	function(data){ 
		
		if(data)
		{	
			$('.cont_hide_after_act_'+comment_id).hide();
			$('#comment_edit_'+comment_id).html(data)
			//$('#comment_notice_'+comment_id).html('');
			//$('#comment_'+comment_id).hide();
			//$('#comment_'+comment_id).after(data);
		}
		
	});
}

// Отменить редактирование отзыва
function cancel_edit_comment(comment_id)
{
	$('.cont_hide_after_act_'+comment_id).show();
	$('#comment_edit_'+comment_id).html('')
}

// Сохранить изменения отзыва
function save_edit_comment(comment_id)
{
	var comment_text;
	
	comment_text = $('#comment_text_'+comment_id).val();
	
	$('#comment_edit_error_'+comment_id).html('');
	
	loading_btn('save_comment_btn_'+comment_id);
	
	$.post('/ajax/ajaxComment.php', 
	{   
		mode : 'save_edit_comment',
		comment_id : comment_id,
		comment_text : comment_text
	},
	function(data){ 
		
		loading_btn('save_comment_btn_'+comment_id, 1);
		
		if(data['error'])
		{
			 if(data['error']['comment_text']==1)
			 {
			 	$('#comment_text_'+comment_id).focus();
			 }
		}
		if(data['success']==1)
		{
			// Подгружаем отредактированный блок
			get_comment_item(comment_id, 'replace');
			
			$('#comment_edit_'+comment_id).remove();
			
		}
		
	}, 'json');
}