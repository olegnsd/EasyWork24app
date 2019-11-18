<div class="style_list_item finance_item" id="finance_{FINANCE_ID}">

<div id="finance_content_{FINANCE_ID}">

<div class="finance_list_action_block">
{EDIT_TOOLS}
<div class="{HIDDEN_OWNER_BLOCK_CLASS}">
{FINANCE_ACCESS_BLOCK}
</div>
</div>

<table cellpadding="0" cellspacing="0" class="style_list_item_tb">
	<tr>
    	<td class="td_title">Номер счета</td>
        <td class="td_value"><b>{FINANCE_ID}</b></td>
        <td class="error"></td>
    </tr>
    <tr>
    	<td class="td_title">Название счета</td>
        <td class="td_value">{FINANCE_NAME}</td>
        <td class="error"></td>
    </tr>
    <tr>
    	<td class="td_title">Остаток на счете</td>
        <td class="td_value">{FINANCE_SUMMA} {FINANCE_CURRENCY}</td>
        <td class="error"></td>
    </tr>
   	
</table><div class="clear"></div>
<div style="margin-top:5px"> 
<div class="client_added_by">Добавил: {CREATER_USER_SURNAME} {CREATER_USER_NAME} {CREATER_USER_MIDDLENAME}</div>
</div>
 
</div>

<div id="client_result_{CLIENT_ID}"></div>
<div id="client_proc_{CLIENT_ID}"></div>

</div>