<div style="display:none" id="add_form_block" class="add_form_margin">
<div class="title_add_form">Спланировать</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="tables_data_1">
<tbody>
<tr>
    <td class="td_title" style="width:100px">Уведомить</td>
    <td class="td_value"><input type="radio" name="notice_to" value="1" id="notice_to_1" checked="checked" /> <label for="notice_to_1">Руководителя</label> <input type="radio" name="notice_to" value="2" id="notice_to_2"/> <label for="notice_to_2">Сотрудников</label>
    </td>    
</tr>
</tbody> 

<tbody class="planning_forms">

{FORM_FOR_BOSS}

{FORM_FOR_WORKERS}

</tbody> 
<tbody>
<tr>
    	<td class="td_title">Причина</td>
        <td class="td_value"><select id="planning_type_id" class="input_text"><option value="0">- Выбрать -</option>{PLANNING_TYPE_LIST}</select> или <input type="text" id="planning_type_str" placeholder="Другое.." class="input_text" style="width:200px"/></td>
        
    </tr>
<tr>
    <td class="td_title td_vert_top">Дата <sup>*</sup></td>
    <td class="td_value">
    
    <div class="" id="planning_date_one_list">
    <div class="planning_data_item"><input type="text" id="date_one_0" class="input_text date_input" /> <input class="input_text time_inp date_input_hours" maxlength="2" id="date_one_hour_0" placeholder="чч"/>:<input class="input_text time_inp date_input_minutes" maxlength="2" id="date_one_minutes_0" placeholder="мм"/></div>
    </div>
    
    <div class="planning_more_date">
        <a href="javascript:;" class="link" id="more_upload_images_btn" onclick="planning_more_date(1)">[+] Еще дата</a>
    </div>
    
   	 <div class="planning_date_period_list" id="planning_date_period_list">
    <div class="planning_data_item">c <input type="text" id="date_period_from_0" class="input_text date_input date_input_from" /> <input class="input_text time_inp date_input_from_hour" maxlength="2" id="date_period_from_hour_0" placeholder="чч"/>:<input class="input_text time_inp date_input_from_minutes" maxlength="2" id="date_period_from_minutes_0" placeholder="мм"/> по <input type="text" id="date_period_to_0" class="input_text date_input date_input_to"/> <input class="input_text time_inp date_input_to_hour" maxlength="2" id="date_period_to_hour_0" placeholder="чч"/>:<input class="input_text time_inp date_input_to_minutes" maxlength="2" id="date_period_to_minutes_0" placeholder="мм"/></div>
    </div>
    
    <div class="planning_more_date">
        <a href="javascript:;" class="link" id="more_upload_images_btn" onclick="planning_more_date(2)">[+] Еще период</a>
    </div>
    <br> 
    <div id="date_error" class="td_error sub_input_error"></div> 
    </td>
    
</tr>
<tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="add_planning()" href="javascript:;" id="add_planning_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">спланировать</div></a></td>
        
    </tr> 
</tbody>

</table>

</div>

<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#add_form_block').hide(); $('#show_add_form_a').show()">Скрыть</a>
</div>

</div>

<div class="add_new_list_item" id="show_add_form_a" > 
<a href="javascript:;" class="link" onclick="$('#add_form_block').fadeIn(200); $('#show_add_form_a').hide()">+ Спланировать</a>
</div>


<script>
$('input[name="notice_to"]').bind('click', function(e)
{ 
	$('.planning_forms .form_wrap').hide();
	var value = $(e.target).val();
	$('#form_'+value).show();
})
planning_date_init(1, 0);
planning_date_init(2, 0);
</script>