<div class="title_add_form cat_block_margin">Добавить себе новое задание</div>
<div class="add_form">

<table cellpadding="0" cellspacing="0" class="add_task_tb">
<tr>
	<td class="td_title">Крайний срок выполнения:</td>
    <td class="td_value"><input type="text" id="task_max_date" class="select_date_inp d_none input_text" disabled="disabled" onchange="if(this.value!=''){$(this).show(); $('#clear_max_task_date').show()}" />
    <a  href="javascript:;" onclick="$('#task_max_date').focus()" class="link" >выбрать</a>
    <span id="clear_max_task_date" class="d_none add_task_l_s">| 
    <a  href="javascript:;"  onclick="$('#task_max_date').val('');$('#task_max_date').hide(); $('#clear_max_task_date').hide()" class="link">очистить</a>
    </span></td>
</tr>
<tr>
    <td class="td_title">Тема:</td>
    <td class="td_value"><input type="text" class="input_text"  maxlength="80" id="task_theme"/></td>
</tr>
<tr>
    <td class="td_title">Задание:</td>
    <td class="td_value"><textarea id="new_task_text" style="height:100px" class="input_text"></textarea></td>
</tr>
<tr>
    <td class="td_title">Дата старта:</td>
    <td class="td_value"><input type="text" id="task_date" class="select_date_inp d_none input_text" disabled="disabled" onchange="if(this.value!=''){$(this).show(); $('#clear_task_date').show()}" /> <a  href="javascript:;" onclick="$('#task_date').focus()" class="link" >выбрать</a>
    <span id="clear_task_date" class="d_none add_task_l_s">| 
    <a  href="javascript:;"  onclick="$('#task_date').val('');$('#task_date').hide(); $('#clear_task_date').hide()" class="link">очистить</a></span>
    </td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" class="add_task_tb">
<tr class="task_add_btn_block">
    <td class="td_title"></td>
    <td class="td_value">
    <a class="button" onclick="add_task({TO_USER_ID})" href="javascript:;" id="add_task_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить задание</div></a>
	<div  class="clear"></div>
    </td>
</tr>
<tr id="add_task_result" class="d_none task_add_btn_block">
    <td class="td_title"></td>
    <td class="td_value">
    <div id="task_success"></div>
    <div id="task_error" class="error_box"></div></td>
</tr>
</table>

</div>



<script>
$(function() {
	$.datepicker.setDefaults(
	        $.extend($.datepicker.regional["ru"])
	  );
	$( "#task_date" ).datepicker()
	$( "#task_max_date" ).datepicker()
});
</script>
