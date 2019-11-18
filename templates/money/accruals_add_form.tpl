<div style="display:none" id="add_money" class="add_form_margin">
<div class="title_add_form">Сделать начисление</div>
<div class="add_form add_form_margin">

<table cellpadding="0" cellspacing="0" class="add_client_tb">
	<tr>
    	<td class="td_title">Тип</td>
        <td class="td_value">
        <select id="accrual_type" class="input_text" style="width:130px">{ACCRUALS_TYPES}</select></td>
        
    </tr>
 
    <tr>
    	<td class="td_title">Сумма</td>
        <td class="td_value"><input type="text" id="accruals_summa" value="0.00" class="finance_summa input_text" style="width:100px" />
        <div id="" class="td_error sub_input_error"></div></td>
        
    </tr>
 
    <tr>
    	<td class="td_title">Комментарий</td>
        <td class="td_value"><textarea class="input_text" id="accruals_desc"></textarea></td>
        
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value">
        <a class="button" onclick="add_money_accrual()" href="javascript:;" id="add_money_accrual_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">добавить начисление</div></a>
    
        
    </tr>
    <tr>
    	<td class="td_title"></td>
        <td class="td_value"><div id="add_operation_proc"></div></td>
        
    </tr>
  
</table>


</div>
<div class="stand_margin">
<a href="javascript:;" class="link" onclick="$('#add_money').hide(); $('#show_add_money_form_a').show()">скрыть</a>
</div>
</div>

<div class="add_new_list_item " id="show_add_money_form_a"> 
<a href="javascript:;"   class="link" onclick="$('#add_money').fadeIn(200); $('#show_add_money_form_a').hide()">+ Сделать начисление</a>
</div>
<br />
<script>
$('#accruals_summa').keydown(sum_mask_proc);
$('#accruals_summa').blur(check_for_format)
</script>