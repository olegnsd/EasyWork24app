<div class="user_cont_block add_form " id="ofdoc_{OFDOC_ID}">
<div class="cont_hide_after_act_{OFDOC_ID}">
</div>

<table cellpadding="0" cellspacing="0" class="tables_data_1">
	<tr>
    	<td class="td_title">Руководитель</td>
        <td class="td_value">{TO_SURNAME} {TO_NAME} {TO_MIDDLENAME}</td>
    	 
    </tr>
    <tr>
    	<td class="td_title">Тип документа</td>
        <td class="td_value"><select id="ofdocs_type_{OFDOC_ID}" class="input_text">{OFDOCS_TYPE_LIST}</select></td>
        
    </tr>
    <tr>
    	<td class="td_title td_vert_top">Текст</td>
        <td class="td_value"><textarea id="ofdocs_text_{OFDOC_ID}" class="input_text" style="width:600px; height:300px">{OFDOC_TEXT}</textarea>
        <div id="text_error" class="td_error sub_input_error"></div>
        
       
        </td>
        
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="save_ofdoc({OFDOC_ID})" href="javascript:;" id="save_ofdoc_{OFDOC_ID}">
    <div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
    	<div class="button_sep"></div>
        <a class="button" onclick="cancel_save_ofdoc('{OFDOC_ID}')" href="javascript:;" id="cancel_save_btn_{OFDOC_ID}">
    <div class="right"></div><div class="left"></div><div class="btn_cont">отменить</div></a>
    </td>
        
    </tr> 
     
</table>
<div class="clear"></div>
</div>