<div class="contacts_item add_form" id="contact_{CONTACT_ID}">
<div id="contact_content_{CONTACT_ID}">
<table cellpadding="0" cellspacing="0" class="tables_data_1">
	<tr>
    	<td class="td_title">ФИО</td>
        <td class="td_value"><input type="text" id="contact_user_name_{CONTACT_ID}" value="{CONTACT_USER_NAME}" class="input_text" /></td>
        
    </tr>  
    <tr>
    	<td class="td_title td_vert_top">Название контакта<sup>*</sup></td>
        <td class="td_value"><input type="text" id="contact_name_{CONTACT_ID}" value="{CONTACT_NAME}" class="input_text" />
        <div id="" class="td_error sub_input_error"></div></td>
        
    </tr>  
    <tr>
    	<td class="td_title">Телефон</td>
        <td class="td_value"><input type="text" id="contact_phone_{CONTACT_ID}" value="{CONTACT_PHONE}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Должность</td>
        <td class="td_value"><input type="text" id="contact_job_{CONTACT_ID}" value="{CONTACT_JOB}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Описание</td>
        <td class="td_value"><textarea type="text" id="contact_desc_{CONTACT_ID}" class="input_text">{CONTACT_DESC}</textarea></td>
        
    </tr>
    <tr>
    	<td class="td_title">Фото</td>
        <td class="td_value"><div id="contact_images_uploaded_image_{CONTACT_ID}">{IMAGE}{EDIT_IMAGE_DELETE}</div><a href="javascript:;" id="contact_images_upload_{CONTACT_ID}" class="link">загрузить</a></td>
        
    </tr> 
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="save_contact('{CONTACT_ID}')" href="javascript:;" id="save_contact_btn_{CONTACT_ID}">
    <div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a>
    <div class="button_sep"></div>
    <a class="button" onclick="cancel_save_contact('{CONTACT_ID}')" href="javascript:;" id="cancel_save_contact_btn_{CONTACT_ID}">
    <div class="right"></div><div class="left"></div><div class="btn_cont">отмена</div></a>
    </td>
        
    </tr> 
     
</table>

</div>
<div id="contact_result_{CONTACT_ID}"></div>
<div id="contact_proc_{CONTACT_ID}"></div>
</div>

<script>
contact_image_upload_init({CONTACT_ID});
</script>