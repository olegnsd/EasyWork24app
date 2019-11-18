Org = {
	
	save_dept : function(dept_id)
	{
		var dept_name = $('#dept_name_'+dept_id).val();
		var parent_dept = $('#parent_dept_'+dept_id).val();
		var dept_head = $('#dept_head_'+dept_id).val();
		
		$.post('/ajax/ajaxOrg.php', 
		{   
			mode : 'save_dept',
			dept_id : dept_id,
			dept_name : dept_name,
			parent_dept : parent_dept,
			dept_head : dept_head
		},
		function(data){ 
			
			if(data['error']==1)
			{
				$('#dept_name').focus();
			}
			else if(data['error']==2)
			{
				alert('Нельзя выбрать в качестве вышестоящего подразделения');
			}
			else if(data['error']==3)
			{
				alert('Нельзя перенести в нижестоящее подразделение');
			}
			else if(data['success']==1)
			{
				document.location.reload();
			}
			
		}, 'json');
	},
	edit_dept : function(dept_id)
	{
		 
		$.post('/ajax/ajaxOrg.php', 
		{   
			mode : 'edit_dept',
			dept_id : dept_id
		},
		function(data){ 
			
			if(data)
			{
				create_popup_block('edit_form_wrap', 500, data, '', 1);
			}
			
		});
	},
	
	delete_dept : function(dept_id, dept_name)
	{
		if(!confirm('Действитель удалить подразделение '+dept_name+'?'))
		{
			return false;
		}
		
		$.post('/ajax/ajaxOrg.php', 
		{   
			mode : 'delete_dept',
			dept_id : dept_id
		},
		function(data){ 
			
			if(data==1)
			{
				document.location.reload();
			}
			
		});
	},
	show_workers_list_in_dept : function(dept_id)
	{
		$.post('/ajax/ajaxOrg.php', 
		{   
			mode : 'show_workers_list_in_dept',
			dept_id : dept_id
		},
		function(data){ 
			
			$('#workers_block_'+dept_id).html(data);
			$('#workers_block_'+dept_id).show();
			
			$('.workers_block_list').bind('click', function(event){
				event.stopPropagation();
			})
			
			Org.hide_workers_list_in_dept();
			
		});
	},
	
	hide_workers_list_in_dept : function()
	{
		$('body').unbind('click');
			
			setTimeout(function(){
			 
			$('body').bind('click', function(e,elem) {
				
				e.stopPropagation()
				
				var target = e.target;
				 
				// alert($(target).class)
				if(!$(target).hasClass('workers_block_list'))
				{
					$('.workers_block_list').hide();
					$('body').unbind('click');
					//$('.file_open_popup').unbind('click');
				}
				 
			
			})
			}, 300);
	},
	add_dept : function()
	{
		var dept_name = $('#dept_name').val();
		var parent_dept = $('#parent_dept').val();
		var dept_head = $('#dept_head').val();
		
		$.post('/ajax/ajaxOrg.php', 
		{   
			mode : 'add_dept',
			parent_dept : parent_dept,
			dept_name : dept_name,
			dept_head : dept_head
		},
		function(data){ 
			
			if(data['error']==1)
			{
				$('#dept_name').focus();
			}
			else if(data['success']==1)
			{
				document.location.reload();
			}
			
		}, 'json');
	},
	search_users : function(change_org)
	{
		 
		var search_word = $('#search_word').val();
		
		var dept_id = $('#depts_list').val();
		
		var user_is_fired = $('#user_is_fired').attr('checked')=='checked' ? 1 : 0;
		
		/*if(change_org)
		{
			document.location.href='/org?dip='+dept_id;
			return false;
		}*/
		
		$.post('/ajax/ajaxOrg.php', 
		{   
			mode : 'search_users',
			search_word : search_word,
			dept_id : dept_id,
			user_is_fired : user_is_fired
		},
		function(data){ 
			
			$('#users_list_cont').html(data);
			Org.actual_page = 1;
			Load.empty_list=0;
			
		});
	},
	
	actual_page : 1, 
	get_more : function(complete_load_eval_str, empty_load_eval_str)
	{
		var page, search_word;
	
		page = Org.actual_page + 1;
		
		var search_word = $('#search_word').val();
		
		var dept_id = $('#depts_list').val();
		
		var user_is_fired = $('#user_is_fired').attr('checked')=='checked' ? 1 : 0;
		 
		$.post('/ajax/ajaxOrg.php', 
		{   
			mode : 'get_more',
			search_word : search_word,
			dept_id : dept_id,
			page : page,
			user_is_fired : user_is_fired
		},
		function(data){ 
			
			if(data)
			{
				$('#users_list').append(data);
			}
			else
			{
				eval(empty_load_eval_str);
				$('#more_btn').hide();
			}
			 
			
			// Возможный переход к след итерации
			if(complete_load_eval_str)
			{
				eval(complete_load_eval_str);
			}
			 
			
		 	Org.actual_page++;
			 
			if(Org.actual_page>=pages_count)
			{
				$('#more_btn').hide();
			}
		});
	},
	
	scheme_lines_init : function()
	{
		var grhy_offset = $('#grhy').offset();
		var old_row = '';
		var lineh;
		var group_num = 0;
		
		var line_height = 3;
		var line_width = 3;
		
		
		$('.depts_group').each(function(){
			
			group_num++;
			
			var dept_pid = $(this).attr('dept_pid');
			var row = parseInt($(this).attr('row'));
			var parent_row = row - 1;
			var group_width = $(this).width();
			var center_gr_w = Math.round(group_width/2);
			var group_offset = $(this).offset();
			 
			 
			var boss_item = $('.depts_row[row="'+parent_row+'"] .dept_item[dept_id="'+dept_pid+'"]');
			 
			$(boss_item).attr('dept_parent', 1);
			//$(boss_item).css('cursor', 'pointer');
			$(boss_item).attr('group_num', group_num);
			$(this).attr('group_num', group_num);
			 
			if(row!=old_row)
			{
				lineh = 10;
				old_row = row;
			}
			else
			{
				lineh += 6;
			}
			
			if(dept_pid>0)
			{   
				var bi_height = $(boss_item).height();
				var bi_offset = $(boss_item).offset();
				
				var line_1_top = bi_offset.top - grhy_offset.top + bi_height + 2;
				var line_1_height = lineh;
				// Для начальника
				if(row==2)
				{  
					var line_1_left = bi_offset.left - grhy_offset.left + 82; 
				}
				else
				{
					var line_1_left = bi_offset.left - grhy_offset.left + 81; 
				}
				
				var line_2_top = line_1_top + line_1_height;
				
				var group_bl_abs_center = Math.abs(group_offset.left - grhy_offset.left + center_gr_w);
				
				// Для начальника
				if(row==2)
				{
					line_2_left = line_1_left;
					line_2_width = line_1_left - line_2_left + line_width;
					line_3_left = line_2_left;
				}
				else if(line_1_left <= group_bl_abs_center)
				{ 
					var line_2_left = line_1_left;
					var line_2_width = Math.abs(grhy_offset.left - group_offset.left) - line_2_left + center_gr_w;
					var line_3_left = line_2_left + line_2_width;
				}
				else
				{
					line_2_left = group_bl_abs_center;
					line_2_width = line_1_left - line_2_left + line_width;
					line_3_left = line_2_left;
				}
				 
				 
				var  line_3_height = Math.abs(grhy_offset.top - group_offset.top) - line_2_top;
				
				var  line_3_top = line_2_top;
				
				bil_line_1 = '<div class="line_start line_group_num_'+group_num+'" style="height:'+line_1_height+'px;left:'+line_1_left+'px; top:'+line_1_top+'px; width:'+line_height+'px"></div>';
				bil_line_2 = '<div class="line_start line_group_num_'+group_num+'" style="width:'+line_2_width+'px;left:'+line_2_left+'px; top:'+line_2_top+'px; height:'+line_height+'px"></div>';
				bil_line_3 = '<div class="line_start line_group_num_'+group_num+'" style="height:'+line_3_height+'px;left:'+line_3_left+'px; top:'+line_3_top+'px; width:'+line_height+'px"></div>';
			 
				dir_left = line_3_left + 10;
				dir_top = line_3_top + line_3_height - 20;
				 
				dir = '<div class="user_dir_bl_'+dept_pid+' user_dir" style="left:'+dir_left+'px; top:'+dir_top+'px"><a href="javascript:;" class="link dir_user_'+dept_pid+' user_dir_link" group_num="'+group_num+'" user_id="'+dept_pid+'"></a></div>';
				
				$('#grhy').prepend(bil_line_1+bil_line_2+bil_line_3+dir);
				 
	 
			}		 		
				 
		})
		
		$('.dept_item[dept_parent=1]').hover(function() {
				var this_group_num = $(this).attr('group_num');
				$('.line_group_num_'+this_group_num).toggleClass('dept_line_start_hover'); 
				$('.depts_group[group_num='+this_group_num+']').toggleClass('depts_group_hover'); 
				 
			})
		
	 
		 
		
	},
	
	scheme_get_dept_info : function(cont_type)
	{
		var depts_arr = {};
		var num = 0;
		
		$('#grhy .dept_item[loaded=0]').each(function(){
			
			depts_arr[$(this).attr('dept_id')] = $(this).attr('dept_id');
			
			if(num>10)
			return false
			
			num++;
		})
		 
		if(num==0)
		{
			return false
		}
		
		$.post('/ajax/ajaxOrg.php', 
		{   
			mode : 'get_dept_info',
			depts_arr : $.toJSON(depts_arr),
			scheme : scheme,
			cont_type : cont_type
		},
		function(data){ 
			
			$.each(data['depts_info'], function(i,j) {
				
				$('#grhy .dept_id_'+i).html(j['data']);
				$('#grhy .dept_id_'+i).attr('loaded', 1);
				//var name = $('.dept_id_'+i+' .userfullname').html();
				//$('.user_dir_bl_'+i).show();
				//$('.dir_user_'+i).html(name);
			})
			
			Org.scheme_get_dept_info(cont_type);
			
			
		}, 'json');
	},
	structure_to_center : function()
	{
		$(document).ready(function(){
			
		var  fd_offset = $('.dept_item:first').offset();
		
		var w_width = $(window).width();
		
		var result_scroll = fd_offset.left - w_width / 2 + 80;
		
		$('html, body').animate({scrollLeft: result_scroll}, 1300);
		
		})
		
	}
}

