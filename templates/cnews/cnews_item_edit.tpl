<div class="add_form add_form_margin" id="cnews_{CNEWS_ID}">
<div class="title_add_form">Редактирование новости</div>
<table cellpadding="0" cellspacing="0" class="tables_data_1">
    <tr>
    	<td class="td_title td_vert_top">Тема</td>
        <td class="td_value"><input id="cnews_theme_{CNEWS_ID}" class="input_text" value="{CNEWS_THEME}" style="width:650px;">
        <div id="text_error" class="td_error sub_input_error"></div>
        </td>
    </tr>
    <tr>
    	<td class="td_title td_vert_top">Текст</td>
        <td class="td_value"><textarea id="cnews_text_{CNEWS_ID}" class="input_text" style="width:650px; height:300px">{CNEWS_TEXT}</textarea>
        <div id="text_error" class="td_error sub_input_error"></div>
        </td>
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="save_cnews('{CNEWS_ID}')" href="javascript:;" id="save_cnews_btn_{CNEWS_ID}">
    	<div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
        <div class="button_sep"></div>
        <a class="button" onclick="get_cnews_item('{CNEWS_ID}',0)" href="javascript:;" id="add_reprimand_btn">
    	<div class="right"></div><div class="left"></div><div class="btn_cont">отменить</div></a></td>
    </tr>
</table>
</div>
