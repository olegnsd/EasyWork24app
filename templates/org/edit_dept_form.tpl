<div  id="edit_form_block_{DEPT_ID}" class="add_form_margin">
<div class="title_add_form">Изменить подразделение</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="posttr_add_tb">
	<tr>
    	<td class="td_title td_vert_top" style="width:80px">Название</td>
        <td class="td_value"><input type="text" id="dept_name_{DEPT_ID}" value="{DEPT_NAME}"  class="input_text" style="width:350px"/>
        <div class="error_inp"></div>
        </td>
    </tr>
	{DEPTS_LIST_ROW}
    <tr>
    	<td class="td_title td_vert_top" style="width:80px">Руководитель</td>
        <td class="td_value"><select class="input_text" id="dept_head_{DEPT_ID}">{HEAD_SELECTED}</select>
        <div class="error_inp"></div>
        </td>
    </tr>
     
    
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="Org.save_dept('{DEPT_ID}')" href="javascript:;" id="add_tracking_btn">
  	    <div class="right"></div><div class="left"></div><div class="btn_cont">сохранить</div></a> <a onclick="close_popup('', 1)" href="javascript:;" class="cancel_add_btn">отмена</a>
        <div class="clear"></div>
        <div class="error_box" id="error_box"></div>
	    <div id="success" class="success_marg"></div>

	</td>
        
    </tr> 
    
     
</table>

</div>


</div>



<script>
$(document).ready(function(){                
$('#dept_head_{DEPT_ID}').easycomplete(
	{
		str_word_select : 'Выбрать пользователя',
		width:396,
		url:'/ajax/ajaxGetUsers.php?current_user=1&by=name&who=all'
	});	 
 
});
</script>