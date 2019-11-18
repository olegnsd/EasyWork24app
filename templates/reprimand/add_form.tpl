<div style="display:none" id="add_form_block" class="add_form_margin">
<div class="title_add_form">Добавить</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="tables_data_1">
	<tr>
    	<td class="td_title">Тип</td>
        <td class="td_value"><select id="type" class="input_text"><option value="1">Выговор</option><option value="2">Поощрение</option></select>
        </td>
        
    </tr>
	<tr>
    	<td class="td_title">Сотрудник</td>
        <td class="td_value"><select id="worker_id" class="input_text">{WORKERS_LIST}</select>
        </td>
        
    </tr>
    <tr>
    	<td class="td_title td_vert_top">Текст</td>
        <td class="td_value"><textarea id="reprimand_text" class="input_text" style="width:600px; height:200px"></textarea>
        <div id="text_error" class="td_error sub_input_error"></div>
        
       
        </td>
        
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="add_reprimand()" href="javascript:;" id="add_reprimand_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить</div></a></td>
        
    </tr> 
     
</table>

</div>

<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#add_form_block').hide(); $('#show_add_form_a').show()">Скрыть</a>
</div>

</div>

<div class="add_new_list_item" id="show_add_form_a" > 
<a href="javascript:;" class="link" onclick="$('#add_form_block').fadeIn(200); $('#show_add_form_a').hide()">+ Добавить</a>
</div>


<script>

planning_date_init(1, 0);
planning_date_init(2, 0);

</script>