<div style="display:none" id="add_form_block" class="add_form_margin">
<div class="title_add_form">Назначить заместителя</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="tables_data_1">
	<tr>
    	<td class="td_title td_vert_top" style="width:80px">Подразделение</td>
        <td class="td_value"><select id="deputy_dept" class="input_text" onchange="deputy_get_dept_users()">
        <option value="0">- Выберите подразделение -</option>{DEPTS_LIST}</select>
        <div class="error_inp"></div>
        </td>
        
    </tr>
    <tr id="deputy_users_wrap" style="display:none">
    	<td class="td_title td_vert_top" style="width:80px">Заместитель</td>
        <td class="td_value" id="deputy_users_bl">
         
        </td>
        
    </tr>
     

    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="add_deputy()" href="javascript:;" id="add_deputy_btn">
    	<div class="right"></div><div class="left"></div><div class="btn_cont">назначить заместителя</div></a>
        <div class="clear"></div>
        <div class="" id="result"></div>
        </td>
        
    </tr> 
     
</table>

</div>

<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#add_form_block').hide(); $('#show_add_form_a').show()">Скрыть</a>
</div>

 

</div>

<div class="add_new_list_item" id="show_add_form_a" > 
<a href="javascript:;" class="link" onclick="$('#add_form_block').fadeIn(200); $('#show_add_form_a').hide()">+ Назначить заместителя</a>
</div>


<script>

planning_date_init(1, 0);
planning_date_init(2, 0);

</script>