<div style="display:none" id="add_form_block" class="add_form_margin">
<div class="title_add_form">Добавить подразделение</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="posttr_add_tb">
	<tr>
    	<td class="td_title td_vert_top" style="width:80px">Название</td>
        <td class="td_value"><input type="text" id="dept_name"  class="input_text" style="width:350px"/>
        <div class="error_inp"></div>
        </td>
    </tr>
	<tr>
    	<td class="td_title td_vert_top" style="width:80px">Вышестоящее подразделение </td>
        <td class="td_value"><select class="input_text" id="parent_dept" style="width:200px">{DEPST_LIST}</select>
        <div class="error_inp"></div>
        </td>
    </tr>
    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Руководитель</td>
        <td class="td_value"><select class="input_text" id="dept_head"></select>
        <div class="error_inp"></div>
        </td>
    </tr>
     
    
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="Org.add_dept()" href="javascript:;" id="add_tracking_btn">
  	    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить подразделение</div></a>
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
<a href="javascript:;" class="link" onclick="$('#add_form_block').fadeIn(200); $('#show_add_form_a').hide()">+ Добавить подразделение</a>
</div>

<script>
$(document).ready(function(){                
$('#dept_head').easycomplete(
	{
		str_word_select : 'Выбрать пользователя',
		width:396,
		url:'/ajax/ajaxGetUsers.php?current_user=1&by=name&who=all'
	});	 
 
});
</script>