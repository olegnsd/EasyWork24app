// Добавление нового клиента
function save_video_iframe_data(camera_id)
{
	var iframe_src, iframe_text
	 
	iframe_src  = $('#iframe_src').val();
	iframe_text  = $('#iframe_text').val();
	
	loading_btn('save_camera_iframe_btn')
	
	$.post('/ajax/ajaxCamera.php', 
	{   
		mode : 'save_video_iframe_data',
		camera_id : camera_id,
		iframe_src : iframe_src,
		iframe_text : iframe_text	
	},
	function(data){ 
		
		loading_btn('save_camera_iframe_btn', 1);
				
		if(data==1)
		{
			//$('#iframe_'+camera_id).attr('src', iframe_src);
			//$('#iframe_result').html('<div class="success">Изменения успешно сохранены</div>');
			//setTimeout(function(){$('#iframe_result').html('')}, 1000);
			//$('#iframe_'+camera_id).reload()
			document.location.reload();
		}
	});
}
