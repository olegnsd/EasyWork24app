// Показывает блок с историей должностей пользователя
function show_user_positions_block(user_id)
{  
 	if($('#user_positions_'+user_id).css('display')=='block')
	{
		$('#user_positions_'+user_id).hide();
	}
	else
	{ 
		//$('.user_tree_positions_block').hide();
		$('#user_positions_'+user_id).show();
	}
}