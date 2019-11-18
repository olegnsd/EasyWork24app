<div class="add_form">
<table cellpadding="0" cellspacing="0" class="tables_data_1">
	<tr>
    	<td class="td_title">Ссылка на ресурс</td>
        <td class="td_value"><input type="text" value="{VALUE_SRC}" id="iframe_src" class="input_text" /></td>
         
    </tr>
    <tr>
    	<td class="td_title">Комментарий</td>
        <td class="td_value"><textarea class="input_text" id="iframe_text">{CAM_TEXT}</textarea></td>
        
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="save_video_iframe_data('{CAMERA_ID}')" href="javascript:;" id="save_camera_iframe_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">изменить</div></a>
   </td>
         
    </tr>
</table>
</div>
<div id="iframe_result"></div>
{IFRAME_BLOCK}
