// selected result - <div class="selected" key="key">value</div>
(function($){
    
	$.fn.easycompleteVal = function()
	{
		var elem_id = $(this).attr('id');
		
		var key = $('#ea_selected__'+elem_id+' .selected').attr('key');
		
		key = key == undefined ? null : key;
		
		return key;
	 
	}
	
    $.fn.easycomplete = function(options) {
       
	   
	   
		var settings = $.extend({
			url : '',
			width : 500,
			str_word_select : 'Выбрать',
			show_tag : 0,
			trigger : 0
		}, options);
		
		var input_search = '<input type="text" class="s_inp" />';
		var s_width = settings.width+'px';
	 	var _this_element = this;
		var _this_element_id = $(this).attr('id');
		 
		if(options=='clear')
		{
			ea_clear_selected_by_id(_this_element_id);
			return;
		}
	   
		_init();
		
		$(_this_element).hide();
		 
		var next_select_block_elem =  $('#'+_this_element_id).next().next().next('.s_inp_bl');
		
		$(next_select_block_elem).css('width', s_width);
	 	
		$(next_select_block_elem).children('.s_inp_wrap').children('.s_inp').bind('keyup',{element_id : _this_element_id}, get_easycomplete_data);
		$(next_select_block_elem).children('.s_inp_wrap').children('.s_inp').bind('focus',{element_id : _this_element_id}, ea_show_s_result);
		$(next_select_block_elem).children('.s_inp_wrap').children('.s_inp').bind('blur',{element_id : _this_element_id}, ea_hide_s_result);
		
		
		
		function _init()
		{
			var tmp_selected = $('#'+_this_element_id+' .selected');
			var key = $(tmp_selected).val();
			var value = $(tmp_selected).text();
			
		//	$(_this_element).replaceWith('<input type="hidden" id="'+_this_element_id+'">');
			 
			$(_this_element).after('<div class="ea_main_bl"><div class="ea_main_result" id="ea_selected__'+_this_element_id+'"></div> <a href="javascript:;" class="ea_show_link link" id="ea_show_select_link__'+_this_element_id+'">'+settings.str_word_select+'</a></div><div class="clear"></div><div class="s_inp_bl" id="ea_search_block__'+_this_element_id+'"><div class="s_inp_wrap">'+input_search+'<div class="ea_selected"></div></div><div class="sip_rl" style="width:'+s_width+'"></div></div>');
			
			if($.trim(key)!='')
			{ 
				 ea_set_selected_item(_this_element_id, key, value);
				 $('#ea_show_select_link__'+_this_element_id).html('Изменить');
			}
			
			$('#ea_show_select_link__'+_this_element_id).bind('click', {element_id : _this_element_id}, ea_show_select) ;
			
		}
		
		function ea_show_select(e)
		{
			var tmp_result ;
			 
			if(!$('#ea_search_block__'+e.data.element_id).is(':visible'))
			{
				$('#ea_search_block__'+e.data.element_id).show(100);
				$('#ea_show_select_link__'+e.data.element_id).html('Отменить');
				$('#ea_search_block__'+e.data.element_id+' .s_inp_wrap .s_inp').focus();
			}
			else
			{  
				if($.trim($('#ea_selected__'+e.data.element_id+' .selected').attr('key'))!='')
				{ 
					$('#ea_show_select_link__'+e.data.element_id).html('Изменить');
					
				}
				else
				{ 
					$('#ea_show_select_link__'+e.data.element_id).html(settings.str_word_select);
				}
				$('#ea_search_block__'+e.data.element_id).hide(100);
				 
			}
		}
		
		function ea_clear_selected(e)
		{  
			$('#ea_selected__'+e.data.element_id).html('');
			$('#ea_show_select_link__'+e.data.element_id).html(settings.str_word_select);
			$('#'+e.data.element_id).html('')
		}
		
		function ea_clear_selected_by_id(element_id)
		{  
			$('#ea_selected__'+element_id).html('');
			$('#ea_show_select_link__'+element_id).html(settings.str_word_select);
			$('#'+element_id).html('')
		}
		
		function ea_show_s_result(e)
		{ 
			var em_results = $(this).parent().parent().parent('.s_inp_bl').children('.sip_rl');
			$(em_results).show(100);
			if(settings.trigger==1)
			{ 
				get_easycomplete_data(e)
				 //$('#ea_search_block__'+_this_element_id+' .s_inp').trigger(get_easycomplete_data)
			}
		}
		function ea_hide_s_result(e)
		{ 
			setTimeout(function() {  
			//alert(e.data.element_id);
				ea_show_select(e);
			}, 350)
		}
		
		function get_easycomplete_data(e)
		{
			var searched_items = '';
			 
			var es_result_container = $('#ea_search_block__'+_this_element_id+' .sip_rl');
			 
			var tag = $('#ea_search_block__'+_this_element_id+' .s_inp').val();
			  
			$.get(settings.url, 
			{   
				tag : tag
			},
			function(data){ 
				
				$(es_result_container).html('');
				
				if(settings.show_tag && $.trim(tag)!='')
				{
					searched_items += '<div  class="sip_rl_item" key="'+tag+'"><em>'+tag+'</em></div>';
				}
				//data[]
				if(data != null)
				{
					
					data = new Object(data)
					
					$.each(data,function(i, j) {
						 
						searched_items += '<div  class="sip_rl_item" key="'+j['key']+'">'+j['value']+'</div>';
					}) 
					
					 
				}
				
				if(searched_items)
				{
					$(es_result_container).html(searched_items);
					$('.sip_rl_item').bind('click', {element_id : e.data.element_id} ,easycomplete_item_proc);
				}
				
			}, 'json');
			
		}
		
		function easycomplete_item_proc(e)
		{  
			var value, item_text;
			var key = $(this).attr('key');
			var value = $(this).html();
			
			ea_set_selected_item(e.data.element_id, key, value);
			
			var wrap_elem = $(this).parent().parent('.s_inp_bl').children('.s_inp_wrap')

		 	$('#ea_search_block__'+e.data.element_id+' .s_inp_wrap .s_inp').val('');
			 
			$(this).parent().html('');
		}
		
		function ea_set_selected_item(element_id, key, value)
		{
			$('#ea_selected__'+element_id).html('<div key="'+key+'" class="selected">'+value+'</div><div class="ea_close_result"></div>');
			$('#ea_selected__'+element_id).bind('click', {element_id : element_id}, ea_clear_selected);
			 
			$('#'+element_id).html('<option value="'+key+'">'+value+'</option>');
			 
		}
		
		function clear_sip_selected_item()
		{  
			var wrap_elem = $(this).parent().parent('.s_inp_wrap');
			$(wrap_elem).children('.search_input_wrap').show();
			$(wrap_elem).children('.search_input_wrap').children('input').focus()
			$(wrap_elem).children('.ea_selected').hide();
			 
		}
		
		function clear()
		{
			alert(2)
		}
		 
	}

})(jQuery);
