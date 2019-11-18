function grhy_user_info(cont_type, elem)
{
	$('.grhy_cont_type_show_list a').removeClass('grhy_shc_active');
	
	$(elem).addClass('grhy_shc_active');
	
	$('.grhy .worker_user_item[is_top!=1]').attr('loaded', 0);
	$('.grhy .worker_user_item[is_top!=1]').html('<div style="padding-top:20px;">Загрузка данных..</div>');
	grhy_get_user_info(cont_type);
}
function grhy_get_user_info(cont_type)
{
	var users_arr = {};
	var num = 0;
	
	$('.grhy .worker_user_item[loaded=0]').each(function(){
		
		users_arr[$(this).attr('user_id')] = $(this).attr('user_id');
		
		if(num>30)
		return false
		
		num++;
	})
	 
	if(num==0)
	{
		return false
	}
	
	$.post('/ajax/ajaxGrhy.php', 
	{   
		mode : 'get_user_info',
		users_arr : $.toJSON(users_arr),
		scheme : scheme,
		cont_type : cont_type
	},
	function(data){ 
		
		$.each(data['users_data'], function(i,j) {
			
			if(scheme==1)
			{
				$('.grhy .worker_id_'+i).html(j['info']);
				$('.grhy .worker_id_'+i).attr('loaded', 1);
				var name = $('.worker_id_'+i+' .userfullname').html();
				$('.user_dir_bl_'+i).show();
				$('.dir_user_'+i).html(name);
			}
			else if(scheme==2)
			{
				var status_class;
				
				$('.grhy .worker_id_'+i+' .worker_item_hover_cont .worker_item').html(j['info']);
				$('.grhy .worker_id_'+i).attr('loaded', 1);
				
				status_class = $('.grhy .worker_id_'+i+' .worker_item_hover_cont .worker_item div[default_info=1]').attr('status_class');
				
				$('.grhy .worker_id_'+i).addClass(status_class);
			}
			 
		})
		grhy_get_user_info(cont_type);
		
		
	}, 'json');
}


function grhy_lines_init()
{
	var grhy_offset = $('#grhy').offset();
	var old_row = '';
	var lineh;
	var group_num = 0;
	
	if(scheme==1)
	{
		var line_height = 3;
		var line_width = 3;
	}
	else if(scheme==2)
	{
		var line_height = 1;
		var line_width = 1;
	}
	
	$('.workers_group').each(function(){
		
		group_num++;
		
		var boss_id = $(this).attr('boss_id');
		var row = parseInt($(this).attr('row'));
		var parent_row = row - 1;
		var group_width = $(this).width();
		var center_gr_w = Math.round(group_width/2);
		var group_offset = $(this).offset();
		 
		 
	 	var boss_item = $('.workers_row[row="'+parent_row+'"] .worker_user_item[user_id="'+boss_id+'"]');
		 
		$(boss_item).attr('boss_parent', 1);
		$(boss_item).css('cursor', 'pointer');
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
		
		if(boss_id>0)
		{   
			var bi_height = $(boss_item).height();
			var bi_offset = $(boss_item).offset();
			
			var line_1_top = bi_offset.top - grhy_offset.top + bi_height + 2;
			var line_1_height = lineh;
			// Для начальника
			if(row==2)
			{  
				var line_1_left = bi_offset.left - grhy_offset.left + 148; 
			}
			else if(scheme==1)
			{
				var line_1_left = bi_offset.left - grhy_offset.left + 81; 
			}
			else if(scheme==2)
			{
				var line_1_left = bi_offset.left - grhy_offset.left + 8; 
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
			 
		 	dir = '<div class="user_dir_bl_'+boss_id+' user_dir" style="left:'+dir_left+'px; top:'+dir_top+'px"><a href="javascript:;" class="link dir_user_'+boss_id+' user_dir_link" group_num="'+group_num+'" user_id="'+boss_id+'"></a></div>';
			
			$('#grhy').prepend(bil_line_1+bil_line_2+bil_line_3+dir);
			 
 
		}		 		
			 
	})
	
	$('.worker_user_item[boss_parent=1]').hover(function() {
			var this_group_num = $(this).attr('group_num');
			$('.line_group_num_'+this_group_num).toggleClass('line_start_hover'); 
			$('.workers_group[group_num='+this_group_num+']').toggleClass('workers_group_hover'); 
			 
		})
	
 
 	if(scheme==1)
	{
		$('.worker_user_item[boss_parent=1]').bind('click', function() {
				var this_group_num = $(this).attr('group_num');
				var group = $('.workers_group[group_num='+this_group_num+']');
				var group_offset = $(group).offset();
				var grhy_offset = $('#grhy').offset();
				
				var left = grhy_offset.left - group_offset.left + 150;
				var top = grhy_offset.top - group_offset.top + 150;
				
				$(group).addClass('not_confirm');
				
				setTimeout(function(){ $(group).removeClass('not_confirm') }, 1400);
				
				 $(".grhy").animate({
					left: left,
					top : top
				  }, 1100 );
				 
		})
		
		$('.user_dir_link').bind('click', function() {
				var this_group_num = $(this).attr('group_num');
				var user_id = $(this).attr('user_id');
				var user_boss = $('.worker_user_item[group_num="'+this_group_num+'"]');
				var user_offset = $(user_boss).offset();
				var grhy_offset = $('#grhy').offset();
			 
			 
			 	var left = grhy_offset.left - user_offset.left + 150;
				var top = grhy_offset.top - user_offset.top + 150;
				
				$(user_boss).addClass('not_confirm');
				
				setTimeout(function(){ $(user_boss).removeClass('not_confirm') }, 1400);
				
			 	 $(".grhy").animate({
					left: left,
					top : top
				  }, 1100 );
			 	
				
				//alert(user_offset.top)
		})	
	}
	else if(scheme==2)
	{	
	selected_item = 0;
	
		$('.worker_user_item').bind('click', function() {
			
			 var this_group_num;
			 
			 this_group_num = $(this).attr('group_num');
			 
			 if(selected_item)
			 {
				 tmp_group = $(selected_item).attr('group_num');
				 $('.line_group_num_'+tmp_group).removeClass('line_start_click'); 
				 $('.workers_group[group_num='+tmp_group+']').removeClass('workers_group_click'); 
				 $(selected_item).children('.worker_item_hover_cont').hide(200);
			 }
			 
			 selected_item = this;
			  
			 if($(this).children('.worker_item_hover_cont').is(':visible'))
			 {
				 
				 
				 $('.line_group_num_'+this_group_num).removeClass('line_start_click');
				  
				 $('.workers_group[group_num='+this_group_num+']').removeClass('workers_group_click');  
			 }
			 else
			 {
				 $(this).children('.worker_item_hover_cont').show(200);
				 $('.line_group_num_'+this_group_num).addClass('line_start_click'); 
				 $('.workers_group[group_num='+this_group_num+']').addClass('workers_group_click'); 
			 }
			})
	}
}

function g_slide_left_menu()
{
	//alert($('.wrap_grhy').height())
	
	if($('.left_menu_grhy').is(':visible'))
	{
		$('.left_menu_grhy').slideUp(100);
		
		setTimeout(function(){$('.menu_slide').removeClass('menu_slide_open');},110);

		 
		$(".menu_slide").animate({
				left: 10
		}, 300 );
		 
	}
	else
	{
		$('.left_menu_grhy').css('height', $('.wrap_grhy').height()+'px');
		
		$('.left_menu_grhy').slideDown(400);
		
		$(".menu_slide").animate({
				left: 166
		}, 300 );
			
		$('.menu_slide').addClass('menu_slide_open');
	
	}
 
}