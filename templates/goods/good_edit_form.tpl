<div class="add_form_margin">
<div class="title_add_form">Редактирование имущества</div>
<div class="add_form add_form_margin">
<table cellpadding="0" cellspacing="0" class="tables_data_1">
	<tr>
    	<td class="td_title">Название<sup>*</sup></td>
        <td class="td_value"><input type="text" id="good_name" value="{GOOD_NAME}" style="width:618px" class="input_text" />
        <div id="" class="td_error sub_input_error"></div></td>
        
    </tr>
    <tr>
    	<td class="td_title">Цена</td>
        <td class="td_value"><input type="text" id="good_price" style="width:100px" value="{GOOD_PRICE}" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title td_vert_top">Фотографии</td>
        <td class="td_value">
         <div id="goods_images">
         {IMAGES_LIST}
       	 </div>
        
        <div class="good_upload_more">
        <a href="javascript:;" class="link" id="more_upload_images_btn" onclick="more_upload_good_images()">[+] Еще</a>
        </div>
        </td>
        
    </tr>
     

	<tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="save_good('{GOOD_ID}')" href="javascript:;" id="save_good_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">сохранить имущество</div></a>
    </td>
        
    </tr> 
    
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
       <div id="edit_good_success"></div>
    </td>
        
    </tr> 
     
</table>

</div>



</div>

<script>
if(!$('#goods_images .upload_good_image_bl').is('.upload_good_image_bl'))
{ 
	 more_upload_good_images();
}

</script>

