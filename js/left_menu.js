var Left_menu = {

	get_data : function()
	{
	 	$.post('/ajax/ajaxLeftmenu.php', 
		{   
			mode : 'get_data'
		},
		function(data){ 
			
			$.each(data, function(i,j){
				
				if(j>0)
				{
					$('#'+i).html('(+ '+j+')')
				}
			})
			
		},'json');
	},
	
	count_obj : {},
	
	check_new_count : function(what)
	{ 
		$.post('/ajax/ajaxLeftmenu.php', 
		{   
			mode : 'check_new_count',
			what : what
		},
		function(data){ 
			
			$.each(data, function(i,j){
				
				if(j['count']>0 && Left_menu.count_obj[i])
				{ 
					if(Left_menu.count_obj[i] && Left_menu.count_obj[i]['lid']<j['lid'] && j['count']>0)
					{
						nmsg_sound();
					}
					
					 
				}
				
				var tmp = {};
				tmp['count'] = j['count'];
				tmp['lid'] = j['lid'];
				Left_menu.count_obj[i] = tmp;
				
				if(j['count']>0)
					$('#'+i).html('(+ '+j['count']+')');
				else $('#'+i).html('');
			})
			
			setTimeout(function(){Left_menu.check_new_count(what)},5000)
			
		}, 'json');
	}
}