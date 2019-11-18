<div class="user_image_form_cont">
<div class="upload_image_btn"  id="upload_image_btn">
Загрузить фотографию

</div>
<div class="upload_image_recommend_text">Фотография должна иметь размер не меньше {MIN_IMAGE_RESOLUTION} точек и не больше {MAX_IMAGE_RESOLUTION} точек по каждой из сторон. <br />Максимальный размер загружаемого файла {MAX_UPLOAD_SIZE} МБ.</div>

<div class="error" id="upload_error"></div>
<center><div id="upload_proc" style="margin-top:40px; margin-bottom:40px"></div></center>


<div id="image_tb_block" style="display:{IMAGE_FORM_DISPLAY}">
<table cellpadding="0" cellspacing="0" style="margin-top:20px">
	
    <tr>
    	<td style="font-size:13px; padding-bottom:7px">Оригинальное изображение:</td>
        <td style="font-size:13px; padding-bottom:7px">Выберите уменьшенную копию:</td>
    </tr>
	<tr>
    	<td style=" padding-right:20px;vertical-align:top"><div id="uploaded_image"><img id="user_image" src="{IMAGE_SRC}" /></div></td>
        <td style="vertical-align:top"><div class="jcrop_preview_block" id="user_image_preview_avatar_block" ><img src="{IMAGE_SRC}" id="user_image_preview_avatar" /></div></td>
    </tr>
</table>
<div class="save_btn_block" id="save_btn_block">
<div style="width:90px; margin:auto">
<a class="button"  onclick="save_user_image()" href="javascript:;" id="save_user_image_btn"><div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a></div>
 
</div>
</div>
</div>
<script>
$(document).ready(function()
{
	init_crop_coord_x = '{INIT_CROP_COORD_X}';
	init_crop_coord_y = '{INIT_CROP_COORD_Y}';
	init_crop_coord_x2 = '{INIT_CROP_COORD_X2}';
	init_crop_coord_y2 = '{INIT_CROP_COORD_Y2}';
	personal_image_upload_init();
	image_crop_init()
	 
})
</script>