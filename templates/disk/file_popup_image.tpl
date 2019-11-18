<div class="title">{FILE_NAME} <a href="{DOWNLOAD_LINK}" class="to_download_btn">Скачать</a></div>
<div class="popup_img_wrap">
<table cellpadding="0" cellspacing="0" style="width:100%; height:100%">
<tr>
	<td class="fpreview_img_wrap_td fpreview_img_wrap_td_load">
    
    <img id="prev_img" src='{DOWNLOAD_LINK}' style="display:none"></td>
</tr>
</table>
</div>

<script>
$('#prev_img').bind('load', function(){
	$('#prev_img').fadeIn(200);
	$('.fpreview_img_wrap_td_load').removeClass('fpreview_img_wrap_td_load')
});

</script>