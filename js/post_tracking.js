var PostTracking= {
	
	init_add_easycomplete : function()
	{
		$('#tracking_link_deal').easycomplete(
		{
			str_word_select : 'Поиск сделки',
			width:520,
			url:'/ajax/ajaxDeals.php?mode=get_deals'
		});
		$('#tracking_link_client').easycomplete(
		{
			str_word_select : 'Поиск контрагента',
			width:520,
			url:'/ajax/ajaxGetClients.php'
		});
		
	},
	
	check_for_all_checked_list : function()
	{
		var chbx_length = $('.posttr_tb .track_checked_chbx').length;
		var chbx_checked_length = $('.posttr_tb .track_checked_chbx:checked').length;
		
		if(chbx_checked_length >= chbx_length)
		{ 
			$('#track_checked_all').attr('checked', 'checked');
		}
		else
		{
			$('#track_checked_all').removeAttr('checked');
		}
		 
		if(chbx_checked_length>0)
		{ 
			$('#posttr_update_all_btn_bl').show();
		}
		else
		{ 
			$('#posttr_update_all_btn_bl').hide();
		}
	},
	checked_all_list : function(elem)
	{
		var checked_all = $(elem).attr('checked')=='checked' ? 1: 0;
		
		if(checked_all==1)
		{
			$('.posttr_tb .track_checked_chbx').attr('checked', 'checked');
		}
		else
		{
			$('.posttr_tb .track_checked_chbx').removeAttr('checked');
		}
		
		PostTracking.check_for_all_checked_list();
	},
	update_tarcking_status : function(tracking_id, update_all)
	{
		var tracking_data = {};
		
		if(tracking_id)
		{
			tracking_data[tracking_id] = tracking_id;
		}
		else if(update_all==1)
		{
			$('.posttr_tb .track_checked_chbx:checked').each(function(){
				var id = $(this).attr('tracking_id');
				tracking_data[id] = id;
			})
		}
		
		$.each(tracking_data, function(i,j) {
			$('#posttr_update_status_btn_'+i).replaceWith('<img src="/img/ajax-loader.gif" />');
		})
		
		 
		$.post('/ajax/ajaxPostTracking.php', 
		{   
			mode : 'update_tarcking_status',
			tracking_data : $.toJSON(tracking_data)
		},
		function(data){ 
			
			$.each(data['status_block'], function(i, j){
				$('#posttr_status_'+i).html(j);
			})
			 
			
		}, 'json');	
	},
	archive_tracking : function(tracking_id)
	{
		$.post('/ajax/ajaxPostTracking.php', 
		{   
			mode : 'archive_tracking',
			tracking_id : tracking_id
		},
		function(data){ 
		
			if(data==1)
			{
				$('#tracking_'+tracking_id).html('<td colspan="7"><div class="success">Трекинг успешно добавлен в архив | <a href="javascript:;" class="link" onclick="PostTracking.restore_tracking_from_archive(\''+tracking_id+'\')">Восстановить</a> | <a href="javascript:;" class="link" onclick="$(\'#tracking_'+tracking_id+'\').remove()">Скрыть</a></div></td>');
			}
			
		});	
	},
	show_list : function()
	{
		var archive = $('#show_acrhive').attr('checked')=='checked' ? 1 : 0;
		var list_type = $('#list_type').val();
		var key_words = $('#key_words').val();
		var status = $('#status').val();
		
		
		document.location = '/posttr?archive='+archive+'&list_type='+list_type+'&key_words='+key_words+'&status='+status;
		
	},
	restore_tracking_from_archive : function(tracking_id, from_archive)
	{
		$.post('/ajax/ajaxPostTracking.php', 
		{   
			mode : 'restore_tracking_from_archive',
			tracking_id : tracking_id
		},
		function(data){ 
		
			if(data==1)
			{
				if(from_archive==1)
				{
					$('#tracking_'+tracking_id).remove();
				}
				else
				{
					PostTracking.get_posttr_item(tracking_id, 'replace');
				}
			}
			
		});	
	},
	delete_tracking : function(tracking_id)
	{
		$.post('/ajax/ajaxPostTracking.php', 
		{   
			mode : 'delete_tracking',
			tracking_id : tracking_id
		},
		function(data){ 
		
			if(data==1)
			{
				$('#tracking_'+tracking_id).html('<td colspan="7"><div class="success">Трекинг успешно удален | <a href="javascript:;" class="link" onclick="PostTracking.restore_tracking(\''+tracking_id+'\')">Восстановить</a> | <a href="javascript:;" class="link" onclick="$(\'#tracking_'+tracking_id+'\').remove()">Скрыть</a></div></td>');
			}
			
		});	
	},
	restore_tracking : function(tracking_id)
	{
		$.post('/ajax/ajaxPostTracking.php', 
		{   
			mode : 'restore_tracking',
			tracking_id : tracking_id
		},
		function(data){ 
		
			if(data==1)
			{
				PostTracking.get_posttr_item(tracking_id, 'replace');
			}
			
		});	
	},
	add_tracking : function()
	{
		var tracking_barcode, tracking_desc, tracking_client_id, tracking_deal_id;
		
		if(PostTracking.add_tracking_btn==1)
		{
			return '';
		}
		
		$('#error_box').hide();
		$('.posttr_add_tb .input_text').removeClass('light_error_input');
		 
		tracking_barcode = $('#tracking_barcode').val();
		tracking_desc = $('#tracking_desc').val();
		
		var checked_link = $('.posttr_add_tb input[name="tracking_link"]:checked').val();
		
		if(checked_link==1)
		{
			tracking_deal_id = $('#tracking_link_deal').val();
		}
		else if(checked_link==2)
		{
			tracking_client_id = $('#tracking_link_client').val();
		}
	
		 
		loading_btn('add_tracking_btn');
		
		PostTracking.add_tracking_btn = 1;
		
		$.post('/ajax/ajaxPostTracking.php', 
		{   
			mode : 'add_tracking',
			tracking_barcode : tracking_barcode,
			tracking_desc : tracking_desc,
			tracking_client_id : tracking_client_id,
			tracking_deal_id : tracking_deal_id,
			checked_link : checked_link
		},
		function(data){ 
			
			loading_btn('add_tracking_btn', 1);
			
			PostTracking.add_tracking_btn = 0;
			
			if(data['error'])
			{
				var error_text = '';
				
				$.each(data['error'], function(i,j){
					  
					if(i=='tracking_barcode' && j == 1)
					{
						$('#tracking_barcode').addClass('light_error_input');
						error_text += '<div>Введите номер трекинга.</div>';
					}
					if(i=='tracking_barcode' && j == 2)
					{
						$('#tracking_barcode').addClass('light_error_input');
						error_text += '<div>Неверный формат трекинга.</div>';
					}
					if(i=='tracking_link' && j == 1)
					{
						$('#tracking_barcode').addClass('light_error_input');
						error_text += '<div>Выберите сделку.</div>';
					}
					if(i=='tracking_link' && j == 2)
					{
						$('#tracking_barcode').addClass('light_error_input');
						error_text += '<div>Выберите контрагента.</div>';
					}
					 
				})
				
				if(error_text)
				{ 
					$('#error_box').html(error_text);
					$('#error_box').show();
				}
			}
			else if(data['success']==1)
			{
				
				$('#tracking_barcode').val('');
				$('#tracking_desc').val('');
				$('#trr_to_1').trigger('click');
				
				PostTracking.get_posttr_item(data['tracking_id'], 'prepend_list');
				$('.no_contents').remove();
				
				$('#tracking_link_deal').easycomplete('clear');
				$('#tracking_link_client').easycomplete('clear');
			}
			
		}, 'json');	
	},
	
	get_posttr_item : function(tracking_id, method)
	{
		 
		$.post('/ajax/ajaxPostTracking.php', 
		{   
			mode : 'get_posttr_item',
			tracking_id : tracking_id
		},
		function(data){ 
			
			if(data)
			{
				if(method=='prepend_list')
				{
					$('#posttr_list').prepend(data);
				}
				else if(method=='replace')
				{ 
					$('#tracking_'+tracking_id).replaceWith(data);
				}
			}
		});
	},
	
	param : {
		 posttr_actual_page : 1,
		 add_tracking_btn : 0
	},
	
	// Выводит больше контактов
	get_more_posttr : function()
	{
		var page, search_word;
		
		page = PostTracking.param.posttr_actual_page + 1;
	
		$.post('/ajax/ajaxPostTracking.php', 
		{   
			mode : 'get_more_posttr',
			page : page,
			is_archive : is_archive,
			list_type : list_type,
			key_words : key_words,
			status : status
			
		},
		function(data){ 
			
			$('#posttr_list').append(data);
			
			PostTracking.check_for_all_checked_list();
			
			// Актаульная страница
			PostTracking.param.posttr_actual_page++;
			
			if(PostTracking.param.posttr_actual_page>=pages_count)
			{
				$('#more_posttr_btn').hide();
			}
		});
	}
}