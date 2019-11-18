function tp_notice_bar_init(what)
{
	$.post('/ajax/ajaxTopPanel.php', 
	{   
		mode : 'tp_notice_bar_init',
		what : what
	},
	function(data){ 
		
		loading_btn('close_planning_session_btn', 1);
		
		if(what=='ps')
		{
			$('#nc_ps').html(data['planning_session'])
		}
		
		if(data['planning_session'])
		{
			$('#nc_ps').html(data['planning_session'])
		}
		if(data['cal_of_events'])
		{
			$('#nc_cof').html(data['cal_of_events'])
		}
		
	}, 'json');	 
}


function planning_session_notice()
{
	$.post('/ajax/ajaxTopPanel.php', 
	{   
		mode : 'planning_session_notice'
	},
	function(data){ 
		
		loading_btn('close_planning_session_btn', 1);
		
		if(data)
		{
			$('#planning_session_cont').replaceWith(data)
		}
	});	 
}

function wk_top()
{
	$.post('/ajax/ajaxTopPanel.php', 
	{   
		mode : 'wk_top'
	},
	function(data){ 
		
		loading_btn('close_planning_session_btn', 1);
		
		if(data)
		{
			$('#wktime_tpp').html(data)
		}
	});	 
}
function toggle_st_class_btn()
{
	$('.wkact_btn_start').toggleClass('finish');
	setTimeout(toggle_st_class_btn,700)
}


function bind_hide_tp_popup_form()
{  
  	
	$('.top_p_popup').bind('click', function(event){
				event.stopPropagation();
			})
			
	 $('body').unbind('click', remove_top_p_popup);
		
		setTimeout(function(){
		 
			$('body').bind('click', remove_top_p_popup);
			
	 }, 100);
	
}

function remove_top_p_popup(e)
{
	var target = e.target;
	if(!$(target).hasClass('top_p_popup'))
	{
		$('.top_p_popup').remove();
		$('body').unbind('click', remove_top_p_popup);
	}
}
function toggle_top_popups(elem)
{
	if($('.top_p_popup[wh="'+elem+'"]').is(':visible'))
	{
		$('.top_p_popup').remove();
		return false;
	}
	else return true;
}
function open_top_popup(data, elem)
{
	$('#ps_wrap').html("<div class='top_p_popup' wh="+elem+">"+data+"</div>");
}