<div style="display:none" id="add_money" class="add_form_margin">
<div class="title_add_form">Передать деньги</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="add_client_tb">
	
    <tr>
    	<td class="td_title"> </td>
        <td class="td_value"><input type="radio" id="t_2" style="margin-left:0px" name="payments_type" value="2" checked="checked" onchange="payments_add_type_proc()"/> <label for="t_2">Начисления</label> <input type="radio"    id="t_1" name="payments_type" onchange="payments_add_type_proc()" value="1" /> <label for="t_1">Рабочие нужны</label></td>
    </tr>
    
	<tr id="add_p_summa_row" class="d_none">
    	<td class="td_title">Сумма<sup>*</sup></td>
        <td class="td_value"><input type="text" id="money_summa" value="0.00" class="input_text" />
        <div id="money_summa_error" class="td_error sub_input_error"></div></td>
    </tr>
     {ACCRUALS_BLOCK}

    <tr>
    	<td class="td_title">Откуда эти деньги взяты</td>
        <td class="td_value"><input type="text" id="money_from" value="" class="input_text"  /></td>
        
    </tr>
    <tr>
    	<td class="td_title">Комментарий</td>
        <td class="td_value"><textarea class="input_text" id="money_comment"></textarea></td>
        
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="add_money()" href="javascript:;" id="add_money_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">передать деньги</div></a>
        </td>
        
    </tr>
 
</table>
</div>
<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#add_money').hide(); $('#show_add_money_form_a').show()">скрыть</a>
</div>
</div>

<div class="add_new_list_item " id="show_add_money_form_a"> 
<a href="javascript:;"   class="link" onclick="$('#add_money').fadeIn(200); $('#show_add_money_form_a').hide()">+ Передать деньги</a>
</div>

<script>
$('#money_summa').keydown(sum_mask_proc);
$('#money_summa').blur(check_for_format);
</script>
