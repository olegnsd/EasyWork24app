<div style="display:none" id="add_good_form_block" class="add_form_margin">
<div class="title_add_form">Добавить имущество</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="tables_data_1">
	<tr>
    	<td class="td_title">Название<sup>*</sup></td>
        <td class="td_value"><input type="text" id="good_name" value="" style="width:618px" class="input_text" />
        <div id="" class="td_error sub_input_error"></div></td>
        
    </tr>
    <tr>
    	<td class="td_title">Цена</td>
        <td class="td_value"><input type="text" id="good_price" value="0.00" style="width:100px" class="input_text" /></td>
        
    </tr>
    <tr>
    	<td class="td_title td_vert_top">Фотографии</td>
        <td class="td_value">
        
         <div id="goods_images">
             <div  class="upload_good_image_bl" default="1">
                <a href="javascript:;" class="link" id="goods_images_upload_1">Загрузить</a> <span id="goods_images_uploaded_delete_1"></span>
                <div id="goods_images_uploaded_image_1" class="good_load_img_cont"></div>
                <div id="goods_images_upload_proc_1"></div>
             </div>
         
         </div>
       
        <div class="good_upload_more">
        <a href="javascript:;" class="link" id="more_upload_images_btn" style="display:block; margin-top:-10px; float:left" onclick="more_upload_good_images()">[+] Еще</a>
        </div>
        </td>
        
    </tr>
     

	<tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="add_new_good()" href="javascript:;" id="add_good_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить имущество</div></a></td>
        
    </tr> 
     
</table>

</div>

<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#add_good_form_block').hide(); $('#show_good_add_form_a').show()">Скрыть</a>
</div>

</div>

<div class="add_new_list_item" > 
<a href="javascript:;" id="show_good_add_form_a" class="link" onclick="$('#add_good_form_block').fadeIn(200); $(this).hide()">+ Добавить имущество</a>
</div>
<script>
$(document).ready(function(){                
goods_image_upload_init('1');
$('#good_price').keydown(sum_mask_proc);
$('#good_price').blur(check_for_format)
});


</script>