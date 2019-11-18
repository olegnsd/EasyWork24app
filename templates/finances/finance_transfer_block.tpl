<div class="finance_transfer_to_acc_block">
    <table cellpadding="3" cellspacing="3">
        <tr>
            <td class="td_title">Номер счета</td>
            <td class="td_value"><b>{FINANCE_ID_TO}</b></td>
        </tr>
        <tr>
            <td class="td_title">Название счета</td>
            <td class="td_value">{FINANCE_NAME_TO}</td>
        </tr>
        <tr>
            <td class="td_title">Остаток на счете</td>
            <td class="td_value"><b><span id="finance_summa_to">{FINANCE_SUMMA_TO}</span> {CURRENCY_VALUE_TO}</b></td>
        </tr>
        <tr>
            <td class="td_title">Владелец</td>
            <td class="td_value"><a href="/id{TO_USER_ID}" target="_blank" class="user_link">{USER_SURNAME_TO} {USER_NAME_TO} {USER_MIDDLENAME_TO}</a> <span class="user_position">{USER_POSITION_TO}</span></td>
        </tr>
    </table>          
</div>  
<div id="transfer_sub_form" class="transfer_sub_form"> 
<table cellpadding="3" cellspacing="3" >
    <tr>
        <td class="finance_transfer_th">Расход счет <b>№{FINANCE_ID_FROM}</b></td>
        <td class="finance_transfer_td"><input type="text" id="summa_from" class="input_text summa" style="width:100px" value="0.00" tabindex="1" /> {CURRENCY_VALUE_FROM} </td>
       
    </tr>
    
     <tr>
         <td class="finance_transfer_th">Поступление счет <b>№{FINANCE_ID_TO}</b></td>
        <td class="finance_transfer_td"><input type="text" id="summa_to" class="input_text summa" style="width:100px" value="0.00" tabindex="2"/> {CURRENCY_VALUE_TO}</td>
    </tr>
    
    
    <tr>
    	<td>Комментарий:</td>
        <td><textarea id="transfer_comment" class="input_text" style="width:260px"></textarea></td>
    </tr>
    <tr>
        <td class="finance_transfer_td" >
        <a class="button" onclick="to_transfer_finance()" href="javascript:;" id="to_transfer_finance_btn">
    <div class="right"></div><div class="left"></div><div class="btn_cont">перевести деньги</div></a></td>
       
    </tr>
</table>    	
</div>   

<script>
$('.summa').keydown(sum_mask_proc);
$('.summa').blur(check_for_format);

</script>           
              