Load = {
	
	more_proc : 0,
	current_next_msg_page : 1,
	empty_list : 0,
	
	list_scroll : function(elem, fun)
	{  
		if(Load.empty_list)
		{
			return false;
		}
		
		var last_item_offset = $('#'+elem).offset();
		
		var offset_pop = $('#pop').offset();
		
		// След страница
		if(Number(last_item_offset.top - offset_pop.top) < 300 && !Load.more_proc)
		{  
			fun('Load.more_proc=0;', 'Load.empty_list=1;');
			Load.more_proc = 1;
		}
		
		  
	}

		
}
