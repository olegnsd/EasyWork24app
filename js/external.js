function save_external_service_iframe_data(service_id)
{
	var iframe_src, iframe_text
	 
	iframe_src  = $('#iframe_src').val();
	iframe_text  = $('#iframe_text').val();
	
	loading_btn('save_external_service_iframe_data_btn')
	
	$.post('/ajax/ajaxExternal.php', 
	{   
		mode : 'save_external_service_iframe_data',
		service_id : service_id,
		iframe_src : iframe_src,
		iframe_text : iframe_text,
		user_id : current_user_id	
	},
	function(data){ 
		
		loading_btn('save_external_service_iframe_data_btn', 1)
		
		if(data==1)
		{
			document.location.reload();
		}
	});
}
