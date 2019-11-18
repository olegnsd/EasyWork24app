function add_cnews()
{
	var text, cnews_theme;
	
	cnews_text = $('#cnews_text').val();
	cnews_theme = $('#cnews_theme').val();
	
	$('.td_error').html('');
	
	$.post('/ajax/ajaxCnews.php', 
	{   
		mode : 'add_cnews',
		cnews_text : cnews_text,
		cnews_theme : cnews_theme
	},
	function(data){ 
		
		if(data['error'])
		{
			if(data['error']['cnews_text'])
			{
				$('#cnews_text').next().html('Введите текст новости');
			}
			if(data['error']['cnews_theme'])
			{
				$('#cnews_theme').next().html('Введите тему новости');
			}
		}
		if(data['success']==1)
		{
			$('#cnews_text').val('');
			$('#cnews_theme').val('');
			get_cnews_item(data['cnews_id'], 1);
		}
		
	}, 'json');
}

function save_cnews(cnews_id)
{
	var text, cnews_theme;
	
	cnews_text = $('#cnews_text_'+cnews_id).val();
	cnews_theme = $('#cnews_theme_'+cnews_id).val();
	
	$('#cnews_'+cnews_id+' .td_error').html('');
	
	loading_btn('save_cnews_btn_'+cnews_id);
	
	$.post('/ajax/ajaxCnews.php', 
	{   
		mode : 'save_cnews',
		cnews_id : cnews_id,
		cnews_text : cnews_text,
		cnews_theme : cnews_theme
	},
	function(data){ 
		
		if(data['error'])
		{
			loading_btn('save_cnews_btn_'+cnews_id, 1);
			
			if(data['error']['cnews_text'])
			{
				$('#cnews_text_'+cnews_id).next().html('Введите текст новости');
			}
			if(data['error']['cnews_theme'])
			{
				$('#cnews_theme_'+cnews_id).next().html('Введите тему новости');
			}
		}
		if(data['success']==1)
		{
			get_cnews_item(cnews_id, 0);
		}
		
	}, 'json');
}


function get_cnews_item(cnews_id, prepend, form)
{
	$.post('/ajax/ajaxCnews.php', 
	{   
		mode : 'get_cnews_item',
		cnews_id : cnews_id,
		form : form
	},
	function(data){ 
		
		if(prepend)
		{
			$('#cnews_list').prepend(data);
		}
		else
		{  
			$('#cnews_'+cnews_id).replaceWith(data);
		}
		
		$('.no_contents').remove(); 
		 
	});
}

actual_page = 1;

// Выводит больше выговоров
function get_more_cnews()
{
	var page;
	
	page = actual_page + 1;

	$.post('/ajax/ajaxCnews.php', 
	{   
		mode : 'get_more_cnews',
		page : page
	},
	function(data){ 
		
		$('#cnews_list').append(data);
		
		// Актаульная страница
		actual_page++;
		
		if(actual_page>=pages_count)
		{
			$('#more_cnews_btn').hide();
		}
	});
}

function delete_cnews(cnews_id)
{
	var page;
	
	page = actual_page + 1;

	$.post('/ajax/ajaxCnews.php', 
	{   
		mode : 'delete_cnews',
		cnews_id : cnews_id
	},
	function(data){ 
		
		if(data==1)
		{
			$('#cnews_'+cnews_id).replaceWith('<div class="success" id="cnews_'+cnews_id+'">Новость успешно удалена | <a href="javascript:;" onclick="restore_cnews('+cnews_id+')">Восстановить</a> | <a href="javascript:;" onclick="$(this).parent().remove();">Скрыть</a></div>');
		}
	});
}

function restore_cnews(cnews_id)
{
	var page;
	
	page = actual_page + 1;

	$.post('/ajax/ajaxCnews.php', 
	{   
		mode : 'restore_cnews',
		cnews_id : cnews_id
	},
	function(data){ 
		
		if(data==1)
		{
			get_cnews_item(cnews_id);
		}
	});
}