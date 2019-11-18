<div style="display:none" id="add_form_block" class="add_form_margin">
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="tables_data_1">
	<tr>
    	<td class="td_title">Ссылка на ресурс</td>
        <td class="td_value"><input type="text" value="{IFRAME_SRC}" id="iframe_src" style="width:580px;" class="input_text" /></td>
         
    </tr>
    <tr>
    	<td class="td_title">Комментарий</td>
        <td class="td_value"><textarea class="input_text" id="iframe_text" style="width:580px;">{IFRAME_TEXT}</textarea></td>
         
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="save_external_service_iframe_data('{SERVICE_ID}')" href="javascript:;" id="save_external_service_iframe_data_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">изменить</div></a></td>
         
    </tr>
</table>
</div>
<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#add_form_block').hide(); $('#show_add_form_a').show()">Скрыть</a>
</div>
</div>

<div class="add_new_list_item" id="show_add_form_a" > 
<a href="javascript:;" class="link" onclick="$('#add_form_block').fadeIn(200); $('#show_add_form_a').hide()">+ Изменить ссылку на внешний сервис</a>
</div>

<div id="iframe_result"></div>

