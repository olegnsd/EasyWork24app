// Добавление нового клиента
function save_auto_iframe_data(auto_id)
{
	var iframe_src, iframe_text
	 
	iframe_src  = $('#iframe_src').val();
	iframe_text  = $('#iframe_text').val();
	
	loading_btn('save_auto_iframe_btn')
	
	$.post('/ajax/ajaxAuto.php', 
	{   
		mode : 'save_auto_iframe_data',
		auto_id : auto_id,
		iframe_src : iframe_src,
		iframe_text : iframe_text	
	},
	function(data){ 
		
		loading_btn('save_auto_iframe_btn', 1)
		
		if(data==1)
		{
			document.location.reload();
		}
	});
}
