
// Подтверждение добавление в список
function confirm_to_worker_list(invite_user_id, invited_user_id)
{
	 
	$.post('/ajax/ajaxBoss.php', 
	{   
		mode : 'confirm_to_worker_list',
		invite_user_id : invite_user_id,
		invited_user_id : invited_user_id
	},
	function(data){ 
		
		if(data['new_boss_count']>=1)
		{
			$('#new_boss_count').html('(+ '+data['new_boss_count']+')');
		}
		else
		{
			$('#new_boss_count').html('');
		}
				
		get_boss_list(invited_user_id);
		
		$('#notice_'+invite_user_id).remove();
		
	},'json');
}


// Отклонить добавление в список
function not_confirm_to_worker_list(invite_user_id, invited_user_id)
{
	 
	
	$.post('/ajax/ajaxBoss.php', 
	{   
		mode : 'not_confirm_to_worker_list',
		invite_user_id : invite_user_id,
		invited_user_id : invited_user_id
	},
	function(data){ 
		
		if(data['new_boss_count']>=1)
		{
			$('#new_boss_count').html('(+ '+data['new_boss_count']+')');
		}
		else
		{
			$('#new_boss_count').html('');
		}
		
		$('#notice_'+invite_user_id).remove()
		
		
	}, 'json');
}


// Отклонить добавление в список
function get_boss_list(user_id)
{
	$('#boss_list').html("<img src='/img/loading5.gif'>");
	
	$.post('/ajax/ajaxBoss.php', 
	{   
		mode : 'get_boss_list',
		user_id : user_id
	},
	function(data){ 
		
		if(data)
		{
			$('#boss_list').html(data)
		}
		
	});
}