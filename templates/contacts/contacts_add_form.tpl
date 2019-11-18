<div style="display:none" id="add_contact_form_block" class="add_form_margin">
<div class="title_add_form">Добавить контакт</div>
<div class="add_form add_form_margin">
<table cellpadding="0" cellspacing="0"  class="tables_data_1">
	<tr>
    	<td class="td_title td_vert_top">Название контакта<sup>*</sup></td>
        <td class="td_value"><input type="text" id="contact_name" value="" class="input_text" />
        <div id="" class="td_error sub_input_error"></div></td>
        
    </tr> 
	<tr>
    	<td class="td_title">ФИО</td>
        <td class="td_value"><input type="text" id="contact_user_name" value="" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Телефон</td>
        <td class="td_value"><input type="text" id="contact_phone" value="" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Должность</td>
        <td class="td_value"><input type="text" id="contact_job" value="" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Описание</td>
        <td class="td_value"><textarea type="text" id="contact_desc" class="input_text" /></textarea></td>
        
    </tr>
    <tr>
    	<td class="td_title">Фото</td>
        <td class="td_value"><div id="contact_images_uploaded_image_0"></div><a href="javascript:;" id="contact_images_upload_0" class="link">загрузить</a></td>
        
    </tr>  
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="add_contact(0)" href="javascript:;" id="add_contact_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить контакт</div></a>
  	</td>
        
    </tr> 
     
</table>

</div>
<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#add_contact_form_block').hide(); $('#add_contact_show_btn').show()">Скрыть</a>
</div>
</div>

<div class="add_new_list_item" id="add_contact_show_btn"> 
<a href="javascript:;" id="show_good_add_form_a" class="link" onclick="$('#add_contact_form_block').fadeIn(200); $('#add_contact_show_btn').hide()">+ Добавить контакт</a>
</div>

<script>
contact_image_upload_init(0);
</script>