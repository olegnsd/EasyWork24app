function show_video_ins(video_href_id, site_page)
{ 
	var video_name;
	
	$('.video_ins_menu_item').removeClass('video_ins_selected');
	
	$('.video_ins_menu_item[rel='+video_href_id+']').addClass('video_ins_selected');
	
	video_name = $('.video_ins_menu_item[rel='+video_href_id+']').attr('title');
	
	$('#video_ins_title').html(video_name);
	
	$('#video_ins_wrap').html('<iframe src="//player.vimeo.com/video/'+video_href_id+'?byline=0&amp;portrait=0&amp;color=96c83b" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
	
	
	$.post('/ajax/ajaxVideoInstructions.php', 
	{   
		mode : 'user_show_video_ins',
		video_href_id : video_href_id,
		site_page : site_page
	
	},
	function(data){ 
		
		
	});
}

function get_video_instruction(method)
{
	$.post('/ajax/ajaxVideoInstructions.php', 
	{   
		mode : 'get_video_instruction',
		site_page : o,
		method : method
	
	},
	function(data){ 
		
		if(data['video_href_id']!='')
		{ 
			if($('#vi').attr('id')!='vi')
			{
				$('.site_center_block:first').prepend('<div id="vi">'+data['video_block']+'</div>');
				  
			}
			$('#vi').show(200);
			$('.video_ins_menu_item[rel='+data['video_href_id']+']').trigger('click');
		}
	}, 'json');
}

function close_video_instruction_block()
{
	$('#vi').remove();
}
