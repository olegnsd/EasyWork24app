<div style="display:none" id="add_in_colleagues_list_form" class="add_form_margin">
<div class="title_add_form">Добавить коллегу</div>
<div class="add_form add_form_margin">
<div class="display_none" id="add_in_colleagues_list_form">


<table cellpadding="0" cellspacing="0" class="tables_data_1">
<tr>
	<td class="td_title">Пользователь</td>
    <td class="td_value"><select id="colleague_select" class="" ></select></td>
    
</tr>

<tr> 
	<td class="td_title">Комментарий</td>
    <td class="td_value"><textarea id="colleguae_comment" style="width:610px" class="input_text" maxlength="100" ></textarea>
    </td>
    </tr>

<tr>
	<td class="td_title"></td>
    <td class="td_value">
    <a class="button" onclick="add_in_colleague_list()" href="javascript:;" id="add_colleague_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить в коллеги</div></a>
    <div class="clear"></div>
    <div class="error_box" id="add_colleague_error"></div>
    <div id="add_colleague_result"></div>
    </td>
</tr>

</table>
 
</div>
</div>
<div class="stand_margin">
<a href="javascript:;" id="" class="link" onclick="hide_add_colleague_form()">Скрыть</a>
</div>
</div>

<div class="add_new_list_item" id="add_colleague_link" >
<a href="javascript:;" class="link" onclick="show_add_colleague_form()">+ Добавить коллегу</a>
</div>

<script>
 $(document).ready(function(){                
               
            
			
			$('#colleague_select').easycomplete(
			{
				str_word_select : 'Найти пользователя по ID или по номеру телефона (частичный)',
				width:520,
				url:'/ajax/ajaxGetUsers.php?colleague=1'
			});
			
			 });
</script>     