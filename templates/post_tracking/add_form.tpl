<div style="display:none" id="add_form_block" class="add_form_margin">
<div class="title_add_form">Добавить трекинг</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="posttr_add_tb">
	<tr>
    	<td class="td_title td_vert_top" style="width:80px">Номер<sup>*</sup></td>
        <td class="td_value"><input type="text" id="tracking_barcode"  class="input_text" style="width:150px"/>
        <div class="error_inp"></div>
        </td>
    </tr>
	<tr>
    	<td class="td_title td_vert_top" style="width:80px">Описание</td>
        <td class="td_value"><textarea class="input_text" style="width:600px" id="tracking_desc"></textarea>
        <div class="error_inp"></div>
        </td>
    </tr>
    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Привязка</td>
        <td class="td_value">
        
        <input type="radio" id="trr_to_1" name="tracking_link" checked="checked" value="0"/><label for="trr_to_1">Нет</label>
        <input type="radio" id="trr_to_2" name="tracking_link" value="1"/><label for="trr_to_2">Сделка</label>
        <input type="radio" id="trr_to_3" name="tracking_link" value="2"/><label for="trr_to_3">Контрагент</label>
        
        <div class="add_form_sub_rd_bl" style="display:none" id="tracking_link_deal_bl"><select id="tracking_link_deal"></select></div>
        <div class="add_form_sub_rd_bl" style="display:none" id="tracking_link_client_bl"><select id="tracking_link_client"></select></div>
        <div class="error_inp"></div>
        </td>
    </tr>
    
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="PostTracking.add_tracking()" href="javascript:;" id="add_tracking_btn">
  	    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить трекинг</div></a>
        <div class="clear"></div>
        <div class="error_box" id="error_box"></div>
	    <div id="success" class="success_marg"></div>

	</td>
        
    </tr> 
    
     
</table>

</div>

<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#add_form_block').hide(); $('#show_add_form_a').show()">Скрыть</a>
</div>

</div>


<div class="add_new_list_item" id="show_add_form_a" > 
<a href="javascript:;" class="link" onclick="$('#add_form_block').fadeIn(200); $('#show_add_form_a').hide()">+ Добавить трекинг</a>
</div>

<script>
$('input[name="tracking_link"]').bind('change', function(){
	
	var checked_link = $(this).val();
	
	$('.posttr_add_tb .add_form_sub_rd_bl').hide();
	
	if(checked_link==1)
	{
		$('#tracking_link_deal_bl').show();
	}
	else if(checked_link==2)
	{
		$('#tracking_link_client_bl').show();
	}
})

PostTracking.init_add_easycomplete();

</script>