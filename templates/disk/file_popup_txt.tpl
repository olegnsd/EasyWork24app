<div class="title">{FILE_NAME} <a href="{DOWNLOAD_LINK}" class="to_download_btn">Скачать</a></div>

<div  style="background-color:#F8F8F8; height:100%">
<div id="pre_load" style=" text-align:center; padding-top:100px"><img src="/img/ajax-loader.gif" /></div>
<iframe class="fpreview_fr" style="border:medium none; display:none" src="https://docs.google.com/viewer?url={URL}&embedded=true"></iframe>
</div>


<script>
$('.fpreview_fr').bind('load', function(){
	$('.fpreview_fr').show();
	$('#pre_load').remove();
});

</script>