<div style="display:none" id="add_form_block" class="add_form_margin">
<div class="title_add_form">Создать документ</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="tables_data_1">
	<tr>
    	<td class="td_title">Кому</td>
        <td class="td_value"><select id="ofdocs_to_user_id" class="input_text"></select>
        </td>
        
    </tr>
    <tr>
    	<td class="td_title">Тип документа</td>
        <td class="td_value"><select id="ofdocs_type" class="input_text">{OFDOCS_TYPE_LIST}</select></td>
        
    </tr>
    <tr>
    	<td class="td_title td_vert_top">Текст</td>
        <td class="td_value"><textarea id="ofdocs_text" class="input_text" style="width:600px; height:300px"></textarea>
        <div id="text_error" class="td_error sub_input_error"></div>
        
       
        </td>
        
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="add_ofdoc()" href="javascript:;" id="add_ofdoc_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">отправить</div></a></td>
        
    </tr> 
     
</table>

</div>

<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#add_form_block').hide(); $('#show_add_form_a').show()">Скрыть</a>
</div>

</div>

<div class="add_new_list_item" id="show_add_form_a" > 
<a href="javascript:;" class="link" onclick="$('#add_form_block').fadeIn(200); $('#show_add_form_a').hide()">+ Создать документ</a>
</div>


<script>

$('#ofdocs_to_user_id').easycomplete(
	{
		str_word_select : 'Выбрать пользователя',
		width:396,
		trigger : 1,
		url:'/ajax/ajaxGetUsers.php?by=name&who=all_tree&result_name=2'
	});	
	 
</script>